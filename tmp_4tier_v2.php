<?php
require_once 'config/db.php';

function createSlug($text) {
    return strtolower(str_replace([' ', '/'], '-', $text));
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

    // 1. Brands
    $brands = [
        ['Apple', 'Premium electronics and software.', 'assets/images/brands/apple.png'],
        ['Samsung', 'Leading innovator in displays and mobiles.', 'assets/images/brands/samsung.png'],
        ['Microsoft', 'Empowering every person and organization.', 'assets/images/brands/microsoft.png']
    ];

    foreach ($brands as $b) {
        $stmt = $pdo->prepare("INSERT INTO brands (name, slug, description, logo, is_active) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$b[0], createSlug($b[0]), $b[1], $b[2]]);
    }
    
    $brandIds = ['Apple' => 1, 'Samsung' => 2, 'Microsoft' => 3];

    // 2. Level 1 Categories (Model Groups/Series)
    $groups = [
        ['iPhone', 'Apple', 'assets/images/categories/iphone.png'],
        ['iPad', 'Apple', 'assets/images/categories/ipad.png'],
        ['MacBook', 'Apple', 'assets/images/categories/macbook.png'],
        ['Galaxy S', 'Samsung', 'assets/images/categories/galaxy-s.png'],
        ['Galaxy Z', 'Samsung', 'assets/images/categories/galaxy-z.png'],
        ['Surface Pro', 'Microsoft', 'assets/images/categories/surface-pro.png'],
        ['Surface Laptop', 'Microsoft', 'assets/images/categories/surface-laptop.png']
    ];

    $groupIds = [];
    foreach ($groups as $g) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, brand_id, parent_id, image_url) VALUES (?, ?, ?, NULL, ?)");
        $slug = createSlug($g[0]);
        $stmt->execute([$g[0], $slug, $brandIds[$g[1]], $g[2]]);
        $groupIds[$g[0]] = $pdo->lastInsertId();
    }

    // 3. Level 2 Categories (Specific Models)
    $models = [
        // Apple
        ['iPhone 16 Pro Max', 'iPhone', 'Apple'],
        ['iPhone 15 Pro', 'iPhone', 'Apple'],
        ['iPhone 14', 'iPhone', 'Apple'],
        ['iPad Pro M4', 'iPad', 'Apple'],
        ['MacBook Pro M3', 'MacBook', 'Apple'],
        // Samsung
        ['Galaxy S24 Ultra', 'Galaxy S', 'Samsung'],
        ['Galaxy S23 Ultra', 'Galaxy S', 'Samsung'],
        ['Galaxy Z Fold 6', 'Galaxy Z', 'Samsung'],
        // Microsoft
        ['Surface Pro 9', 'Surface Pro', 'Microsoft'],
        ['Surface Laptop 5', 'Surface Laptop', 'Microsoft']
    ];

    $modelIds = [];
    foreach ($models as $m) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, brand_id, parent_id, image_url) VALUES (?, ?, ?, ?, ?)");
        $slug = createSlug($m[0]);
        $stmt->execute([$m[0], $slug, $brandIds[$m[2]], $groupIds[$m[1]], "assets/images/categories/sub-$slug.png"]);
        $modelIds[$m[0]] = $pdo->lastInsertId();
    }

    // 4. Level 3 Categories (Part Clusters)
    $partTypes = [
        ['OLED Screen', 'screen', 189.99],
        ['Battery Pack', 'battery', 49.99],
        ['Rear Housing', 'housing', 79.99],
        ['Camera Module', 'camera', 129.99],
        ['Charging Port', 'charging', 34.99]
    ];

    foreach ($models as $m) {
        $modelName = $m[0];
        $modelId = $modelIds[$modelName];
        $brandId = $brandIds[$m[2]];

        foreach ($partTypes as $pt) {
            $ptName = $pt[0];
            $ptSlugPrefix = $pt[1];
            $basePrice = $pt[2];
            
            // Create Level 3 Category (Part Context)
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, brand_id, parent_id, image_url) VALUES (?, ?, ?, ?, ?)");
            $catName = $ptName; // Displayed simply as "Battery", "OLED Screen" etc.
            $catSlug = createSlug($modelName . " " . $ptName);
            $stmt->execute([$catName, $catSlug, $brandId, $modelId, "assets/images/categories/parts/$ptSlugPrefix.png"]);
            $partCategoryId = $pdo->lastInsertId();

            // 5. Products linked to the Part Category
            $stmt = $pdo->prepare("INSERT INTO products (name, slug, category_id, brand_id, price, sku, part_number, stock_quantity, description, main_image, quality_tier, warranty, compatibility, bulk_pricing, is_active) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            
            $productName = $modelName . " " . $ptName . " - Premium Quality";
            $sku = 'VP-' . strtoupper(substr(str_replace(' ', '', $modelName), 0, 3)) . '-' . rand(100, 999);
            
            $stmt->execute([
                $productName,
                createSlug($productName),
                $partCategoryId,
                $brandId,
                $basePrice + rand(-10, 10),
                $sku,
                'PN-' . rand(1000, 9999),
                rand(10, 50),
                "Premium grade $ptName replacement for $modelName. High durability and perfect fit.",
                "assets/images/products/$ptSlugPrefix.webp",
                'Ultra Premium',
                '12 Months',
                $modelName,
                '[]'
            ]);
        }
    }

    echo "FRESH 4-TIER SEEDING SUCCESSFUL.\n";
    echo "Brands: 3\nModels: " . count($models) . "\nPart Categories: " . (count($models) * count($partTypes)) . "\nProducts: " . (count($models) * count($partTypes));
} catch (Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage();
}
