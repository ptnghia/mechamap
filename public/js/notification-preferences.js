/**
 * Notification Preferences Management
 * Handles interactive features for notification settings
 */

class NotificationPreferences {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateDependentStates();
        console.log('✅ Notification Preferences initialized');
    }

    bindEvents() {
        // Global toggles
        document.querySelectorAll('.global-toggle').forEach(toggle => {
            toggle.addEventListener('change', (e) => {
                this.handleGlobalToggle(e.target);
            });
        });

        // Category toggles
        document.querySelectorAll('.category-toggle').forEach(toggle => {
            toggle.addEventListener('change', (e) => {
                this.handleCategoryToggle(e.target);
            });
        });

        // Type toggles
        document.querySelectorAll('.type-toggle').forEach(toggle => {
            toggle.addEventListener('change', (e) => {
                this.handleTypeToggle(e.target);
            });
        });

        // Quick action buttons
        document.getElementById('enable-all')?.addEventListener('click', () => {
            this.enableAll();
        });

        document.getElementById('disable-all')?.addEventListener('click', () => {
            this.disableAll();
        });

        document.getElementById('reset-defaults')?.addEventListener('click', () => {
            this.resetToDefaults();
        });

        // Form submission
        document.getElementById('notification-preferences-form')?.addEventListener('submit', (e) => {
            this.handleFormSubmit(e);
        });
    }

    handleGlobalToggle(toggle) {
        const method = toggle.dataset.method;
        const isEnabled = toggle.checked;

        // Update all type toggles for this method
        document.querySelectorAll(`.type-toggle[data-method="${method}"]`).forEach(typeToggle => {
            typeToggle.disabled = !isEnabled;
            if (!isEnabled) {
                typeToggle.checked = false;
            }
        });

        // Update delivery method settings
        const deliveryCard = document.querySelector(`#frequency_${method}`)?.closest('.card');
        if (deliveryCard) {
            deliveryCard.style.opacity = isEnabled ? '1' : '0.5';
            deliveryCard.querySelectorAll('input, select').forEach(input => {
                input.disabled = !isEnabled;
            });
        }

        this.showToast(
            isEnabled ? 'success' : 'info',
            `${this.getMethodName(method)} ${isEnabled ? 'đã bật' : 'đã tắt'}`
        );
    }

    handleCategoryToggle(toggle) {
        const category = toggle.dataset.category;
        const isEnabled = toggle.checked;

        // Update all type toggles in this category
        document.querySelectorAll(`.type-toggle[data-category="${category}"]`).forEach(typeToggle => {
            typeToggle.disabled = !isEnabled;
            if (!isEnabled) {
                typeToggle.checked = false;
            }
        });

        // Update visual state of the category section
        const categorySection = document.querySelector(`.notification-types[data-category="${category}"]`);
        if (categorySection) {
            categorySection.style.opacity = isEnabled ? '1' : '0.5';
        }

        this.showToast(
            isEnabled ? 'success' : 'info',
            `Danh mục ${this.getCategoryName(category)} ${isEnabled ? 'đã bật' : 'đã tắt'}`
        );
    }

    handleTypeToggle(toggle) {
        const category = toggle.dataset.category;
        const type = toggle.dataset.type;
        const method = toggle.dataset.method;
        const isEnabled = toggle.checked;

        // Check if global method is enabled
        const globalToggle = document.querySelector(`.global-toggle[data-method="${method}"]`);
        if (globalToggle && !globalToggle.checked) {
            toggle.checked = false;
            this.showToast('warning', `Vui lòng bật ${this.getMethodName(method)} trước`);
            return;
        }

        // Check if category is enabled
        const categoryToggle = document.querySelector(`.category-toggle[data-category="${category}"]`);
        if (categoryToggle && !categoryToggle.checked) {
            toggle.checked = false;
            this.showToast('warning', `Vui lòng bật danh mục ${this.getCategoryName(category)} trước`);
            return;
        }
    }

    enableAll() {
        // Enable all global toggles
        document.querySelectorAll('.global-toggle').forEach(toggle => {
            toggle.checked = true;
            this.handleGlobalToggle(toggle);
        });

        // Enable all category toggles
        document.querySelectorAll('.category-toggle').forEach(toggle => {
            toggle.checked = true;
            this.handleCategoryToggle(toggle);
        });

        // Enable all type toggles
        document.querySelectorAll('.type-toggle').forEach(toggle => {
            toggle.checked = true;
        });

        this.showToast('success', 'Đã bật tất cả thông báo');
    }

    disableAll() {
        // Disable all type toggles first
        document.querySelectorAll('.type-toggle').forEach(toggle => {
            toggle.checked = false;
        });

        // Disable all category toggles
        document.querySelectorAll('.category-toggle').forEach(toggle => {
            toggle.checked = false;
            this.handleCategoryToggle(toggle);
        });

        this.showToast('info', 'Đã tắt tất cả thông báo');
    }

    resetToDefaults() {
        if (!confirm('Bạn có chắc muốn đặt lại về cài đặt mặc định?')) {
            return;
        }

        // Reset to default values (implement based on your default preferences)
        this.applyDefaults();
        this.showToast('info', 'Đã đặt lại về cài đặt mặc định');
    }

    applyDefaults() {
        // Enable email and push globally
        document.querySelector('.global-toggle[data-method="email"]').checked = true;
        document.querySelector('.global-toggle[data-method="push"]').checked = true;
        document.querySelector('.global-toggle[data-method="sms"]').checked = false;
        document.querySelector('.global-toggle[data-method="in_app"]').checked = true;

        // Enable all categories
        document.querySelectorAll('.category-toggle').forEach(toggle => {
            toggle.checked = true;
            this.handleCategoryToggle(toggle);
        });

        // Set default type preferences
        document.querySelectorAll('.type-toggle').forEach(toggle => {
            const method = toggle.dataset.method;
            const category = toggle.dataset.category;

            if (method === 'email') {
                // Only important categories via email
                toggle.checked = ['security', 'marketplace'].includes(category);
            } else if (method === 'push') {
                toggle.checked = true;
            } else if (method === 'sms') {
                // Only security via SMS
                toggle.checked = category === 'security';
            } else if (method === 'in_app') {
                toggle.checked = true;
            }
        });

        this.updateDependentStates();
    }

    updateDependentStates() {
        // Update all dependent states based on current toggles
        document.querySelectorAll('.global-toggle').forEach(toggle => {
            this.handleGlobalToggle(toggle);
        });

        document.querySelectorAll('.category-toggle').forEach(toggle => {
            this.handleCategoryToggle(toggle);
        });
    }

    handleFormSubmit(e) {
        // Show loading state
        const submitButton = e.target.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang lưu...';
        submitButton.disabled = true;

        // Re-enable after a delay (form will submit normally)
        setTimeout(() => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }, 2000);
    }

    getMethodName(method) {
        const names = {
            'email': 'Email',
            'push': 'Push Notification',
            'sms': 'SMS',
            'in_app': 'Trong ứng dụng'
        };
        return names[method] || method;
    }

    getCategoryName(category) {
        const names = {
            'messages': 'Tin nhắn',
            'forum': 'Diễn đàn',
            'marketplace': 'Thương mại',
            'security': 'Bảo mật',
            'social': 'Xã hội',
            'system': 'Hệ thống'
        };
        return names[category] || category;
    }

    showToast(type, message) {
        // Use SweetAlert2 if available, otherwise console log
        if (window.Swal) {
            const icon = type === 'success' ? 'success' : 
                        type === 'warning' ? 'warning' : 'info';
            
            Swal.fire({
                icon: icon,
                title: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            console.log(`[${type.toUpperCase()}] ${message}`);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new NotificationPreferences();
});
