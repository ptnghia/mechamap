<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('images/setting/logo.png') }}" alt="MechaMap" height="30">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('images/setting/logo.png') }}" alt="MechaMap" height="24">
                        <span class="logo-txt">MechaMap Admin</span>
                    </span>
                </a>

                <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('images/setting/logo.png') }}" alt="MechaMap" height="30">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('images/setting/logo.png') }}" alt="MechaMap" height="24">
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
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.users.create') }}">
                                    <i class="fas fa-user-plus text-primary"></i>
                                    <span>Thêm User</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.threads.create') }}">
                                    <i class="fas fa-plus-circle text-success"></i>
                                    <span>Tạo Bài Đăng</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.categories.create') }}">
                                    <i class="fas fa-folder-plus text-warning"></i>
                                    <span>Tạo Danh Mục</span>
                                </a>
                            </div>
                        </div>

                        <!-- Marketplace Actions -->
                        <div class="dropdown-section-header">
                            <small class="text-muted fw-bold">🏪 MARKETPLACE</small>
                        </div>
                        <div class="row g-0 mb-2">
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.marketplace.products.create') ?? '#' }}">
                                    <i class="fas fa-box text-info"></i>
                                    <span>Thêm Sản Phẩm</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.marketplace.orders.index') ?? '#' }}">
                                    <i class="fas fa-shopping-cart text-purple"></i>
                                    <span>Tạo Đơn Hàng</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.users.index') ?? '#' }}">
                                    <i class="fas fa-store text-orange"></i>
                                    <span>Quản Lý Users</span>
                                </a>
                            </div>
                        </div>

                        <!-- Content & System -->
                        <div class="dropdown-section-header">
                            <small class="text-muted fw-bold">📄 NỘI DUNG & HỆ THỐNG</small>
                        </div>
                        <div class="row g-0">
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.forums.index') ?? '#' }}">
                                    <i class="fas fa-image text-teal"></i>
                                    <span>Quản Lý Forums</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.categories.index') ?? '#' }}">
                                    <i class="fas fa-file-plus text-secondary"></i>
                                    <span>Tạo Trang</span>
                                </a>
                            </div>
                            <div class="col">
                                <a class="dropdown-icon-item" href="{{ route('admin.settings.general') }}">
                                    <i class="fas fa-cog text-dark"></i>
                                    <span>Cài Đặt</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-question-circle"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <div class="dropdown-header">
                        <h6 class="m-0">❓ Trợ Giúp & Hỗ Trợ</h6>
                    </div>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-book text-primary me-2"></i>
                        Tài Liệu Hướng Dẫn
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-keyboard text-info me-2"></i>
                        Phím Tắt
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-play-circle text-success me-2"></i>
                        Video Hướng Dẫn
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-headset text-warning me-2"></i>
                        Liên Hệ Hỗ Trợ
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-comment-dots text-secondary me-2"></i>
                        Gửi Phản Hồi
                    </a>
                </div>
            </div>

            <!-- Theme Toggle -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item" id="theme-toggle">
                    <i class="fas fa-sun" id="theme-icon"></i>
                </button>
            </div>

            <!-- Messages -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon position-relative" id="page-header-messages-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-envelope"></i>
                    @php
                        $unreadMessages = 0; // TODO: Implement unreadMessages() method
                    @endphp
                    @if($unreadMessages > 0)
                        <span class="badge bg-success rounded-pill">{{ $unreadMessages }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-messages-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0">Tin Nhắn</h6>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.chat.index') }}" class="small text-reset text-decoration-underline">
                                    Chưa đọc ({{ $unreadMessages }})
                                </a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 230px;">
                        @php
                            $recentMessages = []; // TODO: Implement recentMessages() method
                        @endphp
                        @forelse($recentMessages as $message)
                            <a href="{{ route('admin.chat.conversation', $message->sender_id) }}" class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="avatar-sm me-3">
                                        <img src="{{ $message->sender->getAvatarUrl() }}" alt="{{ $message->sender->name }}" class="rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="m-0">{{ $message->sender->name }}</h6>
                                        <div class="font-size-13 text-muted">
                                            <p class="m-0">{{ Str::limit($message->content, 50) }}</p>
                                            <p class="m-0">
                                                <i class="fas fa-clock" style="width: 12px; height: 12px;"></i>
                                                <span>{{ $message->created_at->diffForHumans() }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-envelope-open" style="width: 24px; height: 24px;" class="text-muted"></i>
                                <p class="text-muted mt-2">Không có tin nhắn mới</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link font-size-14 text-center" href="{{ route('admin.chat.index') }}">
                            <i class="fas fa-arrow-circle-right" class="me-1" style="width: 14px; height: 14px;"></i> <span>Xem Tất Cả</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon position-relative" id="page-header-notifications-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    @if(isset($unreadNotifications) && $unreadNotifications > 0)
                        <span class="badge bg-danger rounded-pill">{{ $unreadNotifications }}</span>
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
                                <a href="{{ route('admin.notifications.index') }}" class="small text-reset text-decoration-underline">
                                    Chưa đọc ({{ $unreadNotifications ?? 0 }})
                                </a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 230px;">
                        @forelse($recentNotifications ?? [] as $notification)
                            <a href="{{ $notification->action_url ?? '#' }}" class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="avatar-sm me-3">
                                        <span class="avatar-title bg-{{ $notification->type_color ?? 'primary' }} rounded-circle">
                                            <i data-feather="{{ $notification->icon ?? 'bell' }}"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="m-0">{{ $notification->title ?? 'Thông báo mới' }}</h6>
                                        <div class="font-size-13 text-muted">
                                            <p class="m-0">{{ Str::limit($notification->message ?? 'Nội dung thông báo', 50) }}</p>
                                            <p class="m-0">
                                                <i class="fas fa-clock" style="width: 12px; height: 12px;"></i>
                                                <span>{{ $notification->created_at ? $notification->created_at->diffForHumans() : 'Vừa xong' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-bell-slash" style="width: 24px; height: 24px;" class="text-muted"></i>
                                <p class="text-muted mt-2">Không có thông báo mới</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link font-size-14 text-center" href="{{ route('admin.notifications.index') }}">
                            <i class="fas fa-arrow-circle-right" class="me-1" style="width: 14px; height: 14px;"></i> <span>Xem Tất Cả</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Profile -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @php
                        $user = Auth::guard('admin')->user();
                        $avatar = $user->avatar ?? asset('assets/images/users/avatar-1.jpg');
                        $userName = $user->name ?? 'Admin';
                        $userRole = $user->role ?? 'Administrator';
                    @endphp
                    <img class="rounded-circle header-profile-user" src="{{ $avatar }}" alt="{{ $userName }}">
                    <span class="d-none d-xl-inline-block ms-1 fw-medium">{{ $userName }}</span>
                    <i class="fas fa-chevron-down" class="d-none d-xl-inline-block" style="width: 16px; height: 16px;"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- User Info -->
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Xin chào {{ $userName }}!</h6>
                        <small class="text-muted">{{ $userRole }}</small>
                    </div>

                    <!-- Profile Links -->
                    <a class="dropdown-item" href="{{ route('admin.profile.index') }}">
                        <i class="fas fa-user" class="align-middle me-1" style="width: 16px; height: 16px;"></i> Hồ Sơ Cá Nhân
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.profile.password') }}">
                        <i class="fas fa-key" class="align-middle me-1" style="width: 16px; height: 16px;"></i> Đổi Mật Khẩu
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.settings.general') }}">
                        <i class="fas fa-cog" class="align-middle me-1" style="width: 16px; height: 16px;"></i> Cài Đặt
                    </a>

                    <!-- Quick Links -->
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ url('/') }}" target="_blank">
                        <i class="fas fa-external-link-alt" class="align-middle me-1" style="width: 16px; height: 16px;"></i> Xem Website
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.statistics.index') }}">
                        <i class="fas fa-chart-bar" class="align-middle me-1" style="width: 16px; height: 16px;"></i> Thống Kê
                    </a>

                    <!-- Logout -->
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt" class="align-middle me-1" style="width: 16px; height: 16px;"></i> Đăng Xuất
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>

<!-- Theme Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;

    // Get saved theme or default to light
    const savedTheme = localStorage.getItem('admin-theme') || 'light';
    body.setAttribute('data-layout-mode', savedTheme);
    updateThemeIcon(savedTheme);

    themeToggle.addEventListener('click', function() {
        const currentTheme = body.getAttribute('data-layout-mode');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        body.setAttribute('data-layout-mode', newTheme);
        localStorage.setItem('admin-theme', newTheme);
        updateThemeIcon(newTheme);
    });

    function updateThemeIcon(theme) {
        if (theme === 'dark') {
            themeIcon.className = 'fas fa-moon';
        } else {
            themeIcon.className = 'fas fa-sun';
        }
        console.log('Theme changed to:', theme);
    }
});
</script>
