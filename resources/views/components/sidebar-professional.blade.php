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
                <h5 class="fw-bold">{{ $sidebarData['site_settings']['name'] ?? 'Cộng đồng MechaMap' }}</h5>
                <p class="text-muted">{{ $sidebarData['site_settings']['tagline'] ?? 'Mạng lưới Kỹ sư Chuyên
                    nghiệp' }}</p>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-regular fa-comment-dots"></i>
                        <span>{{ number_format($sidebarData['community_stats']['total_threads']) }}</span>
                    </div>
                    <div class="stat-label">Thảo luận Kỹ thuật</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-solid fa-user-tie"></i>
                        <span>{{ number_format($sidebarData['community_stats']['verified_users']) }}</span>
                    </div>
                    <div class="stat-label">Kỹ sư</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-solid fa-chart-area"></i>
                        <span>{{number_format($sidebarData['community_stats']['active_users_week']) }}</span>
                    </div>
                    <div class="stat-label">Hoạt động tuần này</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>+{{ $sidebarData['community_stats']['growth_rate'] }}%</span>
                    </div>
                    <div class="stat-label">Tỷ lệ tăng trưởng</div>
                </div>
            </div>

            @guest
            <div class="cta-section mt-3">
                <a href="{{ route('register') }}" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus me-2"></i>Tham gia Mạng lưới Chuyên nghiệp
                </a>
            </div>
            @endguest

        </div>
    </div>

    <!-- Trending Topics Card -->
    <div class="sidebar-card trending-topics" data-aos="fade-up" data-aos-delay="100">
        <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-graph-up me-2 text-success"></i>Xu hướng tuần này</h6>
        </div>
        <div class="card-body p-0">
            <div class="trending-list">
                @foreach($sidebarData['trending_topics'] as $index => $topic)
                <a href="{{ route('forums.show', $topic['slug']) }}" class="trending-item">
                    <div class="trend-rank">0{{ $index + 1 }}</div>
                    <div class="trend-content">
                        <div class="trend-name">{{ $topic['name'] }}</div>
                        <div class="trend-stats">
                            <span class="trend-score">{{ $topic['trend_score'] }} điểm</span>
                            <span class="trend-threads">{{ $topic['thread_count'] }} thảo luận</span>
                        </div>
                    </div>
                    <div class="trend-indicator">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div> <!-- Featured Discussions Card -->
    <div class="sidebar-card featured-discussions" data-aos="fade-up" data-aos-delay="200">
        <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-lightning-fill me-2 text-warning"></i>Thảo luận Nổi bật</h6>
            <a href="{{ route('threads.index', ['featured' => 1]) }}" class="btn btn-sm btn-link">Xem tất cả</a>
        </div>
        <div class="card-body p-0">
            <div class="discussion-list">
                @foreach($sidebarData['featured_threads'] as $thread)
                <a href="{{ route('threads.show', [$thread['slug']]) }}" class="discussion-item">
                    <div class="discussion-avatar">
                        <img src="{{ $thread['author']['avatar'] }}" alt="{{ $thread['author']['name'] }}" class="">
                    </div>
                    <div class="discussion-content">
                        <h6 class="discussion-title">{{ Str::limit($thread['title'], 60) }}</h6>
                        <div class="discussion-meta">
                            <span class="author">{{ $thread['author']['name'] }}</span>
                            <span class="forum">trong {{ $thread['forum']['name'] }}</span>
                        </div>
                        <div class="discussion-metrics">
                            <span class="metric">
                                <i class="bi bi-eye"></i> {{ number_format($thread['metrics']['views']) }}
                            </span>
                            <span class="metric">
                                <i class="bi bi-graph-up"></i> {{ $thread['metrics']['engagement_score'] }}
                            </span>
                        </div>
                    </div>
                    <div class="discussion-time">{{ $thread['time_ago'] }}</div>
                </a>
                @endforeach
            </div>
        </div>
    </div> <!-- Top Engineers Card -->
    <div class="sidebar-card top-engineers" data-aos="fade-up" data-aos-delay="300">
        <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-award me-2 text-primary"></i>Kỹ sư Hàng đầu</h6>
            <a href="{{ route('members.leaderboard') }}" class="btn btn-sm btn-link">Bảng xếp hạng</a>
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
                            {{ $member['contribution_score'] }} điểm
                            @if($member['recent_activity_score'] > 0)
                            <small class="text-success">(+{{ $member['recent_activity_score'] }} gần đây)</small>
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
            <h6 class="mb-0"><i class="bi bi-stars me-2 text-info"></i>Đề xuất cho bạn</h6>
        </div>
        <div class="card-body p-0">
            <div class="recommendations-list">
                @foreach($sidebarData['user_recommendations'] as $recommendation)
                <a href="{{ route('threads.show', $recommendation['id']) }}" class="recommendation-item">
                    <div class="rec-content">
                        <h6 class="rec-title">{{ Str::limit($recommendation['title'], 50) }}</h6>
                        <div class="rec-meta">
                            <span>bởi {{ $recommendation['author'] }}</span>
                            <span>trong {{ $recommendation['forum'] }}</span>
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
            <h6 class="mb-0"><i class="bi bi-collection me-2 text-primary"></i>Diễn đàn Hoạt động</h6>
        </div>
        <div class="card-body p-0">
            <div class="forums-list">
                @foreach($sidebarData['top_forums'] as $forum)
                <a href="{{ route('forums.show', $forum['slug']) }}" class="forum-item">
                    <div class="forum-avatar">
                        <img src="{{ $forum['image_url'] }}" alt="{{ $forum['name'] }}" class="rounded">
                    </div>
                    <div class="forum-info">
                        <h6 class="forum-name">{{ $forum['name'] }}</h6>
                        <p class="forum-desc">{{ $forum['description'] }}</p>
                        <div class="forum-stats">
                            <span class="stat">{{ $forum['recent_threads'] }} mới trong tháng</span>
                            <span class="activity-badge activity-{{ $forum['activity_level'] }}">
                                @if($forum['activity_level'] == 'high')
                                Hoạt động Cao
                                @elseif($forum['activity_level'] == 'medium')
                                Hoạt động Trung bình
                                @else
                                Hoạt động Thấp
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
                icon.className = 'bi bi-chevron-down';
            } else {
                target.classList.add('show');
                icon.className = 'bi bi-chevron-up';
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
