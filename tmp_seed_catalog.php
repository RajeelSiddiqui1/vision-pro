<?php
require_once 'config/db.php';

function createSlug($text) {
    return strtolower(str_replace(' ', '-', $text));
}

try {
    // Brands
    $brands = [
        ['Apple', 'Global tech leader known for premium devices.', 'assets/images/brands/apple.png'],
        ['Samsung', 'Innovator in display and mobile technology.', 'assets/images/brands/samsung.png'],
        ['Google', 'Pioneer in software and AI-driven hardware.', 'assets/images/brands/google.png']
    ];

    foreach ($brands as $b) {
        $stmt = $pdo->prepare("INSERT INTO brands (name, slug, description, logo, is_active) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$b[0], createSlug($b[0]), $b[1], $b[2]]);
    }
    $brandIds = [
        'Apple' => 1,
        'Samsung' => 2,
        'Google' => 3
    ];

    // Level 1 Categories (Model Groups)
    $groups = [
        ['iPhone', 'Apple'],
        ['iPad', 'Apple'],
        ['MacBook', 'Apple'],
        ['Galaxy S', 'Samsung'],
        ['Galaxy Z', 'Samsung'],
        ['Pixel Phone', 'Google'],
        ['Pixel Tablet', 'Google']
    ];

    foreach ($groups as $g) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, brand_id, parent_id, image_url) VALUES (?, ?, ?, NULL, ?)");
        $slug = createSlug($g[0]);
        $stmt->execute([$g[0], $slug, $brandIds[$g[1]], "assets/images/categories/$slug.png"]);
    }

    // Level 2 Categories (Specific Models)
    // Apple
    $models = [
        ['iPhone 16 Pro Max', 1, 1], // id 1 belongs to iPhone group
        ['iPhone 15 Pro', 1, 1],
        ['iPad Pro M4', 2, 1],
        ['MacBook Pro M3', 3, 1],
        // Samsung
        ['Galaxy S24 Ultra', 4, 2],
        ['Galaxy S23 Ultra', 4, 2],
        ['Galaxy Z Fold 6', 5, 2],
        // Google
        ['Pixel 9 Pro XL', 6, 3],
        ['Pixel 8 Pro', 6, 3]
    ];

    foreach ($models as $m) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, brand_id, parent_id, image_url) VALUES (?, ?, ?, ?, ?)");
        $slug = createSlug($m[0]);
        $stmt->execute([$m[0], $slug, $m[2], $m[1], "assets/images/categories/sub-$slug.png"]);
    }

    // Products (Level 3 Parts)
    // We'll fetch all Level 2 categories (Specific Models) and add products to them
    $models = $pdo->query("SELECT id, name, brand_id FROM categories WHERE parent_id IS NOT NULL")->fetchAll();
    
    $productTemplates = [
        ['OEM OLED Screen Assembly', 'Premium quality replacement screen with perfect touch response.', 189.99, 'assets/images/products/screen.webp'],
        ['High Capacity Battery', 'Long-lasting battery with original cycle count.', 49.99, 'assets/images/products/battery.webp'],
        ['Original Back Glass', 'Durable and precisely fitted back glass panel.', 29.99, 'assets/images/products/back-glass.webp'],
        ['Charging Port Flex', 'Stable data sync and fast charging capabilities.', 24.99, 'assets/images/products/port.webp']
    ];

    foreach ($models as $model) {
        foreach ($productTemplates as $template) {
            $stmt = $pdo->prepare("INSERT INTO products (name, slug, category_id, brand_id, price, sku, part_number, stock_quantity, description, main_image, quality_tier, warranty, compatibility, bulk_pricing, is_active) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $productName = $model['name'] . ' ' . $template[0];
            $sku = 'VP-' . strtoupper(substr($model['name'], 0, 3)) . '-' . rand(100, 999);
            
            $stmt->execute([
                $productName,
                createSlug($productName),
                $model['id'],
                $model['brand_id'],
                $template[2],
                $sku,
                'PN-' . rand(1000, 9999),
                rand(10, 50),
                $template[1],
                $template[3],
                'Premium OEM',
                '1 Year',
                $model['name'],
                '[]'
            ]);
        }
    }

    echo "Seeding Successful. Total Brands: 3, Products Created.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
