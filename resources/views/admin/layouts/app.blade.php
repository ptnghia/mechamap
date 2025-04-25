<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('admin.layouts.partials.meta')

    @include('admin.layouts.partials.styles')

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    @include('admin.layouts.partials.header')

    <div class="container-fluid admin-container">
        <div class="row">
            @include('admin.layouts.partials.sidebar')

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-main">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('header', 'Dashboard')</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        @yield('actions')
                    </div>
                </div>

                @include('admin.layouts.partials.breadcrumb')

                @include('admin.layouts.partials.alerts')

                @yield('content')
            </main>
        </div>
    </div>

    @include('admin.layouts.partials.footer')

    @include('admin.layouts.partials.scripts')

    @stack('scripts')
</body>
</html>
