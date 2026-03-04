<?php
/**
 * VisionPro - Database Setup Script
 * Hostinger Deployment
 * 
 * Run this file once to set up the database
 * Access: https://visionprorefurbishing.com/setup_hostinger.php
 */

// Load environment
require_once __DIR__ . '/config/env.php';

$message = '';
$error = '';

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_database'])) {
    try {
        // Get database credentials from .env
        $host = env('DB_HOST', 'localhost');
        $user = env('DB_USERNAME', 'u517319487_visionpro');
        $pass = env('DB_PASSWORD', 'Azadar@3311');
        $dbname = env('DB_DATABASE', 'u517319487_vi');
        
        // Connect without database
        $pdo = new PDO("mysql:host=$host", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbname`");
        
        // Read and execute schema
        $schema = file_get_contents(__DIR__ . '/database_schema.sql');
        
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $schema)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && strpos($statement, '--') !== 0 && strpos($statement, '/*') !== 0) {
                try {
                    $pdo->exec($statement);
                } catch (Exception $e) {
                    // Ignore duplicate key errors
                    if (strpos($e->getMessage(), 'Duplicate') === false) {
                        throw $e;
                    }
                }
            }
        }
        
        $message = "Database setup completed successfully!";
        
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisionPro - Database Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">VisionPro Database Setup</h1>
                <p class="text-gray-600 mt-2">Hostinger Deployment</p>
            </div>
            
            <?php if ($message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <div class="text-center">
                    <a href="index.php" class="inline-block bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700">
                        Go to Website
                    </a>
                </div>
            <?php elseif ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <form method="POST">
                    <button type="submit" name="setup_database" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">
                        Try Again
                    </button>
                </form>
            <?php else: ?>
                <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded mb-4">
                    <p class="font-semibold">Important:</p>
                    <ul class="list-disc list-inside mt-2 text-sm">
                        <li>Make sure .env file is configured</li>
                        <li>Database credentials must be correct</li>
                        <li>Run this only once</li>
                    </ul>
                </div>
                <form method="POST">
                    <button type="submit" name="setup_database" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">
                        Setup Database
                    </button>
                </form>
            <?php endif; ?>
            
            <div class="mt-6 text-center text-sm text-gray-500">
                <p>After setup, delete this file for security</p>
            </div>
        </div>
    </div>
</body>
</html>


