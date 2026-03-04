<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - VisionPro LCD Refurbishing</title>
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
        <!-- Vision & Mission -->
        <section class="py-24 bg-white reveal">
            <div class="container mx-auto px-4 flex flex-col lg:flex-row items-center gap-16 stagger-reveal">
                <div class="lg:w-1/2">
                    <span class="text-primary-600 font-bold uppercase tracking-widest text-sm mb-4 block">Our Story</span>
                    <h1 class="text-5xl font-bold text-gray-900 mb-8 leading-tight">Empowering Repair Professionals Since 2015</h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        VisionPro LCD Refurbishing Inc. started as a small repair shop in Mississauga and has grown into Ontario's leading wholesaler for premium mobile phone parts and supplies.
                    </p>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-primary-100 rounded-2xl flex items-center justify-center text-primary-600 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">Uncompromising Quality</h3>
                                <p class="text-gray-500">Every screen and part is triple-tested before shipping.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-primary-100 rounded-2xl flex items-center justify-center text-primary-600 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">Fast Local Delivery</h3>
                                <p class="text-gray-500">Same-day shipping for Mississauga and GTA area.</p></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:w-1/2 relative">
                    <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?auto=format&fit=crop&w=1000&q=80" class="rounded-[3rem] shadow-2xl" alt="Repair Lab">
                    <div class="absolute -bottom-10 -left-10 bg-primary-600 p-10 rounded-3xl text-white shadow-xl hidden md:block">
                        <p class="text-4xl font-bold">10k+</p>
                        <p class="text-sm opacity-80">SKUs in Stock</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vision, Mission, Goal -->
        <section class="py-24 bg-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Vision -->
                    <div class="bg-gray-50 p-10 rounded-3xl border border-gray-100 hover:shadow-lg transition-all">
                        <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Vision</h3>
                        <p class="text-gray-600 leading-relaxed">To become North America's most trusted partner for mobile repair professionals by setting new standards in quality, transparency, and supply chain efficiency.</p>
                    </div>

                    <!-- Mission -->
                    <div class="bg-gray-50 p-10 rounded-3xl border border-gray-100 hover:shadow-lg transition-all">
                        <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Mission</h3>
                        <p class="text-gray-600 leading-relaxed">To empower repair businesses with premium parts, competitive pricing, and technical expertise, ensuring every device gets a second life.</p>
                    </div>

                    <!-- Goal -->
                    <div class="bg-gray-50 p-10 rounded-3xl border border-gray-100 hover:shadow-lg transition-all">
                        <div class="w-14 h-14 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Goal</h3>
                        <p class="text-gray-600 leading-relaxed">To support 5,000+ local repair shops by 2028 and reduce electronic waste through sustainable refurbishing practices.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us Grid -->
        <section class="py-24 bg-gray-50">
            <div class="container mx-auto px-4">
                 <div class="text-center mb-16 max-w-3xl mx-auto">
                    <span class="text-primary-600 font-bold uppercase tracking-widest text-sm mb-2 block">Our Advantage</span>
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Top Repair Shops Choose VisionPro</h2>
                    <p class="text-gray-500">We understand the restoration business better than anyone else because we started as one.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Card 1 -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                        <div class="w-16 h-16 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-600 mb-6 group-hover:bg-primary-600 group-hover:text-white transition-all">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Premium Quality Control</h3>
                        <p class="text-gray-500 leading-relaxed">Our 3-stage visual and functional testing ensures zero-defect rates. We don't just sell screens; we sell reliability.</p>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                        <div class="w-16 h-16 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-600 mb-6 group-hover:bg-primary-600 group-hover:text-white transition-all">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Lightning Fast Supply</h3>
                        <p class="text-gray-500 leading-relaxed">With our strategic Mississauga location, we offer same-day dispatch and pickup options to keep your repair bench running.</p>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                        <div class="w-16 h-16 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-600 mb-6 group-hover:bg-primary-600 group-hover:text-white transition-all">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Competitive Wholesale Pricing</h3>
                        <p class="text-gray-500 leading-relaxed">Tiered pricing structures designed for high-volume shops. The more you repair, the more you save.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-24 bg-white">
            <div class="container mx-auto px-4 max-w-4xl">
                <div class="text-center mb-16">
                    <span class="text-primary-600 font-bold uppercase tracking-widest text-sm mb-2 block">Common Questions</span>
                    <h2 class="text-4xl font-bold text-gray-900">Frequently Asked Questions</h2>
                </div>

                <div class="space-y-6">
                    <!-- FAQ 1 -->
                    <div class="border border-gray-100 rounded-2xl overflow-hidden hover:shadow-md transition-shadow">
                        <details class="group bg-gray-50">
                            <summary class="flex justify-between items-center font-bold cursor-pointer list-none p-6 text-lg text-gray-900">
                                <span>Do you offer a warranty on your parts?</span>
                                <span class="transition group-open:rotate-180">
                                    <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                                </span>
                            </summary>
                            <div class="text-gray-600 px-6 pb-6 leading-relaxed">
                                Yes, we offer a comprehensive warranty on all our screens and parts. Our Premium line comes with a Lifetime Warranty against manufacturing defects, while other tiers typically carry a 1-year warranty.
                            </div>
                        </details>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="border border-gray-100 rounded-2xl overflow-hidden hover:shadow-md transition-shadow">
                        <details class="group bg-gray-50">
                            <summary class="flex justify-between items-center font-bold cursor-pointer list-none p-6 text-lg text-gray-900">
                                <span>What is your return policy?</span>
                                <span class="transition group-open:rotate-180">
                                    <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                                </span>
                            </summary>
                            <div class="text-gray-600 px-6 pb-6 leading-relaxed">
                                We have a hassle-free return policy. Unused items in their original condition can be returned within 30 days. Defective items can be exchanged under warranty. Please initiate a return request from your dashboard.
                            </div>
                        </details>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="border border-gray-100 rounded-2xl overflow-hidden hover:shadow-md transition-shadow">
                        <details class="group bg-gray-50">
                            <summary class="flex justify-between items-center font-bold cursor-pointer list-none p-6 text-lg text-gray-900">
                                <span>Do you ship internationally?</span>
                                <span class="transition group-open:rotate-180">
                                    <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                                </span>
                            </summary>
                            <div class="text-gray-600 px-6 pb-6 leading-relaxed">
                                Currently, we primarily serve Canada and the USA to ensure fast delivery times. However, we can arrange international shipping for large wholesale orders. Please contact us for a quote.
                            </div>
                        </details>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="border border-gray-100 rounded-2xl overflow-hidden hover:shadow-md transition-shadow">
                        <details class="group bg-gray-50">
                            <summary class="flex justify-between items-center font-bold cursor-pointer list-none p-6 text-lg text-gray-900">
                                <span>Can I pick up my order locally?</span>
                                <span class="transition group-open:rotate-180">
                                    <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                                </span>
                            </summary>
                            <div class="text-gray-600 px-6 pb-6 leading-relaxed">
                                Absolutely! Our Mississauga hub (7215 Goreway Dr #1c27) is open for pickups. Simply select "Local Pickup" at checkout, and we'll text you when your order is ready (usually within 1 hour).
                            </div>
                        </details>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20">
            <div class="container mx-auto px-4">
                <div class="bg-primary-900 rounded-[3rem] p-16 text-center text-white relative overflow-hidden">
                    <!-- Background blobs -->
                    <div class="absolute top-0 left-0 w-64 h-64 bg-primary-500 rounded-full blur-[100px] opacity-30"></div>
                    <div class="absolute bottom-0 right-0 w-64 h-64 bg-blue-500 rounded-full blur-[100px] opacity-30"></div>
                    
                    <div class="relative z-10 max-w-2xl mx-auto">
                        <h2 class="text-4xl font-bold mb-6">Ready to Upgrade Your Supply Chain?</h2>
                        <p class="text-primary-100 mb-10 text-lg">Join hundreds of successful repair shops in Ontario who trust VisionPro.</p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="contact.php" class="bg-white text-primary-900 font-bold px-10 py-4 rounded-xl hover:bg-gray-100 transition-all shadow-lg">Contact Us</a>
                            <a href="products.php" class="bg-primary-700 text-white font-bold px-10 py-4 rounded-xl hover:bg-primary-600 transition-all shadow-lg border border-primary-600">Browse Catalog</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

