@props(['maxFiles' => 5, 'maxSize' => '5MB', 'context' => 'comment', 'uploadText' => 'Thêm hình ảnh mới', 'acceptDescription' => 'Tối đa 5 file • 5MB mỗi file • JPG, JPEG, PNG, GIF, WEBP', 'showPreview' => true, 'compact' => false])

@php
    $uniqueId = 'comment-upload-' . uniqid();
    $compactClass = $compact ? 'compact' : '';
@endphp

<div class="comment-image-upload {{ $compactClass }}" id="{{ $uniqueId }}">
    <div class="upload-container">
        <!-- Upload Area (Left Side) -->
        <div class="upload-area" id="{{ $uniqueId }}-area">
            <input type="file"
                   id="{{ $uniqueId }}-input"
                   name="images[]"
                   multiple
                   accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                   style="display: none;">

            <div class="upload-dropzone" id="{{ $uniqueId }}-dropzone">
                <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div class="upload-text">
                    <strong>{{ $uploadText }}</strong>
                    <p class="upload-description">{{ $acceptDescription }}</p>
                    <p class="upload-hint">Kéo thả file vào đây hoặc <span class="upload-link">chọn file</span></p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="upload-progress" id="{{ $uniqueId }}-progress" style="display: none;">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
                <small class="progress-text">Đang upload...</small>
            </div>
        </div>

        <!-- Preview Area (Right Side) -->
        @if($showPreview)
        <div class="preview-area" id="{{ $uniqueId }}-preview">
            <div class="preview-header">
                <h6 class="mb-0">Hình ảnh đã chọn</h6>
                <small class="text-muted">(<span class="file-count">0</span>/{{ $maxFiles }})</small>
            </div>
            <div class="preview-grid" id="{{ $uniqueId }}-grid">
                <!-- Preview images will be inserted here -->
            </div>
        </div>
        @endif
    </div>

    <!-- Error Messages -->
    <div class="upload-errors" id="{{ $uniqueId }}-errors" style="display: none;">
        <div class="alert alert-danger mb-0">
            <ul class="mb-0" id="{{ $uniqueId }}-error-list"></ul>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/comment-image-upload.css') }}">
