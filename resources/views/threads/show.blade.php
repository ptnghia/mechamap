@extends('layouts.app')

@section('title', $thread->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/thread-detail.css') }}">
@endpush

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
    <div class="thread-header">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h1 class="thread-title">{{ $thread->title }}</h1>

            <div class="thread-actions">
                <a href="#comment-{{ $comments->count() > 0 ? $comments->last()->id : '' }}" class="btn-jump">
                    <i class="bi bi-arrow-right"></i> Jump to Latest
                </a>

                @php
                    $isFollowed = Auth::check() && $thread->isFollowedBy(Auth::user());
                @endphp
                <form action="{{ route('threads.follow.toggle', $thread) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-follow">
                        <i class="bi {{ $isFollowed ? 'bi-bell-fill' : 'bi-bell' }}"></i> {{ $isFollowed ? 'Following' : 'Follow' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="thread-meta">
            <div class="thread-meta-item">
                <i class="bi bi-eye"></i> {{ number_format($thread->view_count) }} views
            </div>
            <div class="thread-meta-item">
                <i class="bi bi-chat"></i> {{ number_format($thread->allComments->count()) }} replies
            </div>
            <div class="thread-meta-item">
                <i class="bi bi-people"></i> {{ number_format($thread->participant_count) }} participants
            </div>
            <div class="thread-meta-item">
                <i class="bi bi-clock"></i> last post by
                <a href="{{ route('profile.show', $thread->lastCommenter) }}" class="ms-1 fw-semibold">
                    {{ $thread->lastCommenter->name ?? $thread->user->name }}
                </a>
                <span class="ms-1">{{ $thread->lastCommentAt ? $thread->lastCommentAt->diffForHumans() : $thread->created_at->diffForHumans() }}</span>
            </div>
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

            <!-- Poll Section -->
            @include('threads.partials.poll')

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
                    <button type="submit" class="btn btn-sm {{ Auth::check() && $isLiked ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="bi bi-hand-thumbs-up"></i>
                        Like
                        <span class="badge bg-secondary">{{ $thread->likes->count() }}</span>
                    </button>
                </form>

                <!-- Save Button -->
                <form action="{{ route('threads.save', $thread) }}" method="POST" class="d-inline ms-2">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ Auth::check() && $isSaved ? 'btn-success' : 'btn-outline-success' }}">
                        <i class="bi {{ Auth::check() && $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
                        {{ Auth::check() && $isSaved ? 'Saved' : 'Save' }}
                    </button>
                </form>

                <!-- Follow Button -->
                @php
                    $isFollowed = Auth::check() && $thread->isFollowedBy(Auth::user());
                @endphp
                <form action="{{ route('threads.follow.toggle', $thread) }}" method="POST" class="d-inline ms-2">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $isFollowed ? 'btn-info' : 'btn-outline-info' }}">
                        <i class="bi {{ $isFollowed ? 'bi-bell-fill' : 'bi-bell' }}"></i>
                        {{ $isFollowed ? 'Following' : 'Follow' }}
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

                @if($comment->has_media && $comment->attachments->count() > 0)
                <div class="comment-attachments mt-3">
                    <div class="row g-2">
                        @foreach($comment->attachments as $attachment)
                            <div class="col-md-3 col-sm-4 col-6">
                                <a href="{{ asset('storage/' . $attachment->file_path) }}" class="d-block" data-lightbox="comment-{{ $comment->id }}-images" data-title="{{ $attachment->file_name }}">
                                    <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->file_name }}" class="img-fluid rounded">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

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

                            @if($reply->has_media && $reply->attachments->count() > 0)
                            <div class="reply-attachments mt-2">
                                <div class="row g-2">
                                    @foreach($reply->attachments as $attachment)
                                        <div class="col-md-3 col-sm-4 col-6">
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" class="d-block" data-lightbox="reply-{{ $reply->id }}-images" data-title="{{ $attachment->file_name }}">
                                                <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->file_name }}" class="img-fluid rounded">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer py-1 d-flex justify-content-between">
                            <div>
                                <!-- Like Button -->
                                <form action="{{ route('comments.like', $reply) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ Auth::check() && $reply->isLikedBy(auth()->user()) ? 'btn-primary' : 'btn-outline-primary' }}">
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
                        <button type="submit" class="btn btn-sm {{ Auth::check() && $comment->isLikedBy(auth()->user()) ? 'btn-primary' : 'btn-outline-primary' }}">
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
            <form action="{{ route('threads.comments.store', $thread) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="parent_id" id="parent_id" value="">

                <div class="mb-3">
                    <label for="content" class="form-label">Your Reply</label>
                    <div class="editor-container">
                        <div class="editor-toolbar">
                            <button type="button" class="btn btn-sm" onclick="formatText('bold')"><i class="bi bi-type-bold"></i></button>
                            <button type="button" class="btn btn-sm" onclick="formatText('italic')"><i class="bi bi-type-italic"></i></button>
                            <button type="button" class="btn btn-sm" onclick="formatText('underline')"><i class="bi bi-type-underline"></i></button>
                            <button type="button" class="btn btn-sm" onclick="formatText('strikethrough')"><i class="bi bi-type-strikethrough"></i></button>
                            <div class="vr mx-1"></div>
                            <button type="button" class="btn btn-sm" onclick="formatText('link')"><i class="bi bi-link"></i></button>
                            <button type="button" class="btn btn-sm" id="image-upload-btn"><i class="bi bi-image"></i></button>
                            <div class="vr mx-1"></div>
                            <button type="button" class="btn btn-sm" onclick="formatText('quote')"><i class="bi bi-chat-quote"></i></button>
                            <button type="button" class="btn btn-sm" onclick="formatText('code')"><i class="bi bi-code"></i></button>
                            <div class="vr mx-1"></div>
                            <button type="button" class="btn btn-sm" onclick="formatText('ul')"><i class="bi bi-list-ul"></i></button>
                            <button type="button" class="btn btn-sm" onclick="formatText('ol')"><i class="bi bi-list-ol"></i></button>
                        </div>
                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5" required>{{ old('content') }}</textarea>
                        @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex align-items-center">
                        <input type="file" class="form-control d-none" id="images" name="images[]" multiple accept="image/*">
                        <div id="image-preview-container" class="d-flex flex-wrap gap-2 mt-2"></div>
                    </div>
                    @error('images.*')
                        <div class="text-danger small mt-1">{{ $message }}</div>
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
    // Text editor formatting functions
    function formatText(command) {
        const textarea = document.getElementById('content');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selectedText = textarea.value.substring(start, end);
        let replacement = '';

        switch(command) {
            case 'bold':
                replacement = `<strong>${selectedText}</strong>`;
                break;
            case 'italic':
                replacement = `<em>${selectedText}</em>`;
                break;
            case 'underline':
                replacement = `<u>${selectedText}</u>`;
                break;
            case 'strikethrough':
                replacement = `<s>${selectedText}</s>`;
                break;
            case 'link':
                const url = prompt('Enter URL:', 'http://');
                if (url) {
                    replacement = `<a href="${url}">${selectedText || url}</a>`;
                } else {
                    return;
                }
                break;
            case 'quote':
                replacement = `<blockquote>${selectedText}</blockquote>`;
                break;
            case 'code':
                replacement = `<pre><code>${selectedText}</code></pre>`;
                break;
            case 'ul':
                replacement = `<ul>\n<li>${selectedText}</li>\n</ul>`;
                break;
            case 'ol':
                replacement = `<ol>\n<li>${selectedText}</li>\n</ol>`;
                break;
        }

        textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
        textarea.focus();
        textarea.selectionStart = start + replacement.length;
        textarea.selectionEnd = start + replacement.length;
    }

    // Image upload handling
    document.getElementById('image-upload-btn').addEventListener('click', function() {
        document.getElementById('images').click();
    });

    document.getElementById('images').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('image-preview-container');
        previewContainer.innerHTML = '';

        if (this.files.length > 0) {
            Array.from(this.files).forEach((file, index) => {
                if (!file.type.match('image.*')) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'image-preview position-relative';
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="height: 80px; width: 80px; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-image" data-index="${index}">
                            <i class="bi bi-x"></i>
                        </button>
                    `;
                    previewContainer.appendChild(preview);

                    // Handle remove image button
                    preview.querySelector('.remove-image').addEventListener('click', function() {
                        preview.remove();
                        // Note: We can't actually remove files from a FileList, but we can hide the preview
                    });
                };
                reader.readAsDataURL(file);
            });
        }
    });

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
            editForm.setAttribute('enctype', 'multipart/form-data');
            editForm.classList.add('edit-comment-form');

            editForm.innerHTML = `
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="edit-content-${commentId}" class="form-label">Edit your comment</label>
                    <div class="editor-container">
                        <div class="editor-toolbar">
                            <button type="button" class="btn btn-sm" onclick="formatEditText('bold', 'edit-content-${commentId}')"><i class="bi bi-type-bold"></i></button>
                            <button type="button" class="btn btn-sm" onclick="formatEditText('italic', 'edit-content-${commentId}')"><i class="bi bi-type-italic"></i></button>
                            <button type="button" class="btn btn-sm" onclick="formatEditText('underline', 'edit-content-${commentId}')"><i class="bi bi-type-underline"></i></button>
                            <button type="button" class="btn btn-sm" onclick="formatEditText('strikethrough', 'edit-content-${commentId}')"><i class="bi bi-type-strikethrough"></i></button>
                            <div class="vr mx-1"></div>
                            <button type="button" class="btn btn-sm" onclick="formatEditText('link', 'edit-content-${commentId}')"><i class="bi bi-link"></i></button>
                            <button type="button" class="btn btn-sm edit-image-upload-btn" data-target="edit-images-${commentId}"><i class="bi bi-image"></i></button>
                        </div>
                        <textarea class="form-control" id="edit-content-${commentId}" name="content" rows="5" required>${commentContent}</textarea>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="file" class="form-control d-none" id="edit-images-${commentId}" name="images[]" multiple accept="image/*">
                    <div id="edit-image-preview-${commentId}" class="d-flex flex-wrap gap-2 mt-2"></div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-secondary me-2 cancel-edit-button">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            `;

            commentBody.innerHTML = '';
            commentBody.appendChild(editForm);

            // Setup image upload for edit form
            const editImageBtn = commentBody.querySelector('.edit-image-upload-btn');
            const editImageInput = commentBody.querySelector(`#edit-images-${commentId}`);

            editImageBtn.addEventListener('click', function() {
                editImageInput.click();
            });

            editImageInput.addEventListener('change', function(e) {
                const previewContainer = commentBody.querySelector(`#edit-image-preview-${commentId}`);
                previewContainer.innerHTML = '';

                if (this.files.length > 0) {
                    Array.from(this.files).forEach((file, index) => {
                        if (!file.type.match('image.*')) return;

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const preview = document.createElement('div');
                            preview.className = 'image-preview position-relative';
                            preview.innerHTML = `
                                <img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="height: 80px; width: 80px; object-fit: cover;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-image" data-index="${index}">
                                    <i class="bi bi-x"></i>
                                </button>
                            `;
                            previewContainer.appendChild(preview);

                            // Handle remove image button
                            preview.querySelector('.remove-image').addEventListener('click', function() {
                                preview.remove();
                            });
                        };
                        reader.readAsDataURL(file);
                    });
                }
            });

            // Handle cancel edit
            commentBody.querySelector('.cancel-edit-button').addEventListener('click', function() {
                commentBody.innerHTML = originalContent;
            });
        });
    });

    // Format text in edit form
    function formatEditText(command, textareaId) {
        const textarea = document.getElementById(textareaId);
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selectedText = textarea.value.substring(start, end);
        let replacement = '';

        switch(command) {
            case 'bold':
                replacement = `<strong>${selectedText}</strong>`;
                break;
            case 'italic':
                replacement = `<em>${selectedText}</em>`;
                break;
            case 'underline':
                replacement = `<u>${selectedText}</u>`;
                break;
            case 'strikethrough':
                replacement = `<s>${selectedText}</s>`;
                break;
            case 'link':
                const url = prompt('Enter URL:', 'http://');
                if (url) {
                    replacement = `<a href="${url}">${selectedText || url}</a>`;
                } else {
                    return;
                }
                break;
        }

        textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
        textarea.focus();
        textarea.selectionStart = start + replacement.length;
        textarea.selectionEnd = start + replacement.length;
    }
