/**
 * VisionPro - Main JavaScript
 * Optimized with AJAX cart, live search, lazy images, toast notifications
 */

// ─── Toast Notification System ───────────────────────────────────────────────
const Toast = (() => {
    let container;
    function getContainer() {
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.cssText = 'position:fixed;top:1.25rem;right:1.25rem;z-index:9999;display:flex;flex-direction:column;gap:0.5rem;';
            document.body.appendChild(container);
        }
        return container;
    }
    function show(message, type = 'success') {
        const colors = {
            success: 'background:#10b981;color:#fff;',
            error:   'background:#ef4444;color:#fff;',
            info:    'background:#0284c7;color:#fff;',
        };
        const toast = document.createElement('div');
        toast.style.cssText = `${colors[type] || colors.success}padding:0.875rem 1.25rem;border-radius:0.875rem;font-size:0.875rem;font-weight:600;box-shadow:0 10px 25px rgba(0,0,0,0.15);display:flex;align-items:center;gap:0.5rem;max-width:320px;opacity:0;transform:translateX(100%);transition:all 0.3s cubic-bezier(0.4,0,0.2,1);`;
        toast.textContent = message;
        getContainer().appendChild(toast);
        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        });
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }
    return { show };
})();

// ─── Cart Badge Updater ───────────────────────────────────────────────────────
function updateCartBadge(count) {
    document.querySelectorAll('[data-cart-badge]').forEach(el => {
        el.textContent = count;
        el.style.display = count > 0 ? 'flex' : 'none';
        if (count > 0) {
            el.animate([
                { transform: 'scale(1)' },
                { transform: 'scale(1.4)' },
                { transform: 'scale(1)' }
            ], { duration: 300, easing: 'ease-out' });
        }
    });
}

