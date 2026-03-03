<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth_helper.php';

// Optional auth - auto-login if remember cookie exists, but doesn't require login
optionalAuth();

$cart_items = [];
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    foreach ($products as $p) {
        $qty = $_SESSION['cart'][$p['id']];
        $subtotal = $p['price'] * $qty;
        $total += $subtotal;
        $cart_items[] = [
            'product' => $p,
            'quantity' => $qty,
            'subtotal' => $subtotal
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="assets/images/visionpro-logo.jpeg">
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="flex-grow container mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-10">Shopping Cart</h1>

        <?php if (empty($cart_items)): ?>
        <div class="bg-white rounded-3xl p-20 text-center shadow-sm border border-gray-100">
            <div class="text-6xl mb-6">🛒</div>
            <h2 class="text-2xl font-bold text-gray-900">Your cart is empty</h2>
            <p class="text-gray-500 mt-2 mb-8">Looks like you haven't added anything to your cart yet.</p>
            <a href="products.php" class="btn-primary inline-flex">Go to Products</a>
        </div>
        <?php else: ?>
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Items List -->
            <div class="flex-1 space-y-6">
                <?php foreach ($cart_items as $item): ?>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-6">
                    <img src="<?= $item['product']['main_image'] ?: 'https://via.placeholder.com/100' ?>" class="w-24 h-24 object-cover rounded-xl bg-gray-50">
                    <div class="flex-1">
                        <h3 class="font-bold text-lg text-gray-900"><?= $item['product']['name'] ?></h3>
                        <p class="text-sm text-gray-500">Unit Price: $<?= number_format($item['product']['price'], 2) ?></p>
                        <form action="cart_action.php" method="POST" class="mt-4 flex items-center gap-4">
                            <input type="hidden" name="product_id" value="<?= $item['product']['id'] ?>">
                            <input type="hidden" name="action" value="update">
                            <div class="flex items-center border rounded-lg overflow-hidden h-10">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['product']['stock_quantity'] ?>" class="w-16 text-center focus:ring-primary-500 outline-none font-bold">
                            </div>
                            <span class="text-xs text-gray-500"><?= $item['product']['stock_quantity'] ?> in stock</span>
                            <button type="submit" class="text-xs font-bold text-primary-600 uppercase tracking-widest hover:underline">Update</button>
                            <button type="submit" name="action" value="remove" class="text-xs font-bold text-red-500 uppercase tracking-widest hover:underline ml-auto">Remove</button>
                        </form>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-bold text-gray-900">$<?= number_format($item['subtotal'], 2) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Summary -->
            <div class="lg:w-96">
                <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100 sticky top-24">
                    <h2 class="text-xl font-bold mb-6">Order Summary</h2>
                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>$<?= number_format($total, 2) ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="text-green-600 font-bold uppercase text-xs">Calculated at checkout</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax (Ontario 13% HST)</span>
                            <span>$<?= number_format($total * 0.13, 2) ?></span>
                        </div>
                        <div class="pt-6 border-t border-gray-100 flex justify-between">
                            <span class="font-bold text-gray-900">Total</span>
                            <span class="text-2xl font-bold text-primary-600">$<?= number_format($total * 1.13, 2) ?></span>
                        </div>
                    </div>
                    <a href="checkout.php" class="w-full btn-primary block text-center py-4">Proceed to Checkout</a>
                    <p class="text-center mt-6 text-xs text-gray-400">Secure 256-bit SSL encrypted checkout</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
