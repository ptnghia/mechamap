/**
 * Helper functions for the application
 */

/**
 * Format a date string
 * @param {string} dateString - The date string to format
 * @param {string} locale - The locale to use for formatting (default: 'vi-VN')
 * @returns {string} - The formatted date string
 */
export function formatDate(dateString, locale = 'vi-VN') {
    const date = new Date(dateString);
    return date.toLocaleDateString(locale, {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

/**
 * Format a number as currency
 * @param {number} amount - The amount to format
 * @param {string} currency - The currency code (default: 'VND')
 * @param {string} locale - The locale to use for formatting (default: 'vi-VN')
 * @returns {string} - The formatted currency string
 */
export function formatCurrency(amount, currency = 'VND', locale = 'vi-VN') {
    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency
    }).format(amount);
}

/**
 * Truncate a string to a specified length
 * @param {string} str - The string to truncate
 * @param {number} length - The maximum length of the string
 * @param {string} suffix - The suffix to add to the truncated string (default: '...')
 * @returns {string} - The truncated string
 */
export function truncateString(str, length, suffix = '...') {
    if (str.length <= length) {
        return str;
    }
    return str.substring(0, length) + suffix;
}

/**
 * Debounce a function
 * @param {Function} func - The function to debounce
 * @param {number} wait - The debounce wait time in milliseconds
 * @returns {Function} - The debounced function
 */
export function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
