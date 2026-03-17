<?php
require_once 'config/db.php';

function describe($table, $pdo) {
    echo "\nTable: $table\n";
    $stmt = $pdo->query("DESCRIBE $table");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Field: {$row['Field']} | Type: {$row['Type']}\n";
    }
}

describe('brands', $pdo);
describe('categories', $pdo);
describe('products', $pdo);
