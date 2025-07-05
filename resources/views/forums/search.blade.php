@extends('layouts.app')

@section('title', "Search Results for '{$query}' - MechaMap Forums")

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/forums/search.css') }}">
@endpush

@section('content')
<div class="py-5">
    <div class="container">
        <!-- Search Header -->
        <div class="search-header rounded-lg p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-2">
                        <i class="fas fa-search me-2"></i>
                        {{ __('forums.search.results') }}
                    </h1>
                    <p class="mb-0 opacity-90">
                        {{ __('forums.search.results_for') }}: <strong>"{{ $query }}"</strong>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="stats">
                        <span class="badge bg-light text-dark me-2">
                            {{ $threads->total() }} threads
                        </span>
                        <span class="badge bg-light text-dark">
                            {{ $posts->total() }} posts
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('forums.search') }}" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="q" class="form-control"
                            placeholder="Search threads, posts, and discussions..." value="{{ $query }}" required
                            minlength="3">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Thread Results -->
                @if($threads->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-comments me-2"></i>
                            Thread Results ({{ $threads->total() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($threads as $thread)
                        <div class="search-result-item p-3 border-bottom">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <img src="{{ $thread->user->avatar ?? '/images/default-avatar.png' }}"
                                        alt="{{ $thread->user->name }}" class="rounded-circle" width="40" height="40">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('threads.show', $thread) }}" class="text-decoration-none">
                                            {!! highlightSearchQuery($thread->title, $query) !!}
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        {!! Str::limit(highlightSearchQuery(strip_tags($thread->body), $query), 150) !!}
                                    </p>
                                    <div class="d-flex align-items-center text-sm text-muted">
                                        <span class="me-3">
                                            <i class="fas fa-user me-1"></i>
                                            {{ $thread->user->name }}
                                        </span>
                                        <span class="me-3">
                                            <i class="fas fa-folder me-1"></i>
                                            <a href="{{ route('forums.show', $thread->forum) }}"
                                                class="text-muted text-decoration-none">
                                                {{ $thread->forum->name }}
                                            </a>
                                        </span>
                                        <span class="me-3">
                                            <i class="fas fa-comments me-1"></i>
                                            {{ $thread->comments_count }} replies
                                        </span>
                                        <span>
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $thread->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        {{ $threads->links() }}
                    </div>
                </div>
                @endif

                <!-- Post Results -->
                @if($posts->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-comment me-2"></i>
                            Post Results ({{ $posts->total() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($posts as $post)
                        <div class="search-result-item p-3 border-bottom">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <img src="{{ $post->user->avatar ?? '/images/default-avatar.png' }}"
                                        alt="{{ $post->user->name }}" class="rounded-circle" width="40" height="40">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('threads.show', $post->thread) }}#post-{{ $post->id }}"
                                            class="text-decoration-none">
                                            Re: {{ $post->thread->title }}
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        {!! Str::limit(highlightSearchQuery(strip_tags($post->body), $query), 200) !!}
                                    </p>
                                    <div class="d-flex align-items-center text-sm text-muted">
                                        <span class="me-3">
                                            <i class="fas fa-user me-1"></i>
                                            {{ $post->user->name }}
                                        </span>
                                        <span class="me-3">
                                            <i class="fas fa-folder me-1"></i>
                                            <a href="{{ route('forums.show', $post->thread->forum) }}"
                                                class="text-muted text-decoration-none">
                                                {{ $post->thread->forum->name }}
                                            </a>
                                        </span>
                                        <span>
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $post->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        {{ $posts->links() }}
                    </div>
                </div>
                @endif

                <!-- No Results -->
                @if($threads->count() === 0 && $posts->count() === 0)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
                        <h4 class="text-muted mb-2">No Results Found</h4>
                        <p class="text-muted mb-4">
                            We couldn't find any threads or posts matching "<strong>{{ $query }}</strong>".
                        </p>
                        <div class="suggestions">
                            <h6 class="text-muted mb-2">Try:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Check your spelling</li>
                                <li>• Use more general terms</li>
                                <li>• Try different keywords</li>
                                <li>• Browse our <a href="{{ route('forums.index') }}">forum categories</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-4">
                <!-- Search Tips -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            Search Tips
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <strong>Use quotes</strong> for exact phrases: "mechanical design"
                            </li>
                            <li class="mb-2">
                                <strong>Multiple words</strong> will search for threads containing all words
                            </li>
                            <li class="mb-2">
                                <strong>Minimum 3 characters</strong> required for search
                            </li>
                            <li class="mb-0">
                                <strong>Browse categories</strong> for more specific topics
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Popular Categories -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-fire me-2"></i>
                            Popular Categories
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                        $popularCategories = App\Models\Forum::withCount('threads')
                        ->where('parent_id', null)
                        ->orderBy('threads_count', 'desc')
                        ->limit(5)
                        ->get();
                        @endphp

                        @foreach($popularCategories as $category)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <a href="{{ route('forums.show', $category) }}" class="text-decoration-none">
                                {{ $category->title }}
                            </a>
                            <span class="badge bg-light text-dark">
                                {{ $category->threads_count }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Highlight search query in results
function highlightSearchQuery(text, query) {
    if (!query) return text;

    const regex = new RegExp(`(${query})`, 'gi');
    return text.replace(regex, '<span class="highlight">$1</span>');
}
</script>
@endpush
@endsection
