{{-- Simple Avatar Upload Component --}}
@props([
    'name' => 'avatar',
    'id' => null,
    'currentAvatar' => null,
    'size' => 120,
    'maxSize' => '2MB',
    'required' => false,
    'shape' => 'circle', // circle|square|rounded
    'showRemove' => true,
    'placeholderText' => 'Click to upload avatar',
    'class' => '',
    'uploadUrl' => null, // For AJAX upload
    'previewOnly' => false // Only show preview, no upload functionality
])

@php
    $uploadId = $id ?? 'avatar-upload-' . uniqid();
    $maxSizeBytes = is_numeric($maxSize) ? $maxSize * 1024 * 1024 : 
        (int) filter_var($maxSize, FILTER_SANITIZE_NUMBER_INT) * 1024 * 1024;
    
    $shapeClass = match($shape) {
        'circle' => 'rounded-circle',
        'square' => '',
        'rounded' => 'rounded',
        default => 'rounded-circle'
    };
    
    $defaultAvatar = $currentAvatar ?: "https://ui-avatars.com/api/?name=" . urlencode(auth()->user()->name ?? 'User') . "&background=6366f1&color=fff&size={$size}";
@endphp

<div class="avatar-upload-component {{ $class }}" 
     id="{{ $uploadId }}"
     data-max-size="{{ $maxSizeBytes }}"
     data-upload-url="{{ $uploadUrl }}">

    {{-- Avatar Preview --}}
    <div class="avatar-preview-container" style="width: {{ $size }}px; height: {{ $size }}px;">
        <div class="avatar-preview {{ $shapeClass }}" 
             style="width: {{ $size }}px; height: {{ $size }}px;">
            <img id="avatar-img-{{ $uploadId }}" 
                 src="{{ $defaultAvatar }}" 
                 alt="Avatar" 
                 class="avatar-image {{ $shapeClass }}"
                 style="width: 100%; height: 100%; object-fit: cover;">
            
            @if(!$previewOnly)
            {{-- Upload Overlay --}}
            <div class="avatar-overlay {{ $shapeClass }}" 
                 onclick="document.getElementById('avatar-input-{{ $uploadId }}').click()">
                <div class="avatar-overlay-content">
                    <i class="fas fa-camera"></i>
                    <small>{{ $placeholderText }}</small>
                </div>
            </div>
            @endif
        </div>
        
        @if($showRemove && !$previewOnly)
        {{-- Remove Button --}}
        <button type="button" 
                class="avatar-remove-btn" 
                id="remove-btn-{{ $uploadId }}"
                onclick="removeAvatar('{{ $uploadId }}')"
                style="display: {{ $currentAvatar ? 'block' : 'none' }}">
            <i class="fas fa-times"></i>
        </button>
        @endif
    </div>

    @if(!$previewOnly)
    {{-- Hidden File Input --}}
    <input type="file" 
           id="avatar-input-{{ $uploadId }}"
           name="{{ $name }}"
           accept="image/*"
           {{ $required ? 'required' : '' }}
           style="display: none;"
           onchange="handleAvatarChange('{{ $uploadId }}', this)">
    
    {{-- Upload Progress --}}
    <div class="avatar-progress" id="progress-{{ $uploadId }}" style="display: none;">
        <div class="progress mt-2">
            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
        </div>
        <small class="text-muted">Đang upload...</small>
    </div>
    
    {{-- Error Messages --}}
    <div class="avatar-errors" id="errors-{{ $uploadId }}"></div>
    
    {{-- Help Text --}}
    <small class="text-muted d-block mt-2">
        Tối đa {{ $maxSize }} • JPG, PNG, GIF
    </small>
    @endif
</div>

<style>
.avatar-upload-component {
    display: inline-block;
    position: relative;
}

.avatar-preview-container {
    position: relative;
    display: inline-block;
}

.avatar-preview {
    position: relative;
    overflow: hidden;
    border: 3px solid #dee2e6;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s ease;
}

.avatar-preview:hover {
    border-color: #007bff;
    transform: scale(1.02);
}

.avatar-image {
    transition: all 0.3s ease;
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    cursor: pointer;
}

.avatar-preview:hover .avatar-overlay {
    opacity: 1;
}

.avatar-overlay-content {
    text-align: center;
    color: white;
}

.avatar-overlay-content i {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
    display: block;
}

.avatar-overlay-content small {
    font-size: 0.75rem;
    display: block;
}

.avatar-remove-btn {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #dc3545;
    color: white;
    border: 2px solid white;
    font-size: 0.75rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 10;
}

.avatar-remove-btn:hover {
    background: #c82333;
    transform: scale(1.1);
}

.avatar-progress {
    margin-top: 0.5rem;
}

