@extends('layouts.app')

@section('title', __('auth.register'))

@section('content')
<x-auth-layout
    title="{{ __('auth.create_new_account') }}"
    subtitle="{{ __('content.join_engineering_community') }}"
    :show-social-login="true">

    <!-- Page Title -->
    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark mb-2">{{ __('auth.welcome_to_mechamap') }}</h2>
        <p class="text-muted">{{ __('auth.create_account_journey') }}</p>
    </div>

    <!-- Status Messages -->
    @if (session('status'))
        <div class="alert alert-success mb-4">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('status') }}
        </div>
    @endif

    <!-- Registration Form -->
    <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">
                <i class="fas fa-user me-2"></i>Họ và tên
            </label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror"
                   required autofocus autocomplete="name"
                   placeholder="Nhập họ và tên đầy đủ của bạn">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Username -->
        <div class="mb-3">
            <label for="username" class="form-label">
                <i class="fas fa-at me-2"></i>Tên đăng nhập
            </label>
            <input id="username" type="text" name="username" value="{{ old('username') }}"
                   class="form-control @error('username') is-invalid @enderror"
                   required autocomplete="username"
                   placeholder="Chọn tên đăng nhập duy nhất">
            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">Tên đăng nhập sẽ được sử dụng trong URL profile của bạn</small>
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="fas fa-envelope me-2"></i>Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required autocomplete="email"
                   placeholder="Nhập địa chỉ email của bạn">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">
                <i class="fas fa-lock me-2"></i>Mật khẩu
            </label>
            <div class="input-group">
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required autocomplete="new-password"
                       placeholder="Tạo mật khẩu mạnh">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <small class="form-text text-muted">Sử dụng ít nhất 8 ký tự với chữ cái, số và ký hiệu</small>
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">
                <i class="fas fa-lock me-2"></i>Xác nhận mật khẩu
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="form-control @error('password_confirmation') is-invalid @enderror"
                   required autocomplete="new-password"
                   placeholder="Nhập lại mật khẩu">
            @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Account Type -->
        <div class="mb-4">
            <label for="account_type" class="form-label">
                <i class="fas fa-user-tag me-2"></i>Loại tài khoản
            </label>
            <select id="account_type" name="account_type"
                    class="form-select @error('account_type') is-invalid @enderror" required>
                <option value="">Chọn loại tài khoản của bạn</option>

                <optgroup label="🌟 Thành viên cộng đồng">
                    <option value="member" {{ old('account_type') == 'member' ? 'selected' : '' }}>
                        Thành viên - Tham gia thảo luận và chia sẻ kiến thức
                    </option>
                    <option value="student" {{ old('account_type') == 'student' ? 'selected' : '' }}>
                        Sinh viên - Học tập và nghiên cứu về cơ khí
                    </option>
                </optgroup>

                <optgroup label="🏢 Đối tác kinh doanh">
                    <option value="manufacturer" {{ old('account_type') == 'manufacturer' ? 'selected' : '' }}>
                        Nhà sản xuất - Sản xuất và cung cấp sản phẩm cơ khí
                    </option>
                    <option value="supplier" {{ old('account_type') == 'supplier' ? 'selected' : '' }}>
                        Nhà cung cấp - Phân phối thiết bị và vật tư cơ khí
                    </option>
                    <option value="brand" {{ old('account_type') == 'brand' ? 'selected' : '' }}>
                        Nhãn hàng - Quảng bá thương hiệu và sản phẩm
                    </option>
                </optgroup>
            </select>
            @error('account_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">Chọn loại tài khoản phù hợp với mục đích sử dụng. Bạn có thể thay đổi sau này.</small>
        </div>

        <!-- Terms and Privacy -->
        <div class="mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                <label class="form-check-label" for="terms">
                    Tôi đồng ý với <a href="#" class="text-primary">Điều khoản sử dụng</a> và
                    <a href="#" class="text-primary">Chính sách bảo mật</a>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-user-plus me-2"></i>Tạo tài khoản
            </button>
        </div>

        <!-- Login Link -->
        <div class="text-center">
            <span class="text-muted">Đã có tài khoản? </span>
            <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-medium">
                Đăng nhập ngay
            </a>
        </div>
    </form>
</x-auth-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }

    // Password strength indicator
    const password = document.getElementById('password');
    if (password) {
        password.addEventListener('input', function() {
            const value = this.value;
            const strength = calculatePasswordStrength(value);
            // You can add password strength indicator here
        });
    }
});

function calculatePasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    return strength;
}
</script>
@endpush
@endsection
