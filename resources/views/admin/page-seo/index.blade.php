@extends('admin.layouts.app')

@section('title', 'Quản lý SEO cho các trang')
@section('header', 'Quản lý SEO cho các trang')

@section('actions')
    <a href="{{ route('admin.page-seo.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-circle me-1"></i> {{ __('Thêm cấu hình mới') }}
    </a>
    <a href="{{ route('admin.seo.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
        <i class="bi bi-arrow-left me-1"></i> {{ __('Quay lại') }}
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Danh sách cấu hình SEO cho các trang') }}</h5>
            <span class="badge bg-secondary">{{ __('Tổng') }}: {{ $pages->total() }}</span>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle-fill me-2"></i>
                {{ __('Cấu hình SEO cho các trang cụ thể sẽ ghi đè lên cấu hình SEO chung.') }}
            </div>
            
            @if(count($pages) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Trang') }}</th>
                                <th>{{ __('Tiêu đề') }}</th>
                                <th>{{ __('Trạng thái') }}</th>
                                <th>{{ __('Cập nhật lần cuối') }}</th>
                                <th width="120">{{ __('Thao tác') }}</th>
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
                                                <i class="bi bi-link-45deg me-1"></i>{{ $page->url_pattern }}
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
                                            <span class="badge bg-success">{{ __('Hoạt động') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('Không hoạt động') }}</span>
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
                                            <a href="{{ route('admin.page-seo.edit', $page) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="{{ __('Chỉnh sửa') }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $page->id }}" title="{{ __('Xóa') }}">
                                                <i class="bi bi-trash"></i>
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
                                                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                                {{ __('Hành động này không thể hoàn tác.') }}
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                                                            <button type="submit" class="btn btn-danger">{{ __('Xóa') }}</button>
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
                    <i class="bi bi-search text-muted fs-1 d-block mb-3"></i>
                    <p class="text-muted mb-0">{{ __('Chưa có cấu hình SEO nào cho các trang.') }}</p>
                    <div class="mt-3">
                        <a href="{{ route('admin.page-seo.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> {{ __('Thêm cấu hình mới') }}
                        </a>
                    </div>
                </div>
            @endif
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
