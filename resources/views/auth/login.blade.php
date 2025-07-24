@extends('layouts.app')

@section('title', __('auth.login.title'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/login.css') }}">
@endpush

@section('full-width-content')
<div class="min-vh-100 d-flex align-items-center bg-light my-4">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-10 col-xl-9">
                <!-- Main Login Card -->
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden login_page">

                    <div class="row g-0 ">
                        <!-- Brand Section -->
                        <div class="col-lg-5 bg-primary text-white d-lg-flex align-items-center br_img d-md-flex d-none d-sm-none">
                            <!-- Content -->
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

                        <!-- Login Form Section -->
                        <div class="col-lg-7 bg-white">
                            <div class="p-5 login_page_right">
                                <!-- Header -->
                                <div class="text-center mb-4">
                                    <h2 class="fw-bold text-dark mb-2 login_page_right_title">{{ __('auth.login.title') }}</h2>
                                    <p class="text-muted mb-0 login_page_right_description">
                                        {{ $siteSettings['welcome_message'] }}
                                    </p>
                                </div>

                                <!-- Session Status -->
                                @if (session('status'))
                                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>
                                        {{ session('status') }}
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <!-- Login Form -->
                                <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                                    @csrf

                                    <!-- Email/Username Field -->
                                    <div class="mb-3">
                                        <label for="login" class="form-label fw-medium text-dark">
                                            {{ __('auth.email_or_username_label') }}
                                        </label>
                                        <div class="form-group-icon">
                                            <i class="fas fa-user input_icon"></i>
                                            <input type="text" class="form-control @error('login') is-invalid @enderror" id="login" name="login" value="{{ old('login') }}" required autocomplete="username" placeholder="engineer@mechamap.com">
                                        </div>
                                        @error('login')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Password Field -->
                                    <div class="mb-3">
                                        <label for="password" class="form-label fw-medium text-dark">
                                            {{ __('auth.password_label') }}
                                        </label>
                                        <div class="position-relative form-group-icon">
                                            <i class="fas fa-lock input_icon"></i>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"  required autocomplete="current-password" placeholder="••••••••">
                                            <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted btn-atc-input" onclick="toggleLoginPassword()">
                                                <i class="fas fa-eye" id="toggleIcon"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Remember Me and Forgot Password -->
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label text-muted" for="remember">
                                                {{ __('auth.remember_login') }}
                                            </label>
                                        </div>
                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}" class="text-primary">{{ __('auth.forgot_password_link') }}</a>
                                        @endif
                                    </div>

                                    <!-- Login Button -->
                                    <button type="submit" class="btn btn-main active w-100 mb-4 position-relative" id="loginBtn">
                                        <span class="btn-text">
                                            <i class="fas fa-sign-in-alt me-2"></i>
                                            {{ __('auth.login_button') }}
                                        </span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                </form>

                                <!-- Divider -->
                                <div class="position-relative text-center mb-4">
                                    <hr class="">
                                    <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-bold">
                                        {{ __('auth.or_login_with') }}
                                    </span>
                                </div>

                                <!-- Social Login -->
                                <div class="row g-2 mb-4">
                                    <div class="col-6">
                                        <a href="{{ route('auth.socialite', 'google') }}" class="btn btn-main btn-social btn-svg w-100">
                                            <i class="icon-svg svg-google me-2"></i>
                                            {{ __('auth.login_with_google') }}
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('auth.socialite', 'facebook') }}" class="btn btn-main btn-social btn-svg w-100">
                                            <i class="icon-svg svg-facebook me-2"></i>
                                            {{ __('auth.login_with_facebook') }}
                                        </a>
                                    </div>
                                </div>

                                <!-- Register Link -->
                                <div class="text-center">
                                    <p class="mb-0">
                                        {{ __('auth.no_account') }}
                                        <a href="{{ route('register') }}" class="text-primary fw-medium">{{ __('auth.register_now') }}</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Đảm bảo DOM đã load hoàn toàn
document.addEventListener('DOMContentLoaded', function() {

    // Toggle Password Function for Login Page
    window.toggleLoginPassword = function() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        // Kiểm tra xem các phần tử có tồn tại không
        if (!passwordInput || !toggleIcon) {
            console.error('Login page: Password input or toggle icon not found');
            console.log('passwordInput:', passwordInput);
            console.log('toggleIcon:', toggleIcon);
            return;
        }

        try {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
                toggleIcon.setAttribute('title', 'Ẩn mật khẩu');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
                toggleIcon.setAttribute('title', 'Hiện mật khẩu');
            }
        } catch (error) {
            console.error('Error in toggleLoginPassword:', error);
        }
    };

    // Form submission with loading state
    const loginForm = document.querySelector('form');
    if (loginForm) {
        loginForm.addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            if (btn) {
                const btnText = btn.querySelector('.btn-text');
                const spinner = btn.querySelector('.spinner-border');

                if (btnText) btnText.classList.add('d-none');
                if (spinner) spinner.classList.remove('d-none');
                btn.disabled = true;
            }
        });
    }

    // Thêm tooltip cho toggle button
    const toggleButton = document.querySelector('.btn-atc-input');
    if (toggleButton) {
        toggleButton.setAttribute('title', 'Hiện mật khẩu');
    }
});
</script>
@endsection
