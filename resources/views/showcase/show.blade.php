@extends('layouts.app')

@section('title', $showcase->title)

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
<style>
    .showcase-breadcrumb {
        background-color: #f8f9fa;
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1.5rem;
    }

    .showcase-image-gallery img {
        transition: transform 0.2s ease;
        cursor: pointer;
    }

    .showcase-image-gallery img:hover {
        transform: scale(1.05);
    }

    .social-share-buttons .btn {
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="container">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb" class="showcase-breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('showcase.public') }}">Showcases</a></li>
            @if($showcase->showcaseable_type === 'App\Models\Thread' && $showcase->showcaseable->category)
            <li class="breadcrumb-item">
                <a href="{{ route('showcase.public', ['category' => $showcase->showcaseable->category->id]) }}">
                    {{ $showcase->showcaseable->category->name }}
                </a>
            </li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $showcase->title ?? 'Showcase Item' }}</li>
        </ol>
    </nav>

    <div class="row">
        {{-- Cột chính: Nội dung showcase (9 cột) --}}
        <div class="col-md-9">
            {{-- Header với tác giả và thời gian --}}
            <div class="showcase-header mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('profile.show', $showcase->user->username ?? $showcase->user->id) }}"
                            class="me-3">
                            <img src="{{ $showcase->user->getAvatarUrl() }}" class="rounded-circle" width="50"
                                height="50" alt="Avatar của {{ $showcase->user->name }}"
                                onerror="this.src='{{ asset('images/placeholders/50x50.png') }}'">
                        </a>
                        <div>
                            <h6 class="mb-0">
                                <a href="{{ route('profile.show', $showcase->user->username ?? $showcase->user->id) }}"
                                    class="text-decoration-none fw-semibold">
                                    {{ $showcase->user->name }}
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

            {{-- Hình ảnh chính với Gallery Support --}}
            @php
            use App\Services\ShowcaseImageService;
            $featuredImage = ShowcaseImageService::getFeaturedImage($showcase);
            @endphp

            @if($featuredImage)
            <div class="showcase-main-image mb-4">
                <div class="showcase-image-gallery">
                    <a href="{{ $featuredImage->url ?? asset('images/placeholder.svg') }}"
                        data-lightbox="showcase-gallery" data-title="{{ $showcase->title }}">
                        <img src="{{ $featuredImage->url ?? asset('images/placeholder.svg') }}"
                            class="img-fluid rounded shadow" alt="{{ $showcase->title }}"
                            style="max-height: 500px; width: 100%; object-fit: cover;"
                            onerror="this.src='{{ asset('images/placeholder.svg') }}'">
                    </a>
                </div>
            </div>
            @endif

            {{-- Gallery của showcase media --}}
            @if($showcase->media && $showcase->media->count() > 0)
            <div class="showcase-media-gallery mb-4">
                <h5><i class="fas fa-images"></i> Thư viện ảnh</h5>
                <div class="row showcase-image-gallery">
                    @foreach($showcase->media as $media)
                    @if(str_starts_with($media->file_type ?? '', 'image/'))
                    <div class="col-md-4 col-sm-6 mb-3">
                        <a href="{{ $media->url ?? asset('storage/' . $media->file_path) }}"
                            data-lightbox="showcase-gallery" data-title="{{ $media->title ?? $showcase->title }}">
                            <img src="{{ $media->url ?? asset('storage/' . $media->file_path) }}"
                                class="img-fluid rounded shadow-sm" alt="{{ $media->title ?? 'Showcase image' }}"
                                style="height: 200px; width: 100%; object-fit: cover;"
                                onerror="this.src='{{ asset('images/placeholders/300x200.png') }}'">
                        </a>
                    </div>
                    @endif
                    @endforeach
                </div>
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
            @if($showcase->media && $showcase->media->count() > 0)
            <div class="showcase-attachments mb-4">
                <h5><i class="fas fa-paperclip"></i> Tài liệu đính kèm</h5>
                <div class="list-group">
                    @foreach($showcase->media as $mediaItem)
                    <a href="{{ $mediaItem->url }}"
                        class="list-group-item list-group-item-action d-flex align-items-center" target="_blank">
                        <i class="fas fa-file me-2"></i>
                        <div>
                            <div class="fw-semibold">{{ $mediaItem->file_name }}</div>
                            <small class="text-muted">{{ round($mediaItem->file_size / 1024, 2) }} KB</small>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Thống kê tương tác và Social Sharing --}}
            <div class="showcase-stats mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
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

                        {{-- Nút bookmark --}}
                        <form action="{{ route('showcase.bookmark', $showcase) }}" method="POST"
                            class="bookmark-form d-inline">
                            @csrf
                            @php
                            $isBookmarked = auth()->user()->bookmarks()
                            ->where('bookmarkable_type', App\Models\Showcase::class)
                            ->where('bookmarkable_id', $showcase->id)
                            ->exists();
                            @endphp
                            <button type="submit"
                                class="btn btn-sm {{ $isBookmarked ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas {{ $isBookmarked ? 'fa-bookmark' : 'fa-bookmark' }}"></i>
                                {{ $isBookmarked ? 'Đã lưu' : 'Lưu' }}
                            </button>
                        </form>
                        @else
                        <span class="text-muted">
                            <i class="fas fa-heart"></i>
                            {{ $showcase->likesCount() }} Thích
                        </span>
                        @endauth

                        {{-- Các thống kê khác --}}
                        <span class="text-muted">
                            <i class="fas fa-comment"></i>
                            {{ $showcase->commentsCount() }} Bình luận
                        </span>

                        <span class="text-muted">
                            <i class="fas fa-users"></i>
                            {{ $showcase->followsCount() }} Người theo dõi
                        </span>

                        @if($showcase->views_count)
                        <span class="text-muted">
                            <i class="fas fa-eye"></i>
                            {{ number_format($showcase->views_count) }} Lượt xem
                        </span>
                        @endif
                    </div>

                    {{-- Social Sharing Buttons --}}
                    <div class="social-share-buttons">
                        <div class="btn-group" role="group" aria-label="Share options">
                            <button class="btn btn-sm btn-outline-primary" onclick="shareOnFacebook()">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="shareOnTwitter()">
                                <i class="fab fa-twitter"></i> Twitter
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick="shareOnWhatsApp()">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard()">
                                <i class="fas fa-link"></i> Copy Link
                            </button>
                        </div>
                    </div>
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

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
    // Configure Lightbox
