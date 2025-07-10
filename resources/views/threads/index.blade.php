@extends('layouts.app')

@section('title', __('messages.threads.forums_title'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/threads.css') }}">
@endpush

@section('content')
<div class="body_page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-1 title_page">{{ __('messages.threads.forums_title') }}</h1>
        @auth
        <a href="{{ route('threads.create') }}" class="btn btn-link">
            <i class="fa-solid fa-plus me-1"></i> {{ __('messages.threads.new_thread') }}
        </a>
        @endauth
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('threads.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="category" class="form-label">{{ __('messages.threads.category') }}</label>
                    <select class="form-select select_search" id="category" name="category">
                        <option value="">{{ __('messages.threads.all_categories') }}</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category')==$category->id ? 'selected' :
                            '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="forum" class="form-label">{{ __('messages.threads.forum') }}</label>
                    <select class="form-select select_search" id="forum" name="forum">
                        <option value="">{{ __('messages.threads.all_forums') }}</option>
                        @foreach($forums as $forum)
                        <option value="{{ $forum->id }}" {{ request('forum')==$forum->id ? 'selected' : '' }}>{{
                            $forum->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="sort" class="form-label">{{ __('messages.threads.sort_by') }}</label>
                    <select class="form-select select_search" id="sort" name="sort">
                        <option value="latest" {{ request('sort', 'latest' )=='latest' ? 'selected' : '' }}>
                            {{ __('messages.threads.latest') }}</option>
                        <option value="oldest" {{ request('sort')=='oldest' ? 'selected' : '' }}>{{ __('messages.threads.oldest') }}</option>
                        <option value="most_viewed" {{ request('sort')=='most_viewed' ? 'selected' : '' }}>{{ __('messages.threads.most_viewed') }}</option>
                        <option value="most_commented" {{ request('sort')=='most_commented' ? 'selected' : ''
                            }}>{{ __('messages.threads.most_commented') }}</option>
                    </select>
                </div>

                <div class="col-md-8">
                    <label for="search" class="form-label">{{ __('messages.threads.search') }}</label>
                    <input type="text" class="form-control input_search" id="search" name="search"
                        value="{{ request('search') }}" placeholder="{{ __('messages.threads.search_placeholder') }}">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-search w-100">{{ __('messages.threads.apply_filters') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Threads List -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 title_page">{{ __('messages.threads.threads_title') }}</h5>
        <span class="badge bg-secondary">{{ $threads->total() }} {{ __('messages.threads.threads_count') }}</span>
    </div>
    @if($threads->count() > 0)
    <div class="list-group list-group-flush">
        @foreach($threads as $thread)
        @include('partials.thread-item', [
        'thread' => $thread
        ])
        @endforeach
    </div>

    <div class="list_post_threads_footer">
        {{ $threads->links() }}
    </div>
    @else
    <div class="card-body text-center py-5">
        <i class="fas fa-search display-4 text-muted"></i>
        <p class="mt-3">{{ __('messages.threads.no_threads_found') }}</p>
        <a href="{{ route('threads.index') }}" class="btn btn-outline-primary">{{ __('messages.threads.clear_filters') }}</a>
    </div>
    @endif
</div>
@endsection
