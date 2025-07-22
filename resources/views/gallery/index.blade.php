@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

<div class="py-5">
    <div class="container">
        <div class="card shadow-sm rounded-3 mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form action="{{ route('gallery.index') }}" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2"
                                placeholder="{{ __('Search gallery...') }}" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">{{ t_search('form.submit') }}</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end mt-3 mt-md-0">
                            <div class="btn-group" role="group">
                                <a href="{{ route('gallery.index') }}?view=grid"
                                    class="btn btn-outline-secondary {{ request('view', 'grid') == 'grid' ? 'active' : '' }}">
                                    <i class="fas fa-th-3x3-gap"></i>
                                </a>
                                <a href="{{ route('gallery.index') }}?view=list"
                                    class="btn btn-outline-secondary {{ request('view') == 'list' ? 'active' : '' }}">
                                    <i class="fas fa-list"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm rounded-3">
            <div class="card-body">
                @if($mediaItems->count() > 0)
                @if(request('view') == 'list')
                <!-- List View -->
                <div class="list-group list-group-flush">
                    @foreach($mediaItems as $media)
                    <div class="list-group-item py-3 px-0">
                        <div class="row">
                            <div class="col-md-2">
                                <a href="{{ route('gallery.show', $media) }}">
                                    <img src="{{ $media->url }}" alt="{{ $media->title }}" class="img-fluid rounded">
                                </a>
                            </div>
                            <div class="col-md-10">
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-1">
                                        <a href="{{ route('gallery.show', $media) }}" class="text-decoration-none">
                                            {{ $media->title ?: __('Untitled') }}
                                        </a>
                                    </h5>
                                    <small class="text-muted">{{ $media->created_at->diffForHumans() }}</small>
                                </div>

                                @if($media->description)
                                <p class="mb-2">{{ Str::limit($media->description, 150) }}</p>
                                @endif

                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        {{ __('Uploaded by') }}
                                        <a href="{{ route('profile.show', $media->user->username) }}"
                                            class="text-decoration-none">
                                            {{ $media->user->name }}
                                        </a>
                                    </small>

                                    <div>
                                        <span class="badge bg-secondary">{{ strtoupper(pathinfo($media->file_name,
                                            PATHINFO_EXTENSION)) }}</span>
                                        <span class="badge bg-info">{{ round($media->file_size / 1024, 2) }} KB</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <!-- Grid View -->
                <div class="row">
                    @foreach($mediaItems as $media)
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <a href="{{ route('gallery.show', $media) }}">
                                <img src="{{ $media->url }}" class="card-img-top" alt="{{ $media->title }}">
                            </a>
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="{{ route('gallery.show', $media) }}" class="text-decoration-none">
                                        {{ $media->title ?: __('Untitled') }}
                                    </a>
                                </h6>
                                <p class="card-text small text-muted">
                                    {{ __('By') }}
                                    <a href="{{ route('profile.show', $media->user->username) }}"
                                        class="text-decoration-none">
                                        {{ $media->user->name }}
                                    </a>
                                </p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <small class="text-muted">{{ $media->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="d-flex justify-content-center mt-4">
                    {{ $mediaItems->appends(request()->query())->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="images fs-1 text-muted mb-3"></i>
                    <p class="mb-0">{{ __('No media items found.') }}</p>
                    @auth
                    <a href="{{ route('gallery.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-upload me-1"></i> {{ __('Upload Media') }}
                    </a>
                    @endauth
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
