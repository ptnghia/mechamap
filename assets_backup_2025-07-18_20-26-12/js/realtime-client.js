/**
 * MechaMap Real-time Client
 * Handles WebSocket connections and real-time features
 */
class MechaMapRealTime {
    constructor(options = {}) {
        this.options = {
            wsUrl: options.wsUrl || `ws://${window.location.host}/ws`,
            reconnectInterval: options.reconnectInterval || 5000,
            maxReconnectAttempts: options.maxReconnectAttempts || 5,
            heartbeatInterval: options.heartbeatInterval || 30000,
            debug: options.debug || false,
            ...options
        };

        this.websocket = null;
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.heartbeatTimer = null;
        this.eventListeners = new Map();
        this.userId = null;
        this.channels = new Set();

        this.init();
    }

    /**
     * Initialize the real-time client
     */
    init() {
        this.log('Initializing MechaMap Real-time Client');
        
        // Get user ID from meta tag or global variable
        this.userId = this.getUserId();
        
        // Setup event listeners
        this.setupEventListeners();
        
        // Connect to WebSocket
        this.connect();
        
        // Setup page visibility handling
        this.setupVisibilityHandling();
    }

    /**
     * Connect to WebSocket server
     */
    connect() {
        try {
            this.log('Connecting to WebSocket server...');
            
            this.websocket = new WebSocket(this.options.wsUrl);
            
            this.websocket.onopen = (event) => {
                this.handleOpen(event);
            };
            
            this.websocket.onmessage = (event) => {
                this.handleMessage(event);
            };
            
            this.websocket.onclose = (event) => {
                this.handleClose(event);
            };
            
            this.websocket.onerror = (event) => {
                this.handleError(event);
            };
            
        } catch (error) {
            this.log('WebSocket connection error:', error);
            this.scheduleReconnect();
        }
    }

    /**
     * Handle WebSocket open event
     */
    handleOpen(event) {
        this.log('WebSocket connected');
        this.isConnected = true;
        this.reconnectAttempts = 0;
        
        // Start heartbeat
        this.startHeartbeat();
        
        // Join user channel
        if (this.userId) {
            this.joinChannel(`user.${this.userId}`);
        }
        
        // Emit connected event
        this.emit('connected', { event });
        
        // Update UI
        this.updateConnectionStatus(true);
    }

    /**
     * Handle WebSocket message event
     */
    handleMessage(event) {
        try {
            const data = JSON.parse(event.data);
            this.log('Received message:', data);
            
            // Handle different message types
            switch (data.type) {
                case 'notification':
                    this.handleNotification(data);
                    break;
                case 'user_activity':
                    this.handleUserActivity(data);
                    break;
                case 'chat_message':
                    this.handleChatMessage(data);
                    break;
                case 'dashboard_metrics':
                    this.handleDashboardMetrics(data);
                    break;
                case 'typing_indicator':
                    this.handleTypingIndicator(data);
                    break;
                case 'system_announcement':
                    this.handleSystemAnnouncement(data);
                    break;
                case 'heartbeat':
                    this.handleHeartbeat(data);
                    break;
                default:
                    this.emit('message', data);
            }
            
        } catch (error) {
            this.log('Error parsing message:', error);
        }
    }

    /**
     * Handle WebSocket close event
     */
    handleClose(event) {
        this.log('WebSocket disconnected:', event.code, event.reason);
        this.isConnected = false;
        
        // Stop heartbeat
        this.stopHeartbeat();
        
        // Emit disconnected event
        this.emit('disconnected', { event });
        
        // Update UI
        this.updateConnectionStatus(false);
        
        // Schedule reconnect if not intentional close
        if (event.code !== 1000) {
            this.scheduleReconnect();
        }
    }

    /**
     * Handle WebSocket error event
     */
    handleError(event) {
        this.log('WebSocket error:', event);
        this.emit('error', { event });
    }

