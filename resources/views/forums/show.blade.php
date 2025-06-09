@extends('layouts.app')

@section('title', $forum->name)

@section('content')

<div class="py-5">
    <div class="container">
        <div class="card shadow-sm rounded-3 mb-4">
            <div class="card-body">
                <p class="mb-0">{{ $forum->description }}</p>
            </div>
        </div>

        <div class="card shadow-sm rounded-3">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Threads') }}</h5>

                    <div class="d-flex align-items-center">
                        @auth
                        <a href="{{ route('threads.create', ['forum_id' => $forum->id]) }}"
                            class="btn btn-primary btn-sm me-3">
                            <i class="bi bi-plus-circle me-1"></i> {{ __('Create Thread') }}
                        </a>
                        @endauth

                        <div class="dropdown me-2">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('messages.sort_by') }}: {{ request('sort', 'latest') == 'latest' ?
                                __('messages.latest') : __('messages.most_replies') }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                <li><a class="dropdown-item" href="{{ route('forums.show', $forum) }}?sort=latest">{{
                                        __('messages.latest') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('forums.show', $forum) }}?sort=replies">{{
                                        __('messages.most_replies') }}</a></li>
                            </ul>
                        </div>

                        <form action="{{ route('search.index') }}" method="GET" class="d-flex">
                            <input type="hidden" name="forum_id" value="{{ $forum->id }}">
                            <input type="text" name="query" class="form-control form-control-sm me-2"
                                placeholder="{{ __('Search this forum') }}">
                            <button type="submit" class="btn btn-sm btn-primary">{{ __('Search') }}</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($threads->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($threads as $thread)
                    @include('partials.thread-item', [
                    'thread' => $thread,
                    'variant' => 'forum',
                    'showBookmark' => true,
                    'showFollow' => true,
                    'showProjectDetails' => false,
                    'showContentPreview' => false,
                    'showUserInfo' => false
                    ])
                    @endforeach
                </div>

                <div class="d-flex justify-content-center p-3">
                    {{ $threads->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <p class="mb-0">{{ __('No threads found in this forum.') }}</p>
                    @auth
                    <a href="{{ route('threads.create', ['forum_id' => $forum->id]) }}" class="btn btn-primary mt-3">{{
                        __('Create the first thread') }}</a>
                    @endauth
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection