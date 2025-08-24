{{-- Product Item Partial for Swiper Slides --}}
<div class="product-card h-100">
    <div class="product-image-container position-relative">
        <a href="{{ route('marketplace.products.show', $product->slug) }}" class="d-block">
            <img src="{{ $product->getFirstImageUrl() }}"
                 alt="{{ $product->name }}"
                 class="product-image img-fluid">
        </a>

        {{-- Discount Badge --}}
        @if($product->sale_price && $product->sale_price < $product->price)
            @php
                $discountPercent = round((($product->price - $product->sale_price) / $product->price) * 100);
            @endphp
            <div class="discount-badge">-{{ $discountPercent }}%</div>
        @endif

        {{-- Quick Actions --}}
        <div class="product-actions">
            <button class="btn btn-sm btn-outline-light wishlist-btn"
                    data-product-id="{{ $product->id }}"
                    title="{{ __('ui.buttons.add_to_wishlist') }}">
                <i class="fas fa-heart"></i>
            </button>
        </div>
    </div>

    <div class="product-info p-3">
        {{-- Product Title --}}
        <h6 class="product-title mb-2">
            <a href="{{ route('marketplace.products.show', $product->slug) }}"
               class="text-decoration-none text-dark">
                {{ Str::limit($product->name, 150) }}
            </a>
        </h6>

        {{-- Seller Info --}}
        <div class="seller-info mb-2">
            <small class="text-muted">
                {{ __('marketplace.labels.by') }}
                @if($product->seller && $product->seller->store_slug)
                    <a href="{{ route('marketplace.sellers.show', $product->seller->store_slug) }}"
                       class="text-decoration-none">
                        {{ $product->seller->business_name ?? $product->seller->user->name ?? 'N/A' }}
                    </a>
                @else
                    <span>{{ $product->seller->business_name ?? $product->seller->user->name ?? 'N/A' }}</span>
                @endif
            </small>
        </div>

        {{-- Rating --}}
        @if($product->rating_average > 0)
        <div class="product-rating mb-2">
            <div class="stars">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($product->rating_average))
                        <i class="fas fa-star text-warning"></i>
                    @elseif($i - 0.5 <= $product->rating_average)
                        <i class="fas fa-star-half-alt text-warning"></i>
                    @else
                        <i class="far fa-star text-muted"></i>
                    @endif
                @endfor
            </div>
            <small class="text-muted ms-1">({{ $product->rating_count ?? 0 }})</small>
        </div>
        @endif

        {{-- Price --}}
        <div class="product-price mb-3">
            @if($product->sale_price && $product->sale_price < $product->price)
                <span class="current-price fw-bold text-primary">
                    {{ number_format($product->sale_price) }}₫
                </span>
                <span class="original-price text-muted text-decoration-line-through ms-1">
                    {{ number_format($product->price) }}₫
                </span>
            @else
                <span class="current-price fw-bold text-primary">
                    {{ number_format($product->price) }}₫
                </span>
            @endif
        </div>

        {{-- Add to Cart Button --}}
        <div class="product-actions-bottom">
            <button class="btn btn-primary btn-sm w-100 add-to-cart-btn"
                    data-product-id="{{ $product->id }}">
                <i class="fas fa-shopping-cart me-1"></i>
                {{ __('ui.buttons.add_to_cart') }}
            </button>
        </div>
    </div>
</div>
