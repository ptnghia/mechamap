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
                <!-- Tab Navigation -->
                <div class="d-flex gap-2 mb-4" id="{{ $id }}Tabs" role="tablist">
                    <button class="btn btn-auth-tab active flex-fill py-3" id="login-tab" data-bs-toggle="pill" data-bs-target="#login-panel" type="button" role="tab" aria-controls="login-panel" aria-selected="true" style="background-color: #8B7355; color: white; border: none; border-radius: 8px; font-weight: 600;">
                        {{ __('messages.login') }}
                    </button>
                    <button class="btn btn-auth-tab flex-fill py-3" id="register-tab" data-bs-toggle="pill" data-bs-target="#register-panel" type="button" role="tab" aria-controls="register-panel" aria-selected="false" style="background-color: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6; border-radius: 8px; font-weight: 600;">
                        {{ __('messages.register') }}
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="tab-content" id="{{ $id }}TabContent">
                    <!-- Login Panel -->
                    <div class="tab-pane fade show active" id="login-panel" role="tabpanel" aria-labelledby="login-tab">
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
                        <div class="row g-2">
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
                    </div>

                    <!-- Register Panel -->
                    <div class="tab-pane fade" id="register-panel" role="tabpanel" aria-labelledby="register-tab">
                        <form id="registerForm" method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="mb-3">
                                <div class="input-group" style="border-radius: 8px; overflow: hidden;">
                                    <span class="input-group-text bg-light border-0" style="color: #6c757d;">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control border-0 bg-light" id="registerName" name="name" placeholder="{{ __('messages.full_name') }}" required style="padding: 12px;">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group" style="border-radius: 8px; overflow: hidden;">
                                    <span class="input-group-text bg-light border-0" style="color: #6c757d;">
                                        <i class="fas fa-at"></i>
                                    </span>
                                    <input type="text" class="form-control border-0 bg-light" id="registerUsername" name="username" placeholder="{{ __('messages.username') }}" required style="padding: 12px;">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group" style="border-radius: 8px; overflow: hidden;">
                                    <span class="input-group-text bg-light border-0" style="color: #6c757d;">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control border-0 bg-light" id="registerEmail" name="email" placeholder="{{ __('messages.email') }}" required style="padding: 12px;">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group" style="border-radius: 8px; overflow: hidden;">
                                    <span class="input-group-text bg-light border-0" style="color: #6c757d;">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control border-0 bg-light" id="registerPassword" name="password" placeholder="{{ __('messages.password') }}" required style="padding: 12px;">
                                    <button class="btn bg-light border-0" type="button" onclick="togglePassword('registerPassword')" style="color: #6c757d;">
                                        <i class="fas fa-eye" id="registerPasswordIcon"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group" style="border-radius: 8px; overflow: hidden;">
                                    <span class="input-group-text bg-light border-0" style="color: #6c757d;">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control border-0 bg-light" id="registerPasswordConfirm" name="password_confirmation" placeholder="{{ __('messages.confirm_password') }}" required style="padding: 12px;">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                                <label class="form-check-label text-muted" for="agreeTerms" style="font-size: 14px;">
                                    {{ __('messages.agree_terms') }} <a href="/terms" target="_blank" style="color: #8B7355;">{{ __('messages.terms_of_service') }}</a> {{ __('messages.and') }} <a href="/privacy" target="_blank" style="color: #8B7355;">{{ __('messages.privacy_policy') }}</a>
                                </label>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn py-3" style="background-color: #8B7355; color: white; border: none; border-radius: 8px; font-weight: 600;">
                                    {{ __('messages.register') }}
                                </button>
                            </div>
                        </form>

                        <!-- Divider -->
                        <div class="text-center mb-4">
                            <small class="text-muted">{{ __('messages.or_register_with') }}</small>
                        </div>

                        <!-- Social Login -->
                        <div class="row g-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2" style="border-radius: 8px;">
                                    <i class="fab fa-google me-2"></i>{{ __('messages.register_with_google') }}
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2" style="border-radius: 8px;">
                                    <i class="fab fa-facebook-f me-2"></i>{{ __('messages.register_with_facebook') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Forgot Password Panel -->
                    <div class="tab-pane fade" id="forgot-panel" role="tabpanel" aria-labelledby="forgot-tab">
                        <form id="forgotForm" method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="text-center mb-4">
                                <h5 class="mb-3">{{ __('messages.forgot_password') }}</h5>
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
    // Tab switching functionality
    const authTabs = document.querySelectorAll('.btn-auth-tab');
    authTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            authTabs.forEach(t => {
                t.classList.remove('active');
                t.style.backgroundColor = '#f8f9fa';
                t.style.color = '#6c757d';
                t.style.border = '1px solid #dee2e6';
            });

            // Add active class to clicked tab
            this.classList.add('active');
            this.style.backgroundColor = '#8B7355';
            this.style.color = 'white';
            this.style.border = 'none';
        });
    });

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

    // Switch to login tab
    window.switchToLogin = function() {
        const loginTab = document.getElementById('login-tab');
        const registerTab = document.getElementById('register-tab');

        if (loginTab && registerTab) {
            // Show login panel
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
            document.getElementById('login-panel').classList.add('show', 'active');

            // Update tab styling
            registerTab.classList.remove('active');
            registerTab.style.backgroundColor = '#f8f9fa';
            registerTab.style.color = '#6c757d';
            registerTab.style.border = '1px solid #dee2e6';

            loginTab.classList.add('active');
            loginTab.style.backgroundColor = '#8B7355';
            loginTab.style.color = 'white';
            loginTab.style.border = 'none';
        }
    };

    // Switch to forgot password
    window.switchToForgot = function() {
        const forgotPanel = document.getElementById('forgot-panel');
        if (forgotPanel) {
            // Hide all panels
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
            // Show forgot panel
            forgotPanel.classList.add('show', 'active');
        }
    };

    // Form submissions with AJAX
    const forms = ['loginForm', 'registerForm', 'forgotForm'];

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

// Global functions to open specific tabs
window.openLoginModal = function() {
    const modal = new bootstrap.Modal(document.getElementById('{{ $id }}'));
    const loginTab = new bootstrap.Tab(document.getElementById('login-tab'));
    loginTab.show();
    modal.show();
};

window.openRegisterModal = function() {
    const modal = new bootstrap.Modal(document.getElementById('{{ $id }}'));
    const registerTab = new bootstrap.Tab(document.getElementById('register-tab'));
    registerTab.show();
    modal.show();
};

window.openForgotPasswordModal = function() {
    const modal = new bootstrap.Modal(document.getElementById('{{ $id }}'));
    const forgotTab = new bootstrap.Tab(document.getElementById('forgot-tab'));
    forgotTab.show();
    modal.show();
};
</script>
