<?php
// includes/header.php
?>
<header class="sticky top-0 z-50 bg-white bg-opacity-90 backdrop-blur-lg border-b border-gray-100">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="index.php" class="flex items-center gap-3 flex-shrink-0">
                <img src="assets/images/visionpro-logo.png" alt="VisionPro" class="h-14 w-auto">
            </a>

            <!-- Desktop Nav -->
            <nav class="hidden lg:flex items-center gap-8">
                <a href="index.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">Home</a>

                <!-- Brands Dropdown -->
                <div class="relative group">
                    <a href="brands.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors flex items-center gap-1">
                        Parts
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                    <div class="absolute top-full left-0 w-56 pt-4 z-50 invisible opacity-0 translate-y-2 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300">
                        <div class="glass rounded-2xl shadow-xl border border-gray-100 p-4 space-y-1">
                            <?php
                            if (!isset($pdo)) require_once 'config/db.php';
                            $header_brands = $pdo->query("SELECT * FROM brands WHERE is_active = 1 LIMIT 5")->fetchAll();
                            foreach($header_brands as $hb): ?>
                            <a href="categories.php?brand_id=<?= $hb['id'] ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors"><?= htmlspecialchars($hb['name']) ?></a>
                            <?php endforeach; ?>
                            <hr class="border-gray-100 my-2">
                            <a href="brands.php" class="block text-xs font-bold text-center text-primary-600 hover:underline uppercase tracking-widest py-1">All Parts →</a>
                        </div>
                    </div>
                </div>

                <!-- Categories Dropdown -->
                <div class="relative group">
                    <a href="categories.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors flex items-center gap-1">
                        Categories
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                    <div class="absolute top-full left-0 w-64 pt-4 z-50 invisible opacity-0 translate-y-2 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300">
                        <div class="glass rounded-2xl shadow-xl border border-gray-100 p-4 space-y-1">
                            <?php
                            $header_cats = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL LIMIT 8")->fetchAll();
                            foreach($header_cats as $hc): ?>
                            <a href="parts.php?category_id=<?= $hc['id'] ?>" class="flex items-center gap-3 p-2 hover:bg-primary-50 rounded-xl transition-all">
                                <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <?php if($hc['image_url']): ?>
                                        <img src="<?= $hc['image_url'] ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <span class="text-xs">📦</span>
                                    <?php endif; ?>
                                </div>
                                <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($hc['name']) ?></span>
                            </a>
                            <?php endforeach; ?>
                            <hr class="border-gray-100 my-2">
                            <a href="categories.php" class="block text-xs font-bold text-center text-primary-600 hover:underline uppercase tracking-widest py-1">All Series →</a>
                        </div>
                    </div>
                </div>

                <a href="products.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">Inventory</a>
                <a href="accessories.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">Accessories</a>

                <a href="services.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">Services</a>
                <a href="blog.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">Blog</a>
                <a href="about.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">About</a>
                <a href="contact.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">Contact</a>
            </nav>

            <!-- Search Bar (desktop) -->
            <div class="hidden lg:flex flex-1 max-w-xs mx-6">
                <div class="relative w-full">
                    <input type="text" name="q" placeholder="Search products..."
                           class="w-full bg-gray-100 border-none rounded-xl py-2.5 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary-500 transition-all outline-none">
                    <svg class="absolute left-3 top-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    $dashboard_link = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'admin.php' : 'dashboard.php';
                    $user_initial = isset($_SESSION['user_name']) ? strtoupper(substr($_SESSION['user_name'], 0, 1)) : 'U';
                    ?>
                    <div class="relative group hidden sm:block">
                        <button class="flex items-center gap-2 p-1 rounded-xl hover:bg-gray-100 transition-all">
                            <div class="w-9 h-9 bg-primary-100 text-primary-700 font-bold rounded-full flex items-center justify-center border-2 border-white shadow-sm text-sm">
                                <?= $user_initial ?>
                            </div>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="absolute top-full right-0 w-48 pt-2 z-50 invisible opacity-0 translate-y-2 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300">
                            <div class="glass rounded-2xl shadow-xl border border-gray-100 p-2 space-y-1">
                                <a href="profile.php" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Profile
                                </a>
                                <a href="<?= $dashboard_link ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    Dashboard
                                </a>
                                <hr class="border-gray-100 my-1">
                                <a href="logout.php" class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="hidden sm:block text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">Login</a>
                    <a href="signup.php" class="hidden sm:block px-4 py-2 bg-primary-600 text-white text-sm font-bold rounded-xl hover:bg-primary-700 transition-all shadow-md shadow-primary-100">Sign Up</a>
                <?php endif; ?>

                <!-- Cart Icon -->
                <a href="cart.php" class="relative p-2 text-gray-600 hover:text-primary-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <?php
                    $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                    ?>
                    <span data-cart-badge
                          class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] flex items-center justify-center rounded-full font-bold <?= $cart_count > 0 ? '' : 'hidden' ?>">
                        <?= $cart_count ?>
                    </span>
                </a>

                <!-- Mobile Hamburger Button -->
                <button id="mobile-menu-btn" class="lg:hidden p-2 rounded-xl text-gray-600 hover:bg-gray-100 transition-all" aria-label="Menu">
                    <svg id="icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Panel -->
    <div id="mobile-menu" class="lg:hidden hidden border-t border-gray-100 bg-white">
        <div class="container mx-auto px-4 py-4 space-y-1">
            <!-- Mobile Search -->
            <div class="relative mb-4">
                <input type="text" name="q" placeholder="Search products..."
                       class="w-full bg-gray-100 border-none rounded-xl py-3 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                <svg class="absolute left-3 top-3.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Nav Links -->
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium transition-all">
                🏠 Home
            </a>
            <a href="brands.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium transition-all">
                🏅 Brands
            </a>
            <a href="categories.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium transition-all">
                📂 Series & Categories
            </a>
            <a href="products.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium transition-all">
                📦 Full Inventory
            </a>
            <a href="accessories.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium transition-all">
                🎧 Accessories
            </a>
            <a href="services.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium transition-all">
                🔧 Services
            </a>
            <a href="blog.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium transition-all">
                📝 Blog
            </a>
            <a href="about.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium transition-all">
                ℹ️ About Us
            </a>
            <a href="contact.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium transition-all">
                📞 Contact
            </a>

            <hr class="border-gray-100 my-2">

            <!-- Auth Links -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium transition-all">
                    👤 Profile
                </a>
                <a href="<?= $dashboard_link ?? 'dashboard.php' ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium transition-all">
                    📊 Dashboard
                </a>
                <a href="logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 font-medium transition-all">
                    🚪 Logout
                </a>
            <?php else: ?>
                <div class="flex gap-3 pt-2 pb-2">
                    <a href="login.php" class="flex-1 text-center py-3 border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-all text-sm">Login</a>
                    <a href="signup.php" class="flex-1 text-center py-3 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-all text-sm">Sign Up</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
(function() {
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    const iconOpen = document.getElementById('icon-open');
    const iconClose = document.getElementById('icon-close');

    if (btn && menu) {
        btn.addEventListener('click', function () {
            const isOpen = !menu.classList.contains('hidden');
            menu.classList.toggle('hidden', isOpen);
            iconOpen.classList.toggle('hidden', !isOpen);
            iconClose.classList.toggle('hidden', isOpen);
        });
    }
})();
</script>
