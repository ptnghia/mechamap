@extends('layouts.app')

@section('title', __('forum.search.advanced_search') . ' - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/forums/search.css') }}">
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/components/thread-item.css') }}">
@endpush

@section('content')
<div class="py-5">
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Search Header -->
                <div class="search-header rounded-lg p-4 mb-4">
                    <h1 class="h3 mb-2">
                        <i class="fas fa-search-plus me-2"></i>
                        {{ __('forum.search.advanced_title') }}
                    </h1>
                    <p class="mb-0 opacity-90">
                        {{ __('forum.search.advanced_description') }}
                    </p>
                </div>

                <!-- Advanced Search Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i>
                            {{ __('forum.search.search_filters') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('forums.search.advanced') }}" id="advancedSearchForm">
                            <div class="row g-3">
                                <!-- Keywords -->
                                <div class="col-md-6">
                                    <label for="q" class="form-label">{{ __('forum.search.keywords') }}</label>
                                    <input type="text" name="q" id="q" class="form-control"
                                        placeholder="{{ __('forum.search.keywords_placeholder') }}" value="{{ $query }}"
                                        maxlength="255">
                                </div>

                                <!-- Author -->
                                <div class="col-md-6">
                                    <label for="author" class="form-label">{{ __('forum.search.author') }}</label>
                                    <input type="text" name="author" id="author" class="form-control"
                                        placeholder="{{ __('forum.search.author_placeholder') }}" value="{{ $author }}"
                                        maxlength="100">
                                </div>

                                <!-- Category -->
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label">{{ __('forum.search.category') }}</label>
                                    <select name="category_id" id="category_id" class="form-select">
                                        <option value="">{{ __('forum.search.all_categories') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ $categoryId == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Forum -->
                                <div class="col-md-6">
                                    <label for="forum_id" class="form-label">{{ __('forum.search.forum') }}</label>
                                    <select name="forum_id" id="forum_id" class="form-select">
                                        <option value="">{{ __('forum.search.all_forums') }}</option>
                                        @foreach($forums as $forum)
                                            <option value="{{ $forum->id }}"
                                                {{ $forumId == $forum->id ? 'selected' : '' }}>
                                                {{ $forum->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Date Range -->
                                <div class="col-md-6">
                                    <label for="date_from" class="form-label">{{ __('forum.search.date_from') }}</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control"
                                        value="{{ $dateFrom }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="date_to" class="form-label">{{ __('forum.search.date_to') }}</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control"
                                        value="{{ $dateTo }}">
                                </div>

                                <!-- Sort Options -->
                                <div class="col-md-6">
                                    <label for="sort_by" class="form-label">{{ __('forum.search.sort_by') }}</label>
                                    <select name="sort_by" id="sort_by" class="form-select">
                                        <option value="date" {{ $sortBy == 'date' ? 'selected' : '' }}>{{ __('forum.search.date') }}</option>
                                        <option value="replies" {{ $sortBy == 'replies' ? 'selected' : '' }}>{{ __('forum.search.replies') }}</option>
                                        <option value="views" {{ $sortBy == 'views' ? 'selected' : '' }}>{{ __('forum.search.views') }}</option>
                                        <option value="relevance" {{ $sortBy == 'relevance' ? 'selected' : '' }}>{{ __('forum.search.relevance') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="sort_dir" class="form-label">{{ __('forum.search.sort_direction') }}</label>
                                    <select name="sort_dir" id="sort_dir" class="form-select">
                                        <option value="desc" {{ $sortDir == 'desc' ? 'selected' : '' }}>{{ __('forum.search.descending') }}</option>
                                        <option value="asc" {{ $sortDir == 'asc' ? 'selected' : '' }}>{{ __('forum.search.ascending') }}</option>
                                    </select>
                                </div>

                                <!-- Search Buttons -->
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i>
                                            {{ __('forum.search.search_button') }}
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="clearForm()">
                                            <i class="fas fa-times me-1"></i>
                                            {{ __('forum.search.clear_filters') }}
                                        </button>
                                        <a href="{{ route('forums.search') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-search me-1"></i>
                                            {{ __('forum.search.title') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Search Results -->
                @if(isset($threads) && $threads->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-comments me-2"></i>
                            {{ __('forum.search.results') }} ({{ $threads->total() }})
                        </h5>
                        <div class="search-stats">
                            <small>
                                {{ __('forum.search.sort_by') }} {{ ucfirst(__('forum.search.' . $sortBy)) }} ({{ ucfirst(__('forum.search.' . $sortDir . 'ending')) }})
                            </small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @foreach($threads as $thread)
                        <div class="search-result-wrapper border-bottom">
                            @php
                            // Highlight search query in thread title and content for search results
                            $originalTitle = $thread->title;
                            $originalContent = $thread->content ?? '';

                            // Apply highlighting if query exists
                            if($query) {
                                $thread->title = highlightSearchQuery($thread->title, $query);
                                $thread->content = highlightSearchQuery(strip_tags($originalContent), $query);
                            }
                            @endphp

                            @include('partials.thread-item', ['thread' => $thread])

                            @php
                            // Restore original values
                            $thread->title = $originalTitle;
                            $thread->content = $originalContent;
                            @endphp
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        {{ $threads->appends(request()->query())->links() }}
                    </div>
                </div>
                @elseif(request()->has('q') || request()->has('author') || request()->has('category_id'))
                <!-- No Results -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
                        <h4 class="text-muted mb-2">{{ __('forum.search.no_results_found_advanced') }}</h4>
                        <p class="text-muted mb-4">
                            {{ __('forum.search.no_results_message_advanced') }}
                        </p>
                        <button type="button" class="btn btn-outline-primary" onclick="clearForm()">
                            <i class="fas fa-times me-1"></i>
                            {{ __('forum.search.clear_filters') }}
                        </button>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <!-- Search Tips -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            {{ __('forum.search.search_tips') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <strong>{{ __('forum.search.keywords') }}:</strong> {{ __('forum.search.tip_quotes') }}
                            </li>
                            <li class="mb-2">
                                <strong>{{ __('forum.search.author') }}:</strong> {{ __('forum.search.author_placeholder') }}
                            </li>
                            <li class="mb-2">
                                <strong>{{ __('forum.search.date_from') }}:</strong> {{ __('forum.search.tip_minimum_chars') }}
                            </li>
                            <li class="mb-2">
                                <strong>{{ __('forum.search.category') }}:</strong> {{ __('forum.search.tip_browse_categories') }}
                            </li>
                            <li class="mb-0">
                                <strong>{{ __('forum.search.sort_by') }}:</strong> {{ __('forum.search.tip_multiple_words') }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Quick Filters -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            {{ __('forum.search.quick_filters') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('forums.search.advanced', ['sort_by' => 'date', 'sort_dir' => 'desc']) }}"
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-clock me-1"></i>
                                {{ __('forum.search.latest_threads') }}
                            </a>
                            <a href="{{ route('forums.search.advanced', ['sort_by' => 'replies', 'sort_dir' => 'desc']) }}"
                               class="btn btn-outline-success btn-sm">
                                <i class="fas fa-comments me-1"></i>
                                {{ __('forum.search.most_replies') }}
                            </a>
                            <a href="{{ route('forums.search.advanced', ['sort_by' => 'views', 'sort_dir' => 'desc']) }}"
                               class="btn btn-outline-info btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                {{ __('forum.search.most_viewed') }}
                            </a>
                            <a href="{{ route('forums.search.advanced', ['date_from' => now()->subWeek()->format('Y-m-d')]) }}"
                               class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-calendar me-1"></i>
                                {{ __('forum.search.this_week') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function clearForm() {
        document.getElementById('advancedSearchForm').reset();
        // Clear URL parameters
        window.location.href = '{{ route("forums.search.advanced") }}';
    }

    // Category change handler to update forums
    document.getElementById('category_id').addEventListener('change', function() {
        const categoryId = this.value;
        const forumSelect = document.getElementById('forum_id');

        // Clear current forum options except "All Forums"
        forumSelect.innerHTML = '<option value="">All Forums</option>';

        if (categoryId) {
            // Filter forums by category (you might want to implement this via AJAX)
            @foreach($categories as $category)
                if (categoryId == '{{ $category->id }}') {
                    @foreach($category->forums as $forum)
                        forumSelect.innerHTML += '<option value="{{ $forum->id }}">{{ $forum->name }}</option>';
                    @endforeach
                }
            @endforeach
        } else {
            // Show all forums
            @foreach($forums as $forum)
                forumSelect.innerHTML += '<option value="{{ $forum->id }}">{{ $forum->name }}</option>';
            @endforeach
        }
    });

    // Initialize form
    document.addEventListener('DOMContentLoaded', function() {
        // Focus on keywords input
        document.getElementById('q').focus();

        // Set max date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date_from').max = today;
        document.getElementById('date_to').max = today;
    });
</script>
@endpush
@endsection
