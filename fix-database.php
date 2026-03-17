<?php
/**
 * Fix Database - Add missing columns to products table
 * Run this file once to fix the database structure
 */

session_start();
require_once 'config/db.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Admin check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied. Admins Only.");
}

$message = '';
$error = '';

try {
    // Array of columns to add (name => type)
    $columnsToAdd = [
        'quality_tier' => 'VARCHAR(50)',
        'warranty' => 'VARCHAR(100)',
        'compatibility' => 'VARCHAR(255)',
        'bulk_pricing' => 'JSON',
    ];

    // Check which columns already exist
    $existingColumns = [];
    $result = $pdo->query("SHOW COLUMNS FROM products");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $existingColumns[] = $row['Field'];
    }

    // Add missing columns one by one
    foreach ($columnsToAdd as $colName => $colType) {
        if (!in_array($colName, $existingColumns)) {
            try {
                $sql = "ALTER TABLE products ADD $colName $colType";
                $pdo->exec($sql);
                $message .= "✓ Added column: $colName<br>";
            } catch (PDOException $e) {
                $message .= "⚠ Error adding $colName: " . $e->getMessage() . "<br>";
            }
        } else {
            $message .= "✓ Column already exists: $colName<br>";
        }
    }

    // Also check and add device_categories table
    $tableExists = $pdo->query("SHOW TABLES LIKE 'device_categories'")->rowCount() > 0;
    if (!$tableExists) {
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $message .= "✓ Created table: device_categories<br>";
    } else {
        $message .= "✓ Table already exists: device_categories<br>";
    }

    // Check and add device_subcategories table
    $subTableExists = $pdo->query("SHOW TABLES LIKE 'device_subcategories'")->rowCount() > 0;
    if (!$subTableExists) {
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $message .= "✓ Created table: device_subcategories<br>";
    } else {
        $message .= "✓ Table already exists: device_subcategories<br>";
    }

    $message .= "<br><strong>Database fix completed! You can now add products.</strong>";

} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Database - VisionPro Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md w-full">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Database Fix</h1>
        
        <?php if ($message): ?>
            <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-4">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-4">
                <?= $error ?>
            </div>
        <?php endif; ?>
        
        <a href="admin-products.php" class="block text-center bg-primary-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-primary-700">
            Go to Products
        </a>
    </div>
</body>
</html>
