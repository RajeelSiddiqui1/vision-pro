<?php
require_once 'config/db.php';
require_once 'includes/security.php';

// Admin Check
require_admin();

// Prevent caching
no_cache_headers();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $msg = "";
    
    // Check for dependencies (Products)
    $deps = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $deps->execute([$id]);
    
    if ($deps->fetchColumn() > 0) {
        $msg = "?error=has_products";
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $msg = "?success=deleted";
        } catch (PDOException $e) {
            $msg = "?error=db_error";
        }
    }
    header("Location: admin-subcategories.php" . $msg);
    exit;
}

// ─── Pagination Setup ────────────────────────────────────────────────────────
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// ─── Filter & Search Setup ───────────────────────────────────────────────────
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$brand_filter = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
$group_filter = isset($_GET['group']) ? (int)$_GET['group'] : 0;

$params = [];
$where_clauses = ["c.parent_id IS NOT NULL"]; // ONLY Level 2 Specific Models

if ($search) {
    $where_clauses[] = "c.name LIKE ?";
    $params[] = "%$search%";
}

if ($brand_filter) {
    $where_clauses[] = "c.brand_id = ?";
    $params[] = $brand_filter;
}

if ($group_filter) {
    $where_clauses[] = "c.parent_id = ?";
    $params[] = $group_filter;
}

$where_sql = $where_clauses ? "WHERE " . implode(" AND ", $where_clauses) : "";

// ─── Fetch Data ──────────────────────────────────────────────────────────────
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM categories c $where_sql");
$count_stmt->execute($params);
$total_subcategories = $count_stmt->fetchColumn();
$total_pages = ceil($total_subcategories / $limit);

$query = "SELECT c.*, b.name as brand_name, p.name as parent_name 
          FROM categories c 
          LEFT JOIN brands b ON c.brand_id = b.id 
          LEFT JOIN categories p ON c.parent_id = p.id
          $where_sql 
          ORDER BY c.name ASC 
          LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$subcategories = $stmt->fetchAll();

// Get Brands for filter
$brands = $pdo->query("SELECT id, name FROM brands ORDER BY name ASC")->fetchAll();
// Get Model Groups for filter
$groups = $pdo->query("SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Model Registry - VisionPro</title>
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

        <main class="flex-1 min-w-0 p-4 lg:p-8">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                <div class="relative">
                    <div class="absolute -left-4 top-0 bottom-0 w-1 bg-primary-500 rounded-full"></div>
                    <h1 class="text-3xl lg:text-4xl font-black tracking-tighter text-gray-900 mb-1">Model <span class="gradient-text">Registry</span></h1>
                    <p class="text-gray-500 font-bold uppercase tracking-widest text-[9px]">Specific Devices & Variations (Level 2)</p>
                </div>
                <a href="admin-subcategory-add.php" class="bg-primary-600 text-white px-8 py-4 rounded-2xl font-black text-sm shadow-xl shadow-primary-500/20 hover:bg-primary-700 transition-all flex items-center gap-2">
                    <i class="ri-node-tree text-lg"></i>
                    Register Specific Model
                </a>
            </header>

            <div class="flex flex-col lg:flex-row gap-4 mb-8">
                <form action="" method="GET" class="flex-1 flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                                placeholder="Search Specific Model..." 
                                class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold text-sm">
                    </div>
                    
                    <select name="brand" class="px-6 py-3.5 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold text-sm text-gray-500">
                        <option value="0">All Brands</option>
                        <?php foreach($brands as $brand): ?>
                            <option value="<?= $brand['id'] ?>" <?= $brand_filter == $brand['id'] ? 'selected' : '' ?>><?= htmlspecialchars($brand['name']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="group" class="px-6 py-3.5 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold text-sm text-gray-500">
                        <option value="0">All Groups</option>
                        <?php foreach($groups as $group): ?>
                            <option value="<?= $group['id'] ?>" <?= $group_filter == $group['id'] ? 'selected' : '' ?>><?= htmlspecialchars($group['name']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="bg-gray-900 text-white px-8 py-3.5 rounded-2xl font-black text-sm hover:bg-black transition-all shadow-xl shadow-gray-950/10">
                        Filter
                    </button>
                </form>
            </div>

            <div class="table-container">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Mark</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Model Name</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Hierarchy</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">System Slug</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach($subcategories as $sub): ?>
                        <tr class="hover:bg-gray-50/20 transition-all group">
                            <td class="px-4 py-5">
                                <div class="w-12 h-12 rounded-xl overflow-hidden border border-gray-100 shadow-sm transition-transform">
                                    <img src="<?= $sub['image_url'] ?: 'assets/images/placeholder.png' ?>" class="w-full h-full object-cover">
                                </div>
                            </td>
                            <td class="px-4 py-5 font-black text-gray-900 text-sm tracking-tighter"><?= htmlspecialchars($sub['name']) ?></td>
                            <td class="px-4 py-5">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[8px] font-black uppercase text-primary-500"><?= $sub['brand_name'] ?></span>
                                    <span class="text-[10px] font-bold text-gray-400">↳ <?= $sub['parent_name'] ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-5 text-[9px] font-bold text-gray-400 uppercase tracking-widest italic"><?= htmlspecialchars($sub['slug']) ?></td>
                            <td class="px-4 py-5 text-right">
                                <div class="flex justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="admin-subcategory-edit.php?id=<?= $sub['id'] ?>" class="w-8 h-8 bg-primary-50 text-primary-600 rounded-lg flex items-center justify-center hover:bg-primary-600 hover:text-white transition-all shadow-lg shadow-primary-500/5">
                                        <i class="ri-edit-line text-base"></i>
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
                        Showing <?= $offset + 1 ?>-<?= min($offset + $limit, $total_subcategories) ?> of <?= $total_subcategories ?> specific models
                    </p>
                    <div class="flex gap-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&brand=<?= $brand_filter ?>&group=<?= $group_filter ?>" 
                               class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-500 hover:bg-primary-50 hover:text-primary-600 transition-all shadow-sm">
                                <i class="ri-arrow-left-s-line text-xl"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&brand=<?= $brand_filter ?>&group=<?= $group_filter ?>" 
                               class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-black transition-all shadow-sm
                                      <?= $i == $page 
                                          ? 'bg-primary-600 text-white' 
                                          : 'bg-white border border-gray-100 text-gray-500 hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&brand=<?= $brand_filter ?>&group=<?= $group_filter ?>" 
                               class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-500 hover:bg-primary-50 hover:text-primary-600 transition-all shadow-sm">
                                <i class="ri-arrow-right-s-line text-xl"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
