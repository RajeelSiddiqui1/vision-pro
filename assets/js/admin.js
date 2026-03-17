/**
 * visionpro-admin.js 
 * Centralized AJAX handlers and UI utilities for Pro Dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. AJAX Status Toggles
    const ajaxToggles = document.querySelectorAll('.ajax-status-toggle');
    ajaxToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const endpoint = this.dataset.endpoint;
            const id = this.dataset.id;
            const field = this.dataset.field;
            const value = this.checked ? 1 : 0;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

            performAjax(endpoint, { id, field, value, csrf_token: csrf });
        });
    });

    // 2. Select Status Update
    const statusSelects = document.querySelectorAll('.ajax-status-select');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const endpoint = this.dataset.endpoint;
            const id = this.dataset.id;
            const value = this.value;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

            performAjax(endpoint, { id, status: value, csrf_token: csrf }, (res) => {
                // Optional: Update row styling based on status
                if (res.success && this.closest('tr')) {
                    const row = this.closest('tr');
                    // Add some visual feedback like a success flash
                    row.classList.add('bg-green-50');
                    setTimeout(() => row.classList.remove('bg-green-50'), 1000);
                }
            });
        });
    });
});

/**
 * Universal AJAX Helper
 */
async function performAjax(endpoint, data, callback = null) {
    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        
        if (result.success) {
            showToast('Success', result.message || 'Updated successfully', 'success');
            if (callback) callback(result);
        } else {
            showToast('Error', result.message || 'Something went wrong', 'error');
        }
    } catch (error) {
        console.error('AJAX Error:', error);
        showToast('Error', 'Sever connection failed', 'error');
    }
}

/**
 * Toast Notification System
 */
function showToast(title, message, type = 'success') {
    const container = document.getElementById('toast-container') || createToastContainer();
    
    const toast = document.createElement('div');
    const colorClass = type === 'success' ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50';
    const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';

    toast.className = `fixed bottom-4 right-4 z-[9999] border-l-4 p-4 shadow-lg rounded-lg transform transition-all duration-300 translate-y-10 opacity-0 ${colorClass} ${textColor}`;
    toast.innerHTML = `
        <div class="flex items-center gap-3">
            <div class="font-black text-sm uppercase">${title}</div>
            <div class="text-xs font-medium">${message}</div>
        </div>
    `;

    container.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-y-10', 'opacity-0');
    }, 10);

    // Auto remove
    setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

function createToastContainer() {
    const div = document.createElement('div');
    div.id = 'toast-container';
    document.body.appendChild(div);
    return div;
}
