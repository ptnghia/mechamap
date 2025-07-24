@extends('layouts.app')

@section('title', 'Tạo Showcase Mới')

@push('styles')
<style>
    .showcase-create-form {
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

    .form-help {
        background: #e3f2fd;
        padding: 1rem;
        border-radius: 0.5rem;
        border-left: 4px solid #2196f3;
    }
</style>
@endpush

@section('content')
<div class="py-5">
    <div class="container">
        <div class="showcase-create-form">

            <!-- Header -->
            <div class="text-center mb-5">
                <h2><i class="stars me-2"></i>Tạo Showcase Mới</h2>
                <p class="text-muted">Chia sẻ dự án, sản phẩm hoặc thành tựu kỹ thuật của bạn với cộng đồng</p>
            </div>

            <!-- Help Information -->
            <div class="form-help mb-4">
                <h6><i class="info-circle me-2"></i>Về Showcase</h6>
                <p class="mb-2">Showcase là nơi bạn có thể trình bày các dự án cơ khí, sản phẩm kỹ thuật, hoặc giải pháp
                    công nghệ của mình. Đây là cơ hội để:</p>
                <ul class="mb-0">
                    <li>Giới thiệu dự án và ý tưởng sáng tạo</li>
                    <li>Chia sẻ kinh nghiệm và kỹ năng chuyên môn</li>
                    <li>Kết nối với cộng đồng kỹ sư và nhà phát triển</li>
                    <li>Nhận phản hồi và góp ý từ chuyên gia</li>
                </ul>
            </div>

            <!-- Create Form -->
            <form method="POST" action="{{ route('showcase.store') }}" enctype="multipart/form-data" id="showcaseForm">
                @csrf

                <!-- Basic Information Section -->
                <div class="form-section">
                    <h5><i class="fas fa-edit-square me-2"></i>Thông Tin Cơ Bản</h5>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label">Tiêu đề showcase <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                name="title" value="{{ old('title') }}"
                                placeholder="VD: Robot hàn tự động cho ngành sản xuất ô tô" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Tên dự án hoặc sản phẩm bạn muốn showcase</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Địa điểm</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror"
                                id="location" name="location" value="{{ old('location') }}"
                                placeholder="VD: Nhà máy ABC, Bình Dương">
                            @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="usage" class="form-label">Lĩnh vực ứng dụng</label>
                            <input type="text" class="form-control @error('usage') is-invalid @enderror" id="usage"
                                name="usage" value="{{ old('usage') }}"
                                placeholder="VD: Sản xuất ô tô, Cơ khí chính xác">
                            @error('usage')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả chi tiết <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                            name="description" rows="5"
                            placeholder="Mô tả chi tiết về dự án: mục tiêu, công nghệ sử dụng, đặc điểm nổi bật, kết quả đạt được..."
                            required>{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Cung cấp thông tin chi tiết về dự án, công nghệ và kết quả</div>
                    </div>
                </div>

                <!-- Cover Image Section -->
                <div class="form-section">
                    <h5><i class="fas fa-image me-2"></i>Hình Ảnh Đại Diện</h5>

                    <div class="mb-3">
                        <label for="cover_image" class="form-label">Upload hình ảnh đại diện <span
                                class="text-danger">*</span></label>
                        <div class="file-upload-area" id="fileUploadArea">
                            <i class="fas fa-cloud-upload-alt fs-1 text-muted mb-3"></i>
                            <h6>Kéo thả file hoặc click để chọn</h6>
                            <p class="text-muted mb-0">JPG, PNG, WebP (tối đa 5MB)</p>
                            <input type="file" class="d-none @error('cover_image') is-invalid @enderror"
                                id="cover_image" name="cover_image" accept="image/*" required>
                        </div>

                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-3 text-center" style="display: none;">
                            <img id="previewImg" class="image-preview" alt="Preview">
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-danger" id="removeImage">
                                    <i class="fas fa-trash"></i> Xóa ảnh
                                </button>
                            </div>
                        </div>

                        @error('cover_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Hình ảnh đại diện cho showcase của bạn</div>
                    </div>
                </div>

                <!-- Technical Details Section -->
                <div class="form-section">
                    <h5><i class="fas fa-cog me-2"></i>Thông Tin Kỹ Thuật</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="floors" class="form-label">Quy mô/Cấp độ</label>
                            <select class="form-select @error('floors') is-invalid @enderror" id="floors" name="floors">
                                <option value="">Chọn quy mô dự án</option>
                                <option value="1" {{ old('floors')=='1' ? 'selected' : '' }}>Prototype/Demo</option>
                                <option value="2" {{ old('floors')=='2' ? 'selected' : '' }}>Pilot/Thử nghiệm</option>
                                <option value="3" {{ old('floors')=='3' ? 'selected' : '' }}>Sản xuất nhỏ</option>
                                <option value="4" {{ old('floors')=='4' ? 'selected' : '' }}>Sản xuất hàng loạt</option>
                                <option value="5" {{ old('floors')=='5' ? 'selected' : '' }}>Quy mô công nghiệp</option>
                            </select>
                            @error('floors')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Danh mục kỹ thuật</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Chọn danh mục</option>
                                <option value="mechanical-design">Thiết kế cơ khí</option>
                                <option value="automation">Tự động hóa</option>
                                <option value="robotics">Robot học</option>
                                <option value="manufacturing">Sản xuất chế tạo</option>
                                <option value="cad-cam">CAD/CAM</option>
                                <option value="plc-scada">PLC/SCADA</option>
                                <option value="materials">Vật liệu kỹ thuật</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('showcase.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>

                    <div>
                        <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                            <i class="fas fa-eye me-2"></i>Xem trước
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-check-circle me-2"></i>Tạo Showcase
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Xem trước Showcase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <!-- Preview content will be inserted here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="submitFromPreview">Tạo Showcase</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('cover_image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImageBtn = document.getElementById('removeImage');
    const previewBtn = document.getElementById('previewBtn');
    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));

    // File upload area click
    fileUploadArea.addEventListener('click', () => {
        fileInput.click();
    });

    // Drag and drop functionality
    fileUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUploadArea.classList.add('dragover');
    });

    fileUploadArea.addEventListener('dragleave', () => {
        fileUploadArea.classList.remove('dragover');
    });

    fileUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUploadArea.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    // Handle file selection
    function handleFileSelect(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
                fileUploadArea.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    }

    // Remove image
    removeImageBtn.addEventListener('click', () => {
        fileInput.value = '';
        imagePreview.style.display = 'none';
        fileUploadArea.style.display = 'block';
    });

    // Preview functionality
    previewBtn.addEventListener('click', () => {
        generatePreview();
        previewModal.show();
    });

    function generatePreview() {
        const title = document.getElementById('title').value;
        const description = document.getElementById('description').value;
        const location = document.getElementById('location').value;
        const usage = document.getElementById('usage').value;
        const floors = document.getElementById('floors').value;

        const floorsText = {
            '1': 'Prototype/Demo',
            '2': 'Pilot/Thử nghiệm',
            '3': 'Sản xuất nhỏ',
            '4': 'Sản xuất hàng loạt',
            '5': 'Quy mô công nghiệp'
        };

        const previewContent = document.getElementById('previewContent');
        previewContent.innerHTML = `
            <div class="card">
                ${previewImg.src ? `<img src="${previewImg.src}" class="card-img-top" style="height: 250px; object-fit: cover;">` : ''}
                <div class="card-body">
                    <h5 class="card-title">${title || 'Tiêu đề showcase'}</h5>
                    <p class="card-text">${description || 'Mô tả showcase...'}</p>

                    ${location ? `<p><i class="geo-alt"></i> <strong>Địa điểm:</strong> ${location}</p>` : ''}
                    ${usage ? `<p><i class="tools"></i> <strong>Ứng dụng:</strong> ${usage}</p>` : ''}
                    ${floors ? `<p><i class="layers"></i> <strong>Quy mô:</strong> ${floorsText[floors]}</p>` : ''}

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">Bởi {{ auth()->user()->name }}</small>
                        <small class="text-muted">{{ now()->format('d/m/Y') }}</small>
                    </div>
                </div>
            </div>
        `;
    }

    // Submit from preview
    document.getElementById('submitFromPreview').addEventListener('click', () => {
        previewModal.hide();
        document.getElementById('showcaseForm').submit();
    });

    // Form validation
    document.getElementById('showcaseForm').addEventListener('submit', function(e) {
        const requiredFields = ['title', 'description', 'cover_image'];
        let isValid = true;

        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ các thông tin bắt buộc');
        }
    });
});
</script>
@endpush