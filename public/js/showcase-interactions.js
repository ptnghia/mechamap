/**
 * Showcase Interactions JavaScript
 * Handles AJAX interactions for showcase pages: like, bookmark, follow
 *
 * @version 1.0
 * @author MechaMap Development Team
 */

class ShowcaseInteractions {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupToastContainer();
        console.log('✅ Showcase Interactions initialized');
    }

    /**
     * Bind event listeners to interaction buttons
     */
    bindEvents() {
        // Like button
        const likeForm = document.querySelector('.like-form');
        if (likeForm) {
            likeForm.addEventListener('submit', (e) => this.handleLike(e));
        }

        // Bookmark button
        const bookmarkForm = document.querySelector('.bookmark-form');
        if (bookmarkForm) {
            bookmarkForm.addEventListener('submit', (e) => this.handleBookmark(e));
        }

        // Follow button
        const followForm = document.querySelector('.follow-form');
        if (followForm) {
            followForm.addEventListener('submit', (e) => this.handleFollow(e));
        }

        // Share buttons
        this.bindShareEvents();
    }

    /**
     * Handle like/unlike showcase
     */
    async handleLike(event) {
        event.preventDefault();

        const form = event.target;
        const button = form.querySelector('button');
        const originalContent = button.innerHTML;

        try {
            // Set loading state
            this.setButtonLoading(button, 'Đang xử lý...');

            const response = await this.makeRequest(form.action, 'POST');

            if (response.success) {
                // Update button appearance
                const heartIcon = response.is_liked ? 'fas fa-heart' : 'far fa-heart';
                const buttonClass = response.is_liked ? 'btn btn-sm btn-danger' : 'btn btn-sm btn-outline-danger';

                button.innerHTML = `<i class="${heartIcon} me-1"></i>${response.likes_count} Thích`;
                button.className = buttonClass;

                // Update stats in other places
                this.updateStatsDisplay('likes', response.likes_count);

                // Show success message
                this.showToast(response.message, 'success');
            } else {
                throw new Error(response.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Like error:', error);
            button.innerHTML = originalContent;
            this.showToast(error.message || 'Có lỗi xảy ra khi xử lý yêu cầu', 'error');
        } finally {
            this.setButtonEnabled(button);
        }
    }

    /**
     * Handle bookmark/unbookmark showcase
     */
    async handleBookmark(event) {
        event.preventDefault();

        const form = event.target;
        const button = form.querySelector('button');
        const originalContent = button.innerHTML;

        try {
            // Set loading state
            this.setButtonLoading(button, 'Đang xử lý...');

            const response = await this.makeRequest(form.action, 'POST');

            if (response.success) {
                // Update button appearance
                const bookmarkIcon = response.is_bookmarked ? 'fas fa-bookmark' : 'far fa-bookmark';
                const buttonClass = response.is_bookmarked ? 'btn btn-sm btn-warning' : 'btn btn-sm btn-outline-warning';
                const buttonText = response.is_bookmarked ? 'Đã lưu' : 'Lưu';

                button.innerHTML = `<i class="${bookmarkIcon} me-1"></i>${buttonText}`;
                button.className = buttonClass;

                // Show success message
                this.showToast(response.message, 'success');
            } else {
                throw new Error(response.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Bookmark error:', error);
            button.innerHTML = originalContent;
            this.showToast(error.message || 'Có lỗi xảy ra khi xử lý yêu cầu', 'error');
        } finally {
            this.setButtonEnabled(button);
        }
    }

    /**
     * Handle follow/unfollow showcase author
     */
    async handleFollow(event) {
        event.preventDefault();

        const form = event.target;
        const button = form.querySelector('button');
        const originalContent = button.innerHTML;

        try {
            // Set loading state
            this.setButtonLoading(button, 'Đang xử lý...');

            const response = await this.makeRequest(form.action, 'POST');

            if (response.success) {
                // Update button appearance
                const bellIcon = response.is_following ? 'fas fa-bell-slash' : 'fas fa-bell';
                const buttonClass = response.is_following ? 'btn btn-sm btn-success' : 'btn btn-sm btn-outline-success';
                const buttonText = response.is_following ? 'Đang theo dõi' : 'Theo dõi';

                button.innerHTML = `<i class="${bellIcon} me-1"></i>${buttonText}`;
                button.className = buttonClass;

                // Update follow count in stats
                this.updateStatsDisplay('follows', response.follows_count);

                // Show success message
                this.showToast(response.message, 'success');
            } else {
                throw new Error(response.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Follow error:', error);
            button.innerHTML = originalContent;
            this.showToast(error.message || 'Có lỗi xảy ra khi xử lý yêu cầu', 'error');
        } finally {
            this.setButtonEnabled(button);
        }
    }

    /**
     * Bind share event listeners
     */
    bindShareEvents() {
        // Use event delegation for share buttons
        document.addEventListener('click', (e) => {
            const shareButton = e.target.closest('[data-action]');
            if (!shareButton) return;

            e.preventDefault();
            const action = shareButton.dataset.action;

            switch (action) {
                case 'facebook':
                    this.shareOnFacebook();
                    break;
                case 'twitter':
                    this.shareOnTwitter();
                    break;
                case 'whatsapp':
                    this.shareOnWhatsApp();
                    break;
                case 'copy':
                    this.copyToClipboard();
                    break;
                default:
                    console.warn('Unknown share action:', action);
            }
        });
    }

    /**
     * Make AJAX request with proper error handling
     */
    async makeRequest(url, method = 'POST', data = {}) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10s timeout

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: method !== 'GET' ? JSON.stringify(data) : undefined,
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            clearTimeout(timeoutId);
            if (error.name === 'AbortError') {
                throw new Error('Yêu cầu bị hủy do timeout');
            }
            throw error;
        }
    }

    /**
     * Set button to loading state
     */
    setButtonLoading(button, text = 'Đang xử lý...') {
        button.disabled = true;
        button.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i>${text}`;
    }

    /**
     * Enable button and restore functionality
     */
    setButtonEnabled(button) {
        button.disabled = false;
    }

    /**
     * Update stats display in various places
     */
    updateStatsDisplay(type, count) {
        // Update in sidebar stats
        const sidebarStat = document.querySelector(`.sidebar-stats .${type}-count`);
        if (sidebarStat) {
            sidebarStat.textContent = count;
        }

        // Update in main stats area
        const mainStat = document.querySelector(`.showcase-stats .${type}-count`);
        if (mainStat) {
            mainStat.textContent = count;
        }
    }

    /**
     * Social sharing methods
     */
    shareOnFacebook() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(document.title);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
    }

    shareOnTwitter() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(document.title);
        window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank', 'width=600,height=400');
    }

    shareOnWhatsApp() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(document.title);
        window.open(`https://wa.me/?text=${title} ${url}`, '_blank');
    }

    async copyToClipboard() {
        try {
            await navigator.clipboard.writeText(window.location.href);
            this.showToast('Đã sao chép liên kết vào clipboard!', 'success');
        } catch (err) {
            console.error('Could not copy text: ', err);
            this.showToast('Không thể sao chép liên kết', 'error');
        }
    }

    /**
     * Setup toast container for notifications
     */
    setupToastContainer() {
        if (!document.getElementById('toast-container')) {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
    }

    /**
     * Show toast notification
     */
    showToast(message, type = 'info') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toastId = 'toast-' + Date.now();
        const bgClass = {
            'success': 'bg-success',
            'error': 'bg-danger',
            'warning': 'bg-warning',
            'info': 'bg-info'
        }[type] || 'bg-info';

        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center text-white ${bgClass} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        container.appendChild(toast);

        // Initialize and show toast
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: 3000
        });
        bsToast.show();

        // Remove from DOM after hiding
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.showcaseInteractions = new ShowcaseInteractions();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ShowcaseInteractions;
}
