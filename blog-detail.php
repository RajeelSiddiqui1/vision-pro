<?php
session_start();
require_once 'config/db.php';

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    header("Location: blog.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - VisionPro Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">\n <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-12 max-w-4xl">
        <a href="blog.php" class="text-primary-600 font-bold hover:underline mb-6 inline-block">← Back to Blog</a>
        
        <article class="bg-white p-8 md:p-12 rounded-3xl shadow-sm border border-gray-100">
            <?php if ($post['featured_image']): ?>
            <img src="<?= htmlspecialchars($post['featured_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-64 md:h-96 object-cover rounded-xl mb-8">
            <?php endif; ?>
            
            <div class="flex items-center gap-4 mb-6 text-sm text-gray-500">
                <span class="bg-primary-50 text-primary-700 px-3 py-1 rounded-full font-bold uppercase tracking-wide">Blog</span>
                <span><?= date('F d, Y', strtotime($post['created_at'])) ?></span>
                <?php if ($post['author']): ?>
                <span>by <?= htmlspecialchars($post['author']) ?></span>
                <?php endif; ?>
            </div>
            
            <h1 class="text-3xl md:text-4xl font-bold mb-6"><?= htmlspecialchars($post['title']) ?></h1>
            
            <?php if ($post['excerpt']): ?>
            <p class="text-xl text-gray-500 mb-8 leading-relaxed font-medium"><?= htmlspecialchars($post['excerpt']) ?></p>
            <?php endif; ?>
            
            <div class="prose prose-lg max-w-none">
                <?= $post['content'] ?>
            </div>
        </article>
        
        <!-- Related Posts -->
        <?php
        $related = $pdo->prepare("SELECT * FROM blog_posts WHERE status = 'published' AND id != ? ORDER BY created_at DESC LIMIT 3");
        $related->execute([$id]);
        $related_posts = $related->fetchAll();
        ?>
        
        <?php if (!empty($related_posts)): ?>
        <div class="mt-16">
            <h2 class="text-2xl font-bold mb-8">Related Articles</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($related_posts as $rpost): ?>
                <a href="blog-detail.php?id=<?= $rpost['id'] ?>" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow block">
                    <?php if ($rpost['featured_image']): ?>
                    <img src="<?= htmlspecialchars($rpost['featured_image']) ?>" alt="<?= htmlspecialchars($rpost['title']) ?>" class="w-full h-32 object-cover rounded-xl mb-4">
                    <?php endif; ?>
                    <h3 class="font-bold text-gray-900 mb-2"><?= htmlspecialchars($rpost['title']) ?></h3>
                    <p class="text-sm text-gray-500"><?= date('M d, Y', strtotime($rpost['created_at'])) ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

