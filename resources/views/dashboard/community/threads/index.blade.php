@extends('dashboard.layouts.app')

@section('title', __('threads.index.title'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-9">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-2">
                        <i class="fas fa-comments"></i>
                        {{ __('threads.index.heading') }}
                    </h1>
                    <p class="text-muted mb-0">{{ __('threads.index.description') }}</p>
                </div>
                <div>
                    <a href="{{ route('threads.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        {{ __('threads.index.create_thread') }}
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-comments fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="h4 mb-0">{{ $stats['total'] }}</div>
                                    <div class="small">{{ __('threads.index.total_threads') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="h4 mb-0">{{ $stats['approved'] }}</div>
                                    <div class="small">{{ __('threads.index.published') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="h4 mb-0">{{ $stats['pending'] }}</div>
                                    <div class="small">{{ __('threads.index.draft') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-eye fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="h4 mb-0">{{ number_format($stats['total_views']) }}</div>
                                    <div class="small">{{ __('threads.index.total_views') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.community.threads.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search"
                                       value="{{ $search }}" placeholder="{{ __('threads.index.search_placeholder') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="forum" class="form-select">
                                <option value="">{{ __('threads.index.all_forums') }}</option>
                                @foreach($forums as $forum)
                                    <option value="{{ $forum->id }}" {{ $currentFilter == $forum->id ? 'selected' : '' }}>
                                        {{ $forum->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">{{ __('threads.index.all_status') }}</option>
                                <option value="approved" {{ $currentStatus == 'approved' ? 'selected' : '' }}>{{ __('threads.index.published') }}</option>
                                <option value="under_review" {{ $currentStatus == 'under_review' ? 'selected' : '' }}>{{ __('threads.index.draft') }}</option>
                                <option value="rejected" {{ $currentStatus == 'rejected' ? 'selected' : '' }}>{{ __('threads.index.rejected') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="solved" class="form-select">
                                <option value="">{{ __('threads.index.all_solved') }}</option>
                                <option value="1" {{ $currentSolved == '1' ? 'selected' : '' }}>{{ __('threads.index.solved') }}</option>
                                <option value="0" {{ $currentSolved == '0' ? 'selected' : '' }}>{{ __('threads.index.unsolved') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i>
                                    {{ __('threads.index.filter') }}
                                </button>
                                <a href="{{ route('dashboard.community.threads.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                    {{ __('threads.index.clear') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Threads List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('threads.index.threads_list') }}</h5>
                </div>
                <div class="card-body">
                    @if($threads->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('threads.index.title') }}</th>
                                        <th>{{ __('threads.index.forum') }}</th>
                                        <th>{{ __('threads.index.status') }}</th>
                                        <th>{{ __('threads.index.stats') }}</th>
                                        <th>{{ __('threads.index.created') }}</th>
                                        <th>{{ __('threads.index.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($threads as $thread)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">
                                                            <a href="{{ route('threads.show', $thread->slug) }}"
                                                               class="text-decoration-none" target="_blank">
                                                                {{ $thread->title }}
                                                            </a>
                                                            @if($thread->is_solved)
                                                                <span class="badge bg-success ms-2">
                                                                    <i class="fas fa-check"></i>
                                                                    {{ __('threads.index.solved') }}
                                                                </span>
                                                            @endif
                                                        </h6>
                                                        @if($thread->tags->count() > 0)
                                                            <div class="mt-1">
                                                                @foreach($thread->tags as $tag)
                                                                    <span class="badge bg-light text-dark me-1">{{ $tag->name }}</span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $thread->forum->name }}</span>
                                            </td>
                                            <td>
                                                @if($thread->moderation_status == 'approved')
                                                    <span class="badge bg-success">{{ __('threads.index.published') }}</span>
                                                @elseif($thread->moderation_status == 'under_review')
                                                    <span class="badge bg-warning">{{ __('threads.index.draft') }}</span>
                                                @elseif($thread->moderation_status == 'rejected')
                                                    <span class="badge bg-danger">{{ __('threads.index.rejected') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="small text-muted">
                                                    <div><i class="fas fa-eye"></i> {{ number_format($thread->view_count) }}</div>
                                                    <div><i class="fas fa-comments"></i> {{ $thread->comments_count }}</div>
                                                    <div><i class="fas fa-bookmark"></i> {{ $thread->bookmarks_count }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small text-muted">
                                                    {{ $thread->created_at->diffForHumans() }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('threads.show', $thread->slug) }}"
                                                       class="btn btn-outline-primary" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('threads.edit', $thread->slug) }}"
                                                       class="btn btn-outline-secondary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger"
                                                            onclick="deleteThread({{ $thread->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($threads->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $threads->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('threads.index.no_threads') }}</h5>
                            <p class="text-muted">{{ __('threads.index.no_threads_desc') }}</p>
                            <a href="{{ route('threads.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                {{ __('threads.index.start_writing') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        @include('dashboard.layouts.sidebar')
    </div>
</div>

<!-- Delete Thread Modal -->
<div class="modal fade" id="deleteThreadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('threads.index.delete_thread') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('threads.index.delete_confirm') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('threads.index.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteThread">{{ __('threads.index.delete') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let threadToDelete = null;

function deleteThread(threadId) {
    threadToDelete = threadId;
    const modal = new bootstrap.Modal(document.getElementById('deleteThreadModal'));
    modal.show();
}

document.getElementById('confirmDeleteThread').addEventListener('click', function() {
    if (threadToDelete) {
        // Implement delete functionality
        fetch(`/dashboard/community/threads/${threadToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting thread');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting thread');
        });
    }
});
</script>
@endpush
