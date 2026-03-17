<?php
session_start();
require_once 'config/db.php';

// Filter logic
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : null;

// Validate selection
if (!$category_id) {
    header('Location: categories.php');
    exit;
}

// 1. Get Category Details (Specific Model)
$cat_stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$cat_stmt->execute([$category_id]);
$model_category = $cat_stmt->fetch();

if (!$model_category) {
    header('Location: categories.php');
    exit;
}

// Enforce brand logic
if (!$brand_id && $model_category['brand_id']) {
    $brand_id = $model_category['brand_id'];
}

$selected_brand = null;
if ($brand_id) {
    $brand_stmt = $pdo->prepare("SELECT * FROM brands WHERE id = ?");
    $brand_stmt->execute([$brand_id]);
    $selected_brand = $brand_stmt->fetch();
}

// Get Parent Categories for breadcrumbs (Model Group)
$model_group = null;
if ($model_category['parent_id']) {
    $parent_stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $parent_stmt->execute([$model_category['parent_id']]);
    $model_group = $parent_stmt->fetch();
}

// 2. Fetch Part Categories (Level 3 Categories)
$parts_stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC");
$parts_stmt->execute([$category_id]);
$parts = $parts_stmt->fetchAll();

// Auto-forward logic: if there are no sub-parts, go directly to products page for this model
if (empty($parts)) {
    $redirect_url = "products.php?category_id=" . $category_id;
    if ($brand_id) {
        $redirect_url .= "&brand_id=" . $brand_id;
    }
    header("Location: " . $redirect_url);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($model_category['name']) ?> Parts - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Remix Icons for elegant iconography -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">
    <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-theme">
    <?php include 'includes/header.php'; ?>

    <!-- Section Header -->
    <div class="relative overflow-hidden mb-12">
        <div class="container mx-auto px-4 py-24 relative">
            <div class="max-w-3xl">
                <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400 mb-8">
                    <a href="index.php" class="hover:text-primary-600 transition-colors">Home</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <a href="brands.php" class="hover:text-primary-600 transition-colors">Brands</a>
                    
                    <?php if ($selected_brand): ?>
                    <i class="ri-arrow-right-s-line"></i>
                    <a href="categories.php?brand_id=<?= $selected_brand['id'] ?>" class="hover:text-primary-600 transition-colors"><?= htmlspecialchars($selected_brand['name']) ?></a>
                    <?php endif; ?>

                    <?php if ($model_group): ?>
                    <i class="ri-arrow-right-s-line"></i>
                    <a href="parts.php?category_id=<?= $model_group['id'] ?><?= $brand_id ? '&brand_id=' . $brand_id : '' ?>" class="hover:text-primary-600 transition-colors"><?= htmlspecialchars($model_group['name']) ?></a>
                    <?php endif; ?>
                </nav>
                
                <h1 class="text-5xl md:text-7xl font-black text-gray-900 tracking-tight mb-6 leading-none">
                    <?= htmlspecialchars($model_category['name']) ?><br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-primary-400">Components</span>
                </h1>
                <p class="text-xl text-gray-500 font-bold max-w-2xl leading-relaxed">
                    Select the specific replacement part category you need for this device.
                </p>
            </div>
        </div>
    </div>

    <main class="container mx-auto px-4 py-12 mb-24">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-8">
            <?php foreach($parts as $part): ?>
            <?php 
                // $part is a Level 2 Model. We pass it as category_id so products.php shows all its items.
                $link = "products.php?category_id=" . $part['id'];
                if ($brand_id) $link .= "&brand_id=" . $brand_id;
            ?>
            <a href="<?= $link ?>" class="theme-card p-10 flex flex-col group">
                <div class="h-40 theme-inset mb-6 overflow-hidden flex items-center justify-center relative">
                    <?php if($part['image_url']): ?>
                        <img src="<?= $part['image_url'] ?>" alt="<?= $part['name'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                    <?php else: ?>
                        <span class="text-6xl opacity-40">⚙️</span>
                    <?php endif; ?>
                </div>
                
                <h3 class="text-sm font-bold text-gray-700 group-hover:text-primary-600 transition-colors duration-500 mb-2 tracking-tight"><?= $part['name'] ?></h3>
                <p class="text-[10px] text-gray-500 font-bold mb-4 line-clamp-2">High-performance replacement parts engineered for precise fitment.</p>
                
                <div class="mt-auto flex justify-end">
                    <div class="w-10 h-10 theme-button rounded-full flex items-center justify-center text-primary-600 group-hover:text-primary-700">
                        <i class="ri-settings-3-line text-lg"></i>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Global CTA -->
    <section class="py-24 bg-gray-900 text-white text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('assets/images/pattern.png')] opacity-10"></div>
        <div class="container mx-auto px-4 relative z-10">
            <h2 class="text-4xl font-black mb-6">Need bulk parts?</h2>
            <p class="text-gray-400 font-bold mb-8 max-w-xl mx-auto">Get exclusive wholesale pricing and dedicated support for repair shops.</p>
            <a href="signup.php" class="inline-flex px-10 py-5 bg-primary-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-primary-500 transition-all shadow-xl shadow-primary-900/40">Apply for Wholesale</a>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
