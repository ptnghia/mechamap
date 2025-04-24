@extends('layouts.auth')

@section('title', 'Xác minh email')

@section('content')
    <h2 class="auth-title text-center">{{ __('Verify Email') }}</h2>

    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    <p class="auth-subtitle text-center">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-4">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-4">
        <form method="POST" action="{{ route('verification.send') }}" class="w-100">
            @csrf

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-envelope me-2"></i>{{ __('Resend Verification Email') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="w-100">
            @csrf

            <button type="submit" class="btn btn-outline-secondary w-100">
                <i class="bi bi-box-arrow-right me-2"></i>{{ __('Log Out') }}
            </button>
        </form>
    </div>

    <div class="text-center mt-4">
        <p class="text-muted small">
            {{ __('If you\'re having trouble with email verification, please contact our support team.') }}
        </p>
    </div>
@endsection
