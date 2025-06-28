@extends('admin.layouts.dason')

@section('title', 'Cấu hình Wiki')

@section('styles')
<style>
    .setting-card {
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .card-header {
        background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
        color: white;
        border-radius: 10px 10px 0 0 !important;
        padding: 15px 20px;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
        border-color: #6f42c1;
    }

    .btn-primary {
        background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
        border: none;
        border-radius: 25px;
        padding: 10px 30px;
        font-weight: 600;
    }

    .setting-group {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .setting-group h6 {
        color: #495057;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .form-switch .form-check-input:checked {
        background-color: #6f42c1;
        border-color: #6f42c1;
    }

    .file-types-input {
        font-family: 'Courier New', monospace;
        background-color: #f8f9fa;
    }

    .wiki-status {
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .wiki-enabled {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .wiki-disabled {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
</style>
@endsection

@push('styles')
<!-- Page specific CSS -->
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            @include('admin.settings.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Cấu hình Wiki</h2>
                    <p class="text-muted mb-0">Quản lý cài đặt wiki và hệ thống kiến thức</p>
                </div>
            </div>

            <!-- Wiki Status -->
            <div class="wiki-status {{ ($settings['wiki_enabled'] ?? false) ? 'wiki-enabled' : 'wiki-disabled' }}"
                id="wikiStatus">
                <h6 class="mb-2">
                    <i
                        class="bi {{ ($settings['wiki_enabled'] ?? false) ? 'bi-check-circle' : 'bi-x-circle' }} me-2"></i>
                    <span id="wikiStatusText">
                        {{ ($settings['wiki_enabled'] ?? false) ? 'Wiki đang hoạt động' : 'Wiki đã tắt' }}
                    </span>
                </h6>
                <p class="mb-0" id="wikiStatusDesc">
                    {{ ($settings['wiki_enabled'] ?? false) ? 'Người dùng có thể truy cập và sử dụng wiki.' : 'Wiki
                    không khả dụng cho người dùng.' }}
                </p>
            </div>

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Có lỗi xảy ra, vui lòng kiểm tra lại!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form action="{{ route('admin.settings.wiki.update') }}" method="POST" id="wikiSettingsForm">
                @csrf
                @method('PUT')

                <!-- General Wiki Settings -->
                <div class="card setting-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-book me-2"></i>
                            Cài đặt chung
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="setting-group">
                            <h6><i class="fas fa-power-off me-2"></i>Trạng thái wiki</h6>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="wiki_enabled" name="wiki_enabled"
                                    value="1" onchange="updateWikiStatus()" {{ old('wiki_enabled',
                                    $settings['wiki_enabled'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="wiki_enabled">
                                    Bật wiki
                                </label>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Tắt wiki sẽ ẩn tất cả nội dung wiki khỏi người dùng.
                            </div>
                        </div>

                        <div class="setting-group">
                            <h6><i class="fas fa-eye me-2"></i>Quyền truy cập</h6>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="wiki_public_read"
                                    name="wiki_public_read" value="1" {{ old('wiki_public_read',
                                    $settings['wiki_public_read'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="wiki_public_read">
                                    Cho phép khách đọc wiki
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="wiki_public_edit"
                                    name="wiki_public_edit" value="1" {{ old('wiki_public_edit',
                                    $settings['wiki_public_edit'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="wiki_public_edit">
                                    Cho phép khách chỉnh sửa wiki
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="wiki_require_approval"
                                    name="wiki_require_approval" value="1" {{ old('wiki_require_approval',
                                    $settings['wiki_require_approval'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="wiki_require_approval">
                                    Yêu cầu duyệt bài chỉnh sửa
                                </label>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Các chỉnh sửa sẽ cần được admin duyệt trước khi xuất bản.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Version Control -->
                <div class="card setting-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clock-history me-2"></i>
                            Kiểm soát phiên bản
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="setting-group">
                            <h6><i class="fas fa-archive me-2"></i>Lưu trữ phiên bản</h6>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="wiki_versioning_enabled"
                                    name="wiki_versioning_enabled" value="1" {{ old('wiki_versioning_enabled',
                                    $settings['wiki_versioning_enabled'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="wiki_versioning_enabled">
                                    Bật lưu trữ phiên bản
                                </label>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="wiki_max_revisions" class="form-label">
                                        Số phiên bản tối đa <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                        class="form-control @error('wiki_max_revisions') is-invalid @enderror"
                                        id="wiki_max_revisions" name="wiki_max_revisions"
                                        value="{{ old('wiki_max_revisions', $settings['wiki_max_revisions'] ?? 10) }}"
                                        min="1" max="100" required>
                                    @error('wiki_max_revisions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Số phiên bản cũ được lưu trữ cho mỗi trang wiki</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Upload Settings -->
                <div class="card setting-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cloud-upload-alt me-2"></i>
                            Tải lên tệp tin
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="setting-group">
                            <h6><i class="fas fa-file-arrow-up me-2"></i>Cài đặt upload</h6>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="wiki_allow_file_uploads"
                                    name="wiki_allow_file_uploads" value="1" {{ old('wiki_allow_file_uploads',
                                    $settings['wiki_allow_file_uploads'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="wiki_allow_file_uploads">
                                    Cho phép tải lên tệp tin
                                </label>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="wiki_max_file_size" class="form-label">
                                        Kích thước tệp tối đa (KB) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                        class="form-control @error('wiki_max_file_size') is-invalid @enderror"
                                        id="wiki_max_file_size" name="wiki_max_file_size"
                                        value="{{ old('wiki_max_file_size', $settings['wiki_max_file_size'] ?? 10240) }}"
                                        min="1" max="102400" required>
                                    @error('wiki_max_file_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <span id="fileSizeDisplay">{{ number_format(old('wiki_max_file_size',
                                            $settings['wiki_max_file_size'] ?? 10240) / 1024, 1) }} MB</span>
                                        (Tối đa 100MB)
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="wiki_allowed_file_types" class="form-label">
                                        Loại tệp được phép
                                    </label>
                                    <textarea
                                        class="form-control file-types-input @error('wiki_allowed_file_types') is-invalid @enderror"
                                        id="wiki_allowed_file_types" name="wiki_allowed_file_types" rows="3"
                                        placeholder="jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar">{{ old('wiki_allowed_file_types', $settings['wiki_allowed_file_types'] ?? 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar') }}</textarea>
                                    @error('wiki_allowed_file_types')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Danh sách phần mở rộng được phép, phân cách bằng dấu phẩy
                                    </div>
                                </div>
                            </div>

                            <!-- File Type Presets -->
                            <div class="mb-3">
                                <label class="form-label">Bộ mẫu loại tệp:</label>
                                <div class="btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="setFileTypes('images')">
                                        <i class="fas fa-image me-1"></i>Chỉ ảnh
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="setFileTypes('documents')">
                                        <i class="fas fa-file-alt me-1"></i>Tài liệu
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="setFileTypes('media')">
                                        <i class="fas fa-play-circle me-1"></i>Đa phương tiện
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="setFileTypes('all')">
                                        <i class="fas fa-copy me-1"></i>Tất cả
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-3">
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                <i class="fas fa-sync-alt me-2"></i>
                                Đặt lại
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-book-open me-2"></i>
                                Lưu cấu hình
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Update wiki status display
function updateWikiStatus() {
    const enabled = document.getElementById('wiki_enabled').checked;
    const status = document.getElementById('wikiStatus');
    const statusText = document.getElementById('wikiStatusText');
    const statusDesc = document.getElementById('wikiStatusDesc');

    if (enabled) {
        status.className = 'wiki-status wiki-enabled';
        statusText.innerHTML = '<i class="fas fa-check-circle me-2"></i>Wiki đang hoạt động';
        statusDesc.textContent = 'Người dùng có thể truy cập và sử dụng wiki.';
    } else {
        status.className = 'wiki-status wiki-disabled';
        statusText.innerHTML = '<i class="fas fa-times-circle me-2"></i>Wiki đã tắt';
        statusDesc.textContent = 'Wiki không khả dụng cho người dùng.';
    }
}

// Update file size display
document.getElementById('wiki_max_file_size').addEventListener('input', function() {
    const sizeKB = parseInt(this.value) || 0;
    const sizeMB = (sizeKB / 1024).toFixed(1);
    document.getElementById('fileSizeDisplay').textContent = sizeMB + ' MB';
});

// Set predefined file types
function setFileTypes(type) {
    const textarea = document.getElementById('wiki_allowed_file_types');

    const presets = {
        'images': 'jpg,jpeg,png,gif,webp,svg,bmp,ico',
        'documents': 'pdf,doc,docx,xls,xlsx,ppt,pptx,txt,rtf,odt,ods,odp',
        'media': 'jpg,jpeg,png,gif,mp4,mp3,avi,mov,wav,flv,webm,ogg',
        'all': 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,7z,tar,gz,mp4,mp3,avi,mov,wav,txt,rtf,csv'
    };

    textarea.value = presets[type] || '';
}

// Reset form
function resetForm() {
    if (confirm('Bạn có chắc chắn muốn đặt lại tất cả các thay đổi?')) {
        document.getElementById('wikiSettingsForm').reset();
        updateWikiStatus();
        document.getElementById('fileSizeDisplay').textContent = '10.0 MB';
    }
}

// Form validation
document.getElementById('wikiSettingsForm').addEventListener('submit', function(e) {
    const requiredFields = ['wiki_max_revisions', 'wiki_max_file_size'];
    let hasError = false;

    requiredFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        const value = parseInt(field.value);
        const min = parseInt(field.min);
        const max = parseInt(field.max);

        if (!field.value.trim() || value < min || value > max) {
            field.classList.add('is-invalid');
            hasError = true;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    // Validate file types format
    const fileTypes = document.getElementById('wiki_allowed_file_types').value.trim();
    if (fileTypes) {
        const types = fileTypes.split(',').map(t => t.trim());
        const validFormat = types.every(type => /^[a-zA-Z0-9]+$/.test(type));

        if (!validFormat) {
            document.getElementById('wiki_allowed_file_types').classList.add('is-invalid');
            hasError = true;
        } else {
            document.getElementById('wiki_allowed_file_types').classList.remove('is-invalid');
        }
    }

    if (hasError) {
        e.preventDefault();
        alert('Vui lòng kiểm tra lại các trường không hợp lệ!');
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateWikiStatus();
});
</script>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection