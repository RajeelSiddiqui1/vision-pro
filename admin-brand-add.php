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
    $description = htmlspecialchars($_POST['description']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Handle logo upload
    $logo_url = '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/images/brands/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'svg']; // Explicitly allowing these
        
        // Additional MIME check for robust validation
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['logo']['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];

        if (in_array($file_ext, $allowed_ext) && in_array($mime_type, $allowed_mimes)) {
            if ($_FILES['logo']['size'] <= 5 * 1024 * 1024) { // 5MB limit
                $new_filename = $slug . '-' . time() . '.' . $file_ext;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                    $logo_url = $upload_path;
                } else {
                    $error = "Failed to upload logo due to server error.";
                }
            } else {
                $error = "Image size exceeds the 5MB limit.";
            }
        } else {
            $error = "Invalid logo format. Only JPG, PNG, WEBP, and SVG are allowed.";
        }
    }
    
    if (empty($error)) {
        $stmt = $pdo->prepare("INSERT INTO brands (name, slug, logo, description, is_active) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $slug, $logo_url, $description, $is_active])) {
            $success = "Brand added successfully!";
        } else {
            $error = "Failed to add brand.";
        }
    }
}
?>
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Brand - Admin</title>
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
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Register New <span class="text-primary-600">Brand</span></h1>
                <a href="admin-brands.php" class="bg-white px-6 py-3 rounded-xl text-xs font-black text-gray-500 hover:text-primary-600 tracking-widest uppercase border border-gray-100 shadow-sm transition-all">← Back to List</a>
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
                
                <form action="admin-brand-add.php" method="POST" enctype="multipart/form-data" class="space-y-8 relative">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Brand Name</label>
                        <input type="text" name="name" required placeholder="e.g. Apple, Samsung, Google" class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Brand Description</label>
                        <textarea name="description" rows="3" placeholder="General manufacturing details and standards..." class="w-full px-6 py-4 bg-gray-50 border-0 rounded-2xl outline-none focus:ring-2 focus:ring-primary-500 font-bold text-gray-700 transition-all"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Brand Asset (Logo)</label>
                        <div class="relative group">
                            <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,.svg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="w-full px-6 py-10 border-2 border-dashed border-gray-200 rounded-3xl flex flex-col items-center justify-center gap-3 group-hover:bg-gray-50 group-hover:border-primary-300 transition-all">
                                <i class="ri-image-add-line text-4xl text-gray-300 group-hover:text-primary-500"></i>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Select Brand Logo</span>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-2 font-bold tracking-wide text-center">Allowed: <span class="text-primary-600">JPG, PNG, WEBP, SVG</span> (Max 5MB).</p>
                    </div>

                    <div class="flex items-center gap-3 bg-gray-50 p-4 rounded-2xl border border-gray-100 w-fit">
                        <input type="checkbox" name="is_active" id="is_active" checked class="w-5 h-5 text-primary-600 border-gray-300 rounded-lg focus:ring-primary-500">
                        <label for="is_active" class="text-xs font-black text-gray-500 uppercase tracking-widest cursor-pointer">Live on Site</label>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-gray-900 text-white font-black py-5 rounded-2xl hover:bg-primary-600 shadow-xl shadow-gray-200 hover:shadow-primary-200 transition-all uppercase text-xs tracking-[0.3em]">
                            Establish Brand Entity
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
