@extends('admin.layouts.app')

@section('title', 'Cấu Hình Hoa Hồng')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-percentage text-warning me-2"></i>
                Cấu Hình Hoa Hồng
            </h1>
            <p class="text-muted mb-0">Quản lý tỷ lệ hoa hồng theo seller role và product type</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i>
                Lọc
            </button>
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#testCalculationModal">
                <i class="fas fa-calculator me-1"></i>
                Test Tính Toán
            </button>
            <a href="{{ route('admin.commission-settings.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i>
                Tạo Mới
            </a>
        </div>
    </div>

    <!-- Active Settings Summary -->
    <div class="row mb-4">
        @foreach($activeSummary as $role => $data)
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ $data['role_name'] }}
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                @if($data['settings']->isNotEmpty())
                                    {{ $data['settings']->count() }} cấu hình
                                @else
                                    {{ $data['default_rate'] }}% (mặc định)
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Commission Settings Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Danh Sách Cấu Hình Hoa Hồng</h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-warning" onclick="toggleBulkActions()">
                    <i class="fas fa-tasks me-1"></i>
                    Bulk Actions
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Bulk Actions (Hidden by default) -->
            <div id="bulkActionsPanel" class="alert alert-warning" style="display: none;">
                <form method="POST" action="{{ route('admin.commission-settings.bulk-update') }}" onsubmit="return confirmBulkAction()">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Bulk Action</label>
                            <select name="bulk_action" class="form-select" required>
                                <option value="">Chọn hành động</option>
                                <option value="activate">Kích hoạt</option>
                                <option value="deactivate">Vô hiệu hóa</option>
                                <option value="delete">Xóa</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-bolt me-1"></i>
                                Thực Hiện
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="toggleBulkActions()">
                                Hủy
                            </button>
                        </div>
                    </div>
                    <div id="selectedSettings"></div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="settingsTable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Seller Role</th>
                            <th>Product Type</th>
                            <th>Commission Rate</th>
                            <th>Fixed Fee</th>
                            <th>Min/Max Commission</th>
                            <th>Effective Period</th>
                            <th>Trạng Thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commissionSettings as $setting)
                        <tr>
                            <td>
                                <input type="checkbox" class="setting-checkbox" value="{{ $setting->id }}" onchange="updateBulkSelection()">
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $sellerRoles[$setting->seller_role] ?? $setting->seller_role }}
                                </span>
                            </td>
                            <td>
                                @if($setting->product_type)
                                    <span class="badge badge-secondary">
                                        {{ $productTypes[$setting->product_type] ?? $setting->product_type }}
                                    </span>
                                @else
                                    <span class="text-muted">Tất cả</span>
                                @endif
                            </td>
                            <td>
                                <strong class="text-primary">{{ $setting->commission_rate }}%</strong>
                            </td>
                            <td>
                                @if($setting->fixed_fee > 0)
                                    {{ number_format($setting->fixed_fee, 0, ',', '.') }} VNĐ
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small>
                                    @if($setting->min_commission > 0)
                                        Min: {{ number_format($setting->min_commission, 0, ',', '.') }} VNĐ<br>
                                    @endif
                                    @if($setting->max_commission > 0)
                                        Max: {{ number_format($setting->max_commission, 0, ',', '.') }} VNĐ
                                    @endif
                                    @if($setting->min_commission == 0 && $setting->max_commission == 0)
                                        <span class="text-muted">Không giới hạn</span>
                                    @endif
                                </small>
                            </td>
                            <td>
                                <small>
                                    Từ: {{ $setting->effective_from->format('d/m/Y') }}<br>
                                    @if($setting->effective_until)
                                        Đến: {{ $setting->effective_until->format('d/m/Y') }}
                                    @else
                                        <span class="text-success">Vô thời hạn</span>
                                    @endif
                                </small>
                            </td>
                            <td>
                                <span class="badge badge-{{ $setting->is_active ? 'success' : 'secondary' }}">
                                    {{ $setting->is_active ? 'Hoạt động' : 'Vô hiệu' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.commission-settings.show', $setting) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.commission-settings.edit', $setting) }}" 
                                       class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.commission-settings.toggle-status', $setting) }}" 
                                          style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $setting->is_active ? 'secondary' : 'success' }}"
                                                onclick="return confirm('Bạn có chắc muốn thay đổi trạng thái?')">
                                            <i class="fas fa-{{ $setting->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.commission-settings.destroy', $setting) }}" 
                                          style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Bạn có chắc muốn xóa cấu hình này?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-percentage fa-3x text-gray-300 mb-3"></i>
                                <p class="text-muted">Chưa có cấu hình hoa hồng nào</p>
                                <a href="{{ route('admin.commission-settings.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>
                                    Tạo Cấu Hình Đầu Tiên
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $commissionSettings->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lọc Cấu Hình Hoa Hồng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('admin.commission-settings.index') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Seller Role</label>
                            <select name="seller_role" class="form-select">
                                <option value="">Tất cả roles</option>
                                @foreach($sellerRoles as $role => $roleName)
                                <option value="{{ $role }}" {{ request('seller_role') === $role ? 'selected' : '' }}>
                                    {{ $roleName }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Product Type</label>
                            <select name="product_type" class="form-select">
                                <option value="">Tất cả types</option>
                                @foreach($productTypes as $type => $typeName)
                                <option value="{{ $type }}" {{ request('product_type') === $type ? 'selected' : '' }}>
                                    {{ $typeName }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Trạng Thái</label>
                            <select name="status" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Vô hiệu</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Áp Dụng Lọc</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Test Calculation Modal -->
<div class="modal fade" id="testCalculationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Tính Toán Hoa Hồng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="testCalculationForm">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Seller Role</label>
                            <select name="seller_role" class="form-select" required>
                                <option value="">Chọn role</option>
                                @foreach($sellerRoles as $role => $roleName)
                                <option value="{{ $role }}">{{ $roleName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Product Type</label>
                            <select name="product_type" class="form-select">
                                <option value="">Tất cả types</option>
                                @foreach($productTypes as $type => $typeName)
                                <option value="{{ $type }}">{{ $typeName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giá Trị Đơn Hàng (VNĐ)</label>
                            <input type="number" name="order_value" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-calculator me-1"></i>
                            Tính Toán
                        </button>
                    </div>
                </form>
                
                <div id="calculationResult" class="mt-4" style="display: none;">
                    <div class="alert alert-info">
                        <h6>Kết Quả Tính Toán:</h6>
                        <div id="resultContent"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let bulkActionsVisible = false;
let selectedSettings = [];

function toggleBulkActions() {
    bulkActionsVisible = !bulkActionsVisible;
    const panel = document.getElementById('bulkActionsPanel');
    panel.style.display = bulkActionsVisible ? 'block' : 'none';
    
    if (!bulkActionsVisible) {
        // Clear selections
        document.getElementById('selectAll').checked = false;
        document.querySelectorAll('.setting-checkbox').forEach(cb => cb.checked = false);
        selectedSettings = [];
        updateBulkSelection();
    }
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.setting-checkbox');
    
    checkboxes.forEach(cb => {
        cb.checked = selectAll.checked;
    });
    
    updateBulkSelection();
}

function updateBulkSelection() {
    const checkboxes = document.querySelectorAll('.setting-checkbox:checked');
    selectedSettings = Array.from(checkboxes).map(cb => cb.value);
    
    // Update hidden inputs
    const container = document.getElementById('selectedSettings');
    container.innerHTML = '';
    
    selectedSettings.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'setting_ids[]';
        input.value = id;
        container.appendChild(input);
    });
    
    // Update select all checkbox
    const allCheckboxes = document.querySelectorAll('.setting-checkbox');
    const selectAll = document.getElementById('selectAll');
    selectAll.checked = allCheckboxes.length > 0 && selectedSettings.length === allCheckboxes.length;
}

function confirmBulkAction() {
    if (selectedSettings.length === 0) {
        alert('Vui lòng chọn ít nhất một cấu hình để thực hiện bulk action.');
        return false;
    }
    
    const action = document.querySelector('select[name="bulk_action"]').value;
    return confirm(`Bạn có chắc muốn ${action} ${selectedSettings.length} cấu hình đã chọn?`);
}

// Test calculation
document.getElementById('testCalculationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const params = new URLSearchParams(formData);
    
    fetch(`{{ route('admin.commission-settings.test-calculation') }}?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const calc = data.calculation;
                const resultHtml = `
                    <table class="table table-sm">
                        <tr><td><strong>Giá trị đơn hàng:</strong></td><td>${new Intl.NumberFormat('vi-VN').format(calc.order_value)} VNĐ</td></tr>
                        <tr><td><strong>Tỷ lệ hoa hồng:</strong></td><td>${calc.commission_rate}%</td></tr>
                        <tr><td><strong>Hoa hồng Admin:</strong></td><td class="text-success">${new Intl.NumberFormat('vi-VN').format(calc.commission_amount)} VNĐ</td></tr>
                        <tr><td><strong>Thu nhập Seller:</strong></td><td class="text-primary">${new Intl.NumberFormat('vi-VN').format(calc.seller_earnings)} VNĐ</td></tr>
                        <tr><td><strong>Nguồn cấu hình:</strong></td><td><span class="badge badge-${calc.source === 'database' ? 'success' : 'warning'}">${calc.source === 'database' ? 'Database' : 'Mặc định'}</span></td></tr>
                    </table>
                `;
                
                document.getElementById('resultContent').innerHTML = resultHtml;
                document.getElementById('calculationResult').style.display = 'block';
            } else {
                alert('Lỗi tính toán: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi kết nối server');
        });
});
</script>
@endpush
