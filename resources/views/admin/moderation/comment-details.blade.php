<div class="comment-details">
    <div class="row">
        <div class="col-md-8">
            <h5>Chi tiết Comment</h5>
            <div class="card">
                <div class="card-body">
                    <div class="comment-content">
                        {!! nl2br(e($comment->content)) !!}
                    </div>
                    
                    @if($comment->attachments && count($comment->attachments) > 0)
                    <div class="mt-3">
                        <h6>Tệp đính kèm:</h6>
                        @foreach($comment->attachments as $attachment)
                        <div class="attachment-item">
                            <i class="fas fa-paperclip"></i>
                            <a href="{{ $attachment['url'] }}" target="_blank">{{ $attachment['name'] }}</a>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <h5>Thông tin</h5>
            <div class="card">
                <div class="card-body">
                    <div class="info-item">
                        <strong>Tác giả:</strong>
                        <div class="d-flex align-items-center mt-1">
                            <img src="{{ $comment->user->avatar_url ?? '/assets/images/users/default-avatar.png' }}" 
                                 alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                            <div>
                                <div>{{ $comment->user->name }}</div>
                                <small class="text-muted">{{ $comment->user->email }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-item mt-3">
                        <strong>Thread:</strong>
                        <div class="mt-1">
                            <a href="{{ route('threads.show', $comment->thread) }}" target="_blank">
                                {{ $comment->thread->title }}
                            </a>
                        </div>
                    </div>
                    
                    <div class="info-item mt-3">
                        <strong>Diễn đàn:</strong>
                        <div class="mt-1">
                            <span class="badge bg-primary">{{ $comment->thread->forum->name }}</span>
                        </div>
                    </div>
                    
                    <div class="info-item mt-3">
                        <strong>Ngày tạo:</strong>
                        <div class="mt-1">{{ $comment->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    
                    <div class="info-item mt-3">
                        <strong>Trạng thái:</strong>
                        <div class="mt-1">
                            @if($comment->is_spam)
                                <span class="badge bg-danger">Spam</span>
                            @elseif($comment->is_flagged)
                                <span class="badge bg-warning">Từ chối</span>
                            @else
                                <span class="badge bg-success">Đã duyệt</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($comment->reports_count > 0)
                    <div class="info-item mt-3">
                        <strong>Báo cáo:</strong>
                        <div class="mt-1">
                            <span class="badge bg-danger">{{ $comment->reports_count }} báo cáo</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.comment-details .info-item {
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.comment-details .info-item:last-child {
    border-bottom: none;
}

.comment-details .attachment-item {
    padding: 5px 0;
}

.comment-details .attachment-item i {
    margin-right: 5px;
    color: #6c757d;
}

.comment-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}
</style>
