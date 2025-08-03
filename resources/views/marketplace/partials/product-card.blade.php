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
                    <span class="badge bg-success position-absolute top-0 start-0 m-2">{{ t_common("marketplace.in_stock") }}</span>
                @elseif($product->status === 'out_of_stock')
                    <span class="badge bg-warning position-absolute top-0 start-0 m-2">{{ t_common("marketplace.out_of_stock") }}</span>
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

            <!-- Price -->
            <div class="mb-2">
                @if($product->sale_price && $product->sale_price < $product->price)
                    <div class="d-flex align-items-center">
                        <span class="text-decoration-line-through text-muted me-2">
                            {{ number_format($product->price) }} VND
                        </span>
                        <span class="fw-bold text-danger">
                            {{ number_format($product->sale_price) }} VND
                        </span>
                    </div>
                @else
                    <span class="fw-bold text-primary">
                        {{ number_format($product->price) }} VND
                    </span>
                @endif
            </div>

            <!-- Seller Info -->
            @if($showSeller && $product->seller)
                <div class="mb-2">
                    <div class="d-flex align-items-center text-muted small">
                        <i class="fas fa-store me-1"></i>
                        <span>{{ $product->seller->business_name ?? $product->seller->user->name }}</span>
                    </div>
                    <div class="d-flex align-items-center text-muted small">
                        <i class="fas fa-tag me-1"></i>
                        <span>{{ __('marketplace.product_types.' . $product->product_type) }}</span>
                    </div>
                </div>
            @endif

            <!-- Product Stats -->
            <div class="mb-3">
                <div class="d-flex justify-content-between text-muted small">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-eye me-1"></i>
                        <span>{{ $product->view_count ?? 0 }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-shopping-cart me-1"></i>
                        <span>{{ $product->sales_count ?? 0 }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-star me-1"></i>
                        <span>{{ number_format($product->average_rating ?? 0, 1) }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-auto">
                <div class="d-flex gap-2">
                    <!-- Add to Wishlist -->
                    <button type="button"
                            class="btn btn-outline-secondary btn-sm flex-fill wishlist-btn"
                            data-product-id="{{ $product->id }}"
                            title="Thêm vào danh sách yêu thích">
                        <i class="fa-regular fa-heart"></i>
                        <span class="d-none d-md-inline ms-1">Yêu thích</span>
                    </button>

                    <!-- Add to Cart -->
                    <button type="button"
                            class="btn btn-primary btn-sm flex-fill add-to-cart-btn"
                            data-product-id="{{ $product->id }}"
                            data-product-name="{{ $product->name }}"
                            data-product-price="{{ $product->sale_price ?? $product->price }}"
                            data-product-image="{{ get_product_image_url($product->featured_image) }}"
                            title="Thêm vào giỏ hàng">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="d-none d-md-inline ms-1">Giỏ hàng</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.product-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid #e0e0e0;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.product-image {
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-card .card-body {
    padding: 1rem;
}

.product-card .card-title {
    font-size: 0.95rem;
    font-weight: 600;
    line-height: 1.3;
}

.product-card .card-title a:hover {
    color: #007bff !important;
}

/* List view styles */
.product-card.list-view {
    flex-direction: row;
}

.product-card.list-view .card-body {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.product-card.list-view .position-relative {
    width: 200px;
    flex-shrink: 0;
}

.product-card.list-view .product-image {
    height: 150px;
    width: 100%;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .product-card .card-title {
        font-size: 0.9rem;
    }

    .product-image {
        height: 180px;
    }

    .product-card.list-view .position-relative {
        width: 120px;
    }

    .product-card.list-view .product-image {
        height: 100px;
    }
}
</style>