// ─── AJAX Cart ────────────────────────────────────────────────────────────────
function initAjaxCart() {
    document.addEventListener('submit', function(e) {
        const form = e.target.closest('form[action="cart_action.php"]');
        if (!form) return;

        const btn = e.submitter;
        const action = btn?.value || form.querySelector('[name="action"]')?.value;
        if (!['add', 'update', 'remove'].includes(action)) return;

        e.preventDefault();

        const originalText = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>`;
        }

        const formData = new FormData(form);
        if (btn && btn.name) {
            formData.append(btn.name, btn.value);
        }

        fetch('cart_action.php', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Toast.show(data.message, 'success');
                if (typeof data.cart_count !== 'undefined') {
                    updateCartBadge(data.cart_count);
                }
                // Dynamic UI updates for cart.php
                if (window.location.pathname.includes('cart.php')) {
                    if (data.is_empty) {
                        setTimeout(() => location.reload(), 300);
                    } else {
                        // Update Summary
                        const sub = document.getElementById('cart-subtotal');
                        const tax = document.getElementById('cart-tax');
                        const tot = document.getElementById('cart-total');
                        if (sub) sub.textContent = '$' + data.subtotal;
                        if (tax) tax.textContent = '$' + data.tax;
                        if (tot) tot.textContent = '$' + data.total;

                        const pid = formData.get('product_id');
                        const itemRow = document.querySelector(`.cart-item[data-product-id="${pid}"]`);

                        if (action === 'remove') {
                            if (itemRow) {
                                itemRow.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                                itemRow.style.opacity = '0';
                                itemRow.style.transform = 'translateY(20px)';
                                itemRow.style.pointerEvents = 'none';
                                setTimeout(() => itemRow.remove(), 400);
                            }
                        } else if (action === 'update') {
                            if (itemRow && data.items && data.items[pid]) {
                                const subEl = itemRow.querySelector('[data-item-subtotal]');
                                if (subEl) {
                                    subEl.textContent = '$' + data.items[pid].subtotal;
                                    subEl.animate([
                                        { transform: 'scale(1)', color: '#0ea5e9' },
                                        { transform: 'scale(1.1)', color: '#0ea5e9' },
                                        { transform: 'scale(1)', color: 'inherit' }
                                    ], { duration: 400 });
                                }
                            }
                        }
                    }
                }
            } else {
                Toast.show(data.message || 'Something went wrong.', 'error');
            }
        })
        .catch(() => Toast.show('Network error. Please try again.', 'error'))
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    });
}

// ─── Live Search ──────────────────────────────────────────────────────────────
function initLiveSearch() {
    document.querySelectorAll('input[name="q"]').forEach(searchInput => {
        let resultsContainer = searchInput.parentElement.querySelector('.search-results-dropdown');
        if (!resultsContainer) {
            resultsContainer = document.createElement('div');
            resultsContainer.className = 'search-results-dropdown absolute top-full left-0 right-0 bg-white mt-2 rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50 hidden';
            searchInput.parentElement.style.position = 'relative';
            searchInput.parentElement.appendChild(resultsContainer);
        }

        let debounceTimer, controller;

        searchInput.addEventListener('input', () => {
            const q = searchInput.value.trim();
            clearTimeout(debounceTimer);
            if (controller) controller.abort();

            if (q.length < 2) {
                resultsContainer.classList.add('hidden');
                return;
            }

            debounceTimer = setTimeout(() => {
                controller = new AbortController();
                resultsContainer.innerHTML = `<div class="p-4 text-center text-sm text-gray-400">Searching…</div>`;
                resultsContainer.classList.remove('hidden');

                fetch(`search_api.php?q=${encodeURIComponent(q)}`, { signal: controller.signal })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.length) {
                            resultsContainer.innerHTML = `<div class="p-4 text-center text-sm text-gray-500">No results found for "<b>${q}</b>"</div>`;
                            return;
                        }
                        resultsContainer.innerHTML = data.map(item => {
                            const price = item.discount_price
                                ? `<span class="text-primary-600 font-bold">$${parseFloat(item.discount_price).toFixed(2)}</span>
                                   <span class="text-[11px] text-gray-400 line-through ml-1">$${parseFloat(item.price).toFixed(2)}</span>`
                                : `<span class="text-primary-600 font-bold">$${parseFloat(item.price).toFixed(2)}</span>`;
                            return `<a href="product-detail.php?id=${item.id}" class="flex items-center gap-3 p-3 hover:bg-primary-50 transition-colors border-b last:border-0 border-gray-50">
                                <img src="${item.main_image || ''}" onerror="this.src='assets/images/placeholder.png'"
                                     class="w-11 h-11 rounded-lg object-contain bg-gray-50 flex-shrink-0" loading="lazy">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-800 text-sm truncate">${item.name}</p>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">${item.category_name || ''} ${item.sku ? '· ' + item.sku : ''}</p>
                                </div>
                                <div class="text-right flex-shrink-0">${price}</div>
                            </a>`;
                        }).join('');
                    })
                    .catch(err => { if (err.name !== 'AbortError') resultsContainer.classList.add('hidden'); });
            }, 280);
        });

        // Close on outside click
        document.addEventListener('click', e => {
            if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                resultsContainer.classList.add('hidden');
            }
        });

        // Enter key → go to products page
        searchInput.addEventListener('keydown', e => {
            if (e.key === 'Enter' && searchInput.value.trim()) {
                window.location.href = `products.php?search=${encodeURIComponent(searchInput.value.trim())}`;
            }
        });
    });
}

// ─── Lazy Image Loading ───────────────────────────────────────────────────────
function initLazyImages() {
    if (!('IntersectionObserver' in window)) return; // fallback: browser loads normally
    const imgObserver = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const img = entry.target;
            if (img.dataset.src) {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            }
            img.classList.add('loaded');
            obs.unobserve(img);
        });
    }, { rootMargin: '200px' });

    document.querySelectorAll('img[data-src]').forEach(img => imgObserver.observe(img));
}

// ─── Scroll Reveal ────────────────────────────────────────────────────────────
function initScrollReveal() {
    if (!('IntersectionObserver' in window)) {
        document.querySelectorAll('.reveal,.stagger-reveal').forEach(el => el.classList.add('active'));
        return;
    }
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('active');
            if (entry.target.classList.contains('stagger-reveal')) {
                Array.from(entry.target.children).forEach((child, i) => {
                    child.style.transitionDelay = `${i * 0.08}s`;
                });
            }
            revealObserver.unobserve(entry.target);
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.reveal,.stagger-reveal').forEach(el => revealObserver.observe(el));
}

// ─── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initAjaxCart();
    initLiveSearch();
    initLazyImages();
    initScrollReveal();
});
