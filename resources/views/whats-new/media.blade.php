@extends('layouts.app')

@section('title', __('messages.new_media') . ' - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/whats-new.css') }}">
<!-- Fancybox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/fancybox/fancybox.css">
@endpush

@section('content')
<div class="container_2 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 title_page">{{ __('messages.whats_new') }}</h1>

        <a href="{{ route('threads.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> {{ __('messages.create_new_thread') }}
        </a>
    </div>

    <!-- Navigation Tabs -->
    <div class="whats-new-tabs mb-4">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new') }}">{{ __('messages.new_posts') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.popular') }}">{{ __('messages.popular') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.threads') }}">{{ __('messages.new_threads') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.showcases') }}">{{ __('messages.new_showcases')
                    }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('whats-new.media') }}">{{ __('messages.new_media')
                    }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.replies') }}">{{
                    __('messages.looking_for_replies') }}</a>
            </li>
        </ul>
    </div>

    <!-- Pagination Top -->
    <div class="pagination-container mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagination-info">
                <span>{{ __('messages.page') }} {{ $page }} {{ __('messages.of') }} {{ $totalPages }}</span>
            </div>

            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                    <!-- Previous Page -->
                    <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $prevPageUrl }}" aria-label="{{ __('messages.previous') }}">
                            <span aria-hidden="true"><i class="fa-solid fa-chevron-left"></i></span>
                        </a>
                    </li>

                    <!-- First Page -->
                    @if($page > 3)
                    <li class="page-item">
                        <a class="page-link" href="{{ route('whats-new.media', ['page' => 1]) }}">1</a>
                    </li>
                    @endif

                    <!-- Ellipsis for skipped pages -->
                    @if($page > 4)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    @endif

                    <!-- Pages before current -->
                    @for($i = max(1, $page - 2); $i < $page; $i++) <li class="page-item">
                        <a class="page-link" href="{{ route('whats-new.media', ['page' => $i]) }}">{{ $i }}</a>
                        </li>
                        @endfor

                        <!-- Current Page -->
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>

                        <!-- Pages after current -->
                        @for($i = $page + 1; $i <= min($totalPages, $page + 2); $i++) <li class="page-item">
                            <a class="page-link" href="{{ route('whats-new.media', ['page' => $i]) }}">{{ $i
                                }}</a>
                            </li>
                            @endfor

                            <!-- Ellipsis for skipped pages -->
                            @if($page < $totalPages - 3) <li class="page-item disabled">
                                <span class="page-link">...</span>
                                </li>
                                @endif

                                <!-- Last Page -->
                                @if($page < $totalPages - 2) <li class="page-item">
                                    <a class="page-link"
                                        href="{{ route('whats-new.media', ['page' => $totalPages]) }}">{{
                                        $totalPages }}</a>
                                    </li>
                                    @endif

                                    <!-- Next Page -->
                                    <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $nextPageUrl }}" aria-label="{{ __('messages.next') }}">
                                            <span aria-hidden="true"><i class="fa-solid fa-chevron-right"></i></span>
                                        </a>
                                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Modern Media Gallery -->
    @if(count($mediaItems) > 0)
    <div class="media-gallery">
        @foreach($mediaItems as $media)
        <div class="media-item">
            <a href="{{ $media->url }}"
               data-fancybox="media-gallery"
               data-caption="{{ $media->title }}"
               class="media-item-image">
                <img src="{{ $media->url }}" alt="{{ $media->title }}" loading="lazy">
                <div class="media-overlay">
                    <i class="fa-solid fa-expand media-overlay-icon"></i>
                </div>
            </a>

            <div class="media-item-content">
                <h6 class="media-item-title">{{ $media->title }}</h6>

                <div class="media-item-meta">
                    <div class="media-item-user">
                        <img src="{{ get_avatar_url($media->user) }}" alt="{{ $media->user->name }}">
                        <a href="{{ route('profile.show', $media->user->id) }}" class="media-item-user-name">
                            {{ $media->user->name }}
                        </a>
                    </div>
                    <span class="media-item-date">{{ $media->created_at->diffForHumans() }}</span>
                </div>

                <div class="media-item-actions">
                    @if($media->thread)
                    <a href="{{ route('threads.show', $media->thread->slug) }}" class="media-action-btn primary">
                        <i class="fa-solid fa-eye me-1"></i> {{ __('messages.view_thread') }}
                    </a>
                    @endif
                    <a href="{{ $media->url }}" download class="media-action-btn">
                        <i class="fa-solid fa-download me-1"></i> {{ __('messages.download') }}
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="media-empty">
        <div class="media-empty-icon">
            <i class="fa-solid fa-images"></i>
        </div>
        <h3 class="media-empty-title">{{ __('messages.no_media_found') }}</h3>
        <p class="media-empty-text">{{ __('messages.no_media_description') }}</p>
    </div>
    @endif

    <!-- Pagination Bottom -->
    <div class="pagination-container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="pagination-info">
                <span>{{ __('messages.page') }} {{ $page }} {{ __('messages.of') }} {{ $totalPages }}</span>
            </div>

            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                    <!-- Previous Page -->
                    <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $prevPageUrl }}" aria-label="{{ __('messages.previous') }}">
                            <span aria-hidden="true"><i class="fa-solid fa-chevron-left"></i></span>
                        </a>
                    </li>

                    <!-- First Page -->
                    @if($page > 3)
                    <li class="page-item">
                        <a class="page-link" href="{{ route('whats-new.media', ['page' => 1]) }}">1</a>
                    </li>
                    @endif

                    <!-- Ellipsis for skipped pages -->
                    @if($page > 4)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    @endif

                    <!-- Pages before current -->
                    @for($i = max(1, $page - 2); $i < $page; $i++) <li class="page-item">
                        <a class="page-link" href="{{ route('whats-new.media', ['page' => $i]) }}">{{ $i }}</a>
                        </li>
                        @endfor

                        <!-- Current Page -->
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>

                        <!-- Pages after current -->
                        @for($i = $page + 1; $i <= min($totalPages, $page + 2); $i++) <li class="page-item">
                            <a class="page-link" href="{{ route('whats-new.media', ['page' => $i]) }}">{{ $i
                                }}</a>
                            </li>
                            @endfor

                            <!-- Ellipsis for skipped pages -->
                            @if($page < $totalPages - 3) <li class="page-item disabled">
                                <span class="page-link">...</span>
                                </li>
                                @endif

                                <!-- Last Page -->
                                @if($page < $totalPages - 2) <li class="page-item">
                                    <a class="page-link"
                                        href="{{ route('whats-new.media', ['page' => $totalPages]) }}">{{
                                        $totalPages }}</a>
                                    </li>
                                    @endif

                                    <!-- Next Page -->
                                    <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $nextPageUrl }}" aria-label="{{ __('messages.next') }}">
                                            <span aria-hidden="true"><i class="fa-solid fa-chevron-right"></i></span>
                                        </a>
                                    </li>
                </ul>
            </nav>

            <div class="pagination-goto">
                <div class="input-group input-group-sm">
                    <input type="number" class="form-control" id="pageInput" min="1" max="{{ $totalPages }}"
                        value="{{ $page }}" placeholder="{{ __('messages.page') }}">
                    <button class="btn btn-primary" type="button" id="goToPageBtn">{{ __('messages.go') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Fancybox Script -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/fancybox/fancybox.umd.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Go to page functionality
        const goToPageBtn = document.getElementById('goToPageBtn');
        const pageInput = document.getElementById('pageInput');

        if (goToPageBtn && pageInput) {
            goToPageBtn.addEventListener('click', function() {
                const page = parseInt(pageInput.value);
                const maxPages = {{ $totalPages }};

                if (page >= 1 && page <= maxPages) {
                    window.location.href = `{{ route('whats-new.media') }}?page=${page}`;
                } else {
                    alert('{{ __("messages.please_enter_valid_page") }} (1-' + maxPages + ')');
                }
            });

            pageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    goToPageBtn.click();
                }
            });
        }

        // Initialize Fancybox v6
        if (typeof Fancybox !== 'undefined') {
            Fancybox.bind('[data-fancybox="media-gallery"]', {
                // Fancybox v6 options
                Toolbar: {
                    display: {
                        left: ["infobar"],
                        middle: [
                            "zoomIn",
                            "zoomOut",
                            "toggle1to1",
                            "rotateCCW",
                            "rotateCW",
                            "flipX",
                            "flipY",
                        ],
                        right: ["slideshow", "thumbs", "close"],
                    },
                },
                Thumbs: {
                    autoStart: false,
                },
                Images: {
                    zoom: true,
                },
                Carousel: {
                    infinite: true,
                },
                l10n: {
                    CLOSE: '{{ __("messages.close") }}',
                    NEXT: '{{ __("messages.next") }}',
                    PREV: '{{ __("messages.previous") }}',
                    MODAL: '{{ __("messages.image") }}',
                    ERROR: '{{ __("messages.image_not_found") }}',
                    IMAGE_ERROR: '{{ __("messages.image_not_found") }}',
                    ELEMENT_NOT_FOUND: '{{ __("messages.image_not_found") }}',
                    AJAX_NOT_FOUND: '{{ __("messages.image_not_found") }}',
                    LOADING: '{{ __("messages.loading") }}...',
                    DOWNLOAD: '{{ __("messages.download") }}',
                },
                // Custom styling
                backdropClick: "close",
                dragToClose: true,
                keyboard: {
                    Escape: "close",
                    Delete: "close",
                    Backspace: "close",
                    PageUp: "next",
                    PageDown: "prev",
                    ArrowRight: "next",
                    ArrowLeft: "prev",
                    ArrowUp: "prev",
                    ArrowDown: "next",
                },
            });
        }

        // Loading animation for images
        const images = document.querySelectorAll('.media-item-image img');
        images.forEach(function(img) {
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });

            img.addEventListener('error', function() {
                this.src = '{{ asset("images/placeholder.jpg") }}';
                this.alt = '{{ __("messages.image_not_found") }}';
            });
        });
    });
</script>
@endpush

@endsection
