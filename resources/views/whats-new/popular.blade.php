@extends('layouts.app')

@section('title', __('common.buttons.popular') . ' - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/whats-new.css') }}">
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

            <!-- Page Description -->
            <div class="page-description mb-4">
                <div class="alert alert-warning border-0">
                    <i class="fas fa-fire me-2"></i>
                    <strong>{{ __('ui.whats_new.popular.title') }}:</strong> {{ __('ui.whats_new.popular.description') }}
                </div>
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
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="nav-link {{ ($sortType ?? 'trending') == 'trending' ? 'active' : '' }}"
                            id="trending-tab" data-bs-toggle="tab" data-bs-target="#trending"
                            type="button" role="tab" aria-controls="trending"
                            aria-selected="{{ ($sortType ?? 'trending') == 'trending' ? 'true' : 'false' }}">
                        <i class="fas fa-fire me-1"></i>{{ __('navigation.trending') }}
                    </button>
                    <button class="nav-link {{ ($sortType ?? 'trending') == 'most_viewed' ? 'active' : '' }}"
                            id="most-viewed-tab" data-bs-toggle="tab" data-bs-target="#most-viewed"
                            type="button" role="tab" aria-controls="most-viewed"
                            aria-selected="{{ ($sortType ?? 'trending') == 'most_viewed' ? 'true' : 'false' }}">
                        <i class="fas fa-eye me-1"></i>{{ __('navigation.most_viewed') }}
                    </button>
                </div>
            </div>
            <!-- Popular Sub-Navigation -->
            <div class="popular-sub-nav mb-4">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item" role="presentation">

                            </li>
                            <li class="nav-item" role="presentation">

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
            @if($threads->hasPages())
            <div class="text-center mt-4">
                {{ $threads->links() }}
            </div>
            @endif



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
            @if($threads->hasPages())
            <div class="text-center mt-4">
                {{ $threads->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

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
