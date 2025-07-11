@extends('layouts.app')

@section('title', __('marketplace.checkout.title') . ' - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('assets/css/cart-ux-enhancements.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
@endpush

@section('content')
<div class="min-vh-100 bg-light">
    <!-- Breadcrumb -->
    <div class="bg-white border-bottom">
        <div class="container-fluid py-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <i class="fas fa-home me-2"></i>
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('marketplace.index') }}" class="text-decoration-none">Marketplace</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('marketplace.cart.index') }}" class="text-decoration-none">Cart</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('marketplace.checkout.title') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Checkout Steps -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-credit-card me-2"></i>
                            {{ __('marketplace.checkout.secure_checkout') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Success/Error Messages -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                @foreach($errors->all() as $error)
                                    {{ $error }}<br>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        <!-- Progress Steps -->
                        <div class="checkout-progress mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="step {{ session('step') === 'payment' || session('step') === 'review' ? 'completed' : 'active' }}" data-step="1">
                                    <div class="step-circle">
                                        <span class="step-number">1</span>
                                        <i class="fas fa-check step-check d-none"></i>
                                    </div>
                                    <div class="step-label">{{ __('marketplace.checkout.steps.shipping') }}</div>
                                </div>
                                <div class="step-line {{ session('step') === 'payment' || session('step') === 'review' ? 'completed' : '' }}"></div>
                                <div class="step {{ session('step') === 'payment' ? 'active' : (session('step') === 'review' ? 'completed' : '') }}" data-step="2">
                                    <div class="step-circle">
                                        <span class="step-number">2</span>
                                        <i class="fas fa-check step-check d-none"></i>
                                    </div>
                                    <div class="step-label">{{ __('marketplace.checkout.steps.payment') }}</div>
                                </div>
                                <div class="step-line {{ session('step') === 'review' ? 'completed' : '' }}"></div>
                                <div class="step {{ session('step') === 'review' ? 'active' : '' }}" data-step="3">
                                    <div class="step-circle">
                                        <span class="step-number">3</span>
                                        <i class="fas fa-check step-check d-none"></i>
                                    </div>
                                    <div class="step-label">{{ __('marketplace.checkout.steps.review') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Step Content -->
                        <div id="checkoutSteps">
                            <!-- Step 1: Shipping Information -->
                            <div class="step-content {{ session('step') === 'payment' || session('step') === 'review' ? '' : 'active' }}" id="step1">
                                <h6 class="mb-3">{{ __('marketplace.checkout.shipping_information') }}</h6>
                                <form action="{{ route('marketplace.checkout.shipping') }}" method="POST" id="shippingForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('marketplace.checkout.first_name') }} *</label>
                                            <input type="text" class="form-control" name="shipping_address[first_name]" value="{{ old('shipping_address.first_name') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('marketplace.checkout.last_name') }} *</label>
                                            <input type="text" class="form-control" name="shipping_address[last_name]" value="{{ old('shipping_address.last_name') }}" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('marketplace.checkout.email_address') }} *</label>
                                            <input type="email" class="form-control" name="shipping_address[email]" value="{{ old('shipping_address.email') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('marketplace.checkout.phone_number') }}</label>
                                            <input type="tel" class="form-control" name="shipping_address[phone]" value="{{ old('shipping_address.phone') }}">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('marketplace.checkout.address_line_1') }} *</label>
                                        <input type="text" class="form-control" name="shipping_address[address_line_1]" value="{{ old('shipping_address.address_line_1') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('marketplace.checkout.address_line_2') }}</label>
                                        <input type="text" class="form-control" name="shipping_address[address_line_2]" value="{{ old('shipping_address.address_line_2') }}">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">{{ __('marketplace.checkout.city') }} *</label>
                                            <input type="text" class="form-control" name="shipping_address[city]" value="{{ old('shipping_address.city') }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">{{ __('marketplace.checkout.state_province') }} *</label>
                                            <input type="text" class="form-control" name="shipping_address[state]" value="{{ old('shipping_address.state') }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">{{ __('marketplace.checkout.postal_code') }} *</label>
                                            <input type="text" class="form-control" name="shipping_address[postal_code]" value="{{ old('shipping_address.postal_code') }}" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('marketplace.checkout.country') }} *</label>
                                        <select class="form-select" name="shipping_address[country]" required>
                                            <option value="">{{ __('marketplace.checkout.select_country') }}</option>
                                            <option value="VN" selected>{{ __('marketplace.countries.vietnam') }}</option>
                                            <option value="US">{{ __('marketplace.countries.united_states') }}</option>
                                            <option value="CA">{{ __('marketplace.countries.canada') }}</option>
                                            <option value="GB">{{ __('marketplace.countries.united_kingdom') }}</option>
                                            <option value="AU">{{ __('marketplace.countries.australia') }}</option>
                                        </select>
                                    </div>

                                    <!-- Billing Address -->
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="billingSameAsShipping" name="billing_same_as_shipping" checked>
                                            <label class="form-check-label" for="billingSameAsShipping">
                                                {{ __('marketplace.checkout.billing_same_as_shipping') }}
                                            </label>
                                        </div>
                                    </div>

                                    <div id="billingAddressSection" class="d-none">
                                        <h6 class="mb-3">{{ __('marketplace.checkout.billing_information') }}</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">{{ __('marketplace.checkout.first_name') }} *</label>
                                                <input type="text" class="form-control" name="billing_address[first_name]">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">{{ __('marketplace.checkout.last_name') }} *</label>
                                                <input type="text" class="form-control" name="billing_address[last_name]">
                                            </div>
                                        </div>
                                        <!-- Add more billing fields as needed -->
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('marketplace.cart.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            {{ __('marketplace.checkout.back_to_cart') }}
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('marketplace.checkout.continue_to_payment') }}
                                            <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Step 2: Payment Information -->
                            <div class="step-content {{ session('step') === 'payment' ? 'active' : '' }}" id="step2">
                                <h6 class="mb-3">{{ __('marketplace.checkout.payment_information') }}</h6>
                                <form id="paymentForm" action="{{ route('marketplace.checkout.payment') }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="form-label">{{ __('marketplace.checkout.payment_method') }}</label>
                                        <div class="payment-methods">
                                            <div class="form-check payment-option">
                                                <input class="form-check-input" type="radio" name="payment_method" id="stripe" value="stripe" checked>
                                                <label class="form-check-label" for="stripe">
                                                    <i class="fas fa-credit-card me-2"></i>
                                                    {{ __('marketplace.checkout.credit_debit_card') }}
                                                </label>
                                            </div>
                                            <div class="form-check payment-option">
                                                <input class="form-check-input" type="radio" name="payment_method" id="sepay" value="sepay">
                                                <label class="form-check-label" for="sepay">
                                                    <i class="fas fa-university me-2"></i>
                                                    {{ __('marketplace.checkout.bank_transfer') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stripe Details -->
                                    <div id="stripeDetails" class="payment-details">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            {{ __('marketplace.checkout.stripe_redirect_message') }}
                                        </div>
                                    </div>

                                    <!-- SePay Details -->
                                    <div id="sepayDetails" class="payment-details d-none">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            {{ __('marketplace.checkout.sepay_redirect_message') }}
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="bank-info p-3 border rounded">
                                                    <h6 class="mb-3"><i class="fas fa-university me-2"></i>Thông tin chuyển khoản</h6>
                                                    <div class="mb-2">
                                                        <strong>Ngân hàng:</strong> MBBank
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>Số tài khoản:</strong>
                                                        <span class="text-primary fw-bold">0903252427</span>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('0903252427')">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>Chủ tài khoản:</strong> Bùi Tấn Việt
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>Nội dung:</strong>
                                                        <span class="text-danger fw-bold" id="transferContent">MECHAMAP {{ strtoupper(uniqid()) }}</span>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="copyTransferContent()">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                    <div class="alert alert-warning mt-3">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <small>Vui lòng chuyển khoản đúng nội dung để hệ thống tự động xác nhận thanh toán.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="qr-code-section text-center">
                                                    <h6 class="mb-3"><i class="fas fa-qrcode me-2"></i>QR Code thanh toán</h6>
                                                    <div class="qr-code-container p-3 border rounded">
                                                        <div id="qrcode" class="mb-3"></div>
                                                        <small class="text-muted">Quét mã QR để chuyển khoản nhanh</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary" onclick="previousStep()">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            {{ __('marketplace.checkout.back_to_shipping') }}
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('marketplace.checkout.review_order') }}
                                            <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Step 3: Order Review -->
                            <div class="step-content {{ session('step') === 'review' ? 'active' : '' }}" id="step3">
                                <h6 class="mb-3">{{ __('marketplace.checkout.review_your_order') }}</h6>
                                @if(session('step') === 'review')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>{{ __('marketplace.checkout.shipping_address') }}</h6>
                                            @if(session('checkout.shipping_address'))
                                                @php $shipping = session('checkout.shipping_address'); @endphp
                                                <address>
                                                    {{ $shipping['first_name'] }} {{ $shipping['last_name'] }}<br>
                                                    {{ $shipping['address_line_1'] }}<br>
                                                    @if($shipping['address_line_2'])
                                                        {{ $shipping['address_line_2'] }}<br>
                                                    @endif
                                                    {{ $shipping['city'] }}, {{ $shipping['state'] }} {{ $shipping['postal_code'] }}<br>
                                                    {{ $shipping['country'] }}
                                                </address>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <h6>{{ __('marketplace.checkout.payment_method_label') }}</h6>
                                            @if(session('checkout.payment_method'))
                                                <p class="mb-3">
                                                    @switch(session('checkout.payment_method'))
                                                        @case('stripe')
                                                            {{ __('marketplace.checkout.credit_debit_card') }}
                                                            @break
                                                        @case('sepay')
                                                            {{ __('marketplace.checkout.bank_transfer') }}
                                                            @break
                                                        @default
                                                            {{ session('checkout.payment_method') }}
                                                    @endswitch
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary" onclick="previousStep()">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            {{ __('marketplace.checkout.back_to_payment') }}
                                        </button>
                                        <form action="{{ route('marketplace.checkout.place-order') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check-circle me-2"></i>
                                                {{ __('marketplace.checkout.complete_order') }}
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div id="orderReview">
                                        <!-- Order review content will be loaded here -->
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <!-- Cart Items -->
                        <div class="order-items mb-3">
                            @foreach($cart->items as $item)
                                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                    <div class="flex-shrink-0 me-3">
                                        @if($item->product_image)
                                            @php
                                                // Xử lý đường dẫn hình ảnh
                                                $imageUrl = $item->product_image;
                                                if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                                                    // Nếu không phải URL đầy đủ, tạo URL từ storage
                                                    if (strpos($imageUrl, '/images/') === 0) {
                                                        $imageUrl = asset($imageUrl);
                                                    } else {
                                                        $imageUrl = Storage::url($imageUrl);
                                                    }
                                                }
                                            @endphp
                                            <img src="{{ $imageUrl }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $item->product_name }}">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 small">{{ $item->product_name }}</h6>
                                        <div class="small text-muted">Qty: {{ $item->quantity }}</div>
                                        <div class="small fw-bold">{{ number_format($item->total_price) }} VNĐ</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Order Totals -->
                        <div class="order-totals">
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('marketplace.cart.subtotal') }}</span>
                                <span>{{ number_format($cart->subtotal) }} VNĐ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('marketplace.cart.shipping') }}</span>
                                <span id="shippingCost">{{ __('marketplace.checkout.calculated_at_next_step') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('marketplace.cart.tax') }}</span>
                                <span>{{ number_format($cart->tax_amount) }} VNĐ</span>
                            </div>
                            @if($cart->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Discount</span>
                                    <span>-{{ number_format($cart->discount_amount) }} VNĐ</span>
                                </div>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between fw-bold h5">
                                <span>{{ __('marketplace.cart.total') }}</span>
                                <span id="orderTotal">{{ number_format($cart->total_amount) }} VNĐ</span>
                            </div>
                        </div>

                        <!-- Security Info -->
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt-check me-1"></i>
                                {{ __('marketplace.checkout.payment_secure_encrypted') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none" style="background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Checkout Progress Steps */
.checkout-progress {
    position: relative;
}

.step {
    text-align: center;
    position: relative;
    z-index: 2;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    border: 2px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    transition: all 0.3s ease;
}

.step.active .step-circle {
    background: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.step.completed .step-circle {
    background: #198754;
    border-color: #198754;
    color: white;
}

.step.completed .step-number {
    display: none;
}

.step.completed .step-check {
    display: inline-block !important;
}

.step-line {
    height: 2px;
    background: #dee2e6;
    flex: 1;
    margin: 0 20px;
    margin-top: 20px;
    position: relative;
    z-index: 1;
}

.step-line.completed {
    background: #198754;
}

.step-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #6c757d;
}

.step.active .step-label {
    color: #0d6efd;
    font-weight: 600;
}

.step.completed .step-label {
    color: #198754;
    font-weight: 600;
}

/* Step Content */
.step-content {
    display: none;
}

.step-content.active {
    display: block;
}

/* Payment Methods */
.payment-option {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.payment-option:hover {
    border-color: #0d6efd;
    background: #f8f9fa;
}

.payment-option .form-check-input:checked ~ .form-check-label {
    color: #0d6efd;
    font-weight: 500;
}

.payment-option .form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Order Items */
.order-items {
    max-height: 300px;
    overflow-y: auto;
}

/* Loading States */
.btn.loading {
    position: relative;
    color: transparent !important;
}

.btn.loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Form Validation */
.form-control.is-invalid {
    border-color: #dc3545;
}

.form-control.is-valid {
    border-color: #198754;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 4px;
}

/* Responsive */
@media (max-width: 768px) {
    .checkout-progress .d-flex {
        flex-direction: column;
        gap: 20px;
    }

    .step-line {
        width: 2px;
        height: 40px;
        margin: 10px auto;
    }

    .order-items {
        max-height: 200px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Translation keys for JavaScript
window.checkoutTranslations = {
    failedToProcessPayment: '{{ __('marketplace.checkout.failed_to_process_payment') }}',
    failedToLoadReview: '{{ __('marketplace.checkout.failed_to_load_review') }}',
    creditDebitCard: '{{ __('marketplace.checkout.credit_debit_card') }}',
    bankTransfer: '{{ __('marketplace.checkout.bank_transfer') }}',
    shippingAddress: '{{ __('marketplace.checkout.shipping_address') }}',
    paymentMethodLabel: '{{ __('marketplace.checkout.payment_method_label') }}',
    backToPayment: '{{ __('marketplace.checkout.back_to_payment') }}',
    placeOrder: '{{ __('marketplace.checkout.place_order') }}',
    subtotal: '{{ __('marketplace.cart.subtotal') }}',
    shipping: '{{ __('marketplace.cart.shipping') }}',
    tax: '{{ __('marketplace.cart.tax') }}',
    total: '{{ __('marketplace.cart.total') }}',
    error: '{{ __('marketplace.error') }}',
    success: '{{ __('marketplace.success') }}'
};

// Checkout functionality
let currentStep = 1;
const totalSteps = 3;

document.addEventListener('DOMContentLoaded', function() {
    initializeCheckout();
});

function initializeCheckout() {
    // Initialize form handlers
    initializePaymentForm();
    initializeBillingToggle();
    initializePaymentMethods();

    // Pre-fill user data if logged in
    prefillUserData();
}

// Shipping form sẽ submit tự nhiên đến Laravel controller

function initializePaymentForm() {
    // Payment form sẽ submit tự nhiên đến Laravel controller
    // Không cần prevent default
}

function initializeBillingToggle() {
    const billingSameCheckbox = document.getElementById('billingSameAsShipping');
    const billingSection = document.getElementById('billingAddressSection');

    if (billingSameCheckbox && billingSection) {
        billingSameCheckbox.addEventListener('change', function() {
            if (this.checked) {
                billingSection.classList.add('d-none');
            } else {
                billingSection.classList.remove('d-none');
            }
        });
    }
}

function initializePaymentMethods() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');

    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            showPaymentDetails(this.value);
        });
    });

    // Show initial payment details
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
    if (selectedMethod) {
        showPaymentDetails(selectedMethod.value);
    }
}

function showPaymentDetails(method) {
    // Hide all payment details
    document.querySelectorAll('.payment-details').forEach(detail => {
        detail.classList.add('d-none');
    });

    // Show selected payment details
    const detailsElement = document.getElementById(method + 'Details');
    if (detailsElement) {
        detailsElement.classList.remove('d-none');

        // Generate QR code for SePay
        if (method === 'sepay') {
            setTimeout(() => {
                generateQRCode();
            }, 100); // Small delay to ensure DOM is ready
        }
    }
}

// Form submit sẽ được xử lý bởi Laravel tự động

function processPaymentStep() {
    const form = document.getElementById('paymentForm');
    const formData = new FormData(form);
    const data = {};

    // Convert FormData to object
    for (let [key, value] of formData.entries()) {
        if (key.includes('[') && key.includes(']')) {
            const matches = key.match(/^([^[]+)\[([^]]+)\]$/);
            if (matches) {
                const parentKey = matches[1];
                const childKey = matches[2];
                if (!data[parentKey]) data[parentKey] = {};
                data[parentKey][childKey] = value;
            }
        } else {
            data[key] = value;
        }
    }

    showLoading(true);

    fetch('/marketplace/checkout/payment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Load order review
            loadOrderReview();

            // Move to next step
            nextStep();

            showToast('success', data.message);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', window.checkoutTranslations.failedToProcessPayment);
    })
    .finally(() => {
        showLoading(false);
    });
}

