<?php
// migrate_wholesale.php
require_once 'config/db.php';

try {
    // Add quality_tier
    $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS quality_tier VARCHAR(100) DEFAULT 'Standard' AFTER description");
    
    // Add warranty
    $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS warranty VARCHAR(100) DEFAULT 'Lifetime Warranty' AFTER quality_tier");
    
    // Add compatibility
    $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS compatibility TEXT AFTER warranty");
    
    // Add bulk_pricing (JSON format)
    $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS bulk_pricing TEXT AFTER price");

    echo "<h1>Migration Successful!</h1>";
    echo "<p>Added columns: quality_tier, warranty, compatibility, bulk_pricing to 'products' table.</p>";
    echo "<a href='index.php'>Back to Site</a>";
} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage());
}
?>

