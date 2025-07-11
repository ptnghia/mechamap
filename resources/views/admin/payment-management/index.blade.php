@extends('admin.layouts.app')

@section('title', 'Quản Lý Thanh Toán')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-credit-card text-primary me-2"></i>
                Quản Lý Thanh Toán Tập Trung
            </h1>
            <p class="text-muted mb-0">Dashboard tổng quan hệ thống thanh toán và payout</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#dateRangeModal">
                <i class="fas fa-calendar-alt me-1"></i>
                Thay Đổi Khoảng Thời Gian
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-download me-1"></i>
                Xuất Báo Cáo
            </button>
        </div>
    </div>

    <!-- Date Range Display -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        Hiển thị dữ liệu từ <strong>{{ $startDate->format('d/m/Y') }}</strong> đến <strong>{{ $endDate->format('d/m/Y') }}</strong>
    </div>

    <!-- Payment Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng Doanh Thu
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($paymentStats['total_revenue'], 0, ',', '.') }} VNĐ
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
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Thanh Toán Thành Công
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($paymentStats['completed_payments']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Đang Chờ Xử Lý
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($paymentStats['pending_payments']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Thanh Toán Thất Bại
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($paymentStats['failed_payments']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="exportChart('revenue-chart')">Xuất Biểu Đồ</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Phương Thức Thanh Toán</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Stripe
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> SePay
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Statistics -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống Kê Payout</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 font-weight-bold text-warning">
                                    {{ number_format($payoutStats['pending_payouts']) }}
                                </div>
                                <div class="text-xs text-uppercase">Chờ Duyệt</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 font-weight-bold text-success">
                                    {{ number_format($payoutStats['completed_payouts']) }}
                                </div>
                                <div class="text-xs text-uppercase">Đã Hoàn Thành</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <div class="h5 font-weight-bold text-primary">
                            {{ number_format($payoutStats['total_payout_amount'], 0, ',', '.') }} VNĐ
                        </div>
                        <div class="text-xs text-uppercase">Tổng Đã Thanh Toán</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tình Trạng Hệ Thống</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Tỷ Lệ Thành Công</span>
                            <span class="font-weight-bold text-success">{{ number_format($systemHealth['payment_success_rate'], 1) }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: {{ $systemHealth['payment_success_rate'] }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Thời Gian Xử Lý TB</span>
                            <span class="font-weight-bold">{{ number_format($systemHealth['average_processing_time'], 0) }}s</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Đơn Cần Review</span>
                            <span class="font-weight-bold text-warning">{{ $systemHealth['pending_review_orders'] }}</span>
                        </div>
                    </div>

                    <div class="row text-center">
                        <div class="col-6">
                            <span class="badge badge-{{ $systemHealth['system_settings_status']['stripe_configured'] ? 'success' : 'danger' }}">
                                Stripe {{ $systemHealth['system_settings_status']['stripe_configured'] ? 'OK' : 'Error' }}
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="badge badge-{{ $systemHealth['system_settings_status']['sepay_configured'] ? 'success' : 'danger' }}">
                                SePay {{ $systemHealth['system_settings_status']['sepay_configured'] ? 'OK' : 'Error' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thanh Toán Gần Đây</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Mã Thanh Toán</th>
                                    <th>Số Tiền</th>
                                    <th>Trạng Thái</th>
                                    <th>Thời Gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentActivities['recent_payments'] as $payment)
                                <tr>
                                    <td>
                                        <small class="text-muted">{{ $payment->payment_reference }}</small>
                                    </td>
                                    <td>{{ number_format($payment->gross_amount, 0, ',', '.') }} VNĐ</td>
                                    <td>
                                        <span class="badge badge-{{ $payment->status_color }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $payment->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payout Gần Đây</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Mã Payout</th>
                                    <th>Seller</th>
                                    <th>Số Tiền</th>
                                    <th>Trạng Thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentActivities['recent_payouts'] as $payout)
                                <tr>
                                    <td>
                                        <small class="text-muted">{{ $payout->payout_reference }}</small>
                                    </td>
                                    <td>{{ $payout->seller->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($payout->net_payout, 0, ',', '.') }} VNĐ</td>
                                    <td>
                                        <span class="badge badge-{{ $payout->status_color }}">
                                            {{ ucfirst($payout->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
            <form method="GET" action="{{ route('admin.payment-management.index') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Từ Ngày</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Đến Ngày</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}" required>
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

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xuất Báo Cáo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.payment-management.export') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Từ Ngày</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Đến Ngày</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Định Dạng</label>
                        <select name="format" class="form-select" required>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download me-1"></i>
                        Xuất Báo Cáo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
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
        labels: {!! json_encode($revenueAnalytics['revenue_by_method']->pluck('payment_method')->map(function($method) { return ucfirst($method); })) !!},
        datasets: [{
            data: {!! json_encode($revenueAnalytics['revenue_by_method']->pluck('revenue')) !!},
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

function exportChart(chartId) {
    // Chart export functionality would go here
    alert('Tính năng xuất biểu đồ sẽ được phát triển');
}
</script>
@endpush
