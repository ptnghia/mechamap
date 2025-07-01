@extends('admin.layouts.dason')

@section('title', 'Quản Lý Tài Liệu')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Quản Lý Tài Liệu</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.knowledge.index') }}">Cơ Sở Tri Thức</a></li>
                        <li class="breadcrumb-item active">Tài Liệu</li>
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
                            <h4 class="card-title">Danh Sách Tài Liệu</h4>
                            <p class="card-title-desc">Quản lý tất cả tài liệu kỹ thuật</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.knowledge.documents.create') }}" class="btn btn-warning">
                                <i class="fas fa-plus me-1"></i> Thêm Tài Liệu
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
                            <select class="form-select" id="fileTypeFilter">
                                <option value="">Tất cả loại file</option>
                                <option value="pdf">PDF</option>
                                <option value="doc">Word</option>
                                <option value="xls">Excel</option>
                                <option value="ppt">PowerPoint</option>
                                <option value="zip">Archive</option>
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

                    <!-- Documents Table -->
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th scope="col">Tài Liệu</th>
                                    <th scope="col">Danh Mục</th>
                                    <th scope="col">Tác Giả</th>
                                    <th scope="col">Loại File</th>
                                    <th scope="col">Kích Thước</th>
                                    <th scope="col">Trạng Thái</th>
                                    <th scope="col">Lượt Tải</th>
                                    <th scope="col">Ngày Tạo</th>
                                    <th scope="col">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $document)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $document->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded me-3 d-flex align-items-center justify-content-center">
                                                <i class="{{ $document->file_icon }}"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $document->title }}</h6>
                                                <p class="text-muted font-size-13 mb-0">{{ $document->original_filename }}</p>
                                                @if($document->description)
                                                    <p class="text-muted font-size-12 mb-0">{{ Str::limit($document->description, 50) }}</p>
                                                @endif
                                                @if($document->is_featured)
                                                    <span class="badge bg-warning">Nổi bật</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($document->category)
                                            <span class="badge bg-warning">{{ $document->category->name }}</span>
                                        @else
                                            <span class="text-muted">Chưa phân loại</span>
                                        @endif
                                    </td>
                                    <td>{{ $document->author->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ strtoupper($document->file_type) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $document->formatted_file_size }}</span>
                                    </td>
                                    <td>
                                        @switch($document->status)
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
                                                <span class="badge bg-light text-dark">{{ $document->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ number_format($document->download_count ?? 0) }}</span>
                                    </td>
                                    <td>{{ $document->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ $document->download_url }}" target="_blank" class="btn btn-outline-secondary btn-sm" title="Tải xuống">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ route('admin.knowledge.documents.edit', $document) }}" class="btn btn-outline-primary btn-sm" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Xóa" onclick="deleteDocument({{ $document->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-folder font-size-48 text-muted mb-3"></i>
                                            <h5 class="text-muted">Chưa có tài liệu nào</h5>
                                            <p class="text-muted">Hãy thêm tài liệu kỹ thuật đầu tiên</p>
                                            <a href="{{ route('admin.knowledge.documents.create') }}" class="btn btn-warning">
                                                <i class="fas fa-plus me-1"></i> Thêm Tài Liệu
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($documents->hasPages())
                        <div class="row mt-4">
                            <div class="col-sm-6">
                                <div class="dataTables_info">
                                    Hiển thị {{ $documents->firstItem() }} đến {{ $documents->lastItem() }} 
                                    trong tổng số {{ $documents->total() }} tài liệu
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers float-end">
                                    {{ $documents->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
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
                <p>Bạn có chắc chắn muốn xóa tài liệu này không? File sẽ bị xóa vĩnh viễn và không thể khôi phục.</p>
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
function deleteDocument(id) {
    $('#deleteModal').modal('show');
    $('#confirmDelete').off('click').on('click', function() {
        // Add delete logic here
        console.log('Delete document:', id);
        $('#deleteModal').modal('hide');
    });
}

// Filter functionality
$(document).ready(function() {
    $('#statusFilter, #categoryFilter, #fileTypeFilter').on('change', function() {
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
});
</script>
@endsection
