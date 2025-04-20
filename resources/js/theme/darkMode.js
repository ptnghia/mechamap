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
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
    
    // Update theme toggle button state
    updateThemeToggle();
    
    // Add event listener to theme toggle buttons
    const themeToggles = document.querySelectorAll('[data-toggle-theme]');
    themeToggles.forEach(toggle => {
        toggle.addEventListener('click', toggleTheme);
    });
}

/**
 * Toggle between light and dark themes
 */
function toggleTheme() {
    if (localStorage.getItem('theme') === 'light') {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
    
    updateThemeToggle();
}

/**
 * Update theme toggle button state
 */
function updateThemeToggle() {
    const themeToggles = document.querySelectorAll('[data-toggle-theme]');
    const isDarkMode = localStorage.getItem('theme') === 'dark';
    
    themeToggles.forEach(toggle => {
        const lightIcon = toggle.querySelector('.light-icon');
        const darkIcon = toggle.querySelector('.dark-icon');
        
        if (lightIcon && darkIcon) {
            if (isDarkMode) {
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
            } else {
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
            }
        }
    });
}
