@extends('layouts.unified')

@section('title', 'Cart UX Demo - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/cart-ux-enhancements.css') }}">
@endpush

@section('content')
<div class="min-vh-100 bg-light">
    <!-- Header -->
    <div class="bg-white border-bottom">
        <div class="container-fluid py-4">
            <h2 class="mb-0">🎨 Cart UX Enhancements Demo</h2>
            <p class="text-muted mb-0">Test các cải thiện trải nghiệm người dùng cho shopping cart</p>
        </div>
    </div>

    <!-- Demo Content -->
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Demo Controls -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">🧪 Demo Controls</h5>
                    </div>
                    <div class="card-body">
                        <h6>Toast Notifications</h6>
                        <div class="d-grid gap-2 mb-3">
                            <button class="btn btn-success btn-sm" onclick="demoToast('success')">Success Toast</button>
                            <button class="btn btn-danger btn-sm" onclick="demoToast('error')">Error Toast</button>
                            <button class="btn btn-warning btn-sm" onclick="demoToast('warning')">Warning Toast</button>
                            <button class="btn btn-info btn-sm" onclick="demoToast('info')">Info Toast</button>
                        </div>

                        <h6>Confirmation Modals</h6>
                        <div class="d-grid gap-2 mb-3">
                            <button class="btn btn-outline-danger btn-sm" onclick="demoConfirmation('delete')">Delete Confirmation</button>
                            <button class="btn btn-outline-warning btn-sm" onclick="demoConfirmation('clear')">Clear Confirmation</button>
                        </div>

                        <h6>Loading States</h6>
                        <div class="d-grid gap-2 mb-3">
                            <button class="btn btn-primary btn-sm" onclick="demoLoading(this)">Test Loading</button>
                            <button class="btn btn-secondary btn-sm" onclick="demoOptimisticUpdate()">Optimistic Update</button>
                        </div>

                        <h6>Animations</h6>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="demoSlideOut()">Slide Out Animation</button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="demoFadeIn()">Fade In Animation</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Demo Cart Items -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">🛒 Demo Cart Items</h5>
                    </div>
                    <div class="card-body">
                        <!-- Demo Item 1 -->
                        <div class="cart-item cart-item-row border-bottom py-3" data-item-id="demo-1" id="demo-item-1">
                            <div class="row align-items-center">
                                <div class="col-md-2 col-3">
                                    <div class="bg-primary rounded d-flex align-items-center justify-content-center cart-product-image" style="height: 80px;">
                                        <i class="fas fa-cog text-white" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <div class="col-md-4 col-9">
                                    <h6 class="mb-1 product-name">Demo Mechanical Part</h6>
                                    <small class="text-muted">SKU: DEMO-001</small>
                                    <div class="small text-muted">by Demo Supplier</div>
                                </div>
                                <div class="col-md-2 col-6">
                                    <div class="fw-bold">$99.99</div>
                                </div>
                                <div class="col-md-2 col-6">
                                    <div class="input-group input-group-sm">
                                        <button class="btn btn-outline-secondary quantity-btn-minus" type="button">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="form-control text-center quantity-input" 
                                               value="2" data-item-id="demo-1" data-unit-price="99.99">
                                        <button class="btn btn-outline-secondary quantity-btn-plus" type="button">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2 col-12 mt-2 mt-md-0">
                                    <div class="text-center">
                                        <div class="fw-bold mb-2 item-total-price">$199.98</div>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-item-btn" 
                                                data-item-id="demo-1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Demo Item 2 -->
                        <div class="cart-item cart-item-row border-bottom py-3" data-item-id="demo-2" id="demo-item-2">
                            <div class="row align-items-center">
                                <div class="col-md-2 col-3">
                                    <div class="bg-success rounded d-flex align-items-center justify-content-center cart-product-image" style="height: 80px;">
                                        <i class="fas fa-tools text-white" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <div class="col-md-4 col-9">
                                    <h6 class="mb-1 product-name">Demo Engineering Tool</h6>
                                    <small class="text-muted">SKU: DEMO-002</small>
                                    <div class="small text-muted">by Demo Manufacturer</div>
                                </div>
                                <div class="col-md-2 col-6">
                                    <div class="text-danger fw-bold price-sale">$149.99</div>
                                    <div class="text-muted text-decoration-line-through small price-original">$199.99</div>
                                    <div class="text-success small price-savings">Save $50.00</div>
                                </div>
                                <div class="col-md-2 col-6">
                                    <div class="input-group input-group-sm">
                                        <button class="btn btn-outline-secondary quantity-btn-minus" type="button">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="form-control text-center quantity-input" 
                                               value="1" data-item-id="demo-2" data-unit-price="149.99">
                                        <button class="btn btn-outline-secondary quantity-btn-plus" type="button">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2 col-12 mt-2 mt-md-0">
                                    <div class="text-center">
                                        <div class="fw-bold mb-2 item-total-price">$149.99</div>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-item-btn" 
                                                data-item-id="demo-2">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Demo Animation Target -->
                        <div class="mt-4 p-4 bg-light rounded" id="animation-target" style="display: none;">
                            <div class="text-center">
                                <i class="fas fa-magic text-primary" style="font-size: 2rem;"></i>
                                <h5 class="mt-2">Animation Demo</h5>
                                <p class="text-muted">This element demonstrates fade-in animation</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Demo Cart Summary -->
                <div class="card mt-4 cart-summary">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal (3 items)</span>
                            <span class="cart-subtotal">$349.97</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <span class="text-success">Free</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax</span>
                            <span>$34.99</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold h5">
                            <span>Total</span>
                            <span class="cart-total">$384.96</span>
                        </div>
                        <button class="btn checkout-btn w-100 mt-3">
                            <i class="fas fa-lock me-2"></i>
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/cart-ux-enhancements.js') }}"></script>

