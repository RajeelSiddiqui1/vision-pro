<?php
include 'config/db.php';
echo "--- BRANDS ---\n";
$brands = $pdo->query("SELECT * FROM brands LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
print_r($brands);
echo "\n--- CATEGORIES ---\n";
$cats = $pdo->query("SELECT * FROM categories LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
print_r($cats);
echo "\n--- PRODUCTS COLUMNS ---\n";
$cols = $pdo->query("SHOW COLUMNS FROM products")->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
