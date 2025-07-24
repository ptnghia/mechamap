@extends('layouts.app')

@section('title', 'My Wishlist - MechaMap Marketplace')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.index') }}">Marketplace</a></li>
            <li class="breadcrumb-item active">My Wishlist</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-heart text-danger me-2"></i>
                        My Wishlist
                    </h1>
                    <p class="text-muted mb-0">
                        {{ $wishlistItems->count() }} items saved for later
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-sort me-1"></i>
                            Sort By
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?sort=newest">
                                <i class="bx bx-time me-2"></i>Recently Added
                            </a></li>
                            <li><a class="dropdown-item" href="?sort=oldest">
                                <i class="bx bx-history me-2"></i>Oldest First
                            </a></li>
                            <li><a class="dropdown-item" href="?sort=price_low">
                                <i class="bx bx-sort-up me-2"></i>Price: Low to High
                            </a></li>
                            <li><a class="dropdown-item" href="?sort=price_high">
                                <i class="bx bx-sort-down me-2"></i>Price: High to Low
                            </a></li>
                            <li><a class="dropdown-item" href="?sort=name">
                                <i class="bx bx-sort-a-z me-2"></i>Name A-Z
                            </a></li>
                        </ul>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                onclick="changeView('grid')" id="gridView">
                            <i class="bx bx-grid-alt"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                onclick="changeView('list')" id="listView">
                            <i class="bx bx-list-ul"></i>
                        </button>
                    </div>
                    @if($wishlistItems->count() > 0)
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-cog me-1"></i>
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="addAllToCart()">
                                <i class="bx bx-cart-add me-2"></i>Add All to Cart
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="compareSelected()">
                                <i class="bx bx-git-compare me-2"></i>Compare Selected
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="shareWishlist()">
                                <i class="bx bx-share me-2"></i>Share Wishlist
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="clearWishlist()">
                                <i class="bx bx-trash me-2"></i>Clear Wishlist
                            </a></li>
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($wishlistItems->count() > 0)
    <!-- Wishlist Items -->
    <div id="wishlistContainer" class="wishlist-grid">
        @foreach($wishlistItems as $item)
        <div class="wishlist-item" data-product-id="{{ $item->product->id }}">
            <div class="card h-100">
                <div class="position-relative">
                    <!-- Product Image -->
                    <div class="product-image">
                        <img src="{{ $item->product->getFirstImageUrl() }}"
                             alt="{{ $item->product->name }}"
                             class="card-img-top">

                        <!-- Overlay Actions -->
                        <div class="image-overlay">
                            <div class="overlay-actions">
                                <button class="btn btn-sm btn-light rounded-circle"
                                        onclick="removeFromWishlist({{ $item->product->id }})"
                                        title="Remove from wishlist">
                                    <i class="bx bx-x"></i>
                                </button>
                                <button class="btn btn-sm btn-light rounded-circle"
                                        onclick="addToCompare({{ $item->product->id }})"
                                        title="Add to compare">
                                    <i class="bx bx-git-compare"></i>
                                </button>
                                <button class="btn btn-sm btn-light rounded-circle"
                                        onclick="shareProduct({{ $item->product->id }})"
                                        title="Share product">
                                    <i class="bx bx-share"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Product Badges -->
                        <div class="product-badges">
                            @if($item->product->sale_price && $item->product->sale_price < $item->product->price)
                                <span class="badge bg-danger">
                                    {{ round((($item->product->price - $item->product->sale_price) / $item->product->price) * 100) }}% OFF
                                </span>
                            @endif
                            @if($item->product->stock_quantity <= 0)
                                <span class="badge bg-secondary">Out of Stock</span>
                            @elseif($item->product->stock_quantity <= 5)
                                <span class="badge bg-warning">Low Stock</span>
                            @endif
                            @if($item->product->is_featured)
                                <span class="badge bg-primary">Featured</span>
                            @endif
                        </div>

                        <!-- Selection Checkbox -->
                        <div class="selection-checkbox">
                            <input type="checkbox" class="form-check-input product-select"
                                   value="{{ $item->product->id }}" id="select_{{ $item->product->id }}">
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Product Title -->
                        <h6 class="card-title mb-2">
                            <a href="{{ route('marketplace.products.show', $item->product) }}"
                               class="text-decoration-none">
                                {{ $item->product->name }}
                            </a>
                        </h6>

                        <!-- Product Info -->
                        <div class="product-info mb-3">
                            <div class="text-muted small mb-1">
                                SKU: {{ $item->product->sku }}
                            </div>
                            <div class="text-muted small mb-1">
                                Category: {{ $item->product->category->name ?? 'Uncategorized' }}
                            </div>
                            <div class="text-muted small">
                                Seller:
                                <a href="{{ route('marketplace.sellers.show', $item->product->seller) }}"
                                   class="text-decoration-none">
                                    {{ $item->product->seller->store_name }}
                                </a>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="price-section mb-3">
                            @if($item->product->sale_price && $item->product->sale_price < $item->product->price)
                                <div class="d-flex align-items-center">
                                    <span class="text-decoration-line-through text-muted me-2">
                                        ${{ number_format($item->product->price, 2) }}
                                    </span>
                                    <span class="text-success fw-bold fs-5">
                                        ${{ number_format($item->product->sale_price, 2) }}
                                    </span>
                                </div>
                            @else
                                <div class="fw-bold fs-5">
                                    ${{ number_format($item->product->price, 2) }}
                                </div>
                            @endif
                        </div>

                        <!-- Rating -->
                        <div class="rating-section mb-3">
                            <div class="d-flex align-items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bx {{ $i <= $item->product->rating_average ? 'bxs-star text-warning' : 'bx-star text-muted' }}"></i>
                                @endfor
                                <span class="text-muted small ms-2">
                                    ({{ $item->product->rating_count }} reviews)
                                </span>
                            </div>
                        </div>

                        <!-- Added Date -->
                        <div class="added-date mb-3">
                            <small class="text-muted">
                                <i class="bx bx-time me-1"></i>
                                Added {{ $item->created_at->diffForHumans() }}
                            </small>
                        </div>

                        <!-- Price Change Alert -->
                        @if($item->price_when_added && $item->price_when_added != $item->product->getCurrentPrice())
                        <div class="price-change-alert mb-3">
                            @if($item->product->getCurrentPrice() < $item->price_when_added)
                                <div class="alert alert-success alert-sm py-2">
                                    <i class="bx bx-trending-down me-1"></i>
                                    Giá giảm {{ number_format($item->price_when_added - $item->product->getCurrentPrice(), 0, ',', '.') }}₫!
                                </div>
                            @else
                                <div class="alert alert-warning alert-sm py-2">
                                    <i class="bx bx-trending-up me-1"></i>
                                    Giá tăng {{ number_format($item->product->getCurrentPrice() - $item->price_when_added, 0, ',', '.') }}₫
                                </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            @if($item->product->stock_quantity > 0)
                                <button class="btn btn-primary"
                                        onclick="addToCart({{ $item->product->id }})">
                                    <i class="bx bx-cart-add me-1"></i>
                                    Add to Cart
                                </button>
                            @else
                                <button class="btn btn-outline-secondary" disabled>
                                    <i class="bx bx-x-circle me-1"></i>
                                    Out of Stock
                                </button>
                            @endif
                            <div class="d-flex gap-2">
                                <a href="{{ route('marketplace.products.show', $item->product) }}"
                                   class="btn btn-outline-primary btn-sm flex-grow-1">
                                    <i class="bx bx-show me-1"></i>
                                    View
                                </a>
                                <button class="btn btn-outline-danger btn-sm"
                                        onclick="removeFromWishlist({{ $item->product->id }})">
                                    <i class="bx bx-heart-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($wishlistItems->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $wishlistItems->links() }}
    </div>
    @endif

    <!-- Bulk Actions Bar -->
    <div class="bulk-actions-bar" id="bulkActionsBar" style="display: none;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span id="selectedCount">0</span> items selected
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm" onclick="addSelectedToCart()">
                        <i class="bx bx-cart-add me-1"></i>
                        Add to Cart
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="compareSelected()">
                        <i class="bx bx-git-compare me-1"></i>
                        Compare
                    </button>
                    <button class="btn btn-outline-danger btn-sm" onclick="removeSelected()">
                        <i class="bx bx-trash me-1"></i>
                        Remove
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="clearSelection()">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Empty Wishlist -->
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bx bx-heart display-1 text-muted"></i>
            <h3 class="mt-3">Your Wishlist is Empty</h3>
            <p class="text-muted">Save products you love to your wishlist and never lose track of them.</p>
            <div class="mt-4">
                <a href="{{ route('marketplace.index') }}" class="btn btn-primary me-2">
                    <i class="bx bx-store me-1"></i>
                    Browse Products
                </a>
                <a href="{{ route('marketplace.categories.index') }}" class="btn btn-outline-primary">
                    <i class="bx bx-category me-1"></i>
                    Browse Categories
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.wishlist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.wishlist-list .wishlist-item {
    margin-bottom: 1rem;
}

