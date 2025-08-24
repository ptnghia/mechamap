@extends('layouts.app')

@section('title', 'Group WebSocket Test')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-broadcast-tower me-2"></i>
                        Group WebSocket Integration Test
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Connection Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Connection Status</h6>
                                </div>
                                <div class="card-body">
                                    <div id="connection-status" class="mb-3">
                                        <span class="badge bg-secondary">Checking...</span>
                                    </div>
                                    <button id="connect-btn" class="btn btn-primary btn-sm me-2">Connect</button>
                                    <button id="disconnect-btn" class="btn btn-secondary btn-sm">Disconnect</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Group Selection</h6>
                                </div>
                                <div class="card-body">
                                    <select id="group-select" class="form-select mb-3">
                                        <option value="">Select a group...</option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}">{{ $group->title }}</option>
                                        @endforeach
                                    </select>
                                    <button id="join-group-btn" class="btn btn-success btn-sm me-2">Join Group</button>
                                    <button id="leave-group-btn" class="btn btn-warning btn-sm">Leave Group</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Test Actions -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Typing Test</h6>
                                </div>
                                <div class="card-body">
                                    <button id="start-typing-btn" class="btn btn-primary btn-sm me-2">Start Typing</button>
                                    <button id="stop-typing-btn" class="btn btn-secondary btn-sm">Stop Typing</button>
                                    <div id="typing-status" class="mt-2 small text-muted"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">Message Test</h6>
                                </div>
                                <div class="card-body">
                                    <input type="text" id="test-message" class="form-control form-control-sm mb-2" placeholder="Test message...">
                                    <button id="send-message-btn" class="btn btn-warning btn-sm">Send Message</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <button id="get-stats-btn" class="btn btn-info btn-sm me-2">Get Stats</button>
                                    <button id="get-typing-btn" class="btn btn-secondary btn-sm">Get Typing</button>
                                    <div id="stats-display" class="mt-2 small"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Log -->
                    <div class="card border-dark">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Event Log</h6>
                            <button id="clear-log-btn" class="btn btn-outline-light btn-sm">Clear Log</button>
                        </div>
                        <div class="card-body">
                            <div id="event-log" style="height: 300px; overflow-y: auto; font-family: monospace; font-size: 12px; background: #f8f9fa; padding: 10px; border-radius: 4px;">
                                <!-- Events will be logged here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Typing Indicator Display -->
<div id="typing-indicator" class="position-fixed bottom-0 end-0 m-3" style="display: none;">
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-keyboard me-2"></i>
        <span id="typing-text">Someone is typing...</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include WebSocket scripts -->
