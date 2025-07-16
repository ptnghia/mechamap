{{--
    MechaMap Marketplace Mega Menu Component
    4-column mega menu for marketplace navigation
    Uses Bootstrap grid system and custom CSS
--}}

<div class="mega-menu-container">
    <div class="row g-0">
        <!-- Column 1: Khám Phá & Mua Sắm -->
        <div class="col-md-3">
            <div class="mega-menu-section">
                <h6 class="mega-menu-header">
                    <i class="fa-solid fa-shopping-bag me-2 text-primary"></i>
                    <span>{{ __('marketplace.discover_shopping') }}</span>
                </h6>
                <ul class="mega-menu-list">
                    <li>
                        <a href="{{ route('marketplace.products.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-box me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('marketplace.products.all') }}</span>
                                <small class="mega-menu-item-desc">{{ __('marketplace.products.all_desc') }}</small>
                            </div>
                            <span class="activity-indicator" id="totalProductsCount">72</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('marketplace.index') }}#featured" class="mega-menu-item">
                            <i class="fa-solid fa-star me-2 text-warning"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('marketplace.products.featured') }}</span>
                                <small class="mega-menu-item-desc">{{ __('marketplace.products.featured_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('marketplace.products.index', ['sort' => 'newest']) }}" class="mega-menu-item">
                            <i class="fa-solid fa-clock me-2 text-success"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('marketplace.products.newest') }}</span>
                                <small class="mega-menu-item-desc">{{ __('marketplace.products.newest_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('marketplace.products.index', ['discount' => 1]) }}" class="mega-menu-item">
                            <i class="fa-solid fa-tags me-2 text-danger"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('marketplace.products.discounts') }}</span>
                                <small class="mega-menu-item-desc">{{ __('marketplace.products.discounts_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('marketplace.products.index', ['advanced' => 1]) }}" class="mega-menu-item">
                            <i class="fa-solid fa-search-plus me-2 text-info"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('marketplace.search.advanced') }}</span>
                                <small class="mega-menu-item-desc">{{ __('marketplace.search.advanced_desc') }}</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Column 2: Theo Mục Đích Sử Dụng -->
        <div class="col-md-3">
            <div class="mega-menu-section">
                <h6 class="mega-menu-header">
                    <i class="fa-solid fa-bullseye me-2 text-success"></i>
                    <span>{{ __('marketplace.by_purpose') }}</span>
                </h6>
                <ul class="mega-menu-list">
                    <li>
                        <a href="{{ route('marketplace.products.index', ['type' => 'digital']) }}" class="mega-menu-item">
                            <i class="fa-solid fa-file-code me-2 text-primary"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('marketplace.products.digital') }}</span>
                                <small class="mega-menu-item-desc">{{ __('marketplace.products.digital_desc') }}</small>
                            </div>
                            <span class="activity-indicator" id="digitalProductsCount">--</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('marketplace.products.index', ['type' => 'new_product']) }}" class="mega-menu-item">
                            <i class="fa-solid fa-sparkles me-2 text-success"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('marketplace.products.new') }}</span>
                                <small class="mega-menu-item-desc">{{ __('marketplace.products.new_desc') }}</small>
                            </div>
                            <span class="activity-indicator" id="newProductsCount">--</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('marketplace.products.index', ['type' => 'used_product']) }}" class="mega-menu-item">
                            <i class="fa-solid fa-recycle me-2 text-warning"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('marketplace.products.used') }}</span>
                                <small class="mega-menu-item-desc">{{ __('marketplace.products.used_desc') }}</small>
                            </div>
                            <span class="activity-indicator" id="usedProductsCount">--</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Column 3: Nhà Cung Cấp & Đối Tác -->
        <div class="col-md-3">
            <div class="mega-menu-section">
                <h6 class="mega-menu-header">
                    <i class="fa-solid fa-building me-2 text-warning"></i>
                    <span>{{ __('marketplace.suppliers_partners') }}</span>
                </h6>
                <ul class="mega-menu-list">
                    <li>
                        <a href="{{ route('marketplace.suppliers.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-industry me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('marketplace.suppliers.all') }}</span>
                                <small class="mega-menu-item-desc">{{ __('marketplace.suppliers.all_desc') }}</small>
                            </div>
                            <span class="activity-indicator" id="totalSuppliersCount">29</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('marketplace.suppliers.index', ['verified' => 1]) }}" class="mega-menu-item">
                            <i class="fa-solid fa-certificate me-2 text-success"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('marketplace.suppliers.verified') }}</span>
                                <small class="mega-menu-item-desc">{{ __('marketplace.suppliers.verified_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('marketplace.suppliers.index', ['sort' => 'top_rated']) }}" class="mega-menu-item">
                            <i class="fa-solid fa-trophy me-2 text-warning"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('marketplace.suppliers.top_sellers') }}</span>
                                <small class="mega-menu-item-desc">{{ __('marketplace.suppliers.top_sellers_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('companies.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-building-user me-2 text-info"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('marketplace.company_profiles') }}</span>
                                <small class="mega-menu-item-desc">{{ __('marketplace.company_profiles_desc') }}</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Column 4: Tài Khoản & Hỗ Trợ -->
        <div class="col-md-3">
            <div class="mega-menu-section">
                <h6 class="mega-menu-header">
                    <i class="fa-solid fa-user-gear me-2 text-info"></i>
                    <span>{{ __('marketplace.account_support') }}</span>
                </h6>
                <ul class="mega-menu-list">
                    @auth
                        <li>
                            <a href="{{ route('marketplace.cart.index') }}" class="mega-menu-item">
                                <i class="fa-solid fa-shopping-cart me-2 text-primary"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('marketplace.cart.title') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('marketplace.cart.desc') }}</small>
                                </div>
                                <span class="activity-indicator cart-count" id="cartItemsCount">0</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('marketplace.orders.index') }}" class="mega-menu-item">
                                <i class="fa-solid fa-list-check me-2 text-success"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('marketplace.my_orders') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('marketplace.my_orders_desc') }}</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('marketplace.wishlist.index') }}" class="mega-menu-item">
                                <i class="fa-solid fa-heart me-2 text-danger"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('marketplace.wishlist') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('marketplace.wishlist_desc') }}</small>
                                </div>
                            </a>
                        </li>
                        @if(auth()->user()->canSellAnyProduct())
                        <li>
                            <a href="{{ route('marketplace.seller.dashboard') }}" class="mega-menu-item">
                                <i class="fa-solid fa-store me-2 text-warning"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('marketplace.seller_dashboard') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('marketplace.seller_dashboard_desc') }}</small>
                                </div>
                            </a>
                        </li>
                        @endif
                    @else
                        <li>
                            <a href="{{ route('login') }}" class="mega-menu-item">
                                <i class="fa-solid fa-sign-in-alt me-2 text-primary"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('auth.login.title') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('marketplace.login_desc') }}</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}" class="mega-menu-item">
                                <i class="fa-solid fa-user-plus me-2 text-success"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('auth.register.title') }}</span>
                                    <small class="mega-menu-item-desc">{{ __('marketplace.register_desc') }}</small>
                                </div>
                            </a>
                        </li>
                    @endauth
                    <li>
                        <a href="{{ route('contact') }}" class="mega-menu-item">
                            <i class="fa-solid fa-question-circle me-2 text-info"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('marketplace.help_support') }}</span>
                                <small class="mega-menu-item-desc">{{ __('marketplace.help_support_desc') }}</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for loading marketplace stats --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load marketplace stats when mega menu is shown
    function loadMarketplaceStats() {
        fetch('/api/marketplace/quick-stats')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update product counts
                    const totalProducts = document.getElementById('totalProductsCount');
                    const digitalProducts = document.getElementById('digitalProductsCount');
                    const newProducts = document.getElementById('newProductsCount');
                    const usedProducts = document.getElementById('usedProductsCount');
                    const totalSuppliers = document.getElementById('totalSuppliersCount');
                    const cartItems = document.getElementById('cartItemsCount');

                    if (totalProducts) totalProducts.textContent = data.data.total_products || '72';
                    if (digitalProducts) digitalProducts.textContent = data.data.digital_products || '--';
                    if (newProducts) newProducts.textContent = data.data.new_products || '--';
                    if (usedProducts) usedProducts.textContent = data.data.used_products || '--';
                    if (totalSuppliers) totalSuppliers.textContent = data.data.total_suppliers || '29';
                    if (cartItems) cartItems.textContent = data.data.cart_items || '0';
                }
            })
            .catch(error => {
                console.error('Error loading marketplace stats:', error);
                // Fallback to current values
            });
    }

    // Load cart count for authenticated users
    function loadCartCount() {
        @auth
        fetch('/api/marketplace/cart/count')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartItems = document.getElementById('cartItemsCount');
                    if (cartItems) {
                        cartItems.textContent = data.data.count || '0';
                        cartItems.style.display = data.data.count > 0 ? 'inline' : 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading cart count:', error);
            });
        @endauth
    }

    // Load stats when dropdown is shown
    const marketplaceDropdown = document.getElementById('marketplaceDropdown');
    if (marketplaceDropdown) {
        marketplaceDropdown.addEventListener('show.bs.dropdown', function() {
            loadMarketplaceStats();
            loadCartCount();
        });
    }

    // Initial load for cart count
    loadCartCount();
});
</script>
