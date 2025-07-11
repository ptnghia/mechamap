<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'MechaMap Admin') | MechaMap - Mechanical Engineering Community</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="MechaMap Admin Panel - Mechanical Engineering Community Platform" name="description" />
    <meta content="MechaMap" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1c84ee">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MechaMap Admin">
    <meta name="msapplication-TileImage" content="{{ asset('assets/images/icons/admin-icon-144x144.png') }}">
    <meta name="msapplication-TileColor" content="#1c84ee">

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('admin-manifest.json') }}">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="{{ asset('android-chrome-512x512.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">

    @yield('css')

    <!-- Bootstrap Css -->
    <link href="{{ asset_versioned('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset_versioned('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- App Css-->
    <link href="{{ asset_versioned('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <!-- Mobile Admin Css -->
    <link href="{{ asset_versioned('assets/css/admin-mobile.css') }}" rel="stylesheet" type="text/css" />

    <!-- Hide PWA Install Prompt -->
    <link href="{{ asset_versioned('assets/css/hide-pwa-prompt.css') }}" rel="stylesheet" type="text/css" />

    <!-- Custom Admin Styles -->
    <link href="{{ asset_versioned('assets/css/style.css') }}" rel="stylesheet" type="text/css" />

    @stack('styles')
</head>

<body data-sidebar="dark" data-layout-mode="light">

    <!-- <body data-layout="horizontal" data-topbar="dark"> -->

    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('admin.layouts.partials.header')

        <!-- ========== Left Sidebar Start ========== -->
        @include('admin.layouts.partials.sidebar')
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <!-- start page title -->
                    @yield('page-title')
                    <!-- end page title -->

                    @yield('content')

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            @include('admin.layouts.partials.dason-footer')

        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    @include('admin.layouts.partials.dason-right-sidebar')
    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/libs/metismenu/metismenu.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/node-waves.min.js') }}"></script>

    <!-- Chart.js for Phase 2 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    @yield('script')

    <!-- App js -->
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

    <!-- Initialize Font Awesome Icons -->
    <script>
        // Override initFeathericon function to prevent errors
        window.initFeathericon = function() {
            console.log('Feather icons disabled - using Font Awesome');
        };

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Font Awesome icons loaded successfully');

            // Initialize Theme Toggle Functionality
            initializeThemeToggle();
        });

        // Theme Toggle Implementation
        function initializeThemeToggle() {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            const body = document.body;
            const html = document.documentElement;

            if (!themeToggleBtn || !themeIcon) {
                console.log('Theme toggle elements not found');
                return;
            }

            // Load saved theme from localStorage
            const savedTheme = localStorage.getItem('mechamap-admin-theme') || 'light';
            console.log('Loading saved theme:', savedTheme);

            // Apply saved theme
            applyTheme(savedTheme);

            // Add click event listener
            themeToggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const currentTheme = body.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';

                console.log('Switching theme from', currentTheme, 'to', newTheme);

                // Apply new theme
                applyTheme(newTheme);

                // Save to localStorage
                localStorage.setItem('mechamap-admin-theme', newTheme);

                // Show feedback
                showThemeChangeNotification(newTheme);
            });

            function applyTheme(theme) {
                // Update body and html attributes
                body.setAttribute('data-theme', theme);
                html.setAttribute('data-theme', theme);

                // Update body classes
                if (theme === 'dark') {
                    body.classList.add('dark-mode');
                    body.classList.remove('light-mode');
                    // Update icon to sun (for switching back to light)
                    themeIcon.className = 'fas fa-sun';
                } else {
                    body.classList.add('light-mode');
                    body.classList.remove('dark-mode');
                    // Update icon to moon (for switching to dark)
                    themeIcon.className = 'fas fa-moon';
                }

                // Update theme customizer if present
                updateThemeCustomizer(theme);

                console.log('Applied theme:', theme);
            }

            function updateThemeCustomizer(theme) {
                // Update the theme customizer radio buttons if present
                const lightRadio = document.querySelector('input[name="layout-mode"][value="light"]');
                const darkRadio = document.querySelector('input[name="layout-mode"][value="dark"]');

                if (lightRadio && darkRadio) {
                    if (theme === 'dark') {
                        darkRadio.checked = true;
                        lightRadio.checked = false;
                    } else {
                        lightRadio.checked = true;
                        darkRadio.checked = false;
                    }
                }
            }

            function showThemeChangeNotification(theme) {
                // Create a small notification
                const notification = document.createElement('div');
                notification.className = 'theme-change-notification';
                notification.innerHTML = `
                    <i class="fas fa-${theme === 'dark' ? 'moon' : 'sun'}"></i>
                    <span>Đã chuyển sang chế độ ${theme === 'dark' ? 'tối' : 'sáng'}</span>
                `;

                // Add styles
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${theme === 'dark' ? '#2a3042' : '#ffffff'};
                    color: ${theme === 'dark' ? '#ffffff' : '#495057'};
                    padding: 12px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 9999;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 14px;
                    border: 1px solid ${theme === 'dark' ? '#404553' : '#e9ecef'};
                    transition: all 0.3s ease;
                    opacity: 0;
                    transform: translateX(100%);
                `;

                document.body.appendChild(notification);

                // Animate in
                setTimeout(() => {
                    notification.style.opacity = '1';
                    notification.style.transform = 'translateX(0)';
                }, 10);

                // Remove after 3 seconds
                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            }

            console.log('Theme toggle initialized successfully');
        }








    </script>

    <!-- Mobile Admin JavaScript -->
    <script src="{{ asset('assets/js/admin-mobile.js') }}"></script>

    <!-- PWA JavaScript -->
    <script src="{{ asset('assets/js/admin-pwa.js') }}"></script>

    @stack('scripts')

</body>

</html>
