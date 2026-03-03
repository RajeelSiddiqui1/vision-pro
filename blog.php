<?php
session_start();
require_once 'config/db.php';

// Get published blog posts
$posts = $pdo->query("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - VisionPro Industry Insights</title>
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

    <main class="container mx-auto px-4 py-12">
        <div class="text-center mb-16">
            <span class="text-primary-600 font-bold uppercase tracking-widest text-sm">VisionPro Blog</span>
            <h1 class="text-4xl font-bold mt-2">Industry Insights & Updates</h1>
        </div>

        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Main Content -->
            <div class="lg:w-2/3 space-y-8">
                <?php if (empty($posts)): ?>
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 text-center">
                        <p class="text-gray-500 mb-4">No blog posts yet.</p>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <a href="admin-blog-add.php" class="text-primary-600 font-bold hover:underline">Add a blog post from admin panel</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php foreach($posts as $post): ?>
                    <article class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <?php if ($post['featured_image']): ?>
                        <img src="<?= htmlspecialchars($post['featured_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-64 object-cover rounded-xl mb-6">
                        <?php endif; ?>
                        <span class="bg-primary-50 text-primary-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide mb-4 inline-block">Blog</span>
                        <h2 class="text-2xl font-bold mb-3 hover:text-primary-600 cursor-pointer transition-colors"><?= htmlspecialchars($post['title']) ?></h2>
                        <?php if ($post['excerpt']): ?>
                        <p class="text-gray-500 mb-6 leading-relaxed"><?= htmlspecialchars($post['excerpt']) ?></p>
                        <?php endif; ?>
                        <div class="flex items-center justify-between border-t border-gray-100 pt-6">
                            <span class="text-sm text-gray-400 font-medium"><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
                            <a href="blog-detail.php?id=<?= $post['id'] ?>" class="text-primary-600 font-bold hover:underline">Read Full Article →</a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="lg:w-1/3 space-y-8">
                <!-- Search -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">Search</h3>
                    <input type="text" placeholder="Search articles..." class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500">
                </div>

                <!-- Categories -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">Categories</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-2 bg-gray-50 rounded-lg text-sm text-gray-600 hover:bg-gray-100 cursor-pointer">Repair Tips</span>
                        <span class="px-3 py-2 bg-gray-50 rounded-lg text-sm text-gray-600 hover:bg-gray-100 cursor-pointer">Industry News</span>
                        <span class="px-3 py-2 bg-gray-50 rounded-lg text-sm text-gray-600 hover:bg-gray-100 cursor-pointer">Company Updates</span>
                        <span class="px-3 py-2 bg-gray-50 rounded-lg text-sm text-gray-600 hover:bg-gray-100 cursor-pointer">Product Spotlights</span>
                    </div>
                </div>

                 <!-- Newsletter -->
                <div class="bg-primary-900 p-6 rounded-2xl shadow-lg text-white">
                    <h3 class="font-bold mb-2">Subscribe</h3>
                    <p class="text-primary-200 text-sm mb-4">Get the latest repair tips delivered to your inbox.</p>
                    <input type="email" placeholder="Email address" class="w-full bg-white/10 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-white mb-3 text-white placeholder-primary-300">
                    <button class="w-full bg-white text-primary-900 font-bold py-3 rounded-xl hover:bg-primary-50 transition-colors">Join</button>
                </div>
            </aside>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
