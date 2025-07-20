@extends('admin.layouts.dason')

@section('title', 'Quản Lý Refund')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-undo text-info me-2"></i>
                Quản Lý Refund & Hoàn Tiền
            </h1>
            <p class="text-muted mb-0">Xử lý refund requests, approval workflow và gateway processing</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.refund-management.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Tạo Refund
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i>
                Bộ Lọc
            </button>
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-1"></i>
                    Xuất Báo Cáo
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportRefunds('csv')">
                        <i class="fas fa-file-csv me-2"></i>Xuất CSV
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportRefunds('excel')">
                        <i class="fas fa-file-excel me-2"></i>Xuất Excel
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tổng Refunds
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistics['total']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-undo fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
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
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Approved
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistics['approved']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
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
                                Completed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistics['completed']) }}
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
                                Failed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistics['failed']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                                Tổng Tiền
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($statistics['total_amount'], 0, ',', '.') }} VNĐ
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Refunds Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Danh Sách Refunds</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="refundsTable">
                    <thead>
                        <tr>
                            <th>Refund Reference</th>
                            <th>Customer</th>
                            <th>Order</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment Method</th>
                            <th>Requested Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refunds as $refund)
                        <tr>
                            <td>
                                <a href="{{ route('admin.refund-management.show', $refund) }}" class="text-decoration-none">
                                    <strong>{{ $refund->refund_reference }}</strong>
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <div class="avatar-initial bg-primary rounded-circle">
                                            {{ substr($refund->customer->name ?? 'N', 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $refund->customer->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $refund->customer->email ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-primary">
                                    {{ $refund->order->order_number ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ ucfirst($refund->refund_type) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ ucfirst(str_replace('_', ' ', $refund->reason)) }}
                                </span>
                            </td>
                            <td>
                                <strong class="text-success">
                                    {{ number_format($refund->refund_amount, 0, ',', '.') }} VNĐ
                                </strong>
                                @if($refund->gateway_fee > 0)
                                    <br><small class="text-muted">Fee: {{ number_format($refund->gateway_fee, 0, ',', '.') }} VNĐ</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusBadge = match($refund->status) {
                                        'pending' => 'warning',
                                        'approved' => 'primary',
                                        'processing' => 'info',
                                        'completed' => 'success',
                                        'failed' => 'danger',
                                        'rejected' => 'dark',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusBadge }}">
                                    {{ ucfirst($refund->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-outline-secondary">
                                    {{ strtoupper($refund->payment_method) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $refund->requested_at->format('d/m/Y H:i') }}</small>
                                @if($refund->approved_at)
                                    <br><small class="text-success">Approved: {{ $refund->approved_at->format('d/m/Y H:i') }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.refund-management.show', $refund) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($refund->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                data-bs-toggle="modal" data-bs-target="#approveModal{{ $refund->id }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $refund->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    
                                    @if($refund->status === 'approved')
                                        <form method="POST" action="{{ route('admin.refund-management.process', $refund) }}" 
                                              style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn process refund này?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Approve Modal -->
                        @if($refund->status === 'pending')
                        <div class="modal fade" id="approveModal{{ $refund->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Approve Refund</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.refund-management.approve', $refund) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Admin Notes</label>
                                                <textarea name="admin_notes" class="form-control" rows="3" 
                                                          placeholder="Notes về việc approve refund..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-success">Approve</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal{{ $refund->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Reject Refund</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.refund-management.reject', $refund) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Rejection Reason</label>
                                                <textarea name="rejection_reason" class="form-control" rows="3" 
                                                          placeholder="Lý do reject refund..." required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-danger">Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-undo fa-3x text-gray-300 mb-3"></i>
                                <p class="text-muted">Chưa có refund nào</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $refunds->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lọc Refunds</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('admin.refund-management.index') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Tất cả status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Refund Type</label>
                            <select name="refund_type" class="form-select">
                                <option value="">Tất cả types</option>
                                <option value="full" {{ request('refund_type') === 'full' ? 'selected' : '' }}>Full</option>
                                <option value="partial" {{ request('refund_type') === 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="shipping" {{ request('refund_type') === 'shipping' ? 'selected' : '' }}>Shipping</option>
                                <option value="tax" {{ request('refund_type') === 'tax' ? 'selected' : '' }}>Tax</option>
                                <option value="item" {{ request('refund_type') === 'item' ? 'selected' : '' }}>Item</option>
                                <option value="goodwill" {{ request('refund_type') === 'goodwill' ? 'selected' : '' }}>Goodwill</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="">Tất cả methods</option>
                                <option value="stripe" {{ request('payment_method') === 'stripe' ? 'selected' : '' }}>Stripe</option>
                                <option value="sepay" {{ request('payment_method') === 'sepay' ? 'selected' : '' }}>SePay</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Refund reference, customer name..." 
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
function exportRefunds(format) {
    const params = new URLSearchParams({
        start_date: '{{ now()->startOfMonth()->format("Y-m-d") }}',
        end_date: '{{ now()->format("Y-m-d") }}',
        format: format
    });
    
    window.open(`{{ route('admin.refund-management.export') }}?${params}`, '_blank');
}
</script>
@endpush
