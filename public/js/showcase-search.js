/**
 * Showcase Advanced Search JavaScript
 * Handles search form interactions, AJAX filtering, and UI enhancements
 */

document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('showcase-search-form');
    const toggleFiltersBtn = document.getElementById('toggle-filters');
    const advancedFilters = document.getElementById('advanced-filters');
    const searchInput = document.getElementById('search-input');

    if (!searchForm) return;

    // Initialize search functionality
    initializeSearchForm();
    initializeFilterToggle();
    initializeAutoSubmit();
    initializeSearchTags();

    // Check if we need to scroll to search form after page load
    checkScrollToSearchForm();

    /**
     * Initialize main search form functionality
     */
    function initializeSearchForm() {
        // Handle form submission
        searchForm.addEventListener('submit', function(e) {
            // Let the form submit normally for now
            // Can be enhanced with AJAX later
            showLoadingState();

            // Store scroll target in sessionStorage for after page reload
            sessionStorage.setItem('scrollToSearchForm', 'true');
        });

        // Handle search input enhancements
        if (searchInput) {
            // Add search suggestions (can be enhanced with AJAX)
            searchInput.addEventListener('input', debounce(function() {
                const query = this.value.trim();
                if (query.length >= 2) {
                    // Future: Add search suggestions
                    console.log('Search query:', query);
                }
            }, 300));

            // Handle Enter key
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    // Store scroll target before form submission
                    sessionStorage.setItem('scrollToSearchForm', 'true');
                    searchForm.submit();
                }
            });
        }
    }

    /**
     * Initialize filter toggle functionality
     */
    function initializeFilterToggle() {
        if (!toggleFiltersBtn || !advancedFilters) return;

        // Update toggle button text based on collapse state
        advancedFilters.addEventListener('shown.bs.collapse', function() {
            const toggleText = toggleFiltersBtn.querySelector('.filter-toggle-text');
            if (toggleText) {
                toggleText.textContent = getTranslation('showcase.hide_filters', 'Ẩn bộ lọc');
            }
            toggleFiltersBtn.setAttribute('aria-expanded', 'true');
        });

        advancedFilters.addEventListener('hidden.bs.collapse', function() {
            const toggleText = toggleFiltersBtn.querySelector('.filter-toggle-text');
            if (toggleText) {
                toggleText.textContent = getTranslation('showcase.show_filters', 'Hiện bộ lọc');
            }
            toggleFiltersBtn.setAttribute('aria-expanded', 'false');
        });

        // Auto-expand if there are active filters
        const hasActiveFilters = document.querySelector('.search-tags-container');
        if (hasActiveFilters && !advancedFilters.classList.contains('show')) {
            // Auto-expand filters if there are active search tags
            const collapse = new bootstrap.Collapse(advancedFilters, { show: true });
        }
    }

    /**
     * Initialize auto-submit for filter changes
     */
    function initializeAutoSubmit() {
        const filterSelects = searchForm.querySelectorAll('select[name]:not([name="sort"])');
        const filterCheckboxes = searchForm.querySelectorAll('input[type="checkbox"]');
        const sortSelect = searchForm.querySelector('select[name="sort"]');

        // Auto-submit on filter change (except sort)
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                if (this.value !== this.defaultValue) {
                    showLoadingState();
                    searchForm.submit();
                }
            });
        });

        // Auto-submit on checkbox change
        filterCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                showLoadingState();
                searchForm.submit();
            });
        });

        // Auto-submit on sort change
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                showLoadingState();
                searchForm.submit();
            });
        }
    }

    /**
     * Initialize search tags functionality
     */
    function initializeSearchTags() {
        // Handle remove filter buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.search-tag-remove')) {
                e.preventDefault();
                const button = e.target.closest('.search-tag-remove');
                const filterName = button.getAttribute('onclick')?.match(/removeSearchFilter\('(.+?)'\)/)?.[1];

                if (filterName) {
                    removeSearchFilter(filterName);
                }
            }
        });

        // Handle clear all filters
        const clearAllBtn = document.querySelector('[onclick="clearAllFilters()"]');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                clearAllFilters();
            });
        }
    }

    /**
     * Show loading state during form submission
     */
    function showLoadingState() {
        const submitBtns = searchForm.querySelectorAll('button[type="submit"]');
        submitBtns.forEach(btn => {
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tìm kiếm...';
            btn.disabled = true;

            // Restore button after a delay (in case of page reload)
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 5000);
        });
    }

    /**
     * Debounce function for search input
     */
    function debounce(func, wait) {
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

    /**
     * Check if we need to scroll to search form after page load
     */
    function checkScrollToSearchForm() {
        // Check if we should scroll to search form
        if (sessionStorage.getItem('scrollToSearchForm') === 'true') {
            // Remove the flag
            sessionStorage.removeItem('scrollToSearchForm');

            // Wait a bit for page to fully load, then scroll
            setTimeout(() => {
                const searchFormElement = document.getElementById('showcase-search-form');
                if (searchFormElement) {
                    searchFormElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });

                    // Optional: Add a subtle highlight effect
                    searchFormElement.style.transition = 'box-shadow 0.3s ease';
                    searchFormElement.style.boxShadow = '0 0 20px rgba(0, 123, 255, 0.3)';

                    // Remove highlight after 2 seconds
                    setTimeout(() => {
                        searchFormElement.style.boxShadow = '';
                    }, 2000);
                }
            }, 100);
        }
    }

    /**
     * Get translation with fallback
     */
    function getTranslation(key, fallback) {
        // Future: Integrate with translation system
        return fallback;
    }
});

