<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'MechaMap - Cộng đồng Kỹ thuật Cơ khí Việt Nam')</title>
    <meta name="description" content="@yield('description', 'Cộng đồng kỹ thuật cơ khí hàng đầu Việt Nam - Nơi chia sẻ kiến thức CAD, CAM, CNC và kết nối các chuyên gia')">
    <meta name="keywords" content="@yield('keywords', 'cơ khí, kỹ thuật, CAD, CAM, CNC, thiết kế máy móc, vietnam mechanical engineering')">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og_title', 'MechaMap')">
    <meta property="og:description" content="@yield('og_description', 'Cộng đồng kỹ thuật cơ khí hàng đầu Việt Nam')"
    <meta property="og:image" content="@yield('og_image', asset('images/brand/mechamap-banner.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('images/setting/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Main CSS -->
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('css/master-layout.css') }}" rel="stylesheet">
    <link href="{{ asset('css/buttons.css') }}" rel="stylesheet">
    <link href="{{ asset('css/forms.css') }}" rel="stylesheet">
    <link href="{{ asset('css/alerts.css') }}" rel="stylesheet">
    <link href="{{ asset('css/avatar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom-header.css') }}" rel="stylesheet">

    <!-- Page-specific CSS -->
    @stack('styles')

    <!-- Additional CSS for sidebar layout -->
    @if(isset($hasSidebar) && $hasSidebar)
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/mobile-nav.css') }}" rel="stylesheet">
    @endif

    <!-- Auth-specific CSS -->
    @if(isset($isAuthPage) && $isAuthPage)
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
    <link href="{{ asset('css/views/auth.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth-modal.css') }}" rel="stylesheet">
    @endif

    <!-- Admin-specific CSS -->
    @if(isset($isAdminPage) && $isAdminPage)
    <link href="{{ asset('css/views/admin.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin-pagination.css') }}" rel="stylesheet">
    @endif

    <!-- Common CSS -->
    <link href="{{ asset('css/dark-mode.css') }}" rel="stylesheet">
    <link href="{{ asset('css/compact-theme.css') }}" rel="stylesheet">

    <!-- Page-specific view CSS -->
    @if(isset($viewType))
        <link href="{{ asset('css/views/' . $viewType . '.css') }}" rel="stylesheet">
    @endif

    <!-- Google Analytics -->
    @if(config('app.env') === 'production')
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-MECHAMAP2024"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-MECHAMAP2024');
    </script>
    @endif
</head>

<body class="@yield('body_class', '') {{ isset($hasSidebar) && $hasSidebar ? 'has-sidebar' : 'no-sidebar' }}">
    <!-- Header -->
    <x-unified-header
        :show-banner="true"
        :is-marketplace="isset($isMarketplace) ? $isMarketplace : false"
    />

    <!-- Main Content Area -->
    <main class="main-content {{ isset($hasSidebar) && $hasSidebar ? 'with-sidebar' : 'full-width' }}">
        <!-- Sidebar (if needed) -->
        @if(isset($hasSidebar) && $hasSidebar)
            <aside class="sidebar-container">
                @if(isset($sidebarType))
                    @switch($sidebarType)
                        @case('professional')
                            <x-sidebar-professional />
                            @break
                        @case('thread-creation')
                            <x-thread-creation-sidebar />
                            @break
                        @default
                            <x-sidebar />
                    @endswitch
                @else
                    <x-sidebar />
                @endif
            </aside>
        @endif

        <!-- Content Area -->
        <div class="content-area {{ isset($hasSidebar) && $hasSidebar ? 'with-sidebar' : 'full-width' }}">
            <!-- Page Header (if provided) -->
            @hasSection('page_header')
                <div class="page-header">
                    @yield('page_header')
                </div>
            @endif

            <!-- Breadcrumbs (if provided) -->
            @hasSection('breadcrumbs')
                <nav aria-label="breadcrumb" class="breadcrumb-nav">
                    @yield('breadcrumbs')
                </nav>
            @endif

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Main Content -->
            <div class="main-content-wrapper">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Footer -->
    <x-footer />

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Main JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Page-specific JavaScript -->
    @stack('scripts')

    <!-- Auth Modal (if not auth page) -->
    @if(!isset($isAuthPage) || !$isAuthPage)
        <x-auth-modal />
    @endif

    <!-- Chat Widget (if user is logged in) -->
    @auth
        <x-chat-widget />
    @endauth
</body>
</html>
