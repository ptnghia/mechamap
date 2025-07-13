{{--
    MechaMap Member Menu Component
    Menu cho community members: senior_member, member, guest (đã đăng nhập)
    Hiển thị menu với quyền hạn cơ bản của thành viên cộng đồng
--}}

@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();
    
    // Main navigation items cho members
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
        ],
        'docs' => [
            'title' => __('nav.docs'),
            'route' => 'docs.index',
            'icon' => 'fas fa-book'
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
        'dashboard' => [
            'title' => __('nav.user.dashboard'),
            'route' => 'user.dashboard',
            'icon' => 'fas fa-tachometer-alt'
        ],
        'my_threads' => [
            'title' => __('nav.user.my_threads'),
            'route' => 'user.my-threads',
            'icon' => 'fas fa-comments',
            'badge' => $user->threads()->count()
        ],
        'bookmarks' => [
            'title' => __('nav.user.bookmarks'),
            'route' => 'user.bookmarks',
            'icon' => 'fas fa-bookmark',
            'badge' => $user->bookmarks()->count()
        ],
        'following' => [
            'title' => __('nav.user.following'),
            'route' => 'user.following',
            'icon' => 'fas fa-heart',
            'badge' => $user->following()->count()
        ],
        'account_settings' => [
            'title' => __('nav.user.account_settings'),
            'route' => 'profile.edit',
            'icon' => 'fas fa-cog'
        ]
    ];
    
    // Conditional items based on role
    if ($user->role !== 'guest') {
        $profileDropdownItems['ratings'] = [
            'title' => __('nav.user.ratings'),
            'route' => 'user.ratings',
            'icon' => 'fas fa-star-half-alt'
        ];
    }
    
    // Check if user can create content
    $canCreateContent = $user->role !== 'guest';
@endphp

<!-- Member Navigation -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand logo" href="{{ route('home') }}">
            <img src="{{ get_logo_url() }}" alt="{{ get_site_name() }}" class="me-2">
            <span class="brand-text">{{ get_site_name() }}</span>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#memberNavbar" 
                aria-controls="memberNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="memberNavbar">
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
                <!-- Quick Create Dropdown (if can create content) -->
                @if($canCreateContent)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle create-dropdown" href="#" id="createDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-plus-circle me-1"></i>
                        <span class="create-text">Tạo mới</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="createDropdown">
                        @if(Route::has('threads.create'))
                        <li>
                            <a class="dropdown-item" href="{{ route('threads.create') }}">
                                <i class="fas fa-comment me-2"></i>
                                Tạo bài viết
                            </a>
                        </li>
                        @endif
                        @if(Route::has('showcases.create'))
                        <li>
                            <a class="dropdown-item" href="{{ route('showcases.create') }}">
                                <i class="fas fa-star me-2"></i>
                                Tạo showcase
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Search -->
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="fas fa-search"></i>
                    </a>
                </li>

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
                        <li><h6 class="dropdown-header">Thông báo</h6></li>
                        @forelse($user->notifications()->limit(5)->get() as $notification)
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
                        <li><a class="dropdown-item text-center" href="{{ route('user.notifications') }}">Xem tất cả</a></li>
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

<!-- Member Status Info (for Guest role) -->
@if($user->role === 'guest')
<div class="member-status-bar bg-info text-white py-1">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <small>
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Tài khoản Guest:</strong> Một số tính năng bị hạn chế. 
                    <a href="{{ route('register.upgrade') }}" class="text-white text-decoration-underline">
                        Nâng cấp tài khoản
                    </a>
                </small>
            </div>
            <div class="col-md-4 text-end">
                <small>
                    <i class="fas fa-eye me-1"></i>
                    Chế độ xem: {{ $user->role_display_name }}
                </small>
            </div>
        </div>
    </div>
</div>
@endif

<style>
/* Member Menu Specific Styles */
.member-status-bar {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.create-dropdown .create-text {
    font-weight: 500;
    color: var(--bs-success);
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

.nav-link:hover {
    color: var(--bs-primary);
    transition: color 0.3s ease;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .member-status-bar .col-md-4 {
        text-align: center !important;
        margin-top: 5px;
    }
    
    .user-dropdown .user-name {
        display: none;
    }
    
    .create-dropdown .create-text {
        display: none;
    }
    
    .navbar-nav .nav-item {
        text-align: center;
    }
}
</style>
