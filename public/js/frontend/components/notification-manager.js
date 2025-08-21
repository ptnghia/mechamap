/**
 * Enhanced Real-time Notification Manager
 * Manages notification UI components, real-time updates, sound alerts, and toast notifications
 * Integrates with NotificationUIManager for unified UI control
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

        // Reference to NotificationUIManager
        this.uiManager = null;

        // Enhanced features
        this.soundEnabled = this.getSoundPreference();
        this.toastContainer = null;
        this.audioContext = null;
        this.notificationSounds = {};

        // Notification categories with icons and colors
        this.notificationTypes = {
            'system': { icon: 'fas fa-cog', color: 'primary', sound: 'system' },
            'message': { icon: 'fas fa-envelope', color: 'info', sound: 'message' },
            'follow': { icon: 'fas fa-user-plus', color: 'success', sound: 'follow' },
            'like': { icon: 'fas fa-heart', color: 'danger', sound: 'like' },
            'showcase': { icon: 'fas fa-star', color: 'warning', sound: 'showcase' },
            'order': { icon: 'fas fa-shopping-cart', color: 'info', sound: 'order' },
            'warning': { icon: 'fas fa-exclamation-triangle', color: 'warning', sound: 'warning' },
            'success': { icon: 'fas fa-check-circle', color: 'success', sound: 'success' },
            'thread_created': { icon: 'fas fa-plus-circle', color: 'success', sound: 'thread' },
            'thread_replied': { icon: 'fas fa-reply', color: 'info', sound: 'reply' },
            'comment_mention': { icon: 'fas fa-at', color: 'warning', sound: 'mention' },
            'business_verified': { icon: 'fas fa-check-circle', color: 'success', sound: 'success' },
            'business_rejected': { icon: 'fas fa-times-circle', color: 'danger', sound: 'warning' },
            'product_approved': { icon: 'fas fa-box', color: 'success', sound: 'success' },
            'product_rejected': { icon: 'fas fa-box', color: 'danger', sound: 'warning' },
            'order_update': { icon: 'fas fa-shopping-cart', color: 'info', sound: 'order' },
            'role_changed': { icon: 'fas fa-user-cog', color: 'warning', sound: 'system' },
            'commission_paid': { icon: 'fas fa-dollar-sign', color: 'success', sound: 'success' },
            'system_announcement': { icon: 'fas fa-bullhorn', color: 'primary', sound: 'system' },
            'quote_request': { icon: 'fas fa-file-invoice', color: 'info', sound: 'message' },
            'user_registered': { icon: 'fas fa-user-plus', color: 'success', sound: 'follow' },
            'forum_activity': { icon: 'fas fa-comments', color: 'info', sound: 'thread' },
            'marketplace_activity': { icon: 'fas fa-store', color: 'info', sound: 'order' },
            'login_from_new_device': { icon: 'fas fa-shield-alt', color: 'warning', sound: 'warning' },
            'password_changed': { icon: 'fas fa-key', color: 'danger', sound: 'warning' },
            'default': { icon: 'fas fa-bell', color: 'secondary', sound: 'default' }
        };

        this.init();
    }

    /**
     * Initialize notification manager
     */
    init() {
        if (this.isInitialized) return;

        console.log('NotificationManager: Initializing with NotificationUIManager integration...');

        // Wait for NotificationUIManager to be ready
        this.waitForUIManager().then(() => {
            // Find DOM elements
            this.findElements();

            // Setup event listeners
            this.setupEventListeners();

            // Initialize connection status
            this.initConnectionStatus();

            // Initialize enhanced features
            this.initToastContainer();
            this.initAudioSystem();

            // Load initial notifications
            this.loadNotifications();

            // Setup real-time service callbacks
            this.setupRealTimeCallbacks();

            // Request notification permission
            this.requestNotificationPermission();

            this.isInitialized = true;
            console.log('NotificationManager: Enhanced Initialized with NotificationUIManager integration');
        });
    }

    /**
     * Wait for NotificationUIManager to be ready
     */
    async waitForUIManager() {
        return new Promise((resolve) => {
            if (window.NotificationUIManager && window.NotificationUIManager.isInitialized) {
                this.uiManager = window.NotificationUIManager;
                resolve();
            } else {
                document.addEventListener('notificationUI:notificationUIReady', (e) => {
                    this.uiManager = e.detail.manager;
                    resolve();
                });
            }
        });
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
     * Create connection status indicator (DISABLED - cosmetic issue only)
     * Connection status is handled internally, no need for visual indicator
     */
    createConnectionStatus() {
        // Connection status indicator disabled to prevent cosmetic issues
        // WebSocket connection works perfectly without visual indicator
        this.connectionStatus = null;
    }

    /**
     * Initialize toast container for popup notifications
     */
    initToastContainer() {
        // Create toast container if it doesn't exist
        this.toastContainer = document.querySelector('#toast-container');
        if (!this.toastContainer) {
            this.toastContainer = document.createElement('div');
            this.toastContainer.id = 'toast-container';
            this.toastContainer.className = 'toast-container';
            document.body.appendChild(this.toastContainer);
        }
    }

    /**
     * Initialize audio system for notification sounds
     */
    initAudioSystem() {
        try {
            // Initialize NotificationSounds class for generated sounds
            if (window.NotificationSounds) {
                this.notificationSounds = new window.NotificationSounds();
            } else {
                console.warn('NotificationManager: NotificationSounds class not available');
            }
        } catch (error) {
            console.warn('NotificationManager: Audio system not available', error);
        }
    }

    /**
     * Get sound preference from localStorage
     */
    getSoundPreference() {
        return localStorage.getItem('notification-sound-enabled') !== 'false';
    }

    /**
     * Set sound preference
     */
    setSoundPreference(enabled) {
        this.soundEnabled = enabled;
        localStorage.setItem('notification-sound-enabled', enabled.toString());
    }

    /**
     * Toggle sound preference
     */
    toggleSound() {
        this.setSoundPreference(!this.soundEnabled);
        return this.soundEnabled;
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
     * Update connection status (DISABLED - cosmetic issue only)
     * Connection status is handled internally, no need for visual indicator
     */
    updateConnectionStatus(status, message) {
        // Connection status indicator disabled to prevent cosmetic issues
        // WebSocket connection works perfectly without visual indicator
        console.log(`NotificationManager: Connection status ${status} - ${message}`);
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

                    // Update timestamp
                    this.lastNotificationLoad = Date.now();

                    this.updateNotificationCounter();
                    this.renderNotifications();
                }
            }
        } catch (error) {
            console.error('NotificationManager: Failed to load notifications', error);
        }
    }

    /**
     * Handle new notification with enhanced features
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

        // Enhanced features for new notifications
        this.playNotificationSound(notification.type);
        this.showToastNotification(notification);
        this.showBrowserNotification(notification);

        // Bridge to NotificationDropdown component
        this.bridgeToNotificationDropdown(notification);

        // Limit notifications in memory
        if (this.currentNotifications.length > 50) {
            this.currentNotifications = this.currentNotifications.slice(0, 50);
            // Re-render to sync DOM with currentNotifications
            this.renderNotifications();
        }
    }

    /**
     * Bridge notification to NotificationDropdown component
     */
    bridgeToNotificationDropdown(notification) {
        console.log('🌉 NotificationManager: Bridging notification to dropdown component:', notification);

        // Dispatch custom event for NotificationDropdown to listen
        document.dispatchEvent(new CustomEvent('notificationManager:newNotification', {
            detail: notification
        }));

        // Also dispatch generic notification-received event
        document.dispatchEvent(new CustomEvent('notification-received', {
            detail: notification
        }));

        console.log('✅ NotificationManager: Notification bridged to dropdown component');
    }

    /**
     * Play notification sound based on type
     */
    playNotificationSound(notificationType) {
        if (!this.soundEnabled || !this.notificationSounds) return;

        try {
            const typeConfig = this.notificationTypes[notificationType] || this.notificationTypes['default'];
            const soundType = typeConfig.sound;

            // Use generated sounds from NotificationSounds class
            this.notificationSounds.playSound(soundType);
        } catch (error) {
            console.warn('NotificationManager: Sound playback failed', error);
        }
    }

    /**
     * Show toast notification popup
     */
    showToastNotification(notification) {
        if (!this.toastContainer) return;

        const typeConfig = this.notificationTypes[notification.type] || this.notificationTypes['default'];

        const toast = document.createElement('div');
        toast.className = `toast toast-${typeConfig.color} show`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="toast-header">
                <i class="${typeConfig.icon} toast-icon"></i>
                <strong class="me-auto">${this.escapeHtml(notification.title)}</strong>
                <small class="text-muted">vừa xong</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${this.escapeHtml(notification.message)}
                ${notification.data && notification.data.action_url ? `
                    <div class="mt-2">
                        <a href="${notification.data.action_url}" class="btn btn-sm btn-outline-${typeConfig.color}">
                            ${notification.data.action_text || 'Xem chi tiết'}
                        </a>
                    </div>
                ` : ''}
            </div>
        `;

        // Add close button functionality
        const closeBtn = toast.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            });
        }

        // Add to container
        this.toastContainer.appendChild(toast);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }
        }, 5000);

        // Limit number of toasts
        const toasts = this.toastContainer.querySelectorAll('.toast');
        if (toasts.length > 3) {
            const oldestToast = toasts[0];
            oldestToast.classList.remove('show');
            setTimeout(() => {
                if (oldestToast.parentNode) {
                    oldestToast.parentNode.removeChild(oldestToast);
                }
            }, 300);
        }
    }

    /**
     * Show browser notification
     */
    showBrowserNotification(notification) {
        if ('Notification' in window && Notification.permission === 'granted') {
            const typeConfig = this.notificationTypes[notification.type] || this.notificationTypes['default'];

            const browserNotification = new Notification(notification.title, {
                body: notification.message,
                icon: '/images/logo-notification.png',
                badge: '/images/logo-badge.png',
                tag: `mechamap-${notification.id}`,
                requireInteraction: false,
                silent: !this.soundEnabled
            });

            // Auto-close after 5 seconds
            setTimeout(() => {
                browserNotification.close();
            }, 5000);

            // Handle click
            browserNotification.onclick = () => {
                window.focus();
                if (notification.data && notification.data.action_url) {
                    window.location.href = notification.data.action_url;
                }
                browserNotification.close();
            };
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

            // Only load fresh notifications if we don't have any or it's been a while
            if (!this.currentNotifications || this.currentNotifications.length === 0 || this.shouldRefreshNotifications()) {
                this.loadNotifications();
            } else {
                // Use existing notifications and render them
                this.renderNotifications();
            }

            // Add sound toggle button
            this.addSoundToggleButton();
        }
    }

    /**
     * Check if notifications should be refreshed from server
     */
    shouldRefreshNotifications() {
        // Refresh if last load was more than 5 minutes ago
        if (!this.lastNotificationLoad) {
            return true;
        }
        const fiveMinutesAgo = Date.now() - (5 * 60 * 1000);
        return this.lastNotificationLoad < fiveMinutesAgo;
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
     * Render notifications - Use NotificationUIManager
     */
    renderNotifications() {
        if (this.uiManager) {
            // Use unified UI manager for rendering
            this.uiManager.renderNotifications(this.currentNotifications);
        } else {
            console.warn('NotificationManager: UIManager not available for rendering');
        }
    }

    /**
     * Render empty state - Use NotificationUIManager
     * @deprecated Use NotificationUIManager.showEmpty() instead
     */
    renderEmptyState() {
        if (this.uiManager) {
            this.uiManager.showEmpty();
        } else {
            console.warn('NotificationManager: UIManager not available for empty state');
        }
    }

    /**
     * Create notification element with enhanced categorization
     */
    createNotificationElement(notification) {
        const element = document.createElement('div');
        const typeConfig = this.notificationTypes[notification.type] || this.notificationTypes['default'];

        element.className = `notification-item notification-type-${notification.type} ${notification.is_read ? 'read' : 'unread'}`;
        element.setAttribute('data-notification-id', notification.id);
        element.setAttribute('data-notification-type', notification.type);

        const timeAgo = this.formatTimeAgo(new Date(notification.created_at));

        element.innerHTML = `
            <div class="notification-icon">
                <div class="notification-icon-wrapper bg-${typeConfig.color}">
                    <i class="${typeConfig.icon}"></i>
                </div>
                ${!notification.is_read ? '<div class="notification-unread-dot"></div>' : ''}
            </div>
            <div class="notification-content">
                <div class="notification-header">
                    <div class="notification-title-wrapper">
                        <h6 class="notification-title mb-1">${this.escapeHtml(notification.title)}</h6>
                        <span class="notification-category badge bg-${typeConfig.color} bg-opacity-10 text-${typeConfig.color}">
                            ${this.getNotificationCategoryName(notification.type)}
                        </span>
                    </div>
                    <small class="notification-time text-muted">${timeAgo}</small>
                </div>
                <p class="notification-message mb-2">${this.escapeHtml(notification.message)}</p>
                ${notification.data && notification.data.action_url ? `
                    <div class="notification-actions">
                        <a href="${notification.data.action_url}" class="btn btn-sm btn-outline-${typeConfig.color}">
                            <i class="fas fa-external-link-alt me-1"></i>
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
     * Get notification category display name
     */
    getNotificationCategoryName(type) {
        const categoryNames = {
            'system': 'Hệ thống',
            'message': 'Tin nhắn',
            'follow': 'Theo dõi',
            'like': 'Thích',
            'showcase': 'Showcase',
            'order': 'Đơn hàng',
            'warning': 'Cảnh báo',
            'success': 'Thành công',
            'thread_created': 'Chủ đề mới',
            'thread_replied': 'Phản hồi',
            'comment_mention': 'Nhắc đến',
            'business_verified': 'Doanh nghiệp',
            'business_rejected': 'Doanh nghiệp',
            'product_approved': 'Sản phẩm',
            'product_rejected': 'Sản phẩm',
            'order_update': 'Đơn hàng',
            'role_changed': 'Vai trò',
            'commission_paid': 'Hoa hồng',
            'system_announcement': 'Thông báo',
            'quote_request': 'Báo giá',
            'user_registered': 'Người dùng',
            'forum_activity': 'Diễn đàn',
            'marketplace_activity': 'Marketplace',
            'login_from_new_device': 'Bảo mật',
            'password_changed': 'Bảo mật',
            'default': 'Khác'
        };

        return categoryNames[type] || categoryNames['default'];
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
                                if (this.uiManager) {
                                    this.uiManager.showEmpty();
                                }
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
                    if (this.uiManager) {
                        this.uiManager.showEmpty();
                    }
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
     * Add sound toggle button to notification dropdown
     */
    addSoundToggleButton() {
        const dropdown = document.querySelector('.notification-dropdown');
        if (!dropdown) return;

        const footer = dropdown.querySelector('.notification-footer');
        if (!footer) return;

        // Check if sound toggle already exists
        if (footer.querySelector('.sound-toggle-btn')) return;

        const soundToggle = document.createElement('button');
        soundToggle.className = 'sound-toggle-btn btn btn-sm btn-outline-secondary me-2';
        soundToggle.innerHTML = `
            <i class="fas fa-volume-${this.soundEnabled ? 'up' : 'mute'}"></i>
            <span class="ms-1">${this.soundEnabled ? 'Tắt âm' : 'Bật âm'}</span>
        `;
        soundToggle.title = this.soundEnabled ? 'Tắt âm thanh thông báo' : 'Bật âm thanh thông báo';

        soundToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const newState = this.toggleSound();
            soundToggle.innerHTML = `
                <i class="fas fa-volume-${newState ? 'up' : 'mute'}"></i>
                <span class="ms-1">${newState ? 'Tắt âm' : 'Bật âm'}</span>
            `;
            soundToggle.title = newState ? 'Tắt âm thanh thông báo' : 'Bật âm thanh thông báo';

            // Show feedback toast
            this.showSoundToggleFeedback(newState);
        });

        // Insert before the "Xem tất cả" link
        const viewAllLink = footer.querySelector('a');
        if (viewAllLink) {
            footer.insertBefore(soundToggle, viewAllLink);
        } else {
            footer.appendChild(soundToggle);
        }
    }

    /**
     * Show sound toggle feedback
     */
    showSoundToggleFeedback(enabled) {
        if (!this.toastContainer) return;

        const toast = document.createElement('div');
        toast.className = `toast toast-${enabled ? 'success' : 'secondary'} show`;
        toast.innerHTML = `
            <div class="toast-header">
                <i class="fas fa-volume-${enabled ? 'up' : 'mute'} toast-icon"></i>
                <strong class="me-auto">Âm thanh thông báo</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                Âm thanh thông báo đã được ${enabled ? 'bật' : 'tắt'}
            </div>
        `;

        this.toastContainer.appendChild(toast);

        // Auto-hide after 2 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }
        }, 2000);
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
