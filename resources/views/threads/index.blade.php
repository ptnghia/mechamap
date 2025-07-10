@extends('layouts.app')

@section('title', __('nav.forums'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/threads.css') }}">
@endpush

@section('content')
<div class="body_page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-1 title_page">{{ __('nav.forums') }}</h1>
        @auth
        <a href="{{ route('threads.create') }}" class="btn btn-link">
            <i class="fa-solid fa-plus me-1"></i> {{ __('forum.new_thread') }}
        </a>
        @endauth
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('threads.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select select_search" id="category" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category')==$category->id ? 'selected' :
                            '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="forum" class="form-label">Forum</label>
                    <select class="form-select select_search" id="forum" name="forum">
                        <option value="">All Forums</option>
                        @foreach($forums as $forum)
                        <option value="{{ $forum->id }}" {{ request('forum')==$forum->id ? 'selected' : '' }}>{{
                            $forum->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="sort" class="form-label">{{ __('messages.sort_by') }}</label>
                    <select class="form-select select_search" id="sort" name="sort">
                        <option value="latest" {{ request('sort', 'latest' )=='latest' ? 'selected' : '' }}>
                            {{ __('messages.latest') }}</option>
                        <option value="oldest" {{ request('sort')=='oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="most_viewed" {{ request('sort')=='most_viewed' ? 'selected' : '' }}>Most
                            Viewed</option>
                        <option value="most_commented" {{ request('sort')=='most_commented' ? 'selected' : ''
                            }}>Most Commented</option>
                    </select>
                </div>

                <div class="col-md-8">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control input_search" id="search" name="search"
                        value="{{ request('search') }}" placeholder="Search threads...">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-search w-100">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Threads List -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 title_page">Threads</h5>
        <span class="badge bg-secondary">{{ $threads->total() }} threads</span>
    </div>
    @if($threads->count() > 0)
    <div class="list-group list-group-flush">
        @foreach($threads as $thread)
        @include('partials.thread-item', [
        'thread' => $thread
        ])
        @endforeach
    </div>

    <div class="list_post_threads_footer">
        {{ $threads->links() }}
    </div>
    @else
    <div class="card-body text-center py-5">
        <i class="fas fa-search display-4 text-muted"></i>
        <p class="mt-3">No threads found matching your criteria.</p>
        <a href="{{ route('threads.index') }}" class="btn btn-outline-primary">Clear Filters</a>
    </div>
    @endif
</div>
@endsection
