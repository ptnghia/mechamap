@extends('admin.layouts.dason')

@section('title', 'Real-time Analytics Dashboard')

@section('css')
<link href="{{ asset('assets/libs/apexcharts/apexcharts.min.css') }}" rel="stylesheet" type="text/css" />
<style>
.realtime-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    background-color: #28a745;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.metric-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.metric-card.positive {
    border-left-color: #28a745;
}

.metric-card.negative {
    border-left-color: #dc3545;
}

.metric-card.neutral {
    border-left-color: #ffc107;
}

.kpi-widget {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}

.chart-container {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}

.status-online { background-color: #28a745; }
.status-warning { background-color: #ffc107; }
.status-offline { background-color: #dc3545; }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">
                <span class="realtime-indicator"></span>
                Real-time Analytics Dashboard
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Real-time Analytics</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Real-time Metrics Row -->
<div class="row" id="realtime-metrics">
    <div class="col-xl-3 col-md-6">
        <div class="card metric-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block">Online Users</span>
                        <h4 class="mb-3">
                            <span class="counter-value" id="online-users">{{ $realTimeData['system']['online_users'] }}</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="status-indicator status-online"></span>
                            <span class="text-muted font-size-13">Active now</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card metric-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block">Today Revenue</span>
                        <h4 class="mb-3">
                            $<span class="counter-value" id="today-revenue">{{ number_format($realTimeData['business']['today_revenue'], 2) }}</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-success text-success">+12.5%</span>
                            <span class="text-muted font-size-13">vs yesterday</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card metric-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block">Today Orders</span>
                        <h4 class="mb-3">
                            <span class="counter-value" id="today-orders">{{ $realTimeData['business']['today_orders'] }}</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-info text-info">{{ $realTimeData['business']['pending_orders'] }}</span>
                            <span class="text-muted font-size-13">pending</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-shopping-cart text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card metric-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block">Conversion Rate</span>
                        <h4 class="mb-3">
                            <span class="counter-value" id="conversion-rate">{{ $realTimeData['business']['conversion_rate'] }}</span>%
                        </h4>
                        <div class="text-nowrap">
                            <span class="status-indicator status-online"></span>
                            <span class="text-muted font-size-13">Excellent</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Dashboard Row -->
<div class="row">
    @foreach($kpiData as $kpi => $data)
    <div class="col-xl-3 col-md-6">
        <div class="kpi-widget">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-2">{{ ucfirst($kpi) }}</h6>
                    <h3 class="text-white mb-0">${{ number_format($data['current']) }}</h3>
                    <small class="text-white-50">Target: ${{ number_format($data['target']) }}</small>
                </div>
                <div class="text-end">
                    <div class="text-white mb-2">
                        @if($data['trend'] > 0)
                            <i class="fas fa-arrow-up"></i> +{{ $data['trend'] }}%
                        @elseif($data['trend'] < 0)
                            <i class="fas fa-arrow-down"></i> {{ $data['trend'] }}%
                        @else
                            <i class="fas fa-minus"></i> 0%
                        @endif
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: {{ min(($data['current'] / $data['target']) * 100, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Charts Row -->
<div class="row">
    <div class="col-xl-8">
        <div class="chart-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Revenue Timeline (24h)</h5>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary active" data-period="24h">24H</button>
                    <button type="button" class="btn btn-outline-primary" data-period="7d">7D</button>
                    <button type="button" class="btn btn-outline-primary" data-period="30d">30D</button>
                </div>
            </div>
            <div id="revenue-timeline-chart"></div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="chart-container">
            <h5 class="card-title mb-4">System Status</h5>
            <div class="system-status">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Server Load</span>
                    <span class="badge bg-success">{{ $realTimeData['system']['server_load'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Memory Usage</span>
                    <span class="badge bg-warning">{{ $realTimeData['system']['memory_usage'] }}MB</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Active Sessions</span>
                    <span class="badge bg-info">{{ $realTimeData['system']['active_sessions'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Pending Approvals</span>
                    <span class="badge bg-danger">{{ $realTimeData['content']['pending_approvals'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Feed Row -->
<div class="row">
    <div class="col-xl-6">
        <div class="chart-container">
            <h5 class="card-title mb-4">User Activity (24h)</h5>
            <div id="user-activity-chart"></div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="chart-container">
            <h5 class="card-title mb-4">Content Engagement</h5>
            <div class="row text-center">
                <div class="col-4">
                    <h4 class="text-primary">{{ $realTimeData['content']['today_threads'] }}</h4>
                    <p class="text-muted mb-0">New Threads</p>
                </div>
                <div class="col-4">
                    <h4 class="text-success">{{ $realTimeData['content']['today_comments'] }}</h4>
                    <p class="text-muted mb-0">New Comments</p>
                </div>
                <div class="col-4">
                    <h4 class="text-info">{{ $realTimeData['content']['content_engagement'] }}</h4>
                    <p class="text-muted mb-0">Engagement Rate</p>
                </div>
            </div>
            <div id="content-engagement-chart" class="mt-4"></div>
        </div>
    </div>
</div>

<!-- Marketplace Insights -->
<div class="row">
    <div class="col-12">
        <div class="chart-container">
            <h5 class="card-title mb-4">Marketplace Insights</h5>
            <div class="row">
                <div class="col-md-3 text-center">
                    <h4 class="text-primary">{{ $realTimeData['marketplace']['active_products'] }}</h4>
                    <p class="text-muted">Active Products</p>
                </div>
                <div class="col-md-3 text-center">
                    <h4 class="text-success">{{ $realTimeData['marketplace']['today_listings'] }}</h4>
                    <p class="text-muted">New Listings Today</p>
                </div>
                <div class="col-md-3 text-center">
                    <h4 class="text-info">{{ $realTimeData['marketplace']['seller_activity'] }}</h4>
                    <p class="text-muted">Active Sellers (24h)</p>
                </div>
                <div class="col-md-3 text-center">
                    <h4 class="text-warning">{{ $realTimeData['marketplace']['inventory_alerts'] }}</h4>
                    <p class="text-muted">Low Stock Alerts</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize real-time updates
    initRealtimeUpdates();
    
    // Initialize charts
    initRevenueChart();
    initUserActivityChart();
    initContentEngagementChart();
    
    // Auto-refresh every 30 seconds
    setInterval(updateRealtimeMetrics, 30000);
});

function initRealtimeUpdates() {
    // WebSocket connection for real-time updates
    // Implement WebSocket connection here
    console.log('Real-time updates initialized');
}

function updateRealtimeMetrics() {
    fetch('{{ route("admin.analytics.realtime.metrics") }}')
        .then(response => response.json())
        .then(data => {
            // Update metrics
            document.getElementById('online-users').textContent = data.system.online_users;
            document.getElementById('today-revenue').textContent = parseFloat(data.business.today_revenue).toFixed(2);
            document.getElementById('today-orders').textContent = data.business.today_orders;
            document.getElementById('conversion-rate').textContent = data.business.conversion_rate;
            
            // Update timestamp
            document.querySelector('.realtime-indicator').title = 'Last updated: ' + new Date().toLocaleTimeString();
        })
        .catch(error => console.error('Error updating metrics:', error));
}

function initRevenueChart() {
    const options = {
        series: [{
            name: 'Revenue',
            data: @json($chartData['revenue_timeline'])
        }],
        chart: {
            type: 'area',
            height: 350,
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
        },
        colors: ['#1c84ee'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
            }
        },
        xaxis: {
            categories: @json(array_column($chartData['revenue_timeline'], 'time'))
        },
        yaxis: {
            labels: {
                formatter: function(value) {
                    return '$' + value.toFixed(0);
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#revenue-timeline-chart"), options);
    chart.render();
}

function initUserActivityChart() {
    const options = {
        series: [{
            name: 'Active Users',
            data: @json(array_column($chartData['user_activity'], 'value'))
        }],
        chart: {
            type: 'line',
            height: 300
        },
        colors: ['#33c38e'],
        xaxis: {
            categories: @json(array_column($chartData['user_activity'], 'time'))
        }
    };

    const chart = new ApexCharts(document.querySelector("#user-activity-chart"), options);
    chart.render();
}

function initContentEngagementChart() {
    const options = {
        series: [{{ $realTimeData['content']['today_threads'] }}, {{ $realTimeData['content']['today_comments'] }}],
        chart: {
            type: 'donut',
            height: 200
        },
        labels: ['Threads', 'Comments'],
        colors: ['#1c84ee', '#33c38e']
    };

    const chart = new ApexCharts(document.querySelector("#content-engagement-chart"), options);
    chart.render();
}
</script>
@endsection
