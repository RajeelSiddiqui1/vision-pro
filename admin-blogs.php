<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin-blogs.php");
    exit;
}

// Handle Status Toggle
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $stmt = $pdo->prepare("UPDATE blog_posts SET status = IF(status='published','draft','published') WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin-blogs.php");
    exit;
}

// Get All Posts
$posts = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blogs - Admin</title>
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
                    },
                },
            },
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="assets/images/visionpro-logo.jpeg">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <aside class="w-64 bg-gray-900 min-h-screen text-white p-6 sticky top-0">
            <h2 class="text-2xl font-bold mb-10 text-primary-400">
                <img src="assets/images/visionpro-logo.jpeg" alt="VisionPro" class="h-8 w-auto">
                <span class="text-white">Admin</span>
            </h2>
            <nav class="space-y-4">
                <a href="admin.php" class="block py-2 text-gray-400 hover:text-white">Dashboard</a>
                <a href="admin-products.php" class="block py-2 text-gray-400 hover:text-white">Products</a>
                <a href="admin-categories.php" class="block py-2 text-gray-400 hover:text-white">Categories</a>
                <a href="admin-orders.php" class="block py-2 text-gray-400 hover:text-white">Orders</a>
                <a href="admin-users.php" class="block py-2 text-gray-400 hover:text-white">Customers</a>
                <a href="admin-blogs.php" class="block py-2 text-primary-400 font-bold">Blogs</a>
                <a href="index.php" class="block py-2 text-gray-400 hover:text-white border-t border-gray-800 pt-4">View Site</a>
            </nav>
        </aside>

        <main class="flex-1 p-10">
            <header class="flex justify-between items-center mb-10">
                <h1 class="text-3xl font-bold text-gray-800">Blog Posts</h1>
                <a href="admin-blog-add.php" class="bg-primary-600 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-primary-200">Add New Blog</a>
            </header>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="p-6">Title</th>
                            <th class="p-6">Author</th>
                            <th class="p-6">Status</th>
                            <th class="p-6">Date</th>
                            <th class="p-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($posts as $p): ?>
                        <tr>
                            <td class="p-6">
                                <p class="font-bold text-gray-800"><?= htmlspecialchars($p['title']) ?></p>
                                <p class="text-sm text-gray-400">/blog/<?= $p['slug'] ?></p>
                            </td>
                            <td class="p-6 text-sm text-gray-600"><?= htmlspecialchars($p['author'] ?? 'Admin') ?></td>
                            <td class="p-6">
                                <span class="px-2 py-1 <?= $p['status'] === 'published' ? 'bg-green-100 text-green-700' : ($p['status'] === 'draft' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') ?> text-[10px] font-bold uppercase rounded">
                                    <?= $p['status'] ?>
                                </span>
                            </td>
                            <td class="p-6 text-sm text-gray-500"><?= date('M d, Y', strtotime($p['created_at'])) ?></td>
                            <td class="p-6 text-right space-x-2">
                                <a href="admin-blog-edit.php?id=<?= $p['id'] ?>" class="text-xs font-bold text-primary-600 hover:underline">Edit</a>
                                <a href="admin-blogs.php?toggle=<?= $p['id'] ?>" class="text-xs font-bold text-green-600 hover:underline"><?= $p['status'] === 'published' ? 'Draft' : 'Publish' ?></a>
                                <a href="admin-blogs.php?delete=<?= $p['id'] ?>" onclick="return confirm('Delete this blog post?')" class="text-xs font-bold text-red-500 hover:underline">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-400">No blog posts yet. <a href="admin-blog-add.php" class="text-primary-600 underline">Add your first blog</a></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
