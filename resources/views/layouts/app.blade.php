<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Lightbox CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">

    <!-- Auth Modal CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth-modal.css') }}">

    <!-- Search CSS -->
    <link rel="stylesheet" href="{{ asset('css/search.css') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom CSS -->
    @if(!empty($seo['custom_css'] ?? ''))
    <style>
        {
             ! ! $seo['custom_css'] ! !
        }
    </style>
    @endif

    <!-- Page-specific CSS -->
    @if(Route::currentRouteName() === 'home')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    @endif

    <!-- What's New CSS -->
    @if(str_starts_with(Route::currentRouteName(), 'whats-new'))
    <link rel="stylesheet" href="{{ asset('css/whats-new.css') }}">
    @endif

    <!-- Activity CSS -->
    <link rel="stylesheet" href="{{ asset('css/activity.css') }}">

    <!-- Alerts CSS -->
    <link rel="stylesheet" href="{{ asset('css/alerts.css') }}">

    <!-- Sidebar CSS -->
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

    <!-- Compact Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/compact-theme.css') }}">

    <!-- Unified Button Styles -->
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">

    <!-- Unified Form Styles -->
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">

    <!-- Avatar Styles -->
    <link rel="stylesheet" href="{{ asset('css/avatar.css') }}">

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
        @include('layouts.navigation')

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
            'threads.create',
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
        <footer class="border-top bg-light py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <p class="small text-muted mb-0">
                            {{ get_copyright_info()['text'] }}
                        </p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="d-flex justify-content-center justify-content-md-end align-items-center">
                            <!-- Social Media Links -->
                            @php
                            $socialLinks = get_social_links();
                            @endphp

                            @if(!empty($socialLinks['facebook']))
                            <a href="{{ $socialLinks['facebook'] }}" target="_blank"
                                class="btn btn-sm btn-outline-secondary rounded-circle p-2 me-2"
                                data-bs-toggle="tooltip" title="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            @endif

                            @if(!empty($socialLinks['twitter']))
                            <a href="{{ $socialLinks['twitter'] }}" target="_blank"
                                class="btn btn-sm btn-outline-secondary rounded-circle p-2 me-2"
                                data-bs-toggle="tooltip" title="Twitter">
                                <i class="bi bi-twitter"></i>
                            </a>
                            @endif

                            @if(!empty($socialLinks['instagram']))
                            <a href="{{ $socialLinks['instagram'] }}" target="_blank"
                                class="btn btn-sm btn-outline-secondary rounded-circle p-2 me-2"
                                data-bs-toggle="tooltip" title="Instagram">
                                <i class="bi bi-instagram"></i>
                            </a>
                            @endif

                            @if(!empty($socialLinks['linkedin']))
                            <a href="{{ $socialLinks['linkedin'] }}" target="_blank"
                                class="btn btn-sm btn-outline-secondary rounded-circle p-2 me-2"
                                data-bs-toggle="tooltip" title="LinkedIn">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            @endif

                            @if(!empty($socialLinks['youtube']))
                            <a href="{{ $socialLinks['youtube'] }}" target="_blank"
                                class="btn btn-sm btn-outline-secondary rounded-circle p-2 me-2"
                                data-bs-toggle="tooltip" title="YouTube">
                                <i class="bi bi-youtube"></i>
                            </a>
                            @endif

                            <!-- Theme Toggle Button -->
                            <button id="theme-toggle" data-toggle-theme
                                class="btn btn-sm btn-outline-secondary rounded-circle p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-sun dark-icon" viewBox="0 0 16 16">
                                    <path
                                        d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-moon-stars light-icon d-none" viewBox="0 0 16 16">
                                    <path
                                        d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278zM4.858 1.311A7.269 7.269 0 0 0 1.025 7.71c0 4.02 3.279 7.276 7.319 7.276a7.316 7.316 0 0 0 5.205-2.162c-.337.042-.68.063-1.029.063-4.61 0-8.343-3.714-8.343-8.29 0-1.167.242-2.278.681-3.286z" />
                                    <path
                                        d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z" />
                                </svg>
                                <span class="visually-hidden">Chuyển chế độ sáng/tối</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery (Required for Lightbox) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Theme toggle script is now handled by darkMode.js -->

    <!-- Auth Modal Script -->
    <script src="{{ asset('js/auth-modal.js') }}"></script>

    <!-- Search Script -->
    <script src="{{ asset('js/search.js') }}"></script>

    <!-- Lightbox Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
    <script>
        // Lightbox configuration
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'albumLabel': 'Hình %1 / %2',
                'fadeDuration': 300,
                'imageFadeDuration': 300
            });
    </script>

    <!-- CKEditor Script - Only load on pages that need it -->
    @if(in_array(Route::currentRouteName(), ['threads.create', 'threads.edit']))
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/translations/vi.js"></script>
    @endif
</body>

</html>
