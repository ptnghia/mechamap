/**
 * MechaMap Header System
 * Consolidated JavaScript for header functionality including search, menu, and navigation
 * Optimized for performance and maintainability
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all header components
    initSearch();
    initMenu();
    initNavigation();
    initPerformanceOptimizations();
});

/**
 * Search System
 * Handles both desktop AJAX search and mobile search modal
 */
function initSearch() {
    // Desktop search elements
    const searchInput = document.getElementById('unified-search');
    const searchButton = document.getElementById('unified-search-btn');
    const searchResultsDropdown = document.getElementById('search-results-dropdown');
    const searchResultsContent = document.getElementById('search-results-content');
    const searchScopeOptions = document.querySelectorAll('.search-scope-option');

    // Mobile search elements
    const mobileSearchInput = document.getElementById('mobileSearchInput');
    const mobileSearchButton = document.getElementById('mobileSearchButton');
    const mobileRecentSearches = document.getElementById('mobileRecentSearches');

    if (!searchInput && !mobileSearchInput) {
        console.warn('Search inputs not found. Search functionality disabled.');
        return;
    }

    // Search variables
    let currentSearchScope = 'site';
    let searchTimeout;
    let currentThreadId = null;
    let currentForumId = null;

    // Get context IDs if available
    const threadElement = document.querySelector('[data-thread-id]');
    const forumElement = document.querySelector('[data-forum-id]');
    if (threadElement) currentThreadId = threadElement.dataset.threadId;
    if (forumElement) currentForumId = forumElement.dataset.forumId;

    // Desktop search functionality
    if (searchInput) {
        initDesktopSearch();
    }

    // Mobile search functionality
    if (mobileSearchInput) {
        initMobileSearch();
    }

    // Search keyboard shortcuts
    initSearchKeyboardShortcuts();

    function initDesktopSearch() {
        // Initialize search scopes
        initSearchScopes();

        // Search input events
        searchInput.addEventListener('focus', function() {
            if (this.value.length >= 2) {
                showSearchResults();
                performSearch(this.value);
            }
        });

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(searchTimeout);

            if (query.length >= 2) {
                showSearchResults();
                if (searchResultsContent) {
                    searchResultsContent.innerHTML = '<div class="search-loading p-3"><i class="fas fa-spinner fa-spin me-2"></i>Searching...</div>';
                }

                searchTimeout = setTimeout(function() {
                    performSearch(query);
                }, 300);
            } else {
                hideSearchResults();
            }
        });

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = this.value.trim();
                if (query.length >= 2) {
                    redirectToSearchPage(query);
                }
            }
        });

        // Search button event
        if (searchButton) {
            searchButton.addEventListener('click', function() {
                const query = searchInput.value.trim();
                if (query.length >= 2) {
                    redirectToSearchPage(query);
                }
            });
        }

        // Search scope selection
        searchScopeOptions.forEach(option => {
            option.addEventListener('click', function() {
                if (this.classList.contains('disabled')) return;

                searchScopeOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                currentSearchScope = this.dataset.scope;

                const query = searchInput.value.trim();
                if (query.length >= 2) {
                    performSearch(query);
                }
            });
        });

        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if (searchResultsDropdown &&
                !searchResultsDropdown.contains(e.target) &&
                e.target !== searchInput &&
                e.target !== searchButton) {
                hideSearchResults();
            }
        });
    }

    function initMobileSearch() {
        loadRecentSearches();

        if (mobileSearchButton) {
            mobileSearchButton.addEventListener('click', performMobileSearch);
        }

        mobileSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performMobileSearch();
            }
        });

        // Quick search category buttons
        const quickSearchButtons = document.querySelectorAll('.quick-search-btn');
        quickSearchButtons.forEach(button => {
            button.addEventListener('click', function() {
                const query = this.textContent.trim();
                mobileSearchInput.value = query;
                performMobileSearch();
            });
        });

        function performMobileSearch() {
            const query = mobileSearchInput.value.trim();
            if (query.length >= 2) {
                saveRecentSearch(query);

                const modal = bootstrap.Modal.getInstance(document.getElementById('mobileSearchModal'));
                if (modal) modal.hide();

                redirectToSearchPage(query);
            }
        }

        function loadRecentSearches() {
            if (!mobileRecentSearches) return;

            const recentSearches = getRecentSearches();

            if (recentSearches.length === 0) {
                mobileRecentSearches.innerHTML = '<small class="text-muted">No recent searches</small>';
                return;
            }

            let html = '';
            recentSearches.forEach(search => {
                html += `
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2 mb-2 recent-search-btn" data-query="${search}">
                        <i class="fas fa-history me-1"></i>${search}
                    </button>
                `;
            });

            mobileRecentSearches.innerHTML = html;

            const recentSearchButtons = mobileRecentSearches.querySelectorAll('.recent-search-btn');
            recentSearchButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const query = this.dataset.query;
                    mobileSearchInput.value = query;
                    performMobileSearch();
                });
            });
        }
    }

    function initSearchKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();

                if (searchInput && window.innerWidth >= 768) {
                    searchInput.focus();
                } else {
                    const mobileSearchModal = document.getElementById('mobileSearchModal');
                    if (mobileSearchModal) {
                        const modal = new bootstrap.Modal(mobileSearchModal);
                        modal.show();

                        mobileSearchModal.addEventListener('shown.bs.modal', function() {
                            if (mobileSearchInput) {
                                mobileSearchInput.focus();
                            }
                        }, { once: true });
                    }
                }
            }
        });
    }

    function initSearchScopes() {
        if (!currentThreadId) {
            const threadScopeOption = document.querySelector('.search-scope-option[data-scope="thread"]');
            if (threadScopeOption) {
                threadScopeOption.style.display = 'none';
            }
        }

        if (!currentForumId) {
            const forumScopeOption = document.querySelector('.search-scope-option[data-scope="forum"]');
            if (forumScopeOption) {
                forumScopeOption.style.display = 'none';
            }
        }
    }

    function showSearchResults() {
        if (searchResultsDropdown) {
            searchResultsDropdown.classList.remove('d-none');
        }
    }

    function hideSearchResults() {
        if (searchResultsDropdown) {
            searchResultsDropdown.classList.add('d-none');
        }
    }

    function performSearch(query) {
        let params = {
            query: query,
            scope: currentSearchScope
        };

        if (currentSearchScope === 'thread' && currentThreadId) {
            params.thread_id = currentThreadId;
        } else if (currentSearchScope === 'forum' && currentForumId) {
            params.forum_id = currentForumId;
        }

        const queryString = Object.keys(params)
            .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
            .join('&');

        fetch(`/ajax-search?${queryString}`)
            .then(response => response.json())
            .then(data => {
                displaySearchResults(data);
            })
            .catch(error => {
                console.error('Search error:', error);
                if (searchResultsContent) {
                    searchResultsContent.innerHTML = '<div class="search-error p-3 text-danger">An error occurred while searching. Please try again.</div>';
                }
            });
    }

    function displaySearchResults(data) {
        if (!searchResultsContent) return;

        searchResultsContent.innerHTML = '';
        const results = data.results;

        const hasThreads = results.threads && results.threads.length > 0;
        const hasPosts = results.posts && results.posts.length > 0;
        const hasForum = results.forum;
        const hasThread = results.thread;

        if (!hasThreads && !hasPosts && !hasForum && !hasThread) {
            searchResultsContent.innerHTML = `
                <div class="search-no-results p-3 text-center">
                    <i class="fas fa-search me-2"></i>No results found for "${searchInput.value}".
                    <p class="mt-2 mb-0">
                        <a href="${data.advanced_search_url || '/search/advanced'}" class="btn btn-sm btn-primary">
                            <i class="fas fa-sliders-h me-1"></i>Try Advanced Search
                        </a>
                    </p>
                </div>
            `;
            return;
        }

        let resultsHTML = '';

        if (hasThread) {
            resultsHTML += `
                <div class="search-result-section">
                    <div class="search-result-section-title">Thread</div>
                    <div class="search-result-item">
                        <div class="search-result-item-title">
                            <a href="${results.thread.url}">${results.thread.title}</a>
                        </div>
                    </div>
                </div>
            `;
        }

        if (hasForum) {
            resultsHTML += `
                <div class="search-result-section">
                    <div class="search-result-section-title">Forum</div>
                    <div class="search-result-item">
                        <div class="search-result-item-title">
                            <a href="${results.forum.url}">${results.forum.name}</a>
                        </div>
                    </div>
                </div>
            `;
        }

        if (hasThreads) {
            resultsHTML += `<div class="search-result-section"><div class="search-result-section-title">Threads</div>`;
            results.threads.forEach(thread => {
                resultsHTML += `
                    <div class="search-result-item">
                        <div class="search-result-item-title">
                            <a href="${thread.url}">${thread.title}</a>
                        </div>
                        <div class="search-result-item-content">${thread.content}</div>
                        <div class="search-result-item-meta">
                            by <a href="/users/${thread.user.username}">${thread.user.name}</a>
                            ${thread.forum ? `in <a href="${thread.forum.url}">${thread.forum.name}</a>` : ''}
                        </div>
                    </div>
                `;
            });
            resultsHTML += `</div>`;
        }

        if (hasPosts) {
            resultsHTML += `<div class="search-result-section"><div class="search-result-section-title">Posts</div>`;
            results.posts.forEach(post => {
                resultsHTML += `
                    <div class="search-result-item">
                        <div class="search-result-item-content">${post.content}</div>
                        <div class="search-result-item-meta">
                            by <a href="/users/${post.user.username}">${post.user.name}</a>
                            ${post.thread ? `in <a href="${post.thread.url}">${post.thread.title}</a>` : ''}
                        </div>
                    </div>
                `;
            });
            resultsHTML += `</div>`;
        }

        resultsHTML += `
            <div class="text-center p-3 border-top">
                <a href="/search?query=${encodeURIComponent(searchInput.value)}&type=all" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-external-link-alt me-1"></i>View All Results
                </a>
            </div>
        `;

        searchResultsContent.innerHTML = resultsHTML;
    }

    function redirectToSearchPage(query) {
        window.location.href = `/search?query=${encodeURIComponent(query)}&type=all`;
    }

    function saveRecentSearch(query) {
        let recentSearches = getRecentSearches();
        recentSearches = recentSearches.filter(search => search !== query);
        recentSearches.unshift(query);
        recentSearches = recentSearches.slice(0, 5);
        localStorage.setItem('mechamap_recent_searches', JSON.stringify(recentSearches));
    }

    function getRecentSearches() {
        try {
            const searches = localStorage.getItem('mechamap_recent_searches');
            return searches ? JSON.parse(searches) : [];
        } catch (e) {
            return [];
        }
    }
}

