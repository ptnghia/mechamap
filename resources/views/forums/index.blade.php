@extends('layouts.app')

@section('title', 'Forums - MechaMap Community')

@push('styles')
<style>
    .forum-stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .forum-stats-card .stats-item {
        transition: transform 0.2s ease;
    }

    .forum-stats-card .stats-item:hover {
        transform: translateY(-2px);
    }

    .forum-icon {
        transition: transform 0.2s ease;
    }

    .forum-item:hover .forum-icon {
        transform: scale(1.05);
    }

    .category-header {
        background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);
        border-left: 4px solid #007bff;
    }
</style>
@endpush

@section('content')
<div class="py-5">
    <div class="container">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Forums') }}</li>
            </ol>
        </nav>

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">{{ __('Forums') }}</h1>
                <p class="text-muted mb-0">{{ __('Discuss mechanical engineering topics with the community') }}</p>
            </div>
            @auth
            <div>
                <a href="{{ route('threads.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    {{ __('Create Thread') }}
                </a>
            </div>
            @endauth
        </div>

        {{-- Enhanced Forum Statistics --}}
        <div class="card shadow-sm rounded-3 mb-4 forum-stats-card">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="stats-item">
                            <div class="fs-2 fw-bold">{{ number_format($stats['forums']) }}</div>
                            <div class="opacity-75">{{ __('Forums') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="stats-item">
                            <div class="fs-2 fw-bold">{{ number_format($stats['threads']) }}</div>
                            <div class="opacity-75">{{ __('Threads') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="stats-item">
                            <div class="fs-2 fw-bold">{{ number_format($stats['posts']) }}</div>
                            <div class="opacity-75">{{ __('Posts') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-item">
                            <div class="fs-2 fw-bold">{{ number_format($stats['users']) }}</div>
                            <div class="opacity-75">{{ __('Members') }}</div>
                        </div>
                    </div>
                </div>
                @if($stats['newest_member'])
                <div class="text-center mt-3 pt-3 border-top border-light border-opacity-25">
                    <small class="opacity-75">{{ __('Newest Member') }}:</small>
                    <a href="{{ route('profile.show', $stats['newest_member']->username) }}"
                        class="text-white fw-bold text-decoration-none ms-2">
                        {{ $stats['newest_member']->name }}
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Search & Filters --}}
        <div class="card shadow-sm rounded-3 mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <form action="{{ route('forums.search') }}" method="GET" class="d-flex">
                            <input type="search" name="q" class="form-control"
                                placeholder="{{ __('Search forums, threads, and discussions...') }}"
                                value="{{ request('q') }}" minlength="3" required>
                            <button type="submit" class="btn btn-primary ms-2">
                                <i class="fas fa-search me-1"></i>
                                {{ __('Search') }}
                            </button>
                        </form>
                        <small class="text-muted mt-1 d-block">
                            {{ __('Search across all forums and discussions. Minimum 3 characters required.') }}
                        </small>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleView('grid')"
                                id="grid-view-btn">
                                <i class="fas fa-th"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm active"
                                onclick="toggleView('list')" id="list-view-btn">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Enhanced Forum Categories --}}
        @foreach($categories as $category)
        <div class="card shadow-sm rounded-3 mb-4">
            <div class="card-header category-header">
                @php
                // Lấy ảnh đại diện của category từ media relationship
                $categoryImage = $category->media->first();
                if ($categoryImage) {
                $categoryImageUrl = filter_var($categoryImage->file_path, FILTER_VALIDATE_URL)
                ? $categoryImage->file_path
                : asset('storage/' . $categoryImage->file_path);
                } else {
                $categoryImageUrl = null;
                }
                @endphp

                <div class="d-flex align-items-center">
                    @if($categoryImageUrl)
                    <img src="{{ $categoryImageUrl }}" alt="{{ $category->name }}" class="rounded me-3 shadow-sm"
                        width="36" height="36" style="object-fit: cover;">
                    @else
                    <div class="bg-primary bg-opacity-10 rounded me-3 d-flex align-items-center justify-content-center"
                        style="width: 36px; height: 36px;">
                        <i class="bi bi-collection text-primary"></i>
                    </div>
                    @endif
                    <div>
                        <h5 class="card-title mb-0">{{ $category->name }}</h5>
                        @if($category->description)
                        <small class="text-muted">{{ $category->description }}</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($category->subForums as $forum)
                    <a href="{{ route('forums.show', $forum) }}"
                        class="list-group-item list-group-item-action py-3 forum-item">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center">
                                    <div class="forum-icon me-3">
                                        @php
                                        // Lấy ảnh đại diện của forum từ media relationship
                                        $forumImage = $forum->media->first();
                                        if ($forumImage) {
                                        // Nếu file_path là URL đầy đủ thì dùng trực tiếp, ngược lại thì dùng asset
                                        $imageUrl = filter_var($forumImage->file_path, FILTER_VALIDATE_URL)
                                        ? $forumImage->file_path
                                        : asset('storage/' . $forumImage->file_path);
                                        } else {
                                        // Fallback về icon Bootstrap nếu không có ảnh
                                        $imageUrl = null;
                                        }
                                        @endphp

                                        @if($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="{{ $forum->name }}" class="rounded shadow-sm"
                                            width="50" height="50" style="object-fit: cover;">
                                        @else
                                        <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px;">
                                            <i class="bi bi-chat-square-text fs-4 text-primary"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-semibold">{{ $forum->name }}</h6>
                                        <p class="mb-0 text-muted small">{{ $forum->description }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="row text-md-end">
                                    <div class="col-6">
                                        <div class="fw-bold text-primary">{{ number_format($forum->threads_count) }}
                                        </div>
                                        <div class="small text-muted">{{ __('Threads') }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bold text-success">{{ number_format($forum->posts_count) }}</div>
                                        <div class="small text-muted">{{ __('Posts') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    function toggleView(viewType) {
    const gridBtn = document.getElementById('grid-view-btn');
    const listBtn = document.getElementById('list-view-btn');
    const forumItems = document.querySelectorAll('.forum-item');

    if (viewType === 'grid') {
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        // Add grid view styling (could be implemented further)
        localStorage.setItem('forum-view', 'grid');
    } else {
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        localStorage.setItem('forum-view', 'list');
    }
}

// Restore saved view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('forum-view') || 'list';
    toggleView(savedView);

    // Add smooth animations
    const forumItems = document.querySelectorAll('.forum-item');
    forumItems.forEach((item, index) => {
        item.style.animationDelay = (index * 0.1) + 's';
        item.classList.add('animate-fade-in');
    });
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in {
        animation: fadeIn 0.6s ease-out forwards;
    }

    .forum-item {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .forum-item:hover {
        border-left-color: #007bff;
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    .stats-item {
        cursor: pointer;
    }
`;
document.head.appendChild(style);
</script>
@endpush
@endsection