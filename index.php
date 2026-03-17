<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisionPro LCD Refurbishing Inc. - Mobile Parts Wholesale</title>
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Tailwind CDN Fallback -->
    <!-- Tailwind CDN Fallback -->

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        },
                    },
                },
            },
        }
    </script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/visionpro-logo.png">
    <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero Section -->
        <section class="relative bg-primary-900 text-white overflow-hidden reveal">
            <div class="absolute inset-0 opacity-20 bg-[url('https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80')] bg-cover bg-center"></div>
            <div class="container mx-auto px-4 py-24 relative z-10 flex flex-col items-center text-center">
                <h1 class="text-5xl md:text-7xl font-bold mb-6 tracking-tight animate-float">VisionPro LCD Refurbishing</h1>
                <p class="text-xl md:text-2xl mb-10 max-w-2xl text-gray-300">Your premium partner for high-quality mobile phone parts, tools, and refurbishing supplies in Mississauga.</p>
                <div class="flex gap-4">
                    <a href="products.php" class="btn-primary flex items-center gap-2 px-8 py-3 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/20">
                        Shop Products
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <a href="about.php" class="px-8 py-3 rounded-lg border border-white hover:bg-white hover:text-primary-900 transition-all font-semibold">Our Story</a>
                </div>
            </div>
        </section>

        <!-- Brands Section -->
        <section class="py-24 bg-theme overflow-hidden relative border-b border-gray-100">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                    <div>
                        <span class="text-primary-600 font-bold tracking-widest uppercase text-xs">Premium Partners</span>
                        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mt-2">Shop by Brand</h2>
                    </div>
                    <a href="brands.php" class="group flex items-center gap-2 text-gray-900 font-bold hover:text-primary-600 transition-all uppercase tracking-widest text-sm">
                        View All Brands
                        <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                    <?php 
                    $brands = $pdo->query("SELECT * FROM brands WHERE is_active = 1 LIMIT 6")->fetchAll();
                    foreach($brands as $b): 
                    ?>
                    <a href="categories.php?brand_id=<?= $b['id'] ?>" class="theme-card p-8 flex flex-col items-center group">
                        <div class="w-20 h-20 theme-inset rounded-full flex items-center justify-center mb-6 overflow-hidden bg-white p-2">
                            <?php if(!empty($b['logo'])): ?>
                                <img src="<?= $b['logo'] ?>" class="w-12 h-12 object-contain filter grayscale group-hover:grayscale-0 group-hover:scale-110 transition-all duration-500" alt="<?= $b['name'] ?>">
                            <?php else: ?>
                                <span class="text-2xl font-black text-gray-400 group-hover:text-primary-600 transition-colors"><?= substr($b['name'], 0, 1) ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="text-xs font-bold text-gray-600 group-hover:text-primary-600 uppercase tracking-widest transition-colors"><?= $b['name'] ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="py-24 bg-white overflow-hidden border-b border-gray-100">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                    <div>
                        <span class="text-primary-600 font-bold tracking-widest uppercase text-xs">Device Models</span>
                        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mt-2">Explore Series</h2>
                    </div>
                    <a href="categories.php" class="group flex items-center gap-2 text-gray-900 font-bold hover:text-primary-600 transition-all uppercase tracking-widest text-sm">
                        View All Series
                        <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php 
                    $stmt = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL LIMIT 6");
                    $categories = $stmt->fetchAll();
                    $colors = ['bg-blue-100', 'bg-emerald-100', 'bg-purple-100', 'bg-amber-100', 'bg-rose-100', 'bg-indigo-100'];
                    foreach($categories as $index => $cat): 
                        $color = $colors[$index % count($colors)];
                    ?>
                    <a href="parts.php?category_id=<?= $cat['id'] ?>" 
                       class="group theme-card flex flex-col relative overflow-hidden transition-all duration-700">
                        <!-- Image Container -->
                        <div class="relative h-60 theme-inset overflow-hidden m-4 flex items-center justify-center bg-white p-4">
                            <?php if($cat['image_url']): ?>
                                <img src="<?= $cat['image_url'] ?>" class="w-full h-full object-contain transform group-hover:scale-110 transition-transform duration-1000" alt="<?= $cat['name'] ?>">
                            <?php else: ?>
                                <div class="w-full h-full text-gray-300 opacity-60 flex items-center justify-center text-8xl">📦</div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Content -->
                        <div class="px-8 pb-8 pt-4 flex flex-col text-center">
                            <h3 class="text-xl font-bold text-gray-900 group-hover:text-primary-600 transition-colors duration-500 mb-3 tracking-tight"><?= $cat['name'] ?></h3>
                            <div class="flex items-center justify-center gap-2 text-primary-500 font-bold text-[10px] uppercase tracking-widest">
                                <span>Browse Parts</span>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Hot Selling Products -->
        <section class="py-20 bg-theme reveal">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-end mb-12">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">🔥 Hot Selling</h2>
                        <p class="text-gray-500 mt-2">Our most requested wholesale parts</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 stagger-reveal">
                    <?php 
                    $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1 LIMIT 4");
                    $featured = $stmt->fetchAll();
                    foreach($featured as $p): ?>
                    <!-- Reuse product card logic from products.php -->
                    <div class="group theme-card flex flex-col relative overflow-hidden transition-all duration-300 transform hover:-translate-y-2">
                        <a href="product-detail.php?id=<?= $p['id'] ?>" class="absolute inset-0 z-10"><span class="sr-only">View Product</span></a>
                        <div class="relative h-60 theme-inset overflow-hidden m-4 flex items-center justify-center bg-white p-6">
                            <img src="<?= $p['main_image'] ?: 'https://via.placeholder.com/400x400?text=No+Image' ?>" class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-110">
                            <span class="absolute top-4 left-4 bg-orange-500 text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-widest shadow-lg shadow-orange-200">Hot</span>
                        </div>
                        <div class="px-8 pb-8 pt-2 text-center flex-1 flex flex-col">
                            <h3 class="font-bold text-gray-900 mb-3 transition-colors leading-tight line-clamp-2"><?= $p['name'] ?></h3>
                            <div class="flex items-center justify-center gap-3">
                                <?php if($p['discount_price']): ?>
                                <span class="text-2xl font-black text-gray-900">$<?= number_format($p['discount_price'], 2) ?></span>
                                <span class="text-sm text-gray-400 line-through font-bold">$<?= number_format($p['price'], 2) ?></span>
                                <?php else: ?>
                                <span class="text-2xl font-black text-gray-900">$<?= number_format($p['price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- All Products Preview -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-end mb-12">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Latest Arrivals</h2>
                        <div class="h-1.5 w-20 bg-primary-500 mt-2 rounded-full"></div>
                    </div>
                    <a href="products.php" class="text-primary-600 font-bold hover:underline">See All Products →</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php 
                    $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 8");
                    $latest = $stmt->fetchAll();
                    foreach($latest as $p): ?>
                    <div class="group theme-card flex flex-col relative overflow-hidden transition-all duration-300 transform hover:-translate-y-2">
                        <a href="product-detail.php?id=<?= $p['id'] ?>" class="absolute inset-0 z-10"><span class="sr-only">View Product</span></a>
                        <div class="relative h-48 theme-inset overflow-hidden m-4 flex items-center justify-center bg-white p-4">
                             <img src="<?= $p['main_image'] ?: 'https://via.placeholder.com/400x400?text=No+Image' ?>" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="px-6 pb-6 pt-2 text-center flex-1 flex flex-col">
                            <h3 class="font-bold text-gray-900 text-sm line-clamp-2 mb-4"><?= $p['name'] ?></h3>
                            <div class="flex justify-between items-center mt-auto">
                                <?php if($p['discount_price']): ?>
                                <span class="text-primary-600 font-black text-lg">$<?= number_format($p['discount_price'], 2) ?></span>
                                <?php else: ?>
                                <span class="text-primary-600 font-black text-lg">$<?= number_format($p['price'], 2) ?></span>
                                <?php endif; ?>
                                <span class="text-xs px-4 py-2 theme-button font-bold rounded-lg transition-all z-20 relative pointer-events-auto">Details</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Achievements / Stats Section -->
        <section class="py-20 bg-primary-950 text-white overflow-hidden relative reveal">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <div class="absolute top-10 left-10 w-64 h-64 bg-primary-500 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-10 right-10 w-64 h-64 bg-blue-500 rounded-full blur-[120px]"></div>
            </div>
            <div class="container mx-auto px-4 relative z-10">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-12 text-center stagger-reveal">
                    <div class="p-6 bg-white/5 backdrop-blur-sm rounded-3xl border border-white/10">
                        <p class="text-4xl md:text-5xl font-bold mb-2">10+</p>
                        <p class="text-primary-400 text-sm uppercase font-bold tracking-widest">Years Experience</p>
                    </div>
                    <div class="p-6 bg-white/5 backdrop-blur-sm rounded-3xl border border-white/10">
                        <p class="text-4xl md:text-5xl font-bold mb-2">15k+</p>
                        <p class="text-primary-400 text-sm uppercase font-bold tracking-widest">Orders Shipped</p>
                    </div>
                    <div class="p-6 bg-white/5 backdrop-blur-sm rounded-3xl border border-white/10">
                        <p class="text-4xl md:text-5xl font-bold mb-2">2.5k+</p>
                        <p class="text-primary-400 text-sm uppercase font-bold tracking-widest">Partner Shops</p>
                    </div>
                    <div class="p-6 bg-white/5 backdrop-blur-sm rounded-3xl border border-white/10">
                        <p class="text-4xl md:text-5xl font-bold mb-2">100%</p>
                        <p class="text-primary-400 text-sm uppercase font-bold tracking-widest">Quality Tested</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="py-24 bg-white reveal">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">What Our Clients Say</h2>
                    <p class="text-gray-500">Trusted by repair professionals across North America</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 stagger-reveal">
                    <?php 
                    $testimonials = [
                        ['name' => 'Michael Chen', 'shop' => 'Mississauga Tech Hub', 'text' => "VisionPro has been our primary supplier for 5 years. Their LCD quality is unmatched in the GTA."],
                        ['name' => 'Sarah Johnson', 'shop' => 'FixIt Pro', 'text' => "Fast shipping and reliable support. Their warranty process is very straightforward and fair."],
                        ['name' => 'David Miller', 'shop' => 'Elite Repair Lab', 'text' => "The best wholesale prices for premium parts."]
                    ];
                    foreach($testimonials as $t): ?>
                    <div class="bg-gray-50 p-10 rounded-3xl border border-gray-100 relative group">
                        <div class="text-primary-600 text-6xl absolute top-8 right-8 opacity-10 font-serif">"</div>
                        <p class="text-gray-600 italic mb-8 relative z-10">"<?= $t['text'] ?>"</p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-primary-200 rounded-full flex items-center justify-center font-bold text-primary-700">
                                <?= substr($t['name'], 0, 1) ?>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900"><?= $t['name'] ?></h4>
                                <p class="text-xs text-primary-600"><?= $t['shop'] ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Newsletter Section -->
        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="max-w-5xl mx-auto bg-gradient-to-br from-primary-600 to-primary-800 rounded-[3rem] p-12 md:p-20 text-white flex flex-col lg:flex-row items-center justify-between gap-12 overflow-hidden relative shadow-2xl shadow-primary-200">
                    <div class="relative z-10 lg:w-1/2">
                        <h2 class="text-4xl font-bold mb-4">Stay Informed</h2>
                        <p class="text-primary-100 text-lg">Get weekly updates on wholesale price drops, new arrivals, and refurbishing techniques.</p>
                    </div>
                    <div class="relative z-10 lg:w-1/2 w-full">
                        <form action="#" class="flex flex-col sm:flex-row gap-4">
                            <input type="email" placeholder="Your business email" class="flex-1 px-6 py-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white placeholder-primary-200 outline-none focus:ring-2 focus:ring-white/30 transition-all">
                            <button type="submit" class="bg-white text-primary-700 font-bold px-8 py-4 rounded-2xl hover:bg-primary-50 transition-all border-none">Subscribe</button>
                        </form>
                    </div>
                    <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>




