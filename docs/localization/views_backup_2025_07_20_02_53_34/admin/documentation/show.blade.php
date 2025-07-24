@extends('admin.layouts.dason')

@section('title', 'Chi tiết tài liệu')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">📄 Chi tiết tài liệu</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.documentation.index') }}">Tài liệu</a></li>
                        <li class="breadcrumb-item active">Chi tiết</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            @if($documentation->is_featured)
                                <span class="badge badge-soft-warning me-2">Nổi bật</span>
                            @endif
                            @if($documentation->is_public)
                                <span class="badge badge-soft-success me-2">Công khai</span>
                            @endif
                            {{ $documentation->title }}
                        </h5>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i> Thao tác
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.documentation.edit', $documentation) }}">
                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $documentation->id }})">
                                    <i class="fas fa-trash"></i> Xóa
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Document Meta Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-muted mb-2"><strong>Danh mục:</strong>
                                <span class="badge badge-soft-primary">{{ $documentation->category->name ?? 'Chưa phân loại' }}</span>
                            </p>
                            <p class="text-muted mb-2"><strong>Loại:</strong>
                                <span class="badge badge-soft-info">{{ ucfirst($documentation->content_type) }}</span>
                            </p>
                            <p class="text-muted mb-2"><strong>Độ khó:</strong>
                                <span class="badge badge-soft-{{ $documentation->difficulty_level == 'beginner' ? 'success' : ($documentation->difficulty_level == 'expert' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($documentation->difficulty_level) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-2"><strong>Tác giả:</strong> {{ $documentation->author->name ?? 'Không xác định' }}</p>
                            <p class="text-muted mb-2"><strong>Ngày tạo:</strong> {{ $documentation->created_at->format('d/m/Y H:i') }}</p>
                            <p class="text-muted mb-2"><strong>Cập nhật:</strong> {{ $documentation->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <!-- Document Excerpt -->
                    @if($documentation->excerpt)
                    <div class="alert alert-light border-start border-4 border-primary">
                        <h6 class="alert-heading">Tóm tắt</h6>
                        <p class="mb-0">{{ $documentation->excerpt }}</p>
                    </div>
                    @endif

                    <!-- Document Content -->
                    <div class="document-content">
                        <h6 class="mb-3">Nội dung tài liệu</h6>
                        <div class="border rounded p-3" style="background-color: #f8f9fa;">
                            {!! \Illuminate\Support\Str::markdown($documentation->content) !!}
                        </div>
                    </div>

                    <!-- Tags -->
                    @if($documentation->tags && count($documentation->tags) > 0)
                    <div class="mt-4">
                        <h6 class="mb-2">Tags</h6>
                        @foreach($documentation->tags as $tag)
                            <span class="badge badge-soft-secondary me-1">{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Trạng thái</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <span class="badge badge-soft-{{ $documentation->status == 'published' ? 'success' : ($documentation->status == 'draft' ? 'warning' : 'info') }} fs-6 px-3 py-2">
                            {{ ucfirst($documentation->status) }}
                        </span>
                    </div>

                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Lượt xem:</span>
                            <span class="fw-bold">{{ number_format($documentation->view_count) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Đánh giá:</span>
                            <span class="fw-bold">{{ $documentation->rating_average }}/5 ({{ $documentation->rating_count }})</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tải xuống:</span>
                            <span class="fw-bold">{{ number_format($documentation->download_count) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Thời gian đọc:</span>
                            <span class="fw-bold">{{ $documentation->estimated_read_time }} phút</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Info -->
            @if($documentation->meta_title || $documentation->meta_description)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin SEO</h5>
                </div>
                <div class="card-body">
                    @if($documentation->meta_title)
                    <div class="mb-3">
                        <label class="form-label text-muted">Meta Title</label>
                        <p class="mb-0">{{ $documentation->meta_title }}</p>
                    </div>
                    @endif

                    @if($documentation->meta_description)
                    <div class="mb-3">
                        <label class="form-label text-muted">Meta Description</label>
                        <p class="mb-0">{{ $documentation->meta_description }}</p>
                    </div>
                    @endif

                    @if($documentation->meta_keywords)
                    <div>
                        <label class="form-label text-muted">Meta Keywords</label>
                        <p class="mb-0">{{ $documentation->meta_keywords }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Target Audience -->
            @if($documentation->allowed_roles && count($documentation->allowed_roles) > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Đối tượng mục tiêu</h5>
                </div>
                <div class="card-body">
                    @foreach($documentation->allowed_roles as $role)
                        <span class="badge badge-soft-primary me-1 mb-1">{{ ucfirst($role) }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa tài liệu này không? Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/documentation/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection

@section('styles')
<style>
.document-content {
    line-height: 1.6;
}

.document-content h1, .document-content h2, .document-content h3 {
    color: #495057;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.document-content pre {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 1rem;
    overflow-x: auto;
}

.document-content blockquote {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
    margin: 1rem 0;
    color: #6c757d;
}
</style>
@endsection
