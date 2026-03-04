<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}

// Handle Delete/Role Change
if (isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'delete' && $id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($_GET['action'] === 'toggle_role' && $id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("UPDATE users SET role = IF(role='admin', 'user', 'admin') WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: admin-users.php");
    exit;
}

// Get All Users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">\n <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-gray-100 min-h-screen font-sans">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 min-h-screen text-white p-6 sticky top-0">
            <h2 class="text-2xl font-bold mb-10 text-primary-400">
                <img src="assets/images/visionpro-logo.png" alt="VisionPro" class="h-8 w-auto">
                <span class="text-white">Admin</span>
            </h2>
            <nav class="space-y-4">
                <a href="admin.php" class="block py-2 text-gray-400 hover:text-white">Dashboard</a>
                <a href="admin-products.php" class="block py-2 text-gray-400 hover:text-white">Products</a>
                <a href="admin-categories.php" class="block py-2 text-gray-400 hover:text-white">Categories</a>
                <a href="admin-orders.php" class="block py-2 text-gray-400 hover:text-white">Orders</a>
                <a href="admin-users.php" class="block py-2 text-primary-400 font-bold">Customers</a>
                <a href="index.php" class="block py-2 text-gray-400 hover:text-white border-t border-gray-800 pt-4">View Site</a>
            </nav>
        </aside>

        <!-- Content -->
        <main class="flex-1 p-10">
            <header class="mb-10">
                <h1 class="text-3xl font-bold text-gray-800">Customers & Users</h1>
            </header>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="p-6">User</th>
                            <th class="p-6">Business</th>
                            <th class="p-6">Role</th>
                            <th class="p-6">Joined</th>
                            <th class="p-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td class="p-6">
                                <p class="font-bold text-gray-800"><?= htmlspecialchars($u['full_name']) ?></p>
                                <p class="text-xs text-gray-400"><?= htmlspecialchars($u['email']) ?></p>
                            </td>
                            <td class="p-6 text-sm text-gray-600"><?= htmlspecialchars($u['business_name'] ?: 'N/A') ?></td>
                            <td class="p-6">
                                <span class="px-2 py-1 <?= $u['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?> text-[10px] font-bold uppercase rounded">
                                    <?= $u['role'] ?>
                                </span>
                            </td>
                            <td class="p-6 text-sm text-gray-500"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                            <td class="p-6 text-right space-x-2">
                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                <a href="admin-users.php?action=toggle_role&id=<?= $u['id'] ?>" class="text-xs font-bold text-primary-600 hover:underline">Change Role</a>
                                <a href="admin-users.php?action=delete&id=<?= $u['id'] ?>" onclick="return confirm('Delete this user?')" class="text-xs font-bold text-red-500 hover:underline">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>