/**
 * Menu System
 * Handles navigation, dark mode, and mobile menu functionality
 */
function initMenu() {
    initMenuActiveStates();
    initDarkModeToggle();
    initMobileMenuEnhancements();
    initLanguageSwitcher();
    initNotificationBadges();
}

function initMenuActiveStates() {
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.navbar-nav .nav-link');

    menuItems.forEach(item => {
        const href = item.getAttribute('href');

        if (href && currentPath.startsWith(href) && href !== '/') {
            item.classList.add('active');

            const dropdownParent = item.closest('.dropdown');
            if (dropdownParent) {
                const dropdownToggle = dropdownParent.querySelector('.dropdown-toggle');
                if (dropdownToggle) {
                    dropdownToggle.classList.add('active');
                }
            }
        }
    });

    if (currentPath === '/') {
        const homeLink = document.querySelector('.navbar-nav .nav-link[href="/"]');
        if (homeLink) {
            homeLink.classList.add('active');
        }
    }
}

function initDarkModeToggle() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (!darkModeToggle) return;

    const savedTheme = localStorage.getItem('mechamap_theme') || 'light';
    applyTheme(savedTheme);

    darkModeToggle.addEventListener('click', function(e) {
        e.preventDefault();
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';

        applyTheme(newTheme);
        localStorage.setItem('mechamap_theme', newTheme);

        if (typeof gtag !== 'undefined') {
            gtag('event', 'theme_change', {
                'event_category': 'UI',
                'event_label': newTheme
            });
        }
    });

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);

        const icon = darkModeToggle.querySelector('i');
        if (icon) {
            if (theme === 'dark') {
                icon.className = 'fas fa-sun';
                darkModeToggle.setAttribute('title', 'Switch to Light Mode');
            } else {
                icon.className = 'fas fa-moon';
                darkModeToggle.setAttribute('title', 'Switch to Dark Mode');
            }
        }

        if (theme === 'dark') {
            document.body.setAttribute('data-bs-theme', 'dark');
        } else {
            document.body.removeAttribute('data-bs-theme');
        }
    }
}

