/**
 * Bootstrap 5 Standard Navbar JavaScript
 * Thay thế enhanced-menu.js bằng code đơn giản hơn
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeStandardNavbar();
});

function initializeStandardNavbar() {
    // 1. Simple search functionality
    initializeSimpleSearch();
    
    // 2. Analytics tracking (optional)
    initializeAnalytics();
    
    // 3. Accessibility improvements
    initializeAccessibility();
    
    // 4. Mobile menu enhancements
    initializeMobileMenu();
}

/**
 * Simple search functionality
 */
function initializeSimpleSearch() {
    const searchInput = document.querySelector('.header-search');
    const searchResults = document.querySelector('.search-results');
    
    if (!searchInput) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                // Simple search - redirect to search page
                // Hoặc có thể implement AJAX search suggestions
                showSearchSuggestions(query);
            }, 300);
        } else {
            hideSearchResults();
        }
    });
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-container')) {
            hideSearchResults();
        }
    });
    
    // Handle Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = this.value.trim();
            if (query) {
                window.location.href = `/search?q=${encodeURIComponent(query)}`;
            }
        }
    });
}

/**
 * Show search suggestions (simplified)
 */
function showSearchSuggestions(query) {
    const searchResults = document.querySelector('.search-results');
    if (!searchResults) return;
    
    // Simple static suggestions - có thể thay bằng AJAX call
    const suggestions = [
        { title: 'Search in Forum', url: `/search?q=${encodeURIComponent(query)}&scope=forum` },
        { title: 'Search in Marketplace', url: `/search?q=${encodeURIComponent(query)}&scope=marketplace` },
        { title: 'Search Members', url: `/search?q=${encodeURIComponent(query)}&scope=members` }
    ];
    
    const html = suggestions.map(suggestion => `
        <div class="search-suggestion" onclick="window.location.href='${suggestion.url}'">
            ${suggestion.title}
        </div>
    `).join('');
    
    searchResults.innerHTML = html;
    searchResults.classList.remove('d-none');
}

/**
 * Hide search results
 */
function hideSearchResults() {
    const searchResults = document.querySelector('.search-results');
    if (searchResults) {
        searchResults.classList.add('d-none');
    }
}

/**
 * Analytics tracking (optional)
 */
function initializeAnalytics() {
    // Track menu clicks
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function() {
            const menuName = this.closest('.dropdown').querySelector('.dropdown-toggle')?.textContent?.trim();
            const itemName = this.textContent.trim();
            
            // Google Analytics 4
            if (typeof gtag !== 'undefined') {
                gtag('event', 'menu_click', {
                    'menu_section': menuName,
                    'menu_item': itemName
                });
            }
            
            // Facebook Pixel
            if (typeof fbq !== 'undefined') {
                fbq('track', 'ViewContent', {
                    content_name: itemName,
                    content_category: menuName
                });
            }
        });
    });
}

/**
 * Accessibility improvements
 */
function initializeAccessibility() {
    // Keyboard navigation for dropdowns
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
    
    // ESC key to close dropdowns
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close all open dropdowns
            document.querySelectorAll('.dropdown.show').forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                if (toggle) {
                    bootstrap.Dropdown.getInstance(toggle)?.hide();
                }
            });
        }
    });
}

/**
 * Mobile menu enhancements
 */
function initializeMobileMenu() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (!navbarToggler || !navbarCollapse) return;
    
    // Close mobile menu when clicking on a link
    navbarCollapse.addEventListener('click', function(e) {
        if (e.target.classList.contains('nav-link') && !e.target.classList.contains('dropdown-toggle')) {
            // Close the mobile menu
            const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
            if (bsCollapse) {
                bsCollapse.hide();
            }
        }
    });
    
    // Add loading state to navigation links
    document.querySelectorAll('.nav-link:not(.dropdown-toggle)').forEach(link => {
        link.addEventListener('click', function() {
            if (this.href && !this.href.includes('#')) {
                this.classList.add('loading');
            }
        });
    });
}

/**
 * Utility functions
 */
window.navbarUtils = {
    /**
     * Programmatically open a dropdown
     */
    openDropdown: function(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            if (toggle) {
                new bootstrap.Dropdown(toggle).show();
            }
        }
    },
    
    /**
     * Close all dropdowns
     */
    closeAllDropdowns: function() {
        document.querySelectorAll('.dropdown.show').forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            if (toggle) {
                bootstrap.Dropdown.getInstance(toggle)?.hide();
            }
        });
    },
    
    /**
     * Set active navigation item
     */
    setActiveNav: function(path) {
        // Remove all active classes
        document.querySelectorAll('.nav-link.active').forEach(link => {
            link.classList.remove('active');
        });
        
        // Add active class to matching link
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === path) {
                link.classList.add('active');
            }
        });
    }
};

/**
 * Export for use in other scripts
 */
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initializeStandardNavbar, navbarUtils };
}
