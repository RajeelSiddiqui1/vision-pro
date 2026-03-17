<?php
require_once 'config/db.php';
require_once 'includes/security.php';

// Admin Check
require_admin();

// Prevent caching
no_cache_headers();

// Check if device_categories table exists
$categories_table_exists = $pdo->query("SHOW TABLES LIKE 'device_categories'")->rowCount() > 0;

if (!$categories_table_exists) {
    // Create tables if they don't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS device_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            description TEXT,
            icon VARCHAR(100),
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_slug (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS device_subcategories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            description TEXT,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES device_categories(id) ON DELETE CASCADE,
            INDEX idx_slug (slug),
            INDEX idx_category (category_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
} else {
    // Check if device_subcategories table exists separately
    $subcategories_table_exists = $pdo->query("SHOW TABLES LIKE 'device_subcategories'")->rowCount() > 0;
    
    if (!$subcategories_table_exists) {
        // Create device_subcategories table if it doesn't exist
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS device_subcategories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                description TEXT,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES device_categories(id) ON DELETE CASCADE,
                INDEX idx_slug (slug),
                INDEX idx_category (category_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }
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
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">
    <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <?php include 'includes/admin_sidebar.php'; ?>

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
                                <a href="admin-device-categories.php?delete=<?= $cat['id'] ?>" class="text-red-600 font-bold hover:underline" onclick="smartDelete(this, 'Purge Brand Category', 'Are you sure? This will permanently remove the brand and all associated subcategories/device types.')">Delete</a>
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


