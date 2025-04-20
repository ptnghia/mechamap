/**
 * DOM utility functions
 */

/**
 * Get element by selector
 * @param {string} selector - The CSS selector
 * @param {HTMLElement} parent - The parent element (default: document)
 * @returns {HTMLElement|null} - The element or null if not found
 */
export function $(selector, parent = document) {
    return parent.querySelector(selector);
}

/**
 * Get elements by selector
 * @param {string} selector - The CSS selector
 * @param {HTMLElement} parent - The parent element (default: document)
 * @returns {NodeList} - The elements
 */
export function $$(selector, parent = document) {
    return parent.querySelectorAll(selector);
}

/**
 * Create an element with attributes and children
 * @param {string} tag - The tag name
 * @param {Object} attrs - The attributes
 * @param {Array|HTMLElement|string} children - The children
 * @returns {HTMLElement} - The created element
 */
export function createElement(tag, attrs = {}, children = []) {
    const element = document.createElement(tag);
    
    // Set attributes
    Object.entries(attrs).forEach(([key, value]) => {
        if (key === 'className') {
            element.className = value;
        } else if (key === 'dataset') {
            Object.entries(value).forEach(([dataKey, dataValue]) => {
                element.dataset[dataKey] = dataValue;
            });
        } else if (key === 'style') {
            Object.entries(value).forEach(([styleKey, styleValue]) => {
                element.style[styleKey] = styleValue;
            });
        } else if (key.startsWith('on') && typeof value === 'function') {
            element.addEventListener(key.substring(2).toLowerCase(), value);
        } else {
            element.setAttribute(key, value);
        }
    });
    
    // Add children
    if (Array.isArray(children)) {
        children.forEach(child => {
            appendChild(element, child);
        });
    } else {
        appendChild(element, children);
    }
    
    return element;
}

/**
 * Append a child to an element
 * @param {HTMLElement} parent - The parent element
 * @param {HTMLElement|string} child - The child element or text
 */
function appendChild(parent, child) {
    if (child instanceof HTMLElement) {
        parent.appendChild(child);
    } else if (typeof child === 'string') {
        parent.appendChild(document.createTextNode(child));
    }
}

/**
 * Add event listener to elements
 * @param {string|HTMLElement|NodeList} selector - The CSS selector or elements
 * @param {string} event - The event name
 * @param {Function} callback - The callback function
 * @param {Object} options - The options
 */
export function on(selector, event, callback, options = {}) {
    if (typeof selector === 'string') {
        $$(selector).forEach(element => {
            element.addEventListener(event, callback, options);
        });
    } else if (selector instanceof HTMLElement) {
        selector.addEventListener(event, callback, options);
    } else if (selector instanceof NodeList) {
        selector.forEach(element => {
            element.addEventListener(event, callback, options);
        });
    }
}

/**
 * Add or remove a class from an element
 * @param {HTMLElement} element - The element
 * @param {string} className - The class name
 * @param {boolean} condition - The condition
 */
export function toggleClass(element, className, condition) {
    if (condition) {
        element.classList.add(className);
    } else {
        element.classList.remove(className);
    }
}
