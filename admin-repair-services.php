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
    $stmt = $pdo->prepare("DELETE FROM repair_services WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: admin-repair-services.php?deleted=1");
    exit;
}

// Get all categories for filter
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// Get selected category filter
$selected_category = $_GET['category'] ?? '';

// Get services with category filter
if ($selected_category) {
    $stmt = $pdo->prepare("SELECT rs.*, c.name as category_name FROM repair_services rs LEFT JOIN categories c ON rs.category_id = c.id WHERE rs.category_id = ? ORDER BY rs.id ASC");
    $stmt->execute([$selected_category]);
    $services = $stmt->fetchAll();
} else {
    $services = $pdo->query("SELECT rs.*, c.name as category_name FROM repair_services rs LEFT JOIN categories c ON rs.category_id = c.id ORDER BY rs.id ASC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Services - VisionPro Admin</title>
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
                <a href="admin-products.php" class="block py-2 text-gray-400 hover:text-white">Products</a>
                <a href="admin-categories.php" class="block py-2 text-gray-400 hover:text-white">Categories</a>
                <a href="admin-orders.php" class="block py-2 text-gray-400 hover:text-white">Orders</a>
                <a href="admin-users.php" class="block py-2 text-gray-400 hover:text-white">Customers</a>
                <a href="admin-blogs.php" class="block py-2 text-gray-400 hover:text-white">Blogs</a>
                <a href="admin-repair-services.php" class="block py-2 text-primary-400 font-bold">Repair Services</a>
                <a href="admin-appointments.php" class="block py-2 text-gray-400 hover:text-white">Appointments</a>
                <a href="index.php" class="block py-2 text-gray-400 hover:text-white border-t border-gray-800 pt-4">View Site</a>
            </nav>
        </aside>

        <!-- Content -->
        <main class="flex-1 p-10">
            <header class="flex justify-between items-center mb-10">
                <h1 class="text-3xl font-bold text-gray-800">Repair Services</h1>
                <a href="admin-repair-service-add.php" class="bg-primary-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-primary-700 transition-colors">+ Add Service</a>
            </header>

            <!-- Category Filter -->
            <div class="mb-6">
                <form method="GET" class="flex items-center gap-4">
                    <label class="text-sm font-bold text-gray-700">Filter by Category:</label>
                    <select name="category" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Categories</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $selected_category == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if($selected_category): ?>
                        <a href="admin-repair-services.php" class="text-sm text-gray-500 hover:text-gray-700">Clear Filter</a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if (isset($_GET['deleted'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    Service deleted successfully!
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="p-6">Icon</th>
                            <th class="p-6">Service Name</th>
                            <th class="p-6">Category</th>
                            <th class="p-6">Price</th>
                            <th class="p-6">Duration</th>
                            <th class="p-6">Status</th>
                            <th class="p-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($services as $service): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-6 text-2xl"><?= htmlspecialchars($service['icon']) ?></td>
                            <td class="p-6 font-bold text-gray-900"><?= htmlspecialchars($service['name']) ?></td>
                            <td class="p-6">
                                <?php if($service['category_name']): ?>
                                    <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs font-bold uppercase rounded"><?= htmlspecialchars($service['category_name']) ?></span>
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm">No Category</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-6 font-bold text-primary-600">$<?= number_format($service['price'], 2) ?></td>
                            <td class="p-6 text-gray-500"><?= $service['duration_minutes'] ?> min</td>
                            <td class="p-6">
                                <?php if($service['is_active']): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold uppercase rounded">Active</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-[10px] font-bold uppercase rounded">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-6">
                                <a href="admin-repair-service-edit.php?id=<?= $service['id'] ?>" class="text-primary-600 font-bold hover:underline mr-4">Edit</a>
                                <a href="admin-repair-services.php?delete=<?= $service['id'] ?>" class="text-red-600 font-bold hover:underline" onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
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

