@extends('admin.layouts.dason')

@section('title', 'Quản lý danh mục hỏi đáp')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản lý danh mục hỏi đáp</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Quản lý danh mục hỏi đáp</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex gap-2">
            <a href="{{ route('admin.faq-categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> {{ __('Tạo danh mục mới') }}
            </a>
            <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-question-circle me-1"></i> {{ __('Quản lý câu hỏi') }}
            </a>
        </div>
    </div>
</div>
@endsection



@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">{{ __('Danh sách danh mục') }}</h5>
                <span class="badge bg-primary">{{ $categories->count() }} {{ __('danh mục') }}</span>
            </div>

            <!-- Bulk Actions -->
            <div class="d-flex gap-2 mb-3" id="bulk-actions" style="display: none;">
                <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('activate')">
                    <i class="fas fa-check me-1"></i> {{ __('Kích hoạt') }}
                </button>
                <button type="button" class="btn btn-sm btn-warning" onclick="bulkAction('deactivate')">
                    <i class="fas fa-times me-1"></i> {{ __('Vô hiệu hóa') }}
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                    <i class="fas fa-trash me-1"></i> {{ __('Xóa') }}
                </button>
                <span class="text-muted ms-2">
                    <span id="selected-count">0</span> {{ __('mục được chọn') }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" width="40">
                                <input type="checkbox" class="form-check-input" id="select-all" onchange="toggleSelectAll()">
                            </th>
                            <th scope="col" width="60">{{ __('ID') }}</th>
                            <th scope="col">{{ __('Tên danh mục') }}</th>
                            <th scope="col" width="150">{{ __('Slug') }}</th>
                            <th scope="col" width="100">{{ __('Thứ tự') }}</th>
                            <th scope="col" width="100">{{ __('Trạng thái') }}</th>
                            <th scope="col" width="100">{{ __('Số câu hỏi') }}</th>
                            <th scope="col" width="120">{{ __('Thao tác') }}</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-categories">
                        @forelse($categories as $category)
                            <tr data-id="{{ $category->id }}">
                                <td>
                                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $category->id }}" onchange="updateBulkActions()">
                                </td>
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
                                    @if($category->is_active)
                                        <span class="badge bg-success">{{ __('Kích hoạt') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Vô hiệu hóa') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $category->faqs_count }}</span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.faq-categories.edit', $category) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Sửa') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.faq-categories.toggle-status', $category) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $category->is_active ? 'warning' : 'success' }}" title="{{ $category->is_active ? __('Vô hiệu hóa') : __('Kích hoạt') }}">
                                                <i class="fas fa-{{ $category->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $category->id }}" title="{{ __('Xóa') }}" {{ $category->faqs_count > 0 ? 'disabled' : '' }}>
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
                                                    @if($category->faqs_count > 0)
                                                        <div class="alert alert-danger">
                                                            {{ __('Không thể xóa danh mục này vì có') }} {{ $category->faqs_count }} {{ __('câu hỏi thuộc danh mục.') }}
                                                        </div>
                                                    @else
                                                        {{ __('Bạn có chắc chắn muốn xóa danh mục này?') }}
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                                                    @if($category->faqs_count == 0)
                                                        <form action="{{ route('admin.faq-categories.destroy', $category) }}" method="POST">
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
                                <td colspan="7" class="text-center py-4">{{ __('Không có danh mục nào.') }}</td>
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
                    url: '{{ route("admin.faq-categories.reorder") }}',
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

    // Bulk Actions JavaScript
    function toggleSelectAll() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.row-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });

        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        const bulkActions = document.getElementById('bulk-actions');
        const selectedCount = document.getElementById('selected-count');

        if (checkboxes.length > 0) {
            bulkActions.style.display = 'flex';
            selectedCount.textContent = checkboxes.length;
        } else {
            bulkActions.style.display = 'none';
        }

        // Update select-all checkbox state
        const allCheckboxes = document.querySelectorAll('.row-checkbox');
        const selectAll = document.getElementById('select-all');
        selectAll.checked = checkboxes.length === allCheckboxes.length;
        selectAll.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
    }

    function bulkAction(action) {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        const ids = Array.from(checkboxes).map(cb => cb.value);

        if (ids.length === 0) {
            alert('{{ __("Vui lòng chọn ít nhất một mục") }}');
            return;
        }

        let message = '';
        let url = '';

        switch(action) {
            case 'activate':
                message = `{{ __("Bạn có chắc muốn kích hoạt") }} ${ids.length} {{ __("danh mục được chọn?") }}`;
                url = '{{ route("admin.faq-categories.bulk-activate") }}';
                break;
            case 'deactivate':
                message = `{{ __("Bạn có chắc muốn vô hiệu hóa") }} ${ids.length} {{ __("danh mục được chọn?") }}`;
                url = '{{ route("admin.faq-categories.bulk-deactivate") }}';
                break;
            case 'delete':
                message = `{{ __("Bạn có chắc muốn xóa") }} ${ids.length} {{ __("danh mục được chọn? Hành động này không thể hoàn tác!") }}`;
                url = '{{ route("admin.faq-categories.bulk-delete") }}';
                break;
        }

        if (confirm(message)) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            // CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // IDs
            const idsInput = document.createElement('input');
            idsInput.type = 'hidden';
            idsInput.name = 'ids';
            idsInput.value = JSON.stringify(ids);
            form.appendChild(idsInput);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
