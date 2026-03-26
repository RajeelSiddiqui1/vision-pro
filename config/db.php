<?php
// config/db.php
require_once __DIR__ . '/env.php';

define('DB_HOST', env('DB_HOST', 'db')); // 'db' is the service name in docker-compose
define('DB_USER', env('DB_USER', env('DB_USERNAME', 'root'))); 
define('DB_PASS', env('DB_PASS', env('DB_PASSWORD', 'root'))); 
define('DB_NAME', env('DB_NAME', env('DB_DATABASE', 'visionpro_db'))); 

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Use associative arrays by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // In production, don't show the full error but log it
    if (env('APP_ENV') === 'production') {
        error_log("Connection failed: " . $e->getMessage());
        die("Database Connection Error. Please contact administrator.");
    } else {
        die("Connection failed: " . $e->getMessage());
    }
}
?>


