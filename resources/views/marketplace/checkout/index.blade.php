@extends('layouts.app')

@section('title', 'Checkout - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/cart-ux-enhancements.css') }}">
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
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
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
                            Secure Checkout
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
                                <div class="step active" data-step="1">
                                    <div class="step-circle">
                                        <span class="step-number">1</span>
                                        <i class="fas fa-check step-check d-none"></i>
                                    </div>
                                    <div class="step-label">Shipping</div>
                                </div>
                                <div class="step-line"></div>
                                <div class="step" data-step="2">
                                    <div class="step-circle">
                                        <span class="step-number">2</span>
                                        <i class="fas fa-check step-check d-none"></i>
                                    </div>
                                    <div class="step-label">Payment</div>
                                </div>
                                <div class="step-line"></div>
                                <div class="step" data-step="3">
                                    <div class="step-circle">
                                        <span class="step-number">3</span>
                                        <i class="fas fa-check step-check d-none"></i>
                                    </div>
                                    <div class="step-label">Review</div>
                                </div>
                            </div>
                        </div>

                        <!-- Step Content -->
                        <div id="checkoutSteps">
                            <!-- Step 1: Shipping Information -->
                            <div class="step-content {{ session('step') !== 'payment' ? 'active' : '' }}" id="step1">
                                <h6 class="mb-3">Shipping Information</h6>
                                <form action="{{ route('marketplace.checkout.shipping') }}" method="POST" id="shippingForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">First Name *</label>
                                            <input type="text" class="form-control" name="shipping_address[first_name]" value="{{ old('shipping_address.first_name') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Last Name *</label>
                                            <input type="text" class="form-control" name="shipping_address[last_name]" value="{{ old('shipping_address.last_name') }}" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email Address *</label>
                                            <input type="email" class="form-control" name="shipping_address[email]" value="{{ old('shipping_address.email') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" name="shipping_address[phone]" value="{{ old('shipping_address.phone') }}">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Address Line 1 *</label>
                                        <input type="text" class="form-control" name="shipping_address[address_line_1]" value="{{ old('shipping_address.address_line_1') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Address Line 2</label>
                                        <input type="text" class="form-control" name="shipping_address[address_line_2]" value="{{ old('shipping_address.address_line_2') }}">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">City *</label>
                                            <input type="text" class="form-control" name="shipping_address[city]" value="{{ old('shipping_address.city') }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">State/Province *</label>
                                            <input type="text" class="form-control" name="shipping_address[state]" value="{{ old('shipping_address.state') }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Postal Code *</label>
                                            <input type="text" class="form-control" name="shipping_address[postal_code]" value="{{ old('shipping_address.postal_code') }}" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Country *</label>
                                        <select class="form-select" name="shipping_address[country]" required>
                                            <option value="">Select Country</option>
                                            <option value="VN" selected>Vietnam</option>
                                            <option value="US">United States</option>
                                            <option value="CA">Canada</option>
                                            <option value="GB">United Kingdom</option>
                                            <option value="AU">Australia</option>
                                        </select>
                                    </div>

                                    <!-- Billing Address -->
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="billingSameAsShipping" name="billing_same_as_shipping" checked>
                                            <label class="form-check-label" for="billingSameAsShipping">
                                                Billing address is the same as shipping address
                                            </label>
                                        </div>
                                    </div>

                                    <div id="billingAddressSection" class="d-none">
                                        <h6 class="mb-3">Billing Information</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">First Name *</label>
                                                <input type="text" class="form-control" name="billing_address[first_name]">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Last Name *</label>
                                                <input type="text" class="form-control" name="billing_address[last_name]">
                                            </div>
                                        </div>
                                        <!-- Add more billing fields as needed -->
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('marketplace.cart.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Back to Cart
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            Continue to Payment
                                            <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Step 2: Payment Information -->
                            <div class="step-content {{ session('step') === 'payment' ? 'active' : '' }}" id="step2">
                                <h6 class="mb-3">Payment Information</h6>
                                <form id="paymentForm" action="{{ route('marketplace.checkout.payment') }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="form-label">Payment Method</label>
                                        <div class="payment-methods">
                                            <div class="form-check payment-option">
                                                <input class="form-check-input" type="radio" name="payment_method" id="creditCard" value="credit_card" checked>
                                                <label class="form-check-label" for="creditCard">
                                                    <i class="fas fa-credit-card me-2"></i>
                                                    Credit/Debit Card
                                                </label>
                                            </div>
                                            <div class="form-check payment-option">
                                                <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                                <label class="form-check-label" for="paypal">
                                                    <i class="paypal me-2"></i>
                                                    PayPal
                                                </label>
                                            </div>
                                            <div class="form-check payment-option">
                                                <input class="form-check-input" type="radio" name="payment_method" id="bankTransfer" value="bank_transfer">
                                                <label class="form-check-label" for="bankTransfer">
                                                    <i class="bank me-2"></i>
                                                    Bank Transfer
                                                </label>
                                            </div>
                                            <div class="form-check payment-option">
                                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod">
                                                <label class="form-check-label" for="cod">
                                                    <i class="cash me-2"></i>
                                                    Cash on Delivery
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Credit Card Details -->
                                    <div id="creditCardDetails" class="payment-details">
                                        <div class="mb-3">
                                            <label class="form-label">Card Number *</label>
                                            <input type="text" class="form-control" name="payment_details[card_number]" placeholder="1234 5678 9012 3456">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Expiry Date *</label>
                                                <input type="text" class="form-control" name="payment_details[expiry_date]" placeholder="MM/YY">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">CVV *</label>
                                                <input type="text" class="form-control" name="payment_details[cvv]" placeholder="123">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Cardholder Name *</label>
                                            <input type="text" class="form-control" name="payment_details[cardholder_name]">
                                        </div>
                                    </div>

                                    <!-- PayPal Details -->
                                    <div id="paypalDetails" class="payment-details d-none">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            You will be redirected to PayPal to complete your payment.
                                        </div>
                                    </div>

                                    <!-- Bank Transfer Details -->
                                    <div id="bankTransferDetails" class="payment-details d-none">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Bank transfer details will be provided after order confirmation.
                                        </div>
                                    </div>

                                    <!-- COD Details -->
                                    <div id="codDetails" class="payment-details d-none">
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i>
                                            You will pay when your order is delivered.
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary" onclick="previousStep()">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Back to Shipping
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            Review Order
                                            <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Step 3: Order Review -->
                            <div class="step-content {{ session('step') === 'review' ? 'active' : '' }}" id="step3">
                                <h6 class="mb-3">Review Your Order</h6>
                                @if(session('step') === 'review')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Shipping Address</h6>
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
                                            <h6>Payment Method</h6>
                                            @if(session('checkout.payment_method'))
                                                <p class="mb-3">
                                                    @switch(session('checkout.payment_method'))
                                                        @case('credit_card')
                                                            Credit/Debit Card
                                                            @break
                                                        @case('paypal')
                                                            PayPal
                                                            @break
                                                        @case('bank_transfer')
                                                            Bank Transfer
                                                            @break
                                                        @case('cod')
                                                            Cash on Delivery
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
                                            Back to Payment
                                        </button>
                                        <form action="{{ route('marketplace.checkout.place-order') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check-circle me-2"></i>
                                                Complete Order
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
                                        <div class="small fw-bold">${{ number_format($item->total_price, 2) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Order Totals -->
                        <div class="order-totals">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>${{ number_format($cart->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping</span>
                                <span id="shippingCost">Calculated at next step</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax</span>
                                <span>${{ number_format($cart->tax_amount, 2) }}</span>
                            </div>
                            @if($cart->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Discount</span>
                                    <span>-${{ number_format($cart->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between fw-bold h5">
                                <span>Total</span>
                                <span id="orderTotal">${{ number_format($cart->total_amount, 2) }}</span>
                            </div>
                        </div>

                        <!-- Security Info -->
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt-check me-1"></i>
                                Your payment information is secure and encrypted
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
        showToast('error', 'Failed to process payment information');
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
            reviewContainer.innerHTML = '<div class="alert alert-danger">Failed to load order review</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        reviewContainer.innerHTML = '<div class="alert alert-danger">Failed to load order review</div>';
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
                            <td>$${item.unit_price}</td>
                            <td>$${item.total_price}</td>
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
                        <span>$${data.order_summary.subtotal}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Shipping:</span>
                        <span>$${data.order_summary.shipping_amount}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Tax:</span>
                        <span>$${data.order_summary.tax_amount}</span>
                    </div>
                    ${data.order_summary.discount_amount > 0 ? `
                        <div class="d-flex justify-content-between text-success">
                            <span>Discount:</span>
                            <span>-$${data.order_summary.discount_amount}</span>
                        </div>
                    ` : ''}
                    <hr>
                    <div class="d-flex justify-content-between fw-bold h5">
                        <span>Total:</span>
                        <span>$${data.order_summary.total_amount}</span>
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
        'credit_card': 'Credit/Debit Card',
        'paypal': 'PayPal',
        'bank_transfer': 'Bank Transfer',
        'cod': 'Cash on Delivery'
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

            // Redirect to success page
            setTimeout(() => {
                window.location.href = data.redirect_url;
            }, 1500);
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
