@extends('layouts.app')

@section('title', __('forum.threads.looking_for_replies') . ' - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/whats-new.css') }}">
@endpush

@section('content')
<div class="body_page">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="div_title_page">
            <h1 class="h2 mb-1 title_page">{{ seo_title_short(__('forum.threads.looking_for_replies')) }}</h1>
            <p class="text-muted mb-0">{{ seo_value('description', __('ui.whats_new.replies.description'))  }}</p>
        </div>
        <a href="{{ route('threads.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> {{ __('forum.threads.create') }}
        </a>
    </div>

    <!-- Navigation Tabs -->
    <div class="whats-new-tabs mb-4">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new') }}"><i class="fas fa-info-circle me-1"></i>{{ __('forum.posts.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.popular') }}"><i class="fas fa-fire me-1"></i>{{ __('ui.common.popular') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.hot-topics') }}"><i class="fa-solid fa-fire-flame-curved me-1"></i>{{ __('whats_new.hot_topics') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.threads') }}"><i class="fa-solid fa-rss me-1"></i>{{ __('forum.threads.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.showcases') }}"><i class="fa-solid fa-compass-drafting me-1"></i>{{ __('showcase.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whats-new.media') }}"><i class="fa-solid fa-photo-film me-1"></i>{{ __('media.new') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('whats-new.replies') }}"><i class="fa-solid fa-question me-1"></i>{{ __('forum.threads.looking_for_replies') }}</a>
            </li>
        </ul>
    </div>

    <!-- Pagination Top -->
    @if($threads->hasPages())
    <div class="text-center mt-4">
        {{ $threads->links() }}
    </div>
    @endif

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
    @if($threads->hasPages())
    <div class="text-center mt-4">
        {{ $threads->links() }}
    </div>
    @endif

</div>


@push('scripts')

@endpush

@endsection
