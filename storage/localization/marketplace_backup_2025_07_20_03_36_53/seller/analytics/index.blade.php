@extends('layouts.app')

@section('title', 'Sales Analytics - MechaMap Marketplace')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.index') }}">Marketplace</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.seller.dashboard.index') }}">Seller Dashboard</a></li>
            <li class="breadcrumb-item active">Analytics</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-chart-line text-primary me-2"></i>
                        Sales Analytics
                    </h1>
                    <p class="text-muted mb-0">Comprehensive insights into your store performance</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-calendar me-1"></i>
                            {{ ucfirst($selectedPeriod) }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?period=today">Today</a></li>
                            <li><a class="dropdown-item" href="?period=week">This Week</a></li>
                            <li><a class="dropdown-item" href="?period=month">This Month</a></li>
                            <li><a class="dropdown-item" href="?period=quarter">This Quarter</a></li>
                            <li><a class="dropdown-item" href="?period=year">This Year</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="showCustomDateRange()">Custom Range</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-outline-primary" onclick="exportAnalytics()">
                        <i class="bx bx-export me-1"></i>
                        Export Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bx bx-dollar"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mb-0">${{ number_format($analytics['revenue']['current'], 2) }}</h3>
                            <p class="text-muted mb-1">Revenue</p>
                            <div class="d-flex align-items-center">
                                <i class="bx bx-trending-{{ $analytics['revenue']['trend'] == 'up' ? 'up text-success' : 'down text-danger' }} me-1"></i>
                                <span class="small text-{{ $analytics['revenue']['trend'] == 'up' ? 'success' : 'danger' }}">
                                    {{ $analytics['revenue']['change'] }}%
                                </span>
                                <span class="text-muted small ms-1">vs last period</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="bx bx-package"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $analytics['orders']['current'] }}</h3>
                            <p class="text-muted mb-1">Orders</p>
                            <div class="d-flex align-items-center">
                                <i class="bx bx-trending-{{ $analytics['orders']['trend'] == 'up' ? 'up text-success' : 'down text-danger' }} me-1"></i>
                                <span class="small text-{{ $analytics['orders']['trend'] == 'up' ? 'success' : 'danger' }}">
                                    {{ $analytics['orders']['change'] }}%
                                </span>
                                <span class="text-muted small ms-1">vs last period</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon bg-info bg-opacity-10 text-info me-3">
                            <i class="bx bx-show"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ number_format($analytics['views']['current']) }}</h3>
                            <p class="text-muted mb-1">Product Views</p>
                            <div class="d-flex align-items-center">
                                <i class="bx bx-trending-{{ $analytics['views']['trend'] == 'up' ? 'up text-success' : 'down text-danger' }} me-1"></i>
                                <span class="small text-{{ $analytics['views']['trend'] == 'up' ? 'success' : 'danger' }}">
                                    {{ $analytics['views']['change'] }}%
                                </span>
                                <span class="text-muted small ms-1">vs last period</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bx bx-percentage"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ number_format($analytics['conversion']['current'], 1) }}%</h3>
                            <p class="text-muted mb-1">Conversion Rate</p>
                            <div class="d-flex align-items-center">
                                <i class="bx bx-trending-{{ $analytics['conversion']['trend'] == 'up' ? 'up text-success' : 'down text-danger' }} me-1"></i>
                                <span class="small text-{{ $analytics['conversion']['trend'] == 'up' ? 'success' : 'danger' }}">
                                    {{ $analytics['conversion']['change'] }}%
                                </span>
                                <span class="text-muted small ms-1">vs last period</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bx bx-chart-area me-2"></i>
                            Revenue Trend
                        </h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="chartType" id="revenue" checked>
                            <label class="btn btn-outline-primary" for="revenue">Revenue</label>
                            
                            <input type="radio" class="btn-check" name="chartType" id="orders">
                            <label class="btn btn-outline-primary" for="orders">Orders</label>
                            
                            <input type="radio" class="btn-check" name="chartType" id="views">
                            <label class="btn btn-outline-primary" for="views">Views</label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-trophy me-2"></i>
                        Top Performing Products
                    </h6>
                </div>
                <div class="card-body">
                    @if($topProducts->count() > 0)
                    <div class="top-products-list">
                        @foreach($topProducts as $index => $product)
                        <div class="product-item d-flex align-items-center mb-3">
                            <div class="rank-badge me-3">
                                <span class="badge bg-{{ $index < 3 ? ['warning', 'secondary', 'dark'][$index] : 'light text-dark' }}">
                                    #{{ $index + 1 }}
                                </span>
                            </div>
                            <img src="{{ $product->getFirstImageUrl() }}" 
                                 alt="{{ $product->name }}" 
                                 class="rounded me-3" width="40" height="40">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ Str::limit($product->name, 25) }}</h6>
                                <div class="text-muted small">
                                    ${{ number_format($product->revenue, 2) }} revenue
                                </div>
                                <div class="text-muted small">
                                    {{ $product->sales_count }} sales
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="bx bx-package display-6 text-muted"></i>
                        <p class="text-muted mb-0">No sales data available</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sales by Category -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-pie-chart me-2"></i>
                        Sales by Category
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Customer Analytics -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-users me-2"></i>
                        Customer Insights
                    </h6>
                </div>
                <div class="card-body">
                    <div class="customer-metrics">
                        <div class="metric-row d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-medium">New Customers</div>
                                <div class="text-muted small">This {{ $selectedPeriod }}</div>
                            </div>
                            <div class="text-end">
                                <div class="h5 mb-0 text-primary">{{ $analytics['customers']['new'] }}</div>
                                <div class="small text-success">
                                    <i class="bx bx-trending-up"></i> +{{ $analytics['customers']['new_growth'] }}%
                                </div>
                            </div>
                        </div>

                        <div class="metric-row d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-medium">Returning Customers</div>
                                <div class="text-muted small">Repeat purchases</div>
                            </div>
                            <div class="text-end">
                                <div class="h5 mb-0 text-success">{{ $analytics['customers']['returning'] }}</div>
                                <div class="small text-info">
                                    {{ number_format($analytics['customers']['return_rate'], 1) }}% return rate
                                </div>
                            </div>
                        </div>

                        <div class="metric-row d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-medium">Average Order Value</div>
                                <div class="text-muted small">Per customer</div>
                            </div>
                            <div class="text-end">
                                <div class="h5 mb-0 text-warning">${{ number_format($analytics['customers']['avg_order_value'], 2) }}</div>
                                <div class="small text-{{ $analytics['customers']['aov_trend'] == 'up' ? 'success' : 'danger' }}">
                                    <i class="bx bx-trending-{{ $analytics['customers']['aov_trend'] }}"></i> 
                                    {{ $analytics['customers']['aov_change'] }}%
                                </div>
                            </div>
                        </div>

                        <div class="metric-row d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-medium">Customer Lifetime Value</div>
                                <div class="text-muted small">Average CLV</div>
                            </div>
                            <div class="text-end">
                                <div class="h5 mb-0 text-info">${{ number_format($analytics['customers']['lifetime_value'], 2) }}</div>
                                <div class="small text-muted">
                                    {{ number_format($analytics['customers']['avg_orders_per_customer'], 1) }} orders/customer
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bx bx-table me-2"></i>
                            Product Performance Details
                        </h5>
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control form-control-sm" 
                                   placeholder="Search products..." id="productSearch" style="width: 200px;">
                            <button class="btn btn-sm btn-outline-secondary" onclick="exportProductData()">
                                <i class="bx bx-export"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="productAnalyticsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Views</th>
                                    <th>Orders</th>
                                    <th>Revenue</th>
                                    <th>Conversion</th>
                                    <th>Avg Rating</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productAnalytics as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $product->getFirstImageUrl() }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="rounded me-3" width="40" height="40">
                                            <div>
                                                <div class="fw-medium">{{ Str::limit($product->name, 40) }}</div>
                                                <div class="text-muted small">SKU: {{ $product->sku }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ number_format($product->views_count) }}</div>
                                        <div class="small text-{{ $product->views_trend == 'up' ? 'success' : 'danger' }}">
                                            <i class="bx bx-trending-{{ $product->views_trend }}"></i> 
                                            {{ $product->views_change }}%
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $product->orders_count }}</div>
                                        <div class="small text-muted">{{ $product->units_sold }} units</div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">${{ number_format($product->revenue, 2) }}</div>
                                        <div class="small text-{{ $product->revenue_trend == 'up' ? 'success' : 'danger' }}">
                                            <i class="bx bx-trending-{{ $product->revenue_trend }}"></i> 
                                            {{ $product->revenue_change }}%
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ number_format($product->conversion_rate, 1) }}%</div>
                                        <div class="progress mt-1" style="height: 4px;">
                                            <div class="progress-bar" style="width: {{ $product->conversion_rate }}%"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-medium me-1">{{ number_format($product->rating_average, 1) }}</span>
                                            <i class="bx bxs-star text-warning"></i>
                                        </div>
                                        <div class="small text-muted">({{ $product->rating_count }} reviews)</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $product->stock_quantity > 10 ? 'success' : ($product->stock_quantity > 0 ? 'warning' : 'danger') }}">
                                            {{ $product->stock_quantity }} left
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" 
                                                       href="{{ route('marketplace.products.show', $product) }}">
                                                    <i class="bx bx-show me-2"></i>View Product
                                                </a></li>
                                                <li><a class="dropdown-item" 
                                                       href="{{ route('marketplace.seller.products.edit', $product) }}">
                                                    <i class="bx bx-edit me-2"></i>Edit Product
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" 
                                                       onclick="viewDetailedAnalytics({{ $product->id }})">
                                                    <i class="bx bx-chart-line me-2"></i>Detailed Analytics
                                                </a></li>
                                            </ul>
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
    </div>
