@extends('layouts.app')

@section('title', 'Chỉnh sửa Showcase')

@push('styles')
<style>
    .showcase-edit-form {
        max-width: 800px;
        margin: 0 auto;
    }

    .form-section {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .form-section h5 {
        color: #495057;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 3rem 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-upload-area:hover {
        border-color: #007bff;
        background-color: #f8f9ff;
    }

    .file-upload-area.dragover {
        border-color: #007bff;
        background-color: #e3f2fd;
    }

    .image-preview {
        max-width: 100%;
        max-height: 300px;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .step-indicator {
        display: flex;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .step {
        display: flex;
        align-items: center;
        margin: 0 1rem;
        color: #6c757d;
    }

    .step.active {
        color: #007bff;
        font-weight: 600;
    }

    .step-number {
        background: #e9ecef;
        color: #6c757d;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.5rem;
        font-weight: 600;
    }

    .step.active .step-number {
        background: #007bff;
        color: white;
    }

    .current-media {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .media-item {
        position: relative;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .media-item img {
        width: 150px;
        height: 100px;
        object-fit: cover;
    }

    .media-item .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 53, 69, 0.8);
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="showcase-edit-form">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h2 class="h3 mb-2">Chỉnh sửa Showcase</h2>
                    <p class="text-muted">{{ $showcase->title }}</p>
                </div>

                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                        <span>Thông Tin Cơ Bản</span>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                        <span>Media Upload</span>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-number">3</div>
                        <span>Thông Tin Kỹ Thuật</span>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-number">4</div>
                        <span>Tính Năng & Cài Đặt</span>
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('showcase.update', $showcase) }}" method="POST" enctype="multipart/form-data" id="showcaseEditForm">
                    @csrf
                    @method('PATCH')

                    <!-- Step 1: Basic Information -->
                    <div class="form-step" data-step="1">
                        <div class="form-section">
                            <h5>Thông Tin Cơ Bản</h5>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">
                                            Tiêu đề showcase <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="title" name="title"
                                               value="{{ old('title', $showcase->title) }}" required>
                                        <div class="form-text">Tên dự án hoặc sản phẩm bạn muốn showcase</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="location" class="form-label">Địa điểm</label>
                                        <input type="text" class="form-control" id="location" name="location"
                                               value="{{ old('location', $showcase->location) }}">
                                    </div>

                                    <div class="mb-3">
                                        <label for="application_field" class="form-label">Lĩnh vực ứng dụng</label>
                                        <input type="text" class="form-control" id="application_field" name="application_field"
                                               value="{{ old('application_field', $showcase->application_field) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    Mô tả chi tiết <span class="text-danger">*</span>
                                </label>
                                <textarea id="description" name="description" class="form-control tinymce-editor" rows="8" required>{{ old('description', $showcase->description) }}</textarea>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Cung cấp thông tin chi tiết về dự án, công nghệ và kết quả. Hỗ trợ định dạng văn bản, hình ảnh và liên kết.
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted">Bước 1</span> / <span class="text-muted">4</span>
                                <a href="{{ route('showcase.show', $showcase) }}" class="btn btn-outline-secondary ms-3">
                                    <i class="fas fa-eye"></i> Xem trước
                                </a>
                            </div>
                            <button type="button" class="btn btn-primary next-step">
                                Tiếp theo <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Media Upload -->
                    <div class="form-step d-none" data-step="2">
                        <div class="form-section">
                            <h5>Media hiện tại</h5>
                            @if($showcase->images && count($showcase->images) > 0)
                                <div class="current-media">
                                    @foreach($showcase->images as $index => $image)
                                        <div class="media-item">
                                            <img src="{{ $image }}" alt="Showcase Image {{ $index + 1 }}">
                                            <button type="button" class="remove-btn" data-image-index="{{ $index }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Chưa có hình ảnh nào.</p>
                            @endif
                        </div>

                        <div class="form-section">
                            <h5>Thêm Media Mới</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Hình ảnh</label>
                                    <div class="file-upload-area" id="imageUploadArea">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                        <p class="mb-2">Kéo thả hình ảnh vào đây</p>
                                        <p class="text-muted small">hoặc</p>
                                        <button type="button" class="btn btn-outline-primary">Chọn hình ảnh</button>
                                        <input type="file" id="images" name="images[]" multiple accept="image/*" class="d-none">
                                    </div>
                                    <div class="form-text">Định dạng: JPG, PNG, GIF. Tối đa 10 file, mỗi file 5MB.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tệp đính kèm</label>
                                    <div class="file-upload-area" id="attachmentUploadArea">
                                        <i class="fas fa-paperclip fa-3x text-muted mb-3"></i>
                                        <p class="mb-2">Kéo thả tệp vào đây</p>
                                        <p class="text-muted small">hoặc</p>
                                        <button type="button" class="btn btn-outline-primary">Chọn file</button>
                                        <input type="file" id="attachments" name="attachments[]" multiple class="d-none">
                                    </div>
                                    <div class="form-text">Định dạng: PDF, DOC, ZIP, RAR. Tối đa 5 file, mỗi file 10MB.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary prev-step">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </button>
                            <button type="button" class="btn btn-primary next-step">
                                Tiếp theo <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Technical Information -->
                    <div class="form-step d-none" data-step="3">
                        <div class="form-section">
                            <h5>Thông Tin Kỹ Thuật</h5>
                            <div class="mb-3">
                                <label for="technical_specs" class="form-label">Thông số kỹ thuật</label>
                                <textarea id="technical_specs" name="technical_specs" class="form-control tinymce-editor" rows="6">{{ old('technical_specs', $showcase->technical_specs) }}</textarea>
                                <div class="form-text">Chi tiết về thông số kỹ thuật, vật liệu, kích thước, v.v.</div>
                            </div>

                            <div class="mb-3">
                                <label for="features" class="form-label">Tính năng nổi bật</label>
                                <textarea id="features" name="features" class="form-control tinymce-editor" rows="6">{{ old('features', $showcase->features) }}</textarea>
                                <div class="form-text">Các tính năng đặc biệt và điểm mạnh của dự án</div>
                            </div>

                            <div class="mb-3">
                                <label for="benefits" class="form-label">Lợi ích & Ứng dụng</label>
                                <textarea id="benefits" name="benefits" class="form-control tinymce-editor" rows="6">{{ old('benefits', $showcase->benefits) }}</textarea>
                                <div class="form-text">Lợi ích mang lại và các ứng dụng thực tế</div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary prev-step">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </button>
                            <button type="button" class="btn btn-primary next-step">
                                Tiếp theo <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Features & Settings -->
                    <div class="form-step d-none" data-step="4">
                        <div class="form-section">
                            <h5>Tính Năng & Cài Đặt</h5>
                            <div class="mb-3">
                                <label for="tags" class="form-label">Tags</label>
                                <input type="text" class="form-control" id="tags" name="tags"
                                       value="{{ old('tags', is_array($showcase->tags) ? implode(', ', $showcase->tags) : $showcase->tags) }}"
                                       placeholder="Thêm thẻ...">
                                <div class="form-text">Phân cách bằng dấu phẩy. Ví dụ: robot, automation, industry 4.0</div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                                           {{ old('is_featured', $showcase->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Đánh dấu nổi bật
                                    </label>
                                    <div class="form-text">Showcase sẽ được hiển thị ở vị trí nổi bật (chỉ admin mới có thể thay đổi)</div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Lưu ý:</strong> Showcase sẽ được xem xét và phê duyệt trước khi hiển thị công khai.
                            </div>
                        </div>

                        <!-- Final Navigation -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary prev-step">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </button>
                            <div>
                                <a href="{{ route('showcase.show', $showcase) }}" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-times"></i> Hủy
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Cập nhật Showcase
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <x-sidebar />
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- TinyMCE - Self-hosted -->
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 4;

    // Initialize TinyMCE
    tinymce.init({
        selector: '.tinymce-editor',
        height: 300,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | link image | code',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }'
    });

    // Step navigation functions
    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.form-step').forEach(el => {
            el.classList.add('d-none');
        });

        // Show current step
        document.querySelector(`[data-step="${step}"]`).classList.remove('d-none');

        // Update step indicators
        document.querySelectorAll('.step').forEach(el => {
            el.classList.remove('active');
        });
        document.querySelector(`.step[data-step="${step}"]`).classList.add('active');

        currentStep = step;
    }

    // Next step buttons
    document.querySelectorAll('.next-step').forEach(btn => {
        btn.addEventListener('click', function() {
            if (currentStep < totalSteps) {
                showStep(currentStep + 1);
            }
        });
    });

    // Previous step buttons
    document.querySelectorAll('.prev-step').forEach(btn => {
        btn.addEventListener('click', function() {
            if (currentStep > 1) {
                showStep(currentStep - 1);
            }
        });
    });

    // File upload handling
    function setupFileUpload(areaId, inputId) {
        const area = document.getElementById(areaId);
        const input = document.getElementById(inputId);
        const button = area.querySelector('button');

        button.addEventListener('click', () => input.click());

        area.addEventListener('dragover', (e) => {
            e.preventDefault();
            area.classList.add('dragover');
        });

        area.addEventListener('dragleave', () => {
            area.classList.remove('dragover');
        });

        area.addEventListener('drop', (e) => {
            e.preventDefault();
            area.classList.remove('dragover');
            input.files = e.dataTransfer.files;
            updateFileDisplay(area, input.files);
        });

        input.addEventListener('change', () => {
            updateFileDisplay(area, input.files);
        });
    }

    function updateFileDisplay(area, files) {
        const fileList = area.querySelector('.file-list') || document.createElement('div');
        fileList.className = 'file-list mt-2';
        fileList.innerHTML = '';

        Array.from(files).forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'alert alert-info alert-sm d-flex justify-content-between align-items-center';
            fileItem.innerHTML = `
                <span><i class="fas fa-file"></i> ${file.name}</span>
                <small>${(file.size / 1024 / 1024).toFixed(2)} MB</small>
            `;
            fileList.appendChild(fileItem);
        });

        if (!area.querySelector('.file-list')) {
            area.appendChild(fileList);
        }
    }

    // Setup file uploads
    setupFileUpload('imageUploadArea', 'images');
    setupFileUpload('attachmentUploadArea', 'attachments');

    // Remove existing media
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const imageIndex = this.dataset.imageIndex;
            // Add hidden input to mark image for removal
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'remove_images[]';
            hiddenInput.value = imageIndex;
            document.getElementById('showcaseEditForm').appendChild(hiddenInput);

            // Hide the image
            this.closest('.media-item').style.display = 'none';
        });
    });

    // Form validation
    document.getElementById('showcaseEditForm').addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const description = tinymce.get('description').getContent().trim();

        if (!title || !description) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin bắt buộc (Tiêu đề và Mô tả).');
            showStep(1); // Go back to first step
        }
    });
});
</script>
@endpush
