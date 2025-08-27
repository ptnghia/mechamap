@extends('layouts.app')

@section('title', __('forum.forums.title'))

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/threads.css') }}">
@endpush

@section('content')
<div class="body_page">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="div_title_page">
            <h1 class="h2 mb-1 title_page">{{ seo_title_short(__('forum.forums.title')) }}</h1>
            <p class="text-muted mb-0">{{ seo_value('description', '')  }}</p>
        </div>
        @auth
        <a href="{{ route('threads.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> {{ __('forum.threads.create') }}
        </a>
        @endauth
    </div>

    <!-- Advanced Filters -->
    <div class="body_search mb-4">
        <form action="{{ route('threads.index') }}" method="GET" id="threadFiltersForm">
            <!-- Quick Filter Buttons -->
            <div class="mb-3 d-flex justify-content-between">
                <div class="btn-group" role="group" aria-label="Quick filters">
                    <input type="radio" class="btn-check" name="quick_filter" id="filter_all" value="" {{ !request('quick_filter') ? 'checked' : '' }}>
                    <label class="btn btn-sm btn-outline-primary" for="filter_all">{{ __('common.buttons.all') }}</label>

                    <input type="radio" class="btn-check" name="quick_filter" id="filter_today" value="today" {{ request('quick_filter') == 'today' ? 'checked' : '' }}>
                    <label class="btn btn-sm btn-outline-primary" for="filter_today">{{ __('common.time.today') }}</label>

                    <input type="radio" class="btn-check" name="quick_filter" id="filter_week" value="week" {{ request('quick_filter') == 'week' ? 'checked' : '' }}>
                    <label class="btn btn-sm btn-outline-primary" for="filter_week">{{ __('common.time.this_week') }}</label>

                    <input type="radio" class="btn-check" name="quick_filter" id="filter_month" value="month" {{ request('quick_filter') == 'month' ? 'checked' : '' }}>
                    <label class="btn btn-sm btn-outline-primary" for="filter_month">{{ __('common.time.this_month') }}</label>

                    <input type="radio" class="btn-check" name="quick_filter" id="filter_featured" value="" {{ request('featured') ? 'checked' : '' }}>
                    <label class="btn btn-sm btn-outline-primary" for="filter_featured">{{ __('forum.threads.featured') }}</label>
                    @if(request('featured'))
                        <input type="hidden" name="featured" value="1">
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleAdvancedFilters">
                    <i class="fas fa-cog me-1"></i> {{ __('forum.search.advanced') }}
                </button>
            </div>

            <!-- Basic Filters Row -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control input_search" id="q" name="q"
                        value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('forum.search.placeholder') }}">
                </div>

                <div class="col-md-3">
                    <select class="form-select select_search" id="category" name="category">
                        <option value="">{{ __('marketplace.categories.all') }}</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ ($filters['category'] ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select class="form-select select_search" id="forum" name="forum">
                        <option value="">{{ __('forum.forums.all') }}</option>
                        @foreach($forums as $forum)
                        <option value="{{ $forum->id }}" {{ ($filters['forum'] ?? '') == $forum->id ? 'selected' : '' }}>
                            {{ $forum->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Advanced Filters (Collapsible) -->
            <div id="advancedFilters" class="collapse">
                <hr>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="author" class="form-label">{{ __('forum.search.author') }}</label>
                        <input type="text" class="form-control" id="author" name="author"
                            value="{{ $filters['author'] ?? '' }}" placeholder="{{ __('forum.search.author_placeholder') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="date_from" class="form-label">{{ __('forum.search.date_from') }}</label>
                        <input type="date" class="form-control" id="date_from" name="date_from"
                            value="{{ $filters['date_from'] ?? '' }}">
                    </div>

                    <div class="col-md-4">
                        <label for="date_to" class="form-label">{{ __('forum.search.date_to') }}</label>
                        <input type="date" class="form-control" id="date_to" name="date_to"
                            value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="sort" class="form-label">{{ __('ui.actions.sort') }}</label>
                        <select class="form-select select_search" id="sort" name="sort">
                            <option value="latest" {{ $sort == 'latest' ? 'selected' : '' }}>
                                {{ __('common.buttons.latest') }}
                            </option>
                            <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>
                                {{ t_common("oldest") }}
                            </option>
                            <option value="most_viewed" {{ $sort == 'most_viewed' ? 'selected' : '' }}>
                                {{ t_common("most_viewed") }}
                            </option>
                            <option value="most_commented" {{ $sort == 'most_commented' ? 'selected' : '' }}>
                                {{ t_common("most_commented") }}
                            </option>
                            <option value="relevance" {{ $sort == 'relevance' ? 'selected' : '' }}>
                                {{ __('forum.search.relevance') }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="has_poll" name="has_poll" value="1"
                                {{ ($filters['has_poll'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_poll">
                                {{ __('forum.threads.has_poll') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary btn-search">
                    <i class="fas fa-search me-1"></i> {{ __('ui.actions.search') }}
                </button>
                <a href="{{ route('threads.index') }}" class="btn btn-sm  btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> {{ __('ui.actions.clear_filters') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Search Condition Tags -->
    @php
        $hasConditions = !empty($filters['q']) ||
                        !empty($filters['category']) ||
                        !empty($filters['forum']) ||
                        !empty($filters['author']) ||
                        !empty($filters['date_from']) ||
                        !empty($filters['date_to']) ||
                        !empty($filters['has_poll']) ||
                        !empty($filters['featured']) ||
                        !empty(request('quick_filter'));
    @endphp

    @if($hasConditions)
    <div class="search-conditions mb-4">
        <div class="d-flex flex-wrap align-items-center g-4">
            <span class="fw-medium text-muted">{{ __('forum.search.conditions') }}:</span>

            <!-- Quick Filter Tags -->
            @if(request('quick_filter'))
                <span class="condition-tag quick-filter">
                    @switch(request('quick_filter'))
                        @case('today')
                            {{ __('common.time.today') }}
                            @break
                        @case('week')
                            {{ __('common.time.this_week') }}
                            @break
                        @case('month')
                            {{ __('common.time.this_month') }}
                            @break
                        @default
                            {{ request('quick_filter') }}
                    @endswitch
                    <a href="{{ request()->fullUrlWithQuery(['quick_filter' => null]) }}" class="remove-condition" title="{{ __('forum.search.remove_condition') }}">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            <!-- Featured Tag -->
            @if(!empty($filters['featured']))
                <span class="condition-tag featured">
                    {{ __('forum.threads.featured') }}
                    <a href="{{ request()->fullUrlWithQuery(['featured' => null]) }}" class="remove-condition" title="{{ __('forum.search.remove_condition') }}">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            <!-- Search Keyword Tag -->
            @if(!empty($filters['q']))
                <span class="condition-tag">
                    {{ __('forum.search.search_keyword', ['keyword' => $filters['q']]) }}
                    <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="remove-condition" title="{{ __('forum.search.remove_condition') }}">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            <!-- Category Tag -->
            @if(!empty($filters['category']))
                @php
                    $category = $categories->firstWhere('id', $filters['category']);
                @endphp
                @if($category)
                    <span class="condition-tag">
                        {{ __('forum.search.search_category', ['category' => $category->name]) }}
                        <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="remove-condition" title="{{ __('forum.search.remove_condition') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif
            @endif

            <!-- Forum Tag -->
            @if(!empty($filters['forum']))
                @php
                    $forum = $forums->firstWhere('id', $filters['forum']);
                @endphp
                @if($forum)
                    <span class="condition-tag">
                        {{ __('forum.search.search_forum', ['forum' => $forum->name]) }}
                        <a href="{{ request()->fullUrlWithQuery(['forum' => null]) }}" class="remove-condition" title="{{ __('forum.search.remove_condition') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif
            @endif

            <!-- Author Tag -->
            @if(!empty($filters['author']))
                <span class="condition-tag">
                    {{ __('forum.search.search_author', ['author' => $filters['author']]) }}
                    <a href="{{ request()->fullUrlWithQuery(['author' => null]) }}" class="remove-condition" title="{{ __('forum.search.remove_condition') }}">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            <!-- Date From Tag -->
            @if(!empty($filters['date_from']))
                <span class="condition-tag">
                    {{ __('forum.search.search_date_from', ['date' => \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y')]) }}
                    <a href="{{ request()->fullUrlWithQuery(['date_from' => null]) }}" class="remove-condition" title="{{ __('forum.search.remove_condition') }}">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            <!-- Date To Tag -->
            @if(!empty($filters['date_to']))
                <span class="condition-tag">
                    {{ __('forum.search.search_date_to', ['date' => \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y')]) }}
                    <a href="{{ request()->fullUrlWithQuery(['date_to' => null]) }}" class="remove-condition" title="{{ __('forum.search.remove_condition') }}">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            <!-- Has Poll Tag -->
            @if(!empty($filters['has_poll']))
                <span class="condition-tag">
                    {{ __('forum.search.search_has_poll') }}
                    <a href="{{ request()->fullUrlWithQuery(['has_poll' => null]) }}" class="remove-condition" title="{{ __('forum.search.remove_condition') }}">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            <!-- Sort Tag -->
            @if(!empty($filters['sort']) && $filters['sort'] !== 'latest')
                <span class="condition-tag">
                    @switch($filters['sort'])
                        @case('oldest')
                            {{ __('forum.search.search_sort', ['sort' => __('common.buttons.oldest')]) }}
                            @break
                        @case('most_viewed')
                            {{ __('forum.search.search_sort', ['sort' => __('common.buttons.most_viewed')]) }}
                            @break
                        @case('most_replies')
                            {{ __('forum.search.search_sort', ['sort' => __('common.buttons.most_replies')]) }}
                            @break
                        @default
                            {{ __('forum.search.search_sort', ['sort' => $filters['sort']]) }}
                    @endswitch
                    <a href="{{ request()->fullUrlWithQuery(['sort' => null]) }}" class="remove-condition" title="{{ __('forum.search.remove_condition') }}">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            <!-- Clear All Button -->
            <a href="{{ route('threads.index') }}" class="clear-all-btn">
                <i class="fas fa-times-circle me-1"></i>
                {{ __('forum.search.clear_all_conditions') }}
            </a>
        </div>
    </div>
    @endif

    <!-- Threads List -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 title_page">{{ __('forum.threads.title') }}</h5>
        <span class="badge bg-secondary">{{ $threads->total() }} {{ __('forum.threads.count') }}</span>
    </div>
    @if($threads->count() > 0)
    <div class="list-group list-group-flush">
        @foreach($threads as $thread)
        @include('partials.thread-item', [
        'thread' => $thread
        ])
        @endforeach
    </div>

    <div class="">
        {{ $threads->links() }}
    </div>
    @else
    <div class="card-body text-center py-5">
        <i class="fas fa-search display-4 text-muted"></i>
        <p class="mt-3">{{ __('forum.threads.no_threads_found') }}</p>
        <a href="{{ route('threads.index') }}" class="btn btn-outline-primary">{{ __('ui.actions.clear_filters') }}</a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle advanced filters
    const toggleBtn = document.getElementById('toggleAdvancedFilters');
    const advancedFilters = document.getElementById('advancedFilters');

    if (toggleBtn && advancedFilters) {
        toggleBtn.addEventListener('click', function() {
            const isCollapsed = advancedFilters.classList.contains('show');
            if (isCollapsed) {
                advancedFilters.classList.remove('show');
                toggleBtn.innerHTML = '<i class="fas fa-cog me-1"></i> {{ __("forum.search.advanced") }}';
            } else {
                advancedFilters.classList.add('show');
                toggleBtn.innerHTML = '<i class="fas fa-cog me-1"></i> {{ __("forum.search.hide_advanced") }}';
            }
        });

        // Show advanced filters if any advanced filter is active
        const hasAdvancedFilters = {{
            ($filters['author'] ?? false) ||
            ($filters['date_from'] ?? false) ||
            ($filters['date_to'] ?? false) ||
            ($filters['has_poll'] ?? false) ||
            $sort == 'relevance' ? 'true' : 'false'
        }};

        if (hasAdvancedFilters) {
            advancedFilters.classList.add('show');
            toggleBtn.innerHTML = '<i class="fas fa-cog me-1"></i> {{ __("forum.search.hide_advanced") }}';
        }
    }

    // Quick filter buttons auto-submit
    const quickFilterBtns = document.querySelectorAll('input[name="quick_filter"]');
    quickFilterBtns.forEach(btn => {
        btn.addEventListener('change', function() {
            if (this.value === '' && this.id === 'filter_featured') {
                // Handle featured filter
                const featuredInput = document.querySelector('input[name="featured"]');
                if (featuredInput) {
                    featuredInput.remove();
                }
                const newFeaturedInput = document.createElement('input');
                newFeaturedInput.type = 'hidden';
                newFeaturedInput.name = 'featured';
                newFeaturedInput.value = '1';
                document.getElementById('threadFiltersForm').appendChild(newFeaturedInput);
            } else {
                // Remove featured filter for other quick filters
                const featuredInput = document.querySelector('input[name="featured"]');
                if (featuredInput) {
                    featuredInput.remove();
                }
            }

            // Auto-submit form
            document.getElementById('threadFiltersForm').submit();
        });
    });

    // Real-time search with debounce
    const searchInput = document.getElementById('q');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    document.getElementById('threadFiltersForm').submit();
                }
            }, 500);
        });
    }
});
</script>
@endpush
