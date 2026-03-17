<?php
/**
 * VisionPro Premium Alert System
 * Centralized notification component for the admin panel.
 */

function render_admin_toast() {
    $error = $_GET['error'] ?? null;
    $success = $_GET['success'] ?? null;
    $page = basename($_SERVER['PHP_SELF']);

    $type = $error ? 'error' : 'success';
    $code = $error ?: $success;

    // Mapping messages for a consistent experience
    $messages = [
        // Error Codes
        'has_orders' => ['title' => 'Access Denied', 'body' => 'Record is linked to active customer orders.', 'icon' => 'ri-error-warning-fill'],
        'admin_protect' => ['title' => 'Security Guard', 'body' => 'Administrator accounts are protected by the system.', 'icon' => 'ri-shield-user-fill'],
        'db_error' => ['title' => 'System Glitch', 'body' => 'A database anomaly occurred. Please retry.', 'icon' => 'ri-database-2-fill'],
        'has_products' => ['title' => 'Inventory Lock', 'body' => 'This category contains listed products.', 'icon' => 'ri-lock-2-fill'],
        'has_deps' => ['title' => 'Dependency Block', 'body' => 'This brand is currently linked to other assets.', 'icon' => 'ri-git-merge-fill'],
        
        // Success Codes
        'deleted' => ['title' => 'Data Purged', 'body' => 'The record has been permanently removed.', 'icon' => 'ri-delete-bin-5-fill'],
        'role_updated' => ['title' => 'Access Updated', 'body' => 'User permissions have been synchronized.', 'icon' => 'ri-key-2-fill'],
        'added' => ['title' => 'Registry Updated', 'body' => 'New asset has been successfully registered.', 'icon' => 'ri-add-circle-fill'],
        'updated' => ['title' => 'Record Synced', 'body' => 'Changes have been saved to the database.', 'icon' => 'ri-save-3-fill'],
    ];

    $content = $messages[$code] ?? ['title' => ucfirst($type ?? ''), 'body' => 'Operation completed.', 'icon' => 'ri-information-fill'];
    $bgColor = ($type ?? '') === 'success' ? 'emerald' : 'rose';
    $iconColor = ($type ?? '') === 'success' ? 'text-emerald-500' : 'text-rose-500';
    $primaryColor = ($type ?? '') === 'success' ? 'bg-emerald-500' : 'bg-rose-500';
    ?>

    <?php if ($error || $success): ?>

    <div id="toast-container" class="fixed top-8 right-8 z-[9999] flex flex-col gap-4 max-w-sm w-full transition-all duration-500 pointer-events-none">
        <div id="premium-toast" class="pointer-events-auto relative overflow-hidden glass-toast p-5 flex items-start gap-4 animate-toast-in shadow-2xl shadow-<?= $bgColor ?>-500/10 border-l-4 border-<?= $bgColor ?>-500">
            <!-- Glow Effect -->
            <div class="absolute -right-12 -top-12 w-24 h-24 bg-<?= $bgColor ?>-500/10 blur-3xl rounded-full"></div>
            
            <div class="w-12 h-12 <?= $primaryColor ?>/10 rounded-2xl flex items-center justify-center shrink-0 shadow-inner">
                <i class="<?= $content['icon'] ?> text-2xl <?= $iconColor ?>"></i>
            </div>
            
            <div class="flex-1 pt-0.5">
                <h3 class="text-gray-900 font-black text-sm uppercase tracking-tighter mb-0.5"><?= $content['title'] ?></h3>
                <p class="text-gray-500 font-bold text-[11px] leading-relaxed"><?= $content['body'] ?></p>
            </div>

            <button onclick="dismissToast()" class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-400 transition-colors">
                <i class="ri-close-line text-lg"></i>
            </button>

            <!-- Progress Bar -->
            <div class="absolute bottom-0 left-0 h-[3px] <?= $primaryColor ?> opacity-20 toast-progress"></div>
        </div>
    </div>

    <style>
        .glass-toast {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        @keyframes toast-in {
            0% { transform: translateX(100%) scale(0.9); opacity: 0; }
            50% { transform: translateX(-10px) scale(1.02); }
            100% { transform: translateX(0) scale(1); opacity: 1; }
        }

        @keyframes toast-out {
            0% { transform: translateX(0) scale(1); opacity: 1; }
            100% { transform: translateX(120%) scale(0.9); opacity: 0; }
        }

        @keyframes progress {
            0% { width: 100%; }
            100% { width: 0%; }
        }

        .animate-toast-in { animation: toast-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .animate-toast-out { animation: toast-out 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .toast-progress { animation: progress 5s linear forwards; }
    </style>

    <script>
        function dismissToast() {
            const toast = document.getElementById('premium-toast');
            if (!toast) return;
            toast.classList.remove('animate-toast-in');
            toast.classList.add('animate-toast-out');
            setTimeout(() => {
                const container = document.getElementById('toast-container');
                if (container) container.remove();
                // Clean up URL
                const url = new URL(window.location.href);
                url.searchParams.delete('error');
                url.searchParams.delete('success');
                window.history.replaceState({}, '', url);
            }, 500);
        }

        // Auto-dismiss after 5 seconds
        setTimeout(dismissToast, 5000);
    </script>
    <?php endif; ?>

    <script>
        let confirmCallback = null;

        function showConfirm(title, message, callback) {
            confirmCallback = callback;
            document.getElementById('modal-title').innerText = title;
            document.getElementById('modal-message').innerText = message;
            
            const overlay = document.getElementById('confirm-overlay');
            const modal = document.getElementById('confirm-modal');
            
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            overlay.classList.remove('animate-fade-out');
            overlay.classList.add('animate-fade-in');
            
            modal.classList.remove('animate-modal-out');
            modal.classList.add('animate-modal-in');
        }

        function closeConfirm(confirmed) {
            const overlay = document.getElementById('confirm-overlay');
            const modal = document.getElementById('confirm-modal');
            
            modal.classList.remove('animate-modal-in');
            modal.classList.add('animate-modal-out');
            
            overlay.classList.remove('animate-fade-in');
            overlay.classList.add('animate-fade-out');
            
            setTimeout(() => {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
                if (confirmed && typeof confirmCallback === 'function') {
                    confirmCallback();
                }
            }, 400);
        }

        // Utility for link-based deletion
        function smartDelete(element, title, message) {
            event.preventDefault();
            const href = element.getAttribute('href');
            showConfirm(title, message, () => {
                window.location.href = href;
            });
        }
    </script>

    <!-- Confirmation Modal HTML -->
    <div id="confirm-overlay" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-gray-950/40 backdrop-blur-md px-4">
        <div id="confirm-modal" class="glass-toast max-w-sm w-full p-8 shadow-2xl border border-white/20 relative">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-primary-500/10 blur-3xl rounded-full"></div>
            
            <div class="w-16 h-16 bg-red-500/10 rounded-2xl flex items-center justify-center mb-6 shadow-inner">
                <i class="ri-error-warning-fill text-3xl text-red-500"></i>
            </div>
            
            <h2 id="modal-title" class="text-gray-900 font-extrabold text-xl uppercase tracking-tighter mb-2"></h2>
            <p id="modal-message" class="text-gray-500 font-bold text-xs leading-relaxed mb-8"></p>
            
            <div class="flex gap-3">
                <button onclick="closeConfirm(false)" class="flex-1 py-3.5 bg-gray-100 text-gray-700 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">
                    Cancel
                </button>
                <button onclick="closeConfirm(true)" class="flex-1 py-3.5 bg-red-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-700 shadow-xl shadow-red-500/20 transition-all">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in { 0% { opacity: 0; } 100% { opacity: 1; } }
        @keyframes fade-out { 0% { opacity: 1; } 100% { opacity: 0; } }
        @keyframes modal-in { 0% { transform: scale(0.9); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        @keyframes modal-out { 0% { transform: scale(1); opacity: 1; } 100% { transform: scale(0.9); opacity: 0; } }
        
        .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
        .animate-fade-out { animation: fade-out 0.4s ease-in forwards; }
        .animate-modal-in { animation: modal-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .animate-modal-out { animation: modal-out 0.3s ease-in forwards; }
    </style>
    <?php
}