    /**
     * Schedule reconnection attempt
     */
    scheduleReconnect() {
        if (this.reconnectAttempts >= this.options.maxReconnectAttempts) {
            this.log('Max reconnection attempts reached');
            this.emit('reconnect_failed');
            return;
        }

        this.reconnectAttempts++;
        const delay = this.options.reconnectInterval * this.reconnectAttempts;
        
        this.log(`Scheduling reconnection attempt ${this.reconnectAttempts} in ${delay}ms`);
        
        setTimeout(() => {
            if (!this.isConnected) {
                this.connect();
            }
        }, delay);
    }

    /**
     * Start heartbeat to keep connection alive
     */
    startHeartbeat() {
        this.heartbeatTimer = setInterval(() => {
            if (this.isConnected) {
                this.send({
                    type: 'heartbeat',
                    timestamp: Date.now()
                });
            }
        }, this.options.heartbeatInterval);
    }

    /**
     * Stop heartbeat
     */
    stopHeartbeat() {
        if (this.heartbeatTimer) {
            clearInterval(this.heartbeatTimer);
            this.heartbeatTimer = null;
        }
    }

    /**
     * Send message through WebSocket
     */
    send(data) {
        if (this.isConnected && this.websocket.readyState === WebSocket.OPEN) {
            this.websocket.send(JSON.stringify(data));
            this.log('Sent message:', data);
        } else {
            this.log('Cannot send message: WebSocket not connected');
        }
    }

    /**
     * Join a channel
     */
    joinChannel(channel) {
        this.channels.add(channel);
        this.send({
            type: 'join_channel',
            channel: channel,
            user_id: this.userId
        });
        this.log(`Joined channel: ${channel}`);
    }

    /**
     * Leave a channel
     */
    leaveChannel(channel) {
        this.channels.delete(channel);
        this.send({
            type: 'leave_channel',
            channel: channel,
            user_id: this.userId
        });
        this.log(`Left channel: ${channel}`);
    }

    /**
     * Send notification
     */
    sendNotification(userId, notification) {
        this.send({
            type: 'send_notification',
            user_id: userId,
            notification: notification
        });
    }

    /**
     * Send chat message
     */
    sendChatMessage(receiverId, message) {
        this.send({
            type: 'send_chat_message',
            receiver_id: receiverId,
            message: message,
            sender_id: this.userId
        });
    }

    /**
     * Send typing indicator
     */
    sendTypingIndicator(receiverId, typing = true) {
        this.send({
            type: 'typing_indicator',
            receiver_id: receiverId,
            typing: typing,
            sender_id: this.userId
        });
    }

    /**
     * Handle notification message
     */
    handleNotification(data) {
        this.emit('notification', data);
        this.showNotificationToast(data.notification);
    }

    /**
     * Handle user activity message
     */
    handleUserActivity(data) {
        this.emit('user_activity', data);
        this.updateUserStatus(data);
    }

    /**
     * Handle chat message
     */
    handleChatMessage(data) {
        this.emit('chat_message', data);
        this.displayChatMessage(data.message);
    }

    /**
     * Handle dashboard metrics
     */
    handleDashboardMetrics(data) {
        this.emit('dashboard_metrics', data);
    }

    /**
     * Handle typing indicator
     */
    handleTypingIndicator(data) {
        this.emit('typing_indicator', data);
        this.showTypingIndicator(data);
    }

    /**
     * Handle system announcement
     */
    handleSystemAnnouncement(data) {
        this.emit('system_announcement', data);
        this.showSystemAnnouncement(data.announcement);
    }

    /**
     * Handle heartbeat response
     */
    handleHeartbeat(data) {
        this.log('Heartbeat received');
    }

