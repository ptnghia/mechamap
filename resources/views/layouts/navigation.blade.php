<!-- Header with Banner and Navigation -->
<header class="site-header">
    <!-- Banner Image -->
    <div class="header-banner">
        <img src="{{ get_banner_url() }}" alt="Banner" class="banner-img">
    </div>

    <!-- Header Content with Navigation -->
    <div class="header-content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container w-100 d-flex justify-content-between align-items-center">
                <a class="navbar-brand logo" href="{{ route('home') }}">
                    <img src="{{ get_logo_url() }}" alt="{{ get_site_name() }}" class="img-fluid">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="search-container">
                    <div class="input-group">
                        <input type="text" class="form-control" id="header-search" name="query"
                            placeholder="Nhập từ khóa cần tìm..." aria-label="Search">
                        <button class="btn" type="button" id="header-search-btn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <!-- Search Results Dropdown -->
                    <div class="search-results-dropdown" id="search-results-dropdown">
                        <div class="search-scope-options">
                            <div class="search-scope-option active" data-scope="site">Tất cả nội dung</div>
                            <div class="search-scope-option" data-scope="forum">Trong diễn đàn phụ</div>
                            <div class="search-scope-option" data-scope="thread">Trong chủ đề này</div>
                        </div>
                        <div class="search-results-content" id="search-results-content">
                            <!-- Results will be loaded here via AJAX -->
                        </div>
                        <div class="search-results-footer">
                            <a href="{{ route('search.advanced') }}" class="advanced-search-link">
                                <i class="bi bi-sliders"></i> Tìm kiếm nâng cao
                            </a>
                        </div>
                    </div>
                </div>
                <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                    <ul class="navbar-nav nav-icon">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('whats-new') }}" title="What's New">
                                <i class="fa-solid fa-fire-flame-curved"></i>
                                <span class="nav-text">Mới</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forums.index') }}" title="Diễn đàn">
                                <i class="fa-regular fa-rectangle-list"></i>
                                <span class="nav-text">Diễn đàn</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="{{ route('forums.index') }}" id="navbarDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis"></i>
                                More
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('whats-new') }}">
                                        <i class="fa-solid fa-newspaper"></i>
                                        <span>Cập nhật mới</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('forums.listing') }}">
                                        <i class="fa-solid fa-list-ul"></i>
                                        <span>Danh sách diễn đàn</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('showcase.public') }}">
                                        <i class="fa-solid fa-compass-drafting"></i>
                                        <span>Bản vẽ - thiết kế (CAD)</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('gallery.index') }}">
                                        <i class="fa-regular fa-images"></i>
                                        <span>Album ảnh</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('search.advanced') }}">
                                        <i class="fa-brands fa-searchengin"></i>
                                        <span>Tìm kiếm nâng cao</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('members.index') }}">
                                        <i class="fa-solid fa-users-gear"></i>
                                        <span>Thành viên</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('faq.index') }}">
                                        <i class="fa-solid fa-question"></i>
                                        <span>Hỏi đáp</span>
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <div class="dropdown-item d-flex justify-content-between align-items-center">
                                        <span id="themeLabel">
                                            <i class="bi bi-moon theme-icon-dark me-2"></i>
                                            <i class="bi bi-sun theme-icon-light me-2 d-none"></i>
                                            <span class="theme-text">{{ request()->cookie('dark_mode') == 'dark' ? 'Chế
                                                độ sáng' : 'Chế độ tối' }}</span>
                                        </span>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="darkModeSwitch" data-toggle-theme="dark" {{
                                                request()->cookie('dark_mode') == 'dark' ? 'checked'
                                            : '' }}>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- User Menu or Login/Register -->
                        @auth
                        <li class="nav-item dropdown user-menu">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false" title="{{ Auth::user()->name }}">
                                <img src="{{ Auth::user()->getAvatarUrl() }}" alt="{{ Auth::user()->name }}"
                                    class="rounded-circle me-1" width="24" height="24">
                                <span class="nav-text">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.show', Auth::user()->username) }}">
                                        <i class="fa-regular fa-address-card"></i>
                                        <span>Hồ sơ của tôi</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('following.index') }}">
                                        <i class="fa-solid fa-circle-plus"></i>
                                        <span>Đang theo dõi</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('alerts.index') }}">
                                        <i class="fa-solid fa-bell"></i>
                                        <span>Thông báo</span>
                                        @php
                                        $unreadAlertsCount = Auth::user()->alerts()->whereNull('read_at')->count();
                                        @endphp
                                        @if($unreadAlertsCount > 0)
                                        <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadAlertsCount
                                            }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('conversations.index') }}">
                                        <i class="fa-regular fa-envelope"></i>
                                        <span>Tin nhắn</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('bookmarks.index') }}">
                                        <i class="fa-regular fa-bookmark"></i>
                                        <span>Đã lưu</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('showcase.index') }}">
                                        <i class="bi bi-image me-2"></i> My Showcase
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-gear me-2"></i> Cài đặt tài khoản
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('business.index') }}">
                                        <i class="bi bi-briefcase me-2"></i> Doanh nghiệp của tôi
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('subscription.index') }}">
                                        <i class="bi bi-star me-2"></i> Gói đăng ký của tôi
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        @else
                        <li class="nav-item auth-buttons">
                            <a class="nav-link login-link" href="#" title="Login / Join">
                                <i class="fa-regular fa-user"></i>
                                <span>Đăng nhập</span>
                            </a>
                        </li>
                        @endauth
                    </ul>

                </div>
            </div>
        </nav>
    </div>
</header>
