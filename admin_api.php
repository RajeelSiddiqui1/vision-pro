<?php
session_start();
require_once 'config/db.php';

// Security: Use existing security check or simple role check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

header('Content-Type: application/json');

switch ($action) {
    case 'get_categories_by_brand':
        $brand_id = (int)($_GET['brand_id'] ?? 0);
        // Fetch top-level categories (parent_id is null) for this brand
        // We look for categories explicitly linked to the brand OR categories that have products for this brand
        $stmt = $pdo->prepare("SELECT DISTINCT c.id, c.name FROM categories c 
                               LEFT JOIN products p ON p.category_id = c.id
                               WHERE c.parent_id IS NULL AND (c.brand_id = ? OR p.brand_id = ?)
                               ORDER BY c.name ASC");
        $stmt->execute([$brand_id, $brand_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'get_subcategories_by_category':
        $category_id = (int)($_GET['category_id'] ?? 0);
        $brand_id = (int)($_GET['brand_id'] ?? 0);
        
        // Fetch children categories for a given parent and brand
        $stmt = $pdo->prepare("SELECT DISTINCT sub.id, sub.name FROM categories sub
                               LEFT JOIN products p ON p.category_id = sub.id
                               WHERE sub.parent_id = ? AND (sub.brand_id = ? OR p.brand_id = ?)
                               ORDER BY sub.name ASC");
        $stmt->execute([$category_id, $brand_id, $brand_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
