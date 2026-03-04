<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketing Material - VisionPro</title>
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

    <main class="container mx-auto px-4 py-12">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold mb-4">Marketing Assets</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Download official VisionPro logos, product images, and branding guidelines for use in your shop or online store.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Asset Card 1 -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center group hover:-translate-y-1 transition-transform">
                <div class="w-20 h-20 bg-primary-50 rounded-2xl flex items-center justify-center mx-auto mb-6 text-4xl">
                    📁
                </div>
                <h3 class="font-bold text-lg mb-2">Logo Pack</h3>
                <p class="text-sm text-gray-500 mb-6">Includes PNG, SVG, and EPS formats for light and dark backgrounds.</p>
                <button class="w-full py-2 border border-gray-200 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition-colors">Download .ZIP</button>
            </div>

            <!-- Asset Card 2 -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center group hover:-translate-y-1 transition-transform">
                <div class="w-20 h-20 bg-primary-50 rounded-2xl flex items-center justify-center mx-auto mb-6 text-4xl">
                    📱
                </div>
                <h3 class="font-bold text-lg mb-2">Product Images</h3>
                <p class="text-sm text-gray-500 mb-6">High-resolution shots of our premium LCD screens and packaging.</p>
                <button class="w-full py-2 border border-gray-200 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition-colors">By Request Only</button>
            </div>

            <!-- Asset Card 3 -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center group hover:-translate-y-1 transition-transform">
                <div class="w-20 h-20 bg-primary-50 rounded-2xl flex items-center justify-center mx-auto mb-6 text-4xl">
                    📐
                </div>
                <h3 class="font-bold text-lg mb-2">Brand Guidelines</h3>
                <p class="text-sm text-gray-500 mb-6">PDF guide on color codes, typography, and logo usage rules.</p>
                <button class="w-full py-2 border border-gray-200 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition-colors">View PDF</button>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>


