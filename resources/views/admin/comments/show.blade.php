@extends('admin.layouts.dason')

@section('title', 'Chi tiết bình luận')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Chi tiết bình luận</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Chi tiết bình luận</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.comments.edit', $comment) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-edit me-1"></i> {{ __('Chỉnh sửa') }}
    </a>
    <form action="{{ route('admin.comments.toggle-visibility', $comment) }}" method="POST" class="d-inline">
        @csrf
        @method('PUT')
        <button type="submit" class="btn btn-sm {{ $comment->is_hidden ? 'btn-success' : 'btn-warning' }}">
            @if($comment->is_hidden)
                <i class="fas fa-eye me-1"></i> {{ __('Hiện bình luận') }}
            @else
                <i class="fas fa-eye-slash me-1"></i> {{ __('Ẩn bình luận') }}
            @endif
        </button>
    </form>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Nội dung bình luận') }}</h5>
                    <div>
                        @if($comment->is_flagged)
                            <span class="badge bg-warning">{{ __('Đã đánh dấu') }}</span>
                        @endif
                        
                        @if($comment->is_hidden)
                            <span class="badge bg-danger">{{ __('Đã ẩn') }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $comment->user->getAvatarUrl() }}" alt="{{ $comment->user->name }}" class="rounded-circle me-2" width="40" height="40">
                            <div>
                                <div class="fw-bold">{{ $comment->user->name }}</div>
                                <div class="small text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="comment-content {{ $comment->is_hidden ? 'text-muted fst-italic' : '' }}">
                            {!! $comment->content !!}
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">{{ __('Thông tin chi tiết') }}</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('ID') }}:</span>
                                    <span class="text-muted">{{ $comment->id }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('Bài đăng') }}:</span>
                                    <span class="text-muted">
                                        <a href="{{ route('admin.threads.show', $comment->thread) }}">{{ Str::limit($comment->thread->title, 30) }}</a>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('Bình luận cha') }}:</span>
                                    <span class="text-muted">
                                        @if($comment->parent)
                                            <a href="{{ route('admin.comments.show', $comment->parent) }}">#{{ $comment->parent->id }}</a>
                                        @else
                                            {{ __('Không có') }}
                                        @endif
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('Số lượt thích') }}:</span>
                                    <span class="text-muted">{{ $comment->likes()->count() }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('Ngày tạo') }}:</span>
                                    <span class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ __('Cập nhật lần cuối') }}:</span>
                                    <span class="text-muted">{{ $comment->updated_at->format('d/m/Y H:i') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($comment->replies->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Phản hồi') }} ({{ $comment->replies->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @foreach($comment->replies as $reply)
                            <div class="comment mb-4">
                                <div class="d-flex">
                                    <img src="{{ $reply->user->getAvatarUrl() }}" alt="{{ $reply->user->name }}" class="rounded-circle me-2" width="40" height="40">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <span class="fw-bold">{{ $reply->user->name }}</span>
                                                <span class="text-muted ms-2">{{ $reply->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="replyActions{{ $reply->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="replyActions{{ $reply->id }}">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.comments.show', $reply) }}">
                                                            <i class="fas fa-eye me-2"></i> {{ __('Xem chi tiết') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.comments.edit', $reply) }}">
                                                            <i class="fas fa-edit me-2"></i> {{ __('Chỉnh sửa') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.comments.toggle-visibility', $reply) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="dropdown-item">
                                                                @if($reply->is_hidden)
                                                                    <i class="fas fa-eye me-2"></i> {{ __('Hiện bình luận') }}
                                                                @else
                                                                    <i class="fas fa-eye-slash me-2"></i> {{ __('Ẩn bình luận') }}
                                                                @endif
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="comment-content {{ $reply->is_hidden ? 'text-muted fst-italic' : '' }}">
                                            @if($reply->is_hidden)
                                                <div class="alert alert-warning py-1 px-2 mb-2">
                                                    <small><i class="fas fa-eye-slash me-1"></i> {{ __('Bình luận này đã bị ẩn') }}</small>
                                                </div>
                                            @endif
                                            @if($reply->is_flagged)
                                                <div class="alert alert-danger py-1 px-2 mb-2">
                                                    <small><i class="fas fa-flag-fill me-1"></i> {{ __('Bình luận này đã bị đánh dấu') }}</small>
                                                </div>
                                            @endif
                                            {!! $reply->content !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if(!$loop->last)
                                <hr>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thao tác') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.comments.edit', $comment) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> {{ __('Chỉnh sửa') }}
                        </a>
                        
                        <form action="{{ route('admin.comments.toggle-visibility', $comment) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-block {{ $comment->is_hidden ? 'btn-success' : 'btn-warning' }} w-100">
                                @if($comment->is_hidden)
                                    <i class="fas fa-eye me-1"></i> {{ __('Hiện bình luận') }}
                                @else
                                    <i class="fas fa-eye-slash me-1"></i> {{ __('Ẩn bình luận') }}
                                @endif
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.comments.toggle-flag', $comment) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-block {{ $comment->is_flagged ? 'btn-outline-warning' : 'btn-outline-danger' }} w-100">
                                @if($comment->is_flagged)
                                    <i class="fas fa-flag me-1"></i> {{ __('Bỏ đánh dấu') }}
                                @else
                                    <i class="fas fa-flag-fill me-1"></i> {{ __('Đánh dấu') }}
                                @endif
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-1"></i> {{ __('Xóa bình luận') }}
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
                        <img src="{{ $comment->user->getAvatarUrl() }}" alt="{{ $comment->user->name }}" class="rounded-circle me-3" width="64" height="64">
                        <div>
                            <h6 class="mb-1">{{ $comment->user->name }}</h6>
                            <p class="mb-0 text-muted">{{ '@' . $comment->user->username }}</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Bài đăng') }}:</span>
                            <span class="badge bg-primary">{{ $comment->user->threads()->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Bình luận') }}:</span>
                            <span class="badge bg-primary">{{ $comment->user->comments()->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>{{ __('Ngày tham gia') }}:</span>
                            <span class="text-muted">{{ $comment->user->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="d-grid">
                        <a href="{{ route('admin.users.show', $comment->user) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-user me-1"></i> {{ __('Xem hồ sơ') }}
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Bài đăng liên quan') }}</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-2">
                        <a href="{{ route('admin.threads.show', $comment->thread) }}" class="text-decoration-none">
                            {{ $comment->thread->title }}
                        </a>
                    </h6>
                    <div class="small text-muted mb-3">
                        {{ __('Đăng bởi') }} <a href="{{ route('admin.users.show', $comment->thread->user) }}">{{ $comment->thread->user->name }}</a>
                        {{ __('vào') }} {{ $comment->thread->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="d-grid">
                        <a href="{{ route('admin.threads.show', $comment->thread) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> {{ __('Xem bài đăng') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal xóa bình luận -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ __('Xác nhận xóa') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Bạn có chắc chắn muốn xóa bình luận này?') }}
                    @if($comment->replies->count() > 0)
                        <p class="text-danger mt-2">{{ __('Lưu ý: Hành động này sẽ xóa cả') }} {{ $comment->replies->count() }} {{ __('phản hồi của bình luận này.') }}</p>
                    @endif
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

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection
@push('styles')
<style>
    .comment-content {
        word-break: break-word;
    }
</style>
@endpush
