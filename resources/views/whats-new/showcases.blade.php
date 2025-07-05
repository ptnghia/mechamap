@extends('layouts.app')

@section('title', '{{ __("messages.new_showcases") }} - MechaMap')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">What's New</h1>

        <a href="{{ route('showcase.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-lg me-1"></i> Create Showcase
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
                <a class="nav-link active" href="{{ route('whats-new.showcases') }}">{{
                    __('messages.new_showcases') }}</a>
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
                        <a class="page-link" href="{{ route('whats-new.showcases', ['page' => 1]) }}">1</a>
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
                        <a class="page-link" href="{{ route('whats-new.showcases', ['page' => $i]) }}">{{ $i
                            }}</a>
                        </li>
                        @endfor

                        <!-- Current Page -->
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>

                        <!-- Pages after current -->
                        @for($i = $page + 1; $i <= min($totalPages, $page + 2); $i++) <li class="page-item">
                            <a class="page-link" href="{{ route('whats-new.showcases', ['page' => $i]) }}">{{ $i
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
                                        href="{{ route('whats-new.showcases', ['page' => $totalPages]) }}">{{
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

    <!-- Showcases Grid -->
    <div class="body_left">

        @if($showcases->count() > 0)
        <div class="card-body p-0">
            <div class="row g-3">
                @foreach($showcases as $showcase)
                <div class="col-md-6 col-lg-6">
                    <div class="card h-100 border-0 showcase-card">
                        <!-- Showcase Header -->
                        <div class="card-header bg-transparent border-bottom">
                            <div class="d-flex align-items-center">
                                <img src="{{ get_avatar_url($showcase->user) }}" alt="{{ $showcase->user->name }}"
                                    class="rounded-circle me-2" width="32" height="32">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">
                                        <a href="{{ route('profile.show', $showcase->user) }}"
                                            class="text-decoration-none">{{ $showcase->user->name }}</a>
                                    </h6>
                                    <small class="text-muted">{{ $showcase->created_at->diffForHumans()
                                        }}</small>
                                </div>
                                <span class="badge bg-primary">{{ $showcase->showcase_type }}</span>
                            </div>
                        </div>

                        <!-- Showcase Image - Unified Component -->
                        <x-showcase-image :showcase="$showcase" size="large" />

                        <!-- Showcase Content -->
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ $showcase->showcase_url }}" class="text-decoration-none">
                                    @if($showcase->showcase_type === 'Thread')
                                    <i class="fas fa-comment-left-text me-1"></i>
                                    @elseif($showcase->showcase_type === 'Post')
                                    <i class="fas fa-comment-right me-1"></i>
                                    @else
                                    <i class="fas fa-star me-1"></i>
                                    @endif
                                    {{ $showcase->showcase_title }}
                                </a>
                            </h5>

                            @if($showcase->content_preview)
                            <p class="card-text text-muted">{{ $showcase->content_preview }}</p>
                            @endif

                            @if($showcase->description)
                            <div class="mt-2 p-2 bg-light rounded">
                                <small class="text-description">
                                    <i class="quote me-1"></i>
                                    <strong>Showcase reason:</strong> {{ $showcase->description }}
                                </small>
                            </div>
                            @endif
                        </div>

                        <!-- Showcase Footer -->
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ $showcase->showcase_url }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> Xem chi tiết
                                    </a>
                                    <a href="{{ route('showcase.show', $showcase) }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-star me-1"></i> Showcase
                                    </a>
                                </div>

                                @if($showcase->showcaseable && $showcase->showcaseable_type ===
                                'App\Models\Thread')
                                <small class="text-muted">
                                    <i class="folder me-1"></i>
                                    {{ $showcase->showcaseable->forum->name ?? 'Forum' }}
                                </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="card-body text-center py-5">
            <i class="fas fa-star display-4 text-muted"></i>
            <p class="mt-3">No showcases found.</p>
            <a href="{{ route('showcase.create') }}" class="btn btn-primary">Create First Showcase</a>
        </div>
        @endif
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
                        <a class="page-link" href="{{ route('whats-new.showcases', ['page' => 1]) }}">1</a>
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
                        <a class="page-link" href="{{ route('whats-new.showcases', ['page' => $i]) }}">{{ $i
                            }}</a>
                        </li>
                        @endfor

                        <!-- Current Page -->
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>

                        <!-- Pages after current -->
                        @for($i = $page + 1; $i <= min($totalPages, $page + 2); $i++) <li class="page-item">
                            <a class="page-link" href="{{ route('whats-new.showcases', ['page' => $i]) }}">{{ $i
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
                                        href="{{ route('whats-new.showcases', ['page' => $totalPages]) }}">{{
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
</div>
@endsection

@push('styles')
<style>

</style>
@endpush