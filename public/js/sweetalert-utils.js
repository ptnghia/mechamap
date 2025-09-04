/**
 * SweetAlert2 Utility Functions for MechaMap
 * Provides consistent alert, confirm, and prompt dialogs across the application
 */

// Default SweetAlert2 configuration
const defaultSwalConfig = {
    customClass: {
        confirmButton: 'btn btn-primary me-2',
        cancelButton: 'btn btn-secondary',
        denyButton: 'btn btn-danger me-2'
    },
    buttonsStyling: false,
    allowOutsideClick: false,
    allowEscapeKey: true,
    showCloseButton: true
};

/**
 * Show a simple alert message
 * @param {string} title - Alert title
 * @param {string} text - Alert message
 * @param {string} icon - Icon type: 'success', 'error', 'warning', 'info', 'question'
 * @param {object} options - Additional SweetAlert2 options
 */
window.showAlert = function(title, text = '', icon = 'info', options = {}) {
    return Swal.fire({
        ...defaultSwalConfig,
        title: title,
        text: text,
        icon: icon,
        confirmButtonText: options.confirmButtonText || 'OK',
        ...options
    });
};

/**
 * Show a success alert
 * @param {string} title - Alert title
 * @param {string} text - Alert message
 * @param {object} options - Additional options
 */
window.showSuccess = function(title, text = '', options = {}) {
    return window.showAlert(title, text, 'success', {
        confirmButtonText: 'Tuyệt vời!',
        ...options
    });
};

/**
 * Show an error alert
 * @param {string} title - Alert title
 * @param {string} text - Alert message
 * @param {object} options - Additional options
 */
window.showError = function(title, text = '', options = {}) {
    return window.showAlert(title, text, 'error', {
        confirmButtonText: 'Đã hiểu',
        ...options
    });
};

/**
 * Show a warning alert
 * @param {string} title - Alert title
 * @param {string} text - Alert message
 * @param {object} options - Additional options
 */
window.showWarning = function(title, text = '', options = {}) {
    return window.showAlert(title, text, 'warning', {
        confirmButtonText: 'Đã hiểu',
        ...options
    });
};

/**
 * Show a confirmation dialog
 * @param {string} title - Confirmation title
 * @param {string} text - Confirmation message
 * @param {function|object} onConfirmOrOptions - Callback for confirm action or options object
 * @param {function} onCancel - Callback for cancel action (optional)
 * @param {object} options - Additional options (when using callbacks)
 * @returns {Promise} - Resolves with result object
 */
window.showConfirm = function(title, text = '', onConfirmOrOptions = {}, onCancel = null, options = {}) {
    // Check if using callback style (function as 3rd parameter)
    const isCallbackStyle = typeof onConfirmOrOptions === 'function';

    // Determine final options
    const finalOptions = isCallbackStyle ? options : onConfirmOrOptions;

    const swalPromise = Swal.fire({
        ...defaultSwalConfig,
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: finalOptions.confirmButtonText || 'Xác nhận',
        cancelButtonText: finalOptions.cancelButtonText || 'Hủy bỏ',
        reverseButtons: true,
        ...finalOptions
    });

    // If using callback style, handle callbacks
    if (isCallbackStyle) {
        swalPromise.then((result) => {
            if (result.isConfirmed && onConfirmOrOptions) {
                onConfirmOrOptions(result);
            } else if (result.isDismissed && onCancel) {
                onCancel(result);
            }
        });
    }

    return swalPromise;
};

/**
 * Show a delete confirmation dialog
 * @param {string} messageOrItemName - Full message or item name to delete
 * @param {object} options - Additional options
 * @returns {Promise} - Resolves with result object
 */
window.showDeleteConfirm = function(messageOrItemName = 'mục này', options = {}) {
    // Check if the parameter is a full message (contains spaces or special characters)
    // or just an item name
    let text;
    if (messageOrItemName.includes(' ') || messageOrItemName.includes('?')) {
        // It's a full message, use as is
        text = messageOrItemName;
    } else {
        // It's just an item name, create the full message
        text = `Bạn có chắc chắn muốn xóa ${messageOrItemName}? Hành động này không thể hoàn tác.`;
    }

    return Swal.fire({
        ...defaultSwalConfig,
        title: 'Xác nhận xóa',
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy bỏ',
        reverseButtons: true,
        customClass: {
            ...defaultSwalConfig.customClass,
            confirmButton: 'btn btn-danger me-2'
        },
        ...options
    });
};

