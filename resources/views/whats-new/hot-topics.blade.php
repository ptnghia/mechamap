@extends('layouts.app')

@section('title', 'Hot Topics - MechaMap')

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
                        <a class="nav-link" href="{{ route('whats-new.most-viewed') }}">
                            <i class="fas fa-eye me-1"></i>{{ __('navigation.most_viewed') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('whats-new.hot-topics') }}">
                            <i class="fas fa-flame me-1"></i>{{ __('navigation.hot_topics') }}
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

            <!-- Hot Topics Description -->
            <div class="alert alert-warning mb-4">
                <i class="fas fa-flame me-2"></i>
                <strong>Hot Topics:</strong>
                Threads with high recent activity and engagement.
                These discussions are generating the most buzz in the community right now.
            </div>

            <!-- Thread List -->
            @if($threads->count() > 0)
                <div class="threads-list">
                    @foreach($threads as $index => $thread)
                        <div class="hot-topic-thread-wrapper mb-3 position-relative hot-topic-item">
                            <!-- Hot Badge -->
                            <div class="hot-badge position-absolute top-0 start-0 translate-middle">
                                <span class="badge bg-danger rounded-pill">
                                    <i class="fas fa-flame me-1"></i>HOT
                                </span>
                            </div>

                            <!-- Activity Indicator -->
                            @if(isset($thread->recent_comments) && $thread->recent_comments > 0)
                                <div class="activity-badge position-absolute top-0 end-0 translate-middle">
                                    <span class="badge bg-success rounded-pill">
                                        +{{ $thread->recent_comments }} today
                                    </span>
                                </div>
                            @endif

                            @include('partials.thread-item', ['thread' => $thread])

                            <!-- Hot Score Display -->
                            @if(isset($thread->hot_score))
                                <div class="hot-score-display mt-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-thermometer-full me-1"></i>{{ number_format($thread->hot_score, 0) }}
                                        </span>
                                        <div class="progress flex-grow-1 ms-2" style="height: 8px;">
                                            <div class="progress-bar bg-danger"
                                                 style="width: {{ min(100, ($thread->hot_score / 100) * 100) }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted ms-2">Heat Level: {{ number_format($thread->hot_score, 0) }}</small>
                                    </div>
                                </div>
                            @endif

                            <!-- Recent Activity Indicator -->
                            @if(isset($thread->recent_comments) && $thread->recent_comments > 0)
                                <div class="recent-activity-display mt-2">
                                    <span class="badge bg-success">
                                        <i class="fas fa-comments me-1"></i>
                                        {{ $thread->recent_comments }} new comment{{ $thread->recent_comments > 1 ? 's' : '' }} today
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($pagination['totalPages'] > 1)
                    <div class="pagination-wrapper mt-4">
                        <nav aria-label="Hot topics pagination">
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
                <!-- No Hot Topics -->
                <div class="no-content text-center py-5">
                    <i class="fas fa-flame text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">No Hot Topics Right Now</h4>
                    <p class="text-muted mb-4">
                        There are no particularly hot discussions at the moment.
                        Check back later or start a new conversation!
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('threads.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Start New Discussion
                        </a>
                        <a href="{{ route('whats-new') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-1"></i>
                            {{ __('common.actions.view_latest_posts') }}
                        </a>
                        <a href="{{ route('whats-new.trending') }}" class="btn btn-outline-warning">
                            <i class="fas fa-fire me-1"></i>
                            {{ __('common.actions.view_trending') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-refresh hot topics every 2 minutes
    setInterval(function() {
        // Only refresh if user is still on the page and hasn't scrolled much
        if (document.visibilityState === 'visible' && window.scrollY < 100) {
            window.location.reload();
        }
    }, 120000); // 2 minutes

    // Track hot topics views
    document.addEventListener('DOMContentLoaded', function() {
        // Track page view
        if (typeof gtag !== 'undefined') {
            gtag('event', 'page_view', {
                'page_title': 'Hot Topics',
                'page_location': window.location.href
            });
        }

        // Track thread clicks
        document.querySelectorAll('.thread-title').forEach(link => {
            link.addEventListener('click', function() {
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'hot_topic_thread_click', {
                        'thread_title': this.textContent.trim()
                    });
                }
            });
        });

        // Add pulsing animation to hot items
        document.querySelectorAll('.hot-topic-item').forEach(item => {
            item.style.animation = 'pulse 2s infinite';
        });
    });
</script>

<style>
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}

.hot-topic-item .thread-item {
    border-left: 4px solid #dc3545 !important;
    animation: pulse 2s infinite;
}

.hot-badge, .activity-badge {
    z-index: 10;
}

.hot-score-display {
    padding: 0.5rem;
    background-color: rgba(220, 53, 69, 0.1);
    border-radius: 0.375rem;
}

.recent-activity-display {
    text-align: center;
}
</style>
@endpush
@endsection
