{{-- Rich Text Editor Component --}}
@props([
    'name' => 'content',
    'placeholder' => 'Viết nội dung...',
    'value' => '',
    'id' => null,
    'required' => false,
    'allowImages' => true,
    'minHeight' => '120px'
])

@php
    $editorId = $id ?? 'editor-' . uniqid();
@endphp

<div class="rich-text-editor" data-editor-id="{{ $editorId }}">
    {{-- Toolbar --}}
    <div class="editor-toolbar">
        <div class="toolbar-group">
            <button type="button" class="toolbar-btn" data-command="bold" title="Đậm">
                <i class="fas fa-bold"></i>
            </button>
            <button type="button" class="toolbar-btn" data-command="italic" title="Nghiêng">
                <i class="fas fa-italic"></i>
            </button>
            <button type="button" class="toolbar-btn" data-command="underline" title="Gạch chân">
                <i class="fas fa-underline"></i>
            </button>
        </div>
        
        <div class="toolbar-group">
            <button type="button" class="toolbar-btn" data-command="insertUnorderedList" title="Danh sách">
                <i class="fas fa-list-ul"></i>
            </button>
            <button type="button" class="toolbar-btn" data-command="insertOrderedList" title="Danh sách số">
                <i class="fas fa-list-ol"></i>
            </button>
        </div>

        <div class="toolbar-group">
            <button type="button" class="toolbar-btn" data-command="createLink" title="Liên kết">
                <i class="fas fa-link"></i>
            </button>
            @if($allowImages)
            <button type="button" class="toolbar-btn" data-command="insertImage" title="Chèn hình ảnh">
                <i class="fas fa-image"></i>
            </button>
            @endif
        </div>

        <div class="toolbar-group">
            <button type="button" class="toolbar-btn" data-command="undo" title="Hoàn tác">
                <i class="fas fa-undo"></i>
            </button>
            <button type="button" class="toolbar-btn" data-command="redo" title="Làm lại">
                <i class="fas fa-redo"></i>
            </button>
        </div>
    </div>

    {{-- Editor Content --}}
    <div class="editor-content" 
         id="{{ $editorId }}"
         contenteditable="true" 
         data-placeholder="{{ $placeholder }}"
         style="min-height: {{ $minHeight }}">
        {!! $value !!}
    </div>

    {{-- Hidden Input --}}
    <input type="hidden" name="{{ $name }}" id="{{ $editorId }}-input" value="{{ $value }}" {{ $required ? 'required' : '' }}>

    {{-- Image Upload Modal --}}
    @if($allowImages)
    <div class="modal fade" id="{{ $editorId }}-image-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chèn hình ảnh</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tải lên hình ảnh</label>
                        <input type="file" class="form-control" id="{{ $editorId }}-image-upload" 
                               accept="image/*" multiple>
                        <div class="form-text">Hỗ trợ: JPG, PNG, GIF. Tối đa 5MB mỗi file.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hoặc nhập URL hình ảnh</label>
                        <input type="url" class="form-control" id="{{ $editorId }}-image-url" 
                               placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="image-preview" id="{{ $editorId }}-image-preview"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="{{ $editorId }}-insert-image">Chèn</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Link Modal --}}
    <div class="modal fade" id="{{ $editorId }}-link-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm liên kết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <input type="url" class="form-control" id="{{ $editorId }}-link-url" 
                               placeholder="https://example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Văn bản hiển thị</label>
                        <input type="text" class="form-control" id="{{ $editorId }}-link-text" 
                               placeholder="Nhập văn bản...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="{{ $editorId }}-insert-link">Thêm</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rich-text-editor {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    overflow: hidden;
}

.editor-toolbar {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 0.5rem;
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.toolbar-group {
    display: flex;
    gap: 0.25rem;
}

.toolbar-btn {
    background: none;
    border: 1px solid transparent;
    border-radius: 0.25rem;
    padding: 0.375rem 0.5rem;
    color: #495057;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}

.toolbar-btn:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.toolbar-btn.active {
    background: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.editor-content {
    padding: 0.75rem;
    min-height: 120px;
    max-height: 400px;
    overflow-y: auto;
    outline: none;
    line-height: 1.5;
}

.editor-content:empty:before {
    content: attr(data-placeholder);
    color: #6c757d;
    font-style: italic;
}

.editor-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.25rem;
    margin: 0.25rem 0;
}

.image-preview {
    margin-top: 1rem;
}

.image-preview img {
    max-width: 100%;
    max-height: 200px;
    border-radius: 0.25rem;
}

.image-preview .preview-item {
    position: relative;
    display: inline-block;
    margin: 0.25rem;
}

.image-preview .remove-preview {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    cursor: pointer;
}
</style>
