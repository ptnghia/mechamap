@extends('layouts.user-dashboard')

@section('title', 'Tạo Sản Phẩm Số - Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    <i class="fas fa-file-code me-2 text-primary"></i>
                    {{ __('marketplace.digital_products.create_title') }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">{{ __('common.dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.marketplace.seller.products.index') }}">{{ __('marketplace.products.title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('marketplace.digital_products.create') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ __('common.error') }}!</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Digital Product Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary-subtle">
                                <span class="avatar-title rounded-circle bg-primary">
                                    <i class="fas fa-info text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-1">{{ __('marketplace.digital_products.info_title') }}</h5>
                            <p class="card-text text-muted mb-0">
                                {{ __('marketplace.digital_products.info_description') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <form action="{{ route('dashboard.marketplace.seller.products.digital.store') }}" method="POST" enctype="multipart/form-data" id="digitalProductForm">
        @csrf

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit me-2"></i>
                            {{ __('marketplace.products.basic_info') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('marketplace.products.name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                    <div class="form-text">{{ __('marketplace.digital_products.name_help') }}</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="short_description" class="form-label">{{ __('marketplace.products.short_description') }}</label>
                                    <textarea class="form-control" id="short_description" name="short_description" rows="2" maxlength="500">{{ old('short_description') }}</textarea>
                                    <div class="form-text">{{ __('marketplace.products.short_description_help') }}</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">{{ __('marketplace.products.description') }} <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                                    <div class="form-text">{{ __('marketplace.digital_products.description_help') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Digital Files -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cloud-upload-alt me-2"></i>
                            {{ __('marketplace.digital_products.files') }} <span class="text-danger">*</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="digital_files" class="form-label">{{ __('marketplace.digital_products.upload_files') }}</label>
                            <input type="file" class="form-control" id="digital_files" name="digital_files[]" multiple required
                                   accept=".dwg,.dxf,.step,.stp,.iges,.igs,.stl,.pdf,.doc,.docx,.zip,.rar,.3dm,.skp,.f3d">
                            <div class="form-text">
                                {{ __('marketplace.digital_products.files_help') }}
                                <br><strong>{{ __('marketplace.digital_products.supported_formats') }}:</strong> DWG, DXF, STEP, STP, IGES, IGS, STL, PDF, DOC, DOCX, ZIP, RAR, 3DM, SKP, F3D
                                <br><strong>{{ __('marketplace.digital_products.max_size') }}:</strong> 50MB per file
                            </div>
                        </div>

                        <!-- File Preview Area -->
                        <div id="filePreview" class="mt-3" style="display: none;">
                            <h6>{{ __('marketplace.digital_products.selected_files') }}:</h6>
                            <div id="fileList" class="list-group"></div>
                        </div>
                    </div>
                </div>

                <!-- Technical Specifications -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            {{ __('marketplace.digital_products.technical_specs') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="file_formats" class="form-label">{{ __('marketplace.digital_products.file_formats') }}</label>
                                    <input type="text" class="form-control" id="file_formats_input" placeholder="STEP, DWG, PDF...">
                                    <div class="form-text">{{ __('marketplace.digital_products.file_formats_help') }}</div>
                                    <div id="file_formats_tags" class="mt-2"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="software_compatibility" class="form-label">{{ __('marketplace.digital_products.software_compatibility') }}</label>
                                    <input type="text" class="form-control" id="software_compatibility_input" placeholder="SolidWorks, AutoCAD, Fusion 360...">
                                    <div class="form-text">{{ __('marketplace.digital_products.software_help') }}</div>
                                    <div id="software_compatibility_tags" class="mt-2"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="download_limit" class="form-label">{{ __('marketplace.digital_products.download_limit') }}</label>
                                    <input type="number" class="form-control" id="download_limit" name="download_limit" min="1" max="1000" value="{{ old('download_limit', 10) }}">
                                    <div class="form-text">{{ __('marketplace.digital_products.download_limit_help') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-images me-2"></i>
                            {{ __('marketplace.products.images') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="featured_image" class="form-label">{{ __('marketplace.products.featured_image') }}</label>
                                    <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                                    <div class="form-text">{{ __('marketplace.products.featured_image_help') }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="images" class="form-label">{{ __('marketplace.products.gallery_images') }}</label>
                                    <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                                    <div class="form-text">{{ __('marketplace.products.gallery_images_help') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Pricing -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-dollar-sign me-2"></i>
                            {{ __('marketplace.products.pricing') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="price" class="form-label">{{ __('marketplace.products.price') }} (VND) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="price" name="price" min="0" step="1000" value="{{ old('price') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="sale_price" class="form-label">{{ __('marketplace.products.sale_price') }} (VND)</label>
                            <input type="number" class="form-control" id="sale_price" name="sale_price" min="0" step="1000" value="{{ old('sale_price') }}">
                            <div class="form-text">{{ __('marketplace.products.sale_price_help') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Category -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-folder me-2"></i>
                            {{ __('marketplace.products.category') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="product_category_id" class="form-label">{{ __('marketplace.products.select_category') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="product_category_id" name="product_category_id" required>
                                <option value="">{{ __('marketplace.products.choose_category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SEO & Tags -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-search me-2"></i>
                            {{ __('marketplace.products.seo_tags') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="tags" class="form-label">{{ __('marketplace.products.tags') }}</label>
                            <input type="text" class="form-control" id="tags" name="tags" value="{{ old('tags') }}" placeholder="CAD, 3D Model, Engineering...">
                            <div class="form-text">{{ __('marketplace.products.tags_help') }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="meta_title" class="form-label">{{ __('marketplace.products.meta_title') }}</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title') }}" maxlength="191">
                        </div>

                        <div class="mb-3">
                            <label for="meta_description" class="form-label">{{ __('marketplace.products.meta_description') }}</label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="500">{{ old('meta_description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>
                                {{ __('marketplace.digital_products.create_product') }}
                            </button>
                            <a href="{{ route('dashboard.marketplace.seller.products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                {{ __('common.cancel') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
.file-item {
    display: flex;
    align-items: center;
    padding: 0.5rem;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    margin-bottom: 0.5rem;
}

.file-item .file-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 0.25rem;
    margin-right: 0.75rem;
}

.tag-item {
    display: inline-block;
    background: #e9ecef;
    color: #495057;
    padding: 0.25rem 0.5rem;
    margin: 0.125rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

.tag-item .remove-tag {
    margin-left: 0.5rem;
    cursor: pointer;
    color: #dc3545;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // File preview functionality
    const fileInput = document.getElementById('digital_files');
    const filePreview = document.getElementById('filePreview');
    const fileList = document.getElementById('fileList');

    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);

        if (files.length > 0) {
            filePreview.style.display = 'block';
            fileList.innerHTML = '';

            files.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';

                const fileIcon = getFileIcon(file.name);
                const fileSize = formatFileSize(file.size);

                fileItem.innerHTML = `
                    <div class="file-icon">
                        <i class="${fileIcon}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-medium">${file.name}</div>
                        <small class="text-muted">${fileSize}</small>
                    </div>
                `;

                fileList.appendChild(fileItem);
            });
        } else {
            filePreview.style.display = 'none';
        }
    });

    // Tag input functionality
    setupTagInput('file_formats_input', 'file_formats_tags', 'file_formats');
    setupTagInput('software_compatibility_input', 'software_compatibility_tags', 'software_compatibility');
});

function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    const iconMap = {
        'dwg': 'fas fa-drafting-compass text-primary',
        'dxf': 'fas fa-drafting-compass text-primary',
        'step': 'fas fa-cube text-success',
        'stp': 'fas fa-cube text-success',
        'iges': 'fas fa-cube text-success',
        'igs': 'fas fa-cube text-success',
        'stl': 'fas fa-cube text-info',
        'pdf': 'fas fa-file-pdf text-danger',
        'doc': 'fas fa-file-word text-primary',
        'docx': 'fas fa-file-word text-primary',
        'zip': 'fas fa-file-archive text-warning',
        'rar': 'fas fa-file-archive text-warning',
        '3dm': 'fas fa-cube text-secondary',
        'skp': 'fas fa-cube text-warning',
        'f3d': 'fas fa-cube text-orange'
    };

    return iconMap[ext] || 'fas fa-file text-muted';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function setupTagInput(inputId, tagsContainerId, hiddenInputName) {
    const input = document.getElementById(inputId);
    const tagsContainer = document.getElementById(tagsContainerId);
    let tags = [];

    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addTag(input.value.trim());
            input.value = '';
        }
    });

    input.addEventListener('blur', function() {
        if (input.value.trim()) {
            addTag(input.value.trim());
            input.value = '';
        }
    });

    function addTag(tagText) {
        if (tagText && !tags.includes(tagText)) {
            tags.push(tagText);
            renderTags();
            updateHiddenInput();
        }
    }

    function removeTag(tagText) {
        tags = tags.filter(tag => tag !== tagText);
        renderTags();
        updateHiddenInput();
    }

    function renderTags() {
        tagsContainer.innerHTML = tags.map(tag =>
            `<span class="tag-item">
                ${tag}
                <span class="remove-tag" onclick="removeTag_${hiddenInputName}('${tag}')">&times;</span>
            </span>`
        ).join('');
    }

    function updateHiddenInput() {
        // Create or update hidden inputs
        const existingInputs = document.querySelectorAll(`input[name="${hiddenInputName}[]"]`);
        existingInputs.forEach(input => input.remove());

        tags.forEach(tag => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `${hiddenInputName}[]`;
            hiddenInput.value = tag;
            document.getElementById('digitalProductForm').appendChild(hiddenInput);
        });
    }

    // Make remove function globally accessible
    window[`removeTag_${hiddenInputName}`] = removeTag;
}
</script>
@endpush
