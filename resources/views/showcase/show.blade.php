@extends('layouts.app')

@section('title', $showcase->title)

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/thread-detail.css') }}">
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/showcase/show.css') }}">
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/showcase-comments.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 col-md-12">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb" class="showcase-breadcrumb mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('showcase.index') }}">Showcases</a></li>
            @if($showcase->showcaseable_type === 'App\Models\Thread' && $showcase->showcaseable->category)
            <li class="breadcrumb-item">
                <a href="{{ route('showcase.index', ['category' => $showcase->showcaseable->category->id]) }}">
                    {{ $showcase->showcaseable->category->name }}
                </a>
            </li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $showcase->title ?? 'Showcase Item' }}</li>
        </ol>
    </nav>

    {{-- Main Showcase Card (giống threads layout) --}}
    <div class="card mb-4">
        {{-- Showcase Header (giống thread header) --}}
        <div class="thread-header p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="thread-title">{{ $showcase->title }}</h1>

                <div class="thread-actions">
                    <a href="#comments" class="btn-jump">
                        <i class="fas fa-arrow-right"></i> Đến bình luận
                    </a>

                    @auth
                    @if($showcase->user_id !== auth()->id())
                    <form action="{{ route('showcase.toggle-follow', $showcase) }}" method="POST" class="follow-form d-inline">
                        @csrf
                        <button type="submit" class="btn-follow">
                            <i class="{{ $showcase->isFollowedBy(auth()->user()) ? 'fas fa-bell-fill' : 'fas fa-bell' }}"></i>
                            {{ $showcase->isFollowedBy(auth()->user()) ? 'Đang theo dõi' : 'Theo dõi' }}
                        </button>
                    </form>
                    @endif
                    @endauth
                </div>
            </div>

            {{-- Showcase Meta (giống thread meta) --}}
            <div class="thread-meta">
                <div class="d-flex justify-content-start g-3">
                    <div class="thread-meta-item">
                        <i class="fas fa-eye"></i> {{ number_format($showcase->view_count ?? 0) }} Lượt xem
                    </div>
                    <div class="thread-meta-item">
                        <i class="fas fa-star"></i> {{ number_format($showcase->average_rating, 1) }} Đánh giá
                    </div>
                    <div class="thread-meta-item">
                        <i class="fas fa-comment"></i> {{ number_format($showcase->commentsCount()) }} Bình luận
                    </div>
                    <div class="thread-meta-item">
                        <i class="heart"></i> {{ number_format($showcase->likesCount()) }} Thích
                    </div>
                </div>
            </div>
        </div>

        {{-- Author Info (giống thread author) --}}
        <div class="card-header d-flex justify-content-between align-items-center showcase-card-header">
            <div class="d-flex align-items-center">
                <img src="{{ $showcase->user->getAvatarUrl() }}" alt="{{ $showcase->user->name }}"
                    class="rounded-circle me-2" width="40" height="40"
                    onerror="this.src='{{ asset('images/placeholders/50x50.png') }}'">
                <div>
                    <a href="{{ route('profile.show', $showcase->user->username ?? $showcase->user->id) }}"
                        class="fw-bold text-decoration-none">{{ $showcase->user->name }}</a>
                    <div class="text-muted small">
                        <span>{{ $showcase->user->showcases_count ?? 0 }} Showcases</span> ·
                        <span>Tham gia {{ $showcase->user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="text-muted small">
                #1 · {{ $showcase->created_at->diffForHumans() }}
            </div>
        </div>

        {{-- Main Content --}}
        <div class="card-body">

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
                        data-fancybox="showcase-gallery" data-caption="{{ $showcase->title }}">
                        <img src="{{ $featuredImage->url ?? asset('images/placeholder.svg') }}"
                            class="img-fluid rounded shadow" alt="{{ $showcase->title }}"
                            class="showcase-featured-image"
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
                            data-fancybox="showcase-gallery" data-caption="{{ $media->title ?? $showcase->title }}">
                            <img src="{{ $media->url ?? asset('storage/' . $media->file_path) }}"
                                class="img-fluid rounded shadow-sm" alt="{{ $media->title ?? 'Showcase image' }}"
                                class="showcase-gallery-image"
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

            {{-- Tab Navigation --}}
            <div class="showcase-tabs">
                <ul class="nav nav-tabs" id="showcaseTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="ratings-tab" data-bs-toggle="tab" data-bs-target="#ratings"
                            type="button" role="tab" aria-controls="ratings" aria-selected="true">
                            <i class="fas fa-star"></i>
                            Đánh giá ({{ $showcase->ratings_count ?? 0 }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="comments-tab" data-bs-toggle="tab" data-bs-target="#comments"
                            type="button" role="tab" aria-controls="comments" aria-selected="false">
                            <i class="fas fa-comments"></i>
                            Bình luận ({{ $showcase->commentsCount() }})
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="showcaseTabsContent">
                    {{-- Tab Đánh giá --}}
                    <div class="tab-pane fade show active" id="ratings" role="tabpanel" aria-labelledby="ratings-tab">
                        <div class="tab-content-wrapper">
                            {{-- Rating System --}}
                            @include('showcases.partials.rating', ['showcase' => $showcase])

                            <hr>

                            {{-- Danh sách đánh giá --}}
                            <div class="showcase-ratings">
                                <div class="ratings-list">
                                    @php
                                        $ratings = $showcase->ratings()->with('user')->latest()->get();
                                    @endphp

                                    @forelse($ratings as $rating)
                                        @include('showcases.partials.rating-item', ['rating' => $rating, 'showcase' => $showcase])
                                    @empty
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-star fa-2x mb-2"></i>
                                            <p>Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá showcase này!</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab Bình luận --}}
                    <div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="comments-tab">
                        <div class="tab-content-wrapper">
                            <div class="comments-section">
                                <div class="comments-header">
                                    <h5>
                                        <i class="fas fa-comments"></i>
                                        Bình luận ({{ $comments->count() }})
                                    </h5>
                                </div>
                                <div class="comments-body">

                    {{-- Form thêm bình luận --}}
                    @auth
                    <div class="comment-form">
                        <form action="{{ route('showcase.comment', $showcase) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="d-flex gap-3">
                                <img src="{{ auth()->user()->getAvatarUrl() }}"
                                     class="user-avatar"
                                     alt="Avatar của bạn"
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr(auth()->user()->name, 0, 1))) }}&background=6366f1&color=fff&size=48'">
                                <div class="flex-grow-1">
                                    <x-ckeditor5-comment
                                        name="content"
                                        placeholder="Viết bình luận của bạn..."
                                        id="main-comment-editor"
                                        :required="true"
                                        minHeight="100px"
                                    />

                                    {{-- Enhanced Image Upload --}}
                                    <x-enhanced-image-upload
                                        name="images"
                                        id="main-comment-upload"
                                        :maxFiles="5"
                                        :maxSize="5"
                                    />

                                    <div class="comment-form-actions">
                                        <div class="comment-form-info">
                                            <i class="fas fa-info-circle"></i>
                                            Hỗ trợ định dạng văn bản và ảnh
                                        </div>
                                        <button type="submit" class="comment-submit-btn">
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
                        <div class="comment-item" id="comment-{{ $comment->id }}">
                            <div class="comment-header">
                                <a href="{{ route('profile.show', $comment->user->username) }}">
                                    <img src="{{ $comment->user->getAvatarUrl() }}"
                                         class="comment-avatar"
                                         alt="Avatar của {{ $comment->user->display_name }}"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr($comment->user->name, 0, 1))) }}&background=6366f1&color=fff&size=44'">
                                </a>
                                <div class="comment-user-info">
                                    <h6 class="comment-username">
                                        <a href="{{ route('profile.show', $comment->user->username) }}" class="text-decoration-none">
                                            {{ $comment->user->display_name }}
                                        </a>
                                    </h6>
                                    <p class="comment-timestamp">
                                        <i class="fas fa-clock"></i>
                                        {{ $comment->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="comment-options">
                                    {{-- Nút xóa bình luận (chỉ hiện với chủ sở hữu) --}}
                                    @auth
                                    @if($comment->user_id === auth()->id() || $showcase->user_id === auth()->id())
                                    <form action="{{ route('showcase.comment.delete', $comment) }}"
                                          method="POST"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?')"
                                          class="d-inline">
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
                                <div class="comment-content">
                                    {!! nl2br(e($comment->comment)) !!}
                                </div>

                                        {{-- Hiển thị attachments nếu có --}}
                                        @if($comment->has_media && $comment->attachments->count() > 0)
                                        <div class="comment-attachments mt-3">
                                            <div class="row g-2">
                                                @foreach($comment->attachments as $attachment)
                                                <div class="col-auto">
                                                    <div class="attachment-item">
                                                        <a href="{{ $attachment->url }}"
                                                           data-fancybox="comment-{{ $comment->id }}-images"
                                                           data-caption="{{ $attachment->file_name }}">
                                                            <img src="{{ $attachment->url }}"
                                                                 alt="{{ $attachment->file_name }}"
                                                                 class="img-thumbnail comment-image"
                                                                 style="max-width: 150px; max-height: 150px; object-fit: cover; cursor: pointer;">
                                                        </a>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                </div>

                                {{-- Comment Actions --}}
                                <div class="comment-actions">
                                    <button type="button" class="comment-action-btn" onclick="toggleReplyForm({{ $comment->id }})">
                                        <i class="fas fa-reply"></i>
                                        Trả lời
                                    </button>
                                    <button type="button" class="comment-action-btn">
                                        <i class="fas fa-heart"></i>
                                        Thích
                                    </button>
                                    <button type="button" class="comment-action-btn">
                                        <i class="fas fa-share"></i>
                                        Chia sẻ
                                    </button>
                                </div>
                            </div>

                            {{-- Bình luận con (nếu có) --}}
                            @if($comment->replies && $comment->replies->count() > 0)
                            <div class="comment-replies">
                                @foreach($comment->replies as $reply)
                                <div class="reply-item">
                                    <div class="reply-header">
                                        <a href="{{ route('profile.show', $reply->user->username) }}">
                                            <img src="{{ $reply->user->getAvatarUrl() }}"
                                                 class="reply-avatar"
                                                 alt="Avatar của {{ $reply->user->display_name }}"
                                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr($reply->user->name, 0, 1))) }}&background=6366f1&color=fff&size=32'">
                                        </a>
                                        <div class="flex-grow-1">
                                            <h6 class="reply-username">
                                                <a href="{{ route('profile.show', $reply->user->username) }}" class="text-decoration-none">
                                                    {{ $reply->user->display_name }}
                                                </a>
                                            </h6>
                                            <p class="reply-timestamp">
                                                {{ $reply->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="reply-options">
                                            {{-- Nút xóa reply (chỉ hiện với chủ sở hữu) --}}
                                            @auth
                                            @if($reply->user_id === auth()->id() || $showcase->user_id === auth()->id())
                                            <form action="{{ route('showcase.comment.delete', $reply) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa phản hồi này?')"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash fa-xs"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @endauth
                                        </div>
                                    </div>
                                    <div class="reply-body">
                                        {!! nl2br(e($reply->comment)) !!}

                                                        {{-- Hiển thị attachments cho reply nếu có --}}
                                                        @if($reply->has_media && $reply->attachments->count() > 0)
                                                        <div class="reply-attachments mt-2">
                                                            <div class="row g-1">
                                                                @foreach($reply->attachments as $attachment)
                                                                <div class="col-auto">
                                                                    <div class="attachment-item">
                                                                        <a href="{{ $attachment->url }}"
                                                                           data-fancybox="reply-{{ $reply->id }}-images"
                                                                           data-caption="{{ $attachment->file_name }}">
                                                                            <img src="{{ $attachment->url }}"
                                                                                 alt="{{ $attachment->file_name }}"
                                                                                 class="img-thumbnail reply-image"
                                                                                 style="max-width: 100px; max-height: 100px; object-fit: cover; cursor: pointer;">
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        @endif
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
                                <div class="reply-form mt-2" id="reply-form-{{ $comment->id }}" style="display: none;">
                                    <form action="{{ route('showcase.comment', $showcase) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        <div class="d-flex gap-2">
                                            <img src="{{ auth()->user()->getAvatarUrl() }}" class="rounded-circle"
                                                width="30" height="30" alt="Avatar của bạn"
                                                onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr(auth()->user()->name, 0, 1))) }}&background=6366f1&color=fff&size=30'">
                                            <div class="flex-grow-1">
                                                <x-ckeditor5-comment
                                                    name="content"
                                                    placeholder="Trả lời bình luận..."
                                                    id="reply-editor-{{ $comment->id }}"
                                                    :required="true"
                                                    minHeight="80px"
                                                />

                                                {{-- Enhanced Image Upload for Reply --}}
                                                <x-enhanced-image-upload
                                                    name="images"
                                                    id="reply-upload-{{ $comment->id }}"
                                                    :maxFiles="3"
                                                    :maxSize="5"
                                                />

                                                <div class="reply-form-actions">
                                                    <button type="submit" class="reply-submit-btn">
                                                        <i class="fas fa-paper-plane"></i>
                                                        Gửi
                                                    </button>
                                                    <button type="button" class="reply-cancel-btn"
                                                        onclick="toggleReplyForm({{ $comment->id }})">
                                                        Hủy
                                                    </button>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- End Main Content Column --}}
        </div>

        {{-- Sidebar Column --}}
        <div class="col-lg-4 col-md-12">
            <x-sidebar />
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/rating.js') }}"></script>
<script>
    // Showcase Comments System
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Showcase comments system initialized');
        initializeCommentSystem();
    });

    function initializeCommentSystem() {
        // Initialize comment interactions
        console.log('CKEditor5 and Enhanced Upload ready');
    }

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
        // Focus on rich text editor content
        const editorContent = replyForm.querySelector('.editor-content');
        if (editorContent) {
            editorContent.focus();
        }
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

// Comment system functions
function toggleReplyForm(commentId) {
    const replyForm = document.getElementById(`reply-form-${commentId}`);
    if (replyForm) {
        const isVisible = replyForm.style.display !== 'none';
        replyForm.style.display = isVisible ? 'none' : 'block';

        if (!isVisible) {
            // Focus on the CKEditor when showing reply form
            setTimeout(() => {
                const editorId = `reply-editor-${commentId}`;
                if (window[`ckeditor_${editorId}`]) {
                    window[`ckeditor_${editorId}`].editing.view.focus();
                }
            }, 100);
        }
    }
}

// Enhanced comment system ready - using CKEditor5 and Enhanced Upload components
</script>
@endpush

@endsection
