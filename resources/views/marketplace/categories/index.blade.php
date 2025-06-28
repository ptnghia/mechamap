@extends('layouts.app')

@section('title', 'Product Categories - MechaMap Marketplace')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.index') }}">Marketplace</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-category text-primary me-2"></i>
                        Product Categories
                    </h1>
                    <p class="text-muted mb-0">Browse products by category to find exactly what you need</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('marketplace.search.advanced') }}" class="btn btn-outline-primary">
                        <i class="bx bx-search me-1"></i>
                        Advanced Search
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-filter me-1"></i>
                            View Options
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="changeView('grid')">
                                <i class="bx bx-grid-alt me-2"></i>Grid View
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeView('list')">
                                <i class="bx bx-list-ul me-2"></i>List View
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="sortCategories('name')">
                                <i class="bx bx-sort-a-z me-2"></i>Sort by Name
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="sortCategories('products')">
                                <i class="bx bx-package me-2"></i>Sort by Product Count
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $totalCategories }}</h3>
                            <p class="mb-0">Total Categories</p>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-category display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $totalProducts }}</h3>
                            <p class="mb-0">Total Products</p>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-package display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $activeSellers }}</h3>
                            <p class="mb-0">Active Sellers</p>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-store display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $newThisWeek }}</h3>
                            <p class="mb-0">New This Week</p>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-trending-up display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Categories -->
    @if($featuredCategories->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="h4 mb-3">
                <i class="bx bx-star text-warning me-2"></i>
                Featured Categories
            </h3>
            <div class="row">
                @foreach($featuredCategories as $category)
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card featured-category h-100">
                        <div class="card-img-top category-banner" 
                             style="background-image: url('{{ $category->banner_image ?? '/images/default-category.jpg' }}');">
                            <div class="category-overlay">
                                <div class="category-badge">
                                    <span class="badge bg-warning">Featured</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('marketplace.categories.show', $category) }}" class="text-decoration-none">
                                    {{ $category->name }}
                                </a>
                            </h5>
                            <p class="card-text text-muted">{{ Str::limit($category->description, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    {{ $category->products_count }} products
                                </small>
                                <a href="{{ route('marketplace.categories.show', $category) }}" class="btn btn-sm btn-primary">
                                    Browse
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- All Categories -->
    <div class="row">
        <div class="col-12">
            <h3 class="h4 mb-3">
                <i class="bx bx-grid-alt text-primary me-2"></i>
                All Categories
            </h3>
            
            <div id="categoriesContainer" class="categories-grid">
                @foreach($categories as $category)
                <div class="category-item" data-name="{{ $category->name }}" data-products="{{ $category->products_count }}">
                    <div class="card category-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="category-icon me-3">
                                    @if($category->icon)
                                        <i class="{{ $category->icon }} text-primary fs-2"></i>
                                    @else
                                        <i class="bx bx-package text-primary fs-2"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-2">
                                        <a href="{{ route('marketplace.categories.show', $category) }}" 
                                           class="text-decoration-none">
                                            {{ $category->name }}
                                        </a>
                                    </h5>
                                    
                                    @if($category->description)
                                    <p class="card-text text-muted small mb-3">
                                        {{ Str::limit($category->description, 80) }}
                                    </p>
                                    @endif

                                    <!-- Category Stats -->
                                    <div class="row text-center mb-3">
                                        <div class="col-4">
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $category->products_count }}</div>
                                                <div class="stat-label">Products</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $category->sellers_count ?? 0 }}</div>
                                                <div class="stat-label">Sellers</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $category->avg_rating ?? 0 }}</div>
                                                <div class="stat-label">Rating</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Popular Products Preview -->
                                    @if($category->popularProducts && $category->popularProducts->count() > 0)
                                    <div class="popular-products mb-3">
                                        <h6 class="small text-muted mb-2">POPULAR PRODUCTS</h6>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($category->popularProducts->take(3) as $product)
                                            <a href="{{ route('marketplace.products.show', $product) }}" 
                                               class="badge bg-light text-dark text-decoration-none" 
                                               title="{{ $product->name }}">
                                                {{ Str::limit($product->name, 20) }}
                                            </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Subcategories -->
                                    @if($category->subcategories && $category->subcategories->count() > 0)
                                    <div class="subcategories mb-3">
                                        <h6 class="small text-muted mb-2">SUBCATEGORIES</h6>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($category->subcategories->take(4) as $subcategory)
                                            <a href="{{ route('marketplace.categories.show', $subcategory) }}" 
                                               class="badge bg-secondary text-decoration-none">
                                                {{ $subcategory->name }}
                                            </a>
                                            @endforeach
                                            @if($category->subcategories->count() > 4)
                                            <span class="badge bg-light text-muted">
                                                +{{ $category->subcategories->count() - 4 }} more
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('marketplace.categories.show', $category) }}" 
                                           class="btn btn-primary btn-sm flex-grow-1">
                                            <i class="bx bx-show me-1"></i>
                                            Browse Products
                                        </a>
                                        <button class="btn btn-outline-secondary btn-sm" 
                                                onclick="toggleWatchCategory({{ $category->id }})"
                                                title="Watch for new products">
                                            <i class="bx bx-bell"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Category Footer -->
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Updated {{ $category->updated_at->diffForHumans() }}
                                </small>
                                @if($category->is_trending)
                                <span class="badge bg-success">
                                    <i class="bx bx-trending-up me-1"></i>Trending
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($categories->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $categories->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.categories-list .category-item {
    margin-bottom: 1rem;
}

.category-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid rgba(0,0,0,0.125);
}

