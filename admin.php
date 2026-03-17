<?php
session_start();
require_once 'config/db.php';
require_once 'includes/security.php';

// Prevent caching
no_cache_headers();

// Admin Check
require_admin();

// ─── 1. Core Metrics ────────────────────────────────────────────────────────
$total_sales = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled'")->fetchColumn() ?: 0;
$order_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?: 0;
$user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() ?: 0;
$product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn() ?: 0;

// ─── 2. Chart Data: Sales Trend (Last 7 Days) ────────────────────────────────
$sales_trend = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $stmt = $pdo->prepare("SELECT SUM(total_amount) as daily_total FROM orders WHERE DATE(created_at) = ? AND status != 'cancelled'");
    $stmt->execute([$date]);
    $val = $stmt->fetchColumn() ?: 0;
    $sales_trend[date('D', strtotime($date))] = $val;
}

// ─── 3. Chart Data: Order Status Distribution ────────────────────────────────
$status_dist = $pdo->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

// ─── 4. Top Products ─────────────────────────────────────────────────────────
$top_products = $pdo->query("
    SELECT p.name, COUNT(oi.product_id) as sales_count, SUM(oi.price * oi.quantity) as revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY p.id
    ORDER BY sales_count DESC
    LIMIT 5
")->fetchAll();

// ─── 5. Recent Activity ─────────────────────────────────────────────────────
$recent_orders = $pdo->query("
    SELECT o.*, u.full_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 6
")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro Dashboard - VisionPro Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc',
                            400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1',
                            800: '#075985', 900: '#0c4a6e', 950: '#082f49',
                        },
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                },
            },
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.03);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            transform: translateY(-5px) scale(1.01);
            box-shadow: 0 20px 50px -15px rgba(0,0,0,0.06);
            border-color: rgba(14, 165, 233, 0.4);
        }
        .gradient-text {
            background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            font-size: 24px;
        }
    </style>
