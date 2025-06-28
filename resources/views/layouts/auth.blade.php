<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MechaMap') - {{ get_site_name() }}</title>
    <meta name="description" content="@yield('description', 'Cộng đồng Kỹ thuật Cơ khí Việt Nam - Nơi hội tụ tri thức CAD, CAM, CNC và Robot công nghiệp')">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Main CSS -->
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">

    <!-- Auth-specific CSS -->
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
    <link href="{{ asset('css/views/auth.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth-component.css') }}" rel="stylesheet">

    <!-- Shared Components CSS -->
    <link href="{{ asset('css/buttons.css') }}" rel="stylesheet">
    <link href="{{ asset('css/forms.css') }}" rel="stylesheet">
    <link href="{{ asset('css/alerts.css') }}" rel="stylesheet">
    <link href="{{ asset('css/avatar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom-header.css') }}" rel="stylesheet">

    <!-- Additional Styles -->
    @stack('styles')

    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--light-color);
        }

        .navbar-brand img {
            height: 40px;
            width: auto;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        /* Auth specific styles */
        .auth-container {
            min-height: calc(100vh - 200px);
        }

        .auth-card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
            border-radius: 1rem;
            overflow: hidden;
        }

        .auth-brand-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0b5ed7 100%);
            position: relative;
        }

        .auth-brand-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            opacity: 0.1;
        }

        .auth-form-section {
            background: white;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Community highlights */
        .community-card {
            transition: transform 0.2s ease-in-out;
        }

        .community-card:hover {
            transform: translateY(-5px);
        }

        /* Loading animation */
        .btn .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .auth-card {
                margin: 1rem;
            }

            .auth-brand-section {
                padding: 2rem 1rem !important;
            }

            .auth-form-section {
                padding: 2rem 1rem !important;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <x-unified-header
        :show-banner="get_setting('show_banner', true)"
        :is-marketplace="false"
    />

    <!-- Main Content -->
    <main class="auth-container d-flex align-items-center">
        @yield('content')
    </main>

    <!-- Footer -->
    <x-footer />

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery (if needed) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Additional Scripts -->
    @stack('scripts')

    <script>
        // Global auth page functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        alert.style.transition = 'opacity 0.5s';
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            if (alert.parentNode) {
                                alert.parentNode.removeChild(alert);
                            }
                        }, 500);
                    }
                }, 5000);
            });
        });
    </script>
</body>
</html>