.category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.featured-category {
    border: 2px solid #ffc107;
}

.category-banner {
    height: 120px;
    background-size: cover;
    background-position: center;
    position: relative;
}

.category-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(0,0,0,0.3), rgba(0,0,0,0.1));
    display: flex;
    align-items: flex-start;
    justify-content: flex-end;
    padding: 0.5rem;
}

.category-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(var(--bs-primary-rgb), 0.1);
    border-radius: 12px;
    flex-shrink: 0;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--bs-primary);
}

.stat-label {
    font-size: 0.7rem;
    color: var(--bs-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge {
    font-size: 0.7rem;
}

@media (max-width: 768px) {
    .categories-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .category-icon {
        width: 50px;
        height: 50px;
    }
    
    .stat-number {
        font-size: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function changeView(viewType) {
    const container = document.getElementById('categoriesContainer');
    container.className = `categories-${viewType}`;
    
    // Save preference
    localStorage.setItem('categoryViewType', viewType);
}

function sortCategories(sortBy) {
    const container = document.getElementById('categoriesContainer');
    const items = Array.from(container.children);
    
    items.sort((a, b) => {
        if (sortBy === 'name') {
            return a.dataset.name.localeCompare(b.dataset.name);
        } else if (sortBy === 'products') {
            return parseInt(b.dataset.products) - parseInt(a.dataset.products);
        }
        return 0;
    });
    
    // Re-append sorted items
    items.forEach(item => container.appendChild(item));
}

function toggleWatchCategory(categoryId) {
    // AJAX call to toggle category watching
    fetch(`/marketplace/categories/${categoryId}/watch`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const button = event.target.closest('button');
            const icon = button.querySelector('i');
            
            if (data.watching) {
                icon.className = 'bx bxs-bell';
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-warning');
                button.title = 'Stop watching';
            } else {
                icon.className = 'bx bx-bell';
                button.classList.remove('btn-warning');
                button.classList.add('btn-outline-secondary');
                button.title = 'Watch for new products';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Restore view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('categoryViewType') || 'grid';
    changeView(savedView);
});

// Add animation to category cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.category-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
        card.classList.add('animate__animated', 'animate__fadeInUp');
    });
});
</script>
@endpush
