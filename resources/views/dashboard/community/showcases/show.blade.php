@extends('dashboard.layouts.app')

@section('title', $showcase->title)

@push('styles')
<style>
    .showcase-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
    }

    .showcase-image {
        width: 100%;
        max-height: 400px;
        object-fit: cover;
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .showcase-status {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.875rem;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .status-approved {
        background: #d1edff;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .status-featured {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .stats-card {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 1.5rem;
        text-align: center;
        margin-bottom: 1rem;
    }

    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .stats-label {
        color: #6c757d;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-section {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .info-section h5 {
        color: #495057;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #007bff;
    }

    .tag {
        display: inline-block;
        background: #007bff;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        margin: 0.25rem;
    }

    .attachment-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .attachment-item:hover {
        background: #e9ecef;
    }

    .attachment-icon {
        width: 40px;
        height: 40px;
        background: #007bff;
        color: white;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }

    .rating-display {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .stars {
        color: #ffc107;
    }

    .rating-text {
        color: #6c757d;
        font-size: 0.875rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .gallery-item {
        position: relative;
        border-radius: 0.375rem;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .gallery-item:hover {
        transform: scale(1.05);
    }

    .gallery-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .related-thread {
        background: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
    }

    .related-thread h6 {
        color: #1976d2;
        margin-bottom: 0.5rem;
    }

    .breadcrumb-custom {
        background: transparent;
        padding: 0;
        margin-bottom: 1rem;
    }

    .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: #6c757d;
    }
</style>
@endpush

@section('dashboard-content')
<div class="showcase-detail">
    <!-- Header Section -->
    <div class="showcase-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">{{ $showcase->title }}</h2>
                <div class="d-flex align-items-center gap-3">
                    <span class="showcase-status status-{{ $showcase->status }}">
                        {{ __('showcase.status.' . $showcase->status) }}
                    </span>
                    @if($showcase->category)
                        <span class="badge bg-light text-dark">{{ $showcase->category->name }}</span>
                    @endif
                    <small class="opacity-75">
                        <i class="fas fa-calendar me-1"></i>
                        {{ $showcase->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="action-buttons">
                    <a href="{{ route('dashboard.community.showcases.edit', $showcase) }}" 
                       class="btn btn-light">
                        <i class="fas fa-edit me-2"></i>{{ __('common.edit') }}
                    </a>
                    <a href="{{ route('showcase.show', $showcase) }}" 
                       class="btn btn-outline-light" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>{{ __('showcase.view_public') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Cover Image -->
            @if($showcase->cover_image)
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $showcase->cover_image) }}" 
                         class="showcase-image" alt="{{ $showcase->title }}">
                </div>
            @endif

            <!-- Description -->
            <div class="info-section">
                <h5><i class="fas fa-info-circle me-2"></i>{{ __('showcase.description') }}</h5>
                <div class="showcase-description">
                    {!! nl2br(e($showcase->description)) !!}
                </div>
            </div>

            <!-- Tags -->
            @if($showcase->tags && count($showcase->tags) > 0)
                <div class="info-section">
                    <h5><i class="fas fa-tags me-2"></i>{{ __('showcase.tags') }}</h5>
                    <div class="tags-container">
                        @foreach($showcase->tags as $tag)
                            <span class="tag">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Image Gallery -->
            @if($showcase->images && $showcase->images->count() > 0)
                <div class="info-section">
                    <h5><i class="fas fa-images me-2"></i>{{ __('showcase.gallery') }}</h5>
                    <div class="gallery-grid">
                        @foreach($showcase->images as $image)
                            <div class="gallery-item" onclick="openImageModal('{{ asset('storage/' . $image->file_path) }}')">
                                <img src="{{ asset('storage/' . $image->file_path) }}" 
                                     class="gallery-image" alt="Gallery Image">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Attachments -->
            @if($showcase->attachments && $showcase->attachments->count() > 0)
                <div class="info-section">
                    <h5><i class="fas fa-paperclip me-2"></i>{{ __('showcase.attachments') }}</h5>
                    @foreach($showcase->attachments as $attachment)
                        <div class="attachment-item">
                            <div class="attachment-icon">
                                <i class="fas fa-file"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $attachment->file_name }}</h6>
                                <small class="text-muted">
                                    {{ number_format($attachment->file_size / 1024, 2) }} KB
                                </small>
                            </div>
                            <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                               class="btn btn-outline-primary btn-sm" download>
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Related Thread -->
            @if($showcase->thread)
                <div class="related-thread">
                    <h6><i class="fas fa-link me-2"></i>{{ __('showcase.related_thread') }}</h6>
                    <a href="{{ route('threads.show', $showcase->thread) }}" class="text-decoration-none">
                        {{ $showcase->thread->title }}
                    </a>
                    <small class="text-muted d-block mt-1">
                        {{ __('showcase.thread_created') }}: {{ $showcase->thread->created_at->format('d/m/Y') }}
                    </small>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Statistics -->
            <div class="stats-card">
                <div class="stats-number">{{ $showcase->view_count ?? 0 }}</div>
                <div class="stats-label">{{ __('showcase.stats.views') }}</div>
            </div>

            <div class="stats-card">
                <div class="stats-number">{{ $showcase->like_count ?? 0 }}</div>
                <div class="stats-label">{{ __('showcase.stats.likes') }}</div>
            </div>

            <div class="stats-card">
                <div class="stats-number">{{ $showcase->download_count ?? 0 }}</div>
                <div class="stats-label">{{ __('showcase.stats.downloads') }}</div>
            </div>

            <!-- Rating -->
            @if($showcase->ratings && $showcase->ratings->count() > 0)
                <div class="info-section">
                    <h5><i class="fas fa-star me-2"></i>{{ __('showcase.rating') }}</h5>
                    <div class="rating-display">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= ($showcase->rating_average ?? 0))
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="rating-text">
                            {{ number_format($showcase->rating_average ?? 0, 1) }}/5 
                            ({{ $showcase->rating_count ?? 0 }} {{ __('showcase.ratings') }})
                        </span>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="info-section">
                <h5><i class="fas fa-tools me-2"></i>{{ __('showcase.quick_actions') }}</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('dashboard.community.showcases.edit', $showcase) }}" 
                       class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>{{ __('common.edit') }}
                    </a>
                    
                    @if($showcase->status === 'pending')
                        <button class="btn btn-outline-warning" disabled>
                            <i class="fas fa-clock me-2"></i>{{ __('showcase.pending_approval') }}
                        </button>
                    @endif
                    
                    <button type="button" class="btn btn-outline-danger" 
                            onclick="confirmDelete({{ $showcase->id }})">
                        <i class="fas fa-trash me-2"></i>{{ __('common.delete') }}
                    </button>
                </div>
            </div>

            <!-- Showcase Info -->
            <div class="info-section">
                <h5><i class="fas fa-info me-2"></i>{{ __('showcase.information') }}</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>{{ __('showcase.created') }}:</strong></td>
                        <td>{{ $showcase->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('showcase.updated') }}:</strong></td>
                        <td>{{ $showcase->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @if($showcase->approved_at)
                        <tr>
                            <td><strong>{{ __('showcase.approved') }}:</strong></td>
                            <td>{{ $showcase->approved_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('showcase.image_preview') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Preview">
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('showcase.delete.title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('showcase.delete.confirmation') }}</p>
                <p class="text-muted">{{ __('showcase.delete.warning') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('common.cancel') }}
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        {{ __('common.delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}

function confirmDelete(showcaseId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `{{ route('dashboard.community.showcases.index') }}/${showcaseId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endpush
