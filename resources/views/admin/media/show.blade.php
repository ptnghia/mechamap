@extends('admin.layouts.dason')

@section('title', 'Chi tiết Media')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Chi tiết Media</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Chi tiết Media</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.media.edit', $media) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-edit me-1"></i> {{ __('Chỉnh sửa') }}
    </a>
    <a href="{{ route('admin.media.download', $media) }}" class="btn btn-sm btn-outline-primary">
        <i class="fas fa-download me-1"></i> {{ __('Tải xuống') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Xem trước') }}</h5>
                </div>
                <div class="card-body text-center">
                    @if(strpos($media->file_type, 'image') !== false)
                        <img src="{{ Storage::url($media->file_path) }}" alt="{{ $media->title }}" class="img-fluid" style="max-height: 500px;">
                    @elseif(strpos($media->file_type, 'video') !== false)
                        <video controls class="w-100" style="max-height: 500px;">
                            <source src="{{ Storage::url($media->file_path) }}" type="{{ $media->file_type }}">
                            {{ __('Trình duyệt của bạn không hỗ trợ video.') }}
                        </video>
                    @elseif(strpos($media->file_type, 'audio') !== false)
                        <audio controls class="w-100">
                            <source src="{{ Storage::url($media->file_path) }}" type="{{ $media->mime_type }}">
                            {{ __('Trình duyệt của bạn không hỗ trợ audio.') }}
                        </audio>
                    @elseif(strpos($media->mime_type, 'pdf') !== false)
                        <div class="ratio ratio-16x9" style="max-height: 500px;">
                            <iframe src="{{ Storage::url($media->file_path) }}" allowfullscreen></iframe>
                        </div>
                    @else
                        <div class="p-5 text-center">
                            @if(strpos($media->file_type, 'word') !== false || strpos($media->file_type, 'document') !== false)
                                <i class="fas fa-file-word text-primary" style="font-size: 5rem;"></i>
                            @elseif(strpos($media->file_type, 'excel') !== false || strpos($media->file_type, 'spreadsheet') !== false)
                                <i class="fas fa-file-excel text-success" style="font-size: 5rem;"></i>
                            @elseif(strpos($media->file_type, 'powerpoint') !== false || strpos($media->file_type, 'presentation') !== false)
                                <i class="fas fa-file-ppt text-warning" style="font-size: 5rem;"></i>
                            @elseif(strpos($media->file_type, 'zip') !== false || strpos($media->file_type, 'rar') !== false || strpos($media->file_type, 'archive') !== false)
                                <i class="fas fa-file-zip text-secondary" style="font-size: 5rem;"></i>
                            @else
                                <i class="fas fa-file text-secondary" style="font-size: 5rem;"></i>
                            @endif
                            <p class="mt-3">{{ __('Không thể xem trước file này.') }}</p>
                            <a href="{{ route('admin.media.download', $media) }}" class="btn btn-primary">
                                <i class="fas fa-download me-1"></i> {{ __('Tải xuống') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            @if($media->description)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Mô tả') }}</h5>
                    </div>
                    <div class="card-body">
                        {{ $media->description }}
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thông tin file') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('ID') }}:</span>
                            <span class="text-muted">{{ $media->id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Tiêu đề') }}:</span>
                            <span class="text-muted">{{ $media->title }}</span>
                        </li>
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
                            <span>{{ __('Người tải lên') }}:</span>
                            <span class="text-muted">{{ $media->user->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Ngày tải lên') }}:</span>
                            <span class="text-muted">{{ $media->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Cập nhật lần cuối') }}:</span>
                            <span class="text-muted">{{ $media->updated_at->format('d/m/Y H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thao tác') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.media.edit', $media) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> {{ __('Chỉnh sửa') }}
                        </a>
                        <a href="{{ route('admin.media.download', $media) }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-1"></i> {{ __('Tải xuống') }}
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-1"></i> {{ __('Xóa') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Đường dẫn') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="url" class="form-label">{{ __('URL') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="url" value="{{ Storage::url($media->file_path) }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('url')">
                                <i class="fas fa-clipboard"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal xóa -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ __('Xác nhận xóa') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Bạn có chắc chắn muốn xóa file này?') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                    <form action="{{ route('admin.media.destroy', $media) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('Xóa') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        element.select();
        document.execCommand('copy');

        // Hiển thị thông báo
        const button = element.nextElementSibling;
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => {
            button.innerHTML = originalHtml;
        }, 2000);
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
</script>
@endpush
