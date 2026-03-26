<?php
session_start();
require_once 'config/db.php';

// Filter logic
$brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : (isset($_GET['brand']) ? (int)$_GET['brand'] : null);
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : (isset($_GET['category']) ? (int)$_GET['category'] : null);
$part_id = isset($_GET['part_id']) ? (int)$_GET['part_id'] : null;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// 1. Get Brand Context
$selected_brand = null;
if ($brand_id) {
    $brand_stmt = $pdo->prepare("SELECT * FROM brands WHERE id = ?");
    $brand_stmt->execute([$brand_id]);
    $selected_brand = $brand_stmt->fetch();
}

// 2. Identify Current Category Depth & Context
$selected_category = null;
$model_group = null;
$specific_model = null;
$current_part = null;

$final_cat_id = $part_id ? $part_id : $category_id;

if ($final_cat_id) {
    $cat_detail_stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $cat_detail_stmt->execute([$final_cat_id]);
    $selected_category = $cat_detail_stmt->fetch();
    
    if ($selected_category) {
        // Enforce brand_id from category if not explicitly set
        if (!$brand_id && $selected_category['brand_id']) {
            $brand_id = $selected_category['brand_id'];
            $brand_stmt = $pdo->prepare("SELECT * FROM brands WHERE id = ?");
            $brand_stmt->execute([$brand_id]);
            $selected_brand = $brand_stmt->fetch();
        }

        if ($selected_category['parent_id']) {
            $parent_stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
            $parent_stmt->execute([$selected_category['parent_id']]);
            $parent_cat = $parent_stmt->fetch();
            
            if ($parent_cat && $parent_cat['parent_id']) {
                // We are at Level 3 (Part)
                $current_part = $selected_category;
                $specific_model = $parent_cat;
                
                $p2_stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
                $p2_stmt->execute([$specific_model['parent_id']]);
                $model_group = $p2_stmt->fetch();
            } else {
                // We are at Level 2 (Model)
                $specific_model = $selected_category;
                $model_group = $parent_cat;
            }
        } else {
            // We are at Level 1 (Model Group)
            $model_group = $selected_category;
        }
    }
}

// 3. Determine Dynamic Sidebar Content
$sidebar_title = "Departments";
$sidebar_items = [];

if ($specific_model) {
    // Show Part Categories (Level 3)
    $sidebar_title = "Model Parts";
    $parts_stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC");
    $parts_stmt->execute([$specific_model['id']]);
    $sidebar_items = $parts_stmt->fetchAll();
} elseif ($model_group) {
    // Show Specific Models (Level 2)
    $sidebar_title = "Select Model";
    $models_stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC");
    $models_stmt->execute([$model_group['id']]);
    $sidebar_items = $models_stmt->fetchAll();
} elseif ($brand_id) {
    // Show Model Groups (Level 1) for this brand
    $sidebar_title = htmlspecialchars($selected_brand['name']) . " Series";
    $groups_stmt = $pdo->prepare("SELECT * FROM categories WHERE brand_id = ? AND parent_id IS NULL ORDER BY name ASC");
    $groups_stmt->execute([$brand_id]);
    $sidebar_items = $groups_stmt->fetchAll();
} else {
    // Generic fallback: Level 1 Categories
    $sidebar_items = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC")->fetchAll();
}

// 4. Build Accessory Query
$query = "SELECT p.*, c.name as category_name, b.name as brand_name FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN brands b ON p.brand_id = b.id 
          WHERE 1=1 AND p.type = 'accessory'";
$params = [];

if ($brand_id) {
    $query .= " AND p.brand_id = ?";
    $params[] = $brand_id;
}

