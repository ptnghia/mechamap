<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'MechaMap Admin') | MechaMap - Mechanical Engineering Community</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="MechaMap Admin Panel - Mechanical Engineering Community Platform" name="description" />
    <meta content="MechaMap" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1c84ee">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MechaMap Admin">
    <meta name="msapplication-TileImage" content="{{ asset('assets/images/icons/admin-icon-144x144.png') }}">
    <meta name="msapplication-TileColor" content="#1c84ee">

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('admin-manifest.json') }}">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="{{ asset('assets/images/icons/admin-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/icons/admin-icon-180x180.png') }}">

    @yield('css')

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <!-- Mobile Admin Css -->
    <link href="{{ asset('assets/css/admin-mobile.css') }}" rel="stylesheet" type="text/css" />

    <!-- Custom Admin Styles -->
    <style>
        /* Feather Icons Styling */
        .btn i[data-feather] {
            width: 16px;
            height: 16px;
            stroke-width: 2;
        }

        .btn-sm i[data-feather] {
            width: 14px;
            height: 14px;
            stroke-width: 2.5;
        }

        .btn-lg i[data-feather] {
            width: 20px;
            height: 20px;
            stroke-width: 1.5;
        }

        /* Header icons */
        .header-item i[data-feather] {
            width: 20px;
            height: 20px;
            stroke-width: 2;
        }

        /* Table action icons */
        .table .btn-group i[data-feather] {
            width: 14px;
            height: 14px;
            stroke-width: 2.5;
        }

        /* Input group icons */
        .input-group-text i[data-feather] {
            width: 16px;
            height: 16px;
            stroke-width: 2;
        }

        /* Mini stat icons */
        .mini-stat-icon i[data-feather] {
            width: 24px;
            height: 24px;
            stroke-width: 1.5;
        }

        /* Stats icons */
        .stats-icon i[data-feather] {
            width: 24px;
            height: 24px;
            stroke-width: 1.5;
        }

        /* Card trend icons */
        .card-trend i[data-feather] {
            width: 14px;
            height: 14px;
            stroke-width: 2.5;
        }

        /* Ensure proper alignment */
        i[data-feather] {
            vertical-align: middle;
            display: inline-block;
        }

        /* Font Awesome icons styling */
        .fas, .far, .fab {
            vertical-align: middle;
            display: inline-block;
            width: 16px;
            height: 16px;
            font-size: 16px;
        }

        /* Sidebar icon alignment */
        .sidebar-menu i.fas,
        .sidebar-menu i.far,
        .sidebar-menu i.mdi {
            width: 20px;
            text-align: center;
            margin-right: 8px;
        }
    </style>

    @stack('styles')

    <!-- Enhanced Header Styles -->
    <style>
        /* Quick Actions Button Enhancement */
        .quick-actions-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .quick-actions-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white !important;
        }

        /* Dropdown Section Headers */
        .dropdown-section-header {
            padding: 8px 12px 4px 12px;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 8px;
        }

        .dropdown-section-header:first-child {
            margin-top: 8px;
        }

        /* Enhanced Dropdown Icon Items */
        .dropdown-icon-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 12px 8px;
            text-decoration: none;
            color: #495057;
            border-radius: 8px;
            transition: all 0.2s ease;
            margin: 2px;
        }

        .dropdown-icon-item:hover {
            background-color: #f8f9fa;
            color: #495057;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .dropdown-icon-item i {
            font-size: 1.5rem;
            margin-bottom: 4px;
        }

        .dropdown-icon-item span {
            font-size: 0.75rem;
            font-weight: 500;
            text-align: center;
            line-height: 1.2;
        }

        /* Custom Colors for Icons */
        .text-purple { color: #6f42c1 !important; }
        .text-orange { color: #fd7e14 !important; }
        .text-teal { color: #20c997 !important; }

        /* Header Item Enhancements */
        .header-item {
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .header-item:hover {
            background-color: rgba(0,0,0,0.05);
            transform: translateY(-1px);
        }

        /* Notification Badge Improvements */
        .noti-icon .badge {
            position: absolute;
            top: -2px;
            right: -2px;
            font-size: 0.65rem;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Messages specific styling */
        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f3f4;
            transition: background-color 0.2s ease;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        /* Help dropdown styling */
        .dropdown-header h6 {
            color: #495057;
            font-weight: 600;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .quick-actions-btn span {
                display: none !important;
            }

            .dropdown-icon-item span {
                font-size: 0.7rem;
            }
        }

        /* Dark mode adjustments */
        [data-layout-mode="dark"] .quick-actions-btn {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        [data-layout-mode="dark"] .dropdown-icon-item:hover {
            background-color: rgba(255,255,255,0.1);
            color: #fff;
        }

        [data-layout-mode="dark"] .notification-item:hover {
            background-color: rgba(255,255,255,0.05);
        }
    </style>
</head>

<body data-sidebar="dark" data-layout-mode="light">

    <!-- <body data-layout="horizontal" data-topbar="dark"> -->

    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('admin.layouts.partials.dason-header')

        <!-- ========== Left Sidebar Start ========== -->
        @include('admin.layouts.partials.dason-sidebar')
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

            @include('layouts.partials.dason-footer')

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

    @yield('script')

    <!-- App js -->
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

    <!-- Initialize Font Awesome Icons -->
    <script>
        // Create mock feather object to prevent "feather is not defined" errors
        window.feather = {
            icons: {},
            replace: function() {
                console.log('Feather replace disabled - using Font Awesome');
            },
            toSvg: function() {
                return '';
            }
        };

        // Override initFeathericon function before app.min.js loads
        window.initFeathericon = function() {
            console.log('Feather icons disabled - using Font Awesome');
        };

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Font Awesome icons loaded successfully');

            // Remove any remaining data-feather attributes
            document.querySelectorAll('[data-feather]').forEach(function(element) {
                const iconName = element.getAttribute('data-feather');
                console.log('Removing data-feather:', iconName);
                element.removeAttribute('data-feather');

                // Add Font Awesome class if not already present
                if (!element.className.includes('fa')) {
                    element.className = 'fas fa-' + iconName.replace(/-/g, '-');
                }
            });
        });








    </script>

    <!-- Mobile Admin JavaScript -->
    <script src="{{ asset('assets/js/admin-mobile.js') }}"></script>

    <!-- PWA JavaScript -->
    <script src="{{ asset('assets/js/admin-pwa.js') }}"></script>

    @stack('scripts')

</body>

</html>
