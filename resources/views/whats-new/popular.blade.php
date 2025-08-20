@extends('layouts.app')

@section('title', __('common.buttons.popular') . ' - MechaMap')

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
                        <a class="nav-link active" href="{{ route('whats-new.popular') }}">{{ __('ui.common.popular') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.hot-topics') }}">{{ __('whats_new.hot_topics') }}</a>
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

            <!-- Popular Sub-Navigation -->
            <div class="popular-sub-nav mb-4">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ ($sortType ?? 'trending') == 'trending' ? 'active' : '' }}"
                                        id="trending-tab" data-bs-toggle="tab" data-bs-target="#trending"
                                        type="button" role="tab" aria-controls="trending"
                                        aria-selected="{{ ($sortType ?? 'trending') == 'trending' ? 'true' : 'false' }}">
                                    <i class="fas fa-fire me-1"></i>{{ __('navigation.trending') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ ($sortType ?? 'trending') == 'most_viewed' ? 'active' : '' }}"
                                        id="most-viewed-tab" data-bs-toggle="tab" data-bs-target="#most-viewed"
                                        type="button" role="tab" aria-controls="most-viewed"
                                        aria-selected="{{ ($sortType ?? 'trending') == 'most_viewed' ? 'true' : 'false' }}">
                                    <i class="fas fa-eye me-1"></i>{{ __('navigation.most_viewed') }}
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Timeframe Filter Dropdown -->
            <div class="timeframe-filter mb-4">
                <div class="d-flex align-items-center">
                    <label for="timeframe-select" class="form-label me-2 mb-0">
                        <i class="fas fa-clock me-1"></i>{{ __('ui.common.timeframe') }}:
                    </label>
                    <select id="timeframe-select" class="form-select" style="width: auto;">
                        <option value="day" {{ $timeframe == 'day' ? 'selected' : '' }}>{{ __('activity.today') }}</option>
                        <option value="week" {{ $timeframe == 'week' ? 'selected' : '' }}>{{ __('activity.this_week') }}</option>
                        <option value="month" {{ $timeframe == 'month' ? 'selected' : '' }}>{{ __('activity.this_month') }}</option>
                        <option value="year" {{ $timeframe == 'year' ? 'selected' : '' }}>{{ __('common.time.this_year') }}</option>
                        <option value="all" {{ $timeframe == 'all' ? 'selected' : '' }}>{{ __('activity.all_time') }}</option>
                    </select>
                </div>
            </div>

            <!-- Pagination Top -->
            <div class="pagination-container mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="pagination-info">
                        <span>{{ __('ui.pagination.page') }} {{ $page }} {{ __('ui.pagination.of') }} {{ $totalPages }}</span>
                    </div>

                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Previous Page -->
                            <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $prevPageUrl }}" aria-label="{{ __('ui.pagination.previous') }}">
                                    <span aria-hidden="true"><i class="fa-solid fa-chevron-left"></i></span>
                                </a>
                            </li>

                            <!-- First Page -->
                            @if($page > 3)
                            <li class="page-item">
                                <a class="page-link"
                                    href="{{ route('whats-new.popular', ['page' => 1, 'timeframe' => $timeframe]) }}">1</a>
                            </li>
                            @endif

                            <!-- Ellipsis for skipped pages -->
                            @if($page > 4)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            @endif

                            <!-- Pages before current -->
                            @for($i = max(1, $page - 2); $i < $page; $i++) <li class="page-item">
                                <a class="page-link"
                                    href="{{ route('whats-new.popular', ['page' => $i, 'timeframe' => $timeframe]) }}">{{
                                    $i }}</a>
                                </li>
                                @endfor

                                <!-- Current Page -->
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>

                                <!-- Pages after current -->
                                @for($i = $page + 1; $i <= min($totalPages, $page + 2); $i++) <li class="page-item">
                                    <a class="page-link"
                                        href="{{ route('whats-new.popular', ['page' => $i, 'timeframe' => $timeframe]) }}">{{
                                        $i }}</a>
                                    </li>
                                    @endfor

                                    <!-- Ellipsis for skipped pages -->
                                    @if($page < $totalPages - 3) <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                        </li>
                                        @endif

                                        <!-- Last Page -->
                                        @if($page < $totalPages - 2) <li class="page-item">
                                            <a class="page-link"
                                                href="{{ route('whats-new.popular', ['page' => $totalPages, 'timeframe' => $timeframe]) }}">{{
                                                $totalPages }}</a>
                                            </li>
                                            @endif

                                            <!-- Next Page -->
                                            <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $nextPageUrl }}" aria-label="{{ __('ui.pagination.next') }}">
                                                    <span aria-hidden="true"><i class="fa-solid fa-chevron-right"></i></span>
                                                </a>
                                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- Threads List -->
            <div class="body_left">
                <div class="list-group list-group-flush">
                    @foreach($threads as $thread)
                    @include('partials.thread-item', [
                    'thread' => $thread
                    ])
                    @endforeach
                </div>
            </div>

            <!-- Pagination Bottom -->
            <div class="pagination-container mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="pagination-info">
                        <span>{{ __('ui.pagination.page') }} {{ $page }} {{ __('ui.pagination.of') }} {{ $totalPages }}</span>
                    </div>

                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Previous Page -->
                            <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $prevPageUrl }}" aria-label="{{ __('ui.pagination.previous') }}">
                                    <span aria-hidden="true"><i class="fa-solid fa-chevron-left"></i></span>
                                </a>
                            </li>

                            <!-- First Page -->
                            @if($page > 3)
                            <li class="page-item">
                                <a class="page-link"
                                    href="{{ route('whats-new.popular', ['page' => 1, 'timeframe' => $timeframe]) }}">1</a>
                            </li>
                            @endif

                            <!-- Ellipsis for skipped pages -->
                            @if($page > 4)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            @endif

                            <!-- Pages before current -->
                            @for($i = max(1, $page - 2); $i < $page; $i++) <li class="page-item">
                                <a class="page-link"
                                    href="{{ route('whats-new.popular', ['page' => $i, 'timeframe' => $timeframe]) }}">{{
                                    $i }}</a>
                                </li>
                                @endfor

                                <!-- Current Page -->
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>

                                <!-- Pages after current -->
                                @for($i = $page + 1; $i <= min($totalPages, $page + 2); $i++) <li class="page-item">
                                    <a class="page-link"
                                        href="{{ route('whats-new.popular', ['page' => $i, 'timeframe' => $timeframe]) }}">{{
                                        $i }}</a>
                                    </li>
                                    @endfor

                                    <!-- Ellipsis for skipped pages -->
                                    @if($page < $totalPages - 3) <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                        </li>
                                        @endif

                                        <!-- Last Page -->
                                        @if($page < $totalPages - 2) <li class="page-item">
                                            <a class="page-link"
                                                href="{{ route('whats-new.popular', ['page' => $totalPages, 'timeframe' => $timeframe]) }}">{{
                                                $totalPages }}</a>
                                            </li>
                                            @endif

                                            <!-- Next Page -->
                                            <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $nextPageUrl }}" aria-label="{{ __('ui.pagination.next') }}">
                                                    <span aria-hidden="true"><i class="fa-solid fa-chevron-right"></i></span>
                                                </a>
                                            </li>
                        </ul>
                    </nav>

                    <div class="pagination-goto">
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control" id="pageInput" min="1" max="{{ $totalPages }}"
                                value="{{ $page }}" placeholder="{{ __('ui.pagination.page') }}">
                            <button class="btn btn-primary" type="button" id="goToPageBtn">{{ __('ui.pagination.go_to_page') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const goToPageBtn = document.getElementById('goToPageBtn');
        const pageInput = document.getElementById('pageInput');

        goToPageBtn.addEventListener('click', function() {
            const page = parseInt(pageInput.value);
            if (page >= 1 && page <= {{ $totalPages }}) {
                window.location.href = '{{ route("whats-new.popular") }}?page=' + page + '&timeframe={{ $timeframe }}';
            }
        });

        pageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                goToPageBtn.click();
            }
        });

        // Handle sub-navigation tab switching
        const trendingTab = document.getElementById('trending-tab');
        const mostViewedTab = document.getElementById('most-viewed-tab');

        if (trendingTab) {
            trendingTab.addEventListener('click', function() {
                const currentUrl = new URL(window.location);
                currentUrl.searchParams.set('sort', 'trending');
                currentUrl.searchParams.set('page', '1'); // Reset to page 1
                window.location.href = currentUrl.toString();
            });
        }

        if (mostViewedTab) {
            mostViewedTab.addEventListener('click', function() {
                const currentUrl = new URL(window.location);
                currentUrl.searchParams.set('sort', 'most_viewed');
                currentUrl.searchParams.set('page', '1'); // Reset to page 1
                window.location.href = currentUrl.toString();
            });
        }

        // Handle timeframe dropdown change
        const timeframeSelect = document.getElementById('timeframe-select');
        if (timeframeSelect) {
            timeframeSelect.addEventListener('change', function() {
                const currentUrl = new URL(window.location);
                currentUrl.searchParams.set('timeframe', this.value);
                currentUrl.searchParams.set('page', '1'); // Reset to page 1
                window.location.href = currentUrl.toString();
            });
        }
    });
</script>
@endpush

@endsection
