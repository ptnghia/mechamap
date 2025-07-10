@extends('layouts.app')

@section('title', $forum->name . ' - MechaMap Forums')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/forums/index.css') }}">
@endpush

@section('content')
<div class="body_page" data-forum-id="{{ $forum->id }}">
    <!-- Breadcrumb -->
    <!--nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('forums.index') }}" class="text-decoration-none">Forums</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $forum->name }}
            </li>
        </ol>
    </nav-->

    <!-- Forum Header -->
     <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 title_page">{{ $forum->name }}</h1>
            <p class="mb-0 opacity-90">{{ $forum->description }}</p>
        </div>
        <div>
            <a href="{{ route('threads.create', ['forum' => $forum->id]) }}" class="btn btn-link  btn-primary">
                <i class="fas fa-plus me-2"></i>
                {{ __('forums.actions.new_thread') }}
            </a>
        </div>
    </div>
    {{-- Search and Filters - Full Width --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <!-- Search Input -->
                <div class="col-md-6">
                    <div class="input-group input_group_search">
                        <input type="text" name="search" class="form-control"
                            placeholder="{{ __('forums.search.placeholder') }}" value="{{ request('search') }}">
                        <button class="btn" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Sort Dropdown -->
                <div class="col-md-3">
                    <select name="sort" class="form-select select_search" onchange="this.form.submit()">
                        <option value="latest" {{ request('sort')=='latest' ? 'selected' : '' }}>
                            {{ __('forums.sort.latest_activity') }}
                        </option>
                        <option value="oldest" {{ request('sort')=='oldest' ? 'selected' : '' }}>
                            {{ __('forums.sort.oldest_first') }}
                        </option>
                        <option value="popular" {{ request('sort')=='popular' ? 'selected' : '' }}>
                            {{ __('forums.sort.most_replies') }}
                        </option>
                        <option value="views" {{ request('sort')=='views' ? 'selected' : '' }}>
                            {{ __('forums.sort.most_views') }}
                        </option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="col-md-3">
                    <div class="btn-group btn_group_search w-100" role="group">
                        <input type="radio" name="filter" value="" id="all" class="btn-check" {{
                            !request('filter') ? 'checked' : '' }}>
                        <label class="btn filter-btn btn-sm" for="all">{{ __('forums.filter.all') }}</label>

                        <input type="radio" name="filter" value="recent" id="recent" class="btn-check" {{
                            request('filter')=='recent' ? 'checked' : '' }}>
                        <label class="btn filter-btn btn-sm" for="recent">{{ __('forums.filter.recent') }}</label>

                        <input type="radio" name="filter" value="unanswered" id="unanswered"
                            class="btn-check" {{ request('filter')=='unanswered' ? 'checked' : '' }}>
                        <label class="btn filter-btn btn-sm" for="unanswered">{{ __('forums.filter.unanswered') }}</label>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Threads List using thread-item component --}}
    @if($threads->count() > 0)
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 title_page_sub">
            <i class="fas fa-comments me-2"></i>
            {{ $threads->total() }} {{ __('forums.threads.title') }}
            <!--span class="badge bg-secondary">{{ $threads->total() }}</span-->
        </h5>

        @if(request('search') || request('filter'))
        <a href="{{ route('forums.show', $forum) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-times me-1"></i>
            {{ __('forums.actions.clear_filters') }}
        </a>
        @endif
    </div>
    <div class="list_post_threads">
        <div class="list-group list-group-flush">
            @foreach($threads as $thread)
            @include('partials.thread-item', ['thread' => $thread])
            @endforeach
        </div>
    </div>
    <div class="list_post_threads_footer">
        {{ $threads->appends(request()->query())->links() }}
    </div>
    @else
    {{-- No Threads Found --}}
    <div class="card">
        <div class="card-body text-center py-5">
            @if(request('search') || request('filter'))
            <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
            <h4 class="text-muted mb-2">{{ __('forums.threads.no_threads_found') }}</h4>
            <p class="text-muted mb-4">
                {{ __('forums.threads.no_threads_found_desc') }}
            </p>
            <a href="{{ route('forums.show', $forum) }}" class="btn btn-outline-primary">
                <i class="fas fa-times me-2"></i>
                {{ __('forums.actions.clear_filters') }}
            </a>
            @else
            <i class="fas fa-comments text-muted mb-3" style="font-size: 3rem;"></i>
            <h4 class="text-muted mb-2">{{ __('forums.threads.no_threads_yet') }}</h4>
            <p class="text-muted mb-4">
                {{ __('forums.threads.be_first_to_post') }}
            </p>
            <a href="{{ route('threads.create', ['forum' => $forum->id]) }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                {{ __('forums.actions.create_first_thread') }}
            </a>
            @endif
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filter buttons are clicked
    const filterButtons = document.querySelectorAll('input[name="filter"]');
    filterButtons.forEach(button => {
        button.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });

    // Add fade-in animation to thread items
    const threadItems = document.querySelectorAll('.thread-item');
    threadItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';

        setTimeout(() => {
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endpush
@endsection
