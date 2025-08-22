/**
 * MechaMap Unified Notification Service
 * Bridge to MechaMapWebSocket for backward compatibility
 * This file maintains compatibility with existing code while using the unified WebSocket system
 */
class NotificationService {
    constructor() {
        console.log('NotificationService: Using unified MechaMapWebSocket system');

        // Check if MechaMapWebSocket is available
        if (!window.MechaMapWebSocket) {
            console.error('NotificationService: MechaMapWebSocket not found! Make sure websocket-config.js is loaded first.');
            return;
        }

        this.webSocket = window.MechaMapWebSocket;
        this.isConnected = false;
        this.userId = null;

        // Legacy callback support
        this.callbacks = {
            onNotification: [],
            onConnect: [],
            onDisconnect: [],
            onError: [],
            onTyping: [],
            onUserActivity: []
        };

        this.init();
    }

    /**
     * Initialize notification service
     */
    async init() {
        try {
            // Get user data from meta tags
            const userMeta = document.querySelector('meta[name="user-id"]');
            if (!userMeta) {
                console.log('NotificationService: No authenticated user found');
                return;
            }

            this.userId = userMeta.getAttribute('content');
            console.log('NotificationService: Using unified WebSocket for user:', this.userId);

            // Setup callbacks to bridge to unified WebSocket
            this.setupWebSocketBridge();

            // Initialize unified WebSocket if not already done
            if (!this.webSocket.isConnected()) {
                console.log('NotificationService: Initializing unified WebSocket...');
                await this.webSocket.initialize();
            } else {
                console.log('NotificationService: Using existing WebSocket connection');
                this.isConnected = true;
                this.triggerCallbacks('onConnect', { userId: this.userId });
            }

        } catch (error) {
            console.error('NotificationService: Initialization failed', error);
            this.triggerCallbacks('onError', error);
        }
    }

    /**
     * Setup bridge between legacy callbacks and unified WebSocket
     */
    setupWebSocketBridge() {
        // Bridge connect events
        this.webSocket.addCallback('onConnect', (data) => {
            this.isConnected = true;
            console.log('NotificationService: Connected via unified WebSocket');
            this.triggerCallbacks('onConnect', data);
        });

        // Bridge disconnect events
        this.webSocket.addCallback('onDisconnect', (data) => {
            this.isConnected = false;
            console.log('NotificationService: Disconnected from unified WebSocket');
            this.triggerCallbacks('onDisconnect', data);
        });

        // Bridge error events
        this.webSocket.addCallback('onError', (error) => {
            console.error('NotificationService: Error from unified WebSocket:', error);
            this.triggerCallbacks('onError', error);
        });

        // Bridge notification events
        this.webSocket.addCallback('onNotification', (notification) => {
            console.log('NotificationService: Received notification via unified WebSocket:', notification);
            this.triggerCallbacks('onNotification', notification);
        });

        // Bridge typing events
        this.webSocket.addCallback('onTyping', (data) => {
            this.triggerCallbacks('onTyping', data);
        });

        // Bridge user activity events
        this.webSocket.addCallback('onUserActivity', (data) => {
            this.triggerCallbacks('onUserActivity', data);
        });
    }

    /**
     * Add callback for specific event type (legacy support)
     */
    addCallback(eventType, callback) {
        if (this.callbacks[eventType]) {
            this.callbacks[eventType].push(callback);
        }
    }

    /**
     * Remove callback for specific event type (legacy support)
     */
    removeCallback(eventType, callback) {
        if (this.callbacks[eventType]) {
            const index = this.callbacks[eventType].indexOf(callback);
            if (index > -1) {
                this.callbacks[eventType].splice(index, 1);
            }
        }
    }

    /**
     * Trigger callbacks for specific event type (legacy support)
     */
    triggerCallbacks(eventType, data) {
        if (this.callbacks[eventType]) {
            this.callbacks[eventType].forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error(`NotificationService: Callback error for ${eventType}:`, error);
                }
            });
        }
    }

    /**
     * Legacy method - now delegates to unified WebSocket
     */
    connect() {
        console.log('NotificationService: connect() called - delegating to unified WebSocket');
        if (!this.webSocket.isConnected()) {
            return this.webSocket.initialize();
        }
        return Promise.resolve(this.webSocket.getSocket());
    }

    /**
     * Legacy method - now delegates to unified WebSocket
     */
    disconnect() {
        console.log('NotificationService: disconnect() called - delegating to unified WebSocket');
        const socket = this.webSocket.getSocket();
        if (socket) {
            socket.disconnect();
        }
        this.isConnected = false;
    }

    /**
     * Legacy method - check connection status
     */
    getConnectionStatus() {
        return {
            connected: this.webSocket.isConnected(),
            socket: this.webSocket.getSocket()
        };
    }

    /**
     * Legacy method aliases for backward compatibility
     */
    on(eventType, callback) {
        this.addCallback(eventType, callback);
    }

    off(eventType, callback) {
        this.removeCallback(eventType, callback);
    }
}

// Create global instance for backward compatibility
if (typeof window !== 'undefined') {
    // Create instance first
    const notificationServiceInstance = new NotificationService();

    // Expose both class and instance for backward compatibility
    window.NotificationService = notificationServiceInstance;
    window.notificationService = notificationServiceInstance;

    console.log('NotificationService: Global instance created and exposed');
}
