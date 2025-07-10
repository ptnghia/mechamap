@extends('layouts.app')

@section('title', 'Thêm Sản phẩm Mới - Supplier Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-plus-circle text-primary me-2"></i>
                        Thêm Sản phẩm Mới
                    </h1>
                    <p class="text-muted mb-0">Tạo sản phẩm vật lý mới cho MechaMap Marketplace</p>
                </div>
                <div>
                    <a href="{{ route('supplier.products.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i>
                        Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('supplier.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
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

                            <div class="col-md-6 mb-3">
                                <label for="material" class="form-label">Vật liệu</label>
                                <input type="text" class="form-control @error('material') is-invalid @enderror"
                                       id="material" name="material" value="{{ old('material') }}"
                                       placeholder="VD: Thép không gỉ 304, Nhôm 6061...">
                                @error('material')
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

                <!-- Pricing & Inventory -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="bx bx-dollar me-2"></i>
                            Giá cả & Tồn kho
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

                            <div class="col-md-6 mb-3">
                                <label for="stock_quantity" class="form-label">Số lượng tồn kho <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                       id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" required>
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quản lý tồn kho</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="manage_stock" name="manage_stock"
                                           value="1" {{ old('manage_stock', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="manage_stock">
                                        Tự động quản lý tồn kho
                                    </label>
                                </div>
                                <small class="text-muted">Hệ thống sẽ tự động giảm tồn kho khi có đơn hàng</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Specifications -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="bx bx-cog me-2"></i>
                            Thông số kỹ thuật
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
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
                                       placeholder="VD: cơ khí, gia công, chính xác (phân cách bằng dấu phẩy)">
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
                            <a href="{{ route('supplier.products.index') }}" class="btn btn-outline-danger">
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
                                Điền đầy đủ thông tin sản phẩm
                            </li>
                            <li class="mb-2">
                                <i class="bx bx-check text-success me-2"></i>
                                Upload hình ảnh chất lượng cao
                            </li>
                            <li class="mb-2">
                                <i class="bx bx-check text-success me-2"></i>
                                Mô tả chi tiết tính năng và ứng dụng
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

.remove-image {
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
                            <button type="button" class="remove-image" onclick="removeImage(this, ${index})">
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

// Remove image preview
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
});
</script>
@endpush
