@extends('admin.layouts.dason')

@section('title', 'Analytics Dashboard')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Analytics Dashboard</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item active">Analytics</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Period Selector -->
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title mb-0">Tổng Quan Phân Tích</h5>
                        <p class="text-muted mb-0">Theo dõi hiệu suất tổng thể của hệ thống</p>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary btn-sm period-btn" data-period="7">7 ngày</button>
                            <button type="button" class="btn btn-primary btn-sm period-btn" data-period="30">30 ngày</button>
                            <button type="button" class="btn btn-outline-primary btn-sm period-btn" data-period="90">90 ngày</button>
                            <button type="button" class="btn btn-outline-primary btn-sm period-btn" data-period="365">1 năm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Key Performance Indicators -->
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium">Tổng Người Dùng</p>
                        <h4 class="mb-0">{{ number_format($kpis['total_users']) }}</h4>
                        <p class="text-muted mb-0">
                            <span class="text-success me-2">
                                <i data-feather="arrow-up" class="me-1"></i>{{ $growthRates['users'] }}%
                            </span>
                            {{ $period }} ngày qua
                        </p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i class="fas fa-users"></i>
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
                        <p class="text-muted fw-medium">Doanh Thu</p>
                        <h4 class="mb-0">{{ number_format($kpis['total_revenue']) }} VND</h4>
                        <p class="text-muted mb-0">
                            <span class="text-success me-2">
                                <i data-feather="arrow-up" class="me-1"></i>{{ $growthRates['revenue'] }}%
                            </span>
                            {{ $period }} ngày qua
                        </p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                            <span class="avatar-title">
                                <i data-feather="dollar-sign"></i>
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
                        <p class="text-muted fw-medium">Đơn Hàng</p>
                        <h4 class="mb-0">{{ number_format($kpis['total_orders']) }}</h4>
                        <p class="text-muted mb-0">
                            <span class="text-info me-2">
                                <i data-feather="shopping-cart" class="me-1"></i>{{ number_format($kpis['period_orders']) }}
                            </span>
                            {{ $period }} ngày qua
                        </p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                            <span class="avatar-title">
                                <i data-feather="shopping-cart"></i>
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
                        <p class="text-muted fw-medium">Sản Phẩm Hoạt Động</p>
                        <h4 class="mb-0">{{ number_format($kpis['active_products']) }}</h4>
                        <p class="text-muted mb-0">
                            <span class="text-muted me-2">
                                Tổng: {{ number_format($kpis['total_products']) }}
                            </span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                            <span class="avatar-title">
                                <i data-feather="package"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Real-time Metrics -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Biểu Đồ Tổng Quan</h4>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary active" data-chart="revenue">Doanh Thu</button>
                        <button type="button" class="btn btn-outline-primary" data-chart="users">Người Dùng</button>
                        <button type="button" class="btn btn-outline-primary" data-chart="orders">Đơn Hàng</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="overview-chart" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- Real-time Stats -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Thống Kê Thời Gian Thực</h4>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i class="fas fa-users" class="text-primary me-2"></i>
                            Người dùng online
                        </div>
                        <span class="badge bg-primary" id="online-users">0</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i data-feather="shopping-cart" class="text-success me-2"></i>
                            Đơn hàng hôm nay
                        </div>
                        <span class="badge bg-success" id="today-orders">0</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i data-feather="dollar-sign" class="text-info me-2"></i>
                            Doanh thu hôm nay
                        </div>
                        <span class="badge bg-info" id="today-revenue">0 VND</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i class="fas fa-clock" class="text-warning me-2"></i>
                            Chờ phê duyệt
                        </div>
                        <span class="badge bg-warning" id="pending-approvals">0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Thao Tác Nhanh</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.analytics.revenue') }}" class="btn btn-outline-primary">
                        <i data-feather="trending-up" class="me-2"></i>Phân Tích Doanh Thu
                    </a>
                    <a href="{{ route('admin.analytics.marketplace') }}" class="btn btn-outline-success">
                        <i data-feather="store" class="me-2"></i>Phân Tích Marketplace
                    </a>
                    <a href="{{ route('admin.analytics.technical') }}" class="btn btn-outline-info">
                        <i data-feather="tool" class="me-2"></i>Phân Tích Kỹ Thuật
                    </a>
                    <button type="button" class="btn btn-outline-secondary" onclick="exportData()">
                        <i class="fas fa-download" class="me-2"></i>Xuất Báo Cáo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Performers -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Hiệu Suất Hàng Đầu</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle" class="me-2"></i>
                    Dữ liệu hiệu suất sẽ được hiển thị khi có đủ thông tin
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Hoạt Động Gần Đây</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i data-feather="activity" class="me-2"></i>
                    Hoạt động gần đây sẽ được hiển thị ở đây
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- ApexCharts -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script>
let overviewChart;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather Icons with error handling
    if (typeof feather !== 'undefined') {
        try {
            feather.replace();
        } catch (error) {
            console.warn('Feather Icons error in analytics page:', error);
        }
    }

    // Initialize chart
    initOverviewChart();

    // Load real-time data
    loadRealtimeData();

    // Set up real-time updates
    setInterval(loadRealtimeData, 30000); // Update every 30 seconds

    // Period selector
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('btn-primary'));
            document.querySelectorAll('.period-btn').forEach(b => b.classList.add('btn-outline-primary'));
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');

            const period = this.dataset.period;
            window.location.href = `{{ route('admin.analytics.index') }}?period=${period}`;
        });
    });
});

function initOverviewChart() {
    const options = {
        series: [{
            name: 'Doanh Thu',
            data: [0, 0, 0, 0, 0, 0, 0] // Placeholder data
        }],
        chart: {
            type: 'area',
            height: 350,
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
            categories: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return val.toLocaleString() + ' VND';
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
            }
        },
        colors: ['#556ee6'],
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toLocaleString() + ' VND';
                }
            }
        }
    };

    overviewChart = new ApexCharts(document.querySelector("#overview-chart"), options);
    overviewChart.render();
}

function loadRealtimeData() {
    fetch('{{ route('admin.analytics.realtime') }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('online-users').textContent = data.online_users || 0;
            document.getElementById('today-orders').textContent = data.today_orders || 0;
            document.getElementById('today-revenue').textContent = (data.today_revenue || 0).toLocaleString() + ' VND';
            document.getElementById('pending-approvals').textContent = data.pending_approvals || 0;
        })
        .catch(error => console.error('Error loading realtime data:', error));
}

function exportData() {
    alert('Chức năng xuất báo cáo sẽ được triển khai');
}
</script>
@endsection
