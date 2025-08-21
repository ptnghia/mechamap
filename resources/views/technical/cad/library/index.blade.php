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
                    <a href="{{ route('cad.library.my-files') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-folder me-1"></i>
                        My Files
                    </a>
                    <a href="{{ route('cad.library.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-upload me-1"></i>
                        Upload File
                    </a>
                    @endauth
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-download me-1"></i>
                            {{ __('cad.library.export') }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('cad.library.export', ['format' => 'csv']) }}">
                                <i class="fa-solid fa-file-csv me-2"></i>CSV Format
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('cad.library.export', ['format' => 'json']) }}">
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
                    <h5 class="card-title" id="totalFiles">20+</h5>
                    <p class="card-text text-muted">{{ __('cad.library.cad_files') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-download text-success mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title" id="totalDownloads">1,250+</h5>
                    <p class="card-text text-muted">{{ __('cad.library.downloads') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-cube text-info mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">15+</h5>
                    <p class="card-text text-muted">{{ __('cad.library.file_types') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-users text-warning mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">50+</h5>
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
                    <form method="GET" action="{{ route('cad.library.index') }}" class="row g-3">
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
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
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
                                <a href="{{ route('cad.library.index') }}" class="btn btn-outline-secondary">
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
                @if($file->preview_image ?? false)
                <img src="{{ asset('storage/' . $file->preview_image) }}" class="card-img-top" alt="{{ $file->title }}" style="height: 200px; object-fit: cover;">
                @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="fa-solid fa-file-code text-muted" style="font-size: 3rem;"></i>
                </div>
                @endif

                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="badge bg-primary">{{ strtoupper($file->file_type ?? 'CAD') }}</span>
                    <span class="badge bg-success">{{ $file->software_used ?? 'Unknown' }}</span>
                </div>

                <div class="card-body">
                    <h6 class="card-title">{{ $file->title ?? 'Sample CAD File' }}</h6>
                    <p class="card-text text-muted small">
                        {{ Str::limit($file->description ?? 'Professional CAD file for mechanical engineering projects', 100) }}
                    </p>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">{{ __('cad.library.file_size') }}</small>
                            <div class="fw-medium">{{ number_format(($file->file_size ?? 1024000) / 1024, 1) }} KB</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('cad.library.downloads') }}</small>
                            <div class="fw-medium">{{ $file->download_count ?? rand(10, 100) }}</div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">{{ __('cad.library.rating') }}</small>
                            <div class="fw-medium">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= ($file->average_rating ?? 4.5) ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <small class="text-muted">({{ $file->ratings_count ?? rand(5, 25) }})</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('cad.library.license') }}</small>
                            <div class="fw-medium">
                                <span class="badge bg-{{ ($file->license_type ?? 'free') == 'free' ? 'success' : 'warning' }}">
                                    @if(($file->license_type ?? 'free') == 'free')
                                        {{ __('cad.library.free') }}
                                    @elseif(($file->license_type ?? 'free') == 'commercial')
                                        {{ __('cad.library.commercial') }}
                                    @else
                                        {{ __('cad.library.educational') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($file->tags ?? false)
                    <div class="mb-2">
                        @foreach((is_array($file->tags) ? $file->tags : explode(',', $file->tags)) as $tag)
                        <span class="badge bg-light text-dark me-1">{{ trim($tag) }}</span>
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
                            <a href="{{ route('cad.library.show', $file->id ?? 1) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye me-1"></i>
                                {{ __('cad.library.view') }}
                            </a>
                            @auth
                            <a href="{{ route('cad.library.download', $file->id ?? 1) }}" class="btn btn-sm btn-primary">
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
        <!-- Mock CAD Files for Demo -->
        @for($i = 1; $i <= 6; $i++)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 cad-file-card">
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="fa-solid fa-file-code text-muted" style="font-size: 3rem;"></i>
                </div>

                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="badge bg-primary">{{ ['DWG', 'STEP', 'STL', 'SLDPRT'][($i-1) % 4] }}</span>
                    <span class="badge bg-success">{{ ['AutoCAD', 'SolidWorks', 'Fusion 360'][($i-1) % 3] }}</span>
                </div>

                <div class="card-body">
                    <h6 class="card-title">{{ ['Mechanical Gear Assembly', 'Bearing Housing', 'Shaft Coupling', 'Motor Mount', 'Valve Body', 'Pump Impeller'][$i-1] }}</h6>
                    <p class="card-text text-muted small">
                        Professional CAD file for mechanical engineering projects. High-quality 3D model with detailed specifications.
                    </p>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">File Size</small>
                            <div class="fw-medium">{{ rand(500, 5000) }} KB</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Downloads</small>
                            <div class="fw-medium">{{ rand(25, 150) }}</div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Rating</small>
                            <div class="fw-medium">
                                @for($j = 1; $j <= 5; $j++)
                                    <i class="fa-solid fa-star {{ $j <= 4 ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <small class="text-muted">({{ rand(5, 25) }})</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">License</small>
                            <div class="fw-medium">
                                <span class="badge bg-success">Free</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <span class="badge bg-light text-dark me-1">mechanical</span>
                        <span class="badge bg-light text-dark me-1">3d-model</span>
                        <span class="badge bg-light text-dark me-1">engineering</span>
                    </div>
                </div>

                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">by Engineer{{ $i }}</small>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewCADFile({{ $i }})">
                                <i class="fa-solid fa-eye me-1"></i>
                                View
                            </button>
                            @auth
                            <button class="btn btn-sm btn-primary" onclick="downloadCADFile({{ $i }})">
                                <i class="fa-solid fa-download me-1"></i>
                                Download
                            </button>
                            @else
                            <a href="{{ route('login') }}" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-sign-in-alt me-1"></i>
                                Login
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endfor
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
    fetch('{{ route("cad.library.stats") }}')
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
