@extends('admin.layouts.dason')

@section('title', 'Thêm vào Showcase')

@push('styles')
<!-- Page specific CSS -->
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Thêm vào Showcase</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="POST" action="{{ route('admin.showcases.store') }}">
                    @csrf
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="showcaseable_id">ID nội dung <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('showcaseable_id') is-invalid @enderror" id="showcaseable_id" name="showcaseable_id" value="{{ old('showcaseable_id') }}" placeholder="Nhập ID của chủ đề hoặc bài viết" required>
                            <small class="form-text text-muted">ID của chủ đề hoặc bài viết cần thêm vào showcase</small>
                            @error('showcaseable_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="showcaseable_type">Loại nội dung <span class="text-danger">*</span></label>
                            <select class="form-control @error('showcaseable_type') is-invalid @enderror" id="showcaseable_type" name="showcaseable_type" required>
                                <option value="thread" {{ old('showcaseable_type') == 'thread' ? 'selected' : '' }}>Chủ đề</option>
                                <option value="post" {{ old('showcaseable_type') == 'post' ? 'selected' : '' }}>Bài viết</option>
                            </select>
                            @error('showcaseable_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Mô tả ngắn về nội dung này (tùy chọn)">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="order">Thứ tự hiển thị</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}" min="0" placeholder="Thứ tự hiển thị (mặc định: 0)">
                            <small class="form-text text-muted">Số càng lớn thì hiển thị càng ưu tiên</small>
                            @error('order')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Thêm vào Showcase</button>
                        <a href="{{ route('admin.showcases.index') }}" class="btn btn-default ml-2">Hủy</a>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection