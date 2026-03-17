<?php
/**
 * Add sample products with brands for testing
 */

require_once 'config/db.php';

echo "Adding sample products with brands...<br><br>";

// Get category IDs
$categories = $pdo->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_KEY_PAIR);
$brands = $pdo->query("SELECT id, name FROM brands")->fetchAll(PDO::FETCH_KEY_PAIR);

if (empty($categories)) {
    echo "No categories found. Please run the main database setup first.<br>";
    exit;
}

if (empty($brands)) {
    echo "No brands found. Please run migrate_brands.php first.<br>";
    exit;
}

// Sample products data
$products = [
    // iPhone Screens (category 1)
    ['iPhone 11 Pro Max Screen - XO7 Premium', 1, 1, 89.99, 79.99, 'IP11PM-XO7-001', 50, 'Premium quality OLED screen with frame', 'Premium', '1 Year', 'iPhone 11 Pro Max'],
    ['iPhone 12 Screen - XO7 Premium', 1, 1, 119.99, 99.99, 'IP12-XO7-001', 30, 'Premium quality OLED screen', 'Premium', '1 Year', 'iPhone 12'],
    ['iPhone 13 Screen - AQ7 Original', 1, 3, 149.99, 129.99, 'IP13-AQ7-001', 25, 'Original quality OLED screen', 'Original', '1 Year', 'iPhone 13'],
    ['iPhone 14 Pro Screen - Original', 1, 2, 189.99, 169.99, 'IP14PRO-ORG-001', 20, 'Genuine Apple OLED screen', 'Original', 'Lifetime', 'iPhone 14 Pro'],
    
    // iPhone Batteries (category 2)
    ['iPhone 11 Battery - XO7', 2, 1, 35.99, 29.99, 'IP11-BAT-XO7', 100, 'High capacity battery', 'Premium', '1 Year', 'iPhone 11'],
    ['iPhone 12 Battery - Apple', 2, 2, 45.99, 39.99, 'IP12-BAT-APL', 80, 'Genuine Apple battery', 'Original', '1 Year', 'iPhone 12'],
    ['iPhone 13 Battery - AQ7', 2, 3, 39.99, 34.99, 'IP13-BAT-AQ7', 60, 'Advanced quality battery', 'Premium', '1 Year', 'iPhone 13'],
    
    // iPhone Parts (category 3)
    ['iPhone 11 Charging Port - XO7', 3, 1, 15.99, 12.99, 'IP11-CHG-XO7', 40, 'Charging port with flex cable', 'Premium', '6 Months', 'iPhone 11'],
    ['iPhone 12 Speaker - Original', 3, 2, 18.99, 15.99, 'IP12-SPK-ORG', 35, 'Genuine speaker module', 'Original', '6 Months', 'iPhone 12'],
    ['iPhone 13 Camera Lens - XO7', 3, 1, 12.99, 9.99, 'IP13-LEN-XO7', 50, 'Camera lens glass', 'Premium', '6 Months', 'iPhone 13'],
    
    // iPad Screens (category 4)
    ['iPad Pro 11 Screen - Original', 4, 2, 249.99, 219.99, 'IPDP11-ORG-001', 15, 'Genuine iPad Pro screen', 'Original', '1 Year', 'iPad Pro 11"'],
    ['iPad Air Screen - AQ7', 4, 3, 159.99, 139.99, 'IPDAIR-AQ7-001', 20, 'High quality replacement screen', 'Premium', '1 Year', 'iPad Air'],
    
    // Samsung Screens (category 5)
    ['Samsung S21 Screen - Original', 5, 2, 129.99, 109.99, 'S21-ORG-001', 25, 'Genuine Samsung OLED', 'Original', '1 Year', 'Samsung Galaxy S21'],
    ['Samsung S22 Screen - XO7', 5, 1, 99.99, 84.99, 'S22-XO7-001', 30, 'Premium AMOLED screen', 'Premium', '1 Year', 'Samsung Galaxy S22'],
    ['Samsung S23 Screen - AQ7', 5, 3, 119.99, 99.99, 'S23-AQ7-001', 22, 'Advanced quality AMOLED', 'Premium', '1 Year', 'Samsung Galaxy S23'],
];

$stmt = $pdo->prepare("INSERT INTO products (name, slug, category_id, brand_id, price, discount_price, part_number, stock_quantity, description, quality_tier, warranty, compatibility) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($products as $p) {
    $slug = strtolower(str_replace(' ', '-', $p[0]));
    try {
        $stmt->execute([$p[0], $slug, $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9], $p[10]]);
        echo "✓ Added: {$p[0]} (Category: {$categories[$p[1]]}, Brand: {$brands[$p[2]]})<br>";
    } catch (PDOException $e) {
        echo "✗ Failed to add: {$p[0]} - " . $e->getMessage() . "<br>";
    }
}

echo "<br><strong>Sample products added successfully!</strong><br>";
echo "<a href='categories.php'>Go to Categories</a>";
