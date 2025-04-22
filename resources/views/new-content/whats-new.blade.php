@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm rounded-3 mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ request('type') != 'posts' ? 'active' : '' }}" href="{{ route('whats-new') }}">
                                {{ __('New Threads') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('type') == 'posts' ? 'active' : '' }}" href="{{ route('whats-new') }}?type=posts">
                                {{ __('New Posts') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    @if(request('type') != 'posts')
                        <!-- Threads Tab -->
                        @if($threads->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($threads as $thread)
                                    <div class="list-group-item py-3 px-0">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <a href="{{ $thread->forum ? route('forums.show', $thread->forum) . '#thread-' . $thread->id : '#' }}" class="text-decoration-none">
                                                    {{ $thread->title }}
                                                </a>
                                            </h5>
                                            <small class="text-muted">{{ $thread->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit(strip_tags($thread->content), 200) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small>
                                                {{ __('By') }}
                                                <a href="{{ route('profile.show', $thread->user->username) }}" class="text-decoration-none">
                                                    {{ $thread->user->name }}
                                                </a>
                                                {{ __('in') }}
                                                <a href="{{ $thread->forum ? route('forums.show', $thread->forum) : '#' }}" class="text-decoration-none fw-bold">
                                                    {{ $thread->forum ? $thread->forum->name : __('Unknown Forum') }}
                                                </a>
                                            </small>
                                            <div>
                                                <span class="badge bg-primary">{{ $thread->posts_count ?? 0 }} {{ __('replies') }}</span>
                                                <span class="badge bg-secondary">{{ $thread->views ?? 0 }} {{ __('views') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $threads->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <p class="mb-0">{{ __('No threads found.') }}</p>
                            </div>
                        @endif
                    @else
                        <!-- Posts Tab -->
                        @if($posts->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($posts as $post)
                                    <div class="list-group-item py-3 px-0">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <a href="{{ ($post->thread && $post->thread->forum) ? route('forums.show', $post->thread->forum) . '#post-' . $post->id : '#' }}" class="text-decoration-none">
                                                    {{ __('Reply to') }}: {{ $post->thread ? $post->thread->title : __('Unknown Thread') }}
                                                </a>
                                            </h5>
                                            <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit(strip_tags($post->content), 200) }}</p>
                                        <small>
                                            {{ __('By') }}
                                            <a href="{{ route('profile.show', $post->user->username) }}" class="text-decoration-none">
                                                {{ $post->user->name }}
                                            </a>
                                        </small>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $posts->appends(['type' => 'posts'])->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <p class="mb-0">{{ __('No posts found.') }}</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
