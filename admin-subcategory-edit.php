<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$subcategory = $stmt->fetch();

if (!$subcategory || $subcategory['parent_id'] === null) {
    header("Location: admin-subcategories.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $slug = strtolower(str_replace(' ', '-', $name));
    $brand_id = (int)$_POST['brand_id'];
    $parent_id = (int)$_POST['parent_id'];
    
    // Handle image upload
    $image_url = $subcategory['image_url'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/images/categories/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = 'sub-' . $slug . '-' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                if ($subcategory['image_url'] && file_exists($subcategory['image_url'])) unlink($subcategory['image_url']);
                $image_url = $upload_path;
            }
        }
    }

    $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=?, image_url=?, brand_id=?, parent_id=? WHERE id=?");
    if ($stmt->execute([$name, $slug, $image_url, $brand_id, $parent_id, $id])) {
        $success = "Specific Model updated successfully!";
        // Refresh local data
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $subcategory = $stmt->fetch();
    } else {
        $error = "Failed to update model.";
    }
}

$brands = $pdo->query("SELECT id, name FROM brands WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
// Get categories of the current brand for initial dropdown
$initial_groups = $pdo->prepare("SELECT id, name FROM categories WHERE brand_id = ? AND parent_id IS NULL");
$initial_groups->execute([$subcategory['brand_id']]);
$groups = $initial_groups->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Specific Model - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }

        async function updateModelGroups(brandId) {
            const groupSelect = document.getElementById('parent_id');
            groupSelect.innerHTML = '<option value="">Select Model Group</option>';
            
            if (!brandId) return;

            try {
                const response = await fetch(`admin_api.php?action=get_categories_by_brand&brand_id=${brandId}`);
                const categories = await response.json();
                
                categories.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.name;
                    groupSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error fetching categories:', error);
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <?php include 'includes/admin_sidebar.php'; ?>

        <main class="flex-1 p-10">
            <header class="mb-10 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Edit <span class="text-primary-600">Specific Model</span></h1>
                <a href="admin-subcategories.php" class="bg-white px-6 py-3 rounded-xl text-xs font-black text-gray-500 hover:text-primary-600 tracking-widest uppercase border border-gray-100 shadow-sm transition-all">← Back to List</a>
            </header>

            <?php if ($success): ?>
                <div class="bg-emerald-50 text-emerald-600 p-6 rounded-2xl mb-8 font-bold border border-emerald-100 flex items-center gap-3">
                    <i class="ri-checkbox-circle-fill text-xl"></i>
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-[3rem] shadow-sm border border-gray-200 p-12 max-w-2xl relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-primary-50 rounded-full blur-3xl opacity-50"></div>
                
                <form action="admin-subcategory-edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data" class="space-y-8 relative">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">1. Brand</label>
                            <select name="brand_id" required onchange="updateModelGroups(this.value)" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                                <?php foreach($brands as $brand): ?>
                                <option value="<?= $brand['id'] ?>" <?= $subcategory['brand_id'] == $brand['id'] ? 'selected' : '' ?>><?= htmlspecialchars($brand['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">2. Model Group</label>
                            <select id="parent_id" name="parent_id" required class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                                <?php foreach($groups as $group): ?>
                                <option value="<?= $group['id'] ?>" <?= $subcategory['parent_id'] == $group['id'] ? 'selected' : '' ?>><?= htmlspecialchars($group['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">3. Model Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($subcategory['name']) ?>" required class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Visual Asset</label>
                        <?php if ($subcategory['image_url']): ?>
                            <div class="mb-6 p-4 bg-gray-50 rounded-2xl border border-gray-100 w-fit">
                                <img src="<?= $subcategory['image_url'] ?>" class="h-16 w-auto object-cover rounded-xl">
                            </div>
                        <?php endif; ?>
                        <div class="relative group">
                            <input type="file" name="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="w-full px-6 py-8 border-2 border-dashed border-gray-200 rounded-3xl flex flex-col items-center justify-center gap-3 group-hover:bg-gray-50 group-hover:border-primary-300 transition-all">
                                <i class="ri-image-edit-line text-3xl text-gray-300 group-hover:text-primary-500"></i>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Replace model image</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-gray-900 text-white font-black py-5 rounded-2xl hover:bg-primary-600 shadow-xl shadow-gray-200 hover:shadow-primary-200 transition-all uppercase text-xs tracking-[0.3em]">
                            Update Registry Record
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
