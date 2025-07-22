@extends('layouts.app')

@section('title', 'Trending This Week - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/whats-new.css') }}">
@endpush

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0 title_page">{{ __('nav.main.whats_new') }}</h1>

                <a href="{{ route('threads.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> {{ __('forum.threads.create') }}
                </a>
            </div>

            <!-- Navigation Tabs -->
            <div class="whats-new-tabs mb-4">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new') }}">{{ __('forum.posts.new') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.popular') }}">{{ __('common.buttons.popular') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('whats-new.trending') }}">
                            <i class="fas fa-fire me-1"></i>{{ __('navigation.trending') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.threads') }}">{{ __('forum.threads.new') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.showcases') }}">{{ __('showcase.new') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.media') }}">{{ __('media.new') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.replies') }}">{{ __('forum.threads.looking_for_replies') }}</a>
                    </li>
                </ul>
            </div>

            <!-- Timeframe Filter -->
            <div class="timeframe-filter mb-4">
                <div class="btn-group" role="group">
                    <a href="{{ route('whats-new.trending', ['timeframe' => 'day']) }}"
                       class="btn {{ $timeframe == 'day' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-clock me-1"></i>Today
                    </a>
                    <a href="{{ route('whats-new.trending', ['timeframe' => 'week']) }}"
                       class="btn {{ $timeframe == 'week' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-calendar-week me-1"></i>This Week
                    </a>
                    <a href="{{ route('whats-new.trending', ['timeframe' => 'month']) }}"
                       class="btn {{ $timeframe == 'month' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-calendar-alt me-1"></i>This Month
                    </a>
                </div>
            </div>

            <!-- Trending Description -->
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Trending Content:</strong>
                Threads with high engagement and recent activity.
                Trending score is calculated based on views, comments, and recency.
            </div>

            <!-- Thread List -->
            @if($threads->count() > 0)
                <div class="threads-list">
                    @foreach($threads as $thread)
                        <div class="trending-thread-wrapper mb-3">
                            @if(isset($thread->trending_score))
                                <div class="trending-badge">
                                    <span class="badge bg-danger">
                                        <i class="fas fa-fire me-1"></i>{{ number_format($thread->trending_score, 0) }}
                                    </span>
                                </div>
                            @endif
                            @include('partials.thread-item', ['thread' => $thread])
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($pagination['totalPages'] > 1)
                    <div class="pagination-wrapper mt-4">
                        <nav aria-label="Trending threads pagination">
                            <ul class="pagination justify-content-center">
                                @if($pagination['prevPageUrl'] !== '#')
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $pagination['prevPageUrl'] }}">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </a>
                                    </li>
                                @endif

                                @if($pagination['nextPageUrl'] !== '#')
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $pagination['nextPageUrl'] }}">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                @endif
            @else
                <!-- No Trending Content -->
                <div class="no-content text-center py-5">
                    <i class="fas fa-fire text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">No Trending Content</h4>
                    <p class="text-muted mb-4">
                        There's no trending content for the selected timeframe.
                        Check back later or try a different time period.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('whats-new') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-1"></i>
                            {{ __('common.actions.view_latest_posts') }}
                        </a>
                        <a href="{{ route('whats-new.popular') }}" class="btn btn-outline-success">
                            <i class="fas fa-star me-1"></i>
                            {{ __('common.actions.view_popular') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.trending-thread-wrapper {
    position: relative;
}

.trending-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    z-index: 10;
}

.trending-thread-wrapper .thread-item {
    border-left: 4px solid #fd7e14 !important;
}
</style>
@endpush

@push('scripts')
<script>
    // Auto-refresh trending content every 5 minutes
    setInterval(function() {
        // Only refresh if user is still on the page and hasn't scrolled much
        if (document.visibilityState === 'visible' && window.scrollY < 100) {
            window.location.reload();
        }
    }, 300000); // 5 minutes

    // Track trending content views
    document.addEventListener('DOMContentLoaded', function() {
        // Track page view
        if (typeof gtag !== 'undefined') {
            gtag('event', 'page_view', {
                'page_title': 'Trending Content',
                'page_location': window.location.href,
                'timeframe': '{{ $timeframe }}'
            });
        }

        // Track thread clicks
        document.querySelectorAll('.thread-title').forEach(link => {
            link.addEventListener('click', function() {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'trending_thread_click', {
                        'thread_title': this.textContent.trim(),
                        'timeframe': '{{ $timeframe }}'
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
