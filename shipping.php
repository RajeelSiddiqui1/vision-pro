<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Policy - VisionPro</title>
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
        <h1 class="text-3xl font-bold mb-8">Shipping Policy</h1>
        
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6 text-gray-700">
            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">1. Shipping Methods</h2>
                <p>We offer various shipping carriers including FedEx, UPS, and Canada Post. The available options will be shown at checkout based on your location.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">2. Processing Time</h2>
                <p>Orders are typically processed within 1-2 business days. Orders placed before 2 PM EST usually ship the same day.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">3. International Shipping</h2>
                <p>We ship internationally. Please note that customs duties and taxes are the responsibility of the recipient.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">4. Free Shipping</h2>
                <p>Free standard shipping is available for wholesale orders over $1,500 within Canada.</p>
            </section>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>


