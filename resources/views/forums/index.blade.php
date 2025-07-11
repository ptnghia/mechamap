@extends('layouts.app')

@section('title', 'Forums')

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/forums/index.css') }}">
@endpush

@section('content')
<div class="body_page">
    {{-- Breadcrumb --}}
    <!--nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('nav.home') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('nav.forums') }}</li>
        </ol>
    </nav-->

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 title_page">{{ __('nav.forums') }}</h1>
            <p class="text-muted mb-0">{{ __('forums.description') }}</p>
        </div>
        @auth
        <div>
            <a href="{{ route('threads.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i>
                {{ __('forums.actions.create_thread') }}
            </a>
        </div>
        @endauth
    </div>

    {{-- Enhanced Forum Statistics --}}
    <!--div class="card shadow-sm rounded-3 mb-4 forum-stats-card">
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
                        <div class="opacity-75">{{ __('forums.stats.threads') }}</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="stats-item">
                        <div class="fs-2 fw-bold">{{ number_format($stats['posts']) }}</div>
                        <div class="opacity-75">{{ __('forums.stats.posts') }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-item">
                        <div class="fs-2 fw-bold">{{ number_format($stats['users']) }}</div>
                        <div class="opacity-75">{{ __('forums.stats.members') }}</div>
                    </div>
                </div>
            </div>
            @if($stats['newest_member'])
            <div class="text-center mt-3 pt-3 border-top border-light border-opacity-25">
                <small class="opacity-75">{{ __('forums.newest_member') }}:</small>
                <a href="{{ route('profile.show', $stats['newest_member']->username) }}"
                    class="text-white fw-bold text-decoration-none ms-2">
                    {{ $stats['newest_member']->name }}
                </a>
            </div>
            @endif
        </div>
    </div-->

    {{-- Quick Search & Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('forums.search') }}" method="GET" class="d-flex">
                <input type="search" name="q" class="form-control"
                    placeholder="{{ __('forums.search.placeholder_main') }}"
                    value="{{ request('q') }}" minlength="3" required>
                <button type="submit" class="btn btn-primary ms-2">
                    <i class="fas fa-search me-1"></i>
                </button>
            </form>
            <small class="text-muted mt-1 d-block">
                {{ __('forums.search.description') }}
            </small>
        </div>
    </div>
    {{-- Enhanced Forum Categories with Statistics --}}
    @foreach($categories as $category)
    <div class="card shadow-sm rounded-3 mb-4 forums_cate_item">
        <div class="card-header category-header">
            @php
            // Lấy ảnh đại diện của category từ media relationship
            $categoryImage = $category->media->first();
            if ($categoryImage) {
            $categoryImageUrl = filter_var($categoryImage->file_path, FILTER_VALIDATE_URL)
            ? $categoryImage->file_path
            : asset('' . $categoryImage->file_path);
            } else {
            $categoryImageUrl = null;
            }
            @endphp

            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center forums_cate_item_title">
                    @if($categoryImageUrl)
                    <img src="{{ $categoryImageUrl }}" alt="{{ $category->name }}" class="rounded me-3 shadow-sm"
                        width="36" height="36" style="object-fit: cover;">
                    @else
                    <div class="bg-primary bg-opacity-10 rounded me-3 d-flex align-items-center justify-content-center"
                        style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-folder-open"></i>
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
                <div class="d-flex gap-3 text-center forums_cate_thongke">
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
            <div class="row mb-4 forums_list">
                <div class="col-12">
                    <!--h6 class="text-muted mb-3">{{ __('forums.category.forums_in_category') }}:</h6-->
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($category->forums as $forum)
                        <a href="{{ route('forums.show', $forum) }}"
                            class="btn forums_list_item">
                            {{ $forum->name }}
                            <span class="badge ms-1">{{ $forum->threads_count ?? 0 }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Recent Threads from this category --}}
            @if($category->recent_threads && $category->recent_threads->count() > 0)
            <div class="row list_post">
                <div class="col-12 list_post_threads">
                    <div class="d-flex justify-content-between align-items-center mb-3 list_post_threads_content">
                        <h6 class="text-muted mb-0">{{ __('forums.category.recent_threads', ['count' => 5]) }}:</h6>
                        <a href="{{ route('categories.show', $category->slug) }}"
                            class="btn btn-sm btn-link">
                            {{ __('forums.actions.view_more') }} <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="list-group list-group-flush">
                        @foreach($category->recent_threads as $thread)
                        <div class="list-group-item border-0 px-0 py-2 list_post_threads_item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <a href="{{ route('threads.show', $thread) }}"
                                        class="text-decoration-none fw-semibold">
                                        {{ Str::limit($thread->title, 60) }}
                                    </a>
                                    <div class="small text-muted mt-1 list_post_threads_item_info">
                                        <span><i class="fa-solid fa-user"></i>
                                            <a href="{{ route('profile.show', $thread->user->username) }}"
                                               class="text-decoration-none text-muted">{{ $thread->user->name }}</a>
                                        </span>

                                        <span><i class="fa-solid fa-tag"></i>
                                            <a href="{{ route('forums.show', $thread->forum) }}"
                                               class="text-decoration-none text-muted">{{ $thread->forum->name }}</a>
                                        </span>

                                        <span><i class="fa-solid fa-clock"></i> {{ $thread->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="text-end ms-3">
                                    <div class="d-flex gap-2 small text-muted list_post_threads_item_stats">
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

@push('scripts')
<script>
    function toggleView(viewType) {
    const gridBtn = document.getElementById('grid-view-btn');
    const listBtn = document.getElementById('list-view-btn');
    const forumItems = document.querySelectorAll('.forum-item');

    // Check if buttons exist before accessing classList
    if (!gridBtn || !listBtn) {
        console.warn('View toggle buttons not found in DOM');
        return;
    }

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
    if (forumItems.length > 0) {
        forumItems.forEach((item, index) => {
            if (item) {
                item.style.animationDelay = (index * 0.1) + 's';
                item.classList.add('animate-fade-in');
            }
        });
    }
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
