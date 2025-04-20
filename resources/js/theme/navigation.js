/**
 * Navigation functionality
 */

/**
 * Initialize mobile menu
 */
export function initMobileMenu() {
    const mobileMenuButton = document.querySelector('[data-toggle-menu]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
}

/**
 * Initialize dropdowns
 */
export function initDropdowns() {
    const dropdownButtons = document.querySelectorAll('[data-dropdown-toggle]');
    
    dropdownButtons.forEach(button => {
        const targetId = button.getAttribute('data-dropdown-toggle');
        const target = document.getElementById(targetId);
        
        if (target) {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                target.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!target.contains(e.target) && !button.contains(e.target)) {
                    target.classList.add('hidden');
                }
            });
        }
    });
}

/**
 * Initialize smooth scroll
 */
export function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            
            if (targetId !== '#') {
                e.preventDefault();
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
}
