@extends('layouts.app')

@section('title', 'Forums - MechaMap Community')

@push('styles')
<style>
    .forum-stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .forum-stats-card .stats-item {
        transition: transform 0.2s ease;
    }

    .forum-stats-card .stats-item:hover {
        transform: translateY(-2px);
    }

    .forum-icon {
        transition: transform 0.2s ease;
    }

    .forum-item:hover .forum-icon {
        transform: scale(1.05);
    }

    .category-header {
        background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);
        border-left: 4px solid #007bff;
    }
</style>
@endpush

@section('content')
<div class="py-5">
    <div class="container">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('nav.home') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('nav.forums') }}</li>
            </ol>
        </nav>

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">{{ __('nav.forums') }}</h1>
                <p class="text-muted mb-0">{{ __('Discuss mechanical engineering topics with the community') }}</p>
            </div>
            @auth
            <div>
                <a href="{{ route('threads.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i>
                    {{ __('Create Thread') }}
                </a>
            </div>
            @endauth
        </div>

        {{-- Enhanced Forum Statistics --}}
        <div class="card shadow-sm rounded-3 mb-4 forum-stats-card">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="stats-item">
                            <div class="fs-2 fw-bold">{{ number_format($stats['forums']) }}</div>
                            <div class="opacity-75">{{ __('nav.forums') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="stats-item">
                            <div class="fs-2 fw-bold">{{ number_format($stats['threads']) }}</div>
                            <div class="opacity-75">{{ __('Threads') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="stats-item">
                            <div class="fs-2 fw-bold">{{ number_format($stats['posts']) }}</div>
                            <div class="opacity-75">{{ __('Posts') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-item">
                            <div class="fs-2 fw-bold">{{ number_format($stats['users']) }}</div>
                            <div class="opacity-75">{{ __('Members') }}</div>
                        </div>
                    </div>
                </div>
                @if($stats['newest_member'])
                <div class="text-center mt-3 pt-3 border-top border-light border-opacity-25">
                    <small class="opacity-75">{{ __('Newest Member') }}:</small>
                    <a href="{{ route('profile.show', $stats['newest_member']->username) }}"
                        class="text-white fw-bold text-decoration-none ms-2">
                        {{ $stats['newest_member']->name }}
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Search & Filters --}}
        <div class="card shadow-sm rounded-3 mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <form action="{{ route('forums.search') }}" method="GET" class="d-flex">
                            <input type="search" name="q" class="form-control"
                                placeholder="{{ __('Search forums, threads, and discussions...') }}"
                                value="{{ request('q') }}" minlength="3" required>
                            <button type="submit" class="btn btn-primary ms-2">
                                <i class="fas fa-search me-1"></i>
                                {{ __('Search') }}
                            </button>
                        </form>
                        <small class="text-muted mt-1 d-block">
                            {{ __('Search across all forums and discussions. Minimum 3 characters required.') }}
                        </small>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleView('grid')"
                                id="grid-view-btn">
                                <i class="fas fa-th"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm active"
                                onclick="toggleView('list')" id="list-view-btn">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Enhanced Forum Categories with Statistics --}}
        @foreach($categories as $category)
        <div class="card shadow-sm rounded-3 mb-4">
            <div class="card-header category-header">
                @php
                // Lấy ảnh đại diện của category từ media relationship
                $categoryImage = $category->media->first();
                if ($categoryImage) {
                $categoryImageUrl = filter_var($categoryImage->file_path, FILTER_VALIDATE_URL)
                ? $categoryImage->file_path
                : asset('storage/' . $categoryImage->file_path);
                } else {
                $categoryImageUrl = null;
                }
                @endphp

                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        @if($categoryImageUrl)
                        <img src="{{ $categoryImageUrl }}" alt="{{ $category->name }}" class="rounded me-3 shadow-sm"
                            width="36" height="36" style="object-fit: cover;">
                        @else
                        <div class="bg-primary bg-opacity-10 rounded me-3 d-flex align-items-center justify-content-center"
                            style="width: 36px; height: 36px;">
                            <i class="collection text-primary"></i>
                        </div>
                        @endif
                        <div>
                            <h5 class="card-title mb-0">{{ $category->name }}</h5>
                            @if($category->description)
                            <small class="text-muted">{{ $category->description }}</small>
                            @endif
                        </div>
                    </div>

                    {{-- Category Statistics --}}
                    <div class="d-flex gap-3 text-center">
                        <div>
                            <div class="fw-bold text-primary">{{ number_format($category->stats['forums_count']) }}</div>
                            <small class="text-muted">{{ __('forums.stats.forums') }}</small>
                        </div>
                        <div>
                            <div class="fw-bold text-success">{{ number_format($category->stats['threads_count']) }}</div>
                            <small class="text-muted">{{ __('forums.stats.threads') }}</small>
                        </div>
                        <div>
                            <div class="fw-bold text-info">{{ number_format($category->stats['views_count']) }}</div>
                            <small class="text-muted">{{ __('forums.stats.views') }}</small>
                        </div>
                        <div>
                            <div class="fw-bold text-warning">{{ number_format($category->stats['posts_count']) }}</div>
                            <small class="text-muted">{{ __('forums.stats.comments') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{-- Forums in this category --}}
                @if($category->forums->count() > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-muted mb-3">{{ __('forums.category.forums_in_category') }}:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($category->forums as $forum)
                            <a href="{{ route('forums.show', $forum) }}"
                               class="btn btn-outline-primary btn-sm">
                                {{ $forum->name }}
                                <span class="badge bg-primary ms-1">{{ $forum->threads_count ?? 0 }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- Recent Threads from this category --}}
                @if($category->recent_threads && $category->recent_threads->count() > 0)
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted mb-0">{{ __('forums.category.recent_threads', ['count' => 5]) }}:</h6>
                            <a href="{{ route('categories.show', $category->slug) }}"
                               class="btn btn-sm btn-outline-secondary">
                                {{ __('forums.actions.view_more') }} <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="list-group list-group-flush">
                            @foreach($category->recent_threads as $thread)
                            <div class="list-group-item border-0 px-0 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <a href="{{ route('threads.show', $thread) }}"
                                           class="text-decoration-none fw-semibold">
                                            {{ Str::limit($thread->title, 60) }}
                                        </a>
                                        <div class="small text-muted mt-1">
                                            <span>bởi {{ $thread->user->name }}</span>
                                            <span class="mx-1">•</span>
                                            <span>trong {{ $thread->forum->name }}</span>
                                            <span class="mx-1">•</span>
                                            <span>{{ $thread->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="text-end ms-3">
                                        <div class="d-flex gap-2 small text-muted">
                                            <span><i class="fas fa-eye"></i> {{ number_format($thread->view_count ?? 0) }}</span>
                                            <span><i class="fas fa-comment"></i> {{ number_format($thread->comments_count ?? 0) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-comment-square-text fs-1 opacity-50"></i>
                    <p class="mt-2 mb-0">{{ __('forums.category.no_threads') }}</p>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    function toggleView(viewType) {
    const gridBtn = document.getElementById('grid-view-btn');
    const listBtn = document.getElementById('list-view-btn');
    const forumItems = document.querySelectorAll('.forum-item');

    if (viewType === 'grid') {
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        // Add grid view styling (could be implemented further)
        localStorage.setItem('forum-view', 'grid');
    } else {
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        localStorage.setItem('forum-view', 'list');
    }
}

// Restore saved view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('forum-view') || 'list';
    toggleView(savedView);

    // Add smooth animations
    const forumItems = document.querySelectorAll('.forum-item');
    forumItems.forEach((item, index) => {
        item.style.animationDelay = (index * 0.1) + 's';
        item.classList.add('animate-fade-in');
    });
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in {
        animation: fadeIn 0.6s ease-out forwards;
    }

    .forum-item {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .forum-item:hover {
        border-left-color: #007bff;
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    .stats-item {
        cursor: pointer;
    }
`;
document.head.appendChild(style);
</script>
@endpush
@endsection
