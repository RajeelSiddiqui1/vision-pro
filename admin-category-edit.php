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

if (!$category || $category['parent_id'] !== null) {
    header("Location: admin-categories.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $slug = strtolower(str_replace(' ', '-', $name));
    $brand_id = !empty($_POST['brand_id']) ? (int)$_POST['brand_id'] : null;
    
    // Handle image upload
    $image_url = $category['image_url'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/images/categories/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['image']['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];

        if (in_array($file_ext, $allowed_ext) && in_array($mime_type, $allowed_mimes)) {
            if ($_FILES['image']['size'] <= 5 * 1024 * 1024) { // 5MB limit
                $new_filename = 'group-' . $slug . '-' . time() . '.' . $file_ext;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    if ($category['image_url'] && file_exists($category['image_url'])) unlink($category['image_url']);
                    $image_url = $upload_path;
                } else {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "Image size exceeds the 5MB limit.";
            }
        } else {
             $error = "Invalid image format. Only JPG, PNG, and WEBP are allowed.";
        }
    }

    $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=?, image_url=?, brand_id=? WHERE id=?");
    if ($stmt->execute([$name, $slug, $image_url, $brand_id, $id])) {
        $success = "Model Group updated successfully!";
        // Refresh local data
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();
    } else {
        $error = "Failed to update group.";
    }
}

$brands = $pdo->query("SELECT id, name FROM brands WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Model Group - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <?php include 'includes/admin_sidebar.php'; ?>

        <main class="flex-1 p-10">
            <header class="mb-10 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Edit <span class="text-primary-600">Model Group</span></h1>
                <a href="admin-categories.php" class="bg-white px-6 py-3 rounded-xl text-xs font-black text-gray-500 hover:text-primary-600 tracking-widest uppercase border border-gray-100 shadow-sm transition-all">← Back to List</a>
            </header>

            <?php if ($success): ?>
                <div class="bg-emerald-50 text-emerald-600 p-6 rounded-2xl mb-8 font-bold border border-emerald-100 flex items-center gap-3">
                    <i class="ri-checkbox-circle-fill text-xl"></i>
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-[3rem] shadow-sm border border-gray-200 p-12 max-w-2xl relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-primary-50 rounded-full blur-3xl opacity-50"></div>
                
                <form action="admin-category-edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data" class="space-y-8 relative">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Target Brand</label>
                        <select name="brand_id" required class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                            <?php foreach($brands as $brand): ?>
                            <option value="<?= $brand['id'] ?>" <?= $category['brand_id'] == $brand['id'] ? 'selected' : '' ?>><?= htmlspecialchars($brand['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Group Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Visual Asset</label>
                        <?php if ($category['image_url']): ?>
                            <div class="mb-6 p-4 bg-gray-50 rounded-2xl border border-gray-100 w-fit">
                                <img src="<?= $category['image_url'] ?>" class="h-16 w-auto object-cover rounded-xl">
                            </div>
                        <?php endif; ?>
                        <div class="relative group">
                            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="w-full px-6 py-8 border-2 border-dashed border-gray-200 rounded-3xl flex flex-col items-center justify-center gap-3 group-hover:bg-gray-50 group-hover:border-primary-300 transition-all">
                                <i class="ri-image-edit-line text-3xl text-gray-300 group-hover:text-primary-500"></i>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Replace group image<br>Allowed: <span class="text-primary-600">JPG, PNG, WEBP</span> (Max 5MB)</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="w-full bg-gray-900 text-white font-black py-5 rounded-2xl hover:bg-primary-600 shadow-xl shadow-gray-200 hover:shadow-primary-200 transition-all uppercase text-xs tracking-[0.3em]">
                            Commit Changes
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>


