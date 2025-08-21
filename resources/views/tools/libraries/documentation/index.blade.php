@extends('layouts.app')

@section('title', __('docs.title') . ' - MechaMap')

@section('content')
<div class="container-fluid py-4">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="bg-primary text-white rounded-3 p-5 text-center">
                <h1 class="display-4 fw-bold mb-3">📚 {{ __('docs.title') }}</h1>
                <p class="lead mb-4">{{ __('docs.subtitle') }}</p>
                <div class="row text-center">
                    <div class="col-md-3">
                        <h3 class="fw-bold">{{ $totalDocs }}</h3>
                        <p class="mb-0">Tài liệu</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="fw-bold">{{ $totalCategories }}</h3>
                        <p class="mb-0">Danh mục</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="fw-bold">{{ number_format($totalViews) }}</h3>
                        <p class="mb-0">Tổng lượt xem</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="fw-bold">{{ number_format($totalDownloads) }}</h3>
                        <p class="mb-0">Lượt tải</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('search.index') }}" method="GET" class="d-flex">
                        <input type="text" name="q" class="form-control me-2"
                               placeholder="{{ __('docs.search_placeholder') }}"
                               value="{{ request('q') }}">
                        <input type="hidden" name="type" value="documentation">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> {{ __('docs.search') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Categories Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-folder"></i> {{ __('docs.categories') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($categories ?? [] as $category)
                            <a href="#" onclick="alert('Tính năng đang phát triển')"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="{{ $category->icon ?? 'fas fa-folder' }}" style="color: {{ $category->color_code }}"></i>
                                    {{ $category->name }}
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $category->documentations_count ?? 0 }}</span>
                            </a>
                        @empty
                            <div class="list-group-item text-muted">
                                <i class="fas fa-info-circle"></i> No categories available
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Featured Documents -->
            @if(isset($featuredDocs) && $featuredDocs->count() > 0)
            <div class="mb-4">
                <h3 class="mb-3"><i class="fas fa-star text-warning"></i> {{ __('docs.featured_documentation') }}</h3>
                <div class="row">
                    @foreach($featuredDocs as $doc)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-{{ $doc->content_type === 'api' ? 'success' : 'info' }}">
                                            {{ ucfirst($doc->content_type) }}
                                        </span>
                                        <small class="text-muted">{{ $doc->difficulty_level }}</small>
                                    </div>
                                    <h5 class="card-title">
                                        <a href="{{ route('tools.documentation.show', $doc->slug) }}" class="text-decoration-none">
                                            {{ $doc->title }}
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">{{ $doc->excerpt }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-eye"></i> {{ number_format($doc->view_count) }} views
                                        </small>
                                        <small class="text-muted">
                                            {{ $doc->published_at ? $doc->published_at->diffForHumans() : 'Draft' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Recent Documents -->
            <div class="mb-4">
                <h3 class="mb-3"><i class="fas fa-clock"></i> {{ __('docs.recent_documentation') }}</h3>
                <div class="row">
                    @forelse($documentation as $doc)
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-{{ $doc->content_type === 'api' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($doc->content_type) }}
                                        </span>
                                        @if($doc->is_public)
                                            <span class="badge bg-success">Public</span>
                                        @else
                                            <span class="badge bg-warning">Private</span>
                                        @endif
                                    </div>
                                    <h6 class="card-title">
                                        <a href="{{ route('tools.documentation.show', $doc->slug) }}" class="text-decoration-none">
                                            {{ $doc->title }}
                                        </a>
                                    </h6>
                                    <p class="card-text small text-muted">{{ Str::limit($doc->excerpt, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> {{ $doc->author->name ?? 'Unknown' }}
                                        </small>
                                        <small class="text-muted">
                                            {{ $doc->published_at ? $doc->published_at->format('M d, Y') : 'Draft' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No documentation available yet.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-link"></i> {{ __('docs.quick_links') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><a href="{{ route('search.index', ['type' => 'documentation', 'doc_type' => 'guide']) }}" class="text-decoration-none">
                                    <i class="fas fa-book"></i> {{ __('docs.user_guides') }}
                                </a></li>
                                <li><a href="{{ route('search.index', ['type' => 'documentation', 'doc_type' => 'tutorial']) }}" class="text-decoration-none">
                                    <i class="fas fa-graduation-cap"></i> {{ __('docs.tutorials') }}
                                </a></li>
                                <li><a href="{{ route('search.index', ['type' => 'documentation', 'doc_type' => 'api']) }}" class="text-decoration-none">
                                    <i class="fas fa-code"></i> {{ __('docs.api_documentation') }}
                                </a></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><a href="{{ route('search.index', ['type' => 'documentation', 'difficulty' => 'beginner']) }}" class="text-decoration-none">
                                    <i class="fas fa-seedling"></i> {{ __('docs.beginner_guides') }}
                                </a></li>
                                <li><a href="{{ route('search.index', ['type' => 'documentation', 'difficulty' => 'advanced']) }}" class="text-decoration-none">
                                    <i class="fas fa-rocket"></i> {{ __('docs.advanced_topics') }}
                                </a></li>
                                <li><a href="{{ route('contact') }}" class="text-decoration-none">
                                    <i class="fas fa-question-circle"></i> {{ __('docs.need_help') }}
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
</style>
@endpush
