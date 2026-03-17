<?php
/**
 * cart_action.php — Handles Add/Update/Remove cart actions
 * Supports both AJAX (JSON response) and regular form POST (redirect)
 */
session_start();
require_once 'config/db.php';

$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function json_response($success, $message, $cart_count = 0, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge([
        'success'    => $success,
        'message'    => $message,
        'cart_count' => $cart_count,
    ], $data));
    exit;
}

function get_cart_data($pdo) {
    $cart = $_SESSION['cart'] ?? [];
    $total = 0;
    $items_count = 0;
    $items_info = [];

    if (!empty($cart)) {
        $ids = array_keys($cart);
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll();

        foreach ($products as $p) {
            $qty = (int)$cart[$p['id']];
            $items_count += $qty;
            $subtotal = $p['price'] * $qty;
            $total += $subtotal;
            $items_info[$p['id']] = [
                'subtotal' => number_format($subtotal, 2),
                'quantity' => $qty
            ];
        }
    }

    $tax = $total * 0.13;
    $grand_total = $total + $tax;

    return [
        'items_count' => $items_count,
        'subtotal'    => number_format($total, 2),
        'tax'         => number_format($tax, 2),
        'total'       => number_format($grand_total, 2),
        'items'       => $items_info,
        'is_empty'    => empty($cart)
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action     = $_POST['action'] ?? '';
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity   = max(1, (int)($_POST['quantity'] ?? 1));

    // Fetch product stock
    $stmt = $pdo->prepare("SELECT id, stock_quantity, name FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        if ($is_ajax) json_response(false, 'Product not found.');
        header("Location: cart.php");
        exit;
    }

    $current_stock = (int)$product['stock_quantity'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    switch ($action) {
        case 'add':
            $existing  = $_SESSION['cart'][$product_id] ?? 0;
            $new_qty   = $existing + $quantity;
            $_SESSION['cart'][$product_id] = min($new_qty, $current_stock);
            $msg = "'{$product['name']}' added to cart!";
            break;

        case 'update':
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$product_id]);
                $msg = "Item removed from cart.";
            } else {
                $_SESSION['cart'][$product_id] = min($quantity, $current_stock);
                $msg = "Cart updated.";
            }
            break;

        case 'remove':
            unset($_SESSION['cart'][$product_id]);
            $msg = "Item removed from cart.";
            break;

        default:
            if ($is_ajax) json_response(false, 'Invalid action.');
            header("Location: cart.php");
            exit;
    }

    if ($is_ajax) {
        $data = get_cart_data($pdo);
        json_response(true, $msg, $data['items_count'], $data);
    }

    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'cart.php'));
    exit;
}

if ($is_ajax) {
    json_response(false, 'Invalid request.');
}
header("Location: cart.php");
exit;
