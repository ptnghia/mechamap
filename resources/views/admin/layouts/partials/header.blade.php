{{--
    MechaMap Unified Admin Header
    Enhanced header with proper user authentication and responsive design
--}}

@php
    $user = auth()->user();
    $avatar = $user ? $user->getAvatarUrl() : route('avatar.generate', ['initial' => 'A']);
    $userName = $user ? $user->name : 'Admin';
    $userEmail = $user ? $user->email : '';
    $userRole = $user ? (is_string($user->role) ? $user->role : ($user->role ? $user->role->name : 'Administrator')) : 'Administrator';
@endphp

<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('images/brand/logo.png') }}" alt="MechaMap" height="30">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('images/brand/logo.png') }}" alt="MechaMap" height="24">
                        <span class="logo-txt">MechaMap Admin</span>
                    </span>
                </a>

                <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('images/brand/logo.png') }}" alt="MechaMap" height="30">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('images/brand/logo.png') }}" alt="MechaMap" height="24">
                        <span class="logo-txt">MechaMap Admin</span>
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Global Search -->
            <form class="app-search d-none d-lg-block" action="{{ route('admin.search.global') }}" method="GET">
                <div class="position-relative">
                    <input type="text" class="form-control" name="q" placeholder="Tìm kiếm người dùng, bài đăng, sản phẩm..." value="{{ request('q') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="d-flex">

            <!-- Mobile Search -->
            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item" id="page-header-search-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-search"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-search-dropdown">
                    <form class="p-3" action="{{ route('admin.search.global') }}" method="GET">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" name="q" placeholder="Tìm kiếm..." aria-label="Search Result">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Language Switcher -->
            <div class="dropdown d-none d-sm-inline-block">
                <button type="button" class="btn header-item" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-globe"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="{{ route('admin.language.switch', 'vi') }}">
                        <img src="{{ asset('assets/images/flags/vietnam.jpg') }}" alt="Vietnam" height="16" class="me-2">
                        Tiếng Việt
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.language.switch', 'en') }}">
                        <img src="{{ asset('assets/images/flags/us.jpg') }}" alt="English" height="16" class="me-2">
                        English
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item quick-actions-btn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-plus-circle"></i>
                    <span class="d-none d-xl-inline-block ms-1">Thêm mới</span>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <div class="dropdown-header">
                        <h6 class="m-0">🚀 Tạo Nội Dung Mới</h6>
                        <small class="text-muted">Chọn loại nội dung bạn muốn tạo</small>
                    </div>
                    <div class="p-2">
                        <!-- Community Actions -->
                        <div class="dropdown-section-header">
                            <small class="text-muted fw-bold">👥 CỘNG ĐỒNG</small>
                        </div>
                        <div class="row g-0 mb-2">
                            @adminCan('view_users')
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.users.create') }}">
                                    <i class="fas fa-user-plus text-primary"></i>
                                    <span>Thêm User</span>
                                </a>
                            </div>
                            @endadminCan
                            @adminCan('manage-categories')
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.categories.create') }}">
                                    <i class="fas fa-folder-plus text-success"></i>
                                    <span>Tạo Danh Mục</span>
                                </a>
                            </div>
                            @endadminCan
                            @adminCan('manage-forums')
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.forums.create') }}">
                                    <i class="fas fa-comments text-info"></i>
                                    <span>Tạo Diễn Đàn</span>
                                </a>
                            </div>
                            @endadminCan
                        </div>

                        <!-- Marketplace Actions -->
                        @adminCanAny(['view_products', 'manage_sellers'])
                        <div class="dropdown-section-header">
                            <small class="text-muted fw-bold">🛒 MARKETPLACE</small>
                        </div>
                        <div class="row g-0 mb-2">
                            @adminCan('view_products')
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.marketplace.products.create') }}">
                                    <i class="fas fa-box text-warning"></i>
                                    <span>Thêm Sản Phẩm</span>
                                </a>
                            </div>
                            @endadminCan
                            @adminCan('manage_sellers')
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.marketplace.sellers.create') }}">
                                    <i class="fas fa-store text-purple"></i>
                                    <span>Thêm Seller</span>
                                </a>
                            </div>
                            @endadminCan
                        </div>
                        @endadminCanAny

                        <!-- Content Actions -->
                        @isAdmin
                        <div class="dropdown-section-header">
                            <small class="text-muted fw-bold">📝 NỘI DUNG</small>
                        </div>
                        <div class="row g-0">
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.pages.create') }}">
                                    <i class="fas fa-file-alt text-secondary"></i>
                                    <span>Tạo Trang</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.documentation.create') }}">
                                    <i class="fas fa-book text-primary"></i>
                                    <span>Tạo Tài Liệu</span>
                                </a>
                            </div>
                        </div>
                        @endisAdmin
                    </div>
                </div>
            </div>

            <!-- Theme Toggle -->
            <div class="dropdown d-none d-sm-inline-block">
                <button type="button" class="btn header-item" id="theme-toggle">
                    <i id="theme-icon" class="fas fa-moon"></i>
                </button>
            </div>

            <!-- Notifications -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon position-relative" id="page-header-notifications-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    @php
                        // Enable notifications system
                        $unreadNotifications = auth()->user() ? auth()->user()->unreadNotifications()->count() : 0;
                    @endphp
                    @if($unreadNotifications > 0)
                        <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle">
                            {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                        </span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0">Thông Báo</h6>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.notifications.mark-all-read') }}" class="small text-reset text-decoration-underline">Đánh dấu đã đọc</a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 230px;">
                        @forelse(auth()->user() ? auth()->user()->unreadNotifications()->limit(5)->get() : [] as $notification)
                            <a href="{{ route('admin.notifications.show', $notification->id) }}" class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="avatar-sm me-3">
                                        <span class="avatar-title bg-{{ $notification->color }} rounded-circle">
                                            <i class="fas fa-{{ $notification->icon }}"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="m-0">{{ $notification->title }}</h6>
                                        <div class="font-size-13 text-muted">
                                            <p class="m-0">{{ Str::limit($notification->message, 50) }}</p>
                                            <p class="m-0">
                                                <i class="fas fa-clock"></i>
                                                <span>{{ $notification->time_ago }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-bell-slash text-muted" style="font-size: 24px;"></i>
                                <p class="text-muted mt-2">Không có thông báo mới</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link font-size-14 text-center" href="{{ route('admin.notifications.index') }}">
                            <i class="fas fa-arrow-circle-right me-1"></i> <span>Xem Tất Cả</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Profile -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{ $avatar }}" alt="{{ $userName }}" style="width: 32px; height: 32px;">
                    <span class="d-none d-xl-inline-block ms-1 fw-medium">{{ $userName }}</span>
                    <i class="fas fa-chevron-down d-none d-xl-inline-block ms-1"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- User Info -->
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Xin chào {{ $userName }}!</h6>
                        <small class="text-muted">{{ $userEmail }}</small>
                        <small class="text-muted d-block">{{ $userRole }}</small>
                    </div>

                    <!-- Profile Links -->
                    <a class="dropdown-item" href="{{ route('admin.profile.index') }}">
                        <i class="fas fa-user align-middle me-1"></i> Hồ Sơ Cá Nhân
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.profile.password') }}">
                        <i class="fas fa-key align-middle me-1"></i> Đổi Mật Khẩu
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.settings.general') }}">
                        <i class="fas fa-cog align-middle me-1"></i> Cài Đặt
                    </a>

                    <!-- Quick Links -->
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ url('/') }}" target="_blank">
                        <i class="fas fa-external-link-alt align-middle me-1"></i> Xem Website
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.statistics.index') }}">
                        <i class="fas fa-chart-bar align-middle me-1"></i> Thống Kê
                    </a>

                    <!-- Logout -->
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt align-middle me-1"></i> Đăng Xuất
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>
