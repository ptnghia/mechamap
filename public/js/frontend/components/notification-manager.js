/**
 * Real-time Notification Manager
 * Manages notification UI components and real-time updates
 */
class NotificationManager {
    constructor() {
        this.isInitialized = false;
        this.notificationDropdown = null;
        this.notificationBell = null;
        this.notificationCounter = null;
        this.notificationList = null;
        this.connectionStatus = null;
        this.currentNotifications = [];
        this.unreadCount = 0;

        this.init();
    }

    /**
     * Initialize notification manager
     */
    init() {
        if (this.isInitialized) return;

        // Find DOM elements
        this.findElements();

        // Setup event listeners
        this.setupEventListeners();

        // Initialize connection status
        this.initConnectionStatus();

        // Load initial notifications
        this.loadNotifications();

        // Setup real-time service callbacks
        this.setupRealTimeCallbacks();

        // Request notification permission
        this.requestNotificationPermission();

        this.isInitialized = true;
        console.log('NotificationManager: Initialized');
    }

    /**
     * Find DOM elements
     */
    findElements() {
        this.notificationBell = document.querySelector('.notification-bell, #notificationBell');
        this.notificationCounter = document.querySelector('.notification-counter, .badge');
        this.notificationDropdown = document.querySelector('#notificationDropdown');
        this.notificationList = document.querySelector('#notificationItems, .notification-list');

        // Create elements if they don't exist
        if (!this.notificationCounter && this.notificationBell) {
            this.createNotificationCounter();
        }

        if (!this.connectionStatus) {
            this.createConnectionStatus();
        }
    }

    /**
     * Create notification counter
     */
    createNotificationCounter() {
        this.notificationCounter = document.createElement('span');
        this.notificationCounter.className = 'notification-counter';
        this.notificationCounter.style.display = 'none';
        this.notificationBell.appendChild(this.notificationCounter);
    }

