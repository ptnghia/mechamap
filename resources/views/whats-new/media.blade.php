    @extends('layouts.app')

@section('title', __('media.new') . ' - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/whats-new.css') }}">
@endpush

@section('content')
<div class="body_page">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="div_title_page">
            <h1 class="h2 mb-1 title_page">{{ seo_title_short(__('media.new')) }}</h1>
            <p class="text-muted mb-0">{{ seo_value('description', __('ui.whats_new.media.description'))  }}</p>
        </div>
        <a href="{{ route('threads.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> {{ __('forum.threads.create') }}
        </a>
    </div>

    <!-- Navigation Tabs -->
    <div class="whats-new-tabs mb-4">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new') }}"><i class="fas fa-info-circle me-1"></i>{{ __('forum.posts.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.popular') }}"><i class="fas fa-fire me-1"></i>{{ __('ui.common.popular') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.hot-topics') }}"><i class="fa-solid fa-fire-flame-curved me-1"></i>{{ __('whats_new.hot_topics') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.threads') }}"><i class="fa-solid fa-rss me-1"></i>{{ __('forum.threads.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.showcases') }}"><i class="fa-solid fa-compass-drafting me-1"></i>{{ __('showcase.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('whats-new.media') }}"><i class="fa-solid fa-photo-film me-1"></i>{{ __('media.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.replies') }}"><i class="fa-solid fa-question me-1"></i>{{ __('forum.threads.looking_for_replies') }}</a>
            </li>
        </ul>
    </div>

    <!-- Pagination Top -->
    @if($mediaItems->hasPages())
    <div class="text-center mt-4">
        {{ $mediaItems->links() }}
    </div>
    @endif

    <!-- Enhanced Media Gallery -->
    @if(count($mediaItems) > 0)
    <div class="media-gallery">
        @foreach($mediaItems as $media)
        <div class="media-item media-item-enhanced">
            <!-- Enhanced Thumbnail with Overlays -->
            <div class="media-thumbnail-enhanced">
                <a href="{{ $media->url }}"
                   data-fancybox="media-gallery"
                   data-caption="{{ $media->title ?: $media->file_name }}"
                   class="media-item-image">
                    <img src="{{ $media->url }}" alt="{{ $media->title ?: $media->file_name }}" loading="lazy">
                    <div class="media-overlay">
                        <i class="fa-solid fa-expand media-overlay-icon"></i>
                    </div>
                </a>

                <!-- File Type Overlay -->
                <div class="file-type-overlay">
                    <i class="fas fa-{{ getFileIcon($media->file_extension ?? 'file') }}"></i>
                    <span>{{ strtoupper($media->file_extension ?? 'FILE') }}</span>
                </div>

                <!-- Quality Indicator -->
                @if($media->width && $media->height)
                <div class="quality-indicator">
                    @if($media->width >= 1920 && $media->height >= 1080)
                        <span class="badge bg-success">HD</span>
                    @elseif($media->width >= 1280 && $media->height >= 720)
                        <span class="badge bg-primary">HD Ready</span>
                    @else
                        <span class="badge bg-secondary">Standard</span>
                    @endif
                </div>
                @endif

                <!-- Processing Status -->
                @if($media->processing_status !== 'completed')
                <div class="processing-overlay">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>{{ ucfirst($media->processing_status) }}</span>
                </div>
                @endif
            </div>

            <!-- Thread Context -->
            @if($media->mediable_type === 'App\Models\Thread' && $media->mediable)
            <div class="media-context">
                <i class="fas fa-comments me-1"></i>
                <a href="{{ route('threads.show', $media->mediable->slug) }}" class="text-decoration-none">
                    {{ Str::limit($media->mediable->title, 45) }}
                </a>
            </div>
            @endif

            <div class="media-item-content">
                <!-- Title and File Info -->
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="media-item-title mb-0">{{ Str::limit($media->title ?: $media->file_name, 40) }}</h6>
                    <div class="media-file-info">
                        <span class="file-size-badge">{{ formatFileSize($media->file_size ?? 0) }}</span>
                        @if($media->width && $media->height)
                        <span class="dimensions-badge">{{ $media->width }}×{{ $media->height }}</span>
                        @endif
                    </div>
                </div>

                <!-- Author Info -->
                @if($media->user)
                <div class="media-author">
                    <img src="{{ $media->user->getAvatarUrl() }}"
                         alt="{{ $media->user->name }}" class="author-avatar"
                         onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($media->user->name, 0, 1))]) }}'">
                    <div class="author-details">
                        <a href="{{ route('profile.show', $media->user->id) }}" class="author-name">
                            {{ $media->user->name }}
                        </a>
                        <span class="author-role">{{ $media->user->role_display ?? 'Member' }}</span>
                    </div>
                    <div class="upload-time ms-auto">
                        <i class="fa-solid fa-clock me-1"></i>
                        <small>{{ $media->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                @endif

                <!-- Technical Info for CAD Files -->
                @if($media->cad_software)
                <div class="media-technical-info">
                    <div class="tech-item">
                        <i class="fas fa-cube me-1"></i>
                        <span>{{ $media->cad_software }}{{ $media->cad_version ? ' ' . $media->cad_version : '' }}</span>
                    </div>
                    @if($media->drawing_scale)
                    <div class="tech-item">
                        <i class="fas fa-ruler me-1"></i>
                        <span>Scale: {{ $media->drawing_scale }}</span>
                    </div>
                    @endif
                    @if($media->material_specification)
                    <div class="tech-item">
                        <i class="fas fa-industry me-1"></i>
                        <span>{{ Str::limit($media->material_specification, 30) }}</span>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Engagement Stats -->
                <div class="media-stats">
                    <div class="stat-item">
                        <i class="fas fa-download me-1"></i>
                        <span>{{ $media->download_count ?? 0 }}</span>
                    </div>
                    @if($media->mediable && isset($media->mediable->comments_count))
                    <div class="stat-item">
                        <i class="fas fa-comments me-1"></i>
                        <span>{{ $media->mediable->comments_count }}</span>
                    </div>
                    @endif
                    @if($media->mediable && isset($media->mediable->views_count))
                    <div class="stat-item">
                        <i class="fas fa-eye me-1"></i>
                        <span>{{ $media->mediable->views_count }}</span>
                    </div>
                    @endif
                </div>

                <!-- Enhanced Actions -->
                <div class="media-actions-enhanced">
                    <!-- Quick Preview -->
                    <button class="action-btn preview-btn" data-bs-toggle="modal" data-bs-target="#mediaPreview{{ $media->id }}">
                        <i class="fas fa-search-plus me-1"></i>
                        <span class="d-none d-sm-inline">{{ __('ui.actions.preview') }}</span>
                    </button>

                    <!-- Download -->
                    <a href="{{ $media->url }}" download class="action-btn download-btn">
                        <i class="fas fa-download me-1"></i>
                        <span class="d-none d-sm-inline">{{ __('ui.actions.download') }}</span>
                    </a>

                    <!-- View Thread -->
                    @if($media->mediable)
                    <a href="{{ route('threads.show', $media->mediable->slug) }}" class="action-btn primary">
                        <i class="fas fa-external-link-alt me-1"></i>
                        <span class="d-none d-sm-inline">{{ __('ui.actions.view_thread') }}</span>
                    </a>
                    @endif

                    <!-- Share -->
                    <button class="action-btn share-btn" onclick="shareMedia('{{ $media->url }}', '{{ $media->file_name }}')">
                        <i class="fas fa-share me-1"></i>
                        <span class="d-none d-sm-inline">{{ __('ui.actions.share') }}</span>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Media Preview Modals -->
    @foreach($mediaItems as $media)
    <div class="modal fade" id="mediaPreview{{ $media->id }}" tabindex="-1" aria-labelledby="mediaPreviewLabel{{ $media->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mediaPreviewLabel{{ $media->id }}">{{ $media->title ?: $media->file_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Media Preview -->
                    <div class="text-center mb-4">
                        @if(Str::startsWith($media->mime_type, 'image/'))
                            <img src="{{ $media->url }}" class="img-fluid rounded" alt="{{ $media->file_name }}" style="max-height: 500px;">
                        @elseif(Str::startsWith($media->mime_type, 'video/'))
                            <video controls class="w-100 rounded" style="max-height: 500px;">
                                <source src="{{ $media->url }}" type="{{ $media->mime_type }}">
                                Your browser does not support the video tag.
                            </video>
                        @elseif(Str::startsWith($media->mime_type, 'audio/'))
                            <div class="audio-preview p-4 bg-light rounded">
                                <i class="fas fa-music fa-3x mb-3 text-muted"></i>
                                <h5>{{ $media->file_name }}</h5>
                                <audio controls class="w-100 mt-3">
                                    <source src="{{ $media->url }}" type="{{ $media->mime_type }}">
                                    Your browser does not support the audio tag.
                                </audio>
                            </div>
                        @else
                            <div class="file-preview-placeholder p-5 bg-light rounded text-center">
                                <i class="fas fa-{{ getFileIcon($media->file_extension ?? 'file') }} fa-5x mb-3 text-muted"></i>
                                <h4>{{ $media->file_name }}</h4>
                                <p class="text-muted">{{ $media->mime_type }}</p>
                                <p class="text-muted">{{ formatFileSize($media->file_size ?? 0) }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Detailed Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">{{ __('ui.media.file_information') }}</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2"><strong>{{ __('ui.media.filename') }}:</strong> {{ $media->file_name }}</li>
                                <li class="mb-2"><strong>{{ __('ui.media.size') }}:</strong> {{ formatFileSize($media->file_size ?? 0) }}</li>
                                <li class="mb-2"><strong>{{ __('ui.media.type') }}:</strong> {{ $media->mime_type }}</li>
                                @if($media->width && $media->height)
                                <li class="mb-2"><strong>{{ __('ui.media.dimensions') }}:</strong> {{ $media->width }}×{{ $media->height }}</li>
                                @endif
                                <li class="mb-2"><strong>{{ __('ui.media.uploaded') }}:</strong> {{ $media->created_at->format('M d, Y H:i') }}</li>
                                <li class="mb-2"><strong>{{ __('ui.media.downloads') }}:</strong> {{ $media->download_count ?? 0 }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            @if($media->cad_software || $media->user)
                            <h6 class="fw-bold mb-3">{{ __('ui.media.additional_info') }}</h6>
                            <ul class="list-unstyled">
                                @if($media->user)
                                <li class="mb-2"><strong>{{ __('ui.media.uploaded_by') }}:</strong>
                                    <a href="{{ route('profile.show', $media->user->id) }}">{{ $media->user->name }}</a>
                                </li>
                                @endif
                                @if($media->cad_software)
                                <li class="mb-2"><strong>{{ __('ui.media.cad_software') }}:</strong> {{ $media->cad_software }}{{ $media->cad_version ? ' ' . $media->cad_version : '' }}</li>
                                @endif
                                @if($media->drawing_scale)
                                <li class="mb-2"><strong>{{ __('ui.media.scale') }}:</strong> {{ $media->drawing_scale }}</li>
                                @endif
                                @if($media->material_specification)
                                <li class="mb-2"><strong>{{ __('ui.media.material') }}:</strong> {{ $media->material_specification }}</li>
                                @endif
                                @if($media->mediable)
                                <li class="mb-2"><strong>{{ __('ui.media.from_thread') }}:</strong>
                                    <a href="{{ route('threads.show', $media->mediable->slug) }}">{{ $media->mediable->title }}</a>
                                </li>
                                @endif
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ $media->url }}" download class="btn btn-primary">
                        <i class="fas fa-download me-1"></i> {{ __('ui.actions.download') }}
                    </a>
                    @if($media->mediable)
                    <a href="{{ route('threads.show', $media->mediable->slug) }}" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i> {{ __('ui.actions.view_thread') }}
                    </a>
                    @endif
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.actions.close') }}</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

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
</div>
@endsection

@push('scripts')
<script>
// Share functionality
function shareMedia(url, filename) {
    if (navigator.share) {
        navigator.share({
            title: filename,
            url: url
        }).catch(err => {
            console.log('Error sharing:', err);
            fallbackShare(url);
        });
    } else {
        fallbackShare(url);
    }
}

function fallbackShare(url) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            showToast('{{ __("ui.messages.link_copied") }}', 'success');
        }).catch(err => {
            console.log('Error copying to clipboard:', err);
            showToast('{{ __("ui.messages.copy_failed") }}', 'error');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showToast('{{ __("ui.messages.link_copied") }}', 'success');
        } catch (err) {
            showToast('{{ __("ui.messages.copy_failed") }}', 'error');
        }
        document.body.removeChild(textArea);
    }
}

// Toast notification function
function showToast(message, type = 'info') {
    // Check if SweetAlert2 is available
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            icon: type === 'success' ? 'success' : type === 'error' ? 'error' : 'info',
            title: message
        });
    } else {
        // Fallback to alert
        alert(message);
    }
}

