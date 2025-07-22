{{--
    MechaMap Business Menu Component
    Menu cho business partners: verified_partner, manufacturer, supplier, brand
    Hiển thị menu với quyền hạn kinh doanh và marketplace
--}}

@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();

    // Main navigation items cho business users
    $mainMenuItems = [
        'home' => [
            'title' => __('nav.home'),
            'route' => 'home',
            'icon' => 'fas fa-home'
        ],
        'forums' => [
            'title' => __('nav.forums'),
            'route' => 'forums.index',
            'icon' => 'fas fa-comments'
        ],
        'showcases' => [
            'title' => __('nav.showcases'),
            'route' => 'showcases.index',
            'icon' => 'fas fa-star'
        ],
        'marketplace' => [
            'title' => __('nav.marketplace'),
            'route' => 'marketplace.index',
            'icon' => 'fas fa-store'
        ]
    ];

    // Business dashboard dropdown items
    $businessDropdownItems = [];

    // Role-specific dashboard routes
    switch($user->role) {
        case 'verified_partner':
            $businessDropdownItems['dashboard'] = [
                'title' => __('nav.business.partner_dashboard'),
                'route' => 'partner.dashboard',
                'icon' => 'fas fa-tachometer-alt'
            ];
            break;
        case 'manufacturer':
            $businessDropdownItems['dashboard'] = [
                'title' => __('nav.business.manufacturer_dashboard'),
                'route' => 'manufacturer.dashboard',
                'icon' => 'fas fa-industry'
            ];
            break;
        case 'supplier':
            $businessDropdownItems['dashboard'] = [
                'title' => __('nav.business.supplier_dashboard'),
                'route' => 'supplier.dashboard',
                'icon' => 'fas fa-truck'
            ];
            break;
        case 'brand':
            $businessDropdownItems['dashboard'] = [
                'title' => __('nav.business.brand_dashboard'),
                'route' => 'brand.dashboard',
                'icon' => 'fas fa-bullhorn'
            ];
            break;
    }

    // Common business menu items
    if ($user->role !== 'brand') {
        $businessDropdownItems['products'] = [
            'title' => __('nav.business.my_products'),
            'route' => $user->role . '.products.index',
            'icon' => 'fas fa-box'
        ];
        $businessDropdownItems['orders'] = [
            'title' => __('nav.business.orders'),
            'route' => $user->role . '.orders.index',
            'icon' => 'fas fa-shopping-cart'
        ];
    }

    $businessDropdownItems['analytics'] = [
        'title' => __('nav.business.analytics'),
        'route' => $user->role . '.analytics.index',
        'icon' => 'fas fa-chart-line'
    ];

    // Brand specific items
    if ($user->role === 'brand') {
        $businessDropdownItems['insights'] = [
            'title' => __('nav.business.market_insights'),
            'route' => 'brand.insights.index',
            'icon' => 'fas fa-lightbulb'
        ];
        $businessDropdownItems['advertising'] = [
            'title' => __('nav.business.advertising'),
            'route' => 'brand.advertising.index',
            'icon' => 'fas fa-ad'
        ];
    }

    // User profile dropdown items
    $profileDropdownItems = [
        'profile' => [
            'title' => __('nav.user.profile'),
            'route' => 'profile.show',
            'icon' => 'fas fa-user',
            'params' => [$user->username ?? '']
        ],
        'business_profile' => [
            'title' => __('nav.business.business_profile'),
            'route' => $user->role . '.profile.edit',
            'icon' => 'fas fa-building'
        ],
        'account_settings' => [
            'title' => __('nav.user.account_settings'),
            'route' => 'profile.edit',
            'icon' => 'fas fa-cog'
        ]
    ];

    // Check marketplace permissions
    $canSell = in_array($user->role, ['verified_partner', 'manufacturer', 'supplier']);
    $canBuy = in_array($user->role, ['verified_partner', 'manufacturer', 'supplier']);
    $isVerified = $user->business_verified ?? false;
@endphp

