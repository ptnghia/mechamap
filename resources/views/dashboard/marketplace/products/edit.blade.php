@extends('layouts.user-dashboard')

@section('title', 'Chỉnh sửa sản phẩm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Chỉnh sửa sản phẩm</h1>
                    <p class="text-muted">Cập nhật thông tin sản phẩm của bạn</p>
                </div>
                <a href="{{ route('dashboard.marketplace.seller.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>

            <!-- Product Edit Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin sản phẩm</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.marketplace.seller.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Product Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Short Description -->
                                <div class="mb-3">
                                    <label for="short_description" class="form-label">Mô tả ngắn</label>
                                    <textarea class="form-control @error('short_description') is-invalid @enderror"
                                              id="short_description" name="short_description" rows="3">{{ old('short_description', $product->short_description) }}</textarea>
                                    @error('short_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Full Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả chi tiết <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="6" required>{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Product Type -->
                                <div class="mb-3">
                                    <label for="product_type" class="form-label">Loại sản phẩm <span class="text-danger">*</span></label>
                                    <select class="form-select @error('product_type') is-invalid @enderror" id="product_type" name="product_type" required>
                                        <option value="">Chọn loại sản phẩm</option>
                                        <option value="digital" {{ old('product_type', $product->product_type) === 'digital' ? 'selected' : '' }}>Sản phẩm số</option>
                                        <option value="new_product" {{ old('product_type', $product->product_type) === 'new_product' ? 'selected' : '' }}>Sản phẩm mới</option>
                                        <option value="used_product" {{ old('product_type', $product->product_type) === 'used_product' ? 'selected' : '' }}>Sản phẩm cũ</option>
                                    </select>
                                    @error('product_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Category -->
                                <div class="mb-3">
                                    <label for="product_category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                                    <select class="form-select @error('product_category_id') is-invalid @enderror" id="product_category_id" name="product_category_id" required>
                                        <option value="">Chọn danh mục</option>
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

                                <!-- Seller Type -->
                                <div class="mb-3">
                                    <label for="seller_type" class="form-label">Loại người bán <span class="text-danger">*</span></label>
                                    <select class="form-select @error('seller_type') is-invalid @enderror" id="seller_type" name="seller_type" required>
                                        <option value="">Chọn loại người bán</option>
                                        <option value="supplier" {{ old('seller_type', $product->seller_type) === 'supplier' ? 'selected' : '' }}>Nhà cung cấp</option>
                                        <option value="manufacturer" {{ old('seller_type', $product->seller_type) === 'manufacturer' ? 'selected' : '' }}>Nhà sản xuất</option>
                                        <option value="brand" {{ old('seller_type', $product->seller_type) === 'brand' ? 'selected' : '' }}>Thương hiệu</option>
                                    </select>
                                    @error('seller_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Price -->
                                <div class="mb-3">
                                    <label for="price" class="form-label">Giá bán (VND) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₫</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror"
                                               id="price" name="price" value="{{ old('price', $product->price) }}"
                                               step="1000" min="0" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Sale Price (Optional) -->
                                <div class="mb-3">
                                    <label for="sale_price" class="form-label">Giá khuyến mãi (VND)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₫</span>
                                        <input type="number" class="form-control @error('sale_price') is-invalid @enderror"
                                               id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}"
                                               step="1000" min="0">
                                        @error('sale_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">Để trống nếu không có khuyến mãi</small>
                                </div>
                            </div>
                        </div>

                        <!-- Images Section -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>Hình ảnh sản phẩm</h6>
                                <div class="mb-3">
                                    <label for="featured_image" class="form-label">Ảnh đại diện</label>
                                    @if($product->featured_image)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $product->featured_image) }}" alt="Current featured image" class="img-thumbnail" style="max-width: 200px;">
                                            <p class="text-muted small">Ảnh hiện tại</p>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('featured_image') is-invalid @enderror"
                                           id="featured_image" name="featured_image" accept="image/*">
                                    @error('featured_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Chọn ảnh mới để thay thế ảnh hiện tại</small>
                                </div>

                                <div class="mb-3">
                                    <label for="images" class="form-label">Ảnh bổ sung</label>
                                    @if($product->images && count($product->images) > 0)
                                        <div class="mb-2">
                                            <div class="row">
                                                @foreach($product->images as $image)
                                                    <div class="col-3 mb-2">
                                                        <img src="{{ asset('storage/' . $image) }}" alt="Product image" class="img-thumbnail" style="width: 100%;">
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p class="text-muted small">Ảnh hiện tại</p>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('images') is-invalid @enderror"
                                           id="images" name="images[]" accept="image/*" multiple>
                                    @error('images')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Chọn nhiều ảnh để thay thế ảnh hiện tại</small>
                                </div>
                            </div>
                        </div>

                        <!-- Digital Product Files (shown only for digital products) -->
                        <div class="row mt-4" id="digitalFilesSection" style="display: none;">
                            <div class="col-12">
                                <h6>File sản phẩm số</h6>
                                <div class="mb-3">
                                    <label for="digital_files" class="form-label">Upload file</label>
                                    @if($product->digital_files && count($product->digital_files) > 0)
                                        <div class="mb-2">
                                            <p class="text-muted small">File hiện tại:</p>
                                            <ul class="list-group">
                                                @foreach($product->digital_files as $file)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        {{ $file['name'] ?? 'Unknown file' }}
                                                        <span class="badge bg-secondary">{{ number_format(($file['size'] ?? 0) / 1024, 1) }} KB</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('digital_files') is-invalid @enderror"
                                           id="digital_files" name="digital_files[]" multiple>
                                    @error('digital_files')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Upload file mới để thay thế file hiện tại</small>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Management (shown only for physical products) -->
                        <div class="row mt-4" id="stockSection">
                            <div class="col-12">
                                <h6>Quản lý tồn kho</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stock_quantity" class="form-label">Số lượng tồn kho</label>
                                            <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                                   id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0">
                                            @error('stock_quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="low_stock_threshold" class="form-label">Ngưỡng cảnh báo hết hàng</label>
                                            <input type="number" class="form-control @error('low_stock_threshold') is-invalid @enderror"
                                                   id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" min="0">
                                            @error('low_stock_threshold')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="manage_stock" name="manage_stock" value="1"
                                                   {{ old('manage_stock', $product->manage_stock) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="manage_stock">
                                                Quản lý tồn kho tự động
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="tags" class="form-label">Tags</label>
                                    <input type="text" class="form-control @error('tags') is-invalid @enderror"
                                           id="tags" name="tags" value="{{ old('tags', is_array($product->tags) ? implode(', ', $product->tags) : $product->tags) }}"
                                           placeholder="Nhập tags, phân cách bằng dấu phẩy">
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Ví dụ: cơ khí, máy móc, công nghiệp</small>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Options -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>Tùy chọn bổ sung</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                                                   {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">
                                                Sản phẩm nổi bật
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Kích hoạt sản phẩm
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Trạng thái</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                                                <option value="pending" {{ old('status', $product->status) === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                                <option value="approved" {{ old('status', $product->status) === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('dashboard.marketplace.seller.products.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Hủy
                                    </a>
                                    <div>
                                        <button type="submit" name="status" value="draft" class="btn btn-outline-primary me-2">
                                            <i class="fas fa-save me-2"></i>Lưu bản nháp
                                        </button>
                                        <button type="submit" name="status" value="pending" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-2"></i>Cập nhật & Gửi duyệt
                                        </button>
                                    </div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productTypeSelect = document.getElementById('product_type');
    const digitalFilesSection = document.getElementById('digitalFilesSection');
    const stockSection = document.getElementById('stockSection');

    function toggleSections() {
        const productType = productTypeSelect.value;

        if (productType === 'digital') {
            digitalFilesSection.style.display = 'block';
            stockSection.style.display = 'none';
        } else if (productType === 'new_product' || productType === 'used_product') {
            digitalFilesSection.style.display = 'none';
            stockSection.style.display = 'block';
        } else {
            digitalFilesSection.style.display = 'none';
            stockSection.style.display = 'none';
        }
    }

    productTypeSelect.addEventListener('change', toggleSections);
    toggleSections(); // Initial call

    // Price validation
    const priceInput = document.getElementById('price');
    const salePriceInput = document.getElementById('sale_price');

    if (priceInput && salePriceInput) {
        salePriceInput.addEventListener('input', function() {
            const price = parseFloat(priceInput.value) || 0;
            const salePrice = parseFloat(this.value) || 0;

            if (salePrice >= price && price > 0) {
                this.setCustomValidity('Giá khuyến mãi phải nhỏ hơn giá bán');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Tags input enhancement
    const tagsInput = document.getElementById('tags');
    if (tagsInput) {
        tagsInput.addEventListener('blur', function() {
            // Clean up tags format
            const tags = this.value.split(',').map(tag => tag.trim()).filter(tag => tag.length > 0);
            this.value = tags.join(', ');
        });
    }

    // Form submission handling
    const form = document.getElementById('productForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Show loading state
            const submitButtons = form.querySelectorAll('button[type="submit"]');
            submitButtons.forEach(button => {
                button.disabled = true;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';

                // Restore button after 10 seconds (fallback)
                setTimeout(() => {
                    button.disabled = false;
                    button.innerHTML = originalText;
                }, 10000);
            });
        });
    }
});
</script>
@endpush
