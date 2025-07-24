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
                    <x-tinymce-editor
                        name="answer"
                        id="answer"
                        :value="old('answer', $faq->answer)"
                        placeholder="Nhập câu trả lời chi tiết..."
                        context="admin"
                        :height="300"
                        :required="true"
                        class="@error('answer') is-invalid @enderror"
                    />
                    @error('answer')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">{{ __('Sử dụng editor để định dạng văn bản, thêm liên kết và hình ảnh') }}</div>
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
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-info" onclick="previewFAQ()">
                            <i class="fas fa-eye me-1"></i> {{ __('Xem trước') }}
                        </button>
                        <button type="submit" class="btn btn-primary">{{ __('Cập nhật') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">{{ __('Xem trước câu hỏi') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0" id="preview-question">{{ __('Câu hỏi sẽ hiển thị ở đây') }}</h6>
                        </div>
                        <div class="card-body">
                            <div id="preview-answer">{{ __('Câu trả lời sẽ hiển thị ở đây') }}</div>
                        </div>
                        <div class="card-footer text-muted">
                            <small>
                                <strong>{{ __('Danh mục:') }}</strong> <span id="preview-category">{{ __('Chưa chọn') }}</span>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Đóng') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Preview function - TinyMCE is now handled by the component
    function previewFAQ() {
        const question = document.getElementById('question').value;
        const categorySelect = document.getElementById('category_id');
        const categoryText = categorySelect.options[categorySelect.selectedIndex].text;

        // Get content from TinyMCE
        const editor = tinymce.get('answer');
        const answer = editor ? editor.getContent() : document.getElementById('answer').value;

        // Update preview modal
        document.getElementById('preview-question').textContent = question || '{{ __("Chưa nhập câu hỏi") }}';
        document.getElementById('preview-answer').innerHTML = answer || '{{ __("Chưa nhập câu trả lời") }}';
        document.getElementById('preview-category').textContent = categorySelect.value ? categoryText : '{{ __("Chưa chọn danh mục") }}';

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    }
</script>
@endpush
