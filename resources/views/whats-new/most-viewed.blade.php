@extends('layouts.app')

@section('title', 'Most Viewed - MechaMap')

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
                        <a class="nav-link" href="{{ route('whats-new.trending') }}">
                            <i class="fas fa-fire me-1"></i>{{ __('navigation.trending') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('whats-new.most-viewed') }}">
                            <i class="fas fa-eye me-1"></i>{{ __('navigation.most_viewed') }}
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
                    <a href="{{ route('whats-new.most-viewed', ['timeframe' => 'day']) }}"
                       class="btn {{ $timeframe == 'day' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-clock me-1"></i>Today
                    </a>
                    <a href="{{ route('whats-new.most-viewed', ['timeframe' => 'week']) }}"
                       class="btn {{ $timeframe == 'week' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-calendar-week me-1"></i>This Week
                    </a>
                    <a href="{{ route('whats-new.most-viewed', ['timeframe' => 'month']) }}"
                       class="btn {{ $timeframe == 'month' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-calendar-alt me-1"></i>This Month
                    </a>
                    <a href="{{ route('whats-new.most-viewed', ['timeframe' => 'all']) }}"
                       class="btn {{ $timeframe == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-infinity me-1"></i>All Time
                    </a>
                </div>
            </div>

            <!-- Most Viewed Description -->
            <div class="alert alert-success mb-4">
                <i class="fas fa-eye me-2"></i>
                <strong>Most Viewed:</strong>
                Threads with the highest view counts
                @if($timeframe !== 'all')
                    {{ $timeframe === 'day' ? 'today' : 'this ' . $timeframe }}
                @else
                    of all time
                @endif
                . These are the discussions that have captured the most attention from our community.
            </div>

            <!-- Thread List -->
            @if($threads->count() > 0)
                <div class="threads-list">
                    @foreach($threads as $index => $thread)
                        <div class="most-viewed-thread-wrapper mb-3 position-relative">
                            <!-- Ranking Badge -->
                            <div class="ranking-badge position-absolute top-0 start-0 translate-middle">
                                <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }} rounded-pill">
                                    #{{ $index + 1 + (($pagination['currentPage'] ?? 1) - 1) * 20 }}
                                </span>
                            </div>

                            <!-- View Count Badge -->
                            <div class="view-count-badge position-absolute top-0 end-0 translate-middle">
                                <span class="badge bg-primary">
                                    <i class="fas fa-eye me-1"></i>{{ number_format($thread->view_count) }}
                                </span>
                            </div>

                            @include('partials.thread-item', ['thread' => $thread])

                            <!-- Additional Stats for Most Viewed -->
                            @if($thread->created_at)
                                @php
                                    $daysOld = max(1, $thread->created_at->diffInDays(now()));
                                    $viewsPerDay = round($thread->view_count / $daysOld, 1);
                                @endphp
                                <div class="view-rate-info mt-2 text-muted small text-end">
                                    <i class="fas fa-chart-line me-1"></i>
                                    {{ $viewsPerDay }} views/day
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($pagination['totalPages'] > 1)
                    <div class="pagination-wrapper mt-4">
                        <nav aria-label="Most viewed threads pagination">
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
                <!-- No Content -->
                <div class="no-content text-center py-5">
                    <i class="fas fa-eye text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">No Content Found</h4>
                    <p class="text-muted mb-4">
                        There are no threads with views for the selected timeframe.
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
.most-viewed-thread-wrapper {
    position: relative;
}

.ranking-badge, .view-count-badge {
    z-index: 10;
}

.most-viewed-thread-wrapper .thread-item {
    border-left: 4px solid #0d6efd !important;
}

.view-rate-info {
    padding: 0.25rem 0.5rem;
    background-color: rgba(13, 110, 253, 0.1);
    border-radius: 0.25rem;
    margin-right: 1rem;
}
</style>
@endpush

@push('scripts')
<script>
    // Track most viewed content views
    document.addEventListener('DOMContentLoaded', function() {
        // Track page view
        if (typeof gtag !== 'undefined') {
            gtag('event', 'page_view', {
                'page_title': 'Most Viewed Content',
                'page_location': window.location.href,
                'timeframe': '{{ $timeframe }}'
            });
        }

        // Track thread clicks
        document.querySelectorAll('.thread-title').forEach(link => {
            link.addEventListener('click', function() {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'most_viewed_thread_click', {
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
