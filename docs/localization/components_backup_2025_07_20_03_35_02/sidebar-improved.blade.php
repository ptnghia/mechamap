{{--
    MechaMap Improved Sidebar - Đơn giản, hiệu quả, hấp dẫn
    Tối ưu performance và UX
--}}
@props(['showSidebar' => true])

@if($showSidebar)
@php
// Cache dữ liệu để tối ưu performance
$communityStats = Cache::remember('sidebar_community_stats', 3600, function() {
    return [
        'total_threads' => \App\Models\Thread::where('moderation_status', 'approved')->count(),
        'total_users' => \App\Models\User::count(),
        'active_today' => \App\Models\User::whereDate('last_seen_at', today())->count(),
        'growth_rate' => '+12' // Có thể tính toán thực tế
    ];
});

$trendingForums = Cache::remember('sidebar_trending_forums', 1800, function() {
    return \App\Models\Forum::withCount(['threads' => function($q) {
        $q->where('created_at', '>=', now()->subWeek());
    }])
    ->having('threads_count', '>', 0)
    ->orderBy('threads_count', 'desc')
    ->take(5)
    ->get();
});

$featuredThreads = Cache::remember('sidebar_featured_threads', 1800, function() {
    return \App\Models\Thread::with(['user', 'forum'])
        ->where('is_featured', true)
        ->orWhere('is_sticky', true)
        ->orderBy('view_count', 'desc')
        ->take(8)
        ->get();
});

$topEngineers = Cache::remember('sidebar_top_engineers', 3600, function() {
    return \App\Models\User::withCount(['threads' => function($q) {
        $q->where('created_at', '>=', now()->subMonth());
    }])
    ->having('threads_count', '>', 0)
    ->orderBy('threads_count', 'desc')
    ->take(8)
    ->get();
});
@endphp

