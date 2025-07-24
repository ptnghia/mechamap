@extends('layouts.app')

@section('title', 'Thiết lập tài khoản Nhà cung cấp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Thiết lập tài khoản Nhà cung cấp</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="bx bx-store-alt font-size-48 text-primary"></i>
                        <h5 class="mt-3">Chào mừng đến với MechaMap Marketplace!</h5>
                        <p class="text-muted">Để bắt đầu bán hàng, bạn cần hoàn thành thiết lập tài khoản nhà cung cấp.</p>
                    </div>

                    <form action="{{ route('marketplace.seller.setup') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Business Information -->
                        <div class="mb-4">
                            <h6 class="mb-3">Thông tin doanh nghiệp</h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="business_name" class="form-label">Tên doanh nghiệp <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="business_name" name="business_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="business_type" class="form-label">Loại hình kinh doanh <span class="text-danger">*</span></label>
                                        <select class="form-select" id="business_type" name="business_type" required>
                                            <option value="">Chọn loại hình</option>
                                            <option value="individual">Cá nhân</option>
                                            <option value="company">Công ty</option>
                                            <option value="partnership">Hợp danh</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tax_id" class="form-label">Mã số thuế</label>
                                        <input type="text" class="form-control" id="tax_id" name="tax_id">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Địa chỉ kinh doanh <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                        </div>

                        <!-- Store Information -->
                        <div class="mb-4">
                            <h6 class="mb-3">Thông tin cửa hàng</h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="store_name" class="form-label">Tên cửa hàng <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="store_name" name="store_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="store_slug" class="form-label">URL cửa hàng <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">mechamap.test/store/</span>
                                            <input type="text" class="form-control" id="store_slug" name="store_slug" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="store_description" class="form-label">Mô tả cửa hàng</label>
                                <textarea class="form-control" id="store_description" name="store_description" rows="4" placeholder="Giới thiệu về cửa hàng của bạn..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="store_logo" class="form-label">Logo cửa hàng</label>
                                <input type="file" class="form-control" id="store_logo" name="store_logo" accept="image/*">
                                <small class="text-muted">Định dạng: JPG, PNG. Kích thước tối đa: 2MB</small>
                            </div>
                        </div>

                        <!-- Product Categories -->
                        <div class="mb-4">
                            <h6 class="mb-3">Danh mục sản phẩm</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="cat_mechanical" name="categories[]" value="mechanical_parts">
                                        <label class="form-check-label" for="cat_mechanical">Linh kiện cơ khí</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="cat_tools" name="categories[]" value="tools">
                                        <label class="form-check-label" for="cat_tools">Dụng cụ & Thiết bị</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="cat_materials" name="categories[]" value="materials">
                                        <label class="form-check-label" for="cat_materials">Vật liệu</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="cat_electronics" name="categories[]" value="electronics">
                                        <label class="form-check-label" for="cat_electronics">Linh kiện điện tử</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="cat_safety" name="categories[]" value="safety">
                                        <label class="form-check-label" for="cat_safety">Thiết bị an toàn</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="cat_other" name="categories[]" value="other">
                                        <label class="form-check-label" for="cat_other">Khác</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms & Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                                <label class="form-check-label" for="agree_terms">
                                    Tôi đồng ý với <a href="#" target="_blank">Điều khoản dịch vụ</a> và <a href="#" target="_blank">Chính sách bán hàng</a> của MechaMap <span class="text-danger">*</span>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bx bx-check me-2"></i>Hoàn thành thiết lập
                            </button>
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
document.getElementById('store_name').addEventListener('input', function() {
    const storeName = this.value;
    const storeSlug = storeName.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
    document.getElementById('store_slug').value = storeSlug;
});
</script>
@endpush
