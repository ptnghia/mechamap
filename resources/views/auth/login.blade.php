@extends('layouts.app')

@section('title', __('auth.login'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/auth/login.css') }}">
@endpush

@section('content')
<div class="min-vh-100 d-flex align-items-center bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <!-- Main Login Card -->
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="row g-0">
                        <!-- Brand Section -->
                        <div class="col-lg-6 bg-primary text-white position-relative">
                            <div class="p-5 h-100 d-flex flex-column justify-content-center">
                                <!-- Background Pattern -->
                                <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10">
                                    <svg width="100%" height="100%" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                        <defs>
                                            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                                            </pattern>
                                        </defs>
                                        <rect width="100%" height="100%" fill="url(#grid)" />
                                    </svg>
                                </div>

                                <!-- Content -->
                                <div class="position-relative">
                                    <!-- Logo -->
                                    <div class="mb-4">
                                        <img src="{{ get_logo_url() }}" alt="MechaMap" class="mb-3 auth-logo">
                                        <h3 class="fw-bold mb-2">MechaMap</h3>
                                        <p class="fs-5 mb-0 opacity-90">{{ __('content.knowledge_hub') }}</p>
                                    </div>

                                    <!-- Value Propositions -->
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-users-cog me-3 fs-5"></i>
                                            <span>{{ __('content.connect_engineers') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-comments me-3 fs-5"></i>
                                            <span>{{ __('content.join_discussions') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-tools me-3 fs-5"></i>
                                            <span>{{ __('content.share_experience') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-graduation-cap me-3 fs-5"></i>
                                            <span>Học hỏi từ chuyên gia hàng đầu</span>
                                        </div>
                                    </div>

                                    <!-- Trust Indicators -->
                                    <div class="border-top border-white border-opacity-25 pt-4">
                                        <p class="small mb-2 opacity-75">Được tin tưởng bởi:</p>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-white text-primary me-2">Kỹ sư CAD</span>
                                            <span class="badge bg-white text-primary me-2">Chuyên gia CNC</span>
                                            <span class="badge bg-white text-primary">Nhà sản xuất</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Login Form Section -->
                        <div class="col-lg-6 bg-white">
                            <div class="p-5">
                                <!-- Header -->
                                <div class="text-center mb-4">
                                    <h2 class="fw-bold text-dark mb-2">Chào mừng trở lại!</h2>
                                    <p class="text-muted mb-0">
                                        Đăng nhập để tiếp tục hành trình kỹ thuật của bạn
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
                                            <i class="fas fa-user me-2 text-muted"></i>Email hoặc tên đăng nhập
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-lg @error('login') is-invalid @enderror"
                                               id="login"
                                               name="login"
                                               value="{{ old('login') }}"
                                               required
                                               autocomplete="username"
                                               placeholder="engineer@mechamap.com">
                                        @error('login')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Password Field -->
                                    <div class="mb-3">
                                        <label for="password" class="form-label fw-medium text-dark">
                                            <i class="fas fa-lock me-2 text-muted"></i>Mật khẩu
                                        </label>
                                        <div class="position-relative">
                                            <input type="password"
                                                   class="form-control form-control-lg @error('password') is-invalid @enderror"
                                                   id="password"
                                                   name="password"
                                                   required
                                                   autocomplete="current-password"
                                                   placeholder="••••••••">
                                            <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted" onclick="togglePassword()">
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
                                                Ghi nhớ đăng nhập
                                            </label>
                                        </div>
                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}" class="text-primary text-decoration-none">Quên mật khẩu?</a>
                                        @endif
                                    </div>

                                    <!-- Login Button -->
                                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-4 position-relative" id="loginBtn">
                                        <span class="btn-text">
                                            <i class="fas fa-sign-in-alt me-2"></i>
                                            Đăng nhập
                                        </span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                </form>

                                <!-- Divider -->
                                <div class="position-relative text-center mb-4">
                                    <hr class="text-muted">
                                    <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">
                                        Hoặc đăng nhập với
                                    </span>
                                </div>

                                <!-- Social Login -->
                                <div class="row g-2 mb-4">
                                    <div class="col-6">
                                        <a href="#" class="btn btn-outline-danger w-100">
                                            <i class="fab fa-google me-2"></i>
                                            Google
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="#" class="btn btn-outline-primary w-100">
                                            <i class="fab fa-facebook-f me-2"></i>
                                            Facebook
                                        </a>
                                    </div>
                                </div>

                                <!-- Register Link -->
                                <div class="text-center">
                                    <p class="text-muted mb-0">
                                        Chưa có tài khoản?
                                        <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-medium">Đăng ký ngay</a>
                                    </p>
                                </div>

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

                <!-- Community Highlights -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-dark">Tham gia cộng đồng kỹ thuật hàng đầu Việt Nam</h4>
                            <p class="text-muted">Khám phá những thảo luận nổi bật và kết nối với các chuyên gia</p>
                        </div>

                        <div class="row g-4">
                            <!-- Trending Topics -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-fire text-danger fs-1 mb-3"></i>
                                        <h5 class="fw-bold">Xu hướng nổi bật</h5>
                                        <p class="text-muted small">Mastercam, Siemens PLC, Robot công nghiệp</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Expert Network -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user-tie text-primary fs-1 mb-3"></i>
                                        <h5 class="fw-bold">Mạng lưới chuyên gia</h5>
                                        <p class="text-muted small">64+ kỹ sư từ các công ty hàng đầu</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Knowledge Base -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-book-open text-success fs-1 mb-3"></i>
                                        <h5 class="fw-bold">Kho tri thức</h5>
                                        <p class="text-muted small">118+ thảo luận chất lượng cao</p>
                                    </div>
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
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Form submission with loading state
document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    const btnText = btn.querySelector('.btn-text');
    const spinner = btn.querySelector('.spinner-border');

    btnText.classList.add('d-none');
    spinner.classList.remove('d-none');
    btn.disabled = true;
});
</script>
@endsection
