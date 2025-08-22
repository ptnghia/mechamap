/**
 * Unified Notifications Component
 * Handles all notification operations through single interface
 * Replaces separate alert and notification components
 */
class UnifiedNotifications {
    constructor() {
        this.baseUrl = '/ajax/notifications';
        this.apiUrl = '/api/v2/notifications';
        this.isInitialized = false;
        this.unreadCount = 0;
        this.notifications = [];
        this.dropdownContainer = null;
        this.counterElements = [];

        this.init();
    }

    /**
     * Initialize the component
     */
    init() {
        if (this.isInitialized) return;

        console.log('🔔 Initializing Unified Notifications...');

        this.setupElements();
        this.bindEvents();
        this.loadInitialData();
        this.setupRealTimeUpdates();

        this.isInitialized = true;
        console.log('✅ Unified Notifications initialized');
    }

    /**
     * Setup DOM elements
     */
    setupElements() {
        // Find notification counter elements
        this.counterElements = document.querySelectorAll('[data-notification-counter]');

        // Find dropdown container
        this.dropdownContainer = document.querySelector('#notificationDropdown .dropdown-menu');

        // Find notification list container (for index page)
        this.listContainer = document.querySelector('.notification-list');
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Dropdown toggle
        const dropdownToggle = document.querySelector('[data-bs-toggle="dropdown"][data-notification-dropdown]');
        if (dropdownToggle) {
            dropdownToggle.addEventListener('click', () => this.loadDropdownNotifications());
        }

        // Mark all as read
        const markAllBtn = document.getElementById('markAllReadBtn');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', () => this.markAllAsRead());
        }

