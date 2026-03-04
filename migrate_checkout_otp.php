<?php
require_once 'config/db.php';

echo "<h2>Running Checkout OTP Migration...</h2>";

try {
    // 1. Add new columns for OTP
    $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS order_otp VARCHAR(6) NULL AFTER status");
    echo "✅ Added order_otp column<br>";

    $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS otp_expiry DATETIME NULL AFTER order_otp");
    echo "✅ Added otp_expiry column<br>";

    // 2. Update status ENUM to include new statuses
    // First check current enum values
    $stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE 'status'");
    $column = $stmt->fetch();
    
    if ($column && strpos($column['Type'], 'pending_otp') === false) {
        // Drop and recreate enum with new values
        $pdo->exec("ALTER TABLE orders MODIFY status ENUM('pending', 'pending_otp', 'pending_payment', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending'");
        echo "✅ Updated status ENUM with new values<br>";
    } else {
        echo "ℹ️ Status ENUM already has new values<br>";
    }

    // 3. Add payment_status if not exists
    $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid' AFTER status");
    echo "✅ Added payment_status column<br>";

    // 4. Add payment_method if not exists
    $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) NULL AFTER payment_status");
    echo "✅ Added payment_method column<br>";

    echo "<br><h3 style='color:green;'>Migration completed successfully!</h3>";
    echo "<p><a href='checkout.php'>Go to Checkout</a></p>";
} catch (PDOException $e) {
    echo "<br><h3 style='color:red;'>Migration failed!</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>


