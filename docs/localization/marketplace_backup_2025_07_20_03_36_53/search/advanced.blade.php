@extends('layouts.app')

@section('title', 'Advanced Product Search - MechaMap Marketplace')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.index') }}">Marketplace</a></li>
            <li class="breadcrumb-item active">Advanced Search</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-1">
                <i class="bx bx-search-alt text-primary me-2"></i>
                Advanced Product Search
            </h1>
            <p class="text-muted mb-0">Find exactly what you need with detailed filters and specifications</p>
        </div>
    </div>

    <div class="row">
        <!-- Search Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-filter me-2"></i>
                        Search Filters
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('marketplace.search.results') }}" method="GET" id="advancedSearchForm">
                        <!-- Keywords -->
                        <div class="mb-3">
                            <label for="q" class="form-label">Keywords</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="q" 
                                   name="q" 
                                   value="{{ request('q') }}" 
                                   placeholder="Enter product name, SKU, or description">
                            <div class="form-text">
                                Use quotes for exact phrases, + for required terms
                            </div>
                        </div>

                        <!-- Product Type -->
                        <div class="mb-3">
                            <label class="form-label">Product Type</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="physical" name="product_types[]" value="physical"
                                       {{ in_array('physical', request('product_types', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="physical">
                                    <i class="bx bx-package me-1"></i>Physical Products
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="digital" name="product_types[]" value="digital"
                                       {{ in_array('digital', request('product_types', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="digital">
                                    <i class="bx bx-file me-1"></i>Digital Products
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="service" name="product_types[]" value="service"
                                       {{ in_array('service', request('product_types', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="service">
                                    <i class="bx bx-wrench me-1"></i>Services
                                </label>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="mb-3">
                            <label for="categories" class="form-label">Categories</label>
                            <select class="form-select" id="categories" name="categories[]" multiple size="5">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ in_array($category->id, request('categories', [])) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Hold Ctrl/Cmd to select multiple</div>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label">Price Range (USD)</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" 
                                           class="form-control form-control-sm" 
                                           name="price_min" 
                                           value="{{ request('price_min') }}"
                                           placeholder="Min" 
                                           min="0" 
                                           step="0.01">
                                </div>
                                <div class="col-6">
                                    <input type="number" 
                                           class="form-control form-control-sm" 
                                           name="price_max" 
                                           value="{{ request('price_max') }}"
                                           placeholder="Max" 
                                           min="0" 
                                           step="0.01">
                                </div>
                            </div>
                        </div>

                        <!-- Seller Type -->
                        <div class="mb-3">
                            <label class="form-label">Seller Type</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="supplier" name="seller_types[]" value="supplier"
                                       {{ in_array('supplier', request('seller_types', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="supplier">
                                    <i class="bx bx-store me-1 text-success"></i>Suppliers
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="manufacturer" name="seller_types[]" value="manufacturer"
                                       {{ in_array('manufacturer', request('seller_types', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="manufacturer">
                                    <i class="bx bx-cube me-1 text-primary"></i>Manufacturers
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="brand" name="seller_types[]" value="brand"
                                       {{ in_array('brand', request('seller_types', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="brand">
                                    <i class="bx bx-bullhorn me-1 text-warning"></i>Brands
                                </label>
                            </div>
                        </div>

                        <!-- Material -->
                        <div class="mb-3">
                            <label for="material" class="form-label">Material</label>
                            <select class="form-select" id="material" name="materials[]" multiple size="4">
                                <option value="steel" {{ in_array('steel', request('materials', [])) ? 'selected' : '' }}>Steel</option>
                                <option value="aluminum" {{ in_array('aluminum', request('materials', [])) ? 'selected' : '' }}>Aluminum</option>
                                <option value="plastic" {{ in_array('plastic', request('materials', [])) ? 'selected' : '' }}>Plastic</option>
                                <option value="brass" {{ in_array('brass', request('materials', [])) ? 'selected' : '' }}>Brass</option>
                                <option value="copper" {{ in_array('copper', request('materials', [])) ? 'selected' : '' }}>Copper</option>
                                <option value="titanium" {{ in_array('titanium', request('materials', [])) ? 'selected' : '' }}>Titanium</option>
                                <option value="composite" {{ in_array('composite', request('materials', [])) ? 'selected' : '' }}>Composite</option>
                                <option value="ceramic" {{ in_array('ceramic', request('materials', [])) ? 'selected' : '' }}>Ceramic</option>
                            </select>
                        </div>

                        <!-- Standards Compliance -->
                        <div class="mb-3">
                            <label for="standards" class="form-label">Standards</label>
                            <select class="form-select" id="standards" name="standards[]" multiple size="3">
                                <option value="iso_9001" {{ in_array('iso_9001', request('standards', [])) ? 'selected' : '' }}>ISO 9001</option>
                                <option value="iso_14001" {{ in_array('iso_14001', request('standards', [])) ? 'selected' : '' }}>ISO 14001</option>
                                <option value="ce_marking" {{ in_array('ce_marking', request('standards', [])) ? 'selected' : '' }}>CE Marking</option>
                                <option value="ul_listed" {{ in_array('ul_listed', request('standards', [])) ? 'selected' : '' }}>UL Listed</option>
                                <option value="rohs" {{ in_array('rohs', request('standards', [])) ? 'selected' : '' }}>RoHS</option>
                                <option value="reach" {{ in_array('reach', request('standards', [])) ? 'selected' : '' }}>REACH</option>
                            </select>
                        </div>

                        <!-- Availability -->
                        <div class="mb-3">
                            <label class="form-label">Availability</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="in_stock" name="in_stock" value="1"
                                       {{ request('in_stock') ? 'checked' : '' }}>
                                <label class="form-check-label" for="in_stock">
                                    In Stock Only
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="on_sale" name="on_sale" value="1"
                                       {{ request('on_sale') ? 'checked' : '' }}>
                                <label class="form-check-label" for="on_sale">
                                    On Sale
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1"
                                       {{ request('featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="featured">
                                    Featured Products
                                </label>
                            </div>
                        </div>

                        <!-- Rating -->
                        <div class="mb-3">
                            <label for="min_rating" class="form-label">Minimum Rating</label>
                            <select class="form-select" id="min_rating" name="min_rating">
                                <option value="">Any Rating</option>
                                <option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                                <option value="3" {{ request('min_rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                                <option value="2" {{ request('min_rating') == '2' ? 'selected' : '' }}>2+ Stars</option>
                                <option value="1" {{ request('min_rating') == '1' ? 'selected' : '' }}>1+ Stars</option>
                            </select>
                        </div>

                        <!-- Sort Options -->
                        <div class="mb-3">
                            <label for="sort" class="form-label">Sort By</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="relevance" {{ request('sort', 'relevance') == 'relevance' ? 'selected' : '' }}>Relevance</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                            </select>
                        </div>

                        <!-- Results Per Page -->
                        <div class="mb-3">
                            <label for="per_page" class="form-label">Results Per Page</label>
                            <select class="form-select" id="per_page" name="per_page">
                                <option value="12" {{ request('per_page', '12') == '12' ? 'selected' : '' }}>12</option>
                                <option value="24" {{ request('per_page', '12') == '24' ? 'selected' : '' }}>24</option>
                                <option value="48" {{ request('per_page', '12') == '48' ? 'selected' : '' }}>48</option>
                                <option value="96" {{ request('per_page', '12') == '96' ? 'selected' : '' }}>96</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search me-1"></i>
                                Search Products
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                <i class="bx bx-x me-1"></i>
                                Clear All
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="saveSearch()">
                                <i class="bx bx-bookmark me-1"></i>
                                Save Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search Interface -->
        <div class="col-lg-9">
            <!-- Quick Search Bar -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="quickSearch" 
                                       placeholder="Quick search for products..."
                                       value="{{ request('q') }}">
                                <button class="btn btn-primary" type="button" onclick="performQuickSearch()">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-2 mt-md-0">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeView('grid')" id="gridView">
                                    <i class="bx bx-grid-alt"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeView('list')" id="listView">
                                    <i class="bx bx-list-ul"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Searches -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="bx bx-trending-up text-success me-2"></i>
                        Popular Searches
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="?q=bearings" class="badge bg-primary text-decoration-none">Bearings</a>
                        <a href="?q=gears" class="badge bg-primary text-decoration-none">Gears</a>
                        <a href="?q=fasteners" class="badge bg-primary text-decoration-none">Fasteners</a>
                        <a href="?q=motors" class="badge bg-primary text-decoration-none">Motors</a>
                        <a href="?q=sensors" class="badge bg-primary text-decoration-none">Sensors</a>
                        <a href="?q=valves" class="badge bg-primary text-decoration-none">Valves</a>
                        <a href="?q=pumps" class="badge bg-primary text-decoration-none">Pumps</a>
                        <a href="?q=actuators" class="badge bg-primary text-decoration-none">Actuators</a>
                        <a href="?q=couplings" class="badge bg-primary text-decoration-none">Couplings</a>
                        <a href="?q=springs" class="badge bg-primary text-decoration-none">Springs</a>
                    </div>
                </div>
            </div>

            <!-- Search Tips -->
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="bx bx-bulb text-warning me-2"></i>
                        Search Tips
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <strong>Exact phrases:</strong> Use quotes "ball bearing"
                                </li>
                                <li class="mb-2">
                                    <strong>Required terms:</strong> Use + for must-have words
                                </li>
                                <li class="mb-2">
                                    <strong>Exclude terms:</strong> Use - to exclude words
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <strong>Wildcards:</strong> Use * for partial matches
                                </li>
                                <li class="mb-2">
                                    <strong>Categories:</strong> Filter by specific categories
                                </li>
                                <li class="mb-2">
                                    <strong>Specifications:</strong> Use technical filters
                                </li>
                            </ul>
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
.sticky-top {
    z-index: 1020;
}

.form-check-label {
    cursor: pointer;
}

.badge {
    font-size: 0.75em;
    transition: all 0.2s ease;
}

.badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-group .btn.active {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

@media (max-width: 768px) {
    .sticky-top {
        position: relative !important;
        top: auto !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
function clearFilters() {
    document.getElementById('advancedSearchForm').reset();
    window.location.href = '{{ route("marketplace.search.advanced") }}';
}

function performQuickSearch() {
    const query = document.getElementById('quickSearch').value;
    if (query.trim()) {
        window.location.href = `{{ route('marketplace.search.results') }}?q=${encodeURIComponent(query)}`;
    }
}

function changeView(viewType) {
    // Update button states
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById(viewType + 'View').classList.add('active');
    
    // Save preference
    localStorage.setItem('productViewType', viewType);
}

function saveSearch() {
    const formData = new FormData(document.getElementById('advancedSearchForm'));
    const searchParams = new URLSearchParams(formData);
    
    const searchName = prompt('Enter a name for this saved search:');
    if (searchName) {
        const savedSearches = JSON.parse(localStorage.getItem('savedProductSearches') || '[]');
        savedSearches.push({
            name: searchName,
            url: '?' + searchParams.toString(),
            created_at: new Date().toISOString()
        });
        localStorage.setItem('savedProductSearches', JSON.stringify(savedSearches));
        
        alert('Search saved successfully!');
    }
}

// Quick search on Enter
document.getElementById('quickSearch').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        performQuickSearch();
    }
});

// Restore view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('productViewType') || 'grid';
    changeView(savedView);
});
</script>
@endpush
