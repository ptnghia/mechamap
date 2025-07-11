/**
 * Bootstrap 5 Dropdown Enhancements for MechaMap
 * Enhances native Bootstrap 5 dropdowns with accessibility and mobile optimizations
 * Follows Bootstrap 5 standards and best practices
 */

document.addEventListener('DOMContentLoaded', function() {
    initBootstrapDropdownEnhancements();
});

function initBootstrapDropdownEnhancements() {
    // Get all dropdown elements
    const dropdownElements = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    
    dropdownElements.forEach(function(dropdownToggle) {
        // Ensure proper Bootstrap 5 initialization
        if (!bootstrap.Dropdown.getInstance(dropdownToggle)) {
            new bootstrap.Dropdown(dropdownToggle);
        }
        
        // Add enhanced accessibility
        enhanceDropdownAccessibility(dropdownToggle);
        
        // Add mobile optimizations
        enhanceDropdownMobile(dropdownToggle);
        
        // Add keyboard navigation
        enhanceDropdownKeyboard(dropdownToggle);
    });
    
    // Global dropdown event listeners
    addGlobalDropdownListeners();
}

/**
 * Enhance dropdown accessibility
 */
function enhanceDropdownAccessibility(dropdownToggle) {
    const dropdownMenu = dropdownToggle.nextElementSibling;
    
    if (!dropdownMenu || !dropdownMenu.classList.contains('dropdown-menu')) {
        return;
    }
    
    // Ensure proper ARIA attributes
    if (!dropdownToggle.hasAttribute('aria-expanded')) {
        dropdownToggle.setAttribute('aria-expanded', 'false');
    }
    
    if (!dropdownToggle.hasAttribute('aria-haspopup')) {
        dropdownToggle.setAttribute('aria-haspopup', 'true');
    }
    
    // Add unique IDs if missing
    if (!dropdownToggle.id) {
        dropdownToggle.id = 'dropdown-toggle-' + Math.random().toString(36).substr(2, 9);
    }
    
    if (!dropdownMenu.hasAttribute('aria-labelledby')) {
        dropdownMenu.setAttribute('aria-labelledby', dropdownToggle.id);
    }
    
    // Add role to dropdown items
    const dropdownItems = dropdownMenu.querySelectorAll('.dropdown-item');
    dropdownItems.forEach(function(item) {
        if (!item.hasAttribute('role')) {
            item.setAttribute('role', 'menuitem');
        }
    });
}

/**
 * Enhance dropdown for mobile devices
 */
function enhanceDropdownMobile(dropdownToggle) {
    const dropdownMenu = dropdownToggle.nextElementSibling;
    
    if (!dropdownMenu) return;
    
    // Add touch-friendly enhancements
    dropdownToggle.addEventListener('touchstart', function(e) {
        // Prevent double-tap zoom on iOS
        e.preventDefault();
    }, { passive: false });
    
    // Handle mobile viewport adjustments
    dropdownToggle.addEventListener('shown.bs.dropdown', function() {
        if (window.innerWidth <= 768) {
            adjustDropdownPosition(dropdownMenu);
        }
    });
}

/**
 * Enhance keyboard navigation
 */
function enhanceDropdownKeyboard(dropdownToggle) {
    const dropdownMenu = dropdownToggle.nextElementSibling;
    
    if (!dropdownMenu) return;
    
    // Enhanced keyboard navigation
    dropdownToggle.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            
            // Open dropdown if closed
            if (!dropdownMenu.classList.contains('show')) {
                dropdownToggle.click();
                return;
            }
            
            // Focus first/last item
            const items = dropdownMenu.querySelectorAll('.dropdown-item:not(.disabled)');
            if (items.length > 0) {
                if (e.key === 'ArrowDown') {
                    items[0].focus();
                } else {
                    items[items.length - 1].focus();
                }
            }
        }
    });
    
    // Navigation within dropdown menu
    dropdownMenu.addEventListener('keydown', function(e) {
        const items = Array.from(dropdownMenu.querySelectorAll('.dropdown-item:not(.disabled)'));
        const currentIndex = items.indexOf(document.activeElement);
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                const nextIndex = (currentIndex + 1) % items.length;
                items[nextIndex].focus();
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                const prevIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
                items[prevIndex].focus();
                break;
                
            case 'Escape':
                e.preventDefault();
                dropdownToggle.click(); // Close dropdown
                dropdownToggle.focus();
                break;
                
            case 'Tab':
                // Allow normal tab behavior but close dropdown
                setTimeout(() => {
                    if (!dropdownMenu.contains(document.activeElement)) {
                        bootstrap.Dropdown.getInstance(dropdownToggle)?.hide();
                    }
                }, 0);
                break;
        }
    });
}

/**
 * Adjust dropdown position for mobile
 */
function adjustDropdownPosition(dropdownMenu) {
    const rect = dropdownMenu.getBoundingClientRect();
    const viewportHeight = window.innerHeight;
    const viewportWidth = window.innerWidth;
    
    // Adjust if dropdown goes off-screen
    if (rect.bottom > viewportHeight) {
        dropdownMenu.style.transform = 'translateY(-' + (rect.bottom - viewportHeight + 10) + 'px)';
    }
    
    if (rect.right > viewportWidth) {
        dropdownMenu.style.transform = 'translateX(-' + (rect.right - viewportWidth + 10) + 'px)';
    }
}

/**
 * Global dropdown event listeners
 */
function addGlobalDropdownListeners() {
    // Close dropdowns on orientation change
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(function(menu) {
                const toggle = menu.previousElementSibling;
                if (toggle && bootstrap.Dropdown.getInstance(toggle)) {
                    bootstrap.Dropdown.getInstance(toggle).hide();
                }
            });
        }, 100);
    });
    
    // Analytics tracking for dropdown interactions
    document.addEventListener('shown.bs.dropdown', function(e) {
        if (typeof gtag !== 'undefined') {
            const dropdownId = e.target.id || 'unknown-dropdown';
            gtag('event', 'dropdown_opened', {
                'event_category': 'UI Interaction',
                'event_label': dropdownId
            });
        }
    });
    
    // Focus management for better accessibility
    document.addEventListener('hidden.bs.dropdown', function(e) {
        // Return focus to toggle when dropdown closes
        const toggle = e.target;
        if (toggle && toggle.focus) {
            toggle.focus();
        }
    });
}

/**
 * Utility function to reinitialize dropdowns after dynamic content changes
 */
window.reinitializeDropdowns = function() {
    initBootstrapDropdownEnhancements();
};

/**
 * Debug function to check dropdown states
 */
window.debugDropdowns = function() {
    const dropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    console.log('Dropdown Debug Info:');
    dropdowns.forEach(function(dropdown, index) {
        const instance = bootstrap.Dropdown.getInstance(dropdown);
        console.log(`Dropdown ${index + 1}:`, {
            element: dropdown,
            id: dropdown.id,
            instance: instance,
            isOpen: dropdown.getAttribute('aria-expanded') === 'true'
        });
    });
};
