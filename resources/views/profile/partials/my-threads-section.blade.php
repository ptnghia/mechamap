{{-- My Threads Section (thay thế Profile Posts) --}}
<div class="card my-threads-section mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-comments"></i> {{ __('profile.my_threads') }}
            @if(isset($userThreads) && $userThreads->total() > 0)
                <span class="badge bg-secondary ms-2">{{ $userThreads->total() }}</span>
            @endif
        </h5>
        @if(Auth::check() && Auth::id() == $user->id)
            <a href="{{ route('threads.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> {{ __('profile.create_new_thread') }}
            </a>
        @endif
    </div>
    <div class="card-body">
        @if(isset($userThreads) && $userThreads->count() > 0)
            <div class="threads-list">
                @foreach($userThreads as $thread)
                    @include('partials.thread-item', ['thread' => $thread])
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
