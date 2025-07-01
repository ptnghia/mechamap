@extends('admin.layouts.dason')

@section('title', 'Quản Lý Danh Mục')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Quản Lý Danh Mục</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.knowledge.index') }}">Cơ Sở Tri Thức</a></li>
                        <li class="breadcrumb-item active">Danh Mục</li>
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
                            <h4 class="card-title">Danh Sách Danh Mục</h4>
                            <p class="card-title-desc">Quản lý phân loại nội dung tri thức</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.knowledge.categories.create') }}" class="btn btn-info">
                                <i class="fas fa-plus me-1"></i> Thêm Danh Mục
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Categories Table -->
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th scope="col">Danh Mục</th>
                                    <th scope="col">Danh Mục Cha</th>
                                    <th scope="col">Nội Dung</th>
                                    <th scope="col">Thứ Tự</th>
                                    <th scope="col">Trạng Thái</th>
                                    <th scope="col">Ngày Tạo</th>
                                    <th scope="col">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $category->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($category->icon)
                                                <div class="avatar-xs me-3">
                                                    <span class="avatar-title rounded-circle" style="background-color: {{ $category->color }};">
                                                        <i class="{{ $category->icon }} text-white"></i>
                                                    </span>
                                                </div>
                                            @else
                                                <div class="avatar-xs bg-light rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-folder text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $category->name }}</h6>
                                                @if($category->description)
                                                    <p class="text-muted font-size-13 mb-0">{{ Str::limit($category->description, 50) }}</p>
                                                @endif
                                                <small class="text-muted">{{ $category->slug }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($category->parent)
                                            <span class="badge bg-secondary">{{ $category->parent->name }}</span>
                                        @else
                                            <span class="text-muted">Danh mục gốc</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-primary">{{ $category->articles_count }} bài viết</span>
                                            <span class="badge bg-success">{{ $category->videos_count }} video</span>
                                            <span class="badge bg-warning">{{ $category->documents_count }} tài liệu</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $category->sort_order }}</span>
                                    </td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge bg-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-danger">Tạm dừng</span>
                                        @endif
                                    </td>
                                    <td>{{ $category->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" title="Xem chi tiết" onclick="viewCategory({{ $category->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('admin.knowledge.categories.edit', $category) }}" class="btn btn-outline-primary btn-sm" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Xóa" onclick="deleteCategory({{ $category->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-folder font-size-48 text-muted mb-3"></i>
                                            <h5 class="text-muted">Chưa có danh mục nào</h5>
                                            <p class="text-muted">Hãy tạo danh mục đầu tiên để phân loại nội dung</p>
                                            <a href="{{ route('admin.knowledge.categories.create') }}" class="btn btn-info">
                                                <i class="fas fa-plus me-1"></i> Thêm Danh Mục
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category Detail Modal -->
<div class="modal fade" id="categoryDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi Tiết Danh Mục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="categoryDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
                <p>Bạn có chắc chắn muốn xóa danh mục này không?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Lưu ý:</strong> Không thể xóa danh mục có danh mục con hoặc có nội dung.
                </div>
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
function viewCategory(id) {
    // Load category details via AJAX
    $('#categoryDetailContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>');
    $('#categoryDetailModal').modal('show');
    
    // Simulate loading category details
    setTimeout(() => {
        $('#categoryDetailContent').html(`
            <div class="row">
                <div class="col-md-6">
                    <h6>Thông Tin Cơ Bản</h6>
                    <p><strong>Tên:</strong> Danh mục ${id}</p>
                    <p><strong>Slug:</strong> danh-muc-${id}</p>
                    <p><strong>Mô tả:</strong> Mô tả chi tiết về danh mục</p>
                </div>
                <div class="col-md-6">
                    <h6>Thống Kê</h6>
                    <p><strong>Bài viết:</strong> 10</p>
                    <p><strong>Video:</strong> 5</p>
                    <p><strong>Tài liệu:</strong> 3</p>
                </div>
            </div>
        `);
    }, 1000);
}

function deleteCategory(id) {
    $('#deleteModal').modal('show');
    $('#confirmDelete').off('click').on('click', function() {
        // Add delete logic here
        console.log('Delete category:', id);
        $('#deleteModal').modal('hide');
    });
}

// Filter functionality
$(document).ready(function() {
    $('#checkAll').on('change', function() {
        $('tbody input[type="checkbox"]').prop('checked', $(this).prop('checked'));
    });
});
</script>
@endsection
