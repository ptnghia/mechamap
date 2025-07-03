@extends('admin.layouts.dason')

@section('title', 'Chi tiết bài đăng')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Chi tiết bài đăng</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Chi tiết bài đăng</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.threads.edit', $thread) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-edit me-1"></i> {{ __('Chỉnh sửa') }}
    </a>
    @if($thread->status == 'pending')
        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
            <i class="fas fa-check me-1"></i> {{ __('Duyệt') }}
        </button>
        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
            <i class="fas fa-times me-1"></i> {{ __('Từ chối') }}
        </button>
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ $thread->title }}</h5>
                    <div>
                        @if($thread->status == 'draft')
                            <span class="badge bg-secondary">{{ __('Bản nháp') }}</span>
                        @elseif($thread->status == 'pending')
                            <span class="badge bg-warning">{{ __('Chờ duyệt') }}</span>
                        @elseif($thread->status == 'published')
                            <span class="badge bg-success">{{ __('Đã xuất bản') }}</span>
                        @elseif($thread->status == 'rejected')
                            <span class="badge bg-danger">{{ __('Đã từ chối') }}</span>
                        @endif

                        @if($thread->is_sticky)
                            <span class="badge bg-info">{{ __('Ghim') }}</span>
                        @endif

                        @if($thread->is_featured)
                            <span class="badge bg-warning">{{ __('Nổi bật') }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $thread->user->getAvatarUrl() }}" alt="{{ $thread->user->name }}" class="rounded-circle me-2" width="40" height="40">
                            <div>
                                <div class="fw-bold">{{ $thread->user->name }}</div>
                                <div class="small text-muted">{{ $thread->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="thread-content">
                            {!! $thread->content !!}
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-3">{{ __('Thông tin chi tiết') }}</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('ID') }}:</span>
                                    <span class="text-muted">{{ $thread->id }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('Slug') }}:</span>
                                    <span class="text-muted">{{ $thread->slug }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('Diễn đàn') }}:</span>
                                    <span class="text-muted">{{ $thread->forum->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('Chuyên mục') }}:</span>
                                    <span class="text-muted">{{ $thread->category->name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('Trạng thái') }}:</span>
                                    <div>
                                        @if($thread->is_sticky)
                                            <span class="badge bg-info me-1">{{ __('Ghim') }}</span>
                                        @endif
                                        @if($thread->is_locked)
                                            <span class="badge bg-warning me-1">{{ __('Khóa') }}</span>
                                        @endif
                                        @if($thread->is_featured)
                                            <span class="badge bg-success me-1">{{ __('Nổi bật') }}</span>
                                        @endif
                                        @if(!$thread->is_sticky && !$thread->is_locked && !$thread->is_featured)
                                            <span class="text-muted">{{ __('Bình thường') }}</span>
                                        @endif
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('Lượt xem') }}:</span>
                                    <span class="text-muted">{{ $thread->view_count }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('Số bình luận') }}:</span>
                                    <span class="text-muted">{{ $thread->comments->count() }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('Ngày tạo') }}:</span>
                                    <span class="text-muted">{{ $thread->created_at->format('d/m/Y H:i') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('Cập nhật lần cuối') }}:</span>
                                    <span class="text-muted">{{ $thread->updated_at->format('d/m/Y H:i') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Bình luận') }} ({{ $thread->comments->count() }})</h5>
                </div>
                <div class="card-body">
                    @forelse($thread->comments as $comment)
                        <div class="comment mb-4 {{ $comment->parent_id ? 'ms-4' : '' }}">
                            <div class="d-flex">
                                <img src="{{ $comment->user->getAvatarUrl() }}" alt="{{ $comment->user->name }}" class="rounded-circle me-2" width="40" height="40">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="fw-bold">{{ $comment->user->name }}</span>
                                            <span class="text-muted ms-2">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="commentActions{{ $comment->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="commentActions{{ $comment->id }}">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.comments.edit', $comment) }}">
                                                        <i class="fas fa-edit me-2"></i> {{ __('Chỉnh sửa') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.comments.toggle-visibility', $comment) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="dropdown-item">
                                                            @if($comment->is_hidden)
                                                                <i class="fas fa-eye me-2"></i> {{ __('Hiện bình luận') }}
                                                            @else
                                                                <i class="fas fa-eye-slash me-2"></i> {{ __('Ẩn bình luận') }}
                                                            @endif
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteCommentModal{{ $comment->id }}">
                                                        <i class="fas fa-trash me-2"></i> {{ __('Xóa') }}
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="comment-content {{ $comment->is_hidden ? 'text-muted fst-italic' : '' }}">
                                        @if($comment->is_hidden)
                                            <div class="alert alert-warning py-1 px-2 mb-2">
                                                <small><i class="fas fa-eye-slash me-1"></i> {{ __('Bình luận này đã bị ẩn') }}</small>
                                            </div>
                                        @endif
                                        @if($comment->is_flagged)
                                            <div class="alert alert-danger py-1 px-2 mb-2">
                                                <small><i class="fas fa-flag-fill me-1"></i> {{ __('Bình luận này đã bị đánh dấu') }}</small>
                                            </div>
                                        @endif
                                        {!! $comment->content !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal xóa bình luận -->
                        <div class="modal fade" id="deleteCommentModal{{ $comment->id }}" tabindex="-1" aria-labelledby="deleteCommentModalLabel{{ $comment->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteCommentModalLabel{{ $comment->id }}">{{ __('Xác nhận xóa bình luận') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        {{ __('Bạn có chắc chắn muốn xóa bình luận này?') }}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                                        <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">{{ __('Xóa') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(!$loop->last)
                            <hr>
                        @endif
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-comment fs-1 text-muted mb-3"></i>
                            <p class="mb-0">{{ __('Chưa có bình luận nào.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thao tác') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.threads.edit', $thread) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> {{ __('Chỉnh sửa') }}
                        </a>

                        <!-- Moderation Actions -->
                        <div class="row g-2">
                            <div class="col-6">
                                <form action="{{ route('admin.threads.toggle-pin', $thread) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-outline-info w-100">
                                        <i class="fas fa-thumbtack me-1"></i>
                                        {{ $thread->is_sticky ? __('Bỏ ghim') : __('Ghim') }}
                                    </button>
                                </form>
                            </div>
                            <div class="col-6">
                                <form action="{{ route('admin.threads.toggle-lock', $thread) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-{{ $thread->is_locked ? 'unlock' : 'lock' }} me-1"></i>
                                        {{ $thread->is_locked ? __('Mở khóa') : __('Khóa') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <form action="{{ route('admin.threads.toggle-feature', $thread) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-outline-success">
                                <i class="fas fa-star me-1"></i>
                                {{ $thread->is_featured ? __('Bỏ nổi bật') : __('Đánh dấu nổi bật') }}
                            </button>
                        </form>

                        @if($thread->status == 'pending')
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="fas fa-check me-1"></i> {{ __('Duyệt bài đăng') }}
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fas fa-times me-1"></i> {{ __('Từ chối bài đăng') }}
                            </button>
                        @endif

                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-1"></i> {{ __('Xóa bài đăng') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thông tin tác giả') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $thread->user->getAvatarUrl() }}" alt="{{ $thread->user->name }}" class="rounded-circle me-3" width="64" height="64">
                        <div>
                            <h6 class="mb-1">{{ $thread->user->name }}</h6>
                            <p class="mb-0 text-muted">{{ '@' . $thread->user->username }}</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Bài đăng') }}:</span>
                            <span class="badge bg-primary">{{ $thread->user->threads()->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Bình luận') }}:</span>
                            <span class="badge bg-primary">{{ $thread->user->comments()->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>{{ __('Ngày tham gia') }}:</span>
                            <span class="text-muted">{{ $thread->user->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="d-grid">
                        <a href="{{ route('admin.users.show', $thread->user) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-user me-1"></i> {{ __('Xem hồ sơ') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal duyệt bài đăng -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">{{ __('Xác nhận duyệt bài đăng') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Bạn có chắc chắn muốn duyệt bài đăng này?') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                    <form action="{{ route('admin.threads.approve', $thread) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success">{{ __('Duyệt') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal từ chối bài đăng -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">{{ __('Từ chối bài đăng') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.threads.reject', $thread) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reason" class="form-label">{{ __('Lý do từ chối') }}</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                            <div class="form-text">{{ __('Lý do này sẽ được gửi cho tác giả.') }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Từ chối') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal xóa bài đăng -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ __('Xác nhận xóa') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Bạn có chắc chắn muốn xóa bài đăng này?') }}
                    <p class="text-danger mt-2">{{ __('Lưu ý: Hành động này không thể hoàn tác và sẽ xóa tất cả bình luận, phản hồi liên quan.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                    <form action="{{ route('admin.threads.destroy', $thread) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('Xóa') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection
@push('styles')
<style>
    .thread-content img {
        max-width: 100%;
        height: auto;
    }
    .comment-content {
        word-break: break-word;
    }
</style>
@endpush