function initMobileMenuEnhancements() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    if (!navbarToggler || !navbarCollapse) return;

    const mobileMenuLinks = navbarCollapse.querySelectorAll('.nav-link:not(.dropdown-toggle)');
    mobileMenuLinks.forEach(link => {
        link.addEventListener('click', function() {
            const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
            if (bsCollapse) {
                bsCollapse.hide();
            }
        });
    });

    const dropdownToggles = navbarCollapse.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth < 992) {
                e.preventDefault();
                e.stopPropagation();

                const dropdownMenu = this.nextElementSibling;
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    if (dropdownMenu.style.display === 'block') {
                        dropdownMenu.style.display = 'none';
                        this.setAttribute('aria-expanded', 'false');
                    } else {
                        navbarCollapse.querySelectorAll('.dropdown-menu').forEach(menu => {
                            menu.style.display = 'none';
                        });
                        navbarCollapse.querySelectorAll('.dropdown-toggle').forEach(t => {
                            t.setAttribute('aria-expanded', 'false');
                        });

                        dropdownMenu.style.display = 'block';
                        this.setAttribute('aria-expanded', 'true');
                    }
                }
            }
        });
    });

    document.addEventListener('click', function(e) {
        if (window.innerWidth < 992) {
            if (!e.target.closest('.dropdown')) {
                navbarCollapse.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.style.display = 'none';
                });
                navbarCollapse.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                    toggle.setAttribute('aria-expanded', 'false');
                });
            }
        }
    });
}

