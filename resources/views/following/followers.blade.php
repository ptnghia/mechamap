@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <a href="{{ route('following.index') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-users-fill me-2"></i> {{ __('Following') }}
                                </a>
                                <a href="{{ route('following.followers') }}" class="list-group-item list-group-item-action active">
                                    <i class="fas fa-user me-2"></i> {{ __('Followers') }}
                                </a>
                                <a href="{{ route('following.threads') }}" class="list-group-item list-group-item-action">
                                    <i class="far fa-bookmark-fill me-2"></i> {{ __('Followed Threads') }}
                                </a>
                                <a href="{{ route('following.participated') }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-comment-dots-fill me-2"></i> {{ __('Participated Discussions') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card shadow-sm rounded-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('People Following You') }}</h5>
                        </div>
                        <div class="card-body">
                            @if($followers->count() > 0)
                                <div class="row">
                                    @foreach($followers as $user)
                                        <div class="col-md-6 mb-4">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle me-3" width="50" height="50">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <a href="{{ route('profile.show', $user->username) }}" class="text-decoration-none">{{ $user->name }}</a>
                                                    </h6>
                                                    <p class="text-muted small mb-0">{{ '@' . $user->username }}</p>
                                                </div>
                                                <div class="ms-auto">
                                                    @if(Auth::user()->following()->where('following_id', $user->id)->exists())
                                                        <form action="{{ route('profile.unfollow', $user->username) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Unfollow') }}</button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('profile.follow', $user->username) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-primary">{{ __('Follow') }}</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="d-flex justify-content-center mt-4">
                                    {{ $followers->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="person fs-1 text-muted mb-3"></i>
                                    <p class="mb-0">{{ __('You don\'t have any followers yet.') }}</p>
                                    <p class="text-muted">{{ __('When someone follows you, they will appear here.') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
