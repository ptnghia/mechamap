/**
 * Theme initialization
 */

import { initDarkMode } from './darkMode';
import { initMobileMenu, initDropdowns, initSmoothScroll } from './navigation';
import { initTooltips } from './tooltips';

/**
 * Initialize all theme components
 */
export function initTheme() {
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize dark mode
        initDarkMode();
        
        // Initialize navigation
        initMobileMenu();
        initDropdowns();
        initSmoothScroll();
        
        // Initialize tooltips
        initTooltips();
    });
}

// Auto-initialize theme
initTheme();
