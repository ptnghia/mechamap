@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm rounded-3 mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="fs-4 fw-bold">{{ $stats['forums'] }}</div>
                            <div class="text-muted">{{ __('Forums') }}</div>
                        </div>
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="fs-4 fw-bold">{{ $stats['threads'] }}</div>
                            <div class="text-muted">{{ __('Threads') }}</div>
                        </div>
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="fs-4 fw-bold">{{ $stats['posts'] }}</div>
                            <div class="text-muted">{{ __('Posts') }}</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="fs-4 fw-bold">{{ $stats['users'] }}</div>
                            <div class="text-muted">{{ __('Members') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm rounded-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('All Forums') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($categories as $category)
                            <div class="list-group-item bg-light">
                                <h5 class="mb-0">{{ $category->name }}</h5>
                            </div>
                            
                            @foreach($category->subForums as $forum)
                                <a href="{{ route('forums.show', $forum) }}" class="list-group-item list-group-item-action py-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-7">
                                            <div class="d-flex align-items-center">
                                                <div class="forum-icon me-3">
                                                    <i class="bi bi-chat-square-text fs-2 text-primary"></i>
                                                </div>
                                                <div>
                                                    <h5 class="mb-1">{{ $forum->name }}</h5>
                                                    <p class="mb-0 text-muted small">{{ $forum->description }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="row text-md-end">
                                                <div class="col-6">
                                                    <div class="fw-bold">{{ $forum->threads_count }}</div>
                                                    <div class="small text-muted">{{ __('Threads') }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="fw-bold">{{ $forum->posts_count }}</div>
                                                    <div class="small text-muted">{{ __('Posts') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
