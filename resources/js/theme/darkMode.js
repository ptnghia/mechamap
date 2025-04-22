/**
 * Dark mode functionality
 */

/**
 * Initialize dark mode
 */
export function initDarkMode() {
    // Check for saved theme preference or use system preference
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    // Set initial theme
    if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }

    // Update theme toggle button state
    updateThemeToggle();

    // Add event listener to theme toggle buttons
    const themeToggles = document.querySelectorAll('[data-toggle-theme], #theme-toggle');
    themeToggles.forEach(toggle => {
        // Remove existing event listeners to prevent duplicates
        toggle.removeEventListener('click', toggleTheme);
        // Add new event listener
        toggle.addEventListener('click', toggleTheme);
    });
}

/**
 * Toggle between light and dark themes
 */
function toggleTheme() {
    if (localStorage.getItem('theme') === 'light') {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }

    updateThemeToggle();
}

/**
 * Update theme toggle button state
 */
function updateThemeToggle() {
    const themeToggles = document.querySelectorAll('[data-toggle-theme], #theme-toggle');
    const isDarkMode = localStorage.getItem('theme') === 'dark';

    themeToggles.forEach(toggle => {
        const lightIcon = toggle.querySelector('.light-icon');
        const darkIcon = toggle.querySelector('.dark-icon');

        if (lightIcon && darkIcon) {
            if (isDarkMode) {
                lightIcon.classList.remove('hidden');
                lightIcon.classList.remove('d-none');
                darkIcon.classList.add('hidden');
                darkIcon.classList.add('d-none');
            } else {
                lightIcon.classList.add('hidden');
                lightIcon.classList.add('d-none');
                darkIcon.classList.remove('hidden');
                darkIcon.classList.remove('d-none');
            }
        }
    });

    // Cập nhật body class cho Bootstrap
    if (isDarkMode) {
        document.body.classList.add('dark');
    } else {
        document.body.classList.remove('dark');
    }
}
