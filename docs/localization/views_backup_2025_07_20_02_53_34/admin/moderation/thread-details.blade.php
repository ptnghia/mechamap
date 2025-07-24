<div class="thread-details">
    <!-- Thread Header -->
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1">{{ $thread->title }}</h5>
            <small class="text-muted">
                <i class="fas fa-user"></i> {{ $thread->user->name ?? 'Unknown' }} |
                <i class="fas fa-calendar"></i> {{ $thread->created_at->format('d/m/Y H:i') }} |
                <i class="fas fa-comments"></i> {{ $thread->comments_count ?? 0 }} bình luận |
                <i class="fas fa-eye"></i> {{ $thread->views ?? 0 }} lượt xem
            </small>
        </div>
        <div>
            @if($thread->is_pinned)
                <span class="badge badge-info"><i class="fas fa-thumbtack"></i> Ghim</span>
            @endif
            @if($thread->is_featured)
                <span class="badge badge-warning"><i class="fas fa-star"></i> Nổi bật</span>
            @endif
            @if($thread->is_flagged)
                <span class="badge badge-danger"><i class="fas fa-flag"></i> Đã báo cáo</span>
            @endif
        </div>
    </div>

    <!-- Thread Status -->
    <div class="row mb-3">
        <div class="col-md-6">
            <strong>Trạng thái kiểm duyệt:</strong>
            @switch($thread->moderation_status)
                @case('approved')
                    <span class="badge badge-success">Đã duyệt</span>
                    @break
                @case('spam')
                    <span class="badge badge-danger">Spam</span>
                    @break
                @case('flagged')
                    <span class="badge badge-warning">Đã báo cáo</span>
                    @break
                @case('under_review')
                    <span class="badge badge-info">Đang xem xét</span>
                    @break
                @default
                    <span class="badge badge-secondary">Chờ duyệt</span>
            @endswitch
        </div>
        <div class="col-md-6">
            <strong>Diễn đàn:</strong> {{ $thread->forum->name ?? 'N/A' }}
        </div>
    </div>

    <!-- Thread Content -->
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-file-text"></i> Nội dung bài viết</h6>
        </div>
        <div class="card-body">
            <div class="thread-content" style="max-height: 300px; overflow-y: auto;">
                {!! $thread->content !!}
            </div>
        </div>
    </div>

    <!-- Thread Tags -->
    @if($thread->tags)
        <div class="mb-3">
            <strong>Tags:</strong>
            @foreach(explode(',', $thread->tags) as $tag)
                <span class="badge badge-light">{{ trim($tag) }}</span>
            @endforeach
        </div>
    @endif

    <!-- Moderation Notes -->
    @if($thread->moderation_notes)
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-sticky-note"></i> Ghi chú kiểm duyệt</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $thread->moderation_notes }}</p>
                @if($thread->flagged_by)
                    <small class="text-muted">
                        Báo cáo bởi: {{ $thread->flaggedBy->name ?? 'Unknown' }} 
                        vào {{ $thread->flagged_at ? $thread->flagged_at->format('d/m/Y H:i') : 'N/A' }}
                    </small>
                @endif
            </div>
        </div>
    @endif

    <!-- Thread Statistics -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body py-2">
                    <h5 class="mb-0">{{ $thread->views ?? 0 }}</h5>
                    <small class="text-muted">Lượt xem</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body py-2">
                    <h5 class="mb-0">{{ $thread->comments_count ?? 0 }}</h5>
                    <small class="text-muted">Bình luận</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body py-2">
                    <h5 class="mb-0">{{ $thread->bookmarks_count ?? 0 }}</h5>
                    <small class="text-muted">Bookmark</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body py-2">
                    <h5 class="mb-0">{{ number_format($thread->ratings->avg('rating') ?? 0, 1) }}</h5>
                    <small class="text-muted">Đánh giá TB</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Comments -->
    @if($thread->comments && $thread->comments->count() > 0)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-comments"></i> Bình luận gần đây ({{ $thread->comments->count() }} bình luận)</h6>
            </div>
            <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                @foreach($thread->comments->take(5) as $comment)
                    <div class="border-bottom pb-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $comment->user->name ?? 'Unknown' }}</strong>
                            <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <p class="mb-1">{{ Str::limit(strip_tags($comment->content), 100) }}</p>
                        @if($comment->is_flagged)
                            <span class="badge badge-danger badge-sm">Đã báo cáo</span>
                        @endif
                    </div>
                @endforeach
                @if($thread->comments->count() > 5)
                    <small class="text-muted">... và {{ $thread->comments->count() - 5 }} bình luận khác</small>
                @endif
            </div>
        </div>
    @endif
</div>
