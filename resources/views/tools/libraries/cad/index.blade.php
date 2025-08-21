@extends('layouts.app')

@section('title', __('cad.library.title'))

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-file-code text-primary me-2"></i>
                        {{ __('cad.library.title') }}
                    </h1>
                    <p class="text-muted mb-0">{{ __('cad.library.description') }}</p>
                </div>
                <div class="d-flex gap-2">
                    @auth
                    <a href="{{ route('tools.cad-library') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-folder me-1"></i>
                        {{ __('cad.library.my_files') }}
                    </a>
                    <a href="{{ route('tools.cad-library') }}" class="btn btn-primary">
                        <i class="fa-solid fa-upload me-1"></i>
                        {{ __('cad.library.upload_file') }}
                    </a>
                    @endauth
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-download me-1"></i>
                            {{ __('cad.library.export') }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('tools.cad-library') }}">
                                <i class="fa-solid fa-file-csv me-2"></i>CSV Format
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('tools.cad-library') }}">
                                <i class="fa-solid fa-file-code me-2"></i>JSON Format
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-file-code text-primary mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title" id="totalFiles">{{ $cadFiles->total() }}</h5>
                    <p class="card-text text-muted">{{ __('cad.library.cad_files') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-download text-success mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title" id="totalDownloads">{{ number_format($totalDownloads ?? 0) }}+</h5>
                    <p class="card-text text-muted">{{ __('cad.library.downloads') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-cube text-info mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">{{ count($fileTypes) }}+</h5>
                    <p class="card-text text-muted">{{ __('cad.library.file_types') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-users text-warning mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">{{ $totalContributors ?? 0 }}+</h5>
                    <p class="card-text text-muted">{{ __('cad.library.contributors') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('tools.cad-library') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">{{ __('cad.library.search_cad_files') }}</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}"
                                   placeholder="{{ __('cad.library.search_placeholder') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="category" class="form-label">{{ __('cad.library.category') }}</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">{{ __('cad.library.all_categories') }}</option>
                                @foreach($categories as $category)
                                <option value="{{ is_array($category) ? $category['id'] : $category }}" {{ request('category') == (is_array($category) ? $category['id'] : $category) ? 'selected' : '' }}>
                                    {{ is_array($category) ? $category['name'] : ucfirst($category) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="file_type" class="form-label">{{ __('cad.library.file_type') }}</label>
                            <select class="form-select" id="file_type" name="file_type">
                                <option value="">{{ __('cad.library.all_types') }}</option>
                                @foreach($fileTypes as $type)
                                <option value="{{ $type }}" {{ request('file_type') == $type ? 'selected' : '' }}>
                                    {{ strtoupper($type) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="software" class="form-label">{{ __('cad.library.software') }}</label>
                            <select class="form-select" id="software" name="software">
                                <option value="">{{ __('cad.library.all_software') }}</option>
                                @foreach($softwareOptions as $software)
                                <option value="{{ $software }}" {{ request('software') == $software ? 'selected' : '' }}>
                                    {{ $software }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="sort" class="form-label">{{ __('cad.library.sort_by') }}</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>{{ __('cad.library.newest') }}</option>
                                <option value="download_count" {{ request('sort') == 'download_count' ? 'selected' : '' }}>{{ __('cad.library.most_downloaded') }}</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>{{ __('cad.library.highest_rated') }}</option>
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>{{ __('cad.library.name_az') }}</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                                <a href="{{ route('tools.cad-library') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- CAD Files Grid -->
    <div class="row">
        @forelse($cadFiles ?? [] as $file)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 cad-file-card">
                @if($file->cover_image ?? false)
                <img src="{{ asset($file->cover_image) }}" class="card-img-top" alt="{{ $file->title }}" style="height: 200px; object-fit: cover;">
                @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="fa-solid fa-file-code text-muted" style="font-size: 3rem;"></i>
                </div>
                @endif

                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="badge bg-primary">{{ strtoupper($file->file_type ?? 'CAD') }}</span>
                    <span class="badge bg-info">{{ ucfirst($file->complexity_level ?? 'intermediate') }}</span>
                </div>

                <div class="card-body">
                    <h6 class="card-title">{{ $file->title ?? 'Sample CAD File' }}</h6>
                    <p class="card-text text-muted small">
                        {{ Str::limit($file->description ?? 'Professional CAD file for mechanical engineering projects', 100) }}
                    </p>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">{{ __('cad.library.views') }}</small>
                            <div class="fw-medium">{{ $file->view_count ?? 0 }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('cad.library.downloads') }}</small>
                            <div class="fw-medium">{{ $file->download_count ?? 0 }}</div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">{{ __('cad.library.rating') }}</small>
                            <div class="fw-medium">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= ($file->rating ?? 4.5) ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <small class="text-muted">({{ $file->rating ?? 4.5 }})</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('cad.library.category') }}</small>
                            <div class="fw-medium">
                                <span class="badge bg-secondary">
                                    {{ $file->category->name ?? ucfirst($file->industry_application ?? 'General') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($file->file_attachments ?? false)
                    <div class="mb-2">
                        <small class="text-muted">{{ __('cad.library.files') }}:</small><br>
                        @foreach((is_array($file->file_attachments) ? $file->file_attachments : []) as $attachment)
                        <span class="badge bg-light text-dark me-1">{{ basename($attachment) }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            {{ __('cad.library.by') }} {{ $file->user->name ?? 'MechaMap User' }}
                        </small>
                        <div class="d-flex gap-1">
                            <a href="{{ route('showcase.show', $file->showcase_slug ?: $file->showcase_id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye me-1"></i>
                                {{ __('cad.library.view') }}
                            </a>
                            @auth
                            <a href="{{ route('showcase.show', $file->showcase_slug ?: $file->showcase_id) }}" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-download me-1"></i>
                                {{ __('cad.library.download') }}
                            </a>
                            @else
                            <a href="{{ route('login') }}" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-sign-in-alt me-1"></i>
                                {{ __('cad.library.login') }}
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <!-- Empty State -->
        <div class="col-12">
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fa-solid fa-search text-muted" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-muted mb-3">{{ __('cad.library.no_results') }}</h4>
                <p class="text-muted mb-4">
                    {{ __('cad.library.no_results_description') }}
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('tools.cad-library') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-refresh me-1"></i>
                        {{ __('cad.library.clear_filters') }}
                    </a>
                    <a href="{{ route('showcase.index') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-1"></i>
                        {{ __('cad.library.browse_showcases') }}
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Popular Software Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-star me-2"></i>
                        {{ __('cad.library.popular_cad_software') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach(['AutoCAD', 'SolidWorks', 'Fusion 360', 'Inventor'] as $software)
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="bg-light rounded p-3 mb-2">
                                    <i class="fa-solid fa-cube text-primary" style="font-size: 2rem;"></i>
                                </div>
                                <h6 class="mb-1">{{ $software }}</h6>
                                <small class="text-muted">{{ rand(5, 15) }} {{ __('cad.library.files_available') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load stats from API
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("tools.cad-library") }}')
        .then(response => response.json())
        .then(data => {
            if (data.total_files) {
                document.getElementById('totalFiles').textContent = data.total_files;
            }
            if (data.total_downloads) {
                document.getElementById('totalDownloads').textContent = data.total_downloads.toLocaleString();
            }
        })
        .catch(error => console.log('Stats loading failed:', error));
});

function viewCADFile(id) {
    // Implement view functionality
    alert('CAD file viewer will be implemented with 3D preview capabilities');
}

function downloadCADFile(id) {
    // Implement download functionality
    alert('Download will be available after user authentication');
}
</script>

<style>
.cad-file-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.cad-file-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
@endsection
