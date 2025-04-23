@extends('layouts.app')

@section('title', 'Participated Discussions')

@section('content')
    <div class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <a href="{{ route('following.index') }}" class="list-group-item list-group-item-action">
                                    <i class="bi bi-people-fill me-2"></i> {{ __('Following') }}
                                </a>
                                <a href="{{ route('following.followers') }}" class="list-group-item list-group-item-action">
                                    <i class="bi bi-person-fill me-2"></i> {{ __('Followers') }}
                                </a>
                                <a href="{{ route('following.threads') }}" class="list-group-item list-group-item-action">
                                    <i class="bi bi-bookmark-fill me-2"></i> {{ __('Followed Threads') }}
                                </a>
                                <a href="{{ route('following.participated') }}" class="list-group-item list-group-item-action active">
                                    <i class="bi bi-chat-dots-fill me-2"></i> {{ __('Participated Discussions') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-9">
                    <div class="card shadow-sm rounded-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Threads in which you\'ve participated') }}</h5>
                        </div>
                        <div class="card-body">
                            @if($threads->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($threads as $thread)
                                        <div class="list-group-item p-3">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <img src="{{ $thread->user->getAvatarUrl() }}" alt="{{ $thread->user->name }}" class="rounded-circle" width="40" height="40">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <h6 class="mb-0">
                                                            <a href="{{ route('threads.show', $thread) }}" class="text-decoration-none">{{ $thread->title }}</a>
                                                        </h6>
                                                        <div>
                                                            @if(Auth::check() && !$thread->isFollowedBy(Auth::user()))
                                                                <form action="{{ route('threads.follow.toggle', $thread) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                                                        <i class="bi bi-bookmark-plus"></i> {{ __('Follow') }}
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <form action="{{ route('threads.follow.toggle', $thread) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                        <i class="bi bi-bookmark-x"></i> {{ __('Unfollow') }}
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="text-muted small mb-2">
                                                        <a href="{{ route('profile.show', $thread->user->username) }}" class="text-decoration-none">{{ $thread->user->name }}</a> •
                                                        <a href="{{ route('forums.show', $thread->forum) }}" class="text-decoration-none">{{ $thread->forum->name }}</a> •
                                                        {{ $thread->created_at->diffForHumans() }}
                                                    </div>
                                                    <div class="d-flex align-items-center small text-muted">
                                                        <span class="me-3"><i class="bi bi-eye me-1"></i> {{ $thread->view_count }}</span>
                                                        <span class="me-3"><i class="bi bi-chat-dots me-1"></i> {{ $thread->comments->count() }}</span>
                                                        <span><i class="bi bi-heart me-1"></i> {{ $thread->likes->count() }}</span>
                                                    </div>
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
                                    <i class="bi bi-chat-dots fs-1 text-muted mb-3"></i>
                                    <p class="mb-0">{{ __('You haven\'t participated in any discussions yet.') }}</p>
                                    <p class="text-muted">{{ __('Join the conversation by commenting on threads.') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
