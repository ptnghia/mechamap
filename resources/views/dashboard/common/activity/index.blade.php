@extends('dashboard.layouts.app')

@section('title', __('nav.user.activity'))

@php
    $pageTitle = __('nav.user.activity');
    $pageDescription = __('activity.activity_desc');
    $breadcrumbs = [
        ['title' => __('nav.user.activity'), 'url' => '#']
    ];
@endphp

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card primary">
            <div class="stats-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stats-value" data-stat="total_activities">{{ $stats['total_activities'] ?? 0 }}</div>
            <div class="stats-label">{{ __('activity.total_activities') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stats-value" data-stat="today_activities">{{ $stats['today_activities'] ?? 0 }}</div>
            <div class="stats-label">{{ __('activity.today') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-icon">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stats-value" data-stat="week_activities">{{ $stats['week_activities'] ?? 0 }}</div>
            <div class="stats-label">{{ __('activity.this_week') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="stats-icon">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stats-value" data-stat="streak_days">{{ $stats['streak_days'] ?? 0 }}</div>
            <div class="stats-label">{{ __('activity.activity_streak') }}</div>
        </div>
    </div>
</div>

<!-- Activity Timeline -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-history me-2"></i>
            {{ __('activity.recent_activity') }}
        </h5>
        <div class="btn-group" role="group">
            <input type="radio" class="btn-check" name="timeFilter" id="today" value="today" autocomplete="off" checked>
            <label class="btn btn-outline-primary btn-sm" for="today">{{ __('activity.today') }}</label>

            <input type="radio" class="btn-check" name="timeFilter" id="week" value="week" autocomplete="off">
            <label class="btn btn-outline-primary btn-sm" for="week">{{ __('activity.this_week') }}</label>

            <input type="radio" class="btn-check" name="timeFilter" id="month" value="month" autocomplete="off">
            <label class="btn btn-outline-primary btn-sm" for="month">{{ __('activity.this_month') }}</label>

            <input type="radio" class="btn-check" name="timeFilter" id="all" value="all" autocomplete="off">
            <label class="btn btn-outline-primary btn-sm" for="all">{{ __('activity.all_time') }}</label>
        </div>
    </div>
    <div class="card-body">
        <div id="activity-timeline" class="activity-timeline">
            @if($activities && $activities->count() > 0)
                @foreach($activities as $activity)
                    <div class="activity-item" data-date="{{ $activity['created_at']->format('Y-m-d') }}">
                        <div class="activity-icon">
                            <i class="{{ $activity['icon'] ?? 'fas fa-circle' }} text-{{ $activity['color'] ?? 'primary' }}"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-header">
                                <h6 class="activity-title mb-1">{{ $activity['title'] }}</h6>
                                <small class="text-muted">{{ $activity['created_at']->diffForHumans() }}</small>
                            </div>
                            @if($activity['description'])
                                <p class="activity-description mb-2">{{ $activity['description'] }}</p>
                            @endif
                            @if($activity['url'])
                                <a href="{{ $activity['url'] }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt me-1"></i>
                                    {{ __('activity.view_details') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- No pagination needed for activity feed -->
            @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('activity.no_activities') }}</h5>
                    <p class="text-muted">{{ __('activity.no_activities_desc') }}</p>
                    <a href="{{ route('threads.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        {{ __('activity.start_activity') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.stats-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-left: 4px solid;
    transition: transform 0.2s;
}

.stats-card:hover {
    transform: translateY(-2px);
}

.stats-card.primary { border-left-color: #007bff; }
.stats-card.success { border-left-color: #28a745; }
.stats-card.warning { border-left-color: #ffc107; }
.stats-card.info { border-left-color: #17a2b8; }

.stats-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #6c757d;
}

.stats-value {
    font-size: 2.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 0.5rem;
}

.stats-label {
    color: #6c757d;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.activity-timeline {
    position: relative;
}

.activity-item {
    display: flex;
    margin-bottom: 2rem;
    position: relative;
}

.activity-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 20px;
    top: 40px;
    bottom: -2rem;
    width: 2px;
    background: #e9ecef;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
    z-index: 1;
    position: relative;
}

.activity-content {
    flex: 1;
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.activity-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.activity-title {
    color: #333;
    margin: 0;
    flex: 1;
}

.activity-description {
    color: #6c757d;
    margin: 0;
    line-height: 1.5;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Time filter functionality
    const timeFilters = document.querySelectorAll('input[name="timeFilter"]');
    const activityItems = document.querySelectorAll('.activity-item');

    timeFilters.forEach(filter => {
        filter.addEventListener('change', function() {
            const selectedFilter = this.value;
            filterActivities(selectedFilter);
        });
    });

    function filterActivities(filter) {
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
        const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);

        activityItems.forEach(item => {
            const itemDate = new Date(item.dataset.date);
            let show = true;

            switch(filter) {
                case 'today':
                    show = itemDate >= today;
                    break;
                case 'week':
                    show = itemDate >= weekAgo;
                    break;
                case 'month':
                    show = itemDate >= monthAgo;
                    break;
                case 'all':
                default:
                    show = true;
                    break;
            }

            item.style.display = show ? 'flex' : 'none';
        });
    }
});
</script>
@endpush
