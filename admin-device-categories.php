<?php
session_start();
require_once 'config/db.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Admin Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied. Admins Only.");
}

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM device_categories WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: admin-device-categories.php?deleted=1");
    exit;
}

// Get all categories with subcategory count
$categories = $pdo->query("
    SELECT dc.*, 
           (SELECT COUNT(*) FROM device_subcategories WHERE category_id = dc.id) as subcategory_count
    FROM device_categories dc 
    ORDER BY dc.name ASC
")->fetchAll();

// Get all subcategories
$subcategories = $pdo->query("
    SELECT ds.*, dc.name as category_name
    FROM device_subcategories ds
    JOIN device_categories dc ON ds.category_id = dc.id
    ORDER BY dc.name ASC, ds.name ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Categories - VisionPro Admin</title>
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
    <link rel="icon" type="image/jpeg" href="assets/images/visionpro-logo.jpeg">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 min-h-screen text-white p-6 sticky top-0">
            <h2 class="text-2xl font-bold mb-10 text-primary-400">
                <img src="assets/images/visionpro-logo.jpeg" alt="VisionPro" class="h-8 w-auto">
                <span class="text-white">Admin</span>
            </h2>
            <nav class="space-y-4">
                <a href="admin.php" class="block py-2 text-gray-400 hover:text-white">Dashboard</a>
                <a href="admin-products.php" class="block py-2 text-gray-400 hover:text-white">Products</a>
                <a href="admin-categories.php" class="block py-2 text-gray-400 hover:text-white">Categories</a>
                <a href="admin-orders.php" class="block py-2 text-gray-400 hover:text-white">Orders</a>
                <a href="admin-users.php" class="block py-2 text-gray-400 hover:text-white">Customers</a>
                <a href="admin-blogs.php" class="block py-2 text-gray-400 hover:text-white">Blogs</a>
                <a href="admin-repair-services.php" class="block py-2 text-gray-400 hover:text-white">Repair Services</a>
                <a href="admin-device-categories.php" class="block py-2 text-primary-400 font-bold">Device Categories</a>
                <a href="admin-appointments.php" class="block py-2 text-gray-400 hover:text-white">Appointments</a>
                <a href="index.php" class="block py-2 text-gray-400 hover:text-white border-t border-gray-800 pt-4">View Site</a>
            </nav>
        </aside>

        <!-- Content -->
        <main class="flex-1 p-10">
            <header class="flex justify-between items-center mb-10">
                <h1 class="text-3xl font-bold text-gray-800">Device Categories</h1>
                <a href="admin-device-category-add.php" class="bg-primary-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-primary-700 transition-colors">+ Add Brand</a>
            </header>

            <?php if (isset($_GET['deleted'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    Category deleted successfully!
                </div>
            <?php endif; ?>

            <!-- Brands/Categories -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-10">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="font-bold text-gray-800">Device Brands/Companies</h2>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="p-6">Icon</th>
                            <th class="p-6">Brand Name</th>
                            <th class="p-6">Subcategories</th>
                            <th class="p-6">Status</th>
                            <th class="p-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($categories as $cat): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-6 text-2xl"><?= htmlspecialchars($cat['icon']) ?></td>
                            <td class="p-6 font-bold text-gray-900"><?= htmlspecialchars($cat['name']) ?></td>
                            <td class="p-6 text-gray-500"><?= $cat['subcategory_count'] ?> subcategories</td>
                            <td class="p-6">
                                <?php if($cat['is_active']): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold uppercase rounded">Active</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-[10px] font-bold uppercase rounded">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-6">
                                <a href="admin-device-category-edit.php?id=<?= $cat['id'] ?>" class="text-primary-600 font-bold hover:underline mr-4">Edit</a>
                                <a href="admin-device-categories.php?delete=<?= $cat['id'] ?>" class="text-red-600 font-bold hover:underline" onclick="return confirm('Are you sure? This will also delete all subcategories.')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Subcategories -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="font-bold text-gray-800">Device Types (Subcategories)</h2>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="p-6">Type Name</th>
                            <th class="p-6">Brand</th>
                            <th class="p-6">Status</th>
                            <th class="p-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($subcategories as $sub): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-6 font-bold text-gray-900"><?= htmlspecialchars($sub['name']) ?></td>
                            <td class="p-6 text-gray-500"><?= htmlspecialchars($sub['category_name']) ?></td>
                            <td class="p-6">
                                <?php if($sub['is_active']): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold uppercase rounded">Active</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-[10px] font-bold uppercase rounded">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-6">
                                <a href="admin-device-subcategory-edit.php?id=<?= $sub['id'] ?>" class="text-primary-600 font-bold hover:underline">Edit</a>
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
