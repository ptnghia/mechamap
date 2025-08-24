/**
 * MechaMap Group WebSocket Integration
 * Real-time features for group conversations
 */

window.GroupWebSocket = (function() {
    'use strict';

    let socket = null;
    let currentGroupId = null;
    let isInitialized = false;
    let typingTimer = null;
    let typingUsers = new Set();
    let apiBaseUrl = '/api/groups/websocket';

    // Group-specific callbacks
    const callbacks = {
        onGroupMessage: [],
        onMemberJoin: [],
        onMemberLeave: [],
        onMemberUpdate: [],
        onTypingStart: [],
        onTypingStop: [],
        onGroupUpdate: [],
        onMemberCountUpdate: []
    };

    /**
     * Initialize group WebSocket features
     */
    function initialize() {
        if (isInitialized) {
            console.log('GroupWebSocket: Already initialized');
            return;
        }

        // Wait for main WebSocket to be ready
        if (!window.MechaMapWebSocket || !window.MechaMapWebSocket.isConnected()) {
            console.log('GroupWebSocket: Waiting for main WebSocket connection...');
            setTimeout(initialize, 1000);
            return;
        }

        socket = window.MechaMapWebSocket.getSocket();
        if (!socket) {
            console.error('GroupWebSocket: No socket available');
            return;
        }

        setupGroupEventHandlers();
        isInitialized = true;
        console.log('✅ GroupWebSocket: Initialized successfully');
    }

    /**
     * Setup group-specific event handlers
     */
    function setupGroupEventHandlers() {
        if (!socket) return;

        // Group message events
        socket.on('group_message', (data) => {
            console.log('📨 Group message received:', data);
            triggerCallbacks('onGroupMessage', data);
        });

        // Member management events
        socket.on('member_joined', (data) => {
            console.log('👋 Member joined group:', data);
            triggerCallbacks('onMemberJoin', data);
            updateMemberCount(data.group_id);
        });

        socket.on('member_left', (data) => {
            console.log('👋 Member left group:', data);
            triggerCallbacks('onMemberLeave', data);
            updateMemberCount(data.group_id);
        });

        socket.on('member_updated', (data) => {
            console.log('👤 Member updated:', data);
            triggerCallbacks('onMemberUpdate', data);
        });

        // Typing indicators
        socket.on('group_typing_start', (data) => {
            console.log('✏️ Typing started in group:', data);
            triggerCallbacks('onTypingStart', data);
        });

        socket.on('group_typing_stop', (data) => {
            console.log('✏️ Typing stopped in group:', data);
            triggerCallbacks('onTypingStop', data);
        });

        // Group updates
        socket.on('group_updated', (data) => {
            console.log('🔄 Group updated:', data);
            triggerCallbacks('onGroupUpdate', data);
        });

        // Member count updates
        socket.on('member_count_updated', (data) => {
            console.log('📊 Member count updated:', data);
            triggerCallbacks('onMemberCountUpdate', data);
        });
    }

    /**
     * Join a group channel
     */
    function joinGroup(groupId) {
        if (!socket || !socket.connected) {
            console.error('GroupWebSocket: Socket not connected');
            return false;
        }

        currentGroupId = groupId;
        socket.emit('join_group', { group_id: groupId });
        console.log(`🏠 Joined group channel: ${groupId}`);
        return true;
    }

    /**
     * Leave a group channel
     */
    function leaveGroup(groupId = null) {
        if (!socket || !socket.connected) {
            console.error('GroupWebSocket: Socket not connected');
            return false;
        }

        const targetGroupId = groupId || currentGroupId;
        if (!targetGroupId) {
            console.warn('GroupWebSocket: No group to leave');
            return false;
        }

        socket.emit('leave_group', { group_id: targetGroupId });
        console.log(`🚪 Left group channel: ${targetGroupId}`);

        if (targetGroupId === currentGroupId) {
            currentGroupId = null;
        }

        return true;
    }

    /**
     * Send typing indicator
     */
    function sendTyping(groupId, isTyping = true) {
        if (!socket || !socket.connected) {
            console.error('GroupWebSocket: Socket not connected');
            return false;
        }

        const event = isTyping ? 'group_typing_start' : 'group_typing_stop';
        socket.emit(event, { group_id: groupId });
        return true;
    }

    /**
     * Send group message
     */
    function sendMessage(groupId, message) {
        if (!socket || !socket.connected) {
            console.error('GroupWebSocket: Socket not connected');
            return false;
        }

        socket.emit('group_message', {
            group_id: groupId,
            message: message,
            timestamp: new Date().toISOString()
        });

        console.log(`📤 Sent message to group ${groupId}`);
        return true;
    }

    /**
     * Update member count for a group
     */
    function updateMemberCount(groupId) {
        if (!socket || !socket.connected) return;

        socket.emit('get_member_count', { group_id: groupId });
    }

    /**
     * Add callback for specific event type
     */
    function addCallback(eventType, callback) {
        if (callbacks[eventType]) {
            callbacks[eventType].push(callback);
        } else {
            console.warn(`GroupWebSocket: Unknown event type: ${eventType}`);
        }
    }

    /**
     * Remove callback for specific event type
     */
    function removeCallback(eventType, callback) {
        if (callbacks[eventType]) {
            const index = callbacks[eventType].indexOf(callback);
            if (index > -1) {
                callbacks[eventType].splice(index, 1);
            }
        }
    }

    /**
     * Trigger callbacks for specific event type
     */
    function triggerCallbacks(eventType, data) {
        if (callbacks[eventType]) {
            callbacks[eventType].forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error(`GroupWebSocket: Callback error for ${eventType}:`, error);
                }
            });
        }
    }

    /**
     * Get current group ID
     */
    function getCurrentGroupId() {
        return currentGroupId;
    }

    /**
     * Check if connected to a group
     */
    function isInGroup(groupId = null) {
        if (groupId) {
            return currentGroupId === groupId;
        }
        return currentGroupId !== null;
    }

    /**
     * Get connection status
     */
    function getStatus() {
        return {
            initialized: isInitialized,
            connected: socket && socket.connected,
            currentGroup: currentGroupId,
            socketId: socket ? socket.id : null
        };
    }

    // Auto-initialize when main WebSocket is ready
    if (window.MechaMapWebSocket) {
        // Add callback to main WebSocket for connection events
        window.MechaMapWebSocket.addCallback('onConnect', () => {
            console.log('GroupWebSocket: Main WebSocket connected, initializing...');
            initialize();
        });

        // Initialize immediately if already connected
        if (window.MechaMapWebSocket.isConnected()) {
            initialize();
        }
    }

    /**
     * Join group via API
     */
    async function joinGroupAPI(groupId) {
        try {
            const response = await fetch(`${apiBaseUrl}/join`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Authorization': `Bearer ${getAuthToken()}`
                },
                body: JSON.stringify({ group_id: groupId })
            });

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Failed to join group');
            }

            console.log('✅ GroupWebSocket: Joined group via API', groupId);
            return data;

        } catch (error) {
            console.error('❌ GroupWebSocket: Failed to join group via API', error);
            throw error;
        }
    }

    /**
     * Leave group via API
     */
    async function leaveGroupAPI(groupId) {
        try {
            const response = await fetch(`${apiBaseUrl}/leave`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Authorization': `Bearer ${getAuthToken()}`
                },
                body: JSON.stringify({ group_id: groupId })
            });

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Failed to leave group');
            }

            console.log('✅ GroupWebSocket: Left group via API', groupId);
            return data;

        } catch (error) {
            console.error('❌ GroupWebSocket: Failed to leave group via API', error);
            throw error;
        }
    }

    /**
     * Send typing indicator via API
     */
    async function sendTypingAPI(groupId, isTyping) {
        try {
            const response = await fetch(`${apiBaseUrl}/typing`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Authorization': `Bearer ${getAuthToken()}`
                },
                body: JSON.stringify({
                    group_id: groupId,
                    is_typing: isTyping
                })
            });

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Failed to send typing indicator');
            }

            return data;

        } catch (error) {
            console.error('❌ GroupWebSocket: Failed to send typing indicator', error);
            throw error;
        }
    }

    /**
     * Get group channel statistics
     */
    async function getChannelStats(groupId) {
        try {
            const response = await fetch(`${apiBaseUrl}/${groupId}/stats`, {
                headers: {
                    'Authorization': `Bearer ${getAuthToken()}`
                }
            });

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Failed to get channel stats');
            }

            return data.data;

        } catch (error) {
            console.error('❌ GroupWebSocket: Failed to get channel stats', error);
            throw error;
        }
    }

    /**
     * Get active typing users
     */
    async function getTypingUsers(groupId) {
        try {
            const response = await fetch(`${apiBaseUrl}/${groupId}/typing`, {
                headers: {
                    'Authorization': `Bearer ${getAuthToken()}`
                }
            });

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Failed to get typing users');
            }

            return data.data.typing_users;

        } catch (error) {
            console.error('❌ GroupWebSocket: Failed to get typing users', error);
            return [];
        }
    }

    /**
     * Enhanced typing indicator with auto-stop
     */
    function startTyping(groupId) {
        if (!groupId) groupId = currentGroupId;
        if (!groupId) return;

        // Clear existing timer
        if (typingTimer) {
            clearTimeout(typingTimer);
        }

        // Send typing start
        sendTypingAPI(groupId, true).catch(console.error);

        // Auto-stop typing after 3 seconds
        typingTimer = setTimeout(() => {
            stopTyping(groupId);
        }, 3000);
    }

    /**
     * Stop typing indicator
     */
    function stopTyping(groupId) {
        if (!groupId) groupId = currentGroupId;
        if (!groupId) return;

        // Clear timer
        if (typingTimer) {
            clearTimeout(typingTimer);
            typingTimer = null;
        }

        // Send typing stop
        sendTypingAPI(groupId, false).catch(console.error);
    }

    /**
     * Get authentication token
     */
    function getAuthToken() {
        // Try to get from localStorage (Sanctum token)
        const token = localStorage.getItem('auth_token') ||
                     sessionStorage.getItem('auth_token') ||
                     document.querySelector('meta[name="api-token"]')?.getAttribute('content');

        return token;
    }

    /**
     * Update typing users display
     */
    function updateTypingDisplay(groupId, typingUsersList) {
        const typingContainer = document.querySelector(`[data-group-id="${groupId}"] .typing-indicator`);
        if (!typingContainer) return;

        if (typingUsersList.length === 0) {
            typingContainer.style.display = 'none';
            return;
        }

        const names = typingUsersList.map(user => user.user_name);
        let text = '';

        if (names.length === 1) {
            text = `${names[0]} đang nhập...`;
        } else if (names.length === 2) {
            text = `${names[0]} và ${names[1]} đang nhập...`;
        } else {
            text = `${names[0]} và ${names.length - 1} người khác đang nhập...`;
        }

        typingContainer.textContent = text;
        typingContainer.style.display = 'block';
    }

    // Public API
    return {
        // Initialization
        initialize,
        getStatus,

        // Group management
        joinGroup,
        leaveGroup,
        getCurrentGroupId,
        isInGroup,

        // Communication
        sendMessage,
        sendTyping,
        updateMemberCount,

        // Enhanced features
        joinGroupAPI,
        leaveGroupAPI,
        sendTypingAPI,
        getChannelStats,
        getTypingUsers,
        startTyping,
        stopTyping,
        updateTypingDisplay,

        // Event handling
        addCallback,
        removeCallback,

        // Available event types
        EVENTS: {
            GROUP_MESSAGE: 'onGroupMessage',
            MEMBER_JOIN: 'onMemberJoin',
            MEMBER_LEAVE: 'onMemberLeave',
            MEMBER_UPDATE: 'onMemberUpdate',
            TYPING_START: 'onTypingStart',
            TYPING_STOP: 'onTypingStop',
            GROUP_UPDATE: 'onGroupUpdate',
            MEMBER_COUNT_UPDATE: 'onMemberCountUpdate'
        }
    };
})();

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        console.log('🚀 GroupWebSocket: DOM ready, checking for auto-init...');
    });
} else {
    console.log('🚀 GroupWebSocket: DOM already ready');
}
