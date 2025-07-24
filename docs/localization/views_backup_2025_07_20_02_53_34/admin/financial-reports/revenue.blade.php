@extends('admin.layouts.app')

@section('title', 'Báo Cáo Doanh Thu')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
<style>
.revenue-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.revenue-value {
    font-size: 3rem;
    font-weight: bold;
    margin-bottom: 10px;
}
.revenue-label {
    font-size: 1.1rem;
    opacity: 0.9;
}
.chart-container {
    position: relative;
    height: 400px;
}
.trend-indicator {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-top: 10px;
}
.trend-up {
    background-color: rgba(34, 197, 94, 0.1);
    color: #22c55e;
}
.trend-down {
    background-color: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}
.period-selector {
    background: white;
    border-radius: 10px;
    padding: 5px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.period-btn {
    border: none;
    background: transparent;
    padding: 8px 16px;
    border-radius: 6px;
    transition: all 0.3s ease;
}
.period-btn.active {
    background: #4e73df;
    color: white;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-bar text-primary me-2"></i>
                Báo Cáo Doanh Thu Chi Tiết
            </h1>
            <p class="text-muted mb-0">Phân tích doanh thu theo thời gian, phương thức thanh toán và seller</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i>
                Bộ Lọc
            </button>
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-1"></i>
                    Xuất Báo Cáo
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportReport('csv')">
                        <i class="fas fa-file-csv me-2"></i>Xuất CSV
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportReport('excel')">
                        <i class="fas fa-file-excel me-2"></i>Xuất Excel
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportReport('pdf')">
                        <i class="fas fa-file-pdf me-2"></i>Xuất PDF
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Period Selector -->
    <div class="period-selector mb-4">
        <div class="d-flex">
            <button class="period-btn {{ $groupBy === 'day' ? 'active' : '' }}" onclick="changeGroupBy('day')">
                Theo Ngày
            </button>
            <button class="period-btn {{ $groupBy === 'week' ? 'active' : '' }}" onclick="changeGroupBy('week')">
                Theo Tuần
            </button>
            <button class="period-btn {{ $groupBy === 'month' ? 'active' : '' }}" onclick="changeGroupBy('month')">
                Theo Tháng
            </button>
        </div>
    </div>

    <!-- Revenue Overview -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="revenue-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="revenue-value">
                            {{ number_format($revenueData->sum('revenue'), 0, ',', '.') }} VNĐ
                        </div>
                        <div class="revenue-label">
                            Tổng Doanh Thu ({{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }})
                        </div>
                        @if($revenueTrends['growth_rate'] !== null)
                        <div class="trend-indicator {{ $revenueTrends['growth_rate'] >= 0 ? 'trend-up' : 'trend-down' }}">
                            <i class="fas fa-{{ $revenueTrends['growth_rate'] >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                            {{ number_format(abs($revenueTrends['growth_rate']), 1) }}% so với kỳ trước
                        </div>
                        @endif
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="h4 mb-1">{{ number_format($revenueData->sum('payment_count')) }}</div>
                        <div class="opacity-75">Tổng Giao Dịch</div>
                        <div class="h5 mb-1 mt-3">{{ number_format($revenueData->avg('avg_payment'), 0, ',', '.') }} VNĐ</div>
                        <div class="opacity-75">Giá Trị TB/Giao Dịch</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống Kê Nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Ngày có doanh thu cao nhất:</span>
                        </div>
                        @if($revenueData->isNotEmpty())
                        <div class="text-primary fw-bold">
                            {{ \Carbon\Carbon::parse($revenueData->sortByDesc('revenue')->first()->date)->format('d/m/Y') }}
                        </div>
                        <div class="text-success">
                            {{ number_format($revenueData->sortByDesc('revenue')->first()->revenue, 0, ',', '.') }} VNĐ
                        </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Tăng trưởng trung bình:</span>
                        </div>
                        <div class="text-info fw-bold">
                            {{ number_format($revenueTrends['average_growth'] ?? 0, 1) }}% / {{ $groupBy === 'day' ? 'ngày' : ($groupBy === 'week' ? 'tuần' : 'tháng') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Biểu Đồ Doanh Thu {{ $groupBy === 'day' ? 'Theo Ngày' : ($groupBy === 'week' ? 'Theo Tuần' : 'Theo Tháng') }}
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="exportChart('revenueChart')">Xuất Biểu Đồ</a>
                            <a class="dropdown-item" href="#" onclick="toggleChartType()">Đổi Kiểu Biểu Đồ</a>
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
    </div>

    <!-- Revenue Breakdown -->
    <div class="row mb-4">
        <!-- Revenue by Payment Method -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Doanh Thu Theo Phương Thức Thanh Toán</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                    <div class="mt-3">
                        @foreach($revenueByMethod as $method)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <i class="fas fa-{{ $method->payment_method === 'stripe' ? 'credit-card' : 'university' }} text-{{ $method->payment_method === 'stripe' ? 'primary' : 'success' }}"></i>
                                </div>
                                <span>{{ $method->payment_method === 'stripe' ? 'Stripe (Thẻ quốc tế)' : 'SePay (Chuyển khoản)' }}</span>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ number_format($method->revenue, 0, ',', '.') }} VNĐ</div>
                                <small class="text-muted">{{ $method->payment_count }} giao dịch</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Seller Type -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Doanh Thu Theo Loại Seller</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="sellerTypeChart"></canvas>
                    </div>
                    <div class="mt-3">
                        @foreach($revenueBySeller as $seller)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <span class="badge badge-{{ $seller->seller_type === 'manufacturer' ? 'primary' : ($seller->seller_type === 'supplier' ? 'success' : 'info') }}">
                                        {{ ucfirst($seller->seller_type) }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ number_format($seller->revenue, 0, ',', '.') }} VNĐ</div>
                                <small class="text-muted">{{ $seller->order_count }} đơn hàng</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Revenue Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Chi Tiết Doanh Thu</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="revenueTable">
                    <thead>
                        <tr>
                            <th>{{ $groupBy === 'day' ? 'Ngày' : ($groupBy === 'week' ? 'Tuần' : 'Tháng') }}</th>
                            <th>Doanh Thu</th>
                            <th>Số Giao Dịch</th>
                            <th>Giá Trị TB</th>
                            <th>Tăng Trưởng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenueData as $index => $data)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($data->date)->format($groupBy === 'day' ? 'd/m/Y' : ($groupBy === 'week' ? 'W/Y' : 'm/Y')) }}</td>
                            <td>
                                <strong class="text-success">
                                    {{ number_format($data->revenue, 0, ',', '.') }} VNĐ
                                </strong>
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $data->payment_count }}</span>
                            </td>
                            <td>{{ number_format($data->avg_payment, 0, ',', '.') }} VNĐ</td>
                            <td>
                                @if($index > 0)
                                    @php
                                        $prevRevenue = $revenueData[$index - 1]->revenue;
                                        $growth = $prevRevenue > 0 ? (($data->revenue - $prevRevenue) / $prevRevenue) * 100 : 0;
                                    @endphp
                                    <span class="badge badge-{{ $growth >= 0 ? 'success' : 'danger' }}">
                                        <i class="fas fa-{{ $growth >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                                        {{ number_format(abs($growth), 1) }}%
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bộ Lọc Báo Cáo Doanh Thu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('admin.financial-reports.revenue') }}">
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
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Nhóm Theo</label>
                            <select name="group_by" class="form-select">
                                <option value="day" {{ $groupBy === 'day' ? 'selected' : '' }}>Theo Ngày</option>
                                <option value="week" {{ $groupBy === 'week' ? 'selected' : '' }}>Theo Tuần</option>
                                <option value="month" {{ $groupBy === 'month' ? 'selected' : '' }}>Theo Tháng</option>
                            </select>
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
let currentChartType = 'line';

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
let revenueChart = new Chart(revenueCtx, {
    type: currentChartType,
    data: {
        labels: {!! json_encode($revenueData->pluck('date')->map(function($date) use ($groupBy) { 
            return \Carbon\Carbon::parse($date)->format($groupBy === 'day' ? 'd/m' : ($groupBy === 'week' ? 'W/Y' : 'm/Y')); 
        })) !!},
        datasets: [{
            label: 'Doanh Thu (VNĐ)',
            data: {!! json_encode($revenueData->pluck('revenue')) !!},
            borderColor: 'rgb(78, 115, 223)',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.3,
            fill: true
        }, {
            label: 'Số Giao Dịch',
            data: {!! json_encode($revenueData->pluck('payment_count')) !!},
            borderColor: 'rgb(28, 200, 138)',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            tension: 0.3,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                grid: {
                    drawOnChartArea: false,
                },
                ticks: {
                    callback: function(value) {
                        return value + ' GD';
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
        labels: {!! json_encode($revenueByMethod->pluck('payment_method')->map(function($method) { 
            return $method === 'stripe' ? 'Stripe' : 'SePay'; 
        })) !!},
        datasets: [{
            data: {!! json_encode($revenueByMethod->pluck('revenue')) !!},
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
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

// Seller Type Chart
const sellerCtx = document.getElementById('sellerTypeChart').getContext('2d');
const sellerChart = new Chart(sellerCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($revenueBySeller->pluck('seller_type')->map(function($type) { 
            return ucfirst($type); 
        })) !!},
        datasets: [{
            label: 'Doanh Thu',
            data: {!! json_encode($revenueBySeller->pluck('revenue')) !!},
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
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

// Functions
function changeGroupBy(groupBy) {
    const url = new URL(window.location);
    url.searchParams.set('group_by', groupBy);
    window.location.href = url.toString();
}

function toggleChartType() {
    currentChartType = currentChartType === 'line' ? 'bar' : 'line';
    revenueChart.destroy();
    revenueChart = new Chart(revenueCtx, {
        type: currentChartType,
        data: revenueChart.data,
        options: revenueChart.options
    });
}

function exportChart(chartId) {
    const canvas = document.getElementById(chartId);
    const url = canvas.toDataURL('image/png');
    const a = document.createElement('a');
    a.href = url;
    a.download = chartId + '_' + new Date().toISOString().split('T')[0] + '.png';
    a.click();
}

function exportReport(format) {
    const params = new URLSearchParams({
        report_type: 'revenue',
        start_date: '{{ $startDate->format("Y-m-d") }}',
        end_date: '{{ $endDate->format("Y-m-d") }}',
        format: format
    });
    
    window.open(`{{ route('admin.financial-reports.export') }}?${params}`, '_blank');
}
</script>
@endpush
