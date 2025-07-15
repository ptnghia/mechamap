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

        // Check if we have any results
        const hasThreads = results.threads && results.threads.length > 0;
        const hasShowcases = results.showcases && results.showcases.length > 0;
        const hasProducts = results.products && results.products.length > 0;
        const hasUsers = results.users && results.users.length > 0;
        const totalResults = results.meta ? results.meta.total : 0;

        if (totalResults === 0) {
            searchResultsContent.innerHTML = `
                <div class="search-no-results p-3 text-center">
                    <i class="fas fa-search me-2"></i>No results found for "${searchInput.value}".
                    <p class="mt-2 mb-0">
                        <a href="${data.advanced_search_url || '/forums/search/advanced'}" class="btn btn-sm btn-primary">
                            <i class="fas fa-sliders-h me-1"></i>Try Advanced Search
                        </a>
                    </p>
                </div>
            `;
            return;
        }

        let resultsHTML = '';

        // Display results count
        resultsHTML += `
            <div class="search-results-header p-2 border-bottom">
                <small class="text-muted">Tìm thấy ${totalResults} kết quả</small>
            </div>
        `;

        // Display Threads (Thảo luận)
        if (hasThreads) {
            resultsHTML += `
                <div class="search-result-section">
                    <div class="search-result-section-title">
                        <i class="fas fa-comments me-2"></i>Thảo luận
                    </div>
            `;
            results.threads.forEach(thread => {
                resultsHTML += `
                    <div class="search-result-item">
                        <div class="d-flex">
                            <img src="${thread.author.avatar}" alt="${thread.author.name}" class="search-result-avatar me-2">
                            <div class="flex-grow-1">
                                <div class="search-result-item-title">
                                    <a href="${thread.url}">${thread.title}</a>
                                </div>
                                <div class="search-result-item-content">${thread.excerpt}</div>
                                <div class="search-result-item-meta">
                                    <span class="me-2">
                                        <i class="fas fa-user me-1"></i>${thread.author.name}
                                    </span>
                                    <span class="me-2">
                                        <i class="fas fa-folder me-1"></i>${thread.forum.name}
                                    </span>
                                    <span class="me-2">
                                        <i class="fas fa-comments me-1"></i>${thread.stats.comments}
                                    </span>
                                    <span class="me-2">
                                        <i class="fas fa-eye me-1"></i>${thread.stats.views}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            resultsHTML += `</div>`;
        }

        // Display Showcases (Dự án)
        if (hasShowcases) {
            resultsHTML += `
                <div class="search-result-section">
                    <div class="search-result-section-title">
                        <i class="fas fa-project-diagram me-2"></i>Dự án
                    </div>
            `;
            results.showcases.forEach(showcase => {
                resultsHTML += `
                    <div class="search-result-item">
                        <div class="d-flex">
                            ${showcase.image ? `<img src="${showcase.image}" alt="${showcase.title}" class="search-result-image me-2">` : ''}
                            <div class="flex-grow-1">
                                <div class="search-result-item-title">
                                    <a href="${showcase.url}">${showcase.title}</a>
                                    <span class="badge bg-secondary ms-2">${showcase.project_type}</span>
                                </div>
                                <div class="search-result-item-content">${showcase.excerpt}</div>
                                <div class="search-result-item-meta">
                                    <span class="me-2">
                                        <i class="fas fa-user me-1"></i>${showcase.author.name}
                                    </span>
                                    <span class="me-2">
                                        <i class="fas fa-eye me-1"></i>${showcase.stats.views}
                                    </span>
                                    <span class="me-2">
                                        <i class="fas fa-heart me-1"></i>${showcase.stats.likes}
                                    </span>
                                    ${showcase.stats.rating > 0 ? `<span class="me-2"><i class="fas fa-star me-1"></i>${showcase.stats.rating}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            resultsHTML += `</div>`;
        }

        // Display Products (Sản phẩm)
        if (hasProducts) {
            resultsHTML += `
                <div class="search-result-section">
                    <div class="search-result-section-title">
                        <i class="fas fa-shopping-cart me-2"></i>Sản phẩm
                    </div>
            `;
            results.products.forEach(product => {
                resultsHTML += `
                    <div class="search-result-item">
                        <div class="d-flex">
                            ${product.image ? `<img src="${product.image}" alt="${product.title}" class="search-result-image me-2">` : ''}
                            <div class="flex-grow-1">
                                <div class="search-result-item-title">
                                    <a href="${product.url}">${product.title}</a>
                                    <span class="badge bg-success ms-2">${product.price.formatted}</span>
                                </div>
                                <div class="search-result-item-content">${product.excerpt}</div>
                                <div class="search-result-item-meta">
                                    <span class="me-2">
                                        <i class="fas fa-store me-1"></i>${product.seller.name}
                                    </span>
                                    <span class="me-2">
                                        <i class="fas fa-eye me-1"></i>${product.stats.views}
                                    </span>
                                    ${product.type === 'marketplace_product' ?
                                        `<span class="me-2"><i class="fas fa-shopping-bag me-1"></i>${product.stats.purchases}</span>` :
                                        `<span class="me-2"><i class="fas fa-download me-1"></i>${product.stats.downloads}</span>`
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            resultsHTML += `</div>`;
        }

        // Display Users (Thành viên)
        if (hasUsers) {
            resultsHTML += `
                <div class="search-result-section">
                    <div class="search-result-section-title">
                        <i class="fas fa-users me-2"></i>Thành viên
                    </div>
            `;
            results.users.forEach(user => {
                resultsHTML += `
                    <div class="search-result-item">
                        <div class="d-flex">
                            <img src="${user.avatar}" alt="${user.name}" class="search-result-avatar me-2">
                            <div class="flex-grow-1">
                                <div class="search-result-item-title">
                                    <a href="${user.url}">${user.name}</a>
                                    <span class="text-muted">@${user.username}</span>
                                    <span class="badge bg-info ms-2">${user.role}</span>
                                </div>
                                <div class="search-result-item-meta">
                                    <span class="me-2">
                                        <i class="fas fa-comments me-1"></i>${user.stats.threads} threads
                                    </span>
                                    <span class="me-2">
                                        <i class="fas fa-reply me-1"></i>${user.stats.posts} posts
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            resultsHTML += `</div>`;
        }

        // Advanced search link
        resultsHTML += `
            <div class="text-center p-3 border-top">
                <a href="${data.advanced_search_url}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-search-plus me-1"></i>🔍 Tìm kiếm nâng cao
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

    // Close navbar when non-dropdown links are clicked
    const mobileMenuLinks = navbarCollapse.querySelectorAll('.nav-link:not(.dropdown-toggle)');
    mobileMenuLinks.forEach(link => {
        link.addEventListener('click', function() {
            const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
            if (bsCollapse) {
                bsCollapse.hide();
            }
        });
    });

    // Enhanced mobile dropdown handling with Bootstrap 5
    const dropdownToggles = navbarCollapse.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        // Use Bootstrap 5 events for mobile
        toggle.addEventListener('show.bs.dropdown', function(e) {
            if (window.innerWidth < 992) {
                // Close other dropdowns in mobile
                dropdownToggles.forEach(otherToggle => {
                    if (otherToggle !== toggle) {
                        const otherInstance = bootstrap.Dropdown.getInstance(otherToggle);
                        if (otherInstance) {
                            otherInstance.hide();
                        }
                    }
                });
            }
        });
    });

    // Close dropdowns when navbar collapses
    navbarCollapse.addEventListener('hidden.bs.collapse', function() {
        dropdownToggles.forEach(toggle => {
            const dropdownInstance = bootstrap.Dropdown.getInstance(toggle);
            if (dropdownInstance) {
                dropdownInstance.hide();
            }
        });
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
