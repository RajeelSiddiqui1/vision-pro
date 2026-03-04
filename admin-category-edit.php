<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) die("Category not found.");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $slug = htmlspecialchars($_POST['slug']);
    $image_url = htmlspecialchars($_POST['image_url']);

    $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=?, image_url=? WHERE id=?");
    if ($stmt->execute([$name, $slug, $image_url, $id])) {
        $success = "Category updated successfully!";
        // Refresh local data
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();
    } else {
        $error = "Failed to update category.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <aside class="w-64 bg-gray-900 min-h-screen text-white p-6 sticky top-0">
            <h2 class="text-2xl font-bold mb-10 text-primary-400">VisionPro Admin</h2>
            <nav class="space-y-4">
                <a href="admin.php" class="block py-2 text-gray-400 hover:text-white">Dashboard</a>
                <a href="admin-products.php" class="block py-2 text-gray-400 hover:text-white">Products</a>
                <a href="admin-categories.php" class="block py-2 text-primary-400 font-bold">Categories</a>
            </nav>
        </aside>

        <main class="flex-1 p-10">
            <header class="mb-10 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">Edit Category</h1>
                <a href="admin-categories.php" class="text-sm font-bold text-gray-500 hover:text-gray-800 tracking-widest uppercase">← Back to List</a>
            </header>

            <?php if ($success): ?>
                <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 font-bold"><?= $success ?></div>
            <?php endif; ?>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-10 max-w-2xl">
                <form action="admin-category-edit.php?id=<?= $id ?>" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Category Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Slug</label>
                        <input type="text" name="slug" value="<?= htmlspecialchars($category['slug']) ?>" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Image URL</label>
                        <input type="text" name="image_url" value="<?= htmlspecialchars($category['image_url']) ?>" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                        <?php if($category['image_url']): ?>
                        <img src="<?= $category['image_url'] ?>" class="mt-4 w-32 h-32 object-cover rounded-xl border">
                        <?php endif; ?>
                    </div>
                    <div class="pt-6">
                        <button type="submit" class="w-full bg-primary-600 text-white font-bold py-4 rounded-xl hover:bg-primary-700 shadow-lg shadow-primary-200 transition-all">Update Category</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>

