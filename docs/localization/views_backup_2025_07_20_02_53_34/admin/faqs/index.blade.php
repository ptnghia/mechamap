@extends('admin.layouts.dason')

@section('title', 'Quản lý hỏi đáp')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản lý hỏi đáp</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Quản lý hỏi đáp</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex gap-2">
            <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> {{ __('Tạo câu hỏi mới') }}
            </a>
            <a href="{{ route('admin.faq-categories.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-folder me-1"></i> {{ __('Quản lý danh mục') }}
            </a>
        </div>
    </div>
</div>
@endsection



@push('styles')
<!-- Page specific CSS -->
@endpush

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Bộ lọc') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.faqs.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="category_id" class="form-label">{{ __('Danh mục') }}</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">{{ __('Tất cả') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">{{ __('Trạng thái') }}</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">{{ __('Tất cả') }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Kích hoạt') }}</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Vô hiệu hóa') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">{{ __(__('ui.actions.search')) }}</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Câu hỏi, câu trả lời...') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> {{ __('Lọc') }}
                    </button>
                    <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times-circle me-1"></i> {{ __('Xóa bộ lọc') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">{{ __('Danh sách câu hỏi') }}</h5>
                <span class="badge bg-primary">{{ $faqs->total() }} {{ __('câu hỏi') }}</span>
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
                            <th scope="col">{{ __('Câu hỏi') }}</th>
                            <th scope="col" width="150">{{ __('Danh mục') }}</th>
                            <th scope="col" width="100">{{ __('Thứ tự') }}</th>
                            <th scope="col" width="100">{{ __('Trạng thái') }}</th>
                            <th scope="col" width="120">{{ __('Thao tác') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faqs as $faq)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $faq->id }}" onchange="updateBulkActions()">
                                </td>
                                <td>{{ $faq->id }}</td>
                                <td>
                                    <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-decoration-none fw-bold">
                                        {{ $faq->question }}
                                    </a>
                                </td>
                                <td>{{ $faq->category->name }}</td>
                                <td>{{ $faq->order }}</td>
                                <td>
                                    @if($faq->is_active)
                                        <span class="badge bg-success">{{ __('Kích hoạt') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Vô hiệu hóa') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Sửa') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.faqs.toggle-status', $faq) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $faq->is_active ? 'warning' : 'success' }}" title="{{ $faq->is_active ? __('Vô hiệu hóa') : __('Kích hoạt') }}">
                                                <i class="fas fa-{{ $faq->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $faq->id }}" title="{{ __('Xóa') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal xóa -->
                                    <div class="modal fade" id="deleteModal{{ $faq->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $faq->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $faq->id }}">{{ __('Xác nhận xóa') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ __('Bạn có chắc chắn muốn xóa câu hỏi này?') }}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                                                    <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">{{ __('Xóa') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">{{ __('Không có câu hỏi nào.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $faqs->withQueryString()->links() }}
        </div>
    </div>

@push('scripts')
<script>
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
                message = `{{ __("Bạn có chắc muốn kích hoạt") }} ${ids.length} {{ __("câu hỏi được chọn?") }}`;
                url = '{{ route("admin.faqs.bulk-activate") }}';
                break;
            case 'deactivate':
                message = `{{ __("Bạn có chắc muốn vô hiệu hóa") }} ${ids.length} {{ __("câu hỏi được chọn?") }}`;
                url = '{{ route("admin.faqs.bulk-deactivate") }}';
                break;
            case 'delete':
                message = `{{ __("Bạn có chắc muốn xóa") }} ${ids.length} {{ __("câu hỏi được chọn? Hành động này không thể hoàn tác!") }}`;
                url = '{{ route("admin.faqs.bulk-delete") }}';
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
@endsection
