<?php
require 'config/db.php';
$stmt = $pdo->query("SELECT id, name, parent_id FROM categories ORDER BY parent_id, id");
$cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($cats as $c) {
    if ($c['parent_id'] === null) {
        echo "L1 (Series): " . $c['name'] . " (" . $c['id'] . ")\n";
        foreach ($cats as $sub) {
            if ($sub['parent_id'] == $c['id']) {
                echo "  L2 (Model): " . $sub['name'] . " (" . $sub['id'] . ")\n";
                foreach ($cats as $part) {
                    if ($part['parent_id'] == $sub['id']) {
                        echo "    L3 (Part): " . $part['name'] . " (" . $part['id'] . ")\n";
                    }
                }
            }
        }
    }
}
