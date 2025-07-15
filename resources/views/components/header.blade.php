{{--
    MechaMap Unified Header Component
    Sử dụng cho tất cả trang frontend user
--}}
@props(['showBanner' => true, 'isMarketplace' => false])

<!-- Unified Search CSS -->
<link rel="stylesheet" href="{{ asset('css/frontend/components/unified-search.css') }}">

<header class="site-header">
    <!-- Banner (optional) -->
    @if($showBanner && get_setting('show_banner', true))
    <div class="header-banner">
        <img src="{{ get_banner_url() }}" alt="Banner" class="w-100">
    </div>
    @endif
    <div class="header-content" id="header-content">
    <!-- Main Header -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand logo" href="{{ url('/') }}">
                    <img src="{{ get_logo_url() }}" alt="{{ get_site_name() }}" class="me-2">
                </a>

                <!-- Mobile Quick Actions -->
                <div class="d-flex d-lg-none align-items-center">
                    <!-- Mobile Search Button -->
                    <button class="btn btn-outline-secondary btn-sm me-2" type="button" data-bs-toggle="modal" data-bs-target="#mobileSearchModal">
                        <i class="fa-solid fa-search"></i>
                    </button>

                    <!-- Mobile Cart - Only show if user can buy products -->
                    @auth
                        @if(auth()->user()->canBuyAnyProduct())
                            <a class="btn btn-outline-primary btn-sm me-2 position-relative" href="{{ route('marketplace.cart.index') }}">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" id="mobileCartCount" style="display: none;">0</span>
                            </a>
                        @endif
                    @endauth

                    <!-- Mobile Menu Button - HC-MobileNav -->
                    <button class="hc-mobile-nav-toggle border-0 bg-transparent" type="button" aria-label="Toggle mobile navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <!-- Navigation Menu -->
                <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                    <!-- Search Bar - Using old header structure -->
                    <div class="search-container position-relative" style="min-width: 300px;">
                        <div class="input-group">
                            <input type="text" class="form-control search-input" id="unified-search" name="query" autocomplete="off"
                                placeholder="{{ $isMarketplace ? __('forum.search.placeholder') : __('forum.search.placeholder') }}" aria-label="{{ __('ui.actions.search') }}">
                            <button class="btn btn-outline-secondary" type="button" id="unified-search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        <!-- Search Results Dropdown - Exact structure from old header -->
                        <div class="search-results-dropdown" id="search-results-dropdown">
                            <div class="search-scope-options">
                                <div class="search-scope-option active" data-scope="site">{{ __('search.all_content') }}</div>
                                <div class="search-scope-option" data-scope="thread" style="display: none;">{{ __('search.search_in_thread') }}</div>
                                <div class="search-scope-option" data-scope="forum" style="display: none;">{{ __('search.search_in_forum') }}</div>
                                @if($isMarketplace)
                                <div class="search-scope-option" data-scope="marketplace">{{ __('nav.marketplace') }}</div>
                                @endif
                            </div>
                            <div class="search-results-content" id="search-results-content">
                                <!-- Results will be loaded here via AJAX -->
                            </div>
                            <div class="search-results-footer">
                                <a href="{{ route('forums.search.advanced') }}" class="advanced-search-link">
                                    🔍 {{ __('search.advanced') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <ul class="navbar-nav">
                        <!-- 2. Community/Forum - PRIORITY #1 - Mega Menu -->
                        <li class="nav-item dropdown mega-menu-dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs(['forums.*', 'members.*', 'events.*', 'jobs.*']) ? 'active' : '' }}" href="#" id="communityDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-users me-1"></i>
                                {{ __('ui.common.community') }}
                            </a>
                            <div class="dropdown-menu mega-menu" aria-labelledby="communityDropdown">
                                @include('components.menu.community-mega-menu')
                            </div>
                        </li>

                        <!-- 3. Dự án - Direct Link -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('showcase.*') ? 'active' : '' }}" href="{{ route('showcase.public') }}">
                                <i class="fa-solid fa-trophy me-1"></i>
                                {{ __('ui.common.showcase') }}
                            </a>
                        </li>

                        <!-- 4. Marketplace - PRIORITY #3 - Only show if user has marketplace access -->
                        @if(!auth()->check() || auth()->user()->canBuyAnyProduct() || auth()->user()->canSellAnyProduct())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('marketplace.*') ? 'active' : '' }}" href="#" id="marketplaceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-store me-1"></i>
                                {{ __('ui.common.marketplace') }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="marketplaceDropdown">
                                <li><h6 class="dropdown-header"><i class="fa-solid fa-shopping-bag me-2"></i>{{ __('marketplace.shop') }}</h6></li>
                                <li><a class="dropdown-item" href="{{ route('marketplace.products.index') }}">
                                    <i class="fa-solid fa-box me-2"></i>{{ __('marketplace.products.all') }}
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('marketplace.categories.index') }}">
                                    <i class="fa-solid fa-grid-2 me-2"></i>{{ __('marketplace.categories.title') }}
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('marketplace.suppliers.index') }}">
                                    <i class="fa-solid fa-building me-2"></i>{{ __('marketplace.suppliers.title') }}
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('companies.index') }}">
                                    <i class="fa-solid fa-building-user me-2"></i>{{ __('ui.common.company_profiles') }}
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('marketplace.index') }}#featured">
                                    <i class="fa-solid fa-star me-2"></i>{{ __('marketplace.products.featured') }}
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header"><i class="fa-solid fa-briefcase me-2"></i>{{ __('marketplace.business_tools') }}</h6></li>
                                <li><a class="dropdown-item" href="{{ route('marketplace.rfq.index') }}">
                                    <i class="fa-solid fa-file-invoice me-2"></i>{{ __('marketplace.rfq.title') }}
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('marketplace.bulk-orders') }}">
                                    <i class="fa-solid fa-boxes-stacked me-2"></i>{{ __('marketplace.bulk_orders') }}
                                </a></li>
                                @auth
                                    @if(auth()->user()->canSellAnyProduct())
                                    <li><a class="dropdown-item" href="{{ route('marketplace.seller.setup') }}">
                                        <i class="fa-solid fa-store me-2"></i>{{ __('marketplace.become_seller') }}
                                    </a></li>
                                    @endif
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header"><i class="fa-solid fa-user me-2"></i>{{ __('marketplace.my_account') }}</h6></li>
                                <li><a class="dropdown-item" href="{{ route('marketplace.orders.index') }}">
                                    <i class="fa-solid fa-list-check me-2"></i>{{ __('marketplace.my_orders') }}
                                </a></li>
                                    @if(auth()->user()->canBuyAnyProduct())
                                    <li><a class="dropdown-item" href="{{ route('marketplace.cart.index') }}">
                                        <i class="fa-solid fa-shopping-cart me-2"></i>{{ __('marketplace.cart.title') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('marketplace.downloads.index') }}">
                                        <i class="fa-solid fa-download me-2"></i>{{ __('marketplace.downloads') }}
                                    </a></li>
                                    @endif
                                @endauth
                            </ul>
                        </li>
                        @endif

                        <!-- 5. Technical Resources - UPDATED (removed showcase) -->
                        <!--li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs(['technical.*', 'materials.*', 'standards.*', 'cad.*', 'manufacturing.*']) ? 'active' : '' }}" href="#" id="technicalDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-screwdriver-wrench me-1"></i>
                                {{ __('ui.common.technical_resources') }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="technicalDropdown">
                                <li><h6 class="dropdown-header"><i class="fa-solid fa-database me-2"></i>{{ __('ui.common.technical_database') }}</h6></li>
                                <li><a class="dropdown-item" href="{{ route('materials.index') }}">
                                    <i class="fa-solid fa-cube me-2"></i>{{ __('ui.common.materials_database') }}
                                    <span class="badge bg-primary ms-2">10</span>
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('standards.index') }}">
                                    <i class="fa-solid fa-certificate me-2"></i>{{ __('ui.common.engineering_standards') }}
                                    <span class="badge bg-success ms-2">8</span>
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('manufacturing.processes.index') }}">
                                    <i class="fa-solid fa-gears me-2"></i>{{ __('ui.common.manufacturing_processes') }}
                                    <span class="badge bg-info ms-2">10</span>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header"><i class="fa-solid fa-drafting-compass me-2"></i>{{ __('ui.common.design_resources') }}</h6></li>
                                <li><a class="dropdown-item" href="{{ route('cad.library.index') }}">
                                    <i class="fa-solid fa-file-code me-2"></i>{{ __('ui.common.cad_library') }}
                                    <span class="badge bg-warning ms-2">20+</span>
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('technical.drawings.index') }}">
                                    <i class="fa-solid fa-compass-drafting me-2"></i>{{ __('ui.common.technical_drawings') }}
                                    <span class="badge bg-secondary ms-2">15+</span>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header"><i class="fa-solid fa-calculator me-2"></i>{{ __('ui.common.tools_calculators') }}</h6></li>
                                <li><a class="dropdown-item" href="{{ route('tools.material-calculator') }}">
                                    <i class="fa-solid fa-calculator me-2"></i>{{ __('ui.common.material_cost_calculator') }}
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('tools.process-selector') }}">
                                    <i class="fa-solid fa-route me-2"></i>{{ __('ui.common.process_selector') }}
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('tools.standards-checker') }}">
                                    <i class="fa-solid fa-check-circle me-2"></i>{{ __('ui.common.standards_compliance') }}
                                </a></li>
                            </ul>
                        </li-->

                        <!-- 6. Knowledge -->
                        <!--li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs(['knowledge.*', 'tutorials.*', 'news.*', 'docs.*']) ? 'active' : '' }}" href="#" id="knowledgeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-graduation-cap me-1"></i>
                                {{ __('ui.common.knowledge') }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="knowledgeDropdown">
                                <li><h6 class="dropdown-header"><i class="fa-solid fa-book me-2"></i>{{ __('ui.common.learning_resources') }}</h6></li>
                                <li><a class="dropdown-item" href="{{ route('knowledge.base.index') }}">
                                    <i class="fa-solid fa-book-open me-2"></i>{{ __('ui.common.knowledge_base') }}
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('tutorials.index') }}">
                                    <i class="fa-solid fa-chalkboard-teacher me-2"></i>{{ __('ui.common.tutorials_guides') }}
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('documentation.index') }}">
                                    <i class="fa-solid fa-file-lines me-2"></i>{{ __('ui.common.technical_documentation') }}
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header"><i class="fa-solid fa-newspaper me-2"></i>{{ __('ui.common.industry_updates') }}</h6></li>
                                <li><a class="dropdown-item" href="{{ route('news.industry.index') }}">
                                    <i class="fa-solid fa-newspaper me-2"></i>{{ __('ui.common.industry_news') }}
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('whats-new') }}">
                                    <i class="fa-solid fa-fire-flame-curved me-2"></i>{{ __('ui.common.whats_new') }}
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('reports.industry.index') }}">
                                    <i class="fa-solid fa-chart-line me-2"></i>{{ __('ui.common.industry_reports') }}
                                </a></li>
                            </ul>
                        </li-->

                        <!-- Role-based Quick Access -->
                        @auth
                            @if(Auth::user()->hasAnyRole(['admin', 'moderator']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-shield-check me-1"></i>
                                    Quản trị
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bx bx-tachometer me-2"></i>{{ __('ui.common.admin_dashboard') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                        <i class="bx bx-user me-2"></i>{{ __('ui.common.user_management') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.threads.index') }}">
                                        <i class="bx bx-chat me-2"></i>{{ __('ui.common.forum_management') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.products.index') }}">
                                        <i class="bx bx-package me-2"></i>{{ __('ui.common.marketplace_management') }}
                                    </a></li>
                                </ul>
                            </li>
                            @endif

                            @if(Auth::user()->hasRole('supplier'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="supplierDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-store me-1"></i>
                                    Nhà cung cấp
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="supplierDropdown">
                                    <li><a class="dropdown-item" href="{{ route('supplier.dashboard') }}">
                                        <i class="bx bx-tachometer me-2"></i>{{ __('ui.common.dashboard') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('supplier.products.index') }}">
                                        <i class="bx bx-package me-2"></i>{{ __('ui.common.my_products') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('supplier.orders.index') }}">
                                        <i class="bx bx-list-ul me-2"></i>{{ __('ui.common.orders') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('supplier.analytics.index') }}">
                                        <i class="bx bx-bar-chart me-2"></i>{{ __('ui.common.reports') }}
                                    </a></li>
                                </ul>
                            </li>
                            @endif



                            @if(Auth::user()->hasRole('brand'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="brandDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-bullhorn me-1"></i>
                                    Thương hiệu
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="brandDropdown">
                                    <li><a class="dropdown-item" href="{{ route('brand.dashboard') }}">
                                        <i class="bx bx-tachometer me-2"></i>{{ __('ui.common.dashboard') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('brand.insights.index') }}">
                                        <i class="bx bx-bulb me-2"></i>{{ __('ui.common.market_insights') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('brand.marketplace.analytics') }}">
                                        <i class="bx bx-store me-2"></i>{{ __('ui.common.marketplace_analytics') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('brand.promotion.index') }}">
                                        <i class="bx bx-megaphone me-2"></i>{{ __('ui.common.promotion_opportunities') }}
                                    </a></li>
                                </ul>
                            </li>
                            @endif
                        @endauth

                        <!-- More Dropdown - Enhanced -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis me-1"></i>
                                {{ __('ui.common.more') }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="moreDropdown">
                                <li><h6 class="dropdown-header"><i class="fa-solid fa-search me-2"></i>{{ __('ui.common.search_discovery') }}</h6></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('forums.search.advanced') }}">
                                        <i class="fa-brands fa-searchengin me-2"></i>
                                        {{ __('ui.common.advanced_search') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('gallery.index') }}">
                                        <i class="fa-regular fa-images me-2"></i>
                                        {{ __('ui.common.photo_gallery') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('tags.index') }}">
                                        <i class="fa-solid fa-tags me-2"></i>
                                        {{ __('ui.common.browse_by_tags') }}
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header"><i class="fa-solid fa-info-circle me-2"></i>{{ __('ui.common.help_support') }}</h6></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('faq.index') }}">
                                        <i class="fa-solid fa-question me-2"></i>
                                        {{ __('ui.common.faq') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('help.index') }}">
                                        <i class="fa-solid fa-life-ring me-2"></i>
                                        {{ __('ui.common.help_center') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('contact') }}">
                                        <i class="fa-solid fa-envelope me-2"></i>
                                        {{ __('ui.common.contact_support') }}
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header"><i class="fa-solid fa-info me-2"></i>{{ __('ui.common.about_mechamap') }}</h6></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('about.index') }}">
                                        <i class="fa-solid fa-building me-2"></i>
                                        {{ __('ui.common.about_us') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('terms.index') }}">
                                        <i class="fa-solid fa-file-contract me-2"></i>
                                        {{ __('ui.common.terms_of_service') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('privacy.index') }}">
                                        <i class="fa-solid fa-shield-halved me-2"></i>
                                        {{ __('ui.common.privacy_policy') }}
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <div class="dropdown-item d-flex justify-content-between align-items-center">
                                        <span id="themeLabel">
                                            <i class="fas fa-moon theme-icon-dark me-2"></i>
                                            <i class="fas fa-sun theme-icon-light me-2 d-none"></i>
                                            <span class="theme-text">{{ request()->cookie('dark_mode') == 'dark' ? 'Light Mode' : 'Dark Mode' }}</span>
                                        </span>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="darkModeSwitch" data-toggle-theme="dark" {{ request()->cookie('dark_mode') == 'dark' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>

                    <!-- Right Side Actions -->
                    <ul class="navbar-nav">
                        <!-- Cart - Only show if user can buy products -->
                        @auth
                            @if(auth()->user()->canBuyAnyProduct())
                                <li class="nav-item dropdown">
                                    <a class="nav-link position-relative" href="#" id="cartToggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-shopping-cart"></i>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" id="cartCount" style="display: none;">
                                            0
                                        </span>
                                    </a>
                                    <!-- Mini Cart Dropdown -->
                                    <div class="dropdown-menu dropdown-menu-end p-0" style="width: 380px;" id="miniCart">
                                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ __('marketplace.cart.shopping_cart') }}</h6>
                                            <span class="badge bg-primary" id="miniCartItemCount">0</span>
                                        </div>
                                <div id="miniCartItems" style="max-height: 350px; overflow-y: auto;">
                                    <!-- Empty state -->
                                    <div class="text-center text-muted py-4" id="miniCartEmpty">
                                        <i class="fas fa-shopping-cart-x" style="font-size: 2.5rem;"></i>
                                        <p class="mb-0 mt-2">{{ __('marketplace.cart.cart_empty') }}</p>
                                        <small>{{ __('marketplace.cart.add_products') }}</small>
                                    </div>
                                    <!-- Cart items will be loaded here -->
                                </div>
                                <div class="p-3 border-top" id="miniCartFooter" style="display: none;">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span class="fw-bold" id="miniCartSubtotal">$0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3 small text-muted">
                                        <span>Shipping & taxes calculated at checkout</span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('marketplace.cart.index') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-shopping-cart me-1"></i>
                                            View Cart
                                        </a>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="proceedToCheckout()">
                                            <i class="fas fa-credit-card me-1"></i>
                                            Checkout
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                            @endif
                        @endauth

                        <!-- Notifications - Enhanced with new system -->
                        <li class="nav-item">
                            <x-notification-dropdown position="end" />
                        </li>

                        <!-- Language Switcher -->
                        <li class="nav-item">
                            @include('partials.language-switcher')
                        </li>

                        @auth
                        <!-- User Menu -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ Auth::user()->getAvatarUrl() }}" alt="{{ Auth::user()->name }}" class="rounded-circle me-2" width="24" height="24">
                                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <!-- User Info Header -->
                                <li class="dropdown-header">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ Auth::user()->getAvatarUrl() }}" alt="{{ Auth::user()->name }}" class="rounded-circle me-2" width="32" height="32">
                                        <div>
                                            <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                            <small class="text-muted">{{ Auth::user()->getRoleDisplayName() }}</small>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>

                                <!-- Role-based Dashboard Links -->
                                @if(Auth::user()->hasAnyRole(['admin', 'moderator']))
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bx bx-tachometer me-2"></i>
                                        {{ __('ui.common.admin_dashboard') }}
                                    </a>
                                </li>
                                @endif

                                @if(Auth::user()->hasRole('supplier'))
                                <li>
                                    <a class="dropdown-item" href="{{ route('supplier.dashboard') }}">
                                        <i class="bx bx-store me-2"></i>
                                        {{ __('ui.common.supplier_dashboard') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('supplier.products.index') }}">
                                        <i class="bx bx-package me-2"></i>
                                        {{ __('ui.common.product_management') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('supplier.orders.index') }}">
                                        <i class="bx bx-list-ul me-2"></i>
                                        {{ __('ui.common.my_orders') }}
                                    </a>
                                </li>
                                @endif

                                @if(Auth::user()->hasRole('manufacturer'))
                                <li>
                                    <a class="dropdown-item" href="{{ route('manufacturer.dashboard') }}">
                                        <i class="bx bx-cube me-2"></i>
                                        {{ __('ui.common.manufacturer_dashboard') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('manufacturer.designs.index') }}">
                                        <i class="bx bx-cube-alt me-2"></i>
                                        {{ __('ui.common.design_management') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('manufacturer.orders.index') }}">
                                        <i class="bx bx-download me-2"></i>
                                        {{ __('ui.common.download_orders') }}
                                    </a>
                                </li>
                                @endif

                                @if(Auth::user()->hasRole('brand'))
                                <li>
                                    <a class="dropdown-item" href="{{ route('brand.dashboard') }}">
                                        <i class="bx bx-bullhorn me-2"></i>
                                        {{ __('ui.common.brand_dashboard') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('brand.insights.index') }}">
                                        <i class="bx bx-bar-chart me-2"></i>
                                        {{ __('ui.common.market_analysis') }}
                                    </a>
                                </li>
                                @endif

                                @if(Auth::user()->hasAnyRole(['supplier', 'manufacturer', 'brand']))
                                <li><hr class="dropdown-divider"></li>
                                @endif



                                <!-- Common User Menu Items -->
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.show', Auth::user()->username) }}">
                                        <i class="fa-regular fa-address-card me-2"></i>
                                        {{ __('nav.user.profile') }}
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('chat.index') }}">
                                        <i class="fa-solid fa-comments me-2"></i>
                                        {{ __('ui.common.messages') }}
                                        @php
                                        $unreadMessagesCount = \App\Models\Message::whereHas('conversation.participants', function ($query) {
                                            $query->where('user_id', Auth::id())
                                                ->where(function ($q) {
                                                    $q->whereNull('last_read_at')
                                                        ->orWhereColumn('messages.created_at', '>', 'conversation_participants.last_read_at');
                                                });
                                        })
                                        ->where('user_id', '!=', Auth::id())
                                        ->count();
                                        @endphp
                                        @if($unreadMessagesCount > 0)
                                        <span class="badge bg-primary rounded-pill ms-auto" id="headerMessagesBadge">{{ $unreadMessagesCount }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('alerts.index') }}">
                                        <i class="fa-solid fa-bell me-2"></i>
                                        {{ __('ui.common.notifications') }}
                                        @php
                                        $unreadAlertsCount = Auth::user()->alerts()->whereNull('read_at')->count();
                                        @endphp
                                        @if($unreadAlertsCount > 0)
                                        <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadAlertsCount }}</span>
                                        @endif
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('user.bookmarks') }}">
                                        <i class="fa-regular fa-bookmark me-2"></i>
                                        {{ __('ui.common.saved') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('showcase.index') }}">
                                        <i class="fas fa-image me-2"></i>
                                        {{ __('ui.common.my_showcase') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-cog me-2"></i>
                                        {{ __('nav.user.settings') }}
                                    </a>
                                </li>

                                <!-- Business Features -->
                                @if(Auth::user()->hasAnyRole(['supplier', 'manufacturer', 'brand']))
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('business.index') }}">
                                        <i class="fas fa-briefcase me-2"></i>
                                        {{ __('ui.common.my_business') }}
                                    </a>
                                </li>
                                @if(Auth::user()->hasAnyRole(['supplier', 'manufacturer']))
                                <li>
                                    <a class="dropdown-item" href="{{ route('marketplace.seller.verification-status') }}">
                                        <i class="bx bx-shield-check me-2"></i>
                                        {{ __('ui.common.verification_status') }}
                                    </a>
                                </li>
                                @endif
                                @endif

                                <li>
                                    <a class="dropdown-item" href="{{ route('subscription.index') }}">
                                        <i class="fas fa-star me-2"></i>
                                        {{ __('ui.common.my_subscription') }}
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            {{ __('auth.logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        @else
                        <!-- Login/Register -->
                        <li class="nav-item">
                            <button type="button" class="nav-link btn btn-link" onclick="openLoginModal()">
                                <i class="fa-regular fa-user me-1"></i>
                            </button>
                        </li>
                        <!--li class="nav-item">
                            <button type="button" class="btn btn-primary btn-sm ms-2" onclick="openRegisterModal()">
                                {{ __('auth.register.title') }}
                            </button>
                        </li-->
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <!-- Mobile Search Modal -->
    <div class="modal fade" id="mobileSearchModal" tabindex="-1" aria-labelledby="mobileSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="mobileSearchModalLabel">
                        <i class="fa-solid fa-search me-2"></i>Search MechaMap
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg" id="mobileSearchInput" placeholder="Search products, forums, members..." aria-label="Search">
                        <button class="btn btn-primary" type="button" id="mobileSearchButton">
                            <i class="fa-solid fa-search"></i>
                        </button>
                    </div>

                    <!-- Quick Search Categories -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <button class="btn btn-outline-primary btn-sm w-100 mobile-search-scope" data-scope="marketplace">
                                <i class="fa-solid fa-store me-1"></i>Products
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-primary btn-sm w-100 mobile-search-scope" data-scope="forum">
                                <i class="fa-solid fa-comments me-1"></i>Forums
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-primary btn-sm w-100 mobile-search-scope" data-scope="members">
                                <i class="fa-solid fa-users me-1"></i>Members
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-primary btn-sm w-100 mobile-search-scope" data-scope="technical">
                                <i class="fa-solid fa-screwdriver-wrench me-1"></i>Technical
                            </button>
                        </div>
                    </div>

                    <!-- Recent Searches -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">{{ __('forum.search.recent_searches') }}</h6>
                        <div id="mobileRecentSearches">
                            <small class="text-muted">{{ __('forum.search.no_recent_searches') }}</small>
                        </div>
                    </div>

                    <!-- Popular Searches -->
                    <div>
                        <h6 class="text-muted mb-2">{{ __('forum.search.popular_searches') }}</h6>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark">bearings</span>
                            <span class="badge bg-light text-dark">steel materials</span>
                            <span class="badge bg-light text-dark">{{ __('forum.search.cad_files') }}</span>
                            <span class="badge bg-light text-dark">manufacturing</span>
                            <span class="badge bg-light text-dark">{{ __('forum.search.iso_standards') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- JavaScript for header functionality -->
<script>
    window.addEventListener('scroll', function () {
        const header = document.getElementById('header-content');

        if (window.scrollY > 200) {
        header.classList.add('sticky-top', 'shadow'); // Thêm sticky-top và hiệu ứng đổ bóng
        } else {
        header.classList.remove('sticky-top', 'shadow');
        }
    });
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality - Reusing old header structure
    const searchInput = document.getElementById('unified-search');
    const searchButton = document.getElementById('unified-search-btn');
    const searchResultsDropdown = document.getElementById('search-results-dropdown');
    const searchResultsContent = document.getElementById('search-results-content');
    const searchScopeOptions = document.querySelectorAll('.search-scope-option');

    // Variables from old search
    let currentSearchScope = 'site';
    let searchTimeout;
    let currentThreadId = null;
    let currentForumId = null;

    // Try to get thread and forum IDs from the page if available
    const threadElement = document.querySelector('[data-thread-id]');
    const forumElement = document.querySelector('[data-forum-id]');

    if (threadElement) {
        currentThreadId = threadElement.dataset.threadId;
    }

    if (forumElement) {
        currentForumId = forumElement.dataset.forumId;
    }

    // Initialize search scope options
    if (!currentThreadId) {
        // Disable thread scope option if not on a thread page
        const threadScopeOption = document.querySelector('.search-scope-option[data-scope="thread"]');
        if (threadScopeOption) {
            threadScopeOption.classList.add('disabled');
            threadScopeOption.style.display = 'none';
        }
    } else {
        // Show thread scope option if on a thread page
        const threadScopeOption = document.querySelector('.search-scope-option[data-scope="thread"]');
        if (threadScopeOption) {
            threadScopeOption.style.display = 'block';
        }
    }

    if (!currentForumId) {
        // Disable forum scope option if not on a forum page
        const forumScopeOption = document.querySelector('.search-scope-option[data-scope="forum"]');
        if (forumScopeOption) {
            forumScopeOption.classList.add('disabled');
            forumScopeOption.style.display = 'none';
        }
    } else {
        // Show forum scope option if on a forum page
        const forumScopeOption = document.querySelector('.search-scope-option[data-scope="forum"]');
        if (forumScopeOption) {
            forumScopeOption.style.display = 'block';
        }
    }

    // Check if search elements exist
    if (!searchInput || !searchButton || !searchResultsDropdown || !searchResultsContent) {
        console.warn('Search elements not found on this page');
        return;
    }

    // Duplicate code removed - already handled above

    // Event Listeners - From old search
    searchInput.addEventListener('focus', function() {
        if (this.value.length >= 2) {
            showSearchResults();
            performSearch(this.value);
        }
    });

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        // Clear previous timeout
        clearTimeout(searchTimeout);

        if (query.length >= 2) {
            // Show loading state
            showSearchResults();
            searchResultsContent.innerHTML = '<div class="search-loading p-3 text-center"><i class="fas fa-hourglass-half me-2"></i>Searching...</div>';

            // Set a timeout to avoid too many requests
            searchTimeout = setTimeout(function() {
                performSearch(query);
            }, 300);
        } else {
            hideSearchResults();
        }
    });

    searchButton.addEventListener('click', function() {
        const query = searchInput.value.trim();

        if (query.length >= 2) {
            // Redirect to search page
            window.location.href = `/search?query=${encodeURIComponent(query)}&type=all`;
        }
    });

    // Handle Enter key in search input
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = this.value.trim();

            if (query.length >= 2) {
                // Redirect to search page
                window.location.href = `/search?query=${encodeURIComponent(query)}&type=all`;
            }
        }
    });

    // Handle search scope selection
    searchScopeOptions.forEach(option => {
        option.addEventListener('click', function() {
            if (this.classList.contains('disabled')) {
                return;
            }

            // Update active scope
            searchScopeOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');

            // Update current scope
            currentSearchScope = this.dataset.scope;

            // Perform search with new scope
            const query = searchInput.value.trim();
            if (query.length >= 2) {
                performSearch(query);
            }
        });
    });

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchResultsDropdown.contains(e.target) && e.target !== searchInput && e.target !== searchButton) {
            hideSearchResults();
        }
    });

    // Functions - From old search
    function showSearchResults() {
        searchResultsDropdown.style.display = 'block';
        searchResultsDropdown.classList.add('show');
    }

    function hideSearchResults() {
        searchResultsDropdown.style.display = 'none';
        searchResultsDropdown.classList.remove('show');
    }

    function performSearch(query) {
        // Prepare request parameters
        let params = {
            query: query,
            scope: currentSearchScope
        };

        // Add thread or forum ID if needed
        if (currentSearchScope === 'thread' && currentThreadId) {
            params.thread_id = currentThreadId;
        } else if (currentSearchScope === 'forum' && currentForumId) {
            params.forum_id = currentForumId;
        }

        // Build query string
        const queryString = Object.keys(params)
            .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
            .join('&');

        // Make AJAX request to unified search API
        fetch(`/api/v1/search/unified?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displayUnifiedSearchResults(data);
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResultsContent.innerHTML = '<div class="search-no-results p-3 text-center text-danger">An error occurred while searching. Please try again.</div>';
            });
    }

    function displayUnifiedSearchResults(data) {
        // Clear previous results
        searchResultsContent.innerHTML = '';

        if (!data.success || !data.results) {
            searchResultsContent.innerHTML = '<div class="search-no-results p-3 text-center text-danger">Search failed. Please try again.</div>';
            return;
        }

        const results = data.results;
        const totalResults = results.meta.total;

        if (totalResults === 0) {
            searchResultsContent.innerHTML = `
                <div class="search-no-results p-3 text-center">
                    <i class="fas fa-search me-2"></i>No results found for "${results.meta.query}".
                    <p class="mt-2 mb-0">
                        <a href="${data.advanced_search_url}" class="btn btn-sm btn-primary" style="background: #8B7355; border-color: #8B7355;">
                            <i class="fas fa-sliders-h me-1"></i>THỬ TÌM KIẾM NÂNG CAO
                        </a>
                    </p>
                </div>
            `;
            return;
        }

        let html = '';

        // Display Threads
        if (results.threads && results.threads.length > 0) {
            html += '<div class="search-category mb-3">';
            html += '<h6 class="search-category-title text-primary mb-2"><i class="fas fa-comments me-1"></i>Thảo luận</h6>';
            results.threads.forEach(thread => {
                html += `
                    <div class="search-result-item p-2 border-bottom">
                        <div class="d-flex">
                            <img src="${thread.author.avatar}" class="rounded-circle me-2" width="32" height="32" alt="${thread.author.name}">
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><a href="${thread.url}" class="text-decoration-none">${thread.title}</a></h6>
                                <p class="mb-1 text-muted small">${thread.excerpt}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>${thread.author.name} •
                                        <i class="fas fa-folder me-1"></i>${thread.forum.name}
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-comments me-1"></i>${thread.stats.comments} • ${thread.created_at}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }

        // Display Showcases
        if (results.showcases && results.showcases.length > 0) {
            html += '<div class="search-category mb-3">';
            html += '<h6 class="search-category-title text-success mb-2"><i class="fas fa-star me-1"></i>Showcase</h6>';
            results.showcases.forEach(showcase => {
                html += `
                    <div class="search-result-item p-2 border-bottom">
                        <div class="d-flex">
                            ${showcase.image ? `<img src="${showcase.image}" class="rounded me-2" width="40" height="40" alt="${showcase.title}" style="object-fit: cover;">` : '<div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-image text-muted"></i></div>'}
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><a href="${showcase.url}" class="text-decoration-none">${showcase.title}</a></h6>
                                <p class="mb-1 text-muted small">${showcase.excerpt}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>${showcase.author.name} •
                                        <span class="badge badge-sm bg-secondary">${showcase.project_type}</span>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-eye me-1"></i>${showcase.stats.views} • ⭐${showcase.stats.rating}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }

        // Display Products
        if (results.products && results.products.length > 0) {
            html += '<div class="search-category mb-3">';
            html += '<h6 class="search-category-title text-warning mb-2"><i class="fas fa-shopping-cart me-1"></i>Sản phẩm</h6>';
            results.products.forEach(product => {
                html += `
                    <div class="search-result-item p-2 border-bottom">
                        <div class="d-flex">
                            ${product.image ? `<img src="${product.image}" class="rounded me-2" width="40" height="40" alt="${product.title}" style="object-fit: cover;">` : '<div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-box text-muted"></i></div>'}
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="${product.url}" class="text-decoration-none">${product.title}</a>
                                    <span class="badge bg-success ms-1">${product.price.formatted}</span>
                                </h6>
                                <p class="mb-1 text-muted small">${product.excerpt}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-store me-1"></i>${product.seller.name}
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-eye me-1"></i>${product.stats.views} • ${product.created_at}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }

        // Display Users
        if (results.users && results.users.length > 0) {
            html += '<div class="search-category mb-3">';
            html += '<h6 class="search-category-title text-info mb-2"><i class="fas fa-users me-1"></i>Thành viên</h6>';
            results.users.forEach(user => {
                html += `
                    <div class="search-result-item p-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <img src="${user.avatar}" class="rounded-circle me-2" width="32" height="32" alt="${user.name}">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="${user.url}" class="text-decoration-none">${user.name}</a>
                                    <small class="text-muted">@${user.username}</small>
                                </h6>
                                <small class="text-muted">
                                    <span class="badge bg-secondary">${user.role}</span>
                                    ${user.business_name ? `• ${user.business_name}` : ''}
                                </small>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }

        // Add footer with advanced search link
        html += `
            <div class="search-results-footer p-2 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Tìm thấy ${totalResults} kết quả</small>
                    <a href="${data.advanced_search_url}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-search-plus me-1"></i>Tìm kiếm nâng cao
                    </a>
                </div>
            </div>
        `;

        searchResultsContent.innerHTML = html;
    }

    // Legacy function for backward compatibility
    function displaySearchResults(data) {
        // Clear previous results
        searchResultsContent.innerHTML = '';

        const results = data.results;

        // Check if we have any results - Exact logic from old search
        const hasThreads = results.threads && results.threads.length > 0;
        const hasPosts = results.posts && results.posts.length > 0;
        const hasForum = results.forum;
        const hasThread = results.thread;

        if (!hasThreads && !hasPosts && !hasForum && !hasThread) {
            searchResultsContent.innerHTML = `
                <div class="search-no-results p-3 text-center">
                    <i class="fas fa-search me-2"></i>No results found for "${searchInput.value}".
                    <p class="mt-2">
                        <a href="${data.advanced_search_url || '{{ route('forums.search.advanced') }}'}" class="btn btn-sm btn-primary" style="background: #8B7355; border-color: #8B7355;">
                            <i class="fas fa-sliders-h me-1"></i>THỬ TÌM KIẾM NÂNG CAO
                        </a>
                    </p>
                </div>
            `;
            return;
        }

        // Build results HTML - Exact structure from old search
        let resultsHTML = '';

        // If searching in a specific thread
        if (hasThread) {
            resultsHTML += `
                <div class="search-result-section">
                    <div class="search-result-section-title">Chủ đề</div>
                    <div class="search-result-item">
                        <div class="search-result-item-title">
                            <a href="${results.thread.url}">${results.thread.title}</a>
                        </div>
                    </div>
                </div>
            `;
        }

        // If searching in a specific forum
        if (hasForum) {
            resultsHTML += `
                <div class="search-result-section">
                    <div class="search-result-section-title">{{ __('forum.search.forum') }}</div>
                    <div class="search-result-item">
                        <div class="search-result-item-title">
                            <a href="${results.forum.url}">${results.forum.name}</a>
                        </div>
                    </div>
                </div>
            `;
        }

        // Display threads - Exact structure from old search
        if (hasThreads) {
            resultsHTML += `
                <div class="search-result-section">
                    <div class="search-result-section-title">{{ __('forum.search.threads') }}</div>
            `;

            results.threads.forEach(thread => {
                resultsHTML += `
                    <div class="search-result-item">
                        <div class="search-result-item-title">
                            <a href="${thread.url}">${thread.title}</a>
                        </div>
                        <div class="search-result-item-content">${thread.content}</div>
                        <div class="search-result-item-meta">
                            by <a href="/users/${thread.user.username}">${thread.user.name}</a>
                            ${thread.forum ? `in <a href="${thread.forum.url}">${thread.forum.name}</a>` : ''}
                        </div>
                    </div>
                `;
            });

            resultsHTML += `</div>`;
        }

        // Display posts - Exact structure from old search
        if (hasPosts) {
            resultsHTML += `
                <div class="search-result-section">
                    <div class="search-result-section-title">Posts</div>
            `;

            results.posts.forEach(post => {
                resultsHTML += `
                    <div class="search-result-item">
                        <div class="search-result-item-content">${post.content}</div>
                        <div class="search-result-item-meta">
                            by <a href="/users/${post.user.username}">${post.user.name}</a>
                            ${post.thread ? `in <a href="${post.thread.url}">${post.thread.title}</a>` : ''}
                        </div>
                    </div>
                `;
            });

            resultsHTML += `</div>`;
        }

        // Add "View all results" link - Exact from old search
        resultsHTML += `
            <div class="text-center mt-3">
                <a href="/search?query=${encodeURIComponent(searchInput.value)}&type=all" class="btn btn-sm btn-outline-primary">
                    Xem tất cả kết quả
                </a>
            </div>
        `;

        // Update content
        searchResultsContent.innerHTML = resultsHTML;
    }



    // Cart toggle - Use Bootstrap dropdown events instead of click to avoid conflicts
    // Always initialize if cart element exists (regardless of isMarketplace flag)
    const cartToggle = document.getElementById('cartToggle');
    if (cartToggle) {
        // Use Bootstrap dropdown events instead of click to avoid preventDefault conflicts
        cartToggle.addEventListener('show.bs.dropdown', function() {
            // Load cart data when dropdown is about to show
            setTimeout(() => loadMiniCart(), 50);
            // Dispatch custom event for cart toggle
            window.dispatchEvent(new CustomEvent('toggle-cart'));
        });
    }

    // Notification toggle - Already handled by notification-dropdown component
    // The notification dropdown component has its own event handling with show.bs.dropdown
    // No additional event listeners needed here to avoid conflicts

    // Dark mode toggle
    const darkModeSwitch = document.getElementById('darkModeSwitch');
    if (darkModeSwitch) {
        darkModeSwitch.addEventListener('change', function() {
            const isDark = this.checked;

            // Update cookie
            document.cookie = `dark_mode=${isDark ? 'dark' : 'light'}; path=/; max-age=31536000`;

            // Update theme icons and text
            const themeIconDark = document.querySelector('.theme-icon-dark');
            const themeIconLight = document.querySelector('.theme-icon-light');
            const themeText = document.querySelector('.theme-text');

            if (isDark) {
                themeIconDark.classList.add('d-none');
                themeIconLight.classList.remove('d-none');
                themeText.textContent = 'Chế độ sáng';
                document.body.classList.add('dark-mode');
            } else {
                themeIconDark.classList.remove('d-none');
                themeIconLight.classList.add('d-none');
                themeText.textContent = 'Chế độ tối';
                document.body.classList.remove('dark-mode');
            }
        });
    }

    // Mini Cart functionality
    window.loadMiniCart = function() {
        fetch('/marketplace/cart/data')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateMiniCartUI(data.cart);
                }
            })
            .catch(error => console.error('Error loading mini cart:', error));
    };

    window.updateMiniCartUI = function(cart) {
        const cartCount = document.getElementById('cartCount');
        const miniCartItemCount = document.getElementById('miniCartItemCount');
        const miniCartItems = document.getElementById('miniCartItems');
        const miniCartSubtotal = document.getElementById('miniCartSubtotal');
        const miniCartFooter = document.getElementById('miniCartFooter');

        // Update cart count
        if (cartCount) {
            cartCount.textContent = cart.total_items;
            cartCount.style.display = cart.total_items > 0 ? 'inline' : 'none';
        }

        if (miniCartItemCount) {
            miniCartItemCount.textContent = cart.total_items;
        }

        if (cart.total_items === 0) {
            // Show empty state
            if (miniCartFooter) miniCartFooter.style.display = 'none';
            miniCartItems.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-shopping-cart-x" style="font-size: 2.5rem;"></i>
                    <p class="mb-0 mt-2">Your cart is empty</p>
                    <small>Add some products to get started</small>
                </div>
            `;
        } else {
            // Show cart items
            if (miniCartFooter) miniCartFooter.style.display = 'block';

            let itemsHTML = '';
            cart.items.forEach(item => {
                itemsHTML += `
                    <div class="p-3 border-bottom mini-cart-item">
                        <div class="row align-items-center">
                            <div class="col-3">
                                ${item.product_image ?
                                    `<img src="${item.product_image}" class="img-fluid rounded" alt="${item.product_name}" style="height: 50px; object-fit: cover;">` :
                                    `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 50px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>`
                                }
                            </div>
                            <div class="col-6">
                                <h6 class="mb-1 small">${item.product_name}</h6>
                                <div class="small text-muted">Qty: ${item.quantity}</div>
                                ${item.is_on_sale ?
                                    `<div class="small text-danger">$${item.sale_price}</div>` :
                                    `<div class="small">$${item.unit_price}</div>`
                                }
                            </div>
                            <div class="col-3 text-end">
                                <div class="fw-bold small">$${item.total_price}</div>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-1 mini-cart-remove-btn"
                                        data-item-id="${item.id}" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            miniCartItems.innerHTML = itemsHTML;

            // Update subtotal
            if (miniCartSubtotal) {
                miniCartSubtotal.textContent = '$' + cart.subtotal;
            }
        }
    };

    window.removeMiniCartItem = function(itemId) {
        fetch(`/marketplace/cart/remove/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMiniCart(); // Reload mini cart
                if (typeof showToast === 'function') {
                    showToast('success', data.message);
                }
            } else {
                if (typeof showToast === 'function') {
                    showToast('error', data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof showToast === 'function') {
                showToast('error', 'Failed to remove item');
            }
        });
    };

    // Load mini cart on page load
    loadMiniCart();

    // Refresh mini cart when dropdown is opened - handled above with show.bs.dropdown event
    // Removed duplicate event listener to avoid conflicts with Bootstrap dropdown

    // Notification functionality
    window.loadNotifications = function() {
        @auth
        fetch('/api/notifications/recent')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationUI(data.notifications);
                    updateNotificationCount(data.total_unread);
                }
            })
            .catch(error => console.error('Error loading notifications:', error));
        @endauth
    };

    window.updateNotificationUI = function(notifications) {
        const notificationItems = document.getElementById('notificationItems');
        const notificationEmpty = document.getElementById('notificationEmpty');

        if (!notificationItems) return;

        if (notifications.length === 0) {
            notificationEmpty.style.display = 'block';
            return;
        }

        notificationEmpty.style.display = 'none';

        let itemsHTML = '';
        notifications.forEach(notification => {
            itemsHTML += `
                <div class="notification-item p-3 border-bottom" data-id="${notification.id}">
                    <div class="d-flex">
                        <div class="avatar-sm me-3">
                            <span class="avatar-title bg-${notification.color} rounded-circle">
                                <i class="fas fa-${notification.icon}"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 font-size-14">${notification.title}</h6>
                            <p class="mb-1 text-muted font-size-13">${notification.message}</p>
                            <small class="text-muted">${notification.time_ago}</small>
                        </div>
                        ${!notification.is_read ? '<div class="ms-2"><span class="badge bg-primary rounded-pill">Mới</span></div>' : ''}
                    </div>
                </div>
            `;
        });

        notificationItems.innerHTML = itemsHTML;
    };

    window.updateNotificationCount = function(count) {
        const notificationCount = document.getElementById('notificationCount');
        if (notificationCount) {
            if (count > 0) {
                notificationCount.textContent = count > 99 ? '99+' : count;
                notificationCount.style.display = 'block';
            } else {
                notificationCount.style.display = 'none';
            }
        }
    };

    window.markAllNotificationsRead = function() {
        @auth
        fetch('/api/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
                showNotification('Đã đánh dấu tất cả thông báo là đã đọc', 'success');
            }
        })
        .catch(error => console.error('Error marking notifications as read:', error));
        @endauth
    };

    // Load notifications when dropdown is opened - handled by notification-dropdown component
    // Removed duplicate event listener to avoid conflicts with Bootstrap dropdown

    // Auto-refresh notifications every 30 seconds
    @auth
    setInterval(() => {
        if (document.visibilityState === 'visible') {
            loadNotifications();
        }
    }, 30000);
    @endauth
});
</script>

<!-- Mobile Navigation Component -->
@include('components.mobile-nav')

<!-- Mini Cart Enhancements -->
<script src="{{ asset('assets/js/mini-cart-enhancements.js') }}"></script>
