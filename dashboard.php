<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth_helper.php';

// Prevent caching to ensure back button doesn't work after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Require authentication (checks session and remember cookie)
requireAuth();

if ($_SESSION['user_role'] === 'admin') {
    header("Location: admin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get order history
$order_stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$order_stmt->execute([$user_id]);
$orders = $order_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - VisionPro</title>
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

    <main class="container mx-auto px-4 py-12">
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Sidebar -->
            <aside class="w-full lg:w-64">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            <?= substr($user['full_name'], 0, 1) ?>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-900"><?= $user['full_name'] ?></h2>
                            <p class="text-xs text-gray-500 uppercase tracking-widest"><?= $user['role'] ?></p>
                        </div>
                    </div>
                    <nav class="space-y-1">
                        <a href="dashboard.php" class="block px-4 py-2.5 rounded-xl bg-primary-50 text-primary-700 font-bold">Orders History</a>
                        <a href="profile.php" class="block px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50">Account Profile</a>
                        <a href="addresses.php" class="block px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50">Address Book</a>
                        <div class="pt-4 mt-4 border-t border-gray-100">
                            <a href="logout.php" class="block px-4 py-2.5 rounded-xl text-red-600 hover:bg-red-50 font-bold">Logout</a>
                        </div>
                    </nav>
                </div>
            </aside>

            <!-- Content -->
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 mb-8">Order History</h1>
                
                <?php if (empty($orders)): ?>
                <div class="bg-white rounded-2xl p-12 text-center border border-gray-100">
                    <p class="text-gray-500">You haven't placed any orders yet.</p>
                    <a href="products.php" class="text-primary-600 font-bold mt-4 inline-block">Start Shopping</a>
                </div>
                <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($orders as $order): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 flex flex-wrap justify-between items-center bg-gray-50 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-widest mb-1">Order Placed</p>
                                <p class="text-sm font-bold text-gray-900"><?= date('M d, Y', strtotime($order['created_at'])) ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-widest mb-1">Total Amount</p>
                                <p class="text-sm font-bold text-gray-900">$<?= number_format($order['total_amount'], 2) ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-widest mb-1">Status</p>
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-black uppercase rounded-full">
                                    <?= $order['status'] ?>
                                </span>
                            </div>
                            <div class="ml-auto">
                                <a href="order-details.php?id=<?= $order['id'] ?>" class="text-sm font-bold text-primary-600 hover:underline">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

