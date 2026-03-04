<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-Owned Devices - VisionPro</title>
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

    <main>
        <!-- Banner -->
        <section class="bg-gray-900 text-white py-16">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-4xl font-bold mb-4">Pre-Owned Devices</h1>
                <p class="text-gray-400">Strictly graded, fully tested used smartphones for resale.</p>
            </div>
        </section>

        <div class="container mx-auto px-4 py-12 flex flex-col md:flex-row gap-8">
            <!-- Sidebar -->
            <aside class="w-full md:w-64 space-y-8">
                <div>
                    <h3 class="font-bold text-gray-900 mb-4">Grade</h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2"><input type="checkbox" checked class="rounded text-primary-600"> Grade A+ (Pristine)</label>
                        <label class="flex items-center gap-2"><input type="checkbox" class="rounded text-primary-600"> Grade A (Near Mint)</label>
                        <label class="flex items-center gap-2"><input type="checkbox" class="rounded text-primary-600"> Grade B (Minor Wear)</label>
                    </div>
                </div>
                 <div>
                    <h3 class="font-bold text-gray-900 mb-4">Brand</h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2"><input type="checkbox" checked class="rounded text-primary-600"> Apple</label>
                        <label class="flex items-center gap-2"><input type="checkbox" class="rounded text-primary-600"> Samsung</label>
                        <label class="flex items-center gap-2"><input type="checkbox" class="rounded text-primary-600"> Google</label>
                    </div>
                </div>
            </aside>

            <!-- Grid -->
            <div class="flex-1">
                <div class="bg-yellow-50 border border-yellow-100 text-yellow-800 p-4 rounded-xl mb-8 flex items-center gap-3">
                    <span class="text-2xl">🚧</span>
                    <div>
                        <span class="font-bold">Inventory Syncing...</span>
                        <p class="text-sm">Live pre-owned inventory is being updated. Please check back later or contact sales for a stock list.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Placeholder Item -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-4 opacity-75">
                        <div class="h-48 bg-gray-100 rounded-xl mb-4"></div>
                        <div class="h-6 w-3/4 bg-gray-200 rounded mb-2"></div>
                        <div class="h-4 w-1/2 bg-gray-200 rounded"></div>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-100 p-4 opacity-75">
                        <div class="h-48 bg-gray-100 rounded-xl mb-4"></div>
                        <div class="h-6 w-3/4 bg-gray-200 rounded mb-2"></div>
                        <div class="h-4 w-1/2 bg-gray-200 rounded"></div>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-100 p-4 opacity-75">
                        <div class="h-48 bg-gray-100 rounded-xl mb-4"></div>
                        <div class="h-6 w-3/4 bg-gray-200 rounded mb-2"></div>
                        <div class="h-4 w-1/2 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

