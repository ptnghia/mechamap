/**
 * Example: Using ThreadItemBuilder for dynamic thread loading
 * File: public/js/examples/thread-loading-example.js
 *
 * Ví dụ về cách sử dụng ThreadItemBuilder để load threads động với
 * layout consistency hoàn toàn giữa server-side và client-side.
 */

document.addEventListener('DOMContentLoaded', function() {
    const threadContainer = document.getElementById('thread-list');
    const loadMoreButton = document.getElementById('load-more-threads');

    if (!threadContainer || !loadMoreButton) {
        return; // Exit if elements not found
    }

    let currentPage = 1;
    let isLoading = false;

    // Translations for badges
    const translations = {
        sticky: 'Đã ghim',
        locked: 'Đã khóa'
    };

    /**
     * Load more threads via API
     */
    async function loadMoreThreads() {
        if (isLoading) return;

        isLoading = true;
        loadMoreButton.disabled = true;

        // Show skeleton loading
        ThreadItemBuilder.showSkeletonLoading(threadContainer, 3);

        try {
            const response = await fetch(`/api/threads/more?page=${currentPage + 1}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();

            // Remove skeleton loading
            ThreadItemBuilder.removeSkeletonLoading(threadContainer);

            // Create and append thread elements
            data.threads.forEach(thread => {
                const threadElement = ThreadItemBuilder.createThreadElement(thread, translations);
                threadContainer.appendChild(threadElement);
            });

            // Bind events cho các thread mới được thêm
            ThreadItemBuilder.bindActionEvents(threadContainer);

            // Update pagination
            currentPage++;

            // Hide load more button if no more threads
            if (!data.has_more) {
                loadMoreButton.style.display = 'none';
            }

        } catch (error) {
            console.error('Error loading threads:', error);
            ThreadItemBuilder.removeSkeletonLoading(threadContainer);

            // Show error message
            const errorElement = document.createElement('div');
            errorElement.className = 'alert alert-danger';
            errorElement.innerHTML = `
                <i class="bi bi-exclamation-triangle me-2"></i>
                Có lỗi xảy ra khi tải thêm threads.
                <button class="btn btn-sm btn-outline-danger ms-2" onclick="this.parentElement.remove()">
                    Đóng
                </button>
            `;
            threadContainer.appendChild(errorElement);

        } finally {
            isLoading = false;
            loadMoreButton.disabled = false;
        }
    }

    /**
     * Infinite scroll implementation
     */
    function handleInfiniteScroll() {
        const scrollPosition = window.innerHeight + window.scrollY;
        const documentHeight = document.documentElement.offsetHeight;
        const threshold = 200; // Load when 200px from bottom

        if (scrollPosition >= documentHeight - threshold && !isLoading) {
            loadMoreThreads();
        }
    }

    // Event listeners
    loadMoreButton.addEventListener('click', loadMoreThreads);

    // Optional: Enable infinite scroll
    window.addEventListener('scroll', throttle(handleInfiniteScroll, 100));

    // Bind events cho threads đã có sẵn trên trang
    ThreadItemBuilder.bindActionEvents(threadContainer);
});

/**
 * Throttle function để optimize scroll performance
 * @param {Function} func - Function to throttle
 * @param {number} wait - Wait time in milliseconds
 * @returns {Function} - Throttled function
 */
function throttle(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Global error handler cho AJAX bookmark/follow actions
 */
window.addEventListener('error', function(e) {
    if (e.target && e.target.classList && e.target.classList.contains('thread-actions')) {
        console.error('Thread action error:', e);
    }
});

/**
 * Show toast notification
 * @param {string} message - Message to show
 * @param {string} type - Type: success, error, info
 */
function showToast(message, type = 'info') {
    // Simple toast implementation
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

    document.body.appendChild(toast);

    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}

// Export functions để có thể sử dụng ở nơi khác
window.ThreadLoader = {
    loadMoreThreads,
    showToast,
    throttle
};
