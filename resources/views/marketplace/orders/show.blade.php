@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' - MechaMap Marketplace')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.index') }}">Marketplace</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.orders.index') }}">My Orders</a></li>
            <li class="breadcrumb-item active">Order #{{ $order->order_number }}</li>
        </ol>
    </nav>

    <!-- Order Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="h2 mb-1">Order #{{ $order->order_number }}</h1>
                    <p class="text-muted mb-0">
                        Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    @if($order->tracking_number)
                    <a href="{{ route('marketplace.orders.tracking', $order) }}" class="btn btn-info">
                        <i class="bx bx-map me-1"></i>
                        Track Order
                    </a>
                    @endif
                    @if($order->canBeCancelled())
                    <button class="btn btn-outline-danger" onclick="cancelOrder()">
                        <i class="bx bx-x me-1"></i>
                        Cancel Order
                    </button>
                    @endif
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-horizontal-rounded me-1"></i>
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="downloadInvoice()">
                                <i class="bx bx-download me-2"></i>Download Invoice
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="printOrder()">
                                <i class="bx bx-printer me-2"></i>Print Order
                            </a></li>
                            @if($order->canReorder())
                            <li><a class="dropdown-item" href="#" onclick="reorderItems()">
                                <i class="bx bx-refresh me-2"></i>Reorder Items
                            </a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="contactSupport()">
                                <i class="bx bx-support me-2"></i>Contact Support
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-{{ $order->getStatusColor() }}">
                <div class="card-body text-center">
                    <i class="bx bx-package display-6 text-{{ $order->getStatusColor() }}"></i>
                    <h6 class="mt-2 mb-1">Order Status</h6>
                    <span class="badge bg-{{ $order->getStatusColor() }} fs-6">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-{{ $order->getPaymentStatusColor() }}">
                <div class="card-body text-center">
                    <i class="bx bx-credit-card display-6 text-{{ $order->getPaymentStatusColor() }}"></i>
                    <h6 class="mt-2 mb-1">Payment Status</h6>
                    <span class="badge bg-{{ $order->getPaymentStatusColor() }} fs-6">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="bx bx-dollar display-6 text-info"></i>
                    <h6 class="mt-2 mb-1">Total Amount</h6>
                    <h5 class="text-info mb-0">${{ number_format($order->total_amount, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <i class="bx bx-package display-6 text-secondary"></i>
                    <h6 class="mt-2 mb-1">Items Count</h6>
                    <h5 class="text-secondary mb-0">{{ $order->items->sum('quantity') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Order Items -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-list-ul me-2"></i>
                        Order Items ({{ $order->items->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Seller</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item->product->getFirstImageUrl() }}" 
                                                 alt="{{ $item->product_name }}" 
                                                 class="rounded me-3" width="60" height="60">
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="{{ route('marketplace.products.show', $item->product) }}" 
                                                       class="text-decoration-none">
                                                        {{ $item->product_name }}
                                                    </a>
                                                </h6>
                                                <div class="text-muted small">
                                                    SKU: {{ $item->product_sku }}
                                                </div>
                                                @if($item->product_options)
                                                <div class="text-muted small">
                                                    Options: {{ implode(', ', $item->product_options) }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <a href="{{ route('marketplace.sellers.show', $item->seller) }}" 
                                               class="text-decoration-none">
                                                {{ $item->seller->store_name }}
                                            </a>
                                            <div class="text-muted small">
                                                {{ $item->seller->user->name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->sale_price && $item->sale_price < $item->unit_price)
                                            <div class="text-decoration-line-through text-muted small">
                                                ${{ number_format($item->unit_price, 2) }}
                                            </div>
                                            <div class="text-success fw-medium">
                                                ${{ number_format($item->sale_price, 2) }}
                                            </div>
                                        @else
                                            <div class="fw-medium">
                                                ${{ number_format($item->unit_price, 2) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $item->quantity }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-medium">
                                            ${{ number_format($item->total_price, 2) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $item->getFulfillmentStatusColor() }}">
                                            {{ ucfirst($item->fulfillment_status) }}
                                        </span>
                                        @if($item->tracking_number)
                                        <div class="text-muted small mt-1">
                                            Tracking: {{ $item->tracking_number }}
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" 
                                                       href="{{ route('marketplace.products.show', $item->product) }}">
                                                    <i class="bx bx-show me-2"></i>View Product
                                                </a></li>
                                                @if($item->canBeReviewed())
                                                <li><a class="dropdown-item" href="#" 
                                                       onclick="reviewProduct({{ $item->product->id }})">
                                                    <i class="bx bx-star me-2"></i>Write Review
                                                </a></li>
                                                @endif
                                                @if($item->canBeReturned())
                                                <li><a class="dropdown-item text-warning" href="#" 
                                                       onclick="returnItem({{ $item->id }})">
                                                    <i class="bx bx-undo me-2"></i>Return Item
                                                </a></li>
                                                @endif
                                                <li><a class="dropdown-item" href="#" 
                                                       onclick="contactSeller({{ $item->seller->id }})">
                                                    <i class="bx bx-message me-2"></i>Contact Seller
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

            <!-- Order Timeline -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-time me-2"></i>
                        Order Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @if($order->placed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Order Placed</h6>
                                <p class="text-muted mb-0">{{ $order->placed_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($order->confirmed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Order Confirmed</h6>
                                <p class="text-muted mb-0">{{ $order->confirmed_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($order->shipped_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Order Shipped</h6>
                                <p class="text-muted mb-0">{{ $order->shipped_at->format('M d, Y \a\t g:i A') }}</p>
                                @if($order->tracking_number)
                                <p class="text-muted mb-0">
                                    Tracking: {{ $order->tracking_number }}
                                    @if($order->carrier)
                                        ({{ $order->carrier }})
                                    @endif
                                </p>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($order->delivered_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Order Delivered</h6>
                                <p class="text-muted mb-0">{{ $order->delivered_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($order->cancelled_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Order Cancelled</h6>
                                <p class="text-muted mb-0">{{ $order->cancelled_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-receipt me-2"></i>
                        Order Summary
                    </h6>
                </div>
                <div class="card-body">
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
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total:</span>
                        <span>${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-map me-2"></i>
                        Shipping Information
                    </h6>
                </div>
                <div class="card-body">
                    @if($order->shipping_address)
                    <div class="mb-3">
                        <h6 class="small text-muted mb-1">SHIPPING ADDRESS</h6>
                        <div>
                            {{ $order->shipping_address['name'] ?? '' }}<br>
                            {{ $order->shipping_address['address_line_1'] ?? '' }}<br>
                            @if($order->shipping_address['address_line_2'] ?? '')
                                {{ $order->shipping_address['address_line_2'] }}<br>
                            @endif
                            {{ $order->shipping_address['city'] ?? '' }}, 
                            {{ $order->shipping_address['state'] ?? '' }} 
                            {{ $order->shipping_address['postal_code'] ?? '' }}<br>
                            {{ $order->shipping_address['country'] ?? '' }}
                        </div>
                    </div>
                    @endif

                    @if($order->shipping_method)
                    <div class="mb-3">
                        <h6 class="small text-muted mb-1">SHIPPING METHOD</h6>
                        <div>{{ ucfirst($order->shipping_method) }}</div>
                    </div>
                    @endif

                    @if($order->tracking_number)
                    <div class="mb-3">
                        <h6 class="small text-muted mb-1">TRACKING NUMBER</h6>
                        <div class="d-flex align-items-center">
                            <span class="me-2">{{ $order->tracking_number }}</span>
                            <button class="btn btn-sm btn-outline-primary" onclick="copyTracking()">
                                <i class="bx bx-copy"></i>
                            </button>
                        </div>
                        @if($order->carrier)
                        <div class="text-muted small">Carrier: {{ $order->carrier }}</div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-credit-card me-2"></i>
                        Payment Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}
                    </div>
                    @if($order->payment_gateway)
                    <div class="mb-2">
                        <strong>Gateway:</strong> {{ ucfirst($order->payment_gateway) }}
                    </div>
                    @endif
                    @if($order->payment_transaction_id)
                    <div class="mb-2">
                        <strong>Transaction ID:</strong> {{ $order->payment_transaction_id }}
                    </div>
                    @endif
                    <div class="mb-2">
                        <strong>Status:</strong> 
                        <span class="badge bg-{{ $order->getPaymentStatusColor() }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Customer Notes -->
            @if($order->customer_notes)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-note me-2"></i>
                        Order Notes
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->customer_notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0.25rem;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    padding-left: 1rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    color: #495057;
}

@media (max-width: 768px) {
    .timeline {
        padding-left: 1.5rem;
    }
    
    .timeline-marker {
        left: -1.5rem;
        width: 1rem;
        height: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function cancelOrder() {
    if (confirm('Are you sure you want to cancel this order?')) {
        fetch(`/marketplace/orders/{{ $order->id }}/cancel`, {
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

function downloadInvoice() {
    window.open(`/marketplace/orders/{{ $order->id }}/invoice`, '_blank');
}

function printOrder() {
    window.print();
}

function reorderItems() {
    if (confirm('Add all items from this order to your cart?')) {
        fetch(`/marketplace/orders/{{ $order->id }}/reorder`, {
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

function copyTracking() {
    const trackingNumber = '{{ $order->tracking_number }}';
    navigator.clipboard.writeText(trackingNumber).then(() => {
        showToast('Tracking number copied to clipboard', 'success');
    });
}

function reviewProduct(productId) {
    // Open review modal or redirect to review page
    window.location.href = `/marketplace/products/${productId}/review`;
}

function returnItem(itemId) {
    if (confirm('Are you sure you want to return this item?')) {
        fetch(`/marketplace/orders/items/${itemId}/return`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Return request submitted successfully', 'success');
                location.reload();
            } else {
                showToast(data.message || 'Error submitting return request', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error submitting return request', 'error');
        });
    }
}

function contactSeller(sellerId) {
    window.location.href = `/marketplace/sellers/${sellerId}/contact`;
}

function contactSupport() {
    window.location.href = `/support/contact?order={{ $order->order_number }}`;
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
