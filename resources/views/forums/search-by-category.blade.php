@extends('layouts.app')

@section('title', isset($category) ? __('forum.search.search_in_category') . " {$category->name} - MechaMap Forums" : __('forum.search.search_by_category') . " - MechaMap Forums")

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
                <!-- Category Header -->
                <div class="search-header rounded-lg p-4 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2">
                                <i class="fas fa-folder-open me-2"></i>
                                @if(isset($category))
                                    {{ __('forum.search.search_in_category') }} {{ $category->name }}
                                @else
                                    {{ __('forum.search.search_by_category') }}
                                @endif
                            </h1>
                            <p class="mb-0 opacity-90">
                                @if(isset($category))
                                    @if($query ?? false)
                                        {{ __('forum.search.results_for') }} "<strong>{{ $query }}</strong>" {{ __('forum.search.search_in_forum') }} {{ $category->name }}
                                    @else
                                        {{ __('forum.search.browse_all_threads_in_category') }} {{ $category->name }}
                                    @endif
                                @else
                                    {{ __('forum.search.select_category_to_search') }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="stats">
                                @if(isset($threads))
                                    <span class="badge bg-light text-dark">
                                        {{ $threads->total() }} {{ __('forum.search.threads_found') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if(!isset($category))
                <!-- Category Selection Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('forums.search.categories') }}" class="row g-3">
                            <div class="col-md-8">
                                <label for="category_select" class="form-label">{{ __('forum.search.select_category') }}</label>
                                <select name="category_id" id="category_select" class="form-select" required>
                                    <option value="">{{ __('forum.search.choose_category') }}</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">
                                            {{ $cat->name }} ({{ $cat->forums->count() }} {{ __('forum.search.forums_count') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="q" class="form-label">{{ __('forum.search.search_query_optional') }}</label>
                                <input type="text" name="q" id="q" class="form-control"
                                    placeholder="{{ __('forum.search.search_query_placeholder') }}" maxlength="255">
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>
                                    {{ __('forum.search.search_in_selected_category') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @else
                <!-- Search Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('forums.search.categories') }}" class="row g-3">
                            <input type="hidden" name="category_id" value="{{ $category->id }}">

                            <div class="col-md-6">
                                <label for="q" class="form-label">{{ __('forum.search.search_in_category') }} {{ $category->name }}</label>
                                <input type="text" name="q" id="q" class="form-control"
                                    placeholder="{{ __('forum.search.search_query_placeholder') }}" value="{{ $query ?? '' }}"
                                    maxlength="255">
                            </div>

                            <div class="col-md-4">
                                <label for="category_select" class="form-label">{{ __('forum.search.change_category') }}</label>
                                <select name="category_id" id="category_select" class="form-select" onchange="changeCategory()">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $cat->id == $category->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>
                                    {{ __('forum.search.search_button') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                @if(isset($category))
                <!-- Category Forums -->
                @if($category->forums->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-comments me-2"></i>
                            {{ __('forum.search.forums_in_category', ['category' => $category->name]) }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($category->forums as $forum)
                            <div class="col-md-6 mb-3">
                                <div class="forum-card p-3 border rounded">
                                    <h6 class="mb-2">
                                        <a href="{{ route('forums.show', $forum) }}" class="text-decoration-none">
                                            {{ $forum->name }}
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-2">{{ $forum->description }}</p>
                                    <div class="d-flex justify-content-between text-sm">
                                        <span class="text-muted">
                                            <i class="fas fa-comments me-1"></i>
                                            {{ $forum->threads_count ?? 0 }} {{ __('forum.threads') }}
                                        </span>
                                        <a href="{{ route('forums.search.categories', ['category_id' => $category->id, 'forum_id' => $forum->id]) }}"
                                           class="text-primary text-decoration-none">
                                            <i class="fas fa-search me-1"></i>
                                            {{ __('forum.search.search_in_forum') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @else
                <!-- Category List when no category selected -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-folder me-2"></i>
                            {{ __('forum.search.available_categories') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($categories as $cat)
                            <div class="col-md-6 mb-3">
                                <div class="category-card p-3 border rounded">
                                    <h6 class="mb-2">
                                        <a href="{{ route('forums.search.categories', ['category_id' => $cat->id]) }}" class="text-decoration-none">
                                            <i class="fas fa-folder-open me-2"></i>
                                            {{ $cat->name }}
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-2">{{ $cat->description ?? 'No description available' }}</p>
                                    <div class="text-sm text-muted">
                                        <i class="fas fa-comments me-1"></i>
                                        {{ $cat->forums->count() }} forums
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Thread Results -->
                @if(isset($threads) && $threads->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Threads ({{ $threads->total() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($threads as $thread)
                        <div class="search-result-wrapper border-bottom">
                            @php
                            // Highlight search query in thread title and content for search results
                            $originalTitle = $thread->title;
                            $originalContent = $thread->content ?? '';

                            // Apply highlighting if query exists
                            if($query ?? false) {
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
                @elseif(isset($category))
                <!-- No Results -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-folder-open text-muted mb-3" style="font-size: 3rem;"></i>
                        <h4 class="text-muted mb-2">
                            @if($query ?? false)
                                {{ __('forum.search.no_results_found') }}
                            @else
                                {{ __('forum.no_threads') }}
                            @endif
                        </h4>
                        <p class="text-muted mb-4">
                            @if($query ?? false)
                                {{ __('forum.search.no_threads_in_category', ['category' => $category->name]) }} "<strong>{{ $query }}</strong>".
                            @else
                                This category doesn't have any threads yet. Be the first to start a discussion!
                            @endif
                        </p>
                        <div class="d-flex gap-2 justify-content-center">
                            @if($query)
                                <a href="{{ route('forums.search.categories', ['category_id' => $category->id]) }}"
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-list me-1"></i>
                                    {{ __('forum.search.browse_all_threads') }}
                                </a>
                            @endif
                            <a href="{{ route('forums.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                {{ __('forum.search.back_to_forums') }}
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                @if(isset($category))
                <!-- Category Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('forum.search.category_info') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <h6>{{ $category->name }}</h6>
                        @if($category->description)
                            <p class="text-muted small mb-3">{{ $category->description }}</p>
                        @endif
                        <div class="stats">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Forums:</span>
                                <strong>{{ $category->forums->count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Threads:</span>
                                <strong>{{ $category->forums->sum('threads_count') ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <!-- Search Tips when no category selected -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            Search Tips
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Select a category to narrow your search
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Use specific keywords for better results
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Browse categories to discover content
                            </li>
                        </ul>
                    </div>
                </div>
                @endif

                @if(isset($category))
                <!-- Other Categories -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-folder me-2"></i>
                            {{ __('forum.search.other_categories') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($categories->where('id', '!=', $category->id) as $otherCategory)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <a href="{{ route('forums.search.categories', ['category_id' => $otherCategory->id]) }}"
                               class="text-decoration-none">
                                {{ $otherCategory->name }}
                            </a>
                            <span class="badge bg-light text-dark">
                                {{ $otherCategory->forums->count() }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(isset($category))
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            {{ __('forum.search.quick_actions') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('threads.index', ['category' => $category->id]) }}"
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-search-plus me-1"></i>
                                {{ __('forum.search.advanced_search') }}
                            </a>
                            <a href="{{ route('forums.index') }}"
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-list me-1"></i>
                                {{ __('forum.search.all_categories') }}
                            </a>
                            @auth
                            <a href="{{ route('threads.create') }}"
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                Start New Thread
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @else
                <!-- Quick Actions when no category selected -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('forums.index') }}"
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-list me-1"></i>
                                {{ __('forum.search.all_categories') }}
                            </a>
                            @auth
                            <a href="{{ route('threads.create') }}"
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                Start New Thread
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function changeCategory() {
        const categorySelect = document.getElementById('category_select');
        const categoryId = categorySelect.value;
        const currentQuery = document.getElementById('q').value;

        let url = '{{ route("forums.search.categories") }}?category_id=' + categoryId;
        if (currentQuery) {
            url += '&q=' + encodeURIComponent(currentQuery);
        }

        window.location.href = url;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Focus on search input
        document.getElementById('q').focus();
    });
</script>
@endpush
@endsection
