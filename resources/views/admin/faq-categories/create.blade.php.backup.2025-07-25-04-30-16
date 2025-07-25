@extends('admin.layouts.dason')

@section('title', 'Tạo danh mục hỏi đáp mới')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Tạo danh mục hỏi đáp mới</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Tạo danh mục hỏi đáp mới</li>
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
            <h5 class="card-title mb-0">{{ __('Tạo danh mục hỏi đáp mới') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.faq-categories.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('Tên danh mục') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('Mô tả') }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="order" class="form-label">{{ __('Thứ tự') }}</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}" min="0">
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            {{ __('Kích hoạt') }}
                        </label>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.faq-categories.index') }}" class="btn btn-secondary">{{ __('Hủy') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Tạo danh mục') }}</button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection