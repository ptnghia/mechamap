<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-authenticated" content="{{ Auth::check() ? 'true' : 'false' }}">

    <title>@yield('title', config('app.name', 'MechaMap'))</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ get_favicon_url() }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet">

    @stack('styles')

    <style>
        /* Tailwind CSS Configuration */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Loading animation */
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <!-- Unified Header -->
    <x-unified-header
        :show-banner="get_setting('show_banner', true)"
        :is-marketplace="false"
    />

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">{{ get_site_name() }}</h3>
                    <p class="text-gray-400 mb-4">
                        Cộng đồng kỹ sư cơ khí hàng đầu Việt Nam. Chia sẻ kiến thức, kết nối chuyên gia, phát triển sự nghiệp.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-md font-semibold mb-4">Liên kết nhanh</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/forums" class="hover:text-white transition-colors">Diễn đàn</a></li>
                        <li><a href="/marketplace" class="hover:text-white transition-colors">Marketplace</a></li>
                        <li><a href="/showcase" class="hover:text-white transition-colors">CAD/Thiết kế</a></li>
                        <li><a href="/gallery" class="hover:text-white transition-colors">Album ảnh</a></li>
                        <li><a href="/members" class="hover:text-white transition-colors">Thành viên</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="text-md font-semibold mb-4">Hỗ trợ</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/faq" class="hover:text-white transition-colors">Hỏi đáp</a></li>
                        <li><a href="/contact" class="hover:text-white transition-colors">Liên hệ</a></li>
                        <li><a href="/help" class="hover:text-white transition-colors">Trung tâm trợ giúp</a></li>
                        <li><a href="/privacy" class="hover:text-white transition-colors">Chính sách bảo mật</a></li>
                        <li><a href="/terms" class="hover:text-white transition-colors">Điều khoản sử dụng</a></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h4 class="text-md font-semibold mb-4">Nhận tin tức</h4>
                    <p class="text-gray-400 mb-4">Đăng ký để nhận thông tin mới nhất về kỹ thuật cơ khí.</p>
                    <div class="flex">
                        <input
                            type="email"
                            placeholder="Email của bạn"
                            class="flex-1 px-3 py-2 bg-gray-800 border border-gray-700 rounded-l-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="border-t border-gray-800 mt-8 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-gray-400 text-sm mb-4 md:mb-0">
                        &copy; {{ date('Y') }} {{ get_site_name() }}. All rights reserved.
                    </div>
                    <div class="flex space-x-6 text-sm text-gray-400">
                        <a href="/privacy" class="hover:text-white transition-colors">Privacy Policy</a>
                        <a href="/terms" class="hover:text-white transition-colors">Terms of Service</a>
                        <a href="/cookies" class="hover:text-white transition-colors">Cookie Policy</a>
                        <a href="/accessibility" class="hover:text-white transition-colors">Accessibility</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Global Laravel configuration
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            user: @json(Auth::user()),
            siteName: '{{ get_site_name() }}',
            logoUrl: '{{ get_logo_url() }}',
            bannerUrl: '{{ get_banner_url() }}',
            apiUrl: '{{ url('/api') }}'
        };
    </script>

    <!-- jQuery -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap JavaScript -->
    <script src="{{ asset('assets/libs/bootstrap/bootstrap.min.js') }}"></script>

    <!-- Axios -->
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    @stack('scripts')

    <!-- Additional Scripts -->
    <script>
        // Initialize tooltips and other Bootstrap components
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize any additional functionality here
            console.log('Unified layout loaded');
        });
    </script>

    <!-- Authentication Modal -->
    @guest
    <x-auth-modal id="authModal" size="lg" />
    @endguest
</body>
</html>
