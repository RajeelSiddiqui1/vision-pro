<?php
require_once 'config/db.php';
try {
    $pdo->exec("ALTER TABLE products ADD COLUMN type ENUM('product', 'accessory') DEFAULT 'product'");
    echo "Successfully added type column to products table.\\n";
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') { // Duplicate column
        echo "Column type already exists.\\n";
    } else {
        echo "Error: " . $e->getMessage() . "\\n";
    }
}
?>
