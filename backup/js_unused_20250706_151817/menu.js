/**
 * Enhanced Menu JavaScript for MechaMap
 * Handles mobile menu, search functionality, and user interactions
 */

document.addEventListener('DOMContentLoaded', function() {

    // Initialize menu functionality
    initializeMenu();
    initializeMobileSearch();
    initializeQuickAccess();
    initializeSearchEnhancements();

    /**
     * Initialize main menu functionality
     */
    function initializeMenu() {
        // Add active state management
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && currentPath.startsWith(href) && href !== '/') {
                link.classList.add('active');
                // Also mark parent dropdown as active
                const parentDropdown = link.closest('.dropdown');
                if (parentDropdown) {
                    const parentLink = parentDropdown.querySelector('.dropdown-toggle');
                    if (parentLink) {
                        parentLink.classList.add('active');
                    }
                }
            }
        });

        // Add hover effects for desktop
        if (window.innerWidth >= 992) {
            const dropdowns = document.querySelectorAll('.navbar-nav .dropdown');
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');

                // Skip if toggle element not found
                if (!toggle) {
                    console.warn('Dropdown toggle not found for:', dropdown);
                    return;
                }

                let hoverTimeout;

                dropdown.addEventListener('mouseenter', () => {
                    clearTimeout(hoverTimeout);
                    if (!dropdown.classList.contains('show')) {
                        toggle.click();
                    }
                });

                dropdown.addEventListener('mouseleave', () => {
                    hoverTimeout = setTimeout(() => {
                        if (dropdown.classList.contains('show')) {
                            toggle.click();
                        }
                    }, 300);
                });
            });
        }

        // Track menu interactions
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                const menuName = this.closest('.dropdown').querySelector('.dropdown-toggle').textContent.trim();
                const itemName = this.textContent.trim();

                // Analytics tracking (if available)
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'menu_click', {
                        'menu_section': menuName,
                        'menu_item': itemName
                    });
                }
            });
        });
    }

    /**
     * Initialize mobile search functionality
     */
    function initializeMobileSearch() {
        const mobileSearchModal = document.getElementById('mobileSearchModal');
        const mobileSearchInput = document.getElementById('mobileSearchInput');
        const mobileSearchButton = document.getElementById('mobileSearchButton');
        const searchScopes = document.querySelectorAll('.mobile-search-scope');

        if (!mobileSearchModal) return;

        let currentScope = 'all';

        // Handle search scope selection
        searchScopes.forEach(scope => {
            scope.addEventListener('click', function() {
                searchScopes.forEach(s => s.classList.remove('active'));
                this.classList.add('active');
                currentScope = this.dataset.scope;

                // Update placeholder
                const placeholders = {
                    'marketplace': 'Search products, parts, materials...',
                    'forum': 'Search discussions, topics...',
                    'members': 'Search members, companies...',
                    'technical': 'Search CAD files, standards...'
                };

                mobileSearchInput.placeholder = placeholders[currentScope] || 'Search MechaMap...';
            });
        });

        // Handle search execution
        function executeSearch() {
            const query = mobileSearchInput.value.trim();
            if (!query) return;

            // Save to recent searches
            saveRecentSearch(query);

            // Redirect to search results
            const searchUrl = `/search?q=${encodeURIComponent(query)}&scope=${currentScope}`;
            window.location.href = searchUrl;
        }

        mobileSearchButton.addEventListener('click', executeSearch);
        mobileSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                executeSearch();
            }
        });

        // Auto-focus search input when modal opens
        mobileSearchModal.addEventListener('shown.bs.modal', function() {
            mobileSearchInput.focus();
        });

        // Load recent searches
        loadRecentSearches();
    }

    /**
     * Initialize quick access toolbar
     */
    function initializeQuickAccess() {
        // Create quick access toolbar for mobile
        if (window.innerWidth <= 768) {
            createQuickAccessToolbar();
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            const toolbar = document.querySelector('.quick-access-toolbar');
            if (window.innerWidth <= 768 && !toolbar) {
                createQuickAccessToolbar();
            } else if (window.innerWidth > 768 && toolbar) {
                toolbar.remove();
            }
        });
    }

    /**
     * Create quick access toolbar for mobile
     */
    function createQuickAccessToolbar() {
        const toolbar = document.createElement('div');
        toolbar.className = 'quick-access-toolbar';
        toolbar.innerHTML = `
            <button class="quick-access-btn btn-primary" onclick="scrollToTop()" title="Back to top">
                <i class="fa-solid fa-arrow-up"></i>
            </button>
            <button class="quick-access-btn btn-success" onclick="openMobileSearch()" title="Search">
                <i class="fa-solid fa-search"></i>
            </button>
            <button class="quick-access-btn btn-warning" onclick="toggleBookmarks()" title="Bookmarks">
                <i class="fa-solid fa-bookmark"></i>
            </button>
        `;

        document.body.appendChild(toolbar);
    }

    /**
     * Initialize search enhancements
     */
    function initializeSearchEnhancements() {
        const headerSearch = document.getElementById('headerSearch');
        const searchResults = document.getElementById('searchResults');

        if (!headerSearch) return;

        let searchTimeout;

        // Real-time search suggestions
        headerSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    fetchSearchSuggestions(query);
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

        // Handle search scope changes
        document.querySelectorAll('.search-scope').forEach(scope => {
            scope.addEventListener('click', function() {
                document.querySelectorAll('.search-scope').forEach(s => s.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }

    /**
     * Fetch search suggestions
     */
    function fetchSearchSuggestions(query) {
        // Simulate API call - replace with actual endpoint
        const suggestions = [
            { type: 'product', title: 'Steel Bearings', category: 'Marketplace' },
            { type: 'thread', title: 'CAD Software Discussion', category: 'Forum' },
            { type: 'material', title: 'Aluminum 6061', category: 'Technical' },
            { type: 'member', title: 'John Engineer', category: 'Members' }
        ];

        displaySearchSuggestions(suggestions.filter(s =>
            s.title.toLowerCase().includes(query.toLowerCase())
        ));
    }

    /**
     * Display search suggestions
     */
    function displaySearchSuggestions(suggestions) {
        const searchResults = document.getElementById('searchResults');
        if (!searchResults) return;

        if (suggestions.length === 0) {
            searchResults.innerHTML = '<div class="p-3"><p class="text-muted mb-0">No suggestions found</p></div>';
        } else {
            const html = suggestions.map(suggestion => `
                <div class="search-suggestion p-2 border-bottom" data-type="${suggestion.type}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>${suggestion.title}</span>
                        <small class="text-muted">${suggestion.category}</small>
                    </div>
                </div>
            `).join('');

            searchResults.innerHTML = `<div class="p-2">${html}</div>`;
        }

        searchResults.classList.remove('d-none');
    }

    /**
     * Hide search results
     */
    function hideSearchResults() {
        const searchResults = document.getElementById('searchResults');
        if (searchResults) {
            searchResults.classList.add('d-none');
        }
    }

    /**
     * Save recent search
     */
    function saveRecentSearch(query) {
        let recentSearches = JSON.parse(localStorage.getItem('mechamap_recent_searches') || '[]');

        // Remove if already exists
        recentSearches = recentSearches.filter(search => search !== query);

        // Add to beginning
        recentSearches.unshift(query);

        // Keep only last 5
        recentSearches = recentSearches.slice(0, 5);

        localStorage.setItem('mechamap_recent_searches', JSON.stringify(recentSearches));
        loadRecentSearches();
    }

    /**
     * Load recent searches
     */
    function loadRecentSearches() {
        const container = document.getElementById('mobileRecentSearches');
        if (!container) return;

        const recentSearches = JSON.parse(localStorage.getItem('mechamap_recent_searches') || '[]');

        if (recentSearches.length === 0) {
            container.innerHTML = '<small class="text-muted">No recent searches</small>';
        } else {
            const html = recentSearches.map(search => `
                <span class="badge bg-light text-dark me-1 mb-1 recent-search" style="cursor: pointer;">${search}</span>
            `).join('');
            container.innerHTML = html;

            // Add click handlers
            container.querySelectorAll('.recent-search').forEach(badge => {
                badge.addEventListener('click', function() {
                    document.getElementById('mobileSearchInput').value = this.textContent;
                });
            });
        }
    }
});

/**
 * Global functions for quick access
 */
window.scrollToTop = function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

window.openMobileSearch = function() {
    const modal = new bootstrap.Modal(document.getElementById('mobileSearchModal'));
    modal.show();
};

window.toggleBookmarks = function() {
    // Implement bookmarks functionality
    // console.log('Bookmarks functionality to be implemented');
};

/**
 * Menu performance optimization
 */
window.addEventListener('load', function() {
    // Preload critical menu resources
    const criticalMenus = ['marketplace', 'technical', 'community'];
    criticalMenus.forEach(menu => {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = `/${menu}`;
        document.head.appendChild(link);
    });
});
