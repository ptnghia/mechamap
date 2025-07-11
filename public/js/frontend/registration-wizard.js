/**
 * 🧙‍♂️ Registration Wizard JavaScript
 * Handles multi-step registration form interactions, validation, and auto-save
 */

class RegistrationWizard {
    constructor(options = {}) {
        this.options = {
            autoSave: true,
            autoSaveInterval: 30000, // 30 seconds
            validationDelay: 500,    // 500ms debounce
            sessionTimeout: 1800000, // 30 minutes
            ...options
        };

        this.currentStep = 1;
        this.sessionId = null;
        this.autoSaveTimer = null;
        this.validationTimers = {};

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializePasswordStrength();
        this.initializeUsernameCheck();
        this.initializeAccountTypeSelection();
        this.initializeAutoSave();
        this.initializeFormValidation();

        console.log('Registration Wizard initialized');
    }

    setupEventListeners() {
        // Password toggle
        const togglePassword = document.getElementById('togglePassword');
        if (togglePassword) {
            togglePassword.addEventListener('click', this.togglePasswordVisibility.bind(this));
        }

        // Form submission
        const forms = document.querySelectorAll('form[id*="step"]');
        forms.forEach(form => {
            form.addEventListener('submit', this.handleFormSubmit.bind(this));
        });

        // Real-time validation
        const inputs = document.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', this.handleFieldValidation.bind(this));
            input.addEventListener('input', this.handleFieldInput.bind(this));
        });
    }

    initializePasswordStrength() {
        const passwordInput = document.getElementById('password');
        const strengthIndicator = document.getElementById('passwordStrength');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');

        if (!passwordInput || !strengthIndicator) return;

        passwordInput.addEventListener('input', (e) => {
            const password = e.target.value;
            const strength = this.calculatePasswordStrength(password);

            if (password.length > 0) {
                strengthIndicator.style.display = 'block';
                strengthFill.style.width = `${strength.percentage}%`;
                strengthFill.className = `strength-fill strength-${strength.level}`;
                strengthText.textContent = strength.text;
                strengthText.className = `strength-text text-${strength.color}`;
            } else {
                strengthIndicator.style.display = 'none';
            }
        });
    }

    calculatePasswordStrength(password) {
        let score = 0;
        let feedback = [];

        // Length check
        if (password.length >= 8) score += 25;
        else feedback.push('ít nhất 8 ký tự');

        // Uppercase check
        if (/[A-Z]/.test(password)) score += 25;
        else feedback.push('chữ hoa');

        // Lowercase check
        if (/[a-z]/.test(password)) score += 25;
        else feedback.push('chữ thường');

        // Number check
        if (/\d/.test(password)) score += 25;
        else feedback.push('số');

        // Special character check
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score += 25;
        else feedback.push('ký tự đặc biệt');

        // Determine strength level
        let level, text, color;
        if (score < 50) {
            level = 'weak';
            text = `Yếu - Cần: ${feedback.join(', ')}`;
            color = 'danger';
        } else if (score < 75) {
            level = 'medium';
            text = `Trung bình - Cần: ${feedback.join(', ')}`;
            color = 'warning';
        } else {
            level = 'strong';
            text = 'Mạnh - Mật khẩu tốt';
            color = 'success';
        }

        return {
            percentage: Math.min(score, 100),
            level,
            text,
            color
        };
    }

    initializeUsernameCheck() {
        const usernameInput = document.getElementById('username');
        if (!usernameInput) return;

        usernameInput.addEventListener('input', (e) => {
            const username = e.target.value.trim();

            // Clear previous timer
            if (this.validationTimers.username) {
                clearTimeout(this.validationTimers.username);
            }

            // Set new timer
            this.validationTimers.username = setTimeout(() => {
                if (username.length >= 3) {
                    this.checkUsernameAvailability(username);
                }
            }, this.options.validationDelay);
        });
    }

    async checkUsernameAvailability(username) {
        const usernameInput = document.getElementById('username');
        const errorDiv = document.getElementById('username-error');
        const successDiv = document.getElementById('username-success');

        try {
            const response = await fetch('/register/wizard/check-username', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({ username })
            });

            const data = await response.json();

            if (data.available) {
                usernameInput.classList.remove('is-invalid');
                usernameInput.classList.add('is-valid');
                if (successDiv) successDiv.textContent = data.message;
            } else {
                usernameInput.classList.remove('is-valid');
                usernameInput.classList.add('is-invalid');
                if (errorDiv) errorDiv.textContent = data.message;
            }
        } catch (error) {
            console.error('Username check failed:', error);
        }
    }

    initializeAccountTypeSelection() {
        const accountTypeInputs = document.querySelectorAll('input[name="account_type"]');
        const accountTypeGroups = document.querySelectorAll('.account-type-group');

        accountTypeInputs.forEach(input => {
            input.addEventListener('change', () => {
                // Remove selected class from all groups
                accountTypeGroups.forEach(group => {
                    group.classList.remove('selected');
                });

                // Add selected class to current group
                const selectedGroup = input.closest('.account-type-group');
                if (selectedGroup) {
                    selectedGroup.classList.add('selected');
                }

                // Show/hide business notice
                this.toggleBusinessNotice(input.value);
            });
        });

        // Initialize selected state
        const checkedInput = document.querySelector('input[name="account_type"]:checked');
        if (checkedInput) {
            const selectedGroup = checkedInput.closest('.account-type-group');
            if (selectedGroup) {
                selectedGroup.classList.add('selected');
            }
        }
    }

    toggleBusinessNotice(accountType) {
        const businessNotice = document.querySelector('.business-notice');
        if (!businessNotice) return;

        const isBusinessAccount = ['manufacturer', 'supplier', 'brand'].includes(accountType);
        businessNotice.style.display = isBusinessAccount ? 'block' : 'none';
    }

    initializeAutoSave() {
        if (!this.options.autoSave) return;

        const form = document.querySelector('form[id*="step"]');
        if (!form) return;

        // Auto-save on form changes
        form.addEventListener('input', () => {
            if (this.autoSaveTimer) {
                clearTimeout(this.autoSaveTimer);
            }

            this.autoSaveTimer = setTimeout(() => {
                this.saveProgress();
            }, this.options.autoSaveInterval);
        });
    }

    async saveProgress() {
        const form = document.querySelector('form[id*="step"]');
        if (!form) return;

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/register/wizard/save-progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({ data })
            });

            const result = await response.json();

            if (result.success) {
                this.showAutoSaveIndicator('Đã lưu tự động');
            }
        } catch (error) {
            console.error('Auto-save failed:', error);
        }
    }

    showAutoSaveIndicator(message) {
        const indicator = document.getElementById('autoSaveIndicator');
        const text = document.getElementById('autoSaveText');

        if (indicator && text) {
            text.textContent = message;
            indicator.style.display = 'block';

            setTimeout(() => {
                indicator.style.display = 'none';
            }, 2000);
        }
    }

    initializeFormValidation() {
        const forms = document.querySelectorAll('form[id*="step"]');

        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Focus first invalid field
                    const firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }

                form.classList.add('was-validated');
            });
        });
    }

    async handleFieldValidation(e) {
        const field = e.target;
        const fieldName = field.name;
        const fieldValue = field.value.trim();

        if (!fieldName || !fieldValue) return;

        // Clear previous timer
        if (this.validationTimers[fieldName]) {
            clearTimeout(this.validationTimers[fieldName]);
        }

        // Set new timer
        this.validationTimers[fieldName] = setTimeout(async () => {
            try {
                const response = await fetch('/register/wizard/validate-field', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({
                        field: fieldName,
                        value: fieldValue,
                        step: this.currentStep
                    })
                });

                const data = await response.json();
                this.updateFieldValidation(field, data.valid, data.message);
            } catch (error) {
                console.error('Field validation failed:', error);
            }
        }, this.options.validationDelay);
    }

    updateFieldValidation(field, isValid, message) {
        const errorDiv = document.getElementById(`${field.name}-error`);
        const successDiv = document.getElementById(`${field.name}-success`);

        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
            if (successDiv) {
                successDiv.textContent = message;
                successDiv.style.display = 'block';
            }
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            }
            if (successDiv) {
                successDiv.textContent = '';
                successDiv.style.display = 'none';
            }
        }
    }

    handleFieldInput(e) {
        const field = e.target;

        // Remove validation classes on input to allow re-validation
        field.classList.remove('is-valid', 'is-invalid');

        // Hide validation messages when user starts typing
        const errorDiv = document.getElementById(`${field.name}-error`);
        const successDiv = document.getElementById(`${field.name}-success`);

        if (errorDiv) errorDiv.style.display = 'none';
        if (successDiv) successDiv.style.display = 'none';

        // Special handling for password confirmation
        if (field.name === 'password_confirmation') {
            this.validatePasswordConfirmation();
        }

        // Special handling for password field - also clear confirmation validation
        if (field.name === 'password') {
            const confirmationField = document.getElementById('password_confirmation');
            if (confirmationField && confirmationField.value) {
                setTimeout(() => this.validatePasswordConfirmation(), 100);
            }
        }
    }

    validatePasswordConfirmation() {
        const password = document.getElementById('password');
        const confirmation = document.getElementById('password_confirmation');

        if (!password || !confirmation || !confirmation.value) return;

        const isMatch = password.value === confirmation.value && confirmation.value.length > 0;
        const message = isMatch ? 'Mật khẩu khớp' : 'Mật khẩu không khớp';

        this.updateFieldValidation(confirmation, isMatch, message);
    }

    togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (!passwordInput || !toggleIcon) return;

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    handleFormSubmit(e) {
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');

        if (submitBtn) {
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
        }

        // Re-enable button after 5 seconds (fallback)
        setTimeout(() => {
            if (submitBtn) {
                submitBtn.classList.remove('btn-loading');
                submitBtn.disabled = false;
            }
        }, 5000);
    }
}

// Initialize wizard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on a wizard page
    if (document.querySelector('.registration-wizard-container')) {
        window.registrationWizard = new RegistrationWizard({
            autoSave: true,
            autoSaveInterval: 30000,
            validationDelay: 500,
            sessionTimeout: 1800000
        });
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RegistrationWizard;
}
