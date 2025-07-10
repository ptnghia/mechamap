@extends('admin.layouts.dason')

@section('title', 'Tạo Sản Phẩm Mới')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Tạo Sản Phẩm Mới</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketplace.products.index') }}">Sản Phẩm</a></li>
                    <li class="breadcrumb-item active">Tạo Mới</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('admin.marketplace.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
    @csrf

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông Tin Cơ Bản</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên Sản Phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_type" class="form-label">Loại Sản Phẩm <span class="text-danger">*</span></label>
                                <select class="form-select @error('product_type') is-invalid @enderror"
                                        id="product_type" name="product_type" required>
                                    <option value="">Chọn loại sản phẩm</option>
                                    <option value="digital" {{ old('product_type') == 'digital' ? 'selected' : '' }}>Sản phẩm kỹ thuật số</option>
                                    <option value="new_product" {{ old('product_type') == 'new_product' ? 'selected' : '' }}>Sản phẩm mới</option>
                                    <option value="used_product" {{ old('product_type') == 'used_product' ? 'selected' : '' }}>Sản phẩm cũ</option>
                                </select>
                                @error('product_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="seller_type" class="form-label">Loại Người Bán <span class="text-danger">*</span></label>
                                <select class="form-select @error('seller_type') is-invalid @enderror"
                                        id="seller_type" name="seller_type" required>
                                    <option value="">Chọn loại người bán</option>
                                    <option value="supplier" {{ old('seller_type') == 'supplier' ? 'selected' : '' }}>Nhà Cung Cấp</option>
                                    <option value="manufacturer" {{ old('seller_type') == 'manufacturer' ? 'selected' : '' }}>Nhà Sản Xuất</option>
                                    <option value="brand" {{ old('seller_type') == 'brand' ? 'selected' : '' }}>Thương Hiệu</option>
                                </select>
                                @error('seller_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="short_description" class="form-label">Mô Tả Ngắn</label>
                                <textarea class="form-control @error('short_description') is-invalid @enderror"
                                          id="short_description" name="short_description" rows="3">{{ old('short_description') }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">Mô Tả Chi Tiết <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Digital Files Section (Only for digital products) -->
            <div class="card" id="digitalFilesSection" style="display: none;">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-file-download me-2"></i>
                        Tệp Kỹ Thuật Số
                    </h4>
                    <p class="card-title-desc">Upload các file CAD, tài liệu kỹ thuật cho sản phẩm</p>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="digital_files" class="form-label">Chọn Tệp <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('digital_files') is-invalid @enderror"
                               id="digital_files" name="digital_files[]" multiple
                               accept=".dwg,.dxf,.step,.stp,.iges,.igs,.stl,.pdf,.doc,.docx,.zip,.rar">
                        <div class="form-text">
                            Định dạng hỗ trợ: DWG, DXF, STEP, IGES, STL, PDF, DOC, ZIP, RAR<br>
                            Kích thước tối đa: 50MB mỗi file
                        </div>
                        @error('digital_files')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- File Preview Area -->
                    <div id="filePreviewArea" class="mt-3" style="display: none;">
                        <h6>Tệp Đã Chọn:</h6>
                        <div id="fileList" class="row"></div>
                    </div>

                    <!-- File Formats & Compatibility -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="file_formats" class="form-label">Định Dạng File</label>
                                <input type="text" class="form-control" id="file_formats" name="file_formats"
                                       placeholder="VD: DWG, STEP, PDF" value="{{ old('file_formats') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="software_compatibility" class="form-label">Tương Thích Phần Mềm</label>
                                <input type="text" class="form-control" id="software_compatibility" name="software_compatibility"
                                       placeholder="VD: AutoCAD, SolidWorks, CATIA" value="{{ old('software_compatibility') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Specifications -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông Số Kỹ Thuật</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="material" class="form-label">Vật Liệu</label>
                                <input type="text" class="form-control" id="material" name="material"
                                       value="{{ old('material') }}" placeholder="VD: Thép, Nhôm, Composite">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="manufacturing_process" class="form-label">Quy Trình Sản Xuất</label>
                                <input type="text" class="form-control" id="manufacturing_process" name="manufacturing_process"
                                       value="{{ old('manufacturing_process') }}" placeholder="VD: CNC, In 3D, Đúc">
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
                    <h4 class="card-title">Giá & Kho Hàng</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="price" class="form-label">Giá <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                   id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required>
                            <span class="input-group-text">VNĐ</span>
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="sale_price" class="form-label">Giá Khuyến Mãi</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="sale_price" name="sale_price"
                                   value="{{ old('sale_price') }}" step="0.01" min="0">
                            <span class="input-group-text">VNĐ</span>
                        </div>
                    </div>

                    <div class="mb-3" id="stockSection">
                        <label for="stock_quantity" class="form-label">Số Lượng Kho <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                               id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" required>
                        @error('stock_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check" id="manageStockSection">
                        <input class="form-check-input" type="checkbox" id="manage_stock" name="manage_stock" value="1"
                               {{ old('manage_stock') ? 'checked' : '' }}>
                        <label class="form-check-label" for="manage_stock">
                            Quản lý kho hàng
                        </label>
                    </div>
                </div>
            </div>

            <!-- Category & Seller -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Phân Loại</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="seller_id" class="form-label">Người Bán <span class="text-danger">*</span></label>
                        <select class="form-select @error('seller_id') is-invalid @enderror" id="seller_id" name="seller_id" required>
                            <option value="">Chọn người bán</option>
                            @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" {{ old('seller_id') == $seller->id ? 'selected' : '' }}>
                                    {{ $seller->user->name }} ({{ $seller->business_name }})
                                </option>
                            @endforeach
                        </select>
                        @error('seller_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="product_category_id" class="form-label">Danh Mục</label>
                        <select class="form-select" id="product_category_id" name="product_category_id">
                            <option value="">Chọn danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="industry_category" class="form-label">Ngành Công Nghiệp</label>
                        <input type="text" class="form-control" id="industry_category" name="industry_category"
                               value="{{ old('industry_category') }}" placeholder="VD: Ô tô, Hàng không">
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Trạng Thái</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng Thái <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Bản Nháp</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ Duyệt</option>
                            <option value="approved" {{ old('status', 'approved') == 'approved' ? 'selected' : '' }}>Đã Duyệt</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Tạo Sản Phẩm
                        </button>
                        <a href="{{ route('admin.marketplace.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Quay Lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
.file-preview-item {
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 0.5rem;
    background-color: #f8f9fa;
    position: relative;
}

.file-preview-item .file-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.file-preview-item .file-name {
    font-weight: 500;
    margin-bottom: 0.25rem;
    word-break: break-all;
}

.file-preview-item .file-size {
    color: #6c757d;
    font-size: 0.875rem;
}

.file-preview-item .remove-file {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 0.75rem;
    cursor: pointer;
}

.file-icon-dwg, .file-icon-dxf { color: #0d6efd; }
.file-icon-step, .file-icon-stp, .file-icon-iges, .file-icon-igs { color: #198754; }
.file-icon-stl { color: #fd7e14; }
.file-icon-pdf { color: #dc3545; }
.file-icon-doc, .file-icon-docx { color: #0d6efd; }
.file-icon-zip, .file-icon-rar { color: #6c757d; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle digital files section based on product type
    $('#product_type, #seller_type').on('change', function() {
        toggleDigitalFilesSection();
    });

    // Toggle stock section for digital products
    $('#product_type').on('change', function() {
        toggleStockSection();
    });

    // Digital files upload preview
    $('#digital_files').on('change', function() {
        handleDigitalFilesPreview(this.files);
    });

    // Featured image preview
    $('#featured_image').on('change', function() {
        handleFeaturedImagePreview(this.files[0]);
    });

    // Multiple images preview
    $('#images').on('change', function() {
        handleMultipleImagesPreview(this.files);
    });

    // Initialize on page load
    toggleDigitalFilesSection();
    toggleStockSection();
});

function toggleDigitalFilesSection() {
    const productType = $('#product_type').val();
    const sellerType = $('#seller_type').val();

    if (productType === 'digital' || sellerType === 'manufacturer') {
        $('#digitalFilesSection').show();
        $('#digital_files').prop('required', true);
    } else {
        $('#digitalFilesSection').hide();
        $('#digital_files').prop('required', false);
    }
}

function toggleStockSection() {
    const productType = $('#product_type').val();

    if (productType === 'digital') {
        $('#stockSection').hide();
        $('#manageStockSection').hide();
        $('#stock_quantity').val(999999).prop('required', false);
        $('#manage_stock').prop('checked', false);
    } else {
        $('#stockSection').show();
        $('#manageStockSection').show();
        $('#stock_quantity').val(0).prop('required', true);
    }
}

function handleDigitalFilesPreview(files) {
    const fileList = $('#fileList');
    const previewArea = $('#filePreviewArea');

    fileList.empty();

    if (files.length > 0) {
        previewArea.show();

        Array.from(files).forEach((file, index) => {
            const fileExtension = file.name.split('.').pop().toLowerCase();
            const fileSize = formatFileSize(file.size);
            const fileIcon = getFileIcon(fileExtension);

            const fileItem = $(`
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="file-preview-item">
                        <div class="text-center">
                            <i class="${fileIcon} file-icon-${fileExtension}"></i>
                        </div>
                        <div class="file-name">${file.name}</div>
                        <div class="file-size">${fileSize}</div>
                        <button type="button" class="remove-file" onclick="removeFilePreview(this, ${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `);

            fileList.append(fileItem);
        });

        // Auto-fill file formats
        const formats = Array.from(files).map(file =>
            file.name.split('.').pop().toUpperCase()
        ).filter((value, index, self) => self.indexOf(value) === index);

        $('#file_formats').val(formats.join(', '));
    } else {
        previewArea.hide();
    }
}

function handleFeaturedImagePreview(file) {
    const preview = $('#featuredImagePreview');
    const img = $('#previewFeaturedImg');

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.attr('src', e.target.result);
            preview.show();
        };
        reader.readAsDataURL(file);
    } else {
        preview.hide();
    }
}

function handleMultipleImagesPreview(files) {
    const preview = $('#imagesPreview');
    preview.empty();

    Array.from(files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgItem = $(`
                    <div class="col-md-6 mb-2">
                        <img src="${e.target.result}" alt="Preview ${index + 1}"
                             class="img-fluid rounded" style="max-height: 150px;">
                    </div>
                `);
                preview.append(imgItem);
            };
            reader.readAsDataURL(file);
        }
    });
}

function removeFilePreview(button, index) {
    // Note: This is just visual removal.
    // Actual file removal would require more complex handling
    $(button).closest('.col-md-6').remove();

    // If no files left, hide preview area
    if ($('#fileList').children().length === 0) {
        $('#filePreviewArea').hide();
    }
}

function getFileIcon(extension) {
    const iconMap = {
        'dwg': 'fas fa-drafting-compass',
        'dxf': 'fas fa-drafting-compass',
        'step': 'fas fa-cube',
        'stp': 'fas fa-cube',
        'iges': 'fas fa-cube',
        'igs': 'fas fa-cube',
        'stl': 'fas fa-shapes',
        'pdf': 'fas fa-file-pdf',
        'doc': 'fas fa-file-word',
        'docx': 'fas fa-file-word',
        'zip': 'fas fa-file-archive',
        'rar': 'fas fa-file-archive'
    };

    return iconMap[extension] || 'fas fa-file';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endpush
