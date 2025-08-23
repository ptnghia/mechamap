@extends('dashboard.layouts.app')

@section('title', __('bookmarks.index.title'))

@section('content')
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">
                    <i class="fas fa-bookmark me-2 text-primary"></i>
                    {{ __('bookmarks.index.heading') }}
                </h1>
                <p class="text-muted mb-0">{{ __('bookmarks.index.description') }}</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                    <i class="fas fa-folder-plus me-2"></i>
                    {{ __('bookmarks.index.create_folder') }}
                </button>
                <button type="button" class="btn btn-outline-danger" id="bulkDeleteBtn" style="display: none;">
                    <i class="fas fa-trash me-2"></i>
                    {{ __('bookmarks.index.delete_selected') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card primary">
            <div class="stats-icon">
                <i class="fas fa-bookmark"></i>
            </div>
            <div class="stats-value">{{ $stats['total_bookmarks'] ?? 0 }}</div>
            <div class="stats-label">{{ __('bookmarks.index.total_bookmarks') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-icon">
                <i class="fas fa-folder"></i>
            </div>
            <div class="stats-value">{{ $stats['total_folders'] ?? 0 }}</div>
            <div class="stats-label">{{ __('bookmarks.index.total_folders') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-icon">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stats-value">{{ $stats['this_week'] ?? 0 }}</div>
            <div class="stats-label">{{ __('bookmarks.index.this_week') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="stats-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stats-value">{{ $stats['favorites'] ?? 0 }}</div>
            <div class="stats-label">{{ __('bookmarks.index.favorites') }}</div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchInput" 
                           placeholder="{{ __('bookmarks.index.search_placeholder') }}"
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="folderFilter">
                    <option value="">{{ __('bookmarks.index.all_folders') }}</option>
                    @if($folders && $folders->count() > 0)
                        @foreach($folders as $folder)
                            <option value="{{ $folder->id }}" {{ request('folder') == $folder->id ? 'selected' : '' }}>
                                {{ $folder->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="typeFilter">
                    <option value="">{{ __('bookmarks.index.all_types') }}</option>
                    <option value="thread" {{ request('type') === 'thread' ? 'selected' : '' }}>
                        {{ __('bookmarks.index.threads') }}
                    </option>
                    <option value="showcase" {{ request('type') === 'showcase' ? 'selected' : '' }}>
                        {{ __('bookmarks.index.showcases') }}
                    </option>
                    <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>
                        {{ __('bookmarks.index.products') }}
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="sortFilter">
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>
                        {{ __('bookmarks.index.sort_latest') }}
                    </option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>
                        {{ __('bookmarks.index.sort_oldest') }}
                    </option>
                    <option value="alphabetical" {{ request('sort') === 'alphabetical' ? 'selected' : '' }}>
                        {{ __('bookmarks.index.sort_alphabetical') }}
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                    <label class="form-check-label" for="selectAllCheckbox">
                        {{ __('bookmarks.index.select_all') }}
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bookmarks List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ __('bookmarks.index.bookmarks_list') }}</h5>
    </div>
    
    <div class="card-body p-0">
        @if($bookmarks && $bookmarks->count() > 0)
            <div class="bookmark-list">
                @foreach($bookmarks as $bookmark)
                    <div class="bookmark-item border-bottom p-3" data-bookmark-id="{{ $bookmark->id }}">
                        <div class="d-flex">
                            <!-- Checkbox -->
                            <div class="bookmark-checkbox me-3">
                                <input type="checkbox" class="form-check-input bookmark-select" 
                                       value="{{ $bookmark->id }}" id="bookmark-{{ $bookmark->id }}">
                            </div>
                            
                            <!-- Icon -->
                            <div class="bookmark-icon me-3">
                                <div class="bg-{{ $bookmark->type_color ?? 'primary' }} bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="{{ $bookmark->type_icon ?? 'fas fa-bookmark' }} text-{{ $bookmark->type_color ?? 'primary' }}"></i>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="bookmark-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="bookmark-title mb-0">
                                        <a href="{{ $bookmark->url }}" class="text-decoration-none">
                                            {{ $bookmark->title }}
                                        </a>
                                    </h6>
                                    <div class="bookmark-actions d-flex gap-1">
                                        @if($bookmark->is_favorite)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-star"></i>
                                            </span>
                                        @endif
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-warning favorite-btn"
                                                data-bookmark-id="{{ $bookmark->id }}"
                                                title="{{ $bookmark->is_favorite ? __('bookmarks.index.remove_favorite') : __('bookmarks.index.add_favorite') }}">
                                            <i class="fas {{ $bookmark->is_favorite ? 'fa-star' : 'fa-star-o' }}"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary edit-btn"
                                                data-bookmark-id="{{ $bookmark->id }}"
                                                title="{{ __('bookmarks.index.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger delete-btn"
                                                data-bookmark-id="{{ $bookmark->id }}"
                                                title="{{ __('bookmarks.index.delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                @if($bookmark->description)
                                    <p class="bookmark-description mb-2 text-muted">{{ $bookmark->description }}</p>
                                @endif
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="bookmark-meta">
                                        <small class="text-muted">
                                            <i class="fas fa-folder me-1"></i>
                                            {{ $bookmark->folder ? $bookmark->folder->name : __('bookmarks.index.no_folder') }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $bookmark->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="bookmark-type">
                                        <span class="badge bg-{{ $bookmark->type_color ?? 'secondary' }}">
                                            {{ __('bookmarks.types.' . $bookmark->type) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($bookmarks->hasPages())
                <div class="card-footer">
                    {{ $bookmarks->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-bookmark fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">{{ __('bookmarks.index.no_bookmarks') }}</h5>
                <p class="text-muted">{{ __('bookmarks.index.no_bookmarks_desc') }}</p>
                <a href="{{ route('threads.index') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    {{ __('bookmarks.index.browse_content') }}
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('bookmarks.index.create_folder') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createFolderForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="folderName" class="form-label">{{ __('bookmarks.index.folder_name') }}</label>
                        <input type="text" class="form-control" id="folderName" required>
                    </div>
                    <div class="mb-3">
                        <label for="folderDescription" class="form-label">{{ __('bookmarks.index.folder_description') }}</label>
                        <textarea class="form-control" id="folderDescription" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('common.create') }}</button>
                </div>
            </form>
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

.bookmark-item {
    transition: background-color 0.2s;
}

.bookmark-item:hover {
    background-color: #f8f9fa;
}

.bookmark-checkbox {
    flex-shrink: 0;
}

.bookmark-icon {
    flex-shrink: 0;
}

.bookmark-content {
    min-width: 0;
}

.bookmark-title a {
    color: #333;
    font-weight: 500;
}

.bookmark-title a:hover {
    color: #007bff;
}

.bookmark-description {
    line-height: 1.5;
}

.bookmark-actions .btn {
    padding: 0.25rem 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search and filter functionality
    const searchInput = document.getElementById('searchInput');
    const folderFilter = document.getElementById('folderFilter');
    const typeFilter = document.getElementById('typeFilter');
    const sortFilter = document.getElementById('sortFilter');
    
    function updateFilters() {
        const params = new URLSearchParams();
        
        if (searchInput.value) params.set('search', searchInput.value);
        if (folderFilter.value) params.set('folder', folderFilter.value);
        if (typeFilter.value) params.set('type', typeFilter.value);
        if (sortFilter.value) params.set('sort', sortFilter.value);
        
        window.location.search = params.toString();
    }
    
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            updateFilters();
        }
    });
    
    folderFilter.addEventListener('change', updateFilters);
    typeFilter.addEventListener('change', updateFilters);
    sortFilter.addEventListener('change', updateFilters);
    
    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const bookmarkCheckboxes = document.querySelectorAll('.bookmark-select');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    selectAllCheckbox.addEventListener('change', function() {
        bookmarkCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });
    
    bookmarkCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
    
    function updateBulkActions() {
        const selectedCount = document.querySelectorAll('.bookmark-select:checked').length;
        bulkDeleteBtn.style.display = selectedCount > 0 ? 'block' : 'none';
    }
    
    // Create folder
    document.getElementById('createFolderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            name: document.getElementById('folderName').value,
            description: document.getElementById('folderDescription').value
        };
        
        fetch('/dashboard/community/bookmarks/folders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('{{ __("bookmarks.index.error_occurred") }}');
            }
        });
    });
});
</script>
@endpush
