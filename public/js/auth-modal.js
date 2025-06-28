/**
 * Auth Modal Functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the auth modal
    initAuthModal();
});

/**
 * Initialize the auth modal
 */
function initAuthModal() {
    // Add event listener to login/join link
    const loginLinks = document.querySelectorAll('.login-link');
    loginLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            showAuthModal();
        });
    });

    // Handle form submissions
    setupFormSubmissions();

    // Handle tab switching
    setupTabSwitching();
}

/**
 * Show the auth modal
 */
function showAuthModal(activeTab = 'login') {
    // Get the modal element
    const modal = document.getElementById('authModal');

    // If modal doesn't exist, create it
    if (!modal) {
        createAuthModal();
        return;
    }

    // Show the modal
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();

    // Activate the specified tab
    activateTab(activeTab);
}

/**
 * Create the auth modal dynamically
 */
function createAuthModal() {
    // Create modal HTML
    const modalHTML = `
    <div class="modal fade auth-modal" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-pills nav-fill" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-content" type="button" role="tab" aria-controls="login-content" aria-selected="true">Đăng nhập</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-content" type="button" role="tab" aria-controls="register-content" aria-selected="false">Đăng ký</button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content" id="authTabsContent">
                        <!-- Login tab -->
                        <div class="tab-pane fade show active" id="login-content" role="tabpanel" aria-labelledby="login-tab">
                            <div id="login-alert" class="alert alert-danger d-none" role="alert"></div>

                            <form id="loginForm" method="POST">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">

                                <div class="mb-4 position-relative">
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control ps-4" id="login" name="login" placeholder="Email hoặc Username" required>
                                </div>

                                <div class="mb-4 position-relative">
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control ps-4" id="password" name="password" placeholder="Mật khẩu" required>
                                    <span class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted password-toggle" style="cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>

                                <div class="mb-4 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                                    </div>
                                    <a href="#" class="forgot-password" id="forgotPasswordLink">Quên mật khẩu?</a>
                                </div>

                                <button type="submit" class="btn btn-primary">Đăng nhập</button>
                            </form>

                            <div class="divider">
                                <span>hoặc đăng nhập với</span>
                            </div>

                            <div class="social-login row g-2">
                                <div class="col-6">
                                <a href="/auth/google" class="btn btn-google">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="bi bi-google" viewBox="0 0 16 16">
                                        <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z" fill="#4285F4"/>
                                        <path d="M8 6.5v3.08h4.537c-.18.645-.451 1.189-.814 1.613-.401.472-.997.85-1.714 1.093" stroke="#4285F4" fill="none" stroke-width="0.2"/>
                                        <path d="M10.008 12.286c1.071-.362 1.94-.97 2.568-1.758.118-.148.226-.3.324-.456" stroke="#4285F4" fill="none" stroke-width="0.2"/>
                                        <path d="M8 0a7.689 7.689 0 0 0-5.352 2.082a8 8 0 0 0 0 11.316A7.687 7.687 0 0 0 8 16c2.158 0 3.978-.707 5.302-1.931l-2.582-2.002c-.72.488-1.647.764-2.725.764-2.087 0-3.856-1.408-4.492-3.301" fill="#34A853"/>
                                        <path d="M3.505 6.467a4.792 4.792 0 0 0 0 3.063" fill="#FBBC05"/>
                                        <path d="M8 3.166a4.419 4.419 0 0 1 3.122 1.2l.049.05 2.134-2.134A7.794 7.794 0 0 0 8 0a7.69 7.69 0 0 0-5.352 2.082l2.284 2.284A4.347 4.347 0 0 1 8 3.166z" fill="#EA4335"/>
                                    </svg>
                                    Đăng nhập với Google
                                </a>
                                </div>
                                <div class="col-6">
                                <a href="/auth/facebook" class="btn btn-facebook">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="bi bi-facebook" viewBox="0 0 16 16">
                                        <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z" fill="#1877F2"/>
                                    </svg>
                                    Đăng nhập với Facebook
                                </a>
                                </div>
                            </div>
                        </div>

                        <!-- Register tab -->
                        <div class="tab-pane fade" id="register-content" role="tabpanel" aria-labelledby="register-tab">
                            <div id="register-alert" class="alert alert-danger d-none" role="alert"></div>

                            <form id="registerForm" method="POST">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">

                                <div class="mb-4 position-relative">
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control ps-4" id="register-name" name="name" placeholder="Họ tên" required>
                                </div>

                                <div class="mb-4 position-relative">
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                                        <i class="fas fa-at"></i>
                                    </span>
                                    <input type="text" class="form-control ps-4" id="register-username" name="username" placeholder="Tên đăng nhập" required>
                                </div>

                                <div class="mb-4 position-relative">
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control ps-4" id="register-email" name="email" placeholder="Email" required>
                                </div>

                                <div class="mb-4 position-relative">
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control ps-4" id="register-password" name="password" placeholder="Mật khẩu" required>
                                    <span class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted password-toggle" style="cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <div class="form-text" style="margin-top: -16px !important;margin-bottom: 13px;color: red;font-style: italic;padding-left: 16px;">
                                    <small>
                                        <i class="fas fa-info-circle me-1"></i>
                                        Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt
                                    </small>
                                </div>
                                <div class="mb-4 position-relative">
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control ps-4" id="register-password-confirm" name="password_confirmation" placeholder="Xác nhận mật khẩu" required>
                                    <span class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted password-toggle" style="cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>

                                <div class="mb-4 position-relative">
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                                        <i class="fas fa-user-tag"></i>
                                    </span>
                                    <select class="form-control ps-4" id="register-account-type" name="account_type" required>
                                        <option value="">Chọn loại tài khoản</option>
                                        <optgroup label="🌟 Thành viên cộng đồng">
                                            <option value="member">Thành viên - Tham gia thảo luận</option>
                                            <option value="student">Sinh viên - Học tập nghiên cứu</option>
                                        </optgroup>
                                        <optgroup label="🏢 Đối tác kinh doanh">
                                            <option value="manufacturer">Nhà sản xuất - Sản xuất cơ khí</option>
                                            <option value="supplier">Nhà cung cấp - Phân phối thiết bị</option>
                                            <option value="brand">Nhãn hàng - Quảng bá thương hiệu</option>
                                        </optgroup>
                                    </select>
                                </div>

                                <div class="mb-4 form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">Tôi đồng ý với <a href="/terms" target="_blank">Điều khoản dịch vụ</a> và <a href="/privacy" target="_blank">Chính sách bảo mật</a></label>
                                </div>

                                <button type="submit" class="btn btn-primary">Đăng ký</button>
                            </form>
                        </div>

                        <!-- Forgot Password tab (hidden by default) -->
                        <div class="tab-pane fade" id="forgot-password-content" role="tabpanel" aria-labelledby="forgot-password-tab">
                            <div id="forgot-password-alert" class="alert alert-danger d-none" role="alert"></div>
                            <div id="forgot-password-success" class="alert alert-success d-none" role="alert"></div>

                            <form id="forgotPasswordForm" method="POST">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">

                                <div class="mb-3 position-relative">
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control ps-4" id="forgot-email" name="email" placeholder="Email" required>
                                    <div class="form-text">Nhập email của bạn và chúng tôi sẽ gửi cho bạn liên kết để đặt lại mật khẩu.</div>
                                </div>

                                <button type="submit" class="btn btn-primary">Gửi liên kết đặt lại mật khẩu</button>
                            </form>

                            <div class="mt-3 text-center">
                                <a href="#" id="backToLoginLink" class="forgot-password">Quay lại đăng nhập</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `;

    // Append modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Initialize the modal
    const modal = document.getElementById('authModal');
    const modalInstance = new bootstrap.Modal(modal);

    // Show the modal
    modalInstance.show();

    // Setup event listeners
    setupFormSubmissions();
    setupTabSwitching();
    setupPasswordToggle();
}

