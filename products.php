<?php
session_start();
require_once 'config/db.php';

// Filter logic
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($category_id) {
    $query .= " AND p.category_id = ?";
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
$products = $stmt->fetchAll();

// Get categories for sidebar
$cat_stmt = $pdo->query("SELECT * FROM categories");
$categories = $cat_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - VisionPro LCD Refurbishing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="assets/images/visionpro-logo.jpeg">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-12 reveal">
        <div class="flex flex-col lg:flex-row gap-8 stagger-reveal">
            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-64 space-y-8">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Categories</h3>
                    <div class="space-y-2">
                        <a href="products.php" class="block py-2 px-3 rounded-lg <?= !$category_id ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-600 hover:bg-gray-100' ?>">All Categories</a>
                        <?php foreach($categories as $cat): ?>
                        <a href="products.php?category=<?= $cat['id'] ?>" class="block py-2 px-3 rounded-lg <?= $category_id == $cat['id'] ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-600 hover:bg-gray-100' ?>"><?= $cat['name'] ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Price Range</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <input type="number" placeholder="Min" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <span>-</span>
                            <input type="number" placeholder="Max" class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                        <button class="w-full py-2 bg-gray-900 text-white rounded-lg text-sm font-bold shadow-md hover:bg-gray-800 transition-all">Apply</button>
                    </div>
                </div>
            </aside>

            <!-- Product Grid -->
            <div class="flex-1">
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <?= $category_id ? 'Category Results' : 'All Products' ?>
                        <span class="text-sm font-normal text-gray-500 ml-2">(<?= count($products) ?> items)</span>
                    </h1>
                    <div class="flex items-center gap-4">
                        <label class="text-sm text-gray-500">Sort by:</label>
                        <select onchange="window.location.href='products.php?sort=' + this.value" class="bg-white border text-sm rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Newest Arrivals</option>
                            <option value="price_low" <?= $sort == 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_high" <?= $sort == 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="popular" <?= $sort == 'popular' ? 'selected' : '' ?>>Most Popular</option>
                        </select>
                    </div>
                </div>

                <?php if (empty($products)): ?>
                <div class="bg-white rounded-2xl p-20 text-center border border-dashed border-gray-200">
                    <div class="text-6xl mb-4">🔎</div>
                    <h3 class="text-xl font-bold text-gray-900">No products found</h3>
                    <p class="text-gray-500 mt-2">Try adjusting your filters or search terms.</p>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                    <?php foreach($products as $p): ?>
                    <div class="group bg-white rounded-2xl border border-gray-100 overflow-hidden transition-all hover:shadow-2xl hover:-translate-y-1 relative">
                        <a href="product-detail.php?id=<?= $p['id'] ?>" class="absolute inset-0 z-10"><span class="sr-only">View Product</span></a>
                        <div class="relative h-64 bg-gray-100 overflow-hidden">
                            <img src="<?= $p['main_image'] ?: 'https://via.placeholder.com/400x400?text=No+Image' ?>" alt="<?= $p['name'] ?>" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                            <?php if($p['discount_price']): ?>
                            <span class="absolute top-4 left-4 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase">Sale</span>
                            <?php endif; ?>
                            <button class="absolute bottom-4 right-4 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg text-primary-600 hover:bg-primary-600 hover:text-white transition-all transform translate-y-12 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 z-20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                        <div class="p-6">
                            <div class="text-xs text-primary-600 font-bold uppercase tracking-wider mb-2"><?= $p['category_name'] ?></div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors leading-tight">
                                <?= $p['name'] ?>
                            </h3>
                            <div class="flex items-center gap-3">
                                <?php if($p['discount_price']): ?>
                                <span class="text-xl font-bold text-gray-900">$<?= number_format($p['discount_price'], 2) ?></span>
                                <span class="text-sm text-gray-400 line-through">$<?= number_format($p['price'], 2) ?></span>
                                <?php else: ?>
                                <span class="text-xl font-bold text-gray-900">$<?= number_format($p['price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="mt-4 flex items-center gap-2">
                                <div class="flex text-yellow-400 text-xs">
                                    ★★★★★
                                </div>
                                <span class="text-[10px] text-gray-400 uppercase tracking-tighter">In Stock: <?= $p['stock_quantity'] ?></span>
                            </div>
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
