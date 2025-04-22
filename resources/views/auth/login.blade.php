@extends('layouts.guest')

@section('title', 'Đăng nhập')

@section('content')
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mb-4">
            {{ session('error') }}
        </div>
    @endif

    <h2 class="text-center mb-4">{{ __('Login') }}</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username / Email Address -->
        <div class="mb-3">
            <label for="login" class="form-label">{{ __('Email or Username') }}</label>
            <input id="login" type="text" name="login" value="{{ old('login') }}" class="form-control @error('login') is-invalid @enderror" required autofocus autocomplete="username">
            @error('login')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-3 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label" for="remember_me">{{ __('Remember me') }}</label>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            @if (Route::has('password.request'))
                <a class="text-decoration-none" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <button type="submit" class="btn btn-primary">
                {{ __('Log in') }}
            </button>
        </div>
    </form>

    <!-- Social Login -->
    <div class="my-4">
        <div class="d-flex align-items-center my-3">
            <hr class="flex-grow-1">
            <span class="mx-3 text-muted">{{ __('Or continue with') }}</span>
            <hr class="flex-grow-1">
        </div>

        <div class="d-grid gap-2">
            <a href="{{ route('auth.socialite', 'google') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center">
                <svg class="me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 488 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z"/></svg>
                {{ __('Google') }}
            </a>
            <a href="{{ route('auth.socialite', 'facebook') }}" class="btn btn-primary d-flex align-items-center justify-content-center">
                <svg class="me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z"/></svg>
                {{ __('Facebook') }}
            </a>
        </div>
    </div>

    <div class="text-center mt-4">
        <p>{{ __('Don\'t have an account?') }} <a href="{{ route('register') }}" class="text-decoration-none">{{ __('Register') }}</a></p>
    </div>
@endsection
