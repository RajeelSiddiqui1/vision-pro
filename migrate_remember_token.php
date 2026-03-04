<?php
session_start();
require_once 'config/db.php';

// Add remember_token and token_expiry columns to users table if they don't exist
$stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'remember_token'");
if ($stmt->rowCount() === 0) {
    $pdo->exec("ALTER TABLE users ADD COLUMN remember_token VARCHAR(100) AFTER password");
    $pdo->exec("ALTER TABLE users ADD COLUMN token_expiry DATETIME AFTER remember_token");
    echo "remember_token and token_expiry columns added successfully!";
} else {
    echo "Columns already exist.";
}
?>


