@extends('layouts.app')

@section('title', __('auth.reset_password.title'))

@section('content')
<x-auth-layout
    title="{{ __('auth.reset_password.title') }}"
    subtitle="{{ __('auth.reset_password.subtitle') }}"
    :show-social-login="false">

    <!-- Page Title -->
    <div class="text-center mb-4">
        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
            <i class="fas fa-shield-alt text-success fs-2"></i>
        </div>
        <h2 class="fw-bold text-dark mb-2">{{ __('auth.reset_password.heading') }}</h2>
        <p class="text-muted">{{ __('auth.reset_password.description') }}</p>
    </div>

    <!-- Status Messages -->
    @if (session('status'))
        <div class="alert alert-success mb-4">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('status') }}
        </div>
    @endif

    <!-- Reset Form -->
    <form method="POST" action="{{ route('password.store') }}" class="needs-validation" novalidate>
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address (readonly) -->
        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="fas fa-envelope me-2"></i>Địa chỉ email
            </label>
            <input id="email" type="email" name="email"
                   value="{{ old('email', $request->email) }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required readonly autocomplete="username">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">
                <i class="fas fa-lock me-1"></i>
                Đây là địa chỉ email được liên kết với tài khoản của bạn
            </small>
        </div>

        <!-- New Password -->
        <div class="mb-3">
            <label for="password" class="form-label">
                <i class="fas fa-key me-2"></i>{{ __('auth.reset_password.new_password') }}
            </label>
            <div class="input-group">
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required autocomplete="new-password"
                       placeholder="{{ __('auth.reset_password.password_placeholder') }}">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <small class="form-text text-muted">{{ __('auth.reset_password.password_hint') }}</small>

            <!-- Password Strength Indicator -->
            <div class="mt-2">
                <div class="progress" style="height: 4px;">
                    <div id="passwordStrength" class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
                <small id="passwordStrengthText" class="form-text text-muted"></small>
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">
                <i class="fas fa-check-double me-2"></i>{{ __('auth.reset_password.confirm_password') }}
            </label>
            <div class="input-group">
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="form-control @error('password_confirmation') is-invalid @enderror"
                       required autocomplete="new-password"
                       placeholder="{{ __('auth.reset_password.confirm_placeholder') }}">
                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                    <i class="fas fa-eye"></i>
                </button>
                @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div id="passwordMatch" class="mt-1"></div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid mb-4">
            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                <i class="fas fa-shield-alt me-2"></i>{{ __('auth.reset_password.update_password') }}
            </button>
        </div>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-muted text-decoration-none">
                <i class="fas fa-arrow-left me-2"></i>Quay lại đăng nhập
            </a>
        </div>
    </form>

    <!-- Security Tips -->
    <div class="mt-5 p-4 bg-light rounded-3">
        <h6 class="fw-bold mb-3">
            <i class="fas fa-lightbulb me-2 text-warning"></i>Mẹo bảo mật
        </h6>
        <div class="row g-3">
            <div class="col-12">
                <div class="d-flex align-items-start">
                    <i class="fas fa-check text-success me-3 mt-1"></i>
                    <div>
                        <strong>{{ __('auth.reset_password.tips.strong_title') }}</strong>
                        <p class="mb-0 small text-muted">{{ __('auth.reset_password.tips.strong_desc') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex align-items-start">
                    <i class="fas fa-check text-success me-3 mt-1"></i>
                    <div>
                        <strong>{{ __('auth.reset_password.tips.avoid_personal_title') }}</strong>
                        <p class="mb-0 small text-muted">{{ __('auth.reset_password.tips.avoid_personal_desc') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex align-items-start">
                    <i class="fas fa-check text-success me-3 mt-1"></i>
                    <div>
                        <strong>{{ __('auth.reset_password.tips.unique_title') }}</strong>
                        <p class="mb-0 small text-muted">{{ __('auth.reset_password.tips.unique_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');
    const passwordMatch = document.getElementById('passwordMatch');
    const submitBtn = document.getElementById('submitBtn');

    // Toggle password visibility
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            togglePasswordVisibility(password, this);
        });
    }

    if (togglePasswordConfirm) {
        togglePasswordConfirm.addEventListener('click', function() {
            togglePasswordVisibility(passwordConfirm, this);
        });
    }

    // Password strength checker
    if (password) {
        password.addEventListener('input', function() {
            const strength = calculatePasswordStrength(this.value);
            updatePasswordStrength(strength);
            checkPasswordMatch();
        });
    }

    // Password match checker
    if (passwordConfirm) {
        passwordConfirm.addEventListener('input', checkPasswordMatch);
    }

    function togglePasswordVisibility(input, button) {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);

        const icon = button.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }

    function calculatePasswordStrength(password) {
        let strength = 0;
        const checks = [
            password.length >= 8,
            /[a-z]/.test(password),
            /[A-Z]/.test(password),
            /[0-9]/.test(password),
            /[^A-Za-z0-9]/.test(password)
        ];

        strength = checks.filter(Boolean).length;
        return strength;
    }

    function updatePasswordStrength(strength) {
        const percentage = (strength / 5) * 100;
        const colors = ['danger', 'danger', 'warning', 'info', 'success'];
        const texts = ['Rất yếu', 'Yếu', 'Trung bình', 'Mạnh', 'Rất mạnh'];

        strengthBar.style.width = percentage + '%';
        strengthBar.className = 'progress-bar bg-' + (colors[strength - 1] || 'danger');
        strengthText.textContent = strength > 0 ? 'Độ mạnh: ' + (texts[strength - 1] || 'Rất yếu') : '';
    }

    function checkPasswordMatch() {
        if (password.value && passwordConfirm.value) {
            const isMatch = password.value === passwordConfirm.value;
            passwordMatch.innerHTML = isMatch
                ? '<small class="text-success"><i class="fas fa-check me-1"></i>{!! addslashes(__('auth.reset_password.password_match')) !!}</small>'
                : '<small class="text-danger"><i class="fas fa-times me-1"></i>{!! addslashes(__('auth.reset_password.password_mismatch')) !!}</small>';

            submitBtn.disabled = !isMatch;
        } else {
            passwordMatch.innerHTML = '';
            submitBtn.disabled = false;
        }
    }

    // Form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang cập nhật...';
            submitBtn.disabled = true;
        });
    }
});
</script>
@endpush
@endsection
