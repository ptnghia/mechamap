@extends('layouts.app')

@section('title', 'Page Title')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/search.css') }}">
@endpush

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm rounded-3 mb-4">
                <div class="card-body">
                    <form action="{{ route('search.index') }}" method="GET" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" name="query" class="form-control" placeholder="{{ __('search.search_placeholder') }}" value="{{ $query }}">
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-select">
                                <option value="all" {{ $type == 'all' ? 'selected' : '' }}>{{ __('search.all') }}</option>
                                <option value="threads" {{ $type == 'threads' ? 'selected' : '' }}>{{ __('search.threads') }}</option>
                                <option value="posts" {{ $type == 'posts' ? 'selected' : '' }}>{{ __('search.posts') }}</option>
                                <option value="users" {{ $type == 'users' ? 'selected' : '' }}>{{ __('search.users') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">{{ t_search('form.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    @if($query)
                        <p class="mb-0">{{ __('search.search_results_for') }}: <strong>{{ $query }}</strong></p>
                    @endif
                </div>
                <div>
                    <a href="{{ route('forums.search.advanced') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-sliders-h me-1"></i> {{ __('search.advanced_search') }}
                    </a>
                </div>
            </div>

            @if($query)
                @if(($type == 'all' || $type == 'threads') && $threads->count() > 0)
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('search.threads') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($threads as $thread)
                                    <div class="list-group-item py-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <a href="#" class="text-decoration-none">{{ $thread->title }}</a>
                                            </h5>
                                            <small class="text-muted">{{ $thread->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit(strip_tags($thread->content), 150) }}</p>
                                        <small>
                                            {{ __('By') }}
                                            <a href="{{ route('profile.show', $thread->user->username) }}" class="text-decoration-none">
                                                {{ $thread->user->name }}
                                            </a>
                                            {{ __('search.in') }}
                                            <a href="{{ route('forums.show', $thread->forum) }}" class="text-decoration-none fw-bold">
                                                {{ $thread->forum->name }}
                                            </a>
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if(($type == 'all' || $type == 'posts') && $posts->count() > 0)
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('search.posts') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($posts as $post)
                                    <div class="list-group-item py-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <a href="#" class="text-decoration-none">{{ __('Reply in') }}: {{ $post->thread->title }}</a>
                                            </h5>
                                            <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit(strip_tags($post->content), 150) }}</p>
                                        <small>
                                            {{ __('By') }}
                                            <a href="{{ route('profile.show', $post->user->username) }}" class="text-decoration-none">
                                                {{ $post->user->name }}
                                            </a>
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if(($type == 'all' || $type == 'users') && $users->count() > 0)
                    <div class="card shadow-sm rounded-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('search.users') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($users as $user)
                                    <div class="list-group-item py-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle me-3" width="50" height="50">
                                            <div>
                                                <h5 class="mb-1">
                                                    <a href="{{ route('profile.show', $user->username) }}" class="text-decoration-none">
                                                        {{ $user->name }}
                                                    </a>
                                                </h5>
                                                <p class="mb-0 text-muted small">
                                                    {{ '@' . $user->username }} · {{ __('Joined') }} {{ $user->created_at->format('M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if(($type == 'all' && $threads->count() == 0 && $posts->count() == 0 && $users->count() == 0) ||
                    ($type == 'threads' && $threads->count() == 0) ||
                    ($type == 'posts' && $posts->count() == 0) ||
                    ($type == 'users' && $users->count() == 0))
                    <div class="card shadow-sm rounded-3">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-search fs-1 text-muted mb-3"></i>
                            <p class="mb-0">{{ __('search.no_results_found') }}</p>
                            <p class="text-muted">{{ __('search.try_different_keywords') }}</p>
                            <a href="{{ route('search.advanced') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-sliders-h me-1"></i> {{ __('search.advanced_search') }}
                            </a>
                        </div>
                    </div>
                @endif
            @else
                <div class="card shadow-sm rounded-3">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search fs-1 text-muted mb-3"></i>
                        <p class="mb-0">{{ __('search.enter_search_term') }}</p>
                        <p class="text-muted">{{ __('search.search_description') }}</p>
                        <a href="{{ route('search.advanced') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-sliders-h me-1"></i> {{ __('search.advanced_search') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
