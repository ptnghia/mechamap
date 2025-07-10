@extends('layouts.app')

@section('title', __('marketplace.cart.title') . ' - MechaMap')

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
                            <i class="house me-2"></i>
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('marketplace.index') }}" class="text-decoration-none">Marketplace</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('marketplace.cart.shopping_cart') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>
                            {{ __('marketplace.cart.shopping_cart') }}
                            @if(!$cart->isEmpty())
                                <span class="badge bg-primary ms-2">{{ $cart->total_items }} {{ __('marketplace.cart.items') }}</span>
                            @endif
                        </h5>
                        @if(!$cart->isEmpty())
                            <div class="d-flex gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                    <label class="form-check-label" for="selectAll">
                                        {{ __('marketplace.cart.select_all') }}
                                    </label>
                                </div>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="removeSelected()" id="removeSelectedBtn" style="display: none;">
                                    <i class="fas fa-trash me-1"></i>
                                    {{ __('marketplace.cart.remove_selected') }}
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearCart()">
                                    <i class="fas fa-trash me-1"></i>
                                    {{ __('marketplace.cart.clear_cart') }}
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($cart->isEmpty())
                            <!-- Empty Cart State -->
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart-x text-muted" style="font-size: 4rem;"></i>
                                <h4 class="mt-3">Your cart is empty</h4>
                                <p class="text-muted">Add some products to get started</p>
                                <a href="{{ route('marketplace.products.index') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Continue Shopping
                                </a>
                            </div>
                        @else
                            <!-- Cart Items -->
                            <div id="cartItems">
                                @foreach($cart->items as $item)
                                    <div class="cart-item cart-item-row border-bottom py-3" data-item-id="{{ $item->id }}" style="transition: all 0.3s ease;">
                                        <div class="row align-items-center">
                                            <!-- Checkbox -->
                                            <div class="col-auto">
                                                <div class="form-check">
                                                    <input class="form-check-input item-checkbox" type="checkbox" value="{{ $item->id }}" onchange="updateSelectedItems()">
                                                </div>
                                            </div>

                                            <!-- Product Image -->
                                            <div class="col-md-2 col-3">
                                                @if($item->product_image)
                                                    <img src="{{ $item->product_image }}" class="img-fluid rounded" alt="{{ $item->product_name }}" style="height: 80px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Product Info -->
                                            <div class="col-md-4 col-9">
                                                <h6 class="mb-1 product-name">
                                                    @if($item->product)
                                                        <a href="{{ route('marketplace.products.show', $item->product->slug) }}" class="text-decoration-none">
                                                            {{ $item->product_name }}
                                                        </a>
                                                    @else
                                                        {{ $item->product_name }}
                                                        <small class="text-danger">({{ __('marketplace.cart.product_no_longer_available') }})</small>
                                                    @endif
                                                </h6>
                                                @if($item->product_sku)
                                                    <small class="text-muted">SKU: {{ $item->product_sku }}</small>
                                                @endif
                                                @if($item->product && $item->product->seller)
                                                    <div class="small text-muted">
                                                        by {{ $item->product->seller->business_name ?? $item->product->seller->user->name }}
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Price -->
                                            <div class="col-md-2 col-6">
                                                @if($item->is_on_sale)
                                                    <div class="text-danger fw-bold">{{ number_format($item->sale_price) }} VNĐ</div>
                                                    <div class="text-muted text-decoration-line-through small">{{ number_format($item->unit_price) }} VNĐ</div>
                                                    <div class="text-success small">Tiết kiệm {{ number_format($item->savings) }} VNĐ</div>
                                                @else
                                                    <div class="fw-bold">{{ number_format($item->unit_price) }} VNĐ</div>
                                                @endif
                                            </div>

                                            <!-- Quantity -->
                                            <div class="col-md-2 col-6">
                                                <div class="input-group input-group-sm">
                                                    <button class="btn btn-outline-secondary quantity-btn-minus" type="button"
                                                            onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" class="form-control text-center quantity-input"
                                                           value="{{ $item->quantity }}"
                                                           min="1"
                                                           max="{{ $item->product && $item->product->manage_stock ? $item->product->stock_quantity : 100 }}"
                                                           data-item-id="{{ $item->id }}"
                                                           data-unit-price="{{ $item->is_on_sale ? $item->sale_price : $item->unit_price }}">
                                                    <button class="btn btn-outline-secondary quantity-btn-plus" type="button"
                                                            onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                                            {{ $item->product && $item->product->manage_stock && $item->quantity >= $item->product->stock_quantity ? 'disabled' : '' }}>
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                @if($item->product && $item->product->manage_stock)
                                                    <small class="text-muted">{{ $item->product->stock_quantity }} {{ __('marketplace.cart.available') }}</small>
                                                @endif
                                            </div>

                                            <!-- Total & Actions -->
                                            <div class="col-md-2 col-12 mt-2 mt-md-0">
                                                <div class="text-center">
                                                    <div class="fw-bold mb-2 item-total-price">${{ number_format($item->total_price, 2) }}</div>
                                                    <div class="d-flex flex-column gap-1">
                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-item-btn"
                                                                data-item-id="{{ $item->id }}" title="{{ __('marketplace.cart.remove') }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="saveForLater({{ $item->id }})" title="{{ __('marketplace.cart.save_for_later') }}">
                                                            <i class="fas fa-heart"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Continue Shopping -->
                            <div class="mt-4">
                                <a href="{{ route('marketplace.products.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    {{ __('marketplace.cart.continue_shopping') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            @if(!$cart->isEmpty())
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('marketplace.cart.order_summary') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('marketplace.cart.subtotal') }} ({{ $cart->total_items }} {{ __('marketplace.cart.items') }})</span>
                                <span class="cart-subtotal">{{ number_format($cart->subtotal) }} VNĐ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('marketplace.cart.shipping') }}</span>
                                <span>
                                    @if($cart->shipping_amount > 0)
                                        {{ number_format($cart->shipping_amount) }} VNĐ
                                    @else
                                        <span class="text-success">{{ __('marketplace.cart.free') }}</span>
                                    @endif
                                </span>
                            </div>

                            <!-- Shipping Calculator -->
                            <div class="mb-3">
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" data-bs-toggle="collapse" data-bs-target="#shippingCalculator">
                                    <i class="fas fa-calculator me-1"></i>
                                    {{ __('marketplace.cart.calculate_shipping') }}
                                </button>
                                <div class="collapse mt-2" id="shippingCalculator">
                                    <div class="border rounded p-3">
                                        <div class="mb-2">
                                            <label class="form-label small">Country</label>
                                            <select class="form-select form-select-sm">
                                                <option value="US">United States</option>
                                                <option value="VN" selected>Vietnam</option>
                                                <option value="CA">Canada</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">Zip Code</label>
                                            <input type="text" class="form-control form-control-sm" placeholder="Enter zip code">
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm">Calculate</button>
                                    </div>
                                </div>
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
                                <span class="cart-total">{{ number_format($cart->total_amount) }} VNĐ</span>
                            </div>

                            <!-- Coupon Code -->
                            <div class="mt-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="{{ __('marketplace.cart.coupon_code') }}" id="couponCode">
                                    <button class="btn btn-outline-secondary" type="button" onclick="applyCoupon()">{{ __('marketplace.cart.apply') }}</button>
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <div class="d-grid mt-4">
                                <button type="button" class="btn btn-primary btn-lg" onclick="proceedToCheckout()">
                                    <i class="fas fa-credit-card me-2"></i>
                                    {{ __('marketplace.cart.proceed_to_checkout') }}
                                </button>
                            </div>

                            <!-- Security Info -->
                            <div class="mt-3 text-center">
                                <small class="text-muted">
                                    <i class="shield-check me-1"></i>
                                    {{ __('marketplace.cart.ssl_encryption') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Info -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="truck me-2"></i>
                                {{ __('marketplace.cart.shipping_information') }}
                            </h6>
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-1">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    {{ __('marketplace.cart.free_shipping_over') }}
                                </li>
                                <li class="mb-1">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    {{ __('marketplace.cart.standard_delivery') }}
                                </li>
                                <li class="mb-1">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    {{ __('marketplace.cart.express_delivery_available') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Recently Viewed Products -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock-history me-2"></i>
                            Recently Viewed Products
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row" id="recentlyViewedProducts">
                            <div class="col-12 text-center text-muted py-3">
                                <i class="fas fa-eye" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2">No recently viewed products</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommended Products -->
        @if(!$cart->isEmpty())
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="lightbulb me-2"></i>
                            You Might Also Like
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row" id="recommendedProducts">
                            <div class="col-12 text-center text-muted py-3">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mb-0 mt-2">Loading recommendations...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
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
.cart-item {
    transition: background-color 0.2s ease;
}

.cart-item:hover {
    background-color: #f8f9fa;
}

.input-group-sm .form-control {
    max-width: 60px;
}

@media (max-width: 768px) {
    .cart-item .row > div {
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<!-- Include Cart UX Enhancements -->
<script src="{{ asset('assets/js/cart-ux-enhancements.js') }}"></script>

<script>
// Translation keys for JavaScript
window.cartTranslations = {
    clearCart: '{{ __('marketplace.cart.clear_cart') }}',
    clearCartConfirm: '{{ __('marketplace.cart.clear_cart_confirm') }}',
    clearCartSuccess: '{{ __('marketplace.cart.clear_cart_success') }}',
    clearCartFailed: '{{ __('marketplace.cart.clear_cart_failed') }}',
    removeSelectedConfirm: '{{ __('marketplace.cart.remove_selected_confirm', ['count' => ':count']) }}',
    removeSelectedFailed: '{{ __('marketplace.cart.remove_selected_failed') }}',
    pleaseSelectItems: '{{ __('marketplace.cart.please_select_items') }}',
    couponRequired: '{{ __('marketplace.cart.coupon_required') }}',
    couponApplyFailed: '{{ __('marketplace.cart.coupon_apply_failed') }}',
    saveForLaterMessage: '{{ __('marketplace.cart.save_for_later_message') }}',
    loading: '{{ __('marketplace.loading') }}',
    error: '{{ __('marketplace.error') }}',
    success: '{{ __('marketplace.success') }}',
    warning: '{{ __('marketplace.warning') }}'
};
</script>

<script>
// Legacy function wrappers for backward compatibility
function updateQuantity(itemId, quantity) {
    // Use new UX enhancement system
    if (window.cartUX) {
        const input = document.querySelector(`[data-item-id="${itemId}"] .quantity-input`);
        if (input) {
            input.value = quantity;
            window.cartUX.handleQuantityChange(input);
        }
    }
}

function removeItem(itemId) {
    // Use new UX enhancement system
    if (window.cartUX) {
        window.cartUX.handleItemRemoval(itemId);
    }
}

function clearCart() {
    // Use new UX enhancement system
    if (window.cartUX) {
        window.cartUX.showConfirmation({
            title: window.cartTranslations.clearCart,
            message: window.cartTranslations.clearCartConfirm,
            icon: 'fa-trash text-danger',
            confirmText: window.cartTranslations.clearCart,
            confirmClass: 'btn-danger',
            onConfirm: () => {
                performClearCart();
            }
        });
    }
}

function performClearCart() {
    const clearBtn = document.querySelector('[onclick="clearCart()"]');
    const stopLoading = window.cartUX ? window.cartUX.showEnhancedLoading(clearBtn, 'Clearing...') : () => {};

    fetch('/marketplace/cart/clear', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (window.cartUX) {
                window.cartUX.showToast('success', 'Success', 'Cart cleared successfully');
                setTimeout(() => location.reload(), 1000);
            } else {
                location.reload();
            }
        } else {
            if (window.cartUX) {
                window.cartUX.showToast('error', window.cartTranslations.error, data.message || window.cartTranslations.clearCartFailed);
            } else {
                alert(data.message);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (window.cartUX) {
            window.cartUX.showToast('error', window.cartTranslations.error, window.cartTranslations.clearCartFailed);
        } else {
            alert(window.cartTranslations.clearCartFailed);
        }
    })
    .finally(() => {
        stopLoading();
    });
}

function applyCoupon() {
    const couponCode = document.getElementById('couponCode').value.trim();
    if (!couponCode) {
        if (window.cartUX) {
            window.cartUX.showToast('warning', window.cartTranslations.warning, window.cartTranslations.couponRequired);
        } else {
            alert(window.cartTranslations.couponRequired);
        }
        return;
    }

    const applyBtn = document.querySelector('[onclick="applyCoupon()"]');
    const stopLoading = window.cartUX ? window.cartUX.showEnhancedLoading(applyBtn, 'Applying...') : () => {};

    fetch('/marketplace/cart/coupon', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ coupon_code: couponCode })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(window.cartTranslations.couponApplyFailed);
    })
    .finally(() => {
        showLoading(false);
    });
}

function proceedToCheckout() {
    showLoading(true);
    window.location.href = '/marketplace/cart/checkout';
}

function showLoading(show) {
    const overlay = document.getElementById('loadingOverlay');
    if (show) {
        overlay.classList.remove('d-none');
    } else {
        overlay.classList.add('d-none');
    }
}

// Bulk actions functionality
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');

    itemCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });

    updateSelectedItems();
}

