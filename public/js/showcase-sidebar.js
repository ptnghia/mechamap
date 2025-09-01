/**
 * Showcase Sidebar JavaScript
 * Handles follow/unfollow functionality and other sidebar interactions
 */

class ShowcaseSidebar {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        // this.initLazyLoading(); // Disabled lazy loading for sidebar
        console.log('✅ Showcase Sidebar initialized');
    }

    bindEvents() {
        // Follow/Unfollow button
        document.addEventListener('click', (e) => {
            if (e.target.closest('.follow-btn')) {
                e.preventDefault();
                this.handleFollowToggle(e.target.closest('.follow-btn'));
            }
        });

        // Lazy loading for images - DISABLED
        // if ('IntersectionObserver' in window) {
        //     this.setupLazyLoading();
        // }
    }

    /**
     * Handle follow/unfollow toggle
     */
    async handleFollowToggle(button) {
        const userId = button.dataset.userId;
        const isFollowing = button.dataset.following === 'true';

        if (!userId) {
            console.error('User ID not found');
            return;
        }

        // Disable button during request
        button.disabled = true;
        const originalText = button.querySelector('.follow-text').textContent;
        button.querySelector('.follow-text').textContent = 'Đang xử lý...';

        try {
            const response = await fetch(`/ajax/users/${userId}/follow`, {
                method: isFollowing ? 'DELETE' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Update button state
                const newFollowing = !isFollowing;
                button.dataset.following = newFollowing.toString();

                // Update button appearance
                const icon = button.querySelector('i');
                const text = button.querySelector('.follow-text');

                if (newFollowing) {
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-outline-primary');
                    icon.className = 'fas fa-user-minus';
                    text.textContent = 'Bỏ theo dõi';
                } else {
                    button.classList.remove('btn-outline-primary');
                    button.classList.add('btn-primary');
                    icon.className = 'fas fa-user-plus';
                    text.textContent = 'Theo dõi';
                }

                // Show success message
                this.showNotification(data.message || 'Cập nhật thành công!', 'success');

            } else {
                throw new Error(data.message || 'Có lỗi xảy ra');
            }

        } catch (error) {
            console.error('Follow toggle error:', error);
            button.querySelector('.follow-text').textContent = originalText;
            this.showNotification(error.message || 'Có lỗi xảy ra, vui lòng thử lại', 'error');
        } finally {
            button.disabled = false;
        }
    }

    /**
     * Setup lazy loading for images - DISABLED
     */
    setupLazyLoading() {
        // Lazy loading disabled for sidebar
        // const imageObserver = new IntersectionObserver((entries, observer) => {
        //     entries.forEach(entry => {
        //         if (entry.isIntersecting) {
        //             const img = entry.target;
        //             if (img.dataset.src) {
        //                 img.src = img.dataset.src;
        //                 img.removeAttribute('data-src');
        //                 img.classList.remove('lazy');
        //                 observer.unobserve(img);
        //             }
        //         }
        //     });
        // });

        // // Observe all lazy images in sidebar
        // document.querySelectorAll('.showcase-sidebar img[data-src]').forEach(img => {
        //     imageObserver.observe(img);
        // });
    }

    /**
     * Initialize lazy loading for existing images - DISABLED
     */
    initLazyLoading() {
        // Lazy loading disabled for sidebar - images will load normally
        // const sidebarImages = document.querySelectorAll('.showcase-sidebar img[src]:not([data-src])');
        // sidebarImages.forEach(img => {
        //     if (img.src && !img.src.includes('ui-avatars.com')) {
        //         img.dataset.src = img.src;
        //         img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="60" height="45"%3E%3Crect width="100%25" height="100%25" fill="%23f8f9fa"/%3E%3C/svg%3E';
        //         img.classList.add('lazy');
        //     }
        // });
        console.log('✅ Lazy loading disabled for sidebar images');
    }

    /**
     * Show notification message
     */
    showNotification(message, type = 'info') {
        // Check if SweetAlert2 is available
        if (typeof Swal !== 'undefined') {
            const icon = type === 'success' ? 'success' : type === 'error' ? 'error' : 'info';
            Swal.fire({
                text: message,
                icon: icon,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            // Fallback to browser alert
            alert(message);
        }
    }

    /**
     * Refresh sidebar data (for future use with AJAX)
     */
    async refreshSidebarData(showcaseId) {
        try {
            const response = await fetch(`/ajax/showcase/${showcaseId}/sidebar-data`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateSidebarContent(data);
            }
        } catch (error) {
            console.error('Failed to refresh sidebar data:', error);
        }
    }

    /**
     * Update sidebar content with new data
     */
    updateSidebarContent(data) {
        // Update author stats
        if (data.authorStats) {
            const statsElements = document.querySelectorAll('.author-stats .stat-number');
            if (statsElements[0]) statsElements[0].textContent = data.authorStats.total_showcases || 0;
            if (statsElements[1]) statsElements[1].textContent = this.formatNumber(data.authorStats.total_views || 0);
            if (statsElements[2]) statsElements[2].textContent = (data.authorStats.avg_rating || 0).toFixed(1);
        }

        // Update other showcases if needed
        if (data.otherShowcases) {
            // Implementation for updating other showcases list
        }

        // Update featured showcases if needed
        if (data.featuredShowcases) {
            // Implementation for updating featured showcases list
        }

        // Update top contributors if needed
        if (data.topContributors) {
            // Implementation for updating top contributors list
        }
    }

    /**
     * Format number with commas
     */
    formatNumber(num) {
        return new Intl.NumberFormat('vi-VN').format(num);
    }

    /**
     * Handle image loading errors
     */
    handleImageError(img) {
        img.src = '/images/placeholder-showcase.jpg';
        img.onerror = null; // Prevent infinite loop
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new ShowcaseSidebar();
});

// Handle image errors globally for sidebar
document.addEventListener('error', (e) => {
    if (e.target.tagName === 'IMG' && e.target.closest('.showcase-sidebar')) {
        e.target.src = '/images/placeholder-showcase.jpg';
        e.target.onerror = null;
    }
}, true);

// Export for potential external use
window.ShowcaseSidebar = ShowcaseSidebar;
