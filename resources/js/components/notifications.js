/**
 * Notification component
 */

/**
 * Show a notification
 * @param {string} message - The notification message
 * @param {string} type - The notification type (success, error, warning, info)
 * @param {number} duration - The duration in milliseconds (default: 3000)
 */
export function showNotification(message, type = 'info', duration = 3000) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type} fixed top-4 right-4 z-50 transform transition-all duration-300 ease-in-out translate-x-full`;
    
    // Create notification content
    notification.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="notification-icon">
                ${getIconForType(type)}
            </div>
            <div>
                <h3 class="notification-title">${getTitleForType(type)}</h3>
                <p class="notification-description">${message}</p>
            </div>
        </div>
        <button class="notification-close" aria-label="Close notification">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    `;
    
    // Add notification to the DOM
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 10);
    
    // Add close button event listener
    const closeButton = notification.querySelector('.notification-close');
    closeButton.addEventListener('click', () => {
        hideNotification(notification);
    });
    
    // Auto hide notification after duration
    setTimeout(() => {
        hideNotification(notification);
    }, duration);
}

/**
 * Hide a notification
 * @param {HTMLElement} notification - The notification element
 */
function hideNotification(notification) {
    notification.classList.add('translate-x-full');
    setTimeout(() => {
        notification.remove();
    }, 300);
}

/**
 * Get the icon for a notification type
 * @param {string} type - The notification type
 * @returns {string} - The icon HTML
 */
function getIconForType(type) {
    switch (type) {
        case 'success':
            return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>`;
        case 'error':
        case 'danger':
            return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>`;
        case 'warning':
            return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>`;
        case 'info':
        default:
            return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>`;
    }
}

/**
 * Get the title for a notification type
 * @param {string} type - The notification type
 * @returns {string} - The title
 */
function getTitleForType(type) {
    switch (type) {
        case 'success':
            return 'Thành công';
        case 'error':
        case 'danger':
            return 'Lỗi';
        case 'warning':
            return 'Cảnh báo';
        case 'info':
        default:
            return 'Thông báo';
    }
}
