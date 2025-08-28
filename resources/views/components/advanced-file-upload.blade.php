{{-- Advanced File Upload Component with Full Configuration --}}
@props([
    'name' => 'files',
    'id' => null,
    'fileTypes' => ['jpg', 'png', 'gif', 'pdf'],
    'maxSize' => '5MB',
    'maxFiles' => 10,
    'multiple' => true,
    'dragDrop' => true,
    'showPreview' => true,
    'showProgress' => true,
    'required' => false,
    'accept' => null,
    'acceptDescription' => null,
    'uploadText' => null,
    'context' => 'default', // default|showcase|thread|product|document
    'class' => ''
])

@php
    $uploadId = $id ?? 'advanced-upload-' . uniqid();
    
    // Convert maxSize to bytes
    $maxSizeBytes = is_numeric($maxSize) ? $maxSize * 1024 * 1024 : 
        (int) filter_var($maxSize, FILTER_SANITIZE_NUMBER_INT) * 1024 * 1024;
    
    // Generate accept attribute
    $acceptAttr = $accept ?? implode(',', array_map(fn($type) => 
        in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? "image/{$type}" : 
        ($type === 'pdf' ? 'application/pdf' : 
        ($type === 'doc' ? 'application/msword' : 
        ($type === 'docx' ? 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' :
        ".{$type}"))), $fileTypes));
    
    // Context-based configurations
    $contextConfig = [
        'showcase' => [
            'uploadText' => 'Kéo thả file vào đây hoặc click để chọn',
            'acceptDescription' => 'Hình ảnh, tài liệu, file CAD',
            'icon' => 'fas fa-images'
        ],
        'thread' => [
            'uploadText' => 'Đính kèm file cho thảo luận',
            'acceptDescription' => 'Hình ảnh, tài liệu',
            'icon' => 'fas fa-paperclip'
        ],
        'product' => [
            'uploadText' => 'Upload hình ảnh sản phẩm',
            'acceptDescription' => 'Hình ảnh sản phẩm',
            'icon' => 'fas fa-camera'
        ],
        'document' => [
            'uploadText' => 'Upload tài liệu',
            'acceptDescription' => 'PDF, Word, Excel',
            'icon' => 'fas fa-file-alt'
        ],
        'default' => [
            'uploadText' => 'Chọn file để upload',
            'acceptDescription' => 'Các loại file được hỗ trợ',
            'icon' => 'fas fa-cloud-upload-alt'
        ]
    ];
    
    $config = $contextConfig[$context] ?? $contextConfig['default'];
    $finalUploadText = $uploadText ?? $config['uploadText'];
    $finalAcceptDescription = $acceptDescription ?? $config['acceptDescription'];
@endphp

<div class="advanced-file-upload {{ $class }}" 
     id="{{ $uploadId }}"
     data-name="{{ $name }}"
     data-file-types="{{ json_encode($fileTypes) }}"
     data-max-size="{{ $maxSizeBytes }}"
     data-max-files="{{ $maxFiles }}"
     data-multiple="{{ $multiple ? 'true' : 'false' }}"
     data-context="{{ $context }}">

    {{-- Upload Area --}}
    <div class="upload-area {{ $dragDrop ? 'drag-drop-enabled' : '' }}" 
         id="upload-area-{{ $uploadId }}">
        <div class="upload-content">
            <div class="upload-icon">
                <i class="{{ $config['icon'] }}"></i>
            </div>
            <div class="upload-text">
                <h6>{{ $finalUploadText }}</h6>
                <p class="text-muted mb-0">{{ $finalAcceptDescription }}</p>
            </div>
            <div class="upload-info">
                <small class="text-muted">
                    @if($multiple)
                        Tối đa {{ $maxFiles }} file • {{ $maxSize }} mỗi file
                    @else
                        Tối đa {{ $maxSize }}
                    @endif
                    • {{ strtoupper(implode(', ', $fileTypes)) }}
                </small>
            </div>
        </div>
        
        <input type="file" 
               name="{{ $multiple ? $name . '[]' : $name }}" 
               id="input-{{ $uploadId }}" 
               accept="{{ $acceptAttr }}"
               {{ $multiple ? 'multiple' : '' }}
               {{ $required ? 'required' : '' }}
               style="display: none;">
    </div>

    {{-- Progress Bar --}}
    @if($showProgress)
    <div class="upload-progress" id="progress-{{ $uploadId }}" style="display: none;">
        <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
        </div>
        <small class="text-muted mt-1">Đang upload...</small>
    </div>
    @endif

    {{-- Preview Area --}}
    @if($showPreview)
    <div class="upload-preview" id="preview-{{ $uploadId }}"></div>
    @endif

    {{-- Error Messages --}}
    <div class="upload-errors" id="errors-{{ $uploadId }}"></div>
