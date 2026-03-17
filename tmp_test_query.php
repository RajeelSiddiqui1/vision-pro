<?php
require_once 'config/db.php';

echo "--- TESTING BRAND_ID=1 (Apple) ---\n";
$brand_id = 1;
$query = "SELECT p.id, p.name, c.name as cat_name, c.parent_id FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE 1=1 AND p.brand_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$brand_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Found " . count($rows) . " products.\n";
foreach(array_slice($rows, 0, 3) as $r) echo "- " . $r['name'] . " (Cat: " . $r['cat_name'] . ")\n";

echo "\n--- TESTING CATEGORY_ID=10 (Specific Model) ---\n";
// Let's find a Level 2 ID
$model_id = $pdo->query("SELECT id FROM categories WHERE parent_id IS NOT NULL AND parent_id IN (SELECT id FROM categories WHERE parent_id IS NULL) LIMIT 1")->fetchColumn();
echo "Testing with Model ID: $model_id\n";

$query = "SELECT p.id, p.name FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE 1=1 AND (p.category_id = ? OR c.parent_id = ? OR c.parent_id IN (SELECT id FROM categories WHERE parent_id = ?))";
$stmt = $pdo->prepare($query);
$stmt->execute([$model_id, $model_id, $model_id]);
echo "Found " . $pdo->prepare($query)->execute([$model_id, $model_id, $model_id]) ? count($stmt->fetchAll()) : "error" . " products.\n";

echo "\n--- CATEGORY HIERARCHY SAMPLE ---\n";
$cats = $pdo->query("SELECT id, name, parent_id FROM categories LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
print_r($cats);
