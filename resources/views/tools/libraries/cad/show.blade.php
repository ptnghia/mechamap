@extends('layouts.app')

@section('title', $cadFile->title . ' - CAD Library')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- CAD File Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="h3 mb-2">{{ $cadFile->title }}</h1>
                            <div class="text-muted mb-2">
                                <i class="fas fa-user"></i> {{ $cadFile->user->name ?? 'Unknown' }}
                                <span class="mx-2">•</span>
                                <i class="fas fa-calendar"></i> {{ $cadFile->created_at->format('M d, Y') }}
                                <span class="mx-2">•</span>
                                <i class="fas fa-eye"></i> {{ number_format($cadFile->view_count) }} views
                                <span class="mx-2">•</span>
                                <i class="fas fa-download"></i> {{ number_format($cadFile->download_count) }} downloads
                            </div>

                            <!-- Rating -->
                            @if($cadFile->average_rating)
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $cadFile->average_rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-2">{{ number_format($cadFile->average_rating, 1) }} ({{ $cadFile->ratings_count ?? 0 }} reviews)</span>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="btn-group">
                            @auth
                                @if($cadFile->canBeDownloaded())
                                    <a href="{{ route('tools.cad-library') }}" class="btn btn-primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                @else
                                    <button class="btn btn-secondary" disabled>
                                        <i class="fas fa-clock"></i> Pending Review
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt"></i> Login to Download
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="mb-3">
                        @switch($cadFile->status)
                            @case('approved')
                                <span class="badge bg-success">Approved</span>
                                @break
                            @case('pending_review')
                                <span class="badge bg-warning">Pending Review</span>
                                @break
                            @case('rejected')
                                <span class="badge bg-danger">Rejected</span>
                                @break
                            @default
                                <span class="badge bg-secondary">{{ ucfirst($cadFile->status) }}</span>
                        @endswitch

                        @if($cadFile->license_type)
                            <span class="badge bg-info ms-2">{{ ucfirst($cadFile->license_type) }} License</span>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p class="text-muted">{{ $cadFile->description }}</p>
                    </div>

                    <!-- Tags -->
                    @if($cadFile->tags)
                        <div class="mb-3">
                            <h6>Tags</h6>
                            @foreach($cadFile->tags as $tag)
                                <span class="badge bg-light text-dark me-1">#{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Preview Image -->
            @if($cadFile->preview_image)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Preview</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ Storage::url($cadFile->preview_image) }}"
                             alt="{{ $cadFile->title }}"
                             class="img-fluid rounded shadow-sm"
                             style="max-height: 400px;">
                    </div>
                </div>
            @endif

            <!-- Technical Specifications -->
            @if($cadFile->technical_specs && count($cadFile->technical_specs) > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Technical Specifications</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($cadFile->technical_specs as $key => $value)
                                <div class="col-md-6 mb-2">
                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Rating & Review Section -->
            @auth
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Rate this CAD File</h5>
                    </div>
                    <div class="card-body">
                        <form action="#" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div class="rating-input">
                                    @for($i = 1; $i <= 5; $i++)
                                        <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" class="d-none">
                                        <label for="star{{ $i }}" class="star-label">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="review" class="form-label">Review (Optional)</label>
                                <textarea name="review" id="review" class="form-control" rows="3"
                                         placeholder="Share your thoughts about this CAD file..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit Rating</button>
                        </form>
                    </div>
                </div>
            @endauth

            <!-- Comments Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Comments ({{ $cadFile->comments ? $cadFile->comments->count() : 0 }})</h5>
                </div>
                <div class="card-body">
                    @auth
                        <!-- Add Comment Form -->
                        {{-- <form action="{{ route('cad.library.comment', $cadFile) }}" method="POST" class="mb-4"> --}}
                        <form action="#" method="POST" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <textarea name="comment" class="form-control" rows="3"
                                         placeholder="Add a comment..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </form>
                    @else
                        <p class="text-muted mb-4">
                            <a href="{{ route('login') }}">Login</a> to post a comment.
                        </p>
                    @endauth

                    <!-- Comments List -->
                    @forelse($cadFile->comments ?? [] as $comment)
                        <div class="comment mb-3 pb-3 border-bottom">
                            <div class="d-flex">
                                <img src="{{ $comment->user->avatar ?? '/images/default-avatar.png' }}"
                                     alt="{{ $comment->user->name }}"
                                     class="rounded-circle me-3"
                                     width="40" height="40">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $comment->user->name }}</strong>
                                            <small class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    <p class="mb-0 mt-1">{{ $comment->content }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No comments yet. Be the first to comment!</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- File Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">File Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>File Type:</strong></td>
                            <td>{{ strtoupper($cadFile->file_type) }}</td>
                        </tr>
                        <tr>
                            <td><strong>File Size:</strong></td>
                            <td>{{ number_format($cadFile->file_size / 1024 / 1024, 2) }} MB</td>
                        </tr>
                        <tr>
                            <td><strong>Software:</strong></td>
                            <td>{{ $cadFile->software_used }}</td>
                        </tr>
                        <tr>
                            <td><strong>Category:</strong></td>
                            <td>{{ $cadFile->category->name ?? 'Uncategorized' }}</td>
                        </tr>
                        <tr>
                            <td><strong>License:</strong></td>
                            <td>{{ ucfirst($cadFile->license_type) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Uploaded:</strong></td>
                            <td>{{ $cadFile->created_at->format('M d, Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Related Files -->
            @if($relatedFiles->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Related Files</h5>
                    </div>
                    <div class="card-body">
                        @foreach($relatedFiles as $related)
                            <div class="d-flex mb-3">
                                @if($related->preview_image)
                                    <img src="{{ Storage::url($related->preview_image) }}"
                                         alt="{{ $related->title }}"
                                         class="rounded me-3"
                                         width="60" height="60"
                                         style="object-fit: cover;">
                                @else
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-cube text-muted"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('tools.cad-library.show', $related) }}" class="text-decoration-none">
                                            {{ Str::limit($related->title, 40) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        {{ $related->user->name ?? 'Unknown' }} •
                                        {{ $related->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.rating-input .star-label {
    color: #ddd;
    font-size: 1.5rem;
    cursor: pointer;
    transition: color 0.2s;
}

.rating-input .star-label:hover,
.rating-input input:checked ~ .star-label,
.rating-input input:checked + .star-label {
    color: #ffc107;
}

.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating-input input:checked ~ .star-label {
    color: #ffc107;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Rating system
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const starLabels = document.querySelectorAll('.star-label');

    starLabels.forEach((label, index) => {
        label.addEventListener('mouseenter', function() {
            for (let i = 0; i <= index; i++) {
                starLabels[starLabels.length - 1 - i].style.color = '#ffc107';
            }
        });

        label.addEventListener('mouseleave', function() {
            starLabels.forEach(star => {
                star.style.color = '#ddd';
            });
            // Restore checked state
            const checkedInput = document.querySelector('input[name="rating"]:checked');
            if (checkedInput) {
                const checkedIndex = Array.from(ratingInputs).indexOf(checkedInput);
                for (let i = 0; i <= checkedIndex; i++) {
                    starLabels[starLabels.length - 1 - i].style.color = '#ffc107';
                }
            }
        });
    });
});
</script>
@endsection
