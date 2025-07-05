@extends('layouts.app')

@section('title', $category->name . ' - MechaMap Community')

@push('styles')
<style>
    .category-header {
        background: linear-gradient(135deg, {{ $category->color_code ?? '#007bff' }}15 0%, {{ $category->color_code ?? '#007bff' }}05 100%);
        border-left: 4px solid {{ $category->color_code ?? '#007bff' }};
    }

    .stats-card {
        transition: transform 0.2s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
    }

    .forum-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .forum-card:hover {
        border-left-color: {{ $category->color_code ?? '#007bff' }};
        transform: translateX(5px);
    }

    /* Thread item styles for category page */
    .threads-list .thread-item-container {
        background: #fff;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .threads-list .thread-item-container:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="py-5">
    <div class="container">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Diễn đàn</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
            </ol>
        </nav>

        {{-- Category Header --}}
        <div class="card shadow-sm rounded-3 mb-4 category-header">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            @if($category->icon)
                            <img src="{{ $category->icon }}" alt="{{ $category->name }}"
                                 class="rounded me-3" width="48" height="48">
                            @else
                            <div class="bg-primary bg-opacity-10 rounded me-3 d-flex align-items-center justify-content-center"
                                 style="width: 48px; height: 48px;">
                                <i class="collection fs-4 text-primary"></i>
                            </div>
                            @endif
                            <div>
                                <h1 class="h3 mb-1">{{ $category->name }}</h1>
                                @if($category->description)
                                <p class="text-muted mb-0">{{ $category->description }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        @auth
                        <a href="{{ route('threads.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i>
                            Tạo bài đăng mới
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        {{-- Category Statistics --}}
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stats-card shadow-sm">
                    <div class="card-body text-center">
                        <div class="fs-2 fw-bold text-primary">{{ number_format($categoryStats['forums_count']) }}</div>
                        <div class="text-muted">Diễn đàn</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card shadow-sm">
                    <div class="card-body text-center">
                        <div class="fs-2 fw-bold text-success">{{ number_format($categoryStats['threads_count']) }}</div>
                        <div class="text-muted">Bài đăng</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card shadow-sm">
                    <div class="card-body text-center">
                        <div class="fs-2 fw-bold text-info">{{ number_format($categoryStats['views_count']) }}</div>
                        <div class="text-muted">Lượt xem</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card shadow-sm">
                    <div class="card-body text-center">
                        <div class="fs-2 fw-bold text-warning">{{ number_format($categoryStats['posts_count']) }}</div>
                        <div class="text-muted">Bình luận</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Forums in Category - Full Width --}}
        <div class="card shadow-sm rounded-3 mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Diễn đàn trong {{ $category->name }}</h5>
            </div>
            <div class="card-body p-0">
                @if($category->forums->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($category->forums as $forum)
                    <a href="{{ route('forums.show', $forum) }}"
                       class="list-group-item list-group-item-action py-3 forum-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    @if($forum->media->first())
                                    <img src="{{ asset('storage/' . $forum->media->first()->file_path) }}"
                                         alt="{{ $forum->name }}" class="rounded me-3" width="40" height="40">
                                    @else
                                    <div class="bg-primary bg-opacity-10 rounded me-3 d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-comment-square-text text-primary"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-1 fw-semibold">{{ $forum->name }}</h6>
                                        <p class="mb-0 text-muted small">{{ $forum->description }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="fw-bold text-primary">{{ number_format($forum->threads_count ?? 0) }}</div>
                                        <div class="small text-muted">Threads</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bold text-success">{{ number_format($forum->posts_count ?? 0) }}</div>
                                        <div class="small text-muted">Posts</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-comment-square-text fs-1 text-muted opacity-50"></i>
                    <p class="text-muted mt-3">Chưa có diễn đàn nào trong danh mục này</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Recent Threads - Full Width using thread-item component --}}
        <div class="card shadow-sm rounded-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Bài đăng gần đây trong {{ $category->name }}</h5>
            </div>
            <div class="card-body">
                @if($recentThreads->count() > 0)
                <div class="threads-list">
                    @foreach($recentThreads as $thread)
                    <div class="mb-4 @if(!$loop->last) pb-4 border-bottom @endif">
                        @include('partials.thread-item', ['thread' => $thread])
                    </div>
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    {{ $recentThreads->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-comment-square-text fs-1 text-muted opacity-50"></i>
                    <p class="text-muted mt-3 mb-0">Chưa có bài đăng nào trong danh mục này</p>
                    @auth
                    <a href="{{ route('threads.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus-circle me-1"></i>
                        Tạo bài đăng đầu tiên
                    </a>
                    @endauth
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
