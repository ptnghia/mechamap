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

        console.log('MechaMap WebSocket: Creating new WebSocketManager instance');
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
            return 'https://realtime.mechamap.com';
        }
    }

    /**
     * Get authentication token from Laravel Sanctum
     */
    async function getAuthToken() {
        console.log('🔍 MechaMap WebSocket: Getting JWT token for authentication...');

        // Always fetch fresh JWT token from API
        return await getSanctumToken();
    }

    /**
     * Get Sanctum token from Laravel API (for authenticated users)
     */
    async function getSanctumToken() {
        console.log('🔄 MechaMap WebSocket: Fetching Sanctum token from Laravel API...');
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
                    console.log('✅ Successfully got Sanctum WebSocket token:', token.substring(0, 10) + '...');
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
        autoConnect: true
    };

    /**
     * Create Socket.IO connection
     */
    async function createConnection(options = {}) {
        console.log('MechaMap WebSocket: createConnection called', {
            hasSocket: !!wsManager.socket,
            isConnected: wsManager.socket?.connected,
            hasPromise: !!wsManager.connectionPromise,
            attempts: wsManager.connectionAttempts,
            maxAttempts: wsManager.MAX_CONNECTION_ATTEMPTS
        });

        // Return existing connection if available
        if (wsManager.socket && wsManager.socket.connected) {
            console.log('MechaMap WebSocket: Using existing connection', { id: wsManager.socket.id });
            return wsManager.socket;
        }

        // Return existing connection promise if in progress
        if (wsManager.connectionPromise) {
            console.log('MechaMap WebSocket: Connection already in progress, waiting...');
            return await wsManager.connectionPromise;
        }

        // Check connection attempts limit
        if (wsManager.connectionAttempts >= wsManager.MAX_CONNECTION_ATTEMPTS) {
            console.log('MechaMap WebSocket: Maximum connection attempts reached');
            return null;
        }

        console.log('MechaMap WebSocket: Creating new connection...');

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

        console.log('MechaMap WebSocket: Connecting to', serverUrl, {
            environment: isProduction ? 'production' : 'development',
            hasToken: !!authToken,
            tokenPrefix: authToken.substring(0, 10) + '...',
            config: socketConfig
        });

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

                console.log('MechaMap WebSocket: Connected successfully', { id: socket.id });
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
            console.log('MechaMap WebSocket: Connected', {
                id: socket.id,
                transport: socket.io.engine.transport.name
            });
        });

        socket.on('disconnect', (reason) => {
            console.log('MechaMap WebSocket: Disconnected', { reason });
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
            console.log('MechaMap WebSocket: Authenticated', data);
        });

        socket.on('authentication_error', (error) => {
            console.error('MechaMap WebSocket: Authentication error', error);
        });

        // Transport change logging
        socket.io.on('upgrade', () => {
            console.log('MechaMap WebSocket: Upgraded to', socket.io.engine.transport.name);
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

        console.log('MechaMap WebSocket: Subscribed to channel', channel);
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
            console.log('MechaMap WebSocket: Notification received', notification);
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
            console.log('MechaMap WebSocket: Message received', message);
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
            const latency = Date.now() - data.timestamp;
            console.log('MechaMap WebSocket: Connection test', {
                latency: latency + 'ms',
                connected: socket.connected,
                transport: socket.io.engine.transport.name
            });
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
console.log('MechaMap WebSocket: Checking auto-init conditions', {
    autoInitWebSocket: window.autoInitWebSocket,
    alreadyInitialized: window.mechaMapSocketInitialized,
    readyState: document.readyState
});

// Use multiple checks to prevent race conditions
if (window.autoInitWebSocket &&
    !window.mechaMapSocketInitialized &&
    !window.mechaMapSocketInitializing) {

    window.mechaMapSocketInitialized = true;
    window.mechaMapSocketInitializing = true;
    console.log('MechaMap WebSocket: Setting up auto-initialization');

    function initializeWebSocket() {
        console.log('MechaMap WebSocket: Starting auto-initialization');
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
                        console.log('MechaMap WebSocket: NotificationManager initialized');
                    }

                    // Initialize NotificationService if available and user is authenticated
                    // Wait for DOM to be ready to ensure meta tags are available
                    const initNotificationService = () => {
                        if (window.NotificationService && typeof window.NotificationService.init === 'function') {
                            // Check if user is authenticated before initializing
                            const userMeta = document.querySelector('meta[name="user-id"]');
                            if (userMeta && userMeta.getAttribute('content')) {
                                window.NotificationService.init();
                                console.log('MechaMap WebSocket: NotificationService initialized for user', userMeta.getAttribute('content'));

                                // Setup NotificationManager callbacks after NotificationService is initialized
                                if (window.NotificationManager && typeof window.NotificationManager.setupRealTimeCallbacks === 'function') {
                                    window.NotificationManager.setupRealTimeCallbacks();
                                    console.log('MechaMap WebSocket: NotificationManager callbacks setup');
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

                    console.log('MechaMap WebSocket: Auto-initialized successfully');
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
