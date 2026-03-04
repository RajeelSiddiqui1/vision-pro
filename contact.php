<?php
session_start();
require_once 'config/db.php';

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);
    $full_name = "$first_name $last_name";
    $full_message = "From: $full_name ($email)\nPhone: $phone\n\n$message";
    
    // Send real email to admin
    require_once 'includes/email_helper.php';
    send_contact_form($full_name, $email, $subject, $full_message);
    
    $success = "Thank you! Your message has been sent. We will contact you shortly.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - VisionPro LCD Refurbishing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">\n <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero Section -->
        <section class="bg-primary-900 text-white py-24 relative overflow-hidden">
            <div class="absolute inset-0 opacity-20 bg-[url('https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?auto=format&fit=crop&w=1600&q=80')] bg-cover bg-center"></div>
            <div class="container mx-auto px-4 relative z-10 text-center">
                <span class="text-primary-400 font-bold uppercase tracking-widest text-sm mb-4 block animate-fade-in-up">Get in Touch</span>
                <h1 class="text-5xl font-bold mb-6 animate-fade-in-up delay-100">Contact VisionPro</h1>
                <p class="text-xl text-primary-100 max-w-2xl mx-auto animate-fade-in-up delay-200">We're here to help with all your wholesale parts and refurbishing needs.</p>
            </div>
        </section>

        <!-- Contact Info & Form -->
        <section class="py-24 -mt-10 relative z-20">
            <div class="container mx-auto px-4">
                <div class="bg-white rounded-[3rem] shadow-xl border border-gray-100 overflow-hidden flex flex-col lg:flex-row">
                    
                    <!-- Contact Information -->
                    <div class="lg:w-1/3 bg-primary-950 text-white p-12 lg:p-16 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-primary-500 rounded-full blur-[100px] opacity-20"></div>
                        
                        <h2 class="text-3xl font-bold mb-10 relative z-10">Contact Information</h2>
                        
                        <div class="space-y-10 relative z-10">
                            <!-- Address -->
                            <div class="flex gap-6 group">
                                <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-primary-400 group-hover:bg-primary-500 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-400 uppercase text-xs tracking-widest mb-1">Visit Us</h4>
                                    <p class="text-lg font-medium leading-relaxed">7215 Goreway Dr #1c27,<br>Mississauga, L4T2T9, Ontario</p>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="flex gap-6 group">
                                <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-primary-400 group-hover:bg-primary-500 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-400 uppercase text-xs tracking-widest mb-1">Call Us</h4>
                                    <p class="text-lg font-medium">647-402-6699</p>
                                    <p class="text-md text-gray-400">+1 (647) 261-5077</p>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="flex gap-6 group">
                                <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-primary-400 group-hover:bg-primary-500 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-400 uppercase text-xs tracking-widest mb-1">Email Us</h4>
                                    <p class="text-lg font-medium">info@visionprolcd.com</p>
                                </div>
                            </div>

                            <!-- Hours -->
                            <div class="flex gap-6 group">
                                <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-primary-400 group-hover:bg-primary-500 group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-400 uppercase text-xs tracking-widest mb-1">Working Hours</h4>
                                    <p class="text-md"><span class="w-20 inline-block text-gray-400">Mon - Fri:</span> 10:00 AM - 7:00 PM</p>
                                    <p class="text-md"><span class="w-20 inline-block text-gray-400">Sat:</span> 11:00 AM - 5:00 PM</p>
                                    <p class="text-md"><span class="w-20 inline-block text-gray-400">Sun:</span> Closed</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="lg:w-2/3 p-12 lg:p-16">
                        <?php if($success): ?>
                            <div class="bg-green-50 text-green-700 p-6 rounded-2xl mb-8 flex items-center gap-4 border border-green-100">
                                <span class="bg-green-100 p-2 rounded-full">✓</span>
                                <p class="font-bold"><?= $success ?></p>
                            </div>
                        <?php endif; ?>

                        <h2 class="text-3xl font-bold text-gray-900 mb-8">Send Us a Message</h2>
                        <form action="" method="POST" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">First Name</label>
                                    <input type="text" name="first_name" required class="w-full px-5 py-4 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 focus:bg-white transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Last Name</label>
                                    <input type="text" name="last_name" required class="w-full px-5 py-4 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 focus:bg-white transition-all">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                                    <input type="email" name="email" required class="w-full px-5 py-4 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 focus:bg-white transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Phone (Optional)</label>
                                    <input type="tel" name="phone" class="w-full px-5 py-4 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 focus:bg-white transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Subject</label>
                                <select name="subject" class="w-full px-5 py-4 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 focus:bg-white transition-all">
                                    <option>Wholesale Inquiry</option>
                                    <option>Order Status</option>
                                    <option>Return / Warranty</option>
                                    <option>General Support</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Message</label>
                                <textarea name="message" required rows="5" class="w-full px-5 py-4 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 focus:bg-white transition-all"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-primary-600 text-white font-bold py-4 rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-200">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Map Section -->
        <section class="pb-24">
            <div class="container mx-auto px-4">
                <div class="bg-gray-100 rounded-[3rem] overflow-hidden h-[500px] border border-gray-200 shadow-inner relative">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2885.123456789!2d-79.65!3d43.72!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x882b15e4b0f3f3f3%3A0xabcdef123456!2s7215%20Goreway%20Dr%2C%20Mississauga%2C%20ON%20L4T2T9!5e0!3m2!1sen!2sus!4v1647895234567!5m2!1sen!2sus" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                    <!-- Map Overlay Card -->
                    <div class="absolute bottom-6 left-6 bg-white p-6 rounded-2xl shadow-xl max-w-xs hidden sm:block">
                        <div class="flex items-center gap-4 mb-2">
                             <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center text-white font-bold">V</div>
                             <div>
                                 <h4 class="font-bold text-gray-900">VisionPro HQ</h4>
                                 <p class="text-xs text-primary-600 font-bold uppercase">Wholesale Hub</p>
                             </div>
                        </div>
                        <p class="text-gray-500 text-sm">East of Airport Rd, off Williams Pkwy.</p>
                        <a href="https://goo.gl/maps/example" target="_blank" class="block mt-3 text-center text-primary-600 font-bold text-sm bg-primary-50 py-2 rounded-lg hover:bg-primary-100 transition-colors">Get Directions</a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

