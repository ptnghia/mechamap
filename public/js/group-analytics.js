/**
 * MechaMap Group Analytics
 * Advanced analytics and statistics for group management
 */

class GroupAnalytics {
    constructor(options = {}) {
        this.options = {
            refreshInterval: 30000, // 30 seconds
            chartColors: {
                primary: '#0d6efd',
                success: '#198754',
                warning: '#ffc107',
                danger: '#dc3545',
                info: '#0dcaf0'
            },
            ...options
        };

        this.charts = {};
        this.refreshTimer = null;
        this.currentGroupId = null;

        this.init();
    }

    init() {
        this.bindEvents();
        this.setupCharts();
        this.startAutoRefresh();
    }

    bindEvents() {
        // Group selection
        $(document).on('change', '#analytics-group-select', this.onGroupChange.bind(this));
        
        // Date range selection
        $(document).on('change', '#analytics-date-range', this.onDateRangeChange.bind(this));
        
        // Refresh button
        $(document).on('click', '.btn-refresh-analytics', this.refreshAnalytics.bind(this));
        
        // Export buttons
        $(document).on('click', '.btn-export-analytics', this.exportAnalytics.bind(this));
        
        // Chart type toggles
        $(document).on('click', '.chart-type-toggle', this.toggleChartType.bind(this));

        // Real-time toggle
        $(document).on('change', '#realtime-toggle', this.toggleRealtime.bind(this));
    }

    /**
     * Handle group selection change
     */
    onGroupChange(event) {
        const groupId = $(event.target).val();
        this.currentGroupId = groupId;
        
        if (groupId) {
            this.loadGroupAnalytics(groupId);
        } else {
            this.clearAnalytics();
        }
    }

    /**
     * Handle date range change
     */
    onDateRangeChange(event) {
        const dateRange = $(event.target).val();
        
        if (this.currentGroupId) {
            this.loadGroupAnalytics(this.currentGroupId, { dateRange });
        }
    }

