<?php
/**
 * search_api.php — AJAX Live Search API
 * Returns JSON array of matching products
 */
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');
header('Cache-Control: public, max-age=60'); // Cache for 60s

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $search = '%' . $q . '%';
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.price, p.discount_price, p.main_image, p.sku, p.stock_quantity,
               c.name as category_name, b.name as brand_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE (p.name LIKE ? OR p.sku LIKE ? OR p.part_number LIKE ? OR p.description LIKE ?)
        LIMIT 8
    ");
    $stmt->execute([$search, $search, $search, $search]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode([]);
}
