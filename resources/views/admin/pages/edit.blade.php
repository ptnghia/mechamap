@extends('admin.layouts.dason')

@section('title', 'Chỉnh sửa bài viết')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Chỉnh sửa bài viết</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa bài viết</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Chỉnh sửa bài viết') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pages.update', $page) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Tiêu đề') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $page->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="excerpt" class="form-label">{{ __('Tóm tắt') }}</label>
                            <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $page->excerpt) }}</textarea>
                            @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">{{ __('Nội dung') }} <span class="text-danger">*</span></label>
                            <x-tinymce-editor
                                name="content"
                                id="content"
                                :value="old('content', $page->content)"
                                placeholder="Nhập nội dung trang..."
                                context="admin"
                                :height="500"
                                :required="true"
                                class="@error('content') is-invalid @enderror"
                            />
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">{{ __('Danh mục') }} <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">{{ __('Chọn danh mục') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $page->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('Trạng thái') }}</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="draft" {{ old('status', $page->status) == 'draft' ? 'selected' : '' }}>{{ __('Bản nháp') }}</option>
                                <option value="published" {{ old('status', $page->status) == 'published' ? 'selected' : '' }}>{{ __('Xuất bản') }}</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="order" class="form-label">{{ __('Thứ tự') }}</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $page->order) }}" min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" {{ old('is_featured', $page->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">
                                    {{ __('Đánh dấu là bài viết nổi bật') }}
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="featured_image" class="form-label">{{ __('Ảnh đại diện') }}</label>
                            @if($page->attachments->count() > 0)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($page->attachments->first()->file_path) }}" alt="{{ $page->title }}" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('featured_image') is-invalid @enderror" id="featured_image" name="featured_image" accept="image/*">
                            <small class="form-text text-muted">{{ __('Để trống nếu không muốn thay đổi ảnh.') }}</small>
                            @error('featured_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">SEO</label>
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <label for="meta_title" class="form-label small">{{ __('Meta Title') }}</label>
                                        <input type="text" class="form-control form-control-sm @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}">
                                        @error('meta_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label for="meta_description" class="form-label small">{{ __('Meta Description') }}</label>
                                        <textarea class="form-control form-control-sm @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description" rows="2">{{ old('meta_description', $page->meta_description) }}</textarea>
                                        @error('meta_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="meta_keywords" class="form-label small">{{ __('Meta Keywords') }}</label>
                                        <input type="text" class="form-control form-control-sm @error('meta_keywords') is-invalid @enderror" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $page->meta_keywords) }}">
                                        @error('meta_keywords')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">{{ __('Hủy') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Cập nhật') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

{{-- TinyMCE is now handled by the component --}}
