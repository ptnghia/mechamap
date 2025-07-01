@extends('admin.layouts.dason')

@section('title', 'Quản lý danh mục tài liệu')

@push('styles')
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<style>
.category-tree {
    padding-left: 20px;
}
.category-item {
    border-left: 2px solid #e9ecef;
    margin-left: 10px;
    padding-left: 15px;
}
.category-badge {
    font-size: 0.75rem;
}
</style>
@endpush

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Quản lý danh mục tài liệu</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.documentation.index') }}">Tài liệu</a></li>
                            <li class="breadcrumb-item active">Danh mục</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2">Tổng danh mục</p>
                                <h4 class="mb-0">{{ $categories->total() }}</h4>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-folder-open font-size-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2">Danh mục hoạt động</p>
                                <h4 class="mb-0">{{ $categories->where('is_active', true)->count() }}</h4>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-check-circle font-size-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2">Danh mục gốc</p>
                                <h4 class="mb-0">{{ $parentCategories->count() }}</h4>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-sitemap font-size-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2">Tổng tài liệu</p>
                                <h4 class="mb-0">{{ $categories->sum('documentations_count') }}</h4>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-file-alt font-size-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title mb-0">Danh sách danh mục</h4>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.documentation.categories.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tạo danh mục mới
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <form method="GET" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Tìm kiếm danh mục..." 
                                           value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" name="parent_id">
                                        <option value="">Tất cả danh mục</option>
                                        <option value="root" {{ request('parent_id') === 'root' ? 'selected' : '' }}>
                                            Danh mục gốc
                                        </option>
                                        @foreach($parentCategories as $parent)
                                            <option value="{{ $parent->id }}" 
                                                    {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" name="is_active">
                                        <option value="">Tất cả trạng thái</option>
                                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>
                                            Hoạt động
                                        </option>
                                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>
                                            Không hoạt động
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-secondary w-100">
                                        <i class="fas fa-search"></i> Lọc
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Bulk Actions -->
                        <form id="bulk-form" method="POST" action="{{ route('admin.documentation.categories.bulk-action') }}">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select class="form-select" name="action" required>
                                            <option value="">Chọn hành động</option>
                                            <option value="activate">Kích hoạt</option>
                                            <option value="deactivate">Vô hiệu hóa</option>
                                            <option value="make_public">Công khai</option>
                                            <option value="make_private">Riêng tư</option>
                                            <option value="delete">Xóa</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="fas fa-play"></i> Thực hiện
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <span class="text-muted">
                                        <span id="selected-count">0</span> danh mục được chọn
                                    </span>
                                </div>
                            </div>

                            <!-- Categories Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="select-all">
                                                </div>
                                            </th>
                                            <th>Danh mục</th>
                                            <th>Danh mục cha</th>
                                            <th>Tài liệu</th>
                                            <th>Trạng thái</th>
                                            <th>Thứ tự</th>
                                            <th>Ngày tạo</th>
                                            <th width="120">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($categories as $category)
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input category-checkbox" 
                                                               type="checkbox" name="selected_ids[]" 
                                                               value="{{ $category->id }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($category->icon)
                                                            <i class="{{ $category->icon }} me-2" 
                                                               style="color: {{ $category->color_code }}"></i>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0">
                                                                <a href="{{ route('admin.documentation.categories.show', $category) }}" 
                                                                   class="text-dark">
                                                                    {{ $category->name }}
                                                                </a>
                                                            </h6>
                                                            @if($category->description)
                                                                <small class="text-muted">
                                                                    {{ Str::limit($category->description, 50) }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($category->parent)
                                                        <span class="badge bg-light text-dark">
                                                            {{ $category->parent->name }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Danh mục gốc</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $category->documentations_count }} tài liệu
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column gap-1">
                                                        @if($category->is_active)
                                                            <span class="badge bg-success category-badge">Hoạt động</span>
                                                        @else
                                                            <span class="badge bg-danger category-badge">Không hoạt động</span>
                                                        @endif
                                                        @if($category->is_public)
                                                            <span class="badge bg-primary category-badge">Công khai</span>
                                                        @else
                                                            <span class="badge bg-warning category-badge">Riêng tư</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $category->sort_order }}</span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $category->created_at->format('d/m/Y H:i') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.documentation.categories.show', $category) }}" 
                                                           class="btn btn-sm btn-outline-info" title="Xem">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.documentation.categories.edit', $category) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="Sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteCategory({{ $category->id }})" title="Xóa">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-folder-open font-size-48 mb-3"></i>
                                                        <p>Không có danh mục nào được tìm thấy</p>
                                                        <a href="{{ route('admin.documentation.categories.create') }}" 
                                                           class="btn btn-primary">
                                                            <i class="fas fa-plus"></i> Tạo danh mục đầu tiên
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </form>

                        <!-- Pagination -->
                        @if($categories->hasPages())
                            <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <div class="dataTables_info">
                                        Hiển thị {{ $categories->firstItem() }} đến {{ $categories->lastItem() }} 
                                        trong tổng số {{ $categories->total() }} danh mục
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-7">
                                    {{ $categories->appends(request()->query())->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa danh mục này?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Hành động này không thể hoàn tác!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select all checkbox
    $('#select-all').change(function() {
        $('.category-checkbox').prop('checked', this.checked);
        updateSelectedCount();
    });

    // Individual checkbox
    $('.category-checkbox').change(function() {
        updateSelectedCount();
        
        // Update select all checkbox
        const total = $('.category-checkbox').length;
        const checked = $('.category-checkbox:checked').length;
        $('#select-all').prop('indeterminate', checked > 0 && checked < total);
        $('#select-all').prop('checked', checked === total);
    });

    // Update selected count
    function updateSelectedCount() {
        const count = $('.category-checkbox:checked').length;
        $('#selected-count').text(count);
    }

    // Bulk form submission
    $('#bulk-form').submit(function(e) {
        const selectedCount = $('.category-checkbox:checked').length;
        const action = $('select[name="action"]').val();
        
        if (selectedCount === 0) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất một danh mục!');
            return;
        }
        
        if (action === 'delete') {
            if (!confirm('Bạn có chắc chắn muốn xóa ' + selectedCount + ' danh mục đã chọn?')) {
                e.preventDefault();
                return;
            }
        }
    });
});

// Delete category function
function deleteCategory(id) {
    const form = document.getElementById('delete-form');
    form.action = '{{ route("admin.documentation.categories.destroy", ":id") }}'.replace(':id', id);
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
