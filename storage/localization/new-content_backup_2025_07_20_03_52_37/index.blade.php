@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <!-- Newest Threads -->
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ __('Newest Threads') }}</h5>
                            <a href="{{ route('whats-new') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                        </div>
                        <div class="card-body">
                            @if($threads->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($threads as $thread)
                                        <a href="{{ $thread->forum ? route('forums.show', $thread->forum) . '#thread-' . $thread->id : '#' }}" class="list-group-item list-group-item-action py-3 px-0">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ $thread->title }}</h6>
                                                <small class="text-muted">{{ $thread->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-1 text-truncate">{{ Str::limit(strip_tags($thread->content), 100) }}</p>
                                            <small>
                                                {{ __('By') }} {{ $thread->user->name }} {{ __('in') }}
                                                <span class="fw-bold">{{ $thread->forum ? $thread->forum->name : __('Unknown Forum') }}</span>
                                            </small>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="mb-0">{{ __('No threads found.') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Newest Posts -->
                    <div class="card shadow-sm rounded-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ __('Newest Replies') }}</h5>
                            <a href="{{ route('whats-new') }}?type=posts" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                        </div>
                        <div class="card-body">
                            @if($posts->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($posts as $post)
                                        <a href="{{ ($post->thread && $post->thread->forum) ? route('forums.show', $post->thread->forum) . '#post-' . $post->id : '#' }}" class="list-group-item list-group-item-action py-3 px-0">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ __('Reply to') }}: {{ $post->thread ? $post->thread->title : __('Unknown Thread') }}</h6>
                                                <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-1 text-truncate">{{ Str::limit(strip_tags($post->content), 100) }}</p>
                                            <small>{{ __('By') }} {{ $post->user->name }}</small>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="mb-0">{{ __('No posts found.') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Statistics -->
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Statistics') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('Threads') }}:</span>
                                <span class="fw-bold">{{ \App\Models\Thread::count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('Posts') }}:</span>
                                <span class="fw-bold">{{ \App\Models\Post::count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('Members') }}:</span>
                                <span class="fw-bold">{{ \App\Models\User::count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>{{ __('Newest Member') }}:</span>
                                @php
                                    $newestMember = \App\Models\User::latest()->first();
                                @endphp
                                <span class="fw-bold">
                                    @if($newestMember)
                                        <a href="{{ route('profile.show', $newestMember->username) }}">{{ $newestMember->name }}</a>
                                    @else
                                        {{ __('None') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Online Users -->
                    <div class="card shadow-sm rounded-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ __('Online Now') }}</h5>
                            <a href="{{ route('members.online') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                        </div>
                        <div class="card-body">
                            @php
                                $onlineUsers = \App\Models\User::where('last_seen_at', '>=', now()->subMinutes(15))
                                    ->take(5)
                                    ->get();
                            @endphp

                            @if($onlineUsers->count() > 0)
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($onlineUsers as $user)
                                        <a href="{{ route('profile.show', $user->username) }}" class="text-decoration-none" data-bs-toggle="tooltip" title="{{ $user->name }}">
                                            <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle" width="40" height="40">
                                        </a>
                                    @endforeach
                                </div>
                                <div class="mt-2 small text-muted">
                                    {{ __('Total online') }}: {{ \App\Models\User::where('last_seen_at', '>=', now()->subMinutes(15))->count() }}
                                </div>
                            @else
                                <div class="text-center py-2">
                                    <p class="mb-0">{{ __('No users online.') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
