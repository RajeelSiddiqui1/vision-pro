<?php
require_once 'config/db.php';
require_once 'includes/security.php';

// Admin Check
require_admin();

// Prevent caching
no_cache_headers();

$success = '';
if (isset($_GET['success'])) {
    $success = "Accessory added successfully!";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $msg = "";
    
    // Check for dependencies (Order Items)
    // We try to see if order_items table exists and if it has this accessory
    try {
        $deps = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE accessory_id = ?");
        $deps->execute([$id]);
        if ($deps->fetchColumn() > 0) {
            $msg = "?error=has_orders";
        } else {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $msg = "?success=deleted";
        }
    } catch (PDOException $e) {
        // If order_items doesn't exist or other DB error
        try {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $msg = "?success=deleted";
        } catch (PDOException $ex) {
            $msg = "?error=db_error";
        }
    }
    header("Location: admin-accessories.php" . $msg);
    exit;
}

// ─── Pagination Setup ────────────────────────────────────────────────────────
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// ─── Filter & Search Setup ───────────────────────────────────────────────────
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';

$params = [];
$where_clauses = ["p.type = 'accessory'"];

if ($search) {
    $where_clauses[] = "(p.name LIKE ? OR p.sku LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_filter) {
    $where_clauses[] = "p.category_id = ?";
    $params[] = $category_filter;
}

$where_sql = $where_clauses ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Allowed sort columns
$allowed_sorts = ['name', 'price', 'stock_quantity', 'created_at'];
if (!in_array($sort, $allowed_sorts)) $sort = 'created_at';

// ─── Fetch Data ──────────────────────────────────────────────────────────────
// Get Total Count for Pagination
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products p $where_sql");
$count_stmt->execute($params);
$total_accessories = $count_stmt->fetchColumn();
$total_pages = ceil($total_accessories / $limit);

// Get Accessories
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          $where_sql 
          ORDER BY p.$sort $order 
          LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$accessories = $stmt->fetchAll();

