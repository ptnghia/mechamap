@extends('layouts.app')

@section('title', $thread->title)

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('threads.index') }}">Forums</a></li>
            @if($thread->forum)
                <li class="breadcrumb-item"><a href="{{ route('threads.index', ['forum' => $thread->forum->id]) }}">{{ $thread->forum->name }}</a></li>
            @endif
            @if($thread->category)
                <li class="breadcrumb-item"><a href="{{ route('threads.index', ['category' => $thread->category->id]) }}">{{ $thread->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $thread->title }}</li>
        </ol>
    </nav>

    <!-- Thread Info -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ $thread->title }}</h1>
        <div class="thread-stats">
            <span class="badge bg-secondary">{{ $thread->view_count }} views</span>
            <span class="badge bg-secondary">{{ $thread->allComments->count() }} replies</span>
            <span class="badge bg-secondary">{{ $thread->participant_count }} participants</span>
        </div>
    </div>

    <!-- Main Thread -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="{{ $thread->user->profile_photo_url }}" alt="{{ $thread->user->name }}" class="rounded-circle me-2" width="40">
                <div>
                    <a href="{{ route('profile.show', $thread->user) }}" class="fw-bold text-decoration-none">{{ $thread->user->name }}</a>
                    <div class="text-muted small">
                        <span>{{ $thread->user->threads->count() }} posts</span> · 
                        <span>Joined {{ $thread->user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="text-muted small">
                #1 · {{ $thread->created_at->diffForHumans() }}
            </div>
        </div>
        <div class="card-body">
            <!-- Project Details -->
            @if($thread->location || $thread->usage || $thread->floors || $thread->status)
            <div class="project-details mb-3 p-3 bg-light rounded">
                @if($thread->location)
                <div><strong>Vị trí:</strong> {{ $thread->location }}</div>
                @endif
                
                @if($thread->usage)
                <div><strong>Công dụng:</strong> {{ $thread->usage }}</div>
                @endif
                
                @if($thread->floors)
                <div><strong>Số tầng:</strong> {{ $thread->floors }}</div>
                @endif
                
                @if($thread->status)
                <div><strong>Trạng thái:</strong> {{ $thread->status }}</div>
                @endif
            </div>
            @endif

            <!-- Thread Content -->
            <div class="thread-content">
                {!! $thread->content !!}
            </div>
            
            <!-- Thread Images -->
            @if($thread->media->count() > 0)
            <div class="thread-images mt-3">
                <div class="row">
                    @foreach($thread->media as $media)
                    <div class="col-md-4 mb-3">
                        <a href="{{ asset('storage/' . $media->path) }}" data-lightbox="thread-images">
                            <img src="{{ asset('storage/' . $media->path) }}" alt="Thread image" class="img-fluid rounded">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        <div class="card-footer d-flex justify-content-between">
            <div>
                <!-- Like Button -->
                <form action="{{ route('threads.like', $thread) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $isLiked ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="bi bi-hand-thumbs-up"></i> 
                        Like
                        <span class="badge bg-secondary">{{ $thread->likes->count() }}</span>
                    </button>
                </form>
                
                <!-- Save Button -->
                <form action="{{ route('threads.save', $thread) }}" method="POST" class="d-inline ms-2">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $isSaved ? 'btn-success' : 'btn-outline-success' }}">
                        <i class="bi {{ $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i> 
                        {{ $isSaved ? 'Saved' : 'Save' }}
                    </button>
                </form>
            </div>
            
            <div>
                <!-- Share Button -->
                <div class="dropdown d-inline">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="shareDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-share"></i> Share
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="shareDropdown">
                        <li><a class="dropdown-item" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank"><i class="bi bi-facebook me-2"></i>Facebook</a></li>
                        <li><a class="dropdown-item" href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($thread->title) }}" target="_blank"><i class="bi bi-twitter me-2"></i>Twitter</a></li>
                        <li><a class="dropdown-item" href="https://wa.me/?text={{ urlencode($thread->title . ' ' . request()->url()) }}" target="_blank"><i class="bi bi-whatsapp me-2"></i>WhatsApp</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="navigator.clipboard.writeText('{{ request()->url() }}'); alert('Link copied to clipboard!'); return false;"><i class="bi bi-clipboard me-2"></i>Copy Link</a></li>
                    </ul>
                </div>
                
                <!-- Reply Button -->
                <a href="#reply-form" class="btn btn-sm btn-primary ms-2">
                    <i class="bi bi-reply"></i> Reply
                </a>
                
                <!-- Edit/Delete Buttons (if owner) -->
                @can('update', $thread)
                <div class="btn-group ms-2">
                    <a href="{{ route('threads.edit', $thread) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteThreadModal">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
                
                <!-- Delete Modal -->
                <div class="modal fade" id="deleteThreadModal" tabindex="-1" aria-labelledby="deleteThreadModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteThreadModalLabel">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this thread? This action cannot be undone.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form action="{{ route('threads.destroy', $thread) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete Thread</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="comments-section mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>{{ $comments->total() }} Replies</h3>
            
            <!-- Sort Options -->
            <div class="btn-group">
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}" class="btn btn-sm {{ request('sort', 'oldest') == 'oldest' ? 'btn-primary' : 'btn-outline-primary' }}">Oldest first</a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" class="btn btn-sm {{ request('sort') == 'newest' ? 'btn-primary' : 'btn-outline-primary' }}">Newest first</a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'reactions']) }}" class="btn btn-sm {{ request('sort') == 'reactions' ? 'btn-primary' : 'btn-outline-primary' }}">Most reactions</a>
            </div>
        </div>

        <!-- Comments List -->
        @forelse($comments as $comment)
        <div class="card mb-3" id="comment-{{ $comment->id }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="{{ $comment->user->profile_photo_url }}" alt="{{ $comment->user->name }}" class="rounded-circle me-2" width="40">
                    <div>
                        <a href="{{ route('profile.show', $comment->user) }}" class="fw-bold text-decoration-none">{{ $comment->user->name }}</a>
                        <div class="text-muted small">
                            <span>{{ $comment->user->comments->count() }} comments</span> · 
                            <span>Joined {{ $comment->user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-muted small">
                    #{{ $loop->iteration + 1 }} · {{ $comment->created_at->diffForHumans() }}
                </div>
            </div>
            <div class="card-body">
                <div class="comment-content">
                    {!! $comment->content !!}
                </div>
                
                <!-- Nested Replies -->
                @if($comment->replies->count() > 0)
                <div class="nested-replies mt-3">
                    @foreach($comment->replies as $reply)
                    <div class="card mb-2" id="comment-{{ $reply->id }}">
                        <div class="card-header d-flex justify-content-between align-items-center py-2">
                            <div class="d-flex align-items-center">
                                <img src="{{ $reply->user->profile_photo_url }}" alt="{{ $reply->user->name }}" class="rounded-circle me-2" width="30">
                                <div>
                                    <a href="{{ route('profile.show', $reply->user) }}" class="fw-bold text-decoration-none">{{ $reply->user->name }}</a>
                                </div>
                            </div>
                            <div class="text-muted small">
                                {{ $reply->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <div class="reply-content">
                                {!! $reply->content !!}
                            </div>
                        </div>
                        <div class="card-footer py-1 d-flex justify-content-between">
                            <div>
                                <!-- Like Button -->
                                <form action="{{ route('comments.like', $reply) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $reply->isLikedBy(auth()->user()) ? 'btn-primary' : 'btn-outline-primary' }}">
                                        <i class="bi bi-hand-thumbs-up"></i> 
                                        <span class="badge bg-secondary">{{ $reply->like_count }}</span>
                                    </button>
                                </form>
                            </div>
                            
                            <div>
                                <!-- Reply Button -->
                                <button class="btn btn-sm btn-outline-secondary reply-button" data-parent-id="{{ $comment->id }}">
                                    <i class="bi bi-reply"></i> Reply
                                </button>
                                
                                <!-- Edit/Delete Buttons (if owner) -->
                                @can('update', $reply)
                                <div class="btn-group ms-2">
                                    <button class="btn btn-sm btn-outline-secondary edit-comment-button" data-comment-id="{{ $reply->id }}" data-comment-content="{{ $reply->content }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('comments.destroy', $reply) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this reply?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="card-footer d-flex justify-content-between">
                <div>
                    <!-- Like Button -->
                    <form action="{{ route('comments.like', $comment) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $comment->isLikedBy(auth()->user()) ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-hand-thumbs-up"></i> 
                            <span class="badge bg-secondary">{{ $comment->like_count }}</span>
                        </button>
                    </form>
                </div>
                
                <div>
                    <!-- Quote Button -->
                    <button class="btn btn-sm btn-outline-secondary quote-button" data-comment-id="{{ $comment->id }}" data-comment-content="{{ $comment->content }}" data-user-name="{{ $comment->user->name }}">
                        <i class="bi bi-chat-quote"></i> Quote
                    </button>
                    
                    <!-- Reply Button -->
                    <button class="btn btn-sm btn-outline-secondary reply-button ms-2" data-parent-id="{{ $comment->id }}">
                        <i class="bi bi-reply"></i> Reply
                    </button>
                    
                    <!-- Edit/Delete Buttons (if owner) -->
                    @can('update', $comment)
                    <div class="btn-group ms-2">
                        <button class="btn btn-sm btn-outline-secondary edit-comment-button" data-comment-id="{{ $comment->id }}" data-comment-content="{{ $comment->content }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="alert alert-info">
            No comments yet. Be the first to comment!
        </div>
        @endforelse
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $comments->links() }}
        </div>
    </div>

    <!-- Reply Form -->
    @auth
    <div class="card" id="reply-form">
        <div class="card-header">
            <h4>Post a Reply</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('threads.comments.store', $thread) }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" id="parent_id" value="">
                
                <div class="mb-3">
                    <label for="content" class="form-label">Your Reply</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5" required>{{ old('content') }}</textarea>
                    @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <div id="reply-to-info" class="text-muted" style="display: none;">
                        Replying to: <span id="reply-to-name"></span>
                        <button type="button" class="btn btn-sm btn-link p-0 ms-2" id="cancel-reply">Cancel</button>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Post Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="alert alert-info">
        Please <a href="{{ route('login') }}">login</a> or <a href="{{ route('register') }}">register</a> to post a reply.
    </div>
    @endauth

    <!-- Related Threads -->
    @if($relatedThreads->count() > 0)
    <div class="related-threads mt-4">
        <h3>Related Threads</h3>
        <div class="list-group">
            @foreach($relatedThreads as $relatedThread)
            <a href="{{ route('threads.show', $relatedThread) }}" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $relatedThread->title }}</h5>
                    <small>{{ $relatedThread->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-1">{{ Str::limit(strip_tags($relatedThread->content), 100) }}</p>
                <small>By {{ $relatedThread->user->name }} · {{ $relatedThread->view_count }} views · {{ $relatedThread->allComments->count() }} replies</small>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Handle reply buttons
    document.querySelectorAll('.reply-button').forEach(button => {
        button.addEventListener('click', function() {
            const parentId = this.getAttribute('data-parent-id');
            const parentUser = this.closest('.card').querySelector('.fw-bold').textContent;
            
            document.getElementById('parent_id').value = parentId;
            document.getElementById('reply-to-name').textContent = parentUser;
            document.getElementById('reply-to-info').style.display = 'block';
            
            // Scroll to reply form
            document.getElementById('reply-form').scrollIntoView({ behavior: 'smooth' });
            document.getElementById('content').focus();
        });
    });
    
    // Handle cancel reply
    document.getElementById('cancel-reply').addEventListener('click', function() {
        document.getElementById('parent_id').value = '';
        document.getElementById('reply-to-info').style.display = 'none';
    });
    
    // Handle quote buttons
    document.querySelectorAll('.quote-button').forEach(button => {
        button.addEventListener('click', function() {
            const commentContent = this.getAttribute('data-comment-content');
            const userName = this.getAttribute('data-user-name');
            
            const quoteText = `<blockquote>
                <p><strong>${userName} wrote:</strong></p>
                ${commentContent}
            </blockquote>
            <p></p>`;
            
            document.getElementById('content').value = quoteText;
            
            // Scroll to reply form
            document.getElementById('reply-form').scrollIntoView({ behavior: 'smooth' });
            document.getElementById('content').focus();
        });
    });
    
    // Handle edit comment buttons
    document.querySelectorAll('.edit-comment-button').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            const commentContent = this.getAttribute('data-comment-content');
            
            // Create edit form
            const commentCard = this.closest('.card');
            const commentBody = commentCard.querySelector('.card-body');
            const originalContent = commentBody.innerHTML;
            
            const editForm = document.createElement('form');
            editForm.setAttribute('action', `/comments/${commentId}`);
            editForm.setAttribute('method', 'POST');
            editForm.classList.add('edit-comment-form');
            
            editForm.innerHTML = `
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="edit-content-${commentId}" class="form-label">Edit your comment</label>
                    <textarea class="form-control" id="edit-content-${commentId}" name="content" rows="5" required>${commentContent}</textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-secondary me-2 cancel-edit-button">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            `;
            
            commentBody.innerHTML = '';
            commentBody.appendChild(editForm);
            
            // Handle cancel edit
            commentBody.querySelector('.cancel-edit-button').addEventListener('click', function() {
                commentBody.innerHTML = originalContent;
            });
        });
    });
</script>
@endpush
