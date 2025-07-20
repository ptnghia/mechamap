{{--
🧙‍♂️ Registration Wizard Component
Reusable wizard component với progress indicator, step navigation, và responsive design
--}}

@props([
    'currentStep' => 1,
    'totalSteps' => 2,
    'title' => 'Đăng ký tài khoản MechaMap',
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

<div class="registration-wizard-container">
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
                                {{ $stepLabels[$i] ?? "Bước $i" }}
                            </li>
                        @endfor
                    </ol>
                </nav>

                {{-- Error Messages --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Có lỗi xảy ra:</strong>
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

{{-- Auto-save indicator --}}
<div class="auto-save-indicator" id="autoSaveIndicator" style="display: none;">
    <div class="auto-save-content">
        <i class="fas fa-save me-2"></i>
        <span id="autoSaveText">{{ __('auth.register.auto_saving') }}</span>
    </div>
</div>

@push('styles')
<style>
.registration-wizard-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.wizard-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.wizard-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 2rem;
}

.wizard-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.wizard-subtitle {
    font-size: 1rem;
    opacity: 0.9;
}

.step-indicator {
    text-align: right;
}

.step-text {
    font-size: 0.875rem;
    opacity: 0.9;
    display: block;
}

.step-label {
    font-size: 1rem;
    font-weight: 600;
    margin-top: 0.25rem;
}

.progress-container {
    margin-top: 1.5rem;
}

.progress {
    height: 8px;
    border-radius: 4px;
    background-color: rgba(255, 255, 255, 0.2);
    overflow: hidden;
}

.progress-bar {
    background: linear-gradient(90deg, #28a745, #20c997);
    border-radius: 4px;
    transition: width 0.6s ease-in-out;
}

.progress-text {
    text-align: center;
}

.wizard-body {
    padding: 2rem;
    min-height: 400px;
}

.wizard-breadcrumb {
    background: none;
    padding: 0;
    margin: 0;
}

.wizard-breadcrumb .breadcrumb-item {
    font-weight: 500;
}

.wizard-breadcrumb .breadcrumb-item.active {
    color: #007bff;
}

.wizard-breadcrumb .breadcrumb-item.completed {
    color: #28a745;
}

.wizard-content {
    margin-top: 1rem;
}

.wizard-footer {
    background-color: #f8f9fa;
    padding: 1.5rem 2rem;
    border-top: 1px solid #e9ecef;
}

.btn-wizard-back,
.btn-wizard-next {
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease-out;
}

.btn-wizard-next:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.wizard-login-link {
    padding: 1rem 2rem;
    background-color: #ffffff;
    border-top: 1px solid #f1f3f4;
}

.auto-save-indicator {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
    background: #28a745;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .registration-wizard-container {
        margin: 1rem auto;
        padding: 0 0.5rem;
    }

    .wizard-header {
        padding: 1.5rem;
    }

    .wizard-title {
        font-size: 1.5rem;
    }

    .wizard-body {
        padding: 1.5rem;
    }

    .wizard-footer {
        padding: 1rem 1.5rem;
    }

    .wizard-footer .row {
        flex-direction: column;
        gap: 1rem;
    }

    .wizard-footer .col-md-6 {
        text-align: center !important;
    }

    .btn-wizard-back,
    .btn-wizard-next {
        width: 100%;
    }

    .step-indicator {
        text-align: left;
        margin-top: 1rem;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .wizard-card {
        background: #2d3748;
        border-color: #4a5568;
        color: #e2e8f0;
    }

    .wizard-footer {
        background-color: #1a202c;
        border-color: #4a5568;
    }

    .wizard-login-link {
        background-color: #2d3748;
        border-color: #4a5568;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-save functionality
    let autoSaveTimeout;
    const autoSaveIndicator = document.getElementById('autoSaveIndicator');
    const autoSaveText = document.getElementById('autoSaveText');

    function showAutoSaveIndicator(message = 'Đang lưu tự động...') {
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
