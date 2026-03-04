<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin-products.php");
    exit;
}

// Get All Products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
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
                <a href="admin.php" class="block py-2 text-gray-400 hover:text-white">Dashboard</a>
                <a href="admin-products.php" class="block py-2 text-primary-400 font-bold">Products</a>
                <a href="admin-categories.php" class="block py-2 text-gray-400 hover:text-white">Categories</a>
                <a href="admin-orders.php" class="block py-2 text-gray-400 hover:text-white">Orders</a>
                <a href="admin-users.php" class="block py-2 text-gray-400 hover:text-white">Customers</a>
                <a href="index.php" class="block py-2 text-gray-400 hover:text-white border-t border-gray-800 pt-4">View Site</a>
            </nav>
        </aside>

        <!-- Content -->
        <main class="flex-1 p-10">
            <header class="flex justify-between items-center mb-10">
                <h1 class="text-3xl font-bold text-gray-800">Products</h1>
                <a href="admin-product-add.php" class="bg-primary-600 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-primary-200">Add New Product</a>
            </header>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="p-6">Product</th>
                            <th class="p-6">Category</th>
                            <th class="p-6">Price</th>
                            <th class="p-6">Stock</th>
                            <th class="p-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($products as $p): ?>
                        <tr>
                            <td class="p-6 flex items-center gap-4">
                                <img src="<?= $p['main_image'] ?: 'https://via.placeholder.com/50' ?>" class="w-10 h-10 rounded-lg object-cover bg-gray-50">
                                <div>
                                    <p class="font-bold text-gray-800"><?= $p['name'] ?></p>
                                    <p class="text-xs text-gray-400">SKU: <?= $p['sku'] ?></p>
                                </div>
                            </td>
                            <td class="p-6 text-sm text-gray-600"><?= $p['category_name'] ?></td>
                            <td class="p-6 font-bold">$<?= number_format($p['price'], 2) ?></td>
                            <td class="p-6">
                                <span class="px-2 py-1 <?= $p['stock_quantity'] < 10 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?> text-[10px] font-bold uppercase rounded">
                                    <?= $p['stock_quantity'] ?> in stock
                                </span>
                            </td>
                            <td class="p-6 text-right space-x-2">
                                <a href="admin-product-edit.php?id=<?= $p['id'] ?>" class="text-xs font-bold text-primary-600 hover:underline">Edit</a>
                                <a href="admin-products.php?delete=<?= $p['id'] ?>" onclick="return confirm('Delete this product?')" class="text-xs font-bold text-red-500 hover:underline">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>

