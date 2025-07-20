@extends('admin.layouts.app')

@section('title', 'Báo Cáo Tài Chính')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
<style>
.financial-card {
    transition: transform 0.2s ease-in-out;
}
.financial-card:hover {
    transform: translateY(-2px);
}
.chart-container {
    position: relative;
    height: 400px;
}
.metric-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}
.metric-value {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 5px;
}
.metric-label {
    font-size: 0.9rem;
    opacity: 0.9;
}
.growth-indicator {
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}
.growth-positive {
    background-color: rgba(34, 197, 94, 0.1);
    color: #22c55e;
}
.growth-negative {
    background-color: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-line text-primary me-2"></i>
                Báo Cáo Tài Chính
            </h1>
            <p class="text-muted mb-0">Phân tích doanh thu, hoa hồng và hiệu suất tài chính</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#dateRangeModal">
                <i class="fas fa-calendar me-1"></i>
                Chọn Khoảng Thời Gian
            </button>
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-1"></i>
                    Xuất Báo Cáo
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.financial-reports.revenue') }}">
                        <i class="fas fa-chart-bar me-2"></i>Báo Cáo Doanh Thu
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.financial-reports.commission') }}">
                        <i class="fas fa-percentage me-2"></i>Báo Cáo Hoa Hồng
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.financial-reports.payout') }}">
                        <i class="fas fa-money-check-alt me-2"></i>Báo Cáo Payout
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.financial-reports.seller-performance') }}">
                        <i class="fas fa-users me-2"></i>Hiệu Suất Seller
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Date Range Display -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        Hiển thị dữ liệu từ <strong>{{ $startDate->format('d/m/Y') }}</strong> đến <strong>{{ $endDate->format('d/m/Y') }}</strong>
        <span class="ms-3">
            <small class="text-muted">({{ $startDate->diffInDays($endDate) + 1 }} ngày)</small>
        </span>
    </div>

    <!-- Financial Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card financial-card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng Doanh Thu
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($financialOverview['total_revenue'], 0, ',', '.') }} VNĐ
                            </div>
                            <div class="mt-2">
                                <span class="growth-indicator {{ $financialOverview['growth_rate'] >= 0 ? 'growth-positive' : 'growth-negative' }}">
                                    <i class="fas fa-{{ $financialOverview['growth_rate'] >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                                    {{ number_format(abs($financialOverview['growth_rate']), 1) }}%
                                </span>
                                <small class="text-muted ms-2">so với kỳ trước</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card financial-card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng Hoa Hồng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($financialOverview['total_commission'], 0, ',', '.') }} VNĐ
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    {{ $financialOverview['total_revenue'] > 0 ? number_format(($financialOverview['total_commission'] / $financialOverview['total_revenue']) * 100, 1) : 0 }}% 
                                    của doanh thu
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card financial-card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Payout Đã Hoàn Thành
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($financialOverview['total_payouts'], 0, ',', '.') }} VNĐ
                            </div>
                            <div class="mt-2">
                                <small class="text-warning">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ number_format($financialOverview['pending_payouts'], 0, ',', '.') }} VNĐ đang chờ
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card financial-card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Lợi Nhuận Ròng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($financialOverview['net_profit'], 0, ',', '.') }} VNĐ
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    Hoa hồng - Phí gateway
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Biểu Đồ Doanh Thu Theo Ngày</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="{{ route('admin.financial-reports.revenue') }}">Xem Chi Tiết</a>
                            <a class="dropdown-item" href="#" onclick="exportChart('revenueChart')">Xuất Biểu Đồ</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Doanh Thu Theo Phương Thức</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Chỉ Số Hiệu Suất</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="metric-card bg-gradient-primary">
                                <div class="metric-value">{{ number_format($financialOverview['payment_count']) }}</div>
                                <div class="metric-label">Tổng Số Giao Dịch</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="metric-card bg-gradient-success">
                                <div class="metric-value">{{ number_format($financialOverview['average_order_value'], 0, ',', '.') }}</div>
                                <div class="metric-label">Giá Trị Đơn Hàng Trung Bình (VNĐ)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Sellers Theo Doanh Thu</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Seller</th>
                                    <th>Doanh Thu</th>
                                    <th>Đơn Hàng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPerformers['top_sellers']->take(5) as $seller)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <div class="avatar-initial bg-primary rounded-circle">
                                                    {{ substr($seller->seller->name ?? 'N/A', 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $seller->seller->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $seller->seller->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-success">
                                            {{ number_format($seller->total_revenue, 0, ',', '.') }} VNĐ
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $seller->order_count }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.financial-reports.seller-performance') }}" class="btn btn-sm btn-outline-primary">
                            Xem Tất Cả Sellers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Báo Cáo Chi Tiết</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('admin.financial-reports.revenue') }}" class="btn btn-outline-primary btn-block mb-2">
                                <i class="fas fa-chart-bar me-2"></i>
                                Báo Cáo Doanh Thu
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.financial-reports.commission') }}" class="btn btn-outline-success btn-block mb-2">
                                <i class="fas fa-percentage me-2"></i>
                                Báo Cáo Hoa Hồng
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.financial-reports.payout') }}" class="btn btn-outline-info btn-block mb-2">
                                <i class="fas fa-money-check-alt me-2"></i>
                                Báo Cáo Payout
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.financial-reports.seller-performance') }}" class="btn btn-outline-warning btn-block mb-2">
                                <i class="fas fa-users me-2"></i>
                                Hiệu Suất Seller
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Date Range Modal -->
<div class="modal fade" id="dateRangeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chọn Khoảng Thời Gian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('admin.financial-reports.index') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Từ Ngày</label>
                            <input type="date" name="start_date" class="form-control" 
                                   value="{{ $startDate->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Đến Ngày</label>
                            <input type="date" name="end_date" class="form-control" 
                                   value="{{ $endDate->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Hoặc chọn khoảng thời gian có sẵn:</label>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('today')">Hôm Nay</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('week')">Tuần Này</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('month')">Tháng Này</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDateRange('quarter')">Quý Này</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Áp Dụng</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($revenueAnalytics['daily_revenue']->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m'); })) !!},
        datasets: [{
            label: 'Doanh Thu (VNĐ)',
            data: {!! json_encode($revenueAnalytics['daily_revenue']->pluck('revenue')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
                    }
                }
            }
        }
    }
});