<!-- Business Navigation -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand logo" href="{{ route('home') }}">
            <img src="{{ get_logo_url() }}" alt="{{ get_site_name() }}" class="me-2">
            <span class="brand-text">{{ get_site_name() }}</span>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#businessNavbar"
                aria-controls="businessNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="businessNavbar">
            <!-- Main Menu Items -->
            <ul class="navbar-nav me-auto">
                @foreach($mainMenuItems as $key => $item)
                    @if(Route::has($item['route']))
                        <li class="nav-item">
                            <a class="nav-link {{ $currentRoute === $item['route'] ? 'active' : '' }}"
                               href="{{ route($item['route']) }}">
                                <i class="{{ $item['icon'] }} me-1"></i>
                                {{ $item['title'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>

            <!-- Right Side Menu -->
            <ul class="navbar-nav ms-auto">
                <!-- Business Dashboard Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle business-dropdown" href="#" id="businessDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-briefcase me-1"></i>
                        <span class="business-badge">{{ $user->role_display_name }}</span>
                    </a>
                    <ul class="dropdown-menu business-menu" aria-labelledby="businessDropdown">
                        @foreach($businessDropdownItems as $key => $item)
                            @if(Route::has($item['route']))
                                <li>
                                    <a class="dropdown-item" href="{{ route($item['route']) }}">
                                        <i class="{{ $item['icon'] }} me-2"></i>
                                        {{ $item['title'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                        <li><hr class="dropdown-divider"></li>
                        @if(Route::has('business.verification.status'))
                        <li>
                            <a class="dropdown-item" href="{{ route('business.verification.status') }}">
                                <i class="fas fa-{{ $isVerified ? 'check-circle text-success' : 'clock text-warning' }} me-2"></i>
                                Trạng thái xác thực
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>

                <!-- Shopping Cart (if can buy) -->
                @if($canBuy && $isVerified && Route::has('marketplace.cart.index'))
                <li class="nav-item">
                    <a class="nav-link position-relative" href="{{ route('marketplace.cart.index') }}">
                        <i class="fas fa-shopping-cart"></i>
                        @if(($cartCount = auth()->user()->cart_items_count ?? 0) > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                            {{ $cartCount }}
                            <span class="visually-hidden">items in cart</span>
                        </span>
                        @endif
                    </a>
                </li>
                @endif

                <!-- Quick Actions (if can sell) -->
                @if($canSell && $isVerified)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="quickActionsDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-plus-circle me-1"></i>
                        <span class="d-none d-md-inline">Tạo mới</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="quickActionsDropdown">
                        @if(Route::has($user->role . '.products.create'))
                        <li>
                            <a class="dropdown-item" href="{{ route($user->role . '.products.create') }}">
                                <i class="fas fa-box me-2"></i>
                                Thêm sản phẩm
                            </a>
                        </li>
                        @endif
                        @if(Route::has('threads.create'))
                        <li>
                            <a class="dropdown-item" href="{{ route('threads.create') }}">
                                <i class="fas fa-comment me-2"></i>
                                Tạo bài viết
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        @if($user->unread_notifications_count > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $user->unread_notifications_count }}
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end notification-menu" aria-labelledby="notificationDropdown">
                        <li><h6 class="dropdown-header">Thông báo kinh doanh</h6></li>
                        @forelse($user->business_notifications()->limit(5)->get() as $notification)
                        <li>
                            <a class="dropdown-item" href="{{ $notification->action_url ?? '#' }}">
                                <i class="fas fa-{{ $notification->icon ?? 'bell' }} me-2"></i>
                                {{ Str::limit($notification->message, 50) }}
                                <small class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                            </a>
                        </li>
                        @empty
                        <li><span class="dropdown-item-text text-muted">Không có thông báo mới</span></li>
                        @endforelse
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="{{ route('business.notifications') }}">Xem tất cả</a></li>
                    </ul>
                </li>

                <!-- User Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle user-dropdown" href="#" id="userDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ $user->avatar_url ?? '/images/default-avatar.png' }}"
                             alt="{{ $user->name }}" class="rounded-circle me-1" width="24" height="24">
                        <span class="user-name">{{ $user->name }}</span>
                        @if($isVerified)
                            <i class="fas fa-check-circle text-success ms-1" title="Đã xác thực"></i>
                        @else
                            <i class="fas fa-clock text-warning ms-1" title="Chờ xác thực"></i>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end user-menu" aria-labelledby="userDropdown">
                        @foreach($profileDropdownItems as $key => $item)
                            @if(Route::has($item['route']))
                                <li>
                                    <a class="dropdown-item" href="{{ route($item['route'], $item['params'] ?? []) }}">
                                        <i class="{{ $item['icon'] }} me-2"></i>
                                        {{ $item['title'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    {{ t_auth('logout.title') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Business Status Bar -->
<div class="business-status-bar bg-{{ $isVerified ? 'success' : 'warning' }} text-{{ $isVerified ? 'white' : 'dark' }} py-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <small>
                    <i class="fas fa-{{ $isVerified ? 'check-circle' : 'clock' }} me-1"></i>
                    <strong>Tài khoản kinh doanh:</strong> {{ $user->role_display_name }}
                    @if($isVerified)
                        - Đã xác thực
                    @else
                        - Chờ xác thực
                    @endif
                    @if($canSell && $isVerified)
                        <span class="ms-3">
                            <i class="fas fa-percentage me-1"></i>
                            Hoa hồng: {{ config('mechamap_permissions.marketplace_features.' . $user->role . '.commission_rate', 0) }}%
                        </span>
                    @endif
                </small>
            </div>
            <div class="col-md-4 text-end">
                @if(!$isVerified && Route::has('business.verification.apply'))
                <a href="{{ route('business.verification.apply') }}" class="btn btn-sm btn-dark">
                    <i class="fas fa-certificate me-1"></i>
                    Xác thực ngay
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Business Menu Specific Styles */
.business-status-bar {
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.business-dropdown .business-badge {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.business-menu {
    min-width: 250px;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.user-dropdown .user-name {
    font-weight: 500;
}

.notification-menu {
    min-width: 320px;
    max-height: 400px;
    overflow-y: auto;
}

.nav-link.active {
    color: var(--bs-primary) !important;
    font-weight: 500;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .business-status-bar .col-md-4 {
        text-align: center !important;
        margin-top: 5px;
    }

    .user-dropdown .user-name {
        display: none;
    }

    .business-dropdown .business-badge {
        font-size: 0.7rem;
        padding: 1px 6px;
    }
}
</style>
