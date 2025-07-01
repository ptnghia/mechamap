@extends('admin.layouts.dason')

@section('title', 'Quản Lý Bài Viết')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Quản Lý Bài Viết</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.knowledge.index') }}">Cơ Sở Tri Thức</a></li>
                        <li class="breadcrumb-item active">Bài Viết</li>
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
                            <h4 class="card-title">Danh Sách Bài Viết</h4>
                            <p class="card-title-desc">Quản lý tất cả bài viết kỹ thuật</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.knowledge.articles.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Thêm Bài Viết
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters and Bulk Actions -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="published">Đã xuất bản</option>
                                <option value="draft">Bản nháp</option>
                                <option value="archived">Lưu trữ</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="categoryFilter">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="difficultyFilter">
                                <option value="">Tất cả độ khó</option>
                                <option value="beginner">Cơ bản</option>
                                <option value="intermediate">Trung bình</option>
                                <option value="advanced">Nâng cao</option>
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
                        <div class="col-md-3">
                            <div class="btn-group" role="group" id="bulkActions">
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="bulkDelete()">
                                    <i class="fas fa-trash me-1"></i> Xóa Đã Chọn
                                </button>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-edit me-1"></i> Cập Nhật Trạng Thái
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('published')">
                                            <i class="fas fa-check text-success me-2"></i>Xuất bản
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('draft')">
                                            <i class="fas fa-edit text-warning me-2"></i>Chuyển về nháp
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('archived')">
                                            <i class="fas fa-archive text-secondary me-2"></i>Lưu trữ
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Articles Table -->
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th scope="col">Tiêu Đề</th>
                                    <th scope="col">Danh Mục</th>
                                    <th scope="col">Tác Giả</th>
                                    <th scope="col">Trạng Thái</th>
                                    <th scope="col">Độ Khó</th>
                                    <th scope="col">Lượt Xem</th>
                                    <th scope="col">Ngày Tạo</th>
                                    <th scope="col">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($articles as $article)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $article->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($article->featured_image)
                                                <img src="{{ Storage::url($article->featured_image) }}" alt="" class="avatar-sm rounded me-3">
                                            @else
                                                <div class="avatar-sm bg-light rounded me-3 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-file-alt text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $article->title }}</h6>
                                                @if($article->excerpt)
                                                    <p class="text-muted font-size-13 mb-0">{{ Str::limit($article->excerpt, 60) }}</p>
                                                @endif
                                                @if($article->is_featured)
                                                    <span class="badge bg-warning">Nổi bật</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($article->category)
                                            <span class="badge bg-primary">{{ $article->category->name }}</span>
                                        @else
                                            <span class="text-muted">Chưa phân loại</span>
                                        @endif
                                    </td>
                                    <td>{{ $article->author->name ?? 'N/A' }}</td>
                                    <td>
                                        @switch($article->status)
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
                                                <span class="badge bg-light text-dark">{{ $article->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($article->difficulty_level)
                                            @case('beginner')
                                                <span class="badge bg-success">Cơ bản</span>
                                                @break
                                            @case('intermediate')
                                                <span class="badge bg-warning">Trung bình</span>
                                                @break
                                            @case('advanced')
                                                <span class="badge bg-danger">Nâng cao</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ number_format($article->views_count ?? 0) }}</span>
                                    </td>
                                    <td>{{ $article->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('admin.knowledge.articles.edit', $article) }}" class="btn btn-outline-primary btn-sm" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Xóa" onclick="deleteArticle({{ $article->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-file-alt font-size-48 text-muted mb-3"></i>
                                            <h5 class="text-muted">Chưa có bài viết nào</h5>
                                            <p class="text-muted">Hãy tạo bài viết đầu tiên của bạn</p>
                                            <a href="{{ route('admin.knowledge.articles.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i> Thêm Bài Viết
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($articles->hasPages())
                        <div class="row mt-4">
                            <div class="col-sm-6">
                                <div class="dataTables_info">
                                    Hiển thị {{ $articles->firstItem() }} đến {{ $articles->lastItem() }}
                                    trong tổng số {{ $articles->total() }} bài viết
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers float-end">
                                    {{ $articles->links() }}
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
                <p>Bạn có chắc chắn muốn xóa bài viết này không? Hành động này không thể hoàn tác.</p>
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
document.addEventListener('DOMContentLoaded', function() {
    // Check all functionality
    const checkAllBox = document.getElementById('checkAll');
    if (checkAllBox) {
        checkAllBox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Individual checkbox change
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Update check all status
            const totalCheckboxes = document.querySelectorAll('tbody input[type="checkbox"]').length;
            const checkedCheckboxes = document.querySelectorAll('tbody input[type="checkbox"]:checked').length;
            if (checkAllBox) {
                checkAllBox.checked = totalCheckboxes === checkedCheckboxes;
            }
        });
    });
});

function deleteArticle(id) {
    $('#deleteModal').modal('show');
    $('#confirmDelete').off('click').on('click', function() {
        // Add delete logic here
        console.log('Delete article:', id);
        $('#deleteModal').modal('hide');
    });
}

// Bulk operations are now always visible for better UX

function getSelectedIds() {
    const ids = [];
    $('tbody input[type="checkbox"]:checked').each(function() {
        ids.push($(this).val());
    });
    return ids;
}

function bulkDelete() {
    const ids = getSelectedIds();
    if (ids.length === 0) {
        alert('Vui lòng chọn ít nhất một bài viết để xóa.');
        return;
    }

    if (confirm(`Bạn có chắc chắn muốn xóa ${ids.length} bài viết đã chọn?`)) {
        $.ajax({
            url: '{{ route("admin.knowledge.articles.bulk-delete") }}',
            method: 'POST',
            data: {
                ids: ids,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                }
            },
            error: function(xhr) {
                alert('Có lỗi xảy ra khi xóa bài viết.');
            }
        });
    }
}

function bulkUpdateStatus(status) {
    const ids = getSelectedIds();
    if (ids.length === 0) {
        alert('Vui lòng chọn ít nhất một bài viết để cập nhật.');
        return;
    }

    const statusText = {
        'published': 'xuất bản',
        'draft': 'chuyển về nháp',
        'archived': 'lưu trữ'
    };

    if (confirm(`Bạn có chắc chắn muốn ${statusText[status]} ${ids.length} bài viết đã chọn?`)) {
        $.ajax({
            url: '{{ route("admin.knowledge.bulk-update-status") }}',
            method: 'POST',
            data: {
                type: 'articles',
                ids: ids,
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                }
            },
            error: function(xhr) {
                alert('Có lỗi xảy ra khi cập nhật trạng thái.');
            }
        });
    }
}

function performSearch() {
    const query = $('#searchInput').val();
    const status = $('#statusFilter').val();
    const category = $('#categoryFilter').val();
    const difficulty = $('#difficultyFilter').val();

    // Build URL with parameters
    const params = new URLSearchParams();
    if (query) params.append('search', query);
    if (status) params.append('status', status);
    if (category) params.append('category', category);
    if (difficulty) params.append('difficulty', difficulty);

    // Reload page with filters
    window.location.href = '{{ route("admin.knowledge.articles") }}?' + params.toString();
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endsection
