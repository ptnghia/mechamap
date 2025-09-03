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

        console.log('TranslationService initialized with locale:', this.currentLocale);
    }

    /**
     * Get translation for a key
     * @param {string} key - Translation key (e.g., 'notifications.default.title')
     * @param {object} replacements - Key-value pairs for replacements
     * @param {string} fallback - Fallback text if translation not found
     * @returns {string} Translated text
     */
    trans(key, replacements = {}, fallback = null) {
        // First try to get from nested structure (cached translations)
        let translation = this.getNestedValue(this.translations, key);

        // If not found in nested structure, try direct key lookup
        if (!translation && this.translations) {
            translation = this.getDirectKeyValue(this.translations, key);
        }

        // If still not found, check if we need to load the group
        if (!translation) {
            const group = key.split('.')[0];
            if (group && !this.isLoaded(group)) {
                // Load the group asynchronously and return key for now
                this.loadTranslationsAsync(group);
                // For now, return the fallback or key
                translation = fallback || key;
            } else {
                // Group is loaded but key not found
                translation = fallback || key;
            }
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
     * Get value by direct key lookup (for flat structure)
     * @param {object} obj - Object to search in
     * @param {string} key - Direct key
     * @returns {*} Value or null if not found
     */
    getDirectKeyValue(obj, key) {
        // Search through all nested objects for the direct key
        const searchInObject = (searchObj) => {
            if (!searchObj || typeof searchObj !== 'object') return null;

            // Check if key exists directly
            if (searchObj[key] !== undefined) {
                return searchObj[key];
            }

            // Search recursively in nested objects
            for (const prop in searchObj) {
                if (typeof searchObj[prop] === 'object') {
                    const result = searchInObject(searchObj[prop]);
                    if (result !== null) return result;
                }
            }
            return null;
        };

        return searchInObject(obj);
    }

    /**
     * Load translations asynchronously for a group
     * @param {string} group - Translation group to load
     */
    loadTranslationsAsync(group) {
        // Check if already loading this group
        if (this.loadingGroups && this.loadingGroups.has(group)) {
            return;
        }

        // Initialize loading tracker
        if (!this.loadingGroups) {
            this.loadingGroups = new Set();
        }

        this.loadingGroups.add(group);

        // Load the group asynchronously
        this.loadTranslations([group])
            .then(() => {
                console.log(`Asynchronously loaded translations for group: ${group}`);
                this.loadingGroups.delete(group);

                // Trigger a re-render or update if needed
                this.notifyTranslationsLoaded(group);
            })
            .catch(error => {
                console.warn(`Failed to load translations for group: ${group}`, error);
                this.loadingGroups.delete(group);
            });
    }

    /**
     * Notify that translations have been loaded for a group
     * @param {string} group - The group that was loaded
     */
    notifyTranslationsLoaded(group) {
        // Dispatch a custom event that components can listen to
        if (typeof window !== 'undefined') {
            window.dispatchEvent(new CustomEvent('translationsLoaded', {
                detail: { group, locale: this.currentLocale }
            }));
        }
    }

    /**
     * Load translations synchronously for immediate use (deprecated)
     * @param {string} group - Translation group
     * @param {string} key - Specific key being requested
     */
    loadTranslationsSync(group, key) {
        console.warn('loadTranslationsSync is deprecated. Use loadTranslationsAsync instead.');
        // Fallback to async loading
        this.loadTranslationsAsync(group);
    }

    /**
     * Check if a translation group has been loaded
     * @param {string} group - Group name to check
     * @returns {boolean} True if group is loaded
     */
    isLoaded(group) {
        return this.translations && this.translations[group] &&
               Object.keys(this.translations[group]).length > 0;
    }

    /**
     * Deep merge two objects
     * @param {object} target - Target object
     * @param {object} source - Source object
     * @returns {object} Merged object
     */
    deepMerge(target, source) {
        const result = { ...target };

        for (const key in source) {
            if (source.hasOwnProperty(key)) {
                if (typeof source[key] === 'object' && source[key] !== null && !Array.isArray(source[key])) {
                    result[key] = this.deepMerge(result[key] || {}, source[key]);
                } else {
                    result[key] = source[key];
                }
            }
        }

        return result;
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

    /**
     * Translate Vietnamese text to target language using external API
     * @param {string} vietnameseText - Vietnamese text to translate
     * @param {string} targetLanguage - Target language code (e.g., 'en', 'fr', 'de')
     * @param {string} contentType - Content type: 'text' or 'html' (default: 'text')
     * @returns {Promise<string>} Promise that resolves with translated text
     */
    async translateVietnamese(vietnameseText, targetLanguage, contentType = 'text') {
        // Validate input
        if (!vietnameseText || typeof vietnameseText !== 'string') {
            throw new Error('Vietnamese text is required and must be a string');
        }

        if (!targetLanguage || typeof targetLanguage !== 'string') {
            throw new Error('Target language is required and must be a string');
        }

        // Check text length limit
        if (vietnameseText.length > 5000) {
            throw new Error('Text content exceeds maximum length of 5000 characters');
        }

        // Validate content type
        if (!['text', 'html'].includes(contentType)) {
            throw new Error('Content type must be either "text" or "html"');
        }

        try {
            const response = await fetch('https://realtime.mechamap.com/api/translate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    sourceLanguage: 'vi',
                    targetLanguage: targetLanguage,
                    content: vietnameseText,
                    contentType: contentType
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            // Check if translation was successful
            if (!data.success) {
                throw new Error(data.message || 'Translation failed');
            }

            // Return the translated text
            return data.data.translatedText;

        } catch (error) {
            console.error('Translation error:', error);
            throw new Error(`Translation failed: ${error.message}`);
        }
    }

    /**
     * Detect language of text content using external API
     * @param {string} content - Text content to detect language
     * @returns {Promise<string>} Promise that resolves with detected language code
     */
    async detectLanguage(content) {
        if (!content || typeof content !== 'string') {
            throw new Error('Content is required and must be a string');
        }

        try {
            const response = await fetch('https://realtime.mechamap.com/api/detect-language', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    content: content
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Language detection failed');
            }

            return data.data.detectedLanguage;

        } catch (error) {
            console.error('Language detection error:', error);
            throw new Error(`Language detection failed: ${error.message}`);
        }
    }

    /**
     * Get list of supported languages for translation
     * @returns {Promise<object>} Promise that resolves with supported languages object
     */
    async getSupportedLanguages() {
        try {
            const response = await fetch('https://realtime.mechamap.com/api/supported-languages', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to get supported languages');
            }

            return data.data;

        } catch (error) {
            console.error('Get supported languages error:', error);
            throw new Error(`Failed to get supported languages: ${error.message}`);
        }
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

// Global helper functions for external translation API
window.translateVietnamese = function(vietnameseText, targetLanguage, contentType = 'text') {
    return window.translationService.translateVietnamese(vietnameseText, targetLanguage, contentType);
};

window.detectLanguage = function(content) {
    return window.translationService.detectLanguage(content);
};

window.getSupportedLanguages = function() {
    return window.translationService.getSupportedLanguages();
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
