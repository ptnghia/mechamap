@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    @if($bookmarks->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($bookmarks as $bookmark)
                                <div class="list-group-item py-3 px-0">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div>
                                            @if($bookmark->bookmarkable_type === 'App\\Models\\Thread')
                                                <h5 class="mb-1">
                                                    <i class="fas fa-comment-left-text me-2"></i>
                                                    <a href="{{ route('threads.show', $bookmark->bookmarkable) }}" class="text-decoration-none">
                                                        {{ $bookmark->bookmarkable->title }}
                                                    </a>
                                                </h5>
                                                <p class="mb-1 text-muted">
                                                    {{ __('Thread in') }} {{ $bookmark->bookmarkable->forum->name }}
                                                </p>
                                            @elseif($bookmark->bookmarkable_type === 'App\\Models\\Post')
                                                <h5 class="mb-1">
                                                    <i class="fas fa-comment-right me-2"></i>
                                                    <a href="{{ route('threads.show', $bookmark->bookmarkable->thread_id) }}#post-{{ $bookmark->bookmarkable->id }}" class="text-decoration-none">
                                                        {{ __('Reply in') }} {{ $bookmark->bookmarkable->thread->title }}
                                                    </a>
                                                </h5>
                                                <p class="mb-1 text-muted">
                                                    {{ Str::limit(strip_tags($bookmark->bookmarkable->content), 100) }}
                                                </p>
                                            @else
                                                <h5 class="mb-1">
                                                    <i class="far fa-bookmark me-2"></i>
                                                    {{ __('Bookmarked item') }}
                                                </h5>
                                            @endif
                                            
                                            @if($bookmark->notes)
                                                <div class="mt-2 p-2 bg-light rounded">
                                                    <small>{{ __('Notes') }}: {{ $bookmark->notes }}</small>
                                                </div>
                                            @endif
                                            
                                            <small class="text-muted">{{ __('Bookmarked') }} {{ $bookmark->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div>
                                            <form action="{{ route('bookmarks.destroy', $bookmark) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="far fa-bookmark-x"></i> {{ __('Remove') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $bookmarks->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="far fa-bookmark fs-1 text-muted mb-3"></i>
                            <p class="mb-0">{{ __('You don\'t have any bookmarks yet.') }}</p>
                            <p class="text-muted">{{ __('Bookmark threads and posts to find them easily later.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
