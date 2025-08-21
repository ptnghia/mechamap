@extends('layouts.app')

@section('title', __('search.search_results'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/search.css') }}">
@endpush

@section('content')
<div class="py-5">
    <div class="container">
        <!-- Search Form -->
        <div class="card shadow-sm rounded-3 mb-4">
            <div class="card-body">
                <form action="{{ route('search.index') }}" method="GET" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" name="query" class="form-control" placeholder="{{ __('search.search_placeholder') }}" value="{{ $query }}">
                    </div>
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="all" {{ $type == 'all' ? 'selected' : '' }}>{{ __('search.all') }}</option>
                            <option value="threads" {{ $type == 'threads' ? 'selected' : '' }}>{{ __('search.threads') }}</option>
                            <option value="posts" {{ $type == 'posts' ? 'selected' : '' }}>{{ __('search.posts') }}</option>
                            <option value="showcases" {{ $type == 'showcases' ? 'selected' : '' }}>Dự án</option>
                            <option value="products" {{ $type == 'products' ? 'selected' : '' }}>Sản phẩm</option>
                            <option value="materials" {{ $type == 'materials' ? 'selected' : '' }}>Vật liệu</option>
                            <option value="cad_files" {{ $type == 'cad_files' ? 'selected' : '' }}>File CAD</option>
                            <option value="users" {{ $type == 'users' ? 'selected' : '' }}>{{ __('search.users') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">{{ __('search.form.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Search Results Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                @if($query)
                    <p class="mb-0">{{ __('search.search_results_for') }}: <strong>{{ $query }}</strong></p>
                @endif
            </div>
            <div>
                <a href="{{ route('threads.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-sliders-h me-1"></i> {{ __('search.advanced_search') }}
                </a>
            </div>
        </div>

            @if($query)
                @if(($type == 'all' || $type == 'threads') && $threads->count() > 0)
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('search.threads') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($threads as $thread)
                                    <div class="list-group-item py-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <a href="#" class="text-decoration-none">{{ $thread->title }}</a>
                                            </h5>
                                            <small class="text-muted">{{ $thread->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit(strip_tags($thread->content), 150) }}</p>
                                        <small>
                                            {{ __('common.by') }}
                                            <a href="{{ route('profile.show', $thread->user->username) }}" class="text-decoration-none">
                                                {{ $thread->user->name }}
                                            </a>
                                            {{ __('search.in') }}
                                            <a href="{{ route('forums.show', $thread->forum) }}" class="text-decoration-none fw-bold">
                                                {{ $thread->forum->name }}
                                            </a>
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if(($type == 'all' || $type == 'posts') && $posts->count() > 0)
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('search.posts') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($posts as $post)
                                    <div class="list-group-item py-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <a href="#" class="text-decoration-none">{{ __('common.reply_in') }}: {{ $post->thread->title }}</a>
                                            </h5>
                                            <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit(strip_tags($post->content), 150) }}</p>
                                        <small>
                                            {{ __('common.by') }}
                                            <a href="{{ route('profile.show', $post->user->username) }}" class="text-decoration-none">
                                                {{ $post->user->name }}
                                            </a>
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if(($type == 'all' || $type == 'users') && $users->count() > 0)
                    <div class="card shadow-sm rounded-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('search.users') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($users as $user)
                                    <div class="list-group-item py-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle me-3" width="50" height="50">
                                            <div>
                                                <h5 class="mb-1">
                                                    <a href="{{ route('profile.show', $user->username) }}" class="text-decoration-none">
                                                        {{ $user->name }}
                                                    </a>
                                                </h5>
                                                <p class="mb-0 text-muted small">
                                                    {{ '@' . $user->username }} · {{ __('common.joined') }} {{ $user->created_at->format('M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if(($type == 'all' || $type == 'showcases') && $showcases->count() > 0)
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Dự án</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($showcases as $showcase)
                                    <div class="list-group-item py-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <a href="{{ route('showcase.show', $showcase->id) }}" class="text-decoration-none">{{ $showcase->title }}</a>
                                            </h5>
                                            <small class="text-muted">{{ $showcase->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit(strip_tags($showcase->description), 150) }}</p>
                                        <small>
                                            {{ __('common.by') }}
                                            <a href="{{ route('profile.show', $showcase->user->username) }}" class="text-decoration-none">
                                                {{ $showcase->user->name }}
                                            </a>
                                            @if($showcase->project_type)
                                                · <span class="badge bg-secondary">{{ $showcase->project_type }}</span>
                                            @endif
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if(($type == 'all' || $type == 'products') && $products->count() > 0)
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Sản phẩm</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($products as $product)
                                    <div class="list-group-item py-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <a href="{{ route('marketplace.products.show', $product->slug) }}" class="text-decoration-none">{{ $product->name }}</a>
                                            </h5>
                                            <small class="text-muted">
                                                @if($product->price)
                                                    <span class="fw-bold text-primary">{{ number_format($product->price) }} VNĐ</span>
                                                @endif
                                            </small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit(strip_tags($product->description), 150) }}</p>
                                        <small>
                                            {{ __('common.by') }}
                                            <a href="{{ route('profile.show', $product->seller->username) }}" class="text-decoration-none">
                                                {{ $product->seller->name }}
                                            </a>
                                            @if($product->category)
                                                · <span class="badge bg-info">{{ $product->category }}</span>
                                            @endif
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if(($type == 'all' || $type == 'materials') && $materials->count() > 0)
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Vật liệu</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($materials as $material)
                                    <div class="list-group-item py-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <a href="#" class="text-decoration-none">{{ $material->name }}</a>
                                            </h5>
                                            <small class="text-muted">{{ $material->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit(strip_tags($material->description), 150) }}</p>
                                        <small>
                                            @if($material->category)
                                                <span class="badge bg-success">{{ $material->category }}</span>
                                            @endif
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if(($type == 'all' || $type == 'cad_files') && $cadFiles->count() > 0)
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">File CAD</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($cadFiles as $cadFile)
                                    <div class="list-group-item py-3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <a href="#" class="text-decoration-none">{{ $cadFile->title }}</a>
                                            </h5>
                                            <small class="text-muted">{{ $cadFile->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit(strip_tags($cadFile->description), 150) }}</p>
                                        <small>
                                            {{ __('common.by') }}
                                            <a href="{{ route('profile.show', $cadFile->creator->username) }}" class="text-decoration-none">
                                                {{ $cadFile->creator->name }}
                                            </a>
                                            @if($cadFile->drawing_type)
                                                · <span class="badge bg-warning">{{ $cadFile->drawing_type }}</span>
                                            @endif
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if(($type == 'all' && $threads->count() == 0 && $posts->count() == 0 && $users->count() == 0 && $showcases->count() == 0 && $products->count() == 0 && $materials->count() == 0 && $cadFiles->count() == 0) ||
                    ($type == 'threads' && $threads->count() == 0) ||
                    ($type == 'posts' && $posts->count() == 0) ||
                    ($type == 'showcases' && $showcases->count() == 0) ||
                    ($type == 'products' && $products->count() == 0) ||
                    ($type == 'materials' && $materials->count() == 0) ||
                    ($type == 'cad_files' && $cadFiles->count() == 0) ||
                    ($type == 'users' && $users->count() == 0))
                    <div class="card shadow-sm rounded-3">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-search fs-1 text-muted mb-3"></i>
                            <p class="mb-0">{{ __('search.no_results_found') }}</p>
                            <p class="text-muted">{{ __('search.try_different_keywords') }}</p>
                            <a href="{{ route('search.advanced') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-sliders-h me-1"></i> {{ __('search.advanced_search') }}
                            </a>
                        </div>
                    </div>
                @endif
            @else
                <div class="card shadow-sm rounded-3">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search fs-1 text-muted mb-3"></i>
                        <p class="mb-0">{{ __('search.enter_search_term') }}</p>
                        <p class="text-muted">{{ __('search.search_description') }}</p>
                        <a href="{{ route('search.advanced') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-sliders-h me-1"></i> {{ __('search.advanced_search') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
