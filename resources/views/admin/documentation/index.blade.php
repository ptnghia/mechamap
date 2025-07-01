@extends('admin.layouts.dason')

@section('title', 'Quản lý Tài liệu')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">📚 Quản lý Tài liệu</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tài liệu</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng tài liệu</p>
                            <h4 class="mb-0">{{ \App\Models\Documentation::count() }}</h4>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-file-alt font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Đã xuất bản</p>
                            <h4 class="mb-0">{{ \App\Models\Documentation::where('status', 'published')->count() }}</h4>
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
                            <p class="text-truncate font-size-14 mb-2">Bản nháp</p>
                            <h4 class="mb-0">{{ \App\Models\Documentation::where('status', 'draft')->count() }}</h4>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-edit font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Lượt xem hôm nay</p>
                            <h4 class="mb-0">{{ \App\Models\DocumentationView::whereDate('created_at', today())->count() }}</h4>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-eye font-size-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">Danh sách Tài liệu</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.documentation.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Tạo tài liệu mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Tìm kiếm..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="category_id" class="form-select">
                                    <option value="">Tất cả danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                                    <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>Đang duyệt</option>
                                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="content_type" class="form-select">
                                    <option value="">Tất cả loại</option>
                                    <option value="guide" {{ request('content_type') == 'guide' ? 'selected' : '' }}>Hướng dẫn</option>
                                    <option value="api" {{ request('content_type') == 'api' ? 'selected' : '' }}>API</option>
                                    <option value="tutorial" {{ request('content_type') == 'tutorial' ? 'selected' : '' }}>Tutorial</option>
                                    <option value="reference" {{ request('content_type') == 'reference' ? 'selected' : '' }}>Tham khảo</option>
                                    <option value="faq" {{ request('content_type') == 'faq' ? 'selected' : '' }}>FAQ</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="difficulty_level" class="form-select">
                                    <option value="">Tất cả độ khó</option>
                                    <option value="beginner" {{ request('difficulty_level') == 'beginner' ? 'selected' : '' }}>Cơ bản</option>
                                    <option value="intermediate" {{ request('difficulty_level') == 'intermediate' ? 'selected' : '' }}>Trung bình</option>
                                    <option value="advanced" {{ request('difficulty_level') == 'advanced' ? 'selected' : '' }}>Nâng cao</option>
                                    <option value="expert" {{ request('difficulty_level') == 'expert' ? 'selected' : '' }}>Chuyên gia</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Bulk Actions -->
                    <form id="bulk-form" method="POST" action="{{ route('admin.documentation.bulk-action') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select name="action" class="form-select" required>
                                        <option value="">Chọn hành động...</option>
                                        <option value="publish">Xuất bản</option>
                                        <option value="unpublish">Hủy xuất bản</option>
                                        <option value="feature">Đánh dấu nổi bật</option>
                                        <option value="unfeature">Bỏ nổi bật</option>
                                        <option value="delete">Xóa</option>
                                    </select>
                                    <button type="submit" class="btn btn-outline-secondary" onclick="return confirm('Bạn có chắc chắn?')">
                                        Thực hiện
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="select-all" class="form-check-input">
                                        </th>
                                        <th>Tiêu đề</th>
                                        <th>Danh mục</th>
                                        <th>Tác giả</th>
                                        <th>Loại</th>
                                        <th>Trạng thái</th>
                                        <th>Lượt xem</th>
                                        <th>Ngày tạo</th>
                                        <th width="120">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($documentations as $doc)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_ids[]" value="{{ $doc->id }}" 
                                                       class="form-check-input row-checkbox">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($doc->is_featured)
                                                        <i class="fas fa-star text-warning me-2" title="Nổi bật"></i>
                                                    @endif
                                                    @if($doc->is_public)
                                                        <i class="fas fa-globe text-info me-2" title="Công khai"></i>
                                                    @endif
                                                    <div>
                                                        <a href="{{ route('admin.documentation.show', $doc) }}" 
                                                           class="text-dark fw-medium">{{ $doc->title }}</a>
                                                        @if($doc->excerpt)
                                                            <br><small class="text-muted">{{ Str::limit($doc->excerpt, 80) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $doc->category->color_code }}">
                                                    {{ $doc->category->name }}
                                                </span>
                                            </td>
                                            <td>{{ $doc->author->name }}</td>
                                            <td>
                                                @php
                                                    $typeLabels = [
                                                        'guide' => 'Hướng dẫn',
                                                        'api' => 'API',
                                                        'tutorial' => 'Tutorial',
                                                        'reference' => 'Tham khảo',
                                                        'faq' => 'FAQ'
                                                    ];
                                                @endphp
                                                <span class="badge bg-secondary">{{ $typeLabels[$doc->content_type] ?? $doc->content_type }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'draft' => 'warning',
                                                        'review' => 'info',
                                                        'published' => 'success',
                                                        'archived' => 'secondary'
                                                    ];
                                                    $statusLabels = [
                                                        'draft' => 'Bản nháp',
                                                        'review' => 'Đang duyệt',
                                                        'published' => 'Đã xuất bản',
                                                        'archived' => 'Lưu trữ'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$doc->status] }}">
                                                    {{ $statusLabels[$doc->status] }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($doc->view_count) }}</td>
                                            <td>{{ $doc->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.documentation.show', $doc) }}" 
                                                       class="btn btn-sm btn-outline-info" title="Xem">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.documentation.edit', $doc) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.documentation.destroy', $doc) }}" 
                                                          class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <i class="fas fa-file-alt font-size-48 text-muted mb-3"></i>
                                                <p class="text-muted">Chưa có tài liệu nào.</p>
                                                <a href="{{ route('admin.documentation.create') }}" class="btn btn-primary">
                                                    Tạo tài liệu đầu tiên
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <!-- Pagination -->
                    @if($documentations->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $documentations->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    selectAllCheckbox.addEventListener('change', function() {
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Update select all when individual checkboxes change
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === rowCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
        });
    });
});
</script>
@endpush
