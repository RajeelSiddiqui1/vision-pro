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
    $bulk_pricing = $_POST['bulk_pricing']; // Store as JSON string

    if (empty($error) && $image_url) {
        $stmt = $pdo->prepare("INSERT INTO products (name, slug, category_id, price, sku, part_number, stock_quantity, description, main_image, quality_tier, warranty, compatibility, bulk_pricing) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $slug, $category_id, $price, $sku, $part_number, $stock, $description, $image_url, $quality_tier, $warranty, $compatibility, $bulk_pricing])) {
            $success = "Product added successfully!";
        } else {
            $error = "Failed to add product.";
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <a href="admin-products.php" class="block py-2 text-primary-400 font-bold">Products</a>
                <a href="admin-categories.php" class="block py-2 text-gray-400 hover:text-white">Categories</a>
                <a href="admin-orders.php" class="block py-2 text-gray-400 hover:text-white">Orders</a>
                <a href="admin-users.php" class="block py-2 text-gray-400 hover:text-white">Customers</a>
            </nav>
        </aside>

        <main class="flex-1 p-10">
            <header class="mb-10 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">Add New Product</h1>
                <a href="admin-products.php" class="text-sm font-bold text-gray-500 hover:text-gray-800 tracking-widest uppercase">← Back to List</a>
            </header>

            <?php if ($success): ?>
                <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 font-bold"><?= $success ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 font-bold"><?= $error ?></div>
            <?php endif; ?>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-10 max-w-4xl">
                <form action="admin-product-add.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Product Name</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                            <select name="category_id" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                                <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Price ($)</label>
                                <input type="number" step="0.01" name="price" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Stock</label>
                                <input type="number" name="stock" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">SKU</label>
                                <input type="text" name="sku" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Part Number</label>
                                <input type="text" name="part_number" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Product Image</label>
                            <input type="file" name="image" accept="image/*" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                            <p class="text-xs text-gray-500 mt-1">Allowed formats: jpg, jpeg, png, gif, webp</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Quality Tier</label>
                                <input type="text" name="quality_tier" placeholder="e.g. Premium, Original" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Warranty</label>
                                <input type="text" name="warranty" placeholder="e.g. 1 Year, Lifetime" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Compatibility</label>
                            <input type="text" name="compatibility" placeholder="e.g. iPhone 13, 13 Pro (A2638, A2483)" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Bulk Pricing (JSON)</label>
                            <textarea name="bulk_pricing" rows="3" class="w-full px-4 py-3 border rounded-xl font-mono text-xs outline-none focus:ring-2 focus:ring-primary-500" placeholder='[{"qty": 10, "price": 175.00}, {"qty": 50, "price": 160.00}]'></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="4" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                        </div>
                    </div>
                    <div class="md:col-span-2 pt-6">
                        <button type="submit" class="w-full bg-[#0284c7] text-white font-bold py-4 px-6 rounded-xl hover:bg-[#0369a1] shadow-lg border border-[#0284c7] transition-all" style="background-color: #0284c7 !important; border: 2px solid #0284c7 !important; color: #ffffff !important;">Create Product</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
