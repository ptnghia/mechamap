@extends('admin.layouts.dason')

@section('title', 'Tạo tài liệu mới')

@push('styles')
<link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.css') }}" rel="stylesheet" type="text/css" />
<style>
.editor-container {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    min-height: 400px;
}
.form-check-input:checked {
    background-color: #556ee6;
    border-color: #556ee6;
}
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
}
</style>
@endpush

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Tạo tài liệu mới</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.documentation.index') }}">Tài liệu</a></li>
                            <li class="breadcrumb-item active">Tạo mới</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <form action="{{ route('admin.documentation.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Thông tin cơ bản</h4>
                        </div>
                        <div class="card-body">
                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Slug -->
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                       id="slug" name="slug" value="{{ old('slug') }}">
                                <div class="form-text">Để trống để tự động tạo từ tiêu đề</div>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Excerpt -->
                            <div class="mb-3">
                                <label for="excerpt" class="form-label">Tóm tắt</label>
                                <textarea class="form-control @error('excerpt') is-invalid @enderror"
                                          id="excerpt" name="excerpt" rows="3">{{ old('excerpt') }}</textarea>
                                @error('excerpt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Content -->
                            <div class="mb-3">
                                <label for="content" class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <div class="editor-container">
                                    <textarea class="form-control @error('content') is-invalid @enderror"
                                              id="content" name="content" rows="20" required>{{ old('content') }}</textarea>
                                </div>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tags -->
                            <div class="mb-3">
                                <label for="tags" class="form-label">Tags</label>
                                <input type="text" class="form-control" id="tags" name="tags"
                                       value="{{ old('tags') }}" data-role="tagsinput">
                                <div class="form-text">Nhấn Enter để thêm tag</div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Settings -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Cài đặt SEO</h4>
                        </div>
                        <div class="card-body">
                            <!-- Meta Title -->
                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                       id="meta_title" name="meta_title" value="{{ old('meta_title') }}">
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Meta Description -->
                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                          id="meta_description" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Meta Keywords -->
                            <div class="mb-3">
                                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror"
                                       id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}">
                                @error('meta_keywords')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Publish Settings -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Xuất bản</h4>
                        </div>
                        <div class="card-body">
                            <!-- Status -->
                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ old('status', 'draft') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Visibility -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_public" name="is_public"
                                           value="1" {{ old('is_public', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">
                                        Công khai
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                           value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Nổi bật
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="allow_comments" name="allow_comments"
                                           value="1" {{ old('allow_comments', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_comments">
                                        Cho phép bình luận
                                    </label>
                                </div>
                            </div>

                            <!-- Published Date -->
                            <div class="mb-3">
                                <label for="published_at" class="form-label">Ngày xuất bản</label>
                                <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror"
                                       id="published_at" name="published_at" value="{{ old('published_at') }}">
                                @error('published_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Category & Type -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Phân loại</h4>
                        </div>
                        <div class="card-body">
                            <!-- Category -->
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Danh mục</label>
                                <select class="form-select select2 @error('category_id') is-invalid @enderror"
                                        id="category_id" name="category_id">
                                    <option value="">Chọn danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Content Type -->
                            <div class="mb-3">
                                <label for="content_type" class="form-label">Loại nội dung</label>
                                <select class="form-select @error('content_type') is-invalid @enderror"
                                        id="content_type" name="content_type">
                                    @foreach($contentTypes as $type)
                                        <option value="{{ $type }}" {{ old('content_type', 'guide') == $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('content_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Difficulty Level -->
                            <div class="mb-3">
                                <label for="difficulty_level" class="form-label">Độ khó</label>
                                <select class="form-select @error('difficulty_level') is-invalid @enderror"
                                        id="difficulty_level" name="difficulty_level">
                                    @foreach($difficultyLevels as $level)
                                        <option value="{{ $level }}" {{ old('difficulty_level', 'beginner') == $level ? 'selected' : '' }}>
                                            {{ ucfirst($level) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('difficulty_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Target Roles -->
                            <div class="mb-3">
                                <label class="form-label">Đối tượng mục tiêu</label>
                                @foreach($userRoles as $role)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               id="role_{{ $role }}" name="target_roles[]" value="{{ $role }}"
                                               {{ in_array($role, old('target_roles', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role }}">
                                            {{ ucfirst(str_replace('_', ' ', $role)) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Lưu tài liệu
                                </button>
                                <button type="submit" name="action" value="save_and_continue" class="btn btn-success">
                                    <i class="fas fa-save"></i> Lưu và tiếp tục chỉnh sửa
                                </button>
                                <a href="{{ route('admin.documentation.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Hủy
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<!-- TinyMCE Editor - Self-hosted -->
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize TinyMCE
    tinymce.init({
        selector: '#content',
        height: 400,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Chọn danh mục',
        allowClear: true
    });

    // Auto-generate slug from title
    $('#title').on('input', function() {
        if ($('#slug').val() === '') {
            let slug = $(this).val()
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            $('#slug').val(slug);
        }
    });

    // Auto-generate meta title from title
    $('#title').on('input', function() {
        if ($('#meta_title').val() === '') {
            $('#meta_title').val($(this).val());
        }
    });

    // Auto-generate meta description from excerpt
    $('#excerpt').on('input', function() {
        if ($('#meta_description').val() === '') {
            $('#meta_description').val($(this).val());
        }
    });

    // Form validation
    $('form').on('submit', function(e) {
        let isValid = true;

        // Update TinyMCE content
        tinymce.triggerSave();

        // Check required fields
        if ($('#title').val().trim() === '') {
            $('#title').addClass('is-invalid');
            isValid = false;
        } else {
            $('#title').removeClass('is-invalid');
        }

        if ($('#content').val().trim() === '') {
            $('#content').addClass('is-invalid');
            isValid = false;
        } else {
            $('#content').removeClass('is-invalid');
        }

        if (!isValid) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
        }
    });
});
</script>
@endpush