lightbox.option({
    'resizeDuration': 200,
    'wrapAround': true,
    'albumLabel': 'Ảnh %1 / %2'
});

// Social Sharing Functions
function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank', 'width=600,height=400');
}

function shareOnWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    window.open(`https://wa.me/?text=${title} ${url}`, '_blank');
}

function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Đã sao chép!';
        button.classList.add('btn-success');
        button.classList.remove('btn-outline-secondary');

        setTimeout(() => {
            button.innerHTML = originalHtml;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}

// Reply form toggle
function toggleReplyForm(commentId) {
    const replyForm = document.getElementById('reply-form-' + commentId);
    if (replyForm.style.display === 'none' || replyForm.style.display === '') {
        replyForm.style.display = 'block';
        replyForm.querySelector('textarea').focus();
    } else {
        replyForm.style.display = 'none';
    }
}

// AJAX Interactions
document.addEventListener('DOMContentLoaded', function() {
    // Handle like button with AJAX
    const likeForm = document.querySelector('.like-form');
    if (likeForm) {
        likeForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const button = this.querySelector('button');
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.innerHTML = `<i class="fas fa-heart"></i> ${data.likes_count} Thích`;
                    button.className = data.is_liked ? 'btn btn-sm btn-danger' : 'btn btn-sm btn-outline-danger';

                    // Update stats in sidebar
                    const statsCard = document.querySelector('.card-body .fw-bold.text-danger');
                    if (statsCard) {
                        statsCard.textContent = data.likes_count;
                    }
                } else {
                    button.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                button.innerHTML = originalText;
            })
            .finally(() => {
                button.disabled = false;
            });
        });
    }

    // Handle bookmark button with AJAX
    const bookmarkForm = document.querySelector('.bookmark-form');
    if (bookmarkForm) {
        bookmarkForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const button = this.querySelector('button');
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = data.is_bookmarked ? 'fa-bookmark' : 'fa-bookmark';
                    const text = data.is_bookmarked ? 'Đã lưu' : 'Lưu';
                    const btnClass = data.is_bookmarked ? 'btn btn-sm btn-warning' : 'btn btn-sm btn-outline-warning';

                    button.innerHTML = `<i class="fas ${icon}"></i> ${text}`;
                    button.className = btnClass;

                    // Show temporary success message
                    const toast = document.createElement('div');
                    toast.className = 'position-fixed top-0 end-0 p-3';
                    toast.style.zIndex = '9999';
                    toast.innerHTML = `
                        <div class="toast show" role="alert">
                            <div class="toast-body bg-success text-white">
                                <i class="fas fa-check me-2"></i>${data.message}
                            </div>
                        </div>
                    `;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                } else {
                    button.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                button.innerHTML = originalText;
            })
            .finally(() => {
                button.disabled = false;
            });
        });
    }

    // Handle follow button with AJAX
    const followForm = document.querySelector('.follow-form');
    if (followForm) {
        followForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const button = this.querySelector('button');
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = data.is_following ? 'fa-user-check' : 'fa-user-plus';
                    const text = data.is_following ? 'Đang theo dõi' : 'Theo dõi';
                    const btnClass = data.is_following ? 'btn btn-sm btn-outline-primary' : 'btn btn-sm btn-primary';

                    button.innerHTML = `<i class="fas ${icon}"></i> ${text}`;
                    button.className = btnClass;

                    // Update follow count in sidebar
                    const followStats = document.querySelector('.card-body .fw-bold.text-success');
                    if (followStats) {
                        followStats.textContent = data.follows_count;
                    }
                } else {
                    button.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                button.innerHTML = originalText;
            })
            .finally(() => {
                button.disabled = false;
            });
        });
    }

    // Auto-expand textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });

    // Smooth scroll to comments
    const commentLinks = document.querySelectorAll('a[href^="#comment-"]');
    commentLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                target.classList.add('highlight');
                setTimeout(() => target.classList.remove('highlight'), 3000);
            }
        });
    });
});

// Add highlight CSS for smooth scroll
const style = document.createElement('style');
style.textContent = `
    .highlight {
        animation: highlight 3s ease-in-out;
    }

    @keyframes highlight {
        0% { background-color: #fff3cd; }
        50% { background-color: #fff3cd; }
        100% { background-color: transparent; }
    }

    .showcase-image-gallery img {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .showcase-image-gallery img:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
`;
document.head.appendChild(style);
</script>
@endpush

@endsection