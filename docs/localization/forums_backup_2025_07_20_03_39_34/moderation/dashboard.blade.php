@extends('layouts.app')

@section('title', 'Moderation Dashboard')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
            <li class="breadcrumb-item active">Moderation</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">
                <i class="bx bx-shield-check text-warning me-2"></i>
                Moderation Dashboard
            </h1>
            <p class="text-muted mb-0">Manage forum content and user behavior</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="refreshStats()">
                <i class="bx bx-refresh me-1"></i>
                Refresh
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bx bx-filter me-1"></i>
                    Quick Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('forums.moderation.reported-content') }}">
                        <i class="bx bx-flag me-2"></i>Reported Content
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('forums.moderation.pending-approval') }}">
                        <i class="bx bx-time me-2"></i>Pending Approval
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('forums.moderation.banned-users') }}">
                        <i class="bx bx-user-x me-2"></i>Banned Users
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card border-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="bx bx-flag"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $stats['reported_content'] }}</h3>
                            <p class="text-muted mb-0">Reported Content</p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('forums.moderation.reported-content') }}" class="btn btn-sm btn-outline-danger">
                            Review Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bx bx-time"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $stats['pending_approval'] }}</h3>
                            <p class="text-muted mb-0">Pending Approval</p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('forums.moderation.pending-approval') }}" class="btn btn-sm btn-outline-warning">
                            Review Queue
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bx bx-user-x"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $stats['banned_users'] }}</h3>
                            <p class="text-muted mb-0">Banned Users</p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('forums.moderation.banned-users') }}" class="btn btn-sm btn-outline-info">
                            Manage Bans
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bx bx-check-circle"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $stats['actions_today'] }}</h3>
                            <p class="text-muted mb-0">Actions Today</p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('forums.moderation.activity-log') }}" class="btn btn-sm btn-outline-success">
                            View Log
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Reports -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-flag text-danger me-2"></i>
                        Recent Reports
                    </h5>
                    <a href="{{ route('forums.moderation.reported-content') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @forelse($recentReports as $report)
                    <div class="report-item border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-{{ $report->priority_color }} me-2">
                                        {{ ucfirst($report->priority) }}
                                    </span>
                                    <span class="badge bg-secondary me-2">
                                        {{ ucfirst($report->type) }}
                                    </span>
                                    <small class="text-muted">
                                        {{ $report->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                
                                <h6 class="mb-1">
                                    <a href="{{ $report->content_url }}" class="text-decoration-none">
                                        {{ Str::limit($report->content_title, 60) }}
                                    </a>
                                </h6>
                                
                                <p class="text-muted mb-2">{{ Str::limit($report->reason, 100) }}</p>
                                
                                <div class="d-flex align-items-center text-muted small">
                                    <img src="{{ $report->reporter->getAvatarUrl() }}" 
                                         alt="{{ $report->reporter->name }}" 
                                         class="rounded-circle me-2" width="20" height="20">
                                    <span>Reported by {{ $report->reporter->name }}</span>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-success" 
                                        onclick="approveReport({{ $report->id }})">
                                    <i class="bx bx-check"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="rejectReport({{ $report->id }})">
                                    <i class="bx bx-x"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" 
                                        onclick="viewReport({{ $report->id }})">
                                    <i class="bx bx-show"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="bx bx-check-circle display-4 text-success"></i>
                        <h5 class="mt-3">No Recent Reports</h5>
                        <p class="text-muted">All caught up! No new reports to review.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions & Stats -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-zap text-primary me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('forums.moderation.reported-content') }}" 
                           class="btn btn-outline-danger btn-sm">
                            <i class="bx bx-flag me-2"></i>
                            Review Reports ({{ $stats['reported_content'] }})
                        </a>
                        <a href="{{ route('forums.moderation.pending-approval') }}" 
                           class="btn btn-outline-warning btn-sm">
                            <i class="bx bx-time me-2"></i>
                            Pending Approval ({{ $stats['pending_approval'] }})
                        </a>
                        <a href="{{ route('forums.moderation.user-warnings') }}" 
                           class="btn btn-outline-info btn-sm">
                            <i class="bx bx-user-voice me-2"></i>
                            Issue Warning
                        </a>
                        <a href="{{ route('forums.moderation.banned-users') }}" 
                           class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-user-x me-2"></i>
                            Manage Bans
                        </a>
                    </div>
                </div>
            </div>

            <!-- Activity Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-chart-line text-success me-2"></i>
                        Activity Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="h5 mb-0 text-primary">{{ $stats['actions_today'] }}</div>
                            <small class="text-muted">Today</small>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h5 mb-0 text-success">{{ $stats['actions_week'] }}</div>
                            <small class="text-muted">This Week</small>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-0 text-info">{{ $stats['actions_month'] }}</div>
                            <small class="text-muted">This Month</small>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-0 text-warning">{{ $stats['pending_total'] }}</div>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-history text-info me-2"></i>
                        Recent Activity
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($recentActivity as $activity)
                    <div class="d-flex align-items-start mb-3">
                        <div class="activity-icon bg-{{ $activity->type_color }} bg-opacity-10 text-{{ $activity->type_color }} me-3">
                            <i class="bx {{ $activity->type_icon }}"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="small">
                                <strong>{{ $activity->moderator->name }}</strong>
                                {{ $activity->description }}
                            </div>
                            <div class="text-muted small">
                                {{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">
                        <small>No recent activity</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.stat-card {
    transition: transform 0.2s ease-in-out;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.report-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.activity-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.min-w-0 {
    min-width: 0;
}

@media (max-width: 768px) {
    .stat-card .d-flex {
        flex-direction: column;
        text-align: center;
    }
    
    .stat-icon {
        margin: 0 auto 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function refreshStats() {
    // Add loading state
    const refreshBtn = document.querySelector('[onclick="refreshStats()"]');
    const originalText = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i>Refreshing...';
    refreshBtn.disabled = true;
    
    // Simulate refresh (replace with actual AJAX call)
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function approveReport(reportId) {
    if (confirm('Are you sure you want to approve this report?')) {
        // AJAX call to approve report
        fetch(`/forums/moderation/reports/${reportId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function rejectReport(reportId) {
    if (confirm('Are you sure you want to reject this report?')) {
        // AJAX call to reject report
        fetch(`/forums/moderation/reports/${reportId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function viewReport(reportId) {
    window.open(`/forums/moderation/reports/${reportId}`, '_blank');
}
</script>
@endpush
