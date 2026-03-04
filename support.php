<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Center - VisionPro</title>
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

    <main class="container mx-auto px-4 py-12 text-center">
        <h1 class="text-4xl font-bold mb-4">How can we help?</h1>
        <p class="text-gray-500 mb-12 max-w-xl mx-auto">Our support team is available Monday to Friday, 10 AM - 7 PM EST.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
            
            <!-- Phone -->
            <a href="tel:+16474026699" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-primary-200 hover:-translate-y-1 transition-all">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-6">📞</div>
                <h3 class="font-bold text-lg mb-2">Call Us</h3>
                <p class="text-gray-500 text-sm">Direct line for urgent inquiries & ordering.</p>
                <div class="mt-4 font-bold text-primary-600">647-402-6699</div>
            </a>

            <!-- WhatsApp -->
            <a href="https://wa.me/16474026699" target="_blank" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-green-200 hover:-translate-y-1 transition-all">
                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-6">💬</div>
                <h3 class="font-bold text-lg mb-2">WhatsApp</h3>
                <p class="text-gray-500 text-sm">Quick chat for photos and stock checks.</p>
                <div class="mt-4 font-bold text-green-600">Chat Now</div>
            </a>

            <!-- Email -->
            <a href="mailto:info@visionprolcd.com" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-orange-200 hover:-translate-y-1 transition-all">
                <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-6">✉️</div>
                <h3 class="font-bold text-lg mb-2">Email</h3>
                <p class="text-gray-500 text-sm">For RMAs, invoices, and general questions.</p>
                <div class="mt-4 font-bold text-orange-600">info@visionprolcd.com</div>
            </a>

            <!-- Live Chat (Simulation) -->
            <div onclick="alert('Live Chat widget would open here.')" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-purple-200 hover:-translate-y-1 transition-all cursor-pointer">
                <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-6">🤖</div>
                <h3 class="font-bold text-lg mb-2">Live Chat</h3>
                <p class="text-gray-500 text-sm">Chat with our bot or a live agent.</p>
                <div class="mt-4 font-bold text-purple-600">Start Chat</div>
            </div>

        </div>

        <section class="mt-20">
            <h2 class="text-2xl font-bold mb-8">Common Topics</h2>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="return-policy.php" class="px-6 py-3 bg-white rounded-full border border-gray-200 font-bold text-gray-700 hover:bg-gray-50 transition-colors">Return Policy</a>
                <a href="shipping.php" class="px-6 py-3 bg-white rounded-full border border-gray-200 font-bold text-gray-700 hover:bg-gray-50 transition-colors">Shipping Rates</a>
                <a href="lcd-buyback.php" class="px-6 py-3 bg-white rounded-full border border-gray-200 font-bold text-gray-700 hover:bg-gray-50 transition-colors">LCD Buyback</a>
                <a href="quality-standards.php" class="px-6 py-3 bg-white rounded-full border border-gray-200 font-bold text-gray-700 hover:bg-gray-50 transition-colors">Quality Grades</a>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

