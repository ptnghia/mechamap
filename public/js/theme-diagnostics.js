/**
 * Theme Diagnostics Script
 * Provides debugging information for theme system
 */

(function() {
    'use strict';
    
    window.ThemeDiagnostics = {
        // Check theme system health
        checkThemeHealth: function() {
            const diagnostics = {
                localStorage: this.checkLocalStorage(),
                themeAttribute: this.checkThemeAttribute(),
                themeButton: this.checkThemeButton(),
                cssVariables: this.checkCSSVariables(),
                timestamp: new Date().toISOString()
            };
            
            console.log('[Theme Diagnostics] Health check:', diagnostics);
            return diagnostics;
        },
        
        // Check localStorage functionality
        checkLocalStorage: function() {
            try {
                const testKey = 'mechamap-theme-test';
                localStorage.setItem(testKey, 'test');
                const result = localStorage.getItem(testKey) === 'test';
                localStorage.removeItem(testKey);
                return { working: result, currentTheme: localStorage.getItem('mechamap-theme') };
            } catch (e) {
                return { working: false, error: e.message };
            }
        },
        
        // Check theme attribute
        checkThemeAttribute: function() {
            const attr = document.documentElement.getAttribute('data-theme');
            return { 
                present: !!attr, 
                value: attr,
                classList: Array.from(document.documentElement.classList)
            };
        },
        
        // Check theme button
        checkThemeButton: function() {
            const button = document.querySelector('[data-theme-toggle]');
            return {
                found: !!button,
                visible: button ? window.getComputedStyle(button).display !== 'none' : false
            };
        },
        
        // Check CSS variables
        checkCSSVariables: function() {
            const style = getComputedStyle(document.documentElement);
            return {
                backgroundColor: style.getPropertyValue('--bs-body-bg') || style.backgroundColor,
                textColor: style.getPropertyValue('--bs-body-color') || style.color
            };
        },
        
        // Auto-run diagnostics on load
        init: function() {
            if (window.location.search.includes('theme-debug')) {
                setTimeout(() => this.checkThemeHealth(), 1000);
            }
        }
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => window.ThemeDiagnostics.init());
    } else {
        window.ThemeDiagnostics.init();
    }
})();
