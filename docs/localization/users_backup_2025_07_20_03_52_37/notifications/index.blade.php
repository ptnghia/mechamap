@extends('layouts.app')

@section('title', 'Notifications - MechaMap')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.profile.index') }}">My Profile</a></li>
            <li class="breadcrumb-item active">Notifications</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-bell text-primary me-2"></i>
                        Notifications
                        @if($unreadCount > 0)
                        <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                        @endif
                    </h1>
                    <p class="text-muted mb-0">Stay updated with your MechaMap activity</p>
                </div>
                <div class="d-flex gap-2">
                    @if($unreadCount > 0)
                    <button class="btn btn-outline-success" onclick="markAllAsRead()">
                        <i class="bx bx-check-double me-1"></i>
                        Mark All Read
                    </button>
                    @endif
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-filter me-1"></i>
                            Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?filter=all">All Notifications</a></li>
                            <li><a class="dropdown-item" href="?filter=unread">Unread Only</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="?type=forum">
                                <i class="bx bx-message-dots me-2"></i>Forum
                            </a></li>
                            <li><a class="dropdown-item" href="?type=marketplace">
                                <i class="bx bx-store me-2"></i>Marketplace
                            </a></li>
                            <li><a class="dropdown-item" href="?type=system">
                                <i class="bx bx-cog me-2"></i>System
                            </a></li>
                            <li><a class="dropdown-item" href="?type=social">
                                <i class="bx bx-users me-2"></i>Social
                            </a></li>
                        </ul>
                    </div>
                    <a href="{{ route('users.preferences.index') }}" class="btn btn-outline-primary">
                        <i class="bx bx-cog me-1"></i>
                        Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card notification-stat-card">
                <div class="card-body text-center">
                    <div class="notification-stat-icon bg-danger bg-opacity-10 text-danger mb-2">
                        <i class="bx bx-bell"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['unread'] }}</h4>
                    <p class="text-muted mb-0">Unread</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card notification-stat-card">
                <div class="card-body text-center">
                    <div class="notification-stat-icon bg-success bg-opacity-10 text-success mb-2">
                        <i class="bx bx-check-circle"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['read'] }}</h4>
                    <p class="text-muted mb-0">Read</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card notification-stat-card">
                <div class="card-body text-center">
                    <div class="notification-stat-icon bg-primary bg-opacity-10 text-primary mb-2">
                        <i class="bx bx-calendar"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['today'] }}</h4>
                    <p class="text-muted mb-0">Today</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card notification-stat-card">
                <div class="card-body text-center">
                    <div class="notification-stat-icon bg-warning bg-opacity-10 text-warning mb-2">
                        <i class="bx bx-time"></i>
                    </div>
                    <h4 class="mb-1">{{ $stats['this_week'] }}</h4>
                    <p class="text-muted mb-0">This Week</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Notifications List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bx bx-list-ul me-2"></i>
                            All Notifications
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">
                                Showing {{ $notifications->firstItem() ?? 0 }}-{{ $notifications->lastItem() ?? 0 }} 
                                of {{ $notifications->total() }}
                            </span>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="sortOrder" id="newest" {{ request('sort', 'newest') == 'newest' ? 'checked' : '' }}>
                                <label class="btn btn-outline-secondary" for="newest">Newest</label>
                                
                                <input type="radio" class="btn-check" name="sortOrder" id="oldest" {{ request('sort') == 'oldest' ? 'checked' : '' }}>
                                <label class="btn btn-outline-secondary" for="oldest">Oldest</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                    <div class="notifications-list">
                        @foreach($notifications as $notification)
                        <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}" 
                             data-notification-id="{{ $notification->id }}">
                            <div class="notification-content">
                                <div class="d-flex align-items-start">
                                    <div class="notification-icon me-3">
                                        <i class="bx {{ $notification->getTypeIcon() }} text-{{ $notification->getTypeColor() }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="notification-header">
                                            <h6 class="notification-title mb-1">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </h6>
                                            <div class="notification-time">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        <div class="notification-message">
                                            {{ $notification->data['message'] ?? '' }}
                                        </div>
                                        
                                        @if(isset($notification->data['action_url']))
                                        <div class="notification-action mt-2">
                                            <a href="{{ $notification->data['action_url'] }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               onclick="markAsRead('{{ $notification->id }}')">
                                                {{ $notification->data['action_text'] ?? 'View' }}
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="notification-actions">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if(!$notification->read_at)
                                                <li><a class="dropdown-item" href="#" 
                                                       onclick="markAsRead('{{ $notification->id }}')">
                                                    <i class="bx bx-check me-2"></i>Mark as Read
                                                </a></li>
                                                @else
                                                <li><a class="dropdown-item" href="#" 
                                                       onclick="markAsUnread('{{ $notification->id }}')">
                                                    <i class="bx bx-envelope me-2"></i>Mark as Unread
                                                </a></li>
                                                @endif
                                                <li><a class="dropdown-item text-danger" href="#" 
                                                       onclick="deleteNotification('{{ $notification->id }}')">
                                                    <i class="bx bx-trash me-2"></i>Delete
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                @if(!$notification->read_at)
                                <div class="unread-indicator"></div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($notifications->hasPages())
                    <div class="card-footer">
                        {{ $notifications->appends(request()->query())->links() }}
                    </div>
                    @endif
                    @else
                    <div class="text-center py-5">
                        <i class="bx bx-bell-off display-1 text-muted"></i>
                        <h4 class="mt-3">No Notifications</h4>
                        <p class="text-muted">You're all caught up! New notifications will appear here.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Notification Types -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-category me-2"></i>
                        Notification Types
                    </h6>
                </div>
                <div class="card-body">
                    <div class="notification-types">
                        <div class="type-item d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-message-dots text-primary me-2"></i>
                                <span>Forum Activity</span>
                            </div>
                            <span class="badge bg-primary">{{ $typeStats['forum'] ?? 0 }}</span>
                        </div>
                        <div class="type-item d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-store text-success me-2"></i>
                                <span>Marketplace</span>
                            </div>
                            <span class="badge bg-success">{{ $typeStats['marketplace'] ?? 0 }}</span>
                        </div>
                        <div class="type-item d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-users text-info me-2"></i>
                                <span>Social</span>
                            </div>
                            <span class="badge bg-info">{{ $typeStats['social'] ?? 0 }}</span>
                        </div>
                        <div class="type-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-cog text-warning me-2"></i>
                                <span>System</span>
                            </div>
                            <span class="badge bg-warning">{{ $typeStats['system'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-zap me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($unreadCount > 0)
                        <button class="btn btn-primary btn-sm" onclick="markAllAsRead()">
                            <i class="bx bx-check-double me-1"></i>
                            Mark All as Read
                        </button>
                        @endif
                        <button class="btn btn-outline-secondary btn-sm" onclick="deleteAllRead()">
                            <i class="bx bx-trash me-1"></i>
                            Delete All Read
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="exportNotifications()">
                            <i class="bx bx-export me-1"></i>
                            Export History
                        </button>
                        <a href="{{ route('users.preferences.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-cog me-1"></i>
                            Notification Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notification Preferences -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-bell-ring me-2"></i>
                        Preferences
                    </h6>
                </div>
                <div class="card-body">
                    <div class="preference-item d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="fw-medium">Email Notifications</div>
                            <div class="text-muted small">Receive notifications via email</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" 
                                   {{ auth()->user()->email_notifications ? 'checked' : '' }}
                                   onchange="updatePreference('email_notifications', this.checked)">
                        </div>
                    </div>
                    
                    <div class="preference-item d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="fw-medium">Push Notifications</div>
                            <div class="text-muted small">Browser push notifications</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="pushNotifications" 
                                   {{ auth()->user()->push_notifications ? 'checked' : '' }}
                                   onchange="updatePreference('push_notifications', this.checked)">
                        </div>
                    </div>
                    
                    <div class="preference-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-medium">Sound Alerts</div>
                            <div class="text-muted small">Play sound for new notifications</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="soundAlerts" 
                                   {{ auth()->user()->sound_alerts ? 'checked' : '' }}
                                   onchange="updatePreference('sound_alerts', this.checked)">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.notification-stat-card {
    transition: transform 0.2s ease-in-out;
}

.notification-stat-card:hover {
    transform: translateY(-2px);
}

.notification-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto;
}

.notifications-list {
    max-height: 600px;
    overflow-y: auto;
}

.notification-item {
    position: relative;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.2s ease;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #f0f8ff;
    border-left: 4px solid var(--bs-primary);
}

.notification-item.read {
    opacity: 0.8;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(var(--bs-primary-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.notification-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.notification-title {
    flex-grow: 1;
    font-weight: 600;
    margin-bottom: 0;
}

.notification-time {
    font-size: 0.75rem;
    color: #6c757d;
    white-space: nowrap;
}

.notification-message {
    color: #6c757d;
    font-size: 0.875rem;
    line-height: 1.4;
}

.unread-indicator {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 8px;
    height: 8px;
    background: var(--bs-primary);
    border-radius: 50%;
}

.notification-actions {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.notification-item:hover .notification-actions {
    opacity: 1;
}

.type-item {
    padding: 0.5rem 0;
}

.preference-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.preference-item:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .notification-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .notification-time {
        margin-top: 0.25rem;
    }
    
    .notification-actions {
        opacity: 1;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Sort order switching
document.querySelectorAll('input[name="sortOrder"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const sort = this.id;
        const url = new URL(window.location);
        url.searchParams.set('sort', sort);
        window.location.href = url.toString();
    });
});

function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
            item.classList.remove('unread');
            item.classList.add('read');
            
            const indicator = item.querySelector('.unread-indicator');
            if (indicator) indicator.remove();
            
            updateUnreadCount(-1);
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function markAsUnread(notificationId) {
    fetch(`/notifications/${notificationId}/unread`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
            item.classList.remove('read');
            item.classList.add('unread');
            
            if (!item.querySelector('.unread-indicator')) {
                const indicator = document.createElement('div');
                indicator.className = 'unread-indicator';
                item.querySelector('.notification-content').appendChild(indicator);
            }
            
            updateUnreadCount(1);
        }
    })
    .catch(error => {
        console.error('Error marking notification as unread:', error);
    });
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
                item.remove();
            }
        })
        .catch(error => {
            console.error('Error deleting notification:', error);
        });
    }
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
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
        console.error('Error marking all as read:', error);
    });
}