// Track download clicks
document.addEventListener('DOMContentLoaded', function() {
    const downloadButtons = document.querySelectorAll('.download-btn');
    downloadButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const mediaUrl = this.getAttribute('href');
            // Track download (you can send this to analytics)
            console.log('Download tracked:', mediaUrl);

            // Optional: Send to backend to increment download count
            // fetch('/api/media/track-download', {
            //     method: 'POST',
            //     headers: {
            //         'Content-Type': 'application/json',
            //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            //     },
            //     body: JSON.stringify({ url: mediaUrl })
            // });
        });
    });
});

// Enhanced modal functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add keyboard navigation for modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                const modal = bootstrap.Modal.getInstance(openModal);
                if (modal) modal.hide();
            }
        }
    });

    // Lazy load images in modals
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            const images = this.querySelectorAll('img[data-src]');
            images.forEach(img => {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            });
        });
    });
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Go to page functionality
    const pageInput = document.getElementById('pageInput');
    const goToPageBtn = document.getElementById('goToPageBtn');

    if (pageInput && goToPageBtn) {
        goToPageBtn.addEventListener('click', function() {
            const pageNumber = parseInt(pageInput.value);
            const maxPages = parseInt(pageInput.getAttribute('max'));

            if (pageNumber >= 1 && pageNumber <= maxPages) {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('page', pageNumber);
                window.location.href = currentUrl.toString();
            } else {
                alert('{{ __("ui.pagination.invalid_page_number") }}');
            }
        });

        // Allow Enter key to trigger go to page
        pageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                goToPageBtn.click();
            }
        });
    }
});
</script>
@endpush
