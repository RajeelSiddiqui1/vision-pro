<?php
// includes/admin_sidebar.php
require_once 'admin_alerts.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
render_admin_toast();

function isActive($page, $current) {
    return $current === $page ? 'bg-primary-600/10 text-primary-400 border-r-4 border-primary-500 shadow-[inset_-10px_0_15px_-10px_rgba(14,165,233,0.3)]' : 'text-gray-400 hover:text-white hover:bg-white/5';
}

function getIcon($page) {
    $icons = [
        'admin' => 'ri-dashboard-3-line',
        'admin-products' => 'ri-shopping-bag-3-line',
        'admin-product-add' => 'ri-add-box-line',
        'admin-product-edit' => 'ri-edit-box-line',
        'admin-brands' => 'ri-medal-line',
        'admin-categories' => 'ri-list-check-2',
        'admin-orders' => 'ri-file-list-3-line',
        'admin-users' => 'ri-group-line',
        'admin-blogs' => 'ri-article-line',
        'admin-repair-services' => 'ri-tools-line',
        'admin-device-categories' => 'ri-smartphone-line',
        'admin-device-subcategory-add' => 'ri-node-tree',
        'admin-appointments' => 'ri-calendar-todo-line',
    ];
    return $icons[$page] ?? 'ri-bookmark-line';
}
?>
<!-- Sidebar -->
<div class="w-72 bg-[#0b0e14] min-h-screen text-white flex flex-col sticky top-0 border-r border-white/5 shadow-2xl z-50">
    <div class="p-8 pb-12">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/20">
                <i class="ri-shield-user-fill text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-black tracking-tighter uppercase italic">Vision<span class="text-primary-500">Pro</span></h2>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Control Center</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 space-y-1 px-4 overflow-y-auto custom-scrollbar">
        <p class="px-4 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em] mb-4">Main Navigation</p>
        
        <a href="admin.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group <?= isActive('admin', $current_page) ?>">
            <i class="<?= getIcon('admin') ?> text-lg"></i>
            <span class="text-sm font-bold tracking-tight">Dashboard</span>
        </a>

        <a href="admin-products.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group <?= isActive('admin-products', $current_page) ?> <?= isActive('admin-product-add', $current_page) ?> <?= isActive('admin-product-edit', $current_page) ?>">
            <i class="<?= getIcon('admin-products') ?> text-lg"></i>
            <span class="text-sm font-bold tracking-tight">Product Gallery</span>
        </a>

        <a href="admin-orders.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group <?= isActive('admin-orders', $current_page) ?> <?= isActive('admin-order-details', $current_page) ?>">
            <i class="<?= getIcon('admin-orders') ?> text-lg"></i>
            <span class="text-sm font-bold tracking-tight">Order Activity</span>
        </a>

        <a href="admin-users.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group <?= isActive('admin-users', $current_page) ?>">
            <i class="<?= getIcon('admin-users') ?> text-lg"></i>
            <span class="text-sm font-bold tracking-tight">Client Base</span>
        </a>

        <div class="pt-6">
            <p class="px-4 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em] mb-4">Discovery Architecture</p>
            <a href="admin-brands.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group <?= isActive('admin-brands', $current_page) ?>">
                <i class="<?= getIcon('admin-brands') ?> text-lg"></i>
                <span class="text-sm font-bold tracking-tight text-white/90">1. Brand Assets</span>
            </a>
            <a href="admin-categories.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group <?= isActive('admin-categories', $current_page) ?>">
                <i class="<?= getIcon('admin-categories') ?> text-lg"></i>
                <span class="text-sm font-bold tracking-tight text-white/90">2. Model Groups</span>
            </a>
            <a href="admin-subcategories.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group <?= isActive('admin-subcategories', $current_page) ?> <?= isActive('admin-subcategory-add', $current_page) ?>">
                <i class="ri-node-tree text-lg"></i>
                <span class="text-sm font-bold tracking-tight text-white/90">3. Specific Models</span>
            </a>
        </div>

        <div class="pt-6">
            <p class="px-4 text-[10px] font-black text-gray-600 uppercase tracking-[0.2em] mb-4">Repairs & Appointments</p>
            <a href="admin-repair-services.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group <?= isActive('admin-repair-services', $current_page) ?>">
                <i class="<?= getIcon('admin-repair-services') ?> text-lg"></i>
                <span class="text-sm font-bold tracking-tight">Services Matrix</span>
            </a>
            <a href="admin-appointments.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group <?= isActive('admin-appointments', $current_page) ?>">
                <i class="<?= getIcon('admin-appointments') ?> text-lg"></i>
                <span class="text-sm font-bold tracking-tight">Booking Log</span>
            </a>
            <a href="admin-blogs.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group <?= isActive('admin-blogs', $current_page) ?>">
                <i class="<?= getIcon('admin-blogs') ?> text-lg"></i>
                <span class="text-sm font-bold tracking-tight">Content Engine</span>
            </a>
        </div>
    </nav>

    <div class="p-6 mt-auto border-t border-white/5 bg-black/20">
        <a href="index.php" class="flex items-center gap-3 px-4 py-2 text-gray-500 hover:text-white transition-colors mb-2">
            <i class="ri-external-link-line"></i>
            <span class="text-xs font-bold uppercase tracking-wider">Live Site</span>
        </a>
        <a href="logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300">
            <i class="ri-logout-box-r-line text-lg"></i>
            <span class="text-sm font-black">Secure Exit</span>
        </a>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #1f2937; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #374151; }
</style>