/**
 * Global functions for search tag management
 */
window.removeSearchFilter = function(filterName) {
    const url = new URL(window.location);
    url.searchParams.delete(filterName);
    window.location.href = url.toString();
};

window.clearAllFilters = function() {
    const baseUrl = window.location.pathname;
    window.location.href = baseUrl;
};

/**
 * Enhanced search functionality with AJAX (Future enhancement)
 */
window.ShowcaseSearch = {
    /**
     * Perform AJAX search (Future implementation)
     */
    performAjaxSearch: function(formData) {
        // Future: Implement AJAX search
        console.log('AJAX search:', formData);
    },

    /**
     * Update results container (Future implementation)
     */
    updateResults: function(html) {
        // Future: Update results without page reload
        console.log('Update results:', html);
    },

    /**
     * Show search suggestions (Future implementation)
     */
    showSuggestions: function(query) {
        // Future: Show search suggestions dropdown
        console.log('Show suggestions for:', query);
    }
};

/**
 * Initialize search form enhancements when DOM is ready
 */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Showcase search form initialized');
        // Check if we need to scroll to search form after page load
        checkScrollToSearchFormGlobal();
    });
} else {
    console.log('Showcase search form initialized');
    // Check if we need to scroll to search form after page load
    checkScrollToSearchFormGlobal();
}

/**
 * Global function to check scroll to search form
 */
function checkScrollToSearchFormGlobal() {
    // Check if we should scroll to search form
    if (sessionStorage.getItem('scrollToSearchForm') === 'true') {
        // Remove the flag
        sessionStorage.removeItem('scrollToSearchForm');

        // Wait a bit for page to fully load, then scroll
        setTimeout(() => {
            const searchFormElement = document.getElementById('showcase-search-form');
            if (searchFormElement) {
                searchFormElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

                // Optional: Add a subtle highlight effect
                searchFormElement.style.transition = 'box-shadow 0.3s ease';
                searchFormElement.style.boxShadow = '0 0 20px rgba(0, 123, 255, 0.3)';

                // Remove highlight after 2 seconds
                setTimeout(() => {
                    searchFormElement.style.boxShadow = '';
                }, 2000);
            }
        }, 300); // Increased delay to ensure page is fully loaded
    }
}
