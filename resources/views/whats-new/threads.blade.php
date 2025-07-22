@extends('layouts.app')

@section('title', __('forum.threads.new') . ' - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/whats-new.css') }}">
@endpush

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0 title_page">{{ __('nav.main.whats_new') }}</h1>

                <a href="{{ route('threads.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> {{ __('forum.threads.create') }}
                </a>
            </div>

            <!-- Navigation Tabs -->
            <div class="whats-new-tabs mb-4">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new') }}">{{ __('forum.posts.new') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.popular') }}">{{ __('common.buttons.popular') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('whats-new.threads') }}">{{ __('forum.threads.new')
                            }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.showcases') }}">{{ __('showcase.new')
                            }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.media') }}">{{ __('media.new') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('whats-new.replies') }}">{{
                            __('forum.threads.looking_for_replies') }}</a>
                    </li>
                </ul>
            </div>

            <!-- Pagination Top -->
            <div class="pagination-container mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="pagination-info">
                        <span>{{ __('ui.pagination.page') }} {{ $page }} {{ __('ui.pagination.of') }} {{ $totalPages }}</span>
                    </div>

                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Previous Page -->
                            <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $prevPageUrl }}" aria-label="{{ __('ui.pagination.previous') }}">
                                    <span aria-hidden="true"><i class="fa-solid fa-chevron-left"></i></span>
                                </a>
                            </li>

                            <!-- First Page -->
                            @if($page > 3)
                            <li class="page-item">
                                <a class="page-link" href="{{ route('whats-new.threads', ['page' => 1]) }}">1</a>
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
                                <a class="page-link" href="{{ route('whats-new.threads', ['page' => $i]) }}">{{ $i
                                    }}</a>
                                </li>
                                @endfor

                                <!-- Current Page -->
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>

                                <!-- Pages after current -->
                                @for($i = $page + 1; $i <= min($totalPages, $page + 2); $i++) <li class="page-item">
                                    <a class="page-link" href="{{ route('whats-new.threads', ['page' => $i]) }}">{{ $i
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
                                                href="{{ route('whats-new.threads', ['page' => $totalPages]) }}">{{
                                                $totalPages }}</a>
                                            </li>
                                            @endif

                                            <!-- Next Page -->
                                            <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $nextPageUrl }}" aria-label="{{ __('ui.pagination.next') }}">
                                                    <span aria-hidden="true"><i class="fa-solid fa-chevron-right"></i></span>
                                                </a>
                                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- Threads List -->
            <div class="body_left">
                <div class="list-group list-group-flush">
                    @foreach($threads as $thread)
                    @include('partials.thread-item', [
                    'thread' => $thread
                    ])
                    @endforeach
                </div>
            </div>

            <!-- Pagination Bottom -->
            <div class="pagination-container mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="pagination-info">
                        <span>{{ __('ui.pagination.page') }} {{ $page }} {{ __('ui.pagination.of') }} {{ $totalPages }}</span>
                    </div>

                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Previous Page -->
                            <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $prevPageUrl }}" aria-label="{{ __('ui.pagination.previous') }}">
                                    <span aria-hidden="true"><i class="fa-solid fa-chevron-left"></i></span>
                                </a>
                            </li>

                            <!-- First Page -->
                            @if($page > 3)
                            <li class="page-item">
                                <a class="page-link" href="{{ route('whats-new.threads', ['page' => 1]) }}">1</a>
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
                                <a class="page-link" href="{{ route('whats-new.threads', ['page' => $i]) }}">{{ $i
                                    }}</a>
                                </li>
                                @endfor

                                <!-- Current Page -->
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>

                                <!-- Pages after current -->
                                @for($i = $page + 1; $i <= min($totalPages, $page + 2); $i++) <li class="page-item">
                                    <a class="page-link" href="{{ route('whats-new.threads', ['page' => $i]) }}">{{ $i
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
                                                href="{{ route('whats-new.threads', ['page' => $totalPages]) }}">{{
                                                $totalPages }}</a>
                                            </li>
                                            @endif

                                            <!-- Next Page -->
                                            <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $nextPageUrl }}" aria-label="{{ __('ui.pagination.next') }}">
                                                    <span aria-hidden="true"><i class="fa-solid fa-chevron-right"></i></span>
                                                </a>
                                            </li>
                        </ul>
                    </nav>

                    <div class="pagination-goto">
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control" id="pageInput" min="1" max="{{ $totalPages }}"
                                value="{{ $page }}" placeholder="{{ __('ui.pagination.page') }}">
                            <button class="btn btn-primary" type="button" id="goToPageBtn">{{ __('ui.pagination.go_to_page') }}</button>
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
                window.location.href = '{{ route("whats-new.threads") }}?page=' + page;
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

</style>
@endsection
