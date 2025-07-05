@extends('layouts.app')

@section('title', 'Saved Threads')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Saved Threads</h1>
                <a href="{{ route('threads.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Back to Forums
                </a>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Your Saved Threads</h5>
                    <span class="badge bg-secondary">{{ $savedThreads->total() }} threads</span>
                </div>

                @if($savedThreads->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($savedThreads as $thread)
                    <div class="list-group-item p-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <img src="{{ $thread->user->profile_photo_url }}" alt="{{ $thread->user->name }}"
                                    class="rounded-circle" width="50" height="50">
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5 class="mb-1">
                                        <a href="{{ route('threads.show', $thread) }}" class="text-decoration-none">{{
                                            $thread->title }}</a>
                                        @if($thread->is_sticky)
                                        <span class="badge bg-primary ms-1">{{ __('messages.thread_status.sticky')
                                            }}</span>
                                        @endif
                                        @if($thread->is_locked)
                                        <span class="badge bg-danger ms-1">{{ __('messages.thread_status.locked')
                                            }}</span>
                                        @endif
                                    </h5>
                                    <div>
                                        <small class="text-muted">{{ $thread->created_at->diffForHumans() }}</small>
                                        <form action="{{ route('threads.save', $thread) }}" method="POST"
                                            class="d-inline ms-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Remove from saved">
                                                <i class="far fa-bookmark-x"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Project Details -->
                                @if($thread->status)
                                <div class="project-details mb-2 small">
                                    <span class="badge bg-light text-dark me-2">Status: {{ $thread->status }}</span>
                                </div>
                                @endif

                                <p class="mb-1 text-muted">{{ Str::limit(strip_tags($thread->content), 150) }}</p>

                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div class="small">
                                        <span class="me-3"><i class="person"></i> {{ $thread->user->name }}</span>
                                        <span class="me-3"><i class="fas fa-eye"></i> {{ $thread->view_count }}
                                            views</span>
                                        <span class="me-3"><i class="fas fa-comment"></i> {{ $thread->allComments->count()
                                            }} {{ __('messages.replies') }}</span>
                                        <span><i class="fas fa-users"></i> {{ $thread->participant_count }}
                                            participants</span>
                                    </div>

                                    <div>
                                        @if($thread->category)
                                        <a href="{{ route('threads.index', ['category' => $thread->category->id]) }}"
                                            class="badge bg-secondary text-decoration-none">{{ $thread->category->name
                                            }}</a>
                                        @endif

                                        @if($thread->forum)
                                        <a href="{{ route('threads.index', ['forum' => $thread->forum->id]) }}"
                                            class="badge bg-info text-decoration-none">{{ $thread->forum->name }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="card-footer">
                    {{ $savedThreads->links() }}
                </div>
                @else
                <div class="card-body text-center py-5">
                    <i class="far fa-bookmark display-4 text-muted"></i>
                    <p class="mt-3">You haven't saved any threads yet.</p>
                    <a href="{{ route('threads.index') }}" class="btn btn-primary">Browse Threads</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection