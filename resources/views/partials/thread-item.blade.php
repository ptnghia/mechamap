@php
// Configuration variables cho layout variations
$variant = $variant ?? 'default'; // default, compact, forum, whats-new
$showBookmark = $showBookmark ?? true;
$showFollow = $showFollow ?? true;
$showProjectDetails = $showProjectDetails ?? false;
$showContentPreview = $showContentPreview ?? true;
$showUserInfo = $showUserInfo ?? true;
$customActions = $customActions ?? []; // Array của custom action buttons

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
$commentsCount = isset($thread->allComments) ? $thread->allComments->count() : ($thread->comments_count ??
$thread->comment_count ?? 0);
$viewCount = $thread->view_count ?? $thread->views ?? 0;
$createdAt = isset($thread->created_at) && $thread->created_at instanceof \Carbon\Carbon
? $thread->created_at->diffForHumans()
: (isset($thread->created_at) ? $thread->created_at : '');

// Column layout dựa trên variant
$contentColClass = match($variant) {
'forum' => $thread->featured_image ? 'col-md-6' : 'col-md-9',
'compact' => 'col-12',
default => $thread->featured_image ? 'col-md-9' : 'col-12'
};

$imageColClass = match($variant) {
'forum' => 'col-md-3',
'compact' => 'd-none',
default => 'col-md-3'
};

$statsColClass = $variant === 'forum' ? 'col-md-3' : '';

// User authentication checks
$isAuthenticated = Auth::check();
$isBookmarked = false;
$isFollowed = false;

if ($isAuthenticated && isset($thread->id)) {
$user = Auth::user();
$isBookmarked = \App\Models\ThreadBookmark::where('user_id', $user->id)
->where('thread_id', $thread->id)
->exists();
$isFollowed = \App\Models\ThreadFollow::where('user_id', $user->id)
->where('thread_id', $thread->id)
->exists();
}
@endphp

<div class="list-group-item thread-item thread-item-container" data-thread-id="{{ $thread->id ?? '' }}">
    @if($showUserInfo && $variant !== 'compact')
    <!-- Thread Header với user info và badges -->
    <div class="thread-item-header">
        <div class="thread-user-info">
            <div class="flex-shrink-0 me-3 d-none d-sm-block">
                <img src="{{ $userAvatar }}" alt="{{ $userName }}"
                    class="{{ $variant === 'forum' ? 'rounded-circle' : 'avatar' }}" @if($variant==='forum' ) width="50"
                    height="50" @endif>
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
    @endif

    <div class="row">
        <!-- Nội dung chính -->
        <div class="{{ $contentColClass }}">
            @if($variant === 'compact' || $variant === 'whats-new')
            <!-- Inline user info cho compact và whats-new variants -->
            <div class="d-flex">
                <div class="flex-shrink-0 me-3 d-none d-sm-block">
                    <img src="{{ $userAvatar }}" alt="{{ $userName }}" class="avatar avatar-md">
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="thread-title">
                            <a href="{{ $threadUrl }}">{{ $thread->title }}</a>
                            @if($thread->is_sticky ?? false)
                            <span class="badge bg-primary ms-1">{{ __('messages.thread_status.sticky') }}</span>
                            @endif
                            @if($thread->is_locked ?? false)
                            <span class="badge bg-danger ms-1">{{ __('messages.thread_status.locked') }}</span>
                            @endif
                        </div>
                        <small class="text-muted d-md-none">{{ $createdAt }}</small>
                    </div>
                </div>
            </div>
            @else
            <!-- Default title section -->
            <div class="thread-title-section">
                <div class="thread-title">
                    <a href="{{ $threadUrl }}">{{ $thread->title }}</a>
                </div>
                <small class="text-muted d-md-none">{{ $createdAt }}</small>
            </div>
            @endif

            <!-- Status badge nếu có -->
            @if(isset($thread->status) && $thread->status)
            <div class="mb-2 small">
                <span class="badge bg-light text-dark"><i class="bi bi-info-circle me-1"></i>{{ $thread->status
                    }}</span>
            </div>
            @endif

            <!-- Project Details - chỉ hiển thị khi được yêu cầu -->
            @if($showProjectDetails && ($thread->location || $thread->usage || $thread->floors || $thread->status))
            <div class="project-details mb-2 small">
                @if($thread->location)
                <span class="badge bg-light text-dark me-2">{{ $thread->location }}</span>
                @endif
                @if($thread->usage)
                <span class="badge bg-light text-dark me-2">{{ $thread->usage }}</span>
                @endif
                @if($thread->floors)
                <span class="badge bg-light text-dark me-2">{{ $thread->floors }} tầng</span>
                @endif
                @if($thread->status)
                <span class="badge bg-light text-dark me-2">{{ $thread->status }}</span>
                @endif
            </div>
            @endif

            <!-- Mô tả ngắn thread - tùy chọn hiển thị -->
            @if($showContentPreview && $contentPreview && $variant !== 'forum')
            <p class="text-muted small mb-2 thread-content">{{ $contentPreview }}</p>
            @endif
        </div>

        <!-- Hình ảnh - chỉ hiển thị khi có ảnh và không phải compact -->
        @if(isset($thread->featured_image) && $thread->featured_image && $variant !== 'compact')
        <div class="{{ $imageColClass }} d-none d-md-block">
            <div class="thread-image-container">
                <img src="{{ $thread->featured_image }}" alt="{{ $thread->title }}" class="img-fluid rounded"
                    onerror="this.style.display='none'">
            </div>
        </div>
        @endif

        <!-- Stats column cho forum variant -->
        @if($variant === 'forum' && $statsColClass)
        <div class="{{ $statsColClass }}">
            <div class="row text-md-end">
                <div class="col-6">
                    <div class="fw-bold">{{ $commentsCount }}</div>
                    <div class="small text-muted">{{ __('messages.replies') }}</div>
                </div>
                <div class="col-6">
                    <div class="fw-bold">{{ $viewCount }}</div>
                    <div class="small text-muted">{{ __('Views') }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="thread-item-footer">
        <div class="thread-meta-left">
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

        <!-- Action buttons cho authenticated users -->
        @if($isAuthenticated && ($showBookmark || $showFollow || !empty($customActions)))
        <div class="thread-actions">
            <!-- Custom actions -->
            @if(!empty($customActions))
            @foreach($customActions as $action)
            <a href="{{ $action['url'] }}" class="btn {{ $action['class'] ?? 'btn-sm btn-primary' }}"
                title="{{ $action['title'] ?? $action['label'] }}">
                @if(isset($action['icon']))
                <i class="bi {{ $action['icon'] }}"></i>
                @endif
                {{ $action['label'] }}
            </a>
            @endforeach
            @endif

            @if($showBookmark)
            @if($isBookmarked)
            <!-- Remove bookmark form -->
            <form method="POST" action="{{ route('threads.bookmark.remove', $thread) }}" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-primary" title="Bỏ bookmark">
                    <i class="bi bi-bookmark-fill"></i>
                    <span class="d-none d-md-inline ms-1">Đã lưu</span>
                </button>
            </form>
            @else
            <!-- Add bookmark form -->
            <form method="POST" action="{{ route('threads.bookmark.add', $thread) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary" title="Thêm bookmark">
                    <i class="bi bi-bookmark"></i>
                    <span class="d-none d-md-inline ms-1">Lưu</span>
                </button>
            </form>
            @endif
            @endif

            @if($showFollow)
            @if($isFollowed)
            <!-- Unfollow form -->
            <form method="POST" action="{{ route('threads.follow.remove', $thread) }}" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-success" title="Bỏ theo dõi">
                    <i class="bi bi-bell-fill"></i>
                    <span class="d-none d-md-inline ms-1">Đang theo dõi</span>
                </button>
            </form>
            @else
            <!-- Follow form -->
            <form method="POST" action="{{ route('threads.follow.add', $thread) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-success" title="Theo dõi">
                    <i class="bi bi-bell"></i>
                    <span class="d-none d-md-inline ms-1">Theo dõi</span>
                </button>
            </form>
            @endif
            @endif
        </div>
        @endif
    </div>
</div>