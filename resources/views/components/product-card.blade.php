@props(['product', 'showSeller' => true, 'showDescription' => true, 'cardClass' => 'col-xl-4 col-lg-6 col-md-6 mb-4'])

<div class="{{ $cardClass }}">
    <div class="card product-card h-100">
        <!-- Product Image with Link -->
        <a href="{{ url('/marketplace/products/' . $product->slug) }}" class="text-decoration-none">
            <div class="position-relative">
                @if($product->featured_image && file_exists(public_path($product->featured_image)))
                    <img src="{{ get_product_image_url($product->featured_image) }}"
                         class="card-img-top product-image"
                         alt="{{ $product->name }}"
                         onerror="this.src='{{ asset('/images/placeholder-product.jpg') }}'; this.onerror=null;">
                @else
                    <img src="{{ asset('/images/placeholder-product.jpg') }}"
                         class="card-img-top product-image"
                         alt="{{ $product->name }}">
                @endif

                <!-- Product Status Badge -->
                @if($product->status === 'active')
                    <span class="badge bg-success position-absolute top-0 start-0 m-2">{{ __('messages.marketplace.in_stock') }}</span>
                @elseif($product->status === 'out_of_stock')
                    <span class="badge bg-warning position-absolute top-0 start-0 m-2">{{ __('messages.marketplace.out_of_stock') }}</span>
                @endif

                <!-- Discount Badge -->
                @if($product->discount_percentage > 0)
                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                        -{{ $product->discount_percentage }}%
                    </span>
                @endif
            </div>
        </a>

        <div class="card-body d-flex flex-column">
            <!-- Product Info -->
            <div class="mb-2">
                <h6 class="card-title mb-1">
                    <a href="{{ url('/marketplace/products/' . $product->slug) }}" class="text-decoration-none text-dark">
                        {{ $product->name }}
                    </a>
                </h6>

                @if($showDescription && $product->short_description)
                    <p class="card-text text-muted small mb-2">
                        {{ Str::limit($product->short_description, 80) }}
                    </p>
                @endif
            </div>

            <!-- Seller Info -->
            @if($showSeller && $product->seller)
                <div class="mb-2">
                    <small class="text-muted">
                        {{ __('messages.marketplace.by') }} <a href="{{ url('/marketplace/sellers/' . $product->seller->store_slug) }}" class="text-decoration-none">
                            {{ $product->seller->business_name ?? $product->seller->user->name }}
                        </a>
                    </small>
                </div>
            @endif

            <!-- Rating -->
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="text-warning me-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= ($product->rating_average ?? 0))
                                ★
                            @else
                                ☆
                            @endif
                        @endfor
                    </div>
                    <small class="text-muted">({{ $product->reviews_count ?? 0 }})</small>
                </div>
            </div>

            <!-- Price -->
            <div class="mt-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        @if($product->discount_percentage > 0)
                            <h5 class="text-primary mb-0">${{ number_format($product->discounted_price, 2) }}</h5>
                            <small class="text-muted text-decoration-line-through">
                                ${{ number_format($product->price, 2) }}
                            </small>
                        @else
                            <h5 class="text-primary mb-0">${{ number_format($product->price, 2) }}</h5>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm"
                                onclick="addToWishlist({{ $product->id }})"
                                title="{{ __('messages.marketplace.add_to_wishlist') }}">
                            <i class="bi bi-heart"></i>
                        </button>
                        <button type="button" class="btn btn-primary btn-sm"
                                onclick="addToCart({{ $product->id }})"
                                title="{{ __('messages.marketplace.add_to_cart') }}">
                            <i class="bi bi-cart-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.product-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid #e9ecef;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.product-image {
    height: 200px;
    object-fit: cover;
    transition: transform 0.2s ease-in-out;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.card-title a:hover {
    color: var(--bs-primary) !important;
}
</style>

<script>
function addToWishlist(productId) {
    // TODO: Implement wishlist functionality
    console.log('Add to wishlist:', productId);
    // Show toast notification
    showToast('{{ __('messages.marketplace.added_to_wishlist') }}', 'success');
}

function addToCart(productId) {
    // TODO: Implement cart functionality
    console.log('Add to cart:', productId);
    // Show toast notification
    showToast('{{ __('messages.marketplace.added_to_cart') }}', 'success');
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
