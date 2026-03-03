<?php
session_start();
require_once 'config/db.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$email_sent = false;

// Send confirmation email
if ($order_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if ($order) {
        $user_stmt = $pdo->prepare("SELECT email, full_name FROM users WHERE id = ?");
        $user_stmt->execute([$order['user_id']]);
        $user = $user_stmt->fetch();
        
        // Send real email
        require_once 'includes/email_helper.php';
        
        $order_details = [
            'total' => number_format($order['total_amount'], 2),
            'status' => ucfirst($order['status'])
        ];
        
        $email_sent = send_order_confirmation($order_id, $user['email'], $user['full_name'], $order_details);
        
        // Also log to file for debugging
        $log_entry = "[" . date('Y-m-d H:i:s') . "] Confirmation email sent to {$user['email']} for order #$order_id" . ($email_sent ? " (SUCCESS)" : " (FAILED)") . "\n";
        file_put_contents('email_log.txt', $log_entry, FILE_APPEND);
    }
}

// Get order items for display
$items_html = '';
if ($order_id > 0) {
    $items_stmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $items_stmt->execute([$order_id]);
    $order_items = $items_stmt->fetchAll();
    
    foreach ($order_items as $item) {
        $image_html = $item['image'] ? "<img src=\"{$item['image']}\" alt=\"{$item['product_name']}\" style=\"width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin-right: 12px;\">" : '';
        $items_html .= "
        <div class=\"flex items-center justify-between py-4 border-b border-gray-100 last:border-0\">
            <div class=\"flex items-center\">
                $image_html
                <div>
                    <p class=\"font-medium text-gray-900\">{$item['product_name']}</p>
                    <p class=\"text-sm text-gray-500\">Qty: {$item['quantity']}</p>
                </div>
            </div>
            <p class=\"font-medium text-gray-900\">$" . number_format($item['quantity'] * $item['price'], 2) . "</p>
        </div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="assets/images/visionpro-logo.jpeg">
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="flex-grow flex items-center justify-center p-4">
        <div class="max-w-2xl w-full bg-white rounded-3xl shadow-xl p-12 border border-gray-100">
            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4 text-center">Order Successful!</h1>
            <p class="text-gray-500 mb-8 leading-relaxed text-center">Thank you for your business. Your order #<?= $order_id ?> has been received and is now being processed.</p>
            
            <?php if (isset($email_sent) && $email_sent): ?>
                <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 text-sm font-medium border border-green-200 text-center">
                    ✓ A confirmation email has been sent to <strong><?= htmlspecialchars($user['email']) ?></strong> with your order details.
                </div>
            <?php endif; ?>
            
            <!-- Order Items -->
            <?php if ($items_html): ?>
            <div class="bg-gray-50 rounded-2xl p-6 mb-6">
                <h3 class="font-bold text-gray-900 mb-4">Order Items</h3>
                <?= $items_html ?>
            </div>
            <?php endif; ?>
            
            <div class="bg-gray-50 rounded-2xl p-6 mb-10">
                <h3 class="font-bold text-gray-900 mb-2">Next Steps:</h3>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li>• You will receive an email confirmation shortly.</li>
                    <li>• Our team will verify your business status if required.</li>
                    <li>• Track your order status in your dashboard.</li>
                </ul>
            </div>

            <div class="flex gap-4">
                <?php $dash_url = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'admin.php' : 'dashboard.php'; ?>
                <a href="<?= $dash_url ?>" class="flex-1 bg-gray-900 text-white font-bold py-4 rounded-xl hover:bg-black transition-all">Go to Dashboard</a>
                <a href="products.php" class="flex-1 border border-gray-200 text-gray-700 font-bold py-4 rounded-xl hover:bg-gray-50 transition-all">Continue Shopping</a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
