@extends('layouts.app')

@section('title', $company->business_name . ' - ' . __('companies.company_products'))

@section('content')
<!-- Company Products Content -->
            <!-- Company Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            @if($company->store_logo)
                                <img src="{{ asset('storage/' . $company->store_logo) }}"
                                     alt="{{ $company->business_name }}"
                                     class="img-fluid rounded-circle"
                                     style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 80px; height: 80px; font-size: 1.5rem;">
                                    {{ substr($company->business_name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h1 class="h4 mb-2">{{ $company->business_name }}</h1>
                            <p class="text-muted mb-2">{{ $company->business_description }}</p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">
                                    <i class="fas fa-check-circle"></i> {{ $company->verification_status_label }}
                                </span>
                                <span class="badge bg-info me-2">{{ $company->seller_type_label }}</span>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="{{ route('companies.show', $company) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>
                        Sản phẩm của {{ $company->business_name }}
                    </h5>
                    <div class="d-flex align-items-center">
                        <!-- Search -->
                        <form method="GET" class="me-3">
                            <div class="input-group input-group-sm">
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Tìm kiếm sản phẩm..."
                                       value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <!-- Sort -->
                        <form method="GET" class="me-3">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên Z-A</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá thấp đến cao</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá cao đến thấp</option>
                            </select>
                        </form>

                        <!-- View Toggle -->
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active" onclick="changeView('grid')">
                                <i class="fas fa-th"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="changeView('list')">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($products && $products->count() > 0)
                        <!-- Products Grid -->
                        <div id="productsContainer" class="products-grid">
                            @foreach($products as $product)
                                <div class="product-card">
                                    <div class="card h-100">
                                        @if($product->featured_image)
                                            <img src="{{ asset('storage/' . $product->featured_image) }}"
                                                 class="card-img-top"
                                                 style="height: 200px; object-fit: cover;"
                                                 alt="{{ $product->name }}">
                                        @else
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                                 style="height: 200px;">
                                                <i class="fas fa-image text-muted fa-3x"></i>
                                            </div>
                                        @endif

                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title">{{ $product->name }}</h6>
                                            <p class="card-text text-muted small flex-grow-1">
                                                {{ Str::limit($product->short_description, 100) }}
                                            </p>

                                            @if($product->price)
                                                <div class="mb-2">
                                                    <span class="text-primary fw-bold">{{ number_format($product->price) }} VNĐ</span>
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    @if($product->category)
                                                        <i class="fas fa-tag"></i> {{ $product->category->name }}
                                                    @endif
                                                </small>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('marketplace.products.show', $product) }}"
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($product->type === 'service')
                                                        <button class="btn btn-outline-success btn-sm"
                                                                onclick="contactForService({{ $product->id }})">
                                                            <i class="fas fa-phone"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-outline-success btn-sm"
                                                                onclick="addToCart({{ $product->id }})">
                                                            <i class="fas fa-cart-plus"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($products->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $products->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <!-- No Products -->
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có sản phẩm nào</h5>
                            <p class="text-muted">{{ $company->business_name }} chưa đăng sản phẩm nào.</p>
                            @if(request('search'))
                                <a href="{{ route('companies.products', $company) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left"></i> Xem tất cả sản phẩm
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="actionToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Thông báo</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <!-- Message will be inserted here -->
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.products-list .product-card {
    margin-bottom: 1rem;
}

.products-list .card {
    flex-direction: row;
}

.products-list .card-img-top {
    width: 200px;
    height: 150px;
    border-radius: 0.375rem 0 0 0.375rem;
}

.products-list .card-body {
    flex: 1;
}

@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }

    .products-list .card {
        flex-direction: column;
    }

    .products-list .card-img-top {
        width: 100%;
        height: 200px;
        border-radius: 0.375rem 0.375rem 0 0;
    }
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

function addToCart(productId) {
    fetch(`/marketplace/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        showToast(data.message || 'Đã thêm vào giỏ hàng');
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Có lỗi xảy ra. Vui lòng thử lại.');
    });
}

function contactForService(productId) {
    // Redirect to contact form or show contact modal
    window.location.href = `/marketplace/products/${productId}#contact`;
}

function showToast(message) {
    const toastElement = document.getElementById('actionToast');
    const toastBody = toastElement.querySelector('.toast-body');
    toastBody.textContent = message;

    const toast = new bootstrap.Toast(toastElement);
    toast.show();
}

// Load saved view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('productViewType');
    if (savedView === 'list') {
        changeView('list');
        document.querySelector('[onclick="changeView(\'list\')"]').classList.add('active');
        document.querySelector('[onclick="changeView(\'grid\')"]').classList.remove('active');
    }
});
</script>
@endpush
