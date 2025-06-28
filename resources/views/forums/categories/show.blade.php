@extends('layouts.app')

@section('title', $category->name . ' - Forum Category')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forums.categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card category-header">
                @if($category->image)
                <div class="card-img-top category-banner" style="background-image: url('{{ $category->image }}');">
                    <div class="category-banner-overlay">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h1 class="display-6 text-white mb-2">{{ $category->name }}</h1>
                                    @if($category->description)
                                    <p class="text-white-50 mb-0">{{ $category->description }}</p>
                                    @endif
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <div class="category-stats-banner">
                                        <div class="stat-item">
                                            <div class="stat-number text-white">{{ $forums->count() }}</div>
                                            <div class="stat-label text-white-50">Forums</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number text-white">{{ $totalThreads }}</div>
                                            <div class="stat-label text-white-50">Threads</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number text-white">{{ $totalPosts }}</div>
                                            <div class="stat-label text-white-50">Posts</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h2 mb-2">{{ $category->name }}</h1>
                            @if($category->description)
                            <p class="text-muted mb-0">{{ $category->description }}</p>
                            @endif
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex justify-content-md-end gap-3">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-primary">{{ $forums->count() }}</div>
                                    <small class="text-muted">Forums</small>
                                </div>
                                <div class="text-center">
                                    <div class="h4 mb-0 text-success">{{ $totalThreads }}</div>
                                    <small class="text-muted">Threads</small>
                                </div>
                                <div class="text-center">
                                    <div class="h4 mb-0 text-info">{{ $totalPosts }}</div>
                                    <small class="text-muted">Posts</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Forums in Category -->
    <div class="row">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Forums in {{ $category->name }}</h3>
                @auth
                <a href="{{ route('threads.create', ['category' => $category->id]) }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i>
                    New Thread
                </a>
                @endauth
            </div>

            @forelse($forums as $forum)
            <div class="card forum-item mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-start">
                                @if($forum->icon)
                                <div class="forum-icon me-3">
                                    <i class="{{ $forum->icon }} text-primary fs-3"></i>
                                </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">
                                        <a href="{{ route('forums.show', $forum) }}" class="text-decoration-none">
                                            {{ $forum->name }}
                                        </a>
                                    </h5>
                                    @if($forum->description)
                                    <p class="text-muted mb-2">{{ $forum->description }}</p>
                                    @endif
                                    
                                    <!-- Forum Stats -->
                                    <div class="d-flex gap-3 small text-muted">
                                        <span><i class="bx bx-message-dots me-1"></i>{{ $forum->threads_count }} threads</span>
                                        <span><i class="bx bx-chat me-1"></i>{{ $forum->posts_count }} posts</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            @if($forum->latest_thread)
                            <div class="latest-activity">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $forum->latest_thread->user->getAvatarUrl() }}" 
                                         alt="{{ $forum->latest_thread->user->name }}" 
                                         class="rounded-circle me-2" width="32" height="32">
                                    <div class="flex-grow-1 min-w-0">
                                        <a href="{{ route('threads.show', $forum->latest_thread) }}" 
                                           class="text-decoration-none small fw-medium">
                                            {{ Str::limit($forum->latest_thread->title, 30) }}
                                        </a>
                                        <div class="text-muted small">
                                            by {{ $forum->latest_thread->user->name }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $forum->latest_thread->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="text-center text-muted">
                                <small>No threads yet</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-message-square-dots display-1 text-muted"></i>
                    <h4 class="mt-3">No Forums Found</h4>
                    <p class="text-muted">This category doesn't have any forums yet.</p>
                    @can('create', App\Models\Forum::class)
                    <a href="{{ route('admin.forums.create', ['category' => $category->id]) }}" class="btn btn-primary">
                        Create First Forum
                    </a>
                    @endcan
                </div>
            </div>
            @endforelse
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Category Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Category Information</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number text-primary">{{ $forums->count() }}</div>
                                <div class="stat-label">Forums</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number text-success">{{ $totalThreads }}</div>
                                <div class="stat-label">Threads</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number text-info">{{ $totalPosts }}</div>
                                <div class="stat-label">Posts</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Threads -->
            @if($recentThreads->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Recent Threads</h6>
                </div>
                <div class="card-body">
                    @foreach($recentThreads as $thread)
                    <div class="d-flex align-items-start mb-3">
                        <img src="{{ $thread->user->getAvatarUrl() }}" 
                             alt="{{ $thread->user->name }}" 
                             class="rounded-circle me-2" width="32" height="32">
                        <div class="flex-grow-1 min-w-0">
                            <a href="{{ route('threads.show', $thread) }}" 
                               class="text-decoration-none small fw-medium">
                                {{ Str::limit($thread->title, 40) }}
                            </a>
                            <div class="text-muted small">
                                {{ $thread->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Active Users -->
            @if($activeUsers->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Active Users</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($activeUsers as $user)
                        <a href="{{ route('profile.show', $user->username) }}" 
                           class="text-decoration-none" title="{{ $user->name }}">
                            <img src="{{ $user->getAvatarUrl() }}" 
                                 alt="{{ $user->name }}" 
                                 class="rounded-circle" width="32" height="32">
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.category-banner {
    height: 200px;
    background-size: cover;
    background-position: center;
    position: relative;
}

.category-banner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(0,0,0,0.6), rgba(0,0,0,0.3));
    display: flex;
    align-items: center;
}

.category-stats-banner {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.forum-item {
    transition: transform 0.2s ease-in-out;
    border: 1px solid rgba(0,0,0,0.125);
}

.forum-item:hover {
    transform: translateX(5px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.forum-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(var(--bs-primary-rgb), 0.1);
    border-radius: 8px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 1.25rem;
    font-weight: 600;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--bs-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.latest-activity {
    padding: 0.5rem;
    background: rgba(var(--bs-primary-rgb), 0.05);
    border-radius: 8px;
}

.min-w-0 {
    min-width: 0;
}

@media (max-width: 768px) {
    .category-banner {
        height: 150px;
    }
    
    .category-stats-banner {
        justify-content: center;
        margin-top: 1rem;
    }
    
    .forum-item:hover {
        transform: none;
    }
}
</style>
@endpush
