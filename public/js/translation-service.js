/**
 * Translation Service (Refactored w/ Manifest & Versioned Groups)
 * Features:
 *  - Manifest (group hashes + version)
 *  - Group-level lazy loading with batching
 *  - Flat index for O(1) lookups
 *  - localStorage persistence (per locale + version)
 *  - Backward compatible: still supports legacy endpoints
 */
class TranslationService {
    constructor() {
        this.translations = {};       // Nested (group -> keys...)
        this.flat = {};               // Flat index: full.key.path -> value
        this.currentLocale = document.documentElement.lang || 'vi';
    this.fallbackLocale = (window.Laravel && window.Laravel.fallbackLocale) || document.documentElement.getAttribute('data-fallback-locale') || 'en';
        this.cache = new Map();       // Ephemeral group cache
        this.loadPromises = new Map();// Composite load promises
        this.manifest = null;         // { version, groups:{group:hash}, ... }
        this.manifestPromise = null;
        this.pendingGroups = new Set();
        this.batchTimer = null;
        this.localStorageKeyPrefix = 'mm_tr';
    this.metrics = { lookups:0, hits:0, misses:0, network:{requests:0, bytes:0, timeMs:0} };
    this.fallbackFlat = {}; // separate flat map for fallback locale

        if (window.Laravel && window.Laravel.translations) {
            this.mergeTranslations(window.Laravel.translations, { rebuildIndex: true });
        }

        this.restorePersisted();
        console.log('[TranslationService] Initialized locale=', this.currentLocale);

    this.registerServiceWorker();
    }

    /**
     * Get translation for a key
     * @param {string} key - Translation key (e.g., 'notifications.default.title')
     * @param {object} replacements - Key-value pairs for replacements
     * @param {string} fallback - Fallback text if translation not found
     * @returns {string} Translated text
     */
    trans(key, replacements = {}, fallback = null) {
        this.metrics.lookups++;
        let val = this.flat[key];
        if (val == null) {
            const group = key.split('.')[0];
            if (group && !this.isLoaded(group)) {
                this.queueGroupLoad(group);
            }
            // Try plural auto-detection if count provided
            if (replacements && typeof replacements.count !== 'undefined') {
                const pluralVal = this.resolvePlural(key, replacements.count);
                if (pluralVal != null) {
                    val = pluralVal;
                }
            }
            if (val == null) {
                // Try fallback locale flat if available
                const fb = this.fallbackFlat[key];
                if (fb != null) {
                    val = fb;
                }
            }
            if (val == null) {
                this.metrics.misses++;
                val = fallback || key;
            } else {
                this.metrics.hits++;
            }
        } else {
            this.metrics.hits++;
        }
        if (typeof val === 'string' && replacements && Object.keys(replacements).length) {
            for (const k in replacements) {
                if (!Object.prototype.hasOwnProperty.call(replacements, k)) continue;
                const re = new RegExp(`:${k}\\b`, 'g');
                val = val.replace(re, replacements[k]);
            }
        }
        return val;
    }

    transCount(baseKey, count, replacements = {}, fallback = null) {
        replacements.count = count;
        const pluralResolved = this.resolvePlural(baseKey, count);
        if (pluralResolved != null) {
            return this.trans(baseKey, replacements, fallback); // trans will pick up plural variant already in flat if present
        }
        return this.trans(baseKey, replacements, fallback);
    }

    resolvePlural(baseKey, count) {
        // Pattern: baseKey.one / .other or .zero
        const forms = ['zero','one','two','few','many','other'];
        const available = forms.filter(f => this.flat[`${baseKey}.${f}`] != null);
        if (!available.length) return null;
        try {
            const pr = new Intl.PluralRules(this.currentLocale);
            let category = pr.select(count);
            if (!available.includes(category)) {
                category = available.includes('other') ? 'other' : available[0];
            }
            const key = `${baseKey}.${category}`;
            return this.flat[key];
        } catch(_) {
            const key = `${baseKey}.other`;
            return this.flat[key] || null;
        }
    }

