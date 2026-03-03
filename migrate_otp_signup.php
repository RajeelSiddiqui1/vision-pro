<?php
session_start();
require_once 'config/db.php';

echo "Starting OTP migration...<br>";

// Add is_verified column if not exists
$stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'is_verified'");
if ($stmt->rowCount() === 0) {
    $pdo->exec("ALTER TABLE users ADD COLUMN is_verified TINYINT(1) DEFAULT 1 AFTER created_at");
    echo "✓ Added is_verified column<br>";
} else {
    echo "✓ is_verified column already exists<br>";
}

// Add order_otp column if not exists
$stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'order_otp'");
if ($stmt->rowCount() === 0) {
    $pdo->exec("ALTER TABLE users ADD COLUMN order_otp VARCHAR(10) AFTER is_verified");
    echo "✓ Added order_otp column<br>";
} else {
    echo "✓ order_otp column already exists<br>";
}

// Add otp_expiry column if not exists
$stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'otp_expiry'");
if ($stmt->rowCount() === 0) {
    $pdo->exec("ALTER TABLE users ADD COLUMN otp_expiry DATETIME AFTER order_otp");
    echo "✓ Added otp_expiry column<br>";
} else {
    echo "✓ otp_expiry column already exists<br>";
}

// Add remember_token column if not exists
$stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'remember_token'");
if ($stmt->rowCount() === 0) {
    $pdo->exec("ALTER TABLE users ADD COLUMN remember_token VARCHAR(100) AFTER otp_expiry");
    echo "✓ Added remember_token column<br>";
} else {
    echo "✓ remember_token column already exists<br>";
}

// Add token_expiry column if not exists
$stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'token_expiry'");
if ($stmt->rowCount() === 0) {
    $pdo->exec("ALTER TABLE users ADD COLUMN token_expiry DATETIME AFTER remember_token");
    echo "✓ Added token_expiry column<br>";
} else {
    echo "✓ token_expiry column already exists<br>";
}

echo "<br>Migration complete!";
?>