if ($part_id) {
    $query .= " AND p.category_id = ?";
    $params[] = $part_id;
} elseif ($category_id) {
    // If specific model selected, show its direct accessories AND accessories of its parts (Level 3)
    // If model group selected, show accessories of all its models (Level 2) and their parts (Level 3)
    $query .= " AND (p.category_id = ? OR c.parent_id = ? OR c.parent_id IN (SELECT id FROM categories WHERE parent_id = ?))";
    $params[] = $category_id;
    $params[] = $category_id;
    $params[] = $category_id;
}

if ($search) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

switch ($sort) {
    case 'price_low': $query .= " ORDER BY p.price ASC"; break;
    case 'price_high': $query .= " ORDER BY p.price DESC"; break;
    case 'popular': $query .= " ORDER BY p.is_featured DESC"; break;
    default: $query .= " ORDER BY p.created_at DESC"; break;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$accessories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessories - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-theme">
    <?php include 'includes/header.php'; ?>

    <div class="relative overflow-hidden mb-12 border-b border-gray-100 bg-white">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-50/50 to-transparent pointer-events-none"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary-100/20 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="container mx-auto px-4 py-20 relative">
            <div class="max-w-3xl">
                <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-primary-600 mb-6">
                    <a href="index.php" class="hover:text-primary-800 transition-colors">Home</a>
                    <i class="ri-arrow-right-s-line text-gray-300"></i>
                    <a href="brands.php" class="hover:text-primary-800 transition-colors">Brands</a>
                    
                    <?php if ($selected_brand): ?>
                    <i class="ri-arrow-right-s-line text-gray-300"></i>
                    <a href="categories.php?brand_id=<?= $selected_brand['id'] ?>" class="hover:text-primary-800 transition-colors"><?= htmlspecialchars($selected_brand['name']) ?></a>
                    <?php endif; ?>

                    <?php if ($model_group): ?>
                    <i class="ri-arrow-right-s-line text-gray-300"></i>
                    <a href="parts.php?category_id=<?= $model_group['id'] ?><?= $brand_id ? '&brand_id=' . $brand_id : '' ?>" class="hover:text-primary-800 transition-colors"><?= htmlspecialchars($model_group['name']) ?></a>
                    <?php endif; ?>

                    <?php if ($specific_model): ?>
                    <i class="ri-arrow-right-s-line text-gray-300"></i>
                    <span class="text-gray-900"><?= htmlspecialchars($specific_model['name']) ?></span>
                    <?php endif; ?>
                </nav>
                
                <h1 class="text-5xl md:text-6xl font-black text-gray-900 tracking-tight mb-6">
                    <?php if ($selected_category): ?>
                        <?= htmlspecialchars($selected_category['name']) ?>
                    <?php elseif ($selected_brand): ?>
                        <?= htmlspecialchars($selected_brand['name']) ?> <span class="text-primary-600">Inventory</span>
                    <?php else: ?>
                        Our <span class="text-primary-600">Accessories</span>
                    <?php endif; ?>
                </h1>
                
                <p class="text-lg text-gray-500 leading-relaxed font-bold">
                    <?php if($specific_model): ?>
                        Precision engineered parts and accessories for <strong class="text-gray-900"><?= $specific_model['name'] ?></strong>. Strictly verified for performance.
                    <?php else: ?>
                        Browse our extensive catalog of high-quality mobile parts and specialized refurbishing tools.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <main class="container mx-auto px-4 py-12 mb-24">
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Sidebar -->
            <aside class="w-full lg:w-72 space-y-8">
                <div class="theme-card p-8">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6"><?= $sidebar_title ?></h3>
                    <div class="space-y-1">
                        <?php 
                        $all_url = "accessories.php";
                        if ($brand_id && !$model_group) {
                            $all_url .= "?brand_id=$brand_id";
                        } elseif ($model_group && !$specific_model) {
                            $all_url .= "?category_id=" . $model_group['id'] . ($brand_id ? "&brand_id=$brand_id" : "");
                        } elseif ($specific_model) {
                            $all_url .= "?category_id=" . $specific_model['id'] . ($brand_id ? "&brand_id=$brand_id" : "");
                        }

                        $is_all_active = false;
                        if (!$part_id) {
                            if (!$category_id && !$brand_id) $is_all_active = true;
                            elseif (!$category_id && $brand_id) $is_all_active = true;
                            elseif ($category_id && ($category_id == ($specific_model['id'] ?? $model_group['id'] ?? 0))) $is_all_active = true;
                        }
                        ?>
                        <a href="<?= $all_url ?>" 
                           class="group flex items-center justify-between py-3 px-4 rounded-lg transition-all <?= $is_all_active ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-500 hover:bg-gray-50 font-bold' ?>">
                            <span class="text-sm">All <?= $sidebar_title == 'Departments' ? 'Accessories' : 'Items' ?></span>
                        </a>

                        <?php foreach($sidebar_items as $item): 
                            $isActive = ($category_id == $item['id'] || $part_id == $item['id']);
                            
                            $link = "accessories.php?category_id=" . $item['id'];
                            if ($specific_model) {
                                $link = "accessories.php?part_id=" . $item['id'] . "&category_id=" . $specific_model['id'];
                            }
                            if ($brand_id) {
                                $link .= "&brand_id=$brand_id";
                            }
                        ?>
                        <a href="<?= $link ?>" 
                           class="group flex items-center justify-between py-3 px-4 rounded-lg transition-all <?= $isActive ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-500 hover:bg-gray-50 font-bold' ?>">
                            <span class="text-sm"><?= htmlspecialchars($item['name']) ?></span>
                            <i class="ri-arrow-right-s-line opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (!$brand_id): ?>
                <div class="theme-card p-8">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Explore Brands</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <?php 
                        $brand_sidebar = $pdo->query("SELECT * FROM brands WHERE is_active = 1 ORDER BY name ASC LIMIT 6")->fetchAll();
                        foreach($brand_sidebar as $b): 
                        ?>
                        <a href="accessories.php?brand_id=<?= $b['id'] ?>" class="flex flex-col items-center gap-2 p-4 theme-inset hover:theme-card transition-all text-center group">
                            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-[10px] font-bold text-gray-400 shadow-sm group-hover:text-primary-600 transition-colors">
                                <?= substr($b['name'], 0, 1) ?>
                            </div>
                            <span class="text-[10px] font-bold text-gray-600 truncate w-full group-hover:text-primary-600"><?= htmlspecialchars($b['name']) ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="theme-card p-8 bg-gray-900 border-gray-900 text-white shadow-2xl">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Price Filtering</h3>
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[10px] text-gray-500 font-bold uppercase">Min</label>
                                <input type="number" placeholder="$0" class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary-500 outline-none transition-all placeholder-gray-500 text-white">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] text-gray-500 font-bold uppercase">Max</label>
                                <input type="number" placeholder="$999" class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary-500 outline-none transition-all placeholder-gray-500 text-white">
                            </div>
                        </div>
                        <button class="w-full py-4 theme-button rounded-xl text-xs uppercase tracking-widest text-center shadow-lg hover:bg-white">
                            Update Results
                        </button>
                    </div>
                </div>
            </aside>

            <!-- Accessory Grid -->
            <div class="flex-1">
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <?php if ($selected_category && $selected_brand): ?>
                            <?= htmlspecialchars($selected_brand['name']) ?> - <?= htmlspecialchars($selected_category['name']) ?>
                        <?php elseif ($selected_brand): ?>
                            <?= htmlspecialchars($selected_brand['name']) ?> Accessories
                        <?php elseif ($selected_category): ?>
                            <?= htmlspecialchars($selected_category['name']) ?>
                        <?php else: ?>
                            All Accessories
                        <?php endif; ?>
                        <span class="text-sm font-normal text-gray-500 ml-2">(<?= count($accessories) ?> items)</span>
                    </h1>
                    <div class="flex items-center gap-4">
                        <label class="text-sm text-gray-500 font-bold">Sort by:</label>
                        <select onchange="window.location.href='accessories.php?<?= http_build_query(array_merge($_GET, ['sort' => ''])) ?>' + this.value" class="bg-white border text-sm rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-primary-500 font-medium">
                            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Newest Arrivals</option>
                            <option value="price_low" <?= $sort == 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_high" <?= $sort == 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="popular" <?= $sort == 'popular' ? 'selected' : '' ?>>Most Popular</option>
                        </select>
                    </div>
                </div>

                <?php if (empty($accessories)): ?>
                <div class="theme-card p-20 text-center">
                    <div class="text-6xl mb-4">🔎</div>
                    <h3 class="text-xl font-bold text-gray-900">No accessories found</h3>
                    <p class="text-gray-500 mt-2">Try adjusting your filters or search terms.</p>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <?php foreach($accessories as $p): ?>
                    <div class="group theme-card flex flex-col relative overflow-hidden transition-all duration-300 transform hover:-translate-y-2">
                        <a href="accessory-detail.php?id=<?= $p['id'] ?>" class="absolute inset-0 z-10"><span class="sr-only">View Accessory</span></a>
                        
                        <!-- Image Container -->
                        <div class="relative h-60 theme-inset overflow-hidden m-4 flex flex-col items-center justify-center p-6 bg-white">
                            <img src="<?= $p['main_image'] ?: 'https://via.placeholder.com/400x400?text=No+Image' ?>" 
                                 alt="<?= $p['name'] ?>" 
                                 class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-110"
                                 loading="lazy">
                            
                            <?php if($p['discount_price']): ?>
                            <span class="absolute top-4 left-4 bg-red-500 text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-widest shadow-lg shadow-red-200">Sale</span>
                            <?php endif; ?>
                            
                            <!-- Quick View Overlay -->
                            <div class="absolute inset-0 bg-primary-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        </div>

                        <!-- Accessory Info -->
                        <div class="px-8 pb-8 pt-2 text-center flex-1 flex flex-col">
                            <div class="text-[10px] text-primary-600 font-black uppercase tracking-[0.2em] mb-3">
                                <?= htmlspecialchars($p['category_name']) ?>
                                <?php if (!empty($p['brand_name'])): ?>
                                    <span class="mx-1 text-gray-300">•</span> <?= htmlspecialchars($p['brand_name']) ?>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="text-sm font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition-colors leading-tight line-clamp-2">
                                <?= htmlspecialchars($p['name']) ?>
                            </h3>

                            <div class="flex flex-col items-center gap-1 mt-auto">
                                <div class="flex items-center justify-center gap-3">
                                    <?php if($p['discount_price']): ?>
                                    <span class="text-2xl font-black text-gray-900">$<?= number_format($p['discount_price'], 2) ?></span>
                                    <span class="text-sm text-gray-400 line-through font-bold">$<?= number_format($p['price'], 2) ?></span>
                                    <?php else: ?>
                                    <span class="text-2xl font-black text-gray-900">$<?= number_format($p['price'], 2) ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex items-center gap-3 mt-4 w-full">
                                    <div class="flex-1 h-[1px] bg-gray-100"></div>
                                    <div class="flex text-yellow-400 text-[10px]">
                                        ★★★★★
                                    </div>
                                    <div class="flex-1 h-[1px] bg-gray-100"></div>
                                </div>
                                
                                <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest mt-3">
                                    Availability: <span class="<?= $p['stock_quantity'] > 0 ? 'text-green-500' : 'text-red-500' ?>"><?= $p['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock' ?></span>
                                </span>
                            </div>
                        </div>

                        <!-- Hover Action Button -->
                        <div class="px-8 pb-8 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-4 group-hover:translate-y-0 relative z-20">
                            <button class="w-full theme-button py-3 rounded-xl text-xs text-center uppercase tracking-widest block relative z-20 pointer-events-auto">
                                View Details
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
