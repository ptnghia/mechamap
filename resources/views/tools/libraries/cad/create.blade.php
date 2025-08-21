@extends('layouts.app')

@section('title', 'Upload CAD File')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="fas fa-cloud-upload-alt text-primary"></i>
                            Upload CAD File
                        </h1>
                        <p class="text-muted mb-0">Share your CAD designs with the MechaMap community</p>
                    </div>
                    <div>
                        <a href="{{ route('cad.library.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Library
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Upload Guidelines -->
                    <div class="alert alert-info mb-4">
                        <h6><i class="fas fa-info-circle"></i> Upload Guidelines</h6>
                        <ul class="mb-0">
                            <li><strong>Supported formats:</strong> DWG, STEP, IGES, STL, OBJ, SLDPRT, IPT, F3D</li>
                            <li><strong>Maximum file size:</strong> 50MB for CAD files, 5MB for preview images</li>
                            <li><strong>Review process:</strong> All uploads are reviewed before being published</li>
                            <li><strong>License:</strong> Choose appropriate license for your design</li>
                        </ul>
                    </div>

                    <!-- Upload Form -->
                    <form action="{{ route('cad.library.store') }}" method="POST" enctype="multipart/form-data" id="cadUploadForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Left Column - Basic Information -->
                            <div class="col-lg-8">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-info-circle text-primary"></i> Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Title -->
                                        <div class="mb-3">
                                            <label for="title" class="form-label">
                                                Title <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('title') is-invalid @enderror" 
                                                   id="title" 
                                                   name="title" 
                                                   value="{{ old('title') }}" 
                                                   placeholder="Enter a descriptive title for your CAD file"
                                                   maxlength="255"
                                                   required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Maximum 255 characters</div>
                                        </div>

                                        <!-- Description -->
                                        <div class="mb-3">
                                            <label for="description" class="form-label">
                                                Description <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" 
                                                      name="description" 
                                                      rows="4" 
                                                      placeholder="Provide a detailed description of your CAD design, its purpose, and key features"
                                                      minlength="50"
                                                      required>{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Minimum 50 characters. Be descriptive to help others understand your design.</div>
                                        </div>

                                        <!-- Category -->
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">
                                                Category <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                                    id="category_id" 
                                                    name="category_id" 
                                                    required>
                                                <option value="">Select a category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" 
                                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Tags -->
                                        <div class="mb-3">
                                            <label for="tags" class="form-label">Tags</label>
                                            <input type="text" 
                                                   class="form-control @error('tags') is-invalid @enderror" 
                                                   id="tags" 
                                                   name="tags" 
                                                   value="{{ old('tags') }}" 
                                                   placeholder="mechanical, automotive, prototype, 3d-printing">
                                            @error('tags')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Separate tags with commas to help others find your design</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Technical Specifications -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-cogs text-warning"></i> Technical Specifications</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Software Used -->
                                            <div class="col-md-6 mb-3">
                                                <label for="software_used" class="form-label">
                                                    Software Used <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select @error('software_used') is-invalid @enderror" 
                                                        id="software_used" 
                                                        name="software_used" 
                                                        required>
                                                    <option value="">Select software</option>
                                                    @foreach($softwareOptions as $software)
                                                        <option value="{{ $software }}" 
                                                                {{ old('software_used') == $software ? 'selected' : '' }}>
                                                            {{ $software }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('software_used')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- File Type -->
                                            <div class="col-md-6 mb-3">
                                                <label for="file_type" class="form-label">
                                                    File Type <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select @error('file_type') is-invalid @enderror" 
                                                        id="file_type" 
                                                        name="file_type" 
                                                        required>
                                                    <option value="">Select file type</option>
                                                    <option value="dwg" {{ old('file_type') == 'dwg' ? 'selected' : '' }}>DWG (AutoCAD)</option>
                                                    <option value="step" {{ old('file_type') == 'step' ? 'selected' : '' }}>STEP</option>
                                                    <option value="iges" {{ old('file_type') == 'iges' ? 'selected' : '' }}>IGES</option>
                                                    <option value="stl" {{ old('file_type') == 'stl' ? 'selected' : '' }}>STL</option>
                                                    <option value="obj" {{ old('file_type') == 'obj' ? 'selected' : '' }}>OBJ</option>
                                                    <option value="sldprt" {{ old('file_type') == 'sldprt' ? 'selected' : '' }}>SLDPRT (SolidWorks)</option>
                                                    <option value="ipt" {{ old('file_type') == 'ipt' ? 'selected' : '' }}>IPT (Inventor)</option>
                                                    <option value="f3d" {{ old('file_type') == 'f3d' ? 'selected' : '' }}>F3D (Fusion 360)</option>
                                                </select>
                                                @error('file_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Technical Specs (Optional) -->
                                        <div class="mb-3">
                                            <label class="form-label">Additional Technical Specifications (Optional)</label>
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <input type="text" 
                                                           class="form-control" 
                                                           name="technical_specs[dimensions]" 
                                                           placeholder="Dimensions (e.g., 100x50x25mm)"
                                                           value="{{ old('technical_specs.dimensions') }}">
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <input type="text" 
                                                           class="form-control" 
                                                           name="technical_specs[material]" 
                                                           placeholder="Material (e.g., Steel, Aluminum)"
                                                           value="{{ old('technical_specs.material') }}">
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <input type="text" 
                                                           class="form-control" 
                                                           name="technical_specs[weight]" 
                                                           placeholder="Weight (e.g., 2.5kg)"
                                                           value="{{ old('technical_specs.weight') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - File Upload & Settings -->
                            <div class="col-lg-4">
                                <!-- File Upload -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-file-upload text-success"></i> File Upload</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- CAD File Upload -->
                                        <div class="mb-3">
                                            <label for="cad_file" class="form-label">
                                                CAD File <span class="text-danger">*</span>
                                            </label>
                                            <div class="upload-area" id="cadFileUpload">
                                                <input type="file" 
                                                       class="form-control @error('cad_file') is-invalid @enderror" 
                                                       id="cad_file" 
                                                       name="cad_file" 
                                                       accept=".dwg,.step,.iges,.stl,.obj,.sldprt,.ipt,.f3d"
                                                       required>
                                                <div class="upload-placeholder text-center p-4">
                                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                                    <p class="mb-1">Drop your CAD file here or click to browse</p>
                                                    <small class="text-muted">Max 50MB • DWG, STEP, IGES, STL, OBJ, SLDPRT, IPT, F3D</small>
                                                </div>
                                            </div>
                                            @error('cad_file')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Preview Image Upload -->
                                        <div class="mb-3">
                                            <label for="preview_image" class="form-label">Preview Image</label>
                                            <div class="upload-area" id="previewImageUpload">
                                                <input type="file" 
                                                       class="form-control @error('preview_image') is-invalid @enderror" 
                                                       id="preview_image" 
                                                       name="preview_image" 
                                                       accept="image/jpeg,image/jpg,image/png">
                                                <div class="upload-placeholder text-center p-3">
                                                    <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                                    <p class="mb-1 small">Optional preview image</p>
                                                    <small class="text-muted">Max 5MB • JPG, PNG</small>
                                                </div>
                                            </div>
                                            @error('preview_image')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- License & Visibility -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-shield-alt text-info"></i> License & Visibility</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- License Type -->
                                        <div class="mb-3">
                                            <label for="license_type" class="form-label">
                                                License Type <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('license_type') is-invalid @enderror" 
                                                    id="license_type" 
                                                    name="license_type" 
                                                    required>
                                                <option value="">Select license</option>
                                                <option value="free" {{ old('license_type') == 'free' ? 'selected' : '' }}>
                                                    Free - Open for all uses
                                                </option>
                                                <option value="commercial" {{ old('license_type') == 'commercial' ? 'selected' : '' }}>
                                                    Commercial - Requires permission
                                                </option>
                                                <option value="educational" {{ old('license_type') == 'educational' ? 'selected' : '' }}>
                                                    Educational - For learning only
                                                </option>
                                            </select>
                                            @error('license_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Public Visibility -->
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="is_public" 
                                                       name="is_public" 
                                                       value="1" 
                                                       {{ old('is_public', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_public">
                                                    Make this file public
                                                </label>
                                            </div>
                                            <small class="text-muted">Public files can be discovered and downloaded by other users</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                        <i class="fas fa-cloud-upload-alt"></i> Upload CAD File
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.upload-area {
    position: relative;
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
}

.upload-area:hover {
    border-color: var(--bs-primary);
    background-color: rgba(var(--bs-primary-rgb), 0.05);
}

.upload-area.dragover {
    border-color: var(--bs-primary);
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}

.upload-area input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.upload-placeholder {
    pointer-events: none;
}

.file-info {
    display: none;
    padding: 1rem;
    background-color: var(--bs-light);
    border-radius: 0.375rem;
}

.file-info.show {
    display: block;
}

.progress {
    height: 4px;
    margin-top: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // File upload handling
    const cadFileInput = document.getElementById('cad_file');
    const previewImageInput = document.getElementById('preview_image');
    const cadUploadArea = document.getElementById('cadFileUpload');
    const previewUploadArea = document.getElementById('previewImageUpload');
    const form = document.getElementById('cadUploadForm');
    const submitBtn = document.getElementById('submitBtn');

    // Handle CAD file upload
    handleFileUpload(cadFileInput, cadUploadArea, {
        maxSize: 50 * 1024 * 1024, // 50MB
        allowedTypes: ['dwg', 'step', 'iges', 'stl', 'obj', 'sldprt', 'ipt', 'f3d']
    });

    // Handle preview image upload
    handleFileUpload(previewImageInput, previewUploadArea, {
        maxSize: 5 * 1024 * 1024, // 5MB
        allowedTypes: ['jpg', 'jpeg', 'png'],
        showPreview: true
    });

    // Form submission handling
    form.addEventListener('submit', function(e) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        submitBtn.disabled = true;
    });

    function handleFileUpload(input, uploadArea, options) {
        const placeholder = uploadArea.querySelector('.upload-placeholder');

        // Drag and drop events
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                handleFileSelection(files[0], uploadArea, options);
            }
        });

        // File input change event
        input.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleFileSelection(e.target.files[0], uploadArea, options);
            }
        });
    }

    function handleFileSelection(file, uploadArea, options) {
        const placeholder = uploadArea.querySelector('.upload-placeholder');
        
        // Validate file size
        if (file.size > options.maxSize) {
            alert(`File size exceeds ${options.maxSize / (1024 * 1024)}MB limit`);
            return;
        }

        // Validate file type
        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (!options.allowedTypes.includes(fileExtension)) {
            alert(`File type .${fileExtension} is not supported`);
            return;
        }

        // Show file info
        const fileSize = (file.size / (1024 * 1024)).toFixed(2);
        placeholder.innerHTML = `
            <div class="text-center">
                <i class="fas fa-file-alt fa-2x text-success mb-2"></i>
                <p class="mb-1 fw-bold">${file.name}</p>
                <small class="text-muted">${fileSize} MB • ${fileExtension.toUpperCase()}</small>
            </div>
        `;

        // Show image preview if it's an image
        if (options.showPreview && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                placeholder.innerHTML = `
                    <div class="text-center">
                        <img src="${e.target.result}" alt="Preview" class="img-fluid mb-2" style="max-height: 100px;">
                        <p class="mb-1 small fw-bold">${file.name}</p>
                        <small class="text-muted">${fileSize} MB</small>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>
@endpush