.wishlist-list .card {
    flex-direction: row;
}

.wishlist-list .product-image {
    width: 200px;
    flex-shrink: 0;
}

.product-image {
    position: relative;
    overflow: hidden;
}

.product-image img {
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-image:hover img {
    transform: scale(1.05);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-image:hover .image-overlay {
    opacity: 1;
}

.overlay-actions {
    display: flex;
    gap: 0.5rem;
}

.product-badges {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.selection-checkbox {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
}

.price-change-alert .alert-sm {
    font-size: 0.75rem;
    margin-bottom: 0;
}

.bulk-actions-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--bs-primary);
    color: white;
    padding: 1rem 0;
    z-index: 1000;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}

.btn-group .btn.active {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

@media (max-width: 768px) {
    .wishlist-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }

    .wishlist-list .card {
        flex-direction: column;
    }

    .wishlist-list .product-image {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
function changeView(viewType) {
    const container = document.getElementById('wishlistContainer');
    const gridBtn = document.getElementById('gridView');
    const listBtn = document.getElementById('listView');

    container.className = `wishlist-${viewType}`;

    gridBtn.classList.toggle('active', viewType === 'grid');
    listBtn.classList.toggle('active', viewType === 'list');

    localStorage.setItem('wishlistViewType', viewType);
}

function removeFromWishlist(productId) {
    if (confirm('Remove this item from your wishlist?')) {
        fetch(`/marketplace/wishlist/remove/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`[data-product-id="${productId}"]`).remove();
                showToast('Item removed from wishlist', 'success');

                // Check if wishlist is empty
                if (document.querySelectorAll('.wishlist-item').length === 0) {
                    location.reload();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error removing item from wishlist', 'error');
        });
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
            showToast('Product added to cart!', 'success');
            updateCartCount();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error adding product to cart', 'error');
    });
}

function addToCompare(productId) {
    const url = new URL('{{ route("marketplace.products.compare") }}');
    url.searchParams.append('products[]', productId);
    window.open(url.toString(), '_blank');
}

function shareProduct(productId) {
    const productUrl = `/marketplace/products/${productId}`;
    if (navigator.share) {
        navigator.share({
            title: 'Check out this product',
            url: productUrl
        });
    } else {
        navigator.clipboard.writeText(window.location.origin + productUrl).then(() => {
            showToast('Product link copied to clipboard!', 'success');
        });
    }
}

function clearWishlist() {
    if (confirm('Are you sure you want to clear your entire wishlist?')) {
        fetch('/marketplace/wishlist/clear', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function shareWishlist() {
    const wishlistUrl = window.location.href;
    if (navigator.share) {
        navigator.share({
            title: 'My MechaMap Wishlist',
            url: wishlistUrl
        });
    } else {
        navigator.clipboard.writeText(wishlistUrl).then(() => {
            showToast('Wishlist link copied to clipboard!', 'success');
        });
    }
}

// Selection handling
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('product-select')) {
        updateBulkActions();
    }
});

function updateBulkActions() {
    const selected = document.querySelectorAll('.product-select:checked');
    const bulkBar = document.getElementById('bulkActionsBar');
    const countSpan = document.getElementById('selectedCount');

    if (selected.length > 0) {
        bulkBar.style.display = 'block';
        countSpan.textContent = selected.length;
    } else {
        bulkBar.style.display = 'none';
    }
}

function addSelectedToCart() {
    const selected = Array.from(document.querySelectorAll('.product-select:checked'))
                          .map(cb => cb.value);

    if (selected.length === 0) return;

    Promise.all(selected.map(productId =>
        fetch(`/marketplace/cart/add/${productId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ quantity: 1 })
        })
    ))
    .then(() => {
        showToast(`${selected.length} products added to cart!`, 'success');
        updateCartCount();
        clearSelection();
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error adding products to cart', 'error');
    });
}

