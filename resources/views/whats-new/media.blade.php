@extends('layouts.app')

@section('title', '{{ __("messages.new_media") }} - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/whats-new.css') }}">
<!-- Lightbox CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
@endpush

@section('content')
<div class="container_2 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Cập nhật mới</h1>

        <a href="{{ route('threads.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-lg me-1"></i> Tạo chủ đề mới
        </a>
    </div>

    <!-- Navigation Tabs -->
    <div class="whats-new-tabs mb-4">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new') }}">{{ __('messages.new_posts') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.popular') }}">{{ __('messages.popular') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.threads') }}">{{ __('messages.new_threads') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.showcases') }}">{{ __('messages.new_showcases')
                    }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('whats-new.media') }}">{{ __('messages.new_media')
                    }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.replies') }}">{{
                    __('messages.looking_for_replies') }}</a>
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
                            <span aria-hidden="true"><i class="chevron-left"></i></span>
                        </a>
                    </li>

                    <!-- First Page -->
                    @if($page > 3)
                    <li class="page-item">
                        <a class="page-link" href="{{ route('whats-new.media', ['page' => 1]) }}">1</a>
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
                        <a class="page-link" href="{{ route('whats-new.media', ['page' => $i]) }}">{{ $i }}</a>
                        </li>
                        @endfor

                        <!-- Current Page -->
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>

                        <!-- Pages after current -->
                        @for($i = $page + 1; $i <= min($totalPages, $page + 2); $i++) <li class="page-item">
                            <a class="page-link" href="{{ route('whats-new.media', ['page' => $i]) }}">{{ $i
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
                                        href="{{ route('whats-new.media', ['page' => $totalPages]) }}">{{
                                        $totalPages }}</a>
                                    </li>
                                    @endif

                                    <!-- Next Page -->
                                    <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $nextPageUrl }}" aria-label="Next">
                                            <span aria-hidden="true"><i class="chevron-right"></i></span>
                                        </a>
                                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Media Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-2 g-3">
        @foreach($mediaItems as $media)
        <div class="col">
            <div class="card h-100">
                <a href="{{ $media->url }}" data-lightbox="media-gallery" data-title="{{ $media->title }}">
                    <img src="{{ $media->url }}" class="card-img-top" alt="{{ $media->title }}"
                        style="height: 200px; object-fit: cover;">
                </a>
                <div class="card-body">
                    <h5 class="card-title">{{ $media->title }}</h5>
                    <p class="card-text small text-muted">{{ Str::limit($media->description, 100) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group">
                            @if($media->thread)
                            <a href="{{ route('threads.show', $media->thread->slug) }}"
                                class="btn btn-sm btn-outline-primary">Xem chủ để</a>
                            @endif
                            <a href="{{ $media->url }}" download class="btn btn-sm btn-outline-secondary">Tải
                                về</a>
                        </div>
                        <small class="text-muted">{{ $media->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex align-items-center">
                        <img src="{{ get_avatar_url($media->user) }}" alt="{{ $media->user->name }}"
                            class="rounded-circle me-2" width="24" height="24">
                        <small class="text-muted">
                            <a href="{{ route('profile.show', $media->user->id) }}"
                                class="text-decoration-none">{{ $media->user->name }}</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
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
                            <span aria-hidden="true"><i class="chevron-left"></i></span>
                        </a>
                    </li>

                    <!-- First Page -->
                    @if($page > 3)
                    <li class="page-item">
                        <a class="page-link" href="{{ route('whats-new.media', ['page' => 1]) }}">1</a>
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
                        <a class="page-link" href="{{ route('whats-new.media', ['page' => $i]) }}">{{ $i }}</a>
                        </li>
                        @endfor

                        <!-- Current Page -->
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>

                        <!-- Pages after current -->
                        @for($i = $page + 1; $i <= min($totalPages, $page + 2); $i++) <li class="page-item">
                            <a class="page-link" href="{{ route('whats-new.media', ['page' => $i]) }}">{{ $i
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
                                        href="{{ route('whats-new.media', ['page' => $totalPages]) }}">{{
                                        $totalPages }}</a>
                                    </li>
                                    @endif

                                    <!-- Next Page -->
                                    <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $nextPageUrl }}" aria-label="Next">
                                            <span aria-hidden="true"><i class="chevron-right"></i></span>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const goToPageBtn = document.getElementById('goToPageBtn');
        const pageInput = document.getElementById('pageInput');

        goToPageBtn.addEventListener('click', function() {
            const page = parseInt(pageInput.value);
            if (page >= 1 && page <= {{ $totalPages }}) {
                window.location.href = '{{ route("whats-new.media") }}?page=' + page;
            }
        });

        pageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                goToPageBtn.click();
            }
        });
    });

    // Lightbox configuration
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'albumLabel': 'Hình %1 / %2',
        'fadeDuration': 300,
        'imageFadeDuration': 300
    });
</script>

<!-- Lightbox Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
@endpush

@endsection
