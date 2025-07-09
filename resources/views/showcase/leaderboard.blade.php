@extends('layouts.app')

@section('title', 'Showcase Leaderboard - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/showcase-item.css') }}">
<style>
.leaderboard-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.leaderboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.rank-badge {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    color: white;
}

.rank-1 { background: linear-gradient(135deg, #FFD700, #FFA500); }
.rank-2 { background: linear-gradient(135deg, #C0C0C0, #A0A0A0); }
.rank-3 { background: linear-gradient(135deg, #CD7F32, #B8860B); }
.rank-other { background: linear-gradient(135deg, #6c757d, #495057); }

.stats-item {
    text-align: center;
    padding: 0.5rem;
}

.stats-number {
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
}

.stats-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
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
                        <h1 class="h2 mb-2">{{ __('Showcase Leaderboard') }}</h1>
                        <p class="text-muted mb-0">{{ __('Top creators in our mechanical engineering community') }}</p>
                    </div>
                    <div>
                        <a href="{{ route('showcase.public') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('All Showcases') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaderboard -->
        @if($topCreators->count() > 0)
        <div class="row">
            @foreach($topCreators as $index => $creator)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card leaderboard-card h-100">
                    <div class="card-body">
                        <!-- Rank and User Info -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="rank-badge {{ $index < 3 ? 'rank-' . ($index + 1) : 'rank-other' }}">
                                #{{ $index + 1 }}
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $creator->avatar ? asset($creator->avatar) : asset('images/users/default-avatar.png') }}"
                                         alt="{{ $creator->name }}"
                                         class="rounded-circle me-2"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0">
                                            <a href="{{ route('profile.show', $creator->username) }}" class="text-decoration-none">
                                                {{ $creator->name }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">{{ '@' . $creator->username }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="stats-item">
                                    <div class="stats-number">{{ $creator->showcases_count ?? 0 }}</div>
                                    <div class="stats-label">{{ __('Showcases') }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stats-item">
                                    <div class="stats-number">{{ number_format($creator->total_likes ?? 0) }}</div>
                                    <div class="stats-label">{{ __('Total Likes') }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stats-item">
                                    <div class="stats-number">{{ number_format($creator->total_views ?? 0) }}</div>
                                    <div class="stats-label">{{ __('Total Views') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- User Info -->
                        @if($creator->about_me)
                        <div class="mt-3">
                            <p class="text-muted small mb-0">{{ Str::limit($creator->about_me, 100) }}</p>
                        </div>
                        @endif

                        <!-- Location -->
                        @if($creator->location)
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $creator->location }}
                            </small>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="mt-3 d-flex gap-2">
                            <a href="{{ route('profile.show', $creator->username) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="fas fa-user me-1"></i>{{ __('View Profile') }}
                            </a>
                            @if($creator->showcases_count > 0)
                            <a href="{{ route('profile.show', $creator->username) }}#showcases" class="btn btn-sm btn-primary flex-fill">
                                <i class="fas fa-eye me-1"></i>{{ __('View Showcases') }}
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-trophy text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="h4 mb-3">{{ __('No Leaderboard Data Yet') }}</h3>
                    <p class="text-muted mb-4">{{ __('The leaderboard will show top creators when showcases are created and liked by the community.') }}</p>
                    <a href="{{ route('showcase.public') }}" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i>{{ __('Browse Showcases') }}
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Call to Action -->
        @auth
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-light border-0">
                    <div class="card-body text-center py-4">
                        <h4 class="mb-3">{{ __('Join the Competition') }}</h4>
                        <p class="text-muted mb-4">{{ __('Create amazing showcases and climb up the leaderboard to become a top creator in our community.') }}</p>
                        <a href="{{ route('showcase.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>{{ __('Create Showcase') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endauth

        <!-- Leaderboard Info -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-info-circle text-primary me-2"></i>{{ __('How Rankings Work') }}
                        </h5>
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-primary">{{ __('Primary Ranking') }}</h6>
                                <p class="small text-muted">{{ __('Users are ranked primarily by total likes received on all their showcases.') }}</p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-primary">{{ __('Secondary Ranking') }}</h6>
                                <p class="small text-muted">{{ __('In case of tie in likes, users with more showcases rank higher.') }}</p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-primary">{{ __('Updated Regularly') }}</h6>
                                <p class="small text-muted">{{ __('Rankings are updated in real-time as users create showcases and receive likes.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any specific JavaScript for leaderboard page
    console.log('Showcase Leaderboard page loaded');

    // Add hover effects for leaderboard cards
    const cards = document.querySelectorAll('.leaderboard-card');
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
