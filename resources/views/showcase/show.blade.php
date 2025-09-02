@extends('layouts.app-full')

@section('title', $showcase->title)

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/showcase.css') }}">
@endpush

@section('content')
<div class="body_page" data-showcase-id="{{ $showcase->id }}">
    <div class="row">
        <div class="col-lg-8 col-md-12">
            <div class="mb-4 showcase_detail p-3 bg-white">
                <!-- Showcase Header -->
                <div class="showcase_detail_header">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                             <h1 class="showcas-title mb-2">{{ $showcase->title }}</h1>
                            <!-- Showcase Meta  -->
                            <div class="Showcase_meta d-flex justify-content-start gap-2">
                                <div class="thread-meta-item">
                                    <i class="fas fa-eye"></i> {{ number_format($showcase->view_count ?? 0) }} <span class="d-none d-md-inline">Lượt xem</span>
                                </div>
                                <div class="thread-meta-item">
                                    <i class="fas fa-star"></i> {{ number_format($showcase->average_rating, 1) }} <span class="d-none d-md-inline">Đánh giá</span>
                                </div>
                                <div class="thread-meta-item">
                                    <i class="fas fa-comment"></i> {{ number_format($showcase->commentsCount()) }} <span class="d-none d-md-inline">Bình luận</span>
                                </div>
                                <div class="thread-meta-item">
                                    <i class="heart"></i> {{ number_format($showcase->likesCount()) }} <span class="d-none d-md-inline">Thích</span>
                                </div>
                            </div>
                            <!-- End Showcase Meta  -->
                        </div>

                        <div class="thread-actions">
                            @auth
                            @if($showcase->user_id === auth()->id())
                            <a href="{{ route('showcase.edit', $showcase) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            @else
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
                                    class="btn btn-sm {{ $isBookmarked ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="fas {{ $isBookmarked ? 'fa-bookmark' : 'fa-bookmark' }} me-1"></i>
                                    {{ $isBookmarked ? 'Đã lưu' : t_feature('showcase.actions.save') }}
                                </button>
                            </form>
                            {{-- Nút follow --}}
                            <form action="{{ route('showcase.toggle-follow', $showcase) }}" method="POST" class="follow-form d-inline">
                                @csrf
                                <button type="submit" class="btn-follow btn btn-sm {{ $showcase->isFollowedBy(auth()->user()) ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="{{ $showcase->isFollowedBy(auth()->user()) ? 'fas fa-bell-fill' : 'fas fa-bell' }} me-1"></i>
                                    {{ $showcase->isFollowedBy(auth()->user()) ? 'Đang theo dõi' : 'Theo dõi' }}
                                </button>
                            </form>
                            @endif
                            @endauth
                        </div>
                    </div>
                    <!-- Author Info -->
                    <div class="d-flex justify-content-between align-items-center showcase-author">
                        <div class="d-flex align-items-center">
                            <img src="{{ $showcase->user->getAvatarUrl() }}" alt="{{ $showcase->user->name }}" class="rounded-circle me-2" width="40" height="40" onerror="this.src='{{ asset('images/placeholders/50x50.png') }}'">
                            <div>
                                <a href="{{ route('profile.show', $showcase->user->username ?? $showcase->user->id) }}" class="fw-bold text-decoration-none">{{ $showcase->user->name }}</a>
                                <div class="text-muted small">
                                    <span>{{ $showcase->user->showcases_count ?? 0 }} Showcases</span> ·
                                    <span>Tham gia {{ $showcase->user->created_at->format('M Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-muted small">
                            {{ $showcase->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <!-- End Author Info -->
                </div>
                <!-- End Showcase Header  -->

                <!-- Showcase body -->
                <div class="showcase-body">
                    {{-- Mô tả --}}
                    @if($showcase->description)
                    <div class="showcase-description mb-3">
                        {{ $showcase->description }}
                    </div>
                    @endif

                    {{-- Hình ảnh chính với Gallery Support --}}
                    @php
                    use App\Services\ShowcaseImageService;
                    $featuredImage = ShowcaseImageService::getFeaturedImage($showcase);
                    @endphp

                    @if($featuredImage)
                    <div class="showcase-main-image mb-4">
                        <div class="showcase-image">
                            <a href="{{ $featuredImage->url ?? asset('images/placeholder.svg') }}" data-fancybox="showcase-gallery" data-caption="{{ $showcase->title }}">
                                <img src="{{ $featuredImage->url ?? asset('images/placeholder.svg') }}" class="img-fluid rounded" alt="{{ $showcase->title }}" lass="showcase-featured-image" onerror="this.src='{{ asset('images/placeholder.svg') }}'">
                            </a>
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
                    {{-- End Nội dung chi tiết --}}

                    {{-- Gallery của showcase media --}}
                    @if($showcase->media && $showcase->media->count() > 0)
                    <div class="showcase-media-gallery mb-4">
                        <h5><i class="fas fa-images"></i> {{__('media.image_gallery') }}</h5>
                        <div class="row g-3 showcase-image-gallery">
                            @foreach($showcase->media as $media)
                            @if(str_starts_with($media->mime_type ?? '', 'image/'))
                            <div class="col-md-4 col-sm-6 mb-3 col-lg-3">
                                <a href="{{ $media->url }}" data-fancybox="showcase-gallery" data-caption="{{ $media->title ?? $showcase->title }}">
                                    <img src="{{ $media->url }}"  class="img-fluid rounded showcase-gallery-image" alt="{{ $media->title ?? __('showcase.show.image_alt') }}" onerror="this.src='{{ asset('images/placeholders/300x200.png') }}'">
                                </a>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                    {{-- End Gallery của showcase media --}}

                    {{-- File đính kèm (non-image files) --}}
                    @php
                        $nonImageFiles = $showcase->media ? $showcase->media->filter(function($media) {
                            return !str_starts_with($media->mime_type ?? '', 'image/');
                        }) : collect();
                    @endphp

                    @if($nonImageFiles->count() > 0)
                    <div class="showcase-attachments mb-4">
                        <h5><i class="fas fa-paperclip"></i> Tài liệu đính kèm</h5>
                        <div class="list-group">
                            @foreach($nonImageFiles as $mediaItem)
                            <a href="{{ $mediaItem->url }}" class="list-group-item list-group-item-action d-flex align-items-center" target="_blank" download="{{ $mediaItem->file_name }}">
                                @php
                                    $extension = strtolower($mediaItem->file_extension ?? '');
                                    $iconClass = 'fas fa-file';
                                    $iconColor = 'text-secondary';

                                    if (in_array($extension, ['pdf'])) {
                                        $iconClass = 'fas fa-file-pdf';
                                        $iconColor = 'text-danger';
                                    } elseif (in_array($extension, ['doc', 'docx'])) {
                                        $iconClass = 'fas fa-file-word';
                                        $iconColor = 'text-primary';
                                    } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                        $iconClass = 'fas fa-file-excel';
                                        $iconColor = 'text-success';
                                    } elseif (in_array($extension, ['dwg', 'dxf', 'step', 'stp', 'stl', 'obj', 'iges', 'igs'])) {
                                        $iconClass = 'fas fa-cube';
                                        $iconColor = 'text-info';
                                    } elseif (in_array($extension, ['zip', 'rar', '7z'])) {
                                        $iconClass = 'fas fa-file-archive';
                                        $iconColor = 'text-warning';
                                    }
                                @endphp
                                <i class="{{ $iconClass }} {{ $iconColor }} me-3 fs-4"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $mediaItem->file_name }}</div>
                                    <small class="text-muted">
                                        {{ strtoupper($extension) }} •
                                        @if($mediaItem->file_size)
                                            @if($mediaItem->file_size < 1024)
                                                {{ $mediaItem->file_size }} B
                                            @elseif($mediaItem->file_size < 1024 * 1024)
                                                {{ round($mediaItem->file_size / 1024, 1) }} KB
                                            @else
                                                {{ round($mediaItem->file_size / (1024 * 1024), 1) }} MB
                                            @endif
                                        @else
                                            Không xác định
                                        @endif
                                    </small>
                                </div>
                                <i class="fas fa-download text-muted"></i>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                <!-- End Showcase body -->

                {{-- Thống kê tương tác và Social Sharing --}}
                <div class="showcase-stats">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            @auth
                            {{-- Nút thích --}}
                            <form action="{{ route('showcase.toggle-like', $showcase) }}" method="POST"
                                class="like-form d-inline">
                                @csrf
                                <button type="submit"
                                    class="btn btn-sm btn-like-showcase {{ $showcase->isLikedBy(auth()->user()) ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="fas fa-heart me-1"></i>
                                    {{ $showcase->likesCount() }} Thích
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
                        <!-- Share Button -->
                        <div class="dropdown dropdown-button d-inline">
                            <button class="btn btn-sm no-border dropdown-toggle btn-share" type="button"
                                id="shareDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-share-alt me-1"></i> {{ __('thread.share') }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="shareDropdown">
                                <li>
                                    <a class="dropdown-item share-facebook" href="#" data-action="facebook">
                                        <i class="fab fa-facebook-f me-2"></i>Facebook
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item share-twitter" href="#" data-action="twitter">
                                        <i class="fab fa-twitter me-2"></i>Twitter
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item share-whatsapp" href="#" data-action="whatsapp">
                                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item copy-link" href="#" data-action="copy">
                                        <i class="fas fa-clipboard me-2"></i>{{ __('thread.copy_link') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                {{-- End Thống kê tương tác và Social Sharing --}}

                {{-- Rating Summary --}}
                <div class="rating-summary mb-4">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-md-3">
                            <div class="overall-rating text-center">
                                <div class="rating-number">{{ number_format($showcase->average_rating, 1) }}</div>
                                <div class="rating-stars mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= round($showcase->average_rating) ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                                <div class="rating-count text-muted">
                                    {{ $showcase->ratings_count }} đánh giá
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="rating-breakdown">
                            @php $categories = $showcase->getCategoryAverages(); @endphp
                            @foreach(\App\Models\ShowcaseRating::getCategoryNames() as $key => $name)
                                <div class="rating-category mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="category-name">{{ $name }}</span>
                                        <div class="category-rating">
                                            <div class="stars-small">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= round($categories[$key]) ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="rating-value ms-2">{{ number_format($categories[$key], 1) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        </div>
                    </div>
                </div>
                {{-- End Rating Summary --}}
            </div>
            {{-- Integrated Rating & Comment Form --}}
            @php $userRating = auth()->check() ? $showcase->getUserRating(auth()->user()) : null; @endphp
            @include('showcases.partials.rating-comment-form', [
                'showcase' => $showcase,
                'userRating' => $userRating
            ])

            <hr>

            {{-- Ratings List --}}
            @php
                $ratings = $showcase->ratings()
                    ->with(['user' => function($query) {
                        $query->withCount(['showcaseItems', 'showcaseRatings']);
                    }])
                    ->latest()
                    ->get();
            @endphp

            @include('showcases.partials.ratings-list', [
                'ratings' => $ratings,
                'showcase' => $showcase
            ])

            {{-- Integrated Rating & Comment Form --}}

        </div>
        <div class="col-lg-4 col-md-12">
            <div class="showcase_slidbar sidebar-professional" id="professional-sidebar">

                @include('showcases.partials.sidebar', [
                    'showcase' => $showcase,
                    'authorStats' => $authorStats ?? [],
                    'otherShowcases' => $otherShowcases ?? collect(),
                    'featuredShowcases' => $featuredShowcases ?? collect(),
                    'topContributors' => $topContributors ?? collect()
                ])
            </div>
        </div>
    </div>
</div>


@push('styles')

@endpush

@push('scripts')
<!-- Showcase Interactions JavaScript -->
<script src="{{ asset_versioned('js/showcase-interactions.js') }}"></script>
<script src="{{ asset('js/rating.js') }}"></script>
<script>
    // Showcase Rating & Comments System
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Showcase rating & comments system initialized');
        initializeShowcaseSystem();
    });

    function initializeShowcaseSystem() {
        // Initialize tab switching
        initializeTabSwitching();
    }

    function initializeTabSwitching() {
        // Function to switch to ratings tab
        window.switchToRatingsTab = function() {
            const ratingsTab = document.getElementById('ratings-tab');
            const ratingsPane = document.getElementById('ratings');
            const commentsPane = document.getElementById('comments');
            const commentsTab = document.getElementById('comments-tab');

            if (ratingsTab && ratingsPane && commentsPane && commentsTab) {
                // Remove active from comments
                commentsTab.classList.remove('active');
                commentsTab.setAttribute('aria-selected', 'false');
                commentsPane.classList.remove('show', 'active');

                // Add active to ratings
                ratingsTab.classList.add('active');
                ratingsTab.setAttribute('aria-selected', 'true');
                ratingsPane.classList.add('show', 'active');

                // Scroll to ratings section
                ratingsPane.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        };
    }

// Utility Functions for backward compatibility
function shareOnFacebook() {
    if (window.showcaseInteractions) {
        window.showcaseInteractions.shareOnFacebook();
    }
}

function shareOnTwitter() {
    if (window.showcaseInteractions) {
        window.showcaseInteractions.shareOnTwitter();
    }
}

function shareOnWhatsApp() {
    if (window.showcaseInteractions) {
        window.showcaseInteractions.shareOnWhatsApp();
    }
}

function copyToClipboard() {
    if (window.showcaseInteractions) {
        window.showcaseInteractions.copyToClipboard();
    }
}

// Reply form toggle
function toggleReplyForm(commentId) {
    const replyForm = document.getElementById('reply-form-' + commentId);
    if (replyForm) {
        replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
        if (replyForm.style.display === 'block') {
            const editorContent = replyForm.querySelector('.editor-content');
            if (editorContent) editorContent.focus();
        }
    }
}

// Legacy AJAX interactions - now handled by ShowcaseInteractions class
// Keeping this section for any custom logic that might be needed






// Additional utility functions
document.addEventListener('DOMContentLoaded', function() {
    // Auto-expand textareas
    document.addEventListener('input', function(e) {
        if (e.target.tagName === 'TEXTAREA') {
            e.target.style.height = 'auto';
            e.target.style.height = (e.target.scrollHeight) + 'px';
        }
    });

    // Smooth scroll to comments
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a[href^="#comment-"]');
        if (link) {
            e.preventDefault();
            const target = document.querySelector(link.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                target.classList.add('highlight');
                setTimeout(() => target.classList.remove('highlight'), 3000);
            }
        }
    });
});

    // Add optimized CSS for showcase interactions
    if (!document.getElementById('showcase-styles')) {
        const style = document.createElement('style');
        style.id = 'showcase-styles';
        style.textContent = `
            .highlight {
                animation: highlight 3s ease-in-out;
            }
            @keyframes highlight {
                0%, 50% { background-color: #fff3cd; }
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
    }


</script>

<!-- Showcase Rating System JavaScript -->
<script src="{{ asset('js/showcase-rating-system.js') }}"></script>

@endpush

@endsection
