/**
 * MechaMap Real-time Notification Service
 * Socket.IO-based WebSocket connection to Node.js server
 * Replaces Laravel Reverb/Pusher.js implementation
 */
class NotificationService {
    constructor() {
        this.socket = null;
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 1000;
        this.userId = null;
        this.userToken = null;
        this.callbacks = {
            onNotification: [],
            onConnect: [],
            onDisconnect: [],
            onError: [],
            onTyping: [],
            onUserActivity: []
        };

        // Configuration
        this.config = {
            serverUrl: this.getServerUrl(),
            transports: ['websocket', 'polling'],
            timeout: 10000,
            forceNew: false,
            autoConnect: false
        };

        console.log('NotificationService: Initializing with Node.js WebSocket server');
        this.init();
    }

    /**
     * Get WebSocket server URL based on environment
     */
    getServerUrl() {
        const hostname = window.location.hostname;
        const protocol = window.location.protocol === 'https:' ? 'https' : 'http';

        // Production: realtime.mechamap.com
        // All environments now use production server
        if (hostname === 'mechamap.com' || hostname === 'www.mechamap.com') {
            return 'https://realtime.mechamap.com';
        } else if (hostname === 'mechamap.test' || hostname.includes('mechamap')) {
            return 'https://realtime.mechamap.com';
        } else {
            return 'https://realtime.mechamap.com';
        }
    }

    /**
     * Initialize notification service
     */
    init() {
        // Get user authentication data
        this.getUserData();

        if (this.userId && this.userToken) {
            console.log(`NotificationService: Initializing for user ${this.userId}`);
            this.connect();
        } else {
            console.log('NotificationService: No authenticated user found');
        }
    }

    /**
     * Get user data from meta tags and localStorage
     */
    getUserData() {
        // Get user ID from meta tag
        const userMeta = document.querySelector('meta[name="user-id"]');
        if (userMeta) {
            this.userId = userMeta.getAttribute('content');
        }

        // Get Sanctum token from localStorage first, then try API
        this.userToken = localStorage.getItem('sanctum_token');
        if (!this.userToken) {
            // If no cached token, we'll get it async when needed
            this.getAuthToken().then(token => {
                if (token) {
                    this.userToken = token;
                }
            });
        }
    }

