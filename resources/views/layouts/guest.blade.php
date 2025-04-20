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
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-muted/20">
            <div class="absolute top-4 right-4">
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

            <div>
                <a href="/" class="flex items-center justify-center">
                    <x-application-logo class="w-20 h-20 fill-current text-primary" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-card text-card-foreground shadow-soft-lg border border-border/40 overflow-hidden rounded-xl">
                {{ $slot }}
            </div>

            <div class="mt-8 text-center text-sm text-muted-foreground">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tất cả các quyền được bảo lưu.</p>
            </div>
        </div>

        <!-- Custom Scripts -->
        @stack('scripts')
    </body>
</html>
