@extends('layouts.app-full')

@section('title', __('docs.title') . ' - MechaMap')
@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/tool.css') }}">
@endpush
@section('content')
<div class="body_page">
    <!-- Hero Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="div_title_page">
            <h1 class="h2 mb-1 title_page"><i class="fa-solid fa-book text-primary me-2"></i>  {{ __('docs.title') }}</h1>
            <p class="text-muted mb-0">{{ __('docs.portal_description') }}</p>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="fw-bold">{{ $stats['total_docs'] ?? 0 }}</h3>
                    <p class="mb-0">{{ __('docs.documents') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="fw-bold">{{ $stats['total_categories'] ?? 0 }}</h3>
                    <p class="mb-0">{{ __('docs.categories') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="fw-bold">{{ number_format($stats['total_views'] ?? 0) }}</h3>
                    <p class="mb-0">{{ __('docs.total_views') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3 class="fw-bold">{{ number_format($stats['total_downloads'] ?? 0) }}</h3>
                    <p class="mb-0">{{ __('docs.total_downloads') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('tools.documentation') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('docs.search_documentation') }}</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="{{ __('docs.search_placeholder') }}"
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('docs.category') }}</label>
                            <select name="category" class="form-select">
                                <option value="">{{ __('docs.all_categories') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->documentations_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('docs.content_type') }}</label>
                            <select name="content_type" class="form-select">
                                <option value="">{{ __('docs.all_types') }}</option>
                                <option value="guide" {{ request('content_type') == 'guide' ? 'selected' : '' }}>{{ __('docs.guide') }}</option>
                                <option value="api" {{ request('content_type') == 'api' ? 'selected' : '' }}>{{ __('docs.api') }}</option>
                                <option value="tutorial" {{ request('content_type') == 'tutorial' ? 'selected' : '' }}>{{ __('docs.tutorial') }}</option>
                                <option value="reference" {{ request('content_type') == 'reference' ? 'selected' : '' }}>{{ __('docs.reference') }}</option>
                                <option value="faq" {{ request('content_type') == 'faq' ? 'selected' : '' }}>{{ __('docs.faq') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('docs.difficulty') }}</label>
                            <select name="difficulty_level" class="form-select">
                                <option value="">{{ __('docs.all_levels') }}</option>
                                <option value="beginner" {{ request('difficulty_level') == 'beginner' ? 'selected' : '' }}>{{ __('docs.beginner') }}</option>
                                <option value="intermediate" {{ request('difficulty_level') == 'intermediate' ? 'selected' : '' }}>{{ __('docs.intermediate') }}</option>
                                <option value="advanced" {{ request('difficulty_level') == 'advanced' ? 'selected' : '' }}>{{ __('docs.advanced') }}</option>
                                <option value="expert" {{ request('difficulty_level') == 'expert' ? 'selected' : '' }}>{{ __('docs.expert') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('docs.sort_by') }}</label>
                            <select name="sort" class="form-select">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __('docs.newest') }}</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>{{ __('docs.most_viewed') }}</option>
                                <option value="downloads" {{ request('sort') == 'downloads' ? 'selected' : '' }}>{{ __('docs.most_downloaded') }}</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>{{ __('docs.highest_rated') }}</option>
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>{{ __('docs.title_az') }}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> {{ __('docs.search') }}
                            </button>
                            <a href="{{ route('tools.documentation') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> {{ __('docs.clear_filters') }}
                            </a>
                        </div>
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
                <div class="card-body p-3">
                    <div class="list-group list-group-flush">
                        @forelse($categories ?? [] as $category)
                            <a href="{{ route('tools.documentation', ['category' => $category->id]) }}"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request('category') == $category->id ? 'active' : '' }}">
                                <div>
                                    <i class="{{ $category->icon ?? 'fas fa-folder' }}" style="color: {{ request('category') == $category->id ? '#fff' : $category->color_code }}"></i>
                                    {{ $category->name }}
                                </div>
                                <span class="badge {{ request('category') == $category->id ? 'bg-light text-dark' : 'bg-primary' }} rounded-pill">{{ $category->documentations_count ?? 0 }}</span>
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
