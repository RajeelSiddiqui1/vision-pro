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

// Get all device categories for dropdown
$device_categories = $pdo->query("SELECT * FROM device_categories WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $duration_minutes = intval($_POST['duration_minutes'] ?? 60);
    $icon = trim($_POST['icon'] ?? '🔧');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $device_category_id = !empty($_POST['device_category_id']) ? intval($_POST['device_category_id']) : null;

    if (empty($name) || empty($slug) || $price <= 0) {
        $error = 'Please fill in all required fields.';
    } else {
        // Check if slug exists
        $stmt = $pdo->prepare("SELECT id FROM repair_services WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $error = 'This slug already exists. Please use a different one.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO repair_services (name, slug, description, price, duration_minutes, icon, is_active, device_category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $description, $price, $duration_minutes, $icon, $is_active, $device_category_id]);
            $success = 'Service added successfully!';
            header("Location: admin-repair-services.php?added=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Repair Service - VisionPro Admin</title>
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
        <?php include 'includes/admin_sidebar.php'; ?>

        <!-- Content -->
        <main class="flex-1 p-10">
            <header class="flex justify-between items-center mb-10">
                <div>
                    <a href="admin-repair-services.php" class="text-primary-600 font-bold hover:underline mb-2 inline-block">← Back to Services</a>
                    <h1 class="text-3xl font-bold text-gray-800">Add Repair Service</h1>
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
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Service Name *</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g., Screen Repair">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Slug *</label>
                            <input type="text" name="slug" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g., screen-repair">
                            <p class="text-xs text-gray-500 mt-1">URL-friendly name (e.g., screen-repair)</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Device Category</label>
                        <select name="device_category_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Select a Device Category (Optional)</option>
                            <?php foreach($device_categories as $dc): ?>
                                <option value="<?= $dc['id'] ?>"><?= htmlspecialchars($dc['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Describe the service..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Price ($) *</label>
                            <input type="number" name="price" step="0.01" min="0" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="99.00">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" min="1" value="60" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="60">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Icon</label>
                            <input type="text" name="icon" value="🔧" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="📱">
                            <p class="text-xs text-gray-500 mt-1">Emoji icon to display</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <label for="is_active" class="ml-2 text-sm font-bold text-gray-700">Service is Active</label>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="bg-primary-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-primary-700 transition-colors">Add Service</button>
                        <a href="admin-repair-services.php" class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-bold hover:bg-gray-400 transition-colors">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>