/**
 * Show a prompt dialog for text input
 * @param {string} title - Prompt title
 * @param {string} text - Prompt message
 * @param {string} placeholder - Input placeholder
 * @param {string} defaultValue - Default input value
 * @param {object} options - Additional options
 * @returns {Promise} - Resolves with result object containing value
 */
window.showPrompt = function(title, text = '', placeholder = '', defaultValue = '', options = {}) {
    return Swal.fire({
        ...defaultSwalConfig,
        title: title,
        text: text,
        input: 'text',
        inputPlaceholder: placeholder,
        inputValue: defaultValue,
        showCancelButton: true,
        confirmButtonText: options.confirmButtonText || 'Xác nhận',
        cancelButtonText: options.cancelButtonText || 'Hủy bỏ',
        reverseButtons: true,
        inputValidator: (value) => {
            if (options.required !== false && !value) {
                return 'Vui lòng nhập thông tin!';
            }
            if (options.validator && typeof options.validator === 'function') {
                return options.validator(value);
            }
        },
        ...options
    });
};

/**
 * Show a textarea prompt dialog
 * @param {string} title - Prompt title
 * @param {string} text - Prompt message
 * @param {string} placeholder - Textarea placeholder
 * @param {string} defaultValue - Default textarea value
 * @param {object} options - Additional options
 * @returns {Promise} - Resolves with result object containing value
 */
window.showTextareaPrompt = function(title, text = '', placeholder = '', defaultValue = '', options = {}) {
    return Swal.fire({
        ...defaultSwalConfig,
        title: title,
        text: text,
        input: 'textarea',
        inputPlaceholder: placeholder,
        inputValue: defaultValue,
        showCancelButton: true,
        confirmButtonText: options.confirmButtonText || 'Xác nhận',
        cancelButtonText: options.cancelButtonText || 'Hủy bỏ',
        reverseButtons: true,
        inputValidator: (value) => {
            if (options.required !== false && !value) {
                return 'Vui lòng nhập thông tin!';
            }
            if (options.validator && typeof options.validator === 'function') {
                return options.validator(value);
            }
        },
        ...options
    });
};

/**
 * Show a select dropdown prompt
 * @param {string} title - Prompt title
 * @param {string} text - Prompt message
 * @param {object} options - Select options and additional SweetAlert options
 * @returns {Promise} - Resolves with result object containing value
 */
window.showSelectPrompt = function(title, text = '', options = {}) {
    return Swal.fire({
        ...defaultSwalConfig,
        title: title,
        text: text,
        input: 'select',
        inputOptions: options.inputOptions || {},
        inputPlaceholder: options.placeholder || 'Chọn một tùy chọn',
        showCancelButton: true,
        confirmButtonText: options.confirmButtonText || 'Xác nhận',
        cancelButtonText: options.cancelButtonText || 'Hủy bỏ',
        reverseButtons: true,
        inputValidator: (value) => {
            if (options.required !== false && !value) {
                return 'Vui lòng chọn một tùy chọn!';
            }
        },
        ...options
    });
};

/**
 * Show a loading dialog
 * @param {string} title - Loading title
 * @param {string} text - Loading message
 */
