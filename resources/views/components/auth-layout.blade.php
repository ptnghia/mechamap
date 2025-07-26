{{-- Auth Layout Component --}}
@props([
    'title' => 'MechaMap',
    'subtitle' => 'Nơi hội tụ tri thức cơ khí',
    'showSocialLogin' => true,
    'formWidth' => 'col-lg-6'
])
@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/login.css') }}">
@endpush

<div class="min-vh-100 d-flex align-items-center bg-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <!-- Main Auth Card -->
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden login_page">
                    <div class="row g-0">
                        <!-- Brand Section -->
                        <div class="col-lg-5 bg-primary text-white d-lg-flex align-items-center br_img d-md-flex d-none d-sm-none">
                            <div class="login_page_left">
                                <!-- Logo -->
                                <div class="mb-4 login_page_left_logo">
                                    <img src="{{ get_logo_url() }}" alt="{{ $siteSettings['name'] }}" class="mb-2 auth-logo">
                                    <!--h3 class="fw-bold mb-2">{{ $siteSettings['name'] }}</h3-->
                                    <p class="fs-5 mb-0 opacity-90">{{ $siteSettings['slogan'] }}</p>
                                </div>

                                <!-- Value Propositions -->
                                <div class="login_page_left_content">
                                    <p class="d-flex align-items-center mb-3">
                                        <i class="fas fa-users-cog me-3 fs-5"></i>
                                        <span>{{ __('auth.connect_engineers') }}</span>
                                    </p>
                                    <p class="d-flex align-items-center mb-3">
                                        <i class="fas fa-comments me-3 fs-5"></i>
                                        <span>{{ __('auth.join_discussions') }}</span>
                                    </p>
                                    <p class="d-flex align-items-center mb-3">
                                        <i class="fas fa-tools me-3 fs-5"></i>
                                        <span>{{ __('auth.share_experience') }}</span>
                                    </p>
                                    <p class="d-flex align-items-center">
                                        <i class="fas fa-store me-3 fs-5"></i>
                                        <span>{{ __('auth.marketplace_products') }}</span>
                                    </p>
                                </div>

                            </div>
                        </div>

                        <!-- Form Section -->
                        <div class="col-lg-7 bg-white">
                            <div class="p-4 login_page_right xacminh_page_right">
                                <!-- Form Content -->
                                {{ $slot }}

                                <!-- Social Login (if enabled) -->
                                @if($showSocialLogin)
                                <div class="my-4">
                                    <div class="position-relative text-center">
                                        <hr class="my-4">
                                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">
                                            Hoặc đăng nhập với
                                        </span>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <a href="{{ route('auth.socialite', 'google') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center">
                                            <i class="fab fa-google me-2"></i>
                                            Google
                                        </a>
                                        <a href="{{ route('auth.socialite', 'facebook') }}" class="btn btn-primary d-flex align-items-center justify-content-center">
                                            <i class="fab fa-facebook-f me-2"></i>
                                            Facebook
                                        </a>
                                    </div>
                                </div>
                                @endif

                                <!-- Security Badge -->
                                <div class="text-center mt-4">
                                    <small class="text-muted d-flex align-items-center justify-content-center">
                                        <i class="fas fa-shield-alt me-2 text-success"></i>
                                        Bảo mật SSL 256-bit
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
