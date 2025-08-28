@props(['showSidebar' => true, 'user' => null])

@if($showSidebar)
@php
$currentRoute = Route::currentRouteName();
$sidebarService = app(\App\Services\SidebarDataService::class);
$sidebarData = $sidebarService->getSidebarData($user);
@endphp

<div class="sidebar-professional" id="professional-sidebar">
    @if($currentRoute === 'threads.create')
    @include('components.thread-creation-sidebar')
    @else
    <!-- Community Overview Card -->
    <div class="sidebar-card community-overview" data-aos="fade-up">
        <div class="card-body">
            <div class="community_header">
                <h5 class="fw-bold">{{ $sidebarData['site_settings']['name'] ?? t_sidebar('professional.mechamap_community') }}</h5>
                <p class="text-muted">{{ $sidebarData['site_settings']['tagline'] ?? t_sidebar('professional.professional_network') }}</p>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-regular fa-comment-dots"></i>
                        <span>{{ number_format($sidebarData['community_stats']['total_threads']) }}</span>
                    </div>
                    <div class="stat-label">{{ t_sidebar('professional.technical_discussions') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-solid fa-user-tie"></i>
                        <span>{{ number_format($sidebarData['community_stats']['verified_users']) }}</span>
                    </div>
                    <div class="stat-label">{{ t_sidebar('professional.engineers') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-solid fa-chart-area"></i>
                        <span>{{number_format($sidebarData['community_stats']['active_users_week']) }}</span>
                    </div>
                    <div class="stat-label">{{ t_sidebar('professional.weekly_activity') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>+{{ $sidebarData['community_stats']['growth_rate'] }}%</span>
                    </div>
                    <div class="stat-label">{{ t_sidebar('professional.growth_rate') }}</div>
                </div>
            </div>

            @guest
            <div class="cta-section mt-3">
                <a href="{{ route('register') }}" class="btn btn-primary w-100">
                    <i class="fas fa-user-plus me-2"></i>{{ t_sidebar('professional.join_professional_network') }}
                </a>
            </div>
            @endguest

        </div>
    </div>

    <!-- Trending Topics Card -->
    @if(isset($sidebarData['trending_topics']) && count($sidebarData['trending_topics']) > 0)
    <div class="sidebar-card trending-topics" data-aos="fade-up" data-aos-delay="100">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-chart-line me-2 text-success"></i>{{ t_sidebar('professional.weekly_trends') }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="trending-list">
                @foreach($sidebarData['trending_topics'] as $index => $topic)
                <a href="{{ route('forums.show', $topic['slug']) }}" class="trending-item">
                    <div class="trend-rank">0{{ $index + 1 }}</div>
                    <div class="trend-content">
                        <div class="trend-name">{{ $topic['name'] }}</div>
                        <div class="trend-stats">
                            <span class="trend-score">{{ $topic['trend_score'] }} {{ t_sidebar('professional.points') }}</span>
                            <span class="trend-threads">{{ $topic['thread_count'] }} {{ t_sidebar('professional.discussions') }}</span>
                        </div>
                    </div>
                    <div class="trend-indicator">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif <!-- Featured Discussions Card -->
    <div class="sidebar-card featured-discussions" data-aos="fade-up" data-aos-delay="200">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-bolt me-2 text-warning"></i>{{ t_sidebar('professional.featured_discussions') }}</h6>
            <a href="{{ route('threads.index', ['featured' => 1]) }}" class="btn btn-sm btn-link">{{ t_sidebar('main.view_all') }}</a>
        </div>
        <div class="card-body p-0">
            <div class="discussion-list">
                @foreach($sidebarData['featured_threads'] as $thread)
                <div class="discussion-item">
                    <div class="discussion-avatar">
                        <img src="{{ $thread['author']['avatar_url'] ?? 'https://ui-avatars.com/api/?name=' . urlencode(strtoupper(substr($thread['author']['name'], 0, 1))) . '&background=6366f1&color=fff&size=200' }}"
                             alt="{{ $thread['author']['name'] }}">
                    </div>
                    <div class="discussion-content">
                        <h6 class="discussion-title"><a href="{{ route('threads.show', [$thread['slug']]) }}" class="text-decoration-none">{{ Str::limit($thread['title'], 60) }}</a></h6>
                        <div class="discussion-meta">
                            <span class="author">
                                <i class="fas fa-user me-1"></i>
                                <a href="{{ route('profile.show', $thread['author']['username'] ?? $thread['author']['id']) }}"
                                   class="text-decoration-none" onclick="event.stopPropagation();">
                                    {{ $thread['author']['name'] }}
                                </a>
                            </span>
                            <span class="forum">
                                <i class="fas fa-comments me-1"></i>
                                <a href="{{ route('forums.show', $thread['forum']['slug'] ?? $thread['forum']['id']) }}"
                                   class="text-decoration-none" onclick="event.stopPropagation();">
                                    {{ $thread['forum']['name'] }}
                                </a>
                            </span>
                        </div>
                        <div class="discussion-metrics d-flex align-items-center justify-content-between">
                            <div class="">
                                <span class="metric">
                                    <i class="fas fa-eye"></i> {{ number_format($thread['metrics']['views']) }}
                                </span>
                                <span class="metric">
                                    <i class="fas fa-chart-line"></i> {{ $thread['metrics']['engagement_score'] }}
                                </span>
                            </div>
                            <div class="discussion-time">{{ $thread['time_ago'] }}</div>
                        </div>
                    </div>

                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Top Engineers Card -->
    <div class="sidebar-card top-engineers" data-aos="fade-up" data-aos-delay="300">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-trophy me-2 text-primary"></i>{{ t_sidebar('professional.top_engineers') }}</h6>
            <a href="{{ route('members.leaderboard') }}" class="btn btn-sm btn-link">{{ t_sidebar('professional.leaderboard') }}</a>
        </div>
        <div class="card-body p-0">
            <div class="engineers-list">
                @foreach($sidebarData['active_members'] as $index => $member)
                <a href="#" class="engineer-item">
                    <div class="engineer-rank">#{{ $index + 1 }}</div>
                    <div class="engineer-avatar">
                        <img src="{{ $member['avatar'] }}" alt="{{ $member['name'] }}" class="rounded-circle">
                        @if($member['is_recently_active'])
                        <div class="online-indicator"></div>
                        @endif
                    </div>
                    <div class="engineer-info">
                        <div class="engineer-name">{{ $member['name'] }}</div>
                        <div class="engineer-badge {{ $member['badge']['class'] }}">
                            <i class="{{ $member['badge']['icon'] }} me-1"></i>
                            {{ $member['badge']['name'] }}
                        </div>
                        <div class="engineer-score">
                            {{ $member['contribution_score'] }} {{ t_sidebar('professional.points') }}
                            @if($member['recent_activity_score'] > 0)
                            <small class="text-success">(+{{ $member['recent_activity_score'] }} {{ t_sidebar('professional.recently') }})</small>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Personalized Recommendations (for logged in users) -->
    @if($user && !empty($sidebarData['user_recommendations'])) <div class="sidebar-card recommendations"
        data-aos="fade-up" data-aos-delay="400">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-star me-2 text-info"></i>{{ t_sidebar('professional.recommendations_for_you') }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="recommendations-list">
                @foreach($sidebarData['user_recommendations'] as $recommendation)
                <a href="{{ route('threads.show', $recommendation['id']) }}" class="recommendation-item">
                    <div class="rec-content">
                        <h6 class="rec-title">{{ Str::limit($recommendation['title'], 50) }}</h6>
                        <div class="rec-meta">
                            <span>{{ t_sidebar('professional.by') }} {{ $recommendation['author'] }}</span>
                            <span>{{ t_sidebar('professional.in') }} {{ $recommendation['forum'] }}</span>
                        </div>
                    </div>
                    <div class="rec-score">{{ $recommendation['relevance_score'] }}%</div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <!-- Popular Forums Card -->
    <div class="sidebar-card popular-forums" data-aos="fade-up" data-aos-delay="500">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-layer-group me-2 text-primary"></i>{{ t_sidebar('professional.active_forums') }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="forums-list">
                @foreach($sidebarData['top_forums'] as $forum)
                <a href="{{ route('forums.show', $forum['slug']) }}" class="forum-item">
                    <div class="forum-info">
                        <h6 class="forum-name">{{ $forum['name'] }}</h6>
                        <p class="forum-desc">{{ $forum['description'] }}</p>
                        <div class="forum-stats">
                            <span class="stat">{{ $forum['recent_threads'] }} {{ t_sidebar('professional.new_this_month') }}</span>
                            <span class="activity-badge activity-{{ $forum['activity_level'] }}">
                                @if($forum['activity_level'] == 'high')
                                {{ t_sidebar('professional.high_activity') }}
                                @elseif($forum['activity_level'] == 'medium')
                                {{ t_sidebar('professional.medium_activity') }}
                                @else
                                {{ t_sidebar('professional.low_activity') }}
                                @endif
                            </span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    // Professional Sidebar JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeSidebar();
});

function initializeSidebar() {
    // Initialize collapsible sections
    document.querySelectorAll('.toggle-section').forEach(button => {
        button.addEventListener('click', function() {
            const target = document.getElementById(this.dataset.target);
            const icon = this.querySelector('i');

            if (target.classList.contains('show')) {
                target.classList.remove('show');
                icon.className = 'fas fa-chevron-down';
            } else {
                target.classList.add('show');
                icon.className = 'fas fa-chevron-up';
            }
        });
    });

    // Initialize tooltips for metrics
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Track sidebar interactions
    trackSidebarInteractions();

    // Initialize lazy loading for images
    initializeLazyLoading();
}

function trackSidebarInteractions() {
    document.querySelectorAll('.sidebar-professional a').forEach(link => {
        link.addEventListener('click', function() {
            // Track clicks for analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'sidebar_click', {
                    'link_text': this.textContent.trim(),
                    'link_url': this.href,
                    'sidebar_section': this.closest('.sidebar-card').className
                });
            }
        });
    });
}

function initializeLazyLoading() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
}

// Real-time updates
function updateSidebarStats() {
    fetch('/api/sidebar/stats')
        .then(response => response.json())
        .then(data => {
            // Update community stats
            document.querySelectorAll('.stat-number').forEach((el, index) => {
                if (data.stats && data.stats[index]) {
                    animateNumber(el, parseInt(el.textContent.replace(/,/g, '')), data.stats[index]);
                }
            });
        })
        .catch(error => console.error('Error updating sidebar stats:', error));
}

function animateNumber(element, start, end) {
    const duration = 1000;
    const increment = (end - start) / (duration / 16);
    let current = start;

    const timer = setInterval(() => {
        current += increment;
        if (current >= end) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current).toLocaleString();
    }, 16);
}

// Update stats every 5 minutes
setInterval(updateSidebarStats, 5 * 60 * 1000);
</script>
@endif
