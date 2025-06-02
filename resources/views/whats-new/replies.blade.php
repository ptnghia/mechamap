@extends('layouts.app')

@section('title', 'Looking for Replies - MechaMap')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">What's New</h1>

                <a href="{{ route('threads.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Create Thread
                </a>
            </div>

            <!-- Navigation Tabs -->
            <div class="whats-new-tabs mb-4">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new') }}">New Posts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.popular') }}">Popular</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.threads') }}">New Threads</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.media') }}">New Media</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('whats-new.replies') }}">Looking for Replies</a>
                    </li>
                </ul>
            </div>

            <!-- Pagination Top -->
            <div class="pagination-container mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="pagination-info">
                        <span>Page {{ $page }} of {{ $totalPages }}</span>
                    </div>

                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Previous Page -->
                            <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $prevPageUrl }}" aria-label="Previous">
                                    <span aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
                                </a>
                            </li>

                            <!-- First Page -->
                            @if($page > 3)
                            <li class="page-item">
                                <a class="page-link" href="{{ route('whats-new.replies', ['page' => 1]) }}">1</a>
                            </li>
                            @endif

                            <!-- Ellipsis for skipped pages -->
                            @if($page > 4)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            @endif

                            <!-- Pages before current -->
                            @for($i = max(1, $page - 2); $i < $page; $i++) <li class="page-item">
                                <a class="page-link" href="{{ route('whats-new.replies', ['page' => $i]) }}">{{ $i
                                    }}</a>
                                </li>
                                @endfor

                                <!-- Current Page -->
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>

                                <!-- Pages after current -->
                                @for($i = $page + 1; $i <= min($totalPages, $page + 2); $i++) <li class="page-item">
                                    <a class="page-link" href="{{ route('whats-new.replies', ['page' => $i]) }}">{{ $i
                                        }}</a>
                                    </li>
                                    @endfor

                                    <!-- Ellipsis for skipped pages -->
                                    @if($page < $totalPages - 3) <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                        </li>
                                        @endif

                                        <!-- Last Page -->
                                        @if($page < $totalPages - 2) <li class="page-item">
                                            <a class="page-link"
                                                href="{{ route('whats-new.replies', ['page' => $totalPages]) }}">{{
                                                $totalPages }}</a>
                                            </li>
                                            @endif

                                            <!-- Next Page -->
                                            <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $nextPageUrl }}" aria-label="Next">
                                                    <span aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                                                </a>
                                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- Threads List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Threads Looking for Replies</h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($threads as $thread)
                    <div class="list-group-item thread-item">
                        <div class="row">
                            <!-- Nội dung chính - responsive columns dựa trên việc có featured_image hay không -->
                            <div class="{{ $thread->featured_image ? 'col-md-9' : 'col-12' }}">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3 d-none d-sm-block">
                                        <img src="{{ get_avatar_url($thread->user) }}" alt="{{ $thread->user->name }}"
                                            class="avatar avatar-md">
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="thread-title">
                                                <a href="{{ route('threads.show', $thread->slug) }}">{{ $thread->title
                                                    }}</a>
                                                @if($thread->is_sticky)
                                                <span class="badge bg-primary ms-1">Sticky</span>
                                                @endif
                                                @if($thread->is_locked)
                                                <span class="badge bg-danger ms-1">Locked</span>
                                                @endif
                                            </div>
                                            <small class="text-muted d-md-none">{{ $thread->created_at->diffForHumans()
                                                }}</small>
                                        </div>

                                        <!-- Thread Excerpt -->
                                        <div class="thread-excerpt mb-2">
                                            <p class="text-muted small">{{ Str::limit(strip_tags($thread->content), 150)
                                                }}</p>
                                        </div>

                                        <!-- Project Details -->
                                        @if($thread->location || $thread->usage || $thread->floors || $thread->status)
                                        <div class="project-details mb-2 small">
                                            @if($thread->location)
                                            <span class="badge bg-light text-dark me-2">{{ $thread->location }}</span>
                                            @endif

                                            @if($thread->usage)
                                            <span class="badge bg-light text-dark me-2">{{ $thread->usage }}</span>
                                            @endif

                                            @if($thread->floors)
                                            <span class="badge bg-light text-dark me-2">{{ $thread->floors }}
                                                tầng</span>
                                            @endif

                                            @if($thread->status)
                                            <span class="badge bg-light text-dark me-2">{{ $thread->status }}</span>
                                            @endif
                                        </div>
                                        @endif

                                        <div class="d-flex justify-content-between align-items-center mt-2 thread-meta">
                                            <div>
                                                <span class="me-3"><i class="bi bi-person"></i> {{ $thread->user->name
                                                    }}</span>
                                                <span class="me-3"><i class="bi bi-eye"></i> {{ $thread->view_count }}
                                                    lượt xem</span>
                                                <span><i class="bi bi-chat"></i> {{ $thread->comment_count }} phản
                                                    hồi</span>
                                            </div>

                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @if($thread->category)
                                                    <a href="{{ route('categories.show', $thread->category->slug) }}"
                                                        class="badge bg-secondary text-decoration-none">{{
                                                        $thread->category->name }}</a>
                                                    @endif

                                                    @if($thread->forum)
                                                    <a href="{{ route('forums.show', $thread->forum->slug) }}"
                                                        class="badge bg-info text-decoration-none">{{
                                                        $thread->forum->name }}</a>
                                                    @endif
                                                </div>

                                                <a href="{{ route('threads.show', [$thread->slug, 'reply' => 'true']) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bi bi-reply"></i> Reply Now
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hình ảnh - chỉ hiển thị khi có featured_image -->
                            @if($thread->featured_image)
                            <div class="col-md-3 d-none d-md-block">
                                <div class="thread-image">
                                    <img src="{{ $thread->featured_image }}" alt="{{ $thread->title }}"
                                        class="img-fluid rounded"
                                        style="max-height: 100px; width: 100%; object-fit: cover;">
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination Bottom -->
            <div class="pagination-container mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="pagination-info">
                        <span>Page {{ $page }} of {{ $totalPages }}</span>
                    </div>

                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Previous Page -->
                            <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $prevPageUrl }}" aria-label="Previous">
                                    <span aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
                                </a>
                            </li>

                            <!-- First Page -->
                            @if($page > 3)
                            <li class="page-item">
                                <a class="page-link" href="{{ route('whats-new.replies', ['page' => 1]) }}">1</a>
                            </li>
                            @endif

                            <!-- Ellipsis for skipped pages -->
                            @if($page > 4)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            @endif

                            <!-- Pages before current -->
                            @for($i = max(1, $page - 2); $i < $page; $i++) <li class="page-item">
                                <a class="page-link" href="{{ route('whats-new.replies', ['page' => $i]) }}">{{ $i
                                    }}</a>
                                </li>
                                @endfor

                                <!-- Current Page -->
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>

                                <!-- Pages after current -->
                                @for($i = $page + 1; $i <= min($totalPages, $page + 2); $i++) <li class="page-item">
                                    <a class="page-link" href="{{ route('whats-new.replies', ['page' => $i]) }}">{{ $i
                                        }}</a>
                                    </li>
                                    @endfor

                                    <!-- Ellipsis for skipped pages -->
                                    @if($page < $totalPages - 3) <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                        </li>
                                        @endif

                                        <!-- Last Page -->
                                        @if($page < $totalPages - 2) <li class="page-item">
                                            <a class="page-link"
                                                href="{{ route('whats-new.replies', ['page' => $totalPages]) }}">{{
                                                $totalPages }}</a>
                                            </li>
                                            @endif

                                            <!-- Next Page -->
                                            <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $nextPageUrl }}" aria-label="Next">
                                                    <span aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                                                </a>
                                            </li>
                        </ul>
                    </nav>

                    <div class="pagination-goto">
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control" id="pageInput" min="1" max="{{ $totalPages }}"
                                value="{{ $page }}" placeholder="Page">
                            <button class="btn btn-primary" type="button" id="goToPageBtn">Go</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const goToPageBtn = document.getElementById('goToPageBtn');
        const pageInput = document.getElementById('pageInput');

        goToPageBtn.addEventListener('click', function() {
            const page = parseInt(pageInput.value);
            if (page >= 1 && page <= {{ $totalPages }}) {
                window.location.href = '{{ route("whats-new.replies") }}?page=' + page;
            }
        });

        pageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                goToPageBtn.click();
            }
        });
    });