function deleteAllRead() {
    if (confirm('Are you sure you want to delete all read notifications?')) {
        fetch('/notifications/delete-all-read', {
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
            console.error('Error deleting read notifications:', error);
        });
    }
}

function exportNotifications() {
    window.open('/notifications/export', '_blank');
}

function updatePreference(key, value) {
    fetch('/users/preferences/update', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            [key]: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Preference updated successfully', 'success');
        }
    })
    .catch(error => {
        console.error('Error updating preference:', error);
    });
}

function updateUnreadCount(change) {
    const badge = document.querySelector('.badge.bg-danger');
    if (badge) {
        const current = parseInt(badge.textContent);
        const newCount = current + change;
        
        if (newCount <= 0) {
            badge.remove();
        } else {
            badge.textContent = newCount;
        }
    }
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Auto-refresh notifications every 30 seconds
setInterval(() => {
    if (document.visibilityState === 'visible') {
        fetch('/notifications/check-new')
            .then(response => response.json())
            .then(data => {
                if (data.hasNew) {
                    // Show notification indicator or refresh
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-info alert-dismissible fade show position-fixed';
                    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
                    alertDiv.innerHTML = `
                        <i class="bx bx-bell me-2"></i>
                        You have new notifications!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alertDiv);
                }
            });
    }
}, 30000);
</script>
@endpush
