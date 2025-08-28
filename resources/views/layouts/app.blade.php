{{--
    MechaMap Full-Width Layout - Frontend User
    Layout toàn màn hình không sidebar cho các trang cần không gian rộng
    Sử dụng: header.blade.php, footer.blade.php
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-authenticated" content="{{ Auth::check() ? 'true' : 'false' }}">
    @auth
    <!-- User Info for Real-time Features -->
    <meta name="user-id" content="{{ auth()->id() }}">
    <meta name="user-name" content="{{ auth()->user()->name }}">
    <meta name="auth-token" content="{{ auth()->user()->createToken('websocket-access')->plainTextToken }}">
    @endauth

    <!-- SEO Meta Tags with Multilingual Support -->
    <x-seo-meta :locale="app()->getLocale()" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Narrow:ital,wght@0,400..700;1,400..700&family=Arimo:ital,wght@0,400..700;1,400..700&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css" integrity="sha512-fw7f+TcMjTb7bpbLJZlP8g2Y4XcCyFZW8uy8HsRZsH/SwbMw0plKHFHr99DN3l04VsYNwvzicUX/6qurvIxbxw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- HC-MobileNav CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/hc-offcanvas-nav@6.1.5/dist/hc-offcanvas-nav.css">

    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Theme Preloader -->
    <script src="{{ asset_versioned('js/theme-preload.js') }}"></script>

    <!-- Frontend CSS -->
    <link rel="stylesheet" href="{{ asset_versioned('css/frontend/main-user.css') }}">
    <link rel="stylesheet" href="{{ asset_versioned('css/frontend/main.css') }}">
    <link rel="stylesheet" href="{{ asset_versioned('css/frontend/animation.css') }}">
    <link rel="stylesheet" href="{{ asset_versioned('css/frontend/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset_versioned('css/frontend/dark-mode.css') }}" id="darkModeCSS" disabled>

    <!-- Custom CSS -->
    @if(!empty($seo['custom_css'] ?? ''))
    <style>
        {!! $seo['custom_css'] !!}
    </style>
    @endif

    <!-- Custom Styles -->
    @stack('styles')

    <!-- Extra Meta Tags -->
    @if(!empty($seo['extra_meta'] ?? ''))
    {!! $seo['extra_meta'] !!}
    @endif

    <!-- Header Scripts -->
    @if(!empty($seo['header_scripts'] ?? ''))
    {!! $seo['header_scripts'] !!}
    @endif
</head>

<body class="user-frontend">
    <div class="d-flex flex-column min-vh-100">
        <!-- Header -->
        <x-header />

        <!-- Page Heading -->
        @isset($header)
        <header class="sticky-top">
            <div class="container-fluid py-3">
                {{ $header }}
            </div>
        </header>
        @endisset
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

            // Tools & Calculators - All tools use full-width layout
            'tools.index',
            'tools.material-calculator',
            'tools.process-calculator',
            'tools.materials',
            'tools.materials.show',
            'tools.standards',
            'tools.processes',
            'tools.cad-library',
            'tools.cad-library.show',
            'tools.technical-docs',
            'tools.documentation',
            'tools.documentation.show',

            // showcaseß
            'showcase.index',
            'showcase.show',


            ];

            // Kiểm tra route hiện tại
            $currentRoute = Route::currentRouteName();
            $showSidebar = !in_array($currentRoute, $excludedRoutes);
            @endphp
        <!-- Page Content -->
        <!-- Dynamic Breadcrumb -->
        <x-breadcrumb :breadcrumbs="$breadcrumbs ?? []" />
        <main>
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

        </main>

        <!-- Footer -->
        <x-footer />
    </div>
    <!-- Core JavaScript Libraries -->
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!-- Fancybox JS -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- MechaMap Core Scripts -->
    <script src="{{ asset_versioned('js/sweetalert-utils.js') }}"></script>
    <script src="{{ asset_versioned('js/notification-system.js') }}"></script>
    <script src="{{ asset_versioned('js/app.js') }}"></script>

    <!-- AJAX Error Handling -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.setupGlobalAjaxErrorHandling) {
                window.setupGlobalAjaxErrorHandling({
                    skipNetworkErrors: true,
                    skipCSRFErrors: false,
                    skipAPIErrors: true,
                    logErrors: true
                });
            }
        });
    </script>

    <!-- WebSocket & Real-time Features -->
    @auth
    <x-websocket-config :auto-init="false" />
    @endauth

    <!-- Fancybox Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Fancybox.bind("[data-fancybox]", {
                Toolbar: {
                    display: {
                        left: ["infobar"],
                        middle: ["zoomIn", "zoomOut", "toggle1to1", "rotateCCW", "rotateCW", "flipX", "flipY"],
                        right: ["slideshow", "thumbs", "close"],
                    },
                },
                Thumbs: { autoStart: false },
                l10n: {
                    CLOSE: {!! json_encode(__('ui.actions.close')) !!},
                    NEXT: {!! json_encode(__('ui.pagination.next')) !!},
                    PREV: {!! json_encode(__('ui.pagination.previous')) !!},
                    MODAL: {!! json_encode(__('ui.layout.fancybox.modal_esc_hint')) !!},
                    ERROR: {!! json_encode(__('ui.layout.fancybox.error_loading')) !!},
                    DOWNLOAD: {!! json_encode(__('ui.actions.download')) !!}
                }
            });
        });
    </script>


    <!-- HC-MobileNav JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/hc-offcanvas-nav@6.1.5/dist/hc-offcanvas-nav.js"></script>

    <!-- Additional Scripts -->
    <script src="{{ asset_versioned('js/translation-service.js') }}"></script>
    <script src="{{ asset_versioned('js/components.js') }}"></script>
    <script src="{{ asset_versioned('js/threads.js') }}"></script>
    <script src="{{ asset_versioned('js/thread-actions.js') }}"></script>

    <!-- WebSocket Initialization -->
    @auth
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.MechaMapWebSocket) {
                window.MechaMapWebSocket.initialize().then(socket => {
                    if (socket) {
                        console.log('✅ MechaMap WebSocket initialized');
                    }
                }).catch(error => {
                    console.error('❌ WebSocket error:', error);
                });
            }
        });
    </script>
    @endauth

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
</body>
</html>
