@extends('layouts.app')

@section('title', 'Public Showcase - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/showcase-item.css') }}">
@endpush

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
                        <div class="row">
                            @foreach($userShowcases as $showcase)
                            <div class="col-lg-4 col-md-6 mb-4">
                                @include('partials.showcase-item', ['showcase' => $showcase])
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
