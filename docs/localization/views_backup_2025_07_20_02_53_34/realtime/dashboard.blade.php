@extends('layouts.app')

@section('title', 'Real-time Dashboard')

@section('css')
<style>
.realtime-dashboard {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 20px 0;
}

.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px 0;
    margin-bottom: 30px;
    border-radius: 15px;
}

.status-indicator {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 8px;
    animation: pulse 2s infinite;
}

.status-online { background: #28a745; }
.status-offline { background: #dc3545; }
.status-warning { background: #ffc107; }

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.metric-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.metric-value {
    font-size: 2.5rem;
    font-weight: bold;
    color: #495057;
    margin-bottom: 5px;
}

.metric-label {
    color: #6c757d;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.metric-change {
    font-size: 0.8rem;
    margin-top: 5px;
}

.metric-change.positive { color: #28a745; }
.metric-change.negative { color: #dc3545; }
.metric-change.neutral { color: #6c757d; }

.chart-container {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    height: 400px;
}

.activity-feed {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    max-height: 500px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f8f9fa;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 16px;
    color: white;
}

.activity-icon.user { background: #007bff; }
.activity-icon.system { background: #6c757d; }
.activity-icon.error { background: #dc3545; }
.activity-icon.success { background: #28a745; }

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    margin-bottom: 2px;
}

.activity-time {
    font-size: 0.8rem;
    color: #6c757d;
}

.online-users {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    max-height: 400px;
    overflow-y: auto;
}

.user-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    margin-right: 12px;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: #6c757d;
}

.user-info {
    flex: 1;
}

.user-name {
    font-weight: 500;
    margin-bottom: 2px;
}

.user-status {
    font-size: 0.8rem;
    color: #6c757d;
}

.connection-status {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.status-item {
    text-align: center;
    padding: 15px;
    border-radius: 10px;
    background: #f8f9fa;
}

.status-item.healthy { background: #d4edda; color: #155724; }
.status-item.warning { background: #fff3cd; color: #856404; }
.status-item.error { background: #f8d7da; color: #721c24; }

.refresh-button {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #667eea;
    color: white;
    border: none;
    font-size: 20px;
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.refresh-button:hover {
    background: #5a6fd8;
    transform: scale(1.1);
}

.refresh-button.spinning {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .realtime-dashboard {
        padding: 10px;
    }
    
    .dashboard-header {
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .metric-card,
    .chart-container,
    .activity-feed,
    .online-users,
    .connection-status {
        padding: 15px;
        margin-bottom: 15px;
    }
    
    .metric-value {
        font-size: 2rem;
    }
}
</style>
@endsection

@section('content')
<div class="realtime-dashboard">
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="mb-2">Real-time Dashboard</h1>
                        <p class="mb-0">Monitor live system metrics and user activity</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="d-flex align-items-center justify-content-md-end">
                            <span class="status-indicator status-online" id="connection-status"></span>
                            <span id="connection-text">Connected</span>
                        </div>
                        <small class="d-block mt-1" id="last-update">Last updated: --</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- Connection Status -->
            <div class="connection-status">
                <h5 class="mb-3">System Health</h5>
                <div class="status-grid" id="system-health">
                    <div class="status-item healthy">
                        <div><strong>WebSocket</strong></div>
                        <div>Connected</div>
                    </div>
                    <div class="status-item healthy">
                        <div><strong>Database</strong></div>
                        <div>Operational</div>
                    </div>
                    <div class="status-item healthy">
                        <div><strong>Cache</strong></div>
                        <div>Active</div>
                    </div>
                    <div class="status-item healthy">
                        <div><strong>Queue</strong></div>
                        <div>Processing</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Key Metrics -->
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="metric-card">
                                <div class="metric-value" id="online-users">--</div>
                                <div class="metric-label">Online Users</div>
                                <div class="metric-change neutral" id="users-change">--</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <div class="metric-value" id="active-connections">--</div>
                                <div class="metric-label">Connections</div>
                                <div class="metric-change neutral" id="connections-change">--</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <div class="metric-value" id="messages-sent">--</div>
                                <div class="metric-label">Messages/min</div>
                                <div class="metric-change neutral" id="messages-change">--</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <div class="metric-value" id="system-load">--</div>
                                <div class="metric-label">System Load</div>
                                <div class="metric-change neutral" id="load-change">--</div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h5 class="mb-3">User Activity</h5>
                                <canvas id="user-activity-chart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h5 class="mb-3">System Performance</h5>
                                <canvas id="performance-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Online Users -->
                    <div class="online-users">
                        <h5 class="mb-3">Online Users</h5>
                        <div id="online-users-list">
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-spinner fa-spin"></i>
                                <div>Loading...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Feed -->
                    <div class="activity-feed">
                        <h5 class="mb-3">Live Activity</h5>
                        <div id="activity-feed">
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-spinner fa-spin"></i>
                                <div>Loading...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Refresh Button -->
    <button class="refresh-button" id="refresh-btn" title="Refresh Data">
        <i class="fas fa-sync-alt"></i>
    </button>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
class RealTimeDashboard {
    constructor() {
        this.websocket = null;
        this.charts = {};
        this.updateInterval = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        
        this.init();
    }
    
    init() {
        this.setupWebSocket();
        this.setupCharts();
        this.setupEventListeners();
        this.startPeriodicUpdates();
        this.loadInitialData();
    }
    
    setupWebSocket() {
        try {
            // Initialize WebSocket connection
            this.websocket = new WebSocket(`ws://${window.location.host}/ws`);
            
            this.websocket.onopen = () => {
                console.log('WebSocket connected');
                this.updateConnectionStatus(true);
                this.reconnectAttempts = 0;
            };
            
            this.websocket.onmessage = (event) => {
                const data = JSON.parse(event.data);
                this.handleWebSocketMessage(data);
            };
            
            this.websocket.onclose = () => {
                console.log('WebSocket disconnected');
                this.updateConnectionStatus(false);
                this.attemptReconnect();
            };
            
            this.websocket.onerror = (error) => {
                console.error('WebSocket error:', error);
                this.updateConnectionStatus(false);
            };
            
        } catch (error) {
            console.error('WebSocket setup error:', error);
            this.updateConnectionStatus(false);
        }
    }
    
    handleWebSocketMessage(data) {
        switch (data.type) {
            case 'metrics_update':
                this.updateMetrics(data.metrics);
                break;
            case 'user_activity':
                this.updateUserActivity(data.activity);
                break;
            case 'system_health':
                this.updateSystemHealth(data.health);
                break;
            case 'notification':
                this.showNotification(data.notification);
                break;
        }
    }
    
    setupCharts() {
        // User Activity Chart
        const userActivityCtx = document.getElementById('user-activity-chart').getContext('2d');
        this.charts.userActivity = new Chart(userActivityCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Online Users',
                    data: [],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Performance Chart
        const performanceCtx = document.getElementById('performance-chart').getContext('2d');
        this.charts.performance = new Chart(performanceCtx, {
            type: 'doughnut',
            data: {
                labels: ['CPU', 'Memory', 'Disk'],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffce56']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
    
    setupEventListeners() {
        document.getElementById('refresh-btn').addEventListener('click', () => {
            this.refreshData();
        });
        
        // Handle page visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseUpdates();
            } else {
                this.resumeUpdates();
            }
        });
    }
    
    startPeriodicUpdates() {
        this.updateInterval = setInterval(() => {
            this.fetchMetrics();
        }, 30000); // Update every 30 seconds
    }
    
    async loadInitialData() {
        try {
            await this.fetchMetrics();
            await this.fetchOnlineUsers();
            await this.fetchActivityFeed();
        } catch (error) {
            console.error('Failed to load initial data:', error);
        }
    }
    
    async fetchMetrics() {
        try {
            const response = await fetch('/api/realtime/metrics');
            const data = await response.json();
            
            if (data.success) {
                this.updateMetrics(data.data);
            }
        } catch (error) {
            console.error('Failed to fetch metrics:', error);
        }
    }
    
    async fetchOnlineUsers() {
        try {
            const response = await fetch('/api/realtime/online-users');
            const data = await response.json();
            
            if (data.success) {
                this.updateOnlineUsers(data.data);
            }
        } catch (error) {
            console.error('Failed to fetch online users:', error);
        }
    }
    
    async fetchActivityFeed() {
        try {
            const response = await fetch('/api/realtime/activity');
            const data = await response.json();
            
            if (data.success) {
                this.updateActivityFeed(data.data);
            }
        } catch (error) {
            console.error('Failed to fetch activity feed:', error);
        }
    }
    
    updateMetrics(metrics) {
        // Update metric cards
        document.getElementById('online-users').textContent = metrics.online_users || '--';
        document.getElementById('active-connections').textContent = metrics.connections || '--';
        document.getElementById('messages-sent').textContent = metrics.messages_per_minute || '--';
        document.getElementById('system-load').textContent = (metrics.system_load || 0).toFixed(1) + '%';
        
        // Update charts
        this.updateCharts(metrics);
        
        // Update last update time
        document.getElementById('last-update').textContent = `Last updated: ${new Date().toLocaleTimeString()}`;
    }
    
    updateCharts(metrics) {
        // Update user activity chart
        const now = new Date().toLocaleTimeString();
        const chart = this.charts.userActivity;
        
        chart.data.labels.push(now);
        chart.data.datasets[0].data.push(metrics.online_users || 0);
        
        // Keep only last 20 data points
        if (chart.data.labels.length > 20) {
            chart.data.labels.shift();
            chart.data.datasets[0].data.shift();
        }
        
        chart.update('none');
        
        // Update performance chart
        this.charts.performance.data.datasets[0].data = [
            metrics.cpu_usage || 0,
            metrics.memory_usage || 0,
            metrics.disk_usage || 0
        ];
        this.charts.performance.update('none');
    }
    
    updateOnlineUsers(users) {
        const container = document.getElementById('online-users-list');
        
        if (!users || users.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-3">No users online</div>';
            return;
        }
        
        const html = users.map(user => `
            <div class="user-item">
                <div class="user-avatar">
                    ${user.avatar ? `<img src="${user.avatar}" alt="${user.name}">` : user.name.charAt(0)}
                </div>
                <div class="user-info">
                    <div class="user-name">${user.name}</div>
                    <div class="user-status">${user.status || 'Online'}</div>
                </div>
            </div>
        `).join('');
        
        container.innerHTML = html;
    }
    
    updateActivityFeed(activities) {
        const container = document.getElementById('activity-feed');
        
        if (!activities || activities.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-3">No recent activity</div>';
            return;
        }
        
        const html = activities.map(activity => `
            <div class="activity-item">
                <div class="activity-icon ${activity.type}">
                    <i class="${activity.icon}"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">${activity.title}</div>
                    <div class="activity-time">${activity.time}</div>
                </div>
            </div>
        `).join('');
        
        container.innerHTML = html;
    }
    
    updateConnectionStatus(connected) {
        const indicator = document.getElementById('connection-status');
        const text = document.getElementById('connection-text');
        
        if (connected) {
            indicator.className = 'status-indicator status-online';
            text.textContent = 'Connected';
        } else {
            indicator.className = 'status-indicator status-offline';
            text.textContent = 'Disconnected';
        }
    }
    
    attemptReconnect() {
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++;
            setTimeout(() => {
                console.log(`Reconnection attempt ${this.reconnectAttempts}`);
                this.setupWebSocket();
            }, 5000 * this.reconnectAttempts);
        }
    }
    
    refreshData() {
        const btn = document.getElementById('refresh-btn');
        btn.classList.add('spinning');
        
        Promise.all([
            this.fetchMetrics(),
            this.fetchOnlineUsers(),
            this.fetchActivityFeed()
        ]).finally(() => {
            btn.classList.remove('spinning');
        });
    }
    
    pauseUpdates() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
        }
    }
    
    resumeUpdates() {
        this.startPeriodicUpdates();
        this.fetchMetrics();
    }
    
    showNotification(notification) {
        // Show toast notification
        console.log('Notification:', notification);
    }
}

// Initialize dashboard when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new RealTimeDashboard();
});
</script>
@endsection
