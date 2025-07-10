/**
 * Real-time Notification Service
 * Handles WebSocket connections and real-time notification updates
 */
class NotificationService {
    constructor() {
        this.socket = null;
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 1000;
        this.userId = null;
        this.callbacks = {
            onNotification: [],
            onConnect: [],
            onDisconnect: [],
            onError: []
        };

        // Initialize if user is authenticated
        this.init();
    }

    /**
     * Initialize notification service
     */
    init() {
        // Get user ID from meta tag or global variable
        const userMeta = document.querySelector('meta[name="user-id"]');
        if (userMeta) {
            this.userId = userMeta.getAttribute('content');
            this.connect();
        }
    }

    /**
     * Connect to WebSocket server
     */
    connect() {
        if (!this.userId) {
            console.warn('NotificationService: No user ID found');
            return;
        }

        try {
            // Temporarily disable WebSocket and use HTTP polling only
            console.log('NotificationService: WebSocket disabled, using HTTP polling only');
            this.isConnected = true; // Mark as connected for HTTP polling
            this.triggerCallbacks('onConnect');

            // Start HTTP polling for notifications
            this.startPolling();

            // Use Laravel Echo or direct WebSocket connection (disabled for now)
            // if (window.Echo) {
            //     this.connectWithEcho();
            // } else {
            //     this.connectDirectly();
            // }
        } catch (error) {
            console.error('NotificationService: Connection failed', error);
            this.handleConnectionError(error);
        }
    }

    /**
     * Connect using Laravel Echo
     */
    connectWithEcho() {
        // Listen to user-specific notification channel
        window.Echo.private(`notifications.${this.userId}`)
            .listen('NotificationSent', (data) => {
                this.handleNotification(data);
            })
            .listen('NotificationRead', (data) => {
                this.handleNotificationRead(data);
            })
            .listen('NotificationDeleted', (data) => {
                this.handleNotificationDeleted(data);
            });

        // Listen to typing indicators
        window.Echo.channel('typing.thread.1') // Example channel
            .listen('typing.started', (data) => {
                this.handleTypingStarted(data);
            })
            .listen('typing.stopped', (data) => {
                this.handleTypingStopped(data);
            });

        this.isConnected = true;
        this.reconnectAttempts = 0;
        this.triggerCallbacks('onConnect');
        console.log('NotificationService: Connected via Echo');
    }

