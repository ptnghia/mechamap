@extends('layouts.app')

@section('title', 'Marketplace - Products')

@section('content')
<div class="min-vh-100 bg-light">
    <!-- Breadcrumb & Page Title -->
    <div class="bg-white border-bottom">
        <div class="container-fluid py-4">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <i class="bi bi-house me-2"></i>
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('marketplace.index') }}" class="text-decoration-none">Marketplace</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Products</li>
                </ol>
            </nav>

            <!-- Page Title & Controls -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                <div>
                    <h1 class="h2 fw-bold text-dark">Products</h1>
                    <p class="text-muted mb-0">Discover mechanical engineering products and solutions</p>
                </div>
                <div class="mt-3 mt-sm-0 d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" id="advancedSearchToggle" onclick="toggleAdvancedSearch()">
                        <i class="bi bi-search me-2"></i> Advanced Search
                    </button>
                    <button class="btn btn-outline-secondary btn-sm d-lg-none" id="filterToggle">
                        <i class="bi bi-funnel me-2"></i> Filters
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-sort-down me-2"></i> Sort
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'relevance']) }}">Relevance</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'created_at']) }}">Latest</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}">Price: Low to High</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}">Price: High to Low</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}">Highest Rated</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}">Most Popular</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'name']) }}">Name A-Z</a></li>
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

        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card" id="filtersCard">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Filters</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('marketplace.products.index') }}" id="filtersForm">
                            <!-- Search -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Search</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search products...">
                            </div>

                            <!-- Category Filter -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Category</label>
                                <select class="form-select" name="category">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Product Type Filter -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Product Type</label>
                                <select class="form-select" name="product_type">
                                    <option value="">All Types</option>
                                    <option value="physical" {{ request('product_type') == 'physical' ? 'selected' : '' }}>Physical Products</option>
                                    <option value="digital" {{ request('product_type') == 'digital' ? 'selected' : '' }}>Digital Products</option>
                                    <option value="service" {{ request('product_type') == 'service' ? 'selected' : '' }}>Services</option>
                                </select>
                            </div>

                            <!-- Seller Type Filter -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Seller Type</label>
                                <select class="form-select" name="seller_type">
                                    <option value="">All Sellers</option>
                                    <option value="supplier" {{ request('seller_type') == 'supplier' ? 'selected' : '' }}>Suppliers</option>
                                    <option value="manufacturer" {{ request('seller_type') == 'manufacturer' ? 'selected' : '' }}>Manufacturers</option>
                                    <option value="brand" {{ request('seller_type') == 'brand' ? 'selected' : '' }}>Brands</option>
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Price Range</label>
                                @foreach($priceRanges as $range)
                                    <div class="form-check">
                                        <input type="radio" name="price_range" value="{{ $range['min'] }}-{{ $range['max'] }}"
                                               id="price_{{ $loop->index }}" class="form-check-input"
                                               {{ (request('min_price') == $range['min'] && request('max_price') == $range['max']) ? 'checked' : '' }}>
                                        <label for="price_{{ $loop->index }}" class="form-check-label">
                                            {{ $range['label'] }}
                                        </label>
                                    </div>
                                @endforeach

                                <!-- Custom Price Range -->
                                <div class="mt-3">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="number" class="form-control form-control-sm"
                                                   name="min_price" value="{{ request('min_price') }}" placeholder="Min $" min="0">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" class="form-control form-control-sm"
                                                   name="max_price" value="{{ request('max_price') }}" placeholder="Max $" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Rating Filter -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Minimum Rating</label>
                                <select class="form-select" name="min_rating">
                                    <option value="">Any Rating</option>
                                    <option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                                    <option value="3" {{ request('min_rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                                    <option value="2" {{ request('min_rating') == '2' ? 'selected' : '' }}>2+ Stars</option>
                                </select>
                            </div>

                            <!-- Availability -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input type="checkbox" name="in_stock" value="1" id="inStock"
                                           class="form-check-input"
                                           {{ request('in_stock') ? 'checked' : '' }}>
                                    <label for="inStock" class="form-check-label">
                                        In Stock Only
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    Apply Filters
                                </button>
                                <a href="{{ route('marketplace.products.index') }}" class="btn btn-outline-secondary">
                                    Clear All
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <!-- Results Info -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
                    <div>
                        <p class="text-muted mb-0">
                            Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }}
                            of {{ $products->total() }} products
                        </p>
                    </div>
                    <div class="mt-3 mt-sm-0 d-flex align-items-center gap-3">
                        <span class="text-muted small">View:</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active" id="gridView">
                                <i class="bi bi-grid"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="listView">
                                <i class="bi bi-list"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row" id="productsGrid">
                    @forelse($products as $product)
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                            <div class="card product-card h-100">
                                <!-- Product Image -->
                                <div class="position-relative">
                                    @if($product->featured_image)
                                        <img src="{{ $product->featured_image }}" class="card-img-top product-image" alt="{{ $product->name }}">
                                    @else
                                        <div class="card-img-top product-image bg-light d-flex align-items-center justify-content-center">
                                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif

                                    <!-- Badges -->
                                    <div class="position-absolute top-0 start-0 p-2">
                                        @if($product->is_featured)
                                            <span class="badge bg-warning text-dark">Featured</span>
                                        @endif
                                        @if($product->is_on_sale)
                                            <span class="badge bg-danger">Sale</span>
                                        @endif
                                    </div>

                                    <!-- Quick Actions -->
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <button class="btn btn-sm btn-light rounded-circle" title="Add to Wishlist">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body d-flex flex-column">
                                    <!-- Product Info -->
                                    <div class="mb-2">
                                        <h6 class="card-title mb-1">
                                            <a href="{{ route('marketplace.products.show', $product->slug) }}" class="text-decoration-none">
                                                {{ $product->name }}
                                            </a>
                                        </h6>
                                        <p class="card-text text-muted small mb-2">{{ Str::limit($product->short_description, 80) }}</p>
                                    </div>

                                    <!-- Seller Info -->
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            by <a href="{{ route('marketplace.sellers.show', $product->seller->store_slug) }}" class="text-decoration-none">
                                                {{ $product->seller->business_name ?? $product->seller->user->name }}
                                            </a>
                                        </small>
                                    </div>

                                    <!-- Rating -->
                                    @if($product->rating_average > 0)
                                        <div class="mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="text-warning me-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $product->rating_average)
                                                            <i class="bi bi-star-fill"></i>
                                                        @else
                                                            <i class="bi bi-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <small class="text-muted">({{ $product->rating_count }})</small>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Price -->
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                @if($product->is_on_sale && $product->sale_price)
                                                    <span class="h6 text-danger mb-0">${{ number_format($product->sale_price, 2) }}</span>
                                                    <small class="text-muted text-decoration-line-through ms-1">${{ number_format($product->price, 2) }}</small>
                                                @else
                                                    <span class="h6 text-primary mb-0">${{ number_format($product->price, 2) }}</span>
                                                @endif
                                            </div>
                                            <button class="btn btn-sm btn-primary" onclick="addToCart({{ $product->id }}, 1)" title="Add to Cart">
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                                <h4 class="mt-3">No products found</h4>
                                <p class="text-muted">Try adjusting your filters or search terms</p>
                                <a href="{{ route('marketplace.products.index') }}" class="btn btn-primary mt-3">
                                    View All Products
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

