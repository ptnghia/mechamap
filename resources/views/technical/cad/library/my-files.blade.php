@extends('layouts.app')

@section('title', 'My CAD Files - CAD Library')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-2">My CAD Files</h1>
                    <p class="text-muted mb-0">Manage your uploaded CAD files</p>
                </div>
                <a href="{{ route('cad.library.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Upload New File
                </a>
            </div>
            
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                                    <p class="mb-0">Total Files</p>
                                </div>
                                <i class="fas fa-cube fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['approved'] ?? 0 }}</h4>
                                    <p class="mb-0">Approved</p>
                                </div>
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['pending'] ?? 0 }}</h4>
                                    <p class="mb-0">Pending Review</p>
                                </div>
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['total_downloads'] ?? 0 }}</h4>
                                    <p class="mb-0">Total Downloads</p>
                                </div>
                                <i class="fas fa-download fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="pending_review" {{ request('status') == 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="file_type" class="form-label">File Type</label>
                            <select name="file_type" id="file_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="dwg" {{ request('file_type') == 'dwg' ? 'selected' : '' }}>DWG</option>
                                <option value="step" {{ request('file_type') == 'step' ? 'selected' : '' }}>STEP</option>
                                <option value="iges" {{ request('file_type') == 'iges' ? 'selected' : '' }}>IGES</option>
                                <option value="stl" {{ request('file_type') == 'stl' ? 'selected' : '' }}>STL</option>
                                <option value="obj" {{ request('file_type') == 'obj' ? 'selected' : '' }}>OBJ</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Search by title or description..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Files List -->
            <div class="card">
                <div class="card-body">
                    @if($cadFiles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Preview</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Status</th>
                                        <th>Downloads</th>
                                        <th>Rating</th>
                                        <th>Uploaded</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cadFiles as $file)
                                        <tr>
                                            <td>
                                                @if($file->preview_image)
                                                    <img src="{{ Storage::url($file->preview_image) }}" 
                                                         alt="{{ $file->title }}" 
                                                         class="rounded" 
                                                         width="50" height="50" 
                                                         style="object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-cube text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <a href="{{ route('cad.library.show', $file) }}" 
                                                       class="text-decoration-none fw-medium">
                                                        {{ Str::limit($file->title, 40) }}
                                                    </a>
                                                    @if($file->description)
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ Str::limit($file->description, 60) }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ strtoupper($file->file_type) }}</span>
                                            </td>
                                            <td>{{ number_format($file->file_size / 1024 / 1024, 2) }} MB</td>
                                            <td>
                                                @switch($file->status)
                                                    @case('approved')
                                                        <span class="badge bg-success">Approved</span>
                                                        @break
                                                    @case('pending_review')
                                                        <span class="badge bg-warning">Pending Review</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($file->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <i class="fas fa-download text-muted"></i> 
                                                {{ number_format($file->download_count) }}
                                            </td>
                                            <td>
                                                @if($file->average_rating)
                                                    <div class="d-flex align-items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $file->average_rating ? 'text-warning' : 'text-muted' }}" 
                                                               style="font-size: 0.8rem;"></i>
                                                        @endfor
                                                        <small class="ms-1">{{ number_format($file->average_rating, 1) }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">No ratings</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $file->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('cad.library.show', $file) }}" 
                                                       class="btn btn-outline-primary" 
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($file->status == 'pending_review' || $file->status == 'rejected')
                                                        <button class="btn btn-outline-secondary" 
                                                                title="Edit" 
                                                                onclick="editFile({{ $file->id }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    <button class="btn btn-outline-danger" 
                                                            title="Delete" 
                                                            onclick="deleteFile({{ $file->id }}, '{{ $file->title }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($cadFiles->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $cadFiles->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-cube fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No CAD files found</h5>
                            <p class="text-muted mb-4">
                                @if(request()->hasAny(['status', 'file_type', 'search']))
                                    Try adjusting your filters or 
                                    <a href="{{ route('cad.library.my-files') }}">clear all filters</a>.
                                @else
                                    You haven't uploaded any CAD files yet.
                                @endif
                            </p>
                            <a href="{{ route('cad.library.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Upload Your First CAD File
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete "<span id="deleteFileName"></span>"?</p>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteFile(fileId, fileName) {
    document.getElementById('deleteFileName').textContent = fileName;
    document.getElementById('deleteForm').action = `/cad/library/${fileId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function editFile(fileId) {
    // Redirect to edit page (to be implemented)
    window.location.href = `/cad/library/${fileId}/edit`;
}

// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const fileTypeSelect = document.getElementById('file_type');
    
    [statusSelect, fileTypeSelect].forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endsection
