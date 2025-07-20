{{--
    MechaMap Marketplace Sidebar Component
    Sidebar chuyên dụng cho trang Marketplace với thống kê và gợi ý sản phẩm
--}}
@props(['showSidebar' => true, 'user' => null])

@if($showSidebar)
@php
$sidebarService = app(\App\Services\MarketplaceSidebarService::class);
$sidebarData = $sidebarService->getMarketplaceSidebarData($user);
@endphp

<div class="sidebar-marketplace" id="marketplace-sidebar">
    <!-- Marketplace Overview Card -->
    <div class="sidebar-card marketplace-overview" data-aos="fade-up">
        <div class="card-body">
            <div class="marketplace-header">
                <h5 class="fw-bold">
                    <i class="fas fa-store me-2 text-success"></i>
                    {{ __('marketplace.engineering_marketplace') }}
                </h5>
                <p class="text-muted">{{ __('marketplace.buy_sell_engineering_products') }}</p>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-solid fa-box"></i>
                        <span>{{ number_format((int)($sidebarData['marketplace_stats']['total_products'] ?? 0)) }}</span>
                    </div>
                    <div class="stat-label">{{ __('marketplace.total_products') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-solid fa-handshake"></i>
                        <span>{{ number_format((int)($sidebarData['marketplace_stats']['total_sales'] ?? 0)) }}</span>
                    </div>
                    <div class="stat-label">{{ __('marketplace.total_sales') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-solid fa-dong-sign"></i>
                        <span>{{ number_format((float)($sidebarData['marketplace_stats']['avg_price'] ?? 0)) }}</span>
                    </div>
                    <div class="stat-label">{{ __('marketplace.avg_price_vnd') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-solid fa-users"></i>
                        <span>{{ number_format((int)($sidebarData['marketplace_stats']['active_sellers'] ?? 0)) }}</span>
                    </div>
                    <div class="stat-label">{{ __('marketplace.active_sellers') }}</div>
                </div>
            </div>

            @auth
            @if(\App\Services\UnifiedMarketplacePermissionService::canSell(auth()->user(), 'digital') ||
                \App\Services\UnifiedMarketplacePermissionService::canSell(auth()->user(), 'new_product') ||
                \App\Services\UnifiedMarketplacePermissionService::canSell(auth()->user(), 'used_product'))
            <div class="cta-section mt-3">
                <a href="{{ route('marketplace.products.create') }}" class="btn btn-success w-100">
                    <i class="fas fa-plus me-2"></i>{{ __('marketplace.list_product') }}
                </a>
            </div>
            @endif
            @else
            <div class="cta-section mt-3">
                <a href="{{ route('register') }}" class="btn btn-primary w-100">
                    <i class="fas fa-user-plus me-2"></i>{{ __('marketplace.join_marketplace') }}
                </a>
            </div>
            @endauth
        </div>
    </div>

    <!-- Product Categories Card -->
    <div class="sidebar-card product-categories" data-aos="fade-up" data-aos-delay="100">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-tags me-2 text-primary"></i>
                {{ __('marketplace.product_categories') }}
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="categories-list">
                @foreach($sidebarData['product_categories'] as $category)
                <a href="{{ route('marketplace.products.index', ['category' => $category['slug']]) }}" class="category-item">
                    <div class="category-icon">
                        <i class="{{ $category['icon'] }}"></i>
                    </div>
                    <div class="category-info">
                        <div class="category-name">{{ $category['name'] }}</div>
                        <div class="category-stats">
                            <span class="product-count">{{ (int)($category['product_count'] ?? 0) }} {{ __('marketplace.products.title') }}</span>
                            <span class="price-range">{{ number_format((float)($category['min_price'] ?? 0)) }} - {{ number_format((float)($category['max_price'] ?? 0)) }} VND</span>
                        </div>
                    </div>
                    <div class="category-trend">
                        @if($category['trend'] > 0)
                        <i class="fas fa-arrow-up text-success"></i>
                        @else
                        <i class="fas fa-minus text-muted"></i>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Featured Products Card -->
    <div class="sidebar-card featured-products" data-aos="fade-up" data-aos-delay="200">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-fire me-2 text-danger"></i>
                {{ __('marketplace.hot_products') }}
            </h6>
            <a href="{{ route('marketplace.products.index', ['featured' => 1]) }}" class="btn btn-sm btn-link">
                {{ __('content.view_all') }}
            </a>
        </div>
        <div class="card-body p-0">
            <div class="products-list">
                @foreach($sidebarData['featured_products'] as $product)
                <div class="product-item">
                    <div class="product-image">
                        <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}"
                             onerror="this.src='{{ asset('images/placeholder-product.jpg') }}'">
                        @if($product['is_featured'])
                        <span class="featured-badge">
                            <i class="fas fa-star"></i>
                        </span>
                        @endif
                        @if($product['discount_percent'] > 0)
                        <span class="discount-badge">
                            -{{ $product['discount_percent'] }}%
                        </span>
                        @endif
                    </div>
                    <div class="product-content">
                        <h6 class="product-title">
                            <a href="{{ route('marketplace.products.show', $product['id']) }}" class="text-decoration-none">
                                {{ Str::limit($product['name'], 40) }}
                            </a>
                        </h6>
                        <div class="product-price">
                            @if(($product['discount_percent'] ?? 0) > 0)
                            <span class="original-price">{{ number_format((float)($product['original_price'] ?? 0)) }} VND</span>
                            <span class="sale-price">{{ number_format((float)($product['price'] ?? 0)) }} VND</span>
                            @else
                            <span class="current-price">{{ number_format((float)($product['price'] ?? 0)) }} VND</span>
                            @endif
                        </div>
                        <div class="product-meta">
                            <span class="seller">
                                <i class="fas fa-store me-1"></i>
                                {{ $product['seller']['name'] }}
                            </span>
                            <span class="product-type">
                                <i class="fas fa-tag me-1"></i>
                                {{ __('marketplace.product_types.' . $product['type']) }}
                            </span>
                        </div>
                        <div class="product-metrics">
                            <span class="metric">
                                <i class="fas fa-eye"></i> {{ number_format((int)($product['views'] ?? 0)) }}
                            </span>
                            <span class="metric">
                                <i class="fas fa-shopping-cart"></i> {{ number_format((int)($product['sales'] ?? 0)) }}
                            </span>
                            @if(($product['rating'] ?? 0) > 0)
                            <span class="rating">
                                <i class="fas fa-star text-warning"></i> {{ number_format((float)($product['rating'] ?? 0), 1) }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top Sellers Card -->
    <div class="sidebar-card top-sellers" data-aos="fade-up" data-aos-delay="300">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-crown me-2 text-warning"></i>
                {{ __('marketplace.top_sellers') }}
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="sellers-list">
                @foreach($sidebarData['top_sellers'] as $index => $seller)
                <a href="{{ route('marketplace.sellers.show', $seller['username']) }}" class="seller-item">
                    <div class="seller-rank">#{{ $index + 1 }}</div>
                    <div class="seller-avatar">
                        <img src="{{ $seller['avatar'] }}" alt="{{ $seller['name'] }}"
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr($seller['name'], 0, 1))) }}&background=6366f1&color=fff&size=40'">
                        @if($seller['is_verified'])
                        <div class="verified-badge">
                            <i class="fas fa-check"></i>
                        </div>
                        @endif
                    </div>
                    <div class="seller-info">
                        <div class="seller-name">{{ $seller['name'] }}</div>
                        <div class="seller-badge {{ $seller['role_class'] }}">
                            <i class="{{ $seller['role_icon'] }} me-1"></i>
                            {{ $seller['role_name'] }}
                        </div>
                        <div class="seller-stats">
                            <span class="product-count">{{ (int)($seller['product_count'] ?? 0) }} {{ __('marketplace.products.title') }}</span>
                            <span class="total-sales">{{ number_format((int)($seller['total_sales'] ?? 0)) }} {{ __('marketplace.sales.title') }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Payment Methods Card -->
    <div class="sidebar-card payment-methods" data-aos="fade-up" data-aos-delay="400">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-credit-card me-2 text-info"></i>
                {{ __('marketplace.payment_methods') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="payment-list">
                <div class="payment-item">
                    <img src="{{ asset('images/payment/stripe.png') }}" alt="Stripe" class="payment-logo">
                    <span class="payment-name">{{ __('marketplace.international_cards') }}</span>
                </div>
                <div class="payment-item">
                    <img src="{{ asset('images/payment/sepay.png') }}" alt="SePay" class="payment-logo">
                    <span class="payment-name">{{ __('marketplace.vietnam_banking') }}</span>
                </div>
            </div>
            <div class="security-note mt-2">
                <i class="fas fa-shield-alt me-1 text-success"></i>
                <small class="text-muted">{{ __('marketplace.secure_payment_guarantee') }}</small>
            </div>
        </div>
    </div>
</div>

<script>
// Marketplace Sidebar JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeMarketplaceSidebar();
});

function initializeMarketplaceSidebar() {
    // Initialize tooltips
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Track sidebar interactions
    trackMarketplaceSidebarInteractions();
}

function trackMarketplaceSidebarInteractions() {
    document.querySelectorAll('.sidebar-marketplace a').forEach(link => {
        link.addEventListener('click', function() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'marketplace_sidebar_click', {
                    'link_text': this.textContent.trim(),
                    'link_url': this.href,
                    'sidebar_section': this.closest('.sidebar-card').className
                });
            }
        });
    });
}
</script>
@endif