</div>

<style>
.advanced-file-upload {
    margin-bottom: 1rem;
}

.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 0.5rem;
    padding: 2rem;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-area:hover,
.upload-area.drag-over {
    border-color: #007bff;
    background: #e3f2fd;
}

.upload-icon {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.upload-text h6 {
    color: #495057;
    margin-bottom: 0.5rem;
}

.upload-info {
    margin-top: 1rem;
}

.upload-progress {
    margin-top: 1rem;
}

.upload-preview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.preview-item {
    position: relative;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    overflow: hidden;
    background: #fff;
}

.preview-image {
    width: 100%;
    height: 120px;
    object-fit: cover;
}

.preview-file {
    padding: 1rem;
    text-align: center;
    height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.preview-file-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.preview-file-name {
    font-size: 0.8rem;
    color: #6c757d;
    word-break: break-word;
}

.preview-remove {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 0.8rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.preview-remove:hover {
    background: #dc3545;
}

.upload-errors {
    margin-top: 0.5rem;
}

.upload-error {
    background: #f8d7da;
    color: #721c24;
    padding: 0.5rem;
    border-radius: 0.25rem;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

/* Responsive */
@media (max-width: 768px) {
    .upload-area {
        padding: 1.5rem 1rem;
    }
    
    .upload-icon {
        font-size: 2rem;
    }
    
    .upload-preview {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.75rem;
    }
    
    .preview-image,
    .preview-file {
        height: 100px;
    }
}

/* Dark Mode */
[data-bs-theme="dark"] .upload-area {
    background: #343a40;
    border-color: #495057;
}

[data-bs-theme="dark"] .upload-area:hover,
[data-bs-theme="dark"] .upload-area.drag-over {
    background: #495057;
    border-color: #0d6efd;
}

[data-bs-theme="dark"] .preview-item {
    background: #343a40;
    border-color: #495057;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeAdvancedUpload('{{ $uploadId }}');
});

function initializeAdvancedUpload(uploadId) {
    const container = document.getElementById(uploadId);
    const uploadArea = document.getElementById(`upload-area-${uploadId}`);
    const fileInput = document.getElementById(`input-${uploadId}`);
    const previewContainer = document.getElementById(`preview-${uploadId}`);
    const progressContainer = document.getElementById(`progress-${uploadId}`);
    const errorsContainer = document.getElementById(`errors-${uploadId}`);
    
    if (!container || !uploadArea || !fileInput) return;
    
    const config = {
        maxFiles: parseInt(container.dataset.maxFiles),
        maxSize: parseInt(container.dataset.maxSize),
        fileTypes: JSON.parse(container.dataset.fileTypes),
        multiple: container.dataset.multiple === 'true',
        context: container.dataset.context
    };
    
    let selectedFiles = [];
    
    // Click to select files
    uploadArea.addEventListener('click', () => fileInput.click());
    
    // File input change
    fileInput.addEventListener('change', (e) => handleFiles(e.target.files));
    
    // Drag and drop events
    if (uploadArea.classList.contains('drag-drop-enabled')) {
        uploadArea.addEventListener('dragover', handleDragOver);
        uploadArea.addEventListener('dragleave', handleDragLeave);
        uploadArea.addEventListener('drop', handleDrop);
    }
    
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
        clearErrors();
        const fileArray = Array.from(files);
        
        // Validate file count
        if (!config.multiple && fileArray.length > 1) {
            showError('Chỉ được chọn 1 file');
            return;
        }
        
        if (config.multiple && selectedFiles.length + fileArray.length > config.maxFiles) {
            showError(`Chỉ được chọn tối đa ${config.maxFiles} file`);
            return;
        }
        
        fileArray.forEach(file => {
            // Validate file type
            const fileExt = file.name.split('.').pop().toLowerCase();
            if (!config.fileTypes.includes(fileExt)) {
                showError(`File "${file.name}" không được hỗ trợ`);
                return;
            }
            
            // Validate file size
            if (file.size > config.maxSize) {
                const maxSizeMB = Math.round(config.maxSize / 1024 / 1024);
                showError(`File "${file.name}" quá lớn (tối đa ${maxSizeMB}MB)`);
                return;
            }
            
            if (!config.multiple) {
                selectedFiles = [file];
                if (previewContainer) previewContainer.innerHTML = '';
            } else {
                selectedFiles.push(file);
            }
            
            createPreview(file, selectedFiles.length - 1);
        });
        
        updateFileInput();
    }
    
    function createPreview(file, index) {
        if (!previewContainer) return;
        
        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';
        
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.className = 'preview-image';
            img.src = URL.createObjectURL(file);
            previewItem.appendChild(img);
        } else {
            const fileDiv = document.createElement('div');
            fileDiv.className = 'preview-file';
            
            const icon = document.createElement('i');
            icon.className = `preview-file-icon ${getFileIcon(file.name)}`;
            
            const name = document.createElement('div');
            name.className = 'preview-file-name';
            name.textContent = file.name;
            
            fileDiv.appendChild(icon);
            fileDiv.appendChild(name);
            previewItem.appendChild(fileDiv);
        }
        
        // Remove button
        const removeBtn = document.createElement('button');
        removeBtn.className = 'preview-remove';
        removeBtn.innerHTML = '×';
        removeBtn.onclick = () => removeFile(index);
        previewItem.appendChild(removeBtn);
        
        previewContainer.appendChild(previewItem);
    }
    
    function removeFile(index) {
        selectedFiles.splice(index, 1);
        updatePreview();
        updateFileInput();
    }
    
    function updatePreview() {
        if (!previewContainer) return;
        previewContainer.innerHTML = '';
        selectedFiles.forEach((file, index) => createPreview(file, index));
    }
    
    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }
    
    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const iconMap = {
            pdf: 'fas fa-file-pdf text-danger',
            doc: 'fas fa-file-word text-primary',
            docx: 'fas fa-file-word text-primary',
            xls: 'fas fa-file-excel text-success',
            xlsx: 'fas fa-file-excel text-success',
            ppt: 'fas fa-file-powerpoint text-warning',
            pptx: 'fas fa-file-powerpoint text-warning',
            zip: 'fas fa-file-archive text-secondary',
            rar: 'fas fa-file-archive text-secondary',
            dwg: 'fas fa-drafting-compass text-info',
            dxf: 'fas fa-drafting-compass text-info',
            step: 'fas fa-cube text-info',
            stp: 'fas fa-cube text-info',
            stl: 'fas fa-cube text-info'
        };
        return iconMap[ext] || 'fas fa-file text-secondary';
    }
    
    function showError(message) {
        if (!errorsContainer) return;
        const errorDiv = document.createElement('div');
        errorDiv.className = 'upload-error';
        errorDiv.textContent = message;
        errorsContainer.appendChild(errorDiv);
    }
    
    function clearErrors() {
        if (errorsContainer) errorsContainer.innerHTML = '';
    }
}
</script>