<script>
// Demo functions
function demoToast(type) {
    if (window.cartUX) {
        const messages = {
            success: { title: 'Success!', message: 'Operation completed successfully' },
            error: { title: 'Error!', message: 'Something went wrong' },
            warning: { title: 'Warning!', message: 'Please check your input' },
            info: { title: 'Info', message: 'Here is some information' }
        };
        
        const msg = messages[type];
        window.cartUX.showToast(type, msg.title, msg.message);
    }
}

function demoConfirmation(type) {
    if (window.cartUX) {
        const configs = {
            delete: {
                title: 'Delete Item',
                message: 'Are you sure you want to delete this item? This action cannot be undone.',
                icon: 'fa-trash text-danger',
                confirmText: 'Delete',
                confirmClass: 'btn-danger',
                onConfirm: () => window.cartUX.showToast('success', 'Deleted', 'Item deleted successfully')
            },
            clear: {
                title: 'Clear Cart',
                message: 'Are you sure you want to clear your entire cart?',
                icon: 'fa-exclamation-triangle text-warning',
                confirmText: 'Clear',
                confirmClass: 'btn-warning',
                onConfirm: () => window.cartUX.showToast('info', 'Cleared', 'Cart cleared successfully')
            }
        };
        
        window.cartUX.showConfirmation(configs[type]);
    }
}

function demoLoading(button) {
    if (window.cartUX) {
        const stopLoading = window.cartUX.showEnhancedLoading(button, 'Processing...');
        
        setTimeout(() => {
            stopLoading();
            window.cartUX.showToast('success', 'Complete', 'Loading demo completed');
        }, 3000);
    }
}

function demoOptimisticUpdate() {
    if (window.cartUX) {
        const rollback = window.cartUX.optimisticQuantityUpdate('demo-1', 5, 99.99);
        
        setTimeout(() => {
            rollback();
            window.cartUX.showToast('info', 'Rollback', 'Optimistic update rolled back');
        }, 2000);
    }
}

function demoSlideOut() {
    const item = document.getElementById('demo-item-1');
    if (item && window.cartUX) {
        window.cartUX.animateItemRemoval('demo-1', () => {
            window.cartUX.showToast('info', 'Animation', 'Slide out animation completed');
            // Restore item after 2 seconds
            setTimeout(() => {
                item.style.transform = '';
                item.style.opacity = '';
                item.style.display = '';
            }, 2000);
        });
    }
}

function demoFadeIn() {
    const target = document.getElementById('animation-target');
    if (target) {
        target.style.display = 'block';
        target.style.opacity = '0';
        target.style.transform = 'translateY(20px)';
        target.style.transition = 'all 0.5s ease';
        
        setTimeout(() => {
            target.style.opacity = '1';
            target.style.transform = 'translateY(0)';
        }, 100);
        
        setTimeout(() => {
            target.style.display = 'none';
        }, 3000);
    }
}

// Initialize demo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎨 Cart UX Demo initialized');
    
    // Demo quantity input handlers
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('input', function() {
            if (window.cartUX) {
                window.cartUX.handleQuantityChange(this);
            }
        });
    });
});
</script>
@endpush
