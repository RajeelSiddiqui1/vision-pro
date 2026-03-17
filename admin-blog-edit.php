<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) die("Blog post not found.");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $excerpt = htmlspecialchars($_POST['excerpt']);
    $content = $_POST['content'];
    $author = htmlspecialchars($_POST['author']);
    $featured_image = htmlspecialchars($_POST['featured_image']);
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE blog_posts SET title=?, excerpt=?, content=?, author=?, featured_image=?, status=? WHERE id=?");
    if ($stmt->execute([$title, $excerpt, $content, $author, $featured_image, $status, $id])) {
        $success = "Blog post updated successfully!";
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();
    } else {
        $error = "Failed to update blog post.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog - Admin</title>
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
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <?php include 'includes/admin_sidebar.php'; ?>

        <main class="flex-1 p-10">
            <header class="mb-10 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">Edit Blog Post</h1>
                <a href="admin-blogs.php" class="text-sm font-bold text-gray-500 hover:text-gray-800 tracking-widest uppercase">← Back to List</a>
            </header>

            <?php if ($success): ?>
                <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 font-bold"><?= $success ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 font-bold"><?= $error ?></div>
            <?php endif; ?>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-10 max-w-4xl">
                <form action="admin-blog-edit.php?id=<?= $id ?>" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Title</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Author</label>
                            <input type="text" name="author" value="<?= htmlspecialchars($post['author'] ?? 'VisionPro Team') ?>" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="archived" <?= $post['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Featured Image URL</label>
                        <input type="text" name="featured_image" value="<?= htmlspecialchars($post['featured_image'] ?? '') ?>" placeholder="https://example.com/image.jpg" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Excerpt (Short Description)</label>
                        <textarea name="excerpt" rows="3" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Content</label>
                        <textarea name="content" id="content" rows="10" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500 font-mono text-sm" placeholder="Write your blog content here... You can use HTML tags like &lt;p&gt;, &lt;br&gt;, &lt;b&gt;, &lt;i&gt;, &lt;ul&gt;, &lt;li&gt;, etc."><?= $post['content'] ?? '' ?></textarea>
                        <p class="text-xs text-gray-400 mt-2">Tip: You can use basic HTML tags: &lt;p&gt; for paragraphs, &lt;br&gt; for line breaks, &lt;b&gt; for bold, &lt;i&gt; for italic, &lt;ul&gt;&lt;li&gt; for lists, etc.</p>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full bg-[#0284c7] text-white font-bold py-4 px-6 rounded-xl hover:bg-[#0369a1] shadow-lg border border-[#0284c7] transition-all" style="background-color: #0284c7 !important; border: 2px solid #0284c7 !important; color: #ffffff !important;">
                            Update Blog Post
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>


