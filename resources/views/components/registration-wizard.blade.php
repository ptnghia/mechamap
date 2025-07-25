{{--
🧙‍♂️ Registration Wizard Component
Reusable wizard component với progress indicator, step navigation, và responsive design
--}}
@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/login.css') }}">
@endpush
@props([
    'currentStep' => 1,
    'totalSteps' => 2,
    'title' => t_ui('auth/register_mechamap_account'),
    'subtitle' => null,
    'progress' => null,
    'showBackButton' => true,
    'showNextButton' => true,
    'nextButtonText' => 'Tiếp tục',
    'backButtonText' => 'Quay lại',
    'nextButtonDisabled' => false,
    'formId' => 'wizardForm',
    'sessionData' => []
])

@php
    $calculatedProgress = $progress ?? (($currentStep - 1) / $totalSteps) * 100;
    $stepLabels = [
        1 => __('auth.register.step1_label'),
        2 => __('auth.register.step2_label')
    ];
@endphp
<div class="min-vh-100 d-flex align-items-center bg-light my-4">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-8 col-xl-8">
                <div class="wizard-card shadow-lg">
                    {{-- Wizard Header --}}
                    <div class="wizard-header">
                        <div class="container-fluid">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h1 class="wizard-title">
                                        <i class="fas fa-user-plus me-2"></i>
                                        {{ $title }}
                                    </h1>
                                    @if($subtitle)
                                        <p class="wizard-subtitle mb-0">{{ $subtitle }}</p>
                                    @endif
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="step-indicator">
                                        <span class="step-text">{{ __('auth.register.step_indicator', ['current' => $currentStep, 'total' => $totalSteps]) }}</span>
                                        <div class="step-label">{{ $stepLabels[$currentStep] ?? '' }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="progress-container mt-3">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                        role="progressbar"
                                        style="width: {{ $calculatedProgress }}%"
                                        aria-valuenow="{{ $calculatedProgress }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="progress-text mt-2">
                                    <small class="text-muted">{{ __('auth.register.progress_complete', ['percent' => round($calculatedProgress)]) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Wizard Body --}}
                    <div class="wizard-body">
                        <div class="container-fluid">
                            {{-- Step Navigation Breadcrumb --}}
                            <nav aria-label="Wizard steps" class="mb-4">
                                <ol class="breadcrumb wizard-breadcrumb">
                                    @for($i = 1; $i <= $totalSteps; $i++)
                                        <li class="breadcrumb-item {{ $i == $currentStep ? 'active' : '' }} {{ $i < $currentStep ? 'completed' : '' }}">
                                            @if($i < $currentStep)
                                                <i class="fas fa-check-circle text-success me-1"></i>
                                            @elseif($i == $currentStep)
                                                <i class="fas fa-circle text-primary me-1"></i>
                                            @else
                                                <i class="far fa-circle text-muted me-1"></i>
                                            @endif
                                            {{ $stepLabels[$i] ?? __('auth.register.step_default', ['number' => $i]) }}
                                        </li>
                                    @endfor
                                </ol>
                            </nav>

                            {{-- Error Messages --}}
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>{{ __('auth.register.errors_occurred') }}:</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- Success Messages --}}
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- Warning Messages --}}
                            @if(session('warning'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ session('warning') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- Main Content Slot --}}
                            <div class="wizard-content">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>

                    {{-- Wizard Footer --}}
                    <div class="wizard-footer">
                        <div class="container-fluid">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    @if($showBackButton && $currentStep > 1)
                                        <button type="button" class="btn btn-outline-secondary btn-wizard-back" id="wizardBackBtn">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            {{ $backButtonText }}
                                        </button>
                                    @endif
                                </div>
                                <div class="col-md-6 text-end">
                                    @if($showNextButton)
                                        <button type="submit"
                                                form="{{ $formId }}"
                                                class="btn btn-primary btn-wizard-next"
                                                id="wizardNextBtn"
                                                {{ $nextButtonDisabled ? 'disabled' : '' }}>
                                            {{ $nextButtonText }}
                                            <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Additional Info --}}
                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        {{ __('auth.register.security_note') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Login Link --}}
                    <div class="wizard-login-link">
                        <div class="container-fluid text-center">
                            <p class="mb-0">
                                <span class="text-muted">{{ __('auth.register.already_have_account') }} </span>
                                <a href="{{ route('login') }}" class="text-decoration-none fw-medium">
                                    {{ __('auth.register.login_now') }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Auto-save indicator --}}
<div class="auto-save-indicator" id="autoSaveIndicator" style="display: none;">
    <div class="auto-save-content">
        <i class="fas fa-save me-2"></i>
        <span id="autoSaveText">{{ __('auth.register.auto_saving') }}</span>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-save functionality
    let autoSaveTimeout;
    const autoSaveIndicator = document.getElementById('autoSaveIndicator');
    const autoSaveText = document.getElementById('autoSaveText');

    function showAutoSaveIndicator(message = '{{ __('auth.register.auto_saving') }}') {
        autoSaveText.textContent = message;
        autoSaveIndicator.style.display = 'block';

        setTimeout(() => {
            autoSaveIndicator.style.display = 'none';
        }, 2000);
    }

    // Back button functionality
    const backBtn = document.getElementById('wizardBackBtn');
    if (backBtn) {
        backBtn.addEventListener('click', function() {
            window.history.back();
        });
    }

    // Form validation state management
    const nextBtn = document.getElementById('wizardNextBtn');
    const form = document.getElementById('{{ $formId }}');

    if (form && nextBtn) {
        // Enable/disable next button based on form validity
        function updateNextButtonState() {
            const isValid = form.checkValidity();
            nextBtn.disabled = !isValid;
        }

        // Check form validity on input changes
        form.addEventListener('input', updateNextButtonState);
        form.addEventListener('change', updateNextButtonState);

        // Initial check
        updateNextButtonState();
    }
});
</script>
@endpush
