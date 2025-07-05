{{--
    MechaMap Main Layout - Frontend User
    Layout chính thống nhất cho tất cả trang frontend user
    Sử dụng: header.blade.php, sidebar.blade.php, footer.blade.php
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-authenticated" content="{{ Auth::check() ? 'true' : 'false' }}">

    <!-- User Info for Chat Widget -->
    @auth
    <meta name="user-id" content="{{ auth()->id() }}">
    @endauth

    <!-- SEO Meta Tags -->
    <title>{{ $title ?? $seo['site_title'] ?? config('app.name') }} - @yield('title', 'Diễn đàn cộng đồng')</title>
    <meta name="description"
        content="{{ $description ?? $seo['site_description'] ?? 'MechaMap - Diễn đàn cộng đồng chia sẻ kiến thức và kinh nghiệm' }}">
    <meta name="keywords"
        content="{{ $keywords ?? $seo['site_keywords'] ?? 'mechamap, diễn đàn, cộng đồng, forum, laravel' }}">
    <meta name="author" content="MechaMap Team">

    @if(!($seo['allow_indexing'] ?? true))
    <meta name="robots" content="noindex, nofollow">
    @endif

    @if(!empty($seo['canonical_url'] ?? ''))
    <link rel="canonical" href="{{ $seo['canonical_url'] }}">
    @else
    <link rel="canonical" href="{{ url()->current() }}">
    @endif

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? $seo['og_title'] ?? $seo['site_title'] ?? config('app.name') }}">
    <meta property="og:description"
        content="{{ $description ?? $seo['og_description'] ?? $seo['site_description'] ?? 'MechaMap - Diễn đàn cộng đồng chia sẻ kiến thức và kinh nghiệm' }}">
    @if(!empty($seo['og_image'] ?? ''))
    <meta property="og:image" content="{{ url($seo['og_image']) }}">
    @endif
    @if(!empty($seo['facebook_app_id'] ?? ''))
    <meta property="fb:app_id" content="{{ $seo['facebook_app_id'] }}">
    @endif

    <!-- Twitter -->
    <meta name="twitter:card" content="{{ $seo['twitter_card'] ?? 'summary' }}">
    @if(!empty($seo['twitter_username'] ?? ''))
    <meta name="twitter:site" content="{{ " @" . $seo['twitter_username'] }}">
    <meta name="twitter:creator" content="{{ " @" . $seo['twitter_username'] }}">
    @endif
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title"
        content="{{ $title ?? $seo['twitter_title'] ?? $seo['site_title'] ?? config('app.name') }}">
    <meta name="twitter:description"
        content="{{ $description ?? $seo['twitter_description'] ?? $seo['site_description'] ?? 'MechaMap - Diễn đàn cộng đồng chia sẻ kiến thức và kinh nghiệm' }}">
    @if(!empty($seo['twitter_image'] ?? ''))
    <meta name="twitter:image" content="{{ url($seo['twitter_image']) }}">
    @endif

    <!-- Google Search Console Verification -->
    @if(!empty($seo['google_search_console_id'] ?? ''))
    <meta name="google-site-verification" content="{{ $seo['google_search_console_id'] }}">
    @endif

    <!-- Favicon -->
    <link rel="icon" href="{{ get_favicon_url() }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ get_favicon_url() }}" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css" integrity="sha512-fw7f+TcMjTb7bpbLJZlP8g2Y4XcCyFZW8uy8HsRZsH/SwbMw0plKHFHr99DN3l04VsYNwvzicUX/6qurvIxbxw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Iconsax -->
    <link href="https://iconsax.gitlab.io/i/icons.css" rel="stylesheet">

    <!-- All component CSS now included in main-user-optimized.css -->

    <!-- Scripts -->
    <!-- Theme Preloader - Loads before page rendering to prevent flashing -->
    <script src="{{ asset('js/theme-preload.js') }}"></script>

    <!-- Optimized Frontend CSS - New Structure -->
    <link rel="stylesheet" href="{{ asset('css/frontend/main-user-optimized.css') }}">

    <!-- Page-specific CSS now loaded in individual views via @push('styles') -->

    <!-- Custom CSS -->
    @if(!empty($seo['custom_css'] ?? ''))
    <style>
        {
             ! ! $seo['custom_css'] ! !
        }
    </style>
    @endif

    <!-- All component CSS now included in main-user-optimized.css -->

    <!-- Extra Meta Tags -->
    @if(!empty($seo['extra_meta'] ?? ''))
    {!! $seo['extra_meta'] !!}
    @endif

    <!-- Header Scripts -->
    @if(!empty($seo['header_scripts'] ?? ''))
    {!! $seo['header_scripts'] !!}
    @endif

    <!-- Custom Styles -->
    @stack('styles')
</head>

