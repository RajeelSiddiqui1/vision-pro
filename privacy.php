<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - VisionPro</title>
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
        <h1 class="text-3xl font-bold mb-8">Privacy Policy</h1>
        
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6 text-gray-700">
            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">1. Information Collection</h2>
                <p>We collect information you fulfill directly to us, such as when you create an account, make a purchase, or contact support. This includes your name, email address, shipping address, and payment information.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">2. Use of Information</h2>
                <p>We use your information to process transactions, provide customer support, and improve our services. We do not sell your personal data to third parties.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">3. Data Security</h2>
                <p>We implement industry-standard security measures to protect your personal information during transmission and storage, including SSL encryption.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">4. Cookies</h2>
                <p>We use cookies to enhance your browsing experience and analyze site traffic. You can choose to disable cookies through your browser settings, though some site features may not function properly.</p>
            </section>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>


