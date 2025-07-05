@extends('layouts.app')

@section('title', __('messages.nav.marketplace') . ' - ' . __('messages.marketplace.products'))

@section('content')
<div class="min-vh-100 bg-light">
    <!-- Breadcrumb & Page Title -->
    <div class="bg-white border-bottom">
        <div class="container-fluid py-4">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}" class="text-decoration-none">
                            <i class="fas fa-home me-2"></i>
                            {{ __('messages.home') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ url('/marketplace') }}" class="text-decoration-none">{{ __('messages.nav.marketplace') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.marketplace.products') }}</li>
                </ol>
            </nav>

            <!-- Page Title & Controls -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                <div>
                    <h1 class="h2 fw-bold text-dark">{{ __('messages.marketplace.products') }}</h1>
                    <p class="text-muted mb-0">{{ __('messages.marketplace.discover_products') }}</p>
                </div>
                <div class="mt-3 mt-sm-0 d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" id="advancedSearchToggle" onclick="toggleAdvancedSearch()">
                        <i class="fas fa-search me-2"></i> {{ __('messages.marketplace.advanced_search') }}
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-sort-down me-2"></i> {{ __('messages.marketplace.sort') }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'relevance']) }}">{{ __('messages.marketplace.relevance') }}</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'created_at']) }}">{{ __('messages.marketplace.latest') }}</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}">{{ __('messages.marketplace.price_low_to_high') }}</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}">{{ __('messages.marketplace.price_high_to_low') }}</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}">{{ __('messages.marketplace.highest_rated') }}</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}">{{ __('messages.marketplace.most_popular') }}</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'name']) }}">{{ __('messages.marketplace.name_a_z') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <!-- Advanced Search Panel -->
        <x-marketplace.advanced-search :categories="$categories" />

        <!-- Products Grid -->
        <div class="row">
            <div class="col-12">
                <!-- Results Info -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
                    <div>
                        <p class="text-muted mb-0">
                            {{ __('messages.marketplace.showing_results', [
                                'first' => $products->firstItem() ?? 0,
                                'last' => $products->lastItem() ?? 0,
                                'total' => $products->total()
                            ]) }}
                        </p>
                    </div>
                    <div class="mt-3 mt-sm-0 d-flex align-items-center gap-3">
                        <span class="text-muted small">{{ __('messages.marketplace.view') }}:</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active" id="gridView">
                                <i class="fas fa-th"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="listView">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row" id="productsGrid">
                    @forelse($products as $product)
                        <x-product-card :product="$product" card-class="col-lg-4 col-md-6 mb-4" />
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-search text-muted" style="font-size: 4rem;"></i>
                                <h4 class="mt-3">{{ __('messages.marketplace.no_products_found') }}</h4>
                                <p class="text-muted">{{ __('messages.marketplace.try_adjusting_filters') }}</p>
                                <a href="{{ route('marketplace.products.index') }}" class="btn btn-primary mt-3">
                                    {{ __('messages.marketplace.view_all_products') }}
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const productsGrid = document.getElementById('productsGrid');

    if (gridView && listView && productsGrid) {
        gridView.addEventListener('click', function() {
            // Grid view - Bootstrap row with columns
            gridView.classList.add('active');
            listView.classList.remove('active');

            // Update each product card to use grid layout (3 columns)
            const productCards = productsGrid.querySelectorAll('[class*="col-"]');
            productCards.forEach(card => {
                card.className = 'col-lg-4 col-md-6 mb-4';

                // Reset card structure to vertical layout
                const productCard = card.querySelector('.product-card');
                if (productCard) {
                    productCard.classList.remove('list-view');

                    // Reset card body
                    const cardBody = productCard.querySelector('.card-body');
                    if (cardBody) {
                        cardBody.style.display = 'flex';
                        cardBody.style.flexDirection = 'column';
                        cardBody.style.alignItems = 'stretch';
                        cardBody.style.gap = '';
                    }

                    // Reset image container
                    const imageContainer = productCard.querySelector('.position-relative');
                    if (imageContainer) {
                        imageContainer.style.width = '';
                        imageContainer.style.flexShrink = '';
                    }

                    // Reset image
                    const image = productCard.querySelector('.product-image');
                    if (image) {
                        image.style.height = '200px';
                        image.style.width = '';
                    }
                }
            });
        });

        listView.addEventListener('click', function() {
            // List view - single column
            listView.classList.add('active');
            gridView.classList.remove('active');

            // Update each product card to use list layout
            const productCards = productsGrid.querySelectorAll('[class*="col-"]');
            productCards.forEach(card => {
                card.className = 'col-12 mb-3';

                // Change card structure to horizontal layout
                const productCard = card.querySelector('.product-card');
                if (productCard) {
                    productCard.classList.add('list-view');

                    // Find card body and make it flex
                    const cardBody = productCard.querySelector('.card-body');
                    if (cardBody) {
                        cardBody.style.display = 'flex';
                        cardBody.style.alignItems = 'center';
                        cardBody.style.gap = '1rem';
                    }

                    // Adjust image container
                    const imageContainer = productCard.querySelector('.position-relative');
                    if (imageContainer) {
                        imageContainer.style.width = '200px';
                        imageContainer.style.flexShrink = '0';
                    }

                    // Adjust image
                    const image = productCard.querySelector('.product-image');
                    if (image) {
                        image.style.height = '150px';
                        image.style.width = '100%';
                    }
                }
            });
        });
    }

    // Cart functionality
    window.addToCart = function(productId, quantity = 1) {
        // Show loading state
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
        button.disabled = true;

        fetch('{{ route("marketplace.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast('success', data.message);

                // Update cart count in header if exists
                updateCartCount();

                // Change button to "Added" state temporarily
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.remove('btn-primary');
                button.classList.add('btn-success');

                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-primary');
                    button.disabled = false;
                }, 2000);
            } else {
                showToast('error', data.message);
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Failed to add product to cart');
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    };

    window.updateCartCount = function() {
        fetch('{{ route("marketplace.cart.count") }}')
            .then(response => response.json())
            .then(data => {
                const cartBadge = document.querySelector('#cartCount');
                if (cartBadge && data.success) {
                    cartBadge.textContent = data.count;
                    cartBadge.style.display = data.count > 0 ? 'inline' : 'none';
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
    };

    window.showToast = function(type, message) {
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
    };

    // Update cart count on page load
    updateCartCount();
});
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/marketplace/products/index.css') }}">
@endpush

@endsection
