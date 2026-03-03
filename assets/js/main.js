// assets/js/main.js
document.addEventListener('DOMContentLoaded', () => {
    console.log('VisionPro LCD Refurbishing Inc. - JavaScript Initialized');

    // Scroll Reveal Animation
    const reveals = document.querySelectorAll('.reveal, .stagger-reveal');
    const observerOptions = {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');

                // Stagger children if it's a stagger-reveal container
                if (entry.target.classList.contains('stagger-reveal')) {
                    const children = entry.target.children;
                    Array.from(children).forEach((child, index) => {
                        child.style.transitionDelay = `${index * 0.1}s`;
                    });
                }
            }
        });
    }, observerOptions);

    reveals.forEach(el => observer.observe(el));

    // Mobile menu toggle logic
    const mobileMenuBtn = document.querySelector('button.lg\\:hidden');
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            console.log('Mobile menu clicked');
        });
    }

    // Live Search Logic
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        // Create results container if it doesn't exist
        let resultsContainer = document.getElementById('search-results');
        if (!resultsContainer) {
            resultsContainer = document.createElement('div');
            resultsContainer.id = 'search-results';
            resultsContainer.className = 'absolute top-full left-0 right-0 glass mt-2 rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50 hidden transition-all duration-300';
            searchInput.parentElement.classList.add('relative');
            searchInput.parentElement.appendChild(resultsContainer);
        }

        let debounceTimer;
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 2) {
                resultsContainer.classList.add('hidden');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`search_api.php?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            resultsContainer.innerHTML = data.map(item => `
                                <a href="product-detail.php?id=${item.id}" class="flex items-center gap-4 p-4 hover:bg-primary-50 transition-colors border-b last:border-0 border-gray-50">
                                    <img src="${item.main_image || 'https://via.placeholder.com/50'}" class="w-12 h-12 rounded-lg object-contain bg-gray-50 flex-shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gray-800 text-sm truncate">${item.name}</p>
                                        <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">SKU: ${item.sku}</p>
                                    </div>
                                    <p class="font-bold text-primary-600 text-sm">$${parseFloat(item.price).toFixed(2)}</p>
                                </a>
                            `).join('');
                            resultsContainer.classList.remove('hidden');
                        } else {
                            resultsContainer.innerHTML = '<div class="p-4 text-center text-sm text-gray-500 font-medium">No results found</div>';
                            resultsContainer.classList.remove('hidden');
                        }
                    });
            }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                resultsContainer.classList.add('hidden');
            }
        });
    }
});
