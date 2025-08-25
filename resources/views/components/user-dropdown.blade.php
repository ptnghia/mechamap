{{--
    MechaMap User Avatar Dropdown Component
    Dropdown menu hiện đại cho user avatar với dashboard routes
--}}

@php
    $user = auth()->user();

    // Dashboard menu items theo nhóm chức năng
    $dashboardMenuItems = [
        'main' => [
            'title' => __('ui.dashboard.main'),
            'route' => 'dashboard',
            'icon' => 'fa-solid fa-gauge-high',
            'description' => __('ui.dashboard.main_desc')
        ],
        'profile' => [
            'title' => __('ui.dashboard.profile'),
            'route' => 'dashboard.profile.edit',
            'icon' => 'fa-solid fa-user',
            'description' => __('ui.dashboard.profile_desc')
        ],
        'activity' => [
            'title' => __('ui.dashboard.activity'),
            'route' => 'dashboard.activity',
            'icon' => 'fa-solid fa-clock-rotate-left',
            'description' => __('ui.dashboard.activity_desc')
        ]
    ];

    // Community features
    $communityMenuItems = [
        'threads' => [
            'title' => __('ui.dashboard.my_threads'),
            'route' => 'dashboard.community.threads.index',
            'icon' => 'fa-solid fa-comments',
            'badge' => $user->threads()->count()
        ],
        'bookmarks' => [
            'title' => __('ui.dashboard.bookmarks'),
            'route' => 'dashboard.community.bookmarks.index',
            'icon' => 'fa-solid fa-bookmark',
            'badge' => $user->bookmarks()->count()
        ],
        'comments' => [
            'title' => __('ui.dashboard.comments'),
            'route' => 'dashboard.community.comments.index',
            'icon' => 'fa-solid fa-comment',
            'badge' => $user->comments()->count()
        ]
    ];

    // Communication features
    $communicationMenuItems = [
        'messages' => [
            'title' => __('ui.dashboard.messages'),
            'route' => 'dashboard.messages.index',
            'icon' => 'fa-solid fa-envelope',
            'badge' => $user->unreadMessagesCount()
        ],
        'notifications' => [
            'title' => __('ui.dashboard.notifications'),
            'route' => 'dashboard.notifications.index',
            'icon' => 'fa-solid fa-bell',
            'badge' => $user->unreadNotificationsCount()
        ]
    ];

    // Settings
    $settingsMenuItems = [
        'settings' => [
            'title' => __('ui.dashboard.settings'),
            'route' => 'dashboard.settings.index',
            'icon' => 'fa-solid fa-gear',
            'description' => __('ui.dashboard.settings_desc')
        ]
    ];

    // Marketplace items (chỉ cho user có quyền mua/bán)
    $marketplaceMenuItems = [];

    // Chỉ hiển thị "Đơn hàng" và "Danh sách yêu thích" cho user có thể mua
    if ($user->canBuyAnyProduct()) {
        $marketplaceMenuItems['orders'] = [
            'title' => __('ui.dashboard.orders'),
            'route' => 'dashboard.marketplace.orders.index',
            'icon' => 'fa-solid fa-shopping-bag',
            'badge' => $user->pendingOrdersCount()
        ];
        $marketplaceMenuItems['wishlist'] = [
            'title' => __('ui.dashboard.wishlist'),
            'route' => 'dashboard.marketplace.wishlist.index',
            'icon' => 'fa-solid fa-heart',
            'badge' => $user->wishlistItemsCount()
        ];
    }

    // Seller features (nếu có quyền bán)
    if ($user->canSellAnyProduct()) {
        $marketplaceMenuItems['seller'] = [
            'title' => __('ui.dashboard.seller'),
            'route' => 'dashboard.marketplace.seller.dashboard',
            'icon' => 'fa-solid fa-store',
            'description' => __('ui.dashboard.seller_desc')
        ];
    }

    // Role-based admin access
    $adminMenuItems = [];
    if ($user->hasAnyRole(['super_admin', 'system_admin', 'content_admin', 'content_moderator', 'marketplace_moderator', 'community_moderator'])) {
        $adminMenuItems['admin'] = [
            'title' => __('ui.dashboard.admin'),
            'route' => 'admin.dashboard',
            'icon' => 'fa-solid fa-shield-halved',
            'description' => __('ui.dashboard.admin_desc')
        ];
    }
@endphp

