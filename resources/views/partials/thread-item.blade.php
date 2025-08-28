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

<div class="thread-item thread-item-container" data-thread-id="{{ $thread->id ?? '' }}" data-thread-slug="{{ $thread->slug ?? '' }}">
    <!-- Thread Header với user info và badges -->
    <div class="thread-item-header">
        <div class="thread-user-info">
            <div class="flex-shrink-0 me-3 d-none d-sm-block">
                <img src="{{ $userAvatar }}" alt="{{ $userName }}" class="rounded-circle" width="50" height="50"
                    style="object-fit: cover;">
            </div>
            <div>
                <strong class="thread-user-name">
                    @if(isset($thread->user) && $thread->user->username)
                        <a href="{{ route('profile.show', $thread->user->username) }}" class="text-decoration-none">
                            {{ $userName }}
                        </a>
                    @else
                        {{ $userName }}
                    @endif
                </strong>
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
            <span class="btn btn-sm bg-primary thread_status">
                <i class="fa-solid fa-thumbtack"></i>
                {{ t_forums('status.pinned') }}
            </span>
            @endif
            @if($thread->is_locked ?? false)
            <span class="btn btn-sm bg-danger thread_status">
                <i class="fa-solid fa-lock"></i>
                {{ t_forums('status.locked') }}
            </span>
            @endif

            <!-- Action buttons cho authenticated users -->
            @if($isAuthenticated)
            <div class="thread-actions d-flex gx-2">
                <!-- Bookmark button -->
                <button type="button"
                        class="btn btn-sm {{ $isBookmarked ? 'btn-primary active' : 'btn-outline-primary' }} btn-bookmark me-2"
                        data-thread-id="{{ $thread->id }}"
                        data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}"
                        title="{{ $isBookmarked ? t_forums('actions.bookmark_remove') : t_forums('actions.bookmark_add') }}">
                    <i class="{{ $isBookmarked ? 'fas fa-bookmark' : 'far fa-bookmark' }}"></i>
                    <span class="d-none d-md-inline ms-1 bookmark-text">
                        {{ $isBookmarked ? t_forums('actions.bookmarked') : t_forums('actions.bookmark') }}
                    </span>
                </button>

                <!-- Follow button -->
                <button type="button"
                        class="btn btn-sm {{ $isFollowed ? 'btn-success active' : 'btn-outline-success' }} btn-follow"
                        data-thread-id="{{ $thread->id }}"
                        data-followed="{{ $isFollowed ? 'true' : 'false' }}"
                        title="{{ $isFollowed ? t_forums('actions.unfollow_thread') : t_forums('actions.follow_thread') }}">
                    <i class="{{ $isFollowed ? 'fas fa-bell' : 'far fa-bell' }}"></i>
                    <span class="d-none d-md-inline ms-1 follow-text">
                        {{ $isFollowed ? t_forums('actions.following') : t_forums('actions.follow') }}
                    </span>
                </button>
            </div>
            @endif
        </div>
    </div>

    <div class="row align-items-center">
        <!-- Nội dung chính -->
        <div class="{{ $hasImage ? 'col-md-9 col-sm-8 col-9' : 'col-12' }}">
            <div class="thread-title-section">
                <div class="thread-title">
                    <a href="{{ $threadUrl }}">
                        @if((request()->routeIs('forums.search') || request()->routeIs('threads.index')) && str_contains($thread->title, '<span class="highlight">'))
                            {!! $thread->title !!}
                        @else
                            {{ $thread->title }}
                        @endif
                    </a>
                </div>
                <small class="text-muted d-md-none">{{ $createdAt }}</small>
            </div>

            <!-- Mô tả ngắn thread -->
            @if($contentPreview)
            <div class="thread-content">
                @if((request()->routeIs('forums.search') || request()->routeIs('threads.index')) && isset($thread->has_highlighted_content) && $thread->has_highlighted_content)
                    {!! $thread->content !!}
                @else
                    {{ $contentPreview }}
                @endif
            </div>
            @endif
        </div>

        <!-- Hình ảnh - chỉ hiển thị khi có hình ảnh thực tế -->
        @if($hasImage)
        <div class="col-md-3 col-sm-4 col-3">
            <div class="thread-image-container">
                <img src="{{ $threadImage }}" alt="{{ $thread->original_title ?? strip_tags($thread->title) }}" class="img-fluid rounded"
                    onerror="this.style.display='none'">
            </div>
        </div>
        @endif
    </div>

    <div class="thread-item-footer">
        <div class="thread-meta-left">
            <div class="thread-meta">
                <span class="meta-item"><i class="fas fa-eye"></i> {{ $viewCount }} {{ t_forums('meta.views') }}</span>
                <span class="meta-item"><i class="fas fa-comment"></i> {{ $commentsCount }} {{ t_forums('meta.replies') }}</span>
            </div>

            <div class="thread-category-badges">
                @if(isset($thread->category) && $thread->category && $thread->category->slug)
                <a href="{{ route('categories.show', $thread->category->slug) }}" class="badge bg-secondary text-decoration-none">
                    <i class="fa-solid fa-tag"></i>
                    {{ $thread->category->name }}
                </a>
                @endif

                @if(isset($thread->forum) && $thread->forum && $thread->forum->slug)
                <a href="{{ route('forums.show', $thread->forum->slug) }}" class="badge bg-info text-decoration-none">
                    <i class="fa-solid fa-folder-open"></i>
                     {{ $thread->forum->name }}
                </a>
                @endif
            </div>
        </div>

    </div>
</div>
