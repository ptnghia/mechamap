@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm rounded-3 mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Search Criteria') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($keywords)
                            <div class="col-md-3 mb-2">
                                <strong>{{ __('Keywords') }}:</strong> {{ $keywords }}
                            </div>
                        @endif
                        
                        @if($author)
                            <div class="col-md-3 mb-2">
                                <strong>{{ __('Author') }}:</strong> {{ $author }}
                            </div>
                        @endif
                        
                        @if($forumId)
                            <div class="col-md-3 mb-2">
                                <strong>{{ __('Forum') }}:</strong> 
                                @php
                                    $forum = $forums->firstWhere('id', $forumId);
                                @endphp
                                {{ $forum ? $forum->name : __('Unknown') }}
                            </div>
                        @endif
                        
                        @if($dateFrom || $dateTo)
                            <div class="col-md-3 mb-2">
                                <strong>{{ __('Date Range') }}:</strong> 
                                {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : __('Any') }}
                                {{ __('to') }}
                                {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : __('Present') }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('search.advanced') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-edit me-1"></i> {{ __('Modify Search') }}
                        </a>
                        
                        <div>
                            <span class="me-2">{{ __('Sort by') }}: {{ ucfirst($sortBy) }} ({{ $sortDir == 'desc' ? __('Descending') : __('Ascending') }})</span>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'relevance']) }}" class="btn btn-outline-secondary {{ $sortBy == 'relevance' ? 'active' : '' }}">
                                    {{ __('Relevance') }}
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'date']) }}" class="btn btn-outline-secondary {{ $sortBy == 'date' ? 'active' : '' }}">
                                    {{ __('Date') }}
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'replies']) }}" class="btn btn-outline-secondary {{ $sortBy == 'replies' ? 'active' : '' }}">
                                    {{ __('Replies') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm rounded-3 mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#threads" data-bs-toggle="tab">
                                {{ __('Threads') }} ({{ $threads->total() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#posts" data-bs-toggle="tab">
                                {{ __('Posts') }} ({{ $posts->total() }})
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="threads">
                            @if($threads->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($threads as $thread)
                                        <div class="list-group-item py-3 px-0">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">
                                                    <a href="#" class="text-decoration-none">{{ $thread->title }}</a>
                                                </h5>
                                                <small class="text-muted">{{ $thread->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-1">{{ Str::limit(strip_tags($thread->content), 150) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small>
                                                    {{ __('By') }} 
                                                    <a href="{{ route('profile.show', $thread->user->username) }}" class="text-decoration-none">
                                                        {{ $thread->user->name }}
                                                    </a> 
                                                    {{ __('in') }} 
                                                    <a href="{{ route('forums.show', $thread->forum) }}" class="text-decoration-none fw-bold">
                                                        {{ $thread->forum->name }}
                                                    </a>
                                                </small>
                                                <div>
                                                    <span class="badge bg-primary">{{ $thread->posts_count ?? 0 }} {{ __('replies') }}</span>
                                                    <span class="badge bg-secondary">{{ $thread->views ?? 0 }} {{ __('views') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $threads->appends(request()->except('page'))->links() }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="mb-0">{{ __('No threads found matching your search criteria.') }}</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="tab-pane fade" id="posts">
                            @if($posts->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($posts as $post)
                                        <div class="list-group-item py-3 px-0">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">
                                                    <a href="#" class="text-decoration-none">{{ __('Reply in') }}: {{ $post->thread->title }}</a>
                                                </h5>
                                                <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-1">{{ Str::limit(strip_tags($post->content), 150) }}</p>
                                            <small>
                                                {{ __('By') }} 
                                                <a href="{{ route('profile.show', $post->user->username) }}" class="text-decoration-none">
                                                    {{ $post->user->name }}
                                                </a>
                                                {{ __('in') }} 
                                                <a href="{{ route('forums.show', $post->thread->forum) }}" class="text-decoration-none fw-bold">
                                                    {{ $post->thread->forum->name }}
                                                </a>
                                            </small>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $posts->appends(request()->except('page'))->links() }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="mb-0">{{ __('No posts found matching your search criteria.') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
