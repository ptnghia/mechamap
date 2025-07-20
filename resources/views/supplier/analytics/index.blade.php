@extends('layouts.app')

@section('title', 'Phân tích dữ liệu - Supplier Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            @include('supplier.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">📊 Phân tích dữ liệu</h1>
                    <p class="text-muted">Theo dõi hiệu suất kinh doanh của bạn</p>
                </div>
                <div class="d-flex gap-2">
                    <select class="form-select" id="periodSelect" onchange="changePeriod()">
                        <option value="7" {{ $period == '7' ? 'selected' : '' }}>7 ngày qua</option>
                        <option value="30" {{ $period == '30' ? 'selected' : '' }}>30 ngày qua</option>
                        <option value="90" {{ $period == '90' ? 'selected' : '' }}>90 ngày qua</option>
                        <option value="365" {{ $period == '365' ? 'selected' : '' }}>1 năm qua</option>
                    </select>
                    <a href="{{ route('supplier.analytics.export', ['period' => $period]) }}" class="btn btn-outline-primary">
                        <i class="fas fa-download"></i> Xuất báo cáo
                    </a>
                </div>
            </div>

            <!-- Revenue Overview -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1"{{ t_features('supplier.labels.revenue') }}/h6>
                                    <h4 class="mb-0">{{ number_format($analytics['revenue']['current_period'], 0, ',', '.') }} VND</h4>
                                    <small class="text-{{ $analytics['revenue']['growth_rate'] >= 0 ? 'success' : 'danger' }}">
                                        <i class="fas fa-{{ $analytics['revenue']['growth_rate'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                        {{ abs($analytics['revenue']['growth_rate']) }}%
                                    </small>
                                </div>
                                <div class="text-primary">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1"{{ t_features('supplier.labels.orders') }}/h6>
                                    <h4 class="mb-0">{{ number_format($analytics['orders']['total_orders']) }}</h4>
                                    <small class="text-muted">Tổng đơn hàng</small>
                                </div>
                                <div class="text-success">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1"{{ t_features('supplier.labels.customers') }}/h6>
                                    <h4 class="mb-0">{{ number_format($analytics['customers']['unique_customers']) }}</h4>
                                    <small class="text-muted">Khách hàng duy nhất</small>
                                </div>
                                <div class="text-info">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1"{{ t_features('supplier.labels.products') }}/h6>
                                    <h4 class="mb-0">{{ number_format($analytics['products']['active_products']) }}</h4>
                                    <small class="text-muted">Đang hoạt động</small>
                                </div>
                                <div class="text-warning">
                                    <i class="fas fa-box fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">📈 Biểu đồ doanh thu theo ngày</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">📊 Trạng thái đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="orderStatusChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">🏆 Sản phẩm bán chạy</h5>
                        </div>
                        <div class="card-body">
                            @if($analytics['products']['top_products']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th class="text-end">Đã bán</th>
                                                <th class="text-end">Doanh thu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($analytics['products']['top_products'] as $product)
                                            <tr>
                                                <td>{{ Str::limit($product->product_name, 30) }}</td>
                                                <td class="text-end">{{ number_format($product->total_sold) }}</td>
                                                <td class="text-end">{{ number_format($product->total_revenue, 0, ',', '.') }} VND</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Chưa có dữ liệu sản phẩm</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">👥 Khách hàng hàng đầu</h5>
                        </div>
                        <div class="card-body">
                            @if($analytics['customers']['top_customers']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Khách hàng</th>
                                                <th class="text-end">Đơn hàng</th>
                                                <th class="text-end">Chi tiêu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($analytics['customers']['top_customers'] as $customer)
                                            <tr>
                                                <td>{{ Str::limit($customer->name, 20) }}</td>
                                                <td class="text-end">{{ number_format($customer->order_count) }}</td>
                                                <td class="text-end">{{ number_format($customer->total_spent, 0, ',', '.') }} VND</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Chưa có dữ liệu khách hàng</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Performance -->
            @if(isset($analytics['trends']['category_performance']) && $analytics['trends']['category_performance']->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">📂 Hiệu suất theo danh mục</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Danh mục</th>
                                            <th class="text-end">Đơn hàng</th>
                                            <th class="text-end">Doanh thu</th>
                                            <th class="text-end">Tỷ lệ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analytics['trends']['category_performance'] as $category)
                                        <tr>
                                            <td>{{ $category->category_name }}</td>
                                            <td class="text-end">{{ number_format($category->orders) }}</td>
                                            <td class="text-end">{{ number_format($category->revenue, 0, ',', '.') }} VND</td>
                                            <td class="text-end">
                                                @php
                                                    $totalRevenue = $analytics['trends']['category_performance']->sum('revenue');
                                                    $percentage = $totalRevenue > 0 ? ($category->revenue / $totalRevenue) * 100 : 0;
                                                @endphp
                                                {{ number_format($percentage, 1) }}%
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
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function changePeriod() {
    const period = document.getElementById('periodSelect').value;
    window.location.href = `{{ route('supplier.analytics.index') }}?period=${period}`;
}

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: @json($analytics['revenue']['daily_revenue']->pluck('date')),
        datasets: [{
            label: 'Doanh thu (VND)',
            data: @json($analytics['revenue']['daily_revenue']->pluck('revenue')),
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
                        return new Intl.NumberFormat('vi-VN').format(value) + ' VND';
                    }
                }
            }
        }
    }
});

// Order Status Chart
const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
const orderStatusChart = new Chart(orderStatusCtx, {
    type: 'doughnut',
    data: {
        labels: @json($analytics['orders']['orders_by_status']->pluck('fulfillment_status')),
        datasets: [{
            data: @json($analytics['orders']['orders_by_status']->pluck('count')),
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF'
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
</script>
@endpush
@endsection
