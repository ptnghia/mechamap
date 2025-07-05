/**
 * Theme Recovery Script
 * Handles theme system recovery and fallbacks
 */

(function() {
    'use strict';

    window.ThemeRecovery = {
        // Recover from theme system failures
        recover: function() {
            // console.log('[Theme Recovery] Starting recovery process...');

            try {
                // Clear corrupted theme data
                this.clearThemeData();

                // Reset to default theme
                this.resetToDefault();

                // Reinitialize theme system
                this.reinitialize();

                // console.log('[Theme Recovery] Recovery completed successfully');
                return true;
            } catch (e) {
                console.error('[Theme Recovery] Recovery failed:', e);
                return false;
            }
        },

        // Clear all theme-related data
        clearThemeData: function() {
            try {
                localStorage.removeItem('mechamap-theme');
                document.documentElement.removeAttribute('data-theme');
                document.documentElement.classList.remove('dark-theme');
                // console.log('[Theme Recovery] Cleared theme data');
            } catch (e) {
                console.error('[Theme Recovery] Failed to clear theme data:', e);
            }
        },

        // Reset to default light theme
        resetToDefault: function() {
            try {
                localStorage.setItem('mechamap-theme', 'light');
                document.documentElement.setAttribute('data-theme', 'light');
                // console.log('[Theme Recovery] Reset to default theme');
            } catch (e) {
                console.error('[Theme Recovery] Failed to reset theme:', e);
            }
        },

        // Reinitialize theme system
        reinitialize: function() {
            try {
                // Trigger theme system reinitialization
                if (window.ThemeManager && typeof window.ThemeManager.init === 'function') {
                    window.ThemeManager.init();
                }

                // Dispatch recovery event
                window.dispatchEvent(new CustomEvent('themeRecovered', {
                    detail: { timestamp: new Date().toISOString() }
                }));

                // console.log('[Theme Recovery] Reinitialized theme system');
            } catch (e) {
                console.error('[Theme Recovery] Failed to reinitialize:', e);
            }
        },

        // Check if recovery is needed
        isRecoveryNeeded: function() {
            const theme = localStorage.getItem('mechamap-theme');
            const attr = document.documentElement.getAttribute('data-theme');

            // Recovery needed if theme data is inconsistent
            return !theme || !attr || (theme !== attr);
        },

        // Auto-recovery on errors
        setupAutoRecovery: function() {
            window.addEventListener('error', (e) => {
                if (e.message && e.message.includes('theme')) {
                    console.warn('[Theme Recovery] Theme-related error detected, attempting recovery...');
                    this.recover();
                }
            });
        }
    };

    // Setup auto-recovery
    window.ThemeRecovery.setupAutoRecovery();

    // Expose recovery function globally
    window.recoverTheme = () => window.ThemeRecovery.recover();

})();
