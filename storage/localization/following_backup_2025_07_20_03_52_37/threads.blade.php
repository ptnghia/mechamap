@extends('layouts.app')

@section('title', 'Followed Threads')

@section('content')
<div class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="card shadow-sm rounded-3 mb-4">
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('following.index') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-users-fill me-2"></i> {{ __('Following') }}
                            </a>
                            <a href="{{ route('following.followers') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-user me-2"></i> {{ __('Followers') }}
                            </a>
                            <a href="{{ route('following.threads') }}"
                                class="list-group-item list-group-item-action active">
                                <i class="far fa-bookmark-fill me-2"></i> {{ __('Followed Threads') }}
                            </a>
                            <a href="{{ route('following.participated') }}"
                                class="list-group-item list-group-item-action">
                                <i class="fas fa-comment-dots-fill me-2"></i> {{ __('Participated Discussions') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Followed Threads') }}</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                id="filtersDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-filter me-1"></i> {{ __('Filters') }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filtersDropdown">
                                <li><a class="dropdown-item" href="{{ route('following.threads') }}">{{ __('All Forums')
                                        }}</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                @foreach(App\Models\Forum::all() as $forum)
                                <li><a class="dropdown-item"
                                        href="{{ route('following.threads', ['forum' => $forum->id]) }}">{{ $forum->name
                                        }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($threads->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($threads as $thread)
                            <div class="list-group-item p-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <img src="{{ $thread->user->getAvatarUrl() }}" alt="{{ $thread->user->name }}"
                                            class="rounded-circle" width="40" height="40">
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0">
                                                <a href="{{ route('threads.show', $thread) }}"
                                                    class="text-decoration-none">{{ $thread->title }}</a>
                                            </h6>
                                            <div>
                                                <form action="{{ route('threads.follow.remove', $thread) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="far fa-bookmark-x"></i> {{ __('Unfollow') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="text-muted small mb-2">
                                            <a href="{{ route('profile.show', $thread->user->username) }}"
                                                class="text-decoration-none">{{ $thread->user->name }}</a> •
                                            <a href="{{ route('forums.show', $thread->forum) }}"
                                                class="text-decoration-none">{{ $thread->forum->name }}</a> •
                                            {{ $thread->created_at->diffForHumans() }}
                                        </div>
                                        <div class="d-flex align-items-center small text-muted">
                                            <span class="me-3"><i class="fas fa-eye me-1"></i> {{ $thread->view_count
                                                }}</span>
                                            <span class="me-3"><i class="fas fa-comment-dots me-1"></i> {{
                                                $thread->comments->count() }}</span>
                                            <span><i class="fas fa-heart me-1"></i> {{ $thread->likes->count() }}</span>
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
                            <i class="far fa-bookmark fs-1 text-muted mb-3"></i>
                            <p class="mb-0">{{ __('You are not watching any threads.') }}</p>
                            <p class="text-muted">{{ __('Follow threads to see them here.') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
