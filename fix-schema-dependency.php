<?php
require_once 'config/db.php';

try {
    // 1. Add brand_id to categories
    $pdo->exec("ALTER TABLE categories ADD COLUMN brand_id INT NULL AFTER parent_id");
    $pdo->exec("ALTER TABLE categories ADD FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL");
    echo "Added brand_id to categories.\n";
    
    // 2. Add brand_id to products if missing
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'brand_id'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE products ADD COLUMN brand_id INT NULL AFTER category_id");
        $pdo->exec("ALTER TABLE products ADD FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL");
        echo "Added brand_id to products.\n";
    } else {
        echo "brand_id already exists in products.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
