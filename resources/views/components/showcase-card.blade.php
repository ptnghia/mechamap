@props([
'showcase',
'showCategory' => true,
'showDescription' => true,
'showAuthor' => true,
'compact' => false,
'enhanced' => true
])

<div class="showcase-card {{ $compact ? 'showcase-card-compact' : 'showcase-card-full' }}">
    {{-- Showcase Image --}}
    <div class="showcase-image-wrapper {{ $compact ? 'mb-2' : 'mb-3' }}">
        <x-showcase-image :showcase="$showcase" :size="$compact ? 'medium' : 'card'" :showLink="true" />

        {{-- Enhanced: Overlay badges --}}
        @if($enhanced && !$compact)
        <div class="showcase-overlay-badges">
            @if($showcase->complexity_level)
            <span class="badge badge-complexity badge-{{ $showcase->complexity_level }}">
                {{ ucfirst($showcase->complexity_level) }}
            </span>
            @endif

            @if($showcase->has_cad_files)
            <span class="badge badge-feature bg-success">
                <i class="fas fa-cube me-1"></i>CAD
            </span>
            @endif

            @if($showcase->allow_downloads)
            <span class="badge badge-feature bg-info">
                <i class="fas fa-download me-1"></i>Download
            </span>
            @endif
        </div>
        @endif
    </div>

    {{-- Showcase Content --}}
    <div class="showcase-content">
        {{-- Enhanced: Category & Project Type --}}
        @if($showCategory)
        <div class="showcase-category mb-2">
            @if($showcase->category)
            <span class="badge bg-primary me-1">
                {{ ucfirst($showcase->category) }}
            </span>
            @endif

            @if($enhanced && $showcase->project_type)
            <span class="badge bg-secondary">
                {{ ucfirst($showcase->project_type) }}
            </span>
            @endif
        </div>
        @endif

        {{-- Title --}}
        <h5 class="showcase-title {{ $compact ? 'h6' : 'h5' }}">
            <a href="{{ route('showcase.show', $showcase) }}" class="text-decoration-none">
                @if($showcase->showcaseable_type === 'App\Models\Thread')
                <i class="fas fa-comment-left-text me-1"></i>
                @elseif($showcase->showcaseable_type === 'App\Models\Post')
                <i class="fas fa-comment-right me-1"></i>
                @else
                <i class="fas fa-star me-1"></i>
                @endif
                {{ $showcase->title ?? $showcase->showcaseable->title ?? __('content.showcase_item') }}
            </a>
        </h5>

        {{-- Description --}}
        @if($showDescription && $showcase->description && !$compact)
        <p class="showcase-description text-muted small mb-2">
            {{ Str::limit($showcase->description, 100) }}
        </p>
        @endif

        {{-- Enhanced: Technical Info --}}
        @if($enhanced && !$compact)
        <div class="showcase-tech-info mb-2">
            @if($showcase->software_used)
            <div class="tech-item">
                <i class="fas fa-tools text-muted me-1"></i>
                <span class="small text-muted">{{ Str::limit($showcase->software_used, 40) }}</span>
            </div>
            @endif

            @if($showcase->industry_application)
            <div class="tech-item">
                <i class="fas fa-industry text-muted me-1"></i>
                <span class="small text-muted">{{ ucfirst($showcase->industry_application) }}</span>
            </div>
            @endif
        </div>
        @endif

        {{-- Enhanced: Rating & Stats --}}
        @if($enhanced && ($showcase->rating_average > 0 || $showcase->view_count > 0))
        <div class="showcase-stats-enhanced mb-2">
            @if($showcase->rating_average > 0)
            <div class="rating-display">
                <div class="stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($showcase->rating_average))
                        <i class="fas fa-star text-warning"></i>
                        @elseif($i - 0.5 <= $showcase->rating_average)
                        <i class="fas fa-star-half-alt text-warning"></i>
                        @else
                        <i class="far fa-star text-muted"></i>
                        @endif
                    @endfor
                </div>
                <span class="rating-text small text-muted">
                    {{ number_format($showcase->rating_average, 1) }} ({{ $showcase->rating_count }})
                </span>
            </div>
            @endif

            <div class="view-stats small text-muted">
                <i class="fas fa-eye me-1"></i>{{ number_format($showcase->view_count) }}
                @if($showcase->download_count > 0)
                <i class="fas fa-download ms-2 me-1"></i>{{ number_format($showcase->download_count) }}
                @endif
            </div>
        </div>
        @endif

        {{-- Author & Meta --}}
        @if($showAuthor)
        <div class="showcase-meta d-flex align-items-center {{ $compact ? 'small' : '' }}">
            <img src="{{ $showcase->user->getAvatarUrl() }}" alt="{{ $showcase->user->name }}"
                class="rounded-circle me-2" width="{{ $compact ? '24' : '32' }}" height="{{ $compact ? '24' : '32' }}"
                onerror="this.src='{{ asset('images/placeholders/50x50.png') }}'">

            <div class="flex-grow-1">
                <div class="fw-semibold">
                    <a href="{{ route('profile.show', $showcase->user->username ?? $showcase->user->id) }}"
                        class="text-decoration-none">
                        {{ $showcase->user->name }}
                    </a>
                </div>
                <div class="text-muted small">
                    {{ $showcase->created_at->diffForHumans() }}
                </div>
            </div>

            {{-- Stats --}}
            <div class="showcase-stats text-muted small">
                @if(method_exists($showcase, 'likesCount'))
                <span class="me-2">
                    <i class="heart"></i> {{ $showcase->likesCount() }}
                </span>
                @endif

                @if($showcase->media && $showcase->media->count() > 0)
                <span>
                    <i class="images"></i> {{ $showcase->media->count() }}
                </span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .showcase-card {
        background: white;
        border-radius: 0.5rem;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        position: relative;
    }

    .showcase-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .showcase-card-compact {
        padding: 0.75rem;
    }

    .showcase-image-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 0.375rem;
    }

    /* Enhanced: Overlay badges */
    .showcase-overlay-badges {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        z-index: 2;
    }

    .badge-complexity {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }

    .badge-beginner { background-color: #10b981; }
    .badge-intermediate { background-color: #f59e0b; }
    .badge-advanced { background-color: #ef4444; }
    .badge-expert { background-color: #8b5cf6; }

    .badge-feature {
        font-size: 0.65rem;
        padding: 0.15rem 0.3rem;
    }

    .showcase-title a {
        color: #1f2937;
        font-weight: 600;
    }

    .showcase-title a:hover {
        color: #3b82f6;
    }

    .showcase-description {
        line-height: 1.5;
    }

    /* Enhanced: Technical info */
    .showcase-tech-info {
        border-left: 3px solid #e5e7eb;
        padding-left: 0.75rem;
    }

    .tech-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.25rem;
    }

    .tech-item:last-child {
        margin-bottom: 0;
    }

    /* Enhanced: Rating and stats */
    .showcase-stats-enhanced {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem;
        background-color: #f8fafc;
        border-radius: 0.25rem;
    }

    .rating-display {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stars {
        display: flex;
        gap: 0.1rem;
    }

    .stars i {
        font-size: 0.8rem;
    }

    .rating-text {
        font-weight: 500;
    }

    .view-stats {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .showcase-meta {
        border-top: 1px solid #e5e7eb;
        padding-top: 0.75rem;
        margin-top: 0.75rem;
    }

    .showcase-card-compact .showcase-meta {
        padding-top: 0.5rem;
        margin-top: 0.5rem;
    }

    .showcase-stats span {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .showcase-overlay-badges {
            top: 0.25rem;
            right: 0.25rem;
        }

        .showcase-stats-enhanced {
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-start;
        }

        .rating-display {
            flex-direction: column;
            gap: 0.25rem;
            align-items: flex-start;
        }
    }
</style>
@endpush
