{{-- Products Section for Seller Users --}}
<div class="products-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="fas fa-box"></i> {{ __('profile.products') }}
            @if(isset($userProducts) && $userProducts->count() > 0)
                <span class="badge bg-primary ms-2">{{ $userProducts->count() }}</span>
            @endif
        </h5>

        @if(Auth::check() && Auth::id() == $user->id)
            <a href="#" class="btn btn-sm btn-primary" onclick="alert('Tính năng đang phát triển')">
                <i class="fas fa-plus"></i> {{ __('profile.add_product') }}
            </a>
        @endif
    </div>

    @if(isset($userProducts) && $userProducts->count() > 0)
        <div class="products-grid">
            <div class="row">
                @foreach($userProducts as $product)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="product-card card h-100">
                            {{-- Product Image --}}
                            <div class="product-image">
                                @if($product->featured_image)
                                    <img src="{{ $product->featured_image }}" alt="{{ $product->name }}"
                                         class="card-img-top" style="height: 200px; object-fit: cover;">
                                @elseif($product->images && $product->images->count() > 0)
                                    <img src="{{ $product->images->first()->url }}" alt="{{ $product->name }}"
                                         class="card-img-top" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="placeholder-image card-img-top d-flex align-items-center justify-content-center bg-light"
                                         style="height: 200px;">
                                        <i class="fas fa-box fa-3x text-muted"></i>
                                    </div>
                                @endif

                                {{-- Product Status Badge --}}
                                <div class="product-status-badge">
                                    @if($product->status == 'active')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> {{ __('profile.active') }}
                                        </span>
                                    @elseif($product->status == 'pending')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock"></i> {{ __('profile.pending') }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-pause"></i> {{ __('profile.inactive') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                {{-- Product Name --}}
                                <h6 class="card-title">
                                    <a href="#" class="text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </h6>

                                {{-- Product Price --}}
                                @if($product->price)
                                    <div class="product-price mb-2">
                                        <span class="h6 text-primary mb-0">
                                            {{ number_format($product->price, 0, ',', '.') }} VNĐ
                                        </span>
                                        @if($product->original_price && $product->original_price > $product->price)
                                            <span class="text-muted text-decoration-line-through ms-2">
                                                {{ number_format($product->original_price, 0, ',', '.') }} VNĐ
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Product Description --}}
                                @if($product->description)
                                    <p class="card-text text-muted small">
                                        {{ Str::limit($product->description, 100) }}
                                    </p>
                                @endif

                                {{-- Product Category --}}
                                @if($product->category)
                                    <div class="product-category mb-2">
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-tag"></i> {{ $product->category->name }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Product Stats --}}
                                <div class="product-stats d-flex justify-content-between align-items-center">
                                    <div class="stats-left">
                                        <small class="text-muted">
                                            <i class="fas fa-eye"></i> {{ $product->views_count ?? 0 }}
                                            @if($product->orders_count)
                                                <i class="fas fa-shopping-cart ms-2"></i> {{ $product->orders_count }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="stats-right">
                                        <small class="text-muted">
                                            {{ $product->created_at->format('M Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Product Actions (for owner) --}}
                            @if(Auth::check() && Auth::id() == $user->id)
                                <div class="card-footer bg-light">
                                    <div class="product-actions d-flex justify-content-between">
                                        <a href="#" class="btn btn-sm btn-outline-primary" onclick="alert('Tính năng đang phát triển')">
                                            <i class="fas fa-edit"></i> {{ __('profile.edit') }}
                                        </a>

                                        @if($product->status == 'active')
                                            <button class="btn btn-sm btn-outline-warning"
                                                    onclick="toggleProductStatus({{ $product->id }}, 'inactive')">
                                                <i class="fas fa-pause"></i> {{ __('profile.deactivate') }}
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-success"
                                                    onclick="toggleProductStatus({{ $product->id }}, 'active')">
                                                <i class="fas fa-play"></i> {{ __('profile.activate') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Load More Button --}}
        @if($userProducts->count() >= 6)
            <div class="text-center mt-4">
                <a href="#" class="btn btn-outline-primary" onclick="alert('Tính năng đang phát triển')">
                    {{ __('profile.view_all_products') }} <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="empty-state text-center py-5">
            <div class="empty-icon mb-3">
                <i class="fas fa-box fa-4x text-muted"></i>
            </div>
            <h6 class="text-muted">{{ __('profile.no_products') }}</h6>
            <p class="text-muted">
                @if(Auth::check() && Auth::id() == $user->id)
                    {{ __('profile.no_products_own') }}
                @else
                    {{ $user->name }} {{ __('profile.no_products_user') }}
                @endif
            </p>

            @if(Auth::check() && Auth::id() == $user->id)
                <a href="#" class="btn btn-primary" onclick="alert('Tính năng đang phát triển')">
                    <i class="fas fa-plus"></i> {{ __('profile.create_first_product') }}
                </a>
            @endif
        </div>
    @endif
</div>

{{-- JavaScript for Product Actions --}}
@if(Auth::check() && Auth::id() == $user->id)
<script>
function toggleProductStatus(productId, status) {
    if (confirm('{{ __("profile.confirm_status_change") }}')) {
        fetch(`/marketplace/products/${productId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('{{ __("profile.status_change_error") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("profile.status_change_error") }}');
        });
    }
}
</script>
@endif

<style>
.product-status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}

.product-image {
    position: relative;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.product-actions .btn {
    flex: 1;
    margin: 0 2px;
}
</style>
