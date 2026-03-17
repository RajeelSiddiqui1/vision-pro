<?php
require_once 'config/db.php';

$tables = ['brands', 'categories', 'products'];
$schema = [];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("DESCRIBE $table");
        $schema[$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $schema[$table] = "Error: " . $e->getMessage();
    }
}

file_put_contents('tmp_full_schema.json', json_encode($schema, JSON_PRETTY_PRINT));
echo "Schema dumped to tmp_full_schema.json";