</script>
@endpush

<style>
    /* Thread Item Styles */
    .thread-item {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .thread-item:hover {
        border-left-color: var(--bs-primary);
        background-color: rgba(0, 0, 0, 0.01);
    }

    .avatar.avatar-md {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 50%;
    }

    .thread-title a {
        color: var(--bs-dark);
        text-decoration: none;
        font-weight: 500;
    }

    .thread-title a:hover {
        color: var(--bs-primary);
    }

    .thread-meta {
        font-size: 0.85rem;
        color: var(--bs-secondary);
    }

    /* Tab Navigation Styles */
    .whats-new-tabs .nav-link {
        border-radius: 0;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }

    .whats-new-tabs .nav-link.active {
        font-weight: bold;
        background-color: var(--bs-primary);
    }

    .whats-new-tabs .nav-link:hover:not(.active) {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }

    /* Pagination Styles */
    .pagination-container {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .pagination-info {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        color: var(--bs-primary);
        border-color: #dee2e6;
        background-color: #fff;
        transition: all 0.2s ease;
    }

    .pagination .page-item.active .page-link {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
        color: #fff;
        font-weight: 500;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    .pagination .page-link:hover:not(.active) {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        border-color: rgba(var(--bs-primary-rgb), 0.2);
        color: var(--bs-primary);
        z-index: 2;
    }

    .pagination-goto .input-group {
        width: 120px;
    }

    .pagination-goto .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        text-align: center;
    }

    .pagination-goto .btn {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    /* Thread Excerpt Styles */
    .thread-excerpt {
        color: #6c757d;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    @media (max-width: 767.98px) {
        .pagination-container .d-flex {
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .pagination-info,
        .pagination-goto {
            margin-bottom: 10px;
        }
    }
</style>
@endsection