<script src="{{ asset('js/websocket-config.js') }}"></script>
<script src="{{ asset('js/group-websocket.js') }}"></script>
<script src="{{ asset('js/group-chat.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentGroupId = null;
    let eventCounter = 0;

    // DOM elements
    const connectionStatus = document.getElementById('connection-status');
    const groupSelect = document.getElementById('group-select');
    const eventLog = document.getElementById('event-log');
    const typingIndicator = document.getElementById('typing-indicator');
    const typingText = document.getElementById('typing-text');

    // Log function
    function logEvent(type, message, data = null) {
        eventCounter++;
        const timestamp = new Date().toLocaleTimeString();
        const logEntry = document.createElement('div');
        logEntry.className = `log-entry log-${type}`;
        logEntry.innerHTML = `
            <span class="text-muted">[${eventCounter}] ${timestamp}</span>
            <span class="badge bg-${getLogColor(type)} me-2">${type.toUpperCase()}</span>
            ${message}
            ${data ? `<pre class="mt-1 mb-0 small">${JSON.stringify(data, null, 2)}</pre>` : ''}
        `;
        eventLog.appendChild(logEntry);
        eventLog.scrollTop = eventLog.scrollHeight;
    }

    function getLogColor(type) {
        const colors = {
            'info': 'info',
            'success': 'success',
            'warning': 'warning',
            'error': 'danger',
            'websocket': 'primary',
            'api': 'secondary'
        };
        return colors[type] || 'dark';
    }

    // Update connection status
    function updateConnectionStatus() {
        if (window.MechaMapWebSocket && window.MechaMapWebSocket.isConnected()) {
            connectionStatus.innerHTML = '<span class="badge bg-success">Connected</span>';
        } else {
            connectionStatus.innerHTML = '<span class="badge bg-danger">Disconnected</span>';
        }
    }

    // Setup WebSocket callbacks
    if (window.GroupWebSocket) {
        // Group message events
        window.GroupWebSocket.addCallback('onGroupMessage', (data) => {
            logEvent('websocket', 'Group message received', data);
        });

        // Member events
        window.GroupWebSocket.addCallback('onMemberJoin', (data) => {
            logEvent('websocket', 'Member joined', data);
        });

        window.GroupWebSocket.addCallback('onMemberLeave', (data) => {
            logEvent('websocket', 'Member left', data);
        });

        // Typing events
        window.GroupWebSocket.addCallback('onTypingStart', (data) => {
            logEvent('websocket', 'Typing started', data);
            if (data.group_id == currentGroupId) {
                typingText.textContent = `${data.user_name} is typing...`;
                typingIndicator.style.display = 'block';
            }
        });

        window.GroupWebSocket.addCallback('onTypingStop', (data) => {
            logEvent('websocket', 'Typing stopped', data);
            if (data.group_id == currentGroupId) {
                typingIndicator.style.display = 'none';
            }
        });

        // Member count updates
        window.GroupWebSocket.addCallback('onMemberCountUpdate', (data) => {
            logEvent('websocket', 'Member count updated', data);
        });
    }

    // Event handlers
    document.getElementById('connect-btn').addEventListener('click', async () => {
        try {
            if (window.MechaMapWebSocket) {
                await window.MechaMapWebSocket.initialize();
                logEvent('success', 'WebSocket connection initiated');
                updateConnectionStatus();
            }
        } catch (error) {
            logEvent('error', 'Failed to connect', error);
        }
    });

    document.getElementById('join-group-btn').addEventListener('click', async () => {
        const groupId = groupSelect.value;
        if (!groupId) {
            logEvent('warning', 'No group selected');
            return;
        }

        try {
            currentGroupId = groupId;
            if (window.GroupWebSocket) {
                await window.GroupWebSocket.joinGroupAPI(groupId);
                logEvent('success', `Joined group ${groupId}`);
            }
        } catch (error) {
            logEvent('error', 'Failed to join group', error);
        }
    });

    document.getElementById('leave-group-btn').addEventListener('click', async () => {
        if (!currentGroupId) {
            logEvent('warning', 'No group joined');
            return;
        }

        try {
            if (window.GroupWebSocket) {
                await window.GroupWebSocket.leaveGroupAPI(currentGroupId);
                logEvent('success', `Left group ${currentGroupId}`);
                currentGroupId = null;
            }
        } catch (error) {
            logEvent('error', 'Failed to leave group', error);
        }
    });

    document.getElementById('start-typing-btn').addEventListener('click', () => {
        if (!currentGroupId) {
            logEvent('warning', 'No group joined');
            return;
        }

        if (window.GroupWebSocket) {
            window.GroupWebSocket.startTyping(currentGroupId);
            logEvent('info', 'Started typing indicator');
        }
    });

    document.getElementById('stop-typing-btn').addEventListener('click', () => {
        if (!currentGroupId) {
            logEvent('warning', 'No group joined');
            return;
        }

        if (window.GroupWebSocket) {
            window.GroupWebSocket.stopTyping(currentGroupId);
            logEvent('info', 'Stopped typing indicator');
        }
    });

    document.getElementById('send-message-btn').addEventListener('click', () => {
        const message = document.getElementById('test-message').value.trim();
        if (!message || !currentGroupId) {
            logEvent('warning', 'No message or group');
            return;
        }

        if (window.GroupWebSocket) {
            window.GroupWebSocket.sendMessage(currentGroupId, message);
            logEvent('info', `Sent message: ${message}`);
            document.getElementById('test-message').value = '';
        }
    });

    document.getElementById('get-stats-btn').addEventListener('click', async () => {
        if (!currentGroupId) {
            logEvent('warning', 'No group joined');
            return;
        }

        try {
            if (window.GroupWebSocket) {
                const stats = await window.GroupWebSocket.getChannelStats(currentGroupId);
                logEvent('api', 'Channel statistics', stats);
                document.getElementById('stats-display').innerHTML = `
                    <strong>Stats:</strong><br>
                    <small>${JSON.stringify(stats, null, 2)}</small>
                `;
            }
        } catch (error) {
            logEvent('error', 'Failed to get stats', error);
        }
    });

    document.getElementById('get-typing-btn').addEventListener('click', async () => {
        if (!currentGroupId) {
            logEvent('warning', 'No group joined');
            return;
        }

        try {
            if (window.GroupWebSocket) {
                const typingUsers = await window.GroupWebSocket.getTypingUsers(currentGroupId);
                logEvent('api', 'Typing users', typingUsers);
            }
        } catch (error) {
            logEvent('error', 'Failed to get typing users', error);
        }
    });

    document.getElementById('clear-log-btn').addEventListener('click', () => {
        eventLog.innerHTML = '';
        eventCounter = 0;
    });

    // Initial status update
    updateConnectionStatus();
    setInterval(updateConnectionStatus, 2000);

    logEvent('info', 'Group WebSocket Test initialized');
});
</script>
@endsection
