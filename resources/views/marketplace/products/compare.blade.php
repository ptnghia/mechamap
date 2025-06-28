@extends('layouts.app')

@section('title', 'Product Comparison - MechaMap Marketplace')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.index') }}">Marketplace</a></li>
            <li class="breadcrumb-item active">Product Comparison</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-git-compare text-primary me-2"></i>
                        Product Comparison
                    </h1>
                    <p class="text-muted mb-0">Compare products side by side to make informed decisions</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="addProduct()">
                        <i class="bx bx-plus me-1"></i>
                        Add Product
                    </button>
                    <button class="btn btn-outline-secondary" onclick="clearComparison()">
                        <i class="bx bx-trash me-1"></i>
                        Clear All
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-export me-1"></i>
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportComparison('pdf')">
                                <i class="bx bx-file-pdf me-2"></i>PDF Report
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportComparison('csv')">
                                <i class="bx bx-file me-2"></i>CSV Data
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="shareComparison()">
                                <i class="bx bx-share me-2"></i>Share Link
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($products && count($products) > 0)
    <!-- Comparison Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered comparison-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="comparison-feature-col">Features</th>
                            @foreach($products as $product)
                            <th class="comparison-product-col text-center">
                                <div class="product-header">
                                    <div class="product-image mb-2">
                                        <img src="{{ $product->getFirstImageUrl() }}" 
                                             alt="{{ $product->name }}" 
                                             class="img-fluid rounded" 
                                             style="max-height: 80px; max-width: 80px;">
                                    </div>
                                    <h6 class="mb-1">
                                        <a href="{{ route('marketplace.products.show', $product) }}" 
                                           class="text-decoration-none">
                                            {{ Str::limit($product->name, 30) }}
                                        </a>
                                    </h6>
                                    <div class="text-muted small">
                                        SKU: {{ $product->sku }}
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger mt-2" 
                                            onclick="removeProduct({{ $product->id }})">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>
                            </th>
                            @endforeach
                            @if(count($products) < 4)
                            <th class="comparison-product-col text-center">
                                <div class="add-product-placeholder">
                                    <button class="btn btn-outline-primary btn-lg" onclick="addProduct()">
                                        <i class="bx bx-plus display-6"></i>
                                        <div class="mt-2">Add Product</div>
                                    </button>
                                </div>
                            </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Basic Information -->
                        <tr class="table-secondary">
                            <td colspan="{{ count($products) + 1 }}" class="fw-bold">
                                <i class="bx bx-info-circle me-2"></i>Basic Information
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Price</td>
                            @foreach($products as $product)
                            <td class="text-center">
                                <div class="price-display">
                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <span class="text-decoration-line-through text-muted">${{ number_format($product->price, 2) }}</span>
                                        <div class="text-success fw-bold">${{ number_format($product->sale_price, 2) }}</div>
                                        <small class="badge bg-success">{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% OFF</small>
                                    @else
                                        <div class="fw-bold">${{ number_format($product->price, 2) }}</div>
                                    @endif
                                </div>
                            </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-medium">Availability</td>
                            @foreach($products as $product)
                            <td class="text-center">
                                @if($product->stock_quantity > 0)
                                    <span class="badge bg-success">In Stock ({{ $product->stock_quantity }})</span>
                                @else
                                    <span class="badge bg-danger">Out of Stock</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-medium">Seller</td>
                            @foreach($products as $product)
                            <td class="text-center">
                                <div class="seller-info">
                                    <a href="{{ route('marketplace.sellers.show', $product->seller) }}" 
                                       class="text-decoration-none">
                                        {{ $product->seller->store_name }}
                                    </a>
                                    <div class="seller-rating mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bx {{ $i <= $product->seller->rating_average ? 'bxs-star text-warning' : 'bx-star text-muted' }}"></i>
                                        @endfor
                                        <small class="text-muted">({{ $product->seller->rating_count }})</small>
                                    </div>
                                </div>
                            </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-medium">Rating</td>
                            @foreach($products as $product)
                            <td class="text-center">
                                <div class="product-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bx {{ $i <= $product->rating_average ? 'bxs-star text-warning' : 'bx-star text-muted' }}"></i>
                                    @endfor
                                    <div class="small text-muted">
                                        {{ number_format($product->rating_average, 1) }} ({{ $product->rating_count }} reviews)
                                    </div>
                                </div>
                            </td>
                            @endforeach
                        </tr>

                        <!-- Technical Specifications -->
                        <tr class="table-secondary">
                            <td colspan="{{ count($products) + 1 }}" class="fw-bold">
                                <i class="bx bx-cog me-2"></i>Technical Specifications
                            </td>
                        </tr>
                        @php
                            $allSpecs = collect($products)->flatMap(function($product) {
                                return array_keys($product->specifications ?? []);
                            })->unique()->sort();
                        @endphp
                        @foreach($allSpecs as $specKey)
                        <tr>
                            <td class="fw-medium">{{ ucwords(str_replace('_', ' ', $specKey)) }}</td>
                            @foreach($products as $product)
                            <td class="text-center">
                                {{ $product->specifications[$specKey] ?? '-' }}
                            </td>
                            @endforeach
                        </tr>
                        @endforeach

                        <!-- Features -->
                        <tr class="table-secondary">
                            <td colspan="{{ count($products) + 1 }}" class="fw-bold">
                                <i class="bx bx-list-check me-2"></i>Features
                            </td>
                        </tr>
                        @php
                            $allFeatures = collect($products)->flatMap(function($product) {
                                return $product->features ?? [];
                            })->unique()->sort();
                        @endphp
                        @foreach($allFeatures as $feature)
                        <tr>
                            <td class="fw-medium">{{ $feature }}</td>
                            @foreach($products as $product)
                            <td class="text-center">
                                @if(in_array($feature, $product->features ?? []))
                                    <i class="bx bx-check-circle text-success fs-5"></i>
                                @else
                                    <i class="bx bx-x-circle text-danger fs-5"></i>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach

                        <!-- Shipping & Support -->
                        <tr class="table-secondary">
                            <td colspan="{{ count($products) + 1 }}" class="fw-bold">
                                <i class="bx bx-package me-2"></i>Shipping & Support
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Shipping Weight</td>
                            @foreach($products as $product)
                            <td class="text-center">
                                {{ $product->weight ? $product->weight . ' kg' : '-' }}
                            </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-medium">Dimensions</td>
                            @foreach($products as $product)
                            <td class="text-center">
                                @if($product->dimensions)
                                    {{ $product->dimensions['length'] ?? '-' }} × 
                                    {{ $product->dimensions['width'] ?? '-' }} × 
                                    {{ $product->dimensions['height'] ?? '-' }} cm
                                @else
                                    -
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-medium">Warranty</td>
                            @foreach($products as $product)
                            <td class="text-center">
                                {{ $product->warranty_period ?? 'No warranty' }}
                            </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-medium">Return Policy</td>
                            @foreach($products as $product)
                            <td class="text-center">
                                {{ $product->return_policy ?? 'Standard' }}
                            </td>
                            @endforeach
                        </tr>

                        <!-- Actions -->
                        <tr class="table-light">
                            <td class="fw-medium">Actions</td>
                            @foreach($products as $product)
                            <td class="text-center">
                                <div class="d-grid gap-2">
                                    @if($product->stock_quantity > 0)
                                        <button class="btn btn-primary btn-sm" 
                                                onclick="addToCart({{ $product->id }})">
                                            <i class="bx bx-cart-add me-1"></i>
                                            Add to Cart
                                        </button>
                                    @else
                                        <button class="btn btn-outline-secondary btn-sm" disabled>
                                            Out of Stock
                                        </button>
                                    @endif
                                    <a href="{{ route('marketplace.products.show', $product) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bx bx-show me-1"></i>
                                        View Details
                                    </a>
                                    <button class="btn btn-outline-info btn-sm" 
                                            onclick="addToWishlist({{ $product->id }})">
                                        <i class="bx bx-heart me-1"></i>
                                        Wishlist
                                    </button>
                                </div>
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Comparison Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-chart-line me-2"></i>
                        Comparison Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="summary-item">
                                <h6 class="text-success">Best Value</h6>
                                @php $bestValue = collect($products)->sortBy('price')->first(); @endphp
                                <div class="d-flex align-items-center">
                                    <img src="{{ $bestValue->getFirstImageUrl() }}" 
                                         alt="{{ $bestValue->name }}" 
                                         class="rounded me-2" width="40" height="40">
                                    <div>
                                        <div class="fw-medium">{{ Str::limit($bestValue->name, 25) }}</div>
                                        <div class="text-success">${{ number_format($bestValue->price, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="summary-item">
                                <h6 class="text-warning">Highest Rated</h6>
                                @php $highestRated = collect($products)->sortByDesc('rating_average')->first(); @endphp
                                <div class="d-flex align-items-center">
                                    <img src="{{ $highestRated->getFirstImageUrl() }}" 
                                         alt="{{ $highestRated->name }}" 
                                         class="rounded me-2" width="40" height="40">
                                    <div>
                                        <div class="fw-medium">{{ Str::limit($highestRated->name, 25) }}</div>
                                        <div class="text-warning">
                                            {{ number_format($highestRated->rating_average, 1) }} ★
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="summary-item">
                                <h6 class="text-info">Most Features</h6>
                                @php $mostFeatures = collect($products)->sortByDesc(function($p) { return count($p->features ?? []); })->first(); @endphp
                                <div class="d-flex align-items-center">
                                    <img src="{{ $mostFeatures->getFirstImageUrl() }}" 
                                         alt="{{ $mostFeatures->name }}" 
                                         class="rounded me-2" width="40" height="40">
                                    <div>
                                        <div class="fw-medium">{{ Str::limit($mostFeatures->name, 25) }}</div>
                                        <div class="text-info">{{ count($mostFeatures->features ?? []) }} features</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="summary-item">
                                <h6 class="text-primary">Best Seller</h6>
                                @php $bestSeller = collect($products)->sortByDesc('sales_count')->first(); @endphp
                                <div class="d-flex align-items-center">
                                    <img src="{{ $bestSeller->getFirstImageUrl() }}" 
                                         alt="{{ $bestSeller->name }}" 
                                         class="rounded me-2" width="40" height="40">
                                    <div>
                                        <div class="fw-medium">{{ Str::limit($bestSeller->name, 25) }}</div>
                                        <div class="text-primary">{{ $bestSeller->sales_count ?? 0 }} sold</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bx bx-git-compare display-1 text-muted"></i>
            <h3 class="mt-3">No Products to Compare</h3>
            <p class="text-muted">Add products to start comparing their features and specifications.</p>
            <button class="btn btn-primary" onclick="addProduct()">
                <i class="bx bx-plus me-1"></i>
                Add Your First Product
            </button>
        </div>
    </div>
    @endif
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product to Comparison</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="productSearch" 
                           placeholder="Search for products to compare...">
                </div>
                <div id="productSearchResults">
                    <!-- Search results will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.comparison-table {
    min-width: 800px;
}

.comparison-feature-col {
    width: 200px;
    background-color: #f8f9fa;
    position: sticky;
    left: 0;
    z-index: 10;
}

.comparison-product-col {
    min-width: 200px;
    max-width: 250px;
}

.product-header {
    padding: 1rem;
}

.add-product-placeholder {
    padding: 2rem;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    margin: 1rem;
}

.price-display {
    font-size: 1.1rem;
}

.seller-info, .product-rating {
    font-size: 0.875rem;
}

.summary-item {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    height: 100%;
}

@media (max-width: 768px) {
    .comparison-feature-col {
        width: 150px;
    }
    
    .comparison-product-col {
        min-width: 150px;
    }
    
    .product-header {
        padding: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function addProduct() {
    const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
    modal.show();
}

function removeProduct(productId) {
    if (confirm('Remove this product from comparison?')) {
        const url = new URL(window.location);
        const products = url.searchParams.getAll('products[]');
        const newProducts = products.filter(id => id != productId);
        
        url.searchParams.delete('products[]');
        newProducts.forEach(id => url.searchParams.append('products[]', id));
        
        window.location.href = url.toString();
    }
}

function clearComparison() {
    if (confirm('Clear all products from comparison?')) {
        window.location.href = '{{ route("marketplace.products.compare") }}';
    }
}

function addToCart(productId) {
    fetch(`/marketplace/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count in header
            updateCartCount();
            
            // Show success message
            showToast('Product added to cart!', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error adding product to cart', 'error');
    });
}

function addToWishlist(productId) {
    fetch(`/marketplace/wishlist/add/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product added to wishlist!', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error adding product to wishlist', 'error');
    });
}

function exportComparison(format) {
    const url = new URL(window.location);
    url.searchParams.set('export', format);
    window.open(url.toString());
}

function shareComparison() {
    if (navigator.share) {
        navigator.share({
            title: 'Product Comparison - MechaMap',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showToast('Comparison link copied to clipboard!', 'success');
        });
    }
}

// Product search functionality
document.getElementById('productSearch').addEventListener('input', function() {
    const query = this.value;
    if (query.length >= 2) {
        fetch(`/marketplace/search/ajax?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                const resultsContainer = document.getElementById('productSearchResults');
                resultsContainer.innerHTML = '';
                
                data.products.forEach(product => {
                    const productItem = document.createElement('div');
                    productItem.className = 'product-search-item d-flex align-items-center p-2 border-bottom';
                    productItem.innerHTML = `
                        <img src="${product.image}" alt="${product.name}" class="rounded me-3" width="50" height="50">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${product.name}</h6>
                            <div class="text-muted small">$${product.price}</div>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="addProductToComparison(${product.id})">
                            Add
                        </button>
                    `;
                    resultsContainer.appendChild(productItem);
                });
            });
    }
});

function addProductToComparison(productId) {
    const url = new URL(window.location);
    url.searchParams.append('products[]', productId);
    window.location.href = url.toString();
}

function showToast(message, type) {
    // Simple toast implementation
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function updateCartCount() {
    // Update cart count in header (implementation depends on your cart system)
    fetch('/marketplace/cart/count')
        .then(response => response.json())
        .then(data => {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.count;
            }
        });
}
</script>
@endpush
