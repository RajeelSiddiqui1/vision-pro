<?php
require_once 'config/db.php';

// Disable foreign key checks
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

// Wipe tables
$pdo->exec("DELETE FROM products");
$pdo->exec("DELETE FROM categories");
$pdo->exec("DELETE FROM brands");

// Reset Auto-inc
$pdo->exec("ALTER TABLE brands AUTO_INCREMENT = 1");
$pdo->exec("ALTER TABLE categories AUTO_INCREMENT = 1");
$pdo->exec("ALTER TABLE products AUTO_INCREMENT = 1");

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

echo "Cleanup Successful.";
