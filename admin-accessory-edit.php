<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$accessory = $stmt->fetch();

if (!$accessory) die("Accessory not found.");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $sku = htmlspecialchars($_POST['sku']);
    $part_number = htmlspecialchars($_POST['part_number']);
    $stock = (int)$_POST['stock'];
    $description = htmlspecialchars($_POST['description']);
    $image_url = htmlspecialchars($_POST['image_url']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // New wholesale fields
    $quality_tier = htmlspecialchars($_POST['quality_tier']);
    $warranty = htmlspecialchars($_POST['warranty']);
    $compatibility = htmlspecialchars($_POST['compatibility']);
    $bulk_pricing = $_POST['bulk_pricing']; // JSON string
    $brand_id = (int)$_POST['brand_id'];

    $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, brand_id=?, price=?, sku=?, part_number=?, stock_quantity=?, description=?, main_image=?, is_featured=?, quality_tier=?, warranty=?, compatibility=?, bulk_pricing=? WHERE id=?");
    if ($stmt->execute([$name, $category_id, $brand_id, $price, $sku, $part_number, $stock, $description, $image_url, $is_featured, $quality_tier, $warranty, $compatibility, $bulk_pricing, $id])) {
        $success = "Accessory updated successfully!";
        // Refresh local data
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $accessory = $stmt->fetch();
    } else {
        $error = "Failed to update accessory.";
    }
}

// Initial data for dropdowns
$model_group_id = null;
if ($accessory['category_id']) {
    $current_cat_stmt = $pdo->prepare("SELECT parent_id FROM categories WHERE id = ?");
    $current_cat_stmt->execute([$accessory['category_id']]);
    $model_group_id = $current_cat_stmt->fetchColumn();
}

$brands = $pdo->query("SELECT * FROM brands WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Accessory - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }

        async function updateModelGroups(brandId, selectedGroupId = null) {
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
                    if (selectedGroupId && cat.id == selectedGroupId) option.selected = true;
                    groupSelect.appendChild(option);
                });

                if (selectedGroupId) updateSpecificModels(selectedGroupId, <?= $accessory['category_id'] ?>);
            } catch (error) {
                console.error('Error fetching model groups:', error);
            }
        }

        async function updateSpecificModels(groupId, selectedModelId = null) {
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
                    if (selectedModelId && model.id == selectedModelId) option.selected = true;
                    modelSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error fetching models:', error);
            }
        }

        window.onload = () => {
            const initialBrand = document.querySelector('select[name="brand_id"]').value;
            if (initialBrand) {
                updateModelGroups(initialBrand, <?= json_encode($model_group_id) ?>);
            }
        };
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <?php include 'includes/admin_sidebar.php'; ?>

        <main class="flex-1 p-10">
            <header class="mb-10 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Edit <span class="text-primary-600">Accessory</span></h1>
                <a href="admin-accessories.php" class="bg-white px-6 py-3 rounded-xl text-xs font-black text-gray-500 hover:text-primary-600 tracking-widest uppercase border border-gray-100 shadow-sm transition-all">← Back to List</a>
            </header>

            <?php if ($success): ?>
                <div class="bg-emerald-50 text-emerald-600 p-6 rounded-2xl mb-8 font-bold border border-emerald-100 flex items-center gap-3">
                    <i class="ri-checkbox-circle-fill text-xl"></i>
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-[3rem] shadow-sm border border-gray-200 p-12 max-w-5xl relative overflow-hidden">
                <div class="absolute -right-24 -top-24 w-80 h-80 bg-primary-50 rounded-full blur-3xl opacity-50"></div>
                
                <form action="admin-accessory-edit.php?id=<?= $id ?>" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-12 relative">
                    <div class="space-y-8">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Accessory Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($accessory['name']) ?>" required class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                        </div>

                        <div class="space-y-6 bg-gray-50/50 p-8 rounded-[2rem] border border-gray-100">
                            <h3 class="text-[10px] font-black text-primary-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <i class="ri-git-branch-line"></i> Inventory Hierarchy
                            </h3>
                            
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">1. Brand</label>
                                <select name="brand_id" required onchange="updateModelGroups(this.value)" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700">
                                    <option value="">Choose Brand</option>
                                    <?php foreach($brands as $brand): ?>
                                    <option value="<?= $brand['id'] ?>" <?= $accessory['brand_id'] == $brand['id'] ? 'selected' : '' ?>><?= $brand['name'] ?></option>
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
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">3. Specific Model</label>
                                <select id="subcategory_id" name="category_id" required class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700">
                                    <option value="">Select Specific Model</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Price ($)</label>
                                <input type="number" step="0.01" name="price" value="<?= $accessory['price'] ?>" required class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Stock Count</label>
                                <input type="number" name="stock" value="<?= $accessory['stock_quantity'] ?>" required class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                            </div>
                        </div>

                        <div class="flex items-center gap-3 bg-primary-50/50 p-4 rounded-2xl border border-primary-100">
                            <input type="checkbox" name="is_featured" id="is_featured" <?= $accessory['is_featured'] ? 'checked' : '' ?> class="w-5 h-5 text-primary-600 border-gray-300 rounded-lg focus:ring-primary-500">
                            <label for="is_featured" class="text-xs font-black text-primary-700 uppercase tracking-widest cursor-pointer">Mark as Hot Selling</label>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Image Resource</label>
                            <input type="text" name="image_url" value="<?= htmlspecialchars($accessory['main_image']) ?>" placeholder="assets/images/accessories/..." class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">SKU</label>
                                <input type="text" name="sku" value="<?= htmlspecialchars($accessory['sku']) ?>" required class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 font-mono text-sm transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Part #</label>
                                <input type="text" name="part_number" value="<?= htmlspecialchars($accessory['part_number']) ?>" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 font-mono text-sm transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Description</label>
                            <textarea name="description" rows="3" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all"><?= htmlspecialchars($accessory['description']) ?></textarea>
                        </div>

                        <div class="space-y-6 bg-gray-50/50 p-8 rounded-[2rem] border border-gray-100">
                            <h3 class="text-[10px] font-black text-primary-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <i class="ri-settings-3-line"></i> Technical Specs
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Quality</label>
                                    <input type="text" name="quality_tier" value="<?= htmlspecialchars($accessory['quality_tier']) ?>" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Warranty</label>
                                    <input type="text" name="warranty" value="<?= htmlspecialchars($accessory['warranty']) ?>" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Bulk Pricing (JSON)</label>
                                <textarea name="bulk_pricing" rows="2" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 font-mono text-[10px]"><?= htmlspecialchars($accessory['bulk_pricing']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 pt-8 flex items-center gap-6">
                        <div class="flex-1 h-px bg-gray-100"></div>
                        <button type="submit" class="px-12 py-5 bg-gray-900 text-white font-black rounded-2xl hover:bg-primary-600 shadow-2xl shadow-gray-200 hover:shadow-primary-200 transition-all uppercase text-xs tracking-[0.2em] flex items-center gap-3">
                            <i class="ri-refresh-line text-lg"></i> Update Records
                        </button>
                        <div class="flex-1 h-px bg-gray-100"></div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>


