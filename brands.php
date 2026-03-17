<?php
session_start();
require_once 'config/db.php';

// Get all active brands
$brands = $pdo->query("SELECT * FROM brands WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Brands - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">
    <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-theme group/body">
    <?php include 'includes/header.php'; ?>

    <main class="min-h-screen">
        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-primary-950 py-20 lg:py-32">
            <!-- Decorative Blobs -->
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-primary-400/20 rounded-full blur-[120px] pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-[400px] h-[400px] bg-blue-400/10 rounded-full blur-[100px] pointer-events-none"></div>
            
            <div class="container mx-auto px-4 relative z-10">
                <div class="max-w-3xl mx-auto text-center reveal active">
                    <span class="inline-block px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-xs font-bold uppercase tracking-widest mb-6">
                        Premium Partners
                    </span>
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 tracking-tight">
                        Our <span class="text-primary-300">Trusted</span> Brands
                    </h1>
                    <p class="text-lg md:text-xl text-primary-100/80 leading-relaxed mb-10 max-w-2xl mx-auto font-medium">
                        Explore our curated selection of industry-leading brands. We partner only with the best to ensure premium quality for your repair needs.
                    </p>
                </div>
            </div>
        </section>

        <!-- Brands Grid Section -->
        <section class="py-20 lg:py-32 bg-gray-50/50">
            <div class="container mx-auto px-4">
                <?php if (empty($brands)): ?>
                <div class="theme-card p-16 text-center reveal active max-w-2xl mx-auto">
                    <div class="w-24 h-24 bg-primary-50 rounded-3xl flex items-center justify-center mx-auto mb-8 text-5xl">📦</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">No Brands Found</h3>
                    <p class="text-gray-500 leading-relaxed mb-8 text-lg">We're currently updating our brand partnerships. Please check back soon or browse our full catalog.</p>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-8 stagger-reveal active">
                    <?php foreach($brands as $brand): ?>
                    <a href="categories.php?brand_id=<?= $brand['id'] ?>" class="theme-card p-10 flex flex-col items-center group">
                        <div class="w-32 h-32 theme-inset rounded-full flex items-center justify-center mb-6 overflow-hidden bg-white p-4">
                            <?php if($brand['logo']): ?>
                                <img src="<?= $brand['logo'] ?>" alt="<?= $brand['name'] ?>" class="w-full h-full object-contain filter grayscale group-hover:grayscale-0 group-hover:scale-110 transition-all duration-500">
                            <?php else: ?>
                                <span class="text-4xl font-black text-gray-300 group-hover:text-primary-600 transition-colors"><?= substr($brand['name'], 0, 1) ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="text-xs font-bold text-gray-700 uppercase tracking-widest text-center mb-6 group-hover:text-primary-600 transition-colors">
                            <?= htmlspecialchars($brand['name']) ?>
                        </h3>
                        
                        <div class="px-6 py-2 theme-button rounded-full text-[10px] font-black uppercase tracking-widest mt-auto">
                            Explore Series
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Dynamic CTA Section -->
        <section class="py-20 relative overflow-hidden">
            <div class="absolute inset-0 bg-gray-900 pointer-events-none"></div>
            <!-- Decorative Pattern -->
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.1) 1px, transparent 0); background-size: 32px 32px;"></div>
            
            <div class="container mx-auto px-4 relative z-10 text-center">
                <div class="max-w-4xl mx-auto reveal active">
                    <h2 class="text-3xl md:text-5xl font-bold text-white mb-8 tracking-tight italic">
                        Quality you can <span class="text-primary-500">Trust</span>, Brands you <span class="text-primary-400">Respect</span>.
                    </h2>
                    <div class="h-1 w-24 bg-primary-600 mx-auto rounded-full mb-10"></div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-white mb-2">100%</div>
                            <div class="text-sm text-gray-400 font-medium uppercase tracking-widest">Genuine Parts</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-white mb-2">50+</div>
                            <div class="text-sm text-gray-400 font-medium uppercase tracking-widest">Global Brands</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-white mb-2">24h</div>
                            <div class="text-sm text-gray-400 font-medium uppercase tracking-widest">Support</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-white mb-2">99%</div>
                            <div class="text-sm text-gray-400 font-medium uppercase tracking-widest">Client Satisfaction</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
