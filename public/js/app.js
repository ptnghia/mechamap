/**
 * MechaMap - Compiled JavaScript for Laravel Application
 * Biên dịch từ resources/js/app.js và các dependencies
 * Không sử dụng Vite - Traditional JavaScript approach
 */

// ========================================
// GLOBAL UTILITIES
// ========================================

// CSRF Token Helper
window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// HTTP Request Helper (thay thế axios)
window.http = {
    /**
     * Thực hiện GET request
     */
    get: async function(url, options = {}) {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                ...options.headers
            },
            ...options
        });
        return this.handleResponse(response);
    },

    /**
     * Thực hiện POST request
     */
    post: async function(url, data = null, options = {}) {
        const formData = data instanceof FormData ? data : JSON.stringify(data);
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json',
            ...options.headers
        };

        if (!(data instanceof FormData)) {
            headers['Content-Type'] = 'application/json';
        }

        const response = await fetch(url, {
            method: 'POST',
            headers,
            body: formData,
            ...options
        });
        return this.handleResponse(response);
    },

    /**
     * Thực hiện PUT request
     */
    put: async function(url, data = null, options = {}) {
        return this.post(url, data, { ...options, method: 'PUT' });
    },

    /**
     * Thực hiện DELETE request
     */
    delete: async function(url, options = {}) {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                ...options.headers
            },
            ...options
        });
        return this.handleResponse(response);
    },

    /**
     * Xử lý response
     */
    handleResponse: async function(response) {
        const contentType = response.headers.get('content-type');

        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }
            return data;
        }

        if (!response.ok) {
            throw new Error('Request failed');
        }

        return response.text();
    }
};

// ========================================
// DOM UTILITIES
// ========================================

window.dom = {
    /**
     * Query selector helper
     */
    $: function(selector, context = document) {
        return context.querySelector(selector);
    },

    /**
     * Query all selector helper
     */
    $$: function(selector, context = document) {
        return context.querySelectorAll(selector);
    },

    /**
     * Add event listener helper
     */
    on: function(element, event, handler, options = {}) {
        if (typeof element === 'string') {
            element = this.$(element);
        }
        if (element) {
            element.addEventListener(event, handler, options);
        }
    },

    /**
     * Remove event listener helper
     */
    off: function(element, event, handler) {
        if (typeof element === 'string') {
            element = this.$(element);
        }
        if (element) {
            element.removeEventListener(event, handler);
        }
    },

    /**
     * Create element helper
     */
    create: function(tag, attributes = {}, textContent = '') {
        const element = document.createElement(tag);

        Object.keys(attributes).forEach(key => {
            if (key === 'className') {
                element.className = attributes[key];
            } else if (key === 'innerHTML') {
                element.innerHTML = attributes[key];
            } else {
                element.setAttribute(key, attributes[key]);
            }
        });

        if (textContent) {
            element.textContent = textContent;
        }

        return element;
    },

    /**
     * Show element
     */
    show: function(element) {
        if (typeof element === 'string') {
            element = this.$(element);
        }
        if (element) {
            element.style.display = '';
            element.classList.remove('d-none');
        }
    },

    /**
     * Hide element
     */
    hide: function(element) {
        if (typeof element === 'string') {
            element = this.$(element);
        }
        if (element) {
            element.style.display = 'none';
            element.classList.add('d-none');
        }
    },

    /**
     * Toggle element visibility
     */
    toggle: function(element) {
        if (typeof element === 'string') {
            element = this.$(element);
        }
        if (element) {
            if (element.style.display === 'none' || element.classList.contains('d-none')) {
                this.show(element);
            } else {
                this.hide(element);
            }
        }
    }
};

// ========================================
// VALIDATION UTILITIES
// ========================================

window.validation = {
    /**
     * Validate email format
     */
    isEmail: function(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },

    /**
     * Validate required field
     */
    isRequired: function(value) {
        return value !== null && value !== undefined && value.toString().trim() !== '';
    },

    /**
     * Validate minimum length
     */
    minLength: function(value, min) {
        return value && value.toString().length >= min;
    },

    /**
     * Validate maximum length
     */
    maxLength: function(value, max) {
        return !value || value.toString().length <= max;
    },

    /**
     * Validate number
     */
    isNumber: function(value) {
        return !isNaN(value) && !isNaN(parseFloat(value));
    },

    /**
     * Validate form
     */
    validateForm: function(form) {
        const errors = {};
        const inputs = form.querySelectorAll('input, textarea, select');

        inputs.forEach(input => {
            const rules = input.dataset.validate ? input.dataset.validate.split('|') : [];
            const fieldErrors = [];

            rules.forEach(rule => {
                if (rule === 'required' && !this.isRequired(input.value)) {
                    fieldErrors.push('Trường này là bắt buộc');
                } else if (rule === 'email' && input.value && !this.isEmail(input.value)) {
                    fieldErrors.push('Email không hợp lệ');
                } else if (rule.startsWith('min:')) {
                    const min = parseInt(rule.split(':')[1]);
                    if (!this.minLength(input.value, min)) {
                        fieldErrors.push(`Tối thiểu ${min} ký tự`);
                    }
                } else if (rule.startsWith('max:')) {
                    const max = parseInt(rule.split(':')[1]);
                    if (!this.maxLength(input.value, max)) {
                        fieldErrors.push(`Tối đa ${max} ký tự`);
                    }
                }
            });

            if (fieldErrors.length > 0) {
                errors[input.name] = fieldErrors;
            }
        });

        return {
            isValid: Object.keys(errors).length === 0,
            errors: errors
        };
    },

    /**
     * Display validation errors
     */
    displayErrors: function(errors) {
        // Clear previous errors
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        Object.keys(errors).forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('is-invalid');

                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = errors[fieldName][0]; // Show first error

                field.parentNode.appendChild(errorDiv);
            }
        });
    }
};

