{{--
    MechaMap Unified Header Component
    Sử dụng cho tất cả trang frontend user
--}}
@props(['showBanner' => true, 'isMarketplace' => false])

<!-- User Dropdown Component CSS -->
<link rel="stylesheet" href="{{ asset('css/frontend/components/user-dropdown.css') }}?v={{ time() }}">

<header class="site-header">
    <!-- Banner (optional) -->
    @if($showBanner && get_setting('show_banner', true))
    <div class="header-banner">
        <img src="{{ get_banner_url() }}" alt="{{ __('messages.header.banner_alt') }}" class="w-100">
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
                    <button class="btn-nav_mobile btn btn-outline-secondary btn-sm me-2" type="button" data-bs-toggle="modal" data-bs-target="#mobileSearchModal">
                        <i class="fa-solid fa-search"></i>
                    </button>

                    <!-- Mobile Cart - Only show if user can buy products -->
                    @auth
                        @if(auth()->user()->canBuyAnyProduct())
                            <a class="btn-nav_mobile btn btn-outline-primary btn-sm me-2 position-relative" href="{{ route('marketplace.cart.index') }}">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" id="mobileCartCount" style="display: none;">0</span>
                            </a>
                        @endif
                    @endauth

                    <!-- Mobile Menu Button - HC-MobileNav -->
                    <button class="btn-nav_mobile hc-mobile-nav-toggle border-0 bg-transparent" type="button" aria-label="{{ __('messages.header.mobile_nav_toggle') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <!-- Navigation Menu -->
                <div class="collapse navbar-collapse justify-content-between position-relative" id="navbarNav">
                    <!-- Search Bar - Using old header structure -->
                    <div class="search-container position-relative" style="min-width: 300px;">
                        <div class="input-group">
                            <input type="text" class="form-control search-input" id="unified-search" name="query" autocomplete="off"
                                placeholder="{{ $isMarketplace ? __('search.form.placeholder') : __('search.form.placeholder') }}" aria-label="{{ __('common.buttons.search') }}">
                            <button class="btn btn-outline-secondary" type="button" id="unified-search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        <!-- Search Results Dropdown - Enhanced with Content Type Filters -->
                        <div class="search-results-dropdown" id="search-results-dropdown">
                            <div class="search-content-filters">
                                <div class="search-filter-option active" data-filter="all">
                                    <i class="fas fa-th-large"></i> {{ __('ui.search.filters.all') }}
                                </div>
                                <div class="search-filter-option" data-filter="threads">
                                    <i class="fas fa-comments"></i> {{ __('ui.search.filters.threads') }}
                                </div>
                                <div class="search-filter-option" data-filter="showcases">
                                    <i class="fas fa-star"></i> {{ __('ui.search.filters.showcases') }}
                                </div>
                                <div class="search-filter-option" data-filter="products">
                                    <i class="fas fa-shopping-cart"></i> {{ __('ui.search.filters.products') }}
                                </div>
                                <div class="search-filter-option" data-filter="users">
                                    <i class="fas fa-users"></i> {{ __('ui.search.filters.users') }}
                                </div>
                            </div>
                            <div class="search-results-content" id="search-results-content">
                                <!-- Results will be loaded here via AJAX -->
                            </div>
                        </div>
                    </div>
                    <ul class="navbar-nav">
                        <!-- 2. Community/Forum - PRIORITY #1 - Mega Menu -->
                        <li class="nav-item dropdown mega-menu-dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs(['forums.*', 'members.*', 'events.*', 'jobs.*']) ? 'active' : '' }}" href="#" id="communityDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-users me-1"></i>
                                {{ t_navigation('main.community') }}
                            </a>
                            <div class="dropdown-menu mega-menu" aria-labelledby="communityDropdown">
                                @include('components.menu.community-mega-menu')
                            </div>
                        </li>

                        <!-- 3. Dự án - Direct Link -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('showcase.*') ? 'active' : '' }}" href="{{ route('showcase.index') }}">
                                <i class="fa-solid fa-trophy me-1"></i>
                                {{ t_navigation('main.showcase') }}
                            </a>
                        </li>

                        <!-- 4. Marketplace - PRIORITY #3 - Always visible for everyone -->
                        <li class="nav-item dropdown mega-menu-dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('marketplace.*') ? 'active' : '' }}" href="#" id="marketplaceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-store me-1"></i>
                                {{ t_navigation('main.marketplace') }}
                            </a>
                            <div class="dropdown-menu mega-menu" aria-labelledby="marketplaceDropdown">
                                @include('components.menu.marketplace-mega-menu')
                            </div>
                        </li>

                        <!-- 5. Add/Create - PRIORITY #4 - Mega Menu for content creation -->
                        <li class="nav-item dropdown mega-menu-dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs(['threads.create', 'showcase.create', 'gallery.upload', 'marketplace.products.create']) ? 'active' : '' }}" href="#" id="addDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-plus me-1"></i>
                                {{ t_navigation('actions.add') }}
                            </a>
                            <div class="dropdown-menu mega-menu" aria-labelledby="addDropdown">
                                @include('components.menu.add-mega-menu')
                            </div>
                        </li>

                        <!-- Role-based Quick Access -->
                        @auth
                            @if(Auth::user()->hasAnyRole(['admin', 'moderator']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-shield-check me-1"></i>
                                    {{ __('navigation.admin.title') }}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bx bx-tachometer me-2"></i>{{ __('navigation.admin.dashboard') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                        <i class="bx bx-user me-2"></i>{{ __('navigation.admin.user_management') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.threads.index') }}">
                                        <i class="bx bx-chat me-2"></i>{{ __('navigation.admin.forum_management') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.products.index') }}">
                                        <i class="bx bx-package me-2"></i>{{ __('navigation.admin.marketplace_management') }}
                                    </a></li>
                                </ul>
                            </li>
                            @endif

                            @if(Auth::user()->hasRole('supplier'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="supplierDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-store me-1"></i>
                                    {{ __('messages.header.roles.supplier') }}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="supplierDropdown">
                                    <li><a class="dropdown-item" href="{{ route('supplier.dashboard') }}">
                                        <i class="bx bx-tachometer me-2"></i>{{ __('navigation.supplier.dashboard') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('supplier.products.index') }}">
                                        <i class="bx bx-package me-2"></i>{{ __('navigation.supplier.my_products') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('supplier.orders.index') }}">
                                        <i class="bx bx-list-ul me-2"></i>{{ __('navigation.supplier.orders') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('supplier.analytics.index') }}">
                                        <i class="bx bx-bar-chart me-2"></i>{{ __('navigation.supplier.reports') }}
                                    </a></li>
                                </ul>
                            </li>
                            @endif



                            @if(Auth::user()->hasRole('brand'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="brandDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-bullhorn me-1"></i>
                                    {{ __('messages.header.roles.brand') }}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="brandDropdown">
                                    <li><a class="dropdown-item" href="{{ route('brand.dashboard') }}">
                                        <i class="bx bx-tachometer me-2"></i>{{ __('navigation.brand.dashboard') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('brand.insights.index') }}">
                                        <i class="bx bx-bulb me-2"></i>{{ __('navigation.brand.market_insights') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('brand.marketplace.analytics') }}">
                                        <i class="bx bx-store me-2"></i>{{ __('navigation.brand.marketplace_analytics') }}
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('brand.promotion.index') }}">
                                        <i class="bx bx-megaphone me-2"></i>{{ __('navigation.brand.promotion_opportunities') }}
                                    </a></li>
                                </ul>
                            </li>
                            @endif
                        @endauth
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
                                            <h6 class="mb-0">{{ __('marketplace.cart.title') }}</h6>
                                            <span class="badge bg-primary" id="miniCartItemCount">0</span>
                                        </div>
                                <div id="miniCartItems" style="max-height: 350px; overflow-y: auto;">
                                    <!-- Empty state -->
                                    <div class="text-center text-muted py-4" id="miniCartEmpty">
                                        <i class="fas fa-shopping-cart-x" style="font-size: 2.5rem;"></i>
                                        <p class="mb-0 mt-2">{{ __('marketplace.cart.empty_message') }}</p>
                                        <small>{{ __('marketplace.cart.add_items') }}</small>
                                    </div>
                                    <!-- Cart items will be loaded here -->
                                </div>
                                <div class="p-3 border-top" id="miniCartFooter" style="display: none;">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>{{ __('messages.cart.subtotal') }}</span>
                                        <span class="fw-bold" id="miniCartSubtotal">$0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3 small text-muted">
                                        <span>{{ __('messages.cart.shipping_taxes_note') }}</span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('marketplace.cart.index') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-shopping-cart me-1"></i>
                                            {{ __('messages.cart.view_cart') }}
                                        </a>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="proceedToCheckout()">
                                            <i class="fas fa-credit-card me-1"></i>
                                            {{ __('messages.cart.checkout') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                            @endif
                        @endauth

                        <!-- Notifications - Realtime Dropdown -->
                        @auth
                        @include('partials.notification-dropdown')
                        @endauth

                        <!-- Language Switcher -->
                        <li class="nav-item">
                            @include('partials.language-switcher')
                        </li>

                        @auth
                        <!-- User Menu - New Dashboard-based Dropdown -->
                        <x-user-dropdown />
                        @else
                        <!-- Login/Register -->
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link" aria-label="{{ t_auth('login.title') }}">
                                <i class="fa-regular fa-user me-1"></i>
                                <span class="d-none d-md-inline">{{ t_auth('login.title') }}</span>
                            </a>
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
                        <i class="fa-solid fa-search me-2"></i>{{ __('messages.search.mobile.title') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <!-- Mobile Search Input with Dropdown -->
                    <div class="position-relative mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-lg" id="mobileSearchInput"
                                   placeholder="{{ __('messages.search.mobile.placeholder') }}" aria-label="Search" autocomplete="off">
                            <button class="btn btn-primary" type="button" id="mobileSearchButton">
                                <i class="fa-solid fa-search"></i>
                            </button>
                        </div>

                        <!-- Mobile Search Results Dropdown -->
                        <div class="mobile-search-results-dropdown" id="mobileSearchResultsDropdown" style="display: none;">
                            <!-- Content Type Filters for Mobile -->
                            <div class="mobile-search-content-filters p-2 border-bottom">
                                <div class="d-flex flex-wrap gap-1">
                                    <button class="btn btn-sm btn-outline-secondary mobile-search-filter-option active" data-filter="all">
                                        <i class="fas fa-th-large me-1"></i>{{ __('ui.search.filters.all') }}
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary mobile-search-filter-option" data-filter="threads">
                                        <i class="fas fa-comments me-1"></i>{{ __('ui.search.filters.threads') }}
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary mobile-search-filter-option" data-filter="showcases">
                                        <i class="fas fa-star me-1"></i>{{ __('ui.search.filters.showcases') }}
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary mobile-search-filter-option" data-filter="products">
                                        <i class="fas fa-shopping-cart me-1"></i>{{ __('ui.search.filters.products') }}
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary mobile-search-filter-option" data-filter="users">
                                        <i class="fas fa-users me-1"></i>{{ __('ui.search.filters.users') }}
                                    </button>
                                </div>
                            </div>
                            <div class="mobile-search-results-content" id="mobileSearchResultsContent">
                                <!-- Results will be loaded here via AJAX -->
                            </div>
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
    const searchFilterOptions = document.querySelectorAll('.search-filter-option');

    // Variables from old search
    let currentSearchFilter = 'all';
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
            searchResultsContent.innerHTML = '<div class="search-loading p-3 text-center"><i class="fas fa-hourglass-half me-2"></i>{{ __("messages.search.searching") }}</div>';

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

    // Handle search content filter selection
    searchFilterOptions.forEach(option => {
        option.addEventListener('click', function() {
            if (this.classList.contains('disabled')) {
                return;
            }

            // Update active filter
            searchFilterOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');

            // Update current filter
            currentSearchFilter = this.dataset.filter;

            // Filter current results instead of new search
            const query = searchInput.value.trim();
            if (query.length >= 2) {
                filterCurrentResults(currentSearchFilter);
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

    // Store current search results for filtering
    let currentSearchResults = null;

    function performSearch(query) {
        // Make AJAX request to unified search API
        fetch(`/api/v1/search/unified?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                // Add advanced search URL to data
                data.advanced_search_url = generateAdvancedSearchUrl(query, currentSearchFilter);

                // Store results for filtering
                currentSearchResults = data;
                displayUnifiedSearchResults(data);
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResultsContent.innerHTML = '<div class="search-no-results p-3 text-center text-danger">{{ __("ui.search.errors.search_failed") }}</div>';
            });
    }

    // Generate advanced search URL based on current filter and query
    function generateAdvancedSearchUrl(query, filter = 'all') {
        const encodedQuery = encodeURIComponent(query);

        switch(filter) {
            case 'threads':
                return `/threads?search=${encodedQuery}`;
            case 'showcases':
                return `/showcase?search=${encodedQuery}`;
            case 'products':
                return `/marketplace/products?search=${encodedQuery}`;
            case 'users':
                return `/members?filter=${encodedQuery}`;
            case 'all':
            default:
                return `/search?query=${encodedQuery}`;
        }
    }

    function filterCurrentResults(filter) {
        if (!currentSearchResults || !currentSearchResults.results) return;

        // Apply filter to current results
        const query = searchInput.value.trim();
        const filteredData = {
            ...currentSearchResults,
            results: applyContentFilter(currentSearchResults.results, filter),
            advanced_search_url: generateAdvancedSearchUrl(query, filter)
        };

        displayUnifiedSearchResults(filteredData);
    }

    function applyContentFilter(results, filter) {
        if (filter === 'all') {
            return results; // Show all content
        }

        // Create filtered results object
        const filtered = {
            threads: filter === 'threads' ? results.threads : [],
            showcases: filter === 'showcases' ? results.showcases : [],
            products: filter === 'products' ? results.products : [],
            users: filter === 'users' ? results.users : [],
            meta: {
                ...results.meta,
                total: 0,
                categories: {}
            }
        };

        // Update total count
        filtered.meta.total = filtered.threads.length + filtered.showcases.length +
                             filtered.products.length + filtered.users.length;

        // Update categories count
        filtered.meta.categories = {
            threads: filtered.threads.length,
            showcases: filtered.showcases.length,
            products: filtered.products.length,
            users: filtered.users.length
        };

        return filtered;
    }

    function displayUnifiedSearchResults(data) {
        // Clear previous results
        searchResultsContent.innerHTML = '';

        // Check if data structure is valid
        if (!data || !data.results) {
            searchResultsContent.innerHTML = '<div class="search-no-results p-3 text-center text-danger">{{ __("ui.search.errors.search_failed") }}</div>';
            return;
        }

        // Extract results from correct structure
        const results = data.results;

        const totalResults = results.meta.total;

        if (totalResults === 0) {
            searchResultsContent.innerHTML = `
                <div class="search-no-results p-3 text-center">
                    <i class="fas fa-search me-2"></i>{{ t_search("no_results_for") }} "${results.meta.query}".
                    <p class="mt-2 mb-0">
                        <a href="${data.advanced_search_url}" class="btn btn-sm btn-primary" style="background: #8B7355; border-color: #8B7355;">
                            <i class="fas fa-sliders-h me-1"></i>{{ t_search("try_advanced") }}
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
            html += '<h6 class="search-category-title text-primary mb-2"><i class="fas fa-comments me-1"></i>{{ __("ui.search.results.threads") }}</h6>';
            results.threads.forEach(thread => {
                html += `
                    <div class="search-result-item p-2 border-bottom">
                        <div class="d-flex">
                            ${thread.image && thread.image.trim() !== '' ? `<img src="${thread.image}" class="rounded me-2 search-result-image" width="40" height="40" alt="${thread.title}" style="object-fit: cover;" onerror="this.style.display='none'; const fallback = document.createElement('div'); fallback.className = 'bg-light rounded me-2 d-flex align-items-center justify-content-center'; fallback.style.cssText = 'width: 40px; height: 40px;'; fallback.innerHTML = '<i class=\\'fas fa-comments text-muted\\'></i>'; this.parentNode.insertBefore(fallback, this.nextSibling);">` : '<div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-comments text-muted"></i></div>'}
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
            html += '<h6 class="search-category-title text-success mb-2"><i class="fas fa-star me-1"></i>{{ __("messages.search.results.showcase") }}</h6>';
            results.showcases.forEach(showcase => {
                html += `
                    <div class="search-result-item p-2 border-bottom">
                        <div class="d-flex">
                            ${showcase.image && showcase.image.trim() !== '' ? `<img src="${showcase.image}" class="rounded me-2 search-result-image" width="40" height="40" alt="${showcase.title}" style="object-fit: cover;" onerror="this.style.display='none'; const fallback = document.createElement('div'); fallback.className = 'bg-light rounded me-2 d-flex align-items-center justify-content-center'; fallback.style.cssText = 'width: 40px; height: 40px;'; fallback.innerHTML = '<i class=\\'fas fa-star text-muted\\'></i>'; this.parentNode.insertBefore(fallback, this.nextSibling);">` : '<div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-star text-muted"></i></div>'}
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
            html += '<h6 class="search-category-title text-warning mb-2"><i class="fas fa-shopping-cart me-1"></i>{{ __("ui.search.results.products") }}</h6>';
            results.products.forEach(product => {
                html += `
                    <div class="search-result-item p-2 border-bottom">
                        <div class="d-flex">
                            ${product.image && product.image.trim() !== '' ? `<img src="${product.image}" class="rounded me-2 search-result-image" width="40" height="40" alt="${product.title}" style="object-fit: cover;" onerror="this.style.display='none'; const fallback = document.createElement('div'); fallback.className = 'bg-light rounded me-2 d-flex align-items-center justify-content-center'; fallback.style.cssText = 'width: 40px; height: 40px;'; fallback.innerHTML = '<i class=\\'fas fa-shopping-cart text-muted\\'></i>'; this.parentNode.insertBefore(fallback, this.nextSibling);">` : '<div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-shopping-cart text-muted"></i></div>'}
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
            html += '<h6 class="search-category-title text-info mb-2"><i class="fas fa-users me-1"></i>{{ __("ui.search.results.users") }}</h6>';
            results.users.forEach(user => {
                html += `
                    <div class="search-result-item p-2 border-bottom">
                        <div class="d-flex align-items-center">
                            ${user.avatar && user.avatar.trim() !== '' ? `<img src="${user.avatar}" class="rounded-circle me-2 search-result-image" width="32" height="32" alt="${user.name}" onerror="this.style.display='none'; const fallback = document.createElement('div'); fallback.className = 'bg-light rounded-circle me-2 d-flex align-items-center justify-content-center'; fallback.style.cssText = 'width: 32px; height: 32px;'; fallback.innerHTML = '<i class=\\'fas fa-user text-muted\\'></i>'; this.parentNode.insertBefore(fallback, this.nextSibling);">` : '<div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;"><i class="fas fa-user text-muted"></i></div>'}
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
                        <i class="fas fa-search-plus me-1"></i>{{ __('messages.search.advanced_search') }}
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
                    <i class="fas fa-search me-2"></i>{{ t_search("no_results_for") }} "${searchInput.value}".
                    <p class="mt-2">
                        <a href="${data.advanced_search_url || '{{ route('threads.index') }}'}" class="btn btn-sm btn-primary" style="background: #8B7355; border-color: #8B7355;">
                            <i class="fas fa-sliders-h me-1"></i>{{ t_search("try_advanced") }}
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
                    <div class="search-result-section-title">{{ __("ui.search.results.thread") }}</div>
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
                    <div class="search-result-section-title">{{ __("ui.search.results.posts") }}</div>
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
                    {{ __('messages.search.view_all_results') }}
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

    // Notification functionality removed - using simple link to notifications page

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
                themeText.textContent = '{{ __('messages.header.theme.light_mode') }}';
                document.body.classList.add('dark-mode');
            } else {
                themeIconDark.classList.remove('d-none');
                themeIconLight.classList.add('d-none');
                themeText.textContent = '{{ __('messages.header.theme.dark_mode') }}';
                document.body.classList.remove('dark-mode');
            }
        });
    }

    // Mini Cart functionality - Only if cart elements exist
    window.loadMiniCart = function() {
        const miniCartItems = document.getElementById('miniCartItems');
        if (!miniCartItems) {
            //console.log('Mini cart not available for this user');
            return;
        }

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

        // Check if mini cart elements exist
        if (!miniCartItems) {
            console.log('Mini cart elements not found - user may not have cart permissions');
            return;
        }

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
                    <p class="mb-0 mt-2">{{ __('messages.cart.empty_message') }}</p>
                    <small>{{ __('messages.cart.add_items_message') }}</small>
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
                                        data-item-id="${item.id}" title="{{ __('messages.cart.remove_item') }}">
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
                showToast('error', '{{ __('messages.cart.remove_failed') }}');
            }
        });
    };

    // Load mini cart on page load
    loadMiniCart();

    // Refresh mini cart when dropdown is opened - handled above with show.bs.dropdown event
    // Removed duplicate event listener to avoid conflicts with Bootstrap dropdown

    // Notification functionality removed - using simple link to notifications page

    // Mobile Search Functionality - Sync with Desktop
    const mobileSearchInput = document.getElementById('mobileSearchInput');
    const mobileSearchButton = document.getElementById('mobileSearchButton');
    const mobileSearchResultsDropdown = document.getElementById('mobileSearchResultsDropdown');
    const mobileSearchResultsContent = document.getElementById('mobileSearchResultsContent');
    const mobileSearchFilterOptions = document.querySelectorAll('.mobile-search-filter-option');

    // Optional elements that may not exist in mobile modal
    const mobileQuickCategories = document.getElementById('mobileQuickCategories');
    const mobileRecentSection = document.getElementById('mobileRecentSection');
    const mobilePopularSection = document.getElementById('mobilePopularSection');

    // Mobile search variables
    let mobileCurrentSearchFilter = 'all';
    let mobileSearchTimeout;
    let mobileCurrentSearchResults = null;

    if (mobileSearchInput && mobileSearchButton && mobileSearchResultsDropdown && mobileSearchResultsContent) {
        // Mobile search input events
        mobileSearchInput.addEventListener('focus', function() {
            if (this.value.length >= 2) {
                showMobileSearchResults();
                performMobileSearch(this.value);
            }
        });

        mobileSearchInput.addEventListener('input', function() {
            const query = this.value.trim();

            // Clear previous timeout
            clearTimeout(mobileSearchTimeout);

            if (query.length >= 2) {
                // Show loading state
                showMobileSearchResults();
                mobileSearchResultsContent.innerHTML = '<div class="search-loading p-3 text-center"><i class="fas fa-hourglass-half me-2"></i>{{ __("messages.search.searching") }}</div>';

                // Hide quick categories and suggestions when searching (only if they exist)
                if (mobileQuickCategories) mobileQuickCategories.style.display = 'none';
                if (mobileRecentSection) mobileRecentSection.style.display = 'none';
                if (mobilePopularSection) mobilePopularSection.style.display = 'none';

                // Set a timeout to avoid too many requests
                mobileSearchTimeout = setTimeout(function() {
                    performMobileSearch(query);
                }, 300);
            } else {
                hideMobileSearchResults();
                // Show quick categories and suggestions when not searching (only if they exist)
                if (mobileQuickCategories) mobileQuickCategories.style.display = 'block';
                if (mobileRecentSection) mobileRecentSection.style.display = 'block';
                if (mobilePopularSection) mobilePopularSection.style.display = 'block';
            }
        });

        mobileSearchButton.addEventListener('click', function() {
            const query = mobileSearchInput.value.trim();

            if (query.length >= 2) {
                // Close modal and redirect to search page
                const modal = bootstrap.Modal.getInstance(document.getElementById('mobileSearchModal'));
                if (modal) modal.hide();
                window.location.href = `/search?query=${encodeURIComponent(query)}&type=all`;
            }
        });

        // Handle Enter key in mobile search input
        mobileSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = this.value.trim();

                if (query.length >= 2) {
                    // Close modal and redirect to search page
                    const modal = bootstrap.Modal.getInstance(document.getElementById('mobileSearchModal'));
                    if (modal) modal.hide();
                    window.location.href = `/search?query=${encodeURIComponent(query)}&type=all`;
                }
            }
        });

        // Handle mobile search content filter selection
        mobileSearchFilterOptions.forEach(option => {
            option.addEventListener('click', function() {
                if (this.classList.contains('disabled')) {
                    return;
                }

                // Update active filter
                mobileSearchFilterOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');

                // Update current filter
                mobileCurrentSearchFilter = this.dataset.filter;

                // Filter current results instead of new search
                const query = mobileSearchInput.value.trim();
                if (query.length >= 2) {
                    filterMobileCurrentResults(mobileCurrentSearchFilter);
                }
            });
        });

        // Handle popular terms click
        const mobilePopularTerms = document.querySelectorAll('.mobile-popular-term');
        mobilePopularTerms.forEach(term => {
            term.addEventListener('click', function() {
                const searchTerm = this.dataset.term;
                mobileSearchInput.value = searchTerm;
                performMobileSearch(searchTerm);
                showMobileSearchResults();

                // Hide quick categories and suggestions (only if they exist)
                if (mobileQuickCategories) mobileQuickCategories.style.display = 'none';
                if (mobileRecentSection) mobileRecentSection.style.display = 'none';
                if (mobilePopularSection) mobilePopularSection.style.display = 'none';
            });
        });

        // Handle quick category buttons
        const mobileSearchScopeButtons = document.querySelectorAll('.mobile-search-scope');
        mobileSearchScopeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const scope = this.dataset.scope;
                let searchQuery = '';

                // Set appropriate search query based on scope
                switch(scope) {
                    case 'marketplace':
                        searchQuery = '{{ __("ui.search.filters.products") }}';
                        break;
                    case 'forum':
                        searchQuery = '{{ __("ui.search.filters.threads") }}';
                        break;
                    case 'members':
                        searchQuery = '@';
                        break;
                    case 'technical':
                        searchQuery = '{{ __("content.technical.title") }}';
                        break;
                }

                mobileSearchInput.value = searchQuery;
                performMobileSearch(searchQuery);
                showMobileSearchResults();

                // Hide quick categories and suggestions (only if they exist)
                if (mobileQuickCategories) mobileQuickCategories.style.display = 'none';
                if (mobileRecentSection) mobileRecentSection.style.display = 'none';
                if (mobilePopularSection) mobilePopularSection.style.display = 'none';
            });
        });

        // Reset mobile search when modal is hidden
        document.getElementById('mobileSearchModal').addEventListener('hidden.bs.modal', function() {
            mobileSearchInput.value = '';
            hideMobileSearchResults();

            // Show quick categories and suggestions when modal is hidden (only if they exist)
            if (mobileQuickCategories) mobileQuickCategories.style.display = 'block';
            if (mobileRecentSection) mobileRecentSection.style.display = 'block';
            if (mobilePopularSection) mobilePopularSection.style.display = 'block';

            // Reset filter to 'all'
            mobileSearchFilterOptions.forEach(opt => opt.classList.remove('active'));
            const allFilterOption = document.querySelector('.mobile-search-filter-option[data-filter="all"]');
            if (allFilterOption) allFilterOption.classList.add('active');
            mobileCurrentSearchFilter = 'all';
        });
    }

    // Mobile search functions
    function showMobileSearchResults() {
        if (mobileSearchResultsDropdown) {
            mobileSearchResultsDropdown.style.display = 'block';
            mobileSearchResultsDropdown.classList.add('show');
        }
    }

    function hideMobileSearchResults() {
        if (mobileSearchResultsDropdown) {
            mobileSearchResultsDropdown.style.display = 'none';
            mobileSearchResultsDropdown.classList.remove('show');
        }
    }

    function performMobileSearch(query) {
        // Make AJAX request to unified search API (same as desktop)
        fetch(`/api/v1/search/unified?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                // Add advanced search URL to data
                data.advanced_search_url = generateAdvancedSearchUrl(query, mobileCurrentSearchFilter);

                // Store results for filtering
                mobileCurrentSearchResults = data;
                displayMobileUnifiedSearchResults(data);
            })
            .catch(error => {
                console.error('Mobile search error:', error);
                mobileSearchResultsContent.innerHTML = '<div class="search-no-results p-3 text-center text-danger">{{ __("ui.search.errors.search_failed") }}</div>';
            });
    }

    function filterMobileCurrentResults(filter) {
        if (!mobileCurrentSearchResults) return;

        // Apply filter to current results (same logic as desktop)
        const query = mobileSearchInput.value.trim();
        const filteredData = {
            ...mobileCurrentSearchResults,
            results: {
                ...mobileCurrentSearchResults.results,
                threads: filter === 'all' || filter === 'threads' ? mobileCurrentSearchResults.results.threads : [],
                showcases: filter === 'all' || filter === 'showcases' ? mobileCurrentSearchResults.results.showcases : [],
                products: filter === 'all' || filter === 'products' ? mobileCurrentSearchResults.results.products : [],
                users: filter === 'all' || filter === 'users' ? mobileCurrentSearchResults.results.users : []
            },
            advanced_search_url: generateAdvancedSearchUrl(query, filter)
        };

        displayMobileUnifiedSearchResults(filteredData);
    }

    function displayMobileUnifiedSearchResults(data) {
        // Clear previous results
        mobileSearchResultsContent.innerHTML = '';

        // Check if data structure is valid
        if (!data || !data.results) {
            mobileSearchResultsContent.innerHTML = '<div class="search-no-results p-3 text-center text-danger">{{ __("ui.search.errors.search_failed") }}</div>';
            return;
        }

        // Extract results from correct structure
        const results = data.results;
        const totalResults = (results.threads ? results.threads.length : 0) +
                           (results.showcases ? results.showcases.length : 0) +
                           (results.products ? results.products.length : 0) +
                           (results.users ? results.users.length : 0);

        if (totalResults === 0) {
            const query = mobileSearchInput.value.trim();
            const advancedUrl = data.advanced_search_url || generateAdvancedSearchUrl(query, mobileCurrentSearchFilter);
            mobileSearchResultsContent.innerHTML = `
                <div class="search-no-results p-3 text-center">
                    <i class="fas fa-search me-2"></i>Không tìm thấy kết quả cho "${query}".
                    <p class="mt-2 mb-0">
                        <a href="${advancedUrl}" class="btn btn-sm btn-primary" style="background: #8B7355; border-color: #8B7355;">
                            <i class="fas fa-sliders-h me-1"></i>{{ __('messages.search.advanced_search') }}
                        </a>
                    </p>
                </div>
            `;
            return;
        }

        // Use the same display logic as desktop but with mobile-optimized styling
        let html = '';

        // Display Threads
        if (results.threads && results.threads.length > 0) {
            html += '<div class="search-category mb-2">';
            html += '<h6 class="search-category-title text-primary mb-2 small"><i class="fas fa-comments me-1"></i>{{ __("ui.search.results.threads") }}</h6>';
            results.threads.forEach(thread => {
                html += `
                    <div class="search-result-item p-2 border-bottom">
                        <div class="d-flex">
                            ${thread.image && thread.image.trim() !== '' ? `<img src="${thread.image}" class="rounded me-2" width="30" height="30" alt="${thread.title}" style="object-fit: cover;" onerror="this.style.display='none'; const fallback = document.createElement('div'); fallback.className = 'bg-light rounded me-2 d-flex align-items-center justify-content-center'; fallback.style.cssText = 'width: 30px; height: 30px;'; fallback.innerHTML = '<i class=\\'fas fa-comments text-muted\\'></i>'; this.parentNode.insertBefore(fallback, this.nextSibling);">` : '<div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;"><i class="fas fa-comments text-muted"></i></div>'}
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small"><a href="${thread.url}" class="text-decoration-none">${thread.title}</a></h6>
                                <p class="mb-1 text-muted" style="font-size: 0.7rem;">${thread.excerpt}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted" style="font-size: 0.65rem;">
                                        <i class="fas fa-user me-1"></i>${thread.author.name}
                                    </small>
                                    <small class="text-muted" style="font-size: 0.65rem;">
                                        <i class="fas fa-comments me-1"></i>${thread.stats.comments}
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
            html += '<div class="search-category mb-2">';
            html += '<h6 class="search-category-title text-success mb-2 small"><i class="fas fa-star me-1"></i>{{ __("ui.search.results.showcases") }}</h6>';
            results.showcases.forEach(showcase => {
                html += `
                    <div class="search-result-item p-2 border-bottom">
                        <div class="d-flex">
                            ${showcase.image && showcase.image.trim() !== '' ? `<img src="${showcase.image}" class="rounded me-2" width="30" height="30" alt="${showcase.title}" style="object-fit: cover;" onerror="this.style.display='none'; const fallback = document.createElement('div'); fallback.className = 'bg-light rounded me-2 d-flex align-items-center justify-content-center'; fallback.style.cssText = 'width: 30px; height: 30px;'; fallback.innerHTML = '<i class=\\'fas fa-star text-muted\\'></i>'; this.parentNode.insertBefore(fallback, this.nextSibling);">` : '<div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;"><i class="fas fa-star text-muted"></i></div>'}
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small"><a href="${showcase.url}" class="text-decoration-none">${showcase.title}</a></h6>
                                <p class="mb-1 text-muted" style="font-size: 0.7rem;">${showcase.excerpt}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted" style="font-size: 0.65rem;">
                                        <i class="fas fa-user me-1"></i>${showcase.author.name}
                                    </small>
                                    <small class="text-muted" style="font-size: 0.65rem;">
                                        <i class="fas fa-eye me-1"></i>${showcase.stats.views}
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
            html += '<div class="search-category mb-2">';
            html += '<h6 class="search-category-title text-warning mb-2 small"><i class="fas fa-shopping-cart me-1"></i>{{ __("ui.search.results.products") }}</h6>';
            results.products.forEach(product => {
                html += `
                    <div class="search-result-item p-2 border-bottom">
                        <div class="d-flex">
                            ${product.image && product.image.trim() !== '' ? `<img src="${product.image}" class="rounded me-2" width="30" height="30" alt="${product.title}" style="object-fit: cover;" onerror="this.style.display='none'; const fallback = document.createElement('div'); fallback.className = 'bg-light rounded me-2 d-flex align-items-center justify-content-center'; fallback.style.cssText = 'width: 30px; height: 30px;'; fallback.innerHTML = '<i class=\\'fas fa-shopping-cart text-muted\\'></i>'; this.parentNode.insertBefore(fallback, this.nextSibling);">` : '<div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;"><i class="fas fa-shopping-cart text-muted"></i></div>'}
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small">
                                    <a href="${product.url}" class="text-decoration-none">${product.title}</a>
                                    <span class="badge bg-success ms-1" style="font-size: 0.6rem;">${product.price.formatted}</span>
                                </h6>
                                <p class="mb-1 text-muted" style="font-size: 0.7rem;">${product.excerpt}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted" style="font-size: 0.65rem;">
                                        <i class="fas fa-store me-1"></i>${product.seller.name}
                                    </small>
                                    <small class="text-muted" style="font-size: 0.65rem;">
                                        <i class="fas fa-eye me-1"></i>${product.stats.views}
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
            html += '<div class="search-category mb-2">';
            html += '<h6 class="search-category-title text-info mb-2 small"><i class="fas fa-users me-1"></i>{{ __("ui.search.results.users") }}</h6>';
            results.users.forEach(user => {
                html += `
                    <div class="search-result-item p-2 border-bottom">
                        <div class="d-flex align-items-center">
                            ${user.avatar && user.avatar.trim() !== '' ? `<img src="${user.avatar}" class="rounded-circle me-2" width="24" height="24" alt="${user.name}" onerror="this.style.display='none'; const fallback = document.createElement('div'); fallback.className = 'bg-light rounded-circle me-2 d-flex align-items-center justify-content-center'; fallback.style.cssText = 'width: 24px; height: 24px;'; fallback.innerHTML = '<i class=\\'fas fa-user text-muted\\'></i>'; this.parentNode.insertBefore(fallback, this.nextSibling);">` : '<div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;"><i class="fas fa-user text-muted"></i></div>'}
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small">
                                    <a href="${user.url}" class="text-decoration-none">${user.name}</a>
                                    <small class="text-muted">@${user.username}</small>
                                </h6>
                                <small class="text-muted" style="font-size: 0.65rem;">
                                    <span class="badge bg-secondary" style="font-size: 0.6rem;">${user.role}</span>
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
        const query = mobileSearchInput.value.trim();
        const advancedUrl = data.advanced_search_url || generateAdvancedSearchUrl(query, mobileCurrentSearchFilter);
        html += `
            <div class="search-results-footer p-2 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">${'{{ __("ui.search.results_found") }}'.replace(':count', totalResults)}</small>
                    <a href="${advancedUrl}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-search-plus me-1"></i>{{ __('messages.search.advanced_search') }}
                    </a>
                </div>
            </div>
        `;

        mobileSearchResultsContent.innerHTML = html;
    }
});
</script>

<!-- Mobile Navigation Component -->
@include('components.mobile-nav')

<!-- Mini Cart Enhancements - Only load if user can buy products -->
@auth
    @if(auth()->user()->canBuyAnyProduct())
        <script src="{{ asset_versioned('assets/js/mini-cart-enhancements.js') }}"></script>
    @endif
    {{-- Simple Notification Dropdown Handler --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const bellBtn = document.getElementById('notificationBell');
        const dropdownMenu = document.getElementById('notificationDropdown');

        if (!bellBtn || !dropdownMenu) {
            return;
        }

        // Toggle dropdown
        bellBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const isOpen = dropdownMenu.classList.contains('show');
            if (isOpen) {
                dropdownMenu.classList.remove('show');
                bellBtn.setAttribute('aria-expanded', 'false');
            } else {
                dropdownMenu.classList.add('show');
                bellBtn.setAttribute('aria-expanded', 'true');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!bellBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
                bellBtn.setAttribute('aria-expanded', 'false');
            }
        });

        // Close dropdown on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && dropdownMenu.classList.contains('show')) {
                dropdownMenu.classList.remove('show');
                bellBtn.setAttribute('aria-expanded', 'false');
            }
        });
    });
    </script>
@endauth

{{-- JavaScript to fix duplicate menu issue --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for all content to load
    setTimeout(function() {
        //console.log('🔍 Checking for duplicate "{{ __("nav.create.title") }}" menus...');

        // Find all nav items with "Thêm" text
        const navItems = document.querySelectorAll('.navbar-nav .nav-item');
        let addMenus = [];

        navItems.forEach(function(item, index) {
            const link = item.querySelector('.nav-link');
            if (link && link.textContent.includes('{{ __("nav.create.title") }}')) {
                const hasPlus = link.querySelector('.fa-plus');
                addMenus.push({
                    element: item,
                    link: link,
                    text: link.textContent.trim(),
                    hasPlus: !!hasPlus,
                    index: index
                });
                // Found menu item
            }
        });

        // If we have more than 1 "Thêm" menu, hide duplicates
        if (addMenus.length > 1) {
            // Found duplicate menus, hiding them

            // Keep only the one with + icon, hide others
            addMenus.forEach(function(menu, idx) {
                if (!menu.hasPlus) {
                    menu.element.style.display = 'none';
                    // Hidden duplicate menu
                }
            });
        }
    }, 100);
});
</script>

{{-- Notification styles removed --}}