/**
 * Setup form submissions
 */
function setupFormSubmissions() {
    // Login form submission
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleLogin(this);
        });
    }

    // Register form submission
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleRegister(this);
        });
    }

    // Forgot password form submission
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleForgotPassword(this);
        });
    }
}

/**
 * Setup tab switching
 */
function setupTabSwitching() {
    // Forgot password link
    const forgotPasswordLink = document.getElementById('forgotPasswordLink');
    if (forgotPasswordLink) {
        forgotPasswordLink.addEventListener('click', function(e) {
            e.preventDefault();
            activateTab('forgot-password');
        });
    }

    // Back to login link
    const backToLoginLink = document.getElementById('backToLoginLink');
    if (backToLoginLink) {
        backToLoginLink.addEventListener('click', function(e) {
            e.preventDefault();
            activateTab('login');
        });
    }
}

/**
 * Setup password toggle functionality
 */
function setupPasswordToggle() {
    // Get all password toggle buttons
    const toggleButtons = document.querySelectorAll('.password-toggle');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Find the password input field
            const passwordInput = this.parentElement.querySelector('input[type="password"], input[type="text"]');

            // Toggle password visibility
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.querySelector('i').classList.remove('fa-eye');
                this.querySelector('i').classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                this.querySelector('i').classList.remove('fa-eye-slash');
                this.querySelector('i').classList.add('fa-eye');
            }
        });
    });
}

/**
 * Activate a specific tab
 */