// Payment Method Chart
const methodCtx = document.getElementById('paymentMethodChart').getContext('2d');
const methodChart = new Chart(methodCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($revenueAnalytics['revenue_by_method']->pluck('payment_method')->map(function($method) { return $method === 'stripe' ? 'Stripe' : 'SePay'; })) !!},
        datasets: [{
            data: {!! json_encode($revenueAnalytics['revenue_by_method']->pluck('revenue')) !!},
            backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#36b9cc',
                '#f6c23e'
            ]
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

// Date range functions
function setDateRange(period) {
    const today = new Date();
    let startDate, endDate;
    
    switch(period) {
        case 'today':
            startDate = endDate = today;
            break;
        case 'week':
            startDate = new Date(today.setDate(today.getDate() - today.getDay()));
            endDate = new Date();
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date();
            break;
        case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            startDate = new Date(today.getFullYear(), quarter * 3, 1);
            endDate = new Date();
            break;
    }
    
    document.querySelector('input[name="start_date"]').value = startDate.toISOString().split('T')[0];
    document.querySelector('input[name="end_date"]').value = endDate.toISOString().split('T')[0];
}

function exportChart(chartId) {
    const canvas = document.getElementById(chartId);
    const url = canvas.toDataURL('image/png');
    const a = document.createElement('a');
    a.href = url;
    a.download = chartId + '_' + new Date().toISOString().split('T')[0] + '.png';
    a.click();
}
</script>
@endpush
