<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - VisionPro</title>
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
        <h1 class="text-3xl font-bold mb-8">Terms and Conditions</h1>
        
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6 text-gray-700">
            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">1. Acceptance of Terms</h2>
                <p>By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">2. Use License</h2>
                <p>Permission is granted to temporarily download one copy of the materials (information or software) on VisionPro's website for personal, non-commercial transitory viewing only.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">3. Disclaimer</h2>
                <p>The materials on VisionPro's website are provided "as is". VisionPro makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">4. Intellectual Property</h2>
                <p>All content included on this site, such as text, graphics, logos, button icons, images, is the property of VisionPro or its content suppliers and protected by international copyright laws.</p>
            </section>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

