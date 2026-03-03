<?php
// Migration: Add device categories for repair services

$pdo = new PDO("mysql:host=localhost;dbname=visionpro;charset=utf8mb4", "root", "");

echo "Creating device_categories table...\n";
$pdo->exec("CREATE TABLE IF NOT EXISTS device_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    icon VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

echo "Creating device_subcategories table...\n";
$pdo->exec("CREATE TABLE IF NOT EXISTS device_subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES device_categories(id) ON DELETE CASCADE
)");

// Add category_id to repair_services if not exists
echo "Adding category_id to repair_services...\n";
$pdo->exec("ALTER TABLE repair_services ADD COLUMN IF NOT EXISTS category_id INT NULL");
$pdo->exec("ALTER TABLE repair_services ADD FOREIGN KEY (category_id) REFERENCES device_categories(id) ON DELETE SET NULL");

// Insert default device categories
echo "Inserting default device categories...\n";

$categories = [
    ['Apple', 'apple', '🍎'],
    ['Samsung', 'samsung', '📱'],
    ['Google', 'google', '🔵'],
    ['OnePlus', 'oneplus', '🔴'],
    ['Xiaomi', 'xiaomi', '🟠'],
    ['Other', 'other', '📱']
];

$stmt = $pdo->prepare("INSERT IGNORE INTO device_categories (name, slug, icon) VALUES (?, ?, ?)");
foreach ($categories as $cat) {
    $stmt->execute($cat);
}

// Get category IDs
$apple_id = $pdo->query("SELECT id FROM device_categories WHERE slug = 'apple'")->fetchColumn();
$samsung_id = $pdo->query("SELECT id FROM device_categories WHERE slug = 'samsung'")->fetchColumn();
$google_id = $pdo->query("SELECT id FROM device_categories WHERE slug = 'google'")->fetchColumn();
$oneplus_id = $pdo->query("SELECT id FROM device_categories WHERE slug = 'oneplus'")->fetchColumn();
$xiaomi_id = $pdo->query("INSERT IGNORE INTO device_categories (name, slug, icon) VALUES ('Xiaomi', 'xiaomi', '🟠')");
$xiaomi_id = $pdo->query("SELECT id FROM device_categories WHERE slug = 'xiaomi'")->fetchColumn();
$other_id = $pdo->query("SELECT id FROM device_categories WHERE slug = 'other'")->fetchColumn();

echo "Inserting default subcategories...\n";

$subcategories = [
    // Apple
    ['Mobile', 'mobile', $apple_id],
    ['Tablet', 'tablet', $apple_id],
    ['Laptop', 'laptop', $apple_id],
    ['Watch', 'watch', $apple_id],
    // Samsung
    ['Mobile', 'mobile', $samsung_id],
    ['Tablet', 'tablet', $samsung_id],
    ['Laptop', 'laptop', $samsung_id],
    ['Watch', 'watch', $samsung_id],
    // Google
    ['Mobile', 'mobile', $google_id],
    ['Tablet', 'tablet', $google_id],
    // OnePlus
    ['Mobile', 'mobile', $oneplus_id],
    // Xiaomi
    ['Mobile', 'mobile', $xiaomi_id],
    ['Tablet', 'tablet', $xiaomi_id],
    // Other
    ['Mobile', 'mobile', $other_id],
    ['Tablet', 'tablet', $other_id],
    ['Laptop', 'laptop', $other_id],
];

$stmt = $pdo->prepare("INSERT IGNORE INTO device_subcategories (name, slug, category_id) VALUES (?, ?, ?)");
foreach ($subcategories as $sub) {
    $stmt->execute($sub);
}

echo "Migration completed successfully!\n";
