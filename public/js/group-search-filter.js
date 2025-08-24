/**
 * MechaMap Group Search & Filter
 * Advanced search and filtering functionality for groups
 */

class GroupSearchFilter {
    constructor(options = {}) {
        this.options = {
            searchDelay: 300,
            minSearchLength: 2,
            maxResults: 50,
            enableAdvancedSearch: true,
            enableRealTimeFilter: true,
            ...options
        };

        this.searchTimer = null;
        this.currentFilters = {};
        this.searchHistory = [];
        this.savedSearches = [];

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadSavedSearches();
        this.initializeFilters();
        this.setupAdvancedSearch();
    }

    bindEvents() {
        // Search input
        $(document).on('input', '.group-search-input', this.handleSearchInput.bind(this));
        $(document).on('keypress', '.group-search-input', this.handleSearchKeypress.bind(this));
        
        // Filter controls
        $(document).on('change', '.filter-control', this.handleFilterChange.bind(this));
        $(document).on('click', '.filter-tag .remove-filter', this.removeFilter.bind(this));
        
        // Advanced search
        $(document).on('click', '.btn-advanced-search', this.toggleAdvancedSearch.bind(this));
        $(document).on('click', '.btn-apply-advanced', this.applyAdvancedSearch.bind(this));
        $(document).on('click', '.btn-clear-advanced', this.clearAdvancedSearch.bind(this));
        
        // Saved searches
        $(document).on('click', '.btn-save-search', this.saveCurrentSearch.bind(this));
        $(document).on('click', '.saved-search-item', this.applySavedSearch.bind(this));
        $(document).on('click', '.delete-saved-search', this.deleteSavedSearch.bind(this));
        
        // Sort controls
        $(document).on('change', '.sort-control', this.handleSortChange.bind(this));
        
        // View mode toggle
        $(document).on('click', '.view-mode-toggle', this.toggleViewMode.bind(this));
        
        // Clear all filters
        $(document).on('click', '.btn-clear-filters', this.clearAllFilters.bind(this));
        
        // Export results
        $(document).on('click', '.btn-export-results', this.exportResults.bind(this));
    }

    /**
     * Handle search input with debouncing
     */
    handleSearchInput(event) {
        const query = $(event.target).val().trim();
        
        // Clear previous timer
        if (this.searchTimer) {
            clearTimeout(this.searchTimer);
        }

        // Set new timer
        this.searchTimer = setTimeout(() => {
            this.performSearch(query);
        }, this.options.searchDelay);

        // Show search suggestions
        if (query.length >= this.options.minSearchLength) {
            this.showSearchSuggestions(query);
        } else {
            this.hideSearchSuggestions();
        }
    }

    /**
     * Handle search keypress (Enter key)
     */
    handleSearchKeypress(event) {
        if (event.which === 13) { // Enter key
            event.preventDefault();
            const query = $(event.target).val().trim();
            this.performSearch(query, true); // Force immediate search
        }
    }

    /**
     * Perform search operation
     */
    async performSearch(query, immediate = false) {
        if (!immediate && query.length < this.options.minSearchLength) {
            this.showAllGroups();
            return;
        }

        try {
            this.showSearchLoading();

            // Add to search history
            if (query && !this.searchHistory.includes(query)) {
                this.searchHistory.unshift(query);
                this.searchHistory = this.searchHistory.slice(0, 10); // Keep last 10 searches
                this.updateSearchHistory();
            }

            const searchParams = {
                query: query,
                filters: this.currentFilters,
                sort: this.getCurrentSort(),
                limit: this.options.maxResults
            };

            const response = await fetch('/dashboard/messages/groups/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(searchParams)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.renderSearchResults(data.results, query);
                this.updateResultsCount(data.total);
                this.hideSearchLoading();
            } else {
                throw new Error(data.message || 'Lỗi tìm kiếm');
            }

        } catch (error) {
            console.error('Search error:', error);
            this.showSearchError('Không thể thực hiện tìm kiếm: ' + error.message);
            this.hideSearchLoading();
        }
    }

    /**
     * Handle filter changes
     */
    handleFilterChange(event) {
        const $filter = $(event.target);
        const filterType = $filter.data('filter-type');
        const filterValue = $filter.val();

        if (filterValue) {
            this.currentFilters[filterType] = filterValue;
        } else {
            delete this.currentFilters[filterType];
        }

        this.updateFilterTags();
        
        if (this.options.enableRealTimeFilter) {
            const currentQuery = $('.group-search-input').val().trim();
            this.performSearch(currentQuery);
        }
    }

