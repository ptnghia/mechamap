<!-- Showcase Sidebar -->
<div class="sidebar-showcase">
    <!-- Project Categories -->
    <div class="sidebar-card">
        <div class="card-header">
            <h6><i class="fas fa-th-large me-2"></i>{{ __('showcase.project_categories') }}</h6>
        </div>
        <div class="categories-list">
            @foreach($categories as $category)
            <a href="{{ $category['url'] }}" class="category-item">
                <div class="category-image">
                    @if(isset($category['cover_image']) && $category['cover_image'])
                        <img src="{{ $category['cover_image'] }}" alt="{{ $category['display_name'] }}" class="category-img">
                    @else
                        <div class="category-icon">
                            <i class="fas fa-{{ $category['name'] === 'design' ? 'drafting-compass' : ($category['name'] === 'manufacturing' ? 'industry' : ($category['name'] === 'analysis' ? 'chart-line' : 'cog')) }}"></i>
                        </div>
                    @endif
                </div>
                <div class="category-content">
                    <div class="category-name">{{ $category['display_name'] }}</div>
                    <div class="category-stats">
                        <span class="project-count">{{ $category['showcase_count'] }} {{ __('showcase.projects') }}</span>
                        <span class="rating-info">
                            <i class="fas fa-star text-warning"></i>
                            {{ number_format($category['avg_rating'], 1) }}
                        </span>
                    </div>
                </div>
                <div class="category-trend">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Advanced Search -->
    <div class="sidebar-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-search me-2"></i>{{ __('showcase.advanced_search') }}</h6>
            <button type="button" class="btn btn-sm btn-outline-secondary d-md-none"
                    onclick="toggleAdvancedSearch()" id="advancedSearchToggle">
                <i class="fas fa-chevron-down" id="advancedSearchIcon"></i>
            </button>
        </div>
        <div class="p-3 advanced-search-body" id="advancedSearchBody">
            <form method="GET" action="{{ route('showcase.index') }}" id="sidebarSearchForm">
                <!-- Project Name -->
                <div class="mb-3">
                    <label for="sidebar_search" class="form-label small fw-semibold">{{ __('showcase.project_name') }}</label>
                    <input type="text" id="sidebar_search" name="search" value="{{ request('search') }}"
                        class="form-control form-control-sm" placeholder="{{ __('showcase.search_placeholder') }}">
                </div>

                <!-- Category -->
                <div class="mb-3">
                    <label for="sidebar_category" class="form-label small fw-semibold">{{ __('showcase.category') }}</label>
                    <select id="sidebar_category" name="category" class="form-select form-select-sm">
                        <option value="">{{ __('showcase.all_categories') }}</option>
                        @foreach($searchFilters['categories'] as $cat)
                        <option value="{{ $cat['value'] }}" {{ request('category') === $cat['value'] ? 'selected' : '' }}>
                            {{ $cat['label'] }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Complexity -->
                <div class="mb-3">
                    <label for="sidebar_complexity" class="form-label small fw-semibold">{{ __('showcase.complexity') }}</label>
                    <select id="sidebar_complexity" name="complexity" class="form-select form-select-sm">
                        <option value="">{{ __('showcase.all_levels') }}</option>
                        @foreach($searchFilters['complexity_levels'] as $level)
                        <option value="{{ $level['value'] }}" {{ request('complexity') === $level['value'] ? 'selected' : '' }}>
                            {{ $level['label'] }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Project Type -->
                <div class="mb-3">
                    <label for="sidebar_project_type" class="form-label small fw-semibold">{{ __('showcase.project_type') }}</label>
                    <select id="sidebar_project_type" name="project_type" class="form-select form-select-sm">
                        <option value="">{{ __('showcase.all_types') }}</option>
                        @foreach($searchFilters['project_types'] as $type)
                        <option value="{{ $type['value'] }}" {{ request('project_type') === $type['value'] ? 'selected' : '' }}>
                            {{ $type['label'] }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Software -->
                <div class="mb-3">
                    <label for="sidebar_software" class="form-label small fw-semibold">{{ __('showcase.software') }}</label>
                    <select id="sidebar_software" name="software" class="form-select form-select-sm">
                        <option value="">{{ __('showcase.all_software') }}</option>
                        @foreach($searchFilters['software_options'] as $software)
                        <option value="{{ $software }}" {{ request('software') === $software ? 'selected' : '' }}>
                            {{ $software }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rating -->
                <div class="mb-3">
                    <label for="sidebar_rating_min" class="form-label small fw-semibold">{{ __('showcase.min_rating') }}</label>
                    <select id="sidebar_rating_min" name="rating_min" class="form-select form-select-sm">
                        <option value="">{{ __('showcase.all_ratings') }}</option>
                        <option value="4" {{ request('rating_min') === '4' ? 'selected' : '' }}>{{ __('showcase.4_plus_stars') }}</option>
                        <option value="3" {{ request('rating_min') === '3' ? 'selected' : '' }}>{{ __('showcase.3_plus_stars') }}</option>
                        <option value="2" {{ request('rating_min') === '2' ? 'selected' : '' }}>{{ __('showcase.2_plus_stars') }}</option>
                    </select>
                </div>

                <!-- Sort -->
                <div class="mb-3">
                    <label for="sidebar_sort" class="form-label small fw-semibold">{{ __('showcase.sort_by') }}</label>
                    <select id="sidebar_sort" name="sort" class="form-select form-select-sm" onchange="document.getElementById('sidebarSearchForm').submit()">
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>{{ __('showcase.newest') }}</option>
                        <option value="most_viewed" {{ request('sort') === 'most_viewed' ? 'selected' : '' }}>{{ __('showcase.most_viewed') }}</option>
                        <option value="highest_rated" {{ request('sort') === 'highest_rated' ? 'selected' : '' }}>{{ __('showcase.highest_rated') }}</option>
                        <option value="most_downloads" {{ request('sort') === 'most_downloads' ? 'selected' : '' }}>{{ __('showcase.most_downloads') }}</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>{{ __('showcase.oldest') }}</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i>{{ __('showcase.search') }}
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearSidebarFilters()">
                        <i class="fas fa-times me-1"></i>{{ __('showcase.clear_filters') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="sidebar-card">
        <div class="card-header">
            <h6><i class="fas fa-chart-bar me-2"></i>{{ __('showcase.quick_stats') }}</h6>
        </div>
        <div class="showcase-overview p-3">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fas fa-project-diagram"></i>
                        {{ $allShowcases->total() }}
                    </div>
                    <div class="stat-label">{{ __('showcase.total_projects') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fas fa-users"></i>
                        {{ collect($categories)->sum('showcase_count') > 0 ? count($categories) : 0 }}
                    </div>
                    <div class="stat-label">{{ __('showcase.contributors') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create New Project CTA -->
    <div class="sidebar-card cta-section">
        <div class="p-3 text-center">
            <h6 class="mb-3">{{ __('showcase.share_your_project') }}</h6>
            <p class="small text-muted mb-3">{{ __('showcase.showcase_description') }}</p>
            <a href="{{ route('showcase.create') }}" class="btn btn-success btn-sm w-100">
                <i class="fas fa-plus me-1"></i>{{ __('showcase.create_new') }}
            </a>
        </div>
    </div>
</div>

<script>
// Clear sidebar filters function
function clearSidebarFilters() {
    // Reset all form inputs
    document.getElementById('sidebarSearchForm').reset();

    // Remove all query parameters and redirect
    window.location.href = '{{ route("showcase.index") }}';
}

// Toggle advanced search on mobile
function toggleAdvancedSearch() {
    const body = document.getElementById('advancedSearchBody');
    const icon = document.getElementById('advancedSearchIcon');
    const isCollapsed = body.style.display === 'none';

    if (isCollapsed) {
        body.style.display = 'block';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        body.style.display = 'none';
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

// Auto-submit form when filters change (optional)
document.addEventListener('DOMContentLoaded', function() {
    const sidebarFilterInputs = document.querySelectorAll('#sidebarSearchForm select:not(#sidebar_sort)');

    sidebarFilterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Optional: Auto-submit on filter change
            // document.getElementById('sidebarSearchForm').submit();
        });
    });

    // Auto-hide advanced search on mobile
    if (window.innerWidth < 768) {
        const body = document.getElementById('advancedSearchBody');
        if (body) {
            body.style.display = 'none';
        }
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        const body = document.getElementById('advancedSearchBody');
        const toggle = document.getElementById('advancedSearchToggle');

        if (window.innerWidth >= 768) {
            // Desktop: always show
            if (body) body.style.display = 'block';
        } else {
            // Mobile: keep current state or hide if first load
            if (body && body.style.display === '') {
                body.style.display = 'none';
            }
        }
    });
});
</script>