    /**
     * Show notification toast
     */
    showNotificationToast(notification) {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${notification.level || 'info'}`;
        toast.innerHTML = `
            <div class="toast-header">
                <i class="${notification.icon || 'fas fa-bell'}"></i>
                <strong>${notification.title}</strong>
                <button type="button" class="toast-close" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
            <div class="toast-body">${notification.message}</div>
        `;

        // Add to toast container
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        container.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }

    /**
     * Update user online status
     */
    updateUserStatus(data) {
        const userElement = document.querySelector(`[data-user-id="${data.user_id}"]`);
        if (userElement) {
            const statusElement = userElement.querySelector('.user-status');
            if (statusElement) {
                statusElement.className = `user-status ${data.activity.type === 'user_online' ? 'online' : 'offline'}`;
            }
        }
    }

    /**
     * Display chat message
     */
    displayChatMessage(message) {
        const chatContainer = document.getElementById('chat-messages');
        if (chatContainer) {
            const messageElement = document.createElement('div');
            messageElement.className = 'chat-message';
            messageElement.innerHTML = `
                <div class="message-sender">${message.sender.name}</div>
                <div class="message-content">${message.content}</div>
                <div class="message-time">${new Date(message.timestamp).toLocaleTimeString()}</div>
            `;
            chatContainer.appendChild(messageElement);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    }

    /**
     * Show typing indicator
     */
    showTypingIndicator(data) {
        const chatContainer = document.getElementById('chat-messages');
        if (chatContainer) {
            const existingIndicator = chatContainer.querySelector('.typing-indicator');
            
            if (data.typing) {
                if (!existingIndicator) {
                    const indicator = document.createElement('div');
                    indicator.className = 'typing-indicator';
                    indicator.innerHTML = `${data.user_name || 'Someone'} is typing...`;
                    chatContainer.appendChild(indicator);
                }
            } else {
                if (existingIndicator) {
                    existingIndicator.remove();
                }
            }
        }
    }

    /**
     * Show system announcement
     */
    showSystemAnnouncement(announcement) {
        // Create modal or banner for system announcement
        alert(`System Announcement: ${announcement.title}\n\n${announcement.message}`);
    }

    /**
     * Update connection status in UI
     */
    updateConnectionStatus(connected) {
        const statusElements = document.querySelectorAll('.connection-status');
        statusElements.forEach(element => {
            element.className = `connection-status ${connected ? 'connected' : 'disconnected'}`;
            element.textContent = connected ? 'Connected' : 'Disconnected';
        });
    }

    /**
     * Setup page visibility handling
     */
    setupVisibilityHandling() {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.log('Page hidden - reducing activity');
            } else {
                this.log('Page visible - resuming activity');
                if (!this.isConnected) {
                    this.connect();
                }
            }
        });
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Handle page unload
        window.addEventListener('beforeunload', () => {
            this.disconnect();
        });
    }

    /**
     * Get user ID from page
     */
    getUserId() {
        // Try to get from meta tag
        const metaTag = document.querySelector('meta[name="user-id"]');
        if (metaTag) {
            return parseInt(metaTag.getAttribute('content'));
        }

        // Try to get from global variable
        if (window.userId) {
            return parseInt(window.userId);
        }

        return null;
    }

    /**
     * Add event listener
     */
    on(event, callback) {
        if (!this.eventListeners.has(event)) {
            this.eventListeners.set(event, []);
        }
        this.eventListeners.get(event).push(callback);
    }

    /**
     * Remove event listener
     */
    off(event, callback) {
        if (this.eventListeners.has(event)) {
            const listeners = this.eventListeners.get(event);
            const index = listeners.indexOf(callback);
            if (index > -1) {
                listeners.splice(index, 1);
            }
        }
    }

    /**
     * Emit event
     */
    emit(event, data) {
        if (this.eventListeners.has(event)) {
            this.eventListeners.get(event).forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    this.log('Error in event callback:', error);
                }
            });
        }
    }

    /**
     * Disconnect from WebSocket
     */
    disconnect() {
        if (this.websocket) {
            this.websocket.close(1000, 'Client disconnect');
        }
        this.stopHeartbeat();
    }

    /**
     * Get connection status
     */
    getStatus() {
        return {
            connected: this.isConnected,
            reconnectAttempts: this.reconnectAttempts,
            channels: Array.from(this.channels),
            userId: this.userId
        };
    }

    /**
     * Log message (if debug enabled)
     */
    log(...args) {
        if (this.options.debug) {
            console.log('[MechaMap RealTime]', ...args);
        }
    }
}

// Auto-initialize if not in module environment
if (typeof module === 'undefined') {
    window.MechaMapRealTime = MechaMapRealTime;
    
    // Auto-initialize on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        if (!window.mechaMapRealTime) {
            window.mechaMapRealTime = new MechaMapRealTime({
                debug: true
            });
        }
    });
}
