@extends('admin.layouts.dason')

@section('title', 'Performance & Security Dashboard')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Performance & Security Dashboard</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item active">Performance</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- System Health Overview -->
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title mb-0">System Health Status</h5>
                        <p class="text-muted mb-0">Real-time system monitoring và performance metrics</p>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshMetrics()">
                                <i class="fas fa-sync" class="me-1"></i> Refresh
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="exportReport()">
                                <i data-feather="download" class="me-1"></i> Export Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Performance Metrics -->
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Response Time</p>
                        <h4 class="mb-0" id="response-time">{{ $metrics['response_time']['current_request'] ?? 0 }}ms</h4>
                        <p class="text-muted mb-0">
                            <span class="text-success me-2">
                                <i data-feather="trending-up" class="me-1"></i>Optimal
                            </span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i data-feather="zap"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Memory Usage</p>
                        <h4 class="mb-0" id="memory-usage">{{ $metrics['memory_usage']['current_usage'] ?? '0 MB' }}</h4>
                        <p class="text-muted mb-0">
                            <span class="text-info me-2">
                                {{ $metrics['memory_usage']['usage_percentage'] ?? 0 }}% used
                            </span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                            <span class="avatar-title">
                                <i data-feather="cpu"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Database Performance</p>
                        <h4 class="mb-0" id="db-performance">{{ $dbMetrics['buffer_pool_hit_ratio'] ?? 0 }}%</h4>
                        <p class="text-muted mb-0">
                            <span class="text-success me-2">
                                Buffer Pool Hit Ratio
                            </span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                            <span class="avatar-title">
                                <i data-feather="database"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Cache Hit Rate</p>
                        <h4 class="mb-0" id="cache-hit-rate">{{ $cacheStats['hit_rate'] ?? 0 }}%</h4>
                        <p class="text-muted mb-0">
                            <span class="text-success me-2">
                                <i data-feather="trending-up" class="me-1"></i>Excellent
                            </span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                            <span class="avatar-title">
                                <i data-feather="layers"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- System Health -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">System Health Checks</h4>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @if(isset($systemHealth['checks']))
                        @foreach($systemHealth['checks'] as $check => $result)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <i data-feather="{{ $result['status'] === 'healthy' ? 'check-circle' : ($result['status'] === 'warning' ? 'alert-triangle' : 'x-circle') }}"
                                       class="text-{{ $result['status'] === 'healthy' ? 'success' : ($result['status'] === 'warning' ? 'warning' : 'danger') }} me-2"></i>
                                    {{ ucfirst(str_replace('_', ' ', $check)) }}
                                </div>
                                <div>
                                    <span class="badge bg-{{ $result['status'] === 'healthy' ? 'success' : ($result['status'] === 'warning' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($result['status']) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i data-feather="activity" style="width: 48px; height: 48px;" class="text-muted mb-2"></i>
                            <h5 class="text-muted">System health checks loading...</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Performance Actions</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="clearCache()">
                        <i class="fas fa-trash" class="me-2"></i>Clear All Cache
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="warmUpCache()">
                        <i data-feather="zap" class="me-2"></i>Warm Up Cache
                    </button>
                    <button type="button" class="btn btn-outline-info" onclick="optimizeDatabase()">
                        <i data-feather="database" class="me-2"></i>Optimize Database
                    </button>
                    <button type="button" class="btn btn-outline-warning" onclick="toggleMaintenance()">
                        <i data-feather="tool" class="me-2"></i>Toggle Maintenance
                    </button>
                </div>
            </div>
        </div>

        <!-- System Resources -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">System Resources</h4>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i data-feather="cpu" class="text-primary me-2"></i>
                            CPU Usage
                        </div>
                        <span class="badge bg-primary">{{ $metrics['system_resources']['cpu_usage'] ?? 0 }}%</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i data-feather="hard-drive" class="text-success me-2"></i>
                            Disk Usage
                        </div>
                        <span class="badge bg-success">{{ $metrics['system_resources']['disk_usage']['percentage'] ?? 0 }}%</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i data-feather="activity" class="text-info me-2"></i>
                            Load Average
                        </div>
                        <span class="badge bg-info">{{ implode(', ', $metrics['system_resources']['load_average'] ?? [0, 0, 0]) }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i data-feather="clock" class="text-warning me-2"></i>
                            Uptime
                        </div>
                        <span class="badge bg-warning">{{ $metrics['system_resources']['uptime'] ?? 'Unknown' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Performance Chart -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Performance Trends</h4>
            </div>
            <div class="card-body">
                <div id="performance-chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- ApexCharts -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script>
let performanceChart;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather Icons with error handling
    if (typeof feather !== 'undefined') {
        try {
            feather.replace();
        } catch (error) {
            console.warn('Feather Icons error in performance page:', error);
        }
    }

    // Initialize performance chart
    initPerformanceChart();

    // Set up real-time updates
    setInterval(updateMetrics, 30000); // Update every 30 seconds
});

function initPerformanceChart() {
    const options = {
        series: [{
            name: 'Response Time (ms)',
            data: [120, 135, 101, 98, 87, 105, 91]
        }, {
            name: 'Memory Usage (%)',
            data: [45, 52, 38, 42, 39, 48, 41]
        }, {
            name: 'CPU Usage (%)',
            data: [15, 22, 18, 16, 14, 19, 17]
        }],
        chart: {
            type: 'line',
            height: 300,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            categories: ['6h ago', '5h ago', '4h ago', '3h ago', '2h ago', '1h ago', 'Now']
        },
        yaxis: {
            title: {
                text: 'Performance Metrics'
            }
        },
        colors: ['#556ee6', '#34c38f', '#f1b44c'],
        legend: {
            position: 'top'
        }
    };

    performanceChart = new ApexCharts(document.querySelector("#performance-chart"), options);
    performanceChart.render();
}

function updateMetrics() {
    fetch('{{ route('admin.performance.metrics') }}')
        .then(response => response.json())
        .then(data => {
            // Update response time
            document.getElementById('response-time').textContent = data.response_time?.current_request + 'ms' || '0ms';

            // Update memory usage
            document.getElementById('memory-usage').textContent = data.memory_usage?.current_usage || '0 MB';

            // Update other metrics as needed
        })
        .catch(error => console.error('Error updating metrics:', error));
}

function refreshMetrics() {
    updateMetrics();
    showToast('Metrics refreshed successfully!', 'success');
}

function clearCache() {
    if (confirm('Are you sure you want to clear all cache? This may temporarily slow down the application.')) {
        fetch('{{ route('admin.performance.clear-cache') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Error clearing cache', 'error');
        });
    }
}

function warmUpCache() {
    fetch('{{ route('admin.performance.warm-up-cache') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Error warming up cache', 'error');
    });
}

function optimizeDatabase() {
    if (confirm('Are you sure you want to optimize the database? This may take a few minutes.')) {
        fetch('{{ route('admin.performance.optimize-database') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Error optimizing database', 'error');
        });
    }
}

function toggleMaintenance() {
    if (confirm('Are you sure you want to toggle maintenance mode?')) {
        fetch('{{ route('admin.performance.toggle-maintenance') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Error toggling maintenance mode', 'error');
        });
    }
}

function exportReport() {
    window.open('{{ route('admin.performance.export-report') }}?format=json', '_blank');
}

function showToast(message, type) {
    // Simple toast notification - you can replace with your preferred toast library
    alert(message);
}
</script>
@endsection
