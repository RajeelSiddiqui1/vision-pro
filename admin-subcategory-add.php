<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $slug = strtolower(str_replace(' ', '-', $name));
    $brand_id = (int)$_POST['brand_id'];
    $parent_id = (int)$_POST['parent_id']; // This is the Level 1 category
    
    // Handle image upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/images/categories/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['image']['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];

        if (in_array($file_ext, $allowed_ext) && in_array($mime_type, $allowed_mimes)) {
            if ($_FILES['image']['size'] <= 5 * 1024 * 1024) { // 5MB limit
                $new_filename = 'sub-' . $slug . '-' . time() . '.' . $file_ext;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
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
    } else {
        $error = "Please select an image.";
    }
    
    if (empty($error) && $image_url) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, image_url, brand_id, parent_id) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $slug, $image_url, $brand_id, $parent_id])) {
            $success = "Specific Model added successfully!";
        } else {
            $error = "Failed to add subcategory.";
        }
    }
}

// Get all brands
$brands = $pdo->query("SELECT id, name FROM brands WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Specific Model - Admin</title>
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
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Add <span class="text-primary-600">Specific Model</span> <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">(Level 2)</span></h1>
                <a href="admin-categories.php" class="bg-white px-6 py-3 rounded-xl text-xs font-black text-gray-500 hover:text-primary-600 tracking-widest uppercase border border-gray-100 shadow-sm transition-all">← Back to List</a>
            </header>

            <?php if ($success): ?>
                <div class="bg-emerald-50 text-emerald-600 p-6 rounded-2xl mb-8 font-bold border border-emerald-100 animate-pulse flex items-center gap-3">
                    <i class="ri-checkbox-circle-fill text-xl"></i>
                    <?= $success ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-rose-50 text-rose-600 p-6 rounded-2xl mb-8 font-bold border border-rose-100 flex items-center gap-3">
                    <i class="ri-error-warning-fill text-xl"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-[3rem] shadow-sm border border-gray-200 p-12 max-w-2xl relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-primary-50 rounded-full blur-3xl opacity-50"></div>
                
                <form action="admin-subcategory-add.php" method="POST" enctype="multipart/form-data" class="space-y-8 relative">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">1. Brand</label>
                            <select name="brand_id" required onchange="updateModelGroups(this.value)" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                                <option value="">Select Brand</option>
                                <?php foreach($brands as $brand): ?>
                                <option value="<?= $brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">2. Model Group</label>
                            <select id="parent_id" name="parent_id" required class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                                <option value="">Select Brand First</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">3. Model Name</label>
                        <input type="text" name="name" required placeholder="e.g. iPhone 16 Pro Max, S24 Ultra" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Visual Asset</label>
                        <div class="relative group">
                            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="w-full px-6 py-8 border-2 border-dashed border-gray-200 rounded-3xl flex flex-col items-center justify-center gap-3 group-hover:bg-gray-50 group-hover:border-primary-300 transition-all">
                                <i class="ri-image-add-line text-3xl text-gray-300 group-hover:text-primary-500"></i>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Select specific model image<br>Allowed: <span class="text-primary-600">JPG, PNG, WEBP</span> (Max 5MB)</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-gray-900 text-white font-black py-5 rounded-2xl hover:bg-primary-600 shadow-xl shadow-gray-200 hover:shadow-primary-200 transition-all uppercase text-xs tracking-[0.3em]">
                            Register Specific Model
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
