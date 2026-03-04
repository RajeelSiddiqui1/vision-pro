<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Brands - VisionPro</title>
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
        <h1 class="text-4xl font-bold text-center mb-16">Our Proprietary Brands</h1>
        
        <div class="space-y-24">
            
            <!-- XO7 -->
            <div class="flex flex-col lg:flex-row gap-12 items-center">
                <div class="lg:w-1/2">
                    <div class="bg-blue-600 rounded-3xl h-96 flex items-center justify-center shadow-xl">
                        <span class="text-white text-6xl font-black tracking-tighter">XO7</span>
                    </div>
                </div>
                <div class="lg:w-1/2">
                    <span class="text-blue-600 font-bold uppercase tracking-widest text-sm">Premium Aftermarket</span>
                    <h2 class="text-4xl font-bold mb-6 mt-2">XO7 Technology</h2>
                    <p class="text-gray-600 text-lg leading-relaxed mb-8">
                        Our flagship aftermarket display assembly. XO7 screens are engineered to match OEM specifications in brightness, color gamut, and touch sensitivity, but at a fraction of the cost.
                    </p>
                    <a href="products.php?search=xo7" class="btn-primary inline-block px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold">Shop XO7 Screens</a>
                </div>
            </div>

            <!-- AQ7 -->
            <div class="flex flex-col lg:flex-row-reverse gap-12 items-center">
                <div class="lg:w-1/2">
                    <div class="bg-purple-600 rounded-3xl h-96 flex items-center justify-center shadow-xl">
                        <span class="text-white text-6xl font-black tracking-tighter">AQ7</span>
                    </div>
                </div>
                <div class="lg:w-1/2">
                    <span class="text-purple-600 font-bold uppercase tracking-widest text-sm">Advanced Quality</span>
                    <h2 class="text-4xl font-bold mb-6 mt-2">AQ7 Series</h2>
                    <p class="text-gray-600 text-lg leading-relaxed mb-8">
                        The perfect balance of performance and value. AQ7 screens utilize advanced In-Cell technology to ensure thinness and responsiveness comparable to original screens.
                    </p>
                    <a href="products.php?search=aq7" class="btn-primary inline-block px-8 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold">Shop AQ7 Screens</a>
                </div>
            </div>

            <!-- ScrewBox -->
            <div class="flex flex-col lg:flex-row gap-12 items-center">
                <div class="lg:w-1/2">
                    <div class="bg-gray-800 rounded-3xl h-96 flex items-center justify-center shadow-xl">
                        <span class="text-white text-5xl font-black tracking-tighter">ScrewBox 2.0</span>
                    </div>
                </div>
                <div class="lg:w-1/2">
                    <span class="text-gray-600 font-bold uppercase tracking-widest text-sm">Organization</span>
                    <h2 class="text-4xl font-bold mb-6 mt-2">ScrewBox 2.0</h2>
                    <p class="text-gray-600 text-lg leading-relaxed mb-8">
                        Never lose a screw again. Our magnetic screw organizers are mapped for every specific device model, speeding up repairs and reducing errors.
                    </p>
                    <a href="products.php?search=screwbox" class="btn-primary inline-block px-8 py-3 rounded-xl bg-gray-800 hover:bg-gray-900 text-white font-bold">Shop Accessories</a>
                </div>
            </div>

        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>


