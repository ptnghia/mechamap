@extends('layouts.app')

@section('title', 'My Orders - MechaMap Marketplace')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.index') }}">Marketplace</a></li>
            <li class="breadcrumb-item active">My Orders</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-package text-primary me-2"></i>
                        My Orders
                    </h1>
                    <p class="text-muted mb-0">Track and manage your marketplace orders</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('marketplace.index') }}" class="btn btn-outline-primary">
                        <i class="bx bx-store me-1"></i>
                        Continue Shopping
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-export me-1"></i>
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?export=pdf">
                                <i class="bx bx-file-pdf me-2"></i>PDF Report
                            </a></li>
                            <li><a class="dropdown-item" href="?export=csv">
                                <i class="bx bx-file me-2"></i>CSV Data
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $stats['total_orders'] }}</h3>
                            <p class="mb-0">Total Orders</p>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-package display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">${{ number_format($stats['total_spent'], 2) }}</h3>
                            <p class="mb-0">Total Spent</p>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-dollar display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $stats['pending_orders'] }}</h3>
                            <p class="mb-0">Pending Orders</p>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-time display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $stats['delivered_orders'] }}</h3>
                            <p class="mb-0">Delivered</p>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-check-circle display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('marketplace.orders.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Order number, product..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders List -->
    @if($orders->count() > 0)
        <div class="orders-container">
            @foreach($orders as $order)
            <div class="card order-card mb-3">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-0">
                                <a href="{{ route('marketplace.orders.show', $order) }}" class="text-decoration-none">
                                    Order #{{ $order->order_number }}
                                </a>
                            </h6>
                            <small class="text-muted">
                                Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}
                            </small>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="d-flex justify-content-md-end align-items-center gap-2">
                                <span class="badge bg-{{ $order->getStatusColor() }} fs-6">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <span class="badge bg-{{ $order->getPaymentStatusColor() }} fs-6">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-horizontal-rounded"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('marketplace.orders.show', $order) }}">
                                            <i class="bx bx-show me-2"></i>View Details
                                        </a></li>
                                        @if($order->tracking_number)
                                        <li><a class="dropdown-item" href="{{ route('marketplace.orders.tracking', $order) }}">
                                            <i class="bx bx-map me-2"></i>Track Order
                                        </a></li>
                                        @endif
                                        @if($order->canBeCancelled())
                                        <li><a class="dropdown-item text-danger" href="#" onclick="cancelOrder('{{ $order->id }}')">
                                            <i class="bx bx-x me-2"></i>Cancel Order
                                        </a></li>
                                        @endif
                                        <li><a class="dropdown-item" href="#" onclick="downloadInvoice('{{ $order->id }}')">
                                            <i class="bx bx-download me-2"></i>Download Invoice
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Order Items -->
                            <div class="order-items">
                                @foreach($order->items->take(3) as $item)
                                <div class="d-flex align-items-center mb-2">
                                    <img src="{{ $item->product->getFirstImageUrl() }}" 
                                         alt="{{ $item->product_name }}" 
                                         class="rounded me-3" width="50" height="50">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $item->product_name }}</h6>
                                        <div class="text-muted small">
                                            Qty: {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}
                                        </div>
                                        <div class="text-muted small">
                                            Seller: {{ $item->seller->store_name ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-medium">${{ number_format($item->total_price, 2) }}</div>
                                        <span class="badge bg-{{ $item->getFulfillmentStatusColor() }} small">
                                            {{ ucfirst($item->fulfillment_status) }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                                @if($order->items->count() > 3)
                                <div class="text-muted small">
                                    +{{ $order->items->count() - 3 }} more items
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- Order Summary -->
                            <div class="order-summary">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>${{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                @if($order->tax_amount > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax:</span>
                                    <span>${{ number_format($order->tax_amount, 2) }}</span>
                                </div>
                                @endif
                                @if($order->shipping_amount > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span>${{ number_format($order->shipping_amount, 2) }}</span>
                                </div>
                                @endif
                                @if($order->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Discount:</span>
                                    <span>-${{ number_format($order->discount_amount, 2) }}</span>
                                </div>
                                @endif
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total:</span>
                                    <span>${{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="mt-3">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('marketplace.orders.show', $order) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="bx bx-show me-1"></i>
                                        View Details
                                    </a>
                                    @if($order->tracking_number)
                                    <a href="{{ route('marketplace.orders.tracking', $order) }}" 
                                       class="btn btn-outline-info btn-sm">
                                        <i class="bx bx-map me-1"></i>
                                        Track Order
                                    </a>
                                    @endif
                                    @if($order->canReorder())
                                    <button class="btn btn-outline-secondary btn-sm" 
                                            onclick="reorderItems('{{ $order->id }}')">
                                        <i class="bx bx-refresh me-1"></i>
                                        Reorder
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Timeline -->
                @if($order->hasTimeline())
                <div class="card-footer bg-light">
                    <div class="order-timeline">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Order Progress:</small>
                            <div class="timeline-steps">
                                <div class="step {{ $order->status == 'pending' ? 'active' : ($order->isStatusPassed('pending') ? 'completed' : '') }}">
                                    <i class="bx bx-time"></i>
                                    <span>Pending</span>
                                </div>
                                <div class="step {{ $order->status == 'confirmed' ? 'active' : ($order->isStatusPassed('confirmed') ? 'completed' : '') }}">
                                    <i class="bx bx-check"></i>
                                    <span>Confirmed</span>
                                </div>
                                <div class="step {{ $order->status == 'processing' ? 'active' : ($order->isStatusPassed('processing') ? 'completed' : '') }}">
                                    <i class="bx bx-cog"></i>
                                    <span>Processing</span>
                                </div>
                                <div class="step {{ $order->status == 'shipped' ? 'active' : ($order->isStatusPassed('shipped') ? 'completed' : '') }}">
                                    <i class="bx bx-package"></i>
                                    <span>Shipped</span>
                                </div>
                                <div class="step {{ $order->status == 'delivered' ? 'active' : ($order->isStatusPassed('delivered') ? 'completed' : '') }}">
                                    <i class="bx bx-check-circle"></i>
                                    <span>Delivered</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="d-flex justify-content-center">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bx bx-package display-1 text-muted"></i>
                <h3 class="mt-3">No Orders Found</h3>
                <p class="text-muted">You haven't placed any orders yet. Start shopping to see your orders here.</p>
                <div class="mt-4">
                    <a href="{{ route('marketplace.index') }}" class="btn btn-primary me-2">
                        <i class="bx bx-store me-1"></i>
                        Start Shopping
                    </a>
                    <a href="{{ route('marketplace.categories.index') }}" class="btn btn-outline-primary">
                        <i class="bx bx-category me-1"></i>
                        Browse Categories
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.order-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid rgba(0,0,0,0.125);
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.order-items .d-flex:last-child {
    border-bottom: none;
}

.order-summary {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
}

.timeline-steps {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.timeline-steps .step {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 0.75rem;
    color: #6c757d;
    position: relative;
}

.timeline-steps .step.active {
    color: var(--bs-primary);
}

.timeline-steps .step.completed {
    color: var(--bs-success);
}

.timeline-steps .step i {
    font-size: 1.25rem;
    margin-bottom: 0.25rem;
}

.timeline-steps .step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 50%;
    right: -0.75rem;
    width: 0.5rem;
    height: 2px;
    background: #dee2e6;
    transform: translateY(-50%);
}

.timeline-steps .step.completed:not(:last-child)::after {
    background: var(--bs-success);
}

@media (max-width: 768px) {
    .timeline-steps {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .timeline-steps .step {
        font-size: 0.7rem;
    }
    
    .timeline-steps .step:not(:last-child)::after {
        display: none;
    }
}
</style>
@endpush

@push('scripts')
<script>
function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order?')) {
        fetch(`/marketplace/orders/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Order cancelled successfully', 'success');
                location.reload();
            } else {
                showToast(data.message || 'Error cancelling order', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error cancelling order', 'error');
        });
    }
}

function reorderItems(orderId) {
    if (confirm('Add all items from this order to your cart?')) {
        fetch(`/marketplace/orders/${orderId}/reorder`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Items added to cart successfully', 'success');
                updateCartCount();
            } else {
                showToast(data.message || 'Error adding items to cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error adding items to cart', 'error');
        });
    }
}

function downloadInvoice(orderId) {
    window.open(`/marketplace/orders/${orderId}/invoice`, '_blank');
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function updateCartCount() {
    fetch('/marketplace/cart/count')
        .then(response => response.json())
        .then(data => {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.count;
            }
        });
}
</script>
@endpush
