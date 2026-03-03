<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - VisionPro</title>
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

    <!-- Hero -->
    <section class="bg-primary-900 text-white py-24 relative overflow-hidden">
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-5xl font-bold mb-6">More Than Just Parts</h1>
            <p class="text-xl text-primary-100 max-w-2xl mx-auto">We offer a suite of services designed to help your repair business grow and succeed.</p>
        </div>
    </section>

    <!-- Services Grid -->
    <section class="py-20">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- LCD Buyback -->
                <a href="lcd-buyback.php" class="group bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-primary-200 transition-all hover:shadow-lg">
                    <div class="w-14 h-14 bg-green-100 text-green-600 rounded-xl flex items-center justify-center text-2xl mb-6 group-hover:bg-green-600 group-hover:text-white transition-colors">♻️</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">LCD Buyback Program</h3>
                    <p class="text-gray-500 mb-4">Turn your broken screens into cash or store credit. Best market rates guaranteed.</p>
                    <span class="text-primary-600 font-bold text-sm">Learn More →</span>
                </a>

                <!-- Pre-Owned Grading -->
                <a href="pre-owned.php" class="group bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-primary-200 transition-all hover:shadow-lg">
                    <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-2xl mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors">📱</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Pre-Owned Devices</h3>
                    <p class="text-gray-500 mb-4">Strictly graded used phones. From pristine Grade A+ to cost-effective Grade C.</p>
                    <span class="text-primary-600 font-bold text-sm">View Inventory →</span>
                </a>

                <!-- Marketing -->
                <a href="marketing-material.php" class="group bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-primary-200 transition-all hover:shadow-lg">
                    <div class="w-14 h-14 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center text-2xl mb-6 group-hover:bg-purple-600 group-hover:text-white transition-colors">📢</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Marketing Support</h3>
                    <p class="text-gray-500 mb-4">Download posters, digital assets, and branding materials for your shop.</p>
                    <span class="text-primary-600 font-bold text-sm">Get Assets →</span>
                </a>

                 <!-- API Integration -->
                <a href="api-consumers.php" class="group bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-primary-200 transition-all hover:shadow-lg">
                    <div class="w-14 h-14 bg-gray-100 text-gray-600 rounded-xl flex items-center justify-center text-2xl mb-6 group-hover:bg-gray-800 group-hover:text-white transition-colors">🔌</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">API Integration</h3>
                    <p class="text-gray-500 mb-4">Connect your POS directly to our inventory for automated ordering.</p>
                    <span class="text-primary-600 font-bold text-sm">Developer Docs →</span>
                </a>

                 <!-- Repair Services -->
                <a href="repair.php" class="group bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-primary-200 transition-all hover:shadow-lg">
                    <div class="w-14 h-14 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center text-2xl mb-6 group-hover:bg-orange-600 group-hover:text-white transition-colors">🔧</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Book a Repair</h3>
                    <p class="text-gray-500 mb-4">Schedule a professional repair for your device. Screen, battery, and more.</p>
                    <span class="text-primary-600 font-bold text-sm">Book Now →</span>
                </a>

            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
