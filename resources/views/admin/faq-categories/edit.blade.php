@extends('admin.layouts.dason')

@section('title', 'Chỉnh sửa danh mục hỏi đáp')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Chỉnh sửa danh mục hỏi đáp</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa danh mục hỏi đáp</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Page specific CSS -->
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Chỉnh sửa danh mục hỏi đáp') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.faq-categories.update', $faqCategory) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">{{ 'Tên danh mục' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $faqCategory->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">{{ 'Mô tả' }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $faqCategory->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="order" class="form-label">{{ 'Thứ tự' }}</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $faqCategory->order) }}" min="0">
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $faqCategory->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            {{ 'Kích hoạt' }}
                        </label>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.faq-categories.index') }}" class="btn btn-secondary">{{ 'Hủy' }}</a>
                    <button type="submit" class="btn btn-primary">{{ 'Cập nhật' }}</button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection