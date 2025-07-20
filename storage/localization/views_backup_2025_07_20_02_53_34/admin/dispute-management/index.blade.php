@extends('admin.layouts.dason')

@section('title', 'Quản Lý Dispute')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                Quản Lý Dispute & Chargeback
            </h1>
            <p class="text-muted mb-0">Xử lý disputes, chargebacks và customer complaints</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i>
                Bộ Lọc
            </button>
            <button type="button" class="btn btn-warning" onclick="toggleBulkActions()">
                <i class="fas fa-tasks me-1"></i>
                Bulk Actions
            </button>
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-1"></i>
                    Xuất Báo Cáo
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportDisputes('csv')">
                        <i class="fas fa-file-csv me-2"></i>Xuất CSV
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportDisputes('excel')">
                        <i class="fas fa-file-excel me-2"></i>Xuất Excel
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tổng Disputes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistics['total']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistics['pending']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Investigating
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistics['investigating']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-search fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Resolved
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistics['resolved']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Urgent
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistics['urgent']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-fire fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-dark shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Overdue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistics['overdue']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Disputes Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Danh Sách Disputes</h6>
        </div>
        <div class="card-body">
            <!-- Bulk Actions (Hidden by default) -->
            <div id="bulkActionsPanel" class="alert alert-warning" style="display: none;">
                <form method="POST" action="{{ route('admin.dispute-management.bulk-update') }}" onsubmit="return confirmBulkAction()">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Bulk Action</label>
                            <select name="bulk_action" class="form-select" required onchange="toggleBulkFields(this.value)">
                                <option value="">Chọn hành động</option>
                                <option value="assign">Assign to Admin</option>
                                <option value="update_status">Update Status</option>
                                <option value="update_priority">Update Priority</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="adminField" style="display: none;">
                            <label class="form-label">Admin</label>
                            <select name="admin_id" class="form-select">
                                <option value="">Chọn admin</option>
                                @foreach($admins as $admin)
                                <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" id="statusField" style="display: none;">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Chọn status</option>
                                <option value="pending">Pending</option>
                                <option value="investigating">Investigating</option>
                                <option value="evidence_required">Evidence Required</option>
                                <option value="escalated">Escalated</option>
                                <option value="resolved">Resolved</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="priorityField" style="display: none;">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="">Chọn priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-12 mt-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-bolt me-1"></i>
                                Thực Hiện
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="toggleBulkActions()">
                                Hủy
                            </button>
                        </div>
                    </div>
                    <div id="selectedDisputes"></div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="disputesTable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Dispute Reference</th>
                            <th>Customer</th>
                            <th>Order</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Assigned To</th>
                            <th>Date</th>
                            <th>Deadline</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($disputes as $dispute)
                        <tr class="{{ $dispute->is_overdue ? 'table-danger' : '' }}">
                            <td>
                                <input type="checkbox" class="dispute-checkbox" value="{{ $dispute->id }}" onchange="updateBulkSelection()">
                            </td>
                            <td>
                                <a href="{{ route('admin.dispute-management.show', $dispute) }}" class="text-decoration-none">
                                    <strong>{{ $dispute->dispute_reference }}</strong>
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <div class="avatar-initial bg-primary rounded-circle">
                                            {{ substr($dispute->customer->name ?? 'N', 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $dispute->customer->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $dispute->customer_email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-primary">
                                    {{ $dispute->order->order_number ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ $dispute->dispute_type_display }}
                                </span>
                            </td>
                            <td>
                                <strong class="text-danger">
                                    {{ number_format($dispute->disputed_amount, 0, ',', '.') }} VNĐ
                                </strong>
                            </td>
                            <td>
                                <span class="badge badge-{{ $dispute->status_badge }}">
                                    {{ ucfirst($dispute->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $dispute->priority_badge }}">
                                    {{ ucfirst($dispute->priority) }}
                                </span>
                                @if($dispute->is_overdue)
                                    <i class="fas fa-exclamation-triangle text-danger ms-1" title="Overdue"></i>
                                @endif
                            </td>
                            <td>
                                @if($dispute->assignedTo)
                                    <span class="text-primary">{{ $dispute->assignedTo->name }}</span>
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $dispute->dispute_date->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                @if($dispute->gateway_deadline)
                                    <small class="{{ $dispute->is_overdue ? 'text-danger' : 'text-muted' }}">
                                        {{ $dispute->gateway_deadline->format('d/m/Y') }}
                                        @if($dispute->days_until_deadline !== null)
                                            <br>({{ $dispute->days_until_deadline > 0 ? $dispute->days_until_deadline . ' days left' : 'Overdue' }})
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.dispute-management.show', $dispute) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($dispute->canBeAssigned())
                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                            data-bs-toggle="modal" data-bs-target="#assignModal{{ $dispute->id }}">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Assign Modal -->
                        <div class="modal fade" id="assignModal{{ $dispute->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Assign Dispute</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.dispute-management.assign', $dispute) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Assign to Admin</label>
                                                <select name="admin_id" class="form-select" required>
                                                    <option value="">Chọn admin</option>
                                                    @foreach($admins as $admin)
                                                    <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Notes</label>
                                                <textarea name="notes" class="form-control" rows="3" placeholder="Assignment notes..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-primary">Assign</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-4">
                                <i class="fas fa-exclamation-triangle fa-3x text-gray-300 mb-3"></i>
                                <p class="text-muted">Chưa có dispute nào</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $disputes->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lọc Disputes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('admin.dispute-management.index') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Tất cả status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>Investigating</option>
                                <option value="evidence_required" {{ request('status') === 'evidence_required' ? 'selected' : '' }}>Evidence Required</option>
                                <option value="escalated" {{ request('status') === 'escalated' ? 'selected' : '' }}>Escalated</option>
                                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="">Tất cả priorities</option>
                                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Dispute Type</label>
                            <select name="dispute_type" class="form-select">
                                <option value="">Tất cả types</option>
                                <option value="chargeback" {{ request('dispute_type') === 'chargeback' ? 'selected' : '' }}>Chargeback</option>
                                <option value="unauthorized" {{ request('dispute_type') === 'unauthorized' ? 'selected' : '' }}>Unauthorized</option>
                                <option value="product_not_received" {{ request('dispute_type') === 'product_not_received' ? 'selected' : '' }}>Product Not Received</option>
                                <option value="product_defective" {{ request('dispute_type') === 'product_defective' ? 'selected' : '' }}>Product Defective</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assigned To</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Tất cả admins</option>
                                @foreach($admins as $admin)
                                <option value="{{ $admin->id }}" {{ request('assigned_to') == $admin->id ? 'selected' : '' }}>
                                    {{ $admin->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Dispute reference, customer name, email..."
                                   value="{{ request('search') }}">
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
@endsection

@push('scripts')
<script>
let bulkActionsVisible = false;
let selectedDisputes = [];

function toggleBulkActions() {
    bulkActionsVisible = !bulkActionsVisible;
    const panel = document.getElementById('bulkActionsPanel');
    panel.style.display = bulkActionsVisible ? 'block' : 'none';

    if (!bulkActionsVisible) {
        // Clear selections
        document.getElementById('selectAll').checked = false;
        document.querySelectorAll('.dispute-checkbox').forEach(cb => cb.checked = false);
        selectedDisputes = [];
        updateBulkSelection();
    }
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.dispute-checkbox');

    checkboxes.forEach(cb => {
        cb.checked = selectAll.checked;
    });

    updateBulkSelection();
}

function updateBulkSelection() {
    const checkboxes = document.querySelectorAll('.dispute-checkbox:checked');
    selectedDisputes = Array.from(checkboxes).map(cb => cb.value);

    // Update hidden inputs
    const container = document.getElementById('selectedDisputes');
    container.innerHTML = '';

    selectedDisputes.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'dispute_ids[]';
        input.value = id;
        container.appendChild(input);
    });

    // Update select all checkbox
    const allCheckboxes = document.querySelectorAll('.dispute-checkbox');
    const selectAll = document.getElementById('selectAll');
    selectAll.checked = allCheckboxes.length > 0 && selectedDisputes.length === allCheckboxes.length;
}

function toggleBulkFields(action) {
    document.getElementById('adminField').style.display = action === 'assign' ? 'block' : 'none';
    document.getElementById('statusField').style.display = action === 'update_status' ? 'block' : 'none';
    document.getElementById('priorityField').style.display = action === 'update_priority' ? 'block' : 'none';
}

function confirmBulkAction() {
    if (selectedDisputes.length === 0) {
        alert('Vui lòng chọn ít nhất một dispute để thực hiện bulk action.');
        return false;
    }

    const action = document.querySelector('select[name="bulk_action"]').value;
    return confirm(`Bạn có chắc muốn ${action} ${selectedDisputes.length} disputes đã chọn?`);
}

function exportDisputes(format) {
    const params = new URLSearchParams({
        start_date: '{{ now()->startOfMonth()->format("Y-m-d") }}',
        end_date: '{{ now()->format("Y-m-d") }}',
        format: format
    });

    window.open(`{{ route('admin.dispute-management.export') }}?${params}`, '_blank');
}
</script>
@endpush
