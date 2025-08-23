@extends('dashboard.layouts.app')

@section('title', __('notifications.index.title'))

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">
                    <i class="fas fa-bell me-2 text-primary"></i>
                    {{ __('notifications.index.heading') }}
                </h1>
                <p class="text-muted mb-0">{{ __('notifications.index.description') }}</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" id="markAllReadBtn">
                    <i class="fas fa-check-double me-1"></i>
                    {{ __('notifications.index.mark_all_read') }}
                </button>
                <button type="button" class="btn btn-outline-danger" id="deleteAllReadBtn">
                    <i class="fas fa-trash me-1"></i>
                    {{ __('notifications.index.delete_read') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-bell fs-4 text-primary"></i>
                    </div>
                </div>
                <h3 class="mb-1">{{ number_format($stats['total'] ?? 0) }}</h3>
                <p class="text-muted mb-0">{{ __('notifications.index.total_notifications') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-envelope fs-4 text-warning"></i>
                    </div>
                </div>
                <h3 class="mb-1">{{ number_format($stats['unread'] ?? 0) }}</h3>
                <p class="text-muted mb-0">{{ __('notifications.index.unread_notifications') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-check-circle fs-4 text-success"></i>
                    </div>
                </div>
                <h3 class="mb-1">{{ number_format($stats['read'] ?? 0) }}</h3>
                <p class="text-muted mb-0">{{ __('notifications.index.read_notifications') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Category Tabs -->
<div class="card mb-4">
    <div class="card-body">
        <div class="notification-categories">
            <div class="row g-2">
                @foreach($categories as $categoryKey => $category)
                    <div class="col-auto">
                        <button type="button"
                                class="btn category-tab {{ $currentCategory === $categoryKey ? 'btn-primary' : 'btn-outline-primary' }}"
                                data-category="{{ $categoryKey }}"
                                data-bs-toggle="tooltip"
                                title="{{ app()->getLocale() === 'vi' ? $category['description_vi'] : $category['description'] }}">
                            <i class="{{ $category['icon'] }} me-1"></i>
                            {{ app()->getLocale() === 'vi' ? $category['name_vi'] : $category['name'] }}
                            @if(isset($categoryCounts[$categoryKey]) && $categoryCounts[$categoryKey] > 0)
                                <span class="badge bg-{{ $category['color'] }} ms-1">{{ $categoryCounts[$categoryKey] }}</span>
                            @endif
                            @if(isset($unreadCategoryCounts[$categoryKey]) && $unreadCategoryCounts[$categoryKey] > 0)
                                <span class="badge bg-danger ms-1">{{ $unreadCategoryCounts[$categoryKey] }}</span>
                            @endif
                        </button>
                    </div>
                @endforeach

                <!-- Archive Tab -->
                <div class="col-auto">
                    <button type="button"
                            class="btn category-tab {{ request('archived') === '1' ? 'btn-warning' : 'btn-outline-warning' }}"
                            onclick="toggleArchiveView()"
                            data-bs-toggle="tooltip"
                            title="{{ __('notifications.index.archived_tooltip') }}">
                        <i class="fas fa-archive me-1"></i>
                        {{ __('notifications.index.archived') }}
                        @if(isset($archivedCount) && $archivedCount > 0)
                            <span class="badge bg-warning ms-1">{{ $archivedCount }}</span>
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Filters -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label">{{ __('notifications.index.filter_status') }}</label>
                <select class="form-select" id="statusFilter">
                    @if(isset($filters['status_options']))
                        @foreach($filters['status_options'] as $value => $label)
                            <option value="{{ $value === 'all' ? '' : $value }}" {{ $currentFilter === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('notifications.index.filter_priority') }}</label>
                <select class="form-select" id="priorityFilter">
                    <option value="">{{ __('notifications.index.all_priorities') }}</option>
                    @if(isset($filters['priority_options']))
                        @foreach($filters['priority_options'] as $value => $label)
                            <option value="{{ $value }}" {{ $currentPriority === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('notifications.index.filter_type') }}</label>
                <select class="form-select" id="typeFilter">
                    <option value="">{{ __('notifications.index.all_types') }}</option>
                    @if(isset($filters['types']))
                        @foreach($filters['types'] as $type)
                            <option value="{{ $type }}" {{ $currentType === $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('notifications.index.sender') }}</label>
                <select class="form-select" id="senderFilter">
                    <option value="">{{ __('notifications.index.all_senders') }}</option>
                    @if(isset($filters['senders']))
                        @foreach($filters['senders'] as $sender)
                            <option value="{{ $sender }}" {{ $currentSender === $sender ? 'selected' : '' }}>
                                {{ $sender }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('notifications.index.date_range') }}</label>
                <select class="form-select" id="dateRangeFilter">
                    <option value="">{{ __('notifications.index.all_time') }}</option>
                    @if(isset($filters['date_ranges']))
                        @foreach($filters['date_ranges'] as $value => $label)
                            <option value="{{ $value }}" {{ $currentDateRange === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="row g-3 mt-2">
            <div class="col-md-8">
                <label class="form-label">{{ __('notifications.index.search') }}</label>
                <input type="text" class="form-control" id="searchInput"
                       placeholder="{{ __('notifications.index.search_placeholder') }}"
                       value="{{ $currentSearch }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-secondary w-100" id="clearFilters">
                    <i class="fas fa-times me-1"></i>
                    {{ __('notifications.index.clear_filters') }}
                </button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100" id="applyFilters">
                    <i class="fas fa-filter me-1"></i>
                    {{ __('notifications.index.apply_filters') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Notifications List -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('notifications.index.notifications_list') }}</h5>

            <!-- Bulk Actions Toolbar -->
            <div class="bulk-actions-toolbar d-none" id="bulkActionsToolbar">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small" id="selectedCount">0 selected</span>
                    <div class="btn-group" role="group">
                        @if(isset($isArchiveView) && $isArchiveView)
                            <!-- Archive View Actions -->
                            <button type="button" class="btn btn-sm btn-outline-success" id="bulkRestore">
                                <i class="fas fa-undo me-1"></i>
                                {{ __('notifications.archive.bulk_restore') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="bulkDelete">
                                <i class="fas fa-trash me-1"></i>
                                {{ __('notifications.index.bulk_delete') }}
                            </button>
                        @else
                            <!-- Normal View Actions -->
                            <button type="button" class="btn btn-sm btn-outline-primary" id="bulkMarkRead">
                                <i class="fas fa-eye me-1"></i>
                                {{ __('notifications.index.bulk_mark_read') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="bulkArchive">
                                <i class="fas fa-archive me-1"></i>
                                {{ __('notifications.index.bulk_archive') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="bulkDelete">
                                <i class="fas fa-trash me-1"></i>
                                {{ __('notifications.index.bulk_delete') }}
                            </button>
                        @endif
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSelection">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Select All Controls -->
        <div class="mt-2 border-top pt-2" id="selectAllControls" style="display: none;">
            <div class="d-flex align-items-center gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAllNotifications">
                    <label class="form-check-label" for="selectAllNotifications">
                        {{ __('notifications.index.select_all') }}
                    </label>
                </div>
                <div class="text-muted small">
                    <span id="totalNotifications">{{ $notifications->total() ?? 0 }}</span> {{ __('notifications.index.total_notifications') }}
                </div>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        @if($notifications && $notifications->count() > 0)
            <div class="notification-list">
                @foreach($notifications as $notification)
                    <div class="notification-item border-bottom p-3 {{ !$notification->is_read ? 'bg-light' : '' }}"
                         data-notification-id="{{ $notification->id }}">
                        <div class="d-flex">
                            <!-- Checkbox -->
                            <div class="notification-checkbox me-3 d-flex align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input notification-select"
                                           type="checkbox"
                                           value="{{ $notification->id }}"
                                           id="notification-{{ $notification->id }}">
                                </div>
                            </div>

                            <!-- Icon -->
                            <div class="notification-icon me-3">
                                @php
                                    $typeIcon = \App\Services\NotificationCategoryService::getTypeIcon($notification->type);
                                    $category = \App\Services\NotificationCategoryService::getCategory($notification->type);
                                    $categoryData = $categories[$category] ?? $categories['system'];
                                    $priorityColor = \App\Services\NotificationCategoryService::getPriorityColor($notification->priority ?? 'normal');
                                @endphp
                                <div class="bg-{{ $categoryData['color'] }} bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="{{ $typeIcon }} text-{{ $categoryData['color'] }}"></i>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="notification-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="notification-title-section">
                                        <h6 class="notification-title mb-1 {{ !$notification->is_read ? 'fw-bold' : '' }}">
                                            {{ $notification->getTypeLabel() }}
                                        </h6>
                                        <div class="notification-badges">
                                            <span class="badge bg-{{ $categoryData['color'] }} bg-opacity-20 text-{{ $categoryData['color'] }}">
                                                {{ app()->getLocale() === 'vi' ? $categoryData['name_vi'] : $categoryData['name'] }}
                                            </span>
                                            @if($notification->priority && $notification->priority !== 'normal')
                                                <span class="badge bg-{{ $priorityColor }}">
                                                    {{ ucfirst($notification->priority) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="notification-actions d-flex gap-1">
                                        @if(!$notification->is_read)
                                            <span class="badge bg-primary">{{ __('notifications.index.new') }}</span>
                                        @endif

                                        @if(isset($isArchiveView) && $isArchiveView)
                                            <!-- Archive View Quick Actions -->
                                            <div class="quick-actions">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-success restore-btn"
                                                        data-notification-id="{{ $notification->id }}"
                                                        title="{{ __('notifications.archive.restore_tooltip') }}">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger delete-btn"
                                                        data-notification-id="{{ $notification->id }}"
                                                        title="{{ __('notifications.index.delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        @else
                                            <!-- Normal View Quick Actions -->
                                            <div class="quick-actions">
                                                @php
                                                    $replyableTypes = ['message_received', 'seller_message', 'comment_mention', 'thread_replied', 'review_received'];
                                                    $canReply = in_array($notification->type, $replyableTypes);
                                                @endphp

                                                @if($canReply)
                                                    <!-- Reply Action -->
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-success reply-btn"
                                                            data-notification-id="{{ $notification->id }}"
                                                            data-notification-type="{{ $notification->type }}"
                                                            title="{{ __('notifications.actions.reply') }}">
                                                        <i class="fas fa-reply"></i>
                                                    </button>
                                                @endif

                                                @if($notification->url)
                                                    <!-- View Details Action -->
                                                    <a href="{{ $notification->url }}"
                                                       class="btn btn-sm btn-outline-primary view-details-btn"
                                                       title="{{ __('notifications.actions.view_details') }}">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                @endif

                                                <!-- Mark Read/Unread Action -->
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-info mark-read-btn"
                                                        data-notification-id="{{ $notification->id }}"
                                                        title="{{ $notification->is_read ? __('notifications.actions.mark_unread') : __('notifications.actions.mark_read') }}">
                                                    <i class="fas {{ $notification->is_read ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                </button>

                                                <!-- Archive Action -->
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-secondary archive-btn"
                                                        data-notification-id="{{ $notification->id }}"
                                                        title="{{ __('notifications.actions.archive') }}">
                                                    <i class="fas fa-archive"></i>
                                                </button>

                                                <!-- Delete Action -->
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger delete-btn"
                                                        data-notification-id="{{ $notification->id }}"
                                                        title="{{ __('notifications.actions.delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <p class="notification-message mb-2 text-muted">{{ $notification->localized_message }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="card-footer">
                    {{ $notifications->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                @if(isset($isArchiveView) && $isArchiveView)
                    <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('notifications.archive.empty_message') }}</h5>
                    <p class="text-muted">{{ __('notifications.archive.auto_archive_info') }}</p>
                @else
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('notifications.index.no_notifications') }}</h5>
                    <p class="text-muted">{{ __('notifications.index.no_notifications_desc') }}</p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.notification-item {
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f8f9fa !important;
}

/* Quick Actions Styling */
.quick-actions {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
}

.quick-actions .btn {
    min-width: 32px;
    height: 32px;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
}

.quick-actions .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.quick-actions .reply-btn:hover {
    background-color: #198754;
    border-color: #198754;
    color: white;
}

.quick-actions .view-details-btn:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.quick-actions .mark-read-btn:hover {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: white;
}

.quick-actions .archive-btn:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.quick-actions .delete-btn:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.notification-icon {
    flex-shrink: 0;
}

.notification-content {
    min-width: 0;
}

.notification-title {
    color: #333;
}

.notification-message {
    line-height: 1.5;
}

.notification-actions .btn {
    padding: 0.25rem 0.5rem;
}

/* Category tabs styling */
.category-tab {
    border-radius: 20px;
    transition: all 0.2s ease;
}

.category-tab:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.category-tab.btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
}

.notification-badges {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
}

.notification-badges .badge {
    font-size: 0.7rem;
}

.notification-title-section {
    flex-grow: 1;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark all as read
    document.getElementById('markAllReadBtn')?.addEventListener('click', function() {
        window.showConfirm(
            '{{ __("notifications.index.confirm_mark_all_read") }}',
            'Tất cả thông báo sẽ được đánh dấu là đã đọc.',
            function() {
                // Show loading
                window.showLoading('Đang xử lý...', 'Vui lòng đợi trong giây lát');

                fetch('/ajax/notifications/mark-all-read', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    window.closeAlert();
                    if (data.success) {
                        window.showSuccess('Thành công!', 'Đã đánh dấu tất cả thông báo là đã đọc.').then(() => {
                            location.reload();
                        });
                    } else {
                        window.showError('Lỗi', data.message || '{{ __("notifications.index.error_occurred") }}');
                    }
                })
                .catch(error => {
                    window.closeAlert();
                    console.error('Error:', error);
                    window.handleAjaxError({
                        status: error.message.includes('500') ? 500 : 0,
                        responseText: error.message
                    }, 'Lỗi', '{{ __("notifications.index.error_occurred") }}');
                });
            }
        );
    });

    // Individual notification actions
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;
            const isRead = this.querySelector('i').classList.contains('fa-eye-slash');
            const action = isRead ? 'unread' : 'read';
            const actionText = isRead ? 'chưa đọc' : 'đã đọc';

            fetch(`/ajax/notifications/${notificationId}/${action}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.showToast(`Đã đánh dấu ${actionText}`, 'success');
                    location.reload();
                } else {
                    window.showError('Lỗi', data.message || 'Không thể cập nhật trạng thái thông báo');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.handleAjaxError({
                    status: error.message.includes('500') ? 500 : 0,
                    responseText: error.message
                }, 'Lỗi', 'Không thể cập nhật trạng thái thông báo');
            });
        });
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;

            window.showDeleteConfirm('thông báo này').then((result) => {
                if (result.isConfirmed) {
                    window.showLoading('Đang xóa...', 'Vui lòng đợi trong giây lát');

                    fetch(`/ajax/notifications/${notificationId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        window.closeAlert();
                        if (data.success) {
                            window.showSuccess('Đã xóa!', 'Thông báo đã được xóa thành công.').then(() => {
                                location.reload();
                            });
                        } else {
                            window.showError('Lỗi', data.message || 'Không thể xóa thông báo');
                        }
                    })
                    .catch(error => {
                        window.closeAlert();
                        console.error('Error:', error);
                        window.handleAjaxError({
                            status: error.message.includes('500') ? 500 : 0,
                            responseText: error.message
                        }, 'Lỗi', 'Không thể xóa thông báo');
                    });
                }
            });
        });
    });

    // Archive button
    document.querySelectorAll('.archive-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;
            performBulkAction('archive', [notificationId]);
        });
    });

    // Restore button
    document.querySelectorAll('.restore-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;

            window.showConfirm(
                '{{ __("notifications.archive.confirm_restore") }}',
                'Thông báo sẽ được khôi phục về danh sách chính.',
                function() {
                    performBulkAction('restore', [notificationId]);
                }
            );
        });
    });

    // Category filtering
    document.querySelectorAll('.category-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const category = this.dataset.category;
            updateFilters({ category: category });
        });
    });

    // Filter controls
    document.getElementById('statusFilter')?.addEventListener('change', function() {
        updateFilters({ filter: this.value });
    });

    document.getElementById('priorityFilter')?.addEventListener('change', function() {
        updateFilters({ priority: this.value });
    });

    document.getElementById('typeFilter')?.addEventListener('change', function() {
        updateFilters({ type: this.value });
    });

    document.getElementById('senderFilter')?.addEventListener('change', function() {
        updateFilters({ sender: this.value });
    });

    document.getElementById('dateRangeFilter')?.addEventListener('change', function() {
        updateFilters({ date_range: this.value });
    });

    // Search with debounce
    let searchTimeout;
    document.getElementById('searchInput')?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            updateFilters({ search: this.value });
        }, 500);
    });

    // Apply filters button
    document.getElementById('applyFilters')?.addEventListener('click', function() {
        const filters = {
            filter: document.getElementById('statusFilter')?.value || '',
            category: '{{ $currentCategory }}',
            priority: document.getElementById('priorityFilter')?.value || '',
            type: document.getElementById('typeFilter')?.value || '',
            sender: document.getElementById('senderFilter')?.value || '',
            date_range: document.getElementById('dateRangeFilter')?.value || '',
            search: document.getElementById('searchInput')?.value || ''
        };

        // Remove empty filters
        Object.keys(filters).forEach(key => {
            if (!filters[key]) delete filters[key];
        });

        updateFilters(filters);
    });

    // Clear filters
    document.getElementById('clearFilters')?.addEventListener('click', function() {
        window.location.href = '{{ route("dashboard.notifications.index") }}';
    });

    // Update filters function
    function updateFilters(newFilters) {
        const url = new URL(window.location);

        // Update URL parameters
        Object.keys(newFilters).forEach(key => {
            if (newFilters[key]) {
                url.searchParams.set(key, newFilters[key]);
            } else {
                url.searchParams.delete(key);
            }
        });

        // Navigate to new URL
        window.location.href = url.toString();
    }

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // ===== BULK OPERATIONS =====
    let selectedNotifications = new Set();
    const bulkToolbar = document.getElementById('bulkActionsToolbar');
    const selectAllControls = document.getElementById('selectAllControls');
    const selectedCountElement = document.getElementById('selectedCount');
    const selectAllCheckbox = document.getElementById('selectAllNotifications');

    // Show/hide bulk controls based on notifications presence
    const notificationItems = document.querySelectorAll('.notification-item');
    if (notificationItems.length > 0) {
        selectAllControls.style.display = 'block';
    }

    // Individual notification checkbox change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('notification-select')) {
            const notificationId = e.target.value;

            if (e.target.checked) {
                selectedNotifications.add(notificationId);
            } else {
                selectedNotifications.delete(notificationId);
            }

            updateBulkControls();
        }
    });

    // Select all checkbox
    selectAllCheckbox?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.notification-select');

        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            const notificationId = checkbox.value;

            if (this.checked) {
                selectedNotifications.add(notificationId);
            } else {
                selectedNotifications.delete(notificationId);
            }
        });

        updateBulkControls();
    });

    // Update bulk controls visibility and count
    function updateBulkControls() {
        const count = selectedNotifications.size;

        if (count > 0) {
            bulkToolbar.classList.remove('d-none');
            selectedCountElement.textContent = `${count} selected`;
        } else {
            bulkToolbar.classList.add('d-none');
        }

        // Update select all checkbox state
        const totalCheckboxes = document.querySelectorAll('.notification-select').length;
        if (count === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (count === totalCheckboxes) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Clear selection
    document.getElementById('clearSelection')?.addEventListener('click', function() {
        selectedNotifications.clear();
        document.querySelectorAll('.notification-select').forEach(cb => cb.checked = false);
        updateBulkControls();
    });

    // Bulk mark as read
    document.getElementById('bulkMarkRead')?.addEventListener('click', function() {
        if (selectedNotifications.size === 0) {
            window.showWarning('Chưa chọn thông báo', '{{ __('notifications.index.no_notifications_selected') }}');
            return;
        }

        window.showConfirm(
            'Đánh dấu đã đọc',
            `Đánh dấu ${selectedNotifications.size} thông báo đã chọn là đã đọc?`,
            function() {
                performBulkAction('mark-read', Array.from(selectedNotifications));
            }
        );
    });

    // Bulk archive
    document.getElementById('bulkArchive')?.addEventListener('click', function() {
        if (selectedNotifications.size === 0) {
            window.showWarning('Chưa chọn thông báo', '{{ __('notifications.index.no_notifications_selected') }}');
            return;
        }

        window.showConfirm(
            'Lưu trữ thông báo',
            `Lưu trữ ${selectedNotifications.size} thông báo đã chọn?`,
            function() {
                performBulkAction('archive', Array.from(selectedNotifications));
            }
        );
    });

    // Bulk restore (for archive view)
    document.getElementById('bulkRestore')?.addEventListener('click', function() {
        if (selectedNotifications.size === 0) {
            window.showWarning('Chưa chọn thông báo', '{{ __('notifications.index.no_notifications_selected') }}');
            return;
        }

        window.showConfirm(
            'Khôi phục thông báo',
            `Khôi phục ${selectedNotifications.size} thông báo đã chọn?`,
            function() {
                performBulkAction('restore', Array.from(selectedNotifications));
            }
        );
    });

    // Bulk delete
    document.getElementById('bulkDelete')?.addEventListener('click', function() {
        if (selectedNotifications.size === 0) {
            window.showWarning('Chưa chọn thông báo', '{{ __('notifications.index.no_notifications_selected') }}');
            return;
        }

        window.showDeleteConfirm(`${selectedNotifications.size} thông báo đã chọn`).then((result) => {
            if (result.isConfirmed) {
                performBulkAction('delete', Array.from(selectedNotifications));
            }
        });
    });

    // Perform bulk action
    function performBulkAction(action, notificationIds) {
        window.showLoading('Đang xử lý...', 'Vui lòng đợi trong giây lát');

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('action', action);
        formData.append('notification_ids', JSON.stringify(notificationIds));

        fetch('{{ route("dashboard.notifications.bulk") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            window.closeAlert();
            if (data.success) {
                const actionText = {
                    'mark-read': 'đánh dấu đã đọc',
                    'archive': 'lưu trữ',
                    'restore': 'khôi phục',
                    'delete': 'xóa'
                };

                const message = `Đã ${actionText[action] || 'xử lý'} ${notificationIds.length} thông báo thành công!`;

                window.showSuccess('Thành công!', message).then(() => {
                    window.location.reload();
                });
            } else {
                window.showError('Lỗi', data.message || 'Đã xảy ra lỗi không mong muốn');
            }
        })
        .catch(error => {
            window.closeAlert();
            console.error('Error:', error);
            window.handleAjaxError({
                status: error.message.includes('500') ? 500 : 0,
                responseText: error.message
            }, 'Lỗi', 'Đã xảy ra lỗi khi xử lý yêu cầu');
        });
    }

    // Toggle archive view
    window.toggleArchiveView = function() {
        const url = new URL(window.location);
        const isArchived = url.searchParams.get('archived') === '1';

        if (isArchived) {
            // Switch back to normal view
            url.searchParams.delete('archived');
        } else {
            // Switch to archive view
            url.searchParams.set('archived', '1');
            // Clear other filters when viewing archive
            url.searchParams.delete('type');
            url.searchParams.delete('status');
        }

        window.location.href = url.toString();
    };

    // Individual restore function for archived notifications
    window.restoreNotification = function(notificationId) {
        window.showConfirm(
            '{{ __('notifications.archive.confirm_restore') }}',
            'Thông báo sẽ được khôi phục về danh sách chính.',
            function() {
                performBulkAction('restore', [notificationId]);
            }
        );
    };

    // ===== QUICK ACTIONS =====

    // Reply button handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.reply-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.reply-btn');
            const notificationId = btn.dataset.notificationId;
            const notificationType = btn.dataset.notificationType;

            showReplyModal(notificationId, notificationType);
        }
    });

    // Show reply confirmation
    function showReplyModal(notificationId, notificationType) {
        const typeMessages = {
            'message_received': 'Chuyển đến trang tin nhắn để trả lời?',
            'seller_message': 'Chuyển đến trang tin nhắn để trả lời seller?',
            'comment_mention': 'Chuyển đến thread để trả lời bình luận?',
            'thread_replied': 'Chuyển đến thread để trả lời?',
            'review_received': 'Chuyển đến trang đơn hàng để xem đánh giá?'
        };

        const message = typeMessages[notificationType] || 'Chuyển đến trang phù hợp để trả lời?';

        Swal.fire({
            title: '{{ __("notifications.reply.modal_title") }}',
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '{{ __("ui.yes") }}',
            cancelButtonText: '{{ __("ui.cancel") }}',
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                sendReply(notificationId);
            }
        });
    }

    // Send reply (redirect to appropriate page)
    function sendReply(notificationId) {
        window.showLoading('{{ __("ui.processing") }}...', '{{ __("ui.please_wait") }}');

        fetch(`/ajax/notifications/${notificationId}/reply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            window.closeAlert();
            if (data.success) {
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    window.location.reload();
                }
            } else {
                window.showError('{{ __("ui.error") }}', data.message || '{{ __("notifications.reply.error") }}');
            }
        })
        .catch(error => {
            window.closeAlert();
            console.error('Error:', error);
            window.handleAjaxError({
                status: error.message.includes('500') ? 500 : 0,
                responseText: error.message
            }, '{{ __("ui.error") }}', '{{ __("notifications.reply.error") }}');
        });
    }
});
</script>
@endpush
