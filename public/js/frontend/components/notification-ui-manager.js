/**
 * NotificationUIManager - Unified Notification UI Controller
 * Quản lý tất cả thao tác UI liên quan đến notifications
 * Thay thế các hàm trùng lập trong header.blade.php, notification-dropdown.blade.php, notification-manager.js
 */
class NotificationUIManager {
    constructor() {
        this.isInitialized = false;
        this.elements = {};
        this.state = {
            notifications: [],
            unreadCount: 0,
            isLoading: false,
            isEmpty: true
        };

        // Event listeners registry
        this.eventListeners = new Map();

        // Initialize
        this.init();
    }

    /**
     * Initialize the UI Manager
     */
    init() {
        if (this.isInitialized) return;

        this.findElements();
        this.setupEventListeners();
        this.setupCustomEvents();
        this.isInitialized = true;

        // Register with event system
        if (window.NotificationEventSystem) {
            window.NotificationEventSystem.registerComponent('NotificationUIManager', this);
        }

        // Dispatch ready event
        this.dispatchEvent('notificationUIReady', { manager: this });

        console.log('NotificationUIManager initialized successfully');
    }

    /**
     * Find and cache DOM elements
     */
    findElements() {
        this.elements = {
            bell: document.querySelector('#notificationBell, .notification-bell'),
            counter: document.querySelector('.notification-counter'),
            dropdown: document.querySelector('#notificationDropdown'),
            list: document.querySelector('#notificationItems'),
            empty: document.querySelector('#notificationEmpty'),
            loading: document.querySelector('.notification-loading'),
            skeleton: document.querySelector('#notificationSkeleton'),
            markAllRead: document.querySelector('#markAllRead'),
            clearAll: document.querySelector('#clearAll')
        };

        // Validate critical elements
        if (!this.elements.list || !this.elements.empty) {
            console.error('NotificationUIManager: Critical elements not found');
            return false;
        }

        return true;
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Bell click handler
        if (this.elements.bell) {
            this.elements.bell.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleDropdown();
            });
        }

        // Mark all as read
        if (this.elements.markAllRead) {
            this.elements.markAllRead.addEventListener('click', () => {
                this.dispatchEvent('markAllAsRead');
            });
        }

        // Clear all notifications
        if (this.elements.clearAll) {
            this.elements.clearAll.addEventListener('click', () => {
                this.dispatchEvent('clearAllNotifications');
            });
        }

        // Click outside to close dropdown
        document.addEventListener('click', (e) => {
            if (!this.elements.dropdown?.contains(e.target) &&
                !this.elements.bell?.contains(e.target)) {
                this.hideDropdown();
            }
        });
    }

    /**
     * Setup custom events for component communication
     */
    setupCustomEvents() {
        // Listen for notification updates from other components
        document.addEventListener('notificationUpdate', (e) => {
            this.handleNotificationUpdate(e.detail);
        });

        document.addEventListener('notificationCountUpdate', (e) => {
            this.updateCounter(e.detail.count);
        });

        document.addEventListener('notificationDeleted', (e) => {
            this.handleNotificationDeleted(e.detail.id);
        });
    }

    /**
     * UNIFIED EMPTY STATE MANAGEMENT
     */
    showEmpty() {
        if (!this.elements.empty) return;

        // Use consistent method: remove d-none class
        this.elements.empty.classList.remove('d-none');
        this.elements.empty.style.display = 'block';

        // Clear notification list
        if (this.elements.list) {
            this.elements.list.innerHTML = '';
        }

        this.state.isEmpty = true;
        this.dispatchEvent('emptyStateShown');
    }

    hideEmpty() {
        if (!this.elements.empty) return;

        // Use consistent method: add d-none class
        this.elements.empty.classList.add('d-none');
        this.elements.empty.style.display = 'none';

        this.state.isEmpty = false;
        this.dispatchEvent('emptyStateHidden');
    }

    /**
     * UNIFIED LOADING STATE MANAGEMENT
     */
    showLoading() {
        if (this.elements.loading) {
            this.elements.loading.classList.remove('d-none');
        }
        if (this.elements.skeleton) {
            this.elements.skeleton.classList.remove('d-none');
        }

        this.hideEmpty();
        this.state.isLoading = true;
    }

    hideLoading() {
        if (this.elements.loading) {
            this.elements.loading.classList.add('d-none');
        }
        if (this.elements.skeleton) {
            this.elements.skeleton.classList.add('d-none');
        }

        this.state.isLoading = false;
    }

    /**
     * UNIFIED COUNTER MANAGEMENT
     */
    updateCounter(count) {
        if (!this.elements.counter) return;

        this.state.unreadCount = count;

        if (count > 0) {
            this.elements.counter.textContent = count > 99 ? '99+' : count;
            this.elements.counter.style.display = 'inline-block';

            // Update bell appearance
            if (this.elements.bell) {
                const icon = this.elements.bell.querySelector('i');
                if (icon) {
                    icon.classList.add('text-primary');
                    icon.classList.remove('text-muted');
                }
            }
        } else {
            this.elements.counter.style.display = 'none';

            // Update bell appearance
            if (this.elements.bell) {
                const icon = this.elements.bell.querySelector('i');
                if (icon) {
                    icon.classList.remove('text-primary');
                    icon.classList.add('text-muted');
                }
            }
        }

        this.dispatchEvent('counterUpdated', { count });
    }

    /**
     * UNIFIED NOTIFICATION RENDERING
     */
    renderNotifications(notifications) {
        if (!this.elements.list) return;

        this.hideLoading();
        this.state.notifications = notifications;

        if (!notifications || notifications.length === 0) {
            this.showEmpty();
            return;
        }

        this.hideEmpty();

        // Render notifications
        const html = notifications.map(notification => this.renderNotification(notification)).join('');
        this.elements.list.innerHTML = html;

        // Bind events for individual notifications
        this.bindNotificationEvents();

        this.dispatchEvent('notificationsRendered', { notifications });
    }

    /**
     * Render single notification
     */
    renderNotification(notification) {
        const isUnread = !notification.is_read;
        const actionUrl = notification.action_url || '#';

        return `
            <div class="notification-item p-3 border-bottom ${isUnread ? 'notification-unread' : ''}"
                 data-notification-id="${notification.id}"
                 data-action-url="${actionUrl}">
                <div class="d-flex">
                    <div class="avatar-sm me-3">
                        <span class="avatar-title bg-${notification.color || 'primary'} rounded-circle">
                            <i class="fas fa-${notification.icon || 'bell'}"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 font-size-14">${notification.title}</h6>
                        <p class="mb-1 text-muted font-size-13">${notification.message}</p>
                        <small class="text-muted">${notification.time_ago}</small>
                    </div>
                    ${isUnread ? '<div class="ms-2"><span class="badge bg-primary rounded-pill">Mới</span></div>' : ''}
                    <div class="notification-actions ms-2">
                        <button class="btn btn-sm btn-outline-danger delete-notification"
                                data-id="${notification.id}" title="Xóa">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Bind events for notification items
     */
    bindNotificationEvents() {
        if (!this.elements.list) return;

        // Click to view notification
        this.elements.list.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', (e) => {
                if (e.target.closest('.delete-notification')) return;

                const id = item.dataset.notificationId;
                const actionUrl = item.dataset.actionUrl;

                this.dispatchEvent('notificationClicked', { id, actionUrl });

                if (actionUrl && actionUrl !== '#') {
                    window.location.href = actionUrl;
                }
            });
        });

        // Delete notification
        this.elements.list.querySelectorAll('.delete-notification').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = btn.dataset.id;
                this.dispatchEvent('deleteNotification', { id });
            });
        });
    }

    /**
     * DROPDOWN MANAGEMENT
     */
    toggleDropdown() {
        if (this.isDropdownVisible()) {
            this.hideDropdown();
        } else {
            this.showDropdown();
        }
    }

    showDropdown() {
        if (!this.elements.dropdown) return;

        this.elements.dropdown.style.display = 'block';
        this.elements.bell?.setAttribute('aria-expanded', 'true');

        // Load notifications if needed
        this.dispatchEvent('dropdownOpened');
    }

    hideDropdown() {
        if (!this.elements.dropdown) return;

        this.elements.dropdown.style.display = 'none';
        this.elements.bell?.setAttribute('aria-expanded', 'false');

        this.dispatchEvent('dropdownClosed');
    }

    isDropdownVisible() {
        return this.elements.dropdown?.style.display === 'block';
    }

    /**
     * EVENT HANDLING
     */
    handleNotificationUpdate(data) {
        if (data.notifications) {
            this.renderNotifications(data.notifications);
        }
        if (data.unreadCount !== undefined) {
            this.updateCounter(data.unreadCount);
        }
    }

    handleNotificationDeleted(id) {
        // Remove from UI
        const item = this.elements.list?.querySelector(`[data-notification-id="${id}"]`);
        if (item) {
            item.remove();
        }

        // Update state
        this.state.notifications = this.state.notifications.filter(n => n.id != id);

        // Check if empty
        if (this.state.notifications.length === 0) {
            this.showEmpty();
        }
    }

    /**
     * UTILITY METHODS
     */
    dispatchEvent(eventName, detail = {}) {
        const event = new CustomEvent(`notificationUI:${eventName}`, {
            detail: { ...detail, manager: this }
        });
        document.dispatchEvent(event);
    }

    /**
     * Public API for external components
     */
    getState() {
        return { ...this.state };
    }

    getElements() {
        return { ...this.elements };
    }

    /**
     * Error handling
     */
    showError(message) {
        if (!this.elements.list) return;

        this.hideLoading();
        this.hideEmpty();

        this.elements.list.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-triangle text-warning fs-1 mb-3"></i>
                <p class="text-muted mb-0">${message}</p>
            </div>
        `;
    }

    /**
     * Handle system events from NotificationEventSystem
     */
    handleSystemEvent(eventName, data) {
        switch (eventName) {
            case 'newNotificationReceived':
                // Handle new notification
                if (data.notification) {
                    this.dispatchEvent('notificationUpdate', {
                        notifications: [...this.state.notifications, data.notification],
                        unreadCount: this.state.unreadCount + 1
                    });
                }
                break;

            case 'refreshRequired':
                // Trigger refresh
                this.dispatchEvent('refreshRequested', { reason: data.reason });
                break;

            case 'stateSynchronization':
                // Update state from system
                if (data.state && data.state.ui) {
                    this.updateState(data.state.ui);
                }
                break;

            case 'emptyStateChanged':
                // Handle empty state changes
                this.updateEmptyState(data.isEmpty);
                break;

            case 'dropdownStateChanged':
                // Handle dropdown state changes
                this.updateDropdownState(data.isOpen);
                break;

            case 'notificationsUpdated':
                // Handle notifications list updates
                this.updateNotificationsList(data.notifications, data.count);
                break;

            case 'unreadCountChanged':
                // Handle unread count updates
                this.updateUnreadCount(data.count);
                break;

            case 'preloadedDataLoaded':
                // Handle preloaded data in one consolidated event
                this.updateNotificationsList(data.notifications, data.count);
                this.updateUnreadCount(data.unreadCount);
                this.updateEmptyState(data.isEmpty);
                break;

            default:
                // Only log truly unhandled events to reduce noise
                if (!['componentRegistered', 'uiManagerReady'].includes(eventName)) {
                    console.debug(`NotificationUIManager: Unhandled system event '${eventName}'`, data);
                }
        }
    }

    /**
     * Update internal state
     */
    updateState(newState) {
        this.state = { ...this.state, ...newState };
        console.log('NotificationUIManager: State updated', this.state);
    }

    /**
     * Update empty state
     */
    updateEmptyState(isEmpty) {
        this.state.isEmpty = isEmpty;
        // Update UI to reflect empty state
        if (this.elements.list) {
            this.elements.list.classList.toggle('empty', isEmpty);
        }
    }

    /**
     * Update dropdown state
     */
    updateDropdownState(isOpen) {
        this.state.isDropdownOpen = isOpen;
        // Update UI to reflect dropdown state
        if (this.elements.dropdown) {
            this.elements.dropdown.classList.toggle('show', isOpen);
        }
        if (this.elements.bell) {
            this.elements.bell.setAttribute('aria-expanded', isOpen.toString());
        }
    }

    /**
     * Update notifications list
     */
    updateNotificationsList(notifications, count) {
        // Only update if data has actually changed
        if (JSON.stringify(this.state.notifications) !== JSON.stringify(notifications)) {
            this.state.notifications = notifications || [];
            this.state.notificationCount = count || 0;
            this.render();
        }
    }

    /**
     * Update unread count
     */
    updateUnreadCount(count) {
        // Only update if count has actually changed
        if (this.state.unreadCount !== count) {
            this.state.unreadCount = count;
            this.updateCounter(count);
        }
    }
}

// Create global instance
window.NotificationUIManager = window.NotificationUIManager || new NotificationUIManager();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NotificationUIManager;
}

console.log('NotificationUIManager loaded successfully');
