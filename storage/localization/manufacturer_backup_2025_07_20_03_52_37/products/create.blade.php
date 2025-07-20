@extends('layouts.app')

@section('title', 'Thêm Sản phẩm Kỹ thuật - Manufacturer Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-plus-circle text-primary me-2"></i>
                        Thêm Sản phẩm Kỹ thuật
                    </h1>
                    <p class="text-muted mb-0">Tạo file CAD hoặc dịch vụ kỹ thuật mới cho MechaMap Marketplace</p>
                </div>
                <div>
                    <a href="{{ route('manufacturer.products.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i>
                        Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('manufacturer.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="bx bx-info-circle me-2"></i>
                            Thông tin cơ bản
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="product_type" class="form-label">Loại sản phẩm <span class="text-danger">*</span></label>
                                <select class="form-select @error('product_type') is-invalid @enderror"
                                        id="product_type" name="product_type" required>
                                    <option value="">Chọn loại sản phẩm</option>
                                    <option value="digital" {{ old('product_type') === 'digital' ? 'selected' : '' }}>
                                        File CAD/Thiết kế
                                    </option>
                                    <option value="service" {{ old('product_type') === 'service' ? 'selected' : '' }}>
                                        Dịch vụ kỹ thuật
                                    </option>
                                </select>
                                @error('product_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="product_category_id" class="form-label">Danh mục</label>
                                <select class="form-select @error('product_category_id') is-invalid @enderror"
                                        id="product_category_id" name="product_category_id">
                                    <option value="">Chọn danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="short_description" class="form-label">Mô tả ngắn</label>
                                <textarea class="form-control @error('short_description') is-invalid @enderror"
                                          id="short_description" name="short_description" rows="2"
                                          placeholder="Mô tả ngắn gọn về sản phẩm (tối đa 500 ký tự)">{{ old('short_description') }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Mô tả chi tiết <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="6" required
                                          placeholder="Mô tả chi tiết về sản phẩm, tính năng, ứng dụng...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="bx bx-dollar me-2"></i>
                            Giá cả
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Giá bán <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                           id="price" name="price" value="{{ old('price') }}" min="0" step="1000" required>
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="sale_price" class="form-label">Giá khuyến mãi</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('sale_price') is-invalid @enderror"
                                           id="sale_price" name="sale_price" value="{{ old('sale_price') }}" min="0" step="1000">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                                @error('sale_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="bx bx-cog me-2"></i>
                            Thông tin kỹ thuật
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Digital Product Fields -->
                            <div id="digital-fields" style="display: none;">
                                <div class="col-md-6 mb-3">
                                    <label for="file_formats" class="form-label">Định dạng file</label>
                                    <input type="text" class="form-control @error('file_formats') is-invalid @enderror"
                                           id="file_formats" name="file_formats" value="{{ old('file_formats') }}"
                                           placeholder="VD: DWG, STEP, IGES, STL">
                                    @error('file_formats')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="software_compatibility" class="form-label">Phần mềm tương thích</label>
                                    <input type="text" class="form-control @error('software_compatibility') is-invalid @enderror"
                                           id="software_compatibility" name="software_compatibility" value="{{ old('software_compatibility') }}"
                                           placeholder="VD: AutoCAD, SolidWorks, Fusion 360">
                                    @error('software_compatibility')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="digital_files" class="form-label">Upload files CAD</label>
                                    <input type="file" class="form-control @error('digital_files') is-invalid @enderror"
                                           id="digital_files" name="digital_files[]" multiple
                                           accept=".dwg,.dxf,.step,.stp,.iges,.igs,.stl,.pdf,.doc,.docx,.zip,.rar">
                                    <small class="text-muted">
                                        Chọn file CAD (DWG, DXF, STEP, STL, PDF, ZIP... - tối đa 50MB mỗi file)
                                    </small>
                                    @error('digital_files')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div id="digital-files-preview" class="row" style="display: none;"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="material" class="form-label">Vật liệu</label>
                                <input type="text" class="form-control @error('material') is-invalid @enderror"
                                       id="material" name="material" value="{{ old('material') }}"
                                       placeholder="VD: Thép không gỉ 304, Nhôm 6061...">
                                @error('material')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="manufacturing_process" class="form-label">Quy trình sản xuất</label>
                                <input type="text" class="form-control @error('manufacturing_process') is-invalid @enderror"
                                       id="manufacturing_process" name="manufacturing_process" value="{{ old('manufacturing_process') }}"
                                       placeholder="VD: CNC Machining, 3D Printing...">
                                @error('manufacturing_process')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tags" class="form-label">Tags</label>
                                <input type="text" class="form-control @error('tags') is-invalid @enderror"
                                       id="tags" name="tags" value="{{ old('tags') }}"
                                       placeholder="VD: cad, thiết kế, cơ khí (phân cách bằng dấu phẩy)">
                                @error('tags')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Thông số kỹ thuật chi tiết</label>
                                <div id="technical-specs">
                                    <div class="row mb-2">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="technical_specs[0][name]"
                                                   placeholder="Tên thông số" value="{{ old('technical_specs.0.name') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="technical_specs[0][value]"
                                                   placeholder="Giá trị" value="{{ old('technical_specs.0.value') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="technical_specs[0][unit]"
                                                   placeholder="Đơn vị" value="{{ old('technical_specs.0.unit') }}">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSpec(this)">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSpec()">
                                    <i class="bx bx-plus me-1"></i>
                                    Thêm thông số
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="bx bx-image me-2"></i>
                            Hình ảnh sản phẩm
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="images" class="form-label">Upload hình ảnh</label>
                            <input type="file" class="form-control @error('images') is-invalid @enderror"
                                   id="images" name="images[]" multiple accept="image/*">
                            <small class="text-muted">
                                Chọn nhiều hình ảnh (JPG, PNG, GIF - tối đa 2MB mỗi file). Hình đầu tiên sẽ là ảnh đại diện.
                            </small>
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div id="image-preview" class="row" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Actions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="bx bx-save me-2"></i>
                            Hành động
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>
                                Tạo Sản phẩm
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                                <i class="bx bx-file me-1"></i>
                                Lưu bản nháp
                            </button>
                            <a href="{{ route('manufacturer.products.index') }}" class="btn btn-outline-danger">
                                <i class="bx bx-x me-1"></i>
                                Hủy
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Help -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="bx bx-help-circle me-2"></i>
                            Hướng dẫn
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bx bx-check text-success me-2"></i>
                                File CAD: Upload file thiết kế chất lượng cao
                            </li>
                            <li class="mb-2">
                                <i class="bx bx-check text-success me-2"></i>
                                Dịch vụ: Mô tả rõ quy trình và deliverable
                            </li>
                            <li class="mb-2">
                                <i class="bx bx-check text-success me-2"></i>
                                Thông số kỹ thuật chi tiết và chính xác
                            </li>
                            <li class="mb-0">
                                <i class="bx bx-check text-success me-2"></i>
                                Sản phẩm sẽ được duyệt trong 24-48h
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.card {
    border-radius: 0.75rem;
}

.image-preview-item {
    position: relative;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
}

.image-preview-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 0.25rem;
}

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

.remove-item {
    position: absolute;
    top: 0.25rem;
    right: 0.25rem;
    background: rgba(220, 53, 69, 0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@push('scripts')
<script>
let specIndex = 1;

// Toggle digital fields based on product type
document.getElementById('product_type').addEventListener('change', function() {
    const digitalFields = document.getElementById('digital-fields');
    if (this.value === 'digital') {
        digitalFields.style.display = 'block';
    } else {
        digitalFields.style.display = 'none';
    }
});

// Add technical specification
function addSpec() {
    const container = document.getElementById('technical-specs');
    const newSpec = document.createElement('div');
    newSpec.className = 'row mb-2';
    newSpec.innerHTML = `
        <div class="col-md-4">
            <input type="text" class="form-control" name="technical_specs[${specIndex}][name]" placeholder="Tên thông số">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control" name="technical_specs[${specIndex}][value]" placeholder="Giá trị">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" name="technical_specs[${specIndex}][unit]" placeholder="Đơn vị">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSpec(this)">
                <i class="bx bx-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newSpec);
    specIndex++;
}

// Remove technical specification
function removeSpec(button) {
    button.closest('.row').remove();
}

// Digital files preview
document.getElementById('digital_files').addEventListener('change', function(e) {
    const preview = document.getElementById('digital-files-preview');
    preview.innerHTML = '';

    if (e.target.files.length > 0) {
        preview.style.display = 'block';

        Array.from(e.target.files).forEach((file, index) => {
            const fileExtension = file.name.split('.').pop().toLowerCase();
            const fileSize = formatFileSize(file.size);
            const fileIcon = getFileIcon(fileExtension);

            const col = document.createElement('div');
            col.className = 'col-md-6 col-lg-4 mb-3';
            col.innerHTML = `
                <div class="file-preview-item">
                    <div class="text-center">
                        <i class="${fileIcon} file-icon text-primary"></i>
                    </div>
                    <div class="file-name fw-bold">${file.name}</div>
                    <div class="file-size text-muted">${fileSize}</div>
                    <button type="button" class="remove-item" onclick="removeDigitalFile(this, ${index})">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
            `;
            preview.appendChild(col);
        });
    } else {
        preview.style.display = 'none';
    }
});

// Image preview
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';

    if (e.target.files.length > 0) {
        preview.style.display = 'block';

        Array.from(e.target.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-6 col-lg-4 mb-3';
                    col.innerHTML = `
                        <div class="image-preview-item">
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="remove-item" onclick="removeImage(this, ${index})">
                                <i class="bx bx-x"></i>
                            </button>
                            ${index === 0 ? '<small class="text-primary">Ảnh đại diện</small>' : ''}
                        </div>
                    `;
                    preview.appendChild(col);
                };
                reader.readAsDataURL(file);
            }
        });
    } else {
        preview.style.display = 'none';
    }
});

// Helper functions
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function getFileIcon(extension) {
    const icons = {
        'dwg': 'bx bx-file',
        'dxf': 'bx bx-file',
        'step': 'bx bx-cube',
        'stp': 'bx bx-cube',
        'iges': 'bx bx-cube',
        'igs': 'bx bx-cube',
        'stl': 'bx bx-cube',
        'pdf': 'bx bx-file-pdf',
        'doc': 'bx bx-file-doc',
        'docx': 'bx bx-file-doc',
        'zip': 'bx bx-archive',
        'rar': 'bx bx-archive'
    };
    return icons[extension] || 'bx bx-file';
}

// Remove file functions
function removeDigitalFile(button, index) {
    const fileInput = document.getElementById('digital_files');
    const dt = new DataTransfer();

    Array.from(fileInput.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });

    fileInput.files = dt.files;
    button.closest('.col-md-6').remove();

    if (fileInput.files.length === 0) {
        document.getElementById('digital-files-preview').style.display = 'none';
    }
}

function removeImage(button, index) {
    const fileInput = document.getElementById('images');
    const dt = new DataTransfer();

    Array.from(fileInput.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });

    fileInput.files = dt.files;
    button.closest('.col-md-6').remove();

    if (fileInput.files.length === 0) {
        document.getElementById('image-preview').style.display = 'none';
    }
}

// Save as draft
function saveDraft() {
    const form = document.getElementById('productForm');
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = 'draft';
    form.appendChild(statusInput);
    form.submit();
}

// Form validation
document.getElementById('productForm').addEventListener('submit', function(e) {
    const price = parseFloat(document.getElementById('price').value);
    const salePrice = parseFloat(document.getElementById('sale_price').value);

    if (salePrice && salePrice >= price) {
        e.preventDefault();
        alert('Giá khuyến mãi phải nhỏ hơn giá bán thường!');
        return false;
    }

    const productType = document.getElementById('product_type').value;
    const digitalFiles = document.getElementById('digital_files').files;

    if (productType === 'digital' && digitalFiles.length === 0) {
        e.preventDefault();
        alert('Vui lòng upload ít nhất một file CAD cho sản phẩm digital!');
        return false;
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const productType = document.getElementById('product_type').value;
    if (productType === 'digital') {
        document.getElementById('digital-fields').style.display = 'block';
    }
});
</script>
@endpush
