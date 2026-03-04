<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LCD Buyback Program - VisionPro</title>
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
        <!-- Hero -->
        <section class="bg-green-900 text-white py-24 relative overflow-hidden">
            <div class="container mx-auto px-4 relative z-10 flex flex-col items-center text-center">
                <span class="bg-green-800 text-green-200 px-4 py-1 rounded-full text-sm font-bold uppercase tracking-widest mb-4">Recycling Program</span>
                <h1 class="text-5xl font-bold mb-6">We Buy Your Cracked Screens</h1>
                <p class="text-xl text-green-100 max-w-2xl">Turn your e-waste into revenue. Highest payout rates for iPhone and Samsung OLEDs.</p>
                <div class="mt-8 flex gap-4">
                    <button class="btn-primary bg-white text-green-900 hover:bg-gray-100">Download Price List</button>
                    <button class="px-8 py-3 rounded-xl border border-white hover:bg-white hover:text-green-900 transition-colors font-bold">Start a Request</button>
                </div>
            </div>
        </section>

        <!-- Steps -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-16">How It Works</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center relative">
                    <!-- Step 1 -->
                    <div>
                        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6">1</div>
                        <h3 class="font-bold text-lg mb-2">Collect & Pack</h3>
                        <p class="text-gray-500 text-sm">Gather your broken LCD/OLED screens and pack them securely.</p>
                    </div>
                    <!-- Step 2 -->
                    <div>
                        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6">2</div>
                        <h3 class="font-bold text-lg mb-2">Ship to Us</h3>
                        <p class="text-gray-500 text-sm">Send your package to our Mississauga facility. Free shipping on 50+ screens.</p>
                    </div>
                    <!-- Step 3 -->
                    <div>
                        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6">3</div>
                        <h3 class="font-bold text-lg mb-2">Testing</h3>
                        <p class="text-gray-500 text-sm">Our team tests each screen and grades them (Good LCD, Bad Touch, etc).</p>
                    </div>
                    <!-- Step 4 -->
                    <div>
                        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6">4</div>
                        <h3 class="font-bold text-lg mb-2">Get Paid</h3>
                        <p class="text-gray-500 text-sm">Receive payment via PayPal, Check, or Store Credit ( +10% bonus).</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Preview -->
        <section class="py-20 bg-gray-50">
             <div class="container mx-auto px-4 max-w-4xl">
                 <h2 class="text-2xl font-bold mb-8">Current Payout Rates (Est.)</h2>
                 <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                     <table class="w-full text-left">
                         <thead class="bg-gray-100 text-xs text-gray-500 uppercase font-bold">
                             <tr>
                                 <th class="p-4">Model</th>
                                 <th class="p-4 text-right">Good LCD / Bad Glass</th>
                                 <th class="p-4 text-right">Bad Touch</th>
                             </tr>
                         </thead>
                         <tbody class="divide-y divide-gray-100">
                             <tr>
                                 <td class="p-4 font-bold">iPhone 14 Pro Max</td>
                                 <td class="p-4 text-right text-green-600 font-bold">$220.00</td>
                                 <td class="p-4 text-right">$180.00</td>
                             </tr>
                             <tr>
                                 <td class="p-4 font-bold">iPhone 13 Pro</td>
                                 <td class="p-4 text-right text-green-600 font-bold">$140.00</td>
                                 <td class="p-4 text-right">$110.00</td>
                             </tr>
                             <tr>
                                 <td class="p-4 font-bold">Samsung S23 Ultra</td>
                                 <td class="p-4 text-right text-green-600 font-bold">$190.00</td>
                                 <td class="p-4 text-right">$150.00</td>
                             </tr>
                         </tbody>
                     </table>
                     <div class="p-4 bg-gray-50 text-center border-t border-gray-100">
                         <a href="#" class="text-primary-600 font-bold text-sm hover:underline">View Full Price List</a>
                     </div>
                 </div>
             </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

