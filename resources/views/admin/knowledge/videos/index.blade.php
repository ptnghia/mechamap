@extends('admin.layouts.dason')

@section('title', 'Quản Lý Video')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Quản Lý Video</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.knowledge.index') }}">Cơ Sở Tri Thức</a></li>
                        <li class="breadcrumb-item active">Video</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Danh Sách Video</h4>
                            <p class="card-title-desc">Quản lý tất cả video hướng dẫn</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.knowledge.videos.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Thêm Video
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="published">Đã xuất bản</option>
                                <option value="draft">Bản nháp</option>
                                <option value="archived">Lưu trữ</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="categoryFilter">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="videoTypeFilter">
                                <option value="">Tất cả loại video</option>
                                <option value="youtube">YouTube</option>
                                <option value="vimeo">Vimeo</option>
                                <option value="local">Video nội bộ</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Tìm kiếm..." id="searchInput">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Videos Table -->
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th scope="col">Video</th>
                                    <th scope="col">Danh Mục</th>
                                    <th scope="col">Tác Giả</th>
                                    <th scope="col">Loại</th>
                                    <th scope="col">Thời Lượng</th>
                                    <th scope="col">Trạng Thái</th>
                                    <th scope="col">Lượt Xem</th>
                                    <th scope="col">Ngày Tạo</th>
                                    <th scope="col">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($videos as $video)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $video->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($video->thumbnail)
                                                <img src="{{ Storage::url($video->thumbnail) }}" alt="" class="avatar-sm rounded me-3">
                                            @else
                                                <div class="avatar-sm bg-light rounded me-3 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-video text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $video->title }}</h6>
                                                @if($video->description)
                                                    <p class="text-muted font-size-13 mb-0">{{ Str::limit($video->description, 60) }}</p>
                                                @endif
                                                @if($video->is_featured)
                                                    <span class="badge bg-warning">Nổi bật</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($video->category)
                                            <span class="badge bg-success">{{ $video->category->name }}</span>
                                        @else
                                            <span class="text-muted">Chưa phân loại</span>
                                        @endif
                                    </td>
                                    <td>{{ $video->author->name ?? 'N/A' }}</td>
                                    <td>
                                        @switch($video->video_type)
                                            @case('youtube')
                                                <span class="badge bg-danger">YouTube</span>
                                                @break
                                            @case('vimeo')
                                                <span class="badge bg-info">Vimeo</span>
                                                @break
                                            @case('local')
                                                <span class="badge bg-secondary">Nội bộ</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ $video->video_type }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($video->duration)
                                            <span class="text-muted">{{ $video->formatted_duration }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($video->status)
                                            @case('published')
                                                <span class="badge bg-success">Đã xuất bản</span>
                                                @break
                                            @case('draft')
                                                <span class="badge bg-warning">Bản nháp</span>
                                                @break
                                            @case('archived')
                                                <span class="badge bg-secondary">Lưu trữ</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ $video->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ number_format($video->views_count ?? 0) }}</span>
                                    </td>
                                    <td>{{ $video->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" title="Xem" onclick="previewVideo('{{ $video->embed_url }}')">
                                                <i class="fas fa-play"></i>
                                            </button>
                                            <a href="{{ route('admin.knowledge.videos.edit', $video) }}" class="btn btn-outline-primary btn-sm" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Xóa" onclick="deleteVideo({{ $video->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-video font-size-48 text-muted mb-3"></i>
                                            <h5 class="text-muted">Chưa có video nào</h5>
                                            <p class="text-muted">Hãy thêm video hướng dẫn đầu tiên</p>
                                            <a href="{{ route('admin.knowledge.videos.create') }}" class="btn btn-success">
                                                <i class="fas fa-plus me-1"></i> Thêm Video
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($videos->hasPages())
                        <div class="row mt-4">
                            <div class="col-sm-6">
                                <div class="dataTables_info">
                                    Hiển thị {{ $videos->firstItem() }} đến {{ $videos->lastItem() }} 
                                    trong tổng số {{ $videos->total() }} video
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers float-end">
                                    {{ $videos->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Video Preview Modal -->
<div class="modal fade" id="videoPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xem Trước Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-16x9">
                    <iframe id="videoFrame" src="" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác Nhận Xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa video này không? Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Xóa</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewVideo(embedUrl) {
    $('#videoFrame').attr('src', embedUrl);
    $('#videoPreviewModal').modal('show');
}

function deleteVideo(id) {
    $('#deleteModal').modal('show');
    $('#confirmDelete').off('click').on('click', function() {
        // Add delete logic here
        console.log('Delete video:', id);
        $('#deleteModal').modal('hide');
    });
}

// Filter functionality
$(document).ready(function() {
    $('#statusFilter, #categoryFilter, #videoTypeFilter').on('change', function() {
        // Add filter logic here
        console.log('Filter changed');
    });
    
    $('#searchInput').on('keyup', function() {
        // Add search logic here
        console.log('Search:', $(this).val());
    });
    
    $('#checkAll').on('change', function() {
        $('tbody input[type="checkbox"]').prop('checked', $(this).prop('checked'));
    });
    
    // Clear video when modal is hidden
    $('#videoPreviewModal').on('hidden.bs.modal', function() {
        $('#videoFrame').attr('src', '');
    });
});
</script>
@endsection
