@extends('admin.layouts.dason')

@section('title', 'Chỉnh sửa bài đăng')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Chỉnh sửa bài đăng</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa bài đăng</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Chỉnh sửa bài đăng') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.threads.update', $thread) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label">{{ 'Tiêu đề' }}</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $thread->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="forum_id" class="form-label">{{ __('Diễn đàn') }}</label>
                    <select class="form-select @error('forum_id') is-invalid @enderror" id="forum_id" name="forum_id" required>
                        <option value="">{{ 'Chọn diễn đàn' }}</option>
                        @foreach($forums as $forum)
                            <option value="{{ $forum->id }}" {{ old('forum_id', $thread->forum_id) == $forum->id ? 'selected' : '' }}>{{ $forum->name }}</option>
                        @endforeach
                    </select>
                    @error('forum_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">{{ 'Chuyên mục' }}</label>
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                        <option value="">{{ 'Chọn chuyên mục' }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $thread->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">{{ 'Nội dung' }}</label>
                    <x-tinymce-editor
                        name="content"
                        id="content"
                        :value="old('content', $thread->content)"
                        placeholder="Nhập nội dung thread..."
                        context="admin"
                        :height="400"
                        :required="true"
                        class="@error('content') is-invalid @enderror"
                    />
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">{{ 'Trạng thái' }}</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="draft" {{ old('status', $thread->status) == 'draft' ? 'selected' : '' }}>{{ 'Bản nháp' }}</option>
                        <option value="pending" {{ old('status', $thread->status) == 'pending' ? 'selected' : '' }}>{{ 'Chờ duyệt' }}</option>
                        <option value="published" {{ old('status', $thread->status) == 'published' ? 'selected' : '' }}>{{ 'Đã xuất bản' }}</option>
                        <option value="rejected" {{ old('status', $thread->status) == 'rejected' ? 'selected' : '' }}>{{ 'Đã từ chối' }}</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_sticky" name="is_sticky" {{ old('is_sticky', $thread->is_sticky) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_sticky">
                            {{ 'Ghim bài đăng' }}
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" {{ old('is_featured', $thread->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">
                            {{ 'Đánh dấu là bài nổi bật' }}
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.threads.index') }}" class="btn btn-secondary">{{ 'Hủy' }}</a>
                    <button type="submit" class="btn btn-primary">{{ 'Cập nhật' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

{{-- TinyMCE is now handled by the component --}}
