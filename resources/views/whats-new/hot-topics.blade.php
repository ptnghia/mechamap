@extends('layouts.app')

@section('title', 'Hot Topics - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/whats-new.css') }}">
@endpush

@section('content')
<div class="body_page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="div_title_page">
            <h1 class="h2 mb-1 title_page">{{ seo_title_short(__('navigation.hot_topics')) }}</h1>
            <p class="text-muted mb-0">{{ seo_value('description', __('ui.whats_new.hot_topics.description'))  }}</p>
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
                <a class="nav-link active" href="{{ route('whats-new.hot-topics') }}"><i class="fa-solid fa-fire-flame-curved me-1"></i>{{ __('whats_new.hot_topics') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.threads') }}"><i class="fa-solid fa-rss me-1"></i>{{ __('forum.threads.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.showcases') }}"><i class="fa-solid fa-compass-drafting me-1"></i>{{ __('showcase.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.media') }}"><i class="fa-solid fa-photo-film me-1"></i>{{ __('media.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.replies') }}"><i class="fa-solid fa-question me-1"></i>{{ __('forum.threads.looking_for_replies') }}</a>
            </li>
        </ul>
    </div>
    <!-- Thread List -->
    @if($threads->count() > 0)
        <div class="threads-list">
            @foreach($threads as $index => $thread)
                <div class="hot-topic-thread-wrapper mb-3 position-relative hot-topic-item">
                    <!-- Hot Badge -->
                    <div class="hot-badge position-absolute top-0 start-0 translate-middle">
                        <span class="badge bg-danger rounded-pill">
                            <i class="fas fa-flame me-1"></i>{{ $thread->hot_score }}
                        </span>
                    </div>

                    <!-- Activity Indicator -->
                    @if(isset($thread->recent_comments) && $thread->recent_comments > 0)
                        <div class="activity-badge position-absolute top-0 end-0 translate-middle">
                            <span class="badge bg-success rounded-pill">
                                {{ __('whats_new.activity_today', ['count' => $thread->recent_comments]) }}
                            </span>
                        </div>
                    @endif

                    @include('partials.thread-item', ['thread' => $thread])

                    <!-- Hot Score Display -->
                    @if(isset($thread->hot_score))
                        <!--div class="hot-score-display mt-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-thermometer-full me-1"></i>{{ number_format($thread->hot_score, 0) }}
                                </span>
                                <div class="progress flex-grow-1 ms-2" style="height: 8px;">
                                    <div class="progress-bar bg-danger"
                                            style="width: {{ min(100, ($thread->hot_score / 100) * 100) }}%">
                                    </div>
                                </div>
                                <small class="text-muted ms-2">{{ __('whats_new.heat_level', ['level' => number_format($thread->hot_score, 0)]) }}</small>
                            </div>
                        </!--div-->
                    @endif

                    <!-- Recent Activity Indicator -->
                    @if(isset($thread->recent_comments) && $thread->recent_comments > 0)
                        <div class="recent-activity-display mt-2">
                            <span class="badge bg-success">
                                <i class="fas fa-comments me-1"></i>
                                {{ __('whats_new.new_comments_today', ['count' => $thread->recent_comments]) }}
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($threads->hasPages())
        <div class="text-center mt-4">
            {{ $threads->links() }}
        </div>
        @endif


    @else
        <!-- No Hot Topics -->
        <div class="no-content text-center py-5">
            <i class="fas fa-flame text-muted mb-3" style="font-size: 4rem;"></i>
            <h4 class="text-muted mb-3">{{ __('whats_new.no_hot_topics') }}</h4>
            <p class="text-muted mb-4">
                {{ __('whats_new.no_hot_topics_description') }}
            </p>
            <div class="d-flex gap-2 justify-content-center">
                <a href="{{ route('threads.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    {{ __('forum.threads.create') }}
                </a>
                <a href="{{ route('whats-new') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list me-1"></i>
                    {{ __('common.actions.view_latest_posts') }}
                </a>
                <a href="{{ route('whats-new.popular') }}" class="btn btn-outline-warning">
                    <i class="fas fa-star me-1"></i>
                    {{ __('ui.common.popular') }}
                </a>
            </div>
        </div>
    @endif
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
        //document.querySelectorAll('.hot-topic-item').forEach(item => {
        //    item.style.animation = 'pulse 2s infinite';
        //});


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