    /**
     * Create connection status indicator
     */
    createConnectionStatus() {
        this.connectionStatus = document.createElement('div');
        this.connectionStatus.className = 'connection-status connecting';
        this.connectionStatus.textContent = 'Đang kết nối...';
        this.connectionStatus.style.display = 'none';
        document.body.appendChild(this.connectionStatus);
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Notification bell click
        if (this.notificationBell) {
            this.notificationBell.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleNotificationDropdown();
            });
        }

        // Mark all as read
        const markAllReadBtn = document.querySelector('#markAllRead, .btn-mark-all-read');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', () => {
                this.markAllAsRead();
            });
        }

        // Clear all notifications
        const clearAllBtn = document.querySelector('#clearAll, .btn-clear-all');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', () => {
                this.clearAllNotifications();
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (this.notificationDropdown &&
                !this.notificationDropdown.contains(e.target) &&
                !this.notificationBell.contains(e.target)) {
                this.hideNotificationDropdown();
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isDropdownVisible()) {
                this.hideNotificationDropdown();
            }
        });
    }

    /**
     * Setup real-time service callbacks
     */
    setupRealTimeCallbacks() {
        if (window.NotificationService) {
            // Connection events
            window.NotificationService.on('onConnect', () => {
                this.updateConnectionStatus('connected', 'Đã kết nối');
            });

            window.NotificationService.on('onDisconnect', () => {
                this.updateConnectionStatus('disconnected', 'Mất kết nối');
            });

            window.NotificationService.on('onError', (error) => {
                this.updateConnectionStatus('disconnected', 'Lỗi kết nối');
                console.error('NotificationManager: Connection error', error);
            });

            // Notification events
            window.NotificationService.on('onNotification', (notification) => {
                this.handleNewNotification(notification);
            });
        }
    }

    /**
     * Initialize connection status
     */
    initConnectionStatus() {
        if (window.NotificationService) {
            const status = window.NotificationService.getConnectionStatus();
            if (status.isConnected) {
                this.updateConnectionStatus('connected', 'Đã kết nối');
            } else {
                this.updateConnectionStatus('connecting', 'Đang kết nối...');
            }
        }
    }

    /**
     * Update connection status
     */
    updateConnectionStatus(status, message) {
        if (this.connectionStatus) {
            this.connectionStatus.className = `connection-status ${status}`;
            this.connectionStatus.textContent = message;

            // Show status for a few seconds
            this.connectionStatus.style.display = 'block';

            if (status === 'connected') {
                setTimeout(() => {
                    this.connectionStatus.style.display = 'none';
                }, 3000);
            }
        }
    }

    /**
     * Load initial notifications
     */
    async loadNotifications() {
        try {
            const response = await fetch('/api/notifications?limit=20', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    // Handle different response structures
                    if (data.notifications) {
                        // Direct route structure: /api/notifications/recent
                        this.currentNotifications = data.notifications;
                        this.unreadCount = data.total_unread || 0;
                    } else if (data.data) {
                        // Controller route structure: /api/v1/notifications/recent
                        this.currentNotifications = data.data;
                        this.unreadCount = data.count || 0;
                    } else {
                        this.currentNotifications = [];
                        this.unreadCount = 0;
                    }
                    this.updateNotificationCounter();
                    this.renderNotifications();
                }
            }
        } catch (error) {
            console.error('NotificationManager: Failed to load notifications', error);
        }
    }

    /**
     * Handle new notification
     */
    handleNewNotification(notification) {
        // Add to current notifications
        this.currentNotifications.unshift(notification);

        // Update unread count
        if (!notification.is_read) {
            this.unreadCount++;
        }

        // Update UI
        this.updateNotificationCounter();
        this.addNotificationToList(notification);
        this.animateNotificationBell();

        // Limit notifications in memory
        if (this.currentNotifications.length > 50) {
            this.currentNotifications = this.currentNotifications.slice(0, 50);
        }
    }

    /**
     * Update notification counter
     */
    updateNotificationCounter() {
        if (this.notificationCounter) {
            if (this.unreadCount > 0) {
                this.notificationCounter.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                this.notificationCounter.style.display = 'flex';
                this.notificationCounter.classList.add('real-time');
            } else {
                this.notificationCounter.style.display = 'none';
                this.notificationCounter.classList.remove('real-time');
            }
        }

        // Update page title
        this.updatePageTitle();
    }

    /**
     * Update page title with notification count
     */
    updatePageTitle() {
        const originalTitle = document.title.replace(/^\(\d+\)\s*/, '');
        document.title = this.unreadCount > 0 ? `(${this.unreadCount}) ${originalTitle}` : originalTitle;
    }

    /**
     * Animate notification bell
     */
    animateNotificationBell() {
        if (this.notificationBell) {
            this.notificationBell.classList.add('has-notifications');
            setTimeout(() => {
                this.notificationBell.classList.remove('has-notifications');
            }, 500);
        }
    }

    /**
     * Toggle notification dropdown
     */
    toggleNotificationDropdown() {
        if (this.isDropdownVisible()) {
            this.hideNotificationDropdown();
        } else {
            this.showNotificationDropdown();
        }
    }

    /**
     * Show notification dropdown
     */
    showNotificationDropdown() {
        if (this.notificationDropdown) {
            this.notificationDropdown.classList.add('show');

            // Load fresh notifications
            this.loadNotifications();
        }
    }

    /**
     * Hide notification dropdown
     */
    hideNotificationDropdown() {
        if (this.notificationDropdown) {
            this.notificationDropdown.classList.remove('show');
        }
    }

    /**
     * Check if dropdown is visible
     */
    isDropdownVisible() {
        return this.notificationDropdown && this.notificationDropdown.classList.contains('show');
    }

    /**
     * Render notifications
     */
    renderNotifications() {
        if (!this.notificationList) return;

        if (this.currentNotifications.length === 0) {
            this.renderEmptyState();
            return;
        }

        this.notificationList.innerHTML = '';

        this.currentNotifications.forEach(notification => {
            const notificationElement = this.createNotificationElement(notification);
            this.notificationList.appendChild(notificationElement);
        });
    }

    /**
     * Render empty state
     */
    renderEmptyState() {
        if (this.notificationList) {
            this.notificationList.innerHTML = `
                <div class="notification-empty">
                    <div class="notification-empty-icon">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <div class="notification-empty-text">
                        Không có thông báo nào
                    </div>
                </div>
            `;
        }
    }

    /**
     * Create notification element
     */
    createNotificationElement(notification) {
        const element = document.createElement('div');
        element.className = `notification-item ${notification.is_read ? 'read' : 'unread'}`;
        element.setAttribute('data-notification-id', notification.id);

        const timeAgo = this.formatTimeAgo(new Date(notification.created_at));

        element.innerHTML = `
            <div class="notification-content">
                <div class="notification-header">
                    <h6 class="notification-title mb-1">${this.escapeHtml(notification.title)}</h6>
                    <small class="notification-time text-muted">${timeAgo}</small>
                </div>
                <p class="notification-message mb-2">${this.escapeHtml(notification.message)}</p>
                ${notification.data && notification.data.action_url ? `
                    <div class="notification-actions">
                        <a href="${notification.data.action_url}" class="btn btn-sm btn-outline-primary">
                            ${notification.data.action_text || 'Xem chi tiết'}
                        </a>
                    </div>
                ` : ''}
            </div>
            <div class="notification-controls">
                ${!notification.is_read ? `
                    <button type="button" class="btn-mark-read" title="Đánh dấu đã đọc">
                        <i class="fas fa-check"></i>
                    </button>
                ` : ''}
                <button type="button" class="btn-delete" title="Xóa">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        // Add event listeners
        this.setupNotificationElementEvents(element, notification);

        return element;
    }

    /**
     * Setup notification element events
     */
    setupNotificationElementEvents(element, notification) {
        const markReadBtn = element.querySelector('.btn-mark-read');
        const deleteBtn = element.querySelector('.btn-delete');

        if (markReadBtn) {
            markReadBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.markNotificationAsRead(notification.id);
            });
        }

        if (deleteBtn) {
            deleteBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.deleteNotification(notification.id);
            });
        }

        // Click to mark as read and navigate
        element.addEventListener('click', () => {
            if (!notification.is_read) {
                this.markNotificationAsRead(notification.id);
            }

            if (notification.data && notification.data.action_url) {
                window.location.href = notification.data.action_url;
            }
        });
    }

    /**
     * Add notification to list
     */
    addNotificationToList(notification) {
        if (!this.notificationList) return;

        // Remove empty state if exists
        const emptyState = this.notificationList.querySelector('.notification-empty');
        if (emptyState) {
            emptyState.remove();
        }

        const notificationElement = this.createNotificationElement(notification);
        notificationElement.classList.add('real-time-new');

        this.notificationList.insertBefore(notificationElement, this.notificationList.firstChild);

        // Remove animation class after animation completes
        setTimeout(() => {
            notificationElement.classList.remove('real-time-new');
        }, 500);

        // Limit DOM elements
        const items = this.notificationList.querySelectorAll('.notification-item');
        if (items.length > 20) {
            items[items.length - 1].remove();
        }
    }

    /**
     * Mark notification as read
     */
    async markNotificationAsRead(notificationId) {
        try {
            const response = await fetch(`/api/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    // Update local state
                    const notification = this.currentNotifications.find(n => n.id == notificationId);
                    if (notification && !notification.is_read) {
                        notification.is_read = true;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                        this.updateNotificationCounter();
                    }

                    // Update UI
                    const element = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (element) {
                        element.classList.remove('unread');
                        element.classList.add('read');
                        const markReadBtn = element.querySelector('.btn-mark-read');
                        if (markReadBtn) {
                            markReadBtn.remove();
                        }
                    }
                }
            }
        } catch (error) {
            console.error('NotificationManager: Failed to mark notification as read', error);
        }
    }

    /**
     * Delete notification
     */
    async deleteNotification(notificationId) {
        try {
            const response = await fetch(`/api/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    // Update local state
                    const notificationIndex = this.currentNotifications.findIndex(n => n.id == notificationId);
                    if (notificationIndex > -1) {
                        const notification = this.currentNotifications[notificationIndex];
                        if (!notification.is_read) {
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                            this.updateNotificationCounter();
                        }
                        this.currentNotifications.splice(notificationIndex, 1);
                    }

                    // Remove from UI
                    const element = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (element) {
                        element.classList.add('removing');
                        setTimeout(() => {
                            if (element.parentNode) {
                                element.parentNode.removeChild(element);
                            }

                            // Show empty state if no notifications left
                            if (this.currentNotifications.length === 0) {
                                this.renderEmptyState();
                            }
                        }, 300);
                    }
                }
            }
        } catch (error) {
            console.error('NotificationManager: Failed to delete notification', error);
        }
    }

    /**
     * Mark all notifications as read
     */
    async markAllAsRead() {
        try {
            const response = await fetch('/api/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    // Update local state
                    this.currentNotifications.forEach(notification => {
                        notification.is_read = true;
                    });
                    this.unreadCount = 0;
                    this.updateNotificationCounter();

                    // Update UI
                    const unreadItems = document.querySelectorAll('.notification-item.unread');
                    unreadItems.forEach(item => {
                        item.classList.remove('unread');
                        item.classList.add('read');
                        const markReadBtn = item.querySelector('.btn-mark-read');
                        if (markReadBtn) {
                            markReadBtn.remove();
                        }
                    });
                }
            }
        } catch (error) {
            console.error('NotificationManager: Failed to mark all as read', error);
        }
    }

    /**
     * Clear all notifications
     */
    async clearAllNotifications() {
        if (!confirm('Bạn có chắc chắn muốn xóa tất cả thông báo?')) {
            return;
        }

        try {
            const response = await fetch('/api/notifications/clear-all', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    // Update local state
                    this.currentNotifications = [];
                    this.unreadCount = 0;
                    this.updateNotificationCounter();

                    // Update UI
                    this.renderEmptyState();
                }
            }
        } catch (error) {
            console.error('NotificationManager: Failed to clear all notifications', error);
        }
    }

    /**
     * Request notification permission
     */
    requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                console.log('NotificationManager: Permission status:', permission);
            });
        }
    }

    /**
     * Escape HTML
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Format time ago
     */
    formatTimeAgo(date) {
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) {
            return 'vừa xong';
        } else if (diffInSeconds < 3600) {
            const minutes = Math.floor(diffInSeconds / 60);
            return `${minutes} phút trước`;
        } else if (diffInSeconds < 86400) {
            const hours = Math.floor(diffInSeconds / 3600);
            return `${hours} giờ trước`;
        } else {
            const days = Math.floor(diffInSeconds / 86400);
            return `${days} ngày trước`;
        }
    }

    /**
     * Get notification count
     */
    getNotificationCount() {
        return {
            total: this.currentNotifications.length,
            unread: this.unreadCount
        };
    }

    /**
     * Refresh notifications
     */
    refresh() {
        this.loadNotifications();
    }

    /**
     * Destroy notification manager
     */
    destroy() {
        // Remove event listeners and clean up
        this.isInitialized = false;

        if (this.connectionStatus && this.connectionStatus.parentNode) {
            this.connectionStatus.parentNode.removeChild(this.connectionStatus);
        }
    }
}

// Initialize global notification manager
document.addEventListener('DOMContentLoaded', () => {
    window.NotificationManager = new NotificationManager();
});