function updateSelectedItems() {
    const selectedItems = document.querySelectorAll('.item-checkbox:checked');
    const removeSelectedBtn = document.getElementById('removeSelectedBtn');
    const selectAllCheckbox = document.getElementById('selectAll');
    const allCheckboxes = document.querySelectorAll('.item-checkbox');

    // Show/hide remove selected button
    if (removeSelectedBtn) {
        removeSelectedBtn.style.display = selectedItems.length > 0 ? 'inline-block' : 'none';
    }

    // Update select all checkbox state
    if (selectAllCheckbox && allCheckboxes.length > 0) {
        if (selectedItems.length === allCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (selectedItems.length > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }
}

function removeSelected() {
    const selectedItems = document.querySelectorAll('.item-checkbox:checked');
    if (selectedItems.length === 0) {
        alert(window.cartTranslations.pleaseSelectItems);
        return;
    }

    if (!confirm(window.cartTranslations.removeSelectedConfirm.replace(':count', selectedItems.length))) {
        return;
    }

    showLoading(true);

    const itemIds = Array.from(selectedItems).map(checkbox => checkbox.value);
    const promises = itemIds.map(itemId =>
        fetch(`/marketplace/cart/remove/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
    );

    Promise.all(promises)
        .then(responses => Promise.all(responses.map(r => r.json())))
        .then(results => {
            const successful = results.filter(r => r.success).length;
            if (successful > 0) {
                location.reload();
            } else {
                alert(window.cartTranslations.removeSelectedFailed);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(window.cartTranslations.removeSelectedFailed);
        })
        .finally(() => {
            showLoading(false);
        });
}

function saveForLater(itemId) {
    // TODO: Implement save for later functionality
    alert(window.cartTranslations.saveForLaterMessage);
}

// Auto-save cart changes
let autoSaveTimeout;
function autoSaveCart() {
    clearTimeout(autoSaveTimeout);
    autoSaveTimeout = setTimeout(() => {
        // Auto-save cart state to prevent data loss
        console.log('Auto-saving cart state...');
    }, 2000);
}

// Validate cart on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize bulk actions
    updateSelectedItems();

    // Validate cart
    fetch('/marketplace/cart/validate', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.has_issues) {
            let message = 'Cart validation issues found:\n';
            data.issues.forEach(issue => {
                message += `- ${issue.product_name}: ${issue.issue}\n`;
            });

            if (data.has_changes) {
                message += '\nYour cart has been updated automatically.';
                setTimeout(() => location.reload(), 2000);
            }

            alert(message);
        }
    })
    .catch(error => console.error('Cart validation error:', error));

    // Add quantity change listeners for auto-save
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', autoSaveCart);
    });
});
</script>
@endpush
