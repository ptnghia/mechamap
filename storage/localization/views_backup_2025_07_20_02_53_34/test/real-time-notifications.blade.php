@extends('layouts.app')

@section('title', 'Real-time Notifications Test')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-bell me-2"></i>
                        Real-time Notifications Test
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Test Controls -->
                        <div class="col-md-6">
                            <h5>Test Controls</h5>
                            
                            <div class="mb-3">
                                <button type="button" class="btn btn-primary" id="testNotification">
                                    <i class="fas fa-bell"></i>
                                    Send Test Notification
                                </button>
                            </div>
                            
                            <div class="mb-3">
                                <button type="button" class="btn btn-success" id="testAchievement">
                                    <i class="fas fa-trophy"></i>
                                    Test Achievement Notification
                                </button>
                            </div>
                            
                            <div class="mb-3">
                                <button type="button" class="btn btn-info" id="testFollower">
                                    <i class="fas fa-user-plus"></i>
                                    Test Follower Notification
                                </button>
                            </div>
                            
                            <div class="mb-3">
                                <button type="button" class="btn btn-warning" id="testTyping">
                                    <i class="fas fa-keyboard"></i>
                                    Test Typing Indicator
                                </button>
                            </div>
                            
                            <div class="mb-3">
                                <button type="button" class="btn btn-secondary" id="requestPermission">
                                    <i class="fas fa-shield-alt"></i>
                                    Request Browser Permission
                                </button>
                            </div>
                        </div>
                        
                        <!-- Status Display -->
                        <div class="col-md-6">
                            <h5>System Status</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Connection Status:</label>
                                <div id="connectionStatus" class="badge bg-secondary">Checking...</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Notification Count:</label>
                                <div id="notificationCount" class="badge bg-info">0</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Browser Permission:</label>
                                <div id="browserPermission" class="badge bg-secondary">Checking...</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Last Event:</label>
                                <div id="lastEvent" class="text-muted">None</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Event Log -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Event Log</h5>
                            <div id="eventLog" class="border rounded p-3" style="height: 300px; overflow-y: auto; background: #f8f9fa;">
                                <div class="text-muted">Event log will appear here...</div>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearLog">
                                    Clear Log
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Typing Test Area -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Typing Indicator Test</h5>
                            <div class="card" data-context-type="thread" data-context-id="1" data-typing-type="comment">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="testTextarea" class="form-label">Type here to test typing indicators:</label>
                                        <textarea class="form-control" id="testTextarea" rows="3" 
                                                placeholder="Start typing to see typing indicators in action..."></textarea>
                                    </div>
                                    
                                    <!-- Typing indicators will appear here -->
                                    <div class="typing-indicators-container">
                                        <!-- Typing indicators will be inserted here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const connectionStatus = document.getElementById('connectionStatus');
    const notificationCount = document.getElementById('notificationCount');
    const browserPermission = document.getElementById('browserPermission');
    const lastEvent = document.getElementById('lastEvent');
    const eventLog = document.getElementById('eventLog');
    
    // Test buttons
    const testNotificationBtn = document.getElementById('testNotification');
    const testAchievementBtn = document.getElementById('testAchievement');
    const testFollowerBtn = document.getElementById('testFollower');
    const testTypingBtn = document.getElementById('testTyping');
    const requestPermissionBtn = document.getElementById('requestPermission');
    const clearLogBtn = document.getElementById('clearLog');
    
    // Log function
    function logEvent(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        const logEntry = document.createElement('div');
        logEntry.className = `mb-1 text-${type}`;
        logEntry.innerHTML = `<small class="text-muted">[${timestamp}]</small> ${message}`;
        eventLog.appendChild(logEntry);
        eventLog.scrollTop = eventLog.scrollHeight;
        
        lastEvent.textContent = message;
    }
    
    // Update status function
    function updateStatus() {
        // Check connection status
        if (window.NotificationService) {
            const status = window.NotificationService.getConnectionStatus();
            connectionStatus.textContent = status.isConnected ? 'Connected' : 'Disconnected';
            connectionStatus.className = `badge bg-${status.isConnected ? 'success' : 'danger'}`;
        }
        
        // Check notification count
        if (window.NotificationManager) {
            const count = window.NotificationManager.getNotificationCount();
            notificationCount.textContent = `${count.unread}/${count.total}`;
        }
        
        // Check browser permission
        if ('Notification' in window) {
            browserPermission.textContent = Notification.permission;
            browserPermission.className = `badge bg-${Notification.permission === 'granted' ? 'success' : 
                                                    Notification.permission === 'denied' ? 'danger' : 'warning'}`;
        } else {
            browserPermission.textContent = 'Not supported';
            browserPermission.className = 'badge bg-secondary';
        }
    }
    
    // Setup event listeners for notification service
    if (window.NotificationService) {
        window.NotificationService.on('onConnect', () => {
            logEvent('Connected to notification service', 'success');
            updateStatus();
        });
        
        window.NotificationService.on('onDisconnect', () => {
            logEvent('Disconnected from notification service', 'warning');
            updateStatus();
        });
        
        window.NotificationService.on('onError', (error) => {
            logEvent(`Connection error: ${error.message}`, 'danger');
            updateStatus();
        });
        
        window.NotificationService.on('onNotification', (notification) => {
            logEvent(`New notification: ${notification.title}`, 'info');
            updateStatus();
        });
    }
    
    // Test buttons
    testNotificationBtn.addEventListener('click', async () => {
        try {
            const response = await fetch('/api/test/notification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title: 'Test Notification',
                    message: 'This is a test notification from the test page',
                    type: 'test'
                })
            });
            
            if (response.ok) {
                logEvent('Test notification sent successfully', 'success');
            } else {
                logEvent('Failed to send test notification', 'danger');
            }
        } catch (error) {
            logEvent(`Error sending test notification: ${error.message}`, 'danger');
        }
    });
    
    testAchievementBtn.addEventListener('click', async () => {
        try {
            const response = await fetch('/api/achievements/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                logEvent('Achievement check triggered', 'success');
            } else {
                logEvent('Failed to trigger achievement check', 'danger');
            }
        } catch (error) {
            logEvent(`Error triggering achievement check: ${error.message}`, 'danger');
        }
    });
    
    testFollowerBtn.addEventListener('click', async () => {
        try {
            const response = await fetch('/api/test/follower-notification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                logEvent('Follower notification test sent', 'success');
            } else {
                logEvent('Failed to send follower notification test', 'danger');
            }
        } catch (error) {
            logEvent(`Error sending follower notification: ${error.message}`, 'danger');
        }
    });
    
    testTypingBtn.addEventListener('click', async () => {
        try {
            const response = await fetch('/api/typing/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    context_type: 'thread',
                    context_id: 1,
                    typing_type: 'comment'
                })
            });
            
            if (response.ok) {
                logEvent('Typing indicator started', 'success');
                
                // Stop after 3 seconds
                setTimeout(async () => {
                    await fetch('/api/typing/stop', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            context_type: 'thread',
                            context_id: 1,
                            typing_type: 'comment'
                        })
                    });
                    logEvent('Typing indicator stopped', 'info');
                }, 3000);
            } else {
                logEvent('Failed to start typing indicator', 'danger');
            }
        } catch (error) {
            logEvent(`Error with typing indicator: ${error.message}`, 'danger');
        }
    });
    
    requestPermissionBtn.addEventListener('click', () => {
        if (window.NotificationManager) {
            window.NotificationManager.requestNotificationPermission();
            logEvent('Browser notification permission requested', 'info');
            setTimeout(updateStatus, 1000);
        }
    });
    
    clearLogBtn.addEventListener('click', () => {
        eventLog.innerHTML = '<div class="text-muted">Event log cleared...</div>';
    });
    
    // Initial status update
    updateStatus();
    
    // Update status every 5 seconds
    setInterval(updateStatus, 5000);
    
    logEvent('Real-time notification test page loaded', 'info');
});
</script>
@endsection
