<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Policy - VisionPro</title>
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
        <h1 class="text-3xl font-bold mb-8">Return Policy</h1>
        
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6 text-gray-700">
            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">1. Return Eligibility</h2>
                <p>We accept returns for refund or exchange within 30 days of the original purchase date. To be eligible for a return, your item must be:</p>
                <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li>Unused and in the same condition that you received it.</li>
                    <li>In the original packaging with all tamper-evident seals intact.</li>
                    <li>Accompanied by the original receipt or proof of purchase.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">2. Return Process</h2>
                <p>To initiate a return, please contact our support team or use the RMA form in your account dashboard. You will receive a Return Merchandise Authorization (RMA) number. Please include this number clearly on the outside of your return package.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">3. Shipping Returns</h2>
                <p>You are responsible for paying for your own shipping costs for returning your item. Shipping costs are non-refundable. If you receive a refund, the cost of return shipping will be deducted from your refund.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-3">4. Refunds</h2>
                <p>Once your return is received and inspected, we will notify you of the approval or rejection of your refund. If approved, your refund will be processed, and a credit will automatically be applied to your credit card or original method of payment, within a certain amount of days.</p>
            </section>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

