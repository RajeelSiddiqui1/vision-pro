<?php
require_once 'config/db.php';

echo "<h1>System Connectivity Diagnostic</h1>";

try {
    $stmt = $pdo->query("SELECT VERSION()");
    $version = $stmt->fetchColumn();
    echo "<p style='color: green;'>✅ Successfully connected to Database!</p>";
    echo "<p>MySQL Version: " . $version . "</p>";
    
    // Check if tables exist
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (empty($tables)) {
        echo "<p style='color: orange;'>⚠️ Database 'visionpro_db' is connected but it is EMPTY. No tables found.</p>";
        echo "<p>Please visit <a href='fix-database.php?secret=visionheal123'>fix-database.php</a> to initialize your schema.</p>";
    } else {
        echo "<p>✅ Found " . count($tables) . " tables.</p>";
        foreach($tables as $table) {
            echo "- $table<br>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Connection Failed: " . $e->getMessage() . "</p>";
    echo "<h3>Configuration Used:</h3>";
    echo "Host: " . DB_HOST . "<br>";
    echo "User: " . DB_USER . "<br>";
    echo "DB: " . DB_NAME . "<br>";
}
?>
