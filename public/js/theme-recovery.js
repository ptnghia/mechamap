/**
 * Theme Recovery System
 * Kiểm tra và khôi phục chức năng chuyển đổi theme nếu nút không hoạt động
 */
(function() {
    'use strict';

    // Wait until page is fully loaded
    window.addEventListener('load', function() {
        // Wait a moment to ensure all other scripts have run
        setTimeout(checkThemeToggleStatus, 3000);
    });

    function checkThemeToggleStatus() {
        console.log('[Theme Recovery] Checking theme toggle functionality...');

        var themeToggle = document.getElementById('theme-toggle');
        var darkModeSwitch = document.getElementById('darkModeSwitch');

        if (!themeToggle && !darkModeSwitch) {
            console.log('[Theme Recovery] No theme toggle elements found');
            return;
        }

        // Check if theme elements have click handlers
        var toggleHasHandler = false;
        var switchHasHandler = false;

        // Try to detect if handlers exist
        if (themeToggle) {
            var clonedToggle = themeToggle.cloneNode(true);
            // If replacing the element removes event handlers, it means handlers existed
            toggleHasHandler = themeToggle.onclick !== null ||
                              themeToggle.getAttribute('onclick') !== null ||
                              themeToggle._hasClickHandler;

            console.log('[Theme Recovery] Theme toggle button has handlers:', toggleHasHandler);
        }

        if (darkModeSwitch) {
            var clonedSwitch = darkModeSwitch.cloneNode(true);
            switchHasHandler = darkModeSwitch.onchange !== null ||
                              darkModeSwitch.getAttribute('onchange') !== null;

            console.log('[Theme Recovery] Dark mode switch has handlers:', switchHasHandler);
        }

        // If no handlers exist, attempt to restore functionality
        if (themeToggle && !toggleHasHandler) {
            console.log('[Theme Recovery] Restoring theme toggle button functionality');
            themeToggle.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('[Theme Recovery] Manual theme toggle triggered');
                toggleTheme();
            });
        }

        if (darkModeSwitch && !switchHasHandler) {
            console.log('[Theme Recovery] Restoring dark mode switch functionality');
            darkModeSwitch.addEventListener('change', function() {
                console.log('[Theme Recovery] Manual switch toggle triggered');
                var newTheme = this.checked ? 'dark' : 'light';
                applyTheme(newTheme);
                if (themeToggle) {
                    updateButtonAppearance(themeToggle, newTheme);
                }
            });
        }
    }

    function toggleTheme() {
        // Get current theme
        var htmlEl = document.documentElement;
        var currentTheme = htmlEl.getAttribute('data-theme') || 'light';
        var newTheme = currentTheme === 'light' ? 'dark' : 'light';

        // Apply theme changes
        applyTheme(newTheme);

        // Update UI elements
        var themeToggle = document.getElementById('theme-toggle');
        var darkModeSwitch = document.getElementById('darkModeSwitch');

        if (themeToggle) {
            updateButtonAppearance(themeToggle, newTheme);
        }

        if (darkModeSwitch) {
            darkModeSwitch.checked = newTheme === 'dark';
        }
    }

    function applyTheme(theme) {
        // Apply theme attribute
        document.documentElement.setAttribute('data-theme', theme);

        // Toggle body class
        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }

        // Store preference
        try {
            localStorage.setItem('theme', theme);
            document.cookie = "dark_mode=" + theme + "; path=/; max-age=31536000";
        } catch (e) {
            console.error('[Theme Recovery] Error saving theme preference:', e);
        }
    }

    function updateButtonAppearance(button, theme) {
        var darkIcon = button.querySelector('.dark-icon');
        var lightIcon = button.querySelector('.light-icon');

        if (!darkIcon || !lightIcon) {
            console.log('[Theme Recovery] Icons not found in button');
            return;
        }

        if (theme === 'dark') {
            darkIcon.classList.add('d-none');
            lightIcon.classList.remove('d-none');
        } else {
            darkIcon.classList.remove('d-none');
            lightIcon.classList.add('d-none');
        }
    }
})();