function activateTab(tabName) {
    // Get all tabs
    const tabs = document.querySelectorAll('#authTabs .nav-link');
    const tabContents = document.querySelectorAll('#authTabsContent .tab-pane');
    const authTabs = document.getElementById('authTabs');

    // Deactivate all tabs
    tabs.forEach(tab => {
        tab.classList.remove('active');
        tab.setAttribute('aria-selected', 'false');
    });

    tabContents.forEach(content => {
        content.classList.remove('show', 'active');
    });

    // Activate the specified tab
    if (tabName === 'login') {
        // Show the tabs if they were hidden
        if (authTabs) {
            authTabs.style.display = '';
        }

        const loginTab = document.getElementById('login-tab');
        const loginContent = document.getElementById('login-content');

        if (loginTab && loginContent) {
            loginTab.classList.add('active');
            loginTab.setAttribute('aria-selected', 'true');
            loginContent.classList.add('show', 'active');
        }
    } else if (tabName === 'register') {
        // Show the tabs if they were hidden
        if (authTabs) {
            authTabs.style.display = '';
        }

        const registerTab = document.getElementById('register-tab');
        const registerContent = document.getElementById('register-content');

        if (registerTab && registerContent) {
            registerTab.classList.add('active');
            registerTab.setAttribute('aria-selected', 'true');
            registerContent.classList.add('show', 'active');
        }
    } else if (tabName === 'forgot-password') {
        // For forgot password, we hide the tabs and show only the content
        if (authTabs) {
            authTabs.style.display = 'none';
        }

        const forgotPasswordContent = document.getElementById('forgot-password-content');
        if (forgotPasswordContent) {
            forgotPasswordContent.classList.add('show', 'active');
        }
    }
}

/**
 * Handle login form submission
 */
function handleLogin(form) {
    // Get form data
    const formData = new FormData(form);

    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';
    submitButton.disabled = true;

    // Clear previous alerts
    const alertElement = document.getElementById('login-alert');
    alertElement.classList.add('d-none');
    alertElement.innerHTML = '';

    // Send AJAX request
    fetch('/ajax/login', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
            return;
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Reload the page on successful login
            window.location.reload();
        } else {
            // Show error message
            alertElement.classList.remove('d-none');
            alertElement.innerHTML = data.message || 'Đăng nhập không thành công. Vui lòng kiểm tra lại thông tin đăng nhập.';

            // Reset button
            submitButton.innerHTML = originalButtonText;
            submitButton.disabled = false;
        }
    })
    .catch(error => {
        console.error('Login error:', error);

        // Show error message
        alertElement.classList.remove('d-none');
        alertElement.innerHTML = 'Đã xảy ra lỗi. Vui lòng thử lại sau.';

        // Reset button
        submitButton.innerHTML = originalButtonText;
        submitButton.disabled = false;
    });
}

/**
 * Handle register form submission
 */
function handleRegister(form) {
    // Get form data
    const formData = new FormData(form);

    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';
    submitButton.disabled = true;

    // Clear previous alerts
    const alertElement = document.getElementById('register-alert');
    alertElement.classList.add('d-none');
    alertElement.innerHTML = '';

    // Send AJAX request
    fetch('/ajax/register', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
            return;
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Reload the page on successful registration
            window.location.reload();
        } else {
            // Show error message
            alertElement.classList.remove('d-none');

            if (data.errors) {
                let errorMessages = '';
                for (const key in data.errors) {
                    errorMessages += `<div>${data.errors[key]}</div>`;
                }
                alertElement.innerHTML = errorMessages;
            } else {
                alertElement.innerHTML = data.message || 'Đăng ký không thành công. Vui lòng kiểm tra lại thông tin.';
            }

            // Reset button
            submitButton.innerHTML = originalButtonText;
            submitButton.disabled = false;
        }
    })
    .catch(error => {
        console.error('Register error:', error);

        // Show error message
        alertElement.classList.remove('d-none');
        alertElement.innerHTML = 'Đã xảy ra lỗi. Vui lòng thử lại sau.';

        // Reset button
        submitButton.innerHTML = originalButtonText;
        submitButton.disabled = false;
    });
}

/**
 * Handle forgot password form submission
 */
function handleForgotPassword(form) {
    // Get form data
    const formData = new FormData(form);

    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';
    submitButton.disabled = true;

    // Clear previous alerts
    const alertElement = document.getElementById('forgot-password-alert');
    const successElement = document.getElementById('forgot-password-success');
    alertElement.classList.add('d-none');
    alertElement.innerHTML = '';
    successElement.classList.add('d-none');
    successElement.innerHTML = '';

    // Send AJAX request
    fetch('/ajax/forgot-password', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            successElement.classList.remove('d-none');
            successElement.innerHTML = data.message || 'Liên kết đặt lại mật khẩu đã được gửi đến email của bạn.';

            // Clear form
            form.reset();
        } else {
            // Show error message
            alertElement.classList.remove('d-none');

            if (data.errors) {
                let errorMessages = '';
                for (const key in data.errors) {
                    errorMessages += `<div>${data.errors[key]}</div>`;
                }
                alertElement.innerHTML = errorMessages;
            } else {
                alertElement.innerHTML = data.message || 'Không thể gửi liên kết đặt lại mật khẩu. Vui lòng kiểm tra lại email.';
            }
        }

        // Reset button
        submitButton.innerHTML = originalButtonText;
        submitButton.disabled = false;
    })
    .catch(error => {
        console.error('Forgot password error:', error);

        // Show error message
        alertElement.classList.remove('d-none');
        alertElement.innerHTML = 'Đã xảy ra lỗi. Vui lòng thử lại sau.';

        // Reset button
        submitButton.innerHTML = originalButtonText;
        submitButton.disabled = false;
    });
}
