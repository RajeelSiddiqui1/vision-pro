<?php
session_start();
require_once 'config/db.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Simple Admin Check (For production, implement more robust role checking)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied. Admins Only.");
}

// Get Stats
$sales_stmt = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled'");
$total_sales = $sales_stmt->fetchColumn();

$order_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// Recent Orders
$recent_stmt = $pdo->query("SELECT o.*, u.full_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
$recent_orders = $recent_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        },
                    },
                },
            },
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">\n <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 min-h-screen text-white p-6 sticky top-0">
            <h2 class="text-2xl font-bold mb-10 text-primary-400">
                <img src="assets/images/visionpro-logo.png" alt="VisionPro" class="h-8 w-auto">
                <span class="text-white">Admin</span>
            </h2>
            <nav class="space-y-4">
                <a href="admin.php" class="block py-2 text-primary-400 font-bold">Dashboard</a>
                <a href="admin-products.php" class="block py-2 text-gray-400 hover:text-white">Products</a>
                <a href="admin-categories.php" class="block py-2 text-gray-400 hover:text-white">Categories</a>
                <a href="admin-orders.php" class="block py-2 text-gray-400 hover:text-white">Orders</a>
                <a href="admin-users.php" class="block py-2 text-gray-400 hover:text-white">Customers</a>
                <a href="admin-blogs.php" class="block py-2 text-gray-400 hover:text-white">Blogs</a>
                <a href="admin-repair-services.php" class="block py-2 text-gray-400 hover:text-white">Repair Services</a>
                <a href="admin-device-categories.php" class="block py-2 text-gray-400 hover:text-white">Device Brands</a>
                <a href="admin-device-subcategory-add.php" class="block py-2 text-gray-400 hover:text-white">Device Types</a>
                <a href="admin-appointments.php" class="block py-2 text-gray-400 hover:text-white">Appointments</a>
                <a href="index.php" class="block py-2 text-gray-400 hover:text-white border-t border-gray-800 pt-4">View Site</a>
            </nav>
        </aside>

        <!-- Content -->
        <main class="flex-1 p-10">
            <header class="flex justify-between items-center mb-10">
                <h1 class="text-3xl font-bold text-gray-800">Overview</h1>
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm font-bold">
                    Admin: <?= $_SESSION['user_name'] ?>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                    <p class="text-gray-500 text-sm mb-2">Total Sales</p>
                    <p class="text-3xl font-bold text-gray-800">$<?= number_format($total_sales, 2) ?></p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                    <p class="text-gray-500 text-sm mb-2">Orders</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $order_count ?></p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                    <p class="text-gray-500 text-sm mb-2">Customers</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $user_count ?></p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
                    <p class="text-gray-500 text-sm mb-2">Products</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $product_count ?></p>
                </div>
            </div>

            <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800">Recent Orders</h2>
                    <a href="admin-orders.php" class="text-sm text-primary-600 font-bold">View All</a>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="p-6">ID</th>
                            <th class="p-6">Customer</th>
                            <th class="p-6">Amount</th>
                            <th class="p-6">Status</th>
                            <th class="p-6">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($recent_orders as $o): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-6 font-bold">#<?= $o['id'] ?></td>
                            <td class="p-6"><?= $o['full_name'] ?></td>
                            <td class="p-6">$<?= number_format($o['total_amount'], 2) ?></td>
                            <td class="p-6">
                                <span class="px-2 py-1 bg-primary-100 text-primary-700 text-[10px] font-bold uppercase rounded">
                                    <?= $o['status'] ?>
                                </span>
                            </td>
                            <td class="p-6 text-sm text-gray-500"><?= date('M d', strtotime($o['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>

