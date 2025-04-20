/**
 * Validation utility functions
 */

/**
 * Check if a value is empty
 * @param {*} value - The value to check
 * @returns {boolean} - True if the value is empty
 */
export function isEmpty(value) {
    if (value === null || value === undefined) {
        return true;
    }
    
    if (typeof value === 'string') {
        return value.trim() === '';
    }
    
    if (Array.isArray(value)) {
        return value.length === 0;
    }
    
    if (typeof value === 'object') {
        return Object.keys(value).length === 0;
    }
    
    return false;
}

/**
 * Validate an email address
 * @param {string} email - The email address to validate
 * @returns {boolean} - True if the email is valid
 */
export function isValidEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

/**
 * Validate a phone number
 * @param {string} phone - The phone number to validate
 * @returns {boolean} - True if the phone number is valid
 */
export function isValidPhone(phone) {
    const re = /^(\+?84|0)[3|5|7|8|9][0-9]{8}$/;
    return re.test(String(phone));
}

/**
 * Validate a password
 * @param {string} password - The password to validate
 * @param {Object} options - The options
 * @returns {boolean} - True if the password is valid
 */
export function isValidPassword(password, options = {}) {
    const {
        minLength = 8,
        requireUppercase = true,
        requireLowercase = true,
        requireNumbers = true,
        requireSpecialChars = true
    } = options;
    
    if (password.length < minLength) {
        return false;
    }
    
    if (requireUppercase && !/[A-Z]/.test(password)) {
        return false;
    }
    
    if (requireLowercase && !/[a-z]/.test(password)) {
        return false;
    }
    
    if (requireNumbers && !/[0-9]/.test(password)) {
        return false;
    }
    
    if (requireSpecialChars && !/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
        return false;
    }
    
    return true;
}
