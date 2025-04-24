@extends('layouts.auth')

@section('title', 'Đặt lại mật khẩu')

@section('content')
    <h2 class="auth-title text-center">{{ __('Reset Password') }}</h2>

    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="auth-form">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" class="form-control @error('email') is-invalid @enderror" required readonly autocomplete="username">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text">This is the email address associated with your account</small>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">{{ __('New Password') }}</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password" placeholder="Enter your new password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text">Use at least 8 characters with letters, numbers and symbols</small>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required autocomplete="new-password" placeholder="Confirm your new password">
            @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
            <a href="{{ route('login') }}" class="auth-footer-link">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back to login') }}
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-key me-2"></i>{{ __('Reset Password') }}
            </button>
        </div>
    </form>
@endsection
