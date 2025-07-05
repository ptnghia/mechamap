@php
// Thiết lập các biến cần thiết cho thread item
$threadUrl = isset($thread->slug) ? route('threads.show', $thread) : '/threads/'.$thread->id;
$userName = $thread->user->name ?? 'Người dùng';
// Sử dụng method getAvatarUrl() để đảm bảo logic nhất quán
$userAvatar = $thread->user->getAvatarUrl();
$threadContent = isset($thread->content) ? strip_tags($thread->content) : '';
$contentPreview = $threadContent ? Str::limit($threadContent, 220) : '';
$commentsCount = $thread->comments_count ?? $thread->cached_comments_count ?? $thread->comment_count ??
($thread->allComments ? $thread->allComments->count() : ($thread->comments ? $thread->comments->count() : 0));
$viewCount = $thread->view_count ?? $thread->views ?? 0;
$createdAt = isset($thread->created_at) && $thread->created_at instanceof \Carbon\Carbon
? $thread->created_at->diffForHumans()
: '';

// Check for thread image - consistent with JavaScript logic
$threadImage = $thread->featured_image ?? $thread->actual_image ?? null;
$hasImage = !empty($threadImage);

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

<div class="thread-item thread-item-container" data-thread-id="{{ $thread->id ?? '' }}">
    <!-- Thread Header với user info và badges -->
    <div class="thread-item-header">
        <div class="thread-user-info">
            <div class="flex-shrink-0 me-3 d-none d-sm-block">
                <img src="{{ $userAvatar }}" alt="{{ $userName }}" class="rounded-circle" width="50" height="50"
                    style="object-fit: cover;"
                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr($userName, 0, 1))) }}&background=6366f1&color=fff&size=200'">
            </div>
            <div>
                <strong class="thread-user-name">{{ $userName }}</strong><br>
                <!-- Status badge nếu có -->
                @if(isset($thread->status) && $thread->status)
                <!--span class="badge bg-light text-dark"><i class="fas fa-info-circle me-1"></i>{{ $thread->status
                    }}</span-->
                @endif
                <span class="d-none d-md-inline text-muted">{{ $createdAt }}</span>

            </div>
        </div>
        <div class="thread-badges">
            @if($thread->is_sticky ?? false)
            <span class="btn btn-sm bg-primary thread_status"><i class="pin-angle"></i>
                Ghim
            </span>
            @endif
            @if($thread->is_locked ?? false)
            <span class="btn btn-sm bg-danger thread_status"><i class="fas fa-lock-fill"></i>
                Khóa
            </span>
            @endif

            <!-- Action buttons cho authenticated users -->
            @if($isAuthenticated)
            <div class="thread-actions">
                @if($isBookmarked)
                <!-- Remove bookmark form -->
                <form method="POST" action="{{ route('threads.bookmark.remove', $thread) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-primary" title="Bỏ bookmark">
                        <i class="far fa-bookmark-fill"></i>
                        <span class="d-none d-md-inline ms-1">Đã lưu</span>
                    </button>
                </form>
                @else
                <!-- Add bookmark form -->
                <form method="POST" action="{{ route('threads.bookmark.add', $thread) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Thêm bookmark">
                        <i class="far fa-bookmark"></i>
                        <span class="d-none d-md-inline ms-1">Lưu</span>
                    </button>
                </form>
                @endif

                @if($isFollowed)
                <!-- Unfollow form -->
                <form method="POST" action="{{ route('threads.follow.remove', $thread) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-success" title="Bỏ theo dõi">
                        <i class="fas fa-bell-fill"></i>
                        <span class="d-none d-md-inline ms-1">Đang theo dõi</span>
                    </button>
                </form>
                @else
                <!-- Follow form -->
                <form method="POST" action="{{ route('threads.follow.add', $thread) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-success" title="Theo dõi">
                        <i class="fas fa-bell"></i>
                        <span class="d-none d-md-inline ms-1">Theo dõi</span>
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>
    </div>

    <div class="row align-items-center">
        <!-- Nội dung chính -->
        <div class="{{ $hasImage ? 'col-md-8' : 'col-12' }}">
            <div class="thread-title-section">
                <div class="thread-title">
                    <a href="{{ $threadUrl }}">{{ $thread->title }}</a>
                </div>
                <small class="text-muted d-md-none">{{ $createdAt }}</small>
            </div>

            <!-- Mô tả ngắn thread -->
            @if($contentPreview)
            <div class="thread-content">{{ $contentPreview }}</div>
            @endif
        </div>

        <!-- Hình ảnh - chỉ hiển thị khi có hình ảnh thực tế -->
        @if($hasImage)
        <div class="col-md-4 d-none d-md-block">
            <div class="thread-image-container">
                <img src="{{ $threadImage }}" alt="{{ $thread->title }}" class="img-fluid rounded"
                    onerror="this.style.display='none'">
            </div>
        </div>
        @endif
    </div>

    <div class="thread-item-footer">
        <div class="thread-meta-left">
            <div class="thread-meta">
                <span class="meta-item"><i class="fas fa-eye"></i> {{ $viewCount }} lượt xem</span>
                <span class="meta-item"><i class="fas fa-comment"></i> {{ $commentsCount }} phản hồi</span>
            </div>

            <div class="thread-category-badges">
                @if(isset($thread->category) && $thread->category)
                <a href="{{ route('threads.index', ['category' => $thread->category->id]) }}"
                    class="badge bg-secondary text-decoration-none">
                    <i class="tag"></i> {{ $thread->category->name }}
                </a>
                @endif

                @if(isset($thread->forum) && $thread->forum)
                <a href="{{ route('threads.index', ['forum' => $thread->forum->id]) }}"
                    class="badge bg-info text-decoration-none">
                    <i class="folder"></i> {{ $thread->forum->name }}
                </a>
                @endif
            </div>
        </div>


    </div>
</div>
