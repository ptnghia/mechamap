@extends('layouts.app')

@section('title', $product->name . ' - Marketplace')

@section('content')
<div class="min-vh-100 bg-light">
    <!-- Breadcrumb & Page Title -->
    <div class="bg-white border-bottom">
        <div class="container-fluid py-4">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <i class="house me-2"></i>
                            {{ __('marketplace.marketplace.home') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('marketplace.index') }}" class="text-decoration-none">{{ __('marketplace.marketplace.marketplace') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('marketplace.products.index') }}" class="text-decoration-none">{{ __('marketplace.products.title') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($product->name, 30) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Product Details -->
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Product Images -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        @if($product->featured_image)
                            <img src="{{ get_product_image_url($product->featured_image) }}" alt="{{ $product->name }}" class="img-fluid rounded" style="width: 100%; height: 400px; object-fit: cover;" onerror="this.src='{{ asset('images/placeholder-product.jpg') }}'">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width: 100%; height: 400px;">
                                <i class="fas fa-image text-muted" style="font-size: 4rem;"></i>
                            </div>
                        @endif

                        <!-- Additional Images -->
                        @if($product->images && count($product->images) > 1)
                            <div class="row g-2 mt-3">
                                @foreach(array_slice($product->images, 1, 4) as $image)
                                    <div class="col-3">
                                        <img src="{{ get_product_image_url($image) }}" alt="{{ $product->name }}" class="img-fluid rounded border" style="height: 80px; object-fit: cover; cursor: pointer;" onclick="changeMainImage(this.src)" onerror="this.src='{{ asset('images/placeholder-product.jpg') }}'">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <!-- Product Title -->
                        <h1 class="h2 fw-bold text-dark mb-3">{{ $product->name }}</h1>

                        <!-- Seller Info -->
                        @if($product->seller)
                        <div class="d-flex align-items-center mb-3">
                            <span class="text-muted small">{{ __('marketplace.marketplace.sold_by') }}</span>
                            <a href="{{ route('marketplace.sellers.show', $product->seller->store_slug) }}" class="ms-2 text-primary text-decoration-none fw-medium">
                                {{ $product->seller->business_name ?? $product->seller->user->name }}
                            </a>
                            <span class="ms-2 badge bg-{{ $product->seller->verification_status === 'verified' ? 'success' : 'secondary' }}">
                                {{ $product->seller->verification_status === 'verified' ? __('marketplace.products.verified') : ucfirst($product->seller->verification_status) }}
                            </span>
                        </div>
                        @else
                        <div class="d-flex align-items-center mb-3">
                            <span class="text-muted small">{{ __('marketplace.marketplace.sold_by') }}</span>
                            <span class="ms-2 text-muted">{{ __('marketplace.marketplace.seller_not_available') }}</span>
                        </div>
                        @endif

                        <!-- Rating -->
                        @if($product->rating_average > 0)
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-warning me-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $product->rating_average)
                                            <i class="fas fa-star-fill"></i>
                                        @else
                                            <i class="fas fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-muted small">({{ $product->rating_count }} {{ __('marketplace.marketplace.reviews') }})</span>
                            </div>
                        @endif

                        <!-- Price -->
                        <div class="mb-4">
                            @if($product->is_on_sale && $product->sale_price)
                                <div class="d-flex align-items-center">
                                    <span class="h3 fw-bold text-danger mb-0">{{ number_format($product->sale_price, 0, ',', '.') }}₫</span>
                                    <span class="h5 text-muted text-decoration-line-through ms-3 mb-0">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                                    <span class="ms-3 badge bg-danger">
                                        {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% OFF
                                    </span>
                                </div>
                            @else
                                <span class="h3 fw-bold text-primary mb-0">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                            @endif
                        </div>

                        <!-- Stock Status -->
                        <div class="mb-4">
                            @if($product->in_stock)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ __('marketplace.marketplace.in_stock') }}
                                </span>
                                @if($product->stock_quantity <= 5)
                                    <span class="ms-2 text-warning small">Only {{ $product->stock_quantity }} left!</span>
                                @endif
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i>
                                    {{ __('marketplace.marketplace.out_of_stock') }}
                                </span>
                            @endif
                        </div>

                        <!-- Product Type & Category -->
                        <div class="mb-4">
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary">
                                    {{ $product->product_type === 'service' ? __('marketplace.products.service') : ucfirst($product->product_type) }}
                                </span>
                                @if($product->category)
                                    <span class="badge bg-secondary">
                                        {{ $product->category->name }}
                                    </span>
                                @endif
                                <span class="badge bg-info">
                                    {{ $product->seller_type === 'manufacturer' ? __('marketplace.products.manufacturer') : ucfirst($product->seller_type) }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            @if($product->in_stock)
                                <button class="btn btn-primary btn-lg" onclick="addToCart({{ $product->id }})">
                                    <i class="fas fa-shopping-cart-plus me-2"></i>
                                    {{ __('marketplace.marketplace.add_to_cart') }}
                                </button>
                            @else
                                <button class="btn btn-secondary btn-lg" disabled>
                                    {{ __('marketplace.marketplace.out_of_stock') }}
                                </button>
                            @endif

                            <button class="btn btn-outline-secondary" onclick="addToWishlist({{ $product->id }})">
                                <i class="heart me-2"></i>
                                {{ __('marketplace.marketplace.add_to_wishlist') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="h4 fw-bold mb-3">{{ __('marketplace.marketplace.product_description') }}</h2>
                        <div class="mb-4">
                            {!! nl2br(e($product->description)) !!}
                        </div>

                        <!-- Technical Specifications -->
                        @if($product->technical_specs)
                            @php
                                $specs = is_string($product->technical_specs) ? json_decode($product->technical_specs, true) : $product->technical_specs;
                            @endphp
                            @if($specs && is_array($specs))
                                <div class="mt-4">
                                    <h3 class="h5 fw-semibold mb-3">{{ __('marketplace.marketplace.technical_specifications') }}</h3>
                                    <div class="row">
                                        @foreach($specs as $key => $value)
                                            <div class="col-md-6 mb-2">
                                                <div class="d-flex justify-content-between py-2 border-bottom">
                                                    <span class="fw-medium text-dark">
                                                        @switch($key)
                                                            @case('lead_time')
                                                                {{ __('marketplace.marketplace.lead_time') }}:
                                                                @break
                                                            @case('minimum_order')
                                                                {{ __('marketplace.marketplace.minimum_order') }}:
                                                                @break
                                                            @case('precision')
                                                                {{ __('marketplace.marketplace.precision') }}:
                                                                @break
                                                            @case('quality_standard')
                                                                {{ __('marketplace.marketplace.quality_standard') }}:
                                                                @break
                                                            @case('material_options')
                                                                {{ __('marketplace.marketplace.material_options') }}:
                                                                @break
                                                            @case('delivery')
                                                                {{ __('marketplace.marketplace.delivery') }}:
                                                                @break
                                                            @default
                                                                {{ ucfirst(str_replace('_', ' ', $key)) }}:
                                                        @endswitch
                                                    </span>
                                                    <span class="text-muted">{{ $value }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <h2 class="h4 fw-bold mb-4">{{ __('marketplace.marketplace.related_products') }}</h2>
                    <div class="row">
                        @foreach($relatedProducts as $relatedProduct)
                            <x-product-card :product="$relatedProduct" card-class="col-lg-4 col-md-6 mb-4" />
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function changeMainImage(src) {
    const mainImage = document.querySelector('.card-body img:first-child');
    if (mainImage) {
        mainImage.src = src;
    }
}

function addToCart(productId) {
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
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            button.innerHTML = '<i class="fas fa-check"></i> Added';
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
}

function addToWishlist(productId) {
    showToast('info', 'Wishlist feature coming soon!');
}

function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}
</script>
@endsection
