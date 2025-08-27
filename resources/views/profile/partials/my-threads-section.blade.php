{{-- My Threads Section (thay thế Profile Posts) --}}
<div class="card my-threads-section mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-comments"></i> {{ __('profile.my_threads') }}
            @if(isset($userThreads) && $userThreads->total() > 0)
                <span class="badge bg-secondary ms-2">{{ $userThreads->total() }}</span>
            @endif
        </h5>
        @if(Auth::id() == $user->id)
            <a href="{{ route('threads.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> {{ __('profile.create_new_thread') }}
            </a>
        @endif
    </div>
    <div class="card-body">
        @if(isset($userThreads) && $userThreads->count() > 0)
            <div class="threads-list">
                @foreach($userThreads as $thread)
                    <div class="thread-item mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="thread-content flex-grow-1">
                                <h6 class="thread-title mb-2">
                                    <a href="{{ route('threads.show', $thread) }}" class="text-decoration-none">
                                        {{ $thread->title }}
                                    </a>
                                    @if($thread->is_pinned)
                                        <span class="badge bg-warning ms-2">
                                            <i class="fas fa-thumbtack"></i> {{ __('profile.pinned') }}
                                        </span>
                                    @endif
                                    @if($thread->is_locked)
                                        <span class="badge bg-secondary ms-2">
                                            <i class="fas fa-lock"></i> {{ __('profile.locked') }}
                                        </span>
                                    @endif
                                </h6>

                                @if($thread->excerpt)
                                    <p class="thread-excerpt text-muted mb-2">
                                        {{ Str::limit($thread->excerpt, 150) }}
                                    </p>
                                @endif

                                <div class="thread-meta d-flex align-items-center text-muted small">
                                    @if($thread->category)
                                        <span class="category me-3">
                                            <i class="fas fa-folder"></i>
                                            <a href="{{ route('forums.show', $thread->category) }}" class="text-decoration-none">
                                                {{ $thread->category->name }}
                                            </a>
                                        </span>
                                    @endif

                                    <span class="created-date me-3">
                                        <i class="fas fa-calendar"></i>
                                        {{ $thread->created_at->format('M d, Y') }}
                                    </span>

                                    @if($thread->updated_at != $thread->created_at)
                                        <span class="updated-date me-3">
                                            <i class="fas fa-edit"></i>
                                            {{ __('profile.updated') }} {{ $thread->updated_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="thread-stats ms-3 text-center">
                                <div class="stat-group">
                                    <div class="stat-item">
                                        <div class="stat-value">{{ $thread->view_count ?? 0 }}</div>
                                        <div class="stat-label small text-muted">{{ __('profile.views') }}</div>
                                    </div>
                                </div>
                                <div class="stat-group">
                                    <div class="stat-item">
                                        <div class="stat-value">{{ $thread->comments_count ?? 0 }}</div>
                                        <div class="stat-label small text-muted">{{ __('profile.replies') }}</div>
                                    </div>
                                </div>
                                <div class="stat-group">
                                    <div class="stat-item">
                                        <div class="stat-value">{{ $thread->reactions_count ?? 0 }}</div>
                                        <div class="stat-label small text-muted">{{ __('profile.reactions') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Thread Tags --}}
                        @if($thread->tags && $thread->tags->count() > 0)
                            <div class="thread-tags mt-2">
                                @foreach($thread->tags as $tag)
                                    <span class="badge bg-light text-dark me-1">
                                        #{{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Thread Attachments Preview --}}
                        @if($thread->attachments && $thread->attachments->count() > 0)
                            <div class="thread-attachments mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-paperclip"></i>
                                    {{ $thread->attachments->count() }} {{ __('profile.attachments') }}
                                </small>
                                <div class="attachment-preview d-flex mt-1">
                                    @foreach($thread->attachments->take(3) as $attachment)
                                        @if(in_array(strtolower(pathinfo($attachment->filename, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                            <img src="{{ $attachment->url }}" alt="{{ $attachment->filename }}"
                                                 class="attachment-thumb me-1" width="40" height="40" style="object-fit: cover; border-radius: 4px;">
                                        @else
                                            <div class="attachment-file me-1 p-1 bg-light rounded" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-file text-muted"></i>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if($thread->attachments->count() > 3)
                                        <div class="more-attachments p-1 bg-light rounded text-center" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <small class="text-muted">+{{ $thread->attachments->count() - 3 }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach

                {{-- Pagination --}}
                @if($userThreads->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $userThreads->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="empty-state text-center py-5">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">{{ __('profile.no_threads_yet') }}</h6>
                @if(Auth::id() == $user->id)
                    <p class="text-muted mb-3">{{ __('profile.create_first_thread_message') }}</p>
                    <a href="{{ route('threads.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('profile.create_first_thread') }}
                    </a>
                @else
                    <p class="text-muted">{{ $user->name }} {{ __('profile.hasnt_created_threads_yet') }}</p>
                @endif
            </div>
        @endif
    </div>
</div>

<style>
.thread-item {
    transition: all 0.2s ease;
}

.thread-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

.thread-stats .stat-group {
    margin-bottom: 8px;
}

.thread-stats .stat-value {
    font-weight: bold;
    font-size: 1.1em;
}

.attachment-thumb {
    border: 1px solid #dee2e6;
}

.thread-title a:hover {
    color: #0056b3 !important;
}

@media (max-width: 768px) {
    .thread-stats {
        margin-left: 0 !important;
        margin-top: 10px;
    }

    .d-flex.justify-content-between.align-items-start {
        flex-direction: column;
    }

    .thread-stats {
        align-self: stretch;
    }

    .thread-stats .d-flex {
        justify-content: space-around;
    }
}
</style>