        // Clear all
        const clearAllBtn = document.getElementById('clearAllBtn');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', () => this.clearAll());
        }

        // Individual notification actions
        document.addEventListener('click', (e) => {
            if (e.target.matches('.mark-read-btn') || e.target.closest('.mark-read-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.mark-read-btn');
                const id = btn.dataset.id;
                this.markAsRead(id);
            }

            if (e.target.matches('.mark-unread-btn') || e.target.closest('.mark-unread-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.mark-unread-btn');
                const id = btn.dataset.id;
                this.markAsUnread(id);
            }

            if (e.target.matches('.delete-btn') || e.target.closest('.delete-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.delete-btn');
                const id = btn.dataset.id;
                this.deleteNotification(id);
            }

            if (e.target.matches('.archive-btn') || e.target.closest('.archive-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.archive-btn');
                const id = btn.dataset.id;
                this.archiveNotification(id);
            }
        });

        // Track notification clicks
        document.addEventListener('click', (e) => {
            const notificationItem = e.target.closest('.notification-item');
            if (notificationItem && !e.target.closest('.notification-actions')) {
                const id = notificationItem.dataset.notificationId;
                if (id) {
                    this.trackInteraction(id, 'click');
                }
            }
        });
    }

    /**
     * Load initial data
     */
    async loadInitialData() {
        try {
            await this.updateUnreadCount();
        } catch (error) {
            console.error('Failed to load initial notification data:', error);
        }
    }

    /**
     * Setup real-time updates
     */
    setupRealTimeUpdates() {
        // Connect to WebSocket if available
        if (window.notificationService && window.notificationService.socket) {
            const socket = window.notificationService.socket;

            socket.on('notification', (data) => {
                console.log('📨 New notification received:', data);
                this.handleNewNotification(data);
            });

            socket.on('notification.read', (data) => {
                console.log('👁️ Notification marked as read:', data);
                this.handleNotificationRead(data);
            });

            socket.on('notification.deleted', (data) => {
                console.log('🗑️ Notification deleted:', data);
                this.handleNotificationDeleted(data);
            });
        }

        // Fallback: Poll for updates every 30 seconds
        setInterval(() => {
            this.updateUnreadCount();
        }, 30000);
    }

    /**
     * Load notifications for dropdown
     */
    async loadDropdownNotifications() {
        try {
            const response = await fetch(`${this.baseUrl}/dropdown`);
            const data = await response.json();

            if (data.success) {
                this.renderDropdownNotifications(data.notifications);
                this.updateCounter(data.unread_count);
            }
        } catch (error) {
            console.error('Failed to load dropdown notifications:', error);
            this.showError('Không thể tải thông báo');
        }
    }

    /**
     * Update unread count
     */
    async updateUnreadCount() {
        try {
            const response = await fetch(`${this.baseUrl}/unread-count`);
            const data = await response.json();

            if (data.success) {
                this.updateCounter(data.unread_count);
            }
        } catch (error) {
            console.error('Failed to update unread count:', error);
        }
    }

    /**
     * Mark notification as read
     */
    async markAsRead(notificationId) {
        try {
            const response = await fetch(`${this.baseUrl}/${notificationId}/read`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.updateNotificationUI(notificationId, { is_read: true });
                this.updateCounter(data.unread_count);
                this.showSuccess('Đã đánh dấu thông báo là đã đọc');
            } else {
                this.showError(data.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
            this.showError('Không thể đánh dấu thông báo');
        }
    }

    /**
     * Mark notification as unread
     */
    async markAsUnread(notificationId) {
        try {
            const response = await fetch(`${this.baseUrl}/${notificationId}/unread`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.updateNotificationUI(notificationId, { is_read: false });
                this.updateCounter(data.unread_count);
                this.showSuccess('Đã đánh dấu thông báo là chưa đọc');
            } else {
                this.showError(data.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Failed to mark notification as unread:', error);
            this.showError('Không thể đánh dấu thông báo');
        }
    }

    /**
     * Mark all notifications as read
     */
    async markAllAsRead() {
        if (!confirm('Bạn có chắc muốn đánh dấu tất cả thông báo là đã đọc?')) {
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}/mark-all-read`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.updateCounter(0);
                this.markAllNotificationsAsRead();
                this.showSuccess(data.message);
            } else {
                this.showError(data.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Failed to mark all as read:', error);
            this.showError('Không thể đánh dấu tất cả thông báo');
        }
    }

    /**
     * Delete notification
     */
    async deleteNotification(notificationId) {
        if (!confirm('Bạn có chắc muốn xóa thông báo này?')) {
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.removeNotificationFromUI(notificationId);
                this.updateCounter(data.unread_count);
                this.showSuccess('Đã xóa thông báo');
            } else {
                this.showError(data.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Failed to delete notification:', error);
            this.showError('Không thể xóa thông báo');
        }
    }

    /**
     * Clear all notifications
     */
    async clearAll() {
        if (!confirm('Bạn có chắc muốn xóa tất cả thông báo? Hành động này không thể hoàn tác.')) {
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}/clear-all`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.clearAllNotificationsFromUI();
                this.updateCounter(0);
                this.showSuccess(data.message);
            } else {
                this.showError(data.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Failed to clear all notifications:', error);
            this.showError('Không thể xóa tất cả thông báo');
        }
    }

    /**
     * Archive notification
     */
    async archiveNotification(notificationId) {
        try {
            const response = await fetch(`${this.baseUrl}/${notificationId}/archive`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.removeNotificationFromUI(notificationId);
                this.showSuccess('Đã lưu trữ thông báo');
            } else {
                this.showError(data.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            console.error('Failed to archive notification:', error);
            this.showError('Không thể lưu trữ thông báo');
        }
    }

    /**
     * Track notification interaction
     */
    async trackInteraction(notificationId, action) {
        try {
            await fetch(`${this.baseUrl}/${notificationId}/track`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ action })
            });
        } catch (error) {
            console.error('Failed to track interaction:', error);
        }
    }

    /**
     * Update notification counter
     */
    updateCounter(count) {
        this.unreadCount = count;

        this.counterElements.forEach(element => {
            element.textContent = count;
            element.style.display = count > 0 ? 'inline' : 'none';
        });

        // Update page title
        if (count > 0) {
            document.title = `(${count}) ${document.title.replace(/^\(\d+\)\s/, '')}`;
        } else {
            document.title = document.title.replace(/^\(\d+\)\s/, '');
        }
    }

    /**
     * Render dropdown notifications
     */
    renderDropdownNotifications(notifications) {
        if (!this.dropdownContainer) return;

        if (notifications.length === 0) {
            this.dropdownContainer.innerHTML = `
                <div class="dropdown-item-text text-center py-4">
                    <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                    <div class="text-muted">Không có thông báo nào</div>
                </div>
            `;
            return;
        }

        const html = notifications.map(notification => `
            <div class="dropdown-item notification-dropdown-item" data-notification-id="${notification.id}">
                <div class="d-flex align-items-start">
                    <div class="notification-icon me-3">
                        <i class="fas fa-${notification.icon} text-${notification.color}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="dropdown-item-title mb-1">${notification.title}</h6>
                        <p class="dropdown-item-text mb-1">${notification.message}</p>
                        <small class="text-muted">${notification.created_at}</small>
                        ${notification.requires_action ? `
                            <div class="mt-2">
                                <a href="${notification.action_url}" class="btn btn-sm btn-primary">
                                    ${notification.action_text}
                                </a>
                            </div>
                        ` : ''}
                    </div>
                    <div class="notification-actions">
                        <button class="btn btn-sm btn-outline-secondary mark-read-btn" data-id="${notification.id}">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        this.dropdownContainer.innerHTML = html + `
            <div class="dropdown-divider"></div>
            <div class="dropdown-item text-center">
                <a href="/notifications" class="btn btn-sm btn-primary">Xem tất cả thông báo</a>
            </div>
        `;
    }

    /**
     * Update notification UI
     */
    updateNotificationUI(notificationId, updates) {
        const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
        if (!notificationElement) return;

        if (updates.is_read !== undefined) {
            if (updates.is_read) {
                notificationElement.classList.remove('notification-unread');
                notificationElement.querySelector('.badge.bg-primary')?.remove();
            } else {
                notificationElement.classList.add('notification-unread');
                const badgeContainer = notificationElement.querySelector('.d-flex.align-items-center.gap-2');
                if (badgeContainer && !badgeContainer.querySelector('.badge.bg-primary')) {
                    badgeContainer.insertAdjacentHTML('beforeend', '<span class="badge bg-primary">Mới</span>');
                }
            }
        }
    }

    /**
     * Remove notification from UI
     */
    removeNotificationFromUI(notificationId) {
        const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
        if (notificationElement) {
            notificationElement.remove();
        }

        // Check if list is empty and show empty state
        this.checkAndShowEmptyState();
    }

    /**
     * Mark all notifications as read in UI
     */
    markAllNotificationsAsRead() {
        const unreadNotifications = document.querySelectorAll('.notification-unread');
        unreadNotifications.forEach(element => {
            element.classList.remove('notification-unread');
            element.querySelector('.badge.bg-primary')?.remove();
        });
    }

    /**
     * Clear all notifications from UI
     */
    clearAllNotificationsFromUI() {
        const notificationList = document.querySelector('.list-group');
        if (notificationList) {
            notificationList.innerHTML = '';
        }
        this.showEmptyState();
    }

    /**
     * Check and show empty state
     */
    checkAndShowEmptyState() {
        const notificationList = document.querySelector('.list-group');
        if (notificationList && notificationList.children.length === 0) {
            this.showEmptyState();
        }
    }

    /**
     * Show empty state
     */
    showEmptyState() {
        const cardBody = document.querySelector('.card-body.p-0');
        if (cardBody) {
            cardBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-bell-slash fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Không có thông báo nào</h5>
                    <p class="text-muted">Bạn chưa có thông báo nào.</p>
                </div>
            `;
        }
    }

    /**
     * Handle new notification from WebSocket
     */
    handleNewNotification(data) {
        this.unreadCount++;
        this.updateCounter(this.unreadCount);

        // Show toast notification
        this.showToast(data.title, data.message, 'info');

        // Add to dropdown if open
        if (this.dropdownContainer && this.dropdownContainer.style.display !== 'none') {
            this.loadDropdownNotifications();
        }
    }

    /**
     * Handle notification read from WebSocket
     */
    handleNotificationRead(data) {
        this.updateNotificationUI(data.notification_id, { is_read: true });
        this.unreadCount = Math.max(0, this.unreadCount - 1);
        this.updateCounter(this.unreadCount);
    }

    /**
     * Handle notification deleted from WebSocket
     */
    handleNotificationDeleted(data) {
        this.removeNotificationFromUI(data.notification_id);
        if (!data.was_read) {
            this.unreadCount = Math.max(0, this.unreadCount - 1);
            this.updateCounter(this.unreadCount);
        }
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        this.showToast('Thành công', message, 'success');
    }

    /**
     * Show error message
     */
    showError(message) {
        this.showToast('Lỗi', message, 'error');
    }

    /**
     * Show toast notification
     */
    showToast(title, message, type = 'info') {
        // Use existing toast system or create simple alert
        if (window.showToast) {
            window.showToast(title, message, type);
        } else {
            console.log(`${type.toUpperCase()}: ${title} - ${message}`);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.unifiedNotifications = new UnifiedNotifications();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = UnifiedNotifications;
}