    /**
     * Connect directly to WebSocket
     */
    connectDirectly() {
        const wsUrl = `ws://localhost:6001/app/mechamap?protocol=7&client=js&version=4.3.1&flash=false`;
        this.socket = new WebSocket(wsUrl);

        this.socket.onopen = () => {
            this.isConnected = true;
            this.reconnectAttempts = 0;
            this.triggerCallbacks('onConnect');
            console.log('NotificationService: Connected directly');
        };

        this.socket.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                this.handleWebSocketMessage(data);
            } catch (error) {
                console.error('NotificationService: Invalid message format', error);
            }
        };

        this.socket.onclose = () => {
            this.isConnected = false;
            this.triggerCallbacks('onDisconnect');
            this.attemptReconnect();
        };

        this.socket.onerror = (error) => {
            console.error('NotificationService: WebSocket error', error);
            this.handleConnectionError(error);
        };
    }

    /**
     * Handle WebSocket message
     */
    handleWebSocketMessage(data) {
        switch (data.event) {
            case 'notification.sent':
                this.handleNotification(data.data);
                break;
            case 'notification.read':
                this.handleNotificationRead(data.data);
                break;
            case 'notification.deleted':
                this.handleNotificationDeleted(data.data);
                break;
            case 'typing.started':
                this.handleTypingStarted(data.data);
                break;
            case 'typing.stopped':
                this.handleTypingStopped(data.data);
                break;
            default:
                console.log('NotificationService: Unknown event', data.event);
        }
    }

    /**
     * Handle new notification
     */
    handleNotification(data) {
        console.log('NotificationService: New notification', data);

        // Update notification counter
        this.updateNotificationCounter(1);

        // Show notification popup
        this.showNotificationPopup(data.notification);

        // Add to notification list
        this.addToNotificationList(data.notification);

        // Play notification sound
        this.playNotificationSound();

        // Trigger callbacks
        this.triggerCallbacks('onNotification', data.notification);
    }

    /**
     * Handle notification read
     */
    handleNotificationRead(data) {
        console.log('NotificationService: Notification read', data);

        // Update notification counter
        this.updateNotificationCounter(-1);

        // Update UI
        this.markNotificationAsRead(data.notification_id);
    }

    /**
     * Handle notification deleted
     */
    handleNotificationDeleted(data) {
        console.log('NotificationService: Notification deleted', data);

        // Remove from UI
        this.removeNotificationFromList(data.notification_id);

        // Update counter if it was unread
        if (!data.was_read) {
            this.updateNotificationCounter(-1);
        }
    }

    /**
     * Handle typing started
     */
    handleTypingStarted(data) {
        console.log('NotificationService: Typing started', data);

        // Show typing indicator
        this.showTypingIndicator(data.indicator);
    }

    /**
     * Handle typing stopped
     */
    handleTypingStopped(data) {
        console.log('NotificationService: Typing stopped', data);

        // Hide typing indicator
        this.hideTypingIndicator(data.indicator);
    }

    /**
     * Update notification counter
     */
    updateNotificationCounter(change) {
        const counter = document.querySelector('.notification-counter');
        if (counter) {
            const currentCount = parseInt(counter.textContent) || 0;
            const newCount = Math.max(0, currentCount + change);

            counter.textContent = newCount;
            counter.style.display = newCount > 0 ? 'inline' : 'none';

            // Update page title
            this.updatePageTitle(newCount);
        }
    }

    /**
     * Update page title with notification count
     */
    updatePageTitle(count) {
        const originalTitle = document.title.replace(/^\(\d+\)\s*/, '');
        document.title = count > 0 ? `(${count}) ${originalTitle}` : originalTitle;
    }

    /**
     * Show notification popup
     */
    showNotificationPopup(notification) {
        // Check if browser notifications are supported and permitted
        if ('Notification' in window && Notification.permission === 'granted') {
            const browserNotification = new Notification(notification.title, {
                body: notification.message,
                icon: '/images/logo-small.png',
                tag: `notification-${notification.id}`,
                requireInteraction: false
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

        // Show in-app notification toast
        this.showInAppNotification(notification);
    }

    /**
     * Show in-app notification toast
     */
    showInAppNotification(notification) {
        const toast = document.createElement('div');
        toast.className = 'notification-toast';
        toast.innerHTML = `
            <div class="notification-toast-content">
                <div class="notification-toast-header">
                    <strong>${this.escapeHtml(notification.title)}</strong>
                    <button type="button" class="notification-toast-close" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="notification-toast-body">
                    ${this.escapeHtml(notification.message)}
                </div>
                ${notification.data && notification.data.action_url ? `
                    <div class="notification-toast-actions">
                        <a href="${notification.data.action_url}" class="btn btn-sm btn-primary">
                            ${notification.data.action_text || 'Xem chi tiết'}
                        </a>
                    </div>
                ` : ''}
            </div>
        `;

        // Add to container
        let container = document.querySelector('.notification-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'notification-toast-container';
            document.body.appendChild(container);
        }

        container.appendChild(toast);

        // Handle close button
        const closeBtn = toast.querySelector('.notification-toast-close');
        closeBtn.addEventListener('click', () => {
            this.removeToast(toast);
        });

        // Auto-remove after 8 seconds
        setTimeout(() => {
            this.removeToast(toast);
        }, 8000);

        // Animate in
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
    }

    /**
     * Remove toast notification
     */
    removeToast(toast) {
        toast.classList.remove('show');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    /**
     * Add notification to list
     */
    addToNotificationList(notification) {
        const notificationList = document.querySelector('.notification-list');
        if (notificationList) {
            const notificationItem = this.createNotificationItem(notification);
            notificationList.insertBefore(notificationItem, notificationList.firstChild);

            // Limit to 50 notifications in DOM
            const items = notificationList.querySelectorAll('.notification-item');
            if (items.length > 50) {
                items[items.length - 1].remove();
            }
        }
    }

    /**
     * Create notification item element
     */
    createNotificationItem(notification) {
        const item = document.createElement('div');
        item.className = 'notification-item';
        item.setAttribute('data-notification-id', notification.id);

        const timeAgo = this.formatTimeAgo(new Date(notification.created_at));

        item.innerHTML = `
            <div class="notification-content">
                <div class="notification-header">
                    <span class="notification-title">${this.escapeHtml(notification.title)}</span>
                    <span class="notification-time">${timeAgo}</span>
                </div>
                <div class="notification-message">
                    ${this.escapeHtml(notification.message)}
                </div>
                ${notification.data && notification.data.action_url ? `
                    <div class="notification-actions">
                        <a href="${notification.data.action_url}" class="notification-action-link">
                            ${notification.data.action_text || 'Xem chi tiết'}
                        </a>
                    </div>
                ` : ''}
            </div>
            <div class="notification-controls">
                <button type="button" class="btn-mark-read" title="Đánh dấu đã đọc">
                    <i class="fas fa-check"></i>
                </button>
                <button type="button" class="btn-delete" title="Xóa">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        // Add event listeners
        const markReadBtn = item.querySelector('.btn-mark-read');
        const deleteBtn = item.querySelector('.btn-delete');

        markReadBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.markNotificationAsRead(notification.id);
        });

        deleteBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.deleteNotification(notification.id);
        });

        return item;
    }

    /**
     * Mark notification as read
     */
    markNotificationAsRead(notificationId) {
        // API call to mark as read
        fetch(`/api/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (item) {
                    item.classList.add('read');
                    const markReadBtn = item.querySelector('.btn-mark-read');
                    if (markReadBtn) {
                        markReadBtn.style.display = 'none';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Failed to mark notification as read:', error);
        });
    }

    /**
     * Delete notification
     */
    deleteNotification(notificationId) {
        // API call to delete
        fetch(`/api/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.removeNotificationFromList(notificationId);
            }
        })
        .catch(error => {
            console.error('Failed to delete notification:', error);
        });
    }

    /**
     * Remove notification from list
     */
    removeNotificationFromList(notificationId) {
        const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
        if (item) {
            item.classList.add('removing');
            setTimeout(() => {
                if (item.parentNode) {
                    item.parentNode.removeChild(item);
                }
            }, 300);
        }
    }

    /**
     * Play notification sound
     */
    playNotificationSound() {
        // Check user preferences
        const soundEnabled = localStorage.getItem('notification-sound') !== 'false';
        if (soundEnabled) {
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.3;
            audio.play().catch(error => {
                console.log('Could not play notification sound:', error);
            });
        }
    }

    /**
     * Show typing indicator
     */
    showTypingIndicator(indicator) {
        const contextSelector = `[data-context-type="${indicator.context_type}"][data-context-id="${indicator.context_id}"]`;
        const contextElement = document.querySelector(contextSelector);

        if (contextElement) {
            let typingContainer = contextElement.querySelector('.typing-indicators');
            if (!typingContainer) {
                typingContainer = document.createElement('div');
                typingContainer.className = 'typing-indicators';
                contextElement.appendChild(typingContainer);
            }

            const typingIndicator = document.createElement('div');
            typingIndicator.className = 'typing-indicator';
            typingIndicator.setAttribute('data-user-id', indicator.user.id);
            typingIndicator.innerHTML = `
                <span class="typing-user">${this.escapeHtml(indicator.user.name)}</span>
                <span class="typing-text">đang gõ...</span>
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            `;

            typingContainer.appendChild(typingIndicator);
        }
    }

    /**
     * Hide typing indicator
     */
    hideTypingIndicator(indicator) {
        const typingIndicator = document.querySelector(`[data-user-id="${indicator.user.id}"]`);
        if (typingIndicator) {
            typingIndicator.classList.add('removing');
            setTimeout(() => {
                if (typingIndicator.parentNode) {
                    typingIndicator.parentNode.removeChild(typingIndicator);
                }
            }, 300);
        }
    }

    /**
     * Attempt to reconnect
     */
    attemptReconnect() {
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++;
            console.log(`NotificationService: Attempting to reconnect (${this.reconnectAttempts}/${this.maxReconnectAttempts})`);

            setTimeout(() => {
                this.connect();
            }, this.reconnectDelay * this.reconnectAttempts);
        } else {
            console.error('NotificationService: Max reconnection attempts reached');
            this.triggerCallbacks('onError', new Error('Max reconnection attempts reached'));
        }
    }

    /**
     * Handle connection error
     */
    handleConnectionError(error) {
        this.isConnected = false;
        this.triggerCallbacks('onError', error);
    }

    /**
     * Add event listener
     */
    on(event, callback) {
        if (this.callbacks[event]) {
            this.callbacks[event].push(callback);
        }
    }

    /**
     * Remove event listener
     */
    off(event, callback) {
        if (this.callbacks[event]) {
            const index = this.callbacks[event].indexOf(callback);
            if (index > -1) {
                this.callbacks[event].splice(index, 1);
            }
        }
    }

    /**
     * Trigger callbacks
     */
    triggerCallbacks(event, data = null) {
        if (this.callbacks[event]) {
            this.callbacks[event].forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error(`NotificationService: Callback error for ${event}:`, error);
                }
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
     * Request notification permission
     */
    requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                console.log('Notification permission:', permission);
            });
        }
    }

    /**
     * Start HTTP polling for notifications
     */
    startPolling() {
        // Poll every 30 seconds for new notifications
        this.pollingInterval = setInterval(() => {
            this.pollNotifications();
        }, 30000);

        // Initial poll
        this.pollNotifications();
    }

    /**
     * Poll notifications via HTTP
     */
    async pollNotifications() {
        try {
            const response = await fetch('/api/notifications/recent', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    this.handlePolledNotifications(data.data);
                }
            }
        } catch (error) {
            console.warn('NotificationService: Polling failed', error);
        }
    }

    /**
     * Handle polled notifications
     */
    handlePolledNotifications(notifications) {
        // Update notification count and trigger callbacks
        if (notifications.length > 0) {
            this.triggerCallbacks('onNotification', notifications);
        }
    }

    /**
     * Stop polling
     */
    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    }

    /**
     * Disconnect
     */
    disconnect() {
        if (this.socket) {
            this.socket.close();
        }
        this.stopPolling();
        this.isConnected = false;
    }

    /**
     * Get connection status
     */
    getConnectionStatus() {
        return {
            isConnected: this.isConnected,
            reconnectAttempts: this.reconnectAttempts,
            userId: this.userId
        };
    }
}

// Initialize global notification service
window.NotificationService = new NotificationService();
