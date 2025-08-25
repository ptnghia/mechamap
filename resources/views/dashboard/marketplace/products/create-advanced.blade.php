@extends('layouts.user-dashboard')

@section('title', 'Tạo sản phẩm chuyên nghiệp')

@push('styles')
<style>
.section-card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    margin-bottom: 1.5rem;
}
.section-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 0.35rem 0.35rem 0 0;
    margin: 0;
}
.section-body {
    padding: 1.5rem;
}
.form-section {
    display: none;
}
.form-section.active {
    display: block;
}
.step-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
}
.step {
    flex: 1;
    text-align: center;
    padding: 0.5rem;
    border-bottom: 3px solid #e3e6f0;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.3s ease;
}
.step.active {
    border-bottom-color: #667eea;
    color: #667eea;
    font-weight: 600;
}
.step.completed {
    border-bottom-color: #28a745;
    color: #28a745;
}
.dynamic-section {
    display: none;
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.spec-input-group {
    margin-bottom: 1rem;
}
.spec-input-group .input-group {
    margin-bottom: 0.5rem;
}
.btn-add-spec {
    background: #f8f9fa;
    border: 1px dashed #dee2e6;
    color: #6c757d;
    transition: all 0.3s ease;
}
.btn-add-spec:hover {
    background: #e9ecef;
    border-color: #adb5bd;
    color: #495057;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        Tạo sản phẩm chuyên nghiệp
                    </h1>
                    <p class="text-muted">Tạo sản phẩm với đầy đủ thông tin kỹ thuật và thương mại</p>
                </div>
                <a href="{{ route('dashboard.marketplace.seller.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" data-step="1">
                    <i class="fas fa-info-circle mb-2"></i><br>
                    Thông tin cơ bản
                </div>
                <div class="step" data-step="2">
                    <i class="fas fa-cogs mb-2"></i><br>
                    Thông số kỹ thuật
                </div>
                <div class="step" data-step="3">
                    <i class="fas fa-dollar-sign mb-2"></i><br>
                    Giá cả & Kho
                </div>
                <div class="step" data-step="4">
                    <i class="fas fa-images mb-2"></i><br>
                    Media & Files
                </div>
                <div class="step" data-step="5">
                    <i class="fas fa-tags mb-2"></i><br>
                    SEO & Marketing
                </div>
            </div>

            <!-- Main Form -->
            <form action="{{ route('dashboard.marketplace.seller.products.store') }}" method="POST" enctype="multipart/form-data" id="advancedProductForm">
                @csrf

                <!-- Step 1: Basic Information -->
                <div class="form-section active" id="step-1">
                    <div class="section-card">
                        <h5 class="section-header">
                            <i class="fas fa-info-circle me-2"></i>
                            Thông tin cơ bản
                        </h5>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            Tên sản phẩm <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name" required
                                               placeholder="Nhập tên sản phẩm chi tiết và mô tả...">
                                        <div class="form-text">Tên sản phẩm nên rõ ràng, mô tả chính xác sản phẩm</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="short_description" class="form-label">Mô tả ngắn</label>
                                        <textarea class="form-control" id="short_description" name="short_description" rows="3"
                                                  placeholder="Mô tả ngắn gọn về sản phẩm, tính năng chính..."></textarea>
                                        <div class="form-text">Mô tả ngắn hiển thị trong danh sách sản phẩm (tối đa 200 ký tự)</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">
                                            Mô tả chi tiết <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="description" name="description" rows="6" required
                                                  placeholder="Mô tả chi tiết về sản phẩm, ứng dụng, lợi ích..."></textarea>
                                        <div class="form-text">Mô tả chi tiết giúp khách hàng hiểu rõ về sản phẩm</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="sku" class="form-label">Mã sản phẩm (SKU)</label>
                                                <input type="text" class="form-control" id="sku" name="sku"
                                                       placeholder="Tự động tạo nếu để trống">
                                                <div class="form-text">Mã định danh duy nhất cho sản phẩm</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="material" class="form-label">Vật liệu chính</label>
                                                <input type="text" class="form-control" id="material" name="material"
                                                       placeholder="VD: Thép carbon, Nhôm 6061, Nhựa ABS...">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="product_type" class="form-label">
                                            Loại sản phẩm <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="product_type" name="product_type" required>
                                            <option value="">Chọn loại sản phẩm</option>
                                            <option value="digital">Sản phẩm số (File kỹ thuật)</option>
                                            <option value="new_product">Sản phẩm mới</option>
                                            <option value="used_product">Sản phẩm đã qua sử dụng</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="product_category_id" class="form-label">
                                            Danh mục <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="product_category_id" name="product_category_id" required>
                                            <option value="">Chọn danh mục</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="industry_category" class="form-label">Ngành công nghiệp</label>
                                        <select class="form-select" id="industry_category" name="industry_category">
                                            <option value="">Chọn ngành công nghiệp</option>
                                            <option value="automotive">Ô tô</option>
                                            <option value="aerospace">Hàng không vũ trụ</option>
                                            <option value="manufacturing">Sản xuất chế tạo</option>
                                            <option value="construction">Xây dựng</option>
                                            <option value="energy">Năng lượng</option>
                                            <option value="marine">Hàng hải</option>
                                            <option value="electronics">Điện tử</option>
                                            <option value="medical">Y tế</option>
                                            <option value="food">Thực phẩm</option>
                                            <option value="textile">Dệt may</option>
                                            <option value="chemical">Hóa chất</option>
                                            <option value="mining">Khai thác</option>
                                            <option value="other">Khác</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="seller_type" class="form-label">Loại người bán</label>
                                        <select class="form-select" id="seller_type" name="seller_type">
                                            <option value="supplier">Nhà cung cấp</option>
                                            <option value="manufacturer">Nhà sản xuất</option>
                                            <option value="brand">Thương hiệu</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="manufacturing_process" class="form-label">Quy trình sản xuất</label>
                                        <input type="text" class="form-control" id="manufacturing_process" name="manufacturing_process"
                                               placeholder="VD: CNC Machining, 3D Printing, Casting...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Technical Specifications -->
                <div class="form-section" id="step-2">
                    <div class="section-card">
                        <h5 class="section-header">
                            <i class="fas fa-cogs me-2"></i>
                            Thông số kỹ thuật
                        </h5>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Thông số cơ bản</h6>

                                    <div id="technicalSpecsContainer">
                                        <div class="spec-input-group">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="technical_specs[0][name]" placeholder="Tên thông số">
                                                <input type="text" class="form-control" name="technical_specs[0][value]" placeholder="Giá trị">
                                                <input type="text" class="form-control" name="technical_specs[0][unit]" placeholder="Đơn vị">
                                                <button type="button" class="btn btn-outline-danger btn-remove-spec">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-add-spec w-100" id="addTechnicalSpec">
                                        <i class="fas fa-plus me-2"></i>Thêm thông số kỹ thuật
                                    </button>

                                    <h6 class="mb-3 mt-4">Tính chất cơ học</h6>

                                    <div id="mechanicalPropsContainer">
                                        <div class="spec-input-group">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="mechanical_properties[0][property]" placeholder="Tính chất">
                                                <input type="text" class="form-control" name="mechanical_properties[0][value]" placeholder="Giá trị">
                                                <input type="text" class="form-control" name="mechanical_properties[0][unit]" placeholder="Đơn vị">
                                                <button type="button" class="btn btn-outline-danger btn-remove-spec">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-add-spec w-100" id="addMechanicalProp">
                                        <i class="fas fa-plus me-2"></i>Thêm tính chất cơ học
                                    </button>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="mb-3">Tiêu chuẩn & Chứng nhận</h6>

                                    <div id="standardsContainer">
                                        <div class="spec-input-group">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="standards_compliance[0][standard]" placeholder="Tiêu chuẩn">
                                                <input type="text" class="form-control" name="standards_compliance[0][certification]" placeholder="Chứng nhận">
                                                <button type="button" class="btn btn-outline-danger btn-remove-spec">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-add-spec w-100" id="addStandard">
                                        <i class="fas fa-plus me-2"></i>Thêm tiêu chuẩn
                                    </button>

                                    <!-- Digital Product Specific -->
                                    <div id="digitalSpecsSection" class="dynamic-section mt-4">
                                        <h6 class="mb-3">Thông tin file số</h6>

                                        <div class="mb-3">
                                            <label for="file_formats" class="form-label">Định dạng file</label>
                                            <input type="text" class="form-control" id="file_formats" name="file_formats"
                                                   placeholder="VD: DWG, STEP, PDF, STL...">
                                            <div class="form-text">Các định dạng file được hỗ trợ, cách nhau bằng dấu phẩy</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="software_compatibility" class="form-label">Phần mềm tương thích</label>
                                            <input type="text" class="form-control" id="software_compatibility" name="software_compatibility"
                                                   placeholder="VD: AutoCAD 2020+, SolidWorks 2019+...">
                                            <div class="form-text">Phần mềm có thể mở file, cách nhau bằng dấu phẩy</div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="file_size_mb" class="form-label">Kích thước file (MB)</label>
                                                    <input type="number" class="form-control" id="file_size_mb" name="file_size_mb"
                                                           step="0.1" min="0" placeholder="0.0">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="download_limit" class="form-label">Giới hạn tải xuống</label>
                                                    <input type="number" class="form-control" id="download_limit" name="download_limit"
                                                           min="0" placeholder="0 = không giới hạn">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Pricing & Inventory -->
                <div class="form-section" id="step-3">
                    <div class="section-card">
                        <h5 class="section-header">
                            <i class="fas fa-dollar-sign me-2"></i>
                            Giá cả & Quản lý kho
                        </h5>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Thông tin giá cả</h6>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="price" class="form-label">
                                                    Giá bán <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text">VND</span>
                                                    <input type="number" class="form-control" id="price" name="price"
                                                           step="1000" min="0" required placeholder="0">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="sale_price" class="form-label">Giá khuyến mãi</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">VND</span>
                                                    <input type="number" class="form-control" id="sale_price" name="sale_price"
                                                           step="1000" min="0" placeholder="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_on_sale" name="is_on_sale" value="1">
                                            <label class="form-check-label" for="is_on_sale">
                                                Đang khuyến mãi
                                            </label>
                                        </div>
                                    </div>

                                    <div id="salePeriodSection" class="dynamic-section">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="sale_starts_at" class="form-label">Bắt đầu khuyến mãi</label>
                                                    <input type="datetime-local" class="form-control" id="sale_starts_at" name="sale_starts_at">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="sale_ends_at" class="form-label">Kết thúc khuyến mãi</label>
                                                    <input type="datetime-local" class="form-control" id="sale_ends_at" name="sale_ends_at">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Stock Management Section -->
                                    <div id="stockSection" class="dynamic-section">
                                        <h6 class="mb-3">Quản lý kho hàng</h6>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="manage_stock" name="manage_stock" value="1" checked>
                                                <label class="form-check-label" for="manage_stock">
                                                    Quản lý tồn kho
                                                </label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="stock_quantity" class="form-label">Số lượng tồn kho</label>
                                                    <input type="number" class="form-control" id="stock_quantity" name="stock_quantity"
                                                           min="0" value="0">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="low_stock_threshold" class="form-label">Ngưỡng cảnh báo</label>
                                                    <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold"
                                                           min="0" value="5">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="in_stock" name="in_stock" value="1" checked>
                                                <label class="form-check-label" for="in_stock">
                                                    Còn hàng
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Digital Files Section -->
                                    <div id="digitalFilesSection" class="dynamic-section">
                                        <h6 class="mb-3">File sản phẩm số</h6>

                                        <div class="mb-3">
                                            <label for="digital_files" class="form-label">Tải lên file</label>
                                            <input type="file" class="form-control" id="digital_files" name="digital_files[]" multiple>
                                            <div class="form-text">File sẽ được gửi cho khách hàng sau khi mua. Hỗ trợ nhiều file.</div>
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Lưu ý:</strong> File sản phẩm số sẽ được mã hóa và bảo vệ. Khách hàng chỉ có thể tải xuống sau khi thanh toán thành công.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Media & Files -->
                <div class="form-section" id="step-4">
                    <div class="section-card">
                        <h5 class="section-header">
                            <i class="fas fa-images me-2"></i>
                            Media & Files
                        </h5>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Hình ảnh sản phẩm</h6>

                                    <div class="mb-3">
                                        <label for="featured_image" class="form-label">
                                            Hình ảnh đại diện <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="featured_image" name="featured_image"
                                               accept="image/*" required>
                                        <div class="form-text">Hình ảnh chính của sản phẩm (tối đa 5MB)</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="images" class="form-label">Hình ảnh bổ sung</label>
                                        <input type="file" class="form-control" id="images" name="images[]"
                                               accept="image/*" multiple>
                                        <div class="form-text">Có thể chọn nhiều hình ảnh (tối đa 10 ảnh, mỗi ảnh 5MB)</div>
                                    </div>

                                    <div class="alert alert-info">
                                        <i class="fas fa-camera me-2"></i>
                                        <strong>Gợi ý:</strong> Sử dụng hình ảnh chất lượng cao, nhiều góc độ để khách hàng có cái nhìn tổng quan về sản phẩm.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="mb-3">Tài liệu đính kèm</h6>

                                    <div class="mb-3">
                                        <label for="attachments" class="form-label">File đính kèm</label>
                                        <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                                        <div class="form-text">Catalog, datasheet, manual... (PDF, DOC, XLS - tối đa 20MB mỗi file)</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="video_url" class="form-label">Video giới thiệu</label>
                                        <input type="url" class="form-control" id="video_url" name="video_url"
                                               placeholder="https://youtube.com/watch?v=...">
                                        <div class="form-text">Link YouTube hoặc Vimeo giới thiệu sản phẩm</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="demo_url" class="form-label">Demo trực tuyến</label>
                                        <input type="url" class="form-control" id="demo_url" name="demo_url"
                                               placeholder="https://...">
                                        <div class="form-text">Link demo hoặc 3D viewer cho sản phẩm</div>
                                    </div>

                                    <div class="alert alert-warning">
                                        <i class="fas fa-shield-alt me-2"></i>
                                        <strong>Bảo mật:</strong> Chỉ upload file từ nguồn tin cậy. Tất cả file sẽ được quét virus trước khi lưu trữ.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5: SEO & Marketing -->
                <div class="form-section" id="step-5">
                    <div class="section-card">
                        <h5 class="section-header">
                            <i class="fas fa-tags me-2"></i>
                            SEO & Marketing
                        </h5>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">SEO Optimization</h6>

                                    <div class="mb-3">
                                        <label for="meta_title" class="form-label">Meta Title</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title"
                                               maxlength="60" placeholder="Tiêu đề SEO (tối đa 60 ký tự)">
                                        <div class="form-text">Tiêu đề hiển thị trên kết quả tìm kiếm Google</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label">Meta Description</label>
                                        <textarea class="form-control" id="meta_description" name="meta_description"
                                                  rows="3" maxlength="160" placeholder="Mô tả SEO (tối đa 160 ký tự)"></textarea>
                                        <div class="form-text">Mô tả hiển thị trên kết quả tìm kiếm Google</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tags" class="form-label">Tags</label>
                                        <input type="text" class="form-control" id="tags" name="tags"
                                               placeholder="tag1, tag2, tag3...">
                                        <div class="form-text">Từ khóa giúp khách hàng tìm kiếm sản phẩm, cách nhau bằng dấu phẩy</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="mb-3">Marketing Settings</h6>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1">
                                            <label class="form-check-label" for="is_featured">
                                                Sản phẩm nổi bật
                                            </label>
                                        </div>
                                        <div class="form-text">Hiển thị trong danh sách sản phẩm nổi bật</div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                            <label class="form-check-label" for="is_active">
                                                Kích hoạt sản phẩm
                                            </label>
                                        </div>
                                        <div class="form-text">Sản phẩm sẽ hiển thị công khai sau khi được duyệt</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label">Trạng thái</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="draft">Bản nháp</option>
                                            <option value="pending" selected>Chờ duyệt</option>
                                        </select>
                                        <div class="form-text">Chọn "Chờ duyệt" để gửi sản phẩm đến admin phê duyệt</div>
                                    </div>

                                    <div class="alert alert-success">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        <strong>Mẹo:</strong> Sản phẩm có thông tin đầy đủ, hình ảnh chất lượng và mô tả chi tiết sẽ có tỷ lệ được duyệt cao hơn.
                                    </div>
                                </div>
                            </div>

                            <!-- Final Review -->
                            <div class="mt-4 p-4 bg-light rounded">
                                <h6 class="mb-3">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Xem lại thông tin
                                </h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Thông tin cơ bản:</strong>
                                        <ul class="list-unstyled mt-2">
                                            <li><i class="fas fa-check text-success me-2"></i>Tên sản phẩm</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Mô tả chi tiết</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Loại sản phẩm</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Danh mục</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Thông số kỹ thuật:</strong>
                                        <ul class="list-unstyled mt-2">
                                            <li><i class="fas fa-check text-success me-2"></i>Thông số cơ bản</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Tính chất cơ học</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Tiêu chuẩn</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Giá cả & Media:</strong>
                                        <ul class="list-unstyled mt-2">
                                            <li><i class="fas fa-check text-success me-2"></i>Giá bán</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Hình ảnh</li>
                                            <li><i class="fas fa-check text-success me-2"></i>SEO</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mb-4">
                    <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                        <i class="fas fa-arrow-left me-2"></i>Trước
                    </button>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-primary" id="nextBtn">
                            Tiếp theo <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                            <i class="fas fa-save me-2"></i>Lưu sản phẩm
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 5;

    // Step navigation
    function showStep(step) {
        // Hide all sections
        document.querySelectorAll('.form-section').forEach(section => {
            section.classList.remove('active');
        });

        // Show current section
        document.getElementById(`step-${step}`).classList.add('active');

        // Update step indicators
        document.querySelectorAll('.step').forEach((stepEl, index) => {
            stepEl.classList.remove('active', 'completed');
            if (index + 1 === step) {
                stepEl.classList.add('active');
            } else if (index + 1 < step) {
                stepEl.classList.add('completed');
            }
        });

        // Update navigation buttons
        document.getElementById('prevBtn').style.display = step > 1 ? 'block' : 'none';
        document.getElementById('nextBtn').style.display = step < totalSteps ? 'block' : 'none';
        document.getElementById('submitBtn').style.display = step === totalSteps ? 'block' : 'none';
    }

    // Next button
    document.getElementById('nextBtn').addEventListener('click', function() {
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
        }
    });

    // Previous button
    document.getElementById('prevBtn').addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });

    // Step indicator clicks
    document.querySelectorAll('.step').forEach((step, index) => {
        step.addEventListener('click', function() {
            currentStep = index + 1;
            showStep(currentStep);
        });
    });

    // Product type change handler
    document.getElementById('product_type').addEventListener('change', function() {
        const productType = this.value;
        const digitalSection = document.getElementById('digitalFilesSection');
        const stockSection = document.getElementById('stockSection');
        const digitalSpecsSection = document.getElementById('digitalSpecsSection');

        if (digitalSection && stockSection) {
            if (productType === 'digital') {
                digitalSection.style.display = 'block';
                digitalSection.classList.add('dynamic-section');
                stockSection.style.display = 'none';
                if (digitalSpecsSection) {
                    digitalSpecsSection.style.display = 'block';
                }
            } else if (productType === 'new_product' || productType === 'used_product') {
                stockSection.style.display = 'block';
                stockSection.classList.add('dynamic-section');
                digitalSection.style.display = 'none';
                if (digitalSpecsSection) {
                    digitalSpecsSection.style.display = 'none';
                }
            } else {
                digitalSection.style.display = 'none';
                stockSection.style.display = 'none';
                if (digitalSpecsSection) {
                    digitalSpecsSection.style.display = 'none';
                }
            }
        }
    });

    // Sale checkbox handler
    document.getElementById('is_on_sale').addEventListener('change', function() {
        const salePeriodSection = document.getElementById('salePeriodSection');
        if (salePeriodSection) {
            salePeriodSection.style.display = this.checked ? 'block' : 'none';
        }
    });

    // Dynamic specification handlers
    let techSpecIndex = 1;
    let mechPropIndex = 1;
    let standardIndex = 1;

    // Add technical specification
    document.getElementById('addTechnicalSpec').addEventListener('click', function() {
        const container = document.getElementById('technicalSpecsContainer');
        const newSpec = document.createElement('div');
        newSpec.className = 'spec-input-group';
        newSpec.innerHTML = `
            <div class="input-group">
                <input type="text" class="form-control" name="technical_specs[${techSpecIndex}][name]" placeholder="Tên thông số">
                <input type="text" class="form-control" name="technical_specs[${techSpecIndex}][value]" placeholder="Giá trị">
                <input type="text" class="form-control" name="technical_specs[${techSpecIndex}][unit]" placeholder="Đơn vị">
                <button type="button" class="btn btn-outline-danger btn-remove-spec">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(newSpec);
        techSpecIndex++;
    });

    // Add mechanical property
    document.getElementById('addMechanicalProp').addEventListener('click', function() {
        const container = document.getElementById('mechanicalPropsContainer');
        const newProp = document.createElement('div');
        newProp.className = 'spec-input-group';
        newProp.innerHTML = `
            <div class="input-group">
                <input type="text" class="form-control" name="mechanical_properties[${mechPropIndex}][property]" placeholder="Tính chất">
                <input type="text" class="form-control" name="mechanical_properties[${mechPropIndex}][value]" placeholder="Giá trị">
                <input type="text" class="form-control" name="mechanical_properties[${mechPropIndex}][unit]" placeholder="Đơn vị">
                <button type="button" class="btn btn-outline-danger btn-remove-spec">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(newProp);
        mechPropIndex++;
    });

    // Add standard
    document.getElementById('addStandard').addEventListener('click', function() {
        const container = document.getElementById('standardsContainer');
        const newStandard = document.createElement('div');
        newStandard.className = 'spec-input-group';
        newStandard.innerHTML = `
            <div class="input-group">
                <input type="text" class="form-control" name="standards_compliance[${standardIndex}][standard]" placeholder="Tiêu chuẩn">
                <input type="text" class="form-control" name="standards_compliance[${standardIndex}][certification]" placeholder="Chứng nhận">
                <button type="button" class="btn btn-outline-danger btn-remove-spec">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(newStandard);
        standardIndex++;
    });

    // Remove specification handler (event delegation)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-spec') || e.target.closest('.btn-remove-spec')) {
            const specGroup = e.target.closest('.spec-input-group');
            if (specGroup) {
                specGroup.remove();
            }
        }
    });

    // Form validation before step change
    function validateCurrentStep() {
        const currentSection = document.querySelector('.form-section.active');
        const requiredFields = currentSection.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    // Override next button to include validation
    document.getElementById('nextBtn').addEventListener('click', function() {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        } else {
            alert('Vui lòng điền đầy đủ thông tin bắt buộc trước khi tiếp tục.');
        }
    });

    // Auto-generate SKU from product name
    document.getElementById('name').addEventListener('input', function() {
        const skuField = document.getElementById('sku');
        if (!skuField.value) {
            const name = this.value.trim();
            if (name) {
                const sku = 'MP-' + name.substring(0, 20).toUpperCase()
                    .replace(/[^A-Z0-9]/g, '')
                    .substring(0, 8) + '-' + Date.now().toString().slice(-4);
                skuField.value = sku;
            }
        }
    });

    // Auto-generate meta title from product name
    document.getElementById('name').addEventListener('input', function() {
        const metaTitleField = document.getElementById('meta_title');
        if (!metaTitleField.value) {
            metaTitleField.value = this.value.substring(0, 60);
        }
    });

    // Character counter for meta fields
    function addCharacterCounter(fieldId, maxLength) {
        const field = document.getElementById(fieldId);
        if (field) {
            const counter = document.createElement('div');
            counter.className = 'form-text text-end';
            counter.innerHTML = `<span id="${fieldId}_counter">0</span>/${maxLength}`;
            field.parentNode.appendChild(counter);

            field.addEventListener('input', function() {
                const count = this.value.length;
                document.getElementById(`${fieldId}_counter`).textContent = count;
                if (count > maxLength) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }
    }

    addCharacterCounter('meta_title', 60);
    addCharacterCounter('meta_description', 160);
});
</script>
@endpush
