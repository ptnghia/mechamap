@extends('layouts.app')

@section('title', 'Dashboard - MechaMap')

@section('content')
<div class="container py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h2 mb-1">
                            Welcome back, {{ auth()->user()->name }}!
                            <span class="wave">👋</span>
                        </h1>
                        <p class="text-muted mb-0">
                            Here's what's happening in your MechaMap world today
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="d-flex justify-content-md-end gap-2">
                            <a href="{{ route('users.profile.index') }}" class="btn btn-outline-primary">
                                <i class="bx bx-user me-1"></i>
                                My Profile
                            </a>
                            <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-primary position-relative">
                                <i class="bx bx-bell me-1"></i>
                                Notifications
                                @if($unreadNotifications > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $unreadNotifications }}
                                </span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $stats['forum_posts'] }}</h3>
                            <p class="mb-0 opacity-75">Forum Posts</p>
                            <small class="opacity-75">
                                <i class="bx bx-trending-up me-1"></i>
                                +{{ $stats['posts_this_week'] }} this week
                            </small>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-message-dots display-6 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-stat-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $stats['marketplace_orders'] }}</h3>
                            <p class="mb-0 opacity-75">Orders Placed</p>
                            <small class="opacity-75">
                                ${{ number_format($stats['total_spent'], 2) }} total spent
                            </small>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-package display-6 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-stat-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $stats['activity_streak'] }}</h3>
                            <p class="mb-0 opacity-75">Day Streak</p>
                            <small class="opacity-75">
                                {{ $stats['activities_today'] }} activities today
                            </small>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-trending-up display-6 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-stat-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0">{{ $stats['achievements'] }}</h3>
                            <p class="mb-0 opacity-75">Achievements</p>
                            <small class="opacity-75">
                                {{ $stats['completion_percentage'] }}% profile complete
                            </small>
                        </div>
                        <div class="ms-3">
                            <i class="bx bx-trophy display-6 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-zap me-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('forums.threads.create') }}" class="quick-action-card">
                                <div class="quick-action-icon bg-primary">
                                    <i class="bx bx-plus"></i>
                                </div>
                                <div class="quick-action-text">Create Post</div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('marketplace.index') }}" class="quick-action-card">
                                <div class="quick-action-icon bg-success">
                                    <i class="bx bx-store"></i>
                                </div>
                                <div class="quick-action-text">Browse Shop</div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('marketplace.orders.index') }}" class="quick-action-card">
                                <div class="quick-action-icon bg-info">
                                    <i class="bx bx-package"></i>
                                </div>
                                <div class="quick-action-text">My Orders</div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('marketplace.wishlist.index') }}" class="quick-action-card">
                                <div class="quick-action-icon bg-danger">
                                    <i class="bx bx-heart"></i>
                                </div>
                                <div class="quick-action-text">Wishlist</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-time me-2"></i>
                        Recent Activity
                    </h5>
                    <a href="{{ route('users.activity.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if($recentActivity->count() > 0)
                    <div class="activity-feed">
                        @foreach($recentActivity as $activity)
                        <div class="activity-item">
                            <div class="activity-icon bg-{{ $activity->getTypeColor() }}">
                                <i class="bx {{ $activity->getTypeIcon() }}"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-text">
                                    {!! $activity->getFormattedDescription() !!}
                                </div>
                                <div class="activity-time">
                                    {{ $activity->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bx bx-time display-4 text-muted"></i>
                        <h6 class="mt-3">No Recent Activity</h6>
                        <p class="text-muted">Start engaging to see your activity here.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Forum Posts -->
            @if($recentPosts->count() > 0)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-message-square me-2"></i>
                        My Recent Posts
                    </h5>
                    <a href="{{ route('forums.index') }}" class="btn btn-sm btn-outline-primary">
                        Browse Forums
                    </a>
                </div>
                <div class="card-body">
                    @foreach($recentPosts as $post)
                    <div class="forum-post-item">
                        <div class="d-flex align-items-start">
                            <div class="post-icon me-3">
                                <i class="bx bx-message-dots text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="{{ route('threads.show', $post->thread) }}" class="text-decoration-none">
                                        {{ $post->thread->title }}
                                    </a>
                                </h6>
                                <p class="text-muted mb-2">{{ Str::limit(strip_tags($post->content), 120) }}</p>
                                <div class="d-flex align-items-center text-muted small">
                                    <span class="me-3">
                                        <i class="bx bx-time me-1"></i>
                                        {{ $post->created_at->diffForHumans() }}
                                    </span>
                                    <span class="me-3">
                                        <i class="bx bx-message me-1"></i>
                                        {{ $post->thread->replies_count }} replies
                                    </span>
                                    <span>
                                        <i class="bx bx-show me-1"></i>
                                        {{ $post->thread->views_count }} views
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Profile Summary -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ auth()->user()->getAvatarUrl() }}"
                         alt="{{ auth()->user()->name }}"
                         class="profile-avatar rounded-circle mb-3">
                    <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-2">{{ '@' . auth()->user()->username }}</p>
                    @if(auth()->user()->title)
                    <p class="text-primary mb-3">{{ auth()->user()->title }}</p>
                    @endif

                    <!-- Profile Completion -->
                    <div class="profile-completion mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Profile Completion</small>
                            <small class="fw-bold">{{ $profileCompletion }}%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" style="width: {{ $profileCompletion }}%"></div>
                        </div>
                    </div>

                    <a href="{{ route('users.profile.edit') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bx bx-edit me-1"></i>
                        Edit Profile
                    </a>
                </div>
            </div>

            <!-- Notifications -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bx bx-bell me-2"></i>
                        Recent Notifications
                    </h6>
                    @if($unreadNotifications > 0)
                    <span class="badge bg-danger">{{ $unreadNotifications }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($recentNotifications->count() > 0)
                    <div class="notifications-preview">
                        @foreach($recentNotifications->take(3) as $notification)
                        <div class="notification-preview-item {{ $notification->read_at ? '' : 'unread' }}">
                            <div class="d-flex align-items-start">
                                <div class="notification-icon me-2">
                                    <i class="bx {{ $notification->getTypeIcon() }} text-{{ $notification->getTypeColor() }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="notification-title">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </div>
                                    <div class="notification-time">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-outline-primary btn-sm w-100 mt-3">
                        View All Notifications
                    </a>
                    @else
                    <div class="text-center py-3">
                        <i class="bx bx-bell-off display-6 text-muted"></i>
                        <p class="text-muted mb-0">No notifications</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Orders -->
            @if($recentOrders->count() > 0)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bx bx-package me-2"></i>
                        Recent Orders
                    </h6>
                    <a href="{{ route('marketplace.orders.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @foreach($recentOrders->take(3) as $order)
                    <div class="order-preview-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-medium">Order #{{ $order->order_number }}</div>
                                <div class="text-muted small">
                                    {{ $order->created_at->format('M d, Y') }}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">${{ number_format($order->total_amount, 2) }}</div>
                                <span class="badge bg-{{ $order->getStatusColor() }} small">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Achievements -->
            @if($recentAchievements->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-trophy me-2"></i>
                        Recent Achievements
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($recentAchievements->take(3) as $achievement)
                    <div class="achievement-preview-item">
                        <div class="d-flex align-items-center">
                            <div class="achievement-icon me-3">
                                <i class="bx {{ $achievement->icon }} text-{{ $achievement->color }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium">{{ $achievement->name }}</div>
                                <div class="text-muted small">
                                    {{ $achievement->pivot->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.welcome-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 1rem;
}

.wave {
    animation: wave 2s infinite;
    transform-origin: 70% 70%;
    display: inline-block;
}

@keyframes wave {
    0% { transform: rotate(0deg); }
    10% { transform: rotate(14deg); }
    20% { transform: rotate(-8deg); }
    30% { transform: rotate(14deg); }
    40% { transform: rotate(-4deg); }
    50% { transform: rotate(10deg); }
    60% { transform: rotate(0deg); }
    100% { transform: rotate(0deg); }
}

.dashboard-stat-card {
    transition: transform 0.2s ease-in-out;
    border: none;
}

.dashboard-stat-card:hover {
    transform: translateY(-2px);
}

.quick-action-card {
    display: block;
    text-decoration: none;
    color: inherit;
    text-align: center;
    padding: 1.5rem 1rem;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    transition: all 0.2s ease-in-out;
    height: 100%;
}

.quick-action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    text-decoration: none;
    color: inherit;
}

.quick-action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    font-size: 1.5rem;
    color: white;
}

.quick-action-text {
    font-size: 0.875rem;
    font-weight: 500;
}

.activity-feed {
    max-height: 300px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.activity-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 0.75rem;
    flex-shrink: 0;
    font-size: 0.875rem;
}

.activity-content {
    flex-grow: 1;
}

.activity-text {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.activity-time {
    font-size: 0.75rem;
    color: #6c757d;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.forum-post-item {
    padding: 1rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.forum-post-item:last-child {
    border-bottom: none;
}

.notification-preview-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.notification-preview-item:last-child {
    border-bottom: none;
}

.notification-preview-item.unread {
    background: rgba(var(--bs-primary-rgb), 0.05);
    margin: 0 -1rem;
    padding: 0.75rem 1rem;
    border-radius: 4px;
}

.notification-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-title {
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.notification-time {
    font-size: 0.75rem;
    color: #6c757d;
}

.order-preview-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.order-preview-item:last-child {
    border-bottom: none;
}

.achievement-preview-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.achievement-preview-item:last-child {
    border-bottom: none;
}

.achievement-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(var(--bs-primary-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

@media (max-width: 768px) {
    .welcome-header {
        padding: 1.5rem;
        text-align: center;
    }

    .quick-action-card {
        padding: 1rem 0.5rem;
    }

    .quick-action-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }

    .profile-avatar {
        width: 60px;
        height: 60px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Auto-refresh dashboard data every 5 minutes
setInterval(() => {
    if (document.visibilityState === 'visible') {
        fetch('/dashboard/refresh', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.hasUpdates) {
                // Update notification badge
                const notificationBadge = document.querySelector('.position-absolute.badge');
                if (notificationBadge && data.unreadNotifications > 0) {
                    notificationBadge.textContent = data.unreadNotifications;
                }

                // Show subtle update indicator
                const updateIndicator = document.createElement('div');
                updateIndicator.className = 'alert alert-info alert-dismissible fade show position-fixed';
                updateIndicator.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
                updateIndicator.innerHTML = `
                    <i class="bx bx-info-circle me-2"></i>
                    Dashboard updated with new activity
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(updateIndicator);

                // Auto-dismiss after 3 seconds
                setTimeout(() => {
                    updateIndicator.remove();
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error refreshing dashboard:', error);
        });
    }
}, 300000); // 5 minutes

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
