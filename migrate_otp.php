<?php
// migrate_otp.php
require_once 'config/db.php';

try {
    $pdo->exec("ALTER TABLE users 
                ADD COLUMN reset_otp VARCHAR(6) NULL AFTER password,
                ADD COLUMN otp_expiry DATETIME NULL AFTER reset_otp");
    echo "<h1>OTP Migration Successful!</h1>";
} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage());
}
?>
