<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Admin Dashboard') | MechaMap - Mechanical Engineering Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="MechaMap Admin Dashboard - Mechanical Engineering Community Platform" name="description" />
    <meta content="MechaMap Development Team" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.svg') }}">
    
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <!-- MechaMap Custom Css -->
    <link href="{{ asset('css/mechamap-admin.css') }}" rel="stylesheet" type="text/css" />
    
    @stack('styles')
</head>

<body data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-keep-enlarged="true">

    <!-- <body data-layout="horizontal" data-topbar="dark"> -->

    <!-- Begin page -->
    <div id="layout-wrapper">

        <!-- ========== Top Bar ========== -->
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="{{ asset('images/logo-sm.svg') }}" alt="MechaMap" height="30">
                            </span>
                            <span class="logo-lg">
                                <img src="{{ asset('images/logo-dark.svg') }}" alt="MechaMap" height="24">
                            </span>
                        </a>

                        <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="{{ asset('images/logo-sm.svg') }}" alt="MechaMap" height="30">
                            </span>
                            <span class="logo-lg">
                                <img src="{{ asset('images/logo-light.svg') }}" alt="MechaMap" height="24">
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>

                    <!-- App Search-->
                    <form class="app-search d-none d-lg-block">
                        <div class="position-relative">
                            <input type="text" class="form-control" placeholder="Search users, products, orders...">
                            <button class="btn btn-primary" type="submit"><i class="bx bx-search-alt align-middle"></i></button>
                        </div>
                    </form>
                </div>

                <div class="d-flex">
                    <!-- Marketplace Quick Stats -->
                    <div class="dropdown d-inline-block d-lg-none ms-2">
                        <button type="button" class="btn header-item" id="page-header-search-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-search" class="icon-lg"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                            aria-labelledby="page-header-search-dropdown">
                            <form class="p-3">
                                <div class="form-group m-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search ..." aria-label="Search Result">
                                        <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item noti-icon position-relative" id="page-header-notifications-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="bell" class="icon-lg"></i>
                            <span class="badge bg-danger rounded-pill">{{ auth()->user()->unreadNotifications->count() ?? 0 }}</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                            aria-labelledby="page-header-notifications-dropdown">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0"> Notifications </h6>
                                    </div>
                                    <div class="col-auto">
                                        <a href="#!" class="small text-reset text-decoration-underline"> Unread ({{ auth()->user()->unreadNotifications->count() ?? 0 }})</a>
                                    </div>
                                </div>
                            </div>
                            <div data-simplebar style="max-height: 230px;">
                                @forelse(auth()->user()->unreadNotifications->take(5) ?? [] as $notification)
                                <a href="#!" class="text-reset notification-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <img src="{{ asset('images/users/avatar-3.jpg') }}" class="rounded-circle avatar-sm" alt="user-pic">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $notification->data['title'] ?? 'New Notification' }}</h6>
                                            <div class="font-size-13 text-muted">
                                                <p class="mb-1">{{ $notification->data['message'] ?? 'You have a new notification' }}</p>
                                                <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span>{{ $notification->created_at->diffForHumans() }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @empty
                                <div class="text-center p-4">
                                    <p class="text-muted mb-0">No new notifications</p>
                                </div>
                                @endforelse
                            </div>
                            <div class="p-2 border-top d-grid">
                                <a class="btn btn-sm btn-link font-size-14 text-center" href="{{ route('admin.notifications') }}">
                                    <i class="mdi mdi-arrow-right-circle me-1"></i> <span>View More..</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile -->
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="{{ auth()->user()->avatar_url ?? asset('images/users/avatar-1.jpg') }}"
                                alt="Header Avatar">
                            <span class="d-none d-xl-inline-block ms-1 fw-medium">{{ auth()->user()->name }}</span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="mdi mdi-face-profile font-size-16 align-middle me-1"></i> Profile</a>
                            <a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="mdi mdi-cog-outline font-size-16 align-middle me-1"></i> Settings</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">
            <div data-simplebar class="h-100">
                <!--- Sidemenu -->
                <div id="sidebar-menu">
                    <!-- Left Menu Start -->
                    <ul class="metismenu list-unstyled" id="side-menu">
                        <li class="menu-title" data-key="t-menu">Menu</li>

                        <!-- Dashboard -->
                        <li>
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-home"></i>
                                <span data-key="t-dashboard">Dashboard</span>
                            </a>
                        </li>

                        <!-- Marketplace Management -->
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="shopping-cart"></i>
                                <span data-key="t-marketplace">Marketplace</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('admin.products.index') }}" data-key="t-products">Products</a></li>
                                <li><a href="{{ route('admin.orders.index') }}" data-key="t-orders">Orders</a></li>
                                <li><a href="{{ route('admin.sellers.index') }}" data-key="t-sellers">Sellers</a></li>
                                <li><a href="{{ route('admin.categories.index') }}" data-key="t-categories">Categories</a></li>
                                <li><a href="{{ route('admin.transactions.index') }}" data-key="t-transactions">Transactions</a></li>
                            </ul>
                        </li>

                        <!-- User Management -->
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i class="fas fa-users"></i>
                                <span data-key="t-users">Users</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('admin.users.index') }}" data-key="t-all-users">All Users</a></li>
                                <li><a href="{{ route('admin.users.roles') }}" data-key="t-roles">Roles & Permissions</a></li>
                                <li><a href="{{ route('admin.users.activities') }}" data-key="t-activities">User Activities</a></li>
                            </ul>
                        </li>

                        <!-- Forum Management -->
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="message-square"></i>
                                <span data-key="t-forum">Forum</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('admin.forum.categories') }}" data-key="t-forum-categories">Categories</a></li>
                                <li><a href="{{ route('admin.forum.threads') }}" data-key="t-threads">Threads</a></li>
                                <li><a href="{{ route('admin.forum.posts') }}" data-key="t-posts">Posts</a></li>
                                <li><a href="{{ route('admin.forum.moderation') }}" data-key="t-moderation">Moderation</a></li>
                            </ul>
                        </li>

                        <!-- Analytics & Reports -->
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="bar-chart-2"></i>
                                <span data-key="t-analytics">Analytics</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('admin.analytics.overview') }}" data-key="t-overview">Overview</a></li>
                                <li><a href="{{ route('admin.analytics.marketplace') }}" data-key="t-marketplace-analytics">Marketplace</a></li>
                                <li><a href="{{ route('admin.analytics.users') }}" data-key="t-user-analytics">Users</a></li>
                                <li><a href="{{ route('admin.analytics.revenue') }}" data-key="t-revenue">Revenue</a></li>
                            </ul>
                        </li>

                        <!-- System Settings -->
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i class="fas fa-cog"></i>
                                <span data-key="t-settings">Settings</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('admin.settings.general') }}" data-key="t-general">General</a></li>
                                <li><a href="{{ route('admin.settings.payment') }}" data-key="t-payment">Payment</a></li>
                                <li><a href="{{ route('admin.settings.email') }}" data-key="t-email">Email</a></li>
                                <li><a href="{{ route('admin.settings.maintenance') }}" data-key="t-maintenance">Maintenance</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- start page title -->
                    @if(isset($pageTitle) || View::hasSection('page-title'))
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">@yield('page-title', $pageTitle ?? 'Dashboard')</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        @yield('breadcrumb')
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!-- end page title -->

                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © MechaMap - Mechanical Engineering Platform
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Design & Develop by <a href="#!" class="text-decoration-underline">MechaMap Team</a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    <div class="right-bar">
        <div data-simplebar class="h-100">
            <div class="rightbar-title d-flex align-items-center bg-dark p-3">
                <h5 class="m-0 me-2 text-white">Theme Customizer</h5>
                <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                    <i class="mdi mdi-close noti-icon"></i>
                </a>
            </div>

            <!-- Settings -->
            <hr class="m-0" />
            <div class="p-4">
                <h6 class="mb-3">Layout</h6>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="layout" id="layout-vertical" value="vertical">
                    <label class="form-check-label" for="layout-vertical">Vertical</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="layout" id="layout-horizontal" value="horizontal">
                    <label class="form-check-label" for="layout-horizontal">Horizontal</label>
                </div>

                <h6 class="mt-4 mb-3 pt-2">Layout Mode</h6>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="layout-mode" id="layout-mode-light" value="light">
                    <label class="form-check-label" for="layout-mode-light">Light</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="layout-mode" id="layout-mode-dark" value="dark">
                    <label class="form-check-label" for="layout-mode-dark">Dark</label>
                </div>
            </div>
        </div>
    </div>
    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    
    <!-- MechaMap Custom JS -->
    <script src="{{ asset('js/mechamap-admin.js') }}"></script>

    @stack('scripts')
</body>

</html>
