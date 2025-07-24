@extends('layouts.app')

@section('title', $category->name . ' - MechaMap Marketplace')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('marketplace.index') }}">Marketplace</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('marketplace.categories.index') }}">Categories</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                </ol>
            </nav>

            <!-- Category Header -->
            <div class="category-header mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-3">
                            @if($category->icon)
                            <div class="category-icon me-3">
                                <i class="{{ $category->icon }} fa-2x text-primary"></i>
                            </div>
                            @endif
                            <div>
                                <h1 class="h2 mb-1">{{ $category->name }}</h1>
                                <p class="text-muted mb-0">{{ $category->description }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="category-stats">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="stat-item">
                                        <div class="stat-number h4 mb-0 text-primary">{{ $products->total() }}</div>
                                        <div class="stat-label small text-muted">Products</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <div class="stat-number h4 mb-0 text-success">{{ $category->children->count() }}</div>
                                        <div class="stat-label small text-muted">Subcategories</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <div class="stat-number h4 mb-0 text-warning">{{ number_format($category->commission_rate ?? 0, 1) }}%</div>
                                        <div class="stat-label small text-muted">Commission</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subcategories -->
                @if($category->children && $category->children->count() > 0)
                <div class="subcategories mb-4">
                    <h5 class="mb-3">Subcategories</h5>
                    <div class="row">
                        @foreach($category->children as $subcategory)
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('marketplace.categories.show', $subcategory->slug) }}"
                               class="card h-100 text-decoration-none border-0 shadow-sm hover-lift">
                                <div class="card-body text-center">
                                    @if($subcategory->icon)
                                    <i class="{{ $subcategory->icon }} fa-2x text-primary mb-2"></i>
                                    @endif
                                    <h6 class="card-title mb-1">{{ $subcategory->name }}</h6>
                                    <small class="text-muted">{{ $subcategory->marketplaceProducts->count() ?? 0 }} products</small>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Filters and Sorting -->
            <div class="filters-section mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-0">Products ({{ $products->total() }})</h4>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end gap-2">
                            <select class="form-select form-select-sm" style="width: auto;" onchange="sortProducts(this.value)">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                                <option value="rating">Highest Rated</option>
                                <option value="popular">Most Popular</option>
                            </select>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm active" onclick="changeView('grid')">
                                    <i class="bx bx-grid-alt"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeView('list')">
                                    <i class="bx bx-list-ul"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div id="productsContainer" class="products-grid">
                @if($products->count() > 0)
                    <div class="row" id="productsList">
                        @foreach($products as $product)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card product-card h-100 border-0 shadow-sm hover-lift">
                                <!-- Product Image -->
                                <div class="product-image-container position-relative">
                                    @if($product->featured_image)
                                    <img src="{{ asset('storage/' . $product->featured_image) }}"
                                         class="card-img-top" alt="{{ $product->name }}"
                                         style="height: 200px; object-fit: cover;">
                                    @else
                                    <div class="placeholder-image d-flex align-items-center justify-content-center bg-light"
                                         style="height: 200px;">
                                        <i class="bx bx-image fa-3x text-muted"></i>
                                    </div>
                                    @endif

                                    <!-- Product Status Badge -->
                                    @if($product->is_featured)
                                    <span class="badge bg-warning position-absolute top-0 start-0 m-2">Featured</span>
                                    @endif

                                    @if($product->discount_percentage > 0)
                                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                        -{{ $product->discount_percentage }}%
                                    </span>
                                    @endif
                                </div>

                                <div class="card-body d-flex flex-column">
                                    <!-- Product Title -->
                                    <h6 class="card-title mb-2">
                                        <a href="{{ route('marketplace.products.show', $product->slug) }}"
                                           class="text-decoration-none text-dark">
                                            {{ Str::limit($product->name, 50) }}
                                        </a>
                                    </h6>

                                    <!-- Product Description -->
                                    <p class="card-text text-muted small mb-3 flex-grow-1">
                                        {{ Str::limit($product->description, 80) }}
                                    </p>

                                    <!-- Seller Info -->
                                    <div class="seller-info mb-3">
                                        <div class="d-flex align-items-center">
                                            @if($product->seller && $product->seller->user && $product->seller->user->avatar)
                                            <img src="{{ asset('storage/' . $product->seller->user->avatar) }}"
                                                 class="rounded-circle me-2" width="24" height="24" alt="Seller">
                                            @else
                                            <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center"
                                                 style="width: 24px; height: 24px;">
                                                <i class="bx bx-user text-white" style="font-size: 12px;"></i>
                                            </div>
                                            @endif
                                            <small class="text-muted">
                                                {{ $product->seller->user->name ?? 'Unknown Seller' }}
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Product Stats -->
                                    <div class="product-stats mb-3">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <i class="bx bx-star text-warning me-1"></i>
                                                    <small>{{ number_format($product->rating_average ?? 0, 1) }}</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <i class="bx bx-download text-info me-1"></i>
                                                    <small>{{ $product->download_count ?? 0 }}</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <i class="bx bx-show text-muted me-1"></i>
                                                    <small>{{ $product->view_count ?? 0 }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price and Action -->
                                    <div class="product-footer mt-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="price">
                                                @if($product->price > 0)
                                                    @if($product->discount_percentage > 0)
                                                    <span class="text-muted text-decoration-line-through small">
                                                        ${{ number_format($product->price, 2) }}
                                                    </span>
                                                    <span class="text-primary fw-bold">
                                                        ${{ number_format($product->discounted_price, 2) }}
                                                    </span>
                                                    @else
                                                    <span class="text-primary fw-bold">
                                                        ${{ number_format($product->price, 2) }}
                                                    </span>
                                                    @endif
                                                @else
                                                <span class="text-success fw-bold">Free</span>
                                                @endif
                                            </div>
                                            <a href="{{ route('marketplace.products.show', $product->slug) }}"
                                               class="btn btn-primary btn-sm">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <i class="bx bx-package fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No Products Found</h4>
                        <p class="text-muted">There are no products in this category yet.</p>
                        <a href="{{ route('marketplace.categories.index') }}" class="btn btn-primary">
                            Browse Other Categories
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="sidebar">
                <!-- Quick Stats -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Category Stats</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Products:</span>
                            <strong>{{ $products->total() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subcategories:</span>
                            <strong>{{ $category->children->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Commission Rate:</span>
                            <strong>{{ number_format($category->commission_rate ?? 0, 1) }}%</strong>
                        </div>
                    </div>
                </div>

                <!-- Related Categories -->
                @if($category->parent)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Parent Category</h6>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('marketplace.categories.show', $category->parent->slug) }}"
                           class="text-decoration-none">
                            <i class="{{ $category->parent->icon ?? 'bx bx-folder' }} me-2"></i>
                            {{ $category->parent->name }}
                        </a>
                    </div>
                </div>
                @endif

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('marketplace.categories.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-category me-1"></i> All Categories
                            </a>
                            <a href="{{ route('marketplace.products.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-package me-1"></i> All Products
                            </a>
                            <a href="{{ route('marketplace.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="bx bx-home me-1"></i> Marketplace Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.hover-lift {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.product-card {
    transition: all 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.category-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(var(--bs-primary-rgb), 0.1);
    border-radius: 12px;
}

.stat-item {
    padding: 0.5rem;
}

.products-list .col-lg-4 {
    flex: 0 0 100%;
    max-width: 100%;
}

.products-list .product-card {
    flex-direction: row;
}

.products-list .product-image-container {
    width: 200px;
    flex-shrink: 0;
}

.products-list .card-body {
    flex-direction: row;
    align-items: center;
}
</style>
@endpush

@push('scripts')
<script>
function changeView(viewType) {
    const container = document.getElementById('productsContainer');
    const buttons = document.querySelectorAll('.btn-group button');

    // Update button states
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    // Update container class
    if (viewType === 'list') {
        container.className = 'products-list';
    } else {
        container.className = 'products-grid';
    }

    // Save preference
    localStorage.setItem('productViewType', viewType);
}

function sortProducts(sortBy) {
    // Add sorting logic here
    const url = new URL(window.location);
    url.searchParams.set('sort', sortBy);
    window.location.href = url.toString();
}

// Restore view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('productViewType') || 'grid';
    if (savedView === 'list') {
        const listBtn = document.querySelector('.btn-group button:last-child');
        if (listBtn) {
            listBtn.click();
        }
    }
});
</script>
@endpush
