<?php
/**
 * Heal Database - Ensures all required tables and columns exist
 * Run this file (visionprorefurbishing.com/fix-database.php) to fix the system.
 */

session_start();
require_once 'config/db.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Safety check: Only allow access if admin OR if special secret key is provided
$secret = $_GET['secret'] ?? '';
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

if (!$is_admin && $secret !== 'visionheal123') {
    die("Access Denied. Please log in as admin or use the recovery key.");
}

$message = '';
$error = '';

try {
    // 1. Ensure token_expiry exists in users (required by auth_helper)
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'token_expiry'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE users ADD COLUMN token_expiry DATETIME NULL AFTER remember_token");
        $message .= "✓ Added column 'token_expiry' to users table.<br>";
    } else {
        $message .= "✓ Column 'token_expiry' already exists.<br>";
    }

    // 2. Ensure missing columns in products exist
    $columnsToAdd = [
        'quality_tier' => 'VARCHAR(50)',
        'warranty' => 'VARCHAR(100)',
        'compatibility' => 'VARCHAR(255)',
        'bulk_pricing' => 'JSON',
        'type' => "ENUM('product', 'accessory') DEFAULT 'product'",
    ];

    $existingColumns = [];
    $result = $pdo->query("SHOW COLUMNS FROM products");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $existingColumns[] = $row['Field'];
    }

    foreach ($columnsToAdd as $colName => $colType) {
        if (!in_array($colName, $existingColumns)) {
            $pdo->exec("ALTER TABLE products ADD $colName $colType");
            $message .= "✓ Added column '$colName' to products table.<br>";
        }
    }

    // 3. Ensure device_categories table exists
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
    $message .= "✓ Table 'device_categories' checked/created.<br>";

    // 4. Ensure device_subcategories table exists
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
    $message .= "✓ Table 'device_subcategories' checked/created.<br>";

    // 5. Ensure repair_services table exists
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS repair_services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            device_category_id INT,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            description TEXT,
            price DECIMAL(10, 2),
            category VARCHAR(100),
            icon VARCHAR(50),
            duration_minutes INT DEFAULT 60,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (device_category_id) REFERENCES device_categories(id) ON DELETE SET NULL,
            INDEX idx_slug (slug),
            INDEX idx_device (device_category_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    $message .= "✓ Table 'repair_services' checked/created.<br>";

    // 6. Ensure orders table exists (referenced in dashboard)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_number VARCHAR(50) NOT NULL UNIQUE,
            user_id INT NOT NULL,
            total_amount DECIMAL(10, 2) NOT NULL,
            status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
            payment_status ENUM('unpaid', 'paid', 'refunded', 'failed') DEFAULT 'unpaid',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user (user_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 7. Ensure order_items exists (referenced in dashboard)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            product_name VARCHAR(255) NOT NULL,
            quantity INT NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            subtotal DECIMAL(10, 2) NOT NULL,
            INDEX idx_order (order_id),
            INDEX idx_product (product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 8. Ensure categories table has image_url
    $stmt = $pdo->query("SHOW COLUMNS FROM categories LIKE 'image_url'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE categories ADD COLUMN image_url VARCHAR(255) NULL");
    }

    $message .= "<br><strong class='text-primary-600 font-bold'>System healing complete!</strong><br>The 500 errors should be resolved now. You can now access your dashboard and repair services.";

} catch (PDOException $e) {
    $error = "Critical Database Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heal Database - VisionPro Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] shadow-2xl p-10 max-w-lg w-full border border-slate-100">
        <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mb-8">
            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        
        <h1 class="text-3xl font-black text-slate-900 mb-2 tracking-tighter">System <span class="text-primary-600 underline decoration-primary-200 underline-offset-8">Healer</span></h1>
        <p class="text-slate-500 font-medium mb-8">Synchronizing database schema for production...</p>
        
        <?php if ($message): ?>
            <div class="bg-emerald-50 text-emerald-700 p-6 rounded-2xl mb-6 text-sm font-bold leading-relaxed border border-emerald-100">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="bg-rose-50 text-rose-700 p-6 rounded-2xl mb-6 text-sm font-bold border border-rose-100">
                <?= $error ?>
            </div>
        <?php endif; ?>
        
        <div class="flex flex-col gap-3">
            <a href="admin.php" class="block text-center bg-primary-600 text-white font-black py-4 px-6 rounded-2xl hover:bg-primary-700 transition-all shadow-xl shadow-primary-500/20 active:scale-95">
                Go to Dashboard
            </a>
            <a href="index.php" class="block text-center py-4 px-6 text-slate-400 font-bold hover:text-slate-600 transition-colors">
                Back to Site
            </a>
        </div>
    </div>
</body>
</html>
