@extends('layouts.app')

@section('title', $category->name . ' - ' . t_common("site.tagline"))

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/forums.css') }}">
@endpush

@section('content')
<div class="body_page">
    {{-- Category Header --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="div_title_page">
            <h1 class="h3 mb-1 title_page">{{ $category->name }}</h1>
            @if($category->description)
            <p class="text-muted mb-0">{{ $category->description }}</p>
            @endif
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            @auth
            <a href="{{ route('threads.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i>
                {{ __('forum.threads.create') }}
            </a>
            @endauth
        </div>
    </div>

    {{-- Category Statistics --}}
    <div class="row mb-4 list_thongke_cate">
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-primary">{{ number_format($categoryStats['forums_count']) }}</div>
                    <div class="text-muted">{{ __('forum.forums.title') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-success">{{ number_format($categoryStats['threads_count']) }}</div>
                    <div class="text-muted">{{ __('forum.threads.title') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-info">{{ number_format($categoryStats['views_count']) }}</div>
                    <div class="text-muted">{{ t_common("views") }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold text-warning">{{ number_format($categoryStats['posts_count']) }}</div>
                    <div class="text-muted">{{ t_common("comments") }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Forums in Category - Grid Layout --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 title_page_sub">{{ __('forum.forums.forums_in_category', ['category' => $category->name]) }}</h5>
        </div>

        @if($category->forums->count() > 0)
        <div class="row g-3">
            @foreach($category->forums as $forum)
            <div class="col-lg-6 col-xl-4">
                <a href="{{ route('forums.show', $forum) }}" class="text-decoration-none">
                    <div class="forum-grid-card h-100 p-3 border rounded-3 bg-white shadow-sm">
                        {{-- Forum Header --}}
                        <div class="forum-header mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="forum-icon me-3">
                                    @if($forum->media->first())
                                    <img src="{{ asset('storage/' . $forum->media->first()->file_path) }}"
                                         alt="{{ $forum->name }}" class="rounded" width="48" height="48">
                                    @else
                                    <div class="icon-circle d-flex align-items-center justify-content-center"
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg, {{ $category->color_code ?? '#007bff' }}15 0%, {{ $category->color_code ?? '#007bff' }}25 100%); border-radius: 12px;">
                                        <i class="fas fa-comments fs-5" style="color: {{ $category->color_code ?? '#007bff' }};"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="forum-title fw-bold mb-1 text-dark">{{ $forum->name }}</h6>
                                    <div class="forum-meta small text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ t_common("updated") }} {{ $forum->updated_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Forum Description --}}
                        <div class="forum-description mb-3">
                            <p class="text-muted small mb-0 lh-sm">
                                {{ Str::limit($forum->description, 100) }}
                            </p>
                        </div>

                        {{-- Forum Stats --}}
                        <div class="forum-stats mt-auto">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="stat-item text-center p-1 rounded-2" style="background-color: #f8f9fa;">
                                        <div class="stat-number fw-bold text-primary">{{ number_format($forum->threads_count ?? 0) }}</div>
                                        <div class="stat-label small text-muted">{{ __('forum.threads.threads') }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item text-center p-1 rounded-2" style="background-color: #f8f9fa;">
                                        <div class="stat-number fw-bold text-success">{{ number_format($forum->posts_count ?? 0) }}</div>
                                        <div class="stat-label small text-muted">{{ __('forum.threads.title') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Activity Indicator --}}
                        <div class="forum-activity mt-2">
                            @php
                                $totalActivity = ($forum->threads_count ?? 0) + ($forum->posts_count ?? 0);
                                $activityLevel = $totalActivity > 50 ? 'high' : ($totalActivity > 20 ? 'medium' : 'low');
                                $activityColor = $activityLevel === 'high' ? 'success' : ($activityLevel === 'medium' ? 'warning' : 'secondary');
                                $activityText = $activityLevel === 'high' ? __('forum.forums.high_activity') : ($activityLevel === 'medium' ? __('forum.forums.medium_activity') : __('forum.forums.low_activity'));
                            @endphp
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-{{ $activityColor }} bg-opacity-10 text-{{ $activityColor }} small">
                                    <i class="fas fa-pulse me-1"></i>
                                    {{ $activityText }}
                                </span>
                                <i class="fas fa-arrow-right text-muted small"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <div class="empty-state">
                <div class="empty-icon mb-3">
                    <i class="fas fa-comments fs-1 text-muted opacity-25"></i>
                </div>
                <h6 class="text-muted mb-2">{{ __('forum.forums.no_forums_in_category') }}</h6>
                <p class="text-muted small mb-0">{{ __('forum.forums.no_forums_description') }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Recent Threads - Full Width using thread-item component --}}
    <div class="list-group">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 title_page_sub">{{ __('common.new_threads', ['category' => $category->name]) }}</h5>
        </div>
        @if($recentThreads->count() > 0)
        <div class="threads-list">
            @foreach($recentThreads as $thread)
            @include('partials.thread-item', ['thread' => $thread])
            @endforeach
        </div>

        <div class="text-center mt-4">
            {{ $recentThreads->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-comment-square-text fs-1 text-muted opacity-50"></i>
            <p class="text-muted mt-3 mb-0">{{ __('forum.threads.no_posts_in_category') }}</p>
            @auth
            <a href="{{ route('threads.create') }}" class="btn btn-primary mt-3">
                <i class="fas fa-plus-circle me-1"></i>
                {{ __('forum.threads.create_first_post') }}
            </a>
            @endauth
        </div>
        @endif
    </div>
</div>
@endsection
