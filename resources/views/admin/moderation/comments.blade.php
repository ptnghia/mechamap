{{-- Admin Moderation Comments Management --}}
@extends('layouts.admin')

@section('title', 'Quản lý Comments - Moderation')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.moderation.dashboard') }}">Moderation</a></li>
        <li class="breadcrumb-item active">Comments</li>
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
                                Quản lý Comments
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
                                <button type="button" class="btn btn-warning btn-sm" onclick="bulkMarkSpam()">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Đánh dấu spam
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
                                value="{{ request('search') }}" placeholder="Nội dung, tác giả...">
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
                                <option value="spam" {{ request('status')==='spam' ? 'selected' : '' }}>
                                    Spam
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="thread" class="form-label">Thread</label>
                            <select class="form-select" id="thread" name="thread_id">
                                <option value="">Tất cả threads</option>
                                @foreach($threads as $thread)
                                <option value="{{ $thread->id }}" {{ request('thread_id')==$thread->id ? 'selected' : ''
                                    }}>
                                    {{ Str::limit($thread->title, 50) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="spam_score" class="form-label">Điểm spam</label>
                            <select class="form-select" id="spam_score" name="spam_score">
                                <option value="">Tất cả</option>
                                <option value="high" {{ request('spam_score')==='high' ? 'selected' : '' }}>
                                    Cao (≥ 0.7)
                                </option>
                                <option value="medium" {{ request('spam_score')==='medium' ? 'selected' : '' }}>
                                    Trung bình (0.3-0.7)
                                </option>
                                <option value="low" {{ request('spam_score')==='low' ? 'selected' : '' }}>
                                    Thấp (< 0.3) </option>
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
                                <option value="spam_score" {{ request('sort')==='spam_score' ? 'selected' : '' }}>
                                    Điểm spam cao nhất
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
                <div class="col-md-2">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Chờ duyệt</h6>
                                    <h4 class="mb-0">{{ $statistics['pending'] ?? 0 }}</h4>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Đã báo cáo</h6>
                                    <h4 class="mb-0">{{ $statistics['flagged'] ?? 0 }}</h4>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-flag fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Đã duyệt</h6>
                                    <h4 class="mb-0">{{ $statistics['approved'] ?? 0 }}</h4>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Từ chối</h6>
                                    <h4 class="mb-0">{{ $statistics['rejected'] ?? 0 }}</h4>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Spam</h6>
                                    <h4 class="mb-0">{{ $statistics['spam'] ?? 0 }}</h4>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-purple text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Tổng số</h6>
                                    <h4 class="mb-0">{{ $statistics['total'] ?? 0 }}</h4>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-comment fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Comments Table --}}
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Danh sách Comments
                            <small class="text-muted">({{ $comments->total() }} kết quả)</small>
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
                    @if($comments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40"></th>
                                    <th>Comment</th>
                                    <th>Tác giả</th>
                                    <th>Thread</th>
                                    <th>Trạng thái</th>
                                    <th>Spam Score</th>
                                    <th>Báo cáo</th>
                                    <th>Ngày tạo</th>
                                    <th width="120">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comments as $comment)
                                <tr data-comment-id="{{ $comment->id }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input comment-checkbox"
                                            value="{{ $comment->id }}">
                                    </td>
                                    <td>
                                        <div class="comment-content">
                                            <div class="fw-medium mb-1">
                                                {{ Str::limit(strip_tags($comment->content), 100) }}
                                            </div>
                                            @if($comment->parent_id)
                                            <small class="text-muted">
                                                <i class="fas fa-reply me-1"></i>
                                                Trả lời comment của {{ $comment->parent->user->name }}
                                            </small>
                                            @endif
                                            {{-- Hiển thị badges đặc biệt --}}
                                            <div class="mt-2">
                                                @if($comment->spam_score >= 0.7)
                                                <span class="badge bg-danger">High Spam Risk</span>
                                                @elseif($comment->spam_score >= 0.3)
                                                <span class="badge bg-warning">Medium Risk</span>
                                                @endif

                                                @if(str_word_count(strip_tags($comment->content)) < 3) <span
                                                    class="badge bg-info">Ngắn</span>
                                                    @endif

                                                    @if(preg_match('/https?:\/\//', $comment->content))
                                                    <span class="badge bg-warning">Có link</span>
                                                    @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $comment->user->avatar ?? 'https://ui-avatars.cc/api/?name=' . urlencode($comment->user->name) }}"
                                                alt="Avatar" class="rounded-circle me-2"
                                                style="width: 32px; height: 32px;">
                                            <div>
                                                <div class="fw-medium">{{ $comment->user->name }}</div>
                                                <small class="text-muted">{{ $comment->user->email }}</small>
                                                @if($comment->user->comments_count > 100)
                                                <span class="badge bg-success">Active User</span>
                                                @elseif($comment->user->created_at->diffInDays() < 7) <span
                                                    class="badge bg-warning">New User</span>
                                                    @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <a href="{{ route('threads.show', $comment->thread->slug) }}"
                                                target="_blank" class="text-decoration-none">
                                                {{ Str::limit($comment->thread->title, 40) }}
                                            </a>
                                        </div>
                                        <small class="text-muted">{{ $comment->thread->forum->name }}</small>
                                    </td>
                                    <td>
                                        @php
                                        $statusClasses = [
                                        'pending' => 'bg-warning',
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        'flagged' => 'bg-danger',
                                        'spam' => 'bg-dark'
                                        ];
                                        $statusLabels = [
                                        'pending' => 'Chờ duyệt',
                                        'approved' => 'Đã duyệt',
                                        'rejected' => 'Từ chối',
                                        'flagged' => 'Đã báo cáo',
                                        'spam' => 'Spam'
                                        ];
                                        @endphp
                                        <span
                                            class="badge {{ $statusClasses[$comment->moderation_status] ?? 'bg-secondary' }}">
                                            {{ $statusLabels[$comment->moderation_status] ?? $comment->moderation_status
                                            }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($comment->spam_score)
                                        @php
                                        $score = round($comment->spam_score * 100);
                                        $scoreClass = 'bg-success';
                                        if ($score >= 70) $scoreClass = 'bg-danger';
                                        elseif ($score >= 30) $scoreClass = 'bg-warning';
                                        @endphp
                                        <span class="badge {{ $scoreClass }}">{{ $score }}%</span>
                                        @else
                                        <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($comment->flags_count > 0)
                                        <span class="badge bg-danger">
                                            <i class="fas fa-flag me-1"></i>
                                            {{ $comment->flags_count }}
                                        </span>
                                        @else
                                        <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $comment->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $comment->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($comment->moderation_status !== 'approved')
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="quickAction({{ $comment->id }}, 'approve')" title="Duyệt">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            @endif
                                            @if($comment->moderation_status !== 'rejected')
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="quickAction({{ $comment->id }}, 'reject')" title="Từ chối">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endif
                                            @if($comment->moderation_status !== 'spam')
                                            <button type="button" class="btn btn-warning btn-sm"
                                                onclick="quickAction({{ $comment->id }}, 'spam')" title="Đánh dấu spam">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </button>
                                            @endif
                                            <button type="button" class="btn btn-info btn-sm"
                                                onclick="showCommentDetails({{ $comment->id }})" title="Chi tiết">
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
                        <h5 class="text-muted">Không có comments nào</h5>
                        <p class="text-muted">Không tìm thấy comments phù hợp với bộ lọc hiện tại.</p>
                    </div>
                    @endif
                </div>
                @if($comments->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Hiển thị {{ $comments->firstItem() }} - {{ $comments->lastItem() }}
                            trong tổng số {{ $comments->total() }} kết quả
                        </div>
                        {{ $comments->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Comment Details Modal --}}
<div class="modal fade" id="commentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="commentDetailsContent">
                {{-- Content will be loaded via AJAX --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success" id="approveCommentBtn">
                    <i class="fas fa-check me-1"></i> Duyệt
                </button>
                <button type="button" class="btn btn-danger" id="rejectCommentBtn">
                    <i class="fas fa-times me-1"></i> Từ chối
                </button>
                <button type="button" class="btn btn-warning" id="spamCommentBtn">
                    <i class="fas fa-exclamation-triangle me-1"></i> Spam
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
    $('#status, #thread, #spam_score, #sort').change(function() {
        $('#filterForm').submit();
    });

    // Select all checkbox functionality
    $('#selectAll').change(function() {
        $('.comment-checkbox').prop('checked', this.checked);
    });

    // Update select all when individual checkboxes change
    $('.comment-checkbox').change(function() {
        const total = $('.comment-checkbox').length;
        const checked = $('.comment-checkbox:checked').length;
        $('#selectAll').prop('indeterminate', checked > 0 && checked < total);
        $('#selectAll').prop('checked', checked === total);
    });
});

// Quick action functions
function quickAction(commentId, action) {
    const actionTexts = {
        'approve': 'duyệt',
        'reject': 'từ chối',
        'spam': 'đánh dấu là spam'
    };
    const actionText = actionTexts[action];

    if (confirm(`Bạn có chắc chắn muốn ${actionText} comment này?`)) {
        $.ajax({
            url: `{{ route('admin.moderation.comments.update-status', ':id') }}`.replace(':id', commentId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: action === 'approve' ? 'approved' : action === 'reject' ? 'rejected' : 'spam'
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

// Show comment details modal
function showCommentDetails(commentId) {
    $('#commentDetailsContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>');
    $('#commentDetailsModal').modal('show');

    // Set button actions
    $('#approveCommentBtn').off('click').on('click', function() {
        quickAction(commentId, 'approve');
        $('#commentDetailsModal').modal('hide');
    });

    $('#rejectCommentBtn').off('click').on('click', function() {
        quickAction(commentId, 'reject');
        $('#commentDetailsModal').modal('hide');
    });

    $('#spamCommentBtn').off('click').on('click', function() {
        quickAction(commentId, 'spam');
        $('#commentDetailsModal').modal('hide');
    });

    // Load comment details (implement this endpoint in controller)
    $.get(`{{ route('admin.moderation.comments.show', ':id') }}`.replace(':id', commentId))
        .done(function(data) {
            $('#commentDetailsContent').html(data);
        })
        .fail(function() {
            $('#commentDetailsContent').html('<div class="alert alert-danger">Không thể tải thông tin comment.</div>');
        });
}

// Bulk operations
function bulkApprove() {
    bulkAction('approve');
}

function bulkReject() {
    bulkAction('reject');
}

function bulkMarkSpam() {
    bulkAction('spam');
}

function bulkAction(action) {
    const selectedComments = $('.comment-checkbox:checked').map(function() {
        return this.value;
    }).get();

    if (selectedComments.length === 0) {
        alert('Vui lòng chọn ít nhất một comment.');
        return;
    }

    showBulkActionModal(action, selectedComments);
}

function showBulkActionModal(action, commentIds) {
    const actionTexts = {
        'approve': 'duyệt',
        'reject': 'từ chối',
        'spam': 'đánh dấu là spam'
    };
    const actionText = actionTexts[action];

    $('#bulkActionTitle').text(`Xác nhận ${actionText} hàng loạt`);
    $('#bulkActionMessage').text(`Bạn có chắc chắn muốn ${actionText} ${commentIds.length} comment đã chọn?`);

    $('#confirmBulkAction').off('click').on('click', function() {
        const reason = $('#bulkActionReason').val();

        $.ajax({
            url: '{{ route("admin.moderation.comments.bulk-update") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                comment_ids: commentIds,
                status: action === 'approve' ? 'approved' : action === 'reject' ? 'rejected' : 'spam',
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

<style>
    .bg-purple {
        background-color: #6f42c1 !important;
    }

    .comment-content {
        max-width: 300px;
    }

    .table td {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.75em;
    }
</style>
@endpush