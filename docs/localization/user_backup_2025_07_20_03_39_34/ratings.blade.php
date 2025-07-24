@extends('layouts.user-dashboard')

@section('title', __('nav.user.ratings'))

@php
    $pageTitle = __('nav.user.ratings');
    $pageDescription = __('messages.ratings_desc');
    $breadcrumbs = [
        ['title' => __('nav.user.ratings'), 'url' => '#']
    ];
@endphp

@section('dashboard-content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card primary">
            <div class="stats-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stats-value" data-stat="total_ratings_given">{{ $stats['total_ratings_given'] ?? 0 }}</div>
            <div class="stats-label">{{ __('messages.ratings_given') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-icon">
                <i class="fas fa-star-half-alt"></i>
            </div>
            <div class="stats-value" data-stat="avg_rating_given">{{ number_format($stats['avg_rating_given'] ?? 0, 1) }}</div>
            <div class="stats-label">{{ __('messages.average_rating_given') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="stats-value" data-stat="total_ratings_received">{{ $stats['total_ratings_received'] ?? 0 }}</div>
            <div class="stats-label">{{ __('messages.ratings_received') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="stats-icon">
                <i class="fas fa-medal"></i>
            </div>
            <div class="stats-value" data-stat="avg_rating_received">{{ number_format($stats['avg_rating_received'] ?? 0, 1) }}</div>
            <div class="stats-label">{{ __('messages.average_rating_received') }}</div>
        </div>
    </div>
</div>

<!-- Rating Distribution Chart -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">{{ __('messages.ratings_given_distribution') }}</h6>
            </div>
            <div class="card-body">
                <canvas id="ratingsGivenChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">{{ __('messages.ratings_received_distribution') }}</h6>
            </div>
            <div class="card-body">
                <canvas id="ratingsReceivedChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabs for Given vs Received Ratings -->
<ul class="nav nav-tabs mb-4" id="ratingsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="given-tab" data-bs-toggle="tab" data-bs-target="#given"
                type="button" role="tab" aria-controls="given" aria-selected="true">
            <i class="fas fa-star me-2"></i>{{ __('messages.ratings_given') }}
            <span class="badge bg-primary ms-2">{{ $stats['total_ratings_given'] ?? 0 }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="received-tab" data-bs-toggle="tab" data-bs-target="#received"
                type="button" role="tab" aria-controls="received" aria-selected="false">
            <i class="fas fa-trophy me-2"></i>{{ __('messages.ratings_received') }}
            <span class="badge bg-success ms-2">{{ $stats['total_ratings_received'] ?? 0 }}</span>
        </button>
    </li>
</ul>

<div class="tab-content" id="ratingsTabContent">
    <!-- Ratings Given Tab -->
    <div class="tab-pane fade show active" id="given" role="tabpanel" aria-labelledby="given-tab">
        <!-- Filters for Given Ratings -->
        <div class="dashboard-filters mb-4">
            <form method="GET" action="{{ route('user.ratings') }}">
                <input type="hidden" name="tab" value="given">
                <div class="row">
                    <div class="col-md-3">
                        <div class="filter-group">
                            <label class="filter-label">{{ __('messages.rating_value') }}</label>
                            <select name="rating" class="form-select">
                                <option value="">{{ __('messages.all_ratings') }}</option>
                                <option value="5" {{ request('rating') === '5' ? 'selected' : '' }}>5 ⭐</option>
                                <option value="4" {{ request('rating') === '4' ? 'selected' : '' }}>4 ⭐</option>
                                <option value="3" {{ request('rating') === '3' ? 'selected' : '' }}>3 ⭐</option>
                                <option value="2" {{ request('rating') === '2' ? 'selected' : '' }}>2 ⭐</option>
                                <option value="1" {{ request('rating') === '1' ? 'selected' : '' }}>1 ⭐</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="filter-group">
                            <label class="filter-label">{{ __('messages.forum') }}</label>
                            <select name="forum" class="form-select">
                                <option value="">{{ __('messages.all_forums') }}</option>
                                @foreach($forums ?? [] as $forum)
                                    <option value="{{ $forum->id }}" {{ request('forum') == $forum->id ? 'selected' : '' }}>
                                        {{ $forum->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="filter-group">
                            <label class="filter-label">{{ __('messages.time_period') }}</label>
                            <select name="period" class="form-select">
                                <option value="all" {{ request('period', 'all') === 'all' ? 'selected' : '' }}>
                                    {{ __('messages.all_time') }}
                                </option>
                                <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>
                                    {{ __('messages.this_week') }}
                                </option>
                                <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>
                                    {{ __('messages.this_month') }}
                                </option>
                                <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>
                                    {{ __('messages.this_year') }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="filter-group">
                            <label class="filter-label">{{ __('messages.search') }}</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="{{ __('messages.search_threads') }}"
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Ratings Given List -->
        @if($ratings && $ratings->count() > 0)
            <div class="ratings-list">
                @foreach($ratings as $rating)
                    <div class="rating-item">
                        <div class="d-flex align-items-start">
                            @if($rating->thread && $rating->thread->featured_image)
                                <img src="{{ $rating->thread->featured_image }}" alt=""
                                     class="me-3 rounded" style="width: 60px; height: 60px; object-fit: cover;">
                            @endif
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="{{ route('threads.show', $rating->thread->slug) }}"
                                               class="text-decoration-none">
                                                {{ $rating->thread->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            {{ $rating->thread->forum->name }} •
                                            {{ $rating->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <div class="rating-display">
                                        <div class="stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $rating->rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="rating-value ms-2">{{ $rating->rating }}/5</span>
                                    </div>
                                </div>
                                @if($rating->comment)
                                    <div class="rating-comment">
                                        <p class="mb-0 text-muted">{{ $rating->comment }}</p>
                                    </div>
                                @endif
                                <div class="rating-actions mt-2">
                                    <a href="{{ route('threads.show', $rating->thread->slug) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>{{ __('messages.view_thread') }}
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="editRating({{ $rating->id }})">
                                        <i class="fas fa-edit me-1"></i>{{ __('messages.edit_rating') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($ratingsGiven->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $ratingsGiven->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="empty-state-title">{{ __('messages.no_ratings_given_yet') }}</div>
                <div class="empty-state-description">{{ __('messages.no_ratings_given_desc') }}</div>
                <a href="{{ route('threads.index') }}" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>{{ __('messages.browse_threads') }}
                </a>
            </div>
        @endif
    </div>

    <!-- Ratings Received Tab -->
    <div class="tab-pane fade" id="received" role="tabpanel" aria-labelledby="received-tab">
        @if(false) {{-- TODO: Implement ratings received when we have ratings on user's threads --}}
            <div class="ratings-list">
                @foreach([] as $rating)
                    <div class="rating-item">
                        <div class="d-flex align-items-start">
                            @if($rating->thread && $rating->thread->featured_image)
                                <img src="{{ $rating->thread->featured_image }}" alt=""
                                     class="me-3 rounded" style="width: 60px; height: 60px; object-fit: cover;">
                            @endif
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="{{ route('threads.show', $rating->thread->slug) }}"
                                               class="text-decoration-none">
                                                {{ $rating->thread->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            {{ __('messages.rated_by') }}
                                            <a href="{{ route('profile.show', $rating->user->username) }}"
                                               class="text-decoration-none">{{ $rating->user->name }}</a> •
                                            {{ $rating->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <div class="rating-display">
                                        <div class="stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $rating->rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="rating-value ms-2">{{ $rating->rating }}/5</span>
                                    </div>
                                </div>
                                @if($rating->comment)
                                    <div class="rating-comment">
                                        <p class="mb-0 text-muted">{{ $rating->comment }}</p>
                                    </div>
                                @endif
                                <div class="rating-actions mt-2">
                                    <a href="{{ route('threads.show', $rating->thread->slug) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>{{ __('messages.view_thread') }}
                                    </a>
                                    <a href="{{ route('profile.show', $rating->user->username) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-user me-1"></i>{{ __('messages.view_profile') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($ratingsReceived->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $ratingsReceived->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="empty-state-title">{{ __('messages.no_ratings_received_yet') }}</div>
                <div class="empty-state-description">{{ __('messages.no_ratings_received_desc') }}</div>
                <a href="{{ route('threads.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>{{ __('messages.create_thread') }}
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.rating-item {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: box-shadow 0.2s;
}

.rating-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.rating-display {
    text-align: right;
}

.stars {
    font-size: 1.1rem;
}

.rating-value {
    font-weight: 600;
    color: #495057;
}

.rating-comment {
    background: #f8f9fa;
    border-left: 3px solid #007bff;
    padding: 0.75rem;
    border-radius: 0 6px 6px 0;
    margin-top: 0.5rem;
}

.rating-actions .btn {
    margin-right: 0.5rem;
}
</style>

<script>
// Initialize charts if Chart.js is available
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined') {
        initializeRatingCharts();
    }
});

function initializeRatingCharts() {
    // Ratings Given Chart
    const givenCtx = document.getElementById('ratingsGivenChart').getContext('2d');
    new Chart(givenCtx, {
        type: 'doughnut',
        data: {
            labels: ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'],
            datasets: [{
                data: {!! json_encode($ratingsGivenDistribution ?? [0,0,0,0,0]) !!},
                backgroundColor: [
                    '#28a745',
                    '#17a2b8',
                    '#ffc107',
                    '#fd7e14',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Ratings Received Chart
    const receivedCtx = document.getElementById('ratingsReceivedChart').getContext('2d');
    new Chart(receivedCtx, {
        type: 'doughnut',
        data: {
            labels: ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'],
            datasets: [{
                data: {!! json_encode($ratingsReceivedDistribution ?? [0,0,0,0,0]) !!},
                backgroundColor: [
                    '#28a745',
                    '#17a2b8',
                    '#ffc107',
                    '#fd7e14',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function editRating(ratingId) {
    // Implementation for editing rating
    console.log('Edit rating:', ratingId);
    // You can implement a modal or redirect to edit page
}

// Handle tab switching with URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');

    if (activeTab === 'received') {
        const receivedTab = document.getElementById('received-tab');
        const receivedPane = document.getElementById('received');
        const givenTab = document.getElementById('given-tab');
        const givenPane = document.getElementById('given');

        givenTab.classList.remove('active');
        givenPane.classList.remove('show', 'active');
        receivedTab.classList.add('active');
        receivedPane.classList.add('show', 'active');
    }
});
</script>
@endsection
