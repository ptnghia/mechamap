@extends('layouts.user-dashboard')

@section('title', __('nav.user.bookmarks'))

@php
    $pageTitle = __('nav.user.bookmarks');
    $pageDescription = __('messages.bookmarks_desc');
    $pageActions = '<button type="button" class="btn btn-outline-primary" onclick="createFolder()">
        <i class="fas fa-folder-plus me-2"></i>' . __('messages.create_folder') . '
    </button>
    <button type="button" class="btn btn-outline-danger" onclick="bulkDelete()">
        <i class="fas fa-trash me-2"></i>' . __('messages.delete_selected') . '
    </button>';
    $breadcrumbs = [
        ['title' => __('nav.user.bookmarks'), 'url' => '#']
    ];
@endphp

@section('dashboard-content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stats-card primary">
            <div class="stats-icon">
                <i class="fas fa-bookmark"></i>
            </div>
            <div class="stats-value" data-stat="total_bookmarks">{{ $stats['total_bookmarks'] ?? 0 }}</div>
            <div class="stats-label">{{ __('messages.total_bookmarks') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card success">
            <div class="stats-icon">
                <i class="fas fa-folder"></i>
            </div>
            <div class="stats-value" data-stat="total_folders">{{ $stats['total_folders'] ?? 0 }}</div>
            <div class="stats-label">{{ __('messages.total_folders') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card info">
            <div class="stats-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-value" data-stat="recent_bookmarks">{{ $stats['recent_bookmarks'] ?? 0 }}</div>
            <div class="stats-label">{{ __('messages.this_week') }}</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="dashboard-filters">
    <form method="GET" action="{{ route('user.bookmarks') }}">
        <div class="row">
            <div class="col-md-3">
                <div class="filter-group">
                    <label class="filter-label">Thư mục</label>
                    <select name="folder" class="form-select">
                        <option value="">{{ __('messages.all_folders') }}</option>
                        @foreach($folders ?? [] as $folder)
                            <option value="{{ $folder->id }}" {{ request('folder') == $folder->id ? 'selected' : '' }}>
                                {{ $folder->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group">
                    <label class="filter-label">{{ __('messages.content_type') }}</label>
                    <select name="type" class="form-select">
                        <option value="">{{ __('messages.all_types') }}</option>
                        <option value="thread" {{ request('type') === 'thread' ? 'selected' : '' }}>
                            {{ __('messages.threads') }}
                        </option>
                        <option value="comment" {{ request('type') === 'comment' ? 'selected' : '' }}>
                            Bình luận
                        </option>
                        <option value="showcase" {{ request('type') === 'showcase' ? 'selected' : '' }}>
                            Dự án
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group">
                    <label class="filter-label">{{ __('messages.sort_by') }}</label>
                    <select name="sort" class="form-select">
                        <option value="created_at" {{ request('sort', 'created_at') === 'created_at' ? 'selected' : '' }}>
                            {{ __('messages.newest_first') }}
                        </option>
                        <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>
                            {{ __('messages.title') }}
                        </option>
                        <option value="folder" {{ request('sort') === 'folder' ? 'selected' : '' }}>
                            {{ __('messages.folder') }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group">
                    <label class="filter-label">{{ __('messages.search') }}</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="{{ __('messages.search_bookmarks') }}"
                           value="{{ request('search') }}">
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Bookmarks Grid/List -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
        <label class="form-check-label" for="selectAll">
            {{ __('messages.select_all') }}
        </label>
    </div>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-outline-secondary {{ request('view', 'grid') === 'grid' ? 'active' : '' }}"
                onclick="changeView('grid')">
            <i class="fas fa-th"></i>
        </button>
        <button type="button" class="btn btn-outline-secondary {{ request('view') === 'list' ? 'active' : '' }}"
                onclick="changeView('list')">
            <i class="fas fa-list"></i>
        </button>
    </div>
</div>

@if($bookmarks && $bookmarks->count() > 0)
    @if(request('view') === 'list')
        <!-- List View -->
        <div class="dashboard-table">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="selectAllTable" onchange="toggleSelectAll()">
                        </th>
                        <th>{{ __('messages.content') }}</th>
                        <th>{{ __('messages.type') }}</th>
                        <th>{{ __('messages.folder') }}</th>
                        <th>{{ __('messages.bookmarked_at') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookmarks as $bookmark)
                        <tr>
                            <td>
                                <input type="checkbox" class="bookmark-checkbox" value="{{ $bookmark->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($bookmark->thread && $bookmark->thread->featured_image)
                                        <img src="{{ $bookmark->thread->featured_image }}" alt=""
                                             class="me-2 rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                    @endif
                                    <div>
                                        <a href="{{ $bookmark->getContentUrl() }}" class="fw-bold text-decoration-none">
                                            {{ $bookmark->getContentTitle() }}
                                        </a>
                                        <div class="text-muted small">
                                            {{ Str::limit($bookmark->getContentExcerpt(), 100) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $bookmark->getTypeBadgeClass() }}">
                                    {{ $bookmark->getTypeDisplayName() }}
                                </span>
                            </td>
                            <td>
                                @if($bookmark->folder)
                                    <span class="badge bg-secondary">{{ $bookmark->folder->name }}</span>
                                @else
                                    <span class="text-muted">{{ __('messages.no_folder') }}</span>
                                @endif
                            </td>
                            <td>{{ $bookmark->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ $bookmark->getContentUrl() }}" class="btn btn-outline-primary"
                                       data-bs-toggle="tooltip" title="{{ __('messages.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary"
                                            onclick="editBookmark({{ $bookmark->id }})" data-bs-toggle="tooltip"
                                            title="{{ __('messages.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger"
                                            onclick="deleteBookmark({{ $bookmark->id }})" data-bs-toggle="tooltip"
                                            title="{{ __('messages.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <!-- Grid View -->
        <div class="row">
            @foreach($bookmarks as $bookmark)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bookmark-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <input type="checkbox" class="bookmark-checkbox" value="{{ $bookmark->id }}">
                            <span class="badge bg-{{ $bookmark->getTypeBadgeClass() }}">
                                {{ $bookmark->getTypeDisplayName() }}
                            </span>
                        </div>
                        @if($bookmark->thread && $bookmark->thread->featured_image)
                            <img src="{{ $bookmark->thread->featured_image }}" class="card-img-top"
                                 style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="{{ $bookmark->getContentUrl() }}" class="text-decoration-none">
                                    {{ Str::limit($bookmark->getContentTitle(), 50) }}
                                </a>
                            </h6>
                            <p class="card-text text-muted small">
                                {{ Str::limit($bookmark->getContentExcerpt(), 100) }}
                            </p>
                            @if($bookmark->folder)
                                <div class="mb-2">
                                    <span class="badge bg-secondary">{{ $bookmark->folder->name }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">{{ $bookmark->created_at->diffForHumans() }}</small>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ $bookmark->getContentUrl() }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                            onclick="editBookmark({{ $bookmark->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="deleteBookmark({{ $bookmark->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Pagination -->
    @if($bookmarks->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $bookmarks->links() }}
        </div>
    @endif
@else
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="fas fa-bookmark"></i>
        </div>
        <div class="empty-state-title">{{ __('messages.no_bookmarks_yet') }}</div>
        <div class="empty-state-description">{{ __('messages.no_bookmarks_desc') }}</div>
        <a href="{{ route('threads.index') }}" class="btn btn-primary">
            <i class="fas fa-search me-2"></i>{{ __('messages.browse_content') }}
        </a>
    </div>
@endif

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.create_folder') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createFolderForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="folderName" class="form-label">{{ __('messages.folder_name') }}</label>
                        <input type="text" class="form-control" id="folderName" required>
                    </div>
                    <div class="mb-3">
                        <label for="folderDescription" class="form-label">{{ __('messages.description') }}</label>
                        <textarea class="form-control" id="folderDescription" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.create') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bookmark-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.bookmark-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.bookmark-checkbox {
    cursor: pointer;
}
</style>

<script>
function changeView(view) {
    const url = new URL(window.location);
    url.searchParams.set('view', view);
    window.location.href = url.toString();
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll') || document.getElementById('selectAllTable');
    const checkboxes = document.querySelectorAll('.bookmark-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function createFolder() {
    const modal = new bootstrap.Modal(document.getElementById('createFolderModal'));
    modal.show();
}

function editBookmark(bookmarkId) {
    // Implementation for editing bookmark
    console.log('Edit bookmark:', bookmarkId);
}

function deleteBookmark(bookmarkId) {
    if (confirm('{{ __("messages.confirm_delete_bookmark") }}')) {
        fetch(`/user/bookmarks/${bookmarkId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || '{{ __("messages.error_occurred") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("messages.error_occurred") }}');
        });
    }
}

function bulkDelete() {
    const selected = document.querySelectorAll('.bookmark-checkbox:checked');
    if (selected.length === 0) {
        alert('{{ __("messages.select_bookmarks_first") }}');
        return;
    }

    if (confirm('{{ __("messages.confirm_delete_selected_bookmarks") }}')) {
        const ids = Array.from(selected).map(cb => cb.value);

        fetch('/user/bookmarks/bulk-delete', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || '{{ __("messages.error_occurred") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("messages.error_occurred") }}');
        });
    }
}

// Create folder form submission
document.getElementById('createFolderForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = {
        name: document.getElementById('folderName').value,
        description: document.getElementById('folderDescription').value
    };

    fetch('/user/bookmarks/folders', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || '{{ __("messages.error_occurred") }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("messages.error_occurred") }}');
    });
});
</script>
@endsection
