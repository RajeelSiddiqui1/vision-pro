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
    try {
        $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "?success=deleted";
    } catch (PDOException $e) {
        $msg = "?error=db_error";
    }
    header("Location: admin-blogs.php" . $msg);
    exit;
}

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
    $where_clauses[] = "(title LIKE ? OR author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status_filter) {
    $where_clauses[] = "status = ?";
    $params[] = $status_filter;
}

$where_sql = $where_clauses ? "WHERE " . implode(" AND ", $where_clauses) : "";

// ─── Fetch Data ──────────────────────────────────────────────────────────────
// Get Total Count for Pagination
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts $where_sql");
$count_stmt->execute($params);
$total_posts = $count_stmt->fetchColumn();
$total_pages = ceil($total_posts / $limit);

// Get Posts
$query = "SELECT * FROM blog_posts $where_sql ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blogs - VisionPro</title>
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

        <main class="flex-1 min-w-0 p-4 lg:p-8">

            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                <div class="relative">
                    <div class="absolute -left-4 top-0 bottom-0 w-1 bg-primary-500 rounded-full"></div>
                    <h1 class="text-3xl lg:text-4xl font-black tracking-tighter text-gray-900 mb-1">Content <span class="gradient-text">Engine</span></h1>
                    <p class="text-gray-500 font-bold uppercase tracking-widest text-[9px]">Blog Management & Corporate Narrative</p>
                </div>
                <a href="admin-blog-add.php" class="bg-primary-600 text-white px-6 py-3 rounded-2xl font-black text-xs shadow-xl shadow-primary-500/20 hover:bg-primary-700 transition-all flex items-center gap-2">
                    <i class="ri-add-line text-lg"></i>
                    Craft New Story
                </a>
            </header>

            <!-- Filters & Search -->
            <div class="flex flex-col lg:flex-row gap-4 mb-8">
                <form action="" method="GET" class="flex-1 flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="<?= e($search) ?>" 
                               placeholder="Search Title or Author..." 
                               class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold text-sm">
                    </div>
                    
                    <select name="status" class="px-6 py-3.5 bg-white border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold text-sm text-gray-500">
                        <option value="">All Statuses</option>
                        <option value="published" <?= $status_filter == 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="draft" <?= $status_filter == 'draft' ? 'selected' : '' ?>>Draft</option>
                    </select>

                    <button type="submit" class="bg-gray-900 text-white px-8 py-3.5 rounded-2xl font-black text-sm hover:bg-black transition-all shadow-xl shadow-gray-950/10">
                        Filter
                    </button>

                    <?php if($search || $status_filter): ?>
                        <a href="admin-blogs.php" class="bg-gray-100 text-gray-500 px-6 py-3.5 rounded-2xl font-black text-sm hover:bg-gray-200 transition-all flex items-center justify-center">
                            Reset
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-container">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Story Title & Slug</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Narrator</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Visibility</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest">Release Date</th>
                            <th class="px-4 py-5 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach($posts as $p): ?>
                        <tr class="hover:bg-gray-50/20 transition-all group">
                            <td class="px-4 py-5">
                                <div class="flex flex-col leading-tight">
                                    <p class="font-black text-gray-900 group-hover:text-primary-600 transition-colors"><?= e($p['title']) ?></p>
                                    <p class="text-[9px] font-bold text-gray-400 italic mt-0.5">/blog/<?= $p['slug'] ?></p>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <div class="flex items-center gap-2 text-xs font-bold text-gray-600">
                                    <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-[10px] text-gray-400 font-black">
                                        <?= strtoupper(substr($p['author'] ?? 'A', 0, 1)) ?>
                                    </div>
                                    <span class="truncate max-w-[100px]"><?= e($p['author'] ?? 'Admin') ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <?php $isPublished = $p['status'] === 'published'; ?>
                                <div class="relative inline-block w-full max-w-[140px]">
                                    <select 
                                        class="ajax-status-select w-full appearance-none px-4 py-2 text-[10px] font-black uppercase tracking-widest border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-primary-500/20 transition-all cursor-pointer <?= $isPublished ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600' ?>"
                                        data-id="<?= $p['id'] ?>"
                                        data-endpoint="admin_api.php?type=blog_status"
                                    >
                                        <option value="published" <?= $p['status'] === 'published' ? 'selected' : '' ?>>Live</option>
                                        <option value="draft" <?= $p['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                    </select>
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none opacity-50">
                                        <i class="ri-arrow-down-s-line"></i>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-xs font-black text-gray-900"><?= date('M d, Y', strtotime($p['created_at'])) ?></span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="admin-blog-edit.php?id=<?= $p['id'] ?>" class="w-10 h-10 bg-primary-50 text-primary-600 rounded-xl flex items-center justify-center hover:bg-primary-600 hover:text-white transition-all">
                                        <i class="ri-edit-3-line text-lg"></i>
                                    </a>
                                    <a href="admin-blogs.php?delete=<?= $p['id'] ?>" onclick="smartDelete(this, 'Archive Narrative', 'Are you sure you want to permanently purge this post from the content engine? Recovering this data will not be possible.')" class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                        <i class="ri-delete-bin-7-line text-lg"></i>
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
                        Showing <?= $offset + 1 ?>-<?= min($offset + $limit, $total_posts) ?> of <?= $total_posts ?> posts
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

            <?php if (empty($posts)): ?>
                <div class="bg-white rounded-[2.5rem] p-24 text-center border border-dashed border-gray-200 mt-8">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-article-line text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">No narratives found</h3>
                    <p class="text-gray-500 font-bold text-sm mb-8">No blog posts match your current search registry.</p>
                    <a href="admin-blogs.php" class="inline-flex items-center gap-2 text-primary-600 font-black text-sm hover:underline">
                        View all stories
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>
    <script src="assets/js/admin.js"></script>
</body>
</html>