// ========================================
// NOTIFICATION SYSTEM
// ========================================

window.showNotification = function(message, type = 'info', duration = 3000) {
    // Remove existing notifications
    document.querySelectorAll('.notification').forEach(el => el.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;

    // Get icon for notification type
    function getIcon(type) {
        const icons = {
            success: '<i class="bi bi-check-circle-fill"></i>',
            error: '<i class="bi bi-exclamation-circle-fill"></i>',
            warning: '<i class="bi bi-exclamation-triangle-fill"></i>',
            info: '<i class="bi bi-info-circle-fill"></i>'
        };
        return icons[type] || icons.info;
    }

    // Set notification content
    notification.innerHTML = `
        <div class="d-flex align-items-start">
            <div class="me-2">
                ${getIcon(type)}
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold">${type.charAt(0).toUpperCase() + type.slice(1)}</div>
                <div>${message}</div>
            </div>
            <button type="button" class="btn-close ms-2" aria-label="Đóng"></button>
        </div>
    `;

    // Add to DOM
    document.body.appendChild(notification);

    // Add close event
    const closeBtn = notification.querySelector('.btn-close');
    closeBtn.addEventListener('click', () => {
        notification.remove();
    });

    // Auto remove after duration
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, duration);
};

// ========================================
// THEME MANAGEMENT
// ========================================

window.theme = {
    /**
     * Get current theme
     */
    get: function() {
        return localStorage.getItem('theme') || 'light';
    },

    /**
     * Set theme
     */
    set: function(theme) {
        localStorage.setItem('theme', theme);
        document.documentElement.setAttribute('data-theme', theme);
        this.updateToggleButton();
    },

    /**
     * Toggle theme
     */
    toggle: function() {
        const currentTheme = this.get();
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        this.set(newTheme);
    },

    /**
     * Initialize theme
     */
    init: function() {
        const savedTheme = this.get();
        this.set(savedTheme);

        // Add toggle event listener
        const toggleBtn = document.getElementById('theme-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggle());
        }
    },

    /**
     * Update toggle button appearance
     */
    updateToggleButton: function() {
        const toggleBtn = document.getElementById('theme-toggle');
        if (toggleBtn) {
            const darkIcon = toggleBtn.querySelector('.dark-icon');
            const lightIcon = toggleBtn.querySelector('.light-icon');
            const currentTheme = this.get();

            if (currentTheme === 'dark') {
                darkIcon?.classList.add('d-none');
                lightIcon?.classList.remove('d-none');
            } else {
                darkIcon?.classList.remove('d-none');
                lightIcon?.classList.add('d-none');
            }
        }
    }
};

// ========================================
// FORM HELPERS
// ========================================

window.forms = {
    /**
     * Submit form with validation
     */
    submit: function(form, options = {}) {
        if (typeof form === 'string') {
            form = document.querySelector(form);
        }

        if (!form) return;

        // Validate form
        const validation = window.validation.validateForm(form);
        if (!validation.isValid) {
            window.validation.displayErrors(validation.errors);
            return;
        }

        // Get form data
        const formData = new FormData(form);
        const action = form.action || window.location.href;
        const method = form.method || 'POST';

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn?.textContent;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang xử lý...';
        }

        // Submit form
        window.http[method.toLowerCase()](action, formData)
            .then(response => {
                if (options.onSuccess) {
                    options.onSuccess(response);
                } else {
                    window.showNotification('Thao tác thành công!', 'success');
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                }
            })
            .catch(error => {
                if (options.onError) {
                    options.onError(error);
                } else {
                    window.showNotification(error.message || 'Có lỗi xảy ra!', 'error');
                }
            })
            .finally(() => {
                // Restore button state
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });
    },

    /**
     * Reset form
     */
    reset: function(form) {
        if (typeof form === 'string') {
            form = document.querySelector(form);
        }

        if (form) {
            form.reset();
            // Clear validation errors
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        }
    }
};

// ========================================
// MODAL HELPERS
// ========================================

window.modal = {
    /**
     * Show modal
     */
    show: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');

            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'modal-backdrop';
            document.body.appendChild(backdrop);
        }
    },

    /**
     * Hide modal
     */
    hide: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');

            // Remove backdrop
            const backdrop = document.getElementById('modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        }
    }
};

// ========================================
// INITIALIZATION
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme
    window.theme.init();

    // Initialize tooltips (nếu có Bootstrap)
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => {
            new bootstrap.Tooltip(tooltip);
        });
    }

    // Initialize form validations
    const forms = document.querySelectorAll('form[data-validate="true"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            window.forms.submit(this);
        });
    });

    // Initialize modal close buttons
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                window.modal.hide(modal.id);
            }
        });
    });

    // Initialize confirmation dialogs
    document.querySelectorAll('[data-confirm]').forEach(element => {
        element.addEventListener('click', function(e) {
            const message = this.dataset.confirm;
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    console.log('MechaMap JavaScript initialized successfully!');
});

// ========================================
// GLOBAL ERROR HANDLER
// ========================================

window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
    // Có thể gửi lỗi về server để tracking
});

window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled Promise Rejection:', e.reason);
    // Có thể gửi lỗi về server để tracking
});
