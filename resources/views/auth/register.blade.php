@extends('layouts.app')

@section('title', __('auth.register.title'))

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
                <i class="fas fa-user me-2"></i>{{ __('auth.full_name_label') }}
            </label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror"
                   required autofocus autocomplete="name"
                   placeholder="{{ __('auth.full_name_placeholder') }}">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Username -->
        <div class="mb-3">
            <label for="username" class="form-label">
                <i class="fas fa-at me-2"></i>{{ __('auth.username_label') }}
            </label>
            <input id="username" type="text" name="username" value="{{ old('username') }}"
                   class="form-control @error('username') is-invalid @enderror"
                   required autocomplete="username"
                   placeholder="{{ __('auth.username_placeholder') }}">
            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">{{ __('auth.username_help') }}</small>
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="fas fa-envelope me-2"></i>{{ __('auth.email_label') }}
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required autocomplete="email"
                   placeholder="{{ __('auth.email_placeholder') }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">
                <i class="fas fa-lock me-2"></i>{{ __('auth.password_label') }}
            </label>
            <div class="input-group">
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required autocomplete="new-password"
                       placeholder="{{ __('auth.password_placeholder') }}">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <small class="form-text text-muted">{{ __('auth.password_help') }}</small>
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">
                <i class="fas fa-lock me-2"></i>{{ __('auth.confirm_password_label') }}
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="form-control @error('password_confirmation') is-invalid @enderror"
                   required autocomplete="new-password"
                   placeholder="{{ __('auth.confirm_password_placeholder') }}">
            @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Account Type -->
        <div class="mb-4">
            <label for="account_type" class="form-label">
                <i class="fas fa-user-tag me-2"></i>{{ __('auth.account_type_label') }}
            </label>
            <select id="account_type" name="account_type"
                    class="form-select @error('account_type') is-invalid @enderror" required>
                <option value="">{{ __('auth.register.account_type_placeholder') }}</option>

                <optgroup label="🌟 {{ __('auth.register.community_member_title') }}">
                    <option value="member" {{ old('account_type') == 'member' ? 'selected' : '' }}>
                        {{ __('auth.register.member_role') }} - {{ __('auth.register.member_role_desc') }}
                    </option>

                </optgroup>

                <optgroup label="🏢 {{ __('auth.register.business_partner_title') }}">
                    <option value="manufacturer" {{ old('account_type') == 'manufacturer' ? 'selected' : '' }}>
                        {{ __('auth.register.manufacturer_role') }} - {{ __('auth.register.manufacturer_role_desc') }}
                    </option>
                    <option value="supplier" {{ old('account_type') == 'supplier' ? 'selected' : '' }}>
                        {{ __('auth.register.supplier_role') }} - {{ __('auth.register.supplier_role_desc') }}
                    </option>
                    <option value="brand" {{ old('account_type') == 'brand' ? 'selected' : '' }}>
                        {{ __('auth.register.brand_role') }} - {{ __('auth.register.brand_role_desc') }}
                    </option>
                </optgroup>
            </select>
            @error('account_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">{{ __('auth.register.account_type_help') }}</small>
        </div>

        <!-- Terms and Privacy -->
        <div class="mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                <label class="form-check-label" for="terms">
                    {!! __('auth.register.terms_agreement') !!}
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-user-plus me-2"></i>{{ __('auth.register.submit') }}
            </button>
        </div>

        <!-- Login Link -->
        <div class="text-center">
            <span class="text-muted">{{ __('auth.register.already_have_account') }} </span>
            <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-medium">
                {{ __('auth.register.sign_in') }}
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
