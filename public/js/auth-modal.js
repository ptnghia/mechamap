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
                    <h5 class="modal-title" id="authModalLabel">Đăng nhập hoặc Đăng ký</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="authTabs" role="tablist">
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

                                <div class="mb-3">
                                    <label for="login" class="form-label">Email hoặc Username</label>
                                    <input type="text" class="form-control" id="login" name="login" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                                </div>

                                <div class="mb-3 text-end">
                                    <a href="#" class="forgot-password" id="forgotPasswordLink">Quên mật khẩu?</a>
                                </div>

                                <button type="submit" class="btn btn-primary">Đăng nhập</button>
                            </form>

                            <div class="divider">
                                <span>hoặc đăng nhập với</span>
                            </div>

                            <div class="social-login">
                                <a href="/auth/google" class="btn btn-google">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-google" viewBox="0 0 16 16">
                                        <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z"/>
                                    </svg>
                                    Đăng nhập với Google
                                </a>
                                <a href="/auth/facebook" class="btn btn-facebook">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                                        <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
                                    </svg>
                                    Đăng nhập với Facebook
                                </a>
                            </div>
                        </div>

                        <!-- Register tab -->
                        <div class="tab-pane fade" id="register-content" role="tabpanel" aria-labelledby="register-tab">
                            <div id="register-alert" class="alert alert-danger d-none" role="alert"></div>

                            <form id="registerForm" method="POST">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">

                                <div class="mb-3">
                                    <label for="register-name" class="form-label">Họ tên</label>
                                    <input type="text" class="form-control" id="register-name" name="name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="register-username" class="form-label">Tên đăng nhập</label>
                                    <input type="text" class="form-control" id="register-username" name="username" required>
                                </div>

                                <div class="mb-3">
                                    <label for="register-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="register-email" name="email" required>
                                </div>

                                <div class="mb-3">
                                    <label for="register-password" class="form-label">Mật khẩu</label>
                                    <input type="password" class="form-control" id="register-password" name="password" required>
                                </div>

                                <div class="mb-3">
                                    <label for="register-password-confirm" class="form-label">Xác nhận mật khẩu</label>
                                    <input type="password" class="form-control" id="register-password-confirm" name="password_confirmation" required>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">Tôi đồng ý với <a href="/terms" target="_blank">Điều khoản dịch vụ</a> và <a href="/privacy" target="_blank">Chính sách bảo mật</a></label>
                                </div>

                                <button type="submit" class="btn btn-primary">Đăng ký</button>
                            </form>

                            <div class="divider">
                                <span>hoặc đăng ký với</span>
                            </div>

                            <div class="social-login">
                                <a href="/auth/google" class="btn btn-google">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-google" viewBox="0 0 16 16">
                                        <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z"/>
                                    </svg>
                                    Đăng ký với Google
                                </a>
                                <a href="/auth/facebook" class="btn btn-facebook">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                                        <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
                                    </svg>
                                    Đăng ký với Facebook
                                </a>
                            </div>
                        </div>

                        <!-- Forgot Password tab (hidden by default) -->
                        <div class="tab-pane fade" id="forgot-password-content" role="tabpanel" aria-labelledby="forgot-password-tab">
                            <div id="forgot-password-alert" class="alert alert-danger d-none" role="alert"></div>
                            <div id="forgot-password-success" class="alert alert-success d-none" role="alert"></div>

                            <form id="forgotPasswordForm" method="POST">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">

                                <div class="mb-3">
                                    <label for="forgot-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="forgot-email" name="email" required>
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
