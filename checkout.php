<?php
session_start();
require_once 'config/db.php';
require_once 'includes/email_helper.php';
require_once 'includes/auth_helper.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Require authentication (checks session and remember cookie)
requireAuth();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: products.php");
    exit;
}

// Calculate Cart Total on Page Load
$total = 0;
$ids = array_keys($_SESSION['cart']);
if (!empty($ids)) {
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    foreach ($_SESSION['cart'] as $id => $qty) {
        $total += ($products[$id] ?? 0) * $qty;
    }
}
$tax = $total * 0.13;
$grand_total = $total + $tax;

$error = '';
$province = htmlspecialchars($_POST['province'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Backend validation
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    
    // Validate all required fields
    if (empty($email)) {
        $error = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (empty($address)) {
        $error = "Shipping address is required.";
    } elseif (empty($city)) {
        $error = "City is required.";
    } elseif (empty($province)) {
        $error = "Province is required.";
    } elseif (empty($postal_code)) {
        $error = "Postal code is required.";
    } elseif (empty($_SESSION['cart'])) {
        $error = "Your cart is empty.";
    }
    
    if (empty($error)) {
        $address = htmlspecialchars($address);
        $city = htmlspecialchars($city);
        $postal_code = htmlspecialchars($postal_code);
        
        try {
            $pdo->beginTransaction();

        // Create Order with province
        $full_address = "$address, $city, $province $postal_code";
        
        // Check if payment_status column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE 'payment_status'");
        $has_payment_status = $stmt->rowCount() > 0;
        
        if ($has_payment_status) {
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, tax_amount, status, shipping_address, payment_status) VALUES (?, ?, ?, 'processing', ?, 'paid')");
            $stmt->execute([$_SESSION['user_id'], $grand_total, $tax, $full_address]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, tax_amount, status, shipping_address) VALUES (?, ?, ?, 'processing', ?)");
            $stmt->execute([$_SESSION['user_id'], $grand_total, $tax, $full_address]);
        }
        
        $order_id = $pdo->lastInsertId();

        // Create Order Items
        $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $id => $qty) {
            // Check stock availability
            $stock_stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ? FOR UPDATE");
            $stock_stmt->execute([$id]);
            $current_stock = $stock_stmt->fetchColumn();
            
            if ($current_stock === false || $current_stock < $qty) {
                throw new Exception("Insufficient stock for product ID: $id. Available: " . ($current_stock ?? 0));
            }
            
            $item_stmt->execute([$order_id, $id, $qty, $products[$id] ?? 0]);
            
            // Deduct stock
            $update_stock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
            $update_stock->execute([$qty, $id]);
        }

        $pdo->commit();
        
        // Clear cart
        unset($_SESSION['cart']);
        
        // Get user info for email
        $user_stmt = $pdo->prepare("SELECT email, full_name FROM users WHERE id = ?");
        $user_stmt->execute([$_SESSION['user_id']]);
        $user = $user_stmt->fetch();

        // Get order items for email
        $items_stmt = $pdo->prepare("SELECT oi.*, p.name as product_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $items_stmt->execute([$order_id]);
        $order_items = $items_stmt->fetchAll();

        // Build items HTML
        $items_html = '';
        foreach ($order_items as $item) {
            $items_html .= "<tr>";
            $items_html .= "<td style='padding: 12px; border-bottom: 1px solid #e5e7eb;'>{$item['product_name']}</td>";
            $items_html .= "<td style='padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;'>{$item['quantity']}</td>";
            $items_html .= "<td style='padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: right;'>$" . number_format($item['price'], 2) . "</td>";
            $items_html .= "<td style='padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: bold;'>$" . number_format($item['quantity'] * $item['price'], 2) . "</td>";
            $items_html .= "</tr>";
        }

        // Send order confirmation email
        $email_body = "
        <div style='font-family: Arial, sans-serif; max-width: 700px; margin: 0 auto;'>
            <div style='background: #0284c7; padding: 20px; text-align: center;'>
                <h1 style='color: white; margin: 0;'>VisionPro LCD</h1>
            </div>
            <div style='padding: 20px; background: #f9fafb;'>
                <h2 style='color: #111827;'>Order Confirmed!</h2>
                <p>Hi {$user['full_name']},</p>
                <p>Thank you for your order! Your order <strong>#$order_id</strong> has been confirmed and is being processed.</p>
                
                <h3 style='margin-top: 24px; color: #111827;'>Order Details</h3>
                <table style='width: 100%; border-collapse: collapse; margin-top: 12px; background: white; border-radius: 8px; overflow: hidden;'>
                    <thead>
                        <tr style='background: #f3f4f6;'>
                            <th style='padding: 12px; text-align: left;'>Product</th>
                            <th style='padding: 12px; text-align: center;'>Qty</th>
                            <th style='padding: 12px; text-align: right;'>Price</th>
                            <th style='padding: 12px; text-align: right;'>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        $items_html
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan='3' style='padding: 12px; text-align: right; font-weight: bold;'>Subtotal:</td>
                            <td style='padding: 12px; text-align: right;'>$" . number_format($total, 2) . "</td>
                        </tr>
                        <tr>
                            <td colspan='3' style='padding: 12px; text-align: right; font-weight: bold;'>Tax (13%):</td>
                            <td style='padding: 12px; text-align: right;'>$" . number_format($tax, 2) . "</td>
                        </tr>
                        <tr style='background: #f3f4f6;'>
                            <td colspan='3' style='padding: 12px; text-align: right; font-weight: bold; font-size: 18px;'>Total:</td>
                            <td style='padding: 12px; text-align: right; font-weight: bold; font-size: 18px;'>$" . number_format($grand_total, 2) . "</td>
                        </tr>
                    </tfoot>
                </table>
                
                <h3 style='margin-top: 24px; color: #111827;'>Shipping Address</h3>
                <p style='margin-top: 8px; line-height: 1.6;'>$full_address</p>
            </div>
            <div style='padding: 20px; text-align: center; color: #6b7280; font-size: 12px;'>
                <p>&copy; " . date('Y') . " VisionPro LCD. All rights reserved.</p>
            </div>
        </div>
        ";
        
        send_email($user['email'], $user['full_name'], "Order #$order_id Confirmed - VisionPro LCD", $email_body);
        
        // Redirect to success page
        header("Location: order-success.php?id=$order_id");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Failed to process order: " . $e->getMessage();
    }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">\n <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-12 max-w-4xl">
        <div class="flex items-center gap-2 mb-6">
            <a href="cart.php" class="text-primary-600 hover:underline">← Cart</a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-800 font-medium">Checkout</span>
        </div>
        
        <h1 class="text-3xl font-bold mb-2">Checkout</h1>
        <p class="text-gray-500 mb-8">Complete your order</p>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6"><?= $error ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <form action="checkout.php" method="POST" class="space-y-6">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold mb-6">Shipping Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500" placeholder="your@email.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Address</label>
                            <input type="text" name="address" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" name="city" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                                <input type="text" name="province" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500" placeholder="e.g. Ontario">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                <input type="text" name="postal_code" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold mb-6">Payment Details</h2>
                    <input type="hidden" name="payment_method" value="clover">
                    
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M21 12.6c-.3-1.6-1.5-2.8-3.1-3.2.7-1.3.6-3-.4-4.1-1.2-1.2-3-1.4-4.3-.5-.4-1.6-1.7-2.8-3.3-3-1.7-.1-3.1 1-3.6 2.6-1.6.4-2.8 1.7-2.9 3.4-.1 1.7 1.1 3.2 2.7 3.6-.3 1.6.9 3.2 2.5 3.5 1.6.3 3.3-.6 3.9-2.1 1.4.9 3.3.6 4.4-.7 1.1-1.3.9-3.2-.4-4.3 1.6-.4 2.8 1.7 3 3.4.1 1.7-1 3.2-2.6 3.7.3 1.5 1.7 2.6 3.3 2.5 1.5-.1 2.8-1.5 2.8-3.1z"/></svg>
                            <div>
                                <h3 class="font-bold text-sm uppercase tracking-wide">Secure Payment via Clover</h3>
                                <p class="text-xs opacity-80">Your transaction is encrypted and secure.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                            <input type="text" name="card_number" placeholder="4242 4242 4242 4242" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Expiration</label>
                                <input type="text" name="card_expiry" placeholder="MM/YY" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                <input type="text" name="card_cvv" placeholder="123" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" id="submitBtn" class="w-full bg-primary-600 text-white font-bold py-4 px-8 rounded-xl hover:bg-primary-700 shadow-lg shadow-primary-200 transition-all text-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Place Order - $<?= number_format($grand_total, 2) ?>
                </button>
            </form>

            <script>
            document.querySelector('form').addEventListener('submit', function(e) {
                const btn = document.getElementById('submitBtn');
                const email = document.querySelector('[name="email"]').value.trim();
                const address = document.querySelector('[name="address"]').value.trim();
                const city = document.querySelector('[name="city"]').value.trim();
                const province = document.querySelector('[name="province"]').value.trim();
                const postalCode = document.querySelector('[name="postal_code"]').value.trim();
                
                // Clear previous errors
                document.querySelectorAll('.error-msg').forEach(el => el.remove());
                document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
                
                let errors = [];
                
                // Email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email) {
                    errors.push('Email address is required');
                    markError(document.querySelector('[name="email"]'));
                } else if (!emailRegex.test(email)) {
                    errors.push('Please enter a valid email address');
                    markError(document.querySelector('[name="email"]'));
                }
                
                // Address validation
                if (!address) {
                    errors.push('Shipping address is required');
                    markError(document.querySelector('[name="address"]'));
                } else if (address.length < 5) {
                    errors.push('Please enter a complete shipping address');
                    markError(document.querySelector('[name="address"]'));
                }
                
                // City validation
                if (!city) {
                    errors.push('City is required');
                    markError(document.querySelector('[name="city"]'));
                }
                
                // Province validation
                if (!province) {
                    errors.push('Province is required');
                    markError(document.querySelector('[name="province"]'));
                }
                
                // Postal code validation
                if (!postalCode) {
                    errors.push('Postal code is required');
                    markError(document.querySelector('[name="postal_code"]'));
                } else if (postalCode.length < 3) {
                    errors.push('Please enter a valid postal code');
                    markError(document.querySelector('[name="postal_code"]'));
                }
                
                if (errors.length > 0) {
                    e.preventDefault();
                    // Show error banner
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'bg-red-50 text-red-600 p-4 rounded-lg mb-6 error-msg';
                    errorDiv.innerHTML = '<strong>Please fix the following errors:</strong><ul class="mt-2 list-disc list-inside">' + 
                        errors.map(err => '<li>' + err + '</li>').join('') + '</ul>';
                    errorDiv.style.marginTop = '0';
                    errorDiv.style.marginBottom = '1.5rem';
                    document.querySelector('form').insertBefore(errorDiv, document.querySelector('.grid'));
                    
                    // Scroll to top
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    // Disable button to prevent double submission
                    btn.disabled = true;
                    btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
                }
            });
            
            function markError(input) {
                input.classList.add('border-red-500', 'focus:ring-red-500');
                input.classList.remove('border-gray-200', 'focus:ring-primary-500');
            }
            </script>

            <div>
                <div class="bg-gray-900 text-white p-8 rounded-3xl sticky top-24 shadow-2xl">
                    <h2 class="text-xl font-bold mb-6">Your Order</h2>
                    <div class="space-y-4 mb-8">
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <?php 
                            $ids = array_keys($_SESSION['cart']);
                            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
                            $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id IN ($placeholders)");
                            $stmt->execute($ids);
                            $product_details = $stmt->fetchAll();
                            $product_map = [];
                            foreach ($product_details as $p) {
                                $product_map[$p['id']] = $p;
                            }
                            foreach ($_SESSION['cart'] as $id => $qty): 
                                $p = $product_map[$id] ?? ['name' => 'Unknown Product', 'price' => 0];
                                $item_total = $p['price'] * $qty;
                            ?>
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium"><?= htmlspecialchars($p['name']) ?></p>
                                        <p class="text-sm text-gray-400">Qty: <?= $qty ?> × $<?= number_format($p['price'], 2) ?></p>
                                    </div>
                                    <span class="font-bold">$<?= number_format($item_total, 2) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-400 text-sm italic">Your cart is empty.</p>
                        <?php endif; ?>
                    </div>
                    <div class="pt-6 border-t border-gray-800 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Subtotal</span>
                            <span>$<?= number_format($total, 2) ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Tax (13%)</span>
                            <span>$<?= number_format($tax, 2) ?></span>
                        </div>
                        <div class="flex justify-between text-xl font-bold pt-2 border-t border-gray-700 mt-2">
                            <span>Total</span>
                            <span class="text-primary-400">$<?= number_format($grand_total, 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

