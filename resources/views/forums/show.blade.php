@extends('layouts.app')

@section('title', $forum->name . ' - MechaMap Forums')

@push('styles')
<style>
    .forum-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .filter-btn {
        border: 1px solid #dee2e6;
        background: white;
        transition: all 0.2s ease;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .thread-item {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .thread-item:hover {
        border-left-color: #007bff;
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .thread-meta {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .forum-stats {
        background: linear-gradient(45deg, #f8f9fa, #ffffff);
    }
</style>
@endpush

@section('content')
<div class="py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('forums.index') }}" class="text-decoration-none">Forums</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $forum->name }}
                </li>
            </ol>
        </nav>

        <!-- Forum Header -->
        <div class="forum-header rounded-lg p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-2">
                        @if($forum->media->first())
                        <img src="{{ $forum->media->first()->url }}" alt="{{ $forum->name }}" class="rounded me-3"
                            width="60" height="60">
                        @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-3"
                            style="width: 60px; height: 60px;">
                            <i class="{{ getForumIcon($forum->name) }} text-muted fa-2x"></i>
                        </div>
                        @endif
                        <div>
                            <h1 class="h2 mb-1">{{ $forum->name }}</h1>
                            <p class="mb-0 opacity-90">{{ $forum->description }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('threads.create', ['forum' => $forum->id]) }}" class="btn btn-light">
                        <i class="fas fa-plus me-2"></i>
                        New Thread
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-9">
                <!-- Search and Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <!-- Search Input -->
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search threads in this forum..." value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Sort Dropdown -->
                            <div class="col-md-3">
                                <select name="sort" class="form-select" onchange="this.form.submit()">
                                    <option value="latest" {{ request('sort')=='latest' ? 'selected' : '' }}>
                                        Latest Activity
                                    </option>
                                    <option value="oldest" {{ request('sort')=='oldest' ? 'selected' : '' }}>
                                        Oldest First
                                    </option>
                                    <option value="popular" {{ request('sort')=='popular' ? 'selected' : '' }}>
                                        Most Replies
                                    </option>
                                    <option value="views" {{ request('sort')=='views' ? 'selected' : '' }}>
                                        Most Views
                                    </option>
                                </select>
                            </div>

                            <!-- Filter Buttons -->
                            <div class="col-md-3">
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" name="filter" value="" id="all" class="btn-check" {{
                                        !request('filter') ? 'checked' : '' }}>
                                    <label class="btn filter-btn btn-sm" for="all">All</label>

                                    <input type="radio" name="filter" value="recent" id="recent" class="btn-check" {{
                                        request('filter')=='recent' ? 'checked' : '' }}>
                                    <label class="btn filter-btn btn-sm" for="recent">Recent</label>

                                    <input type="radio" name="filter" value="unanswered" id="unanswered"
                                        class="btn-check" {{ request('filter')=='unanswered' ? 'checked' : '' }}>
                                    <label class="btn filter-btn btn-sm" for="unanswered">Unanswered</label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Threads List -->
                @if($threads->count() > 0)
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-comments me-2"></i>
                            Threads
                            <span class="badge bg-secondary">{{ $threads->total() }}</span>
                        </h5>

                        @if(request('search') || request('filter'))
                        <a href="{{ route('forums.show', $forum) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i>
                            Clear Filters
                        </a>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        @foreach($threads as $thread)
                        <div class="thread-item p-3 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <img src="{{ $thread->user->avatar ?? '/images/default-avatar.png' }}"
                                                alt="{{ $thread->user->name }}" class="rounded-circle" width="40"
                                                height="40">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="{{ route('threads.show', $thread) }}"
                                                    class="text-decoration-none">
                                                    @if($thread->is_pinned)
                                                    <i class="fas fa-thumbtack text-warning me-1"></i>
                                                    @endif
                                                    @if($thread->is_locked)
                                                    <i class="fas fa-lock text-muted me-1"></i>
                                                    @endif
                                                    {{ $thread->title }}
                                                </a>
                                                @if($thread->is_solved)
                                                <span class="badge bg-success ms-2">Solved</span>
                                                @endif
                                            </h6>
                                            <div class="thread-meta">
                                                <span class="me-3">
                                                    <i class="fas fa-user me-1"></i>
                                                    {{ $thread->user->name }}
                                                    {!! getUserRoleBadge($thread->user) !!}
                                                </span>
                                                <span>
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $thread->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="text-muted small">Replies</div>
                                            <div class="fw-bold">{{ formatNumber($thread->comments_count) }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-muted small">Views</div>
                                            <div class="fw-bold">{{ formatNumber($thread->view_count ?? 0) }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-muted small">Last</div>
                                            <div class="fw-bold small">
                                                {{ $thread->updated_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        {{ $threads->appends(request()->query())->links() }}
                    </div>
                </div>
                @else
                <!-- No Threads Found -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        @if(request('search') || request('filter'))
                        <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
                        <h4 class="text-muted mb-2">No Threads Found</h4>
                        <p class="text-muted mb-4">
                            No threads match your current search or filter criteria.
                        </p>
                        <a href="{{ route('forums.show', $forum) }}" class="btn btn-outline-primary">
                            <i class="fas fa-times me-2"></i>
                            Clear Filters
                        </a>
                        @else
                        <i class="fas fa-comments text-muted mb-3" style="font-size: 3rem;"></i>
                        <h4 class="text-muted mb-2">No Threads Yet</h4>
                        <p class="text-muted mb-4">
                            Be the first to start a discussion in this forum!
                        </p>
                        <a href="{{ route('threads.create', ['forum' => $forum->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Create First Thread
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-3">
                <!-- Forum Statistics -->
                <div class="card forum-stats mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Forum Statistics
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="h4 text-primary mb-0">{{ formatNumber($forumStats['total_threads']) }}</div>
                                <small class="text-muted">Total Threads</small>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="h4 text-success mb-0">{{ formatNumber($forumStats['total_posts']) }}</div>
                                <small class="text-muted">Total Posts</small>
                            </div>
                            <div class="col-6">
                                <div class="h4 text-info mb-0">{{ formatNumber($forumStats['recent_threads']) }}</div>
                                <small class="text-muted">This Week</small>
                            </div>
                            <div class="col-6">
                                <div class="h4 text-warning mb-0">{{ formatNumber($forumStats['active_users']) }}</div>
                                <small class="text-muted">Active Users</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-clock me-2"></i>
                            Recent Activity
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                        $recentThreads = $forum->threads()
                        ->with('user')
                        ->latest()
                        ->limit(5)
                        ->get();
                        @endphp

                        @forelse($recentThreads as $recentThread)
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $recentThread->user->avatar ?? '/images/default-avatar.png' }}"
                                alt="{{ $recentThread->user->name }}" class="rounded-circle me-2" width="30"
                                height="30">
                            <div class="flex-grow-1 min-w-0">
                                <div class="small">
                                    <a href="{{ route('threads.show', $recentThread) }}" class="text-decoration-none">
                                        {{ Str::limit($recentThread->title, 40) }}
                                    </a>
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    by {{ $recentThread->user->name }} • {{ $recentThread->created_at->diffForHumans()
                                    }}
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted small mb-0">No recent activity</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filter buttons are clicked
    const filterButtons = document.querySelectorAll('input[name="filter"]');
    filterButtons.forEach(button => {
        button.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });

    // Add fade-in animation to thread items
    const threadItems = document.querySelectorAll('.thread-item');
    threadItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';

        setTimeout(() => {
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endpush
@endsection