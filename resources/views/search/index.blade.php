@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm rounded-3 mb-4">
                <div class="card-body">
                    <form action="{{ route('search.index') }}" method="GET" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" name="query" class="form-control" placeholder="{{ __('Search...') }}" value="{{ $query }}">
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-select">
                                <option value="all" {{ $type == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                                <option value="threads" {{ $type == 'threads' ? 'selected' : '' }}>{{ __('Threads') }}</option>
                                <option value="posts" {{ $type == 'posts' ? 'selected' : '' }}>{{ __('Posts') }}</option>
                                <option value="users" {{ $type == 'users' ? 'selected' : '' }}>{{ __('Users') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">{{ __('Search') }}</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    @if($query)
                        <p class="mb-0">{{ __('Search results for') }}: <strong>{{ $query }}</strong></p>
                    @endif
                </div>
                <div>
                    <a href="{{ route('search.advanced') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-sliders me-1"></i> {{ __('Advanced Search') }}
                    </a>
                </div>
            </div>
            
            @if($query)
                @if(($type == 'all' || $type == 'threads') && $threads->count() > 0)
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Threads') }}</h5>
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
                                            {{ __('in') }} 
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
                            <h5 class="card-title mb-0">{{ __('Posts') }}</h5>
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
                            <h5 class="card-title mb-0">{{ __('Users') }}</h5>
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
                            <i class="bi bi-search fs-1 text-muted mb-3"></i>
                            <p class="mb-0">{{ __('No results found for your search.') }}</p>
                            <p class="text-muted">{{ __('Try different keywords or use the advanced search.') }}</p>
                            <a href="{{ route('search.advanced') }}" class="btn btn-primary mt-3">
                                <i class="bi bi-sliders me-1"></i> {{ __('Advanced Search') }}
                            </a>
                        </div>
                    </div>
                @endif
            @else
                <div class="card shadow-sm rounded-3">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search fs-1 text-muted mb-3"></i>
                        <p class="mb-0">{{ __('Enter a search term to find content.') }}</p>
                        <p class="text-muted">{{ __('You can search for threads, posts, and users.') }}</p>
                        <a href="{{ route('search.advanced') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-sliders me-1"></i> {{ __('Advanced Search') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
