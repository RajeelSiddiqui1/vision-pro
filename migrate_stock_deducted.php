<?php
session_start();
require_once 'config/db.php';

// Add stock_deducted column to orders table if it doesn't exist
$stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE 'stock_deducted'");
if ($stmt->rowCount() === 0) {
    $pdo->exec("ALTER TABLE orders ADD COLUMN stock_deducted BOOLEAN DEFAULT FALSE AFTER created_at");
    echo "stock_deducted column added successfully!";
} else {
    echo "stock_deducted column already exists.";
}

// Also add order_otp and otp_expiry columns if they don't exist (for OTP verification flow)
$stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE 'order_otp'");
if ($stmt->rowCount() === 0) {
    $pdo->exec("ALTER TABLE orders ADD COLUMN order_otp VARCHAR(10) AFTER stock_deducted");
    $pdo->exec("ALTER TABLE orders ADD COLUMN otp_expiry DATETIME AFTER order_otp");
    echo "\norder_otp and otp_expiry columns added successfully!";
} else {
    echo "\norder_otp and otp_expiry columns already exist.";
}
?>


