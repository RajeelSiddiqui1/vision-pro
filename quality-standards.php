<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality Standards - VisionPro</title>
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
        <h1 class="text-4xl font-bold text-center mb-16">Our Quality Standards</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-5xl mx-auto">
            
            <!-- Quality Type 1: OEM -->
            <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100">
                <div class="h-48 bg-gray-900 flex items-center justify-center">
                    <span class="text-white text-3xl font-bold border-4 border-white px-6 py-2">OEM</span>
                </div>
                <div class="p-8">
                    <h2 class="text-2xl font-bold mb-4">Genuine OEM / Pulls</h2>
                    <p class="text-gray-600 mb-6">Original screens directly from new or disassembled devices. These offer the exact factory brightness, color calibration, and touch sensitivity.</p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Original Retina/OLED Panel</li>
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Original Flex Cables</li>
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Lifetime Warranty</li>
                    </ul>
                </div>
            </div>

            <!-- Quality Type 2: Aftermarket -->
            <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100">
                <div class="h-48 bg-blue-600 flex items-center justify-center">
                    <span class="text-white text-3xl font-bold border-4 border-white px-6 py-2">XO7</span>
                </div>
                <div class="p-8">
                    <h2 class="text-2xl font-bold mb-4">Select Aftermarket (XO7)</h2>
                    <p class="text-gray-600 mb-6">Our premium aftermarket line. Engineered to match OEM specs as closely as possible at a significantly lower price point.</p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> High Brightness & Color Gamut</li>
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Reinforced Glass</li>
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> 1-Year Warranty</li>
                    </ul>
                </div>
            </div>

            <!-- Quality Type 3: Incell -->
            <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100">
                <div class="h-48 bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-500 text-3xl font-bold border-4 border-gray-500 px-6 py-2">INCELL</span>
                </div>
                <div class="p-8">
                    <h2 class="text-2xl font-bold mb-4">Value Incell</h2>
                    <p class="text-gray-600 mb-6">Cost-effective solution for budget repairs. Good functionality but may have slightly thicker bezels or lower max brightness than OEM.</p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Touch Integrated in LCD</li>
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Great Price/Performance</li>
                        <li class="flex items-center gap-2"><span class="text-green-500">✓</span> 6-Month Warranty</li>
                    </ul>
                </div>
            </div>

        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>