window.showLoading = function(title = 'Đang xử lý...', text = 'Vui lòng đợi trong giây lát') {
    return Swal.fire({
        title: title,
        text: text,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
};

/**
 * Close any open SweetAlert dialog
 */
window.closeAlert = function() {
    Swal.close();
};

/**
 * Show a toast notification
 * @param {string} title - Toast title
 * @param {string} icon - Icon type
 * @param {number} timer - Auto close timer in ms
 * @param {object} options - Additional options
 */
window.showToast = function(title, icon = 'success', timer = 3000, options = {}) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: timer,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    return Toast.fire({
        icon: icon,
        title: title,
        ...options
    });
};

// Backward compatibility functions to replace native alert/confirm/prompt
window.alert = function(message) {
    return window.showAlert('Thông báo', message, 'info');
};

window.confirm = function(message) {
    return window.showConfirm('Xác nhận', message).then(result => result.isConfirmed);
};

window.prompt = function(message, defaultValue = '') {
    return window.showPrompt('Nhập thông tin', message, '', defaultValue).then(result => {
        return result.isConfirmed ? result.value : null;
    });
};

/**
 * Smart Error Handler - Handles different types of errors intelligently
 * @param {object} xhr - XMLHttpRequest object
 * @param {string} defaultTitle - Default error title
 * @param {string} defaultMessage - Default error message
 * @param {object} options - Additional options
 */
window.handleAjaxError = function(xhr, defaultTitle = 'Lỗi', defaultMessage = 'Đã xảy ra lỗi không mong muốn', options = {}) {
    let title = defaultTitle;
    let message = defaultMessage;
    let shouldShow = true;
    let actionRequired = false;

    // Handle different HTTP status codes
    switch (xhr.status) {
        case 419: // CSRF Token Mismatch
            title = 'Phiên làm việc hết hạn';
            message = 'Vui lòng tải lại trang để tiếp tục.';
            actionRequired = 'reload';
            break;

        case 401: // Unauthorized
            title = 'Chưa đăng nhập';
            message = 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.';
            actionRequired = 'login';
            break;

        case 403: // Forbidden
            title = 'Không có quyền';
            message = 'Bạn không có quyền thực hiện hành động này.';
            break;

        case 404: // Not Found
            title = 'Không tìm thấy';
            message = 'Trang hoặc tài nguyên không tồn tại.';
            break;

        case 422: // Validation Error
            title = 'Dữ liệu không hợp lệ';
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.message) {
                    message = response.message;
                } else if (response.errors) {
                    const errors = Object.values(response.errors).flat();
                    message = errors.join('\n');
                }
            } catch (e) {
                message = 'Dữ liệu gửi lên không đúng định dạng.';
            }
            break;

        case 429: // Too Many Requests
            title = 'Quá nhiều yêu cầu';
            message = 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau.';
            break;

        case 500: // Internal Server Error
            title = 'Lỗi máy chủ';
            message = 'Máy chủ gặp sự cố. Vui lòng thử lại sau.';
            console.error('Server Error 500:', xhr.responseText);
            break;

        case 502: // Bad Gateway
        case 503: // Service Unavailable
        case 504: // Gateway Timeout
            title = 'Máy chủ không khả dụng';
            message = 'Máy chủ tạm thời không khả dụng. Vui lòng thử lại sau.';
            break;

        case 0: // Network Error
            // Don't show dialog for network errors by default
            shouldShow = options.showNetworkErrors !== false ? false : true;
            title = 'Mất kết nối';
            message = 'Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng.';
            console.warn('Network error detected:', xhr);
            break;

        default:
            // For unknown errors, use default values
            console.error('Unknown error:', xhr.status, xhr.responseText);
            break;
    }

    // Skip showing dialog if configured to do so
    if (!shouldShow && !options.forceShow) {
        return Promise.resolve();
    }

    // Show error dialog with appropriate action
    const errorPromise = window.showError(title, message, {
        confirmButtonText: actionRequired === 'reload' ? 'Tải lại trang' :
                          actionRequired === 'login' ? 'Đăng nhập lại' : 'Đã hiểu'
    });

    // Handle required actions
    if (actionRequired) {
        errorPromise.then(() => {
            switch (actionRequired) {
                case 'reload':
                    window.location.reload();
                    break;
                case 'login':
                    window.location.href = '/login';
                    break;
            }
        });
    }

    return errorPromise;
};

/**
 * Setup global AJAX error handling with SweetAlert
 */
/**
 * Setup Smart Global AJAX Error Handling
 * Intelligently handles different types of errors with appropriate responses
 */
