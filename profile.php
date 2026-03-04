<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth_helper.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Require authentication (checks session and remember cookie)
requireAuth();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    
    // Check if email is being changed to a different one
    if ($email !== $user['email']) {
        // Check if new email already exists
        $check_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check_stmt->execute([$email, $user_id]);
        if ($check_stmt->fetch()) {
            $error = "This email address is already in use by another account.";
        }
    }
    
    if (empty($error)) {
        // Update basic info
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
        if ($stmt->execute([$full_name, $email, $phone, $user_id])) {
            $_SESSION['user_name'] = $full_name; // Update session name
            $success = "Profile updated successfully!";
        } else {
            $error = "Failed to update profile.";
        }
    }

    // Handle Password Update if provided
    if (!empty($_POST['new_password']) && empty($error)) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $pwd_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $pwd_stmt->execute([$hashed_password, $user_id]);
            $success .= " Password updated.";
        } else {
            $error = "Passwords do not match.";
        }
    }
}

// Fetch User Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-12">
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Sidebar -->
            <aside class="w-full lg:w-64">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            <?= substr($user['full_name'], 0, 1) ?>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-900"><?= $user['full_name'] ?></h2>
                            <p class="text-xs text-gray-500 uppercase tracking-widest"><?= $user['role'] ?></p>
                        </div>
                    </div>
                    <nav class="space-y-1">
                        <a href="dashboard.php" class="block px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50">Orders History</a>
                        <a href="profile.php" class="block px-4 py-2.5 rounded-xl bg-primary-50 text-primary-700 font-bold">Account Profile</a>
                        <a href="addresses.php" class="block px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50">Address Book</a>
                        <div class="pt-4 mt-4 border-t border-gray-100">
                            <a href="logout.php" class="block px-4 py-2.5 rounded-xl text-red-600 hover:bg-red-50 font-bold">Logout</a>
                        </div>
                    </nav>
                </div>
            </aside>

            <!-- Content -->
            <div class="flex-1 max-w-2xl">
                <h1 class="text-2xl font-bold text-gray-900 mb-8">Account Profile</h1>

                <?php if ($success): ?>
                    <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-100 font-medium"><?= $success ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 border border-red-100 font-medium"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" class="space-y-8">
                    <!-- Personal Info -->
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-6">Personal Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
                                <input type="text" name="full_name" value="<?= $user['full_name'] ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 focus:bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                                <input type="email" name="email" value="<?= $user['email'] ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 focus:bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" name="phone" value="<?= $user['phone'] ?>" class="w-full px-4 py-3 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 focus:bg-white transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Security -->
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-6">Security</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">New Password (Optional)</label>
                                <input type="password" name="new_password" placeholder="Leave blank to keep current" class="w-full px-4 py-3 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 focus:bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Confirm New Password</label>
                                <input type="password" name="confirm_password" placeholder="Confirm new password" class="w-full px-4 py-3 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 focus:bg-white transition-all">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full md:w-auto px-8 py-4 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-200">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

