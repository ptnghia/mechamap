@extends('admin.layouts.dason')

@section('title', 'Thêm Tài Liệu Mới')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Thêm Tài Liệu Mới</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.knowledge.index') }}">Cơ Sở Tri Thức</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.knowledge.documents') }}">Tài Liệu</a></li>
                        <li class="breadcrumb-item active">Thêm Mới</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <form action="{{ route('admin.knowledge.documents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Thông Tin Tài Liệu</h4>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu Đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Document File Upload Component -->
                        <x-advanced-file-upload
                            name="document_file"
                            :file-types="['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar']"
                            max-size="10MB"
                            :required="true"
                            :multiple="false"
                            id="document-file-upload"
                            context="document"
                            upload-text="File Tài Liệu"
                            accept-description="PDF, Word, Excel, PowerPoint, Archive"
                        />
                        @error('document_file')
                            <div class="text-danger small mt-2">
                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                            </div>
                        @enderror

                        <!-- File Preview -->
                        <div id="filePreview" class="mb-3" style="display: none;">
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i id="fileIcon" class="fas fa-file font-size-24 me-3"></i>
                                    <div>
                                        <h6 id="fileName" class="mb-1"></h6>
                                        <p id="fileSize" class="mb-0 text-muted"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô Tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="5"
                                      placeholder="Mô tả chi tiết về nội dung tài liệu...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div class="mb-3">
                            <label for="tags" class="form-label">Thẻ Tag</label>
                            <input type="text" class="form-control @error('tags') is-invalid @enderror"
                                   id="tags" name="tags" value="{{ old('tags') }}"
                                   placeholder="Nhập các tag, cách nhau bởi dấu phẩy">
                            <div class="form-text">Ví dụ: tiêu chuẩn, iso, quy trình</div>
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Document Information -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Thông Tin Bổ Sung</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="version" class="form-label">Phiên Bản</label>
                                    <input type="text" class="form-control @error('version') is-invalid @enderror"
                                           id="version" name="version" value="{{ old('version') }}"
                                           placeholder="v1.0, 2025, etc.">
                                    @error('version')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="language" class="form-label">Ngôn Ngữ</label>
                                    <select class="form-select @error('language') is-invalid @enderror"
                                            id="language" name="language">
                                        <option value="vi" {{ old('language', 'vi') == 'vi' ? 'selected' : '' }}>Tiếng Việt</option>
                                        <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>English</option>
                                        <option value="both" {{ old('language') == 'both' ? 'selected' : '' }}>Song ngữ</option>
                                    </select>
                                    @error('language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pages" class="form-label">Số Trang</label>
                                    <input type="number" class="form-control @error('pages') is-invalid @enderror"
                                           id="pages" name="pages" value="{{ old('pages') }}" min="1">
                                    @error('pages')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="access_level" class="form-label">Mức Độ Truy Cập</label>
                                    <select class="form-select @error('access_level') is-invalid @enderror"
                                            id="access_level" name="access_level">
                                        <option value="public" {{ old('access_level', 'public') == 'public' ? 'selected' : '' }}>Công khai</option>
                                        <option value="members" {{ old('access_level') == 'members' ? 'selected' : '' }}>Chỉ thành viên</option>
                                        <option value="premium" {{ old('access_level') == 'premium' ? 'selected' : '' }}>Thành viên Premium</option>
                                        <option value="private" {{ old('access_level') == 'private' ? 'selected' : '' }}>Riêng tư</option>
                                    </select>
                                    @error('access_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Publish Settings -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Cài Đặt Xuất Bản</h4>
                    </div>
                    <div class="card-body">
                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng Thái <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Xuất bản ngay</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh Mục <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror"
                                    id="category_id" name="category_id" required>
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Featured -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured"
                                       name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">
                                    Tài liệu nổi bật
                                </label>
                            </div>
                        </div>

                        <!-- Download Tracking -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="track_downloads"
                                       name="track_downloads" value="1" {{ old('track_downloads', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="track_downloads">
                                    Theo dõi lượt tải
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Information -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Thông Tin File</h4>
                    </div>
                    <div class="card-body">
                        <div id="fileInfo" style="display: none;">
                            <div class="mb-2">
                                <small class="text-muted">Tên file:</small>
                                <p id="originalFileName" class="mb-1"></p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Kích thước:</small>
                                <p id="fileSizeInfo" class="mb-1"></p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Loại file:</small>
                                <p id="fileTypeInfo" class="mb-1"></p>
                            </div>
                        </div>
                        <div id="noFileInfo" class="text-center py-3">
                            <i class="fas fa-upload font-size-24 text-muted mb-2"></i>
                            <p class="text-muted mb-0">Chọn file để xem thông tin</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i> Lưu Tài Liệu
                            </button>
                            <a href="{{ route('admin.knowledge.documents') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Hủy
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // File upload preview
    $('#document_file').on('change', function() {
        const file = this.files[0];
        if (file) {
            // Show file preview
            $('#fileName').text(file.name);
            $('#fileSize').text(formatFileSize(file.size));

            // Set file icon based on type
            const extension = file.name.split('.').pop().toLowerCase();
            const icon = getFileIcon(extension);
            $('#fileIcon').attr('class', icon);

            $('#filePreview').show();

            // Show file info in sidebar
            $('#originalFileName').text(file.name);
            $('#fileSizeInfo').text(formatFileSize(file.size));
            $('#fileTypeInfo').text(extension.toUpperCase());
            $('#fileInfo').show();
            $('#noFileInfo').hide();

            // Auto-fill title if empty
            if (!$('#title').val()) {
                const nameWithoutExt = file.name.replace(/\.[^/.]+$/, "");
                $('#title').val(nameWithoutExt);
            }
        } else {
            $('#filePreview').hide();
            $('#fileInfo').hide();
            $('#noFileInfo').show();
        }
    });

    // Tags input enhancement
    $('#tags').on('keyup', function() {
        let tags = $(this).val().split(',');
        // Add tag validation or enhancement logic
    });
});

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function getFileIcon(extension) {
    const icons = {
        'pdf': 'fas fa-file-pdf text-danger',
        'doc': 'fas fa-file-word text-primary',
        'docx': 'fas fa-file-word text-primary',
        'xls': 'fas fa-file-excel text-success',
        'xlsx': 'fas fa-file-excel text-success',
        'ppt': 'fas fa-file-powerpoint text-warning',
        'pptx': 'fas fa-file-powerpoint text-warning',
        'zip': 'fas fa-file-archive text-secondary',
        'rar': 'fas fa-file-archive text-secondary'
    };
    return icons[extension] || 'fas fa-file text-muted';
}
</script>
@endsection