// Get Categories for filter
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accessories - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc',
                            400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1',
                            800: '#075985', 900: '#0c4a6e', 950: '#082f49',
                        },
                    }
                },
            },
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.03);
        }
        .gradient-text {
            background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .table-container {
            background: white;
            border-radius: 2.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 20px 50px -20px rgba(0,0,0,0.05);
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-[#f1f5f9] min-h-screen font-sans text-gray-900 overflow-x-hidden">
    <div class="flex w-full">
        <?php include 'includes/admin_sidebar.php'; ?>

        <!-- Content -->
        <main class="flex-1 min-w-0 p-4 lg:p-8">

            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                <div class="relative">
                    <div class="absolute -left-4 top-0 bottom-0 w-1 bg-primary-500 rounded-full"></div>
                    <h1 class="text-3xl lg:text-4xl font-black tracking-tighter text-gray-900 mb-1">Accessory <span class="gradient-text">Gallery</span></h1>
                    <p class="text-gray-500 font-bold uppercase tracking-widest text-[9px]">Inventory & Stock Management</p>
                </div>
                <a href="admin-accessory-add.php" class="bg-primary-600 text-white px-6 py-3 rounded-2xl font-black text-xs shadow-xl shadow-primary-500/20 hover:bg-primary-700 transition-all flex items-center gap-2">
                    <i class="ri-add-circle-line text-lg"></i>
                    Add New Accessory
                </a>
            </header>

            <?php if ($success): ?>
                <div class="bg-green-50 text-green-600 p-4 rounded-2xl mb-8 font-bold flex items-center gap-3 border border-green-100 animate-pulse">
                    <i class="ri-checkbox-circle-line text-xl"></i>
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <!-- Filters & Search -->
            <div class="flex flex-col lg:flex-row gap-4 mb-8">
                <form action="" method="GET" class="flex-1 flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="<?= e($search) ?>" 
                               placeholder="Search name or SKU..." 
                               class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold text-sm">
                    </div>
                    
                    <select name="category" class="px-6 py-3.5 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold text-sm text-gray-500">
                        <option value="0">All Categories</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $category_filter == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="sort" class="px-6 py-3.5 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold text-sm text-gray-500">
                        <option value="created_at" <?= $sort == 'created_at' ? 'selected' : '' ?>>Newest First</option>
                        <option value="price" <?= $sort == 'price' ? 'selected' : '' ?>>Price</option>
                        <option value="stock_quantity" <?= $sort == 'stock_quantity' ? 'selected' : '' ?>>Stock Level</option>
                        <option value="name" <?= $sort == 'name' ? 'selected' : '' ?>>Name</option>
                    </select>

                    <button type="submit" class="bg-gray-900 text-white px-8 py-3.5 rounded-2xl font-black text-sm hover:bg-black transition-all shadow-xl shadow-gray-950/10">
                        Filter
                    </button>

                    <?php if($search || $category_filter || $sort != 'created_at'): ?>
                        <a href="admin-accessories.php" class="bg-gray-100 text-gray-500 px-6 py-3.5 rounded-2xl font-black text-sm hover:bg-gray-200 transition-all flex items-center justify-center">
                            Reset
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-container">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Accessory Details</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Category</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Market Price</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Stock Level</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach($accessories as $p): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-4 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="relative group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute inset-0 bg-primary-500/10 rounded-xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                        <img src="<?= $p['main_image'] ?: 'https://via.placeholder.com/100' ?>" class="w-12 h-12 rounded-xl object-cover bg-gray-50 relative border border-gray-100">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-black text-gray-900 group-hover:text-primary-600 transition-colors truncate max-w-[200px]"><?= $p['name'] ?></p>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter mt-0.5">SKU: <?= $p['sku'] ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <span class="bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full text-[9px] font-black uppercase whitespace-nowrap"><?= $p['category_name'] ?></span>
                            </td>
                            <td class="px-4 py-5 font-black text-gray-900 text-base whitespace-nowrap">$<?= number_format($p['price'], 2) ?></td>
                            <td class="px-4 py-5">
                                <?php $low_stock = $p['stock_quantity'] < 10; ?>
                                <div class="flex flex-col gap-0.5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full <?= $low_stock ? 'bg-red-500 animate-ping' : 'bg-green-500' ?>"></span>
                                        <span class="text-xs font-black <?= $low_stock ? 'text-red-600' : 'text-green-600' ?>">
                                            <?= $p['stock_quantity'] ?> Units
                                        </span>
                                    </div>
                                    <p class="text-[8px] font-bold text-gray-400 uppercase"><?= $low_stock ? 'Restock' : 'Ready' ?></p>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <label class="relative inline-flex items-center cursor-pointer group/toggle scale-90">
                                    <input type="checkbox" class="sr-only peer ajax-status-toggle" 
                                           data-id="<?= $p['id'] ?>" 
                                           data-endpoint="admin_api.php?type=accessory_status"
                                           <?= (isset($p['is_active']) && $p['is_active']) ? 'checked' : '' ?>>
                                    <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-600"></div>
                                </label>
                            </td>
                            <td class="px-4 py-5 text-right">
                                <div class="flex justify-end gap-1.5">
                                    <a href="admin-accessory-edit.php?id=<?= $p['id'] ?>" class="w-8 h-8 bg-primary-50 text-primary-600 rounded-lg flex items-center justify-center hover:bg-primary-600 hover:text-white transition-all shadow-lg shadow-primary-500/5">
                                        <i class="ri-edit-line text-base"></i>
                                    </a>
                                    <a href="admin-accessories.php?delete=<?= $p['id'] ?>" onclick="smartDelete(this, 'Purge Accessory', 'Are you sure you want to permanently remove this accessory from inventory? This action cannot be undone.')" class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-lg shadow-red-500/5">
                                        <i class="ri-delete-bin-line text-base"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="flex justify-between items-center mt-8 px-4">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        Showing <?= $offset + 1 ?>-<?= min($offset + $limit, $total_accessories) ?> of <?= $total_accessories ?> accessories
                    </p>
                    <div class="flex gap-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&category=<?= $category_filter ?>&sort=<?= $sort ?>" 
                               class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-500 hover:bg-primary-50 hover:text-primary-600 transition-all shadow-sm">
                                <i class="ri-arrow-left-s-line text-xl"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == 1 || $i == $total_pages || ($i >= $page - 1 && $i <= $page + 1)): ?>
                                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $category_filter ?>&sort=<?= $sort ?>" 
                                   class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-black transition-all shadow-sm
                                          <?= $i == $page 
                                              ? 'bg-primary-600 text-white' 
                                              : 'bg-white border border-gray-100 text-gray-500 hover:bg-gray-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php elseif ($i == $page - 2 || $i == $page + 2): ?>
                                <span class="w-10 h-10 flex items-center justify-center text-gray-400 font-bold">...</span>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&category=<?= $category_filter ?>&sort=<?= $sort ?>" 
                               class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-500 hover:bg-primary-50 hover:text-primary-600 transition-all shadow-sm">
                                <i class="ri-arrow-right-s-line text-xl"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($accessories)): ?>
                <div class="bg-white rounded-[2.5rem] p-24 text-center border border-dashed border-gray-200 mt-8">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-search-2-line text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">No matching accessories found</h3>
                    <p class="text-gray-500 font-bold text-sm mb-8">Try adjusting your search filters to find what you're looking for.</p>
                    <a href="admin-accessories.php" class="inline-flex items-center gap-2 text-primary-600 font-black text-sm hover:underline">
                        Clear all filters
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>
    <script src="assets/js/admin.js"></script>
</body>
</html>

