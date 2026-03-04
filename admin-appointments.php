<?php
session_start();
require_once 'config/db.php';
require_once 'includes/email_helper.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Admin Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied. Admins Only.");
}

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['appointment_id']) && isset($_POST['status'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['status'];
    
    // Get current appointment details before update
    $stmt = $pdo->prepare("SELECT a.*, rs.name as service_name, rs.price as service_price FROM appointments a LEFT JOIN repair_services rs ON a.service_id = rs.id WHERE a.id = ?");
    $stmt->execute([$appointment_id]);
    $appointment = $stmt->fetch();
    
    // Update status
    $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $appointment_id]);
    
    // Send email notification to customer based on status
    if ($new_status === 'confirmed') {
        $appointment_details = [
            'service_name' => $appointment['service_name'],
            'service_price' => $appointment['service_price'],
            'appointment_date' => $appointment['appointment_date'],
            'appointment_time' => $appointment['appointment_time'],
            'device_model' => $appointment['device_model']
        ];
        send_appointment_confirmation($appointment_id, $appointment['customer_email'], $appointment['customer_name'], $appointment_details);
    } elseif ($new_status === 'cancelled') {
        send_appointment_cancelled($appointment_id, $appointment['customer_email'], $appointment['customer_name']);
    }
    
    header("Location: admin-appointments.php?updated=1");
    exit;
}

// Get all appointments with service info
$appointments = $pdo->query("
    SELECT a.*, rs.name as service_name, rs.price as service_price, u.full_name as user_name, u.email as user_email
    FROM appointments a
    LEFT JOIN repair_services rs ON a.service_id = rs.id
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
")->fetchAll();

// Get stats
$total_appointments = count($appointments);
$pending_count = count(array_filter($appointments, fn($a) => $a['status'] === 'pending'));
$completed_count = count(array_filter($appointments, fn($a) => $a['status'] === 'completed'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - VisionPro Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        },
                    },
                },
            },
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">\n <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-gray-100 min-h-screen">
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
                <a href="admin-users.php" class="block py-2 text-gray-400 hover:text-white">Customers</a>
                <a href="admin-blogs.php" class="block py-2 text-gray-400 hover:text-white">Blogs</a>
                <a href="admin-repair-services.php" class="block py-2 text-gray-400 hover:text-white">Repair Services</a>
                <a href="admin-appointments.php" class="block py-2 text-primary-400 font-bold">Appointments</a>
                <a href="index.php" class="block py-2 text-gray-400 hover:text-white border-t border-gray-800 pt-4">View Site</a>
            </nav>
        </aside>

        <!-- Content -->
        <main class="flex-1 p-10">
            <header class="flex justify-between items-center mb-10">
                <h1 class="text-3xl font-bold text-gray-800">Appointments</h1>
            </header>

            <?php if (isset($_GET['updated'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    Appointment status updated successfully!
                </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <p class="text-gray-500 text-sm mb-2">Total Appointments</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $total_appointments ?></p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <p class="text-gray-500 text-sm mb-2">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600"><?= $pending_count ?></p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <p class="text-gray-500 text-sm mb-2">Completed</p>
                    <p class="text-3xl font-bold text-green-600"><?= $completed_count ?></p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="p-6">ID</th>
                            <th class="p-6">Customer</th>
                            <th class="p-6">Service</th>
                            <th class="p-6">Device</th>
                            <th class="p-6">Date & Time</th>
                            <th class="p-6">Status</th>
                            <th class="p-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-500">No appointments yet.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($appointments as $apt): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-6 font-bold">#<?= $apt['id'] ?></td>
                            <td class="p-6">
                                <div class="font-bold text-gray-900"><?= htmlspecialchars($apt['customer_name']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($apt['customer_email']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($apt['customer_phone']) ?></div>
                            </td>
                            <td class="p-6">
                                <div class="font-bold text-primary-600"><?= htmlspecialchars($apt['service_name']) ?></div>
                                <div class="text-sm text-gray-500">$<?= number_format($apt['service_price'], 2) ?></div>
                            </td>
                            <td class="p-6 text-gray-500">
                                <?= htmlspecialchars($apt['device_model'] ?? 'Not specified') ?>
                            </td>
                            <td class="p-6">
                                <div class="font-bold text-gray-900"><?= date('M d, Y', strtotime($apt['appointment_date'])) ?></div>
                                <div class="text-sm text-gray-500"><?= date('h:i A', strtotime($apt['appointment_time'])) ?></div>
                            </td>
                            <td class="p-6">
                                <?php
                                $status_colors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'confirmed' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700'
                                ];
                                ?>
                                <span class="px-2 py-1 text-[10px] font-bold uppercase rounded <?= $status_colors[$apt['status']] ?? 'bg-gray-100 text-gray-700' ?>">
                                    <?= $apt['status'] ?>
                                </span>
                            </td>
                            <td class="p-6">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="appointment_id" value="<?= $apt['id'] ?>">
                                    <select name="status" onchange="this.form.submit()" class="text-sm border border-gray-300 rounded px-2 py-1">
                                        <option value="pending" <?= $apt['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="confirmed" <?= $apt['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                        <option value="completed" <?= $apt['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="cancelled" <?= $apt['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>


