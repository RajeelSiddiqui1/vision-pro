<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);

    // Validate stock availability
    $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $current_stock = $stmt->fetchColumn();
    
    if ($current_stock === false) {
        header("Location: cart.php");
        exit;
    }
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    switch ($action) {
        case 'add':
            $new_quantity = (isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0) + $quantity;
            if ($new_quantity > $current_stock) {
                $_SESSION['cart'][$product_id] = $current_stock;
            } else {
                $_SESSION['cart'][$product_id] = $new_quantity;
            }
            break;
        
        case 'update':
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$product_id]);
            } elseif ($quantity > $current_stock) {
                $_SESSION['cart'][$product_id] = $current_stock;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            break;

        case 'remove':
            unset($_SESSION['cart'][$product_id]);
            break;
    }

    header("Location: cart.php");
    exit;
}
?>