.avatar-errors {
    margin-top: 0.5rem;
}

.avatar-error {
    background: #f8d7da;
    color: #721c24;
    padding: 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

/* Size Variants */
.avatar-upload-component.size-sm .avatar-preview {
    width: 60px;
    height: 60px;
}

.avatar-upload-component.size-lg .avatar-preview {
    width: 150px;
    height: 150px;
}

.avatar-upload-component.size-xl .avatar-preview {
    width: 200px;
    height: 200px;
}

/* Dark Mode */
[data-bs-theme="dark"] .avatar-preview {
    border-color: #495057;
    background: #343a40;
}

[data-bs-theme="dark"] .avatar-preview:hover {
    border-color: #0d6efd;
}

[data-bs-theme="dark"] .avatar-error {
    background: #842029;
    color: #ea868f;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .avatar-overlay-content small {
        font-size: 0.7rem;
    }
    
    .avatar-overlay-content i {
        font-size: 1.25rem;
    }
}
</style>

<script>
function handleAvatarChange(uploadId, input) {
    const file = input.files[0];
    if (!file) return;
    
    const container = document.getElementById(uploadId);
    const maxSize = parseInt(container.dataset.maxSize);
    const uploadUrl = container.dataset.uploadUrl;
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        showAvatarError(uploadId, 'Vui lòng chọn file hình ảnh');
        input.value = '';
        return;
    }
    
    // Validate file size
    if (file.size > maxSize) {
        const maxSizeMB = Math.round(maxSize / 1024 / 1024);
        showAvatarError(uploadId, `File quá lớn (tối đa ${maxSizeMB}MB)`);
        input.value = '';
        return;
    }
    
    clearAvatarErrors(uploadId);
    
    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.getElementById(`avatar-img-${uploadId}`);
        const removeBtn = document.getElementById(`remove-btn-${uploadId}`);
        
        img.src = e.target.result;
        if (removeBtn) removeBtn.style.display = 'block';
    };
    reader.readAsDataURL(file);
    
    // Auto upload if URL provided
    if (uploadUrl) {
        uploadAvatar(uploadId, file, uploadUrl);
    }
}

function removeAvatar(uploadId) {
    const img = document.getElementById(`avatar-img-${uploadId}`);
    const input = document.getElementById(`avatar-input-${uploadId}`);
    const removeBtn = document.getElementById(`remove-btn-${uploadId}`);
    
    // Reset to default avatar
    const defaultSrc = img.src.includes('ui-avatars.com') ? 
        img.src : 
        `https://ui-avatars.com/api/?name=${encodeURIComponent('User')}&background=6366f1&color=fff&size=120`;
    
    img.src = defaultSrc;
    input.value = '';
    if (removeBtn) removeBtn.style.display = 'none';
    
    clearAvatarErrors(uploadId);
}

function uploadAvatar(uploadId, file, uploadUrl) {
    const progressContainer = document.getElementById(`progress-${uploadId}`);
    const progressBar = progressContainer?.querySelector('.progress-bar');
    
    if (progressContainer) progressContainer.style.display = 'block';
    
    const formData = new FormData();
    formData.append('avatar', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content);
    
    fetch(uploadUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (progressContainer) progressContainer.style.display = 'none';
        
        if (data.success) {
            // Update avatar if new URL provided
            if (data.avatar_url) {
                const img = document.getElementById(`avatar-img-${uploadId}`);
                img.src = data.avatar_url;
            }
            
            // Show success message
            if (typeof showNotification === 'function') {
                showNotification('Avatar đã được cập nhật', 'success');
            }
        } else {
            showAvatarError(uploadId, data.message || 'Lỗi upload avatar');
        }
    })
    .catch(error => {
        if (progressContainer) progressContainer.style.display = 'none';
        showAvatarError(uploadId, 'Lỗi kết nối. Vui lòng thử lại.');
        console.error('Upload error:', error);
    });
}

function showAvatarError(uploadId, message) {
    const errorsContainer = document.getElementById(`errors-${uploadId}`);
    if (!errorsContainer) return;
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'avatar-error';
    errorDiv.textContent = message;
    errorsContainer.appendChild(errorDiv);
}

function clearAvatarErrors(uploadId) {
    const errorsContainer = document.getElementById(`errors-${uploadId}`);
    if (errorsContainer) errorsContainer.innerHTML = '';
}

// Global function for external use
window.updateAvatarPreview = function(uploadId, imageUrl) {
    const img = document.getElementById(`avatar-img-${uploadId}`);
    const removeBtn = document.getElementById(`remove-btn-${uploadId}`);
    
    if (img) img.src = imageUrl;
    if (removeBtn) removeBtn.style.display = imageUrl ? 'block' : 'none';
};
</script>
