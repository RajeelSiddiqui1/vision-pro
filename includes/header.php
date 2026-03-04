<?php
// includes/header.php
?>
<header class="sticky top-0 z-50 bg-white bg-opacity-80 backdrop-blur-lg border-b border-gray-100">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="index.php" class="flex items-center gap-3">
                <img src="assets/images/visionpro-logo.png" alt="VisionPro" class="h-10 w-auto">
                <div class="hidden sm:block">
                    <span class="text-xl font-bold text-gray-900 tracking-tight">VisionPro</span>
                    <span class="block text-[10px] text-gray-500 uppercase tracking-widest -mt-1">LCD Refurbishing</span>
                </div>
            </a>

            <!-- Desktop Nav -->
            <nav class="hidden lg:flex items-center gap-8">
                <a href="index.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">Home</a>
                
                <!-- Products Dropdown -->
                <div class="relative group">
                    <a href="products.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors flex items-center gap-1">
                        Products
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                    <div class="absolute top-full left-0 w-64 pt-4 z-50 invisible opacity-0 translate-y-2 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300">
                        <div class="glass rounded-2xl shadow-xl border border-gray-100 p-4 space-y-1">
                            <a href="products.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors">All Products</a>
                            <a href="products.php?featured=1" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors">🔥 Hot Selling</a>
                            <a href="products.php?sort=newest" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors">New Arrivals</a>
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
                        <div class="glass bg-white bg-opacity-95 rounded-2xl shadow-xl border border-gray-100 p-4 space-y-1">
                            <div class="grid grid-cols-1 gap-2">
                                <?php 
                                require_once 'config/db.php';
                                $header_cats = $pdo->query("SELECT * FROM categories LIMIT 8")->fetchAll();
                                foreach($header_cats as $hc): ?>
                                <a href="products.php?category=<?= $hc['id'] ?>" class="flex items-center gap-3 p-2 hover:bg-primary-50 rounded-xl transition-all group/item">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                                        <?php if($hc['image_url']): ?>
                                            <img src="<?= $hc['image_url'] ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <span class="text-xs">📦</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 group-hover/item:text-primary-600"><?= $hc['name'] ?></span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <hr class="border-gray-100 my-2">
                            <a href="categories.php" class="block text-xs font-bold text-center text-primary-600 hover:underline uppercase tracking-widest py-2">View All Categories</a>
                        </div>
                    </div>
                </div>

                <a href="services.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">Services</a>
                <a href="blog.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">Blog</a>
                <a href="about.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">About Us</a>
                <a href="contact.php" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">Contact</a>
            </nav>

            <!-- Search Bar -->
            <div class="hidden md:flex flex-1 max-w-md mx-8">
                <div class="relative w-full">
                    <input type="text" name="q" placeholder="Search for screens, batteries..." 
                           class="w-full bg-gray-100 border-none rounded-xl py-2.5 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary-500 transition-all">
                    <svg class="absolute left-3 top-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php 
                    $dashboard_link = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'admin.php' : 'dashboard.php'; 
                    $user_initial = isset($_SESSION['user_name']) ? strtoupper(substr($_SESSION['user_name'], 0, 1)) : 'U';
                    ?>
                    <div class="relative group">
                        <button class="flex items-center gap-2 p-1 rounded-xl hover:bg-gray-100 transition-all">
                            <div class="w-10 h-10 bg-primary-100 text-primary-700 font-bold rounded-full flex items-center justify-center border-2 border-white shadow-sm">
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
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    Dashboard
                                </a>
                                <hr class="border-gray-100 my-1">
                                <a href="logout.php" class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4-4H7m6 4v1h8M7 21a2 2 0 01-2-2V5a2 2 0 012-2h4"/></svg>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="hidden sm:block text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">Login</a>
                    <a href="signup.php" class="hidden sm:block px-5 py-2.5 bg-primary-600 text-white text-sm font-bold rounded-xl hover:bg-primary-700 transition-all shadow-md shadow-primary-100">Sign Up</a>
                <?php endif; ?>
                
                <!-- Cart Icon -->
                <a href="cart.php" class="relative p-2 text-gray-600 hover:text-primary-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                    <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-[10px] flex items-center justify-center rounded-full font-bold">
                        <?= count($_SESSION['cart']) ?>
                    </span>
                    <?php endif; ?>
                </a>

                <!-- Mobile Menu Toggle -->
                <button class="lg:hidden p-2 text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>

