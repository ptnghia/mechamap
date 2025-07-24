{{--
    MechaMap Admin Menu Component
    Menu cho admin roles: super_admin, system_admin, content_admin, moderators
    Hiển thị menu với quyền hạn phù hợp theo role
--}}

@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();
    
    // Main navigation items cho admin
    $mainMenuItems = [
        'home' => [
            'title' => __('nav.home'),
            'route' => 'home',
            'icon' => 'fas fa-home',
            'permission' => null // Public
        ],
        'forums' => [
            'title' => __('nav.forums'),
            'route' => 'forums.index',
            'icon' => 'fas fa-comments',
            'permission' => 'view-content'
        ],
        'showcases' => [
            'title' => __('nav.showcases'),
            'route' => 'showcases.index',
            'icon' => 'fas fa-star',
            'permission' => 'view-content'
        ],
        'marketplace' => [
            'title' => __('nav.marketplace'),
            'route' => 'marketplace.index',
            'icon' => 'fas fa-store',
            'permission' => 'view-content'
        ]
    ];
    
    // Admin dropdown items
    $adminDropdownItems = [
        'dashboard' => [
            'title' => __('nav.admin.dashboard'),
            'route' => 'admin.dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'permission' => 'access-admin-panel'
        ],
        'users' => [
            'title' => __('nav.admin.users'),
            'route' => 'admin.users.index',
            'icon' => 'fas fa-users',
            'permission' => 'view-users'
        ],
        'content' => [
            'title' => __('nav.admin.content'),
            'route' => 'admin.content.index',
            'icon' => 'fas fa-file-alt',
            'permission' => 'manage-content'
        ],
        'marketplace_admin' => [
            'title' => __('nav.admin.marketplace'),
            'route' => 'admin.marketplace.index',
            'icon' => 'fas fa-store-alt',
            'permission' => 'manage-marketplace'
        ],
        'settings' => [
            'title' => __('nav.admin.settings'),
            'route' => 'admin.settings.index',
            'icon' => 'fas fa-cogs',
            'permission' => 'manage-system'
        ]
    ];
    
    // User profile dropdown items
    $profileDropdownItems = [
        'profile' => [
            'title' => __('nav.user.profile'),
            'route' => 'profile.show',
            'icon' => 'fas fa-user',
            'params' => [$user->username ?? '']
        ],
        'account_settings' => [
            'title' => __('nav.user.account_settings'),
            'route' => 'profile.edit',
            'icon' => 'fas fa-cog'
        ],
        'admin_profile' => [
            'title' => __('nav.admin.profile'),
            'route' => 'admin.profile.index',
            'icon' => 'fas fa-user-shield',
            'permission' => 'access-admin-panel'
        ]
    ];
@endphp

<!-- Admin Navigation -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand logo" href="{{ route('home') }}">
            <img src="{{ get_logo_url() }}" alt="{{ get_site_name() }}" class="me-2">
            <span class="brand-text">{{ get_site_name() }}</span>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" 
                aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="adminNavbar">
            <!-- Main Menu Items -->
            <ul class="navbar-nav me-auto">
                @foreach($mainMenuItems as $key => $item)
                    @if(Route::has($item['route']) && (!$item['permission'] || $user->hasPermission($item['permission'])))
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
                <!-- Admin Quick Access Dropdown -->
                @if($user->canAccessAdmin())
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle admin-dropdown" href="#" id="adminDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-shield-alt me-1"></i>
                        <span class="admin-badge">Quản trị</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end admin-menu" aria-labelledby="adminDropdown">
                        @foreach($adminDropdownItems as $key => $item)
                            @if(Route::has($item['route']) && $user->hasPermission($item['permission']))
                                <li>
                                    <a class="dropdown-item" href="{{ route($item['route']) }}">
                                        <i class="{{ $item['icon'] }} me-2"></i>
                                        {{ $item['title'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
                @endif

                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end notification-menu" aria-labelledby="notificationDropdown">
                        <li><h6 class="dropdown-header">Thông báo</h6></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="fas fa-user-plus me-2"></i>
                            Có 5 user mới đăng ký
                        </a></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="fas fa-flag me-2"></i>
                            2 báo cáo cần xử lý
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Xem tất cả</a></li>
                    </ul>
                </li>

                <!-- User Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle user-dropdown" href="#" id="userDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ $user->avatar_url ?? '/images/default-avatar.png' }}" 
                             alt="{{ $user->name }}" class="rounded-circle me-1" width="24" height="24">
                        <span class="user-name">{{ $user->name }}</span>
                        <span class="badge bg-{{ $user->role_color ?? 'primary' }} ms-1">{{ $user->role_display_name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end user-menu" aria-labelledby="userDropdown">
                        @foreach($profileDropdownItems as $key => $item)
                            @if(Route::has($item['route']) && (!isset($item['permission']) || $user->hasPermission($item['permission'])))
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
                                    {{ __('auth.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Admin Status Bar -->
<div class="admin-status-bar bg-warning text-dark py-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <small>
                    <i class="fas fa-shield-alt me-1"></i>
                    <strong>Chế độ quản trị:</strong> {{ $user->role_display_name }}
                    <span class="ms-3">
                        <i class="fas fa-clock me-1"></i>
                        Đăng nhập lúc: {{ $user->last_login_at?->format('H:i d/m/Y') }}
                    </span>
                </small>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-dark">
                    <i class="fas fa-tachometer-alt me-1"></i>
                    Admin Panel
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Admin Menu Specific Styles */
.admin-status-bar {
    background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%);
    border-bottom: 1px solid #e0a800;
}

.admin-dropdown .admin-badge {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.admin-menu {
    min-width: 250px;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.user-dropdown .user-name {
    font-weight: 500;
}

.notification-menu {
    min-width: 300px;
    max-height: 400px;
    overflow-y: auto;
}

.nav-link.active {
    color: var(--bs-primary) !important;
    font-weight: 500;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .admin-status-bar .col-md-4 {
        text-align: center !important;
        margin-top: 5px;
    }
    
    .user-dropdown .user-name {
        display: none;
    }
    
    .admin-dropdown .admin-badge {
        font-size: 0.7rem;
        padding: 1px 6px;
    }
}
</style>
