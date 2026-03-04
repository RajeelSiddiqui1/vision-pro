<?php
// migrate_part_no.php
require_once 'config/db.php';

try {
    $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS part_number VARCHAR(100) AFTER sku");
    echo "<h1>Migration Successful!</h1>";
    echo "<p>Added column: part_number to 'products' table.</p>";
    echo "<a href='index.php'>Back to Site</a>";
} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage());
}
?>

