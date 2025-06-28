@extends('admin.layouts.dason')

@section('title', 'Chỉnh sửa câu hỏi')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Chỉnh sửa câu hỏi</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa câu hỏi</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Chỉnh sửa câu hỏi') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.faqs.update', $faq) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="question" class="form-label">{{ __('Câu hỏi') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('question') is-invalid @enderror" id="question" name="question" value="{{ old('question', $faq->question) }}" required>
                    @error('question')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="answer" class="form-label">{{ __('Câu trả lời') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('answer') is-invalid @enderror" id="answer" name="answer" rows="5" required>{{ old('answer', $faq->answer) }}</textarea>
                    @error('answer')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="category_id" class="form-label">{{ __('Danh mục') }} <span class="text-danger">*</span></label>
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                        <option value="">{{ __('Chọn danh mục') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $faq->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="order" class="form-label">{{ __('Thứ tự') }}</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $faq->order) }}" min="0">
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $faq->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            {{ __('Kích hoạt') }}
                        </label>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">{{ __('Hủy') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Cập nhật') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#answer',
        height: 300,
        menubar: false,
        plugins: 'advlist autolink lists link image charmap print preview anchor',
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat'
    });
</script>
@endpush
