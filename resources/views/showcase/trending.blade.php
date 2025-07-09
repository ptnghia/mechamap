@extends('layouts.app')

@section('title', 'Trending Showcases - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/showcase-item.css') }}">
<style>
.trending-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: linear-gradient(135deg, #ff6b6b, #ee5a24);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: bold;
    z-index: 2;
}

.trending-stats {
    display: flex;
    gap: 1rem;
    align-items: center;
    margin-top: 0.5rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
    color: #6c757d;
}

.stat-number {
    font-weight: 600;
    color: #495057;
}

.showcase-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    position: relative;
    overflow: hidden;
}

.showcase-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.showcase-image {
    height: 200px;
    object-fit: cover;
    width: 100%;
}

.trending-rank {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 4px 8px;
    border-radius: 50%;
    font-size: 0.75rem;
    font-weight: bold;
    min-width: 24px;
    text-align: center;
}
</style>
@endpush

@section('content')

<div class="py-5">
    <div class="container">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2">{{ __('Trending Showcases') }}</h1>
                        <p class="text-muted mb-0">{{ __('Most popular showcases based on likes, comments, and views') }}</p>
                    </div>
                    <div>
                        <a href="{{ route('showcase.public') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('All Showcases') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trending Showcases -->
        @if($showcases->count() > 0)
        <div class="row">
            @foreach($showcases as $index => $showcase)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card showcase-card h-100">
                    <!-- Trending Badge -->
                    @if($index < 3)
                    <div class="trending-badge">
                        <i class="fas fa-fire me-1"></i>{{ __('Hot') }}
                    </div>
                    @endif
                    
                    <!-- Trending Rank -->
                    <div class="trending-rank">#{{ $index + 1 }}</div>

                    <!-- Showcase Image -->
                    @if($showcase->featured_image)
                    <img src="{{ asset($showcase->featured_image) }}" 
                         alt="{{ $showcase->title }}" 
                         class="showcase-image">
                    @else
                    <div class="showcase-image bg-light d-flex align-items-center justify-content-center">
                        <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                    </div>
                    @endif

                    <div class="card-body">
                        <!-- Title -->
                        <h5 class="card-title">
                            <a href="{{ route('showcase.show', $showcase->id) }}" class="text-decoration-none">
                                {{ Str::limit($showcase->title, 60) }}
                            </a>
                        </h5>

                        <!-- Description -->
                        @if($showcase->description)
                        <p class="card-text text-muted">
                            {{ Str::limit(strip_tags($showcase->description), 100) }}
                        </p>
                        @endif

                        <!-- User Info -->
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $showcase->user->avatar ? asset($showcase->user->avatar) : asset('images/users/default-avatar.png') }}" 
                                 alt="{{ $showcase->user->name }}" 
                                 class="rounded-circle me-2" 
                                 style="width: 32px; height: 32px; object-fit: cover;">
                            <div>
                                <small class="text-muted">{{ __('by') }}</small>
                                <a href="{{ route('profile.show', $showcase->user->username) }}" class="text-decoration-none">
                                    <strong>{{ $showcase->user->name }}</strong>
                                </a>
                            </div>
                        </div>

                        <!-- Trending Stats -->
                        <div class="trending-stats">
                            <div class="stat-item">
                                <i class="fas fa-heart text-danger"></i>
                                <span class="stat-number">{{ $showcase->likes_count ?? 0 }}</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-comment text-primary"></i>
                                <span class="stat-number">{{ $showcase->comments_count ?? 0 }}</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-eye text-info"></i>
                                <span class="stat-number">{{ number_format($showcase->view_count ?? 0) }}</span>
                            </div>
                        </div>

                        <!-- Category/Type -->
                        @if($showcase->showcaseable)
                        <div class="mt-2">
                            <span class="badge bg-secondary">
                                {{ class_basename($showcase->showcaseable_type) }}
                            </span>
                        </div>
                        @endif
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                {{ $showcase->created_at->diffForHumans() }}
                            </small>
                            <a href="{{ route('showcase.show', $showcase->id) }}" class="btn btn-sm btn-primary">
                                {{ __('View Details') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                {{ $showcases->links() }}
            </div>
        </div>
        @else
        <!-- Empty State -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-chart-line text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="h4 mb-3">{{ __('No Trending Showcases Yet') }}</h3>
                    <p class="text-muted mb-4">{{ __('Showcases will appear here when they start getting likes, comments, and views from the community.') }}</p>
                    <a href="{{ route('showcase.public') }}" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i>{{ __('Browse All Showcases') }}
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Trending Info -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-info-circle text-primary me-2"></i>{{ __('How Trending Works') }}
                        </h5>
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-primary">{{ __('Likes Priority') }}</h6>
                                <p class="small text-muted">{{ __('Showcases with more likes rank higher in trending.') }}</p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-primary">{{ __('Engagement Matters') }}</h6>
                                <p class="small text-muted">{{ __('Comments and discussions boost trending score.') }}</p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-primary">{{ __('View Count') }}</h6>
                                <p class="small text-muted">{{ __('Popular showcases with high view counts trend higher.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        @auth
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-light border-0">
                    <div class="card-body text-center py-4">
                        <h4 class="mb-3">{{ __('Create Your Own Trending Showcase') }}</h4>
                        <p class="text-muted mb-4">{{ __('Share your mechanical engineering projects and innovations with our community.') }}</p>
                        <a href="{{ route('showcase.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>{{ __('Create Showcase') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endauth
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any specific JavaScript for trending page
    console.log('Trending Showcases page loaded');
    
    // Add hover effects for showcase cards
    const cards = document.querySelectorAll('.showcase-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush
