@extends('layouts.user-dashboard')

@section('title', __('marketplace.digital_products.edit_title'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    <i class="fas fa-edit me-2 text-primary"></i>
                    {{ __('marketplace.digital_products.edit_title') }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">{{ __('common.dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.marketplace.seller.products.index') }}">{{ __('marketplace.products.title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('marketplace.digital_products.edit') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Info Alert -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm mb-4">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle fs-4"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="alert-heading">{{ __('marketplace.digital_products.edit_info_title') }}</h5>
                        <p class="mb-0">{{ __('marketplace.digital_products.edit_info_description') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('dashboard.marketplace.seller.products.digital.update', $product) }}" method="POST" enctype="multipart/form-data" id="digitalProductEditForm">
        @csrf
        @method('PUT')

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
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                    <div class="form-text">{{ __('marketplace.digital_products.name_help') }}</div>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="short_description" class="form-label">{{ __('marketplace.products.short_description') }}</label>
                                    <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="2" maxlength="500">{{ old('short_description', $product->short_description) }}</textarea>
                                    <div class="form-text">{{ __('marketplace.products.short_description_help') }}</div>
                                    @error('short_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">{{ __('marketplace.products.description') }} <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description', $product->description) }}</textarea>
                                    <div class="form-text">{{ __('marketplace.digital_products.description_help') }}</div>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Digital Files -->
                @if($product->digital_files && count($product->digital_files) > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-archive me-2"></i>
                            {{ __('marketplace.digital_products.current_files') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row" id="currentFilesList">
                            @foreach($product->digital_files as $index => $file)
                            <div class="col-md-6 mb-3" data-file-index="{{ $index }}">
                                <div class="border rounded p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-file-alt text-primary fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">{{ $file['original_name'] ?? 'Unknown file' }}</h6>
                                            <small class="text-muted">
                                                {{ isset($file['size']) ? number_format($file['size'] / 1024, 2) . ' KB' : 'Unknown size' }}
                                                @if(isset($file['extension']))
                                                    • {{ strtoupper($file['extension']) }}
                                                @endif
                                            </small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-current-file" data-file-index="{{ $index }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="removed_files" id="removedFiles" value="">
                    </div>
                </div>
                @endif

                <!-- Upload New Digital Files -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cloud-upload-alt me-2"></i>
                            {{ __('marketplace.digital_products.upload_new_files') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="digital_files" class="form-label">{{ __('marketplace.digital_products.upload_files') }}</label>
                            <input type="file" class="form-control @error('digital_files') is-invalid @enderror" id="digital_files" name="digital_files[]" multiple
                                   accept=".dwg,.dxf,.step,.stp,.iges,.igs,.stl,.pdf,.doc,.docx,.zip,.rar,.3dm,.skp,.f3d">
                            <div class="form-text">
                                {{ __('marketplace.digital_products.files_help') }}
                                <br>
                                <strong>{{ __('marketplace.digital_products.supported_formats') }}:</strong> DWG, DXF, STEP, IGES, STL, PDF, DOC, DOCX, ZIP, RAR, 3DM, SKP, F3D
                                <br>
                                <strong>{{ __('marketplace.digital_products.max_size') }}:</strong> 50MB {{ __('marketplace.digital_products.per_file') }}
                            </div>
                            @error('digital_files')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Files Preview -->
                        <div id="filePreview" style="display: none;">
                            <h6>{{ __('marketplace.digital_products.selected_files') }}:</h6>
                            <div class="row" id="fileList"></div>
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
                                    <input type="text" class="form-control @error('file_formats') is-invalid @enderror" id="file_formats" name="file_formats"
                                           value="{{ old('file_formats', is_array($product->file_formats) ? implode(', ', $product->file_formats) : $product->file_formats) }}">
                                    <div class="form-text">{{ __('marketplace.digital_products.file_formats_help') }}</div>
                                    @error('file_formats')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="software_compatibility" class="form-label">{{ __('marketplace.digital_products.software_compatibility') }}</label>
                                    <input type="text" class="form-control @error('software_compatibility') is-invalid @enderror" id="software_compatibility" name="software_compatibility"
                                           value="{{ old('software_compatibility', is_array($product->software_compatibility) ? implode(', ', $product->software_compatibility) : $product->software_compatibility) }}">
                                    <div class="form-text">{{ __('marketplace.digital_products.software_help') }}</div>
                                    @error('software_compatibility')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="download_limit" class="form-label">{{ __('marketplace.digital_products.download_limit') }}</label>
                                    <input type="number" step="1" min="1" max="1000" class="form-control @error('download_limit') is-invalid @enderror" id="download_limit" name="download_limit"
                                           value="{{ old('download_limit', $product->download_limit ?? 10) }}">
                                    <div class="form-text">{{ __('marketplace.digital_products.download_limit_help') }}</div>
                                    @error('download_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                        <!-- Current Featured Image -->
                        @if($product->featured_image)
                        <div class="mb-3">
                            <label class="form-label">{{ __('marketplace.products.current_featured_image') }}</label>
                            <div class="border rounded p-2 d-inline-block">
                                <img src="{{ Storage::url($product->featured_image) }}" alt="Featured Image" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                <div class="mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remove_featured_image" id="removeFeaturedImage">
                                        <label class="form-check-label" for="removeFeaturedImage">
                                            {{ __('marketplace.products.remove_featured_image') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="featured_image" class="form-label">{{ __('marketplace.products.featured_image') }}</label>
                                    <input type="file" class="form-control @error('featured_image') is-invalid @enderror" id="featured_image" name="featured_image" accept="image/*">
                                    <div class="form-text">{{ __('marketplace.products.featured_image_help') }}</div>
                                    @error('featured_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="images" class="form-label">{{ __('marketplace.products.gallery_images') }}</label>
                                    <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" accept="image/*" multiple>
                                    <div class="form-text">{{ __('marketplace.products.gallery_images_help') }}</div>
                                    @error('images')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Current Gallery Images -->
                        @if($product->images && count($product->images) > 0)
                        <div class="mb-3">
                            <label class="form-label">{{ __('marketplace.products.current_gallery_images') }}</label>
                            <div class="row" id="currentGalleryImages">
                                @foreach($product->images as $index => $image)
                                <div class="col-md-3 mb-2" data-image-index="{{ $index }}">
                                    <div class="position-relative">
                                        <img src="{{ Storage::url($image) }}" alt="Gallery Image" class="img-thumbnail w-100" style="height: 120px; object-fit: cover;">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-gallery-image" data-image-index="{{ $index }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="removed_gallery_images" id="removedGalleryImages" value="">
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Product Status -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('marketplace.products.status') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-{{ $product->status === 'active' ? 'success' : ($product->status === 'pending' ? 'warning' : 'secondary') }} fs-6">
                                {{ __('marketplace.products.status_' . $product->status) }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">
                                <strong>{{ __('marketplace.products.created') }}:</strong> {{ $product->created_at->format('d/m/Y H:i') }}<br>
                                <strong>{{ __('marketplace.products.updated') }}:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-dollar-sign me-2"></i>
                            {{ __('marketplace.products.pricing') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="price" class="form-label">{{ __('marketplace.products.price') }} (VND) <span class="text-danger">*</span></label>
                                    <input type="number" step="1" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="sale_price" class="form-label">{{ __('marketplace.products.sale_price') }} (VND)</label>
                                    <input type="number" step="1" min="0" class="form-control @error('sale_price') is-invalid @enderror" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}">
                                    <div class="form-text">{{ __('marketplace.products.sale_price_help') }}</div>
                                    @error('sale_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tags me-2"></i>
                            {{ __('marketplace.products.category') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="product_category_id" class="form-label">{{ __('marketplace.products.select_category') }} <span class="text-danger">*</span></label>
                            <select class="form-select @error('product_category_id') is-invalid @enderror" id="product_category_id" name="product_category_id" required>
                                <option value="">{{ __('marketplace.products.choose_category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('product_category_id', $product->product_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="tags" class="form-label">{{ __('marketplace.products.tags') }}</label>
                                    <input type="text" class="form-control @error('tags') is-invalid @enderror" id="tags" name="tags"
                                           value="{{ old('tags', is_array($product->tags) ? implode(', ', $product->tags) : $product->tags) }}">
                                    <div class="form-text">{{ __('marketplace.products.tags_help') }}</div>
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">{{ __('marketplace.products.meta_title') }}</label>
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}">
                                    @error('meta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">{{ __('marketplace.products.meta_description') }}</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $product->meta_description) }}</textarea>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>
                                {{ __('marketplace.digital_products.update_product') }}
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

.remove-current-file, .remove-gallery-image {
    opacity: 0.7;
    transition: opacity 0.2s;
}

.remove-current-file:hover, .remove-gallery-image:hover {
    opacity: 1;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let removedFiles = [];
    let removedGalleryImages = [];

    // File preview functionality for new files
    const fileInput = document.getElementById('digital_files');
    const filePreview = document.getElementById('filePreview');
    const fileList = document.getElementById('fileList');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            fileList.innerHTML = '';

            if (e.target.files.length > 0) {
                filePreview.style.display = 'block';

                Array.from(e.target.files).forEach((file, index) => {
                    const fileExtension = file.name.split('.').pop().toLowerCase();
                    const fileSize = formatFileSize(file.size);
                    const fileIcon = getFileIcon(fileExtension);

                    const col = document.createElement('div');
                    col.className = 'col-md-6 col-lg-4 mb-3';
                    col.innerHTML = `
                        <div class="file-item">
                            <div class="file-icon">
                                <i class="${fileIcon} text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">${file.name}</div>
                                <div class="text-muted small">${fileSize}</div>
                            </div>
                        </div>
                    `;
                    fileList.appendChild(col);
                });
            } else {
                filePreview.style.display = 'none';
            }
        });
    }

    // Remove current file functionality
    document.querySelectorAll('.remove-current-file').forEach(button => {
        button.addEventListener('click', function() {
            const fileIndex = this.getAttribute('data-file-index');
            const fileElement = document.querySelector(`[data-file-index="${fileIndex}"]`);

            if (confirm('{{ __("marketplace.digital_products.confirm_remove_file") }}')) {
                removedFiles.push(fileIndex);
                document.getElementById('removedFiles').value = JSON.stringify(removedFiles);
                fileElement.style.display = 'none';
            }
        });
    });

    // Remove gallery image functionality
    document.querySelectorAll('.remove-gallery-image').forEach(button => {
        button.addEventListener('click', function() {
            const imageIndex = this.getAttribute('data-image-index');
            const imageElement = document.querySelector(`[data-image-index="${imageIndex}"]`);

            if (confirm('{{ __("marketplace.products.confirm_remove_image") }}')) {
                removedGalleryImages.push(imageIndex);
                document.getElementById('removedGalleryImages').value = JSON.stringify(removedGalleryImages);
                imageElement.style.display = 'none';
            }
        });
    });

    // Auto-calculate sale price validation
    const priceInput = document.getElementById('price');
    const salePriceInput = document.getElementById('sale_price');

    function validateSalePrice() {
        const price = parseFloat(priceInput.value) || 0;
        const salePrice = parseFloat(salePriceInput.value) || 0;

        if (salePrice > 0 && salePrice >= price) {
            salePriceInput.setCustomValidity('{{ __("marketplace.products.sale_price_validation") }}');
        } else {
            salePriceInput.setCustomValidity('');
        }
    }

    if (priceInput && salePriceInput) {
        priceInput.addEventListener('input', validateSalePrice);
        salePriceInput.addEventListener('input', validateSalePrice);
    }

    // Helper functions
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function getFileIcon(extension) {
        const iconMap = {
            'pdf': 'fas fa-file-pdf',
            'doc': 'fas fa-file-word',
            'docx': 'fas fa-file-word',
            'dwg': 'fas fa-file-code',
            'dxf': 'fas fa-file-code',
            'step': 'fas fa-cube',
            'stp': 'fas fa-cube',
            'iges': 'fas fa-cube',
            'igs': 'fas fa-cube',
            'stl': 'fas fa-cube',
            'zip': 'fas fa-file-archive',
            'rar': 'fas fa-file-archive',
            '3dm': 'fas fa-cube',
            'skp': 'fas fa-cube',
            'f3d': 'fas fa-cube'
        };
        return iconMap[extension] || 'fas fa-file';
    }
});
</script>
@endpush

@endsection
