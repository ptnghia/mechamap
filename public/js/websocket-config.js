/**
 * MechaMap WebSocket Configuration
 * Environment-specific WebSocket client configuration
 */

window.MechaMapWebSocket = (function() {
    'use strict';

    // Singleton pattern for WebSocket management
    let instance = null;

    function WebSocketManager() {
        if (instance) {
            console.log('MechaMap WebSocket: Returning existing WebSocketManager instance');
            return instance;
        }

        // Creating new WebSocketManager instance
        this.socket = null;
        this.isConnecting = false;
        this.connectionPromise = null;
        this.connectionAttempts = 0;
        this.MAX_CONNECTION_ATTEMPTS = 1;

        instance = this;
        return this;
    }

    // Create singleton instance
    const wsManager = new WebSocketManager();

    // Get configuration from Laravel backend
    const config = window.websocketConfig || {};

    // Environment detection
    const isProduction = window.location.hostname === 'mechamap.com' ||
                        window.location.hostname === 'www.mechamap.com';

    const isDevelopment = window.location.hostname === 'mechamap.test' ||
                         window.location.hostname === 'localhost' ||
                         window.location.hostname === '127.0.0.1';

    /**
     * Get WebSocket server URL based on environment
     */
    function getWebSocketUrl() {
        if (config.server_url) {
            return config.server_url;
        }

        if (isProduction) {
            return 'https://realtime.mechamap.com';
        } else {
            return 'http://localhost:3000';
        }
    }

    /**
     * Get authentication token from Laravel Sanctum
     */
    async function getAuthToken() {
        // Getting JWT token for authentication

        // Always fetch fresh JWT token from API
        return await getSanctumToken();
    }

    /**
     * Get Sanctum token from Laravel API (for authenticated users)
     */
    async function getSanctumToken() {
        // Fetching Sanctum token from Laravel API
        try {
            const response = await fetch('/api/user/websocket-token', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'include'
            });

            console.log('📡 API Response status:', response.status);

            if (response.ok) {
                const data = await response.json();
                console.log('📦 API Response data:', data);

                // Handle response format from WebSocket token endpoint (following official docs)
                let token = null;
                let websocketUrl = null;
                let userId = null;

                // Expected structure: { success: true, data: { token, user_id, websocket_url } }
                if (data.success && data.data && data.data.token) {
                    token = data.data.token;
                    websocketUrl = data.data.websocket_url;
                    userId = data.data.user_id;
                } else if (data.token) {
                    // Fallback for direct token in response
                    token = data.token;
                    websocketUrl = data.websocket_url;
                    userId = data.user_id;
                }

                console.log('🔍 API Response structure:', {
                    success: data.success,
                    message: data.message,
                    hasDataToken: !!(data.data && data.data.token),
                    user_id: userId,
                    websocket_url: websocketUrl,
                    permissions: data.data ? data.data.permissions : null,
                    expires_at: data.data ? data.data.expires_at : null,
                    extractedToken: token ? token.substring(0, 20) + '...' : 'null'
                });

                if (token) {
                    // Successfully got Sanctum WebSocket token
                    return token;
                } else {
                    console.warn('⚠️ API response missing token field. Response structure:', data);
                }
            } else {
                console.warn('❌ API request failed with status:', response.status);
                const errorText = await response.text();
                console.warn('❌ Error response:', errorText);
            }
        } catch (error) {
            console.warn('❌ MechaMap WebSocket: Failed to get Sanctum token', error);
        }

        return null;
    }

    /**
     * Default Socket.IO configuration
     */
    const defaultSocketConfig = {
        transports: ['websocket', 'polling'],
        timeout: 20000,
        reconnection: true,
        reconnectionDelay: 1000,
        reconnectionAttempts: 5,
        maxReconnectionAttempts: 10,
        forceNew: false,
        autoConnect: true,
        // Disable SSL verification for development
        rejectUnauthorized: false,
        secure: false
    };

    /**
     * Create Socket.IO connection
     */
    async function createConnection(options = {}) {
        // Creating WebSocket connection

        // Return existing connection if available
        if (wsManager.socket && wsManager.socket.connected) {
            // Using existing connection
            return wsManager.socket;
        }

        // Return existing connection promise if in progress
        if (wsManager.connectionPromise) {
            // Connection already in progress, waiting
            return await wsManager.connectionPromise;
        }

        // Check connection attempts limit
        if (wsManager.connectionAttempts >= wsManager.MAX_CONNECTION_ATTEMPTS) {
            console.log('MechaMap WebSocket: Maximum connection attempts reached');
            return null;
        }

        // Creating new connection

        // Create new connection promise
        wsManager.connectionPromise = createNewConnection(options);

        try {
            const socket = await wsManager.connectionPromise;
            return socket;
        } catch (error) {
            console.error('MechaMap WebSocket: Connection failed', error);
            wsManager.connectionPromise = null;
            return null;
        }
    }

    async function createNewConnection(options = {}) {
        wsManager.isConnecting = true;
        wsManager.connectionAttempts++;

        const serverUrl = getWebSocketUrl();
        let authToken = await getAuthToken();

        // If no token found, try to get from Laravel API
        if (!authToken) {
            console.log('MechaMap WebSocket: No cached token, trying to get from Laravel...');
            authToken = await getSanctumToken();
        }

        // Development mode: Skip token requirement
        if (!authToken) {
            console.log('MechaMap WebSocket: No token found, using development mode');
            authToken = 'development-mode';
        }

        const socketConfig = {
            ...defaultSocketConfig,
            ...options,
            auth: {
                token: authToken
            },
            query: {
                token: authToken
            }
        };

        // Connecting to WebSocket server

        // Create Socket.IO connection
        const socket = io(serverUrl, socketConfig);

        // Return promise that resolves when connected
        return new Promise((resolve, reject) => {
            const timeout = setTimeout(() => {
                reject(new Error('Connection timeout'));
            }, 10000); // 10 second timeout

            socket.on('connect', () => {
                clearTimeout(timeout);

                // Store as singleton socket
                wsManager.socket = socket;
                wsManager.isConnecting = false;
                wsManager.connectionPromise = null;

                // Setup event listeners
                setupEventListeners(socket);

                console.log('✅ WebSocket connected:', socket.id);
                resolve(socket);
            });

            socket.on('connect_error', (error) => {
                clearTimeout(timeout);
                wsManager.isConnecting = false;
                wsManager.connectionPromise = null;
                console.error('MechaMap WebSocket: Connection error', error);
                reject(error);
            });
        });
    }

    /**
     * Setup default event listeners
     */
    function setupEventListeners(socket) {
        socket.on('connect', () => {
            // WebSocket connected successfully
        });

        socket.on('disconnect', (reason) => {
            console.log('⚠️ WebSocket disconnected:', reason);
            // Reset singleton state on disconnect
            if (wsManager.socket === socket) {
                wsManager.socket = null;
                wsManager.isConnecting = false;
                wsManager.connectionAttempts = 0;
                wsManager.connectionPromise = null;
            }
        });

        socket.on('connect_error', (error) => {
            console.error('MechaMap WebSocket: Connection error', error);
        });

        socket.on('authenticated', (data) => {
            // WebSocket authenticated successfully
        });

        socket.on('authentication_error', (error) => {
            console.error('MechaMap WebSocket: Authentication error', error);
        });

        // Transport change logging
        socket.io.on('upgrade', () => {
            // WebSocket transport upgraded
        });
    }

    /**
     * Subscribe to user's private channel
     */
    function subscribeToUserChannel(socket, userId) {
        if (!socket || !userId) {
            console.error('MechaMap WebSocket: Invalid socket or userId for channel subscription');
            return;
        }

        const channel = `private-user.${userId}`;

        socket.emit('subscribe', {
            channel: channel
        });

        // Subscribed to channel
    }

    /**
     * Handle notifications
     */
    function setupNotificationHandler(socket, callback) {
        if (!socket || typeof callback !== 'function') {
            console.error('MechaMap WebSocket: Invalid socket or callback for notifications');
            return;
        }

        socket.on('notification.sent', (notification) => {
            // Notification received
            callback(notification);
        });
    }

    /**
     * Handle real-time messages
     */
    function setupMessageHandler(socket, callback) {
        if (!socket || typeof callback !== 'function') {
            console.error('MechaMap WebSocket: Invalid socket or callback for messages');
            return;
        }

        socket.on('message.received', (message) => {
            // Message received
            callback(message);
        });
    }

    /**
     * Test connection health
     */
    function testConnection(socket) {
        if (!socket) {
            console.error('MechaMap WebSocket: No socket for connection test');
            return;
        }

        const startTime = Date.now();

        socket.emit('ping', { timestamp: startTime });

        socket.once('pong', (data) => {
            // Connection test completed
        });
    }

    /**
     * Get connection status
     */
    function getConnectionStatus(socket) {
        if (!socket) {
            return {
                connected: false,
                error: 'No socket instance'
            };
        }

        return {
            connected: socket.connected,
            id: socket.id,
            transport: socket.io.engine.transport.name,
            reconnecting: socket.io.reconnecting,
            url: socket.io.uri
        };
    }

    // Public API
    return {
        // Configuration
        getWebSocketUrl,
        getAuthToken,

        // Connection management
        createConnection,
        testConnection,
        getConnectionStatus,

        // Channel management
        subscribeToUserChannel,

        // Event handlers
        setupNotificationHandler,
        setupMessageHandler,

        // Utilities
        isProduction,
        isDevelopment,

        // Constants
        DEFAULT_CONFIG: defaultSocketConfig
    };
})();