<div class="sidebar-improved">
    <!-- Community Stats Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="sidebar-icon bg-primary">
                    <i class="fas fa-cogs text-white"></i>
                </div>
                <div class="ms-3">
                    <h5 class="mb-0">{{ __('content.mechamap') }}</h5>
                    <small class="text-muted">{{ __('content.engineering_community') }}</small>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <div class="stat-box text-center">
                        <div class="stat-number text-primary">{{ number_format($communityStats['total_threads']) }}</div>
                        <div class="stat-label">{{ __('forums.threads.title') }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box text-center">
                        <div class="stat-number text-success">{{ number_format($communityStats['total_users']) }}</div>
                        <div class="stat-label">{{ __('content.engineers') }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box text-center">
                        <div class="stat-number text-info">{{ number_format($communityStats['active_today']) }}</div>
                        <div class="stat-label">{{ __('content.active_today') }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box text-center">
                        <div class="stat-number text-warning">{{ $communityStats['growth_rate'] }}%</div>
                        <div class="stat-label">{{ __('content.growth_rate') }}</div>
                    </div>
                </div>
            </div>

            @guest
            <div class="d-grid">
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>{{ __('content.join_professional_network') }}
                </a>
            </div>
            @endguest
        </div>
    </div>

    <!-- Trending Forums -->
    @if($trendingForums->count() > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-fire text-danger me-2"></i>{{ __('content.weekly_trends') }}
            </h6>
            <a href="{{ route('forums.index') }}" class="btn btn-sm btn-outline-primary">{{ __('content.view_all') }}</a>
        </div>
        <div class="card-body p-0">
            @foreach($trendingForums as $index => $forum)
            <a href="{{ route('forums.show', $forum) }}" class="d-flex align-items-center p-3 border-bottom text-decoration-none hover-bg-light">
                <div class="trending-rank me-3">
                    <span class="badge bg-primary rounded-pill">{{ $index + 1 }}</span>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold text-dark">{{ $forum->name }}</div>
                    <small class="text-muted">
                        {{ $forum->threads_count }} {{ __('forums.threads.title') }} {{ __('content.this_week') }}
                    </small>
                </div>
                <div class="text-success">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Featured Discussions -->
    @if($featuredThreads->count() > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-star text-warning me-2"></i>{{ __('content.featured_discussions') }}
            </h6>
            <a href="{{ route('threads.index', ['featured' => 1]) }}" class="btn btn-sm btn-outline-primary">{{ __('content.view_all') }}</a>
        </div>
        <div class="card-body p-0">
            @foreach($featuredThreads->take(5) as $thread)
            <a href="{{ route('threads.show', $thread) }}" class="d-flex align-items-center p-3 border-bottom text-decoration-none hover-bg-light">
                <img src="{{ $thread->user->getAvatarUrl() }}"
                     alt="{{ $thread->user->name }}"
                     class="rounded-circle me-3"
                     width="40" height="40"
                     style="object-fit: cover;"
                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr($thread->user->name, 0, 1))) }}&background=6366f1&color=fff&size=40'">
                <div class="flex-grow-1">
                    <div class="fw-semibold text-dark">{{ Str::limit($thread->title, 50) }}</div>
                    <small class="text-muted">
                        {{ $thread->user->name }} • {{ $thread->forum->name ?? '' }}
                    </small>
                </div>
                @if($thread->is_sticky)
                <span class="badge bg-primary ms-2">
                    <i class="fas fa-thumbtack"></i>
                </span>
                @endif
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Top Engineers -->
    @if($topEngineers->count() > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-trophy text-warning me-2"></i>{{ __('content.top_engineers') }}
            </h6>
            <a href="{{ route('members.leaderboard') }}" class="btn btn-sm btn-outline-primary">{{ __('content.leaderboard') }}</a>
        </div>
        <div class="card-body p-0">
            @foreach($topEngineers->take(6) as $index => $engineer)
            <div class="d-flex align-items-center p-3 border-bottom">
                <div class="position-relative me-3">
                    <img src="{{ $engineer->profile_photo_url ?? $engineer->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($engineer->name) . '&color=7F9CF5&background=EBF4FF' }}"
                         alt="{{ $engineer->name }}"
                         class="rounded-circle"
                         width="40" height="40"
                         style="object-fit: cover;"
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($engineer->name) }}&color=7F9CF5&background=EBF4FF'">
                    @if($index < 3)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                        {{ $index + 1 }}
                    </span>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold text-dark">{{ $engineer->name }}</div>
                    <small class="text-muted">
                        {{ $engineer->threads_count }} {{ __('forums.threads.title') }} {{ __('content.this_month') }}
                    </small>
                </div>
                <div class="text-success">
                    <small>+{{ $engineer->threads_count * 5 }} {{ __('content.points') }}</small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    @auth
    <div class="card shadow-sm">
        <div class="card-body">
            <h6 class="mb-3">
                <i class="fas fa-bolt text-primary me-2"></i>{{ __('content.quick_actions') }}
            </h6>
            <div class="d-grid gap-2">
                <a href="{{ route('threads.create') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>{{ __('forums.actions.create_thread') }}
                </a>
                <a href="{{ route('showcase.create') }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-camera me-2"></i>{{ __('content.share_project') }}
                </a>
                <a href="{{ route('profile.show', auth()->user()) }}" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-user me-2"></i>{{ __('content.my_profile') }}
                </a>
            </div>
        </div>
    </div>
    @endauth
</div>

<style>
.sidebar-improved .sidebar-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar-improved .stat-box {
    padding: 0.5rem;
    border-radius: 8px;
    background: rgba(0,123,255,0.05);
}

.sidebar-improved .stat-number {
    font-size: 1.25rem;
    font-weight: 700;
    line-height: 1;
}

.sidebar-improved .stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.sidebar-improved .hover-bg-light:hover {
    background-color: rgba(0,0,0,0.02) !important;
    transition: background-color 0.2s ease;
}

.sidebar-improved .trending-rank {
    min-width: 30px;
}

.sidebar-improved .card {
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 12px;
}

.sidebar-improved .card-header {
    background: rgba(0,123,255,0.02);
    border-bottom: 1px solid rgba(0,0,0,0.08);
    border-radius: 12px 12px 0 0 !important;
}

@media (max-width: 768px) {
    .sidebar-improved {
        margin-top: 2rem;
    }
}
</style>
@endif
