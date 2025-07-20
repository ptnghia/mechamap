@extends('layouts.app')

@section('title', 'Quản lý Comments - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2">
            @include('components.user-dashboard-sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-comment me-2"></i>
                    Quản lý Comments
                </h1>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('user.comments') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="thread_id" class="form-label">Thread</label>
                            <select name="thread_id" id="thread_id" class="form-select">
                                <option value="">Tất cả threads</option>
                                @foreach($threads as $thread)
                                    <option value="{{ $thread->id }}" {{ request('thread_id') == $thread->id ? 'selected' : '' }}>
                                        {{ Str::limit($thread->title, 50) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('user.comments') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Comments List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Danh sách Comments ({{ $comments->total() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($comments->count() > 0)
                        @foreach($comments as $comment)
                            <div class="comment-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">
                                        <a href="{{ route('threads.show', $comment->thread->slug) }}#comment-{{ $comment->id }}" 
                                           class="text-decoration-none">
                                            {{ Str::limit($comment->thread->title, 60) }}
                                        </a>
                                    </h6>
                                    @php
                                        $statusLabels = [
                                            'approved' => 'Đã duyệt',
                                            'pending' => 'Chờ duyệt',
                                            'rejected' => 'Bị từ chối'
                                        ];
                                        $statusClasses = [
                                            'approved' => 'bg-success',
                                            'pending' => 'bg-warning',
                                            'rejected' => 'bg-danger'
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusClasses[$comment->moderation_status] ?? 'bg-secondary' }}">
                                        {{ $statusLabels[$comment->moderation_status] ?? $comment->moderation_status }}
                                    </span>
                                </div>
                                
                                <div class="comment-content mb-2">
                                    <p class="text-muted mb-1">
                                        {{ Str::limit(strip_tags($comment->content), 200) }}
                                    </p>
                                </div>
                                
                                <div class="comment-meta d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $comment->created_at->diffForHumans() }}
                                        @if($comment->parent_id)
                                            <i class="fas fa-reply ms-2 me-1"></i>
                                            Trả lời comment
                                        @endif
                                        @if($comment->likes_count > 0)
                                            <i class="fas fa-heart ms-2 me-1"></i>
                                            {{ $comment->likes_count }} lượt thích
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('threads.show', $comment->thread->slug) }}#comment-{{ $comment->id }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $comments->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comment fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có comments nào</h5>
                            <p class="text-muted">Tham gia thảo luận bằng cách comment vào các threads!</p>
                            <a href="{{ route('browse.threads.index') }}" class="btn btn-primary">
                                <i class="fas fa-comments"></i> Khám phá Threads
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
