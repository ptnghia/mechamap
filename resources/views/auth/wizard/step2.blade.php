@extends('layouts.app')

@section('title', 'Đăng ký tài khoản - Bước 2')

@section('content')
<x-registration-wizard 
    :current-step="$step"
    :total-steps="$totalSteps"
    :progress="$progress"
    title="Đăng ký tài khoản MechaMap"
    subtitle="Bước 2: Thông tin doanh nghiệp"
    next-button-text="Hoàn tất đăng ký"
    :show-back-button="true"
    form-id="step2Form"
    :session-data="$sessionData">

    <form id="step2Form" method="POST" action="{{ route('register.wizard.step2') }}" enctype="multipart/form-data" novalidate>
        @csrf
        
        {{-- Account Type Display --}}
        <div class="account-type-display mb-4">
            <div class="alert alert-primary">
                <i class="fas fa-building me-2"></i>
                <strong>Loại tài khoản:</strong> 
                @switch($accountType)
                    @case('manufacturer')
                        Nhà sản xuất
                        @break
                    @case('supplier')
                        Nhà cung cấp
                        @break
                    @case('brand')
                        Nhãn hàng
                        @break
                    @default
                        Đối tác kinh doanh
                @endswitch
            </div>
        </div>

        {{-- Section: Company Information --}}
        <div class="form-section mb-4">
            <h3 class="section-title">
                <i class="fas fa-building text-primary me-2"></i>
                Thông tin công ty
            </h3>
            <p class="section-description text-muted mb-4">
                Vui lòng cung cấp thông tin chính xác về công ty của bạn để hoàn tất quá trình đăng ký.
            </p>

            {{-- Company Name --}}
            <div class="mb-3">
                <label for="company_name" class="form-label required">
                    <i class="fas fa-building me-1"></i>
                    Tên công ty
                </label>
                <input type="text" 
                       class="form-control @error('company_name') is-invalid @enderror" 
                       id="company_name" 
                       name="company_name" 
                       value="{{ old('company_name', $sessionData['company_name'] ?? '') }}"
                       placeholder="Nhập tên đầy đủ của công ty"
                       required>
                <div class="invalid-feedback">
                    @error('company_name'){{ $message }}@enderror
                </div>
                <small class="form-text text-muted">
                    Tên công ty phải trùng khớp với giấy phép kinh doanh
                </small>
            </div>

            <div class="row">
                {{-- Business License --}}
                <div class="col-md-6 mb-3">
                    <label for="business_license" class="form-label required">
                        <i class="fas fa-certificate me-1"></i>
                        Số giấy phép kinh doanh
                    </label>
                    <input type="text" 
                           class="form-control @error('business_license') is-invalid @enderror" 
                           id="business_license" 
                           name="business_license" 
                           value="{{ old('business_license', $sessionData['business_license'] ?? '') }}"
                           placeholder="Ví dụ: 0123456789-001"
                           required>
                    <div class="invalid-feedback">
                        @error('business_license'){{ $message }}@enderror
                    </div>
                </div>

                {{-- Tax Code --}}
                <div class="col-md-6 mb-3">
                    <label for="tax_code" class="form-label required">
                        <i class="fas fa-hashtag me-1"></i>
                        Mã số thuế
                    </label>
                    <input type="text" 
                           class="form-control @error('tax_code') is-invalid @enderror" 
                           id="tax_code" 
                           name="tax_code" 
                           value="{{ old('tax_code', $sessionData['tax_code'] ?? '') }}"
                           placeholder="Ví dụ: 1234567890"
                           pattern="[0-9]{10,13}"
                           required>
                    <div class="invalid-feedback">
                        @error('tax_code'){{ $message }}@enderror
                    </div>
                    <small class="form-text text-muted">
                        Mã số thuế gồm 10-13 chữ số
                    </small>
                </div>
            </div>

            {{-- Business Description --}}
            <div class="mb-3">
                <label for="business_description" class="form-label required">
                    <i class="fas fa-align-left me-1"></i>
                    Mô tả hoạt động kinh doanh
                </label>
                <textarea class="form-control @error('business_description') is-invalid @enderror" 
                          id="business_description" 
                          name="business_description" 
                          rows="4"
                          placeholder="Mô tả chi tiết về hoạt động kinh doanh, sản phẩm/dịch vụ chính của công ty..."
                          required>{{ old('business_description', $sessionData['business_description'] ?? '') }}</textarea>
                <div class="invalid-feedback">
                    @error('business_description'){{ $message }}@enderror
                </div>
                <small class="form-text text-muted">
                    Tối thiểu 50 ký tự, tối đa 1000 ký tự
                </small>
            </div>

            {{-- Business Categories --}}
            <div class="mb-3">
                <label class="form-label required">
                    <i class="fas fa-tags me-1"></i>
                    Lĩnh vực kinh doanh
                </label>
                <div class="business-categories">
                    @php
                        $categories = [
                            'automotive' => 'Ô tô & Xe máy',
                            'aerospace' => 'Hàng không & Vũ trụ',
                            'manufacturing' => 'Sản xuất chế tạo',
                            'materials' => 'Vật liệu & Kim loại',
                            'components' => 'Linh kiện & Phụ tụng',
                            'industrial' => 'Thiết bị công nghiệp',
                            'electronics' => 'Điện tử & Điện lạnh',
                            'energy' => 'Năng lượng & Dầu khí',
                            'construction' => 'Xây dựng & Kiến trúc',
                            'textiles' => 'Dệt may & Thời trang',
                            'food' => 'Thực phẩm & Đồ uống',
                            'pharmaceutical' => 'Dược phẩm & Y tế',
                            'chemical' => 'Hóa chất & Nhựa',
                            'machinery' => 'Máy móc & Thiết bị',
                            'tools' => 'Dụng cụ & Công cụ'
                        ];
                        $selectedCategories = old('business_categories', $sessionData['business_categories'] ?? []);
                    @endphp
                    
                    <div class="row">
                        @foreach($categories as $value => $label)
                            <div class="col-md-4 col-sm-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="business_categories[]" 
                                           id="category_{{ $value }}" 
                                           value="{{ $value }}"
                                           {{ in_array($value, $selectedCategories) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="category_{{ $value }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @error('business_categories')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
                <small class="form-text text-muted">
                    Chọn ít nhất 1 lĩnh vực, tối đa 5 lĩnh vực
                </small>
            </div>
        </div>

        {{-- Section: Contact Information --}}
        <div class="form-section mb-4">
            <h3 class="section-title">
                <i class="fas fa-address-book text-primary me-2"></i>
                Thông tin liên hệ
            </h3>
            <p class="section-description text-muted mb-4">
                Thông tin liên hệ của công ty (không bắt buộc nhưng được khuyến khích).
            </p>

            <div class="row">
                {{-- Business Phone --}}
                <div class="col-md-6 mb-3">
                    <label for="business_phone" class="form-label">
                        <i class="fas fa-phone me-1"></i>
                        Số điện thoại công ty
                    </label>
                    <input type="tel" 
                           class="form-control @error('business_phone') is-invalid @enderror" 
                           id="business_phone" 
                           name="business_phone" 
                           value="{{ old('business_phone', $sessionData['business_phone'] ?? '') }}"
                           placeholder="Ví dụ: +84 123 456 789">
                    <div class="invalid-feedback">
                        @error('business_phone'){{ $message }}@enderror
                    </div>
                </div>

                {{-- Business Email --}}
                <div class="col-md-6 mb-3">
                    <label for="business_email" class="form-label">
                        <i class="fas fa-envelope me-1"></i>
                        Email công ty
                    </label>
                    <input type="email" 
                           class="form-control @error('business_email') is-invalid @enderror" 
                           id="business_email" 
                           name="business_email" 
                           value="{{ old('business_email', $sessionData['business_email'] ?? '') }}"
                           placeholder="Ví dụ: info@company.com">
                    <div class="invalid-feedback">
                        @error('business_email'){{ $message }}@enderror
                    </div>
                    <small class="form-text text-muted">
                        Email công ty phải khác với email cá nhân
                    </small>
                </div>
            </div>

            {{-- Business Address --}}
            <div class="mb-3">
                <label for="business_address" class="form-label">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    Địa chỉ công ty
                </label>
                <textarea class="form-control @error('business_address') is-invalid @enderror" 
                          id="business_address" 
                          name="business_address" 
                          rows="3"
                          placeholder="Nhập địa chỉ đầy đủ của công ty...">{{ old('business_address', $sessionData['business_address'] ?? '') }}</textarea>
                <div class="invalid-feedback">
                    @error('business_address'){{ $message }}@enderror
                </div>
            </div>
        </div>

        {{-- Section: Document Upload --}}
        <div class="form-section mb-4">
            <h3 class="section-title">
                <i class="fas fa-file-upload text-primary me-2"></i>
                Tài liệu xác minh (Tùy chọn)
            </h3>
            <p class="section-description text-muted mb-4">
                Tải lên các tài liệu để tăng tốc quá trình xác minh tài khoản.
            </p>

            <div class="document-upload">
                <div class="upload-area" id="uploadArea">
                    <div class="upload-content">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <h5>Kéo thả file vào đây hoặc click để chọn</h5>
                        <p class="text-muted">
                            Hỗ trợ: PDF, JPG, JPEG, PNG, DOC, DOCX<br>
                            Kích thước tối đa: 5MB mỗi file
                        </p>
                        <input type="file" 
                               id="verification_documents" 
                               name="verification_documents[]" 
                               multiple 
                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                               class="d-none">
                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('verification_documents').click()">
                            <i class="fas fa-plus me-2"></i>
                            Chọn tài liệu
                        </button>
                    </div>
                </div>
                
                <div class="uploaded-files mt-3" id="uploadedFiles" style="display: none;">
                    <h6>Tài liệu đã chọn:</h6>
                    <div class="file-list" id="fileList"></div>
                </div>
                
                @error('verification_documents')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
                
                <small class="form-text text-muted">
                    Các tài liệu gợi ý: Giấy phép kinh doanh, Chứng nhận đăng ký thuế, Chứng chỉ chất lượng
                </small>
            </div>
        </div>

        {{-- Verification Notice --}}
        <div class="verification-notice">
            <div class="alert alert-warning">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Lưu ý quan trọng:</strong>
                <ul class="mb-0 mt-2">
                    <li>Tài khoản doanh nghiệp cần được xác minh bởi admin trước khi có thể sử dụng đầy đủ tính năng.</li>
                    <li>Quá trình xác minh thường mất 1-3 ngày làm việc.</li>
                    <li>Bạn sẽ nhận được email thông báo khi tài khoản được phê duyệt.</li>
                    <li>Trong thời gian chờ xác minh, bạn có thể đăng nhập và cập nhật thông tin profile.</li>
                </ul>
            </div>
        </div>
    </form>

</x-registration-wizard>
@endsection

@push('styles')
<link href="{{ asset('css/frontend/registration-wizard.css') }}" rel="stylesheet">
<style>
.account-type-display .alert {
    border-left: 4px solid #007bff;
}

.business-categories .form-check {
    margin-bottom: 0.5rem;
}

.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-area:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.upload-area.dragover {
    border-color: #007bff;
    background-color: #e3f2fd;
}

.upload-icon {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    margin-bottom: 0.5rem;
}

.file-info {
    display: flex;
    align-items: center;
}

.file-icon {
    margin-right: 0.5rem;
    color: #007bff;
}

.file-remove {
    color: #dc3545;
    cursor: pointer;
}

.verification-notice {
    margin-top: 2rem;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/frontend/registration-wizard.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // File upload handling
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('verification_documents');
    const uploadedFiles = document.getElementById('uploadedFiles');
    const fileList = document.getElementById('fileList');
    let selectedFiles = [];

    // Drag and drop functionality
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
        handleFiles(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        for (let file of files) {
            if (selectedFiles.length >= 5) {
                alert('Chỉ được tải lên tối đa 5 tài liệu');
                break;
            }
            
            if (file.size > 5 * 1024 * 1024) {
                alert(`File ${file.name} quá lớn. Kích thước tối đa là 5MB.`);
                continue;
            }
            
            selectedFiles.push(file);
        }
        
        updateFileList();
    }

    function updateFileList() {
        if (selectedFiles.length === 0) {
            uploadedFiles.style.display = 'none';
            return;
        }

        uploadedFiles.style.display = 'block';
        fileList.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <div class="file-info">
                    <i class="fas fa-file file-icon"></i>
                    <span>${file.name} (${formatFileSize(file.size)})</span>
                </div>
                <i class="fas fa-times file-remove" onclick="removeFile(${index})"></i>
            `;
            fileList.appendChild(fileItem);
        });

        // Update file input
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    window.removeFile = function(index) {
        selectedFiles.splice(index, 1);
        updateFileList();
    };

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Business categories validation
    const categoryCheckboxes = document.querySelectorAll('input[name="business_categories[]"]');
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('input[name="business_categories[]"]:checked').length;
            if (checkedCount > 5) {
                this.checked = false;
                alert('Chỉ được chọn tối đa 5 lĩnh vực kinh doanh');
            }
        });
    });
});
</script>
@endpush
