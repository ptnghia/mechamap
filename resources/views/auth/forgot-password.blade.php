@extends('layouts.auth')

@section('title', 'Quên mật khẩu')

@section('content')
    <h2 class="auth-title text-center">{{ __('Forgot Password') }}</h2>

    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    <p class="auth-subtitle text-center">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </p>

    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus placeholder="Enter your email address">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
            <a href="{{ route('login') }}" class="auth-footer-link">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back to login') }}
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-envelope me-2"></i>{{ __('Send Reset Link') }}
            </button>
        </div>
    </form>
@endsection