    /**
     * Load analytics data for specific group
     */
    async loadGroupAnalytics(groupId, options = {}) {
        try {
            this.showLoading();

            const params = new URLSearchParams({
                group_id: groupId,
                date_range: options.dateRange || $('#analytics-date-range').val() || '7d'
            });

            const response = await fetch(`/dashboard/messages/groups/analytics?${params}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.renderAnalytics(data.analytics);
                this.hideLoading();
            } else {
                throw new Error(data.message || 'Không thể tải dữ liệu analytics');
            }

        } catch (error) {
            console.error('Error loading group analytics:', error);
            this.showError('Không thể tải dữ liệu analytics: ' + error.message);
            this.hideLoading();
        }
    }

    /**
     * Render analytics data
     */
    renderAnalytics(analytics) {
        this.renderOverviewStats(analytics.overview);
        this.renderActivityChart(analytics.activity);
        this.renderMemberChart(analytics.members);
        this.renderMessageChart(analytics.messages);
        this.renderEngagementChart(analytics.engagement);
        this.renderTopMembers(analytics.topMembers);
        this.renderRecentActivity(analytics.recentActivity);
    }

    /**
     * Render overview statistics
     */
    renderOverviewStats(overview) {
        const statsHtml = `
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card bg-primary text-white">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3>${overview.totalMembers || 0}</h3>
                            <p>Tổng thành viên</p>
                            <small class="stat-change ${overview.memberChange >= 0 ? 'positive' : 'negative'}">
                                <i class="fas fa-arrow-${overview.memberChange >= 0 ? 'up' : 'down'}"></i>
                                ${Math.abs(overview.memberChange || 0)}% so với kỳ trước
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-success text-white">
                        <div class="stat-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="stat-content">
                            <h3>${overview.totalMessages || 0}</h3>
                            <p>Tổng tin nhắn</p>
                            <small class="stat-change ${overview.messageChange >= 0 ? 'positive' : 'negative'}">
                                <i class="fas fa-arrow-${overview.messageChange >= 0 ? 'up' : 'down'}"></i>
                                ${Math.abs(overview.messageChange || 0)}% so với kỳ trước
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-warning text-white">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-content">
                            <h3>${overview.activeMembers || 0}</h3>
                            <p>Thành viên hoạt động</p>
                            <small class="stat-change ${overview.activityChange >= 0 ? 'positive' : 'negative'}">
                                <i class="fas fa-arrow-${overview.activityChange >= 0 ? 'up' : 'down'}"></i>
                                ${Math.abs(overview.activityChange || 0)}% so với kỳ trước
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-info text-white">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3>${overview.avgResponseTime || 0}m</h3>
                            <p>Thời gian phản hồi TB</p>
                            <small class="stat-change ${overview.responseTimeChange <= 0 ? 'positive' : 'negative'}">
                                <i class="fas fa-arrow-${overview.responseTimeChange <= 0 ? 'down' : 'up'}"></i>
                                ${Math.abs(overview.responseTimeChange || 0)}% so với kỳ trước
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#overview-stats').html(statsHtml);
    }

    /**
     * Render activity chart
     */
    renderActivityChart(activityData) {
        const ctx = document.getElementById('activity-chart');
        if (!ctx) return;

        if (this.charts.activity) {
            this.charts.activity.destroy();
        }

        this.charts.activity = new Chart(ctx, {
            type: 'line',
            data: {
                labels: activityData.labels || [],
                datasets: [{
                    label: 'Tin nhắn',
                    data: activityData.messages || [],
                    borderColor: this.options.chartColors.primary,
                    backgroundColor: this.options.chartColors.primary + '20',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Thành viên hoạt động',
                    data: activityData.activeMembers || [],
                    borderColor: this.options.chartColors.success,
                    backgroundColor: this.options.chartColors.success + '20',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Hoạt động nhóm theo thời gian'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    /**
     * Render member growth chart
     */
    renderMemberChart(memberData) {
        const ctx = document.getElementById('member-chart');
        if (!ctx) return;

        if (this.charts.members) {
            this.charts.members.destroy();
        }

        this.charts.members = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: memberData.labels || [],
                datasets: [{
                    label: 'Thành viên mới',
                    data: memberData.newMembers || [],
                    backgroundColor: this.options.chartColors.success,
                }, {
                    label: 'Thành viên rời đi',
                    data: memberData.leftMembers || [],
                    backgroundColor: this.options.chartColors.danger,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Thay đổi thành viên'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    /**
     * Render message statistics chart
     */
    renderMessageChart(messageData) {
        const ctx = document.getElementById('message-chart');
        if (!ctx) return;

        if (this.charts.messages) {
            this.charts.messages.destroy();
        }

        this.charts.messages = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Tin nhắn văn bản', 'Hình ảnh', 'File đính kèm', 'Khác'],
                datasets: [{
                    data: [
                        messageData.textMessages || 0,
                        messageData.imageMessages || 0,
                        messageData.fileMessages || 0,
                        messageData.otherMessages || 0
                    ],
                    backgroundColor: [
                        this.options.chartColors.primary,
                        this.options.chartColors.success,
                        this.options.chartColors.warning,
                        this.options.chartColors.info
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Phân loại tin nhắn'
                    }
                }
            }
        });
    }

    /**
     * Render engagement chart
     */
    renderEngagementChart(engagementData) {
        const ctx = document.getElementById('engagement-chart');
        if (!ctx) return;

        if (this.charts.engagement) {
            this.charts.engagement.destroy();
        }

        this.charts.engagement = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Tin nhắn', 'Phản hồi', 'Chia sẻ', 'Tương tác', 'Thời gian online'],
                datasets: [{
                    label: 'Mức độ tương tác',
                    data: [
                        engagementData.messages || 0,
                        engagementData.responses || 0,
                        engagementData.shares || 0,
                        engagementData.interactions || 0,
                        engagementData.onlineTime || 0
                    ],
                    borderColor: this.options.chartColors.primary,
                    backgroundColor: this.options.chartColors.primary + '20',
                    pointBackgroundColor: this.options.chartColors.primary,
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: this.options.chartColors.primary
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Mức độ tương tác nhóm'
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }

    /**
     * Render top members list
     */
    renderTopMembers(topMembers) {
        const membersHtml = topMembers.map((member, index) => `
            <div class="top-member-item d-flex align-items-center justify-content-between p-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="rank-badge me-3">
                        <span class="badge bg-${this.getRankColor(index + 1)} rounded-pill">${index + 1}</span>
                    </div>
                    <img src="${member.avatar || this.getDefaultAvatar(member.name)}" 
                         alt="${member.name}" 
                         class="rounded-circle me-3" 
                         width="40" height="40">
                    <div>
                        <h6 class="mb-0">${member.name}</h6>
                        <small class="text-muted">${member.role}</small>
                    </div>
                </div>
                <div class="member-stats text-end">
                    <div class="stat-item">
                        <strong>${member.messageCount || 0}</strong>
                        <small class="text-muted d-block">tin nhắn</small>
                    </div>
                </div>
            </div>
        `).join('');

        $('#top-members-list').html(membersHtml);
    }

    /**
     * Render recent activity feed
     */
    renderRecentActivity(recentActivity) {
        const activityHtml = recentActivity.map(activity => `
            <div class="activity-item d-flex align-items-start p-3 border-bottom">
                <div class="activity-icon me-3">
                    <i class="fas fa-${this.getActivityIcon(activity.type)} text-${this.getActivityColor(activity.type)}"></i>
                </div>
                <div class="activity-content flex-grow-1">
                    <div class="activity-text">${activity.description}</div>
                    <small class="text-muted">${this.formatTime(activity.created_at)}</small>
                </div>
            </div>
        `).join('');

        $('#recent-activity-list').html(activityHtml);
    }

    /**
     * Setup charts initialization
     */
    setupCharts() {
        // Initialize Chart.js defaults
        if (typeof Chart !== 'undefined') {
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#6c757d';
        }
    }

    /**
     * Start auto refresh
     */
    startAutoRefresh() {
        if ($('#realtime-toggle').is(':checked')) {
            this.refreshTimer = setInterval(() => {
                if (this.currentGroupId) {
                    this.loadGroupAnalytics(this.currentGroupId);
                }
            }, this.options.refreshInterval);
        }
    }

    /**
     * Stop auto refresh
     */
    stopAutoRefresh() {
        if (this.refreshTimer) {
            clearInterval(this.refreshTimer);
            this.refreshTimer = null;
        }
    }

    /**
     * Toggle real-time updates
     */
    toggleRealtime(event) {
        if ($(event.target).is(':checked')) {
            this.startAutoRefresh();
        } else {
            this.stopAutoRefresh();
        }
    }

    /**
     * Refresh analytics manually
     */
    refreshAnalytics() {
        if (this.currentGroupId) {
            this.loadGroupAnalytics(this.currentGroupId);
        }
    }

    /**
     * Export analytics data
     */
    async exportAnalytics(event) {
        const format = $(event.target).data('format') || 'pdf';
        
        if (!this.currentGroupId) {
            this.showNotification('Vui lòng chọn nhóm để xuất báo cáo', 'warning');
            return;
        }

        try {
            const response = await fetch('/dashboard/messages/groups/analytics/export', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    group_id: this.currentGroupId,
                    format: format,
                    date_range: $('#analytics-date-range').val()
                })
            });

            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `group-analytics-${this.currentGroupId}-${Date.now()}.${format}`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                this.showNotification('Báo cáo đã được tải xuống', 'success');
            } else {
                throw new Error('Không thể xuất báo cáo');
            }
        } catch (error) {
            console.error('Error exporting analytics:', error);
            this.showNotification('Không thể xuất báo cáo: ' + error.message, 'error');
        }
    }

    /**
     * Utility methods
     */
    getRankColor(rank) {
        switch (rank) {
            case 1: return 'warning'; // Gold
            case 2: return 'secondary'; // Silver
            case 3: return 'dark'; // Bronze
            default: return 'primary';
        }
    }

    getActivityIcon(type) {
        const icons = {
            'message': 'comment',
            'join': 'user-plus',
            'leave': 'user-minus',
            'settings': 'cog',
            'file': 'file',
            'image': 'image'
        };
        return icons[type] || 'circle';
    }

    getActivityColor(type) {
        const colors = {
            'message': 'primary',
            'join': 'success',
            'leave': 'danger',
            'settings': 'warning',
            'file': 'info',
            'image': 'info'
        };
        return colors[type] || 'secondary';
    }

    formatTime(timestamp) {
        return new Date(timestamp).toLocaleString('vi-VN');
    }

    getDefaultAvatar(name) {
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=random`;
    }

    showLoading() {
        $('.analytics-content').addClass('loading');
    }

    hideLoading() {
        $('.analytics-content').removeClass('loading');
    }

    showError(message) {
        $('#analytics-error').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `).show();
    }

    showNotification(message, type = 'info') {
        if (window.showNotification) {
            window.showNotification(message, type);
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }

    clearAnalytics() {
        Object.values(this.charts).forEach(chart => {
            if (chart) chart.destroy();
        });
        this.charts = {};
        
        $('#overview-stats').empty();
        $('#top-members-list').empty();
        $('#recent-activity-list').empty();
    }

    destroy() {
        this.stopAutoRefresh();
        this.clearAnalytics();
    }
}

// Auto-initialize on analytics page
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.group-analytics-container')) {
        window.groupAnalytics = new GroupAnalytics();
        console.log('✅ GroupAnalytics initialized');
    }
});

// Export for manual initialization
window.GroupAnalytics = GroupAnalytics;
