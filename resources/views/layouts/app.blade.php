<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="MechaMap - Diễn đàn cộng đồng chia sẻ kiến thức và kinh nghiệm">
        <meta name="keywords" content="mechamap, diễn đàn, cộng đồng, forum, laravel">
        <meta name="author" content="MechaMap Team">

        <title>{{ config('app.name', 'MechaMap') }} - @yield('title', 'Diễn đàn cộng đồng')</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Custom Styles -->
        @stack('styles')
    </head>
    <body class="font-sans antialiased min-h-screen bg-background text-foreground">
        <div class="relative flex min-h-screen flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="sticky top-0 z-30 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 border-b border-border/40 shadow-sm">
                    <div class="container-custom py-4">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="border-t border-border/40 bg-muted/20 py-6 md:py-0">
                <div class="container-custom">
                    <div class="flex flex-col items-center justify-between gap-4 md:h-16 md:flex-row">
                        <p class="text-sm text-muted-foreground">
                            &copy; {{ date('Y') }} {{ config('app.name') }}. Tất cả các quyền được bảo lưu.
                        </p>
                        <div class="flex items-center gap-4">
                            <button data-toggle-theme class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="dark-icon">
                                    <circle cx="12" cy="12" r="4"></circle>
                                    <path d="M12 2v2"></path>
                                    <path d="M12 20v2"></path>
                                    <path d="m4.93 4.93 1.41 1.41"></path>
                                    <path d="m17.66 17.66 1.41 1.41"></path>
                                    <path d="M2 12h2"></path>
                                    <path d="M20 12h2"></path>
                                    <path d="m6.34 17.66-1.41 1.41"></path>
                                    <path d="m19.07 4.93-1.41 1.41"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="light-icon hidden">
                                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                                </svg>
                                <span class="sr-only">Chuyển chế độ sáng/tối</span>
                            </button>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Custom Scripts -->
        @stack('scripts')
    </body>
</html>
