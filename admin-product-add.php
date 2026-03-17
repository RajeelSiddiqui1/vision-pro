<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $slug = strtolower(str_replace(' ', '-', $name));
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $sku = htmlspecialchars($_POST['sku']);
    $part_number = htmlspecialchars($_POST['part_number']);
    $stock = (int)$_POST['stock'];
    $description = htmlspecialchars($_POST['description']);
    
    // Handle image upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/images/products/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = $slug . '-' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url = $upload_path;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid image format. Allowed: jpg, jpeg, png, gif, webp";
        }
    } else {
        $error = "Please select an image.";
    }
    
    // New wholesale fields
    $quality_tier = htmlspecialchars($_POST['quality_tier']);
    $warranty = htmlspecialchars($_POST['warranty']);
    $compatibility = htmlspecialchars($_POST['compatibility']);
    $brand_id = (int)$_POST['brand_id'];
    $bulk_pricing = $_POST['bulk_pricing']; // Store as JSON string

    if (empty($error) && $image_url) {
        $stmt = $pdo->prepare("INSERT INTO products (name, slug, category_id, brand_id, price, sku, part_number, stock_quantity, description, main_image, quality_tier, warranty, compatibility, bulk_pricing) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $slug, $category_id, $brand_id, $price, $sku, $part_number, $stock, $description, $image_url, $quality_tier, $warranty, $compatibility, $bulk_pricing])) {
            header("Location: admin-products.php?success=1");
            exit;
        } else {
            $error = "Failed to add product.";
        }
    }
}

$categories = []; // We will fetch these via AJAX based on Brand
$brands = [];
try {
    $brands = $pdo->query("SELECT * FROM brands WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
} catch (Exception $e) {
    $error = "Database issue: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }

        async function updateModelGroups(brandId) {
            const groupSelect = document.getElementById('category_id');
            const modelSelect = document.getElementById('subcategory_id');
            
            groupSelect.innerHTML = '<option value="">Select Model Group</option>';
            modelSelect.innerHTML = '<option value="">Select Specific Model</option>';
            
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
                console.error('Error fetching model groups:', error);
            }
        }

        async function updateSpecificModels(groupId) {
            const brandId = document.querySelector('select[name="brand_id"]').value;
            const modelSelect = document.getElementById('subcategory_id');
            modelSelect.innerHTML = '<option value="">Select Specific Model</option>';
            
            if (!groupId) return;

            try {
                const response = await fetch(`admin_api.php?action=get_subcategories_by_category&category_id=${groupId}&brand_id=${brandId}`);
                const models = await response.json();
                
                models.forEach(model => {
                    const option = document.createElement('option');
                    option.value = model.id;
                    option.textContent = model.name;
                    modelSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error fetching models:', error);
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <?php include 'includes/admin_sidebar.php'; ?>

        <main class="flex-1 p-10">
            <header class="mb-10 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Add New <span class="text-primary-600">Product</span></h1>
                <a href="admin-products.php" class="bg-white px-6 py-3 rounded-xl text-xs font-black text-gray-500 hover:text-primary-600 tracking-widest uppercase border border-gray-100 shadow-sm transition-all">← Back to List</a>
            </header>

            <?php if ($error): ?>
                <div class="bg-rose-50 text-rose-600 p-6 rounded-2xl mb-8 font-bold border border-rose-100 flex items-center gap-3">
                    <i class="ri-error-warning-fill text-xl"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-[3rem] shadow-sm border border-gray-200 p-12 max-w-5xl relative overflow-hidden">
                <div class="absolute -right-24 -top-24 w-80 h-80 bg-primary-50 rounded-full blur-3xl opacity-50"></div>
                
                <form action="admin-product-add.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-12 relative">
                    <div class="space-y-8">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Product Name</label>
                            <input type="text" name="name" required placeholder="e.g. iPhone 16 OLED Premium Display" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                        </div>

                        <div class="space-y-6 bg-gray-50/50 p-8 rounded-[2rem] border border-gray-100">
                            <h3 class="text-[10px] font-black text-primary-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <i class="ri-git-branch-line"></i> Inventory Hierarchy
                            </h3>
                            
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">1. Select Brand</label>
                                <select name="brand_id" required onchange="updateModelGroups(this.value)" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700">
                                    <option value="">Choose Brand</option>
                                    <?php foreach($brands as $brand): ?>
                                    <option value="<?= $brand['id'] ?>"><?= $brand['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">2. Model Group</label>
                                <select id="category_id" onchange="updateSpecificModels(this.value)" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700">
                                    <option value="">Select Model Group</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">3. Specific Model (Target Category)</label>
                                <select id="subcategory_id" name="category_id" required class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700">
                                    <option value="">Select Specific Model</option>
                                </select>
                                <p class="text-[9px] text-gray-400 mt-2 font-bold italic tracking-wide text-center">Selected Model will become the Product's Primary Category.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Price ($)</label>
                                <input type="number" step="0.01" name="price" required placeholder="0.00" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Initial Stock</label>
                                <input type="number" name="stock" required placeholder="0" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Product Visual</label>
                            <div class="relative group">
                                <input type="file" name="image" accept="image/*" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="w-full px-6 py-10 border-2 border-dashed border-gray-200 rounded-[2rem] flex flex-col items-center justify-center gap-3 group-hover:bg-gray-50 group-hover:border-primary-300 transition-all">
                                    <i class="ri-image-add-line text-4xl text-gray-300 group-hover:text-primary-500"></i>
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Select main image</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">SKU Code</label>
                                <input type="text" name="sku" required placeholder="VP-PRD-001" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 font-mono text-sm transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Internal Part #</label>
                                <input type="text" name="part_number" placeholder="PN-990-X" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 font-mono text-sm transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Description</label>
                            <textarea name="description" rows="3" placeholder="Detail high-performance features..." class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all"></textarea>
                        </div>

                        <div class="space-y-6 bg-gray-50/50 p-8 rounded-[2rem] border border-gray-100">
                            <h3 class="text-[10px] font-black text-primary-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <i class="ri-settings-3-line"></i> Technical Specifications
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Quality Tier</label>
                                    <input type="text" name="quality_tier" placeholder="Premium, Original" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Warranty</label>
                                    <input type="text" name="warranty" placeholder="1 Year, Lifetime" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Compatibility</label>
                                <input type="text" name="compatibility" placeholder="iPhone 16, 16 Pro" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Bulk Pricing (JSON)</label>
                                <textarea name="bulk_pricing" rows="2" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 font-mono text-[10px]" placeholder='[{"qty": 10, "price": 175.00}]'></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 pt-8 flex items-center gap-6">
                        <div class="flex-1 h-px bg-gray-100"></div>
                        <button type="submit" class="px-12 py-5 bg-gray-900 text-white font-black rounded-2xl hover:bg-primary-600 shadow-2xl shadow-gray-200 hover:shadow-primary-200 transition-all uppercase text-xs tracking-[0.3em] flex items-center gap-3">
                            <i class="ri-save-line text-lg"></i> Complete Registry
                        </button>
                        <div class="flex-1 h-px bg-gray-100"></div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>


