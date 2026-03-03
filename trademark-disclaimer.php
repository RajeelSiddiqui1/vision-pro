<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trademark Disclaimer - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-12 max-w-4xl">
        <h1 class="text-3xl font-bold mb-8">Trademark Disclaimer</h1>
        
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6 text-gray-700">
            <p class="leading-relaxed">
                VisionPro LCD Refurbishing Inc. is a third-party replacement parts provider and is not affiliated with Apple Inc., Samsung Electronics Co., Ltd., or any other device manufacturers.
            </p>
            
            <p class="leading-relaxed">
                "Apple", "iPhone", "iPad", "iPod" are registered trademarks of Apple Inc.<br>
                "Samsung" and "Galaxy" are registered trademarks of Samsung Electronics Co., Ltd.
            </p>

            <p class="leading-relaxed">
                We use these trademarks only to describe our products' compatibility (e.g., "Screen for iPhone 13"). All other trademarks are the property of their respective owners.
            </p>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
