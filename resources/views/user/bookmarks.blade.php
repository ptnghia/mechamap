@extends('layouts.app')

@section('title', __('user.bookmarks.title'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar with filters -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bookmark me-2"></i>
                        {{ __('user.bookmarks.filter_title') }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search form -->
                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" name="search"
                                value="{{ request('search') }}" placeholder="{{ __('user.bookmarks.search_placeholder') }}">
                            <button class="btn btn-outline-primary btn-sm" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Folder filter -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('user.bookmarks.folder_label') }}</label>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('user.bookmarks') }}"
                                class="list-group-item list-group-item-action {{ !request('folder') ? 'active' : '' }}">
                                <i class="fas fa-folder-open me-2"></i>
                                Tất cả ({{ $stats['total'] }})
                            </a>

                            @foreach($folders as $folder)
                            <a href="{{ route('user.bookmarks', ['folder' => $folder->folder]) }}"
                                class="list-group-item list-group-item-action {{ request('folder') === $folder->folder ? 'active' : '' }}">
                                <i class="fas fa-folder me-2"></i>
                                {{ $folder->folder ?: __('user.bookmarks.no_folder') }} ({{ $folder->count }})
                            </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Quick stats -->
                    <div class="border-top pt-3">
                        <h6 class="fw-bold mb-2">Thống kê:</h6>
                        <div class="text-muted small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ __('user.bookmarks.stats.total_bookmarks') }}</span>
                                <span class="fw-bold">{{ $stats['total'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ __('user.bookmarks.stats.with_folders') }}</span>
                                <span class="fw-bold">{{ $stats['with_folders'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Có ghi chú:</span>
                                <span class="fw-bold">{{ $stats['with_notes'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-bookmark me-2"></i>
                        {{ __('user.bookmarks.title') }}
                        @if(request('folder'))
                        - {{ request('folder') }}
                        @endif
                    </h5>

                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#addFolderModal">
                            <i class="fas fa-folder-plus me-1"></i>
                            {{ __('user.bookmarks.actions.create_folder') }}
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="bulkDelete()">
                            <i class="fas fa-trash me-1"></i>
                            {{ __('user.bookmarks.actions.delete_selected') }}
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @if($bookmarks->count() > 0)
                    <!-- Bulk actions -->
                    <div class="d-flex align-items-center mb-3">
                        <input type="checkbox" id="selectAll" class="form-check-input me-2">
                        <label for="selectAll" class="form-check-label">Chọn tất cả</label>

                        <div class="ms-auto">
                            <span class="text-muted">{{ trans('user.bookmarks.actions.results_count', ['count' => $bookmarks->total()]) }}</span>
                        </div>
                    </div>

                    <!-- Bookmarks list -->
                    <div class="row g-3">
                        @foreach($bookmarks as $bookmark)
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="row align-items-start">
                                        <!-- Checkbox -->
                                        <div class="col-auto">
                                            <input type="checkbox" class="form-check-input bookmark-checkbox"
                                                value="{{ $bookmark->id }}">
                                        </div>

                                        <!-- Thread info -->
                                        <div class="col">
                                            <h6 class="mb-2">
                                                <a href="{{ route('threads.show', $bookmark->thread->slug) }}"
                                                    class="text-decoration-none">
                                                    {{ $bookmark->thread->title }}
                                                </a>

                                                <!-- Thread status badges -->
                                                @if($bookmark->thread->is_closed)
                                                <span class="badge bg-secondary ms-2">Đã đóng</span>
                                                @endif
                                                @if($bookmark->thread->is_sticky)
                                                <span class="badge bg-warning ms-1">Ghim</span>
                                                @endif
                                                @if($bookmark->thread->has_solution)
                                                <span class="badge bg-success ms-1">Đã giải quyết</span>
                                                @endif
                                            </h6>

                                            <div class="text-muted small mb-2">
                                                <i class="fas fa-user me-1"></i> {{ $bookmark->thread->user->name }}
                                                <i class="fas fa-comments ms-3 me-1"></i> {{
                                                $bookmark->thread->comments_count }} bình luận
                                                <i class="fas fa-eye ms-3 me-1"></i> {{ $bookmark->thread->views_count
                                                }} lượt xem
                                                @if($bookmark->thread->average_rating)
                                                <i class="fas fa-star ms-3 me-1 text-warning"></i>
                                                {{ number_format($bookmark->thread->average_rating, 1) }}/5
                                                ({{ $bookmark->thread->ratings_count }})
                                                @endif
                                            </div>

                                            <!-- Bookmark details -->
                                            <div class="bookmark-details">
                                                @if($bookmark->folder)
                                                <div class="mb-1">
                                                    <i class="fas fa-folder me-1 text-primary"></i>
                                                    <span class="badge bg-light text-dark">{{ $bookmark->folder
                                                        }}</span>
                                                </div>
                                                @endif

                                                @if($bookmark->notes)
                                                <div class="mb-1">
                                                    <i class="fas fa-sticky-note me-1 text-warning"></i>
                                                    <span class="text-muted">{{ $bookmark->notes }}</span>
                                                </div>
                                                @endif

                                                <div class="text-muted small">
                                                    <i class="fas fa-bookmark me-1"></i>
                                                    Bookmark vào {{ $bookmark->created_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="col-auto">
                                            <div class="btn-group-vertical">
                                                <button class="btn btn-sm btn-outline-primary"
                                                    onclick="editBookmark({{ $bookmark->id }}, '{{ $bookmark->folder }}', '{{ addslashes($bookmark->notes) }}')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteBookmark({{ $bookmark->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $bookmarks->links() }}
                    </div>

                    @else
                    <!-- Empty state -->
                    <div class="text-center py-5">
                        <i class="fas fa-bookmark fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có bookmark nào</h5>
                        <p class="text-muted">Hãy bookmark những thread hay để dễ dàng tìm lại sau này!</p>
                        <a href="{{ route('threads.index') }}" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>
                            Khám phá threads
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Folder Modal -->
<div class="modal fade" id="addFolderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('user.bookmarks.folder_modal.create_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addFolderForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="folderName" class="form-label">{{ __('user.bookmarks.folder_modal.name_label') }}</label>
                        <input type="text" class="form-control" id="folderName" name="folder_name"
                            placeholder="{{ __('user.bookmarks.folder_modal.name_placeholder') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="folderDescription" class="form-label">{{ __('user.bookmarks.folder_modal.description_label') }}</label>
                        <textarea class="form-control" id="folderDescription" name="description" rows="3"
                            placeholder="{{ __('user.bookmarks.folder_modal.description_placeholder') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('user.bookmarks.folder_modal.cancel_button') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('user.bookmarks.folder_modal.create_button') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Bookmark Modal -->
<div class="modal fade" id="editBookmarkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa bookmark</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBookmarkForm">
                <input type="hidden" id="editBookmarkId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editFolder" class="form-label">{{ __('user.bookmarks.edit_modal.folder_label') }}</label>
                        <select class="form-select" id="editFolder" name="folder">
                            <option value="">{{ __('user.bookmarks.no_folder') }}</option>
                            @foreach($folders as $folder)
                            <option value="{{ $folder->folder }}">{{ $folder->folder }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editNotes" class="form-label">Ghi chú:</label>
                        <textarea class="form-control" id="editNotes" name="notes" rows="3"
                            placeholder="Ghi chú cá nhân về thread này..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const bookmarkCheckboxes = document.querySelectorAll('.bookmark-checkbox');

    selectAllCheckbox?.addEventListener('change', function() {
        bookmarkCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Add folder form
    document.getElementById('addFolderForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route("user.bookmarks.create-folder") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                bootstrap.Modal.getInstance(document.getElementById('addFolderModal')).hide();
                location.reload();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', {!! json_encode(__('user.bookmarks.errors.create_folder_failed')) !!});
        });
    });

    // Edit bookmark form
    document.getElementById('editBookmarkForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const bookmarkId = document.getElementById('editBookmarkId').value;
        const formData = new FormData(this);

        fetch(`{{ url('user/bookmarks') }}/${bookmarkId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                folder: document.getElementById('editFolder').value,
                notes: document.getElementById('editNotes').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                bootstrap.Modal.getInstance(document.getElementById('editBookmarkModal')).hide();
                location.reload();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'Có lỗi xảy ra khi cập nhật bookmark');
        });
    });
});

function editBookmark(id, folder, notes) {
    document.getElementById('editBookmarkId').value = id;
    document.getElementById('editFolder').value = folder || '';
    document.getElementById('editNotes').value = notes || '';

    new bootstrap.Modal(document.getElementById('editBookmarkModal')).show();
}

function deleteBookmark(id) {
    if (confirm({!! json_encode(__('user.bookmarks.confirmations.delete_bookmark')) !!})) {
        fetch(`{{ url('user/bookmarks') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                location.reload();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', {!! json_encode(__('user.bookmarks.errors.delete_bookmark_failed')) !!});
        });
    }
}

function bulkDelete() {
    const selectedCheckboxes = document.querySelectorAll('.bookmark-checkbox:checked');
    const ids = Array.from(selectedCheckboxes).map(cb => cb.value);

    if (ids.length === 0) {
        showAlert('warning', {!! json_encode(__('user.bookmarks.confirmations.select_at_least_one')) !!});
        return;
    }

    if (confirm({!! json_encode(__('user.bookmarks.confirmations.delete_multiple', ['count' => '${ids.length}'])) !!}.replace('${ids.length}', ids.length))) {
        fetch('{{ route("user.bookmarks.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ bookmark_ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                location.reload();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', {!! json_encode(__('user.bookmarks.errors.delete_bookmarks_failed')) !!});
        });
    }
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        }
    }, 5000);
}
</script>
@endpush
