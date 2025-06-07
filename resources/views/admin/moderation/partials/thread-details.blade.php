{{-- Thread Details Modal Content --}}
<div class="thread-details">
    {{-- Thread Header --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <h5 class="mb-2">{{ $thread->title }}</h5>
            <div class="d-flex align-items-center mb-3">
                <img src="{{ $thread->user->avatar ?? 'https://ui-avatars.cc/api/?name=' . urlencode($thread->user->name) }}"
                    alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                <div>
                    <strong>{{ $thread->user->name }}</strong>
                    <br>
                    <small class="text-muted">{{ $thread->user->email }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-end">
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
            <div class="mb-2">
                <span class="badge {{ $statusClasses[$thread->moderation_status] ?? 'bg-secondary' }} fs-6">
                    {{ $statusLabels[$thread->moderation_status] ?? $thread->moderation_status }}
                </span>
            </div>
            <div class="text-muted small">
                <div><i class="fas fa-calendar me-1"></i> {{ $thread->created_at->format('d/m/Y H:i') }}</div>
                <div><i class="fas fa-eye me-1"></i> {{ $thread->views_count ?? 0 }} lượt xem</div>
                <div><i class="fas fa-comment me-1"></i> {{ $thread->comments_count ?? 0 }} comments</div>
            </div>
        </div>
    </div>

    {{-- Thread Meta Information --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0">Thông tin chung</h6>
                </div>
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-6">
                            <strong>Chủ đề:</strong><br>
                            <span class="badge bg-secondary">{{ $thread->forum->name }}</span>
                        </div>
                        <div class="col-6">
                            <strong>Thread Type:</strong><br>
                            <span class="badge bg-info">{{ ucfirst($thread->thread_type ?? 'discussion') }}</span>
                        </div>
                    </div>
                    @if($thread->tags->count() > 0)
                    <div class="mt-2">
                        <strong>Tags:</strong><br>
                        @foreach($thread->tags as $tag)
                        <span class="badge bg-light text-dark me-1">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    @if($thread->priority && $thread->priority !== 'normal')
                    <div class="mt-2">
                        <strong>Độ ưu tiên:</strong>
                        <span class="badge bg-danger">{{ ucfirst($thread->priority) }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            @if($thread->reports && $thread->reports->count() > 0)
            <div class="card">
                <div class="card-header py-2 bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-flag me-1"></i>
                        Báo cáo ({{ $thread->reports->count() }})
                    </h6>
                </div>
                <div class="card-body py-2">
                    @foreach($thread->reports->take(3) as $report)
                    <div class="mb-2 {{ !$loop->last ? 'border-bottom pb-2' : '' }}">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $report->reporter->name }}</strong>
                            <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="small">{{ $report->reason }}</div>
                    </div>
                    @endforeach
                    @if($thread->reports->count() > 3)
                    <small class="text-muted">và {{ $thread->reports->count() - 3 }} báo cáo khác...</small>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Thread Image (nếu có) --}}
    @if($thread->image)
    <div class="mb-4">
        <h6>Hình ảnh đính kèm:</h6>
        <img src="{{ $thread->image }}" alt="Thread image" class="img-fluid rounded" style="max-height: 300px;">
    </div>
    @endif

    {{-- Thread Content --}}
    <div class="mb-4">
        <h6>Nội dung:</h6>
        <div class="border rounded p-3 bg-light">
            {!! nl2br(e($thread->content)) !!}
        </div>
    </div>

    {{-- Moderation History --}}
    @if($thread->moderation_logs && $thread->moderation_logs->count() > 0)
    <div class="mb-4">
        <h6>Lịch sử moderation:</h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Moderator</th>
                        <th>Hành động</th>
                        <th>Lý do</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($thread->moderation_logs->take(5) as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->moderator->name ?? 'System' }}</td>
                        <td>
                            <span class="badge bg-info">{{ ucfirst($log->action) }}</span>
                        </td>
                        <td>{{ $log->reason ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Recent Comments --}}
    @if($thread->comments && $thread->comments->count() > 0)
    <div class="mb-4">
        <h6>Comments gần đây ({{ $thread->comments->count() }}/{{ $thread->comments_count ?? 0 }}):</h6>
        @foreach($thread->comments as $comment)
        <div class="border rounded p-2 mb-2 bg-light">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="d-flex align-items-center">
                    <img src="{{ $comment->user->avatar ?? 'https://ui-avatars.cc/api/?name=' . urlencode($comment->user->name) }}"
                        alt="Avatar" class="rounded-circle me-2" style="width: 24px; height: 24px;">
                    <strong>{{ $comment->user->name }}</strong>
                </div>
                <div>
                    @php
                    $commentStatusClasses = [
                    'pending' => 'bg-warning',
                    'approved' => 'bg-success',
                    'rejected' => 'bg-danger',
                    'spam' => 'bg-dark'
                    ];
                    @endphp
                    <span
                        class="badge {{ $commentStatusClasses[$comment->moderation_status] ?? 'bg-secondary' }} badge-sm">
                        {{ ucfirst($comment->moderation_status) }}
                    </span>
                    <small class="text-muted ms-1">{{ $comment->created_at->diffForHumans() }}</small>
                </div>
            </div>
            <div class="small">
                {{ Str::limit(strip_tags($comment->content), 150) }}
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- User History Summary --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0">Thông tin tác giả</h6>
                </div>
                <div class="card-body py-2">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="text-primary">
                                <i class="fas fa-comments fa-lg"></i>
                            </div>
                            <div><strong>{{ $thread->user->threads_count ?? 0 }}</strong></div>
                            <small class="text-muted">Threads</small>
                        </div>
                        <div class="col-6">
                            <div class="text-success">
                                <i class="fas fa-comment fa-lg"></i>
                            </div>
                            <div><strong>{{ $thread->user->comments_count ?? 0 }}</strong></div>
                            <small class="text-muted">Comments</small>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="small">
                        <div><strong>Đăng ký:</strong> {{ $thread->user->created_at->format('d/m/Y') }}</div>
                        <div><strong>Hoạt động cuối:</strong> {{ $thread->user->last_activity_at ?
                            $thread->user->last_activity_at->diffForHumans() : 'Chưa xác định' }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0">Tương tác</h6>
                </div>
                <div class="card-body py-2">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="text-info">
                                <i class="fas fa-thumbs-up fa-lg"></i>
                            </div>
                            <div><strong>{{ $thread->likes_count ?? 0 }}</strong></div>
                            <small class="text-muted">Likes</small>
                        </div>
                        <div class="col-4">
                            <div class="text-warning">
                                <i class="fas fa-star fa-lg"></i>
                            </div>
                            <div><strong>{{ number_format($thread->average_rating ?? 0, 1) }}</strong></div>
                            <small class="text-muted">Rating</small>
                        </div>
                        <div class="col-4">
                            <div class="text-secondary">
                                <i class="fas fa-bookmark fa-lg"></i>
                            </div>
                            <div><strong>{{ $thread->bookmarks_count ?? 0 }}</strong></div>
                            <small class="text-muted">Saves</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-resize modal content if needed
$(document).ready(function() {
    setTimeout(() => {
        const modal = $('#threadDetailsModal');
        if (modal.length && modal.hasClass('show')) {
            const maxHeight = $(window).height() - 120;
            modal.find('.modal-body').css('max-height', maxHeight + 'px').css('overflow-y', 'auto');
        }
    }, 100);
});
</script>

<style>
    .badge-sm {
        font-size: 0.65em;
    }

    .thread-details .card {
        border: 1px solid #dee2e6;
    }

    .thread-details .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .thread-details .table-sm td,
    .thread-details .table-sm th {
        padding: 0.3rem;
        font-size: 0.85em;
    }
</style>