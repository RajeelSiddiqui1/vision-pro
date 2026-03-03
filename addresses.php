<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth_helper.php';

// Require authentication (checks session and remember cookie)
requireAuth();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $address_line1 = htmlspecialchars($_POST['address_line1']);
        $address_line2 = htmlspecialchars($_POST['address_line2']);
        $city = htmlspecialchars($_POST['city']);
        $state = htmlspecialchars($_POST['state']);
        $postal_code = htmlspecialchars($_POST['postal_code']);
        $country = htmlspecialchars($_POST['country'] ?? 'Canada');
        $is_default = isset($_POST['is_default']) ? 1 : 0;
        $address_id = $_POST['address_id'] ?? 0;
        
        // If setting as default, unset other defaults first
        if ($is_default) {
            $stmt = $pdo->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = ?");
            $stmt->execute([$user_id]);
        }
        
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO addresses (user_id, address_line1, address_line2, city, state, postal_code, country, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $address_line1, $address_line2, $city, $state, $postal_code, $country, $is_default])) {
                $success = "Address added successfully!";
            } else {
                $error = "Failed to add address.";
            }
        } else {
            $stmt = $pdo->prepare("UPDATE addresses SET address_line1 = ?, address_line2 = ?, city = ?, state = ?, postal_code = ?, country = ?, is_default = ? WHERE id = ? AND user_id = ?");
            if ($stmt->execute([$address_line1, $address_line2, $city, $state, $postal_code, $country, $is_default, $address_id, $user_id])) {
                $success = "Address updated successfully!";
            } else {
                $error = "Failed to update address.";
            }
        }
    } elseif ($action === 'delete') {
        $address_id = (int)$_POST['address_id'];
        $stmt = $pdo->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$address_id, $user_id])) {
            $success = "Address deleted successfully!";
        } else {
            $error = "Failed to delete address.";
        }
    } elseif ($action === 'set_default') {
        $address_id = (int)$_POST['address_id'];
        $stmt = $pdo->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $stmt = $pdo->prepare("UPDATE addresses SET is_default = 1 WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$address_id, $user_id])) {
            $success = "Default address updated!";
        }
    }
}

// Get all addresses for user
$stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC");
$stmt->execute([$user_id]);
$addresses = $stmt->fetchAll();

// Get user info
$stmt = $pdo->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Address Book - VisionPro</title>
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

    <main class="container mx-auto px-4 py-12 max-w-4xl">
        <div class="flex items-center gap-2 mb-6">
            <a href="profile.php" class="text-primary-600 hover:underline">← Back to Profile</a>
        </div>
        
        <h1 class="text-3xl font-bold mb-2">Address Book</h1>
        <p class="text-gray-500 mb-8">Manage your shipping addresses</p>

        <?php if ($success): ?>
            <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 font-medium"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 font-medium"><?= $error ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Address List -->
            <div class="space-y-6">
                <?php if (empty($addresses)): ?>
                    <div class="bg-white p-8 rounded-2xl text-center border border-gray-100">
                        <div class="text-4xl mb-4">📍</div>
                        <p class="text-gray-500">No addresses saved yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($addresses as $addr): ?>
                        <div class="bg-white p-6 rounded-2xl border <?= $addr['is_default'] ? 'border-primary-500 ring-2 ring-primary-100' : 'border-gray-200' ?>">
                            <?php if ($addr['is_default']): ?>
                                <span class="bg-primary-100 text-primary-700 text-xs font-bold px-2 py-1 rounded-full uppercase tracking-wide">Default</span>
                            <?php endif; ?>
                            
                            <div class="mt-3 text-gray-900">
                                <p class="font-bold"><?= htmlspecialchars($user['full_name']) ?></p>
                                <p><?= htmlspecialchars($addr['address_line1']) ?></p>
                                <?php if ($addr['address_line2']): ?>
                                    <p><?= htmlspecialchars($addr['address_line2']) ?></p>
                                <?php endif; ?>
                                <p><?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['state']) ?> <?= htmlspecialchars($addr['postal_code']) ?></p>
                                <p><?= htmlspecialchars($addr['country']) ?></p>
                            </div>
                            
                            <div class="mt-4 flex gap-3">
                                <?php if (!$addr['is_default']): ?>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="action" value="set_default">
                                        <input type="hidden" name="address_id" value="<?= $addr['id'] ?>">
                                        <button type="submit" class="text-sm text-primary-600 hover:underline font-medium">Set as Default</button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="address_id" value="<?= $addr['id'] ?>">
                                    <button type="submit" class="text-sm text-red-500 hover:underline font-medium">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Add/Edit Address Form -->
            <div>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                    <h2 class="text-xl font-bold mb-6">Add New Address</h2>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="add">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                            <input type="text" name="address_line1" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 (Optional)</label>
                            <input type="text" name="address_line2" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" name="city" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
                                <input type="text" name="state" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                <input type="text" name="postal_code" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                <input type="text" name="country" value="Canada" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_default" id="is_default" class="w-4 h-4 text-primary-600 rounded">
                            <label for="is_default" class="text-sm text-gray-700">Set as default shipping address</label>
                        </div>
                        
                        <button type="submit" class="w-full bg-primary-600 text-white font-bold py-3 rounded-xl hover:bg-primary-700 transition-all">Add Address</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
