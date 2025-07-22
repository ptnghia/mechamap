{{--
    MechaMap Guest Menu Component
    Menu cho người dùng chưa đăng nhập
    Chỉ hiển thị các trang công khai và auth links
--}}

@php
    $currentRoute = request()->route()->getName();

    // Menu items cho guest (chưa đăng nhập)
    $menuItems = [
        'home' => [
            'title' => __('nav.home'),
            'route' => 'home',
            'icon' => 'fas fa-home',
            'description' => 'Trang chủ MechaMap'
        ],
        'forums' => [
            'title' => __('nav.forums'),
            'route' => 'forums.index',
            'icon' => 'fas fa-comments',
            'description' => 'Diễn đàn cộng đồng (chỉ xem)'
        ],
        'showcases' => [
            'title' => __('nav.showcases'),
            'route' => 'showcases.index',
            'icon' => 'fas fa-star',
            'description' => 'Showcase sản phẩm (chỉ xem)'
        ],
        'marketplace' => [
            'title' => __('nav.marketplace'),
            'route' => 'marketplace.index',
            'icon' => 'fas fa-store',
            'description' => 'Marketplace (chỉ xem)'
        ]
    ];

    // Auth menu items
    $authItems = [
        'login' => [
            'title' => t_auth('login.title'),
            'route' => 'login',
            'icon' => 'fas fa-sign-in-alt',
            'class' => 'btn btn-outline-primary'
        ],
        'register' => [
            'title' => t_auth('register.title'),
            'route' => 'register',
            'icon' => 'fas fa-user-plus',
            'class' => 'btn btn-primary'
        ]
    ];
@endphp

<!-- Main Navigation for Guest -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand logo" href="{{ route('home') }}">
            <img src="{{ get_logo_url() }}" alt="{{ get_site_name() }}" class="me-2">
            <span class="brand-text">{{ get_site_name() }}</span>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#guestNavbar"
                aria-controls="guestNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="guestNavbar">
            <!-- Main Menu Items -->
            <ul class="navbar-nav me-auto">
                @foreach($menuItems as $key => $item)
                    @if(Route::has($item['route']))
                        <li class="nav-item">
                            <a class="nav-link {{ $currentRoute === $item['route'] ? 'active' : '' }}"
                               href="{{ route($item['route']) }}"
                               title="{{ $item['description'] }}">
                                <i class="{{ $item['icon'] }} me-1"></i>
                                {{ $item['title'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>

            <!-- Auth Actions -->
            <ul class="navbar-nav ms-auto">
                <!-- Language Switcher -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-globe me-1"></i>
                        {{ strtoupper(app()->getLocale()) }}
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                        <li><a class="dropdown-item" href="{{ route('language.switch', 'vi') }}">
                            <i class="flag-icon flag-icon-vn me-2"></i>Tiếng Việt
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('language.switch', 'en') }}">
                            <i class="flag-icon flag-icon-us me-2"></i>English
                        </a></li>
                    </ul>
                </li>

                <!-- Auth Buttons -->
                @foreach($authItems as $key => $item)
                    @if(Route::has($item['route']))
                        <li class="nav-item ms-2">
                            <a href="{{ route($item['route']) }}" class="{{ $item['class'] }}">
                                <i class="{{ $item['icon'] }} me-1"></i>
                                {{ $item['title'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</nav>

<!-- Guest Notice Banner -->
<div class="guest-notice bg-light border-bottom py-2">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Bạn đang xem với quyền khách.
                    <strong>Đăng ký</strong> để tham gia thảo luận và sử dụng đầy đủ tính năng.
                </small>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('register') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-user-plus me-1"></i>
                    Đăng ký ngay
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Guest Menu Specific Styles */
.guest-notice {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.navbar-brand .brand-text {
    font-weight: 600;
    color: var(--bs-primary);
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
    .guest-notice .col-md-4 {
        text-align: center !important;
        margin-top: 10px;
    }

    .navbar-nav .nav-item {
        text-align: center;
    }

    .navbar-nav .ms-2 {
        margin-left: 0 !important;
        margin-top: 0.5rem;
    }
}
</style>

<script>
// Guest Menu JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Track guest interactions for analytics
    const guestLinks = document.querySelectorAll('.nav-link');
    guestLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Log guest navigation for analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'guest_navigation', {
                    'page_title': this.textContent.trim(),
                    'page_location': this.href
                });
            }
        });
    });

    // Auto-hide guest notice after 10 seconds
    setTimeout(function() {
        const guestNotice = document.querySelector('.guest-notice');
        if (guestNotice) {
            guestNotice.style.transition = 'opacity 0.5s ease';
            guestNotice.style.opacity = '0.7';
        }
    }, 10000);
});
</script>
