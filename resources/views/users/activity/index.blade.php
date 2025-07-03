@extends('layouts.app')

@section('title', 'My Activity - MechaMap')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.profile.index') }}">My Profile</a></li>
            <li class="breadcrumb-item active">Activity</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-time text-primary me-2"></i>
                        My Activity
                    </h1>
                    <p class="text-muted mb-0">Track your engagement across MechaMap platform</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-filter me-1"></i>
                            Filter Activity
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?type=all">All Activity</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="?type=forum">
                                <i class="bx bx-message-dots me-2"></i>Forum Activity
                            </a></li>
                            <li><a class="dropdown-item" href="?type=marketplace">
                                <i class="bx bx-store me-2"></i>Marketplace Activity
                            </a></li>
                            <li><a class="dropdown-item" href="?type=profile">
                                <i class="bx bx-user me-2"></i>Profile Updates
                            </a></li>
                            <li><a class="dropdown-item" href="?type=social">
                                <i class="bx bx-users me-2"></i>Social Activity
                            </a></li>
                        </ul>
                    </div>
                    <button class="btn btn-outline-primary" onclick="exportActivity()">
                        <i class="bx bx-export me-1"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card activity-stat-card">
                <div class="card-body text-center">
                    <div class="activity-stat-icon bg-primary bg-opacity-10 text-primary mb-2">
                        <i class="bx bx-calendar"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['total_activities'] }}</h4>
                    <p class="text-muted mb-0">Total Activities</p>
                    <small class="text-success">
                        <i class="bx bx-trending-up"></i> +{{ $stats['activities_this_week'] }} this week
                    </small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card activity-stat-card">
                <div class="card-body text-center">
                    <div class="activity-stat-icon bg-success bg-opacity-10 text-success mb-2">
                        <i class="bx bx-message-dots"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['forum_activities'] }}</h4>
                    <p class="text-muted mb-0">Forum Posts</p>
                    <small class="text-info">
                        {{ $stats['forum_percentage'] }}% of total activity
                    </small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card activity-stat-card">
                <div class="card-body text-center">
                    <div class="activity-stat-icon bg-warning bg-opacity-10 text-warning mb-2">
                        <i class="bx bx-store"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['marketplace_activities'] }}</h4>
                    <p class="text-muted mb-0">Marketplace Actions</p>
                    <small class="text-info">
                        {{ $stats['marketplace_percentage'] }}% of total activity
                    </small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card activity-stat-card">
                <div class="card-body text-center">
                    <div class="activity-stat-icon bg-info bg-opacity-10 text-info mb-2">
                        <i class="bx bx-trophy"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['achievements_earned'] }}</h4>
                    <p class="text-muted mb-0">Achievements</p>
                    <small class="text-success">
                        Latest: {{ $stats['latest_achievement'] ?? 'None' }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Activity Timeline -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bx bx-history me-2"></i>
                            Activity Timeline
                        </h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="timeRange" id="today" {{ request('range', 'week') == 'today' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="today">Today</label>

                            <input type="radio" class="btn-check" name="timeRange" id="week" {{ request('range', 'week') == 'week' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="week">Week</label>

                            <input type="radio" class="btn-check" name="timeRange" id="month" {{ request('range', 'week') == 'month' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="month">Month</label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($activities->count() > 0)
                    <div class="activity-timeline">
                        @foreach($activities->groupBy(function($activity) { return $activity->created_at->format('Y-m-d'); }) as $date => $dayActivities)
                        <div class="timeline-date-group">
                            <div class="timeline-date-header">
                                <h6 class="mb-0">{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h6>
                                <span class="badge bg-light text-dark">{{ $dayActivities->count() }} activities</span>
                            </div>

                            @foreach($dayActivities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $activity->getTypeColor() }}">
                                    <i class="bx {{ $activity->getTypeIcon() }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <div class="timeline-title">
                                            {!! $activity->getFormattedDescription() !!}
                                        </div>
                                        <div class="timeline-time">
                                            {{ $activity->created_at->format('g:i A') }}
                                        </div>
                                    </div>

                                    @if($activity->metadata)
                                    <div class="timeline-metadata">
                                        @if($activity->type == 'forum_post_created' && isset($activity->metadata['thread_title']))
                                        <div class="activity-details">
                                            <i class="bx bx-message-square-detail me-1"></i>
                                            <a href="{{ $activity->metadata['thread_url'] ?? '#' }}" class="text-decoration-none">
                                                {{ $activity->metadata['thread_title'] }}
                                            </a>
                                        </div>
                                        @endif

                                        @if($activity->type == 'marketplace_order_placed' && isset($activity->metadata['order_total']))
                                        <div class="activity-details">
                                            <i class="bx bx-dollar me-1"></i>
                                            Order Total: ${{ number_format($activity->metadata['order_total'], 2) }}
                                        </div>
                                        @endif

                                        @if($activity->type == 'product_reviewed' && isset($activity->metadata['rating']))
                                        <div class="activity-details">
                                            <div class="rating-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bx {{ $i <= $activity->metadata['rating'] ? 'bxs-star text-warning' : 'bx-star text-muted' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @endif

                                    @if($activity->hasAttachments())
                                    <div class="timeline-attachments">
                                        @foreach($activity->attachments as $attachment)
                                        <div class="attachment-item">
                                            <i class="bx bx-paperclip me-1"></i>
                                            <a href="{{ $attachment->url }}" target="_blank" class="text-decoration-none">
                                                {{ $attachment->name }}
                                            </a>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>

                    <!-- Load More -->
                    @if($activities->hasMorePages())
                    <div class="text-center mt-4">
                        <button class="btn btn-outline-primary" onclick="loadMoreActivities()">
                            <i class="bx bx-loader-alt me-1"></i>
                            {{ __('messages.common.load_more') }}
                        </button>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-5">
                        <i class="bx bx-time display-1 text-muted"></i>
                        <h4 class="mt-3">No Activity Found</h4>
                        <p class="text-muted">Start engaging with the community to see your activity here.</p>
                        <div class="mt-4">
                            <a href="{{ route('forums.index') }}" class="btn btn-primary me-2">
                                <i class="bx bx-message-dots me-1"></i>
                                Browse Forums
                            </a>
                            <a href="{{ route('marketplace.index') }}" class="btn btn-outline-primary">
                                <i class="bx bx-store me-1"></i>
                                Visit Marketplace
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activity Sidebar -->
        <div class="col-lg-4">
            <!-- Activity Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-chart-pie me-2"></i>
                        Activity Breakdown
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" height="200"></canvas>
                </div>
            </div>

            <!-- Recent Achievements -->
            @if($recentAchievements->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-trophy me-2"></i>
                        Recent Achievements
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($recentAchievements as $achievement)
                    <div class="achievement-item d-flex align-items-center mb-3">
                        <div class="achievement-icon me-3">
                            <i class="bx {{ $achievement->icon }} text-{{ $achievement->color }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $achievement->name }}</h6>
                            <p class="text-muted small mb-0">{{ $achievement->description }}</p>
                            <div class="text-muted small">
                                {{ $achievement->pivot->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Activity Streaks -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-trending-up me-2"></i>
                        Activity Streaks
                    </h6>
                </div>
                <div class="card-body">
                    <div class="streak-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-medium">Current Streak</div>
                                <div class="text-muted small">Daily activity</div>
                            </div>
                            <div class="text-end">
                                <div class="h5 mb-0 text-primary">{{ $streaks['current'] }} days</div>
                                <div class="small text-success">
                                    <i class="bx bx-trending-up"></i> Active
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="streak-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-medium">Longest Streak</div>
                                <div class="text-muted small">Personal best</div>
                            </div>
                            <div class="text-end">
                                <div class="h5 mb-0 text-warning">{{ $streaks['longest'] }} days</div>
                                <div class="small text-muted">
                                    {{ $streaks['longest_date'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="streak-calendar">
                        <h6 class="small text-muted mb-2">THIS WEEK</h6>
                        <div class="d-flex justify-content-between">
                            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $index => $day)
                            <div class="streak-day text-center">
                                <div class="small text-muted">{{ $day }}</div>
                                <div class="streak-indicator {{ $weeklyActivity[$index] ? 'active' : '' }}">
                                    {{ $weeklyActivity[$index] ?? 0 }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-zap me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="clearActivityHistory()">
                            <i class="bx bx-trash me-1"></i>
                            Clear History
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="downloadActivityReport()">
                            <i class="bx bx-download me-1"></i>
                            Download Report
                        </button>
                        <a href="{{ route('users.preferences.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-cog me-1"></i>
                            Activity Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.activity-stat-card {
    transition: transform 0.2s ease-in-out;
}

.activity-stat-card:hover {
    transform: translateY(-2px);
}

.activity-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto;
}

.activity-timeline {
    position: relative;
}

.timeline-date-group {
    margin-bottom: 2rem;
}

.timeline-date-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
}

.timeline-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    position: relative;
}

.timeline-marker {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 1rem;
    flex-shrink: 0;
    font-size: 1.1rem;
}

.timeline-content {
    flex-grow: 1;
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 3px solid var(--bs-primary);
}

.timeline-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.timeline-title {
    flex-grow: 1;
    font-weight: 500;
}

.timeline-time {
    font-size: 0.875rem;
    color: #6c757d;
    white-space: nowrap;
}

.timeline-metadata {
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid #dee2e6;
}

.activity-details {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    color: #6c757d;
}

.rating-stars {
    display: flex;
    gap: 2px;
}

.achievement-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(var(--bs-primary-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.streak-calendar {
    margin-top: 1rem;
}

.streak-day {
    flex: 1;
}

.streak-indicator {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    margin: 0 auto;
    margin-top: 0.25rem;
}

.streak-indicator.active {
    background: var(--bs-success);
    color: white;
}

@media (max-width: 768px) {
    .timeline-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .timeline-time {
        margin-top: 0.25rem;
    }

    .timeline-marker {
        width: 32px;
        height: 32px;
        font-size: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Activity breakdown chart
const activityCtx = document.getElementById('activityChart').getContext('2d');
const activityChart = new Chart(activityCtx, {
    type: 'doughnut',
    data: {
        labels: @json($chartData['labels']),
        datasets: [{
            data: @json($chartData['values']),
            backgroundColor: [
                '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Time range switching
document.querySelectorAll('input[name="timeRange"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const range = this.id;
        window.location.href = `?range=${range}`;
    });
});

function loadMoreActivities() {
    const nextPage = {{ $activities->currentPage() + 1 }};
    const url = `{{ request()->fullUrl() }}&page=${nextPage}`;

    fetch(url)
        .then(response => response.text())
        .then(html => {
            // Parse and append new activities
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newActivities = doc.querySelector('.activity-timeline').innerHTML;

            document.querySelector('.activity-timeline').insertAdjacentHTML('beforeend', newActivities);
        })
        .catch(error => {
            console.error('Error loading more activities:', error);
        });
}

function exportActivity() {
    const type = new URLSearchParams(window.location.search).get('type') || 'all';
    const range = new URLSearchParams(window.location.search).get('range') || 'week';
    window.open(`/users/activity/export?type=${type}&range=${range}`, '_blank');
}

function clearActivityHistory() {
    if (confirm('Are you sure you want to clear your activity history? This action cannot be undone.')) {
        fetch('/users/activity/clear', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error clearing activity:', error);
        });
    }
}

function downloadActivityReport() {
    window.open('/users/activity/report', '_blank');
}
</script>
@endpush