    /**
     * Remove specific filter
     */
    removeFilter(event) {
        event.preventDefault();
        const filterType = $(event.target).closest('.filter-tag').data('filter-type');
        
        delete this.currentFilters[filterType];
        $(`.filter-control[data-filter-type="${filterType}"]`).val('');
        
        this.updateFilterTags();
        
        const currentQuery = $('.group-search-input').val().trim();
        this.performSearch(currentQuery);
    }

    /**
     * Update filter tags display
     */
    updateFilterTags() {
        const $container = $('.active-filters');
        
        if (Object.keys(this.currentFilters).length === 0) {
            $container.hide();
            return;
        }

        const tagsHtml = Object.entries(this.currentFilters).map(([type, value]) => `
            <span class="filter-tag badge bg-primary me-2 mb-2" data-filter-type="${type}">
                ${this.getFilterDisplayName(type)}: ${this.getFilterValueDisplayName(type, value)}
                <button type="button" class="btn-close btn-close-white ms-1 remove-filter" aria-label="Remove filter"></button>
            </span>
        `).join('');

        $container.html(`
            <div class="d-flex align-items-center flex-wrap">
                <span class="me-2 text-muted">Bộ lọc đang áp dụng:</span>
                ${tagsHtml}
                <button type="button" class="btn btn-sm btn-outline-secondary btn-clear-filters">
                    <i class="fas fa-times me-1"></i>Xóa tất cả
                </button>
            </div>
        `).show();
    }

