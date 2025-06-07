@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

<div class="py-5">
    <div class="container">
        @foreach($categories as $category)
        <div class="card shadow-sm rounded-3 mb-4">
            <div class="card-header bg-light">
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
                        width="32" height="32" style="object-fit: cover;">
                    @endif
                    <h5 class="card-title mb-0">{{ $category->name }}</h5>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($category->subForums as $forum)
                    <a href="{{ route('forums.show', $forum) }}" class="list-group-item list-group-item-action py-3">
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
                                        <i class="bi bi-chat-square-text fs-2 text-primary"></i>
                                        @endif
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
                </div>
            </div>
        </div>
        @endforeach

        <div class="card shadow-sm rounded-3">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">{{ __('Forum Statistics') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <div class="fs-4 fw-bold">{{ \App\Models\Thread::count() }}</div>
                        <div class="text-muted">{{ __('Threads') }}</div>
                    </div>
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <div class="fs-4 fw-bold">{{ \App\Models\Post::count() }}</div>
                        <div class="text-muted">{{ __('Posts') }}</div>
                    </div>
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <div class="fs-4 fw-bold">{{ \App\Models\User::count() }}</div>
                        <div class="text-muted">{{ __('Members') }}</div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="fs-4 fw-bold">
                            @php
                            $newestMember = \App\Models\User::latest()->first();
                            @endphp
                            @if($newestMember)
                            <a href="{{ route('profile.show', $newestMember->username) }}" class="text-decoration-none">
                                {{ $newestMember->name }}
                            </a>
                            @else
                            {{ __('None') }}
                            @endif
                        </div>
                        <div class="text-muted">{{ __('Newest Member') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection