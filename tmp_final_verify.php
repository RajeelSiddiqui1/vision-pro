<?php
require_once 'config/db.php';
$brands = $pdo->query("SELECT COUNT(*) FROM brands")->fetchColumn();
$categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
echo "FINAL_COUNT: Brands=$brands, Categories=$categories, Products=$products";
