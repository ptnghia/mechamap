/**
 * Dark Mode Toggle JavaScript
 * Xử lý chuyển đổi theme sáng/tối
 */

(function() {
    'use strict';

    // Initialize theme when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeTheme();
        setupThemeToggle();
    });

    /**
     * Initialize theme based on saved preference
     */
    function initializeTheme() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        applyTheme(savedTheme);
    }

    /**
     * Setup theme toggle button event
     */
    function setupThemeToggle() {
        const toggleButton = document.getElementById('theme-toggle');
        if (toggleButton) {
            toggleButton.addEventListener('click', function() {
                const currentTheme = getCurrentTheme();
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                applyTheme(newTheme);
                localStorage.setItem('theme', newTheme);
            });
        }
    }

    /**
     * Get current theme
     */
    function getCurrentTheme() {
        return document.documentElement.getAttribute('data-theme') || 'light';
    }

    /**
     * Apply theme to document
     */
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        updateThemeToggleButton(theme);

        // Trigger custom event for other scripts
        window.dispatchEvent(new CustomEvent('themeChanged', {
            detail: { theme: theme }
        }));
    }

    /**
     * Update theme toggle button appearance
     */
    function updateThemeToggleButton(theme) {
        const toggleButton = document.getElementById('theme-toggle');
        if (!toggleButton) return;

        const darkIcon = toggleButton.querySelector('.dark-icon');
        const lightIcon = toggleButton.querySelector('.light-icon');

        if (theme === 'dark') {
            darkIcon?.classList.add('d-none');
            lightIcon?.classList.remove('d-none');
        } else {
            darkIcon?.classList.remove('d-none');
            lightIcon?.classList.add('d-none');
        }
    }

    // Export functions to global scope if needed
    window.themeManager = {
        getCurrentTheme: getCurrentTheme,
        applyTheme: applyTheme,
        toggle: function() {
            const current = getCurrentTheme();
            const newTheme = current === 'light' ? 'dark' : 'light';
            applyTheme(newTheme);
            localStorage.setItem('theme', newTheme);
        }
    };

})();
