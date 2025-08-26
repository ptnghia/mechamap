@extends('layouts.app')

@section('title', __('forums.title'))

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/forums.css') }}">
@endpush

@section('content')
<div class="body_page">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="div_title_page">
            <h1 class="h2 mb-1 title_page">{{ seo_title_short() }}</h1>
            <p class="text-muted mb-0">{{ seo_value('description', __('forums.description'))  }}</p>
        </div>
        @auth
        <div>
            <a href="{{ route('threads.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i>
                {{ __('forums.threads.actions.create') }}
            </a>
        </div>
        @endauth
    </div>

    <div class="search_forums mb-3">
        <form action="{{ route('forums.index') }}" method="GET" class="d-flex">
            <input type="search" name="q" class="form-control"
                placeholder="{{ __('forums.search.placeholder_forums') }}"
                value="{{ $searchQuery ?? request('q') }}" minlength="2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-1"></i> {{ __('activity.search') }}
            </button>
        </form>
        <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
            <small class="text-muted">
                @if($searchQuery ?? request('q'))
                    {{ __('forums.search.description_results') }}
                @else
                    {{ __('forums.search.description_forums') }}
                @endif
            </small>
            <small class="text-muted">
                <a href="{{ route('threads.index') }}" class="text-decoration-none">
                    <i class="fas fa-comments me-1"></i>{{ __('forums.search.search_threads') }}
                </a>
            </small>
        </div>
    </div>

    {{-- Search Results Info --}}
    @if($searchQuery ?? request('q'))
    <div class="alert alert-info mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-search me-2"></i>
                <strong>{{ __('forums.search.results_for') }}:</strong> "{{ $searchQuery ?? request('q') }}"
                <span class="badge bg-primary ms-2">{{ $categories->count() }} {{ __('forums.search.categories_found') }}</span>

                {{-- Show search type breakdown --}}
                @php
                    $categoryMatches = $categories->where('search_type', 'category_match')->count();
                    $forumMatches = $categories->where('search_type', 'forum_match')->count();
                @endphp

                @if($categoryMatches > 0)
                    <span class="badge bg-success ms-1">{{ $categoryMatches }} {{ __('forums.search.category_matches') }}</span>
                @endif

                @if($forumMatches > 0)
                    <span class="badge bg-info ms-1">{{ $forumMatches }} {{ __('forums.search.forum_matches') }}</span>
                @endif
            </div>
            <a href="{{ route('forums.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-list me-1"></i>{{ __('forums.search.show_all') }}
            </a>
        </div>
    </div>
    @endif

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
                        <h5 class="card-title mb-0">
                            {{ $category->name }}
                            {{-- Search match indicator --}}
                            @if(($searchQuery ?? request('q')) && isset($category->search_type))
                                @if($category->search_type === 'category_match')
                                    <span class="badge bg-success ms-2" title="{{ __('forums.search.category_name_match') }}">
                                        <i class="fas fa-bullseye me-1"></i>{{ __('forums.search.exact_match') }}
                                    </span>
                                @else
                                    <span class="badge bg-info ms-2" title="{{ __('forums.search.contains_matching_forums') }}">
                                        <i class="fas fa-search me-1"></i>{{ __('forums.search.contains_matches') }}
                                    </span>
                                @endif
                            @endif
                        </h5>
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
            <div class="row mb-3 forums_list">
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
                    <div class="d-flex justify-content-between align-items-center mb-2 list_post_threads_content">
                        <h6 class="text-muted mb-0">{{ __('forums.category.recent_threads', ['count' => 5]) }}:</h6>
                        <a href="{{ route('categories.show', $category->slug) }}"
                            class="btn btn-sm btn-link">
                            {{ __('forums.actions.view_more') }} <i class="fas fa-arrow-right ms-1"></i>
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

@endpush
@endsection