<!-- User Avatar Dropdown -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle user-dropdown" href="#" id="userDropdown" role="button"
       data-bs-toggle="dropdown" aria-expanded="false">
        <img src="{{ $user->getAvatarUrl() }}"
             alt="{{ $user->name }}" class="rounded-circle me-1" width="24" height="24"
             onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($user->name, 0, 1))]) }}'">
        <span class="user-name d-none d-md-inline">{{ $user->name }}</span>
        <span class="badge bg-{{ $user->role_color ?? 'primary' }} ms-1 d-none d-lg-inline">{{ $user->role_display_name }}</span>
    </a>

    <ul class="dropdown-menu dropdown-menu-end user-menu shadow-lg" aria-labelledby="userDropdown">
        <!-- User Info Header -->
        <li class="dropdown-header user-info">
            <div class="d-flex align-items-center">
                <img src="{{ $user->getAvatarUrl() }}"
                     alt="{{ $user->name }}" class="rounded-circle me-3" width="40" height="40"
                     onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($user->name, 0, 1))]) }}'">
                <div>
                    <h6 class="mb-0">{{ $user->name }}</h6>
                    <small class="text-muted">{{ $user->role_display_name }}</small>
                </div>
            </div>
        </li>
        <li><hr class="dropdown-divider"></li>

        <!-- Main Dashboard Items -->
        @foreach($dashboardMenuItems as $key => $item)
            @if(Route::has($item['route']))
                <li>
                    <a class="dropdown-item" href="{{ route($item['route'], $item['params'] ?? []) }}">
                        <i class="{{ $item['icon'] }} me-2"></i>
                        {{ $item['title'] }}
                        @if(isset($item['badge']) && $item['badge'] > 0)
                            <span class="badge bg-primary ms-auto">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                </li>
            @endif
        @endforeach

        <!-- Community Section -->
        @if(!empty($communityMenuItems))
            <li><hr class="dropdown-divider"></li>
            <li class="dropdown-header">
                <small class="text-muted">{{ __('ui.dashboard.community_section') }}</small>
            </li>
            @foreach($communityMenuItems as $key => $item)
                @if(Route::has($item['route']))
                    <li>
                        <a class="dropdown-item" href="{{ route($item['route'], $item['params'] ?? []) }}">
                            <i class="{{ $item['icon'] }} me-2"></i>
                            {{ $item['title'] }}
                            @if(isset($item['badge']) && $item['badge'] > 0)
                                <span class="badge bg-secondary ms-auto">{{ $item['badge'] }}</span>
                            @endif
                        </a>
                    </li>
                @endif
            @endforeach
        @endif

        <!-- Communication Section -->
        @if(!empty($communicationMenuItems))
            <li><hr class="dropdown-divider"></li>
            <li class="dropdown-header">
                <small class="text-muted">{{ __('ui.dashboard.communication_section') }}</small>
            </li>
            @foreach($communicationMenuItems as $key => $item)
                @if(Route::has($item['route']))
                    <li>
                        <a class="dropdown-item" href="{{ route($item['route'], $item['params'] ?? []) }}">
                            <i class="{{ $item['icon'] }} me-2"></i>
                            {{ $item['title'] }}
                            @if(isset($item['badge']) && $item['badge'] > 0)
                                <span class="badge bg-info ms-auto">{{ $item['badge'] }}</span>
                            @endif
                        </a>
                    </li>
                @endif
            @endforeach
        @endif

        <!-- Marketplace Section -->
        @if(!empty($marketplaceMenuItems))
            <li><hr class="dropdown-divider"></li>
            <li class="dropdown-header">
                <small class="text-muted">{{ __('ui.dashboard.marketplace_section') }}</small>
            </li>
            @foreach($marketplaceMenuItems as $key => $item)
                @if(Route::has($item['route']))
                    <li>
                        <a class="dropdown-item" href="{{ route($item['route'], $item['params'] ?? []) }}">
                            <i class="{{ $item['icon'] }} me-2"></i>
                            {{ $item['title'] }}
                            @if(isset($item['badge']) && $item['badge'] > 0)
                                <span class="badge bg-success ms-auto">{{ $item['badge'] }}</span>
                            @endif
                        </a>
                    </li>
                @endif
            @endforeach
        @endif

        <!-- Admin Section -->
        @if(!empty($adminMenuItems))
            <li><hr class="dropdown-divider"></li>
            <li class="dropdown-header">
                <small class="text-muted">{{ __('ui.dashboard.admin_section') }}</small>
            </li>
            @foreach($adminMenuItems as $key => $item)
                @if(Route::has($item['route']))
                    <li>
                        <a class="dropdown-item" href="{{ route($item['route'], $item['params'] ?? []) }}">
                            <i class="{{ $item['icon'] }} me-2 text-warning"></i>
                            {{ $item['title'] }}
                        </a>
                    </li>
                @endif
            @endforeach
        @endif

        <!-- Settings & Logout -->
        <li><hr class="dropdown-divider"></li>
        @foreach($settingsMenuItems as $key => $item)
            @if(Route::has($item['route']))
                <li>
                    <a class="dropdown-item" href="{{ route($item['route'], $item['params'] ?? []) }}">
                        <i class="{{ $item['icon'] }} me-2"></i>
                        {{ $item['title'] }}
                    </a>
                </li>
            @endif
        @endforeach

        <li>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="dropdown-item text-danger">
                    <i class="fa-solid fa-right-from-bracket me-2"></i>
                    {{ __('Đăng xuất') }}
                </button>
            </form>
        </li>
    </ul>
</li>
