@extends('admin.layouts.dason')

@section('title', 'Chỉnh sửa tài liệu')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">✏️ Chỉnh sửa tài liệu</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.documentation.index') }}">Tài liệu</a></li>
                        <li class="breadcrumb-item active">Chỉnh sửa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.documentation.update', $documentation) }}" method="POST" enctype="multipart/form-data" id="documentationForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Thông tin cơ bản</h4>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $documentation->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                   id="slug" name="slug" value="{{ old('slug', $documentation->slug) }}">
                            <div class="form-text">Để trống để tự động tạo từ tiêu đề</div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Excerpt -->
                        <div class="mb-3">
                            <label for="excerpt" class="form-label">Tóm tắt</label>
                            <textarea class="form-control @error('excerpt') is-invalid @enderror"
                                      id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $documentation->excerpt) }}</textarea>
                            @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      id="content" name="content" rows="15" required>{{ old('content', $documentation->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags</label>
                            <input type="text" class="form-control @error('tags') is-invalid @enderror"
                                   id="tags" name="tags" value="{{ old('tags', is_array($documentation->tags) ? implode(', ', $documentation->tags) : '') }}">
                            <div class="form-text">Nhấn Enter để thêm tag</div>
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                   id="meta_title" name="meta_title" value="{{ old('meta_title', $documentation->meta_title) }}">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Meta Description -->
                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                      id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $documentation->meta_description) }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Meta Keywords -->
                        <div class="mb-3">
                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror"
                                   id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $documentation->meta_keywords) }}">
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
                            <label class="form-label">Trạng thái</label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1"
                                           {{ old('is_public', $documentation->is_public) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">Công khai</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                                           {{ old('is_featured', $documentation->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">Nổi bật</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="allow_comments" name="allow_comments" value="1"
                                           {{ old('allow_comments', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_comments">Cho phép bình luận</label>
                                </div>
                            </div>
                        </div>

                        <!-- Publish Date -->
                        <div class="mb-3">
                            <label for="published_at" class="form-label">Ngày xuất bản</label>
                            <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror"
                                   id="published_at" name="published_at"
                                   value="{{ old('published_at', $documentation->published_at ? $documentation->published_at->format('Y-m-d\TH:i') : '') }}">
                            @error('published_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Classification -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Phân loại</h4>
                    </div>
                    <div class="card-body">
                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh mục</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $documentation->category_id) == $category->id ? 'selected' : '' }}>
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
                            <select class="form-select @error('content_type') is-invalid @enderror" id="content_type" name="content_type" required>
                                <option value="guide" {{ old('content_type', $documentation->content_type) == 'guide' ? 'selected' : '' }}>Guide</option>
                                <option value="api" {{ old('content_type', $documentation->content_type) == 'api' ? 'selected' : '' }}>API</option>
                                <option value="tutorial" {{ old('content_type', $documentation->content_type) == 'tutorial' ? 'selected' : '' }}>Tutorial</option>
                                <option value="reference" {{ old('content_type', $documentation->content_type) == 'reference' ? 'selected' : '' }}>Reference</option>
                                <option value="faq" {{ old('content_type', $documentation->content_type) == 'faq' ? 'selected' : '' }}>FAQ</option>
                            </select>
                            @error('content_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Difficulty Level -->
                        <div class="mb-3">
                            <label for="difficulty_level" class="form-label">Độ khó</label>
                            <select class="form-select @error('difficulty_level') is-invalid @enderror" id="difficulty_level" name="difficulty_level" required>
                                <option value="beginner" {{ old('difficulty_level', $documentation->difficulty_level) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('difficulty_level', $documentation->difficulty_level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('difficulty_level', $documentation->difficulty_level) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                <option value="expert" {{ old('difficulty_level', $documentation->difficulty_level) == 'expert' ? 'selected' : '' }}>Expert</option>
                            </select>
                            @error('difficulty_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Target Audience -->
                        <div class="mb-3">
                            <label class="form-label">Đối tượng mục tiêu</label>
                            @php
                                $roles = ['admin', 'moderator', 'senior_member', 'member', 'supplier', 'manufacturer', 'brand'];
                                $selectedRoles = old('allowed_roles', $documentation->allowed_roles ?? []);
                            @endphp
                            @foreach($roles as $role)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="role_{{ $role }}"
                                           name="allowed_roles[]" value="{{ $role }}"
                                           {{ in_array($role, $selectedRoles) ? 'checked' : '' }}>
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
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save"></i> Cập nhật tài liệu
                        </button>
                        <button type="submit" name="continue_editing" value="1" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-edit"></i> Lưu và tiếp tục chỉnh sửa
                        </button>
                        <a href="{{ route('admin.documentation.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- TinyMCE Editor - Self-hosted -->
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE
    tinymce.init({
        license_key: 'gpl',
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

    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function() {
        const title = this.value;
        const slug = title.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    });

    // Tags input
    const tagsInput = document.getElementById('tags');
    tagsInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            // Add tag logic here if needed
        }
    });
});
</script>
@endpush
