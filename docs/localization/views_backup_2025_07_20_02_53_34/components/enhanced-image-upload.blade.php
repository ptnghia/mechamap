{{-- Enhanced Image Upload Component with Drag & Drop --}}
@props([
    'name' => 'images',
    'id' => null,
    'maxFiles' => 5,
    'maxSize' => 5, // MB
    'accept' => 'image/*',
    'multiple' => true
])

@php
    $uploadId = $id ?? 'upload-' . uniqid();
@endphp

<div class="enhanced-image-upload" data-upload-id="{{ $uploadId }}">
    <div class="upload-area" id="upload-area-{{ $uploadId }}">
        <div class="upload-content">
            <div class="upload-icon">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <div class="upload-text">
                <h6>Kéo thả ảnh vào đây</h6>
                <p>hoặc <span class="upload-link">chọn từ máy tính</span></p>
            </div>
            <div class="upload-info">
                <small class="text-muted">
                    Tối đa {{ $maxFiles }} ảnh • {{ $maxSize }}MB mỗi ảnh • JPG, PNG, GIF, WebP
                </small>
            </div>
        </div>
        <input type="file" 
               name="{{ $name }}[]" 
               id="{{ $uploadId }}" 
               accept="{{ $accept }}"
               {{ $multiple ? 'multiple' : '' }}
               style="display: none;">
    </div>
    
    <div class="upload-preview" id="preview-{{ $uploadId }}"></div>
</div>

<style>
.enhanced-image-upload {
    margin: 1rem 0;
}

.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 0.75rem;
    padding: 2rem;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.upload-area:hover {
    border-color: #0d6efd;
    background: #e7f3ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
}

.upload-area.drag-over {
    border-color: #0d6efd;
    background: #e7f3ff;
    transform: scale(1.02);
}

.upload-area.uploading {
    pointer-events: none;
    opacity: 0.7;
}

.upload-content {
    position: relative;
    z-index: 2;
}

.upload-icon {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.upload-area:hover .upload-icon {
    color: #0d6efd;
    transform: scale(1.1);
}

.upload-text h6 {
    margin: 0 0 0.5rem;
    color: #495057;
    font-weight: 600;
}

.upload-text p {
    margin: 0;
    color: #6c757d;
}

.upload-link {
    color: #0d6efd;
    text-decoration: underline;
    cursor: pointer;
}

.upload-link:hover {
    color: #0056b3;
}

.upload-info {
    margin-top: 1rem;
}

/* Preview Styles */
.upload-preview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.preview-item {
    position: relative;
    border-radius: 0.5rem;
    overflow: hidden;
    background: #fff;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.preview-item:hover {
    border-color: #0d6efd;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
}

.preview-image {
    width: 100%;
    height: 120px;
    object-fit: cover;
    display: block;
}

.preview-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.preview-item:hover .preview-overlay {
    opacity: 1;
}

.preview-actions {
    display: flex;
    gap: 0.5rem;
}

.preview-btn {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #495057;
    transition: all 0.3s ease;
    cursor: pointer;
}

.preview-btn:hover {
    background: #fff;
    transform: scale(1.1);
}

.preview-btn.remove {
    background: rgba(220, 53, 69, 0.9);
    color: white;
}

.preview-btn.remove:hover {
    background: #dc3545;
}

.preview-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
    color: white;
    padding: 0.5rem;
    font-size: 0.75rem;
    transform: translateY(100%);
    transition: transform 0.3s ease;
}

.preview-item:hover .preview-info {
    transform: translateY(0);
}

/* Progress Bar */
.upload-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: rgba(13, 110, 253, 0.2);
    overflow: hidden;
}

.upload-progress-bar {
    height: 100%;
    background: #0d6efd;
    width: 0%;
    transition: width 0.3s ease;
}

/* Error States */
.upload-error {
    border-color: #dc3545;
    background: #f8d7da;
}

.preview-item.error {
    border-color: #dc3545;
}

