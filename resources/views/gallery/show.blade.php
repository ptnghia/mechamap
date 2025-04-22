@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-body p-0">
                            <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->title }}" class="img-fluid w-100">
                        </div>
                    </div>
                    
                    @if($media->description)
                        <div class="card shadow-sm rounded-3 mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ __('Description') }}</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $media->description }}</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="card shadow-sm rounded-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Comments') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-4">
                                <p class="mb-0">{{ __('Comments feature coming soon.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Media Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex mb-3">
                                <img src="{{ $media->user->getAvatarUrl() }}" alt="{{ $media->user->name }}" class="rounded-circle me-3" width="50" height="50">
                                <div>
                                    <h6 class="mb-1">
                                        <a href="{{ route('profile.show', $media->user->username) }}" class="text-decoration-none">
                                            {{ $media->user->name }}
                                        </a>
                                    </h6>
                                    <p class="mb-0 text-muted small">{{ __('Uploaded') }} {{ $media->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="mb-2">
                                <strong>{{ __('File Name') }}:</strong> {{ $media->file_name }}
                            </div>
                            <div class="mb-2">
                                <strong>{{ __('File Type') }}:</strong> {{ $media->file_type }}
                            </div>
                            <div class="mb-2">
                                <strong>{{ __('File Size') }}:</strong> {{ round($media->file_size / 1024, 2) }} KB
                            </div>
                            <div class="mb-2">
                                <strong>{{ __('Dimensions') }}:</strong> 
                                @php
                                    $imagePath = storage_path('app/public/' . $media->file_path);
                                    if (file_exists($imagePath)) {
                                        $imageSize = getimagesize($imagePath);
                                        echo $imageSize[0] . ' x ' . $imageSize[1] . ' px';
                                    } else {
                                        echo __('Unknown');
                                    }
                                @endphp
                            </div>
                            
                            <hr>
                            
                            <div class="d-grid gap-2">
                                <a href="{{ asset('storage/' . $media->file_path) }}" class="btn btn-primary" download="{{ $media->file_name }}">
                                    <i class="bi bi-download me-1"></i> {{ __('Download') }}
                                </a>
                                
                                @if(Auth::check() && (Auth::id() === $media->user_id || Auth::user()->isAdmin()))
                                    <form action="{{ route('gallery.destroy', $media) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this media?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="bi bi-trash me-1"></i> {{ __('Delete') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm rounded-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Share') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" value="{{ route('gallery.show', $media) }}" id="shareUrl" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="copyShareUrl()">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                            
                            <div class="d-flex justify-content-center gap-3">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('gallery.show', $media)) }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('gallery.show', $media)) }}&text={{ urlencode($media->title ?: __('Check out this image')) }}" target="_blank" class="btn btn-outline-info">
                                    <i class="bi bi-twitter"></i>
                                </a>
                                <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(route('gallery.show', $media)) }}&media={{ urlencode(asset('storage/' . $media->file_path)) }}&description={{ urlencode($media->title ?: __('Check out this image')) }}" target="_blank" class="btn btn-outline-danger">
                                    <i class="bi bi-pinterest"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function copyShareUrl() {
            const shareUrl = document.getElementById('shareUrl');
            shareUrl.select();
            document.execCommand('copy');
            
            // Show a tooltip or notification
            alert('{{ __('URL copied to clipboard!') }}');
        }
    </script>
    @endpush
@endsection
