<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$order_id = htmlspecialchars($_GET['id']);
$user_id = $_SESSION['user_id'];

// Fetch Order (Ensure it belongs to the logged-in user)
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: dashboard.php");
    exit;
}

// Fetch Order Items
$item_stmt = $pdo->prepare("SELECT oi.*, p.name, p.main_image, p.sku FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$item_stmt->execute([$order_id]);
$items = $item_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?= $order_id ?> - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">\n <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-12 max-w-4xl">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Order Details</h1>
            <a href="dashboard.php" class="text-primary-600 font-bold hover:underline">&larr; Back to Dashboard</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="p-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <span class="block text-xs text-gray-500 uppercase font-bold tracking-widest">Order ID</span>
                    <span class="text-lg font-bold text-gray-900">#<?= $order['id'] ?></span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500 uppercase font-bold tracking-widest text-right">Date Placed</span>
                    <span class="text-lg font-bold text-gray-900"><?= date('M d, Y', strtotime($order['created_at'])) ?></span>
                </div>
            </div>
            <div class="p-8">
                <div class="flex flex-wrap gap-8 mb-8">
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Shipping Address</h3>
                        <p class="text-gray-900 font-medium whitespace-pre-line"><?= $order['shipping_address'] ?: 'N/A' ?></p>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Order Status</h3>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-black uppercase rounded-full">
                            <?= $order['status'] ?>
                        </span>
                    </div>
                </div>

                <h3 class="text-lg font-bold text-gray-900 mb-6 pb-2 border-b border-gray-100">Items Ordered</h3>
                <div class="space-y-6">
                    <?php foreach ($items as $item): ?>
                    <div class="flex items-center gap-6">
                        <img src="<?= $item['main_image'] ?: 'https://via.placeholder.com/80' ?>" class="w-16 h-16 object-contain bg-gray-50 rounded-lg p-2 border border-gray-100">
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900"><?= $item['name'] ?></h4>
                            <p class="text-xs text-gray-500 font-bold tracking-widest uppercase">SKU: <?= $item['sku'] ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-600"><?= $item['quantity'] ?> x $<?= number_format($item['price'], 2) ?></p>
                            <p class="font-bold text-gray-900 text-lg">$<?= number_format($item['quantity'] * $item['price'], 2) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-100 flex justify-end">
                    <div class="w-full md:w-1/3 space-y-3">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>$<?= number_format($order['total_amount'] - $order['tax_amount'], 2) ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax (13%)</span>
                            <span>$<?= number_format($order['tax_amount'], 2) ?></span>
                        </div>
                        <div class="flex justify-between text-xl font-bold text-gray-900 pt-3 border-t border-gray-100">
                            <span>Total</span>
                            <span>$<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

