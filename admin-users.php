<?php
require_once 'config/db.php';
require_once 'includes/security.php';

// Admin Check
require_admin();

// Prevent caching
no_cache_headers();

// Handle Delete/Role Change
if (isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    $msg = "";
    
    if ($_GET['action'] === 'delete' && $id != $_SESSION['user_id']) {
        // 1. Check if user is an admin
        $target = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $target->execute([$id]);
        $target_user = $target->fetch();
        
        if ($target_user && $target_user['role'] === 'admin') {
            $msg = "?error=admin_protect";
        } else {
            // 2. Check for dependencies (Orders)
            $deps = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
            $deps->execute([$id]);
            if ($deps->fetchColumn() > 0) {
                $msg = "?error=has_orders";
            } else {
                try {
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$id]);
                    $msg = "?success=deleted";
                } catch (PDOException $e) {
                    $msg = "?error=db_error";
                }
            }
        }
    } elseif ($_GET['action'] === 'toggle_role' && $id != $_SESSION['user_id']) {
        // Only allow demoting admin → user, NEVER promoting user → admin
        $target = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $target->execute([$id]);
        $target_user = $target->fetch();
        if ($target_user && $target_user['role'] === 'admin') {
            $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ?");
            $stmt->execute([$id]);
            $msg = "?success=role_updated";
        }
    }
    header("Location: admin-users.php" . $msg);
    exit;
}

// ─── Pagination Setup ────────────────────────────────────────────────────────
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// ─── Filter & Search Setup ───────────────────────────────────────────────────
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';

$params = [];
$where_clauses = [];

if ($search) {
    $where_clauses[] = "(full_name LIKE ? OR email LIKE ? OR business_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($role_filter) {
    if ($role_filter === 'admin') {
        $where_clauses[] = "role = 'admin'";
    } elseif ($role_filter === 'user') {
        $where_clauses[] = "role = 'user'";
    }
}

$where_sql = $where_clauses ? "WHERE " . implode(" AND ", $where_clauses) : "";

// ─── Fetch Data ──────────────────────────────────────────────────────────────
// Get Total Count for Pagination
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM users $where_sql");
$count_stmt->execute($params);
$total_users = $count_stmt->fetchColumn();
$total_pages = ceil($total_users / $limit);

// Get Users
$query = "SELECT * FROM users $where_sql ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - VisionPro</title>
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
                    <h1 class="text-3xl lg:text-4xl font-black tracking-tighter text-gray-900 mb-1">Client <span class="gradient-text">Base</span></h1>
                    <p class="text-gray-500 font-bold uppercase tracking-widest text-[9px]">User Accounts & Business Directory</p>
                </div>
            </header>

            <!-- Filters & Search -->
            <div class="flex flex-col lg:flex-row gap-4 mb-8">
                <form action="" method="GET" class="flex-1 flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="<?= e($search) ?>" 
                               placeholder="Search Identity, Email or Business..." 
                               class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold text-sm">
                    </div>
                    
                    <select name="role" class="px-6 py-3.5 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold text-sm text-gray-500">
                        <option value="">All Roles</option>
                        <option value="admin" <?= $role_filter == 'admin' ? 'selected' : '' ?>>Administrators</option>
                        <option value="user" <?= $role_filter == 'user' ? 'selected' : '' ?>>Standard Users</option>
                    </select>

                    <button type="submit" class="bg-gray-900 text-white px-8 py-3.5 rounded-2xl font-black text-sm hover:bg-black transition-all shadow-xl shadow-gray-950/10">
                        Filter
                    </button>

                    <?php if($search || $role_filter): ?>
                        <a href="admin-users.php" class="bg-gray-100 text-gray-500 px-6 py-3.5 rounded-2xl font-black text-sm hover:bg-gray-200 transition-all flex items-center justify-center">
                            Reset
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-container">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Client Identity</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Business profile</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Permission Level</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Registration</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach($users as $u): ?>
                        <tr class="hover:bg-gray-50/20 transition-all group">
                            <td class="px-4 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center font-black text-[10px]">
                                        <?= strtoupper(substr($u['full_name'], 0, 1)) ?>
                                    </div>
                                    <div class="flex flex-col leading-tight">
                                        <p class="font-black text-gray-900 text-sm group-hover:text-primary-600 transition-colors"><?= e($u['full_name']) ?></p>
                                        <p class="text-[9px] font-bold text-gray-400 lowercase tracking-tighter"><?= e($u['email']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <div class="flex items-center gap-2">
                                    <i class="ri-government-line text-gray-300"></i>
                                    <span class="text-sm font-bold text-gray-600"><?= e($u['business_name'] ?: 'Retail Client') ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <?php $isAdmin = $u['role'] === 'admin'; ?>
                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest <?= $isAdmin ? 'bg-purple-100 text-purple-600 border border-purple-200' : 'bg-blue-100 text-blue-600 border border-blue-200' ?>">
                                    <i class="<?= $isAdmin ? 'ri-shield-star-line' : 'ri-user-line' ?> mr-1"></i>
                                    <?= $u['role'] ?>
                                </span>
                            </td>
                            <td class="px-4 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-gray-900"><?= date('M d, Y', strtotime($u['created_at'])) ?></span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Active Member</span>
                                </div>
                            </td>
                            <td class="px-4 py-5 text-right">
                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                    <?php if($u['role'] === 'admin'): ?>
                                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-400 rounded-xl text-[10px] font-black uppercase tracking-widest italic border border-gray-100">
                                            <i class="ri-lock-2-line"></i> Protected
                                        </div>
                                    <?php else: ?>
                                        <a href="admin-users.php?action=delete&id=<?= $u['id'] ?>" onclick="smartDelete(this, 'Exterminate Account', 'Are you sure you want to permanently purge this client record? This action cannot be revoked.')" class="w-10 h-10 bg-red-50 text-red-500 rounded-xl inline-flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-lg shadow-red-500/5">
                                            <i class="ri-delete-bin-line text-lg"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-50 text-primary-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-primary-100">
                                        <i class="ri-user-smile-line"></i> Current Session
                                    </div>
                                <?php endif; ?>
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
                        Showing <?= $offset + 1 ?>-<?= min($offset + $limit, $total_users) ?> of <?= $total_users ?> users
                    </p>
                    <div class="flex gap-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&role=<?= $role_filter ?>" 
                               class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-500 hover:bg-primary-50 hover:text-primary-600 transition-all shadow-sm">
                                <i class="ri-arrow-left-s-line text-xl"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&role=<?= $role_filter ?>" 
                               class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-black transition-all shadow-sm
                                      <?= $i == $page 
                                          ? 'bg-primary-600 text-white' 
                                          : 'bg-white border border-gray-100 text-gray-500 hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&role=<?= $role_filter ?>" 
                               class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-500 hover:bg-primary-50 hover:text-primary-600 transition-all shadow-sm">
                                <i class="ri-arrow-right-s-line text-xl"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($users)): ?>
                <div class="bg-white rounded-[2.5rem] p-24 text-center border border-dashed border-gray-200 mt-8">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-user-search-line text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">No clients found</h3>
                    <p class="text-gray-500 font-bold text-sm mb-8">No user records match your current criteria.</p>
                    <a href="admin-users.php" class="inline-flex items-center gap-2 text-primary-600 font-black text-sm hover:underline">
                        View all clients
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>


