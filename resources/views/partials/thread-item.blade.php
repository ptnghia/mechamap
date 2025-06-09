@php
// Nếu đang ở trong vòng lặp tĩnh, $thread là một Eloquent model
// Nếu đang ở trong JavaScript, $thread là một object đã được chuyển đổi từ JSON
// Do đó, cần xử lý một số thuộc tính khác nhau
$threadUrl = isset($thread->slug) ? route('threads.show', $thread) : ($thread->slug ? '/threads/'.$thread->slug :
'/threads/'.$thread->id);
$userName = isset($thread->user->name) ? $thread->user->name : ($thread->user->name ?? 'Người dùng');
$userAvatar = isset($thread->user->profile_photo_url) ? $thread->user->profile_photo_url : (
function_exists('get_avatar_url') && isset($thread->user) ? get_avatar_url($thread->user) : '/images/default-avatar.png'
);
$threadContent = isset($thread->content) ? strip_tags($thread->content) : '';
$contentPreview = $threadContent ? Str::limit($threadContent, 220) : '';
$commentsCount = isset($thread->allComments) ? $thread->allComments->count() : ($thread->comments_count ?? 0);
$viewCount = $thread->view_count ?? 0;
$createdAt = isset($thread->created_at) && $thread->created_at instanceof \Carbon\Carbon
? $thread->created_at->diffForHumans()
: (isset($thread->created_at) ? $thread->created_at : '');
@endphp

<div class="list-group-item thread-item thread-item-container">
    <!-- Thread Header với user info và badges -->
    <div class="thread-item-header">
        <div class="thread-user-info">
            <div class="flex-shrink-0 me-3 d-none d-sm-block">
                <img src="{{ $userAvatar }}" alt="{{ $userName }}" class="avatar">
            </div>
            <div>
                <strong class="thread-user-name">{{ $userName }}</strong><br>
                <span class="d-none d-md-inline text-muted">{{ $createdAt }}</span>
            </div>
        </div>
        <div class="thread-badges">
            @if($thread->is_sticky ?? false)
            <span class="badge bg-primary"><i class="bi bi-pin-angle"></i> {{ __('messages.thread_status.sticky')
                }}</span>
            @endif
            @if($thread->is_locked ?? false)
            <span class="badge bg-danger"><i class="bi bi-lock-fill"></i> {{ __('messages.thread_status.locked')
                }}</span>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Nội dung chính -->
        <div class="{{ isset($thread->featured_image) && $thread->featured_image ? 'col-md-9' : 'col-12' }}">
            <div class="thread-title-section">
                <div class="thread-title">
                    <a href="{{ $threadUrl }}">{{ $thread->title }}</a>
                </div>
                <small class="text-muted d-md-none">{{ $createdAt }}</small>
            </div>

            <!-- Status badge nếu có -->
            @if(isset($thread->status) && $thread->status)
            <div class="mb-2 small">
                <span class="badge bg-light text-dark"><i class="bi bi-info-circle me-1"></i>{{ $thread->status
                    }}</span>
            </div>
            @endif

            <!-- Mô tả ngắn thread -->
            @if($contentPreview)
            <p class="text-muted small mb-2 thread-content">{{ $contentPreview }}</p>
            @endif
        </div>

        <!-- Hình ảnh - chỉ hiển thị khi có ảnh -->
        @if(isset($thread->featured_image) && $thread->featured_image)
        <div class="col-md-3 d-none d-md-block">
            <div class="thread-image-container">
                <img src="{{ $thread->featured_image }}" alt="{{ $thread->title }}" class="img-fluid rounded"
                    onerror="this.style.display='none'">
            </div>
        </div>
        @endif
    </div>

    <div class="thread-item-footer">
        <div class="thread-meta">
            <span class="meta-item"><i class="bi bi-eye"></i> {{ $viewCount }} lượt xem</span>
            <span class="meta-item"><i class="bi bi-chat"></i> {{ $commentsCount }} phản hồi</span>

            @if(isset($thread->created_at) && !isset($createdAt))
            <span class="meta-item d-none d-md-inline text-muted">{{ $thread->created_at }}</span>
            @endif
        </div>
        <div class="thread-category-badges">
            @if(isset($thread->category) && $thread->category)
            <a href="{{ isset($thread->category->id) ? route('threads.index', ['category' => $thread->category->id]) : '/threads?category='.$thread->category->id }}"
                class="badge bg-secondary text-decoration-none">
                <i class="bi bi-tag"></i> {{ isset($thread->category->name) ? $thread->category->name :
                $thread->category->name }}
            </a>
            @endif

            @if(isset($thread->forum) && $thread->forum)
            <a href="{{ isset($thread->forum->id) ? route('threads.index', ['forum' => $thread->forum->id]) : '/threads?forum='.$thread->forum->id }}"
                class="badge bg-info text-decoration-none">
                <i class="bi bi-folder"></i> {{ isset($thread->forum->name) ? $thread->forum->name :
                $thread->forum->name }}
            </a>
            @endif
        </div>
    </div>
</div>
