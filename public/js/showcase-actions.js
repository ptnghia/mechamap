/**
 * Showcase Actions JavaScript
 * Handles bookmark and follow functionality for showcases
 */

// Prevent multiple declarations
if (typeof window.ShowcaseActions === 'undefined') {

class ShowcaseActions {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Bind bookmark buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.showcase-bookmark-btn')) {
                e.preventDefault();
                this.handleBookmark(e.target.closest('.showcase-bookmark-btn'));
            }
        });

        // Bind follow buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.showcase-follow-btn')) {
                e.preventDefault();
                this.handleFollow(e.target.closest('.showcase-follow-btn'));
            }
        });
    }

    async handleBookmark(button) {
        const showcaseId = button.dataset.showcaseId;
        const isBookmarked = button.dataset.bookmarked === 'true';

        if (!showcaseId) {
            console.error('Showcase ID not found');
            return;
        }

        // Check if user is logged in
        if (!this.csrfToken) {
            this.showNotification('Vui lòng đăng nhập để sử dụng tính năng này', 'warning');
            return;
        }

        // Set loading state
        this.setButtonLoading(button, true);

        try {
            const response = await fetch(`/ajax/showcases/${showcaseId}/bookmark`, {
                method: isBookmarked ? 'DELETE' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success !== false) {
                // Clear loading state first
                this.setButtonLoading(button, false);

                // Update button state
                this.updateBookmarkButton(button, !isBookmarked);

                // Show success message
                const message = !isBookmarked ? 'Đã lưu showcase vào bookmark.' : 'Đã bỏ lưu showcase.';
                this.showNotification(message, 'success');
            } else {
                // Clear loading state on error
                this.setButtonLoading(button, false);
                throw new Error(data.message || 'Có lỗi xảy ra khi xử lý bookmark');
            }
        } catch (error) {
            console.error('Bookmark error:', error);
            this.showNotification('Không thể lưu showcase. Vui lòng thử lại.', 'error');
            // Clear loading state on error (if not already cleared)
            this.setButtonLoading(button, false);
        }
    }

    async handleFollow(button) {
        const showcaseId = button.dataset.showcaseId;
        const isFollowing = button.dataset.following === 'true';

        if (!showcaseId) {
            console.error('Showcase ID not found');
            return;
        }

        // Check if user is logged in
        if (!this.csrfToken) {
            this.showNotification('Vui lòng đăng nhập để sử dụng tính năng này', 'warning');
            return;
        }

        // Set loading state
        this.setButtonLoading(button, true);

        try {
            const response = await fetch(`/ajax/showcases/${showcaseId}/follow`, {
                method: isFollowing ? 'DELETE' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success !== false) {
                // Clear loading state first
                this.setButtonLoading(button, false);

                // Update button state
                this.updateFollowButton(button, !isFollowing);

                // Show success message
                const message = !isFollowing ? 'Đã theo dõi showcase.' : 'Đã bỏ theo dõi showcase.';
                this.showNotification(message, 'success');
            } else {
                // Clear loading state on error
                this.setButtonLoading(button, false);
                throw new Error(data.message || 'Có lỗi xảy ra khi xử lý theo dõi');
            }
        } catch (error) {
            console.error('Follow error:', error);
            this.showNotification('Không thể theo dõi showcase. Vui lòng thử lại.', 'error');
            // Clear loading state on error (if not already cleared)
            this.setButtonLoading(button, false);
        }
    }

    updateBookmarkButton(button, isBookmarked) {
        const icon = button.querySelector('i');
        const text = button.querySelector('.btn-text');

        // Clear any stored loading state
        delete button.dataset.originalIcon;
        delete button.dataset.originalText;

        // Update data attribute
        button.dataset.bookmarked = isBookmarked ? 'true' : 'false';

        if (isBookmarked) {
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-warning', 'bookmarked');

            if (icon) {
                icon.className = 'fas fa-bookmark';
            }
            if (text) {
                text.textContent = 'Đã lưu';
            }

            button.setAttribute('title', 'Bỏ lưu showcase');
        } else {
            button.classList.remove('btn-warning', 'bookmarked');
            button.classList.add('btn-outline-secondary');

            if (icon) {
                icon.className = 'far fa-bookmark';
            }
            if (text) {
                text.textContent = 'Lưu';
            }

            button.setAttribute('title', 'Lưu showcase vào danh sách yêu thích');
        }
    }

    updateFollowButton(button, isFollowing) {
        const icon = button.querySelector('i');
        const text = button.querySelector('.btn-text');

        // Clear any stored loading state
        delete button.dataset.originalIcon;
        delete button.dataset.originalText;

        // Update data attribute
        button.dataset.following = isFollowing ? 'true' : 'false';

        if (isFollowing) {
            button.classList.remove('btn-outline-primary');
            button.classList.add('btn-success', 'following');

            if (icon) {
                icon.className = 'fas fa-user-check';
            }
            if (text) {
                text.textContent = 'Đang theo dõi';
            }

            button.setAttribute('title', 'Bỏ theo dõi showcase');
        } else {
            button.classList.remove('btn-success', 'following');
            button.classList.add('btn-outline-primary');

            if (icon) {
                icon.className = 'fas fa-user-plus';
            }
            if (text) {
                text.textContent = 'Theo dõi';
            }

            button.setAttribute('title', 'Theo dõi showcase để nhận thông báo');
        }
    }

    setButtonLoading(button, isLoading) {
        const icon = button.querySelector('i');
        const text = button.querySelector('.btn-text');

        if (isLoading) {
            button.disabled = true;
            button.classList.add('loading');

            // Store original state for restoration
            if (icon && !button.dataset.originalIcon) {
                button.dataset.originalIcon = icon.className;
            }
            if (text && !button.dataset.originalText) {
                button.dataset.originalText = text.textContent;
            }

            if (icon) {
                icon.className = 'fas fa-spinner fa-spin me-1';
            }
            if (text) {
                text.textContent = 'Đang xử lý...';
            }
        } else {
            button.disabled = false;
            button.classList.remove('loading');

            // Restore original state if update functions haven't been called yet
            if (icon && button.dataset.originalIcon) {
                icon.className = button.dataset.originalIcon;
                delete button.dataset.originalIcon;
            }
            if (text && button.dataset.originalText) {
                text.textContent = button.dataset.originalText;
                delete button.dataset.originalText;
            }
        }
    }

    showNotification(message, type = 'info') {
        // Try to use existing notification system
        if (typeof window.showNotification === 'function') {
            window.showNotification(message, type);
            return;
        }

        // Fallback to simple alert or toast
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
            return;
        }

        // Simple console log as last resort
        console.log(`${type.toUpperCase()}: ${message}`);

        // You could also create a simple toast here
        this.createSimpleToast(message, type);
    }

    createSimpleToast(message, type) {
        // Create a simple toast notification
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                <span>${message}</span>
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;

        document.body.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }
}

// Store the class globally to prevent redeclaration
window.ShowcaseActions = ShowcaseActions;

} // End of if check

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.showcaseActions === 'undefined') {
        window.showcaseActions = new window.ShowcaseActions();
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = window.ShowcaseActions;
}