    /**
     * Show search suggestions
     */
    async showSearchSuggestions(query) {
        try {
            const response = await fetch('/dashboard/messages/groups/search/suggestions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({ query })
            });

            if (response.ok) {
                const data = await response.json();
                this.renderSearchSuggestions(data.suggestions);
            }
        } catch (error) {
            console.error('Error loading suggestions:', error);
        }
    }

    /**
     * Render search suggestions
     */
    renderSearchSuggestions(suggestions) {
        if (!suggestions || suggestions.length === 0) {
            this.hideSearchSuggestions();
            return;
        }

        const suggestionsHtml = suggestions.map(suggestion => `
            <div class="search-suggestion-item" data-suggestion="${suggestion.text}">
                <i class="fas fa-${suggestion.icon || 'search'} me-2 text-muted"></i>
                <span class="suggestion-text">${suggestion.text}</span>
                <span class="suggestion-count badge bg-light text-dark ms-auto">${suggestion.count || ''}</span>
            </div>
        `).join('');

        $('.search-suggestions').html(suggestionsHtml).show();

        // Bind suggestion click events
        $('.search-suggestion-item').on('click', (event) => {
            const suggestion = $(event.currentTarget).data('suggestion');
            $('.group-search-input').val(suggestion);
            this.performSearch(suggestion, true);
            this.hideSearchSuggestions();
        });
    }

    /**
     * Hide search suggestions
     */
    hideSearchSuggestions() {
        $('.search-suggestions').hide();
    }

    /**
     * Render search results
     */
    renderSearchResults(results, query) {
        const $container = $('.groups-list');
        
        if (!results || results.length === 0) {
            $container.html(`
                <div class="no-results text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không tìm thấy kết quả</h5>
                    <p class="text-muted">Không có nhóm nào phù hợp với từ khóa "${query}"</p>
                    <button type="button" class="btn btn-outline-primary btn-clear-filters">
                        <i class="fas fa-times me-1"></i>Xóa bộ lọc
                    </button>
                </div>
            `);
            return;
        }

        const resultsHtml = results.map(group => this.renderGroupCard(group, query)).join('');
        $container.html(resultsHtml);

        // Highlight search terms
        this.highlightSearchTerms(query);
    }

    /**
     * Render individual group card
     */
    renderGroupCard(group, query) {
        return `
            <div class="group-card card mb-3" data-group-id="${group.id}">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <img src="${group.avatar || this.getDefaultGroupAvatar(group.name)}" 
                             alt="${group.name}" 
                             class="group-avatar rounded me-3" 
                             width="60" height="60">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="group-name mb-1">${group.name}</h5>
                                    <p class="group-description text-muted mb-2">${group.description || 'Không có mô tả'}</p>
                                    <div class="group-meta">
                                        <span class="badge bg-${this.getCategoryColor(group.category)} me-2">${group.category}</span>
                                        <span class="text-muted me-3">
                                            <i class="fas fa-users me-1"></i>${group.member_count || 0} thành viên
                                        </span>
                                        <span class="text-muted me-3">
                                            <i class="fas fa-comments me-1"></i>${group.message_count || 0} tin nhắn
                                        </span>
                                        <span class="text-muted">
                                            <i class="fas fa-clock me-1"></i>${this.formatTime(group.last_activity)}
                                        </span>
                                    </div>
                                </div>
                                <div class="group-actions">
                                    ${this.renderGroupActions(group)}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Render group action buttons
     */
    renderGroupActions(group) {
        const actions = [];

        if (group.can_join) {
            actions.push(`
                <button class="btn btn-sm btn-success btn-join-group" data-group-id="${group.id}">
                    <i class="fas fa-plus me-1"></i>Tham gia
                </button>
            `);
        }

        if (group.can_view) {
            actions.push(`
                <a href="/dashboard/messages/groups/${group.id}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye me-1"></i>Xem
                </a>
            `);
        }

        if (group.can_manage) {
            actions.push(`
                <button class="btn btn-sm btn-outline-secondary btn-manage-group" data-group-id="${group.id}">
                    <i class="fas fa-cog me-1"></i>Quản lý
                </button>
            `);
        }

        return actions.join(' ');
    }

    /**
     * Highlight search terms in results
     */
    highlightSearchTerms(query) {
        if (!query) return;

        const terms = query.split(' ').filter(term => term.length > 1);
        
        terms.forEach(term => {
            $('.group-name, .group-description').each(function() {
                const $element = $(this);
                const text = $element.text();
                const regex = new RegExp(`(${term})`, 'gi');
                const highlightedText = text.replace(regex, '<mark>$1</mark>');
                $element.html(highlightedText);
            });
        });
    }

    /**
     * Toggle advanced search panel
     */
    toggleAdvancedSearch() {
        $('.advanced-search-panel').slideToggle();
    }

    /**
     * Apply advanced search
     */
    applyAdvancedSearch() {
        const advancedFilters = {};
        
        $('.advanced-search-panel .filter-control').each(function() {
            const $input = $(this);
            const filterType = $input.data('filter-type');
            const value = $input.val();
            
            if (value) {
                advancedFilters[filterType] = value;
            }
        });

        this.currentFilters = { ...this.currentFilters, ...advancedFilters };
        this.updateFilterTags();
        
        const currentQuery = $('.group-search-input').val().trim();
        this.performSearch(currentQuery);
        
        $('.advanced-search-panel').slideUp();
    }

    /**
     * Clear advanced search
     */
    clearAdvancedSearch() {
        $('.advanced-search-panel .filter-control').val('');
        this.clearAllFilters();
    }

    /**
     * Clear all filters
     */
    clearAllFilters() {
        this.currentFilters = {};
        $('.filter-control').val('');
        $('.active-filters').hide();
        
        const currentQuery = $('.group-search-input').val().trim();
        this.performSearch(currentQuery);
    }

    /**
     * Save current search
     */
    saveCurrentSearch() {
        const query = $('.group-search-input').val().trim();
        
        if (!query && Object.keys(this.currentFilters).length === 0) {
            this.showNotification('Không có gì để lưu', 'warning');
            return;
        }

        const searchName = prompt('Nhập tên cho tìm kiếm đã lưu:');
        if (!searchName) return;

        const savedSearch = {
            id: Date.now(),
            name: searchName,
            query: query,
            filters: { ...this.currentFilters },
            created_at: new Date().toISOString()
        };

        this.savedSearches.push(savedSearch);
        this.saveSavedSearches();
        this.updateSavedSearchesList();
        
        this.showNotification('Đã lưu tìm kiếm', 'success');
    }

    /**
     * Apply saved search
     */
    applySavedSearch(event) {
        const searchId = $(event.target).closest('.saved-search-item').data('search-id');
        const savedSearch = this.savedSearches.find(s => s.id == searchId);
        
        if (savedSearch) {
            $('.group-search-input').val(savedSearch.query);
            this.currentFilters = { ...savedSearch.filters };
            
            // Update filter controls
            Object.entries(this.currentFilters).forEach(([type, value]) => {
                $(`.filter-control[data-filter-type="${type}"]`).val(value);
            });
            
            this.updateFilterTags();
            this.performSearch(savedSearch.query);
        }
    }

    /**
     * Delete saved search
     */
    deleteSavedSearch(event) {
        event.stopPropagation();
        
        const searchId = $(event.target).closest('.saved-search-item').data('search-id');
        this.savedSearches = this.savedSearches.filter(s => s.id != searchId);
        
        this.saveSavedSearches();
        this.updateSavedSearchesList();
        
        this.showNotification('Đã xóa tìm kiếm đã lưu', 'success');
    }

    /**
     * Handle sort change
     */
    handleSortChange(event) {
        const currentQuery = $('.group-search-input').val().trim();
        this.performSearch(currentQuery);
    }

    /**
     * Get current sort option
     */
    getCurrentSort() {
        return $('.sort-control').val() || 'relevance';
    }

    /**
     * Toggle view mode (grid/list)
     */
    toggleViewMode(event) {
        const mode = $(event.target).data('mode');
        $('.groups-list').removeClass('grid-view list-view').addClass(`${mode}-view`);
        $('.view-mode-toggle').removeClass('active');
        $(event.target).addClass('active');
    }

    /**
     * Export search results
     */
    async exportResults() {
        try {
            const response = await fetch('/dashboard/messages/groups/search/export', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    query: $('.group-search-input').val().trim(),
                    filters: this.currentFilters,
                    sort: this.getCurrentSort()
                })
            });

            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `group-search-results-${Date.now()}.csv`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                this.showNotification('Kết quả đã được xuất', 'success');
            } else {
                throw new Error('Không thể xuất kết quả');
            }
        } catch (error) {
            console.error('Export error:', error);
            this.showNotification('Không thể xuất kết quả: ' + error.message, 'error');
        }
    }

    /**
     * Utility methods
     */
    getFilterDisplayName(type) {
        const names = {
            'category': 'Danh mục',
            'member_count': 'Số thành viên',
            'privacy': 'Quyền riêng tư',
            'activity': 'Hoạt động',
            'created_date': 'Ngày tạo'
        };
        return names[type] || type;
    }

    getFilterValueDisplayName(type, value) {
        // This would be customized based on your filter types
        return value;
    }

    getCategoryColor(category) {
        const colors = {
            'technical': 'primary',
            'general': 'secondary',
            'project': 'success',
            'support': 'warning'
        };
        return colors[category] || 'secondary';
    }

    getDefaultGroupAvatar(name) {
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=random`;
    }

    formatTime(timestamp) {
        if (!timestamp) return 'Chưa có hoạt động';
        return new Date(timestamp).toLocaleDateString('vi-VN');
    }

    showSearchLoading() {
        $('.search-loading').show();
    }

    hideSearchLoading() {
        $('.search-loading').hide();
    }

    showSearchError(message) {
        $('.search-error').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `).show();
    }

    updateResultsCount(count) {
        $('.results-count').text(`${count} kết quả`);
    }

    showAllGroups() {
        // Reset to show all groups without filters
        this.performSearch('');
    }

    initializeFilters() {
        // Initialize filter controls based on available data
    }

    setupAdvancedSearch() {
        // Setup advanced search panel
    }

    loadSavedSearches() {
        const saved = localStorage.getItem('group_saved_searches');
        if (saved) {
            try {
                this.savedSearches = JSON.parse(saved);
                this.updateSavedSearchesList();
            } catch (error) {
                console.error('Error loading saved searches:', error);
            }
        }
    }

    saveSavedSearches() {
        localStorage.setItem('group_saved_searches', JSON.stringify(this.savedSearches));
    }

    updateSavedSearchesList() {
        // Update saved searches UI
    }

    updateSearchHistory() {
        // Update search history UI
    }

    showNotification(message, type = 'info') {
        if (window.showNotification) {
            window.showNotification(message, type);
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }
}

// Auto-initialize on groups page
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.groups-search-container')) {
        window.groupSearchFilter = new GroupSearchFilter();
        console.log('✅ GroupSearchFilter initialized');
    }
});

// Export for manual initialization
window.GroupSearchFilter = GroupSearchFilter;
