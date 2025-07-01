@extends('admin.layouts.dason')

@section('title', 'Chỉnh Sửa Video')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Chỉnh Sửa Video</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.knowledge.index') }}">Cơ Sở Tri Thức</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.knowledge.videos') }}">Video</a></li>
                        <li class="breadcrumb-item active">Chỉnh Sửa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <form action="{{ route('admin.knowledge.videos.update', $video) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Thông Tin Video</h4>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu Đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $video->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Video URL -->
                        <div class="mb-3">
                            <label for="video_url" class="form-label">URL Video <span class="text-danger">*</span></label>
                            <input type="url" class="form-control @error('video_url') is-invalid @enderror" 
                                   id="video_url" name="video_url" value="{{ old('video_url', $video->video_url) }}" 
                                   placeholder="https://www.youtube.com/watch?v=..." required>
                            <div class="form-text">Hỗ trợ YouTube, Vimeo hoặc link video trực tiếp</div>
                            @error('video_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Video Type -->
                        <div class="mb-3">
                            <label for="video_type" class="form-label">Loại Video <span class="text-danger">*</span></label>
                            <select class="form-select @error('video_type') is-invalid @enderror" 
                                    id="video_type" name="video_type" required>
                                <option value="youtube" {{ old('video_type', $video->video_type) == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                <option value="vimeo" {{ old('video_type', $video->video_type) == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                                <option value="local" {{ old('video_type', $video->video_type) == 'local' ? 'selected' : '' }}>Video nội bộ</option>
                            </select>
                            @error('video_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô Tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5" 
                                      placeholder="Mô tả chi tiết về nội dung video...">{{ old('description', $video->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Thời Lượng (giây)</label>
                                    <input type="number" class="form-control @error('duration') is-invalid @enderror" 
                                           id="duration" name="duration" value="{{ old('duration', $video->duration) }}" min="1">
                                    <div class="form-text">Ví dụ: 300 (cho video 5 phút)</div>
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="difficulty_level" class="form-label">Độ Khó <span class="text-danger">*</span></label>
                                    <select class="form-select @error('difficulty_level') is-invalid @enderror" 
                                            id="difficulty_level" name="difficulty_level" required>
                                        <option value="beginner" {{ old('difficulty_level', $video->difficulty_level) == 'beginner' ? 'selected' : '' }}>Cơ bản</option>
                                        <option value="intermediate" {{ old('difficulty_level', $video->difficulty_level) == 'intermediate' ? 'selected' : '' }}>Trung bình</option>
                                        <option value="advanced" {{ old('difficulty_level', $video->difficulty_level) == 'advanced' ? 'selected' : '' }}>Nâng cao</option>
                                    </select>
                                    @error('difficulty_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="mb-3">
                            <label for="tags" class="form-label">Thẻ Tag</label>
                            <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                   id="tags" name="tags" value="{{ old('tags', is_array($video->tags) ? implode(', ', $video->tags) : '') }}" 
                                   placeholder="Nhập các tag, cách nhau bởi dấu phẩy">
                            <div class="form-text">Ví dụ: tutorial, autocad, thiết kế</div>
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Video Preview -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Xem Trước Video</h4>
                    </div>
                    <div class="card-body">
                        <div id="videoPreview" class="ratio ratio-16x9">
                            <iframe id="previewFrame" src="{{ $video->embed_url }}" frameborder="0" allowfullscreen></iframe>
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
                                <option value="draft" {{ old('status', $video->status) == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                                <option value="published" {{ old('status', $video->status) == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                                <option value="archived" {{ old('status', $video->status) == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
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
                                    <option value="{{ $category->id }}" {{ old('category_id', $video->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Featured -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured" 
                                       name="is_featured" value="1" {{ old('is_featured', $video->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">
                                    Video nổi bật
                                </label>
                            </div>
                        </div>

                        <!-- Video Info -->
                        <div class="mb-3">
                            <small class="text-muted">Tác giả:</small>
                            <p class="mb-1">{{ $video->author->name ?? 'N/A' }}</p>
                            <small class="text-muted">Ngày tạo:</small>
                            <p class="mb-1">{{ $video->created_at->format('d/m/Y H:i') }}</p>
                            @if($video->published_at)
                                <small class="text-muted">Ngày xuất bản:</small>
                                <p class="mb-1">{{ $video->published_at->format('d/m/Y H:i') }}</p>
                            @endif
                            <small class="text-muted">Lượt xem:</small>
                            <p class="mb-0">{{ number_format($video->views_count ?? 0) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Thumbnail -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Ảnh Thumbnail</h4>
                    </div>
                    <div class="card-body">
                        <!-- Current Thumbnail -->
                        @if($video->thumbnail)
                            <div class="mb-3">
                                <label class="form-label">Thumbnail hiện tại:</label>
                                <div class="text-center">
                                    <img src="{{ Storage::url($video->thumbnail) }}" alt="Current Thumbnail" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" 
                                   id="thumbnail" name="thumbnail" accept="image/*">
                            <div class="form-text">Định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB</div>
                            @error('thumbnail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Thumbnail Preview -->
                        <div id="thumbnailPreview" class="mt-3" style="display: none;">
                            <img id="previewThumbnail" src="" alt="Preview" class="img-fluid rounded">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Cập Nhật Video
                            </button>
                            <a href="{{ route('admin.knowledge.videos') }}" class="btn btn-secondary">
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
    // Video URL preview
    $('#video_url').on('blur', function() {
        const url = $(this).val();
        if (url) {
            const embedUrl = getEmbedUrl(url);
            if (embedUrl) {
                $('#previewFrame').attr('src', embedUrl);
                
                // Auto-detect video type
                if (url.includes('youtube.com') || url.includes('youtu.be')) {
                    $('#video_type').val('youtube');
                } else if (url.includes('vimeo.com')) {
                    $('#video_type').val('vimeo');
                } else {
                    $('#video_type').val('local');
                }
            }
        }
    });
    
    // Thumbnail preview
    $('#thumbnail').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewThumbnail').attr('src', e.target.result);
                $('#thumbnailPreview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#thumbnailPreview').hide();
        }
    });
    
    // Tags input enhancement
    $('#tags').on('keyup', function() {
        let tags = $(this).val().split(',');
        // Add tag validation or enhancement logic
    });
});

function getEmbedUrl(url) {
    // YouTube
    let youtubeMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/);
    if (youtubeMatch) {
        return `https://www.youtube.com/embed/${youtubeMatch[1]}`;
    }
    
    // Vimeo
    let vimeoMatch = url.match(/vimeo\.com\/(\d+)/);
    if (vimeoMatch) {
        return `https://player.vimeo.com/video/${vimeoMatch[1]}`;
    }
    
    // Direct video URL
    if (url.match(/\.(mp4|webm|ogg)$/)) {
        return url;
    }
    
    return null;
}
</script>
@endsection
