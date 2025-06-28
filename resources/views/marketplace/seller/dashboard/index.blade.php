@extends('layouts.app')

@section('title', 'Seller Dashboard - MechaMap Marketplace')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.index') }}">Marketplace</a></li>
            <li class="breadcrumb-item active">Seller Dashboard</li>
        </ol>
    </nav>

    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-store text-primary me-2"></i>
                        Seller Dashboard
                    </h1>
                    <p class="text-muted mb-0">Welcome back, {{ $seller->store_name }}!</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('marketplace.seller.products.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>
                        Add Product
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-cog me-1"></i>
                            Settings
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('marketplace.seller.profile') }}">
                                <i class="bx bx-user me-2"></i>Store Profile
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('marketplace.seller.settings') }}">
                                <i class="bx bx-cog me-2"></i>Store Settings
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('marketplace.seller.shipping') }}">
                                <i class="bx bx-package me-2"></i>Shipping Settings
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('marketplace.seller.help') }}">
                                <i class="bx bx-help-circle me-2"></i>Help Center
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">${{ number_format($metrics['total_revenue'], 2) }}</h3>
                            <p class="mb-0">Total Revenue</p>
                            <small class="opacity-75">
                                <i class="bx bx-trending-up me-1"></i>
                                +{{ $metrics['revenue_growth'] }}% this month
                            </small>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-dollar display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $metrics['total_orders'] }}</h3>
                            <p class="mb-0">Total Orders</p>
                            <small class="opacity-75">
                                {{ $metrics['pending_orders'] }} pending
                            </small>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-package display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $metrics['total_products'] }}</h3>
                            <p class="mb-0">Products Listed</p>
                            <small class="opacity-75">
                                {{ $metrics['active_products'] }} active
                            </small>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-cube display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ number_format($metrics['avg_rating'], 1) }}</h3>
                            <p class="mb-0">Average Rating</p>
                            <small class="opacity-75">
                                {{ $metrics['total_reviews'] }} reviews
                            </small>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-star display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-zap me-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="{{ route('marketplace.seller.products.create') }}" class="quick-action-card">
                                <div class="quick-action-icon bg-primary">
                                    <i class="bx bx-plus"></i>
                                </div>
                                <div class="quick-action-text">Add Product</div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="{{ route('marketplace.seller.orders.index') }}" class="quick-action-card">
                                <div class="quick-action-icon bg-success">
                                    <i class="bx bx-package"></i>
                                </div>
                                <div class="quick-action-text">Manage Orders</div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="{{ route('marketplace.seller.products.index') }}" class="quick-action-card">
                                <div class="quick-action-icon bg-info">
                                    <i class="bx bx-cube"></i>
                                </div>
                                <div class="quick-action-text">My Products</div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="{{ route('marketplace.seller.analytics.index') }}" class="quick-action-card">
                                <div class="quick-action-icon bg-warning">
                                    <i class="bx bx-chart-line"></i>
                                </div>
                                <div class="quick-action-text">Analytics</div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="{{ route('marketplace.seller.reviews.index') }}" class="quick-action-card">
                                <div class="quick-action-icon bg-secondary">
                                    <i class="bx bx-star"></i>
                                </div>
                                <div class="quick-action-text">Reviews</div>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <a href="{{ route('marketplace.seller.profile') }}" class="quick-action-card">
                                <div class="quick-action-icon bg-dark">
                                    <i class="bx bx-store"></i>
                                </div>
                                <div class="quick-action-text">Store Profile</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-list-ul me-2"></i>
                        Recent Orders
                    </h5>
                    <a href="{{ route('marketplace.seller.orders.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $orderItem)
                                <tr>
                                    <td>
                                        <a href="{{ route('marketplace.seller.orders.show', $orderItem) }}" 
                                           class="text-decoration-none fw-medium">
                                            #{{ $orderItem->order->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-medium">{{ $orderItem->order->customer->name }}</div>
                                            <div class="text-muted small">{{ $orderItem->order->customer_email }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $orderItem->product->getFirstImageUrl() }}" 
                                                 alt="{{ $orderItem->product_name }}" 
                                                 class="rounded me-2" width="40" height="40">
                                            <div>
                                                <div class="fw-medium">{{ Str::limit($orderItem->product_name, 30) }}</div>
                                                <div class="text-muted small">Qty: {{ $orderItem->quantity }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">${{ number_format($orderItem->total_price, 2) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $orderItem->getFulfillmentStatusColor() }}">
                                            {{ ucfirst($orderItem->fulfillment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            {{ $orderItem->created_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" 
                                                       href="{{ route('marketplace.seller.orders.show', $orderItem) }}">
                                                    <i class="bx bx-show me-2"></i>View Details
                                                </a></li>
                                                @if($orderItem->canUpdateStatus())
                                                <li><a class="dropdown-item" href="#" 
                                                       onclick="updateOrderStatus({{ $orderItem->id }})">
                                                    <i class="bx bx-edit me-2"></i>Update Status
                                                </a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bx bx-package display-4 text-muted"></i>
                        <h6 class="mt-3">No Recent Orders</h6>
                        <p class="text-muted">Orders will appear here once customers start purchasing your products.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Performance Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-chart-line me-2"></i>
                        Performance Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="performance-metrics">
                        <div class="metric-item mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">This Month Revenue</span>
                                <span class="fw-bold text-success">${{ number_format($metrics['month_revenue'], 2) }}</span>
                            </div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: {{ $metrics['revenue_progress'] }}%"></div>
                            </div>
                        </div>
                        
                        <div class="metric-item mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Orders Fulfilled</span>
                                <span class="fw-bold">{{ $metrics['fulfilled_orders'] }}/{{ $metrics['total_orders'] }}</span>
                            </div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: {{ $metrics['fulfillment_rate'] }}%"></div>
                            </div>
                        </div>
                        
                        <div class="metric-item mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Customer Satisfaction</span>
                                <span class="fw-bold text-warning">{{ number_format($metrics['avg_rating'], 1) }}/5.0</span>
                            </div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-warning" style="width: {{ ($metrics['avg_rating'] / 5) * 100 }}%"></div>
                            </div>
                        </div>
                        
                        <div class="metric-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Response Time</span>
                                <span class="fw-bold text-info">{{ $metrics['avg_response_time'] }}h</span>
                            </div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-info" style="width: {{ max(0, 100 - ($metrics['avg_response_time'] * 10)) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-bell me-2"></i>
                        Notifications
                    </h6>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                    <div class="notifications-list">
                        @foreach($notifications->take(5) as $notification)
                        <div class="notification-item d-flex align-items-start mb-3">
                            <div class="notification-icon me-3">
                                <i class="bx {{ $notification->getIcon() }} text-{{ $notification->getColor() }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium">{{ $notification->title }}</div>
                                <div class="text-muted small">{{ $notification->message }}</div>
                                <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('marketplace.seller.notifications.index') }}" class="btn btn-sm btn-outline-primary w-100">
                        View All Notifications
                    </a>
                    @else
                    <div class="text-center py-3">
                        <i class="bx bx-bell-off display-6 text-muted"></i>
                        <p class="text-muted mb-0">No new notifications</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-trending-up me-2"></i>
                        Quick Stats
                    </h6>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item text-center mb-3">
                            <div class="stat-value text-primary">{{ $metrics['views_today'] }}</div>
                            <div class="stat-label">Views Today</div>
                        </div>
                        <div class="stat-item text-center mb-3">
                            <div class="stat-value text-success">{{ $metrics['sales_today'] }}</div>
                            <div class="stat-label">Sales Today</div>
                        </div>
                        <div class="stat-item text-center mb-3">
                            <div class="stat-value text-info">{{ $metrics['messages_unread'] }}</div>
                            <div class="stat-label">Unread Messages</div>
                        </div>
                        <div class="stat-item text-center">
                            <div class="stat-value text-warning">{{ $metrics['low_stock_items'] }}</div>
                            <div class="stat-label">Low Stock Items</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.metric-card {
    transition: transform 0.2s ease-in-out;
}

.metric-card:hover {
    transform: translateY(-2px);
}

.quick-action-card {
    display: block;
    text-decoration: none;
    color: inherit;
    text-align: center;
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    transition: all 0.2s ease-in-out;
}

.quick-action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    text-decoration: none;
    color: inherit;
}

.quick-action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    font-size: 1.5rem;
    color: white;
}

.quick-action-text {
    font-size: 0.875rem;
    font-weight: 500;
}

.performance-metrics .metric-item {
    padding: 0.5rem 0;
}

.notifications-list .notification-item:last-child {
    margin-bottom: 0;
}

.notification-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(var(--bs-primary-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
}

.stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .quick-action-card {
        padding: 0.75rem;
    }
    
    .quick-action-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function updateOrderStatus(orderItemId) {
    // This would open a modal or redirect to update status page
    window.location.href = `/marketplace/seller/orders/${orderItemId}/update-status`;
}

// Auto-refresh dashboard data every 5 minutes
setInterval(() => {
    if (document.visibilityState === 'visible') {
        fetch('/marketplace/seller/dashboard/refresh', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.hasUpdates) {
                // Update metrics without full page reload
                updateDashboardMetrics(data.metrics);
            }
        })
        .catch(error => {
            console.error('Error refreshing dashboard:', error);
        });
    }
}, 300000); // 5 minutes

function updateDashboardMetrics(metrics) {
    // Update metric cards with new data
    // This would be implemented based on the actual data structure
    console.log('Updating dashboard metrics:', metrics);
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
