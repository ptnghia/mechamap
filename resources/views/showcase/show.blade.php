@extends('layouts.app')

@section('title', $showcase->title)

@section('content')
<div class="container">
    <div class="row">
        {{-- Cột chính: Nội dung showcase (9 cột) --}}
        <div class="col-md-9">
            {{-- Header với tác giả và thời gian --}}
            <div class="showcase-header mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('profile.show', $showcase->user->username) }}" class="me-3">
                            <img src="{{ $showcase->user->avatar_url }}" class="rounded-circle" width="50" height="50"
                                alt="Avatar của {{ $showcase->user->display_name }}">
                        </a>
                        <div>
                            <h6 class="mb-0">
                                <a href="{{ route('profile.show', $showcase->user->username) }}"
                                    class="text-decoration-none fw-semibold">
                                    {{ $showcase->user->display_name }}
                                </a>
                            </h6>
                            <small class="text-muted">
                                {{ $showcase->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>

                    {{-- Nút theo dõi tác giả --}}
                    @auth
                    @if($showcase->user_id !== auth()->id())
                    <form action="{{ route('showcase.toggle-follow', $showcase) }}" method="POST" class="follow-form">
                        @csrf
                        <button type="submit"
                            class="btn btn-sm {{ $showcase->isFollowedBy(auth()->user()) ? 'btn-outline-primary' : 'btn-primary' }}">
                            <i
                                class="fas {{ $showcase->isFollowedBy(auth()->user()) ? 'fa-user-check' : 'fa-user-plus' }}"></i>
                            {{ $showcase->isFollowedBy(auth()->user()) ? 'Đang theo dõi' : 'Theo dõi' }}
                        </button>
                    </form>
                    @endif
                    @endauth
                </div>
            </div>

            {{-- Tiêu đề showcase --}}
            <h1 class="showcase-title mb-3">{{ $showcase->title }}</h1>

            {{-- Mô tả --}}
            @if($showcase->description)
            <div class="showcase-description mb-4">
                <p class="text-muted">{{ $showcase->description }}</p>
            </div>
            @endif

            {{-- Hình ảnh chính --}}
            @if($showcase->image_url)
            <div class="showcase-main-image mb-4">
                <img src="{{ $showcase->image_url }}" class="img-fluid rounded shadow" alt="{{ $showcase->title }}"
                    style="max-height: 500px; width: 100%; object-fit: cover;">
            </div>
            @endif

            {{-- Nội dung chi tiết --}}
            @if($showcase->content)
            <div class="showcase-content mb-4">
                <div class="card">
                    <div class="card-body">
                        {!! nl2br(e($showcase->content)) !!}
                    </div>
                </div>
            </div>
            @endif

            {{-- File đính kèm --}}
            @if($showcase->attachments && $showcase->attachments->count() > 0)
            <div class="showcase-attachments mb-4">
                <h5><i class="fas fa-paperclip"></i> Tài liệu đính kèm</h5>
                <div class="list-group">
                    @foreach($showcase->attachments as $attachment)
                    <a href="{{ route('showcase.download', $attachment) }}"
                        class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-file me-2"></i>
                        <div>
                            <div class="fw-semibold">{{ $attachment->original_name }}</div>
                            <small class="text-muted">{{ $attachment->file_size_formatted }}</small>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Thống kê tương tác --}}
            <div class="showcase-stats mb-4">
                <div class="d-flex align-items-center gap-4">
                    @auth
                    {{-- Nút thích --}}
                    <form action="{{ route('showcase.toggle-like', $showcase) }}" method="POST"
                        class="like-form d-inline">
                        @csrf
                        <button type="submit"
                            class="btn btn-sm {{ $showcase->isLikedBy(auth()->user()) ? 'btn-danger' : 'btn-outline-danger' }}">
                            <i class="fas fa-heart"></i>
                            {{ $showcase->likesCount() }} Thích
                        </button>
                    </form>
                    @else
                    <span class="text-muted">
                        <i class="fas fa-heart"></i>
                        {{ $showcase->likesCount() }} Thích
                    </span>
                    @endauth

                    {{-- Số lượng bình luận --}}
                    <span class="text-muted">
                        <i class="fas fa-comment"></i>
                        {{ $showcase->commentsCount() }} Bình luận
                    </span>

                    {{-- Số lượng người theo dõi --}}
                    <span class="text-muted">
                        <i class="fas fa-users"></i>
                        {{ $showcase->followsCount() }} Người theo dõi
                    </span>

                    {{-- Lượt xem --}}
                    @if($showcase->views_count)
                    <span class="text-muted">
                        <i class="fas fa-eye"></i>
                        {{ number_format($showcase->views_count) }} Lượt xem
                    </span>
                    @endif
                </div>
            </div>

            <hr>

            {{-- Phần bình luận --}}
            <div class="showcase-comments">
                <h5 class="mb-3">
                    <i class="fas fa-comments"></i>
                    Bình luận ({{ $showcase->commentsCount() }})
                </h5>

                {{-- Form thêm bình luận --}}
                @auth
                <div class="comment-form mb-4">
                    <form action="{{ route('showcase.comment', $showcase) }}" method="POST">
                        @csrf
                        <div class="d-flex gap-3">
                            <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" width="40" height="40"
                                alt="Avatar của bạn">
                            <div class="flex-grow-1">
                                <div class="form-floating">
                                    <textarea class="form-control" id="comment-content" name="content"
                                        placeholder="Viết bình luận của bạn..." style="height: 80px;"
                                        required></textarea>
                                    <label for="comment-content">Viết bình luận của bạn...</label>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-paper-plane"></i>
                                        Gửi bình luận
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <a href="{{ route('login') }}">Đăng nhập</a> để bình luận và tương tác với showcase này.
                </div>
                @endauth

                {{-- Danh sách bình luận --}}
                <div class="comments-list">
                    @forelse($comments as $comment)
                    <div class="comment-item mb-3" id="comment-{{ $comment->id }}">
                        <div class="d-flex gap-3">
                            <a href="{{ route('profile.show', $comment->user->username) }}">
                                <img src="{{ $comment->user->avatar_url }}" class="rounded-circle" width="40"
                                    height="40" alt="Avatar của {{ $comment->user->display_name }}">
                            </a>
                            <div class="flex-grow-1">
                                <div class="comment-content bg-light p-3 rounded">
                                    <div class="comment-header mb-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <a href="{{ route('profile.show', $comment->user->username) }}"
                                                    class="fw-semibold text-decoration-none">
                                                    {{ $comment->user->display_name }}
                                                </a>
                                                <small class="text-muted ms-2">
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                            {{-- Nút xóa bình luận (chỉ hiện với chủ sở hữu) --}}
                                            @auth
                                            @if($comment->user_id === auth()->id() || $showcase->user_id ===
                                            auth()->id())
                                            <form action="{{ route('showcase.comment.delete', $comment) }}"
                                                method="POST"
                                                onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @endauth
                                        </div>
                                    </div>
                                    <div class="comment-body">
                                        {!! nl2br(e($comment->content)) !!}
                                    </div>
                                </div>

                                {{-- Bình luận con (nếu có) --}}
                                @if($comment->replies && $comment->replies->count() > 0)
                                <div class="replies mt-3 ms-4">
                                    @foreach($comment->replies as $reply)
                                    <div class="reply-item mb-2">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('profile.show', $reply->user->username) }}">
                                                <img src="{{ $reply->user->avatar_url }}" class="rounded-circle"
                                                    width="30" height="30"
                                                    alt="Avatar của {{ $reply->user->display_name }}">
                                            </a>
                                            <div class="flex-grow-1">
                                                <div class="reply-content bg-white p-2 rounded border">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <a href="{{ route('profile.show', $reply->user->username) }}"
                                                                class="fw-semibold text-decoration-none small">
                                                                {{ $reply->user->display_name }}
                                                            </a>
                                                            <small class="text-muted ms-1">
                                                                {{ $reply->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                        @auth
                                                        @if($reply->user_id === auth()->id() || $showcase->user_id ===
                                                        auth()->id())
                                                        <form action="{{ route('showcase.comment.delete', $reply) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Bạn có chắc muốn xóa phản hồi này?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-trash fa-xs"></i>
                                                            </button>
                                                        </form>
                                                        @endif
                                                        @endauth
                                                    </div>
                                                    <div class="small mt-1">
                                                        {!! nl2br(e($reply->content)) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                {{-- Form trả lời bình luận --}}
                                @auth
                                <div class="reply-form mt-2" style="display: none;" id="reply-form-{{ $comment->id }}">
                                    <form action="{{ route('showcase.comment', $showcase) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        <div class="d-flex gap-2">
                                            <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle"
                                                width="30" height="30" alt="Avatar của bạn">
                                            <div class="flex-grow-1">
                                                <div class="input-group">
                                                    <textarea class="form-control form-control-sm" name="content"
                                                        placeholder="Trả lời bình luận..." rows="2" required></textarea>
                                                    <button type="submit" class="btn btn-primary btn-sm">Gửi</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="comment-actions mt-2">
                                    <button type="button" class="btn btn-link btn-sm text-muted p-0"
                                        onclick="toggleReplyForm({{ $comment->id }})">
                                        <i class="fas fa-reply"></i> Trả lời
                                    </button>
                                </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-comments fa-2x mb-2"></i>
                        <p>Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar (3 cột) --}}
        <div class="col-md-3">
            {{-- Card thông tin showcase --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin Showcase</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-danger">{{ $showcase->likesCount() }}</div>
                                <small class="text-muted">Thích</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-primary">{{ $showcase->commentsCount() }}</div>
                                <small class="text-muted">Bình luận</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-success">{{ $showcase->followsCount() }}</div>
                                <small class="text-muted">Theo dõi</small>
                            </div>
                        </div>
                    </div>

                    @if($showcase->views_count)
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-eye"></i>
                            {{ number_format($showcase->views_count) }} lượt xem
                        </small>
                    </div>
                    @endif

                    <hr>
                    <div>
                        <small class="text-muted">
                            <strong>Đăng lúc:</strong><br>
                            {{ $showcase->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>

                    @if($showcase->updated_at->gt($showcase->created_at))
                    <div class="mt-2">
                        <small class="text-muted">
                            <strong>Cập nhật:</strong><br>
                            {{ $showcase->updated_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Card thông tin tác giả --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-user"></i> Về tác giả</h6>
                </div>
                <div class="card-body text-center">
                    <a href="{{ route('profile.show', $showcase->user->username) }}">
                        <img src="{{ $showcase->user->avatar_url }}" class="rounded-circle mb-3" width="80" height="80"
                            alt="Avatar của {{ $showcase->user->display_name }}">
                    </a>
                    <h6>
                        <a href="{{ route('profile.show', $showcase->user->username) }}" class="text-decoration-none">
                            {{ $showcase->user->display_name }}
                        </a>
                    </h6>
                    @if($showcase->user->title)
                    <p class="text-muted small">{{ $showcase->user->title }}</p>
                    @endif
                    @if($showcase->user->location)
                    <p class="text-muted small">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $showcase->user->location }}
                    </p>
                    @endif

                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <small class="text-muted">
                                <div class="fw-bold">{{ $showcase->user->showcases_count ?? 0 }}</div>
                                <div>Showcases</div>
                            </small>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">
                                <div class="fw-bold">{{ $showcase->user->followers_count ?? 0 }}</div>
                                <div>Người theo dõi</div>
                            </small>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">
                                <div class="fw-bold">{{ $showcase->user->following_count ?? 0 }}</div>
                                <div>Đang theo dõi</div>
                            </small>
                        </div>
                    </div>

                    @auth
                    @if($showcase->user_id !== auth()->id())
                    <div class="mt-3">
                        <a href="{{ route('profile.show', $showcase->user->username) }}"
                            class="btn btn-outline-primary btn-sm me-2">
                            <i class="fas fa-user"></i> Xem hồ sơ
                        </a>
                    </div>
                    @endif
                    @endauth
                </div>
            </div>

            {{-- Card các showcase khác của tác giả --}}
            @if($otherShowcases && $otherShowcases->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-th-large"></i>
                        Showcase khác từ {{ $showcase->user->display_name }}
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($otherShowcases as $otherShowcase)
                    <div class="d-flex align-items-center mb-3">
                        @if($otherShowcase->image_url)
                        <img src="{{ $otherShowcase->image_url }}" class="rounded me-2" width="50" height="40"
                            style="object-fit: cover;" alt="{{ $otherShowcase->title }}">
                        @else
                        <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 40px;">
                            <i class="fas fa-image text-muted"></i>
                        </div>
                        @endif
                        <div class="flex-grow-1">
                            <a href="{{ route('showcase.show', $otherShowcase) }}"
                                class="text-decoration-none small fw-semibold">
                                {{ Str::limit($otherShowcase->title, 40) }}
                            </a>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                {{ $otherShowcase->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="text-center">
                        <a href="{{ route('profile.show', $showcase->user->username) }}"
                            class="btn btn-outline-primary btn-sm">
                            Xem tất cả showcase
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- JavaScript cho các tương tác --}}
<script>
    function toggleReplyForm(commentId) {
    const replyForm = document.getElementById('reply-form-' + commentId);
    if (replyForm.style.display === 'none' || replyForm.style.display === '') {
        replyForm.style.display = 'block';
        replyForm.querySelector('textarea').focus();
    } else {
        replyForm.style.display = 'none';
    }
}

// AJAX cho like và follow
document.addEventListener('DOMContentLoaded', function() {
    // Handle like button
    const likeForm = document.querySelector('.like-form');
    if (likeForm) {
        likeForm.addEventListener('submit', function(e) {
            e.preventDefault();

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const button = this.querySelector('button');
                    button.innerHTML = `<i class="fas fa-heart"></i> ${data.likes_count} Thích`;
                    button.className = data.is_liked ? 'btn btn-sm btn-danger' : 'btn btn-sm btn-outline-danger';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // Handle follow button
    const followForm = document.querySelector('.follow-form');
    if (followForm) {
        followForm.addEventListener('submit', function(e) {
            e.preventDefault();

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const button = this.querySelector('button');
                    const icon = data.is_following ? 'fa-user-check' : 'fa-user-plus';
                    const text = data.is_following ? 'Đang theo dõi' : 'Theo dõi';
                    const btnClass = data.is_following ? 'btn btn-sm btn-outline-primary' : 'btn btn-sm btn-primary';

                    button.innerHTML = `<i class="fas ${icon}"></i> ${text}`;
                    button.className = btnClass;
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
});
</script>
@endsection