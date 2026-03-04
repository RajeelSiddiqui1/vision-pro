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

$error = '';
$success = '';

// Get category ID
$category_id = $_GET['id'] ?? 0;
if (!is_numeric($category_id)) {
    die("Invalid category ID");
}

// Get category data
$stmt = $pdo->prepare("SELECT * FROM device_categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch();

if (!$category) {
    die("Category not found");
}

// Get subcategories for this category
$subcategories = $pdo->prepare("SELECT * FROM device_subcategories WHERE category_id = ? ORDER BY name ASC");
$subcategories->execute([$category_id]);
$subcategories = $subcategories->fetchAll();

// Handle update
if (isset($_POST['update_category'])) {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $icon = trim($_POST['icon'] ?? '📱');
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name) || empty($slug)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Check if slug exists (excluding current)
        $stmt = $pdo->prepare("SELECT id FROM device_categories WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $category_id]);
        if ($stmt->fetch()) {
            $error = 'This slug already exists.';
        } else {
            $stmt = $pdo->prepare("UPDATE device_categories SET name = ?, slug = ?, icon = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $icon, $is_active, $category_id]);
            $success = 'Brand updated successfully!';
            
            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM device_categories WHERE id = ?");
            $stmt->execute([$category_id]);
            $category = $stmt->fetch();
        }
    }
}

// Handle add subcategory
if (isset($_POST['add_subcategory'])) {
    $sub_name = trim($_POST['sub_name'] ?? '');
    $sub_slug = trim($_POST['sub_slug'] ?? '');
    $sub_is_active = isset($_POST['sub_is_active']) ? 1 : 0;

    if (!empty($sub_name) && !empty($sub_slug)) {
        $stmt = $pdo->prepare("INSERT INTO device_subcategories (name, slug, category_id, is_active) VALUES (?, ?, ?, ?)");
        $stmt->execute([$sub_name, $sub_slug, $category_id, $sub_is_active]);
        header("Location: admin-device-category-edit.php?id=" . $category_id);
        exit;
    }
}

// Handle delete subcategory
if (isset($_GET['delete_sub']) && is_numeric($_GET['delete_sub'])) {
    $stmt = $pdo->prepare("DELETE FROM device_subcategories WHERE id = ?");
    $stmt->execute([$_GET['delete_sub']]);
    header("Location: admin-device-category-edit.php?id=" . $category_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Device Category - VisionPro Admin</title>
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
                <a href="admin-repair-services.php" class="block py-2 text-gray-400 hover:text-white">Repair Services</a>
                <a href="admin-device-categories.php" class="block py-2 text-primary-400 font-bold">Device Categories</a>
                <a href="admin-appointments.php" class="block py-2 text-gray-400 hover:text-white">Appointments</a>
                <a href="index.php" class="block py-2 text-gray-400 hover:text-white border-t border-gray-800 pt-4">View Site</a>
            </nav>
        </aside>

        <!-- Content -->
        <main class="flex-1 p-10">
            <header class="flex justify-between items-center mb-10">
                <div>
                    <a href="admin-device-categories.php" class="text-primary-600 font-bold hover:underline mb-2 inline-block">← Back to Categories</a>
                    <h1 class="text-3xl font-bold text-gray-800">Edit Brand: <?= htmlspecialchars($category['name']) ?></h1>
                </div>
            </header>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <!-- Edit Brand -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Edit Brand/Company</h2>
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Brand Name *</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Slug *</label>
                            <input type="text" name="slug" value="<?= htmlspecialchars($category['slug']) ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Icon</label>
                        <input type="text" name="icon" value="<?= htmlspecialchars($category['icon']) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" <?= $category['is_active'] ? 'checked' : '' ?> class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <label for="is_active" class="ml-2 text-sm font-bold text-gray-700">Active</label>
                    </div>
                    <button type="submit" name="update_category" class="bg-primary-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-primary-700 transition-colors">Update Brand</button>
                </form>
            </div>

            <!-- Subcategories -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Device Types for <?= htmlspecialchars($category['name']) ?></h2>
                
                <!-- Add new subcategory -->
                <form method="POST" class="mb-8 p-6 bg-gray-50 rounded-xl">
                    <h3 class="font-bold text-gray-700 mb-4">Add New Device Type</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Type Name</label>
                            <input type="text" name="sub_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg" placeholder="e.g., Mobile">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Slug</label>
                            <input type="text" name="sub_slug" required class="w-full px-4 py-3 border border-gray-300 rounded-lg" placeholder="e.g., mobile">
                        </div>
                        <div class="flex items-end">
                            <div class="flex items-center mr-4">
                                <input type="checkbox" name="sub_is_active" id="sub_is_active" value="1" checked class="w-4 h-4 text-primary-600 border-gray-300 rounded">
                                <label for="sub_is_active" class="ml-2 text-sm font-bold text-gray-700">Active</label>
                            </div>
                            <button type="submit" name="add_subcategory" class="bg-primary-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-primary-700">Add</button>
                        </div>
                    </div>
                </form>

                <!-- List subcategories -->
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="p-4">Name</th>
                            <th class="p-4">Slug</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($subcategories as $sub): ?>
                        <tr>
                            <td class="p-4 font-bold"><?= htmlspecialchars($sub['name']) ?></td>
                            <td class="p-4 text-gray-500"><?= htmlspecialchars($sub['slug']) ?></td>
                            <td class="p-4">
                                <?php if($sub['is_active']): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold uppercase rounded">Active</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-bold uppercase rounded">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <a href="admin-device-category-edit.php?id=<?= $category_id ?>&delete_sub=<?= $sub['id'] ?>" class="text-red-600 font-bold hover:underline" onclick="return confirm('Delete this device type?')">Delete</a>
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

