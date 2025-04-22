@extends('layouts.guest')

@section('title', 'Page Title')

@section('content')
    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif<h2 class="text-center mb-4">{{ __('Forgot Password') }}</h2>

    <div class="alert alert-info mb-4">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus >
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('login') }}" class="text-decoration-none">
                {{ __('Back to login') }}
            </a>

            <button type="submit" class="btn btn-primary">{{ __('Email Password Reset Link') }}</button>
        </div>
    </form>
@endsection
