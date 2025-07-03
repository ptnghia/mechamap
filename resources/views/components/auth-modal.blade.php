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
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered {{ $sizeClass }}">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0">
                <div class="w-100 text-center">
                    <img src="{{ get_logo_url() }}" alt="{{ get_site_name() }}" class="mb-3" style="height: 40px;">
                    <h5 class="modal-title" id="{{ $id }}Label">{{ __('auth.welcome_back') }} {{ get_site_name() }}</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body px-4 pb-4">
                <!-- Tab Navigation -->
                <ul class="nav nav-pills nav-justified mb-4" id="{{ $id }}Tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="login-tab" data-bs-toggle="pill" data-bs-target="#login-panel" type="button" role="tab" aria-controls="login-panel" aria-selected="true">
                            <i class="fas fa-sign-in-alt me-2"></i>{{ __('auth.login') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="register-tab" data-bs-toggle="pill" data-bs-target="#register-panel" type="button" role="tab" aria-controls="register-panel" aria-selected="false">
                            <i class="fas fa-user-plus me-2"></i>{{ __('auth.register') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="forgot-tab" data-bs-toggle="pill" data-bs-target="#forgot-panel" type="button" role="tab" aria-controls="forgot-panel" aria-selected="false">
                            <i class="fas fa-key me-2"></i>{{ __('auth.forgot_password') }}
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="{{ $id }}TabContent">
                    <!-- Login Panel -->
                    <div class="tab-pane fade show active" id="login-panel" role="tabpanel" aria-labelledby="login-tab">
                        <form id="loginForm" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">{{ __('auth.email') }} {{ __('content.or') }} {{ __('auth.username') }}</label>
                                <input type="text" class="form-control" id="loginEmail" name="login" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">{{ __('auth.password_field') }}</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="loginPassword" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('loginPassword')">
                                        <i class="fas fa-eye" id="loginPasswordIcon"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                                <label class="form-check-label" for="rememberMe">{{ __('auth.remember_me') }}</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>{{ __('auth.login') }}
                                </button>
                            </div>
                        </form>

                        <!-- Social Login -->
                        <div class="text-center my-3">
                            <small class="text-muted">{{ __('content.or') }} {{ __('auth.social_login') }}</small>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-danger w-100">
                                    <i class="fab fa-google me-2"></i>Google
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-primary w-100">
                                    <i class="fab fa-facebook-f me-2"></i>Facebook
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Register Panel -->
                    <div class="tab-pane fade" id="register-panel" role="tabpanel" aria-labelledby="register-tab">
                        <form id="registerForm" method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="registerName" class="form-label">{{ __('auth.name') }}</label>
                                    <input type="text" class="form-control" id="registerName" name="name" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="registerUsername" class="form-label">{{ __('auth.username') }}</label>
                                    <input type="text" class="form-control" id="registerUsername" name="username" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="registerEmail" class="form-label">{{ __('auth.email') }}</label>
                                <input type="email" class="form-control" id="registerEmail" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="registerPassword" class="form-label">{{ __('auth.password_field') }}</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="registerPassword" name="password" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('registerPassword')">
                                            <i class="fas fa-eye" id="registerPasswordIcon"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="registerPasswordConfirm" class="form-label">{{ __('auth.confirm_password') }}</label>
                                    <input type="password" class="form-control" id="registerPasswordConfirm" name="password_confirmation" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                                <label class="form-check-label" for="agreeTerms">
                                    {{ __('auth.agree_terms') }} <a href="/terms" target="_blank">{{ __('auth.terms_of_service') }}</a> {{ __('content.and') }} <a href="/privacy" target="_blank">{{ __('auth.privacy_policy') }}</a>
                                </label>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-user-plus me-2"></i>{{ __('auth.register') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Forgot Password Panel -->
                    <div class="tab-pane fade" id="forgot-panel" role="tabpanel" aria-labelledby="forgot-tab">
                        <form id="forgotForm" method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="text-center mb-4">
                                <i class="fas fa-key fa-3x text-warning mb-3"></i>
                                <h6>{{ __('auth.forgot_password') }}</h6>
                                <p class="text-muted">{{ __('auth.check_email') }}</p>
                            </div>
                            <div class="mb-3">
                                <label for="forgotEmail" class="form-label">{{ __('auth.email') }}</label>
                                <input type="email" class="form-control" id="forgotEmail" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-paper-plane me-2"></i>{{ __('auth.send_reset_link') }}
                                </button>
                            </div>
                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-link btn-sm" onclick="switchToLogin()">
                                    <i class="fas fa-arrow-left me-1"></i>{{ __('auth.login_to_continue') }}
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
        const loginTabInstance = new bootstrap.Tab(loginTab);
        loginTabInstance.show();
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

<style>
/* Auth Modal Custom Styles */
#{{ $id }} .modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

#{{ $id }} .nav-pills .nav-link {
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

#{{ $id }} .nav-pills .nav-link.active {
    background: linear-gradient(45deg, #007bff, #0056b3);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
}

#{{ $id }} .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

#{{ $id }} .btn {
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

#{{ $id }} .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

#{{ $id }} .input-group .btn {
    border-radius: 0 25px 25px 0;
}

#{{ $id }} .input-group .form-control {
    border-radius: 25px 0 0 25px;
}
</style>
