@props([
    'id' => 'authModal',
    'size' => 'md'
])

@php
$sizeClass = [
    'sm' => 'modal-sm',
    'md' => '',
    'lg' => 'modal-lg',
    'xl' => 'modal-xl'
][$size];
@endphp

<!-- Authentication Modal -->
<div class="modal fade auth-modal" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered {{ $sizeClass }}">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0 position-relative">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 10;"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body px-4 pt-0 pb-4">
                <!-- Modal Title -->
                <div class="text-center mb-4">
                    <h4 class="mb-2" style="color: #8B7355; font-weight: 600;">{{ __('messages.login') }}</h4>
                    <p class="text-muted mb-0">{{ __('messages.welcome_back') }}</p>
                </div>

                <!-- Login Content -->
                <div id="login-panel">
                    <!-- Login Form -->
                        <form id="loginForm" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <div class="input-group" style="border-radius: 8px; overflow: hidden;">
                                    <span class="input-group-text bg-light border-0" style="color: #6c757d;">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control border-0 bg-light" id="loginEmail" name="login" placeholder="{{ __('messages.email_or_username') }}" required style="padding: 12px;">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group" style="border-radius: 8px; overflow: hidden;">
                                    <span class="input-group-text bg-light border-0" style="color: #6c757d;">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control border-0 bg-light" id="loginPassword" name="password" placeholder="{{ __('messages.password') }}" required style="padding: 12px;">
                                    <button class="btn bg-light border-0" type="button" onclick="togglePassword('loginPassword')" style="color: #6c757d;">
                                        <i class="fas fa-eye" id="loginPasswordIcon"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                                    <label class="form-check-label text-muted" for="rememberMe">{{ __('messages.remember_login') }}</label>
                                </div>
                                <a href="#" class="text-decoration-none" style="color: #8B7355; font-size: 14px;" onclick="switchToForgot()">{{ __('messages.forgot_password') }}?</a>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                                    <label class="form-check-label text-muted" for="rememberMe" style="font-size: 14px;">
                                        {{ __('messages.remember_me') }}
                                    </label>
                                </div>
                                <button type="button" class="btn btn-link p-0 text-muted" onclick="switchToForgotPassword()" style="text-decoration: none; font-size: 14px;">
                                    {{ __('messages.forgot_password') }}
                                </button>
                            </div>
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn py-3" style="background-color: #8B7355; color: white; border: none; border-radius: 8px; font-weight: 600;">
                                    {{ __('messages.login') }}
                                </button>
                            </div>
                        </form>

                        <!-- Divider -->
                        <div class="text-center mb-4">
                            <small class="text-muted">{{ __('messages.or_login_with') }}</small>
                        </div>

                        <!-- Social Login -->
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2" style="border-radius: 8px;">
                                    <i class="fab fa-google me-2"></i>{{ __('messages.login_with_google') }}
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2" style="border-radius: 8px;">
                                    <i class="fab fa-facebook-f me-2"></i>{{ __('messages.login_with_facebook') }}
                                </button>
                            </div>
                        </div>

                        <!-- Registration Link -->
                        <div class="text-center">
                            <p class="text-muted mb-2">{{ __('messages.dont_have_account') }}</p>
                            <a href="/register" class="btn btn-outline-primary w-100 py-2" style="border-radius: 8px; border-color: #8B7355; color: #8B7355;">
                                <i class="fas fa-user-plus me-2"></i>{{ __('messages.create_business_account') }}
                            </a>
                        </div>
                    </div>

                    <!-- Forgot Password Panel (Hidden by default) -->
                    <div id="forgot-panel" style="display: none;">
                        <form id="forgotForm" method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="text-center mb-4">
                                <h5 class="mb-3" style="color: #8B7355;">{{ __('messages.forgot_password') }}</h5>
                                <p class="text-muted">{{ __('messages.forgot_password_description') }}</p>
                            </div>
                            <div class="mb-3">
                                <div class="input-group" style="border-radius: 8px; overflow: hidden;">
                                    <span class="input-group-text bg-light border-0" style="color: #6c757d;">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control border-0 bg-light" id="forgotEmail" name="email" placeholder="{{ __('messages.email') }}" required style="padding: 12px;">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn py-3" style="background-color: #8B7355; color: white; border: none; border-radius: 8px; font-weight: 600;">
                                    {{ __('messages.send_reset_link') }}
                                </button>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-link text-muted" onclick="switchToLogin()" style="text-decoration: none; font-size: 14px;">
                                    <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back_to_login') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Auth Modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Panel switching functionality
    window.switchToForgotPassword = function() {
        document.getElementById('login-panel').style.display = 'none';
        document.getElementById('forgot-panel').style.display = 'block';

        // Update modal title
        const modalTitle = document.querySelector('#authModal .modal-body h4');
        if (modalTitle) {
            modalTitle.textContent = '{{ __("messages.forgot_password") }}';
        }
    };

    window.switchToLogin = function() {
        document.getElementById('login-panel').style.display = 'block';
        document.getElementById('forgot-panel').style.display = 'none';

        // Update modal title
        const modalTitle = document.querySelector('#authModal .modal-body h4');
        if (modalTitle) {
            modalTitle.textContent = '{{ __("messages.login") }}';
        }
    };

    // Password toggle functionality
    window.togglePassword = function(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(inputId + 'Icon');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    };

    // Reset modal to login state when opened
    window.resetAuthModal = function() {
        document.getElementById('login-panel').style.display = 'block';
        document.getElementById('forgot-panel').style.display = 'none';

        // Reset forms
        document.getElementById('loginForm').reset();
        document.getElementById('forgotForm').reset();

        // Clear any error messages
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));

        // Update modal title
        const modalTitle = document.querySelector('#authModal .modal-body h4');
        if (modalTitle) {
            modalTitle.textContent = '{{ __("messages.login") }}';
        }
    };

    // Form submissions with AJAX
    const forms = ['loginForm', 'forgotForm'];

    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                submitAuthForm(this);
            });
        }
    });

    // Submit form with AJAX
    function submitAuthForm(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __("content.processing") }}';

        // Clear previous errors
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success - redirect or close modal
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        const feedback = input.parentNode.querySelector('.invalid-feedback');

                        if (input && feedback) {
                            input.classList.add('is-invalid');
                            feedback.textContent = data.errors[field][0];
                        }
                    });
                }

                if (data.message) {
                    showAlert(data.message, 'danger');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('{{ __("content.error_occurred") }}', 'danger');
        })
        .finally(() => {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    }

    // Show alert message
    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        const modal = document.querySelector('#{{ $id }} .modal-body');
        modal.insertBefore(alertDiv, modal.firstChild);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});

// Global functions to open modal
window.openLoginModal = function() {
    resetAuthModal();
    const modal = new bootstrap.Modal(document.getElementById('{{ $id }}'));
    modal.show();
};

window.openForgotPasswordModal = function() {
    resetAuthModal();
    switchToForgotPassword();
    const modal = new bootstrap.Modal(document.getElementById('{{ $id }}'));
    modal.show();
};

// Add event listener to reset modal when opened
document.getElementById('{{ $id }}').addEventListener('shown.bs.modal', function () {
    resetAuthModal();
});
</script>
