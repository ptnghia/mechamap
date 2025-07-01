@extends('admin.layouts.dason')

@section('title', 'Chỉnh Sửa Bài Viết')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Chỉnh Sửa Bài Viết</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.knowledge.index') }}">Cơ Sở Tri Thức</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.knowledge.articles') }}">Bài Viết</a></li>
                        <li class="breadcrumb-item active">Chỉnh Sửa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <form action="{{ route('admin.knowledge.articles.update', $article) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Thông Tin Bài Viết</h4>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu Đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $article->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Excerpt -->
                        <div class="mb-3">
                            <label for="excerpt" class="form-label">Tóm Tắt</label>
                            <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                      id="excerpt" name="excerpt" rows="3" 
                                      placeholder="Mô tả ngắn gọn về bài viết...">{{ old('excerpt', $article->excerpt) }}</textarea>
                            @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Nội Dung <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="15" required>{{ old('content', $article->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div class="mb-3">
                            <label for="tags" class="form-label">Thẻ Tag</label>
                            <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                   id="tags" name="tags" value="{{ old('tags', is_array($article->tags) ? implode(', ', $article->tags) : '') }}" 
                                   placeholder="Nhập các tag, cách nhau bởi dấu phẩy">
                            <div class="form-text">Ví dụ: cơ khí, thiết kế, bánh răng</div>
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Cài Đặt SEO</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_read_time" class="form-label">Thời Gian Đọc (phút)</label>
                                    <input type="number" class="form-control @error('estimated_read_time') is-invalid @enderror" 
                                           id="estimated_read_time" name="estimated_read_time" 
                                           value="{{ old('estimated_read_time', $article->estimated_read_time) }}" min="1">
                                    @error('estimated_read_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="article_type" class="form-label">Loại Bài Viết</label>
                                    <select class="form-select @error('article_type') is-invalid @enderror" 
                                            id="article_type" name="article_type">
                                        <option value="">Chọn loại bài viết</option>
                                        <option value="tutorial" {{ old('article_type', $article->article_type) == 'tutorial' ? 'selected' : '' }}>Hướng dẫn</option>
                                        <option value="best_practice" {{ old('article_type', $article->article_type) == 'best_practice' ? 'selected' : '' }}>Thực hành Tốt nhất</option>
                                        <option value="case_study" {{ old('article_type', $article->article_type) == 'case_study' ? 'selected' : '' }}>Nghiên cứu Tình huống</option>
                                        <option value="troubleshooting" {{ old('article_type', $article->article_type) == 'troubleshooting' ? 'selected' : '' }}>Khắc phục Sự cố</option>
                                        <option value="design_guide" {{ old('article_type', $article->article_type) == 'design_guide' ? 'selected' : '' }}>Hướng dẫn Thiết kế</option>
                                        <option value="calculation_method" {{ old('article_type', $article->article_type) == 'calculation_method' ? 'selected' : '' }}>Phương pháp Tính toán</option>
                                    </select>
                                    @error('article_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Publish Settings -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Cài Đặt Xuất Bản</h4>
                    </div>
                    <div class="card-body">
                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng Thái <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="draft" {{ old('status', $article->status) == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                                <option value="published" {{ old('status', $article->status) == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                                <option value="archived" {{ old('status', $article->status) == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh Mục <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Difficulty Level -->
                        <div class="mb-3">
                            <label for="difficulty_level" class="form-label">Độ Khó <span class="text-danger">*</span></label>
                            <select class="form-select @error('difficulty_level') is-invalid @enderror" 
                                    id="difficulty_level" name="difficulty_level" required>
                                <option value="beginner" {{ old('difficulty_level', $article->difficulty_level) == 'beginner' ? 'selected' : '' }}>Cơ bản</option>
                                <option value="intermediate" {{ old('difficulty_level', $article->difficulty_level) == 'intermediate' ? 'selected' : '' }}>Trung bình</option>
                                <option value="advanced" {{ old('difficulty_level', $article->difficulty_level) == 'advanced' ? 'selected' : '' }}>Nâng cao</option>
                            </select>
                            @error('difficulty_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Featured -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured" 
                                       name="is_featured" value="1" {{ old('is_featured', $article->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">
                                    Bài viết nổi bật
                                </label>
                            </div>
                        </div>

                        <!-- Article Info -->
                        <div class="mb-3">
                            <small class="text-muted">Tác giả:</small>
                            <p class="mb-1">{{ $article->author->name ?? 'N/A' }}</p>
                            <small class="text-muted">Ngày tạo:</small>
                            <p class="mb-1">{{ $article->created_at->format('d/m/Y H:i') }}</p>
                            @if($article->published_at)
                                <small class="text-muted">Ngày xuất bản:</small>
                                <p class="mb-1">{{ $article->published_at->format('d/m/Y H:i') }}</p>
                            @endif
                            <small class="text-muted">Lượt xem:</small>
                            <p class="mb-0">{{ number_format($article->views_count ?? 0) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Ảnh Đại Diện</h4>
                    </div>
                    <div class="card-body">
                        <!-- Current Image -->
                        @if($article->featured_image)
                            <div class="mb-3">
                                <label class="form-label">Ảnh hiện tại:</label>
                                <div class="text-center">
                                    <img src="{{ Storage::url($article->featured_image) }}" alt="Current Image" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                                   id="featured_image" name="featured_image" accept="image/*">
                            <div class="form-text">Định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB</div>
                            @error('featured_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <img id="previewImg" src="" alt="Preview" class="img-fluid rounded">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Cập Nhật Bài Viết
                            </button>
                            <a href="{{ route('admin.knowledge.articles') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Hủy
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Image preview
    $('#featured_image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#imagePreview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').hide();
        }
    });
    
    // Tags input enhancement
    $('#tags').on('keyup', function() {
        let tags = $(this).val().split(',');
        // Add tag validation or enhancement logic
    });
});
</script>
@endsection