<body class="min-vh-100">
    <div class="d-flex flex-column min-vh-100">
        <x-header />

        <!-- Page Heading -->
        @isset($header)
        <header class="sticky-top bg-white border-bottom shadow-sm">
            <div class="container py-3">
                {{ $header }}
            </div>
        </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-grow-1">
            @php
            // Xác định các trang không hiển thị sidebar
            $excludedRoutes = [
            'threads.edit',
            'profile.edit',
            'profile.update',
            'profile.destroy',
            'conversations.show',
            'conversations.create',
            'bookmarks.index',
            'showcase.index',
            'subscription.index',
            'business.index',
            'business.services',
            ];

            // Kiểm tra route hiện tại
            $currentRoute = Route::currentRouteName();
            $showSidebar = !in_array($currentRoute, $excludedRoutes);
            @endphp

            @if(View::hasSection('full-width-content'))
            @yield('full-width-content')
            @endif

            @if(View::hasSection('content'))
            <div class="container py-4">
                <div class="row">
                    <div class="col-lg-8">
                        @yield('content')
                    </div>
                    <div class="col-lg-4">
                        <x-sidebar :showSidebar="$showSidebar" />
                    </div>
                </div>
            </div>
            @elseif(!View::hasSection('full-width-content'))
            <div class="container py-4">
                <div class="alert alert-info">
                    Không có nội dung để hiển thị.
                </div>
            </div>
            @endif
        </main>

        <!-- Footer -->
        <x-footer />

    </div>

    <!-- Custom Scripts -->
    @stack('scripts')

    <!-- Chat Widget - Chỉ hiển thị khi đăng nhập -->
    @auth
    <div id="chatWidget" class="chat-widget" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050;">
        <button id="chatToggle" class="btn btn-primary rounded-circle" style="width: 60px; height: 60px;">
            <i class="fas fa-comments"></i>
        </button>
        <div id="chatPanel" class="d-none" style="position: absolute; bottom: 80px; right: 0; width: 350px; height: 500px; background: white; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.15); border: 1px solid #e9ecef;">
            <div class="p-3 bg-primary text-white rounded-top">
                <h6 class="mb-0">Tin nhắn</h6>
            </div>
            <div class="p-3">
                <p>Chat widget đang hoạt động!</p>
                <p>User: {{ auth()->user()->name }}</p>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatToggle = document.getElementById('chatToggle');
        const chatPanel = document.getElementById('chatPanel');

        if (chatToggle && chatPanel) {
            chatToggle.addEventListener('click', function() {
                chatPanel.classList.toggle('d-none');
            });
        }
    });
    </script>
    @endauth

    <!-- Footer Scripts -->
    @if(!empty($seo['footer_scripts'] ?? ''))
    {!! $seo['footer_scripts'] !!}
    @endif

    <!-- Google Analytics -->
    @if(!empty($seo['google_analytics_id'] ?? ''))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $seo['google_analytics_id'] }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ $seo['google_analytics_id'] }}');
    </script>
    @endif

    <!-- jQuery (Required for Lightbox) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Axios (Required for AJAX requests) -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Main App JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Dark Mode JS -->
    <script src="{{ asset('js/dark-mode.js') }}"></script>

    <!-- Theme Diagnostics Tool (activated by adding ?theme-diagnostics=1 to URL) -->
    <script src="{{ asset('js/theme-diagnostics.js') }}"></script>

    <!-- Theme Recovery System - Restores theme toggle functionality if it breaks -->
    <script src="{{ asset('js/theme-recovery.js') }}"></script>

    <!-- Theme Debug Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Theme debug info:', {
                themeButton: document.getElementById('theme-toggle'),
                dataTheme: document.documentElement.getAttribute('data-theme'),
                localStorageTheme: localStorage.getItem('theme')
            });

            // Thêm xử lý lỗi dự phòng
            setTimeout(function() {
                const toggleBtn = document.getElementById('theme-toggle');
                if (toggleBtn && !toggleBtn._hasClickHandler) {
                    console.log('Adding fallback click handler to theme button');
                    toggleBtn._hasClickHandler = true;
                    toggleBtn.addEventListener('click', function(e) {
                        console.log('Theme button clicked (fallback handler)');
                        e.preventDefault();
                        if (window.themeManager) {
                            window.themeManager.toggle();
                        } else {
                            console.error('themeManager not found');
                        }
                    });
                }
            }, 1000);
        });
    </script>

    <!-- Auth Modal Script -->
    <script src="{{ asset('js/auth-modal.js') }}"></script>

    <!-- Search Script - Disabled, using unified header search -->
    {{-- <script src="{{ asset('js/search.js') }}"></script> --}}

    <!-- Header System - Consolidated search, menu, and navigation -->
    <script src="{{ asset('js/header.js') }}"></script>

    <!-- Thread Item Script -->
    <script src="{{ asset('js/thread-item.js') }}"></script>

    <!-- Thread Actions Script -->
    <script src="{{ asset('js/thread-actions-simple.js') }}"></script>

    <!-- Manual Dropdown Script -->
    <script src="{{ asset('js/manual-dropdown.js') }}"></script>

    <!-- CKEditor Script - Only load on pages that need it -->
    @if(in_array(Route::currentRouteName(), ['threads.create', 'threads.edit']))
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/translations/vi.js"></script>
    @endif

    <!-- Authentication Modal -->
    @guest
    <x-auth-modal id="authModal" size="lg" />
    @endguest
</body>

</html>
