<?php
require_once 'config/db.php';

function getSlug($text) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
}

try {
    $pdo->beginTransaction();

    // 1. Ensure Apple Brand exists
    $stmt = $pdo->prepare("SELECT id FROM brands WHERE name = 'Apple'");
    $stmt->execute();
    $apple_id = $stmt->fetchColumn();

    if (!$apple_id) {
        $pdo->prepare("INSERT INTO brands (name, is_active) VALUES ('Apple', 1)")->execute();
        $apple_id = $pdo->lastInsertId();
    }

    // 2. Level 1 Categories (Model Groups)
    $l1_categories = ['iPhone', 'iPad', 'MacBook', 'AirPods'];
    $l1_ids = [];

    foreach ($l1_categories as $cat) {
        $slug = getSlug($cat);
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        $id = $stmt->fetchColumn();
        
        if (!$id) {
            $pdo->prepare("INSERT INTO categories (name, slug, parent_id) VALUES (?, ?, NULL)")->execute([$cat, $slug]);
            $id = $pdo->lastInsertId();
        }
        $l1_ids[$cat] = $id;
    }

    // 3. Level 2 Categories (Specific Models)
    $models = [
        'iPhone' => ['iPhone 16', 'iPhone 15', 'iPhone 14', 'iPhone 11'],
        'iPad' => ['iPad Pro 12.9', 'iPad Air 5'],
        'MacBook' => ['MacBook Pro 14"', 'MacBook Air M2']
    ];
    $model_ids = [];

    foreach ($models as $parent_name => $children) {
        foreach ($children as $child) {
            $slug = getSlug($child);
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
            $stmt->execute([$slug]);
            $id = $stmt->fetchColumn();
            
            if (!$id) {
                $pdo->prepare("INSERT INTO categories (name, slug, parent_id) VALUES (?, ?, ?)")->execute([$child, $slug, $l1_ids[$parent_name]]);
                $id = $pdo->lastInsertId();
            }
            $model_ids[$child] = $id;
        }
    }

    // 4. Level 3 Categories (Parts) for iPhone 16
    $parts = ['LCD Screen', 'Battery', 'Charging Port', 'Back Glass'];
    $part_ids = [];
    $iphone16_id = $model_ids['iPhone 16'];

    foreach ($parts as $part) {
        $name = "iPhone 16 " . $part;
        $slug = getSlug($name);
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        $id = $stmt->fetchColumn();
        
        if (!$id) {
            $pdo->prepare("INSERT INTO categories (name, slug, parent_id) VALUES (?, ?, ?)")->execute([$name, $slug, $iphone16_id]);
            $id = $pdo->lastInsertId();
        }
        $part_ids[$part] = $id;
    }

    // 5. Products for iPhone 16
    $products = [
        ['name' => 'iPhone 16 OLED Screen - Premium', 'price' => 249.99, 'cat' => 'LCD Screen'],
        ['name' => 'iPhone 16 Battery Replacement', 'price' => 89.99, 'cat' => 'Battery'],
        ['name' => 'iPhone 16 Rear Glass - Black', 'price' => 59.99, 'cat' => 'Back Glass']
    ];

    foreach ($products as $p) {
        $slug = getSlug($p['name']);
        $stmt = $pdo->prepare("SELECT id FROM products WHERE slug = ?");
        $stmt->execute([$slug]);
        if (!$stmt->fetch()) {
            $pdo->prepare("INSERT INTO products (category_id, brand_id, name, slug, description, price, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?)")
                ->execute([$part_ids[$p['cat']], $apple_id, $p['name'], $slug, "High quality " . $p['name'], $p['price'], 100]);
        }
    }

    $pdo->commit();
    echo "Seed completed successfully!\n";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
