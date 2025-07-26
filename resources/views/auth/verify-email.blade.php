@extends('layouts.app')

@section('title', __('auth.verify_email.title'))

@section('full-width-content')
<x-auth-layout
    :title="__('auth.verify_email.title')"
    :subtitle="__('auth.verify_email.subtitle')"
    :show-social-login="false">

    <!-- Page Title -->
    <div class="text-center mb-4">
        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
            <i class="fas fa-envelope-open-text text-warning fs-2"></i>
        </div>
        <h2 class="xacminh_page_right_title mb-2">{{ __('auth.verify_email.check_email_title') }}</h2>
        <p class="text-muted">{{ __('auth.verify_email.check_email_subtitle') }}</p>
    </div>

    <!-- Status Messages -->
    @if (session('status'))
        <div class="alert alert-success mb-4">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('status') }}
        </div>
    @endif

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-info mb-4">
            <i class="fas fa-paper-plane me-2"></i>
            {{ __('auth.verify_email.verification_sent') }}
        </div>
    @endif

    <!-- Instructions -->
    <div class="mb-3 p-3 bg-light rounded-3 xacminh_page_right_box">
        <h6 class="fw-bold mb-3">
            <i class="fas fa-list-ol me-2 text-primary"></i>{{ __('auth.verify_email.instructions_title') }}
        </h6>
        <ol class="mb-0">
            <li class="mb-2">{{ __('auth.verify_email.instruction_1') }}</li>
            <li class="mb-2">{!! __('auth.verify_email.instruction_2') !!}</li>
            <li class="mb-2">{!! __('auth.verify_email.instruction_3') !!}</li>
            <li class="mb-0">{{ __('auth.verify_email.instruction_4') }}</li>
        </ol>
    </div>

    <!-- Action Buttons -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-main active w-100">
                    <i class="fas fa-redo me-2"></i>{{ __('auth.verify_email.resend_button') }}
                </button>
            </form>
        </div>
        <div class="col-md-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-main w-100">
                    <i class="fas fa-sign-out-alt me-2"></i>{{ __('auth.verify_email.logout_button') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Help Section -->
    <div class="mt-4 p-4 bg-light rounded-3 xacminh_page_right_box">
        <h6 class="fw-bold mb-3">
            <i class="fas fa-question-circle me-2 text-primary"></i>{{ __('auth.verify_email.help_title') }}
        </h6>
        <div class="row g-3">
            <div class="col-12">
                <div class="d-flex align-items-start">
                    <i class="fas fa-search text-muted me-3 mt-1"></i>
                    <div>
                        <strong>{{ __('auth.verify_email.help_not_found_title') }}</strong>
                        <p class="mb-0 small text-muted">{{ __('auth.verify_email.help_not_found_desc') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex align-items-start">
                    <i class="fas fa-clock text-muted me-3 mt-1"></i>
                    <div>
                        <strong>{{ __('auth.verify_email.help_not_received_title') }}</strong>
                        <p class="mb-0 small text-muted">{{ __('auth.verify_email.help_not_received_desc') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex align-items-start">
                    <i class="fas fa-edit text-muted me-3 mt-1"></i>
                    <div>
                        <strong>{{ __('auth.verify_email.help_wrong_email_title') }}</strong>
                        <p class="mb-0 small text-muted">
                            {!! __('auth.verify_email.help_wrong_email_desc', ['register_url' => route('register')]) !!}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex align-items-start">
                    <i class="fas fa-headset text-muted me-3 mt-1"></i>
                    <div>
                        <strong>{{ __('auth.verify_email.help_support_title') }}</strong>
                        <p class="mb-0 small text-muted">
                            {!! __('auth.verify_email.help_support_desc') !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-refresh notice -->
    <div class="text-center mt-4">
        <small class="text-muted">
            <i class="fas fa-sync-alt me-1"></i>
            {{ __('auth.verify_email.auto_refresh_notice') }}
        </small>
    </div>
</x-auth-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-check verification status every 10 seconds
    let checkInterval;

    function checkVerificationStatus() {
        fetch('{{ route("verification.check") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.verified) {
                clearInterval(checkInterval);
                // Show success message and redirect
                showSuccessAndRedirect();
            }
        })
        .catch(error => {
            console.log('Verification check failed:', error);
        });
    }

    function showSuccessAndRedirect() {
        // Create success overlay
        const overlay = document.createElement('div');
        overlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
        overlay.style.backgroundColor = 'rgba(0,0,0,0.8)';
        overlay.style.zIndex = '9999';

        overlay.innerHTML = `
            <div class="text-center text-white">
                <div class="mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h3>{{ __('auth.verify_email.verified_title') }}</h3>
                <p>{{ __('auth.verify_email.redirecting') }}</p>
                <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        // Redirect after 2 seconds
        setTimeout(() => {
            window.location.href = '{{ route("home") }}';
        }, 2000);
    }

    // Start checking every 10 seconds
    checkInterval = setInterval(checkVerificationStatus, 10000);

    // Form submission feedback
    const resendForm = document.querySelector('form[action="{{ route("verification.send") }}"]');
    if (resendForm) {
        resendForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __('auth.verify_email.sending_button') }}';
                submitBtn.disabled = true;

                // Re-enable after 5 seconds
                setTimeout(() => {
                    submitBtn.innerHTML = '<i class="fas fa-redo me-2"></i>{{ __('auth.verify_email.resend_button') }}';
                    submitBtn.disabled = false;
                }, 5000);
            }
        });
    }

    // Cleanup interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        if (checkInterval) {
            clearInterval(checkInterval);
        }
    });
});
</script>
@endpush
@endsection
