@extends('admin.layouts.dason')

@section('title', 'Quản Lý SEO Cho Các Trang')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản Lý SEO Cho Các Trang</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.seo.index') }}">SEO</a></li>
                    <li class="breadcrumb-item active">SEO Trang</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Danh Sách Cấu Hình SEO Cho Các Trang</h4>
                        <div class="card-title-desc">Quản lý SEO cho từng trang cụ thể</div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <a href="{{ route('admin.page-seo.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Thêm Cấu Hình Mới
                            </a>
                            <a href="{{ route('admin.seo.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="mdi mdi-arrow-left me-1"></i> Quay Lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle-fill me-2"></i>
                {{ __('Cấu hình SEO cho các trang cụ thể sẽ ghi đè lên cấu hình SEO chung.') }}
            </div>

            @if(count($pages) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Trang') }}</th>
                                <th>{{ 'Tiêu đề' }}</th>
                                <th>{{ 'Trạng thái' }}</th>
                                <th>{{ 'Cập nhật lần cuối' }}</th>
                                <th width="120">{{ 'Thao tác' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pages as $page)
                                <tr>
                                    <td>
                                        @if($page->route_name)
                                            <div class="fw-bold">{{ $page->route_name }}</div>
                                        @endif
                                        @if($page->url_pattern)
                                            <div class="small text-muted">
                                                <i class="fas fa-link me-1"></i>{{ $page->url_pattern }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;">{{ $page->title }}</div>
                                        @if($page->description)
                                            <div class="small text-muted text-truncate" style="max-width: 300px;">
                                                {{ $page->description }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($page->is_active)
                                            <span class="badge bg-success">{{ 'Hoạt động' }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ 'Không hoạt động' }}</span>
                                        @endif

                                        @if($page->no_index)
                                            <span class="badge bg-warning ms-1" data-bs-toggle="tooltip" title="{{ __('Không cho phép công cụ tìm kiếm lập chỉ mục') }}">
                                                {{ __('No Index') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $page->updated_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.page-seo.edit', $page) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="{{ 'Chỉnh sửa' }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $page->id }}" title="{{ 'Xóa' }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $page->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $page->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $page->id }}">{{ __('Xóa cấu hình SEO') }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('admin.page-seo.destroy', $page) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="modal-body">
                                                            <p>{{ __('Bạn có chắc chắn muốn xóa cấu hình SEO này?') }}</p>
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle-fill me-2"></i>
                                                                {{ __('Hành động này không thể hoàn tác.') }}
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'Hủy' }}</button>
                                                            <button type="submit" class="btn btn-danger">{{ 'Xóa' }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $pages->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search font-size-48 text-muted mb-3"></i>
                    <p class="text-muted mb-0">Chưa có cấu hình SEO nào cho các trang.</p>
                    <div class="mt-3">
                        <a href="{{ route('admin.page-seo.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> {{ __('Thêm cấu hình mới') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    // Enable tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
