<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}

$order_id = (int)$_GET['id'];

// Get Order Details (Moved up for Email Logic)
$stmt = $pdo->prepare("SELECT o.*, u.full_name, u.email, u.business_name 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) die("Order not found.");

// Get Order Items
$item_stmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.sku 
                            FROM order_items oi 
                            JOIN products p ON oi.product_id = p.id 
                            WHERE oi.order_id = ?");
$item_stmt->execute([$order_id]);
$items = $item_stmt->fetchAll();

// Handle Status & Tracking Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];
    $tracking = $_POST['tracking_number'] ?? '';
    
    // Update DB
    $stmt = $pdo->prepare("UPDATE orders SET status = ?, tracking_number = ? WHERE id = ?");
    $stmt->execute([$status, $tracking, $order_id]);
    
    // Send Real Email Notification
    require_once 'includes/email_helper.php';
    
    $email_sent = send_order_status_update($order_id, $order['email'], $order['full_name'], $status, $tracking);
    
    // Log email result for development
    $log_entry = "[" . date('Y-m-d H:i:s') . "] Order #$order_id status update sent to {$order['email']} - Status: $status" . ($email_sent ? " (SUCCESS)" : " (FAILED)") . "\n";
    file_put_contents('email_log.txt', $log_entry, FILE_APPEND);

    // Redirect to avoid resubmission
    header("Location: admin-order-details.php?id=$order_id&success=updated");
    exit;
}

// Get Addresses
$addr_stmt = $pdo->prepare("SELECT * FROM addresses WHERE id = ?");
$addr_stmt->execute([$order['shipping_address_id']]);
$shipping = $addr_stmt->fetch();

$success = isset($_GET['success']) ? "Order updated and email notification sent!" : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= $order_id ?> Details - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">
    <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <?php include 'includes/admin_sidebar.php'; ?>

        <main class="flex-1 p-10">
            <header class="mb-10 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Order #<?= $order_id ?></h1>
                    <p class="text-gray-500 mt-2">Placed on <?= date('M d, Y H:i', strtotime($order['created_at'])) ?></p>
                </div>
                <a href="admin-orders.php" class="text-sm font-bold text-gray-500 tracking-widest uppercase hover:text-gray-800">← Back to List</a>
            </header>

            <?php if (isset($success)): ?>
                <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 font-bold"><?= $success ?></div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Order Items -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-widest">
                                <tr>
                                    <th class="p-6">Product</th>
                                    <th class="p-6">Price</th>
                                    <th class="p-6 text-center">Qty</th>
                                    <th class="p-6 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach($items as $item): ?>
                                <tr>
                                    <td class="p-6">
                                        <p class="font-bold text-gray-800"><?= htmlspecialchars($item['product_name']) ?></p>
                                        <p class="text-xs text-gray-400">SKU: <?= htmlspecialchars($item['sku']) ?></p>
                                    </td>
                                    <td class="p-6 text-gray-600">$<?= number_format($item['price'], 2) ?></td>
                                    <td class="p-6 text-center font-bold"><?= $item['quantity'] ?></td>
                                    <td class="p-6 text-right font-bold">$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="p-8 bg-gray-50 text-right space-y-2">
                            <p class="text-gray-500">Subtotal: <span class="text-gray-900 font-bold ml-4">$<?= number_format($order['total_amount'] - $order['shipping_amount'] - $order['tax_amount'], 2) ?></span></p>
                            <p class="text-gray-500">Shipping: <span class="text-gray-900 font-bold ml-4">$<?= number_format($order['shipping_amount'], 2) ?></span></p>
                            <p class="text-2xl font-bold text-gray-900 mt-4">Grand Total: <span class="text-primary-600 ml-4">$<?= number_format($order['total_amount'], 2) ?></span></p>
                        </div>
                    </div>
                </div>

                <!-- Customer & Status -->
                <div class="space-y-8">
                    <!-- Status Control -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-200">
                        <h2 class="font-bold text-gray-900 mb-6 uppercase tracking-widest text-sm">Order Status</h2>
                        <form action="admin-order-details.php?id=<?= $order_id ?>" method="POST" class="space-y-4">
                            <select name="status" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500 font-bold mb-4">
                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            
                            <div class="mb-4">
                                <label class="block text-xs text-gray-400 uppercase font-bold tracking-widest mb-1">Tracking Number</label>
                                <input type="text" name="tracking_number" value="<?= htmlspecialchars($order['tracking_number'] ?? '') ?>" placeholder="e.g. TRK-123456789" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 text-sm">
                            </div>

                            <button type="submit" class="w-full bg-primary-600 text-white font-bold py-3 rounded-xl hover:bg-primary-700 transition-all">Update & Send Email</button>

                        </form>
                    </div>

                    <!-- Customer Info -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-200">
                        <h2 class="font-bold text-gray-900 mb-6 uppercase tracking-widest text-sm">Customer</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-bold tracking-widest">Name</p>
                                <p class="font-bold text-gray-800"><?= htmlspecialchars($order['full_name']) ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-bold tracking-widest">Email</p>
                                <p class="text-sm font-medium"><?= htmlspecialchars($order['email']) ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-bold tracking-widest">Business</p>
                                <p class="text-sm font-medium"><?= htmlspecialchars($order['business_name'] ?: 'N/A') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-200">
                        <h2 class="font-bold text-gray-900 mb-6 uppercase tracking-widest text-sm">Shipping To</h2>
                        <?php if($shipping): ?>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p class="font-bold text-gray-800"><?= htmlspecialchars($order['full_name']) ?></p>
                            <p><?= htmlspecialchars($shipping['address_line1']) ?></p>
                            <?php if($shipping['address_line2']): ?>
                            <p><?= htmlspecialchars($shipping['address_line2']) ?></p>
                            <?php endif; ?>
                            <p><?= htmlspecialchars($shipping['city']) ?>, <?= htmlspecialchars($shipping['state']) ?></p>
                            <p><?= htmlspecialchars($shipping['postal_code']) ?></p>
                            <p><?= htmlspecialchars($shipping['country']) ?></p>
                        </div>
                        <?php else: ?>
                        <p class="text-red-500 italic text-sm">Shipping address missing.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>


