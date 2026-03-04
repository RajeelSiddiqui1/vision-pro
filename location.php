<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location & Hours - VisionPro</title>
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

    <main class="container mx-auto px-4 py-12">
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Info Side -->
            <div class="lg:w-1/3 space-y-8">
                <div>
                    <h1 class="text-3xl font-bold mb-4">Visit Our Showroom</h1>
                    <p class="text-gray-600">Experience our quality firsthand. Walk-ins are welcome for wholesale account registration and pickups.</p>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Address
                    </h3>
                    <p class="text-gray-600">
                        7215 Goreway Dr #1c27<br>
                        Mississauga, L4T2T9<br>
                        Canada
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Operating Hours
                    </h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex justify-between border-b pb-2"><span>Monday - Friday</span> <span class="font-bold">10:00 AM - 7:00 PM</span></li>
                        <li class="flex justify-between border-b pb-2"><span>Saturday</span> <span class="font-bold">11:00 AM - 5:00 PM</span></li>
                        <li class="flex justify-between text-gray-400"><span>Sunday</span> <span>Closed</span></li>
                    </ul>
                </div>
            </div>

            <!-- Map Side -->
            <div class="lg:w-2/3">
                <div class="h-[600px] bg-gray-200 rounded-3xl overflow-hidden shadow-lg border border-gray-200 relative">
                     <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2885.123456789!2d-79.65!3d43.72!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x882b15e4b0f3f3f3%3A0xabcdef123456!2s7215%20Goreway%20Dr%2C%20Mississauga%2C%20ON%20L4T2T9!5e0!3m2!1sen!2sus!4v1647895234567!5m2!1sen!2sus" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

