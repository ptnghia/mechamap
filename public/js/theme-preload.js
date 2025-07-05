/**
 * Theme Preload Script
 * Loads theme before page render to prevent flash
 */

(function() {
    'use strict';

    // Get saved theme from localStorage
    const savedTheme = localStorage.getItem('mechamap-theme') || 'light';

    // Apply theme immediately to prevent flash
    document.documentElement.setAttribute('data-theme', savedTheme);

    // Add theme class to body
    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark-theme');
    }

    // console.log('[Theme Preload] Applied theme:', savedTheme);
})();