@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadComponent = document.getElementById('{{ $uniqueId }}');
    if (!uploadComponent) return;

    const fileInput = uploadComponent.querySelector('#{{ $uniqueId }}-input');
    const dropzone = uploadComponent.querySelector('#{{ $uniqueId }}-dropzone');
    const previewGrid = uploadComponent.querySelector('#{{ $uniqueId }}-grid');
    const fileCountSpan = uploadComponent.querySelector('.file-count');
    const progressContainer = uploadComponent.querySelector('#{{ $uniqueId }}-progress');
    const progressBar = progressContainer?.querySelector('.progress-bar');
    const progressText = progressContainer?.querySelector('.progress-text');
    const errorsContainer = uploadComponent.querySelector('#{{ $uniqueId }}-errors');
    const errorsList = uploadComponent.querySelector('#{{ $uniqueId }}-error-list');

    let selectedFiles = [];
    const maxFiles = {{ $maxFiles }};
    const maxSizeBytes = {{ $maxSize === '5MB' ? 5242880 : 5242880 }}; // Default 5MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

    // Click to select files
    dropzone.addEventListener('click', () => fileInput.click());

    // File input change
    fileInput.addEventListener('change', handleFileSelect);

    // Drag and drop
    dropzone.addEventListener('dragover', handleDragOver);
    dropzone.addEventListener('dragleave', handleDragLeave);
    dropzone.addEventListener('drop', handleDrop);

    function handleFileSelect(e) {
        const files = Array.from(e.target.files);
        processFiles(files);
    }

    function handleDragOver(e) {
        e.preventDefault();
        dropzone.classList.add('dragover');
    }

    function handleDragLeave(e) {
        e.preventDefault();
        dropzone.classList.remove('dragover');
    }

    function handleDrop(e) {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        const files = Array.from(e.dataTransfer.files);
        processFiles(files);
    }

    function processFiles(files) {
        clearErrors();
        const errors = [];

        // Filter valid image files
        const validFiles = files.filter(file => {
            if (!allowedTypes.includes(file.type)) {
                errors.push(`File "${file.name}" không phải là hình ảnh hợp lệ.`);
                return false;
            }
            if (file.size > maxSizeBytes) {
                errors.push(`File "${file.name}" quá lớn (tối đa {{ $maxSize }}).`);
                return false;
            }
            return true;
        });

        // Check total file count
        if (selectedFiles.length + validFiles.length > maxFiles) {
            errors.push(`Chỉ được phép tối đa ${maxFiles} hình ảnh.`);
            const allowedCount = maxFiles - selectedFiles.length;
            validFiles.splice(allowedCount);
        }

        if (errors.length > 0) {
            showErrors(errors);
        }

        // Add valid files
        validFiles.forEach(file => {
            selectedFiles.push(file);
            createPreview(file);
        });

        updateFileCount();
        updateFormData();
    }

    function createPreview(file) {
        if (!previewGrid) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            previewItem.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="preview-image">
                <button type="button" class="preview-remove" onclick="removeFile('${file.name}', this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            previewGrid.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    }

    // Global function for removing files
    window.removeFile = function(fileName, button) {
        const index = selectedFiles.findIndex(f => f.name === fileName);
        if (index > -1) {
            selectedFiles.splice(index, 1);
            button.closest('.preview-item').remove();
            updateFileCount();
            updateFormData();
        }
    };

    function updateFileCount() {
        if (fileCountSpan) {
            fileCountSpan.textContent = selectedFiles.length;
        }
    }

    function updateFormData() {
        // Create new FileList for form submission
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    function showErrors(errors) {
        if (!errorsContainer || !errorsList) return;

        errorsList.innerHTML = errors.map(error => `<li>${error}</li>`).join('');
        errorsContainer.style.display = 'block';
    }

    function clearErrors() {
        if (errorsContainer) {
            errorsContainer.style.display = 'none';
        }
    }

    // Upload files to server
    async function uploadFiles() {
        if (selectedFiles.length === 0) return [];

        const formData = new FormData();
        selectedFiles.forEach(file => {
            formData.append('images[]', file);
        });
        formData.append('context', '{{ $context }}');

        try {
            showProgress(0);
            setProgressText('Đang upload hình ảnh...');
            dropzone.classList.add('loading');

            const response = await fetch('/api/comments/images/upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showProgress(100);
                setProgressText('Upload hoàn thành!');
                setTimeout(() => hideProgress(), 1000);
                return data.data; // Return uploaded file data
            } else {
                throw new Error(data.message || 'Upload failed');
            }
        } catch (error) {
            console.error('Upload error:', error);
            showErrors([error.message || 'Có lỗi xảy ra khi upload hình ảnh']);
            return [];
        } finally {
            dropzone.classList.remove('loading');
        }
    }

    function showProgress(percent) {
        if (progressContainer && progressBar) {
            progressContainer.style.display = 'block';
            progressBar.style.width = percent + '%';
        }
    }

    function hideProgress() {
        if (progressContainer) {
            progressContainer.style.display = 'none';
        }
    }

    function setProgressText(text) {
        if (progressText) {
            progressText.textContent = text;
        }
    }

    // Expose component methods for external use
    uploadComponent.commentImageUpload = {
        getFiles: () => selectedFiles,
        uploadFiles: uploadFiles,
        clearFiles: () => {
            selectedFiles = [];
            if (previewGrid) previewGrid.innerHTML = '';
            updateFileCount();
            updateFormData();
            clearErrors();
        },
        showProgress: showProgress,
        hideProgress: hideProgress,
        setProgressText: setProgressText,
        hasFiles: () => selectedFiles.length > 0
    };
});
</script>
