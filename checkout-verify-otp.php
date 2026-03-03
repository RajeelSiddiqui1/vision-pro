<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth_helper.php';

// Check auth (but don't require pending_order_id check here as we need to handle it differently)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['pending_order_id'])) {
    header("Location: products.php");
    exit;
}

$order_id = $_SESSION['pending_order_id'];

// Get order and user info
$stmt = $pdo->prepare("SELECT o.*, u.email, u.full_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order || $order['status'] != 'pending_otp') {
    header("Location: products.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'];
    
    // Verify OTP
    if ($otp == $order['order_otp'] && strtotime($order['otp_expiry']) > time()) {
        // OTP valid - confirm order
        $update_stmt = $pdo->prepare("UPDATE orders SET status = 'pending_payment' WHERE id = ?");
        $update_stmt->execute([$order_id]);
        
        // Deduct stock for all items in the order
        $items_stmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
        $items_stmt->execute([$order_id]);
        $items = $items_stmt->fetchAll();
        
        foreach ($items as $item) {
            $stock_stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
            $stock_stmt->execute([$item['quantity'], $item['product_id']]);
        }
        
        // Clear pending order from session
        unset($_SESSION['pending_order_id']);
        
        // Redirect to success
        header("Location: order-success.php?id=$order_id");
        exit;
    } else {
        $error = "Invalid or expired OTP. Please try again.";
    }
}

// Resend OTP
if (isset($_POST['resend'])) {
    $new_otp = sprintf("%06d", mt_rand(1, 999999));
    $new_expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));
    $otp_stmt = $pdo->prepare("UPDATE orders SET order_otp = ?, otp_expiry = ? WHERE id = ?");
    $otp_stmt->execute([$new_otp, $new_expiry, $order_id]);
    
    // Send new OTP email
    require_once 'includes/email_helper.php';
    $otp_body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: #0284c7; padding: 20px; text-align: center;'>
            <h1 style='color: white; margin: 0;'>VisionPro LCD</h1>
        </div>
        <div style='padding: 20px; background: #f9fafb;'>
            <h2 style='color: #111827;'>Order Verification</h2>
            <p>Hi {$order['full_name']},</p>
            <p>Your new OTP for order <strong>#$order_id</strong>:</p>
            <div style='background: white; padding: 20px; text-align: center; font-size: 32px; letter-spacing: 8px; margin: 20px 0;'>
                <strong>$new_otp</strong>
            </div>
            <p>This OTP expires in 15 minutes.</p>
        </div>
    </div>
    ";
    send_email($order['email'], $order['full_name'], "Order #$order_id - New Verification OTP", $otp_body);
    $success = "New OTP sent to your email!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Order - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-12 max-w-md">
        <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Verify Your Order</h1>
                <p class="text-gray-500 mt-2">Enter the OTP sent to your email</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 font-medium"><?= $error ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 font-medium"><?= $success ?></div>
            <?php endif; ?>

            <div class="bg-gray-50 p-4 rounded-xl mb-6 text-center">
                <p class="text-sm text-gray-500">Order #<?= $order_id ?></p>
                <p class="text-lg font-bold text-gray-900">$<?= number_format($order['total_amount'], 2) ?></p>
                <p class="text-sm text-gray-500 mt-1">Sent to: <?= htmlspecialchars($order['email']) ?></p>
            </div>

            <form action="checkout-verify-otp.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 text-center">Enter 6-digit OTP</label>
                    <input type="text" name="otp" maxlength="6" minlength="6" pattern="[0-9]{6}" required 
                           class="w-full text-center text-3xl tracking-[0.5em] font-bold py-4 border-2 border-gray-200 rounded-xl outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                           placeholder="000000" autocomplete="off">
                </div>

                <button type="submit" class="w-full bg-primary-600 text-white font-bold py-4 rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-200">
                    Verify & Confirm Order
                </button>
            </form>

            <form action="checkout-verify-otp.php" method="POST" class="mt-4">
                <button type="submit" name="resend" class="w-full text-primary-600 font-medium hover:underline">
                    Didn't receive OTP? Resend
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <a href="checkout.php" class="text-gray-500 hover:text-gray-700 text-sm">
                    ← Back to Checkout
                </a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