// Auto-initialize if requested (prevent multiple initializations)

// Use multiple checks to prevent race conditions
if (window.autoInitWebSocket &&
    !window.mechaMapSocketInitialized &&
    !window.mechaMapSocketInitializing) {

    window.mechaMapSocketInitialized = true;
    window.mechaMapSocketInitializing = true;
    // Setting up auto-initialization

    function initializeWebSocket() {
        // Starting auto-initialization
        if (typeof io !== 'undefined') {
            window.MechaMapWebSocket.createConnection().then(socket => {
                if (socket) {
                    window.mechaMapSocket = socket;

                    // Setup notification handler if available
                    if (typeof window.handleWebSocketNotification === 'function') {
                        window.MechaMapWebSocket.setupNotificationHandler(
                            socket,
                            window.handleWebSocketNotification
                        );
                    }

                    // Setup message handler if available
                    if (typeof window.handleWebSocketMessage === 'function') {
                        window.MechaMapWebSocket.setupMessageHandler(
                            socket,
                            window.handleWebSocketMessage
                        );
                    }

                    // Subscribe to user channel if user ID is available
                    if (window.websocketConfig && window.websocketConfig.user_id) {
                        window.MechaMapWebSocket.subscribeToUserChannel(
                            socket,
                            window.websocketConfig.user_id
                        );
                    }

                    // Initialize NotificationManager first (must be available before NotificationService)
                    if (typeof NotificationManager !== 'undefined' && !window.NotificationManager) {
                        window.NotificationManager = new NotificationManager();
                        // NotificationManager initialized
                    }

                    // Initialize NotificationService if available and user is authenticated
                    // Wait for DOM to be ready to ensure meta tags are available
                    const initNotificationService = () => {
                        if (window.NotificationService && typeof window.NotificationService.init === 'function') {
                            // Check if user is authenticated before initializing
                            const userMeta = document.querySelector('meta[name="user-id"]');
                            if (userMeta && userMeta.getAttribute('content')) {
                                window.NotificationService.init();
                                // NotificationService initialized for user

                                // Setup NotificationManager callbacks after NotificationService is initialized
                                if (window.NotificationManager && typeof window.NotificationManager.setupRealTimeCallbacks === 'function') {
                                    window.NotificationManager.setupRealTimeCallbacks();
                                    // NotificationManager callbacks setup
                                }
                            } else {
                                console.log('MechaMap WebSocket: NotificationService skipped - no authenticated user');
                            }
                        }
                    };

                    // Initialize immediately if DOM is ready, otherwise wait
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', initNotificationService);
                    } else {
                        initNotificationService();
                    }

                    console.log('✅ WebSocket auto-initialized');
                } else {
                    console.error('MechaMap WebSocket: Auto-initialization failed');
                }
                window.mechaMapSocketInitializing = false;
            }).catch(error => {
                console.error('MechaMap WebSocket: Auto-initialization error', error);
                window.mechaMapSocketInitializing = false;
            });
        } else {
            console.error('MechaMap WebSocket: Socket.IO library not loaded');
            window.mechaMapSocketInitializing = false;
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeWebSocket, { once: true });
    } else {
        // DOM already loaded
        initializeWebSocket();
    }
}
