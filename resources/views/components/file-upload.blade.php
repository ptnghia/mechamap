{{--
    File Upload Component
    Component upload file có thể tái sử dụng cho toàn bộ hệ thống MechaMap

    @param string $name - Tên của input field (default: 'files')
    @param array $fileTypes - Các loại file được phép upload (default: ['jpg', 'png', 'gif', 'pdf'])
    @param string|int $maxSize - Dung lượng tối đa cho mỗi file (default: '5MB')
    @param bool $multiple - Cho phép upload nhiều file (default: false)
    @param string|null $accept - MIME types được chấp nhận (auto-generate nếu null)
    @param bool $required - Trường bắt buộc (default: false)
    @param string|null $label - Label cho input (default: auto-generate)
    @param string|null $helpText - Text hướng dẫn (default: auto-generate)
    @param int $maxFiles - Số file tối đa khi multiple=true (default: 10)
    @param bool $showProgress - Hiển thị progress bar (default: true)
    @param bool $showPreview - Hiển thị preview file (default: true)
    @param bool $dragDrop - Cho phép drag & drop (default: true)
    @param string|null $id - ID của component (auto-generate nếu null)
--}}

@props([
    'name' => 'files',
    'fileTypes' => ['jpg', 'png', 'gif', 'pdf'],
    'maxSize' => '5MB',
    'multiple' => false,
    'accept' => null,
    'required' => false,
    'label' => null,
    'helpText' => null,
    'maxFiles' => 10,
    'showProgress' => true,
    'showPreview' => true,
    'dragDrop' => true,
    'id' => null
])

@php
    // Generate unique ID nếu không được cung cấp
    $componentId = $id ?? 'file-upload-' . uniqid();

    // Generate accept attribute từ fileTypes nếu không được cung cấp
    if (!$accept) {
        $mimeTypes = [];
        foreach ($fileTypes as $type) {
            switch (strtolower($type)) {
                case 'jpg':
                case 'jpeg':
                    $mimeTypes[] = 'image/jpeg';
                    break;
                case 'png':
                    $mimeTypes[] = 'image/png';
                    break;
                case 'gif':
                    $mimeTypes[] = 'image/gif';
                    break;
                case 'webp':
                    $mimeTypes[] = 'image/webp';
                    break;
                case 'pdf':
                    $mimeTypes[] = 'application/pdf';
                    break;
                case 'doc':
                    $mimeTypes[] = 'application/msword';
                    break;
                case 'docx':
                    $mimeTypes[] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                    break;
                default:
                    // Cho các file extension khác (CAD files, etc.)
                    $mimeTypes[] = '.' . strtolower($type);
            }
        }
        $accept = implode(',', array_unique($mimeTypes));
    }

    // Parse maxSize thành bytes
    $maxSizeBytes = $maxSize;
    if (is_string($maxSize)) {
        $maxSize = strtoupper($maxSize);
        if (str_contains($maxSize, 'MB')) {
            $maxSizeBytes = (int) str_replace('MB', '', $maxSize) * 1024 * 1024;
        } elseif (str_contains($maxSize, 'KB')) {
            $maxSizeBytes = (int) str_replace('KB', '', $maxSize) * 1024;
        } elseif (str_contains($maxSize, 'GB')) {
            $maxSizeBytes = (int) str_replace('GB', '', $maxSize) * 1024 * 1024 * 1024;
        } else {
            $maxSizeBytes = (int) $maxSize;
        }
    }

    // Generate label nếu không được cung cấp
    if (!$label) {
        $label = $multiple ? __('forms.upload.attach_files') : __('forms.upload.attach_file');
        if ($required) {
            $label .= ' <span class="text-danger">*</span>';
        } else {
            $label .= ' <small class="text-muted">(' . __('forms.upload.optional') . ')</small>';
        }
    }

    // Generate help text nếu không được cung cấp
    if (!$helpText) {
        $typesList = implode(', ', array_map('strtoupper', $fileTypes));
        $helpText = __('forms.upload.supported_formats_with_size', [
            'formats' => $typesList,
            'size' => is_string($maxSize) ? $maxSize : number_format($maxSizeBytes / (1024 * 1024), 1) . 'MB'
        ]);
    }
