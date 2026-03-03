<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth_helper.php';

if (!isset($_GET['order_id']) || !isset($_GET['method'])) {
    header("Location: index.php");
    exit;
}

$order_id = htmlspecialchars($_GET['order_id']);
$method = htmlspecialchars($_GET['method']);
$method_name = ($method === 'stripe') ? 'Credit Card (Stripe)' : (($method === 'paypal') ? 'PayPal' : 'Cash on Delivery');

// Simulate processing time
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    sleep(2); // Fake delay
    
    // Update Order Status
    $status = ($method === 'cod') ? 'pending' : 'processing';
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
    
    // Deduct stock for all items in the order (if not already deducted)
    $check_stmt = $pdo->prepare("SELECT stock_deducted FROM orders WHERE id = ?");
    $check_stmt->execute([$order_id]);
    $stock_deducted = $check_stmt->fetchColumn();
    
    if (!$stock_deducted) {
        $items_stmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
        $items_stmt->execute([$order_id]);
        $items = $items_stmt->fetchAll();
        
        foreach ($items as $item) {
            $stock_stmt = $pdo->prepare("UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - ?) WHERE id = ?");
            $stock_stmt->execute([$item['quantity'], $item['product_id']]);
        }
        
        // Mark stock as deducted
        $deduct_stmt = $pdo->prepare("UPDATE orders SET stock_deducted = 1 WHERE id = ?");
        $deduct_stmt->execute([$order_id]);
    }

    // Clear Cart
    unset($_SESSION['cart']);

    // Redirect to Success
    header("Location: order-success.php?id=$order_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Processing Payment - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-2xl border border-gray-100 text-center">
        
        <?php if($method === 'stripe'): ?>
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-4xl">💳</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Stripe Checkout</h1>
            <p class="text-gray-500 mb-8">Enter your card details to complete the purchase.</p>
            
            <form method="POST" class="space-y-4 text-left">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-widest">Card Number</label>
                    <input type="text" value="4242 4242 4242 4242" readonly class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-mono">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-widest">Expiry</label>
                        <input type="text" value="12/30" readonly class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-mono">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-widest">CVC</label>
                        <input type="text" value="123" readonly class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-mono">
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 transition-all mt-4">Pay Now</button>
            </form>

        <?php elseif($method === 'paypal'): ?>
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-4xl text-blue-600 font-bold italic">P</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">PayPal</h1>
            <p class="text-gray-500 mb-8">Login to your PayPal account to pay.</p>
            <form method="POST">
                <button type="submit" class="w-full bg-yellow-400 text-blue-900 font-bold py-4 rounded-xl hover:bg-yellow-500 transition-all">Proceed with PayPal</button>
            </form>

        <?php else: ?>
             <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-4xl">💵</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Cash on Delivery</h1>
            <p class="text-gray-500 mb-8">Confirm your order. You will pay upon receipt.</p>
            <form method="POST">
                <button type="submit" class="w-full bg-green-600 text-white font-bold py-4 rounded-xl hover:bg-green-700 transition-all">Confirm Order</button>
            </form>
        <?php endif; ?>

        <p class="mt-6 text-xs text-gray-400">Secure Payment Simulation Mode</p>
    </div>
</body>
</html>
