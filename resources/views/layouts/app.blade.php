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

    <!-- User Info for Real-time Features -->
    @auth
    <meta name="user-id" content="{{ auth()->id() }}">
    <meta name="user-name" content="{{ auth()->user()->name }}">
    <meta name="auth-token" content="{{ auth()->user()->createToken('websocket-access')->plainTextToken }}">
    @endauth

    <!-- SEO Meta Tags -->
    <title>{{ $title ?? $seo['site_title'] ?? config('app.name') }} - @yield('title', __('seo.site.tagline'))</title>
    <meta name="description"
        content="{{ $description ?? $seo['site_description'] ?? __('seo.site.description') }}">
    <meta name="keywords"
        content="{{ $keywords ?? $seo['site_keywords'] ?? __('seo.site.keywords') }}">
    <meta name="author" content="{{ __('ui.layout.meta_author') }}">

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
        content="{{ $description ?? $seo['og_description'] ?? $seo['site_description'] ?? __('seo.site.description') }}">
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

    <!-- HC-MobileNav CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/hc-offcanvas-nav@6.1.5/dist/hc-offcanvas-nav.css">

    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

    <!-- All component CSS now included in main-user-optimized.css -->

    <!-- Scripts -->
    <!-- Theme Preloader - Loads before page rendering to prevent flashing -->
    <script src="{{ asset_versioned('js/theme-preload.js') }}"></script>
    <!-- Component CSS -->
    <link rel="stylesheet" href="{{ asset_versioned('css/frontend/components/notifications.css') }}">

    <!-- Frontend CSS - Optimized Structure with Cache Busting -->
    <link rel="stylesheet" href="{{ asset_versioned('css/frontend/main-user.css') }}">
    <!-- Animation CSS -->
    <link rel="stylesheet" href="{{ asset_versioned('css/frontend/animation.css') }}">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{ asset_versioned('css/frontend/responsive.css    ') }}">

    <!-- Dark Mode CSS -->
    <link rel="stylesheet" href="{{ asset_versioned('css/frontend/dark-mode.css') }}">



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

<body class="user-frontend">
    <div class="">
        <x-header />
        <!-- Page Heading -->
        @isset($header)
        <header class="sticky-top">
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
            // Authentication routes
            'login',
            'register',
            'password.request',
            'password.email',
            'password.reset',
            'password.update',
            'verification.notice',
            'verification.verify',
            'verification.send',
            'password.confirm',


            // Registration wizard
            'register.wizard.step1',
            'register.wizard.step2',
            'register.wizard.step3',

            // Profile and user management
            'threads.edit',
            'profile.edit',
            'profile.update',
            'profile.destroy',
            'conversations.show',
            'conversations.create',
            'bookmarks.index',

            // Public pages
            'showcase.index',
            'subscription.index',
            'business.index',
            'business.services',
            'companies.index',
            'companies.show',
            'jobs.index',
            'events.index',
            'events.show',
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!-- Fancybox JS -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

    <!-- Main App JS -->
    <script src="{{ asset_versioned('js/app.js') }}"></script>

    <!-- WebSocket & Real-time Dependencies -->
    @auth
    <!-- Socket.IO-based Notification Service for Node.js WebSocket server -->
    <script src="{{ asset_versioned('js/frontend/services/notification-service.js') }}"></script>
    <script src="{{ asset_versioned('js/frontend/components/notification-manager.js') }}"></script>
    <script src="{{ asset_versioned('js/frontend/components/typing-indicator.js') }}"></script>
    @endauth

    <!-- Dark Mode JS -->
    <script src="{{ asset_versioned('js/dark-mode.js') }}"></script>

    <!-- Theme Diagnostics Tool (activated by adding ?theme-diagnostics=1 to URL) -->
    <script src="{{ asset_versioned('js/theme-diagnostics.js') }}"></script>

    <!-- Theme Recovery System - Restores theme toggle functionality if it breaks -->
    <script src="{{ asset_versioned('js/theme-recovery.js') }}"></script>

    <!-- Fancybox Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Fancybox for all images with data-fancybox attribute
            Fancybox.bind("[data-fancybox]", {
                // Options
                Toolbar: {
                    display: {
                        left: ["infobar"],
                        middle: [
                            "zoomIn",
                            "zoomOut",
                            "toggle1to1",
                            "rotateCCW",
                            "rotateCW",
                            "flipX",
                            "flipY",
                        ],
                        right: ["slideshow", "thumbs", "close"],
                    },
                },
                Thumbs: {
                    autoStart: false,
                },
                // Vietnamese labels
                l10n: {
                    CLOSE: {!! json_encode(__('ui.actions.close')) !!},
                    NEXT: {!! json_encode(__('ui.pagination.next')) !!},
                    PREV: {!! json_encode(__('ui.pagination.previous')) !!},
                    MODAL: {!! json_encode(__('ui.layout.fancybox.modal_esc_hint')) !!},
                    ERROR: {!! json_encode(__('ui.layout.fancybox.error_loading')) !!},
                    IMAGE_ERROR: {!! json_encode(__('ui.layout.fancybox.image_error')) !!},
                    ELEMENT_NOT_FOUND: {!! json_encode(__('ui.layout.fancybox.element_not_found')) !!},
                    AJAX_NOT_FOUND: {!! json_encode(__('ui.layout.fancybox.ajax_not_found')) !!},
                    AJAX_FORBIDDEN: {!! json_encode(__('ui.layout.fancybox.ajax_forbidden')) !!},
                    IFRAME_ERROR: {!! json_encode(__('ui.layout.fancybox.iframe_error')) !!},
                    TOGGLE_ZOOM: {!! json_encode(__('ui.layout.fancybox.toggle_zoom')) !!},
                    TOGGLE_THUMBS: {!! json_encode(__('ui.layout.fancybox.toggle_thumbs')) !!},
                    TOGGLE_SLIDESHOW: {!! json_encode(__('ui.layout.fancybox.toggle_slideshow')) !!},
                    TOGGLE_FULLSCREEN: {!! json_encode(__('ui.layout.fancybox.toggle_fullscreen')) !!},
                    DOWNLOAD: {!! json_encode(__('ui.actions.download')) !!}
                }
            });
        });
    </script>

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
                    console.log({!! json_encode(__('ui.layout.console.theme_button_fallback')) !!});
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

    <!-- Search Script - Disabled, using unified header search -->
    {{-- <script src="{{ asset('js/search.js') }}"></script> --}}

    <!-- HC-MobileNav JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/hc-offcanvas-nav@6.1.5/dist/hc-offcanvas-nav.js"></script>

    <!-- Mobile Navigation Script -->
    <script src="{{ asset_versioned('js/mobile-nav.js') }}"></script>

    <!-- Header System - Consolidated search, menu, and navigation -->
    <script src="{{ asset_versioned('js/header.js') }}"></script>

    <!-- Components Script -->
    <script src="{{ asset_versioned('js/components.js') }}"></script>

    <!-- Thread Actions Script -->
    <script src="{{ asset_versioned('js/threads.js') }}"></script>

    <!-- WebSocket Configuration and Connection -->
    @auth
    <x-websocket-config :auto-init="true" />
    @endauth


</body>

</html>
