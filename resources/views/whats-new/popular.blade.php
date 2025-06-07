@extends('layouts.app')

@section('title', '{{ __("messages.popular") }} Content - MechaMap')

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
                        <a class="nav-link" href="{{ route('whats-new') }}">{{ __('messages.new_posts') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('whats-new.popular') }}">{{ __('messages.popular')
                            }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.threads') }}">{{ __('messages.new_threads') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.media') }}">{{ __('messages.new_media') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.replies') }}">{{
                            __('messages.looking_for_replies') }}</a>
                    </li>
                </ul>
            </div>

            <!-- Timeframe Filter -->
            <div class="timeframe-filter mb-4">
                <div class="btn-group" role="group">
                    <a href="{{ route('whats-new.popular', ['timeframe' => 'day']) }}"
                        class="btn btn-outline-secondary {{ $timeframe == 'day' ? 'active' : '' }}">Today</a>
                    <a href="{{ route('whats-new.popular', ['timeframe' => 'week']) }}"
                        class="btn btn-outline-secondary {{ $timeframe == 'week' ? 'active' : '' }}">This Week</a>
                    <a href="{{ route('whats-new.popular', ['timeframe' => 'month']) }}"
                        class="btn btn-outline-secondary {{ $timeframe == 'month' ? 'active' : '' }}">This Month</a>
                    <a href="{{ route('whats-new.popular', ['timeframe' => 'year']) }}"
                        class="btn btn-outline-secondary {{ $timeframe == 'year' ? 'active' : '' }}">This Year</a>
                    <a href="{{ route('whats-new.popular', ['timeframe' => 'all']) }}"
                        class="btn btn-outline-secondary {{ $timeframe == 'all' ? 'active' : '' }}">All Time</a>
                </div>
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
                                <a class="page-link"
                                    href="{{ route('whats-new.popular', ['page' => 1, 'timeframe' => $timeframe]) }}">1</a>
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
                                <a class="page-link"
                                    href="{{ route('whats-new.popular', ['page' => $i, 'timeframe' => $timeframe]) }}">{{
                                    $i }}</a>
                                </li>
                                @endfor

                                <!-- Current Page -->
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>

                                <!-- Pages after current -->
                                @for($i = $page + 1; $i <= min($totalPages, $page + 2); $i++) <li class="page-item">
                                    <a class="page-link"
                                        href="{{ route('whats-new.popular', ['page' => $i, 'timeframe' => $timeframe]) }}">{{
                                        $i }}</a>
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
                                                href="{{ route('whats-new.popular', ['page' => $totalPages, 'timeframe' => $timeframe]) }}">{{
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
                    <h5 class="mb-0">{{ __('messages.popular_threads') }}</h5>
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
                                                <span class="badge bg-primary ms-1">{{
                                                    __('messages.thread_status.sticky') }}</span>
                                                @endif
                                                @if($thread->is_locked)
                                                <span class="badge bg-danger ms-1">{{
                                                    __('messages.thread_status.locked') }}</span>
                                                @endif
                                            </div>
                                            <small class="text-muted d-md-none">{{ $thread->created_at->diffForHumans()
                                                }}</small>
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
                                                <span class="d-none d-md-inline text-muted">{{
                                                    $thread->created_at->diffForHumans() }}</span>
                                            </div>

                                            <div>
                                                @if($thread->category)
                                                <a href="{{ route('categories.show', $thread->category->slug) }}"
                                                    class="badge bg-secondary text-decoration-none">{{
                                                    $thread->category->name }}</a>
                                                @endif

                                                @if($thread->forum)
                                                <a href="{{ route('forums.show', $thread->forum->slug) }}"
                                                    class="badge bg-info text-decoration-none">{{ $thread->forum->name
                                                    }}</a>
                                                @endif
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
                                <a class="page-link"
                                    href="{{ route('whats-new.popular', ['page' => 1, 'timeframe' => $timeframe]) }}">1</a>
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
                                <a class="page-link"
                                    href="{{ route('whats-new.popular', ['page' => $i, 'timeframe' => $timeframe]) }}">{{
                                    $i }}</a>
                                </li>
                                @endfor

                                <!-- Current Page -->
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>

                                <!-- Pages after current -->
                                @for($i = $page + 1; $i <= min($totalPages, $page + 2); $i++) <li class="page-item">
                                    <a class="page-link"
                                        href="{{ route('whats-new.popular', ['page' => $i, 'timeframe' => $timeframe]) }}">{{
                                        $i }}</a>
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
                                                href="{{ route('whats-new.popular', ['page' => $totalPages, 'timeframe' => $timeframe]) }}">{{
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
                window.location.href = '{{ route("whats-new.popular") }}?page=' + page + '&timeframe={{ $timeframe }}';
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
    /* Timeframe Filter Styles */
    .timeframe-filter {
        margin-bottom: 1.5rem;
    }

    .timeframe-filter .btn-group {
        width: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        overflow: hidden;
    }

    .timeframe-filter .btn {
        flex: 1;
        border-radius: 0;
        transition: all 0.2s ease;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0;
    }

    .timeframe-filter .btn:hover:not(.active) {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }

    .timeframe-filter .btn.active {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
        color: white;
        font-weight: 500;
    }

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