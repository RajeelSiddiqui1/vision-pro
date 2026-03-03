<?php
session_start();
require_once 'config/db.php';

$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Categories - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="assets/images/visionpro-logo.jpeg">
</head>
<body class="bg-gray-50 group/body">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-16">
        <header class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Product Categories</h1>
            <p class="text-gray-500 max-w-2xl mx-auto">Explore our extensive range of high-quality mobile parts and refurbishing supplies, organized by category for your convenience.</p>
        </header>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php foreach($categories as $cat): ?>
            <a href="products.php?category=<?= $cat['id'] ?>" class="group bg-white rounded-3xl p-8 border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                <div class="h-48 rounded-2xl bg-gray-50 mb-6 overflow-hidden flex items-center justify-center">
                    <?php if($cat['image_url']): ?>
                        <img src="<?= $cat['image_url'] ?>" alt="<?= $cat['name'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <?php else: ?>
                        <span class="text-6xl opacity-20">📦</span>
                    <?php endif; ?>
                </div>
                <h3 class="text-xl font-bold text-gray-900 group-hover:text-primary-600 transition-colors mb-2"><?= $cat['name'] ?></h3>
                <p class="text-sm text-gray-500">View all products in this category →</p>
            </a>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
