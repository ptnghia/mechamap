/**
 * Translation Service for MechaMap
 * Handles loading and caching translations from database
 */
class TranslationService {
    constructor() {
        this.translations = {};
        this.currentLocale = document.documentElement.lang || 'vi';
        this.cache = new Map();
        this.loadPromises = new Map();

        // Initialize with any existing translations
        if (window.Laravel && window.Laravel.translations) {
            this.translations = window.Laravel.translations;
        }

        //console.log('TranslationService initialized with locale:', this.currentLocale);
    }

    /**
     * Get translation for a key
     * @param {string} key - Translation key (e.g., 'notifications.default.title')
     * @param {object} replacements - Key-value pairs for replacements
     * @param {string} fallback - Fallback text if translation not found
     * @returns {string} Translated text
     */
    trans(key, replacements = {}, fallback = null) {
        let translation = this.getNestedValue(this.translations, key);

        if (!translation) {
            translation = fallback || key;
        }

        // Handle replacements
        if (typeof translation === 'string' && Object.keys(replacements).length > 0) {
            Object.keys(replacements).forEach(placeholder => {
                const regex = new RegExp(`:${placeholder}`, 'g');
                translation = translation.replace(regex, replacements[placeholder]);
            });
        }

        return translation;
    }

    /**
     * Load translations for specific groups
     * @param {string|array} groups - Translation groups to load
     * @param {string} locale - Locale to load (defaults to current)
     * @returns {Promise} Promise that resolves when translations are loaded
     */
    async loadTranslations(groups = ['notifications'], locale = null) {
        locale = locale || this.currentLocale;
        const groupsArray = Array.isArray(groups) ? groups : [groups];
        const cacheKey = `${locale}_${groupsArray.join('_')}`;

        // Return existing promise if already loading
        if (this.loadPromises.has(cacheKey)) {
            return this.loadPromises.get(cacheKey);
        }

        // Return cached translations if available
        if (this.cache.has(cacheKey)) {
            this.translations = { ...this.translations, ...this.cache.get(cacheKey) };
            return Promise.resolve(this.translations);
        }

        console.log('Loading translations for groups:', groupsArray, 'locale:', locale);

        const loadPromise = this.fetchTranslations(groupsArray, locale)
            .then(translations => {
                // Cache the translations
                this.cache.set(cacheKey, translations);

                // Merge with existing translations
                this.translations = { ...this.translations, ...translations };

                console.log('Translations loaded successfully:', translations);
                return this.translations;
            })
            .catch(error => {
                console.error('Failed to load translations:', error);
                throw error;
            })
            .finally(() => {
                // Remove from loading promises
                this.loadPromises.delete(cacheKey);
            });

        this.loadPromises.set(cacheKey, loadPromise);
        return loadPromise;
    }

    /**
     * Load notification-specific translations
     * @param {string} locale - Locale to load
     * @returns {Promise} Promise that resolves when notification translations are loaded
     */
    async loadNotificationTranslations(locale = null) {
        locale = locale || this.currentLocale;
        const cacheKey = `notifications_${locale}`;

        if (this.loadPromises.has(cacheKey)) {
            return this.loadPromises.get(cacheKey);
        }

        if (this.cache.has(cacheKey)) {
            this.translations = { ...this.translations, ...this.cache.get(cacheKey) };
            return Promise.resolve(this.translations);
        }

        //console.log('Loading notification translations for locale:', locale);

        const loadPromise = fetch(`/api/translations/notifications?locale=${locale}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Handle wrapped response format
                let responseData = data;
                if (data.data && data.data.success && data.data.translations) {
                    responseData = data.data;
                } else if (data.success && data.translations) {
                    responseData = data;
                } else {
                    console.error('Invalid response format:', data);
                    throw new Error('Invalid response format');
                }

                this.cache.set(cacheKey, responseData.translations);
                this.translations = { ...this.translations, ...responseData.translations };
                console.log('Notification translations loaded:', responseData.translations);
                return this.translations;
            })
            .catch(error => {
                console.error('Failed to load notification translations:', error);
                throw error;
            })
            .finally(() => {
                this.loadPromises.delete(cacheKey);
            });

        this.loadPromises.set(cacheKey, loadPromise);
        return loadPromise;
    }

    /**
     * Fetch translations from API
     * @param {array} groups - Translation groups
     * @param {string} locale - Locale
     * @returns {Promise} Promise that resolves with translations
     */
    async fetchTranslations(groups, locale) {
        const groupsParam = groups.join(',');
        const response = await fetch(`/api/translations/js?locale=${locale}&groups=${groupsParam}`);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Handle wrapped response format
        let responseData = data;
        if (data.data && data.data.success && data.data.translations) {
            responseData = data.data;
        } else if (data.success && data.translations) {
            responseData = data;
        } else {
            console.error('Invalid response format:', data);
            throw new Error('Invalid response format');
        }

        return responseData.translations;
    }

    /**
     * Get nested value from object using dot notation
     * @param {object} obj - Object to search in
     * @param {string} key - Dot notation key
     * @returns {*} Value or null if not found
     */
    getNestedValue(obj, key) {
        return key.split('.').reduce((current, k) => {
            return current && current[k] !== undefined ? current[k] : null;
        }, obj);
    }

    /**
     * Set current locale
     * @param {string} locale - New locale
     */
    setLocale(locale) {
        this.currentLocale = locale;
        console.log('Locale changed to:', locale);
    }

    /**
     * Get current locale
     * @returns {string} Current locale
     */
    getLocale() {
        return this.currentLocale;
    }

    /**
     * Clear translation cache
     */
    clearCache() {
        this.cache.clear();
        this.loadPromises.clear();
        console.log('Translation cache cleared');
    }

    /**
     * Check if translations are loaded for a group
     * @param {string} group - Translation group
     * @returns {boolean} True if loaded
     */
    isLoaded(group) {
        return this.getNestedValue(this.translations, group) !== null;
    }

    /**
     * Get all loaded translations
     * @returns {object} All translations
     */
    getAllTranslations() {
        return this.translations;
    }
}

// Create global instance
window.translationService = new TranslationService();

// Create global helper functions for backward compatibility
window.trans = function(key, replacements = {}, fallback = null) {
    return window.translationService.trans(key, replacements, fallback);
};

window.__ = function(key, replacements = {}, fallback = null) {
    return window.translationService.trans(key, replacements, fallback);
};

// Auto-load notification translations when service is ready
document.addEventListener('DOMContentLoaded', function() {
    if (window.translationService) {
        window.translationService.loadNotificationTranslations()
            .then(() => {
                console.log('Notification translations auto-loaded');

                // Trigger custom event for components that need translations
                document.dispatchEvent(new CustomEvent('translationsLoaded', {
                    detail: {
                        service: window.translationService,
                        translations: window.translationService.getAllTranslations()
                    }
                }));
            })
            .catch(error => {
                console.error('Failed to auto-load notification translations:', error);
            });
    }
});

//console.log('Translation Service loaded successfully');
