@extends('admin.layouts.app')

@section('title', 'Notification Analytics Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-line mr-2"></i>
                Notification Analytics Dashboard
            </h1>
            <p class="text-muted mb-0">Comprehensive notification performance and engagement metrics</p>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary" id="refreshData">
                <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>
            <button type="button" class="btn btn-outline-success" id="exportData">
                <i class="fas fa-download mr-1"></i> Export
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="analyticsFilters" class="row g-3">
                <div class="col-md-3">
                    <label for="startDate" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="startDate" name="start_date" 
                           value="{{ $filters['start_date']->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="endDate" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="endDate" name="end_date" 
                           value="{{ $filters['end_date']->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="notificationType" class="form-label">Notification Type</label>
                    <select class="form-control" id="notificationType" name="type">
                        <option value="">All Types</option>
                        <option value="thread_created">Thread Created</option>
                        <option value="thread_replied">Thread Replied</option>
                        <option value="comment_mention">Comment Mention</option>
                        <option value="product_out_of_stock">Product Out of Stock</option>
                        <option value="order_status_changed">Order Status Changed</option>
                        <option value="review_received">Review Received</option>
                        <option value="wishlist_available">Wishlist Available</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter mr-1"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Notifications Sent
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSent">
                                {{ number_format($analytics['overview']['total_sent']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Read Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="readRate">
                                {{ $analytics['overview']['read_rate'] }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Unique Recipients
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="uniqueRecipients">
                                {{ number_format($analytics['overview']['unique_recipients']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Avg Time to Read
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgTimeToRead">
                                {{ $analytics['overview']['avg_time_to_read'] }} min
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Daily Trends Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Daily Notification Trends</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Chart Options:</div>
                            <a class="dropdown-item" href="#" onclick="toggleChartType('daily')">Toggle Chart Type</a>
                            <a class="dropdown-item" href="#" onclick="exportChart('daily')">Export Chart</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="dailyTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Notification Types -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top Notification Types</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="topTypesChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Forum
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Marketplace
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Security
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Engagement & Performance Row -->
    <div class="row mb-4">
        <!-- Engagement by Priority -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Engagement by Priority</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="engagementTable">
                            <thead>
                                <tr>
                                    <th>Priority</th>
                                    <th>Total</th>
                                    <th>Read</th>
                                    <th>Read Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['engagement']['engagement_by_priority'] as $priority)
                                <tr>
                                    <td>
                                        <span class="badge badge-{{ $priority['priority'] === 'high' ? 'danger' : ($priority['priority'] === 'normal' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($priority['priority']) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($priority['total']) }}</td>
                                    <td>{{ number_format($priority['read']) }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $priority['read_rate'] }}%"
                                                 aria-valuenow="{{ $priority['read_rate'] }}" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                {{ $priority['read_rate'] }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Metrics -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Delivery Metrics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 font-weight-bold text-success">
                                    {{ $analytics['delivery']['delivery_success_rate'] }}%
                                </div>
                                <div class="text-xs text-uppercase text-success font-weight-bold">
                                    Success Rate
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 font-weight-bold text-info">
                                    {{ number_format($analytics['delivery']['real_time_delivered']) }}
                                </div>
                                <div class="text-xs text-uppercase text-info font-weight-bold">
                                    Real-time Delivered
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 font-weight-bold text-warning">
                                    {{ number_format($analytics['delivery']['offline_delivered']) }}
                                </div>
                                <div class="text-xs text-uppercase text-warning font-weight-bold">
                                    Offline Delivered
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 font-weight-bold text-danger">
                                    {{ number_format($analytics['delivery']['email_fallback']) }}
                                </div>
                                <div class="text-xs text-uppercase text-danger font-weight-bold">
                                    Email Fallback
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Segments -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Segment Analysis</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- By Role -->
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Engagement by User Role</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Role</th>
                                            <th>Total</th>
                                            <th>Read Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analytics['user_segments']['by_role'] as $role)
                                        <tr>
                                            <td>{{ ucfirst($role['role']) }}</td>
                                            <td>{{ number_format($role['total']) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $role['read_rate'] > 70 ? 'success' : ($role['read_rate'] > 50 ? 'warning' : 'danger') }}">
                                                    {{ $role['read_rate'] }}%
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Active vs Inactive -->
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Active vs Inactive Users</h6>
                            <div class="row">
                                <div class="col-6">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <div class="h5">{{ $analytics['user_segments']['active_users']['read_rate'] }}%</div>
                                            <div class="small">Active Users Read Rate</div>
                                            <div class="small">({{ number_format($analytics['user_segments']['active_users']['total']) }} notifications)</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-secondary text-white">
                                        <div class="card-body text-center">
                                            <div class="h5">{{ $analytics['user_segments']['inactive_users']['read_rate'] }}%</div>
                                            <div class="small">Inactive Users Read Rate</div>
                                            <div class="small">({{ number_format($analytics['user_segments']['inactive_users']['total']) }} notifications)</div>
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
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Analytics Data</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="form-group">
                        <label for="exportFormat">Export Format</label>
                        <select class="form-control" id="exportFormat" name="format">
                            <option value="csv">CSV</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeFilters" checked>
                            <label class="form-check-label" for="includeFilters">
                                Include current filters
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmExport">Export</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Analytics Dashboard JavaScript
$(document).ready(function() {
    // Initialize charts
    initializeCharts();
    
    // Auto-refresh every 5 minutes
    setInterval(refreshData, 300000);
    
    // Event handlers
    $('#analyticsFilters').on('submit', handleFilterSubmit);
    $('#refreshData').on('click', refreshData);
    $('#exportData').on('click', showExportModal);
    $('#confirmExport').on('click', handleExport);
});

function initializeCharts() {
    // Daily trends chart
    const dailyCtx = document.getElementById('dailyTrendsChart').getContext('2d');
    const dailyData = @json($analytics['trends']['daily']);
    
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [{
                label: 'Total Notifications',
                data: dailyData.map(d => d.total),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true
            }, {
                label: 'Read Notifications',
                data: dailyData.map(d => d.read),
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                fill: true
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

    // Top types chart
    const typesCtx = document.getElementById('topTypesChart').getContext('2d');
    const typesData = @json($analytics['top_types']);
    
    new Chart(typesCtx, {
        type: 'doughnut',
        data: {
            labels: typesData.map(t => t.type.replace('_', ' ').toUpperCase()),
            datasets: [{
                data: typesData.map(t => t.total),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function handleFilterSubmit(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const params = new URLSearchParams(formData);
    window.location.href = '{{ route("admin.analytics.notifications.index") }}?' + params.toString();
}

function refreshData() {
    location.reload();
}

function showExportModal() {
    $('#exportModal').modal('show');
}

function handleExport() {
    const format = $('#exportFormat').val();
    const includeFilters = $('#includeFilters').is(':checked');
    
    let url = '{{ route("admin.analytics.notifications.export") }}?format=' + format;
    
    if (includeFilters) {
        const currentParams = new URLSearchParams(window.location.search);
        url += '&' + currentParams.toString();
    }
    
    window.open(url, '_blank');
    $('#exportModal').modal('hide');
}

function toggleChartType(chartId) {
    // Implementation for toggling chart types
    console.log('Toggle chart type for:', chartId);
}

function exportChart(chartId) {
    // Implementation for exporting individual charts
    console.log('Export chart:', chartId);
}
</script>
@endsection
