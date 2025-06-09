@extends('layouts.app')

@section('title', 'Forums')

@section('content')
<div class="container">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Forums</h1>
                @auth
                <a href="{{ route('threads.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> New Thread
                </a>
                @endauth
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('threads.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category')==$category->id ? 'selected' :
                                    '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="forum" class="form-label">Forum</label>
                            <select class="form-select" id="forum" name="forum">
                                <option value="">All Forums</option>
                                @foreach($forums as $forum)
                                <option value="{{ $forum->id }}" {{ request('forum')==$forum->id ? 'selected' : '' }}>{{
                                    $forum->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="sort" class="form-label">{{ __('messages.sort_by') }}</label>
                            <select class="form-select" id="sort" name="sort">
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
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}" placeholder="Search threads...">
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Threads List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Threads</h5>
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

                <div class="card-footer">
                    {{ $threads->links() }}
                </div>
                @else
                <div class="card-body text-center py-5">
                    <i class="bi bi-search display-4 text-muted"></i>
                    <p class="mt-3">No threads found matching your criteria.</p>
                    <a href="{{ route('threads.index') }}" class="btn btn-outline-primary">Clear Filters</a>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <!-- Forum Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Forum Stats</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Threads:</span>
                        <span class="fw-bold">{{ App\Models\Thread::count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Comments:</span>
                        <span class="fw-bold">{{ App\Models\Comment::count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Members:</span>
                        <span class="fw-bold">{{ App\Models\User::count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Newest Member:</span>
                        @php
                        $newestUser = App\Models\User::latest()->first();
                        @endphp
                        <span class="fw-bold">{{ $newestUser ? $newestUser->name : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Top Contributors -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Top Contributors This Month</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @php
                        $topContributors = App\Models\User::withCount(['threads' => function($query) {
                        $query->where('created_at', '>=', now()->subMonth());
                        }, 'comments' => function($query) {
                        $query->where('created_at', '>=', now()->subMonth());
                        }])
                        ->orderByRaw('threads_count + comments_count DESC')
                        ->take(5)
                        ->get();
                        @endphp

                        @forelse($topContributors as $user)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                    class="rounded-circle me-2" width="32" height="32">
                                <a href="{{ route('profile.show', $user) }}" class="text-decoration-none">{{ $user->name
                                    }}</a>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $user->threads_count + $user->comments_count
                                }}
                                posts</span>
                        </li>
                        @empty
                        <li class="list-group-item text-center">No contributors yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Categories -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Categories</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($categories as $category)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('threads.index', ['category' => $category->id]) }}"
                                class="text-decoration-none">{{ $category->name }}</a>
                            <span class="badge bg-secondary rounded-pill">{{ $category->threads->count() }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Forums -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Forums</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($forums as $forum)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('threads.index', ['forum' => $forum->id]) }}"
                                class="text-decoration-none">{{
                                $forum->name }}</a>
                            <span class="badge bg-secondary rounded-pill">{{ $forum->threads->count() }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection