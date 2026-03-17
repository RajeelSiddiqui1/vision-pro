<?php
require_once 'config/db.php';
$cats = $pdo->query("SELECT id, name, parent_id FROM categories ORDER BY parent_id, id")->fetchAll(PDO::FETCH_ASSOC);
$prods = $pdo->query("SELECT id, name, category_id, brand_id FROM products")->fetchAll(PDO::FETCH_ASSOC);

echo "CATEGORIES:\n";
foreach($cats as $c) echo "ID: {$c['id']} | Name: {$c['name']} | Parent: {$c['parent_id']}\n";
echo "\nPRODUCTS (sample of 5):\n";
foreach(array_slice($prods, 0, 5) as $p) echo "ID: {$p['id']} | Name: {$p['name']} | Cat: {$p['category_id']} | Brand: {$p['brand_id']}\n";
