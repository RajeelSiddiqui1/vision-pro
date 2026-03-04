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

// Get all categories
$categories = $pdo->query("SELECT * FROM device_categories WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name) || empty($slug) || $category_id == 0) {
        $error = 'Please fill in all required fields.';
    } else {
        // Check if slug exists
        $stmt = $pdo->prepare("SELECT id FROM device_subcategories WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $error = 'This slug already exists. Please use a different one.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO device_subcategories (name, slug, category_id, is_active) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $category_id, $is_active]);
            $success = 'Device Type added successfully!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Device Type - VisionPro Admin</title>
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
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">
    <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
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
                <a href="admin-device-categories.php" class="block py-2 text-gray-400 hover:text-white">Device Brands</a>
                <a href="admin-device-subcategory-add.php" class="block py-2 text-primary-400 font-bold">Device Types</a>
                <a href="admin-appointments.php" class="block py-2 text-gray-400 hover:text-white">Appointments</a>
                <a href="index.php" class="block py-2 text-gray-400 hover:text-white border-t border-gray-800 pt-4">View Site</a>
            </nav>
        </aside>

        <!-- Content -->
        <main class="flex-1 p-10">
            <header class="flex justify-between items-center mb-10">
                <div>
                    <a href="admin-device-categories.php" class="text-primary-600 font-bold hover:underline mb-2 inline-block">← Back to Brands</a>
                    <h1 class="text-3xl font-bold text-gray-800">Add Device Type (Subcategory)</h1>
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

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Add New Device Type</h2>
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Select Brand *</label>
                            <select name="category_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">-- Select Brand --</option>
                                <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['icon'] . ' ' . $cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Device Type Name *</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g., Mobile, Tablet, Laptop, Watch">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Slug *</label>
                        <input type="text" name="slug" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g., mobile, tablet, laptop">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <label for="is_active" class="ml-2 text-sm font-bold text-gray-700">Active</label>
                    </div>
                    <button type="submit" class="bg-primary-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-primary-700 transition-colors">Add Device Type</button>
                </form>
            </div>

            <!-- List of existing subcategories -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mt-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Existing Device Types</h2>
                <?php
                $subcategories = $pdo->query("
                    SELECT ds.*, dc.name as category_name, dc.icon as category_icon
                    FROM device_subcategories ds
                    JOIN device_categories dc ON ds.category_id = dc.id
                    ORDER BY dc.name, ds.name
                ")->fetchAll();
                ?>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php foreach($subcategories as $sub): ?>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="font-bold text-gray-900"><?= htmlspecialchars($sub['name']) ?></div>
                        <div class="text-sm text-gray-500"><?= htmlspecialchars($sub['category_icon'] . ' ' . $sub['category_name']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>


