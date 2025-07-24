@extends('layouts.user-dashboard')

@section('title', __('nav.user.activity'))

@php
    $pageTitle = __('nav.user.activity');
    $pageDescription = __('messages.activity_desc');
    $breadcrumbs = [
        ['title' => __('nav.user.activity'), 'url' => '#']
    ];
@endphp

@section('dashboard-content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card primary">
            <div class="stats-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stats-value" data-stat="total_activities">{{ $stats['total_activities'] ?? 0 }}</div>
            <div class="stats-label">{{ __('messages.total_activities') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stats-value" data-stat="today_activities">{{ $stats['today_activities'] ?? 0 }}</div>
            <div class="stats-label">{{ __('messages.today') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-icon">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stats-value" data-stat="week_activities">{{ $stats['week_activities'] ?? 0 }}</div>
            <div class="stats-label">{{ __('messages.this_week') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="stats-icon">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stats-value" data-stat="streak_days">{{ $stats['streak_days'] ?? 0 }}</div>
            <div class="stats-label">{{ __('messages.activity_streak') }}</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="dashboard-filters">
    <form method="GET" action="{{ route('user.activity') }}">
        <div class="row">
            <div class="col-md-3">
                <div class="filter-group">
                    <label class="filter-label">Loại hoạt động</label>
                    <select name="type" class="form-select">
                        <option value="">{{ __('messages.all_activities') }}</option>
                        <option value="thread_created" {{ request('type') === 'thread_created' ? 'selected' : '' }}>
                            {{ __('messages.thread_created') }}
                        </option>
                        <option value="comment_posted" {{ request('type') === 'comment_posted' ? 'selected' : '' }}>
                            {{ __('messages.comment_posted') }}
                        </option>
                        <option value="thread_bookmarked" {{ request('type') === 'thread_bookmarked' ? 'selected' : '' }}>
                            {{ __('messages.thread_bookmarked') }}
                        </option>
                        <option value="thread_rated" {{ request('type') === 'thread_rated' ? 'selected' : '' }}>
                            {{ __('messages.thread_rated') }}
                        </option>
                        <option value="user_followed" {{ request('type') === 'user_followed' ? 'selected' : '' }}>
                            {{ __('messages.user_followed') }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group">
                    <label class="filter-label">{{ __('messages.time_period') }}</label>
                    <select name="period" class="form-select">
                        <option value="all" {{ request('period', 'all') === 'all' ? 'selected' : '' }}>
                            {{ __('messages.all_time') }}
                        </option>
                        <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>
                            {{ __('messages.today') }}
                        </option>
                        <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>
                            {{ __('messages.this_week') }}
                        </option>
                        <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>
                            {{ __('messages.this_month') }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group">
                    <label class="filter-label">{{ __('messages.forum') }}</label>
                    <select name="forum" class="form-select">
                        <option value="">{{ __('messages.all_forums') }}</option>
                        @foreach($forums ?? [] as $forum)
                            <option value="{{ $forum->id }}" {{ request('forum') == $forum->id ? 'selected' : '' }}>
                                {{ $forum->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group">
                    <label class="filter-label">{{ __('messages.search') }}</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="{{ __('messages.search_activities') }}"
                           value="{{ request('search') }}">
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Activity Timeline -->
@if($activities && $activities->count() > 0)
    <div class="activity-timeline">
        @php $currentDate = null; @endphp
        @foreach($activities as $activity)
            @php $activityDate = $activity->created_at->format('Y-m-d'); @endphp

            @if($currentDate !== $activityDate)
                @if($currentDate !== null)
                    </div> <!-- Close previous day group -->
                @endif

                <div class="timeline-date-group">
                    <div class="timeline-date-header">
                        <h6 class="mb-0">
                            @if($activity->created_at->isToday())
                                {{ __('messages.today') }}
                            @elseif($activity->created_at->isYesterday())
                                {{ __('messages.yesterday') }}
                            @else
                                {{ $activity->created_at->format('d/m/Y') }}
                            @endif
                        </h6>
                        <small class="text-muted">{{ $activity->created_at->format('l') }}</small>
                    </div>

                @php $currentDate = $activityDate; @endphp
            @endif

            <div class="activity-item" data-activity-id="{{ $activity->id }}">
                <div class="activity-icon {{ $activity->type }}">
                    <i class="{{ $activity->getIcon() }}"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-header">
                        <div class="activity-title">{{ $activity->getTitle() }}</div>
                        <div class="activity-time">{{ $activity->created_at->format('H:i') }}</div>
                    </div>
                    <div class="activity-description">{{ $activity->getDescription() }}</div>

                    @if($activity->hasRelatedContent())
                        <div class="activity-related-content mt-2">
                            @if($activity->thread)
                                <div class="related-thread">
                                    <a href="{{ route('threads.show', $activity->thread->slug) }}"
                                       class="text-decoration-none">
                                        <div class="d-flex align-items-center">
                                            @if($activity->thread->featured_image)
                                                <img src="{{ $activity->thread->featured_image }}" alt=""
                                                     class="me-2 rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $activity->thread->title }}</div>
                                                <small class="text-muted">{{ $activity->thread->forum->name }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            @if($activity->comment)
                                <div class="related-comment">
                                    <div class="comment-preview">
                                        <div class="comment-content">
                                            {{ Str::limit(strip_tags($activity->comment->content), 150) }}
                                        </div>
                                        <a href="{{ route('threads.show', $activity->comment->thread->slug) }}#comment-{{ $activity->comment->id }}"
                                           class="btn btn-sm btn-outline-primary mt-2">
                                            {{ __('messages.view_comment') }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($activity->user_followed)
                                <div class="related-user">
                                    <a href="{{ route('profile.show', $activity->user_followed->username) }}"
                                       class="text-decoration-none">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $activity->user_followed->getAvatarUrl() }}" alt=""
                                                 class="me-2 rounded-circle" style="width: 40px; height: 40px;">
                                            <div>
                                                <div class="fw-bold">{{ $activity->user_followed->name }}</div>
                                                <small class="text-muted">{{ $activity->user_followed->getRoleDisplayName() }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Activity Metrics -->
                    @if($activity->hasMetrics())
                        <div class="activity-metrics mt-2">
                            <div class="d-flex gap-3">
                                @if($activity->views_count)
                                    <small class="text-muted">
                                        <i class="fas fa-eye me-1"></i>{{ number_format($activity->views_count) }}
                                    </small>
                                @endif
                                @if($activity->comments_count)
                                    <small class="text-muted">
                                        <i class="fas fa-comment me-1"></i>{{ $activity->comments_count }}
                                    </small>
                                @endif
                                @if($activity->rating)
                                    <small class="text-muted">
                                        <i class="fas fa-star me-1"></i>{{ $activity->rating }}/5
                                    </small>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        @if($currentDate !== null)
            </div> <!-- Close last day group -->
        @endif
    </div>

    <!-- Pagination -->
    @if($activities->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $activities->links() }}
        </div>
    @endif
@else
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="empty-state-title">{{ __('messages.no_activities_yet') }}</div>
        <div class="empty-state-description">{{ __('messages.no_activities_desc') }}</div>
        <a href="{{ route('threads.index') }}" class="btn btn-primary">
            <i class="fas fa-comments me-2"></i>{{ __('messages.start_participating') }}
        </a>
    </div>
@endif

<style>
.activity-timeline {
    position: relative;
}

.timeline-date-group {
    margin-bottom: 2rem;
}

.timeline-date-header {
    background: #f8f9fa;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    border-left: 4px solid #007bff;
}

.activity-item {
    position: relative;
    padding-left: 60px;
    margin-bottom: 1.5rem;
}

.activity-item::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 50px;
    bottom: -1.5rem;
    width: 2px;
    background: #dee2e6;
}

.activity-item:last-child::before {
    display: none;
}

.activity-icon {
    position: absolute;
    left: 0;
    top: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: white;
    z-index: 1;
}

.activity-content {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.activity-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.activity-title {
    font-weight: 600;
    color: #2c3e50;
    flex: 1;
}

.activity-time {
    color: #6c757d;
    font-size: 0.875rem;
    white-space: nowrap;
    margin-left: 1rem;
}

.activity-description {
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.related-thread,
.related-comment,
.related-user {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 0.75rem;
}

.comment-preview {
    background: #fff;
    border-left: 3px solid #007bff;
    padding: 0.75rem;
    border-radius: 0 6px 6px 0;
}

.activity-metrics {
    border-top: 1px solid #eee;
    padding-top: 0.5rem;
}

/* Activity type colors */
.activity-icon.thread_created {
    background: linear-gradient(135deg, #007bff, #0056b3);
}

.activity-icon.comment_posted {
    background: linear-gradient(135deg, #28a745, #1e7e34);
}

.activity-icon.thread_bookmarked {
    background: linear-gradient(135deg, #ffc107, #e0a800);
}

.activity-icon.thread_rated {
    background: linear-gradient(135deg, #fd7e14, #e55a00);
}

.activity-icon.user_followed {
    background: linear-gradient(135deg, #e83e8c, #c2185b);
}

/* Responsive Design */
@media (max-width: 768px) {
    .activity-item {
        padding-left: 50px;
    }

    .activity-icon {
        width: 32px;
        height: 32px;
        font-size: 0.875rem;
    }

    .activity-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .activity-time {
        margin-left: 0;
        margin-top: 0.25rem;
    }
}
</style>

<script>
// Auto-refresh activity feed every 2 minutes
setInterval(function() {
    updateActivityFeed();
}, 120000);

function updateActivityFeed() {
    // This function is defined in user-dashboard.js
    if (typeof window.updateActivityFeed === 'function') {
        window.updateActivityFeed();
    }
}
</script>
@endsection
