<?php
// search_api.php
header('Content-Type: application/json');
require_once 'config/db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    // Search by name, SKU, or Part Number
    $search_term = "%$query%";
    $stmt = $pdo->prepare("SELECT id, name, price, main_image, sku, part_number 
                           FROM products 
                           WHERE name LIKE ? OR sku LIKE ? OR part_number LIKE ? 
                           LIMIT 5");
    $stmt->execute([$search_term, $search_term, $search_term]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Search failed']);
}
?>

