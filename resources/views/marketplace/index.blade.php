@extends('layouts.app')

@section('title', __('messages.marketplace.title') . ' - ' . config('app.name'))

@section('meta')
    <meta name="description" content="{{ __('messages.marketplace.subtitle') }}">
    <meta name="keywords" content="mechanical engineering, CAD files, technical products, marketplace, suppliers, manufacturers">
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/marketplace/index.css') }}">
@endpush

@section('content')
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-4">{{ __('messages.marketplace.title') }}</h1>
                    <p class="lead mb-4">{{ __('messages.marketplace.subtitle') }}</p>

                    <!-- Search Bar -->
                    <div class="search-container mb-4">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" id="marketplaceHeroSearch"
                                   placeholder="{{ __('messages.marketplace.search_placeholder') }}">
                            <button class="btn btn-light" type="button" onclick="marketplace.performSearch()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-number">{{ $stats['total_products'] ?? 71 }}</div>
                                <div class="stats-label">{{ __('messages.marketplace.products_available') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-number">{{ $stats['total_sellers'] ?? 27 }}</div>
                                <div class="stats-label">{{ __('messages.marketplace.verified_sellers') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-number">{{ $stats['total_categories'] ?? 19 }}</div>
                                <div class="stats-label">{{ __('messages.marketplace.categories') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Browse Categories -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">{{ __('messages.marketplace.browse_categories') }}</h3>
            <a href="{{ url('/marketplace/categories') }}" class="btn btn-outline-primary">{{ __('messages.marketplace.view_all') }}</a>
        </div>

        <div class="row g-4" id="categoriesContainer">
            @forelse($categories ?? [] as $category)
                <div class="col-md-4 col-lg-3 mb-4">
                    <a href="{{ url('/marketplace/categories/' . $category->slug) }}" class="category-card text-decoration-none">
                        <i class="{{ $category->icon ?? 'fas fa-cog' }} fa-2x mb-3"></i>
                        <h6 class="fw-bold">{{ $category->name }}</h6>
                        <small>{{ $category->product_count ?? 0 }} {{ __('messages.marketplace.items') }}</small>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __('messages.marketplace.no_categories_available') }}
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Featured Products -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">{{ __('messages.marketplace.featured_products') }}</h3>
            <a href="{{ url('/marketplace/products') }}" class="btn btn-outline-primary">{{ __('messages.marketplace.view_all') }}</a>
        </div>

        <div class="row g-4" id="productsContainer">
            @forelse($featuredProducts ?? [] as $product)
                <x-product-card :product="$product" card-class="col-md-6 col-lg-3 mb-4" />
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="box text-muted empty-state-icon"></i>
                        <h4 class="mt-3">{{ __('messages.marketplace.no_featured_products_available') }}</h4>
                        <p class="text-muted">{{ __('messages.marketplace.check_back_later') }}</p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>
@endsection

@push('scripts')
<script>
    class MarketplaceApp {
        constructor() {
            this.cart = [];
            this.init();
        }

        init() {
            this.setupEventListeners();
            this.loadCartData();
        }

        setupEventListeners() {
            // Search functionality
            document.getElementById('heroSearch')?.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.performSearch();
                }
            });
        }

        performSearch() {
            const query = document.getElementById('heroSearch')?.value.trim();
            if (query) {
                window.location.href = `/marketplace/products?search=${encodeURIComponent(query)}`;
            }
        }

        async addToCart(productId) {
            try {
                const response = await axios.post('/marketplace/cart/add', {
                    product_id: productId,
                    quantity: 1
                }, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.data.success) {
                    this.showToast('Product added to cart!', 'success');
                    this.updateCartCount();
                } else {
                    this.showToast('Failed to add product to cart', 'error');
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
                if (error.response?.status === 401) {
                    this.showToast('Please login to add items to cart', 'warning');
                } else {
                    this.showToast('Failed to add product to cart', 'error');
                }
            }
        }

        async loadCartData() {
            try {
                const response = await axios.get('/marketplace/cart/count');
                if (response.data.success) {
                    this.updateCartCount(response.data.count);
                }
            } catch (error) {
                console.error('Error loading cart data:', error);
            }
        }

        updateCartCount(count = null) {
            const cartBadge = document.getElementById('cartCount');
            if (cartBadge && count !== null) {
                cartBadge.textContent = count;
            }
        }

        showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}-circle me-2"></i>
                ${message}
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    }

    // Initialize marketplace app
    let marketplace;
    document.addEventListener('DOMContentLoaded', function() {
        marketplace = new MarketplaceApp();
    });
</script>
@endpush

<!-- Authentication Modal will be created dynamically by auth-modal.js -->
