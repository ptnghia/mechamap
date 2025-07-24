@extends('admin.layouts.app')

@section('title', 'Quản Lý Payout')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-money-check-alt text-success me-2"></i>
                Quản Lý Payout Sellers
            </h1>
            <p class="text-muted mb-0">Duyệt và xử lý yêu cầu thanh toán cho sellers</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i>
                Lọc
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-download me-1"></i>
                Xuất Báo Cáo
            </button>
            <a href="{{ route('admin.payout-management.analytics') }}" class="btn btn-info">
                <i class="fas fa-chart-line me-1"></i>
                Analytics
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Chờ Duyệt
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['pending_count']) }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ number_format($stats['pending_amount'], 0, ',', '.') }} VNĐ
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Đã Duyệt
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['approved_count']) }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ number_format($stats['approved_amount'], 0, ',', '.') }} VNĐ
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Hoàn Thành Hôm Nay
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['completed_today']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng Tháng Này
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_paid_this_month'], 0, ',', '.') }} VNĐ
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Requests Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Danh Sách Payout Requests</h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-success" onclick="toggleBulkActions()">
                    <i class="fas fa-tasks me-1"></i>
                    Bulk Actions
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Bulk Actions (Hidden by default) -->
            <div id="bulkActionsPanel" class="alert alert-info" style="display: none;">
                <form method="POST" action="{{ route('admin.payout-management.bulk-approve') }}" onsubmit="return confirmBulkAction()">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Ghi Chú Bulk Approval</label>
                            <input type="text" name="bulk_notes" class="form-control" placeholder="Ghi chú cho tất cả payouts được duyệt">
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-1"></i>
                                Duyệt Các Mục Đã Chọn
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="toggleBulkActions()">
                                Hủy
                            </button>
                        </div>
                    </div>
                    <div id="selectedPayouts"></div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="payoutTable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Mã Payout</th>
                            <th>Seller</th>
                            <th>Số Tiền</th>
                            <th>Đơn Hàng</th>
                            <th>Kỳ Thanh Toán</th>
                            <th>Trạng Thái</th>
                            <th>Ngày Tạo</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payoutRequests as $payout)
                        <tr>
                            <td>
                                @if($payout->status === 'pending')
                                <input type="checkbox" class="payout-checkbox" value="{{ $payout->id }}" onchange="updateBulkSelection()">
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.payout-management.show', $payout) }}" class="text-decoration-none">
                                    <strong>{{ $payout->payout_reference }}</strong>
                                </a>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $payout->seller->name ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $payout->seller->email ?? 'N/A' }}</small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong class="text-success">{{ number_format($payout->net_payout, 0, ',', '.') }} VNĐ</strong>
                                    <br>
                                    <small class="text-muted">
                                        Sales: {{ number_format($payout->total_sales, 0, ',', '.') }} VNĐ
                                        <br>
                                        Commission: {{ number_format($payout->commission_amount, 0, ',', '.') }} VNĐ
                                    </small>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $payout->order_count }}</span>
                            </td>
                            <td>
                                <small>
                                    {{ $payout->period_from ? $payout->period_from->format('d/m/Y') : 'N/A' }}
                                    <br>
                                    {{ $payout->period_to ? $payout->period_to->format('d/m/Y') : 'N/A' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge badge-{{ $payout->status_color }}">
                                    {{ ucfirst($payout->status) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $payout->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.payout-management.show', $payout) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($payout->status === 'pending')
                                    <button type="button" class="btn btn-sm btn-success" 
                                            onclick="approvePayout({{ $payout->id }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="rejectPayout({{ $payout->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @elseif($payout->status === 'approved')
                                    <button type="button" class="btn btn-sm btn-info" 
                                            onclick="markCompleted({{ $payout->id }})">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                <p class="text-muted">Không có payout requests nào</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $payoutRequests->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lọc Payout Requests</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('admin.payout-management.index') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Trạng Thái</label>
                            <select name="status" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ Duyệt</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã Duyệt</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn Thành</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ Chối</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Seller</label>
                            <select name="seller_id" class="form-select">
                                <option value="">Tất cả sellers</option>
                                @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" {{ request('seller_id') == $seller->id ? 'selected' : '' }}>
                                    {{ $seller->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Từ Ngày</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Đến Ngày</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
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

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xuất Báo Cáo Payout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.payout-management.export') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Trạng Thái</label>
                            <select name="export_status" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="pending">Chờ Duyệt</option>
                                <option value="approved">Đã Duyệt</option>
                                <option value="completed">Hoàn Thành</option>
                                <option value="rejected">Từ Chối</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Định Dạng</label>
                            <select name="format" class="form-select" required>
                                <option value="csv">CSV</option>
                                <option value="excel">Excel</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Từ Ngày</label>
                            <input type="date" name="export_date_from" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Đến Ngày</label>
                            <input type="date" name="export_date_to" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download me-1"></i>
                        Xuất Báo Cáo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Action Modals will be added via JavaScript -->
<div id="actionModals"></div>
@endsection

@push('scripts')
<script>
let bulkActionsVisible = false;
let selectedPayouts = [];

function toggleBulkActions() {
    bulkActionsVisible = !bulkActionsVisible;
    const panel = document.getElementById('bulkActionsPanel');
    panel.style.display = bulkActionsVisible ? 'block' : 'none';
    
    if (!bulkActionsVisible) {
        // Clear selections
        document.getElementById('selectAll').checked = false;
        document.querySelectorAll('.payout-checkbox').forEach(cb => cb.checked = false);
        selectedPayouts = [];
        updateBulkSelection();
    }
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.payout-checkbox');
    
    checkboxes.forEach(cb => {
        cb.checked = selectAll.checked;
    });
    
    updateBulkSelection();
}

function updateBulkSelection() {
    const checkboxes = document.querySelectorAll('.payout-checkbox:checked');
    selectedPayouts = Array.from(checkboxes).map(cb => cb.value);
    
    // Update hidden inputs
    const container = document.getElementById('selectedPayouts');
    container.innerHTML = '';
    
    selectedPayouts.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'payout_ids[]';
        input.value = id;
        container.appendChild(input);
    });
    
    // Update select all checkbox
    const allCheckboxes = document.querySelectorAll('.payout-checkbox');
    const selectAll = document.getElementById('selectAll');
    selectAll.checked = allCheckboxes.length > 0 && selectedPayouts.length === allCheckboxes.length;
}

function confirmBulkAction() {
    if (selectedPayouts.length === 0) {
        alert('Vui lòng chọn ít nhất một payout để thực hiện bulk action.');
        return false;
    }
    
    return confirm(`Bạn có chắc muốn duyệt ${selectedPayouts.length} payout requests đã chọn?`);
}

function approvePayout(payoutId) {
    const notes = prompt('Ghi chú cho việc duyệt payout (tùy chọn):');
    if (notes !== null) {
        submitPayoutAction('approve', payoutId, { admin_notes: notes });
    }
}

function rejectPayout(payoutId) {
    const reason = prompt('Lý do từ chối payout (bắt buộc):');
    if (reason && reason.trim()) {
        submitPayoutAction('reject', payoutId, { rejection_reason: reason });
    } else if (reason !== null) {
        alert('Vui lòng nhập lý do từ chối.');
    }
}

function markCompleted(payoutId) {
    const notes = prompt('Ghi chú hoàn thành (tùy chọn):');
    const transactionRef = prompt('Mã giao dịch (tùy chọn):');
    
    if (notes !== null) {
        submitPayoutAction('mark-completed', payoutId, { 
            completion_notes: notes || '',
            transaction_reference: transactionRef || ''
        });
    }
}

function submitPayoutAction(action, payoutId, data) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/payout-management/${payoutId}/${action}`;
    
    // CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfInput);
    
    // Data inputs
    Object.keys(data).forEach(key => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = data[key];
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
