/**
 * Dark mode functionality
 */

/**
 * Initialize dark mode
 */
export function initDarkMode() {
    // Check for cookie preference first, then localStorage, then system preference
    const darkModeCookie = getCookie('dark_mode');
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    // Set initial theme based on cookie first
    if (darkModeCookie === 'dark') {
        applyDarkMode(true);
    } else if (darkModeCookie === 'light') {
        applyDarkMode(false);
    } else if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
        applyDarkMode(true);
    } else {
        applyDarkMode(false);
    }

    // Add event listener to theme toggle buttons
    const themeToggles = document.querySelectorAll('[data-toggle-theme], #theme-toggle, #darkModeSwitch');
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
function toggleTheme(e) {
    const isDarkMode = document.documentElement.classList.contains('dark');
    const newMode = !isDarkMode;

    // Apply the new mode
    applyDarkMode(newMode);

    // If this is a checkbox, make sure it's checked state matches the theme
    if (e.target.type === 'checkbox') {
        e.target.checked = newMode;
    }

    // Sync with server via AJAX to update cookie
    syncWithServer(newMode);
}

/**
 * Apply dark mode to the document
 */
function applyDarkMode(isDarkMode) {
    if (isDarkMode) {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }

    updateThemeToggle(isDarkMode);
}

/**
 * Update theme toggle button state
 */
function updateThemeToggle(isDarkMode) {
    const themeToggles = document.querySelectorAll('[data-toggle-theme], #theme-toggle');

    // Update checkbox state
    const darkModeSwitch = document.getElementById('darkModeSwitch');
    if (darkModeSwitch) {
        darkModeSwitch.checked = isDarkMode;
    }

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
}

/**
 * Sync theme preference with server
 */
function syncWithServer(isDarkMode) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) return;

    fetch('/theme/dark-mode', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            dark_mode: isDarkMode ? 'dark' : 'light'
        })
    }).catch(error => console.error('Error syncing theme preference:', error));
}

/**
 * Get cookie value by name
 */
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}
