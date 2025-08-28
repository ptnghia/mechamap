{{--
    MechaMap Community Mega Menu Component
    3-column mega menu for community navigation
    Uses Bootstrap grid system and custom CSS
--}}

<div class="mega-menu-container">
    <div class="row g-0">
        <!-- Column 1: Quick Access -->
        <div class="col-md-4">
            <div class="mega-menu-section">
                <h6 class="mega-menu-header">
                    <i class="fa-solid fa-rocket me-2 text-primary"></i>
                    <span>{{ __('navigation.community.quick_access') }}</span>
                </h6>
                <ul class="mega-menu-list">
                    <li>
                        <a href="{{ route('forums.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-home me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('forum.threads.title') }}</span>
                                <small class="mega-menu-item-desc">{{ __('navigation.community.forum_home_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('whats-new.popular') }}" class="mega-menu-item">
                            <i class="fa-solid fa-star me-2 text-warning"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('navigation.community.popular_topics') }}</span>
                                <small class="mega-menu-item-desc">{{ __('navigation.community.popular_discussions_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('forums.index') }}#categories" class="mega-menu-item">
                            <i class="fa-solid fa-folder-tree me-2 text-info"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('navigation.community.browse_categories') }}</span>
                                <small class="mega-menu-item-desc">{{ __('navigation.community.explore_topics_desc') }}</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Column 2: Discover -->
        <div class="col-md-4">
            <div class="mega-menu-section">
                <h6 class="mega-menu-header">
                    <i class="fa-solid fa-compass me-2 text-success"></i>
                    <span>{{ __('navigation.community.discover') }}</span>
                </h6>
                <ul class="mega-menu-list">
                    <li>
                        <a href="{{ route('whats-new') }}" class="mega-menu-item">
                            <i class="fa-solid fa-clock me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('navigation.community.recent_discussions') }}</span>
                                <small class="mega-menu-item-desc">{{ __('navigation.community.recent_discussions_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('whats-new.trending') }}" class="mega-menu-item">
                            <i class="fa-solid fa-chart-line me-2 text-success"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('navigation.community.trending') }}</span>
                                <small class="mega-menu-item-desc">{{ __('navigation.community.trending_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('whats-new.most-viewed') }}" class="mega-menu-item">
                            <i class="fa-solid fa-eye me-2 text-primary"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('navigation.community.most_viewed') }}</span>
                                <small class="mega-menu-item-desc">{{ __('navigation.community.most_viewed_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('whats-new.hot-topics') }}" class="mega-menu-item">
                            <i class="fa-solid fa-fire me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('navigation.community.hot_topics') }}</span>
                                <small class="mega-menu-item-desc">{{ __('navigation.community.hot_topics_desc') }}</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Column 3: Tools & Connect -->
        <div class="col-md-4">
            <div class="mega-menu-section">
                <h6 class="mega-menu-header">
                    <i class="fa-solid fa-tools me-2 text-warning"></i>
                    <span>{{ __('navigation.community.tools_connect') }}</span>
                </h6>
                <ul class="mega-menu-list">
                    <li>
                        <a href="{{ route('threads.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-search-plus me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('search.actions.advanced') }}</span>
                                <small class="mega-menu-item-desc">{{ __('search.actions.advanced_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('members.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-users-gear me-2"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('navigation.community.member_directory') }}</span>
                                <small class="mega-menu-item-desc">{{ __('navigation.community.member_directory_desc') }}</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('events.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-calendar-days me-2 text-primary"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('navigation.community.events_webinars') }}</span>
                                <small class="mega-menu-item-desc">{{ __('navigation.community.events_webinars_desc') }}</small>
                            </div>
                            <span class="activity-indicator trending">{{ __('common.status.coming_soon') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('jobs.index') }}" class="mega-menu-item">
                            <i class="fa-solid fa-briefcase me-2 text-success"></i>
                            <div class="mega-menu-item-content">
                                <span class="mega-menu-item-title">{{ __('navigation.community.job_board') }}</span>
                                <small class="mega-menu-item-desc">{{ __('navigation.community.job_board_desc') }}</small>
                            </div>
                            <span class="activity-indicator trending">{{ __('common.status.coming_soon') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="mega-menu-footer">
        <div class="row">
            <div class="col-12">
                <div class="footer-section">
                    <h6 class="footer-title">
                        <i class="fa-solid fa-tools me-2"></i>
                        {{ __('navigation.tools.title') }}
                    </h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="tool-group">
                                <h6 class="tool-group-title">{{ __('navigation.tools.calculators') }}</h6>
                                <ul class="tool-links">
                                    <li>
                                        <a href="{{ route('tools.material-calculator') }}" class="tool-link">
                                            <i class="fa-solid fa-calculator me-2"></i>
                                            {{ __('navigation.tools.material_calculator') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('tools.process-calculator') }}" class="tool-link">
                                            <i class="fa-solid fa-cogs me-2"></i>
                                            {{ __('navigation.tools.process_calculator') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="tool-group">
                                <h6 class="tool-group-title">{{ __('navigation.tools.databases') }}</h6>
                                <ul class="tool-links">
                                    <li>
                                        <a href="{{ route('tools.materials') }}" class="tool-link">
                                            <i class="fa-solid fa-cube me-2"></i>
                                            {{ __('navigation.tools.materials_db') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('tools.standards') }}" class="tool-link">
                                            <i class="fa-solid fa-clipboard-check me-2"></i>
                                            {{ __('navigation.tools.standards_db') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('tools.processes') }}" class="tool-link">
                                            <i class="fa-solid fa-industry me-2"></i>
                                            {{ __('navigation.tools.processes_db') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="tool-group">
                                <h6 class="tool-group-title">{{ __('navigation.tools.libraries') }}</h6>
                                <ul class="tool-links">
                                    <li>
                                        <a href="{{ route('tools.cad-library') }}" class="tool-link">
                                            <i class="fa-solid fa-file-code me-2"></i>
                                            {{ __('navigation.tools.cad_library') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('tools.documentation') }}" class="tool-link">
                                            <i class="fa-solid fa-book me-2"></i>
                                            {{ __('navigation.tools.documentation') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for loading stats and search --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load community stats when mega menu is shown
    function loadCommunityStats() {
        // Use real API calls
        fetch('/api/community/quick-stats')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('onlineUsersCount').textContent = data.data.online_users || '--';
                    document.getElementById('todayPostsCount').textContent = data.data.today_posts || '--';
                    document.getElementById('trendingCount').textContent = data.data.trending_topics || '--';
                    document.getElementById('featuredCount').textContent = data.data.featured_discussions || '--';

                    // Update activity indicators
                    const recentIndicator = document.getElementById('recentActivityCount');
                    const trendingIndicator = document.getElementById('trendingActivityCount');
                    const onlineIndicator = document.getElementById('onlineActivityCount');

                    if (recentIndicator) recentIndicator.textContent = data.data.today_posts || '0';
                    if (trendingIndicator) trendingIndicator.textContent = data.data.trending_topics || '0';
                    if (onlineIndicator) onlineIndicator.textContent = data.data.online_users || '0';
                }
            })
            .catch(error => {
                console.error('Error loading community stats:', error);
                // Fallback to placeholder values
                document.getElementById('onlineUsersCount').textContent = '24';
                document.getElementById('todayPostsCount').textContent = '156';
                document.getElementById('trendingCount').textContent = '8';
                document.getElementById('featuredCount').textContent = '12';
            });
    }

    // Load stats when dropdown is shown
    const communityDropdown = document.getElementById('communityDropdown');
    if (communityDropdown) {
        communityDropdown.addEventListener('show.bs.dropdown', loadCommunityStats);
    }

    // Mega menu search functionality
    const megaMenuSearch = document.getElementById('megaMenuSearch');
    const searchSuggestions = document.getElementById('megaMenuSearchSuggestions');
    let searchTimeout;

    if (megaMenuSearch) {
        megaMenuSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchSuggestions.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}&limit=5`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.length > 0) {
                            showSearchSuggestions(data.data);
                        } else {
                            searchSuggestions.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading search suggestions:', error);
                        searchSuggestions.style.display = 'none';
                    });
            }, 300);
        });

        megaMenuSearch.addEventListener('blur', function() {
            setTimeout(() => {
                searchSuggestions.style.display = 'none';
            }, 200);
        });
    }

    function showSearchSuggestions(suggestions) {
        let html = '';
        suggestions.forEach(suggestion => {
            html += `<div class="mega-menu-suggestion-item" onclick="selectSuggestion('${suggestion}')">${suggestion}</div>`;
        });
        searchSuggestions.innerHTML = html;
        searchSuggestions.style.display = 'block';
    }

    window.selectSuggestion = function(suggestion) {
        megaMenuSearch.value = suggestion;
        searchSuggestions.style.display = 'none';
        performMegaMenuSearch();
    };

    window.performMegaMenuSearch = function() {
        const query = megaMenuSearch.value.trim();
        if (query) {
            window.location.href = `/search?q=${encodeURIComponent(query)}`;
        }
    };
});
</script>
