<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-3">
        <!-- DASHBOARD -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Bảng điều khiển
                </a>
            </li>
        </ul>

        <!-- QUẢN LÝ NỘI DUNG -->
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
            <span>Quản lý nội dung</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                    href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-tags"></i> {{ __('Chuyên mục') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.forums.*') ? 'active' : '' }}"
                    href="{{ route('admin.forums.index') }}">
                    <i class="bi bi-chat-left-text"></i> {{ __('Diễn đàn') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.threads.*') ? 'active' : '' }}"
                    href="{{ route('admin.threads.index') }}">
                    <i class="bi bi-file-earmark-text"></i> {{ __('Bài đăng') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}"
                    href="{{ route('admin.comments.index') }}">
                    <i class="bi bi-chat-dots"></i> {{ __('Bình luận') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.pages.*') || request()->routeIs('admin.page-categories.*') ? 'active' : '' }}"
                    href="{{ route('admin.pages.index') }}">
                    <i class="bi bi-file-earmark-text"></i> {{ __('Bài viết') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.showcases.*') ? 'active' : '' }}"
                    href="{{ route('admin.showcases.index') }}">
                    <i class="bi bi-star"></i> Showcase
                </a>
            </li>
        </ul>

        <!-- QUẢN LÝ NGƯỜI DÙNG -->
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
            <span>Quản lý người dùng</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                    href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i> Người dùng
                </a>
            </li>
        </ul>

        <!-- TƯƠNG TÁC & THÔNG BÁO -->
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
            <span>Tương tác & Thông báo</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}"
                    href="{{ route('admin.messages.index') }}">
                    <i class="bi bi-chat-square-dots"></i> Messages
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.alerts.*') ? 'active' : '' }}"
                    href="{{ route('admin.alerts.index') }}">
                    <i class="bi bi-bell"></i> Alerts
                </a>
            </li>
        </ul>

        <!-- HỖ TRỢ & MEDIA -->
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
            <span>Hỗ trợ & Media</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.faqs.*') || request()->routeIs('admin.faq-categories.*') ? 'active' : '' }}"
                    href="{{ route('admin.faqs.index') }}">
                    <i class="bi bi-question-circle"></i> {{ __('Hỏi đáp') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}"
                    href="{{ route('admin.media.index') }}">
                    <i class="bi bi-image"></i> Thư viện media
                </a>
            </li>
        </ul>

        <!-- THỐNG KÊ -->
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
            <span>{{ __('Thống kê') }}</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.statistics.index') ? 'active' : '' }}"
                    href="{{ route('admin.statistics.index') }}">
                    <i class="bi bi-bar-chart"></i> {{ __('Tổng quan') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.statistics.users') ? 'active' : '' }}"
                    href="{{ route('admin.statistics.users') }}">
                    <i class="bi bi-people"></i> {{ __('Người dùng') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.statistics.content') ? 'active' : '' }}"
                    href="{{ route('admin.statistics.content') }}">
                    <i class="bi bi-file-text"></i> {{ __('Nội dung') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.statistics.interactions') ? 'active' : '' }}"
                    href="{{ route('admin.statistics.interactions') }}">
                    <i class="bi bi-graph-up"></i> {{ __('Tương tác') }}
                </a>
            </li>
        </ul>

        <!-- HỆ THỐNG & CÔNG CỤ -->
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
            <span>Hệ thống & Công cụ</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.search.*') ? 'active' : '' }}"
                    href="{{ route('admin.search.index') }}">
                    <i class="bi bi-search-heart"></i> Cấu hình tìm kiếm
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}"
                    href="{{ route('admin.seo.index') }}">
                    <i class="bi bi-search"></i> SEO & Analytics
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                    href="{{ route('admin.settings.general') }}">
                    <i class="bi bi-gear"></i> Cài đặt chung
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-file-earmark-code"></i> Nhật ký hệ thống
                </a>
            </li>
        </ul>

        <!-- TÀI KHOẢN -->
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
            <span>Tài khoản</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"
                    href="{{ route('admin.profile.index') }}">
                    <i class="bi bi-person"></i> Hồ sơ của tôi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> Xem trang web
                </a>
            </li>
        </ul>
    </div>
</nav>