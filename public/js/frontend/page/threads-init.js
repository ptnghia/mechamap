/**
 * Threads Page Initialization with Translation Support
 * Handles loading translations and initializing thread functionality
 */

// Wait for DOM and translation service to be ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Threads page DOM loaded');
    
    // Check if translation service is available
    if (window.translationService) {
        // Initialize threads with translation support
        initializeThreadsWithTranslations();
    } else {
        // Wait for translation service to load
        document.addEventListener('translationsLoaded', function(event) {
            console.log('Translation service loaded, initializing threads');
            initializeThreadsWithTranslations();
        });
        
        // Fallback: initialize after a short delay if translation service doesn't load
        setTimeout(() => {
            if (typeof initializeThreadActions === 'function') {
                console.warn('Initializing threads without translation service');
                initializeThreadActions();
            }
        }, 2000);
    }
});

// Handle language switching
document.addEventListener('languageChanged', function(event) {
    console.log('Language changed in threads page:', event.detail.locale);
    
    // Update translation service locale
    if (window.translationService) {
        window.translationService.setLocale(event.detail.locale);
        
        // Clear cache and reload translations
        window.translationService.clearCache();
        
        // Reload translations and update UI
        window.translationService.loadTranslations(['ui', 'features'])
            .then(() => {
                console.log('Translations reloaded for new language');
                updateThreadsUILanguage();
            })
            .catch(error => {
                console.error('Failed to reload translations:', error);
            });
    }
});

// Global function to refresh thread translations (can be called from other scripts)
window.refreshThreadTranslations = function() {
    if (window.translationService && typeof updateThreadsUILanguage === 'function') {
        updateThreadsUILanguage();
    }
};

// Export for module systems if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initializeThreadsWithTranslations,
        updateThreadsUILanguage
    };
}
