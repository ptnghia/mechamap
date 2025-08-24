@extends('dashboard.layouts.app')

@section('title', __('notifications.archive.title'))

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">
                    <i class="fas fa-archive me-2 text-warning"></i>
                    {{ __('notifications.archive.heading') }}
                </h1>
                <p class="text-muted mb-0">{{ __('notifications.archive.description') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>
                    {{ __('notifications.archive.back_to_notifications') }}
                </a>
                <button type="button" class="btn btn-outline-success" id="restoreAllBtn">
                    <i class="fas fa-undo me-1"></i>
                    {{ __('notifications.archive.restore_all') }}
                </button>
                <button type="button" class="btn btn-outline-danger" id="deleteAllArchivedBtn">
                    <i class="fas fa-trash me-1"></i>
                    {{ __('notifications.archive.delete_all') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Archive Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-archive fs-4 text-warning"></i>
                    </div>
                </div>
                <h3 class="mb-1">{{ number_format($stats['total_archived'] ?? 0) }}</h3>
                <p class="text-muted mb-0">{{ __('notifications.archive.total_archived') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-calendar fs-4 text-info"></i>
                    </div>
                </div>
                <h3 class="mb-1">{{ number_format($stats['this_month'] ?? 0) }}</h3>
                <p class="text-muted mb-0">{{ __('notifications.archive.this_month') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-secondary bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-clock fs-4 text-secondary"></i>
                    </div>
                </div>
                <h3 class="mb-1">{{ number_format($stats['older_than_30_days'] ?? 0) }}</h3>
                <p class="text-muted mb-0">{{ __('notifications.archive.older_than_30_days') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-database fs-4 text-success"></i>
                    </div>
                </div>
                <h3 class="mb-1">{{ $stats['storage_saved'] ?? '0 MB' }}</h3>
                <p class="text-muted mb-0">{{ __('notifications.archive.storage_saved') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Archive Filters -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">{{ __('notifications.archive.filter_category') }}</label>
                <select class="form-select" id="categoryFilter">
                    <option value="">{{ __('notifications.archive.all_categories') }}</option>
                    @foreach($categories as $categoryKey => $category)
                        <option value="{{ $categoryKey }}" {{ $currentCategory === $categoryKey ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'vi' ? $category['name_vi'] : $category['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('notifications.archive.filter_date_archived') }}</label>
                <select class="form-select" id="dateArchivedFilter">
                    <option value="">{{ __('notifications.archive.all_time') }}</option>
                    <option value="today" {{ $currentDateArchived === 'today' ? 'selected' : '' }}>{{ __('notifications.archive.today') }}</option>
                    <option value="week" {{ $currentDateArchived === 'week' ? 'selected' : '' }}>{{ __('notifications.archive.this_week') }}</option>
                    <option value="month" {{ $currentDateArchived === 'month' ? 'selected' : '' }}>{{ __('notifications.archive.this_month') }}</option>
                    <option value="3months" {{ $currentDateArchived === '3months' ? 'selected' : '' }}>{{ __('notifications.archive.last_3_months') }}</option>
                    <option value="6months" {{ $currentDateArchived === '6months' ? 'selected' : '' }}>{{ __('notifications.archive.last_6_months') }}</option>
                    <option value="year" {{ $currentDateArchived === 'year' ? 'selected' : '' }}>{{ __('notifications.archive.this_year') }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('notifications.archive.search') }}</label>
                <input type="text" class="form-control" id="searchInput"
                       placeholder="{{ __('notifications.archive.search_placeholder') }}"
                       value="{{ $currentSearch }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100" id="applyFilters">
                    <i class="fas fa-search me-1"></i>
                    {{ __('notifications.archive.apply_filters') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="form-check me-3">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll">
                        {{ __('notifications.archive.select_all') }}
                    </label>
                </div>
                <span class="text-muted" id="selectedCount">0 {{ __('notifications.archive.selected') }}</span>
            </div>
            <div class="btn-group" id="bulkActions" style="display: none;">
                <button type="button" class="btn btn-outline-success" id="bulkRestoreBtn">
                    <i class="fas fa-undo me-1"></i>
                    {{ __('notifications.archive.restore_selected') }}
                </button>
                <button type="button" class="btn btn-outline-danger" id="bulkDeleteBtn">
                    <i class="fas fa-trash me-1"></i>
                    {{ __('notifications.archive.delete_selected') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Archived Notifications List -->
<div class="card">
    <div class="card-body">
        @if($notifications->count() > 0)
            <div class="notification-list">
                @foreach($notifications as $notification)
                    <div class="notification-item border-bottom py-3" data-notification-id="{{ $notification->id }}">
                        <div class="d-flex align-items-start">
                            <div class="form-check me-3">
                                <input class="form-check-input notification-checkbox" 
                                       type="checkbox" 
                                       value="{{ $notification->id }}">
                            </div>
                            
                            <div class="notification-icon me-3">
                                <div class="bg-{{ $notification->color }} bg-opacity-10 rounded-circle p-2">
                                    <i class="fas fa-{{ $notification->icon }} text-{{ $notification->color }}"></i>
                                </div>
                            </div>
                            
                            <div class="notification-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 text-muted">{{ $notification->title }}</h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-{{ $notification->priority_color }}">
                                            {{ ucfirst($notification->priority) }}
                                        </span>
                                        <small class="text-muted">
                                            {{ __('notifications.archive.archived') }}: {{ $notification->archived_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                                <p class="mb-2 text-muted">{{ $notification->message }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $notification->created_at->format('d/m/Y H:i') }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-tag me-1"></i>
                                            {{ $notification->category_name }}
                                        </small>
                                    </div>
                                    <div class="notification-actions">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-success restore-btn"
                                                data-notification-id="{{ $notification->id }}"
                                                title="{{ __('notifications.archive.restore') }}">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger delete-btn"
                                                data-notification-id="{{ $notification->id }}"
                                                title="{{ __('notifications.archive.delete_permanently') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-archive fa-3x text-muted"></i>
                </div>
                <h5 class="text-muted">{{ __('notifications.archive.no_archived_notifications') }}</h5>
                <p class="text-muted">{{ __('notifications.archive.no_archived_description') }}</p>
                <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-primary">
                    <i class="fas fa-bell me-1"></i>
                    {{ __('notifications.archive.view_active_notifications') }}
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Auto-archive Info -->
<div class="card mt-4">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="fas fa-info-circle text-info"></i>
            </div>
            <div>
                <h6 class="mb-1">{{ __('notifications.archive.auto_archive_title') }}</h6>
                <p class="mb-0 text-muted">{{ __('notifications.archive.auto_archive_info') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/notification-archive.js') }}?v={{ filemtime(public_path('js/notification-archive.js')) }}"></script>
@endpush