@endphp

<!-- File Upload Component -->
<div class="file-upload-component"
     id="{{ $componentId }}"
     data-name="{{ $name }}"
     data-file-types="{{ json_encode($fileTypes) }}"
     data-max-size="{{ $maxSizeBytes }}"
     data-multiple="{{ $multiple ? 'true' : 'false' }}"
     data-max-files="{{ $maxFiles }}"
     data-show-progress="{{ $showProgress ? 'true' : 'false' }}"
     data-show-preview="{{ $showPreview ? 'true' : 'false' }}"
     data-drag-drop="{{ $dragDrop ? 'true' : 'false' }}">

    <!-- Label -->
    @if($label)
    <label for="{{ $componentId }}-input" class="form-label">
        <i class="fas fa-paperclip me-1"></i>
        {!! $label !!}
    </label>
    @endif

    <!-- Upload Area -->
    <div class="file-upload-area border rounded p-3 {{ $dragDrop ? 'drag-drop-enabled' : '' }}"
         id="{{ $componentId }}-area">

        <!-- Upload Zone -->
        <div class="upload-zone text-center py-3 {{ $dragDrop ? 'clickable' : '' }}"
             id="{{ $componentId }}-zone">

            <div class="upload-content">
                <div class="upload-icon mb-2">
                    <i class="fas fa-cloud-upload-alt fs-2 text-muted"></i>
                </div>
                <div class="upload-text">
                    @if($dragDrop)
                        <h6 class="mb-1">{{ __('forms.upload.drag_drop_here') }}</h6>
                        <p class="text-muted mb-2">
                            {{ __('forms.upload.or') }}
                            <button type="button" class="btn btn-link p-0 text-primary fw-semibold"
                                    id="{{ $componentId }}-browse">
                                {{ __('forms.upload.select_from_computer') }}
                            </button>
                        </p>
                    @else
                        <button type="button" class="btn btn-primary" id="{{ $componentId }}-browse">
                            <i class="fas fa-folder-open me-2"></i>
                            {{ __('forms.upload.select_files') }}
                        </button>
                    @endif
                    <small class="text-muted d-block">{{ $helpText }}</small>
                </div>
            </div>

            <!-- Hidden File Input -->
            <input type="file"
                   class="file-input d-none"
                   id="{{ $componentId }}-input"
                   name="{{ $multiple ? $name . '[]' : $name }}"
                   accept="{{ $accept }}"
                   {{ $multiple ? 'multiple' : '' }}
                   {{ $required ? 'required' : '' }}>
        </div>

        <!-- File Previews Container -->
        @if($showPreview)
        <div class="file-previews mt-3 d-none" id="{{ $componentId }}-previews">
            <h6 class="mb-2">
                <i class="fas fa-files me-1"></i>
                {{ __('forms.upload.files_selected') }}
            </h6>
            <div class="row g-2" id="{{ $componentId }}-preview-container"></div>
        </div>
        @endif

        <!-- Upload Progress -->
        @if($showProgress)
        <div class="upload-progress mt-2 d-none" id="{{ $componentId }}-progress">
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated"
                     role="progressbar" style="width: 0%"></div>
            </div>
            <small class="text-muted mt-1">{{ __('forms.upload.uploading') }}</small>
        </div>
        @endif
    </div>

    <!-- Error Messages Container -->
    <div class="upload-errors mt-2" id="{{ $componentId }}-errors"></div>
</div>

<!-- Include CSS và JavaScript -->
@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset_versioned('css/frontend/components/file-upload.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset_versioned('js/frontend/components/file-upload.js') }}"></script>
    @endpush
@endonce

<!-- Initialize Component -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof FileUploadComponent !== 'undefined') {
        // Check if component is already initialized
        const componentElement = document.getElementById('{{ $componentId }}');
        if (componentElement && !componentElement.hasAttribute('data-file-upload-initialized')) {
            new FileUploadComponent('{{ $componentId }}');
            componentElement.setAttribute('data-file-upload-initialized', 'true');
        }
    }
});
</script>
@endpush
