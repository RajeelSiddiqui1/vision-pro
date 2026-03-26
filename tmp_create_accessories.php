<?php
function processFile($source, $target, $isUserFacing = false) {
    $content = file_get_contents($source);
    
    // Replace visual strings - Plural first!
    $content = str_replace('Products', 'Accessories', $content);
    $content = str_replace('products', 'accessories', $content);
    $content = str_replace('Product', 'Accessory', $content);
    $content = str_replace('product', 'accessory', $content);
    
    // Fix database table and column names that got replaced
    $content = str_replace('FROM accessories', 'FROM products', $content);
    $content = str_replace('INTO accessories', 'INTO products', $content);
    $content = str_replace('UPDATE accessories', 'UPDATE products', $content);
    $content = str_replace('DELETE FROM accessories', 'DELETE FROM products', $content);
    
    // Specific fixes for admin-accessories.php
    if (strpos($target, 'admin-accessories.php') !== false) {
        $content = str_replace('$where_clauses = [];', '$where_clauses = ["p.type = \'accessory\'"];', $content);
        $content = preg_replace('/\$where_clauses = \["p\.type = \'accessory\'"\];/', '$where_clauses = ["p.type = \'accessory\'"];', $content); // prevent double if run twice
        // Also fix the count query which might have "FROM products" or "FROM accessories"
        $content = str_replace('FROM products p $where_sql', 'FROM products p $where_sql', $content);
    }
    
    // Specific fixes for admin-accessory-add.php
    if (strpos($target, 'admin-accessory-add.php') !== false) {
        // Find INSERT query and inject type if needed, but since it's hard, let's just do a string replace on the INSERT statement
        $content = str_replace('(`category_id`,', '(`type`, `category_id`,', $content);
        $content = str_replace('VALUES (?,', 'VALUES (\'accessory\', ?,', $content);
    }
    
    // Specific fixes for admin-accessory-edit.php
    // No special DB changes needed, it targets by ID. Maybe ensure if it uses 'product' somewhere it still works.
    
    // Specific fixes for users
    if (strpos($target, 'accessories.php') !== false) {
        $content = str_replace('$where_clauses = ["p.is_active = 1"];', '$where_clauses = ["p.is_active = 1", "p.type = \'accessory\'"];', $content);
    }
    
    if (strpos($target, 'accessory-detail.php') !== false) {
        $content = str_replace('WHERE p.slug = ? AND p.is_active = 1', 'WHERE p.slug = ? AND p.is_active = 1 AND p.type = \'accessory\'', $content);
    }
    
    file_put_contents($target, $content);
    echo "Created $target\\n";
}

// 1. admin-accessories.php
processFile('admin-products.php', 'admin-accessories.php');

// 2. admin-accessory-add.php
processFile('admin-product-add.php', 'admin-accessory-add.php');

// 3. admin-accessory-edit.php
processFile('admin-product-edit.php', 'admin-accessory-edit.php');

// 4. accessories.php
processFile('products.php', 'accessories.php');

// 5. accessory-detail.php
processFile('product-detail.php', 'accessory-detail.php');

?>
