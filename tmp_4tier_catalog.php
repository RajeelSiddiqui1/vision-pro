<?php
require_once 'config/db.php';

function createSlug($text) {
    return strtolower(str_replace(' ', '-', $text));
}

try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DELETE FROM products");
    $pdo->exec("DELETE FROM categories");
    $pdo->exec("DELETE FROM brands");
    $pdo->exec("ALTER TABLE brands AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE categories AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE products AUTO_INCREMENT = 1");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "Cleanup Successful.\n";

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
    
    $brandIds = ['Apple' => 1, 'Samsung' => 2, 'Google' => 3];

    // Level 1 Categories (Model Groups)
    $groups = [
        ['iPhone', 'Apple', 'assets/images/categories/iphone.png'],
        ['iPad', 'Apple', 'assets/images/categories/ipad.png'],
        ['MacBook', 'Apple', 'assets/images/categories/macbook.png'],
        ['Galaxy S', 'Samsung', 'assets/images/categories/galaxy-s.png'],
        ['Galaxy Z', 'Samsung', 'assets/images/categories/galaxy-z.png'],
        ['Pixel Phone', 'Google', 'assets/images/categories/pixel-phone.png'],
        ['Pixel Tablet', 'Google', 'assets/images/categories/pixel-tablet.png']
    ];

    $groupIds = [];
    foreach ($groups as $g) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, brand_id, parent_id, image_url) VALUES (?, ?, ?, NULL, ?)");
        $slug = createSlug($g[0]);
        $stmt->execute([$g[0], $slug, $brandIds[$g[1]], $g[2]]);
        $groupIds[$g[0]] = $pdo->lastInsertId();
    }

    // Level 2 Categories (Specific Models)
    $models = [
        ['iPhone 16 Pro Max', 'iPhone', 'Apple'],
        ['iPhone 15 Pro', 'iPhone', 'Apple'],
        ['iPad Pro M4', 'iPad', 'Apple'],
        ['MacBook Pro M3', 'MacBook', 'Apple'],
        ['Galaxy S24 Ultra', 'Galaxy S', 'Samsung'],
        ['Galaxy S23 Ultra', 'Galaxy S', 'Samsung'],
        ['Galaxy Z Fold 6', 'Galaxy Z', 'Samsung'],
        ['Pixel 9 Pro XL', 'Pixel Phone', 'Google'],
        ['Pixel 8 Pro', 'Pixel Phone', 'Google']
    ];

    $modelIds = [];
    foreach ($models as $m) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, brand_id, parent_id, image_url) VALUES (?, ?, ?, ?, ?)");
        $slug = createSlug($m[0]);
        $stmt->execute([$m[0], $slug, $brandIds[$m[2]], $groupIds[$m[1]], "assets/images/categories/sub-$slug.png"]);
        $modelIds[$m[0]] = $pdo->lastInsertId();
    }

    // Level 3 Categories (Parts)
    $partTypes = [
        ['OLED Screen Assembly', 'screen'],
        ['High Capacity Battery', 'battery'],
        ['Back Glass Panel', 'back-glass'],
        ['Charging Port Flex', 'port']
    ];

    $productTemplates = [
        'OLED Screen Assembly' => ['OEM OLED Screen', 'Premium quality replacement screen with perfect touch response.', 189.99, 'assets/images/products/screen.webp'],
        'High Capacity Battery' => ['Original Battery', 'Long-lasting battery with original cycle count.', 49.99, 'assets/images/products/battery.webp'],
        'Back Glass Panel' => ['Back Glass Panel', 'Durable and precisely fitted back glass panel.', 29.99, 'assets/images/products/back-glass.webp'],
        'Charging Port Flex' => ['Charging Port Flex', 'Stable data sync and fast charging capabilities.', 24.99, 'assets/images/products/port.webp']
    ];

    foreach ($models as $m) {
        $modelName = $m[0];
        $modelId = $modelIds[$modelName];
        $brandId = $brandIds[$m[2]];

        foreach ($partTypes as $pt) {
            $ptName = $pt[0];
            $ptSlugPrefix = $pt[1];
            
            // Create Level 3 Category (Contextual Part)
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, brand_id, parent_id, image_url) VALUES (?, ?, ?, ?, ?)");
            $catName = "$modelName $ptName";
            $catSlug = createSlug($catName);
            $stmt->execute([$ptName, $catSlug, $brandId, $modelId, "assets/images/categories/parts/$ptSlugPrefix.png"]);
            $partCategoryId = $pdo->lastInsertId();

            // Create Product for this Part Category
            $template = $productTemplates[$ptName];
            $stmt = $pdo->prepare("INSERT INTO products (name, slug, category_id, brand_id, price, sku, part_number, stock_quantity, description, main_image, quality_tier, warranty, compatibility, bulk_pricing, is_active) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            
            $productName = $modelName . ' ' . $template[0];
            $sku = 'VP-' . strtoupper(substr(str_replace(' ', '', $modelName), 0, 3)) . '-' . rand(100, 999);
            
            $stmt->execute([
                $productName,
                createSlug($productName),
                $partCategoryId,
                $brandId,
                $template[2],
                $sku,
                'PN-' . rand(1000, 9999),
                rand(10, 50),
                $template[1],
                $template[3],
                'Premium OEM',
                '1 Year',
                $modelName,
                '[]'
            ]);
        }
    }

    echo "4-TIER HIERARCHY SEEDED. Brands: 3, Models: " . count($models) . ", Product Components: " . (count($models) * 4);
} catch (Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage();
}
