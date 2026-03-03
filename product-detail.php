<?php
session_start();
require_once 'config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit;
}

// Get related products
$rel_stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 4");
$rel_stmt->execute([$product['category_id'], $id]);
$related = $rel_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product['name'] ?> - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="assets/images/visionpro-logo.jpeg">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-12">
        <nav class="flex mb-8 text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="index.php" class="hover:text-primary-600">Home</a></li>
                <li><svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
                <li><a href="products.php" class="hover:text-primary-600">Products</a></li>
                <li><svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
                <li class="font-bold text-gray-900"><?= $product['name'] ?></li>
            </ol>
        </nav>

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="flex flex-col lg:flex-row">
                <!-- Product Gallery -->
                <div class="lg:w-1/2 p-12 bg-gray-50 flex items-center justify-center min-h-[400px] lg:min-h-[600px]">
                    <div class="relative w-full h-full flex items-center justify-center">
                        <img src="<?= $product['main_image'] ?: 'https://via.placeholder.com/600x600?text=No+Image' ?>" 
                             alt="<?= $product['name'] ?>" 
                             class="max-w-full max-h-[500px] object-contain rounded-3xl shadow-2xl transition-all duration-500 hover:scale-110">
                    </div>
                </div>

                <!-- Product Info -->
                <div class="lg:w-1/2 p-12 lg:border-l border-gray-100">
                    <div class="mb-4 flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-primary-100 text-primary-700 text-xs font-bold rounded-full uppercase tracking-widest"><?= $product['category_name'] ?></span>
                        <span class="px-3 py-1 bg-primary-600 text-white text-xs font-bold rounded-full uppercase tracking-widest glow-primary"><?= $product['quality_tier'] ?: 'Premium' ?></span>
                        <?php if($product['stock_quantity'] > 0): ?>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full uppercase tracking-widest italic">In Stock</span>
                        <?php endif; ?>
                    </div>
                    
                    <h1 class="text-4xl font-bold text-gray-900 mb-4 leading-tight"><?= $product['name'] ?></h1>
                    
                    <div class="flex items-center gap-4 mb-8">
                        <?php if($product['discount_price']): ?>
                        <span class="text-3xl font-bold text-gray-900">$<?= number_format($product['discount_price'], 2) ?></span>
                        <span class="text-lg text-gray-400 line-through">$<?= number_format($product['price'], 2) ?></span>
                        <?php else: ?>
                        <span class="text-3xl font-bold text-gray-900">$<?= number_format($product['price'], 2) ?></span>
                        <?php endif; ?>
                    </div>

                    <p class="text-gray-600 mb-6 leading-loose text-lg">
                        <?= $product['description'] ?>
                    </p>

                    <!-- Bulk Pricing Table -->
                    <?php 
                    $bulk = json_decode($product['bulk_pricing'], true);
                    if ($bulk && count($bulk) > 0): 
                    ?>
                    <div class="mb-8 overflow-hidden rounded-2xl border border-primary-100">
                        <table class="w-full text-sm">
                            <thead class="bg-primary-50 text-primary-700 font-bold uppercase text-[10px] tracking-widest">
                                <tr>
                                    <th class="px-4 py-3 text-left">Quantity</th>
                                    <th class="px-4 py-3 text-right">Price Per Unit</th>
                                    <th class="px-4 py-3 text-right">Savings</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-primary-50">
                                <?php foreach($bulk as $tier): ?>
                                <tr class="hover:bg-primary-50/30 transition-colors">
                                    <td class="px-4 py-3 font-bold text-gray-700"><?= $tier['qty'] ?>+ Units</td>
                                    <td class="px-4 py-3 text-right font-bold text-primary-600">$<?= number_format($tier['price'], 2) ?></td>
                                    <td class="px-4 py-3 text-right text-green-600 font-bold">
                                        Save <?= round((1 - ($tier['price'] / $product['price'])) * 100) ?>%
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>

                    <!-- Warranty & Compatibility -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center gap-4">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">🛡️</div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Warranty</p>
                                <p class="text-sm font-bold text-gray-800"><?= $product['warranty'] ?: 'Lifetime Warranty' ?></p>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center gap-4">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">🚚</div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Shipping</p>
                                <p class="text-sm font-bold text-gray-800">Same Day Delivery</p>
                            </div>
                        </div>
                    </div>

                    <?php if($product['compatibility']): ?>
                    <div class="mb-8 p-6 bg-primary-50 rounded-2xl border border-primary-100">
                        <h4 class="text-xs font-bold text-primary-700 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Compatibility
                        </h4>
                        <p class="text-sm text-primary-900 font-medium leading-relaxed">
                            <?= $product['compatibility'] ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <div class="space-y-6 pt-6 border-t border-gray-100">
                        <div class="bg-gray-50 p-6 rounded-2xl flex flex-wrap gap-8 items-center">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">SKU</p>
                                <p class="text-sm font-bold text-gray-800 tracking-wider"><?= $product['sku'] ?></p>
                            </div>
                            <?php if($product['part_number']): ?>
                            <div class="pl-8 border-l border-gray-200">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Part Number</p>
                                <p class="text-sm font-bold text-gray-800 tracking-wider"><?= $product['part_number'] ?></p>
                            </div>
                            <?php endif; ?>
                            <div class="pl-8 border-l border-gray-200">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">In Stock</p>
                                <p class="text-sm font-bold text-gray-800"><?= $product['stock_quantity'] ?> Units</p>
                            </div>
                        </div>

                        <form action="cart_action.php" method="POST" class="flex flex-col sm:flex-row gap-4">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="action" value="add">
                            
                            <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                                <button type="button" onclick="this.nextElementSibling.stepDown()" class="px-4 py-3 hover:bg-gray-100 text-gray-600 transition-colors">-</button>
                                <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>" class="w-16 text-center border-none outline-none font-bold">
                                <button type="button" onclick="this.previousElementSibling.stepUp()" class="px-4 py-3 hover:bg-gray-100 text-gray-600 transition-colors">+</button>
                            </div>

                            <button type="submit" class="flex-1 bg-primary-600 text-white font-bold py-4 rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-100 flex items-center justify-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if($related): ?>
        <section class="mt-20">
            <h2 class="text-2xl font-bold mb-8">Related Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach($related as $rel): ?>
                <a href="product-detail.php?id=<?= $rel['id'] ?>" class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all">
                    <div class="aspect-square bg-gray-50 flex items-center justify-center p-4">
                        <img src="<?= $rel['main_image'] ?: 'https://via.placeholder.com/300' ?>" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="p-4 border-t border-gray-50">
                        <h3 class="font-bold text-gray-800 text-sm truncate group-hover:text-primary-600"><?= $rel['name'] ?></h3>
                        <p class="text-primary-600 font-bold mt-1">$<?= number_format($rel['price'], 2) ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