</div>

<!-- Custom Date Range Modal -->
<div class="modal fade" id="customDateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Custom Date Range</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="customDateForm">
                    <div class="row">
                        <div class="col-6">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDate" name="start_date">
                        </div>
                        <div class="col-6">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDate" name="end_date">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="applyCustomDateRange()">Apply</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.kpi-card {
    transition: transform 0.2s ease-in-out;
}

.kpi-card:hover {
    transform: translateY(-2px);
}

.kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.top-products-list .product-item:last-child {
    margin-bottom: 0;
}

.rank-badge {
    width: 40px;
    text-align: center;
}

.customer-metrics .metric-row {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.customer-metrics .metric-row:last-child {
    border-bottom: none;
}

#productAnalyticsTable th {
    font-weight: 600;
    font-size: 0.875rem;
    color: #495057;
}

@media (max-width: 768px) {
    .kpi-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }
    
    .customer-metrics .metric-row {
        flex-direction: column;
        text-align: center;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: @json($chartData['labels']),
        datasets: [{
            label: 'Revenue',
            data: @json($chartData['revenue']),
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4,
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
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: @json($categoryData['labels']),
        datasets: [{
            data: @json($categoryData['values']),
            backgroundColor: [
                '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14'
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

// Chart type switching
document.querySelectorAll('input[name="chartType"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const chartType = this.id;
        updateChart(chartType);
    });
});

function updateChart(type) {
    const datasets = {
        revenue: @json($chartData['revenue']),
        orders: @json($chartData['orders']),
        views: @json($chartData['views'])
    };
    
    revenueChart.data.datasets[0].data = datasets[type];
    revenueChart.data.datasets[0].label = type.charAt(0).toUpperCase() + type.slice(1);
    revenueChart.update();
}

// Product search
document.getElementById('productSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#productAnalyticsTable tbody tr');
    
    rows.forEach(row => {
        const productName = row.querySelector('td:first-child .fw-medium').textContent.toLowerCase();
        const sku = row.querySelector('td:first-child .text-muted').textContent.toLowerCase();
        
        if (productName.includes(searchTerm) || sku.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

function showCustomDateRange() {
    const modal = new bootstrap.Modal(document.getElementById('customDateModal'));
    modal.show();
}

function applyCustomDateRange() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (startDate && endDate) {
        window.location.href = `?period=custom&start_date=${startDate}&end_date=${endDate}`;
    }
}

function exportAnalytics() {
    const period = '{{ $selectedPeriod }}';
    window.open(`/marketplace/seller/analytics/export?period=${period}`, '_blank');
}

function exportProductData() {
    const period = '{{ $selectedPeriod }}';
    window.open(`/marketplace/seller/analytics/export-products?period=${period}`, '_blank');
}

function viewDetailedAnalytics(productId) {
    window.location.href = `/marketplace/seller/products/${productId}/analytics`;
}
</script>
@endpush
