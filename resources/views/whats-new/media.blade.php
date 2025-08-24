    @extends('layouts.app')

@section('title', __('media.new') . ' - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/whats-new.css') }}">
<!-- Fancybox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/fancybox/fancybox.css">
@endpush

@section('content')
<div class="container_2 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 title_page">{{ __('nav.main.whats_new') }}</h1>

        <a href="{{ route('threads.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> {{ __('forum.threads.create') }}
        </a>
    </div>

    <!-- Page Description -->
    <div class="page-description mb-4">
        <div class="alert alert-primary border-0">
            <i class="fas fa-images me-2"></i>
            <strong>{{ __('ui.whats_new.media.title') }}:</strong> {{ __('ui.whats_new.media.description') }}
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="whats-new-tabs mb-4">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new') }}">{{ __('forum.posts.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.popular') }}">{{ __('ui.common.popular') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.hot-topics') }}">{{ __('navigation.hot_topics') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.threads') }}">{{ __('forum.threads.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.showcases') }}">{{ __('showcase.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('whats-new.media') }}">{{ __('media.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.replies') }}">{{ __('forum.threads.looking_for_replies') }}</a>
            </li>
        </ul>
    </div>

    <!-- Pagination Top -->
            @if($mediaItems->hasPages())
            <div class="text-center mt-4">
                {{ $mediaItems->links() }}
            </div>
            @endif

    <!-- Media Description -->
    <div class="media-description mb-4">
        <div class="alert alert-info">
            <i class="fa-solid fa-info-circle me-2"></i>
            <strong>{{ __('media.new') }}:</strong> {{ __('media.description') }}
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

            <div class="media-item-info">
                <h6 class="media-item-title">{{ $media->title }}</h6>
                <div class="media-item-meta">
                    <span class="media-item-date">
                        <i class="fa-solid fa-calendar me-1"></i>
                        {{ $media->created_at->diffForHumans() }}
                    </span>
                    <span class="media-item-size">
                        <i class="fa-solid fa-file me-1"></i>
                        {{ $media->file_size_formatted ?? 'N/A' }}
                    </span>
                </div>

                <div class="media-item-actions">
                    @if($media->thread)
                    <a href="{{ route('threads.show', $media->thread->slug) }}" class="media-action-btn primary">
                        <i class="fa-solid fa-eye me-1"></i> {{ __('ui.actions.view_thread') }}
                    </a>
                    @endif
                    <a href="{{ $media->url }}" download class="media-action-btn">
                        <i class="fa-solid fa-download me-1"></i> {{ __('ui.actions.download') }}
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
        <h3 class="media-empty-title">{{ __('ui.common.no_media_found') }}</h3>
        <p class="media-empty-text">{{ __('ui.common.no_media_description') }}</p>
    </div>
    @endif

    <!-- Pagination Bottom -->
            @if($mediaItems->hasPages())
            <div class="text-center mt-4">
                {{ $mediaItems->links() }}
            </div>
            @endif

    <!-- Go to Page Input -->
    <div class="go-to-page mt-3">
        <div class="input-group input-group-sm" style="max-width: 200px; margin: 0 auto;">
            <input type="number" class="form-control" id="pageInput" min="1" max="{{ $totalPages ?? 1 }}" value="{{ $page ?? 1 }}">
            <button class="btn btn-outline-secondary" type="button" id="goToPageBtn">{{ __('ui.pagination.go_to_page') }}</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Fancybox JS -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/fancybox/fancybox.umd.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Fancybox
        Fancybox.bind("[data-fancybox]", {
            // Options
        });

        // Go to page functionality
        const goToPageBtn = document.getElementById('goToPageBtn');
        const pageInput = document.getElementById('pageInput');

        if (goToPageBtn && pageInput) {



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
