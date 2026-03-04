<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frequently Asked Questions - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-12 max-w-4xl">
        <h1 class="text-3xl font-bold mb-8 text-center">Frequently Asked Questions</h1>
        
        <div class="space-y-4">
            <!-- FAQ Item 1 -->
            <details class="group bg-white rounded-2xl border border-gray-100 open:ring-2 open:ring-primary-100 transition-all">
                <summary class="flex justify-between items-center cursor-pointer p-6 font-bold text-gray-900">
                    What payment methods do you accept?
                    <span class="transform group-open:rotate-180 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </summary>
                <div class="px-6 pb-6 text-gray-600">
                    We accept all major credit cards (Visa, MasterCard, Amex) via Clover, as well as Direct Bank Transfer and PayPal for select wholesale partners.
                </div>
            </details>

            <!-- FAQ Item 2 -->
            <details class="group bg-white rounded-2xl border border-gray-100 open:ring-2 open:ring-primary-100 transition-all">
                <summary class="flex justify-between items-center cursor-pointer p-6 font-bold text-gray-900">
                    Do you ship internationally?
                    <span class="transform group-open:rotate-180 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </summary>
                <div class="px-6 pb-6 text-gray-600">
                    Yes, we ship to most countries. Shipping rates and delivery times vary by location. Please check our Shipping Policy page for more details.
                </div>
            </details>

            <!-- FAQ Item 3 -->
            <details class="group bg-white rounded-2xl border border-gray-100 open:ring-2 open:ring-primary-100 transition-all">
                <summary class="flex justify-between items-center cursor-pointer p-6 font-bold text-gray-900">
                    What is your warranty on LCDs?
                    <span class="transform group-open:rotate-180 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </summary>
                <div class="px-6 pb-6 text-gray-600">
                    We offer a Lifetime Warranty on manufacturer defects for our Premium OEM series screens. Standard Aftermarket screens come with a 1-year warranty.
                </div>
            </details>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

