@props([
'showcase',
'showCategory' => true,
'showDescription' => true,
'showAuthor' => true,
'compact' => false
])

<div class="showcase-card {{ $compact ? 'showcase-card-compact' : 'showcase-card-full' }}">
    {{-- Showcase Image --}}
    <div class="showcase-image-wrapper {{ $compact ? 'mb-2' : 'mb-3' }}">
        <x-showcase-image :showcase="$showcase" :size="$compact ? 'medium' : 'card'" :showLink="true" />
    </div>

    {{-- Showcase Content --}}
    <div class="showcase-content">
        {{-- Category Badge --}}
        @if($showCategory && $showcase->showcaseable_type === 'App\Models\Thread' && $showcase->showcaseable->category)
        <div class="showcase-category mb-2">
            <span class="badge bg-primary">
                {{ $showcase->showcaseable->category->name }}
            </span>
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
</style>
@endpush
