@extends('layouts.app')

@section('title', __('search.basic_search'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>
                        {{ __('search.search_results') }}
                    </h5>
                    <a href="{{ route('forums.search.advanced') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-cogs me-1"></i>
                        {{ __('search.advanced_search') }}
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('search.basic') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text"
                                           name="query"
                                           class="form-control form-control-lg"
                                           placeholder="{{ __('search.enter_search_terms') }}"
                                           value="{{ $query }}"
                                           required>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                        {{ __('search.search') }}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select name="type" class="form-select">
                                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>
                                        {{ __('search.all_content') }}
                                    </option>
                                    <option value="threads" {{ $type === 'threads' ? 'selected' : '' }}>
                                        {{ __('search.threads_only') }}
                                    </option>
                                    <option value="posts" {{ $type === 'posts' ? 'selected' : '' }}>
                                        {{ __('search.posts_only') }}
                                    </option>
                                    <option value="users" {{ $type === 'users' ? 'selected' : '' }}>
                                        {{ __('search.users_only') }}
                                    </option>
                                    <option value="products" {{ $type === 'products' ? 'selected' : '' }}>
                                        {{ __('search.products_only') }}
                                    </option>
                                    <option value="showcases" {{ $type === 'showcases' ? 'selected' : '' }}>
                                        {{ __('search.showcases_only') }}
                                    </option>
                                    <option value="documentation" {{ $type === 'documentation' ? 'selected' : '' }}>
                                        {{ __('search.documentation_only') }}
                                    </option>
                                    <option value="materials" {{ $type === 'materials' ? 'selected' : '' }}>
                                        {{ __('search.materials_only') }}
                                    </option>
                                    <option value="cad_files" {{ $type === 'cad_files' ? 'selected' : '' }}>
                                        {{ __('search.cad_files_only') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </form>

                    @if($query)
                        <!-- Search Results Summary -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('search.found_results', ['count' => $totalResults, 'query' => $query]) }}
                        </div>

                        <!-- Results Tabs -->
                        <ul class="nav nav-tabs mb-3" id="searchTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $type === 'all' || $type === 'threads' ? 'active' : '' }}"
                                        id="threads-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#threads"
                                        type="button"
                                        role="tab">
                                    <i class="fas fa-comments me-1"></i>
                                    {{ __('search.threads') }} ({{ $threads->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $type === 'posts' ? 'active' : '' }}"
                                        id="posts-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#posts"
                                        type="button"
                                        role="tab">
                                    <i class="fas fa-comment me-1"></i>
                                    {{ __('search.posts') }} ({{ $posts->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $type === 'users' ? 'active' : '' }}"
                                        id="users-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#users"
                                        type="button"
                                        role="tab">
                                    <i class="fas fa-users me-1"></i>
                                    {{ __('search.users') }} ({{ $users->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $type === 'products' ? 'active' : '' }}"
                                        id="products-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#products"
                                        type="button"
                                        role="tab">
                                    <i class="fas fa-shopping-cart me-1"></i>
                                    {{ __('search.products') }} ({{ $products->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $type === 'showcases' ? 'active' : '' }}"
                                        id="showcases-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#showcases"
                                        type="button"
                                        role="tab">
                                    <i class="fas fa-trophy me-1"></i>
                                    {{ __('search.showcases') }} ({{ $showcases->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $type === 'documentation' ? 'active' : '' }}"
                                        id="documentation-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#documentation"
                                        type="button"
                                        role="tab">
                                    <i class="fas fa-book me-1"></i>
                                    {{ __('search.documentation') }} ({{ $documentation->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $type === 'materials' ? 'active' : '' }}"
                                        id="materials-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#materials"
                                        type="button"
                                        role="tab">
                                    <i class="fas fa-atom me-1"></i>
                                    {{ __('search.materials') }} ({{ $materials->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $type === 'cad_files' ? 'active' : '' }}"
                                        id="cad-files-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#cad-files"
                                        type="button"
                                        role="tab">
                                    <i class="fas fa-file-alt me-1"></i>
                                    {{ __('search.cad_files') }} ({{ $cadFiles->count() }})
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="searchTabsContent">
                            <!-- Threads Tab -->
                            <div class="tab-pane fade {{ $type === 'all' || $type === 'threads' ? 'show active' : '' }}"
                                 id="threads"
                                 role="tabpanel">
                                @if($threads->count() > 0)
                                    @foreach($threads as $thread)
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $thread->user->getAvatarUrl() }}"
                                                             alt="{{ $thread->user->name }}"
                                                             class="rounded-circle"
                                                             width="40"
                                                             height="40">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">
                                                            <a href="{{ route('threads.show', $thread) }}"
                                                               class="text-decoration-none">
                                                                {{ $thread->title }}
                                                            </a>
                                                        </h6>
                                                        <p class="text-muted mb-2">
                                                            {{ Str::limit(strip_tags($thread->content), 200) }}
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-muted small">
                                                                <i class="fas fa-user me-1"></i>
                                                                {{ $thread->user->name }}
                                                                <span class="mx-2">•</span>
                                                                <i class="fas fa-folder me-1"></i>
                                                                <a href="{{ route('forums.show', $thread->forum) }}"
                                                                   class="text-muted">
                                                                    {{ $thread->forum->name }}
                                                                </a>
                                                                <span class="mx-2">•</span>
                                                                <i class="fas fa-clock me-1"></i>
                                                                {{ $thread->created_at->diffForHumans() }}
                                                            </div>
                                                            <div class="text-muted small">
                                                                <i class="fas fa-eye me-1"></i>
                                                                {{ $thread->views_count ?? 0 }}
                                                                <span class="mx-2">•</span>
                                                                <i class="fas fa-comments me-1"></i>
                                                                {{ $thread->replies_count ?? 0 }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">{{ __('search.no_threads_found') }}</h5>
                                        <p class="text-muted">{{ __('search.try_different_keywords') }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Posts Tab -->
                            <div class="tab-pane fade {{ $type === 'posts' ? 'show active' : '' }}"
                                 id="posts"
                                 role="tabpanel">
                                @if($posts->count() > 0)
                                    @foreach($posts as $post)
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $post->user->getAvatarUrl() }}"
                                                             alt="{{ $post->user->name }}"
                                                             class="rounded-circle"
                                                             width="40"
                                                             height="40">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="mb-2">
                                                            {{ Str::limit(strip_tags($post->content), 300) }}
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-muted small">
                                                                <i class="fas fa-user me-1"></i>
                                                                {{ $post->user->name }}
                                                                <span class="mx-2">•</span>
                                                                <i class="fas fa-comments me-1"></i>
                                                                <a href="{{ route('threads.show', $post->thread) }}"
                                                                   class="text-muted">
                                                                    {{ $post->thread->title }}
                                                                </a>
                                                                <span class="mx-2">•</span>
                                                                <i class="fas fa-clock me-1"></i>
                                                                {{ $post->created_at->diffForHumans() }}
                                                            </div>
                                                            <a href="{{ route('threads.show', $post->thread_id) }}#post-{{ $post->id }}"
                                                               class="btn btn-sm btn-outline-primary">
                                                                {{ __('search.view_post') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">{{ __('search.no_posts_found') }}</h5>
                                        <p class="text-muted">{{ __('search.try_different_keywords') }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Users Tab -->
                            <div class="tab-pane fade {{ $type === 'users' ? 'show active' : '' }}"
                                 id="users"
                                 role="tabpanel">
                                @if($users->count() > 0)
                                    <div class="row">
                                        @foreach($users as $user)
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $user->getAvatarUrl() }}"
                                                                 alt="{{ $user->name }}"
                                                                 class="rounded-circle me-3"
                                                                 width="50"
                                                                 height="50">
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-1">
                                                                    <a href="{{ route('users.show', $user) }}"
                                                                       class="text-decoration-none">
                                                                        {{ $user->name }}
                                                                    </a>
                                                                </h6>
                                                                <p class="text-muted mb-1 small">
                                                                    @{{ $user->username }}
                                                                </p>
                                                                @if($user->bio)
                                                                    <p class="text-muted mb-0 small">
                                                                        {{ Str::limit($user->bio, 100) }}
                                                                    </p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">{{ __('search.no_users_found') }}</h5>
                                        <p class="text-muted">{{ __('search.try_different_keywords') }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Products Tab -->
                            <div class="tab-pane fade {{ $type === 'products' ? 'show active' : '' }}"
                                 id="products"
                                 role="tabpanel">
                                @if($products->count() > 0)
                                    <div class="row">
                                        @foreach($products as $product)
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0">
                                                                <img src="{{ $product->getFirstImageUrl() }}"
                                                                     alt="{{ $product->name }}"
                                                                     class="rounded"
                                                                     width="80"
                                                                     height="80">
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-2">
                                                                    <a href="{{ route('marketplace.products.show', $product) }}"
                                                                       class="text-decoration-none">
                                                                        {{ $product->name }}
                                                                    </a>
                                                                </h6>
                                                                <p class="text-muted small mb-2">
                                                                    {{ Str::limit(strip_tags($product->description), 100) }}
                                                                </p>
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-muted small">
                                                                        <i class="fas fa-tag me-1"></i>
                                                                        {{ number_format($product->price) }} VNĐ
                                                                        <span class="mx-2">•</span>
                                                                        <i class="fas fa-store me-1"></i>
                                                                        {{ $product->seller->name ?? 'N/A' }}
                                                                    </div>
                                                                    <span class="badge bg-primary">{{ $product->type }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">{{ __('search.no_products_found') }}</h5>
                                        <p class="text-muted">{{ __('search.try_different_keywords') }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Showcases Tab -->
                            <div class="tab-pane fade {{ $type === 'showcases' ? 'show active' : '' }}"
                                 id="showcases"
                                 role="tabpanel">
                                @if($showcases->count() > 0)
                                    <div class="row">
                                        @foreach($showcases as $showcase)
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0">
                                                                <img src="{{ $showcase->getCoverImageUrl() }}"
                                                                     alt="{{ $showcase->title }}"
                                                                     class="rounded"
                                                                     width="80"
                                                                     height="80">
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-2">
                                                                    <a href="{{ route('showcase.show', $showcase) }}"
                                                                       class="text-decoration-none">
                                                                        {{ $showcase->title }}
                                                                    </a>
                                                                </h6>
                                                                <p class="text-muted small mb-2">
                                                                    {{ Str::limit(strip_tags($showcase->description), 100) }}
                                                                </p>
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-muted small">
                                                                        <i class="fas fa-user me-1"></i>
                                                                        {{ $showcase->user->name }}
                                                                        <span class="mx-2">•</span>
                                                                        <i class="fas fa-eye me-1"></i>
                                                                        {{ $showcase->view_count }}
                                                                        <span class="mx-2">•</span>
                                                                        <i class="fas fa-heart me-1"></i>
                                                                        {{ $showcase->like_count }}
                                                                    </div>
                                                                    <span class="badge bg-success">{{ $showcase->category }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">{{ __('search.no_showcases_found') }}</h5>
                                        <p class="text-muted">{{ __('search.try_different_keywords') }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Documentation Tab -->
                            <div class="tab-pane fade {{ $type === 'documentation' ? 'show active' : '' }}"
                                 id="documentation"
                                 role="tabpanel">
                                @if($documentation->count() > 0)
                                    @foreach($documentation as $doc)
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0">
                                                        <i class="fas fa-file-alt fa-3x text-primary"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-2">
                                                            <a href="{{ route('documentation.show', $doc) }}"
                                                               class="text-decoration-none">
                                                                {{ $doc->title }}
                                                            </a>
                                                        </h6>
                                                        <p class="text-muted mb-2">
                                                            {{ Str::limit(strip_tags($doc->excerpt ?? $doc->content), 200) }}
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-muted small">
                                                                <i class="fas fa-user me-1"></i>
                                                                {{ $doc->author->name ?? 'N/A' }}
                                                                <span class="mx-2">•</span>
                                                                <i class="fas fa-folder me-1"></i>
                                                                {{ $doc->category->name ?? 'N/A' }}
                                                                <span class="mx-2">•</span>
                                                                <i class="fas fa-eye me-1"></i>
                                                                {{ $doc->view_count }}
                                                            </div>
                                                            <span class="badge bg-info">{{ $doc->content_type }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">{{ __('search.no_documentation_found') }}</h5>
                                        <p class="text-muted">{{ __('search.try_different_keywords') }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Materials Tab -->
                            <div class="tab-pane fade {{ $type === 'materials' ? 'show active' : '' }}"
                                 id="materials"
                                 role="tabpanel">
                                @if($materials->count() > 0)
                                    <div class="row">
                                        @foreach($materials as $material)
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="mb-2">
                                                            <a href="{{ route('materials.show', $material) }}"
                                                               class="text-decoration-none">
                                                                {{ $material->name }}
                                                            </a>
                                                        </h6>
                                                        <p class="text-muted small mb-2">
                                                            <strong>{{ $material->code }}</strong> - {{ $material->category }}
                                                        </p>
                                                        <p class="text-muted small mb-2">
                                                            {{ Str::limit(strip_tags($material->description), 100) }}
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-muted small">
                                                                <i class="fas fa-weight-hanging me-1"></i>
                                                                {{ $material->density }} kg/m³
                                                                @if($material->yield_strength)
                                                                    <span class="mx-2">•</span>
                                                                    <i class="fas fa-bolt me-1"></i>
                                                                    {{ $material->yield_strength }} MPa
                                                                @endif
                                                            </div>
                                                            <span class="badge bg-warning">{{ $material->material_type }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">{{ __('search.no_materials_found') }}</h5>
                                        <p class="text-muted">{{ __('search.try_different_keywords') }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- CAD Files Tab -->
                            <div class="tab-pane fade {{ $type === 'cad_files' ? 'show active' : '' }}"
                                 id="cad-files"
                                 role="tabpanel">
                                @if($cadFiles->count() > 0)
                                    <div class="row">
                                        @foreach($cadFiles as $cadFile)
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0">
                                                                <i class="fas fa-cube fa-3x text-success"></i>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-2">
                                                                    <a href="{{ route('cad-files.show', $cadFile) }}"
                                                                       class="text-decoration-none">
                                                                        {{ $cadFile->name }}
                                                                    </a>
                                                                </h6>
                                                                <p class="text-muted small mb-2">
                                                                    {{ Str::limit(strip_tags($cadFile->description), 100) }}
                                                                </p>
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div class="text-muted small">
                                                                        <i class="fas fa-user me-1"></i>
                                                                        {{ $cadFile->user->name }}
                                                                        <span class="mx-2">•</span>
                                                                        <i class="fas fa-download me-1"></i>
                                                                        {{ $cadFile->download_count }}
                                                                        <span class="mx-2">•</span>
                                                                        <i class="fas fa-file me-1"></i>
                                                                        {{ $cadFile->file_size_human }}
                                                                    </div>
                                                                    <span class="badge bg-secondary">{{ $cadFile->cad_software }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">{{ __('search.no_cad_files_found') }}</h5>
                                        <p class="text-muted">{{ __('search.try_different_keywords') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- No Search Query -->
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted">{{ __('search.enter_search_terms') }}</h4>
                            <p class="text-muted">{{ __('search.search_description') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            @include('components.sidebar')
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus search input
    const searchInput = document.querySelector('input[name="query"]');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }
});
</script>
@endpush
