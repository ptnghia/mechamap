/**
 * Dark Mode Toggle JavaScript
 * Xử lý chuyển đổi theme sáng/tối
 */

(function() {
    'use strict';

    // Debug mode
    const DEBUG = false;

    // Log function that only works in debug mode
    function log(message) {
        if (DEBUG) {
            console.log('[Theme Switcher] ' + message);
        }
    }

    /**
     * Setup keyboard shortcut for toggling theme (Ctrl+Shift+D)
     */
    function setupKeyboardShortcut() {
        document.addEventListener('keydown', function(e) {
            // Check for Ctrl+Shift+D
            if (e.ctrlKey && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                const currentTheme = getCurrentTheme();
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                log('Keyboard shortcut detected: toggling theme to ' + newTheme);
                applyTheme(newTheme);
                localStorage.setItem('theme', newTheme);

                // Update switch state
                const darkModeSwitch = document.getElementById('darkModeSwitch');
                if (darkModeSwitch) {
                    darkModeSwitch.checked = newTheme === 'dark';
                }

                // Show visual feedback
                const feedback = document.createElement('div');
                feedback.style.position = 'fixed';
                feedback.style.top = '10px';
                feedback.style.left = '50%';
                feedback.style.transform = 'translateX(-50%)';
                feedback.style.backgroundColor = 'rgba(0,0,0,0.7)';
                feedback.style.color = 'white';
                feedback.style.padding = '8px 16px';
                feedback.style.borderRadius = '4px';
                feedback.style.zIndex = '9999';
                feedback.textContent = newTheme === 'dark' ? 'Đã chuyển sang chế độ tối' : 'Đã chuyển sang chế độ sáng';
                document.body.appendChild(feedback);

                setTimeout(() => {
                    feedback.style.opacity = '0';
                    feedback.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => feedback.remove(), 500);
                }, 1500);
            }
        });
    }

    // Initialize theme when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        log('DOM loaded - initializing theme system');
        initializeTheme();
        setupThemeToggle();
        setupDarkModeSwitch();
        setupKeyboardShortcut();
    });

    // Fallback in case DOMContentLoaded doesn't fire (đã tải trang rồi)
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        log('DOM already loaded - initializing theme system');
        setTimeout(function() {
            initializeTheme();
            setupThemeToggle();
            setupDarkModeSwitch();
            setupKeyboardShortcut();
        }, 1);
    }

    /**
     * Initialize theme based on saved preference
     */
    function initializeTheme() {
        // Kiểm tra localStorage hoạt động không
        try {
            const testKey = 'test_localStorage';
            localStorage.setItem(testKey, testKey);
            localStorage.removeItem(testKey);
            log('localStorage is working properly');
        } catch (e) {
            log('localStorage error: ' + e.message);
            // Fallback to default theme
            applyTheme('light');
            return;
        }

        const savedTheme = localStorage.getItem('theme') || 'light';
        log('Initializing theme: ' + savedTheme);
        applyTheme(savedTheme);

        // Make sure dropdown reflects the current theme on page load
        updateThemeDropdownLabel(savedTheme);
    }

    /**
     * Setup theme toggle button event
     */
    function setupThemeToggle() {
        // Tìm nút bằng cả ID và data-attribute
        const toggleButton = document.getElementById('theme-toggle') ||
                            document.querySelector('[data-toggle-theme]');

        if (toggleButton) {
            log('Theme toggle button found');

            // Xóa các event listeners trước đó để tránh xung đột
            const newButton = toggleButton.cloneNode(true);
            toggleButton.parentNode.replaceChild(newButton, toggleButton);

            newButton.addEventListener('click', function(e) {
                log('Theme toggle button clicked');
                e.preventDefault();

                // Add click animation
                this.classList.add('clicked');
                const indicator = this.querySelector('.theme-toggle-indicator');
                if (indicator) {
                    indicator.style.display = 'block';
                    setTimeout(() => {
                        this.classList.remove('clicked');
                        setTimeout(() => {
                            indicator.style.display = 'none';
                        }, 700);
                    }, 700);
                }

                const currentTheme = getCurrentTheme();
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                log('Switching to theme: ' + newTheme);
                applyTheme(newTheme);
                localStorage.setItem('theme', newTheme);

                // Dispatch a custom event that can be listened to
                document.dispatchEvent(new CustomEvent('themeToggled', {
                    detail: { theme: newTheme }
                }));
            });
        } else {
            log('ERROR: Theme toggle button not found in DOM');
        }
    }

    /**
     * Get current theme
     */
    function getCurrentTheme() {
        return document.documentElement.getAttribute('data-theme') || 'light';
    }

    /**
     * Setup Dark Mode Switch (the checkbox in the dropdown menu)
     */
    function setupDarkModeSwitch() {
        const darkModeSwitch = document.getElementById('darkModeSwitch');

        if (darkModeSwitch) {
            log('Dark mode switch found');

            // Ensure the switch reflects the current theme
            const currentTheme = getCurrentTheme();
            darkModeSwitch.checked = currentTheme === 'dark';

            // Listen for changes
            darkModeSwitch.addEventListener('change', function() {
                log('Dark mode switch changed to: ' + this.checked);
                const newTheme = this.checked ? 'dark' : 'light';
                applyTheme(newTheme);
                localStorage.setItem('theme', newTheme);

                // Update theme toggle button to be consistent
                updateThemeToggleButton(newTheme);

                // Set the cookie for server-side detection
                document.cookie = "dark_mode=" + newTheme + "; path=/; max-age=31536000"; // 1 year
            });

            // Listen for theme changes from other sources
            document.addEventListener('themeToggled', function(e) {
                log('Theme toggle event received by dark mode switch');
                darkModeSwitch.checked = e.detail.theme === 'dark';
            });
        } else {
            log('Dark mode switch not found');
        }
    }

    /**
     * Apply theme to document
     */
    function applyTheme(theme) {
        try {
            document.documentElement.setAttribute('data-theme', theme);
            log('Applied theme attribute: data-theme="' + theme + '"');

            // Thay đổi class trên body để đảm bảo CSS được kích hoạt
            if (theme === 'dark') {
                document.body.classList.add('dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
            }

            updateThemeToggleButton(theme);
            updateThemeDropdownLabel(theme);

            // Console log để debug CSS
            if (DEBUG) {
                const computedStyle = window.getComputedStyle(document.body);
                log('Body background color: ' + computedStyle.backgroundColor);
                log('Body text color: ' + computedStyle.color);
            }

            // Trigger custom event for other scripts
            window.dispatchEvent(new CustomEvent('themeChanged', {
                detail: { theme: theme }
            }));
            log('Dispatched themeChanged event');
        } catch (e) {
            log('Error applying theme: ' + e.message);
        }
    }

    /**
     * Update theme toggle button appearance
     */
    function updateThemeToggleButton(theme) {
        // Tìm nút bằng cả ID và data-attribute
        const toggleButton = document.getElementById('theme-toggle') ||
                             document.querySelector('[data-toggle-theme]');

        if (!toggleButton) {
            log('ERROR: Button not found when updating appearance');
            return;
        }

        try {
            const darkIcon = toggleButton.querySelector('.dark-icon');
            const lightIcon = toggleButton.querySelector('.light-icon');

            if (!darkIcon || !lightIcon) {
                log('WARNING: Theme icons not found in button');
                return;
            }

            if (theme === 'dark') {
                darkIcon.classList.add('d-none');
                lightIcon.classList.remove('d-none');
                log('Updated button icons to dark theme');
            } else {
                darkIcon.classList.remove('d-none');
                lightIcon.classList.add('d-none');
                log('Updated button icons to light theme');
            }
        } catch (e) {
            log('Error updating toggle button: ' + e.message);
        }
    }

    /**
     * Update theme dropdown menu label and icons
     */
    function updateThemeDropdownLabel(theme) {
        // Tìm phần tử chứa label và icons trong dropdown
        const themeLabel = document.getElementById('themeLabel');

        if (!themeLabel) {
            log('ERROR: Theme label not found when updating dropdown');
            return;
        }

        try {
            const darkIcon = themeLabel.querySelector('.theme-icon-dark');
            const lightIcon = themeLabel.querySelector('.theme-icon-light');
            const themeText = themeLabel.querySelector('.theme-text');

            if (!darkIcon || !lightIcon || !themeText) {
                log('WARNING: Theme icons or text not found in dropdown');
                return;
            }

            if (theme === 'dark') {
                // Đang ở chế độ tối, hiển thị icon mặt trời và "Chế độ sáng"
                // (để user biết sẽ chuyển sang chế độ gì khi click)
                darkIcon.classList.add('d-none');
                lightIcon.classList.remove('d-none');
                themeText.textContent = 'Chế độ sáng';
                log('Updated dropdown to show light mode option');
            } else {
                // Đang ở chế độ sáng, hiển thị icon mặt trăng và "Chế độ tối"
                darkIcon.classList.remove('d-none');
                lightIcon.classList.add('d-none');
                themeText.textContent = 'Chế độ tối';
                log('Updated dropdown to show dark mode option');
            }
        } catch (e) {
            log('Error updating theme dropdown: ' + e.message);
        }
    }

    // Export functions to global scope if needed
    window.themeManager = {
        getCurrentTheme: getCurrentTheme,
        applyTheme: applyTheme,
        toggle: function() {
            const current = getCurrentTheme();
            const newTheme = current === 'light' ? 'dark' : 'light';
            log('Manual toggle called: switching to ' + newTheme);
            applyTheme(newTheme);
            localStorage.setItem('theme', newTheme);
            return newTheme;
        },
        debug: function() {
            // Trả về thông tin debug để kiểm tra trong console
            const toggleButton = document.getElementById('theme-toggle') ||
                                document.querySelector('[data-toggle-theme]');
            return {
                currentTheme: getCurrentTheme(),
                savedTheme: localStorage.getItem('theme'),
                buttonFound: !!toggleButton,
                buttonId: toggleButton ? toggleButton.id : null,
                buttonAttributes: toggleButton ? Array.from(toggleButton.attributes).map(a => a.name + '=' + a.value) : [],
                hasDarkClass: document.body.classList.contains('dark-mode'),
                htmlDataTheme: document.documentElement.getAttribute('data-theme'),
                localStorageWorks: (function() {
                    try {
                        localStorage.setItem('test', 'test');
                        localStorage.removeItem('test');
                        return true;
                    } catch(e) {
                        return false;
                    }
                })()
            };
        }
    };

    // Chạy debugger nếu được yêu cầu qua URL
    if (window.location.search.includes('debug-theme')) {
        window.addEventListener('load', function() {
            // console.log('Theme Debugger:', window.themeManager.debug());
        });
    }

    // Thêm hỗ trợ phím tắt để chuyển đổi theme (Ctrl+Shift+D)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'd') {
            log('Keyboard shortcut for theme toggle detected');
            window.themeManager.toggle();
        }
    });

})();
