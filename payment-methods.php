<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Methods - VisionPro</title>
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
        <h1 class="text-3xl font-bold mb-8">Payment Methods</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Credit / Debit Cards
                </h2>
                <p class="text-gray-600 mb-4">We accept all major credit and debit cards secured by Clover.</p>
                <div class="flex gap-4">
                    <span class="bg-gray-100 px-3 py-1 rounded font-bold text-xs text-gray-500">VISA</span>
                    <span class="bg-gray-100 px-3 py-1 rounded font-bold text-xs text-gray-500">MasterCard</span>
                    <span class="bg-gray-100 px-3 py-1 rounded font-bold text-xs text-gray-500">Amex</span>
                </div>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Interac E-Transfer
                </h2>
                <p class="text-gray-600 mb-4">For Canadian customers, we accept Interac E-Transfer.</p>
                <p class="text-sm bg-primary-50 p-3 rounded text-primary-700">Send to: <strong>payments@visionprolcd.com</strong><br>Auto-Deposit enabled.</p>
            </div>
            
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Bank Wire Transfer
                </h2>
                <p class="text-gray-600 mb-4">Accepted for wholesale orders over $5,000.</p>
                <p class="text-sm text-gray-500">Please contact support for wiring instructions.</p>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