<style>
.product-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.product-image {
    height: 200px;
    object-fit: cover;
}

#filtersCard {
    position: sticky;
    top: 20px;
}

@media (max-width: 991.98px) {
    #filtersCard {
        position: static;
    }
    #filtersCard.d-none {
        display: none !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter toggle for mobile
    const filterToggle = document.getElementById('filterToggle');
    const filtersCard = document.getElementById('filtersCard');

    if (filterToggle && filtersCard) {
        filterToggle.addEventListener('click', function() {
            filtersCard.classList.toggle('d-none');
        });
    }

    // View toggle functionality
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const productsGrid = document.getElementById('productsGrid');

    if (gridView && listView && productsGrid) {
        gridView.addEventListener('click', function() {
            // Grid view - Bootstrap row with columns
            gridView.classList.add('active');
            listView.classList.remove('active');

            // Update each product card to use grid layout
            const productCards = productsGrid.querySelectorAll('[class*="col-"]');
            productCards.forEach(card => {
                card.className = 'col-xl-4 col-lg-6 col-md-6 mb-4';
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
            });
        });
    }

    // Price range radio buttons
    const priceRangeInputs = document.querySelectorAll('input[name="price_range"]');
    const minPriceInput = document.querySelector('input[name="min_price"]');
    const maxPriceInput = document.querySelector('input[name="max_price"]');

    priceRangeInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.checked) {
                const [min, max] = this.value.split('-');
                minPriceInput.value = min;
                maxPriceInput.value = max === 'null' ? '' : max;
            }
        });
    });

    // Auto-submit form on filter change
    const filterInputs = document.querySelectorAll('#filtersForm select, #filtersForm input[type="checkbox"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            document.getElementById('filtersForm').submit();
        });
    });

    // View toggle buttons
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const productsGrid = document.getElementById('productsGrid');

    if (gridView && listView && productsGrid) {
        gridView.addEventListener('click', function() {
            productsGrid.className = 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6';
            gridView.classList.add('bg-gray-100');
            listView.classList.remove('bg-gray-100');
        });

        listView.addEventListener('click', function() {
            productsGrid.className = 'space-y-4';
            listView.classList.add('bg-gray-100');
            gridView.classList.remove('bg-gray-100');
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
                button.innerHTML = '<i class="bi bi-check"></i>';
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
@endsection