    /**
     * Load translations for specific groups
     * @param {string|array} groups - Translation groups to load
     * @param {string} locale - Locale to load (defaults to current)
     * @returns {Promise} Promise that resolves when translations are loaded
     */
    async loadTranslations(groups = ['notifications'], locale = null) {
        locale = locale || this.currentLocale;
        const gArr = Array.isArray(groups) ? groups : [groups];
        await this.ensureManifest(locale);
        // Attempt delta endpoint for efficiency when many groups
        if (gArr.length > 3 && locale === this.currentLocale) {
            try {
                await this.loadDelta(gArr, locale);
            } catch (e) {
                console.warn('[TranslationService] delta fallback -> individual', e);
                await Promise.all(gArr.map(g => this.loadGroup(g, locale)));
            }
        } else {
            await Promise.all(gArr.map(g => this.loadGroup(g, locale)));
        }
        this.persist();
        return this.translations;
    }

    /**
     * Load notification-specific translations
     * @param {string} locale - Locale to load
     * @returns {Promise} Promise that resolves when notification translations are loaded
     */
    async loadNotificationTranslations(locale = null) {
        return this.loadTranslations(['notifications'], locale);
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
        if (!response.ok) throw new Error(`HTTP error ${response.status}`);
        const data = await response.json();
        if (!data.success || !data.translations) throw new Error('Invalid response format');
        return data.translations;
    }

    /**
     * Get nested value from object using dot notation
     * @param {object} obj - Object to search in
     * @param {string} key - Dot notation key
     * @returns {*} Value or null if not found
     */
    getNestedValue(obj, key) { return key.split('.').reduce((c,k)=> c && c[k]!==undefined ? c[k] : null, obj); }

    /**
     * Get value by direct key lookup (for flat structure)
     * @param {object} obj - Object to search in
     * @param {string} key - Direct key
     * @returns {*} Value or null if not found
     */
    getDirectKeyValue() { return null; } // deprecated

    /**
     * Load translations asynchronously for a group
     * @param {string} group - Translation group to load
     */
    loadTranslationsAsync(group) { this.queueGroupLoad(group); }

