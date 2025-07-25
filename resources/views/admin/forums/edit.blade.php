@extends('admin.layouts.dason')

@section('title', 'Chỉnh sửa diễn đàn')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Chỉnh sửa diễn đàn</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa diễn đàn</li>
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
            <h5 class="card-title mb-0">{{ 'Chỉnh sửa diễn đàn' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.forums.update', $forum) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">{{ 'Tên diễn đàn' }}</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $forum->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">{{ 'Mô tả' }}</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $forum->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="parent_id" class="form-label">{{ 'Diễn đàn cha' }}</label>
                    <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                        <option value="">{{ 'Không có' }}</option>
                        @foreach($forums as $parentForum)
                            <option value="{{ $parentForum->id }}" {{ old('parent_id', $forum->parent_id) == $parentForum->id ? 'selected' : '' }}>{{ $parentForum->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="order" class="form-label">{{ 'Thứ tự' }}</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $forum->order) }}" min="0">
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_private" name="is_private" {{ old('is_private', $forum->is_private) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_private">
                            {{ 'Diễn đàn riêng tư' }}
                        </label>
                        <div class="form-text">{{ 'Nếu được chọn, chỉ những người dùng được cấp quyền mới có thể truy cập diễn đàn này.' }}</div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.forums.index') }}" class="btn btn-secondary">{{ 'Hủy' }}</a>
                    <button type="submit" class="btn btn-primary">{{ 'Cập nhật' }}</button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection