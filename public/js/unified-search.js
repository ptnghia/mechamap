/**
 * MechaMap Unified Search System
 * Consolidates all search functionality into one optimized file
 * Supports both desktop AJAX search and mobile search modal
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all search components
    initDesktopSearch();
    initMobileSearch();
    initSearchEnhancements();
});

/**
 * Desktop Search with AJAX Dropdown
 */
function initDesktopSearch() {
    // Elements - Using actual IDs from header
    const searchInput = document.getElementById('unified-search');
    const searchButton = document.getElementById('unified-search-btn');
    const searchResultsDropdown = document.getElementById('search-results-dropdown');
    const searchResultsContent = document.getElementById('search-results-content');
    const searchScopeOptions = document.querySelectorAll('.search-scope-option');

    // Check if search elements exist
    if (!searchInput) {
        console.warn('Desktop search input not found. Desktop search disabled.');
        return;
    }

    // Variables
    let currentSearchScope = 'site';
    let searchTimeout;
    let currentThreadId = null;
    let currentForumId = null;

    // Try to get thread and forum IDs from the page if available
    const threadElement = document.querySelector('[data-thread-id]');
    const forumElement = document.querySelector('[data-forum-id]');

    if (threadElement) {
        currentThreadId = threadElement.dataset.threadId;
    }

    if (forumElement) {
        currentForumId = forumElement.dataset.forumId;
    }

    // Initialize search scope options
    initSearchScopes();

    // Event Listeners
    searchInput.addEventListener('focus', function() {
        if (this.value.length >= 2) {
            showSearchResults();
            performSearch(this.value);
        }
    });

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        // Clear previous timeout
        clearTimeout(searchTimeout);

        if (query.length >= 2) {
            // Show loading state
            showSearchResults();
            if (searchResultsContent) {
                searchResultsContent.innerHTML = '<div class="search-loading p-3"><i class="fas fa-spinner fa-spin me-2"></i>Searching...</div>';
            }

            // Set a timeout to avoid too many requests
            searchTimeout = setTimeout(function() {
                performSearch(query);
            }, 300);
        } else {
            hideSearchResults();
        }
    });

    if (searchButton) {
        searchButton.addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query.length >= 2) {
                redirectToSearchPage(query);
            }
        });
    }

    // Handle Enter key in search input
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = this.value.trim();
            if (query.length >= 2) {
                redirectToSearchPage(query);
            }
        }
    });

    // Handle search scope selection
    searchScopeOptions.forEach(option => {
        option.addEventListener('click', function() {
            if (this.classList.contains('disabled')) {
                return;
            }

            // Update active scope
            searchScopeOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');

            // Update current scope
            currentSearchScope = this.dataset.scope;

            // Perform search with new scope
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

    // Functions
    function initSearchScopes() {
        if (!currentThreadId) {
            // Hide thread scope option if not on a thread page
            const threadScopeOption = document.querySelector('.search-scope-option[data-scope="thread"]');
            if (threadScopeOption) {
                threadScopeOption.style.display = 'none';
            }
        }

        if (!currentForumId) {
            // Hide forum scope option if not on a forum page
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
        // Prepare request parameters
        let params = {
            query: query,
            scope: currentSearchScope
        };

        // Add thread or forum ID if needed
        if (currentSearchScope === 'thread' && currentThreadId) {
            params.thread_id = currentThreadId;
        } else if (currentSearchScope === 'forum' && currentForumId) {
            params.forum_id = currentForumId;
        }

        // Build query string
        const queryString = Object.keys(params)
            .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
            .join('&');

        // Make AJAX request
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

        // Clear previous results
        searchResultsContent.innerHTML = '';

        const results = data.results;

        // Check if we have any results
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

        // Build results HTML
        let resultsHTML = '';

        // Display results sections
        if (hasThread) {
            resultsHTML += buildThreadSection(results.thread);
        }

        if (hasForum) {
            resultsHTML += buildForumSection(results.forum);
        }

        if (hasThreads) {
            resultsHTML += buildThreadsSection(results.threads);
        }

        if (hasPosts) {
            resultsHTML += buildPostsSection(results.posts);
        }

        // Add "View all results" link
        resultsHTML += `
            <div class="text-center p-3 border-top">
                <a href="/search?query=${encodeURIComponent(searchInput.value)}&type=all" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-external-link-alt me-1"></i>View All Results
                </a>
            </div>
        `;

        // Update content
        searchResultsContent.innerHTML = resultsHTML;
    }

    function buildThreadSection(thread) {
        return `
            <div class="search-result-section">
                <div class="search-result-section-title">Thread</div>
                <div class="search-result-item">
                    <div class="search-result-item-title">
                        <a href="${thread.url}">${thread.title}</a>
                    </div>
                </div>
            </div>
        `;
    }

    function buildForumSection(forum) {
        return `
            <div class="search-result-section">
                <div class="search-result-section-title">Forum</div>
                <div class="search-result-item">
                    <div class="search-result-item-title">
                        <a href="${forum.url}">${forum.name}</a>
                    </div>
                </div>
            </div>
        `;
    }

    function buildThreadsSection(threads) {
        let html = `
            <div class="search-result-section">
                <div class="search-result-section-title">Threads</div>
        `;

        threads.forEach(thread => {
            html += `
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

        html += `</div>`;
        return html;
    }

    function buildPostsSection(posts) {
        let html = `
            <div class="search-result-section">
                <div class="search-result-section-title">Posts</div>
        `;

        posts.forEach(post => {
            html += `
                <div class="search-result-item">
                    <div class="search-result-item-content">${post.content}</div>
                    <div class="search-result-item-meta">
                        by <a href="/users/${post.user.username}">${post.user.name}</a>
                        ${post.thread ? `in <a href="${post.thread.url}">${post.thread.title}</a>` : ''}
                    </div>
                </div>
            `;
        });

        html += `</div>`;
        return html;
    }

    function redirectToSearchPage(query) {
        window.location.href = `/search?query=${encodeURIComponent(query)}&type=all`;
    }
}

/**
 * Mobile Search Modal
 */
function initMobileSearch() {
    const mobileSearchInput = document.getElementById('mobileSearchInput');
    const mobileSearchButton = document.getElementById('mobileSearchButton');
    const mobileRecentSearches = document.getElementById('mobileRecentSearches');

    if (!mobileSearchInput) {
        console.warn('Mobile search input not found. Mobile search disabled.');
        return;
    }

    // Load recent searches
    loadRecentSearches();

    // Event listeners
    if (mobileSearchButton) {
        mobileSearchButton.addEventListener('click', function() {
            performMobileSearch();
        });
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
            // Save to recent searches
            saveRecentSearch(query);

            // Close modal and redirect
            const modal = bootstrap.Modal.getInstance(document.getElementById('mobileSearchModal'));
            if (modal) {
                modal.hide();
            }

            // Redirect to search page
            window.location.href = `/search?query=${encodeURIComponent(query)}&type=all`;
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

        // Add click handlers for recent search buttons
        const recentSearchButtons = mobileRecentSearches.querySelectorAll('.recent-search-btn');
        recentSearchButtons.forEach(button => {
            button.addEventListener('click', function() {
                const query = this.dataset.query;
                mobileSearchInput.value = query;
                performMobileSearch();
            });
        });
    }

    function saveRecentSearch(query) {
        let recentSearches = getRecentSearches();

        // Remove if already exists
        recentSearches = recentSearches.filter(search => search !== query);

        // Add to beginning
        recentSearches.unshift(query);

        // Keep only last 5
        recentSearches = recentSearches.slice(0, 5);

        // Save to localStorage
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
 * Search Enhancements & Utilities
 */
function initSearchEnhancements() {
    // Add search analytics tracking
    trackSearchUsage();

    // Initialize search keyboard shortcuts
    initSearchKeyboardShortcuts();

    // Add search performance monitoring
    monitorSearchPerformance();
}

function trackSearchUsage() {
    // Track search interactions for analytics
    document.addEventListener('click', function(e) {
        if (e.target.matches('.search-result-item a, .recent-search-btn, .quick-search-btn')) {
            // Track search result clicks
            if (typeof gtag !== 'undefined') {
                gtag('event', 'search_result_click', {
                    'event_category': 'Search',
                    'event_label': e.target.textContent.trim()
                });
            }
        }
    });
}

function initSearchKeyboardShortcuts() {
    // Ctrl/Cmd + K to focus search
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();

            const searchInput = document.getElementById('unified-search');
            if (searchInput && window.innerWidth >= 768) {
                searchInput.focus();
            } else {
                // Open mobile search modal
                const mobileSearchModal = document.getElementById('mobileSearchModal');
                if (mobileSearchModal) {
                    const modal = new bootstrap.Modal(mobileSearchModal);
                    modal.show();

                    // Focus input after modal is shown
                    mobileSearchModal.addEventListener('shown.bs.modal', function() {
                        const mobileSearchInput = document.getElementById('mobileSearchInput');
                        if (mobileSearchInput) {
                            mobileSearchInput.focus();
                        }
                    }, { once: true });
                }
            }
        }
    });
}

function monitorSearchPerformance() {
    // Monitor search API performance
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        const url = args[0];

        if (typeof url === 'string' && url.includes('/ajax-search')) {
            const startTime = performance.now();

            return originalFetch.apply(this, args).then(response => {
                const endTime = performance.now();
                const duration = endTime - startTime;

                // Log slow searches
                if (duration > 2000) {
                    console.warn(`Slow search detected: ${duration}ms for ${url}`);
                }

                // Track performance
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
