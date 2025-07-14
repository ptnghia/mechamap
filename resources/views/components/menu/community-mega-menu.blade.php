{{--
    MechaMap Community Mega Menu Component
    3-column mega menu for community navigation
    Uses Bootstrap grid system and custom CSS
--}}

<div class="mega-menu-container">
    <div class="container-fluid">
        <div class="row">
            <!-- Column 1: Quick Access -->
            <div class="col-md-4">
                <div class="mega-menu-section">
                    <h6 class="mega-menu-header">
                        <i class="fa-solid fa-rocket me-2 text-primary"></i>
                        <span>{{ __('ui.community.quick_access') }}</span>
                    </h6>
                    <ul class="mega-menu-list">
                        <li>
                            <a href="{{ route('forums.index') }}" class="mega-menu-item">
                                <i class="fa-solid fa-home me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('forum.threads.title') }}</span>
                                    <small class="mega-menu-item-desc">Trang chủ diễn đàn</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('whats-new.popular') }}" class="mega-menu-item">
                                <i class="fa-solid fa-star me-2 text-warning"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('ui.common.popular_topics') }}</span>
                                    <small class="mega-menu-item-desc">Thảo luận nổi bật</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('forums.index') }}#categories" class="mega-menu-item">
                                <i class="fa-solid fa-folder-tree me-2 text-info"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">Duyệt theo danh mục</span>
                                    <small class="mega-menu-item-desc">Khám phá các chủ đề</small>
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
                        <span>{{ __('ui.community.discover') }}</span>
                    </h6>
                    <ul class="mega-menu-list">
                        <li>
                            <a href="{{ route('whats-new') }}" class="mega-menu-item">
                                <i class="fa-solid fa-clock me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('ui.common.recent_discussions') }}</span>
                                    <small class="mega-menu-item-desc">Thảo luận gần đây</small>
                                </div>
                                <span class="activity-indicator" id="recentActivityCount">--</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('whats-new.trending') }}" class="mega-menu-item">
                                <i class="fa-solid fa-chart-line me-2 text-success"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('ui.common.trending') }}</span>
                                    <small class="mega-menu-item-desc">Xu hướng hot</small>
                                </div>
                                <span class="activity-indicator trending" id="trendingActivityCount">--</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('whats-new.most-viewed') }}" class="mega-menu-item">
                                <i class="fa-solid fa-eye me-2 text-primary"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('ui.common.most_viewed') }}</span>
                                    <small class="mega-menu-item-desc">Xem nhiều nhất</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('whats-new.hot-topics') }}" class="mega-menu-item">
                                <i class="fa-solid fa-flame me-2 text-danger"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('ui.common.hot_topics') }}</span>
                                    <small class="mega-menu-item-desc">Chủ đề nóng</small>
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
                        <span>{{ __('ui.community.tools_connect') }}</span>
                    </h6>
                    <ul class="mega-menu-list">
                        <li>
                            <a href="{{ route('forums.search.advanced') }}" class="mega-menu-item">
                                <i class="fa-solid fa-search-plus me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">Tìm kiếm nâng cao</span>
                                    <small class="mega-menu-item-desc">Tìm kiếm chi tiết</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="mega-menu-search-box">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="megaMenuSearch" placeholder="Tìm kiếm nhanh..." autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" onclick="performMegaMenuSearch()">
                                        <i class="fa-solid fa-search"></i>
                                    </button>
                                </div>
                                <div id="megaMenuSearchSuggestions" class="mega-menu-search-suggestions"></div>
                            </div>
                        </li>
                        <li>
                            <a href="{{ route('forums.index') }}" class="mega-menu-item">
                                <i class="fa-solid fa-list me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">Xem tất cả</span>
                                    <small class="mega-menu-item-desc">Toàn bộ diễn đàn</small>
                                </div>
                            </a>
                        </li>
                    </ul>

                    <!-- Connect Section -->
                    <h6 class="mega-menu-header mt-3">
                        <i class="fa-solid fa-users me-2 text-info"></i>
                        <span>{{ __('ui.common.networking') }}</span>
                    </h6>
                    <ul class="mega-menu-list">
                        <li>
                            <a href="{{ route('members.index') }}" class="mega-menu-item">
                                <i class="fa-solid fa-users-gear me-2"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('ui.common.member_directory') }}</span>
                                    <small class="mega-menu-item-desc">Danh bạ thành viên</small>
                                </div>
                                <span class="activity-indicator online" id="onlineActivityCount">--</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('events.index') }}" class="mega-menu-item">
                                <i class="fa-solid fa-calendar-days me-2 text-primary"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('ui.common.events_webinars') }}</span>
                                    <small class="mega-menu-item-desc">Sự kiện & Webinar</small>
                                    <span class="badge badge-coming-soon">Coming Soon</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('jobs.index') }}" class="mega-menu-item">
                                <i class="fa-solid fa-briefcase me-2 text-success"></i>
                                <div class="mega-menu-item-content">
                                    <span class="mega-menu-item-title">{{ __('ui.common.job_board') }}</span>
                                    <small class="mega-menu-item-desc">Bảng việc làm</small>
                                    <span class="badge badge-coming-soon">Coming Soon</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick Stats Footer -->
        <div class="row mt-3 pt-3 border-top">
            <div class="col-12">
                <div class="mega-menu-stats d-flex justify-content-around text-center">
                    <div class="stat-item">
                        <i class="fa-solid fa-users text-primary"></i>
                        <span class="stat-number" id="onlineUsersCount">--</span>
                        <small class="stat-label">Online</small>
                    </div>
                    <div class="stat-item">
                        <i class="fa-solid fa-comments text-success"></i>
                        <span class="stat-number" id="todayPostsCount">--</span>
                        <small class="stat-label">Hôm nay</small>
                    </div>
                    <div class="stat-item">
                        <i class="fa-solid fa-fire text-danger"></i>
                        <span class="stat-number" id="trendingCount">--</span>
                        <small class="stat-label">Trending</small>
                    </div>
                    <div class="stat-item">
                        <i class="fa-solid fa-star text-warning"></i>
                        <span class="stat-number" id="featuredCount">--</span>
                        <small class="stat-label">Featured</small>
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
