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
                        <a class="nav-link" href="{{ route('whats-new.popular') }}">{{ __('ui.common.popular') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('whats-new.hot-topics') }}">{{ __('navigation.hot_topics') }}</a>
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
                <strong>{{ __('whats_new.hot_topics_title') }}:</strong>
                {{ __('whats_new.hot_topics_description') }}
            </div>

            <!-- Thread List -->
            @if($threads->count() > 0)
                <div class="threads-list">
                    @foreach($threads as $index => $thread)
                        <div class="hot-topic-thread-wrapper mb-3 position-relative hot-topic-item">
                            <!-- Hot Badge -->
                            <div class="hot-badge position-absolute top-0 start-0 translate-middle">
                                <span class="badge bg-danger rounded-pill">
                                    <i class="fas fa-flame me-1"></i>{{ __('whats_new.hot_label') }}
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
                                        <small class="text-muted ms-2">{{ __('whats_new.heat_level', ['level' => number_format($thread->hot_score, 0)]) }}</small>
                                    </div>
                                </div>
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
                @if($pagination['totalPages'] > 1)
                    <div class="pagination-container mt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="pagination-info">
                                <span>{{ __('ui.pagination.page') }} {{ $pagination['currentPage'] }} {{ __('ui.pagination.of') }} {{ $pagination['totalPages'] }}</span>
                            </div>

                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-sm mb-0">
                                    <!-- Previous Page -->
                                    <li class="page-item {{ $pagination['currentPage'] <= 1 ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $pagination['prevPageUrl'] }}" aria-label="{{ __('ui.pagination.previous') }}">
                                            <span aria-hidden="true"><i class="fa-solid fa-chevron-left"></i></span>
                                        </a>
                                    </li>

                                    <!-- First Page -->
                                    @if($pagination['currentPage'] > 3)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ route('whats-new.hot-topics', ['page' => 1]) }}">1</a>
                                        </li>
                                        @if($pagination['currentPage'] > 4)
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                    @endif

                                    <!-- Page Numbers -->
                                    @for($i = max(1, $pagination['currentPage'] - 2); $i <= min($pagination['totalPages'], $pagination['currentPage'] + 2); $i++)
                                        <li class="page-item {{ $i == $pagination['currentPage'] ? 'active' : '' }}">
                                            <a class="page-link" href="{{ route('whats-new.hot-topics', ['page' => $i]) }}">{{ $i }}</a>
                                        </li>
                                    @endfor

                                    <!-- Last Page -->
                                    @if($pagination['currentPage'] < $pagination['totalPages'] - 2)
                                        @if($pagination['currentPage'] < $pagination['totalPages'] - 3)
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                        <li class="page-item">
                                            <a class="page-link" href="{{ route('whats-new.hot-topics', ['page' => $pagination['totalPages']]) }}">{{ $pagination['totalPages'] }}</a>
                                        </li>
                                    @endif

                                    <!-- Next Page -->
                                    <li class="page-item {{ $pagination['currentPage'] >= $pagination['totalPages'] ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $pagination['nextPageUrl'] }}" aria-label="{{ __('ui.pagination.next') }}">
                                            <span aria-hidden="true"><i class="fa-solid fa-chevron-right"></i></span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>

                            <div class="pagination-goto">
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control" id="pageInput" min="1" max="{{ $pagination['totalPages'] }}"
                                        value="{{ $pagination['currentPage'] }}" placeholder="{{ __('ui.pagination.page') }}">
                                    <button class="btn btn-primary" type="button" id="goToPageBtn">{{ __('ui.pagination.go_to_page') }}</button>
                                </div>
                            </div>
                        </div>
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

        // Pagination goto functionality
        const goToPageBtn = document.getElementById('goToPageBtn');
        const pageInput = document.getElementById('pageInput');

        if (goToPageBtn && pageInput) {
            goToPageBtn.addEventListener('click', function() {
                const page = parseInt(pageInput.value);
                const maxPage = parseInt(pageInput.getAttribute('max'));

                if (page >= 1 && page <= maxPage) {
                    window.location.href = `{{ route('whats-new.hot-topics') }}?page=${page}`;
                }
            });

            pageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    goToPageBtn.click();
                }
            });
        }
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