function compareSelected() {
    const selected = Array.from(document.querySelectorAll('.product-select:checked'))
                          .map(cb => cb.value);

    if (selected.length < 2) {
        showToast('Please select at least 2 products to compare', 'warning');
        return;
    }

    if (selected.length > 4) {
        showToast('You can compare up to 4 products at once', 'warning');
        return;
    }

    const url = new URL('{{ route("marketplace.products.compare") }}');
    selected.forEach(id => url.searchParams.append('products[]', id));
    window.open(url.toString(), '_blank');
}

function removeSelected() {
    const selected = Array.from(document.querySelectorAll('.product-select:checked'))
                          .map(cb => cb.value);

    if (selected.length === 0) return;

    if (confirm(`Remove ${selected.length} items from your wishlist?`)) {
        Promise.all(selected.map(productId =>
            fetch(`/marketplace/wishlist/remove/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
        ))
        .then(() => {
            selected.forEach(productId => {
                document.querySelector(`[data-product-id="${productId}"]`).remove();
            });
            showToast(`${selected.length} items removed from wishlist`, 'success');
            clearSelection();

            if (document.querySelectorAll('.wishlist-item').length === 0) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error removing items from wishlist', 'error');
        });
    }
}

function clearSelection() {
    document.querySelectorAll('.product-select:checked').forEach(cb => {
        cb.checked = false;
    });
    updateBulkActions();
}

function addAllToCart() {
    const allProducts = Array.from(document.querySelectorAll('.wishlist-item'))
                             .map(item => item.dataset.productId);

    if (confirm(`Add all ${allProducts.length} items to cart?`)) {
        Promise.all(allProducts.map(productId =>
            fetch(`/marketplace/cart/add/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ quantity: 1 })
            })
        ))
        .then(() => {
            showToast(`All ${allProducts.length} products added to cart!`, 'success');
            updateCartCount();
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error adding products to cart', 'error');
        });
    }
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'danger'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function updateCartCount() {
    fetch('/marketplace/cart/count')
        .then(response => response.json())
        .then(data => {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.count;
            }
        });
}

// Restore view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('wishlistViewType') || 'grid';
    changeView(savedView);
});
</script>
@endpush
