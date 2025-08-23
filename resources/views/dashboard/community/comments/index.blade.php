@extends('dashboard.layouts.app')

@section('title', __('comments.index.title'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-9">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-2">
                        <i class="fas fa-comment-dots"></i>
                        {{ __('comments.index.title') }}
                    </h1>
                    <p class="text-muted mb-0">{{ __('comments.index.description') }}</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-comment-dots fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="h4 mb-0">{{ $stats['total'] }}</div>
                                    <div class="small">{{ __('comments.index.total_comments') }}</div>
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
                                    <div class="h4 mb-0">{{ $stats['verified'] }}</div>
                                    <div class="small">{{ __('comments.index.approved') }}</div>
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
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="h4 mb-0">{{ $stats['solutions'] }}</div>
                                    <div class="small">{{ __('comments.index.pending') }}</div>
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
                                    <i class="fas fa-thumbs-up fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="h4 mb-0">{{ number_format($stats['total_likes']) }}</div>
                                    <div class="small">{{ __('comments.index.rejected') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.community.comments.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search"
                                       value="{{ $search }}" placeholder="{{ __('comments.index.search_placeholder') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="thread_id" class="form-select">
                                <option value="">{{ __('comments.index.all_threads') }}</option>
                                @foreach($threads as $thread)
                                    <option value="{{ $thread->id }}" {{ $currentThreadId == $thread->id ? 'selected' : '' }}>
                                        {{ Str::limit($thread->title, 50) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">{{ __('comments.index.all_status') }}</option>
                                <option value="verified" {{ $currentStatus == 'verified' ? 'selected' : '' }}>{{ __('comments.index.approved') }}</option>
                                <option value="flagged" {{ $currentStatus == 'flagged' ? 'selected' : '' }}>{{ __('comments.index.pending') }}</option>
                                <option value="solution" {{ $currentStatus == 'solution' ? 'selected' : '' }}>{{ __('comments.index.rejected') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i>
                                    {{ __('comments.index.filter') }}
                                </button>
                                <a href="{{ route('dashboard.community.comments.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                    {{ __('comments.index.clear') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Comments List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('comments.index.my_comments') }}</h5>
                </div>
                <div class="card-body">
                    @if($comments->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($comments as $comment)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <!-- Thread Info -->
                                            <div class="mb-2">
                                                <h6 class="mb-1">
                                                    <a href="{{ route('threads.show', $comment->thread->slug) }}"
                                                       class="text-decoration-none" target="_blank">
                                                        {{ $comment->thread->title }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-folder"></i>
                                                    {{ $comment->thread->forum->name }}
                                                </small>
                                            </div>

                                            <!-- Comment Content -->
                                            <div class="comment-content mb-2">
                                                <div class="text-muted small mb-1">{{ __('comments.index.comment') }}:</div>
                                                <div class="border-start border-3 border-primary ps-3">
                                                    {!! Str::limit(strip_tags($comment->content), 200) !!}
                                                </div>
                                            </div>

                                            <!-- Comment Stats -->
                                            <div class="d-flex align-items-center gap-3 text-muted small">
                                                <span>
                                                    <i class="fas fa-thumbs-up"></i>
                                                    {{ $comment->like_count ?? 0 }}
                                                </span>
                                                <span>
                                                    <i class="fas fa-clock"></i>
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </span>
                                                @if($comment->edited_at)
                                                    <span>
                                                        <i class="fas fa-edit"></i>
                                                        {{ __('comments.index.created') }} {{ $comment->edited_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Status & Actions -->
                                        <div class="flex-shrink-0 ms-3">
                                            <div class="d-flex flex-column align-items-end gap-2">
                                                <!-- Status Badges -->
                                                <div>
                                                    @if($comment->is_solution)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-star"></i>
                                                            {{ __('comments.index.approved') }}
                                                        </span>
                                                    @endif
                                                    @if($comment->verification_status == 'verified')
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-check"></i>
                                                            {{ __('comments.index.approved') }}
                                                        </span>
                                                    @endif
                                                    @if($comment->is_flagged)
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-flag"></i>
                                                            {{ __('comments.index.pending') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('threads.show', $comment->thread->slug) }}#comment-{{ $comment->id }}"
                                                       class="btn btn-outline-primary" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-secondary"
                                                            onclick="editComment({{ $comment->id }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger"
                                                            onclick="deleteComment({{ $comment->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($comments->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $comments->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comment-dots fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('comments.index.no_comments') }}</h5>
                            <p class="text-muted">{{ __('comments.index.no_comments') }}</p>
                            <a href="{{ route('threads.index') }}" class="btn btn-primary">
                                <i class="fas fa-comments"></i>
                                {{ __('threads.index.create_thread') }}
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

<!-- Edit Comment Modal -->
<div class="modal fade" id="editCommentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('comments.index.edit') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editCommentForm">
                    <div class="mb-3">
                        <label for="commentContent" class="form-label">{{ __('comments.index.comment') }}</label>
                        <textarea class="form-control" id="commentContent" rows="5" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('comments.index.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="saveCommentEdit">{{ __('comments.index.save') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Comment Modal -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('comments.index.delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('comments.index.no_comments') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('comments.index.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteComment">{{ __('comments.index.delete') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let commentToEdit = null;
let commentToDelete = null;

function editComment(commentId) {
    commentToEdit = commentId;
    // Load comment content via AJAX
    fetch(`/dashboard/community/comments/${commentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('commentContent').value = data.comment.content;
                const modal = new bootstrap.Modal(document.getElementById('editCommentModal'));
                modal.show();
            }
        })
        .catch(error => console.error('Error:', error));
}

function deleteComment(commentId) {
    commentToDelete = commentId;
    const modal = new bootstrap.Modal(document.getElementById('deleteCommentModal'));
    modal.show();
}

document.getElementById('saveCommentEdit').addEventListener('click', function() {
    if (commentToEdit) {
        const content = document.getElementById('commentContent').value;

        fetch(`/dashboard/community/comments/${commentToEdit}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ content: content })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating comment');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating comment');
        });
    }
});

document.getElementById('confirmDeleteComment').addEventListener('click', function() {
    if (commentToDelete) {
        fetch(`/dashboard/community/comments/${commentToDelete}`, {
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
                alert('Error deleting comment');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting comment');
        });
    }
});
</script>
@endpush