    /**
     * Get authentication token from Laravel (Sanctum token)
     */
    async getAuthToken() {
        try {
            const response = await fetch('/api/user/token', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                credentials: 'include'
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data && data.data.token) {
                    localStorage.setItem('sanctum_token', data.data.token);
                    return data.data.token;
                }
            }
        } catch (error) {
            console.error('NotificationService: Failed to get auth token', error);
        }
        return null;
    }

    /**
     * Connect to WebSocket server
     */
    connect() {
        if (this.isConnected || !this.userId || !this.userToken) {
            return;
        }

        console.log(`NotificationService: Connecting to ${this.config.serverUrl}`);

        try {
            // Load Socket.IO if not already loaded
            if (typeof io === 'undefined') {
                this.loadSocketIO().then(() => {
                    this.establishConnection();
                });
            } else {
                this.establishConnection();
            }
        } catch (error) {
            console.error('NotificationService: Connection failed', error);
            this.handleConnectionError(error);
        }
    }

    /**
     * Load Socket.IO library dynamically
     */
    async loadSocketIO() {
        return new Promise((resolve, reject) => {
            if (typeof io !== 'undefined') {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdn.socket.io/4.7.4/socket.io.min.js';
            script.onload = () => {
                console.log('NotificationService: Socket.IO loaded');
                resolve();
            };
            script.onerror = () => {
                console.error('NotificationService: Failed to load Socket.IO');
                reject(new Error('Failed to load Socket.IO'));
            };
            document.head.appendChild(script);
        });
    }

    /**
     * Establish Socket.IO connection
     */
    establishConnection() {
        this.socket = io(this.config.serverUrl, {
            ...this.config,
            auth: {
                token: this.userToken,
                userId: this.userId
            }
        });

        this.setupEventListeners();
        this.socket.connect();
    }

    /**
     * Setup Socket.IO event listeners
     */
    setupEventListeners() {
        // Connection events
        this.socket.on('connect', () => {
            this.isConnected = true;
            this.reconnectAttempts = 0;
            console.log('NotificationService: Connected to WebSocket server');
            console.log('   Socket ID:', this.socket.id);
            this.triggerCallbacks('onConnect');

            // Subscribe to user's private channel
            this.subscribeToUserChannel();
        });

        this.socket.on('disconnect', (reason) => {
            this.isConnected = false;
            console.log('NotificationService: Disconnected:', reason);
            this.triggerCallbacks('onDisconnect', reason);

            // Auto-reconnect unless manually disconnected
            if (reason !== 'io client disconnect') {
                this.attemptReconnect();
            }
        });

        this.socket.on('connect_error', (error) => {
            console.error('NotificationService: Connection error:', error);
            this.handleConnectionError(error);
        });

        // Server events
        this.socket.on('connected', (data) => {
            console.log('NotificationService: Server confirmation:', data);
        });

        // Notification events
        this.socket.on('notification.sent', (data) => {
            console.log('NotificationService: Received notification:', data);
            this.handleNotification(data);
        });

        this.socket.on('notification.read', (data) => {
            console.log('NotificationService: Notification read:', data);
            this.handleNotificationRead(data);
        });

        // Typing events
        this.socket.on('user_typing', (data) => {
            this.triggerCallbacks('onTyping', { ...data, isTyping: true });
        });

        this.socket.on('user_stopped_typing', (data) => {
            this.triggerCallbacks('onTyping', { ...data, isTyping: false });
        });

        // User activity events
        this.socket.on('user_activity', (data) => {
            this.triggerCallbacks('onUserActivity', data);
        });

        // Error handling
        this.socket.on('error', (error) => {
            console.error('NotificationService: Server error:', error);
            this.triggerCallbacks('onError', error);
        });
    }

    /**
     * Subscribe to user's private channel
     */
    subscribeToUserChannel() {
        if (!this.socket || !this.userId) return;

        const channel = `private-user.${this.userId}`;
        console.log(`NotificationService: Subscribing to ${channel}`);

        this.socket.emit('subscribe', { channel });

        this.socket.on('subscribed', (data) => {
            if (data.channel === channel) {
                console.log(`NotificationService: Successfully subscribed to ${channel}`);
            }
        });

        this.socket.on('subscription_error', (data) => {
            console.error('NotificationService: Subscription error:', data);
        });
    }

    /**
     * Handle incoming notification
     */
    handleNotification(notification) {
        // Trigger callbacks for notification managers
        this.triggerCallbacks('onNotification', notification);

        // Show browser notification if permission granted
        this.showBrowserNotification(notification);

        // Play notification sound
        this.playNotificationSound();
    }

    /**
     * Handle notification read event
     */
    handleNotificationRead(data) {
        // Update UI to mark notification as read
        const notificationElement = document.querySelector(`[data-notification-id="${data.notificationId}"]`);
        if (notificationElement) {
            notificationElement.classList.add('read');
            notificationElement.classList.remove('unread');
        }
    }

    /**
     * Show browser notification
     */
    showBrowserNotification(notification) {
        if ('Notification' in window && Notification.permission === 'granted') {
            const browserNotification = new Notification(notification.title || 'MechaMap', {
                body: notification.message || notification.content,
                icon: '/images/logo/mechamap-icon.png',
                tag: `mechamap-${notification.id}`,
                requireInteraction: false
            });

            // Auto-close after 5 seconds
            setTimeout(() => {
                browserNotification.close();
            }, 5000);

            // Handle click
            browserNotification.onclick = () => {
                window.focus();
                if (notification.url) {
                    window.location.href = notification.url;
                }
                browserNotification.close();
            };
        }
    }

    /**
     * Play notification sound
     */
    playNotificationSound() {
        try {
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.3;
            audio.play().catch(e => {
                // Ignore autoplay policy errors
                console.log('NotificationService: Audio autoplay blocked');
            });
        } catch (error) {
            // Ignore audio errors
        }
    }

    /**
     * Attempt to reconnect
     */
    attemptReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.error('NotificationService: Max reconnection attempts reached');
            this.triggerCallbacks('onError', new Error('Max reconnection attempts reached'));
            return;
        }

        this.reconnectAttempts++;
        const delay = this.reconnectDelay * Math.pow(2, this.reconnectAttempts - 1); // Exponential backoff

        console.log(`NotificationService: Reconnecting in ${delay}ms (attempt ${this.reconnectAttempts}/${this.maxReconnectAttempts})`);

        setTimeout(() => {
            if (!this.isConnected) {
                this.connect();
            }
        }, delay);
    }

    /**
     * Handle connection error
     */
    handleConnectionError(error) {
        console.error('NotificationService: Connection error:', error);
        this.triggerCallbacks('onError', error);

        // Start HTTP polling as fallback
        setTimeout(() => {
            if (!this.isConnected) {
                console.log('NotificationService: Starting HTTP polling fallback');
                this.startPolling();
            }
        }, 5000);
    }

    /**
     * Start HTTP polling as fallback
     */
    startPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }

        this.pollingInterval = setInterval(async () => {
            try {
                const response = await fetch('/api/notifications/poll', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Authorization': `Bearer ${this.userToken}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.notifications && data.notifications.length > 0) {
                        data.notifications.forEach(notification => {
                            this.handleNotification(notification);
                        });
                    }
                }
            } catch (error) {
                console.error('NotificationService: Polling failed:', error);
            }
        }, 30000); // Poll every 30 seconds
    }

    /**
     * Stop HTTP polling
     */
    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    }

    /**
     * Send typing indicator
     */
    sendTyping(threadId, isTyping = true) {
        if (!this.socket || !this.isConnected) return;

        this.socket.emit('typing', {
            threadId,
            userId: this.userId,
            isTyping
        });
    }

    /**
     * Mark notification as read
     */
    markAsRead(notificationId) {
        if (!this.socket || !this.isConnected) return;

        this.socket.emit('notification.read', {
            notificationId,
            userId: this.userId
        });
    }

    /**
     * Send user activity update
     */
    updateActivity(activity = 'online') {
        if (!this.socket || !this.isConnected) return;

        this.socket.emit('user_activity', {
            userId: this.userId,
            activity,
            timestamp: new Date().toISOString()
        });
    }

    /**
     * Register event callback
     */
    on(event, callback) {
        if (this.callbacks[event]) {
            this.callbacks[event].push(callback);
        }
    }

    /**
     * Remove event callback
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
     * Trigger callbacks for event
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
     * Disconnect from server
     */
    disconnect() {
        if (this.socket) {
            this.socket.disconnect();
        }
        this.stopPolling();
        this.isConnected = false;
        console.log('NotificationService: Manually disconnected');
    }

    /**
     * Get connection status
     */
    getStatus() {
        return {
            connected: this.isConnected,
            socketId: this.socket?.id,
            userId: this.userId,
            reconnectAttempts: this.reconnectAttempts,
            serverUrl: this.config.serverUrl
        };
    }

    /**
     * Get connection status (alias for compatibility)
     */
    getConnectionStatus() {
        return this.getStatus();
    }

    /**
     * Request notification permission
     */
    requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                console.log('NotificationService: Browser notification permission:', permission);
            });
        }
    }
}

// Initialize global notification service
window.NotificationService = new NotificationService();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NotificationService;
}
