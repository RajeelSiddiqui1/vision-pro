<?php
session_start();
require_once 'config/db.php';

// Filter logic
$brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : null;

// Build query
$query = "SELECT * FROM categories WHERE 1=1 AND parent_id IS NULL";
$params = [];

if ($brand_id) {
    $query .= " AND brand_id = ?";
    $params[] = $brand_id;
}

$query .= " ORDER BY name ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$categories = $stmt->fetchAll();

// Get brand details if brand_id is set
$selected_brand = null;
if ($brand_id) {
    $brand_stmt = $pdo->prepare("SELECT * FROM brands WHERE id = ?");
    $brand_stmt->execute([$brand_id]);
    $selected_brand = $brand_stmt->fetch();
}

$colors = ['bg-blue-100', 'bg-green-100', 'bg-purple-100', 'bg-yellow-100', 'bg-red-100', 'bg-indigo-100'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $selected_brand ? htmlspecialchars($selected_brand['name']) . ' Categories' : 'All Categories' ?> - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-theme">
    <?php include 'includes/header.php'; ?>

    <!-- Section Header -->
    <div class="relative overflow-hidden mb-12">
        <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent pointer-events-none"></div>
        <div class="container mx-auto px-4 py-24 relative">
            <div class="max-w-3xl">
                <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-primary-600 mb-6 relative z-10">
                    <a href="index.php" class="hover:text-primary-800 transition-colors">Home</a>
                    <span class="text-gray-300">/</span>
                    <a href="brands.php" class="hover:text-primary-800 transition-colors">Brands</a>
                    <?php if ($selected_brand): ?>
                    <span class="text-gray-300">/</span>
                    <span class="text-gray-400"><?= htmlspecialchars($selected_brand['name']) ?></span>
                    <?php endif; ?>
                </nav>
                
                <h1 class="text-5xl md:text-6xl font-black text-gray-900 tracking-tight mb-6">
                    <?php if ($selected_brand): ?>
                        <?= htmlspecialchars($selected_brand['name']) ?> <span class="text-primary-600">Series</span>
                    <?php else: ?>
                        Device <span class="text-primary-600">Series</span>
                    <?php endif; ?>
                </h1>
                
                <p class="text-lg text-gray-500 leading-relaxed font-medium">
                    Select a series or category to proceed to specific device models and replacement parts.
                </p>
            </div>
        </div>
    </div>

    <main class="container mx-auto px-4 py-12 mb-24">
        <?php if (empty($categories)): ?>
            <div class="max-w-2xl mx-auto theme-card p-20 text-center">
                <div class="text-6xl mb-6">📭</div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">No Series Found</h3>
                <p class="text-gray-500 mb-8">We are actively adding new device series and categories.</p>
                <a href="brands.php" class="inline-flex px-8 py-3 theme-button rounded-xl text-xs uppercase tracking-widest relative z-20">Browse All Brands</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <?php foreach($categories as $index => $cat): 
                    $color = $colors[$index % count($colors)];
                    
                    // Fetch subcategories (models) for this series
                    $sub_stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC LIMIT 3");
                    $sub_stmt->execute([$cat['id']]);
                    $subcategories = $sub_stmt->fetchAll();
                ?>
                <div class="relative group h-full">
                    <a href="parts.php?category_id=<?= $cat['id'] ?><?= $brand_id ? '&brand_id='.$brand_id : '' ?>" class="theme-card flex flex-col h-full overflow-hidden block">
                        <!-- Image Area -->
                        <div class="relative h-48 theme-inset m-4 overflow-hidden flex items-center justify-center p-6 bg-white">
                            <?php if($cat['image_url']): ?>
                                <img src="<?= $cat['image_url'] ?>" class="w-full h-full object-contain filter group-hover:scale-110 transition-transform duration-700" alt="<?= $cat['name'] ?>">
                            <?php else: ?>
                                <div class="w-full h-full <?= $color ?> flex items-center justify-center text-4xl rounded-2xl opacity-50">📦</div>
                            <?php endif; ?>
                        </div>

                        <!-- Content Area -->
                        <div class="px-8 pb-8 pt-2 flex-1 flex flex-col relative z-20">
                            <h3 class="text-lg font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition-colors"><?= htmlspecialchars($cat['name']) ?></h3>
                            
                            <?php if (!empty($subcategories)): ?>
                            <div class="flex flex-wrap gap-2 mb-6">
                                <?php foreach($subcategories as $sub): ?>
                                    <span class="px-3 py-1 bg-gray-50 text-gray-500 rounded-lg text-[10px] font-bold"><?= htmlspecialchars($sub['name']) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <p class="text-xs text-gray-500 mt-2 mb-6 line-clamp-2">Explore the full range of replacement components for the <?= htmlspecialchars($cat['name']) ?> series.</p>
                            <?php endif; ?>

                            <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between pointer-events-none">
                                <span class="text-[10px] font-black tracking-widest uppercase text-gray-500 group-hover:text-primary-600 transition-colors">Start Repair</span>
                                <div class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 group-hover:bg-primary-600 group-hover:text-white group-hover:border-primary-600 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