function loadOrderReview() {
    const reviewContainer = document.getElementById('orderReview');

    fetch('/marketplace/checkout/review')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            reviewContainer.innerHTML = generateOrderReviewHTML(data);
        } else {
            reviewContainer.innerHTML = '<div class="alert alert-danger">' + window.checkoutTranslations.failedToLoadReview + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        reviewContainer.innerHTML = '<div class="alert alert-danger">' + window.checkoutTranslations.failedToLoadReview + '</div>';
    });
}

function generateOrderReviewHTML(data) {
    return `
        <div class="row">
            <div class="col-md-6">
                <h6>Shipping Address</h6>
                <address class="mb-3">
                    ${data.shipping_address.first_name} ${data.shipping_address.last_name}<br>
                    ${data.shipping_address.address_line_1}<br>
                    ${data.shipping_address.address_line_2 ? data.shipping_address.address_line_2 + '<br>' : ''}
                    ${data.shipping_address.city}, ${data.shipping_address.state} ${data.shipping_address.postal_code}<br>
                    ${data.shipping_address.country}
                </address>
            </div>
            <div class="col-md-6">
                <h6>Payment Method</h6>
                <p class="mb-3">${getPaymentMethodLabel(data.payment_method)}</p>
            </div>
        </div>

        <h6>Order Items</h6>
        <div class="table-responsive mb-3">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.items.map(item => `
                        <tr>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                            <td>${item.unit_price.toLocaleString()} VNĐ</td>
                            <td>${item.total_price.toLocaleString()} VNĐ</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-6 offset-md-6">
                <div class="order-summary">
                    <div class="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <span>${data.order_summary.subtotal.toLocaleString()} VNĐ</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Shipping:</span>
                        <span>${data.order_summary.shipping_amount.toLocaleString()} VNĐ</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Tax:</span>
                        <span>${data.order_summary.tax_amount.toLocaleString()} VNĐ</span>
                    </div>
                    ${data.order_summary.discount_amount > 0 ? `
                        <div class="d-flex justify-content-between text-success">
                            <span>Discount:</span>
                            <span>-${data.order_summary.discount_amount.toLocaleString()} VNĐ</span>
                        </div>
                    ` : ''}
                    <hr>
                    <div class="d-flex justify-content-between fw-bold h5">
                        <span>Total:</span>
                        <span>${data.order_summary.total_amount.toLocaleString()} VNĐ</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-outline-secondary" onclick="previousStep()">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Payment
            </button>
            <button type="button" class="btn btn-success btn-lg" onclick="placeOrder()">
                <i class="fas fa-check-circle me-2"></i>
                Place Order
            </button>
        </div>
    `;
}

function getPaymentMethodLabel(method) {
    const labels = {
        'stripe': window.checkoutTranslations.creditDebitCard,
        'sepay': window.checkoutTranslations.bankTransfer
    };
    return labels[method] || method;
}

function nextStep() {
    if (currentStep < totalSteps) {
        // Mark current step as completed
        const currentStepElement = document.querySelector(`.step[data-step="${currentStep}"]`);
        currentStepElement.classList.remove('active');
        currentStepElement.classList.add('completed');

        // Hide current step content
        document.getElementById(`step${currentStep}`).classList.remove('active');

        // Move to next step
        currentStep++;

        // Show next step
        const nextStepElement = document.querySelector(`.step[data-step="${currentStep}"]`);
        nextStepElement.classList.add('active');
        document.getElementById(`step${currentStep}`).classList.add('active');

        // Update progress lines
        updateProgressLines();
    }
}

function previousStep() {
    if (currentStep > 1) {
        // Hide current step content
        document.getElementById(`step${currentStep}`).classList.remove('active');

        // Mark current step as inactive
        const currentStepElement = document.querySelector(`.step[data-step="${currentStep}"]`);
        currentStepElement.classList.remove('active');

        // Move to previous step
        currentStep--;

        // Show previous step
        const prevStepElement = document.querySelector(`.step[data-step="${currentStep}"]`);
        prevStepElement.classList.remove('completed');
        prevStepElement.classList.add('active');
        document.getElementById(`step${currentStep}`).classList.add('active');

        // Update progress lines
        updateProgressLines();
    }
}

function updateProgressLines() {
    const lines = document.querySelectorAll('.step-line');
    lines.forEach((line, index) => {
        if (index < currentStep - 1) {
            line.classList.add('completed');
        } else {
            line.classList.remove('completed');
        }
    });
}

function placeOrder() {
    if (!confirm('Are you sure you want to place this order?')) {
        return;
    }

    showLoading(true);

    fetch('/marketplace/checkout/place-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);

            // Check if we need to process payment
            if (data.payment_required) {
                processPaymentFlow(data.order_id, data.payment_method);
            } else {
                // Redirect to success page
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1500);
            }
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to place order');
    })
    .finally(() => {
        showLoading(false);
    });
}

function processPaymentFlow(orderId, paymentMethod) {
    if (paymentMethod === 'sepay') {
        processSepayPayment(orderId);
    } else if (paymentMethod === 'stripe') {
        processStripePayment(orderId);
    }
}

function processSepayPayment(orderId) {
    showLoading(true);

    // 🏦 Use Centralized Payment System
    fetch('/api/v1/payment/centralized/sepay/create-payment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': 'Bearer ' + getAuthToken(),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            order_id: orderId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSepayPaymentModal(data.data, orderId);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to create SePay payment');
    })
    .finally(() => {
        showLoading(false);
    });
}

function processStripePayment(orderId) {
    showLoading(true);

    // 🏦 Use Centralized Payment System
    fetch('/api/v1/payment/centralized/stripe/create-intent', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': 'Bearer ' + getAuthToken(),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            order_id: orderId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.data.payment_url) {
                // Redirect to Stripe payment page
                window.location.href = data.data.payment_url;
            } else if (data.data.client_secret) {
                // Handle Stripe Elements integration
                showStripePaymentModal(data.data, orderId);
            } else {
                showToast('error', 'Invalid payment response');
            }
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to create Stripe payment');
    })
    .finally(() => {
        showLoading(false);
    });
}

function showStripePaymentModal(paymentData, orderId) {
    // For now, just redirect to a simple success page
    // In a real implementation, you would integrate Stripe Elements here
    showToast('info', 'Stripe payment integration coming soon');
    setTimeout(() => {
        window.location.href = `/marketplace/orders/${orderId}/success`;
    }, 2000);
}

function getAuthToken() {
    // Get auth token from meta tag or localStorage
    const token = document.querySelector('meta[name="api-token"]')?.getAttribute('content');
    return token || localStorage.getItem('auth_token') || '';
}

function showSepayPaymentModal(paymentData, orderId) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'sepayPaymentModal';
    modal.setAttribute('tabindex', '-1');
    modal.setAttribute('aria-hidden', 'true');

    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-qrcode me-2"></i>
                        Thanh toán qua SePay
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <h6 class="mb-3">Quét mã QR để thanh toán</h6>
                            <img src="${paymentData.qr_url}" alt="QR Code" class="img-fluid mb-3" style="max-width: 250px;">
                            <div class="alert alert-info">
                                <small>Sử dụng app ngân hàng để quét mã QR</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Thông tin chuyển khoản</h6>
                            <div class="bank-info">
                                <div class="mb-2">
                                    <strong>Ngân hàng:</strong> ${paymentData.bank_info.bank_code}
                                </div>
                                <div class="mb-2">
                                    <strong>Số tài khoản:</strong>
                                    <span class="text-primary">${paymentData.bank_info.account_number}</span>
                                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('${paymentData.bank_info.account_number}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <div class="mb-2">
                                    <strong>Chủ tài khoản:</strong> ${paymentData.bank_info.account_name}
                                </div>
                                <div class="mb-2">
                                    <strong>Số tiền:</strong>
                                    <span class="text-danger fw-bold">${paymentData.bank_info.amount.toLocaleString()} VNĐ</span>
                                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('${paymentData.amount}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <div class="mb-3">
                                    <strong>Nội dung:</strong>
                                    <span class="text-primary">${paymentData.bank_info.content}</span>
                                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('${paymentData.bank_info.content}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="payment-status">
                                <div class="alert alert-warning" id="paymentStatusAlert">
                                    <i class="fas fa-clock me-2"></i>
                                    Đang chờ thanh toán...
                                </div>
                                <div class="progress mb-3">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                         role="progressbar" style="width: 0%" id="paymentProgress">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="checkPaymentStatus(${orderId})">
                        <i class="fas fa-sync-alt me-2"></i>
                        Kiểm tra thanh toán
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    // Start polling payment status
    startPaymentStatusPolling(orderId);

    // Clean up modal when closed
    modal.addEventListener('hidden.bs.modal', function() {
        stopPaymentStatusPolling();
        document.body.removeChild(modal);
    });
}

let paymentPollingInterval = null;
let paymentPollingAttempts = 0;
const MAX_POLLING_ATTEMPTS = 120; // 10 minutes (5 second intervals)

function startPaymentStatusPolling(orderId) {
    paymentPollingAttempts = 0;

    paymentPollingInterval = setInterval(() => {
        checkPaymentStatus(orderId);
        paymentPollingAttempts++;

        // Update progress bar
        const progress = Math.min((paymentPollingAttempts / MAX_POLLING_ATTEMPTS) * 100, 100);
        const progressBar = document.getElementById('paymentProgress');
        if (progressBar) {
            progressBar.style.width = progress + '%';
        }

        // Stop polling after max attempts
        if (paymentPollingAttempts >= MAX_POLLING_ATTEMPTS) {
            stopPaymentStatusPolling();
            updatePaymentStatus('timeout', 'Hết thời gian chờ thanh toán. Vui lòng kiểm tra lại.');
        }
    }, 5000); // Check every 5 seconds
}

function stopPaymentStatusPolling() {
    if (paymentPollingInterval) {
        clearInterval(paymentPollingInterval);
        paymentPollingInterval = null;
    }
}

function checkPaymentStatus(orderId) {
    fetch('/api/v1/payment/sepay/check-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            order_id: orderId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.payment_status === 'completed') {
                stopPaymentStatusPolling();
                updatePaymentStatus('success', 'Thanh toán thành công!');

                setTimeout(() => {
                    window.location.href = `/marketplace/orders/${orderId}/success`;
                }, 2000);
            } else if (data.payment_status === 'failed') {
                stopPaymentStatusPolling();
                updatePaymentStatus('error', 'Thanh toán thất bại. Vui lòng thử lại.');
            }
            // Continue polling if still pending
        }
    })
    .catch(error => {
        console.error('Error checking payment status:', error);
    });
}

function updatePaymentStatus(type, message) {
    const alertElement = document.getElementById('paymentStatusAlert');
    if (alertElement) {
        alertElement.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'}`;
        alertElement.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'clock'} me-2"></i>
            ${message}
        `;
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('success', 'Đã sao chép vào clipboard');
    }).catch(err => {
        console.error('Failed to copy: ', err);
        showToast('error', 'Không thể sao chép');
    });
}

function copyTransferContent() {
    const transferContent = document.getElementById('transferContent').textContent;
    copyToClipboard(transferContent);
}

function generateQRCode() {
    const transferContent = document.getElementById('transferContent').textContent;
    const bankAccount = '0903252427';
    const bankName = 'MBBank';
    const accountName = 'Bui Tan Viet';
    const amount = {{ $total ?? 0 }};

    // Create QR content for Vietnamese banking
    const qrContent = `2|99|${bankName}|${bankAccount}|${accountName}|${transferContent}|0|0|${amount}|`;

    // Clear existing QR code
    const qrCodeElement = document.getElementById('qrcode');
    qrCodeElement.innerHTML = '';

    try {
        // Generate QR code using qrcode-generator library
        if (typeof qrcode !== 'undefined') {
            const qr = qrcode(0, 'M');
            qr.addData(qrContent);
            qr.make();

            // Create QR code as image
            const qrImage = qr.createImgTag(4, 8);
            qrCodeElement.innerHTML = qrImage;
        } else {
            // Fallback: Create a simple placeholder
            qrCodeElement.innerHTML = `
                <div class="qr-placeholder p-3 border rounded text-center" style="width: 200px; height: 200px; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                    <div>
                        <i class="fas fa-qrcode fa-3x text-muted mb-2"></i>
                        <div class="small text-muted">QR Code sẽ được tạo<br>khi thanh toán</div>
                    </div>
                </div>
            `;
        }
    } catch (error) {
        console.error('QR Code generation failed:', error);
        qrCodeElement.innerHTML = '<div class="text-muted">Không thể tạo QR Code</div>';
    }
}

function prefillUserData() {
    // Pre-fill user data if available
    @auth
        const userEmail = '{{ auth()->user()->email }}';
        const emailInput = document.querySelector('input[name="shipping_address[email]"]');
        if (emailInput && !emailInput.value) {
            emailInput.value = userEmail;
        }
    @endauth
}

function showLoading(show) {
    const overlay = document.getElementById('loadingOverlay');
    if (show) {
        overlay.classList.remove('d-none');
    } else {
        overlay.classList.add('d-none');
    }
}

function showToast(type, message) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}
</script>
@endpush