</script>
@endpush

@push('styles')
<style>
    .editor-container {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        overflow: hidden;
    }

    .editor-toolbar {
        display: flex;
        padding: 0.5rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #ced4da;
    }

    .editor-toolbar .btn {
        color: #6c757d;
        padding: 0.25rem 0.5rem;
        margin-right: 0.25rem;
    }

    .editor-toolbar .btn:hover {
        color: #212529;
        background-color: #e9ecef;
    }

    .editor-container textarea {
        border: none;
        border-radius: 0;
    }

    .editor-container textarea:focus {
        box-shadow: none;
    }

    .image-preview {
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .image-preview .remove-image {
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
    }

    /* Dark mode styles */
    .dark .editor-toolbar {
        background-color: #2d3748;
        border-color: #4a5568;
    }

    .dark .editor-toolbar .btn {
        color: #cbd5e0;
    }

    .dark .editor-toolbar .btn:hover {
        color: #fff;
        background-color: #4a5568;
    }

    .dark .editor-container {
        border-color: #4a5568;
    }

    /* Comment attachment styles */
    .comment-attachments img, .reply-attachments img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        transition: transform 0.2s;
    }

    .comment-attachments a:hover img, .reply-attachments a:hover img {
        transform: scale(1.03);
    }
</style>
@endpush