window.setupGlobalAjaxErrorHandling = function(options = {}) {
    const defaultOptions = {
        skipNetworkErrors: true,        // Don't show dialog for network errors (status 0)
        skipCSRFErrors: false,          // Handle CSRF errors with reload prompt
        skipAPIErrors: true,            // Let API endpoints handle their own errors
        logErrors: true,                // Log errors to console
        ...options
    };

    // jQuery AJAX error handling
    if (typeof $ !== 'undefined') {
        $(document).ajaxError(function(event, xhr, settings) {
            // Skip handling for specific URLs or conditions
            if (defaultOptions.skipAPIErrors && settings.url && settings.url.includes('/api/')) {
                if (defaultOptions.logErrors) {
                    console.log('API error (skipped):', settings.url, xhr.status);
                }
                return; // Let API endpoints handle their own errors
            }

            // Skip specific error types based on configuration
            if (defaultOptions.skipNetworkErrors && xhr.status === 0) {
                if (defaultOptions.logErrors) {
                    console.warn('Network error detected (dialog suppressed):', xhr);
                }
                return;
            }

            if (defaultOptions.skipCSRFErrors && xhr.status === 419) {
                if (defaultOptions.logErrors) {
                    console.warn('CSRF error detected (dialog suppressed):', xhr);
                }
                return;
            }

            // Log error details
            if (defaultOptions.logErrors) {
                console.error('AJAX Error:', {
                    url: settings.url,
                    method: settings.type,
                    status: xhr.status,
                    statusText: xhr.statusText,
                    response: xhr.responseText
                });
            }

            // Handle error with smart error handler
            window.handleAjaxError(xhr, undefined, undefined, {
                showNetworkErrors: !defaultOptions.skipNetworkErrors,
                forceShow: false
            });
        });

        if (defaultOptions.logErrors) {
            console.log('✅ Smart Global AJAX error handling initialized with options:', defaultOptions);
        }
    }

    // Native fetch error handling (for modern AJAX)
    if (typeof window.fetch !== 'undefined' && !window.fetchErrorHandlerSetup) {
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            return originalFetch.apply(this, args)
                .catch(error => {
                    if (defaultOptions.logErrors) {
                        console.error('Fetch Error:', error);
                    }

                    // Handle network errors
                    if (error.name === 'TypeError' && error.message.includes('fetch')) {
                        if (!defaultOptions.skipNetworkErrors) {
                            window.handleAjaxError({ status: 0 }, undefined, undefined, {
                                showNetworkErrors: true
                            });
                        }
                    }

                    throw error; // Re-throw for caller to handle
                });
        };
        window.fetchErrorHandlerSetup = true;

        if (defaultOptions.logErrors) {
            console.log('✅ Fetch error handling initialized');
        }
    }
};



/**
 * CSRF Token Management
 * Automatically refresh CSRF token when needed
 */
window.refreshCSRFToken = function() {
    return fetch('/csrf-token', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.csrf_token) {
            // Update meta tag
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                metaTag.setAttribute('content', data.csrf_token);
            }

            // Update jQuery AJAX setup
            if (typeof $ !== 'undefined') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': data.csrf_token
                    }
                });
            }

            console.log('✅ CSRF token refreshed successfully');
            return data.csrf_token;
        }
        throw new Error('Invalid CSRF token response');
    })
    .catch(error => {
        console.error('❌ Failed to refresh CSRF token:', error);
        throw error;
    });
};

/**
 * Auto-retry AJAX request with fresh CSRF token
 * @param {Function} requestFunction - Function that makes the AJAX request
 * @param {number} maxRetries - Maximum number of retries
 */
window.retryWithFreshCSRF = function(requestFunction, maxRetries = 1) {
    return requestFunction()
        .catch(error => {
            // If CSRF error and we have retries left
            if ((error.status === 419 || error.responseJSON?.message?.includes('CSRF')) && maxRetries > 0) {
                console.log('🔄 CSRF error detected, refreshing token and retrying...');
                return window.refreshCSRFToken()
                    .then(() => window.retryWithFreshCSRF(requestFunction, maxRetries - 1));
            }
            throw error;
        });
};

/**
 * Enhanced AJAX wrapper with automatic CSRF handling
 * @param {object} options - jQuery AJAX options
 */
window.ajaxWithCSRFRetry = function(options) {
    const requestFunction = () => $.ajax(options);
    return window.retryWithFreshCSRF(requestFunction);
};

//console.log('SweetAlert2 utilities loaded successfully!');
