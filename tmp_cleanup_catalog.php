<?php
require_once 'config/db.php';

try {
    $pdo->beginTransaction();
    
    // Disable foreign key checks for a clean wipe
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Truncate tables
    $pdo->exec("TRUNCATE TABLE products");
    $pdo->exec("TRUNCATE TABLE categories"); // Handles both Model Groups and Specific Models
    $pdo->exec("TRUNCATE TABLE brands");
    
    // Optional: Reset auto-increments (Truncate usually does this, but being explicit)
    $pdo->exec("ALTER TABLE brands AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE categories AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE products AUTO_INCREMENT = 1");
    
    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    $pdo->commit();
    echo "SUCCESS: Catalog data has been fully wiped.";
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "FAILURE: " . $e->getMessage();
}