.error-message {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .upload-area {
        padding: 1.5rem 1rem;
    }
    
    .upload-icon {
        font-size: 2rem;
    }
    
    .upload-text h6 {
        font-size: 1rem;
    }
    
    .upload-preview {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.75rem;
    }
    
    .preview-image {
        height: 100px;
    }
}

/* Dark Mode */
[data-bs-theme="dark"] .upload-area {
    background: #343a40;
    border-color: #495057;
}

[data-bs-theme="dark"] .upload-area:hover {
    background: #495057;
}

[data-bs-theme="dark"] .preview-item {
    background: #343a40;
    border-color: #495057;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeEnhancedUpload('{{ $uploadId }}');
});

function initializeEnhancedUpload(uploadId) {
    const uploadArea = document.getElementById(`upload-area-${uploadId}`);
    const fileInput = document.getElementById(uploadId);
    const previewContainer = document.getElementById(`preview-${uploadId}`);
    
    if (!uploadArea || !fileInput || !previewContainer) return;
    
    let selectedFiles = [];
    const maxFiles = {{ $maxFiles }};
    const maxSize = {{ $maxSize }} * 1024 * 1024; // Convert to bytes
    
    // Click to select files
    uploadArea.addEventListener('click', () => fileInput.click());
    
    // Drag and drop events
    uploadArea.addEventListener('dragover', handleDragOver);
    uploadArea.addEventListener('dragleave', handleDragLeave);
    uploadArea.addEventListener('drop', handleDrop);
    
    // File input change
    fileInput.addEventListener('change', (e) => handleFiles(e.target.files));
    
    function handleDragOver(e) {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
    }
    
    function handleDragLeave(e) {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
    }
    
    function handleDrop(e) {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        handleFiles(e.dataTransfer.files);
    }
    
    function handleFiles(files) {
        const fileArray = Array.from(files);
        
        // Validate file count
        if (selectedFiles.length + fileArray.length > maxFiles) {
            showError(`Chỉ được chọn tối đa ${maxFiles} ảnh`);
            return;
        }
        
        fileArray.forEach(file => {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                showError(`File "${file.name}" không phải là ảnh`);
                return;
            }
            
            // Validate file size
            if (file.size > maxSize) {
                showError(`File "${file.name}" quá lớn (tối đa {{ $maxSize }}MB)`);
                return;
            }
            
            selectedFiles.push(file);
            createPreview(file, selectedFiles.length - 1);
        });
        
        updateFileInput();
    }
    
    function createPreview(file, index) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            previewItem.innerHTML = `
                <img src="${e.target.result}" alt="${file.name}" class="preview-image">
                <div class="preview-overlay">
                    <div class="preview-actions">
                        <button type="button" class="preview-btn" onclick="viewImage('${e.target.result}', '${file.name}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="preview-btn remove" onclick="removeImage(${index}, '${uploadId}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="preview-info">
                    <div>${file.name}</div>
                    <div>${formatFileSize(file.size)}</div>
                </div>
            `;
            previewContainer.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    }
    
    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }
    
    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
        uploadArea.parentNode.appendChild(errorDiv);
        
        setTimeout(() => errorDiv.remove(), 5000);
    }
    
    // Global functions
    window.removeImage = function(index, uploadId) {
        if (uploadId === '{{ $uploadId }}') {
            selectedFiles.splice(index, 1);
            previewContainer.children[index].remove();
            updateFileInput();
            
            // Re-index remaining items
            Array.from(previewContainer.children).forEach((item, newIndex) => {
                const removeBtn = item.querySelector('.remove');
                if (removeBtn) {
                    removeBtn.setAttribute('onclick', `removeImage(${newIndex}, '${uploadId}')`);
                }
            });
        }
    };
    
    window.viewImage = function(src, name) {
        if (typeof Fancybox !== 'undefined') {
            Fancybox.show([{
                src: src,
                caption: name
            }]);
        }
    };
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}
</script>