    queueGroupLoad(group) {
        if (this.isLoaded(group)) return;
        this.pendingGroups.add(group);
        if (this.batchTimer) return;
        this.batchTimer = requestAnimationFrame(() => {
            const toLoad = Array.from(this.pendingGroups);
            this.pendingGroups.clear();
            this.batchTimer = null;
            this.loadTranslations(toLoad).then(() => {
                toLoad.forEach(g => this.notifyTranslationsLoaded(g));
            }).catch(e => console.warn('[TranslationService] batch load failed', e));
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
    isLoaded(group) { return !!(this.translations && this.translations[group]); }

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
        this.manifest = null;
        this.flat = {};
        try { localStorage.removeItem(this.localStorageNamespace()); } catch(_) {}
        console.log('[TranslationService] cache cleared');
    }

    /**
     * Check if translations are loaded for a group
     * @param {string} group - Translation group
     * @returns {boolean} True if loaded
     */
    // duplicate isLoaded removed

    /**
     * Get all loaded translations
     * @returns {object} All translations
     */
    getAllTranslations() { return this.translations; }

    async ensureManifest(locale = this.currentLocale) {
        if (this.manifest && this.manifest.locale === locale) return this.manifest;
        if (!this.manifestPromise) {
            this.manifestPromise = fetch(`/api/translations/manifest?locale=${locale}`)
                .then(r => { if (!r.ok) throw new Error('Manifest HTTP '+r.status); return r.json(); })
                .then(data => {
                    if (!data.success) throw new Error('Manifest invalid');
                    this.manifest = data;
                    const persisted = this.getPersistedMeta();
                    if (persisted && persisted.version !== data.version) {
                        this.clearCache();
                    }
                    return data;
                })
                .finally(() => { this.manifestPromise = null; });
        }
        return this.manifestPromise;
    }

    async loadGroup(group, locale = this.currentLocale) {
        if (this.isLoaded(group) && locale === this.currentLocale) return;
        const existingHash = this.manifest && this.manifest.groups ? this.manifest.groups[group] : undefined;
        const url = new URL('/api/translations/group', window.location.origin);
        url.searchParams.set('locale', locale);
        url.searchParams.set('group', group);
        if (existingHash && locale === this.currentLocale) {
            url.searchParams.set('hash', existingHash);
        }
        const t0 = performance.now();
        const res = await fetch(url.toString());
        if (!res.ok) throw new Error('Group HTTP '+res.status);
        const data = await res.json();
        const t1 = performance.now();
        this.metrics.network.requests++;
        this.metrics.network.timeMs += (t1 - t0);
        const size = (res.headers.get('Content-Length') ? parseInt(res.headers.get('Content-Length'),10) : JSON.stringify(data).length) || 0;
        this.metrics.network.bytes += size;
        if (!data.success) throw new Error('Group response invalid');
        if (data.unchanged) {
            // No action needed
            return {};
        }
        if (locale === this.currentLocale) {
            const wrapper = { [group]: data.translations };
            this.mergeTranslations(wrapper, { rebuildIndex: true });
            // Update manifest hash for this group
            if (this.manifest && this.manifest.groups) {
                this.manifest.groups[group] = data.hash;
                this.manifest.version = data.version; // may or may not change
            }
        } else if (locale === this.fallbackLocale) {
            // index into fallbackFlat without polluting main locale
            this.indexFallback(group, data.translations);
        }
        return data.translations;
    }

    async loadDelta(groups, locale = this.currentLocale) {
        const payload = { locale, groups: {} };
        groups.forEach(g => {
            const existingHash = this.manifest && this.manifest.groups ? this.manifest.groups[g] : null;
            payload.groups[g] = existingHash || null;
        });
        const t0 = performance.now();
        const res = await fetch('/api/translations/delta', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        if (!res.ok) throw new Error('Delta HTTP '+res.status);
        const data = await res.json();
        const t1 = performance.now();
        this.metrics.network.requests++;
        this.metrics.network.timeMs += (t1 - t0);
        const size = (res.headers.get('Content-Length') ? parseInt(res.headers.get('Content-Length'),10) : JSON.stringify(data).length) || 0;
        this.metrics.network.bytes += size;
    if (!data || data.success !== true) throw new Error('Delta response invalid (success flag)');
        if (!data.groups || typeof data.groups !== 'object') {
            console.warn('[TranslationService] delta response missing groups – falling back to individual loads', data);
            return {};
        }
        const changed = (data.groups.changed && typeof data.groups.changed === 'object') ? data.groups.changed : {};
        Object.keys(changed).forEach(group => {
            const entry = changed[group];
            if (!entry || !entry.translations) return; // skip malformed
            const wrapper = { [group]: entry.translations };
            this.mergeTranslations(wrapper, { rebuildIndex: true });
            if (this.manifest && this.manifest.groups) {
                this.manifest.groups[group] = entry.hash;
            }
        });
        if (this.manifest) {
            this.manifest.version = data.version;
        }
        // Dispatch events for changed groups only
        Object.keys(changed).forEach(g => this.notifyTranslationsLoaded(g));
        return changed;
    }

    mergeTranslations(obj, { rebuildIndex = false } = {}) {
        this.translations = this.deepMerge(this.translations, obj);
        if (rebuildIndex) {
            this.rebuildFlatIndex();
        } else {
            this.incrementalIndex(obj);
        }
    }

    rebuildFlatIndex() {
        this.flat = {};
        this.incrementalIndex(this.translations);
    }

    incrementalIndex(obj, prefix = '') {
        for (const k in obj) {
            if (!Object.prototype.hasOwnProperty.call(obj, k)) continue;
            const val = obj[k];
            const full = prefix ? `${prefix}.${k}` : k;
            if (val && typeof val === 'object' && !Array.isArray(val)) {
                this.incrementalIndex(val, full);
            } else {
                this.flat[full] = val;
            }
        }
    }

    persist() {
        try {
            if (!this.manifest) return;
            const payload = { meta: { version: this.manifest.version, locale: this.currentLocale, ts: Date.now() }, translations: this.translations };
            localStorage.setItem(this.localStorageNamespace(), JSON.stringify(payload));
        } catch (_) {}
    }

    restorePersisted() {
        try {
            const raw = localStorage.getItem(this.localStorageNamespace());
            if (!raw) return;
            const parsed = JSON.parse(raw);
            if (parsed && parsed.translations) {
                this.translations = parsed.translations;
                this.rebuildFlatIndex();
            }
        } catch (_) {}
    }

    getPersistedMeta() {
        try {
            const raw = localStorage.getItem(this.localStorageNamespace());
            if (!raw) return null;
            const parsed = JSON.parse(raw);
            return parsed.meta || null;
        } catch (_) { return null; }
    }

    localStorageNamespace() { return `${this.localStorageKeyPrefix}:${this.currentLocale}`; }

    indexFallback(group, obj) {
        // Flatten into fallbackFlat
        const stack = [{ prefix: group, value: obj }];
        while (stack.length) {
            const { prefix, value } = stack.pop();
            if (value && typeof value === 'object' && !Array.isArray(value)) {
                for (const k in value) {
                    if (!Object.prototype.hasOwnProperty.call(value, k)) continue;
                    const v = value[k];
                    const full = `${prefix}.${k}`;
                    if (v && typeof v === 'object' && !Array.isArray(v)) {
                        stack.push({ prefix: full, value: v });
                    } else {
                        if (this.flat[full] == null) { // don't override main locale
                            this.fallbackFlat[full] = v;
                        }
                    }
                }
            }
        }
    }

    registerServiceWorker() {
        if (!('serviceWorker' in navigator)) return;
        // Avoid multiple registrations
        const swUrl = '/sw-translations.js';
        navigator.serviceWorker.getRegistrations().then(regs => {
            const already = regs.some(r => r.active && r.active.scriptURL.endsWith('sw-translations.js'));
            if (already) return;
            navigator.serviceWorker.register(swUrl).catch(()=>{});
        });
    }

    logMetrics() {
        if (console && console.debug) {
            console.debug('[TranslationService][metrics]', this.metrics);
        }
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

// Auto-load critical groups per manifest
document.addEventListener('DOMContentLoaded', async () => {
    if (!window.translationService) return;
    try {
        const svc = window.translationService;
        const manifest = await svc.ensureManifest();
        let critical = manifest.critical_groups || ['common','notifications'];
        if (Array.isArray(window.TRANSLATION_CRITICAL_EXTRA)) {
            critical = Array.from(new Set(critical.concat(window.TRANSLATION_CRITICAL_EXTRA)));
        }
        await svc.loadTranslations(critical);
        // Preload fallback groups lightly (async, no blocking)
        if (svc.fallbackLocale && svc.fallbackLocale !== svc.currentLocale) {
            critical.forEach(g => svc.loadGroup(g, svc.fallbackLocale).catch(()=>{}));
        }
        document.dispatchEvent(new CustomEvent('translationsLoaded', {
            detail: { service: svc, translations: svc.getAllTranslations() }
        }));
        console.log('[TranslationService] Critical groups loaded', critical);
        setTimeout(()=> svc.logMetrics(), 3000);
    } catch (e) {
        console.warn('[TranslationService] Critical preload failed', e);
    }
});

//console.log('Translation Service loaded successfully');
