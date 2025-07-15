{{--
    MechaMap Showcase Sidebar Component
    Sidebar chuyên dụng cho trang Showcase với thống kê và gợi ý dự án
--}}
@props(['showSidebar' => true, 'user' => null])

@if($showSidebar)
@php
$sidebarService = app(\App\Services\ShowcaseSidebarService::class);
$sidebarData = $sidebarService->getShowcaseSidebarData($user);
@endphp

<div class="sidebar-showcase" id="showcase-sidebar">
    <!-- Showcase Overview Card -->
    <div class="sidebar-card showcase-overview" data-aos="fade-up">
        <div class="card-body">
            <div class="showcase-header">
                <h5 class="fw-bold">
                    <i class="fas fa-project-diagram me-2 text-primary"></i>
                    {{ __('showcase.project_showcase') }}
                </h5>
                <p class="text-muted">{{ __('showcase.discover_engineering_projects') }}</p>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-solid fa-cubes"></i>
                        <span>{{ number_format($sidebarData['showcase_stats']['total_showcases']) }}</span>
                    </div>
                    <div class="stat-label">{{ __('showcase.total_projects') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-solid fa-download"></i>
                        <span>{{ number_format($sidebarData['showcase_stats']['total_downloads']) }}</span>
                    </div>
                    <div class="stat-label">{{ __('showcase.downloads') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-solid fa-star"></i>
                        <span>{{ number_format($sidebarData['showcase_stats']['avg_rating'], 1) }}</span>
                    </div>
                    <div class="stat-label">{{ __('showcase.avg_rating') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-solid fa-eye"></i>
                        <span>{{ number_format($sidebarData['showcase_stats']['total_views']) }}</span>
                    </div>
                    <div class="stat-label">{{ __('showcase.total_views') }}</div>
                </div>
            </div>

            @guest
            <div class="cta-section mt-3">
                <a href="{{ route('showcase.create') }}" class="btn btn-primary w-100">
                    <i class="fas fa-plus me-2"></i>{{ __('showcase.create_project') }}
                </a>
            </div>
            @endguest
        </div>
    </div>

    <!-- Popular Categories Card -->
    <div class="sidebar-card popular-categories" data-aos="fade-up" data-aos-delay="100">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-folder-open me-2 text-success"></i>
                {{ __('showcase.popular_categories') }}
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="categories-list">
                @foreach($sidebarData['popular_categories'] as $category)
                <a href="{{ route('showcase.public', ['category' => $category['slug']]) }}" class="category-item">
                    <div class="category-info">
                        <div class="category-name">{{ $category['name'] }}</div>
                        <div class="category-stats">
                            <span class="project-count">{{ $category['project_count'] }} {{ __('showcase.projects') }}</span>
                            <span class="avg-rating">
                                <i class="fas fa-star text-warning"></i> {{ number_format($category['avg_rating'], 1) }}
                            </span>
                        </div>
                    </div>
                    <div class="category-trend">
                        @if($category['trend'] > 0)
                        <i class="fas fa-arrow-up text-success"></i>
                        @else
                        <i class="fas fa-minus text-muted"></i>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Featured Projects Card -->
    <div class="sidebar-card featured-projects" data-aos="fade-up" data-aos-delay="200">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-star me-2 text-warning"></i>
                {{ __('showcase.featured_projects') }}
            </h6>
            <a href="{{ route('showcase.public', ['featured' => 1]) }}" class="btn btn-sm btn-link">
                {{ __('content.view_all') }}
            </a>
        </div>
        <div class="card-body p-0">
            <div class="projects-list">
                @foreach($sidebarData['featured_projects'] as $project)
                <div class="project-item">
                    <div class="project-image">
                        <img src="{{ $project['image_url'] }}" alt="{{ $project['title'] }}"
                             onerror="this.src='{{ asset('images/placeholder-project.jpg') }}'">
                        @if($project['complexity_level'])
                        <span class="complexity-badge badge-{{ $project['complexity_level'] }}">
                            {{ __('showcase.complexity_levels.' . $project['complexity_level']) }}
                        </span>
                        @endif
                    </div>
                    <div class="project-content">
                        <h6 class="project-title">
                            <a href="{{ route('showcase.show', $project['id']) }}" class="text-decoration-none">
                                {{ Str::limit($project['title'], 50) }}
                            </a>
                        </h6>
                        <div class="project-meta">
                            <span class="author">
                                <i class="fas fa-user me-1"></i>
                                {{ $project['author']['name'] }}
                            </span>
                            <span class="category">
                                <i class="fas fa-folder me-1"></i>
                                {{ $project['category'] }}
                            </span>
                        </div>
                        <div class="project-metrics">
                            <span class="metric">
                                <i class="fas fa-eye"></i> {{ number_format($project['views']) }}
                            </span>
                            <span class="metric">
                                <i class="fas fa-download"></i> {{ number_format($project['downloads']) }}
                            </span>
                            <span class="rating">
                                <i class="fas fa-star text-warning"></i> {{ number_format($project['rating'], 1) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Software Tools Card -->
    <div class="sidebar-card software-tools" data-aos="fade-up" data-aos-delay="300">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-tools me-2 text-info"></i>
                {{ __('showcase.popular_software') }}
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="software-list">
                @foreach($sidebarData['popular_software'] as $software)
                <a href="{{ route('showcase.public', ['software' => $software['name']]) }}" class="software-item">
                    <div class="software-icon">
                        <i class="{{ $software['icon'] }}"></i>
                    </div>
                    <div class="software-info">
                        <div class="software-name">{{ $software['name'] }}</div>
                        <div class="software-count">{{ $software['project_count'] }} {{ __('showcase.projects') }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top Contributors Card -->
    <div class="sidebar-card top-contributors" data-aos="fade-up" data-aos-delay="400">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-trophy me-2 text-primary"></i>
                {{ __('showcase.top_contributors') }}
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="contributors-list">
                @foreach($sidebarData['top_contributors'] as $index => $contributor)
                <a href="{{ route('profile.show', $contributor['username']) }}" class="contributor-item">
                    <div class="contributor-rank">#{{ $index + 1 }}</div>
                    <div class="contributor-avatar">
                        <img src="{{ $contributor['avatar'] }}" alt="{{ $contributor['name'] }}"
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr($contributor['name'], 0, 1))) }}&background=6366f1&color=fff&size=40'">
                    </div>
                    <div class="contributor-info">
                        <div class="contributor-name">{{ $contributor['name'] }}</div>
                        <div class="contributor-stats">
                            <span class="project-count">{{ $contributor['project_count'] }} {{ __('showcase.projects') }}</span>
                            <span class="total-views">{{ number_format($contributor['total_views']) }} {{ __('showcase.views') }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
// Showcase Sidebar JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeShowcaseSidebar();
});

function initializeShowcaseSidebar() {
    // Initialize tooltips
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Track sidebar interactions
    trackShowcaseSidebarInteractions();
}

function trackShowcaseSidebarInteractions() {
    document.querySelectorAll('.sidebar-showcase a').forEach(link => {
        link.addEventListener('click', function() {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'showcase_sidebar_click', {
                    'link_text': this.textContent.trim(),
                    'link_url': this.href,
                    'sidebar_section': this.closest('.sidebar-card').className
                });
            }
        });
    });
}
</script>
@endif