</head>
<body class="bg-[#f1f5f9] min-h-screen font-sans text-gray-900 overflow-x-hidden">
    <div class="flex w-full">
        <?php include 'includes/admin_sidebar.php'; ?>

        <main class="flex-1 min-w-0 p-8 lg:p-12">
            <!-- Header -->
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 mb-16">
                <div class="relative">
                    <div class="absolute -left-4 top-0 bottom-0 w-1 bg-primary-500 rounded-full"></div>
                    <h1 class="text-4xl lg:text-5xl font-black tracking-tighter text-gray-900 mb-2">Pro <span class="gradient-text">Dashboard</span></h1>
                    <p class="text-gray-500 font-bold uppercase tracking-widest text-[10px]">Administrative Systems & Analytics</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex flex-col items-end">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Server Time</span>
                        <span class="font-bold text-gray-700"><?= date('H:i') ?> <span class="text-xs text-gray-400 font-normal"><?= date('M d, Y') ?></span></span>
                    </div>
                </div>
            </header>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
                <!-- Revenue -->
                <div class="glass-card p-8 rounded-[2.5rem] relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-6">
                        <div class="stat-icon bg-green-50 text-green-600">
                            <i class="ri-money-dollar-circle-line"></i>
                        </div>
                        <div class="text-xs font-black text-green-500 flex items-center gap-1">
                            <i class="ri-arrow-right-up-line"></i> 12%
                        </div>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Total Revenue</p>
                    <h3 class="text-3xl font-black text-gray-900">$<?= number_format($total_sales, 2) ?></h3>
                </div>

                <!-- Orders -->
                <div class="glass-card p-8 rounded-[2.5rem] relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-6">
                        <div class="stat-icon bg-blue-50 text-blue-600">
                            <i class="ri-shopping-cart-2-line"></i>
                        </div>
                        <div class="text-xs font-black text-blue-500 flex items-center gap-1">
                            <i class="ri-line-chart-line"></i> Active
                        </div>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Total Orders</p>
                    <h3 class="text-3xl font-black text-gray-900"><?= number_format($order_count) ?></h3>
                </div>

                <!-- Customers -->
                <div class="glass-card p-8 rounded-[2.5rem] relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-6">
                        <div class="stat-icon bg-purple-50 text-purple-600">
                            <i class="ri-user-heart-line"></i>
                        </div>
                        <div class="text-xs font-black text-purple-500 flex items-center gap-1">
                            <i class="ri-pulse-line"></i> Growing
                        </div>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Client Base</p>
                    <h3 class="text-3xl font-black text-gray-900"><?= number_format($user_count) ?></h3>
                </div>

                <!-- Products -->
                <div class="glass-card p-8 rounded-[2.5rem] relative overflow-hidden group">
                    <div class="flex justify-between items-start mb-6">
                        <div class="stat-icon bg-orange-50 text-orange-600">
                            <i class="ri-box-3-line"></i>
                        </div>
                        <div class="text-xs font-black text-orange-500 flex items-center gap-1">
                            <i class="ri-check-double-line"></i> In Stock
                        </div>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Total SKUs</p>
                    <h3 class="text-3xl font-black text-gray-900"><?= number_format($product_count) ?></h3>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                <!-- Sales Trend -->
                <div class="lg:col-span-2 glass-card p-8 rounded-[2.5rem] shadow-2xl shadow-blue-500/5">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Sales Analytics</h3>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Last 7 Days Revenue</p>
                        </div>
                        <div class="bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100 flex items-center gap-2 text-[10px] font-black uppercase text-gray-400 tracking-tighter">
                            <span class="w-2 h-2 rounded-full bg-primary-500 animate-pulse"></span> Live Data
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <!-- Distribution -->
                <div class="glass-card p-8 rounded-[2.5rem] shadow-2xl shadow-gray-500/5">
                    <h3 class="text-xl font-black text-gray-900 mb-2">Order Status</h3>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-8">Overall Distribution</p>
                    <div class="h-64 relative">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-8 grid grid-cols-2 gap-4">
                        <?php foreach(['pending', 'completed', 'processing', 'cancelled'] as $s): ?>
                        <div class="bg-gray-50/50 p-3 rounded-2xl border border-gray-100">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1"><?= $s ?></p>
                            <p class="text-lg font-black text-gray-800"><?= $status_dist[$s] ?? 0 ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                <!-- Top Products -->
                <div class="glass-card rounded-[2.5rem] overflow-hidden shadow-2xl shadow-gray-500/5">
                    <div class="p-8 border-b border-gray-50">
                        <h3 class="text-xl font-black text-gray-900">Top Performing Products</h3>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Based on recent sales</p>
                    </div>
                    <div class="p-4">
                        <?php foreach($top_products as $tp): ?>
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-3xl transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-primary-50 flex items-center justify-center text-primary-600 font-black text-lg group-hover:bg-primary-600 group-hover:text-white transition-all">
                                    <?= strtoupper(substr($tp['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 line-clamp-1"><?= e($tp['name']) ?></h4>
                                    <p class="text-[10px] font-black text-primary-500 uppercase tracking-widest"><?= $tp['sales_count'] ?> Sales</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-black text-gray-900">$<?= number_format($tp['revenue'], 2) ?></p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter mt-0.5">Revenue Generated</p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="glass-card rounded-[2.5rem] overflow-hidden shadow-2xl shadow-gray-500/5">
                    <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Recent Orders</h3>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Live Feed</p>
                        </div>
                        <a href="admin-orders.php" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-tighter hover:bg-gray-200 transition-all">All Orders</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <tbody class="divide-y divide-gray-50">
                                <?php foreach($recent_orders as $o): ?>
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center font-black text-gray-400 text-xs">#<?= $o['id'] ?></div>
                                            <div>
                                                <p class="font-bold text-gray-800 text-sm"><?= e($o['full_name']) ?></p>
                                                <p class="text-[10px] text-gray-400 font-medium"><?= date('h:i A', strtotime($o['created_at'])) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-sm font-black text-gray-900">$<?= number_format($o['total_amount'], 2) ?></td>
                                    <td class="px-8 py-5 text-right">
                                        <?php
                                            $c = [
                                                'pending' => 'bg-yellow-100 text-yellow-600',
                                                'completed' => 'bg-green-100 text-green-600',
                                                'processing' => 'bg-blue-100 text-blue-600',
                                                'cancelled' => 'bg-red-100 text-red-600'
                                            ][$o['status']] ?? 'bg-gray-100 text-gray-500';
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest <?= $c ?>"><?= e($o['status']) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // Sales Trend Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesData = <?= json_encode(array_values($sales_trend)) ?>;
        const salesLabels = <?= json_encode(array_keys($sales_trend)) ?>;
        
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Revenue',
                    data: salesData,
                    borderColor: '#0ea5e9',
                    borderWidth: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0ea5e9',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: (context) => {
                        const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(14, 165, 233, 0.1)');
                        gradient.addColorStop(1, 'rgba(14, 165, 233, 0)');
                        return gradient;
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Inter', size: 10, weight: '700' },
                            color: '#94a3b8',
                            callback: (val) => '$' + val
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Inter', size: 10, weight: '700' },
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });

        // Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = <?= json_encode(array_values($status_dist)) ?>;
        const statusLabels = <?= json_encode(array_keys($status_dist)) ?>;

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: ['#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#64748b'],
                    borderWidth: 0,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: { legend: { display: false } }
            }
        });
    </script>
</body>
</html>


