@extends('admin.layouts.dason')

@section('title', 'Báo Cáo Vi Phạm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-flag me-2"></i>
                    Báo Cáo Vi Phạm
                </h1>
                <div class="page-subtitle">Quản lý và xử lý báo cáo vi phạm từ cộng đồng</div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                            <p class="mb-0">Chờ Xử Lý</p>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['high_priority'] ?? 0 }}</h3>
                            <p class="mb-0">Ưu Tiên Cao</p>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['resolved'] ?? 0 }}</h3>
                            <p class="mb-0">Đã Giải Quyết</p>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                            <p class="mb-0">Tổng Báo Cáo</p>
                        </div>
                        <i class="fas fa-list fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.moderation.reports') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Trạng Thái</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                                <option value="dismissed" {{ request('status') === 'dismissed' ? 'selected' : '' }}>Đã bỏ qua</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="priority" class="form-label">Mức Độ Ưu Tiên</label>
                            <select name="priority" id="priority" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Thấp</option>
                                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Trung bình</option>
                                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Cao</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="type" class="form-label">Loại Nội Dung</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="App\Models\Thread" {{ request('type') === 'App\Models\Thread' ? 'selected' : '' }}>Bài đăng</option>
                                <option value="App\Models\Comment" {{ request('type') === 'App\Models\Comment' ? 'selected' : '' }}>Bình luận</option>
                                <option value="App\Models\User" {{ request('type') === 'App\Models\User' ? 'selected' : '' }}>Người dùng</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="search" class="form-label">Tìm Kiếm</label>
                            <div class="input-group">
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Tìm theo lý do, mô tả..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh Sách Báo Cáo Vi Phạm</h5>
                        <div>
                            <button type="button" class="btn btn-success btn-sm" id="bulk-resolve">
                                <i class="fas fa-check"></i> Giải quyết
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="bulk-dismiss">
                                <i class="fas fa-times"></i> Bỏ qua
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($reports->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" id="select-all" class="form-check-input">
                                    </th>
                                    <th>Báo Cáo</th>
                                    <th>Nội Dung Bị Báo Cáo</th>
                                    <th>Người Báo Cáo</th>
                                    <th>Trạng Thái</th>
                                    <th>Ưu Tiên</th>
                                    <th>Ngày Tạo</th>
                                    <th width="120">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $report)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="report_ids[]" value="{{ $report->id }}" class="form-check-input report-checkbox">
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $report->reason }}</strong>
                                            @if($report->description)
                                            <br><small class="text-muted">{{ Str::limit($report->description, 100) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($report->reportable_type === 'App\Models\Thread')
                                            <span class="badge bg-primary">Bài đăng</span>
                                            <br><small>{{ Str::limit($report->reportable->title ?? 'N/A', 50) }}</small>
                                        @elseif($report->reportable_type === 'App\Models\Comment')
                                            <span class="badge bg-info">Bình luận</span>
                                            <br><small>{{ Str::limit($report->reportable->content ?? 'N/A', 50) }}</small>
                                        @elseif($report->reportable_type === 'App\Models\User')
                                            <span class="badge bg-warning">Người dùng</span>
                                            <br><small>{{ $report->reportable->name ?? 'N/A' }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $report->reporter->avatar ?? '/assets/images/users/default.jpg' }}" 
                                                 alt="{{ $report->reporter->name }}" class="rounded-circle me-2" width="32" height="32">
                                            <div>
                                                <div class="fw-medium">{{ $report->reporter->name }}</div>
                                                <small class="text-muted">{{ $report->reporter->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($report->status === 'pending')
                                            <span class="badge bg-warning">Chờ xử lý</span>
                                        @elseif($report->status === 'resolved')
                                            <span class="badge bg-success">Đã giải quyết</span>
                                        @elseif($report->status === 'dismissed')
                                            <span class="badge bg-secondary">Đã bỏ qua</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->priority === 'high')
                                            <span class="badge bg-danger">Cao</span>
                                        @elseif($report->priority === 'medium')
                                            <span class="badge bg-warning">Trung bình</span>
                                        @else
                                            <span class="badge bg-info">Thấp</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $report->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $report->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($report->status === 'pending')
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-success btn-sm resolve-report" 
                                                    data-id="{{ $report->id }}" title="Giải quyết">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm dismiss-report" 
                                                    data-id="{{ $report->id }}" title="Bỏ qua">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <button type="button" class="btn btn-info btn-sm view-report" 
                                                    data-id="{{ $report->id }}" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @else
                                        <button type="button" class="btn btn-info btn-sm view-report" 
                                                data-id="{{ $report->id }}" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Hiển thị {{ $reports->firstItem() ?? 0 }} đến {{ $reports->lastItem() ?? 0 }} 
                            trong tổng số {{ $reports->total() }} báo cáo
                        </div>
                        {{ $reports->links() }}
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-flag fa-3x text-muted mb-3"></i>
                        <h5>Không có báo cáo vi phạm nào</h5>
                        <p class="text-muted">Chưa có báo cáo vi phạm nào được gửi.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Detail Modal -->
<div class="modal fade" id="reportDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi Tiết Báo Cáo Vi Phạm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="reportDetailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success" id="modalResolveBtn">Giải quyết</button>
                <button type="button" class="btn btn-warning" id="modalDismissBtn">Bỏ qua</button>
            </div>
        </div>
    </div>
</div>

<!-- Resolution Modal -->
<div class="modal fade" id="resolutionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resolutionModalTitle">Giải Quyết Báo Cáo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="resolutionForm">
                    <div class="mb-3">
                        <label for="resolutionNote" class="form-label">Ghi Chú Giải Quyết</label>
                        <textarea class="form-control" id="resolutionNote" name="resolution_note" rows="3" 
                                  placeholder="Nhập ghi chú về cách giải quyết báo cáo này..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmResolution">Xác Nhận</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Select all checkbox
    $('#select-all').change(function() {
        $('.report-checkbox').prop('checked', $(this).prop('checked'));
    });

    // Individual checkbox change
    $('.report-checkbox').change(function() {
        if (!$(this).prop('checked')) {
            $('#select-all').prop('checked', false);
        } else if ($('.report-checkbox:checked').length === $('.report-checkbox').length) {
            $('#select-all').prop('checked', true);
        }
    });

    // Bulk actions
    $('#bulk-resolve').click(function() {
        performBulkAction('resolve', 'Giải quyết các báo cáo đã chọn?');
    });

    $('#bulk-dismiss').click(function() {
        performBulkAction('dismiss', 'Bỏ qua các báo cáo đã chọn?');
    });

    function performBulkAction(action, confirmMessage) {
        const selectedIds = $('.report-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            alert('Vui lòng chọn ít nhất một báo cáo.');
            return;
        }

        if (confirm(confirmMessage)) {
            $.ajax({
                url: '{{ route("admin.moderation.reports.bulk-action") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    report_ids: selectedIds,
                    action: action
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                }
            });
        }
    }

    // Individual actions
    $('.resolve-report').click(function() {
        const reportId = $(this).data('id');
        showResolutionModal(reportId, 'resolve');
    });

    $('.dismiss-report').click(function() {
        const reportId = $(this).data('id');
        showResolutionModal(reportId, 'dismiss');
    });

    function showResolutionModal(reportId, action) {
        $('#resolutionModalTitle').text(action === 'resolve' ? 'Giải Quyết Báo Cáo' : 'Bỏ Qua Báo Cáo');
        $('#resolutionModal').modal('show');
        
        $('#confirmResolution').off('click').on('click', function() {
            const note = $('#resolutionNote').val();
            const url = action === 'resolve' 
                ? '{{ route("admin.moderation.reports.resolve", ":id") }}'.replace(':id', reportId)
                : '{{ route("admin.moderation.reports.dismiss", ":id") }}'.replace(':id', reportId);

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    resolution_note: note
                },
                success: function(response) {
                    if (response.success) {
                        $('#resolutionModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                }
            });
        });
    }
});
</script>
@endsection