function initLanguageSwitcher() {
    const languageDropdown = document.querySelector('.language-switcher');
    if (!languageDropdown) return;

    const languageOptions = languageDropdown.querySelectorAll('.dropdown-item');
    languageOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();

            const selectedLang = this.dataset.lang;
            if (selectedLang) {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'language_change', {
                        'event_category': 'Localization',
                        'event_label': selectedLang
                    });
                }

                const currentUrl = new URL(window.location);
                currentUrl.searchParams.set('lang', selectedLang);
                window.location.href = currentUrl.toString();
            }
        });
    });
}

function initNotificationBadges() {
    updateNotificationBadges();
    setInterval(updateNotificationBadges, 30000);

    function updateNotificationBadges() {
        const isAuthenticated = document.querySelector('meta[name="user-authenticated"]');
        if (!isAuthenticated || isAuthenticated.content !== 'true') {
            return;
        }

        fetch('/api/notifications/count')
            .then(response => response.json())
            .then(data => {
                updateBadge('notifications-badge', data.notifications || 0);
                updateBadge('messages-badge', data.messages || 0);
            })
            .catch(error => {
                console.warn('Failed to update notification badges:', error);
            });
    }

    function updateBadge(badgeId, count) {
        const badge = document.getElementById(badgeId);
        if (!badge) return;

        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count.toString();
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }
}

/**
 * Navigation System
 * Handles navigation analytics and interactions
 */
function initNavigation() {
    // Track navigation clicks
    document.addEventListener('click', function(e) {
        if (e.target.matches('.nav-link, .dropdown-item')) {
            const linkText = e.target.textContent.trim();
            const linkHref = e.target.getAttribute('href');

            if (typeof gtag !== 'undefined') {
                gtag('event', 'navigation_click', {
                    'event_category': 'Navigation',
                    'event_label': linkText,
                    'value': linkHref
                });
            }
        }

        // Track search result clicks
        if (e.target.matches('.search-result-item a, .recent-search-btn, .quick-search-btn')) {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'search_result_click', {
                    'event_category': 'Search',
                    'event_label': e.target.textContent.trim()
                });
            }
        }
    });

    // Track mobile menu usage
    const navbarToggler = document.querySelector('.navbar-toggler');
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'mobile_menu_toggle', {
                    'event_category': 'Mobile',
                    'event_label': 'Menu Toggle'
                });
            }
        });
    }
}

/**
 * Performance Optimizations
 * Handles preloading, monitoring, and optimization
 */
function initPerformanceOptimizations() {
    initMenuPerformanceOptimization();
    monitorSearchPerformance();
}

function initMenuPerformanceOptimization() {
    const criticalLinks = [
        '/threads',
        '/showcase',
        '/marketplace',
        '/community'
    ];

    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        let preloadTimeout;

        link.addEventListener('mouseenter', function() {
            const href = this.getAttribute('href');
            if (href && criticalLinks.includes(href)) {
                preloadTimeout = setTimeout(() => {
                    preloadPage(href);
                }, 500);
            }
        });

        link.addEventListener('mouseleave', function() {
            if (preloadTimeout) {
                clearTimeout(preloadTimeout);
            }
        });
    });

    function preloadPage(url) {
        if (document.querySelector(`link[rel="prefetch"][href="${url}"]`)) {
            return;
        }

        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = url;
        document.head.appendChild(link);
    }
}

function monitorSearchPerformance() {
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        const url = args[0];

        if (typeof url === 'string' && url.includes('/ajax-search')) {
            const startTime = performance.now();

            return originalFetch.apply(this, args).then(response => {
                const endTime = performance.now();
                const duration = endTime - startTime;

                if (duration > 2000) {
                    console.warn(`Slow search detected: ${duration}ms for ${url}`);
                }

                if (typeof gtag !== 'undefined') {
                    gtag('event', 'search_performance', {
                        'event_category': 'Performance',
                        'value': Math.round(duration)
                    });
                }

                return response;
            });
        }

        return originalFetch.apply(this, args);
    };
}
