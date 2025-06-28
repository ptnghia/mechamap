@extends('admin.layouts.dason')

@section('title', 'Tải lên Media')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Tải lên Media</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Tải lên Media</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Tải lên Media') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                
                <div class="mb-3">
                    <label for="files" class="form-label">{{ __('Chọn file') }} <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('files') is-invalid @enderror @error('files.*') is-invalid @enderror" id="files" name="files[]" multiple required>
                    <div class="form-text">{{ __('Hỗ trợ: JPG, PNG, GIF, SVG, PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, RAR. Kích thước tối đa: 10MB.') }}</div>
                    @error('files')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @error('files.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="title" class="form-label">{{ __('Tiêu đề') }}</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}">
                    <div class="form-text">{{ __('Để trống để sử dụng tên file.') }}</div>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('Mô tả') }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="preview-container" id="previewContainer">
                        <div class="row" id="previewRow"></div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.media.index') }}" class="btn btn-secondary">{{ __('Hủy') }}</a>
                    <button type="submit" class="btn btn-primary" id="uploadButton">
                        <i class="fas fa-upload me-1"></i> {{ __('Tải lên') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .preview-item {
        position: relative;
        margin-bottom: 15px;
    }
    .preview-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 5px;
    }
    .preview-item .preview-info {
        margin-top: 5px;
    }
    .preview-item .preview-remove {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0,0,0,0.5);
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .preview-item .preview-icon {
        width: 100%;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
        border-radius: 5px;
    }
    .preview-item .preview-pdf {
        background-color: #dc3545;
    }
    .preview-item .preview-doc {
        background-color: #0d6efd;
    }
    .preview-item .preview-xls {
        background-color: #198754;
    }
    .preview-item .preview-ppt {
        background-color: #fd7e14;
    }
    .preview-item .preview-zip {
        background-color: #6c757d;
    }
    .preview-item .preview-other {
        background-color: #6c757d;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Xử lý xem trước file
        $('#files').on('change', function() {
            const files = Array.from(this.files);
            const previewRow = $('#previewRow');
            previewRow.empty();
            
            files.forEach((file, index) => {
                const reader = new FileReader();
                const extension = file.name.split('.').pop().toLowerCase();
                
                const previewCol = $('<div class="col-md-3 preview-item"></div>');
                
                reader.onload = function(e) {
                    let previewContent = '';
                    
                    if (file.type.match('image.*')) {
                        previewContent = `<img src="${e.target.result}" alt="${file.name}">`;
                    } else if (extension === 'pdf') {
                        previewContent = `<div class="preview-icon preview-pdf"><i class="fas fa-file-pdf"></i></div>`;
                    } else if (['doc', 'docx'].includes(extension)) {
                        previewContent = `<div class="preview-icon preview-doc"><i class="fas fa-file-word"></i></div>`;
                    } else if (['xls', 'xlsx'].includes(extension)) {
                        previewContent = `<div class="preview-icon preview-xls"><i class="fas fa-file-excel"></i></div>`;
                    } else if (['ppt', 'pptx'].includes(extension)) {
                        previewContent = `<div class="preview-icon preview-ppt"><i class="fas fa-file-ppt"></i></div>`;
                    } else if (['zip', 'rar'].includes(extension)) {
                        previewContent = `<div class="preview-icon preview-zip"><i class="fas fa-file-zip"></i></div>`;
                    } else {
                        previewContent = `<div class="preview-icon preview-other"><i class="fas fa-file"></i></div>`;
                    }
                    
                    previewCol.html(`
                        ${previewContent}
                        <div class="preview-info">
                            <div class="small text-truncate">${file.name}</div>
                            <div class="small text-muted">${formatFileSize(file.size)}</div>
                        </div>
                        <button type="button" class="preview-remove" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                    `);
                    
                    previewRow.append(previewCol);
                };
                
                reader.readAsDataURL(file);
            });
        });
        
        // Xử lý xóa file khỏi danh sách
        $(document).on('click', '.preview-remove', function() {
            const index = $(this).data('index');
            const input = document.getElementById('files');
            const dt = new DataTransfer();
            
            const files = Array.from(input.files);
            files.forEach((file, i) => {
                if (i !== index) {
                    dt.items.add(file);
                }
            });
            
            input.files = dt.files;
            $(this).closest('.preview-item').remove();
        });
        
        // Định dạng kích thước file
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // Hiển thị loading khi submit form
        $('#uploadForm').on('submit', function() {
            $('#uploadButton').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __("Đang tải lên...") }}');
        });
    });
</script>
@endpush
