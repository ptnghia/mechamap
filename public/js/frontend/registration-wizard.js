/**
 * 🧙‍♂️ Registration Wizard JavaScript
 * Handles multi-step registration form interactions, validation, and auto-save
 *
 * Dependencies:
 * - Requires app.js for global utilities (window.http, window.validation)
 * - Uses Bootstrap 5 for UI components
 * - Integrates with Laravel CSRF protection
 *
 * Note: This script takes precedence over global form validation
 * for registration wizard forms to provide specialized functionality.
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

        // Get translations for password strength
        const translations = window.passwordStrengthTranslations || {
            // English fallback
            requirements: {
                length: 'at least 8 characters',
                uppercase: 'uppercase letter',
                lowercase: 'lowercase letter',
                number: 'number',
                special: 'special character'
            },
            levels: {
                weak: 'Weak - Need',
                medium: 'Medium - Need',
                strong: 'Strong - Good password'
            }
        };

        // Length check
        if (password.length >= 8) score += 25;
        else feedback.push(translations.requirements.length);

        // Uppercase check
        if (/[A-Z]/.test(password)) score += 25;
        else feedback.push(translations.requirements.uppercase);

        // Lowercase check
        if (/[a-z]/.test(password)) score += 25;
        else feedback.push(translations.requirements.lowercase);

        // Number check
        if (/\d/.test(password)) score += 25;
        else feedback.push(translations.requirements.number);

        // Special character check
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score += 25;
        else feedback.push(translations.requirements.special);

        // Determine strength level
        let level, text, color;
        if (score < 50) {
            level = 'weak';
            text = `${translations.levels.weak}: ${feedback.join(', ')}`;
            color = 'danger';
        } else if (score < 100) {  // Changed from 75 to 100 - only perfect score is "strong"
            level = 'medium';
            text = `${translations.levels.medium}: ${feedback.join(', ')}`;
            color = 'warning';
        } else {
            level = 'strong';
            text = translations.levels.strong;
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

    /**
     * Enhanced email validation with domain checking
     * Uses global validation utility as base, adds domain validation
     * Supports both personal and business email domains
     */
    validateEmail(email) {
        // Use global email validation first
        if (window.validation && !window.validation.isEmail(email)) {
            return {
                valid: false,
                message: 'Địa chỉ email không đúng định dạng.'
            };
        }

        // Additional domain validation for registration
        const domain = email.split('@')[1]?.toLowerCase();

        // Check for suspicious patterns (but allow business domains)
        const suspiciousPatterns = [
            /^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$/, // IP addresses
            /\.test$/, /\.local$/, /\.localhost$/, // Test domains
            /example\.(com|org|net)$/, // Example domains
            /^[a-z]\.com$/, // Single letter domains
            /\.(tk|ml|ga|cf)$/ // Free domains often used for spam
        ];

        const isSuspicious = suspiciousPatterns.some(pattern => pattern.test(domain));

        if (isSuspicious) {
            return {
                valid: false,
                message: 'Vui lòng sử dụng địa chỉ email hợp lệ. Tránh sử dụng email tạm thời hoặc không uy tín.'
            };
        }

        // Check if domain has proper format
        const hasValidFormat = domain && domain.includes('.') && domain.length > 3;

        if (!hasValidFormat) {
            return {
                valid: false,
                message: 'Tên miền email không hợp lệ.'
            };
        }

        // Check account type to provide appropriate guidance
        const accountType = this.getSelectedAccountType();
        const isBusinessAccount = ['manufacturer', 'supplier', 'brand'].includes(accountType);

        // For business accounts, provide helpful guidance
        if (isBusinessAccount) {
            const commonPersonalDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
            if (commonPersonalDomains.includes(domain)) {
                return {
                    valid: true,
                    message: 'Email hợp lệ. Lưu ý: Bạn có thể sử dụng email công ty để tăng độ tin cậy.',
                    warning: true
                };
            }
        }

        return {
            valid: true,
            message: 'Email hợp lệ'
        };
    }

    /**
     * Get currently selected account type
     */
    getSelectedAccountType() {
        const selectedInput = document.querySelector('input[name="account_type"]:checked');
        return selectedInput ? selectedInput.value : 'member';
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

        // Remove password_confirmation from auto-save data to prevent server-side validation conflicts
        delete data.password_confirmation;

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

        // Skip server-side validation for password_confirmation - handle it client-side only
        if (fieldName === 'password_confirmation') {
            this.validatePasswordConfirmation();
            return;
        }

        // Handle client-side email validation first
        if (fieldName === 'email') {
            const emailValidation = this.validateEmail(fieldValue);
            if (!emailValidation.valid) {
                this.updateFieldValidation(field, false, emailValidation.message);
                return;
            } else if (emailValidation.warning) {
                this.updateFieldValidation(field, true, emailValidation.message, true);
                // Continue to server-side validation for uniqueness check
            }
        }

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

    updateFieldValidation(field, isValid, message, isWarning = false) {
        // Handle special naming for password_confirmation field
        let fieldName = field.name;
        if (fieldName === 'password_confirmation') {
            fieldName = 'password-confirmation';
        }

        const errorDiv = document.getElementById(`${fieldName}-error`);
        const successDiv = document.getElementById(`${fieldName}-success`);

        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');

            // Hide error message
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
                errorDiv.classList.remove('text-warning');
            }

            // Handle success/warning messages
            if (successDiv) {
                if (field.name === 'password_confirmation' || isWarning) {
                    successDiv.textContent = message;
                    successDiv.style.display = 'block';

                    // Apply warning styling if needed
                    if (isWarning) {
                        successDiv.classList.remove('text-success');
                        successDiv.classList.add('text-warning');
                    } else {
                        successDiv.classList.remove('text-warning');
                        successDiv.classList.add('text-success');
                    }
                } else {
                    // For other fields, hide success message - only show visual validation via is-valid class
                    successDiv.textContent = '';
                    successDiv.style.display = 'none';
                    successDiv.classList.remove('text-warning', 'text-success');
                }
            }
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');

            // Show error message
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            }
            // Hide success message
            if (successDiv) {
                successDiv.textContent = '';
                successDiv.style.display = 'none';
            }
        }
    }

    handleFieldInput(e) {
        const field = e.target;

        // Special handling for password confirmation - don't clear validation classes immediately
        if (field.name === 'password_confirmation') {
            // Delay validation to allow smooth typing
            setTimeout(() => this.validatePasswordConfirmation(), 300);
            return;
        }

        // Special handling for password field - also validate confirmation
        if (field.name === 'password') {
            const confirmationField = document.getElementById('password_confirmation');
            if (confirmationField) {
                // Always validate confirmation when password changes, even if confirmation is empty
                setTimeout(() => this.validatePasswordConfirmation(), 300);
            }
        }

        // For other fields, remove validation classes on input to allow re-validation
        field.classList.remove('is-valid', 'is-invalid');

        // Hide validation messages when user starts typing
        // Handle special naming for password_confirmation field
        let fieldName = field.name;
        if (fieldName === 'password_confirmation') {
            fieldName = 'password-confirmation';
        }

        const errorDiv = document.getElementById(`${fieldName}-error`);
        const successDiv = document.getElementById(`${fieldName}-success`);

        if (errorDiv) errorDiv.style.display = 'none';
        if (successDiv) successDiv.style.display = 'none';
    }

    validatePasswordConfirmation() {
        const password = document.getElementById('password');
        const confirmation = document.getElementById('password_confirmation');

        if (!password || !confirmation) return;

        // Only validate if confirmation field has content
        if (!confirmation.value) {
            // Clear validation when field is empty
            confirmation.classList.remove('is-valid', 'is-invalid');
            const errorDiv = document.getElementById('password-confirmation-error');
            const successDiv = document.getElementById('password-confirmation-success');
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
            if (successDiv) {
                successDiv.textContent = '';
                successDiv.style.display = 'none';
            }
            return;
        }

        const isMatch = password.value === confirmation.value && confirmation.value.length > 0;

        // Use translations if available, fallback to English
        const translations = window.authTranslations || {
            passwordMatch: 'Passwords match',
            passwordMismatch: 'Passwords do not match'
        };

        const message = isMatch ? translations.passwordMatch : translations.passwordMismatch;

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
    if (document.querySelector('.wizard-card')) {
        // Avoid conflicts with other form validation systems
        if (window.registrationWizard) {
            console.warn('Registration wizard already initialized');
            return;
        }

        // Disable global form validation for wizard forms
        const wizardForms = document.querySelectorAll('.wizard-card form');
        wizardForms.forEach(form => {
            form.removeAttribute('data-validate');
        });

        window.registrationWizard = new RegistrationWizard({
            autoSave: true,
            autoSaveInterval: 30000,
            validationDelay: 500,
            sessionTimeout: 1800000
        });

        console.log('Registration wizard initialized');
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RegistrationWizard;
}
