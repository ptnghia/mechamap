@extends('layouts.app')

@section('title', __('showcase.new') . ' - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/whats-new.css') }}">
@endpush

@section('content')
<div class="body_page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="div_title_page">
            <h1 class="h2 mb-1 title_page">{{ seo_title_short(__('forum.threads.new')) }}</h1>
            <p class="text-muted mb-0">{{ seo_value('description', __('ui.whats_new.showcases.description'))  }}</p>
        </div>

        <a href="{{ route('threads.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> {{ __('forum.threads.create') }}
        </a>
    </div>

    <!-- Navigation Tabs -->
    <div class="whats-new-tabs mb-4">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new') }}"><i class="fas fa-info-circle me-1"></i>{{ __('forum.posts.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.popular') }}"><i class="fas fa-fire me-1"></i>{{ __('ui.common.popular') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.hot-topics') }}"><i class="fa-solid fa-fire-flame-curved me-1"></i>{{ __('whats_new.hot_topics') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.threads') }}"><i class="fa-solid fa-rss me-1"></i>{{ __('forum.threads.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('whats-new.showcases') }}"><i class="fa-solid fa-compass-drafting me-1"></i>{{ __('showcase.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.media') }}"><i class="fa-solid fa-photo-film me-1"></i>{{ __('media.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.replies') }}"><i class="fa-solid fa-question me-1"></i>{{ __('forum.threads.looking_for_replies') }}</a>
            </li>
        </ul>
    </div>

    <!-- Pagination Top -->
    @if($showcases->hasPages())
    <div class="text-center mt-4">
        {{ $showcases->links() }}
    </div>
    @endif

    <!-- Showcases Grid -->
    <div class="body_left">
        @if($showcases->count() > 0)
        <div class="card-body p-0">
            <div class="row g-3">
                @foreach($showcases as $showcase)
                <div class="col-md-6 col-lg-6">
                    <div class="card h-100 border-0 showcase-card showcase_thead">
                        <!-- Showcase Header -->
                        <div class="card-header bg-transparent border-bottom">
                            <div class="d-flex align-items-center">
                                <img src="{{ get_avatar_url($showcase->user) }}" alt="{{ $showcase->user->name }}"
                                    class="rounded-circle me-2" width="32" height="32">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">
                                        <a href="{{ route('profile.show', $showcase->user) }}"
                                            class="text-decoration-none">{{ $showcase->user->name }}</a>
                                    </h6>
                                    <small class="text-muted">{{ $showcase->created_at->diffForHumans()
                                        }}</small>
                                </div>
                                <span class="badge bg-primary">{{ __('common.' . strtolower($showcase->showcase_type)) }}</span>
                            </div>
                        </div>

                        <!-- Showcase Image - Unified Component -->
                        <x-showcase-image :showcase="$showcase" size="large" />

                        <!-- Showcase Content -->
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ $showcase->showcase_url }}" class="text-decoration-none">
                                    @if($showcase->showcase_type === 'Thread')
                                    <i class="fas fa-comment-left-text me-1"></i>
                                    @elseif($showcase->showcase_type === 'Post')
                                    <i class="fas fa-comment-right me-1"></i>
                                    @else
                                    <i class="fas fa-star me-1"></i>
                                    @endif
                                    {{ $showcase->showcase_title }}
                                </a>
                            </h5>

                            @if($showcase->content_preview)
                            <p class="card-text text-muted">{{ $showcase->content_preview }}</p>
                            @endif

                            @if($showcase->description)
                            <div class="mt-2 p-2 bg-light rounded">
                                <small class="text-description">
                                    <i class="fa-solid fa-quote-left me-1"></i>
                                    <strong>{{ t_common("showcase_reason") }}</strong> {{ $showcase->description }}
                                </small>
                            </div>
                            @endif
                        </div>

                        <!-- Showcase Footer -->
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ $showcase->showcase_url }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> {{ __('ui.actions.view_details') }}
                                    </a>
                                    <a href="{{ route('showcase.show', $showcase) }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-star me-1"></i> {{ t_common("showcase") }}
                                    </a>
                                </div>

                                @if($showcase->showcaseable && $showcase->showcaseable_type ===
                                'App\Models\Thread')
                                <small class="text-muted">
                                    <i class="fa-solid fa-folder-closed"></i>
                                    {{ $showcase->showcaseable->forum->name ?? __('common.labels.forum') }}
                                </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="card-body text-center py-5">
            <i class="fas fa-star display-4 text-muted"></i>
            <p class="mt-3">{{ t_common("no_showcases_found") }}</p>
            <a href="{{ route('showcase.create') }}" class="btn btn-primary">{{ t_common("create_first_showcase") }}</a>
        </div>
        @endif
    </div>

    <!-- Pagination Bottom -->
    @if($showcases->hasPages())
    <div class="text-center mt-4">
        {{ $showcases->links() }}
    </div>
    @endif
</div>

@endsection

@push('styles')
@endpush
