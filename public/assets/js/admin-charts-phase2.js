/**
 * MechaMap Admin Charts - Phase 2
 * Enhanced interactive charts with real-time updates
 */

class MechaMapCharts {
    constructor() {
        this.charts = {};
        this.colors = {
            primary: '#667eea',
            success: '#22c55e',
            info: '#0ea5e9',
            warning: '#f59e0b',
            danger: '#ef4444',
            marketplace: {
                primary: '#667eea',
                success: '#f093fb',
                info: '#4facfe',
                warning: '#43e97b'
            }
        };
        this.init();
    }

    init() {
        this.setupChartDefaults();
        this.createMetricsCharts();
        this.createTrendCharts();
        this.setupAutoRefresh();
    }

    setupChartDefaults() {
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#6b7280';
        Chart.defaults.plugins.legend.display = false;
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;
    }

    /**
     * Create enhanced donut charts for metrics cards
     */
    createMetricsCharts() {
        // Community Metrics Charts
        this.createDonutChart('usersDonutChart', {
            data: [61, 39],
            labels: ['Active Users', 'Inactive'],
            colors: [this.colors.primary, '#e5e7eb']
        });

        this.createDonutChart('threadsDonutChart', {
            data: [118, 82],
            labels: ['Published', 'Draft'],
            colors: [this.colors.success, '#e5e7eb']
        });

        this.createDonutChart('commentsDonutChart', {
            data: [359, 141],
            labels: ['Approved', 'Pending'],
            colors: [this.colors.info, '#e5e7eb']
        });

        this.createDonutChart('activityDonutChart', {
            data: [33, 67],
            labels: ['This Week', 'Previous'],
            colors: [this.colors.warning, '#e5e7eb']
        });

        // Marketplace Metrics Charts
        this.createDonutChart('revenueDonutChart', {
            data: [75, 25],
            labels: ['This Month', 'Previous'],
            colors: [this.colors.marketplace.primary, '#e5e7eb']
        });

        this.createDonutChart('ordersDonutChart', {
            data: [23, 77],
            labels: ['Pending', 'Completed'],
            colors: [this.colors.marketplace.success, '#e5e7eb']
        });

        this.createDonutChart('productsDonutChart', {
            data: [8, 92],
            labels: ['Pending', 'Approved'],
            colors: [this.colors.marketplace.info, '#e5e7eb']
        });

        this.createDonutChart('commissionDonutChart', {
            data: [60, 40],
            labels: ['Unpaid', 'Paid'],
            colors: [this.colors.marketplace.warning, '#e5e7eb']
        });
    }

    /**
     * Create donut chart
     */
    createDonutChart(canvasId, config) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        
        this.charts[canvasId] = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: config.labels,
                datasets: [{
                    data: config.data,
                    backgroundColor: config.colors,
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: config.colors[0],
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${percentage}%`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    duration: 1000
                }
            }
        });
    }

    /**
     * Create trend charts for analytics
     */
    createTrendCharts() {
        this.createUserTrendChart();
        this.createContentTrendChart();
        this.createRevenueTrendChart();
    }

    /**
     * Create user trend chart
     */
    createUserTrendChart() {
        const canvas = document.getElementById('userTrendChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        
        // Mock data for 12 months
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const userData = [45, 48, 52, 55, 58, 61, 59, 62, 65, 63, 66, 61];

        this.charts['userTrendChart'] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Users',
                    data: userData,
                    borderColor: this.colors.primary,
                    backgroundColor: this.colors.primary + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: this.colors.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: this.colors.primary,
                        borderWidth: 1,
                        cornerRadius: 6
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    /**
     * Create content trend chart
     */
    createContentTrendChart() {
        const canvas = document.getElementById('contentTrendChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const threadsData = [95, 102, 108, 112, 115, 118, 116, 119, 122, 120, 123, 118];
        const commentsData = [280, 295, 315, 325, 340, 359, 350, 365, 375, 370, 380, 359];

        this.charts['contentTrendChart'] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Threads',
                        data: threadsData,
                        backgroundColor: this.colors.success + '80',
                        borderColor: this.colors.success,
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Comments',
                        data: commentsData,
                        backgroundColor: this.colors.info + '80',
                        borderColor: this.colors.info,
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 6
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    /**
     * Create revenue trend chart (Phase 2 feature)
     */
    createRevenueTrendChart() {
        const canvas = document.getElementById('revenueTrendChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const revenueData = [85, 92, 105, 118, 125, 135, 128, 142, 155, 148, 162, 125];

        this.charts['revenueTrendChart'] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Revenue (Million VND)',
                    data: revenueData,
                    borderColor: this.colors.marketplace.primary,
                    backgroundColor: this.colors.marketplace.primary + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: this.colors.marketplace.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            color: '#6b7280',
                            callback: function(value) {
                                return value + 'M';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: this.colors.marketplace.primary,
                        borderWidth: 1,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ' + context.parsed.y + 'M VND';
                            }
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    /**
     * Setup auto-refresh for real-time updates
     */
    setupAutoRefresh() {
        // Refresh charts every 5 minutes
        setInterval(() => {
            this.refreshChartData();
        }, 300000); // 5 minutes
    }

    /**
     * Refresh chart data (Phase 2.2 feature)
     */
    refreshChartData() {
        // This will be implemented in Phase 2.2 with AJAX calls
        console.log('Refreshing chart data...');
    }

    /**
     * Update chart data
     */
    updateChart(chartId, newData) {
        if (this.charts[chartId]) {
            this.charts[chartId].data.datasets[0].data = newData;
            this.charts[chartId].update('active');
        }
    }

    /**
     * Destroy all charts
     */
    destroy() {
        Object.values(this.charts).forEach(chart => {
            chart.destroy();
        });
        this.charts = {};
    }
}

// Initialize charts when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.mechaMapCharts = new MechaMapCharts();
});
