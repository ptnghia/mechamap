@extends('layouts.app')

@section('title', $documentation->meta_title ?: $documentation->title)
@section('meta_description', $documentation->meta_description ?: $documentation->excerpt)

@push('styles')
<style>
.documentation-content {
    line-height: 1.8;
}
.documentation-content h1,
.documentation-content h2,
.documentation-content h3,
.documentation-content h4,
.documentation-content h5,
.documentation-content h6 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 600;
}
.documentation-content h1 { font-size: 2rem; }
.documentation-content h2 { font-size: 1.75rem; }
.documentation-content h3 { font-size: 1.5rem; }
.documentation-content h4 { font-size: 1.25rem; }
.documentation-content h5 { font-size: 1.1rem; }
.documentation-content h6 { font-size: 1rem; }

.documentation-content pre {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 1rem;
    overflow-x: auto;
}
.documentation-content code {
    background: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}
.documentation-content pre code {
    background: none;
    padding: 0;
}
.documentation-content blockquote {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
    color: #6c757d;
}
.documentation-content table {
    width: 100%;
    margin: 1rem 0;
    border-collapse: collapse;
}
.documentation-content table th,
.documentation-content table td {
    border: 1px solid #dee2e6;
    padding: 0.75rem;
    text-align: left;
}
.documentation-content table th {
    background: #f8f9fa;
    font-weight: 600;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('docs.index') }}">Documentation</a></li>
            @if($documentation->category)
                <li class="breadcrumb-item">
                    <a href="{{ route('docs.category', $documentation->category->slug) }}">
                        {{ $documentation->category->name }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $documentation->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Document Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-{{ $documentation->content_type === 'api' ? 'success' : 'primary' }} mb-2">
                                {{ ucfirst($documentation->content_type) }}
                            </span>
                            <span class="badge bg-secondary mb-2 ms-1">
                                {{ ucfirst($documentation->difficulty_level) }}
                            </span>
                            @if(!$documentation->is_public)
                                <span class="badge bg-warning mb-2 ms-1">
                                    <i class="fas fa-lock"></i> Private
                                </span>
                            @endif
                        </div>
                        <div class="text-end">
                            <small class="text-muted">
                                <i class="fas fa-eye"></i> {{ number_format($documentation->view_count) }} views
                            </small>
                        </div>
                    </div>

                    <h1 class="h2 mb-3">{{ $documentation->title }}</h1>
                    
                    @if($documentation->excerpt)
                        <p class="lead text-muted mb-3">{{ $documentation->excerpt }}</p>
                    @endif

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @if($documentation->author)
                                <img src="{{ $documentation->author->avatar_url ?? '/images/default-avatar.png' }}" 
                                     alt="{{ $documentation->author->name }}" 
                                     class="rounded-circle me-2" 
                                     width="32" height="32">
                                <div>
                                    <small class="text-muted">By</small>
                                    <strong>{{ $documentation->author->name }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="text-end">
                            <small class="text-muted">
                                Published {{ $documentation->published_at ? $documentation->published_at->format('M d, Y') : 'Draft' }}
                            </small>
                            @if($documentation->updated_at && $documentation->updated_at != $documentation->created_at)
                                <br>
                                <small class="text-muted">
                                    Updated {{ $documentation->updated_at->format('M d, Y') }}
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Content -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="documentation-content">
                        {!! \Illuminate\Support\Str::markdown($documentation->content) !!}
                    </div>
                </div>
            </div>

            <!-- Tags -->
            @if($documentation->tags && count($documentation->tags) > 0)
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title">Tags</h6>
                        @foreach($documentation->tags as $tag)
                            <span class="badge bg-light text-dark me-1 mb-1">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Rating & Comments Section -->
            @auth
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title">Rate this documentation</h6>
                        <div class="d-flex align-items-center mb-3">
                            <div class="rating-stars me-3" data-rating="{{ $userRating->rating ?? 0 }}">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-warning" data-rating="{{ $i }}"></i>
                                @endfor
                            </div>
                            <span class="text-muted">
                                ({{ number_format($documentation->rating_average, 1) }}/5 from {{ $documentation->rating_count }} ratings)
                            </span>
                        </div>
                        @if($userRating)
                            <p class="text-success">
                                <i class="fas fa-check"></i> You rated this {{ $userRating->rating }}/5 stars
                            </p>
                        @endif
                    </div>
                </div>
            @endauth

            <!-- Comments -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Comments ({{ $comments->total() }})</h6>
                </div>
                <div class="card-body">
                    @auth
                        <form id="comment-form" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <textarea class="form-control" name="content" rows="3" 
                                          placeholder="Add your comment..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-comment"></i> Post Comment
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Please <a href="{{ route('login') }}">login</a> to post comments.
                        </div>
                    @endauth

                    @forelse($comments as $comment)
                        <div class="comment mb-3 pb-3 border-bottom">
                            <div class="d-flex">
                                <img src="{{ $comment->user->avatar_url ?? '/images/default-avatar.png' }}" 
                                     alt="{{ $comment->user->name }}" 
                                     class="rounded-circle me-3" 
                                     width="40" height="40">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $comment->user->name }}</strong>
                                            @if($comment->is_staff_response)
                                                <span class="badge bg-success ms-1">Staff</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-2">{{ $comment->content }}</p>
                                    @auth
                                        <button class="btn btn-sm btn-outline-primary reply-btn" 
                                                data-comment-id="{{ $comment->id }}">
                                            <i class="fas fa-reply"></i> Reply
                                        </button>
                                    @endauth
                                </div>
                            </div>

                            <!-- Replies -->
                            @if($comment->replies && $comment->replies->count() > 0)
                                <div class="ms-5 mt-3">
                                    @foreach($comment->replies as $reply)
                                        <div class="comment-reply mb-2 pb-2 border-bottom">
                                            <div class="d-flex">
                                                <img src="{{ $reply->user->avatar_url ?? '/images/default-avatar.png' }}" 
                                                     alt="{{ $reply->user->name }}" 
                                                     class="rounded-circle me-2" 
                                                     width="32" height="32">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <strong>{{ $reply->user->name }}</strong>
                                                            @if($reply->is_staff_response)
                                                                <span class="badge bg-success ms-1">Staff</span>
                                                            @endif
                                                        </div>
                                                        <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    <p class="mb-0">{{ $reply->content }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">
                            <i class="fas fa-comments"></i><br>
                            No comments yet. Be the first to comment!
                        </p>
                    @endforelse

                    {{ $comments->links() }}
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Table of Contents -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-list"></i> Table of Contents</h6>
                </div>
                <div class="card-body">
                    <div id="toc">
                        <!-- TOC will be generated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Related Documents -->
            @if($relatedDocs && $relatedDocs->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-link"></i> Related Documents</h6>
                    </div>
                    <div class="card-body">
                        @foreach($relatedDocs as $related)
                            <div class="mb-3">
                                <a href="{{ route('docs.show', $related->slug) }}" class="text-decoration-none">
                                    <h6 class="mb-1">{{ $related->title }}</h6>
                                </a>
                                <p class="small text-muted mb-1">{{ Str::limit($related->excerpt, 80) }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-eye"></i> {{ number_format($related->view_count) }} views
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-tools"></i> Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('docs.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Documentation
                        </a>
                        @if($documentation->category)
                            <a href="{{ route('docs.category', $documentation->category->slug) }}" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-folder"></i> View Category
                            </a>
                        @endif
                        <button class="btn btn-outline-info btn-sm" onclick="window.print()">
                            <i class="fas fa-print"></i> Print Document
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="copyToClipboard()">
                            <i class="fas fa-copy"></i> Copy URL
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Generate Table of Contents
document.addEventListener('DOMContentLoaded', function() {
    const content = document.querySelector('.documentation-content');
    const toc = document.getElementById('toc');
    const headings = content.querySelectorAll('h1, h2, h3, h4, h5, h6');
    
    if (headings.length > 0) {
        const tocList = document.createElement('ul');
        tocList.className = 'list-unstyled';
        
        headings.forEach((heading, index) => {
            const id = 'heading-' + index;
            heading.id = id;
            
            const li = document.createElement('li');
            li.className = 'mb-1';
            
            const link = document.createElement('a');
            link.href = '#' + id;
            link.textContent = heading.textContent;
            link.className = 'text-decoration-none small';
            
            // Add indentation based on heading level
            const level = parseInt(heading.tagName.charAt(1));
            if (level > 1) {
                link.style.paddingLeft = (level - 1) * 15 + 'px';
            }
            
            li.appendChild(link);
            tocList.appendChild(li);
        });
        
        toc.appendChild(tocList);
    } else {
        toc.innerHTML = '<p class="text-muted small">No headings found</p>';
    }
});

// Copy URL to clipboard
function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('URL copied to clipboard!');
    });
}

// Rating functionality
@auth
document.querySelectorAll('.rating-stars i').forEach(star => {
    star.addEventListener('click', function() {
        const rating = this.dataset.rating;
        
        fetch('{{ route("docs.rate", $documentation->slug) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ rating: rating })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    });
});

// Comment form
document.getElementById('comment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("docs.comment", $documentation->slug) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
});
@endauth
</script>
@endpush
