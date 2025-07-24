@extends('admin.layouts.dason')

@section('title', 'Quản lý danh mục bài viết')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản lý danh mục bài viết</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Quản lý danh mục bài viết</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.page-categories.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus me-1"></i> {{ __('Tạo danh mục mới') }}
    </a>
    <a href="{{ route('admin.pages.index') }}" class="btn btn-sm btn-outline-primary">
        <i class="fas fa-file-text me-1"></i> {{ __('Quản lý bài viết') }}
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Danh sách danh mục') }}</h5>
            <span class="badge bg-primary">{{ $categories->count() }} {{ __('danh mục') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" width="60">{{ __('ID') }}</th>
                            <th scope="col">{{ __('Tên danh mục') }}</th>
                            <th scope="col" width="150">{{ __('Slug') }}</th>
                            <th scope="col" width="100">{{ __('Thứ tự') }}</th>
                            <th scope="col" width="100">{{ __('Số bài viết') }}</th>
                            <th scope="col" width="120">{{ __('Thao tác') }}</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-categories">
                        @forelse($categories as $category)
                            <tr data-id="{{ $category->id }}">
                                <td>{{ $category->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-grip-vertical me-2 text-muted handle" style="cursor: move;"></i>
                                        <span>{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td><code>{{ $category->slug }}</code></td>
                                <td>
                                    <span class="order-value">{{ $category->order }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $category->pages_count }}</span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.page-categories.edit', $category) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Sửa') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $category->id }}" title="{{ __('Xóa') }}" {{ $category->pages_count > 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal xóa -->
                                    <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $category->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $category->id }}">{{ __('Xác nhận xóa') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @if($category->pages_count > 0)
                                                        <div class="alert alert-danger">
                                                            {{ __('Không thể xóa danh mục này vì có') }} {{ $category->pages_count }} {{ __('bài viết thuộc danh mục.') }}
                                                        </div>
                                                    @else
                                                        {{ __('Bạn có chắc chắn muốn xóa danh mục này?') }}
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                                                    @if($category->pages_count == 0)
                                                        <form action="{{ route('admin.page-categories.destroy', $category) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">{{ __('Xóa') }}</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">{{ __('Không có danh mục nào.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .handle {
        cursor: move;
    }
    .ui-sortable-helper {
        display: table;
        background-color: #f8f9fa;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(function() {
        $("#sortable-categories").sortable({
            handle: '.handle',
            update: function(event, ui) {
                let categories = [];
                $('#sortable-categories tr').each(function(index) {
                    const id = $(this).data('id');
                    const order = index;
                    $(this).find('.order-value').text(order);
                    categories.push({
                        id: id,
                        order: order
                    });
                });
                
                // Gửi AJAX request để cập nhật thứ tự
                $.ajax({
                    url: '{{ route("admin.page-categories.reorder") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        categories: categories
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('{{ __("Thứ tự danh mục đã được cập nhật.") }}');
                        } else {
                            toastr.error('{{ __("Có lỗi xảy ra khi cập nhật thứ tự danh mục.") }}');
                        }
                    },
                    error: function() {
                        toastr.error('{{ __("Có lỗi xảy ra khi cập nhật thứ tự danh mục.") }}');
                    }
                });
            }
        });
    });
</script>
@endpush
