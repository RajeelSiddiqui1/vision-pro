<?php
require_once 'config/db.php';
require_once 'includes/security.php';

// Admin Check
require_admin();

// Prevent caching
no_cache_headers();

// ─── Pagination Setup ────────────────────────────────────────────────────────
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// ─── Filter & Search Setup ───────────────────────────────────────────────────
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$params = [];
$where_clauses = [];

if ($search) {
    if (is_numeric($search)) {
        $where_clauses[] = "o.id = ?";
        $params[] = (int)$search;
    } else {
        $where_clauses[] = "(u.full_name LIKE ? OR u.email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
}

if ($status_filter) {
    $where_clauses[] = "o.status = ?";
    $params[] = $status_filter;
}

$where_sql = $where_clauses ? "WHERE " . implode(" AND ", $where_clauses) : "";

// ─── Fetch Data ──────────────────────────────────────────────────────────────
// Get Total Count for Pagination
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM orders o JOIN users u ON o.user_id = u.id $where_sql");
$count_stmt->execute($params);
$total_orders = $count_stmt->fetchColumn();
$total_pages = ceil($total_orders / $limit);

// Get Orders
$query = "SELECT o.*, u.full_name, u.email 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          $where_sql 
          ORDER BY o.created_at DESC 
          LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - VisionPro</title>
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
                    <h1 class="text-3xl lg:text-4xl font-black tracking-tighter text-gray-900 mb-1">Order <span class="gradient-text">Activity</span></h1>
                    <p class="text-gray-500 font-bold uppercase tracking-widest text-[9px]">Processing & Fulfillment Pipeline</p>
                </div>
            </header>

            <!-- Filters & Search -->
            <div class="flex flex-col lg:flex-row gap-4 mb-8">
                <form action="" method="GET" class="flex-1 flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="<?= e($search) ?>" 
                               placeholder="Search Tracking ID, Name or Email..." 
                               class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold text-sm">
                    </div>
                    
                    <select name="status" class="px-6 py-3.5 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold text-sm text-gray-500">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="processing" <?= $status_filter == 'processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="shipped" <?= $status_filter == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                        <option value="delivered" <?= $status_filter == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="cancelled" <?= $status_filter == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>

                    <button type="submit" class="bg-gray-900 text-white px-8 py-3.5 rounded-2xl font-black text-sm hover:bg-black transition-all shadow-xl shadow-gray-950/10">
                        Filter
                    </button>

                    <?php if($search || $status_filter): ?>
                        <a href="admin-orders.php" class="bg-gray-100 text-gray-500 px-6 py-3.5 rounded-2xl font-black text-sm hover:bg-gray-200 transition-all flex items-center justify-center">
                            Reset
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-container">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Tracking ID</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Client Details</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Amount Due</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Fulfillment</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach($orders as $o): ?>
                        <tr class="hover:bg-gray-50/20 transition-all group">
                            <td class="px-4 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center font-black text-gray-400 text-[10px]">#<?= $o['id'] ?></div>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase italic tracking-tighter"><?= date('M d, Y', strtotime($o['created_at'])) ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <div class="flex flex-col leading-tight">
                                    <p class="font-black text-gray-900 text-sm"><?= e($o['full_name']) ?></p>
                                    <p class="text-[9px] font-bold text-primary-500 lowercase tracking-tighter opacity-80"><?= e($o['email']) ?></p>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <span class="text-base font-black text-gray-900">$<?= number_format($o['total_amount'], 2) ?></span>
                            </td>
                            <td class="px-4 py-5">
                                <div class="relative inline-block w-full max-w-[120px]">
                                    <select 
                                        class="ajax-status-select w-full appearance-none px-3 py-1.5 text-[9px] font-black uppercase tracking-widest bg-gray-50/50 border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-primary-500/20 transition-all cursor-pointer"
                                        data-id="<?= $o['id'] ?>"
                                        data-endpoint="admin_api.php?type=order_status"
                                    >
                                        <option value="pending" <?= $o['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="processing" <?= $o['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="shipped" <?= $o['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                        <option value="delivered" <?= $o['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                        <option value="cancelled" <?= $o['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                        <i class="ri-arrow-down-s-line"></i>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-5 text-right">
                                <a href="admin-order-details.php?id=<?= $o['id'] ?>" class="inline-flex items-center gap-2 px-3 py-2 bg-gray-900 text-white rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-primary-600 transition-all shadow-lg shadow-gray-900/10">
                                    <i class="ri-eye-line text-sm"></i>
                                    Details
                                </a>
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
                        Showing <?= $offset + 1 ?>-<?= min($offset + $limit, $total_orders) ?> of <?= $total_orders ?> orders
                    </p>
                    <div class="flex gap-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&status=<?= $status_filter ?>" 
                               class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-500 hover:bg-primary-50 hover:text-primary-600 transition-all shadow-sm">
                                <i class="ri-arrow-left-s-line text-xl"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $status_filter ?>" 
                               class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-black transition-all shadow-sm
                                      <?= $i == $page 
                                          ? 'bg-primary-600 text-white' 
                                          : 'bg-white border border-gray-100 text-gray-500 hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&status=<?= $status_filter ?>" 
                               class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-500 hover:bg-primary-50 hover:text-primary-600 transition-all shadow-sm">
                                <i class="ri-arrow-right-s-line text-xl"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($orders)): ?>
                <div class="bg-white rounded-[2.5rem] p-24 text-center border border-dashed border-gray-200 mt-8">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-shopping-bag-2-line text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">No orders found</h3>
                    <p class="text-gray-500 font-bold text-sm mb-8">No records match your current search criteria.</p>
                    <a href="admin-orders.php" class="inline-flex items-center gap-2 text-primary-600 font-black text-sm hover:underline">
                        View all orders
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>
    <script src="assets/js/admin.js"></script>
</body>
</html>


