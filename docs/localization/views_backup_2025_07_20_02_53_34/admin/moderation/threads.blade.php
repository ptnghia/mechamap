{{-- Admin Moderation Threads Management --}}
@extends('admin.layouts.dason')

@section('title', 'Quản lý Threads - Moderation')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.moderation.dashboard') }}">Moderation</a></li>
        <li class="breadcrumb-item active">Threads</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Header với Search và Filters --}}
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-0">
                                <i class="fas fa-comments me-2"></i>
                                Quản lý Threads
                            </h4>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success btn-sm" onclick="bulkApprove()">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Duyệt hàng loạt
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="bulkReject()">
                                    <i class="fas fa-times-circle me-1"></i>
                                    Từ chối hàng loạt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Search và Filter Form --}}
                    <form method="GET" class="row g-3" id="filterForm">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Tiêu đề, nội dung, tác giả...">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>
                                    Chờ duyệt
                                </option>
                                <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>
                                    Đã duyệt
                                </option>
                                <option value="rejected" {{ request('status')==='rejected' ? 'selected' : '' }}>
                                    Từ chối
                                </option>
                                <option value="flagged" {{ request('status')==='flagged' ? 'selected' : '' }}>
                                    Đã báo cáo
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="forum" class="form-label">Chủ đề</label>
                            <select class="form-select" id="forum" name="forum_id">
                                <option value="">Tất cả chủ đề</option>
                                @foreach($forums as $forum)
                                <option value="{{ $forum->id }}" {{ request('forum_id')==$forum->id ? 'selected' : ''
                                    }}>
                                    {{ $forum->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="priority" class="form-label">Độ ưu tiên</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="">Tất cả</option>
                                <option value="high" {{ request('priority')==='high' ? 'selected' : '' }}>
                                    Cao
                                </option>
                                <option value="normal" {{ request('priority')==='normal' ? 'selected' : '' }}>
                                    Bình thường
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="sort" class="form-label">Sắp xếp</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="newest" {{ request('sort')==='newest' ? 'selected' : '' }}>
                                    Mới nhất
                                </option>
                                <option value="oldest" {{ request('sort')==='oldest' ? 'selected' : '' }}>
                                    Cũ nhất
                                </option>
                                <option value="most_flagged" {{ request('sort')==='most_flagged' ? 'selected' : '' }}>
                                    Nhiều báo cáo nhất
                                </option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Chờ duyệt</h6>
                                    <h3 class="mb-0">{{ $statistics['pending'] ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Đã báo cáo</h6>
                                    <h3 class="mb-0">{{ $statistics['flagged'] ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-flag fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Đã duyệt</h6>
                                    <h3 class="mb-0">{{ $statistics['approved'] ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Từ chối</h6>
                                    <h3 class="mb-0">{{ $statistics['rejected'] ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Threads Table --}}
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Danh sách Threads
                            <small class="text-muted">({{ $threads->total() }} kết quả)</small>
                        </h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label" for="selectAll">
                                Chọn tất cả
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($threads->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40"></th>
                                    <th>Thread</th>
                                    <th>Tác giả</th>
                                    <th>Chủ đề</th>
                                    <th>Trạng thái</th>
                                    <th>Báo cáo</th>
                                    <th>Ngày tạo</th>
                                    <th width="120">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($threads as $thread)
                                <tr data-thread-id="{{ $thread->id }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input thread-checkbox"
                                            value="{{ $thread->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            @if($thread->image)
                                            <img src="{{ $thread->image }}" alt="Thread image" class="me-3 rounded"
                                                style="width: 60px; height: 40px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="{{ route('threads.show', $thread->slug) }}" target="_blank"
                                                        class="text-decoration-none">
                                                        {{ Str::limit($thread->title, 60) }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    {{ Str::limit(strip_tags($thread->content), 80) }}
                                                </small>
                                                @if($thread->priority === 'high')
                                                <span class="badge bg-danger ms-2">Ưu tiên cao</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $thread->user->avatar ?? 'https://ui-avatars.cc/api/?name=' . urlencode($thread->user->name) }}"
                                                alt="Avatar" class="rounded-circle me-2"
                                                style="width: 32px; height: 32px;">
                                            <div>
                                                <div class="fw-medium">{{ $thread->user->name }}</div>
                                                <small class="text-muted">{{ $thread->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $thread->forum->name }}</span>
                                    </td>
                                    <td>
                                        @php
                                        $statusClasses = [
                                        'pending' => 'bg-warning',
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        'flagged' => 'bg-danger'
                                        ];
                                        $statusLabels = [
                                        'pending' => 'Chờ duyệt',
                                        'approved' => 'Đã duyệt',
                                        'rejected' => 'Từ chối',
                                        'flagged' => 'Đã báo cáo'
                                        ];
                                        @endphp
                                        <span
                                            class="badge {{ $statusClasses[$thread->moderation_status] ?? 'bg-secondary' }}">
                                            {{ $statusLabels[$thread->moderation_status] ?? $thread->moderation_status
                                            }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($thread->flags_count > 0)
                                        <span class="badge bg-danger">
                                            <i class="fas fa-flag me-1"></i>
                                            {{ $thread->flags_count }}
                                        </span>
                                        @else
                                        <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $thread->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $thread->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($thread->moderation_status !== 'approved')
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="quickAction({{ $thread->id }}, 'approve')" title="Duyệt">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            @endif
                                            @if($thread->moderation_status !== 'rejected')
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="quickAction({{ $thread->id }}, 'reject')" title="Từ chối">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endif
                                            <button type="button" class="btn btn-info btn-sm"
                                                onclick="showThreadDetails({{ $thread->id }})" title="Chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không có threads nào</h5>
                        <p class="text-muted">Không tìm thấy threads phù hợp với bộ lọc hiện tại.</p>
                    </div>
                    @endif
                </div>
                @if($threads->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Hiển thị {{ $threads->firstItem() }} - {{ $threads->lastItem() }}
                            trong tổng số {{ $threads->total() }} kết quả
                        </div>
                        {{ $threads->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Thread Details Modal --}}
<div class="modal fade" id="threadDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết Thread</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="threadDetailsContent">
                {{-- Content will be loaded via AJAX --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success" id="approveThreadBtn">
                    <i class="fas fa-check me-1"></i> Duyệt
                </button>
                <button type="button" class="btn btn-danger" id="rejectThreadBtn">
                    <i class="fas fa-times me-1"></i> Từ chối
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Bulk Action Modal --}}
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionTitle">Xác nhận thao tác</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="bulkActionMessage"></p>
                <div class="form-group">
                    <label for="bulkActionReason">Lý do (tùy chọn):</label>
                    <textarea class="form-control" id="bulkActionReason" rows="3"
                        placeholder="Nhập lý do cho hành động này..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmBulkAction">Xác nhận</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
    // Auto-submit form when filters change
    $('#status, #forum, #priority, #sort').change(function() {
        $('#filterForm').submit();
    });

    // Select all checkbox functionality
    $('#selectAll').change(function() {
        $('.thread-checkbox').prop('checked', this.checked);
    });

    // Update select all when individual checkboxes change
    $('.thread-checkbox').change(function() {
        const total = $('.thread-checkbox').length;
        const checked = $('.thread-checkbox:checked').length;
        $('#selectAll').prop('indeterminate', checked > 0 && checked < total);
        $('#selectAll').prop('checked', checked === total);
    });
});

// Quick action functions
function quickAction(threadId, action) {
    const actionText = action === 'approve' ? 'duyệt' : 'từ chối';

    if (confirm(`Bạn có chắc chắn muốn ${actionText} thread này?`)) {
        $.ajax({
            url: `{{ route('admin.moderation.threads.update-status', ':id') }}`.replace(':id', threadId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: action === 'approve' ? 'approved' : 'rejected'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra: ' + response.message);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra khi thực hiện hành động.');
            }
        });
    }
}

// Show thread details modal
function showThreadDetails(threadId) {
    $('#threadDetailsContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>');
    $('#threadDetailsModal').modal('show');

    // Set button actions
    $('#approveThreadBtn').off('click').on('click', function() {
        quickAction(threadId, 'approve');
        $('#threadDetailsModal').modal('hide');
    });

    $('#rejectThreadBtn').off('click').on('click', function() {
        quickAction(threadId, 'reject');
        $('#threadDetailsModal').modal('hide');
    });

    // Load thread details (implement this endpoint in controller)
    $.get(`{{ route('admin.moderation.threads.show', ':id') }}`.replace(':id', threadId))
        .done(function(data) {
            $('#threadDetailsContent').html(data);
        })
        .fail(function() {
            $('#threadDetailsContent').html('<div class="alert alert-danger">Không thể tải thông tin thread.</div>');
        });
}

// Bulk operations
function bulkApprove() {
    const selectedThreads = $('.thread-checkbox:checked').map(function() {
        return this.value;
    }).get();

    if (selectedThreads.length === 0) {
        alert('Vui lòng chọn ít nhất một thread.');
        return;
    }

    showBulkActionModal('approve', selectedThreads);
}

function bulkReject() {
    const selectedThreads = $('.thread-checkbox:checked').map(function() {
        return this.value;
    }).get();

    if (selectedThreads.length === 0) {
        alert('Vui lòng chọn ít nhất một thread.');
        return;
    }

    showBulkActionModal('reject', selectedThreads);
}

function showBulkActionModal(action, threadIds) {
    const actionText = action === 'approve' ? 'duyệt' : 'từ chối';

    $('#bulkActionTitle').text(`Xác nhận ${actionText} hàng loạt`);
    $('#bulkActionMessage').text(`Bạn có chắc chắn muốn ${actionText} ${threadIds.length} thread đã chọn?`);

    $('#confirmBulkAction').off('click').on('click', function() {
        const reason = $('#bulkActionReason').val();

        $.ajax({
            url: '{{ route("admin.moderation.threads.bulk-update") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                thread_ids: threadIds,
                status: action === 'approve' ? 'approved' : 'rejected',
                reason: reason
            },
            success: function(response) {
                if (response.success) {
                    $('#bulkActionModal').modal('hide');
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra: ' + response.message);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra khi thực hiện hành động.');
            }
        });
    });

    $('#bulkActionModal').modal('show');
}
</script>
@endpush
