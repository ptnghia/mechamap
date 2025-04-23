@extends('admin.layouts.app')

@section('title', 'Tạo bài đăng mới')

@section('header', 'Tạo bài đăng mới')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Tạo bài đăng mới') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.threads.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="title" class="form-label">{{ __('Tiêu đề') }}</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="forum_id" class="form-label">{{ __('Diễn đàn') }}</label>
                    <select class="form-select @error('forum_id') is-invalid @enderror" id="forum_id" name="forum_id" required>
                        <option value="">{{ __('Chọn diễn đàn') }}</option>
                        @foreach($forums as $forum)
                            <option value="{{ $forum->id }}" {{ old('forum_id') == $forum->id ? 'selected' : '' }}>{{ $forum->name }}</option>
                        @endforeach
                    </select>
                    @error('forum_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="category_id" class="form-label">{{ __('Chuyên mục') }}</label>
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                        <option value="">{{ __('Chọn chuyên mục') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">{{ __('Nội dung') }}</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10" required>{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="status" class="form-label">{{ __('Trạng thái') }}</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>{{ __('Bản nháp') }}</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>{{ __('Chờ duyệt') }}</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>{{ __('Đã xuất bản') }}</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_sticky" name="is_sticky" {{ old('is_sticky') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_sticky">
                            {{ __('Ghim bài đăng') }}
                        </label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">
                            {{ __('Đánh dấu là bài nổi bật') }}
                        </label>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.threads.index') }}" class="btn btn-secondary">{{ __('Hủy') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Tạo bài đăng') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 400,
        plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        toolbar_mode: 'floating',
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
    });
</script>
@endpush
