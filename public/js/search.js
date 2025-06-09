/**
 * MechaMap Search Functionality
 *
 * This script handles the AJAX search functionality for the MechaMap forum.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const searchInput = document.getElementById('header-search');
    const searchButton = document.getElementById('header-search-btn');
    const searchResultsDropdown = document.getElementById('search-results-dropdown');
    const searchResultsContent = document.getElementById('search-results-content');
    const searchScopeOptions = document.querySelectorAll('.search-scope-option');

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
    if (!currentThreadId) {
        // Disable thread scope option if not on a thread page
        const threadScopeOption = document.querySelector('.search-scope-option[data-scope="thread"]');
        if (threadScopeOption) {
            threadScopeOption.classList.add('disabled');
            threadScopeOption.style.display = 'none';
        }
    }

    if (!currentForumId) {
        // Disable forum scope option if not on a forum page
        const forumScopeOption = document.querySelector('.search-scope-option[data-scope="forum"]');
        if (forumScopeOption) {
            forumScopeOption.classList.add('disabled');
            forumScopeOption.style.display = 'none';
        }
    }

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
            searchResultsContent.innerHTML = '<div class="search-loading"><i class="bi bi-hourglass-split me-2"></i>Searching...</div>';

            // Set a timeout to avoid too many requests
            searchTimeout = setTimeout(function() {
                performSearch(query);
            }, 300);
        } else {
            hideSearchResults();
        }
    });

    searchButton.addEventListener('click', function() {
        const query = searchInput.value.trim();

        if (query.length >= 2) {
            // Redirect to search page
            window.location.href = `/search?query=${encodeURIComponent(query)}&type=all`;
        }
    });

    // Handle Enter key in search input
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = this.value.trim();

            if (query.length >= 2) {
                // Redirect to search page
                window.location.href = `/search?query=${encodeURIComponent(query)}&type=all`;
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
        if (!searchResultsDropdown.contains(e.target) && e.target !== searchInput && e.target !== searchButton) {
            hideSearchResults();
        }
    });

    // Functions
    function showSearchResults() {
        searchResultsDropdown.style.display = 'block';
    }

    function hideSearchResults() {
        searchResultsDropdown.style.display = 'none';
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
                searchResultsContent.innerHTML = '<div class="search-no-results">An error occurred while searching. Please try again.</div>';
            });
    }

    function displaySearchResults(data) {
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
                <div class="search-no-results">
                    <i class="bi bi-search me-2"></i>No results found for "${searchInput.value}".
                    <p class="mt-2">
                        <a href="${data.advanced_search_url}" class="btn btn-sm btn-primary">
                            <i class="bi bi-sliders me-1"></i>Thử tìm kiếm nâng cao
                        </a>
                    </p>
                </div>
            `;
            return;
        }

        // Build results HTML
        let resultsHTML = '';

        // If searching in a specific thread
        if (hasThread) {
            resultsHTML += `
                <div class="search-result-section">
                    <div class="search-result-section-title">Chủ đề</div>
                    <div class="search-result-item">
                        <div class="search-result-item-title">
                            <a href="${results.thread.url}">${results.thread.title}</a>
                        </div>
                    </div>
                </div>
            `;
        }

        // If searching in a specific forum
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

        // Display threads
        if (hasThreads) {
            resultsHTML += `
                <div class="search-result-section">
                    <div class="search-result-section-title">Threads</div>
            `;

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

        // Display posts
        if (hasPosts) {
            resultsHTML += `
                <div class="search-result-section">
                    <div class="search-result-section-title">Posts</div>
            `;

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

        // Add "View all results" link
        resultsHTML += `
            <div class="text-center mt-3">
                <a href="/search?query=${encodeURIComponent(searchInput.value)}&type=all" class="btn btn-sm btn-outline-primary">
                    Xem tất cả kết quả
                </a>
            </div>
        `;

        // Update content
        searchResultsContent.innerHTML = resultsHTML;
    }
});
