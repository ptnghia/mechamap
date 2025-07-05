@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

<div class="py-5">
    <div class="container">
        <!-- Featured Content -->
        <div class="card shadow-sm rounded-3 mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Featured Content') }}</h5>
            </div>
            <div class="card-body">
                @if($featuredThreads->count() > 0)
                <div class="row">
                    @foreach($featuredThreads as $thread)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            @if($thread->featured_image)
                            <img src="{{ $thread->featured_image }}" class="card-img-top"
                                style="height: 180px; object-fit: cover;" alt="{{ $thread->title }}">
                            @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                style="height: 180px;">
                                <i class="fas fa-image text-muted fs-1"></i>
                            </div>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="{{ route('threads.show', $thread) }}" class="text-decoration-none">{{
                                        $thread->title }}</a>
                                </h5>
                                <p class="card-text">{{ Str::limit(strip_tags($thread->content), 100) }}</p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <small class="text-muted">
                                    {{ __('By') }}
                                    <a href="{{ route('profile.show', $thread->user->username) }}"
                                        class="text-decoration-none">
                                        {{ $thread->user->name }}
                                    </a>
                                    {{ __('in') }}
                                    <a href="{{ $thread->forum ? route('forums.show', $thread->forum) : '#' }}"
                                        class="text-decoration-none">
                                        {{ $thread->forum ? $thread->forum->name : __('Unknown Forum') }}
                                    </a>
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <p class="mb-0">{{ __('No featured content available.') }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- User Showcases -->
                <div class="card shadow-sm rounded-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Community Showcases') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($userShowcases->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($userShowcases as $showcase)
                            <div class="list-group-item py-3 px-0">
                                <div class="d-flex">
                                    <!-- Unified Featured Image cho tất cả showcase types -->
                                    <div class="me-3">
                                        <x-showcase-image :showcase="$showcase" size="medium" />
                                    </div>

                                    <div class="me-3">
                                        <img src="{{ $showcase->user->getAvatarUrl() }}"
                                            alt="{{ $showcase->user->name }}" class="rounded-circle" width="50"
                                            height="50">
                                    </div>
                                    <div>
                                        <h5 class="mb-1">
                                            @if($showcase->showcaseable_type === 'App\\Models\\Thread')
                                            <i class="fas fa-comment-left-text me-2"></i>
                                            <a href="{{ route('showcase.show', $showcase) }}"
                                                class="text-decoration-none">
                                                {{ $showcase->showcaseable ? $showcase->showcaseable->title :
                                                __('Unknown Title') }}
                                            </a>
                                            @elseif($showcase->showcaseable_type === 'App\\Models\\Post')
                                            <i class="fas fa-comment-right me-2"></i>
                                            <a href="{{ route('showcase.show', $showcase) }}"
                                                class="text-decoration-none">
                                                {{ __('Reply in') }} {{ ($showcase->showcaseable &&
                                                $showcase->showcaseable->thread) ?
                                                $showcase->showcaseable->thread->title : __('Unknown Thread') }}
                                            </a>
                                            @elseif($showcase->showcaseable_type === 'App\\Models\\Project')
                                            <i class="fas fa-briefcase me-2"></i>
                                            <a href="{{ route('showcase.show', $showcase) }}"
                                                class="text-decoration-none">
                                                {{ $showcase->showcaseable ? $showcase->showcaseable->title :
                                                __('Unknown Title') }}
                                            </a>
                                            @else
                                            <i class="fas fa-star me-2"></i>
                                            <a href="{{ route('showcase.show', $showcase) }}"
                                                class="text-decoration-none">
                                                {{ $showcase->title ?? __('Showcase item') }}
                                            </a>
                                            @endif
                                        </h5>

                                        @if($showcase->description)
                                        <p class="mb-2">{{ $showcase->description }}</p>
                                        @endif

                                        <p class="mb-0 small text-muted">
                                            {{ __('Showcased by') }}
                                            <a href="{{ route('profile.show', $showcase->user->username) }}"
                                                class="text-decoration-none">
                                                {{ $showcase->user->name }}
                                            </a>
                                            {{ $showcase->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $userShowcases->links() }}
                        </div>
                        @else
                        <div class="text-center py-4">
                            <p class="mb-0">{{ __('No showcase items available.') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Popular Threads -->
                <div class="card shadow-sm rounded-3 mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Popular Threads') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($popularThreads->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($popularThreads as $thread)
                            <a href="{{ route('threads.show', $thread) }}"
                                class="list-group-item list-group-item-action py-2 px-0">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $thread->title }}</h6>
                                    <small class="text-muted">{{ $thread->posts_count }} {{ __('replies') }}</small>
                                </div>
                                <small>
                                    {{ __('By') }} {{ $thread->user->name }} {{ __('in') }}
                                    <span class="fw-bold">{{ $thread->forum ? $thread->forum->name : __('Unknown Forum')
                                        }}</span>
                                </small>
                            </a>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-3">
                            <p class="mb-0">{{ __('No popular threads available.') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Create Your Showcase -->
                <div class="card shadow-sm rounded-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Create Your Showcase') }}</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ __('Showcase your best content and contributions to the community.') }}</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('showcase.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>{{ __('Create New Showcase') }}
                            </a>
                            <a href="{{ route('showcase.index') }}" class="btn btn-outline-secondary">
                                {{ __('Manage Your Showcase') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection