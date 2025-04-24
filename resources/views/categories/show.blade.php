@extends('layouts.app')

@section('title', $category->name . ' - MechaMap')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    @if($category->parent)
                    <li class="breadcrumb-item"><a href="{{ route('categories.show', $category->parent->slug) }}">{{ $category->parent->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                </ol>
            </nav>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>{{ $category->name }}</h1>
                @auth
                <a href="{{ route('forums.select') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Thread
                </a>
                @endauth
            </div>
            
            @if($category->description)
            <div class="card mb-4">
                <div class="card-body">
                    <p class="card-text">{{ $category->description }}</p>
                </div>
            </div>
            @endif
            
            @if($subcategories->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Subcategories</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($subcategories as $subcategory)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <i class="fas fa-folder fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">
                                        <a href="{{ route('categories.show', $subcategory->slug) }}" class="text-decoration-none">
                                            {{ $subcategory->name }}
                                        </a>
                                    </h5>
                                    @if($subcategory->description)
                                    <p class="text-muted small mb-0">{{ Str::limit($subcategory->description, 100) }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Threads</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Sort
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="sortDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}">Latest</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}">Popular</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'most_viewed']) }}">Most Viewed</a></li>
                        </ul>
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($threads as $thread)
                    <div class="list-group-item p-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <img src="{{ $thread->user->avatar }}" alt="{{ $thread->user->name }}" class="rounded-circle" width="50" height="50">
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5 class="mb-1">
                                        @if($thread->is_sticky)
                                        <span class="badge bg-primary me-1">Sticky</span>
                                        @endif
                                        @if($thread->is_locked)
                                        <span class="badge bg-secondary me-1">Locked</span>
                                        @endif
                                        <a href="{{ route('threads.show', $thread->slug) }}" class="text-decoration-none">
                                            {{ $thread->title }}
                                        </a>
                                    </h5>
                                    <small class="text-muted">{{ $thread->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 text-muted small">
                                    By <a href="{{ route('profile.show', $thread->user->username) }}" class="text-decoration-none">{{ $thread->user->name }}</a>
                                    @if($thread->forum)
                                    in <a href="{{ route('forums.show', $thread->forum->slug) }}" class="text-decoration-none">{{ $thread->forum->name }}</a>
                                    @endif
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div>
                                        <span class="badge bg-secondary me-2" title="Replies">
                                            <i class="fas fa-comment"></i> {{ $thread->comments->count() }}
                                        </span>
                                        <span class="badge bg-secondary" title="Views">
                                            <i class="fas fa-eye"></i> {{ $thread->view_count }}
                                        </span>
                                    </div>
                                    <div class="text-muted small">
                                        @if($thread->comments->count() > 0)
                                        Last reply {{ $thread->comments->sortByDesc('created_at')->first()->created_at->diffForHumans() }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item p-4 text-center">
                        <p class="mb-0">No threads found in this category.</p>
                        @auth
                        <a href="{{ route('forums.select') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus"></i> Create the first thread
                        </a>
                        @endauth
                    </div>
                    @endforelse
                </div>
            </div>
            
            <div class="mt-4">
                {{ $threads->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
