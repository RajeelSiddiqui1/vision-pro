<?php
// Migration: Add category_id to repair_services table

$pdo = new PDO("mysql:host=localhost;dbname=visionpro;charset=utf8mb4", "root", "");

echo "Adding category_id column to repair_services table...\n";

try {
    // Check if column exists
    $stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'repair_services' AND column_name = 'category_id'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE repair_services ADD COLUMN category_id INT DEFAULT NULL");
        echo "Column 'category_id' added successfully!\n";
    } else {
        echo "Column 'category_id' already exists.\n";
    }
    
    // Add foreign key if not exists
    try {
        $pdo->exec("ALTER TABLE repair_services ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL");
        echo "Foreign key added successfully!\n";
    } catch (Exception $e) {
        echo "Foreign key may already exist: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Migration completed!\n";

