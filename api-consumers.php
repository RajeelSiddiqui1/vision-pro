<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API for Developers - VisionPro</title>
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

    <main class="container mx-auto px-4 py-24">
        <div class="max-w-4xl mx-auto text-center mb-16">
            <span class="text-primary-600 font-bold tracking-widest uppercase text-sm font-mono">VisionPro API v1</span>
            <h1 class="text-4xl md:text-5xl font-bold mt-4 mb-6">Automate Your Inventory</h1>
            <p class="text-xl text-gray-500">Connect your repair shop POS or e-commerce store directly to our inventory for real-time pricing and stock levels.</p>
        </div>

        <div class="bg-gray-900 rounded-3xl p-8 md:p-12 text-white relative overflow-hidden max-w-5xl mx-auto shadow-2xl">
            <!-- Code Decoration -->
            <div class="absolute top-0 right-0 p-4 opacity-20 font-mono text-sm hidden md:block">
                GET /v1/products/sku/12390<br>
                { "stock": 450, "price": 22.50 }
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 relative z-10">
                <div>
                     <h2 class="text-2xl font-bold mb-4">Request Access</h2>
                     <p class="text-gray-400 mb-8">API access is available to registered wholesale partners with a minimum monthly spend of $5,000.</p>
                     
                     <form class="space-y-4">
                         <input type="text" placeholder="API Project Name" class="w-full bg-gray-800 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 text-white">
                         <button class="w-full btn-primary py-3 rounded-xl">Generate API Keys</button>
                     </form>
                </div>
                <div class="space-y-6">
                    <h3 class="font-bold text-gray-400 uppercase text-xs tracking-widest">Available Endpoints</h3>
                    <ul class="space-y-4 font-mono text-sm">
                        <li class="flex items-center gap-3">
                            <span class="text-green-400">GET</span> 
                            <span>/products</span>
                            <span class="text-gray-500 text-xs ml-auto">List all items</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-green-400">GET</span> 
                            <span>/inventory/{sku}</span>
                            <span class="text-gray-500 text-xs ml-auto">Real-time stock</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-orange-400">POST</span> 
                            <span>/orders</span>
                            <span class="text-gray-500 text-xs ml-auto">Create order</span>
                        </li>
                    </ul>
                    <div class="mt-8 pt-6 border-t border-gray-800">
                        <a href="#" class="text-primary-400 hover:text-white transition-colors flex items-center gap-2 text-sm font-bold">
                            View Documentation 
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>


