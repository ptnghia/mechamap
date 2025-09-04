{{-- TinyMCE Editor Component (Refactored: batch & lazy initialization) --}}
@props([
    'name',
    'id' => '',
    'value' => '',
    'placeholder' => 'Nhập nội dung của bạn...',
    'context' => 'comment',
    'height' => 200,
    'required' => false,
    'class' => '',
    'disabled' => false
])

@php
    $editorId = $id ?: ($name . '_' . uniqid());
    $editorClass = trim('form-control tinymce-editor tinymce-auto ' . $class);
@endphp

<div class="tinymce-editor-wrapper" data-context="{{ $context }}">
    <textarea
        name="{{ $name }}"
        id="{{ $editorId }}"
        class="{{ $editorClass }}"
        data-editor-id="{{ $editorId }}"
        data-context="{{ $context }}"
        data-height="{{ $height }}"
        data-placeholder="{{ $placeholder }}"
        data-required="{{ $required ? '1' : '0' }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes }}
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <div class="invalid-feedback d-block" id="{{ $editorId }}-error">
            {{ $message }}
        </div>
    @enderror

    <div class="tinymce-loading" id="{{ $editorId }}-loading" style="display:none;">
        <div class="d-flex align-items-center justify-content-center p-3">
            <div class="spinner-border spinner-border-sm me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span class="text-muted">Đang khởi tạo editor...</span>
        </div>
    </div>
</div>

@once
@push('scripts')
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('js/tinymce-config.js') }}"></script>
<script src="{{ asset('js/tinymce-uploader.js') }}"></script>
<script src="{{ asset('js/tinymce-batch-init.js') }}"></script>
@endpush
@endonce

