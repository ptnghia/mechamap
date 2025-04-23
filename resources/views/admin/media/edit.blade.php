@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Media')

@section('header', 'Chỉnh sửa Media')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Chỉnh sửa thông tin') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.media.update', $media) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Tiêu đề') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $media->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Mô tả') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $media->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.media.show', $media) }}" class="btn btn-secondary">{{ __('Hủy') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Cập nhật') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Xem trước') }}</h5>
                </div>
                <div class="card-body text-center">
                    @if(strpos($media->file_type, 'image') !== false)
                        <img src="{{ Storage::url($media->file_path) }}" alt="{{ $media->title }}" class="img-fluid" style="max-height: 300px;">
                    @elseif(strpos($media->file_type, 'video') !== false)
                        <video controls class="w-100" style="max-height: 300px;">
                            <source src="{{ Storage::url($media->file_path) }}" type="{{ $media->file_type }}">
                            {{ __('Trình duyệt của bạn không hỗ trợ video.') }}
                        </video>
                    @elseif(strpos($media->file_type, 'audio') !== false)
                        <audio controls class="w-100">
                            <source src="{{ Storage::url($media->file_path) }}" type="{{ $media->file_type }}">
                            {{ __('Trình duyệt của bạn không hỗ trợ audio.') }}
                        </audio>
                    @else
                        <div class="p-4 text-center">
                            @if(strpos($media->file_type, 'pdf') !== false)
                                <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 5rem;"></i>
                            @elseif(strpos($media->file_type, 'word') !== false || strpos($media->file_type, 'document') !== false)
                                <i class="bi bi-file-earmark-word text-primary" style="font-size: 5rem;"></i>
                            @elseif(strpos($media->file_type, 'excel') !== false || strpos($media->file_type, 'spreadsheet') !== false)
                                <i class="bi bi-file-earmark-excel text-success" style="font-size: 5rem;"></i>
                            @elseif(strpos($media->file_type, 'powerpoint') !== false || strpos($media->file_type, 'presentation') !== false)
                                <i class="bi bi-file-earmark-ppt text-warning" style="font-size: 5rem;"></i>
                            @elseif(strpos($media->file_type, 'zip') !== false || strpos($media->file_type, 'rar') !== false || strpos($media->file_type, 'archive') !== false)
                                <i class="bi bi-file-earmark-zip text-secondary" style="font-size: 5rem;"></i>
                            @else
                                <i class="bi bi-file-earmark text-secondary" style="font-size: 5rem;"></i>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thông tin file') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Tên file') }}:</span>
                            <span class="text-muted text-truncate" style="max-width: 200px;">{{ $media->file_name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Loại file') }}:</span>
                            <span class="text-muted">{{ $media->file_type }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Kích thước') }}:</span>
                            <span class="text-muted">{{ formatFileSize($media->file_size) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Ngày tải lên') }}:</span>
                            <span class="text-muted">{{ $media->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
</script>
@endpush
