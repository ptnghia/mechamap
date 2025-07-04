@extends('layouts.app')

@section('title', 'Order Confirmation - MechaMap')

@section('content')
<div class="min-vh-100 bg-light">
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Success Message -->
                <div class="text-center mb-5">
                    <div class="success-icon mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h1 class="h2 mb-3">Order Confirmed!</h1>
                    <p class="lead text-muted">
                        Thank you for your purchase. Your order has been successfully placed and is being processed.
                    </p>
                </div>

                <!-- Order Details Card -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-receipt me-2"></i>
                                    Order Details
                                </h5>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-light text-dark">{{ $order->status_label }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Order Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Order Number:</strong></td>
                                        <td>{{ $order->order_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Order Date:</strong></td>
                                        <td>{{ $order->created_at->format('M d, Y \a\t g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Status:</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                                {{ $order->payment_status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Amount:</strong></td>
                                        <td><strong class="text-success">${{ number_format($order->total_amount, 2) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Shipping Address</h6>
                                <address>
                                    {{ $order->shipping_address['first_name'] ?? '' }} {{ $order->shipping_address['last_name'] ?? '' }}<br>
                                    {{ $order->shipping_address['address_line_1'] ?? '' }}<br>
                                    @if(!empty($order->shipping_address['address_line_2']))
                                        {{ $order->shipping_address['address_line_2'] }}<br>
                                    @endif
                                    {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}<br>
                                    {{ $order->shipping_address['country'] ?? '' }}
                                </address>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <h6>Order Items</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->product && $item->product->featured_image)
                                                        <img src="{{ get_product_image_url($item->product->featured_image) }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $item->product_name }}" onerror="this.src='{{ asset('images/placeholder-product.jpg') }}'">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $item->product_name }}</h6>
                                                        @if($item->product_sku)
                                                            <small class="text-muted">SKU: {{ $item->product_sku }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>${{ number_format($item->unit_price, 2) }}</td>
                                            <td>${{ number_format($item->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3"><strong>Subtotal</strong></td>
                                        <td><strong>${{ number_format($order->subtotal, 2) }}</strong></td>
                                    </tr>
                                    @if($order->shipping_amount > 0)
                                        <tr>
                                            <td colspan="3">Shipping</td>
                                            <td>${{ number_format($order->shipping_amount, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if($order->tax_amount > 0)
                                        <tr>
                                            <td colspan="3">Tax</td>
                                            <td>${{ number_format($order->tax_amount, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if($order->discount_amount > 0)
                                        <tr class="text-success">
                                            <td colspan="3">Discount</td>
                                            <td>-${{ number_format($order->discount_amount, 2) }}</td>
                                        </tr>
                                    @endif
                                    <tr class="table-success">
                                        <td colspan="3"><strong>Total</strong></td>
                                        <td><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            What's Next?
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <i class="bi bi-envelope text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h6>Order Confirmation</h6>
                                    <p class="small text-muted">You'll receive an email confirmation shortly with your order details.</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <i class="bi bi-box-seam text-warning mb-2" style="font-size: 2rem;"></i>
                                    <h6>Processing</h6>
                                    <p class="small text-muted">Your order will be processed and prepared for shipping within 1-2 business days.</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <i class="bi bi-truck text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6>Shipping</h6>
                                    <p class="small text-muted">You'll receive tracking information once your order ships.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center">
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('marketplace.index') }}" class="btn btn-primary">
                            <i class="bi bi-shop me-2"></i>
                            Continue Shopping
                        </a>
                        <a href="#" class="btn btn-outline-primary" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>
                            Print Order
                        </a>
                        @auth
                            <a href="{{ route('profile.orders') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-list-ul me-2"></i>
                                View All Orders
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Support Info -->
                <div class="text-center mt-4">
                    <p class="text-muted">
                        <i class="bi bi-headset me-1"></i>
                        Need help? Contact our support team at
                        <a href="mailto:support@mechamap.com" class="text-decoration-none">support@mechamap.com</a>
                        or call <a href="tel:+1234567890" class="text-decoration-none">+1 (234) 567-890</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.success-icon {
    animation: successPulse 2s ease-in-out;
}

@keyframes successPulse {
    0% {
        transform: scale(0.8);
        opacity: 0.5;
    }
    50% {
        transform: scale(1.1);
        opacity: 1;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@media print {
    .btn, .breadcrumb, .navbar, .footer {
        display: none !important;
    }

    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }

    .bg-light {
        background: white !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-scroll to top
    window.scrollTo(0, 0);

    // Show success animation
    setTimeout(() => {
        const successIcon = document.querySelector('.success-icon i');
        if (successIcon) {
            successIcon.style.animation = 'successPulse 2s ease-in-out';
        }
    }, 100);
});
</script>
@endpush
