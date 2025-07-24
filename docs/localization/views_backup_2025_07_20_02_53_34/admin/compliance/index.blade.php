@extends('admin.layouts.dason')

@section('title', 'Bảo Mật & Tuân Thủ')

@section('css')
<link href="{{ asset('css/admin/verification.css') }}" rel="stylesheet" type="text/css" />
<style>
.compliance-card {
    border-left: 4px solid #28a745;
    transition: all 0.3s ease;
}
.compliance-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.security-alert {
    border-left: 4px solid #dc3545;
    background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
}
.compliance-score {
    font-size: 2rem;
    font-weight: bold;
}
.score-excellent { color: #28a745; }
.score-good { color: #17a2b8; }
.score-warning { color: #ffc107; }
.score-danger { color: #dc3545; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Bảo Mật & Tuân Thủ</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Bảo Mật & Tuân Thủ</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Compliance Overview Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card compliance-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Điểm Tuân Thủ Tổng Thể</p>
                            <h4 class="mb-2">
                                <span class="compliance-score score-{{ $complianceMetrics['compliance_score'] >= 90 ? 'excellent' : ($complianceMetrics['compliance_score'] >= 75 ? 'good' : ($complianceMetrics['compliance_score'] >= 60 ? 'warning' : 'danger')) }}">
                                    {{ number_format($complianceMetrics['compliance_score'], 1) }}%
                                </span>
                            </h4>
                            <p class="text-muted mb-0">
                                <span class="text-success fw-bold font-size-12 me-2">
                                    <i class="ri-arrow-right-up-line me-1 align-middle"></i>
                                    {{ $complianceMetrics['compliance_score'] >= 80 ? 'Tuân thủ' : 'Cần cải thiện' }}
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="ri-shield-check-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card compliance-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Tỷ Lệ Mã Hóa Dữ Liệu</p>
                            <h4 class="mb-2">{{ number_format($complianceMetrics['data_encryption_rate'], 1) }}%</h4>
                            <p class="text-muted mb-0">
                                <span class="text-info fw-bold font-size-12 me-2">
                                    <i class="ri-lock-line me-1 align-middle"></i>
                                    Dữ liệu nhạy cảm được bảo vệ
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="ri-lock-2-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card {{ $securitySummary['security_incidents'] > 0 ? 'security-alert' : 'compliance-card' }}">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Sự Cố Bảo Mật (7 ngày)</p>
                            <h4 class="mb-2">{{ $securitySummary['total_incidents'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">
                                <span class="{{ $securitySummary['total_incidents'] > 0 ? 'text-danger' : 'text-success' }} fw-bold font-size-12 me-2">
                                    <i class="ri-{{ $securitySummary['total_incidents'] > 0 ? 'alert' : 'check' }}-line me-1 align-middle"></i>
                                    {{ $securitySummary['total_incidents'] > 0 ? 'Cần xem xét' : 'An toàn' }}
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-{{ $securitySummary['total_incidents'] > 0 ? 'danger' : 'success' }} rounded-3">
                                <i class="ri-{{ $securitySummary['total_incidents'] > 0 ? 'alarm-warning' : 'shield-check' }}-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card compliance-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Phạm Vi Nhật Ký Kiểm Toán</p>
                            <h4 class="mb-2">{{ number_format($complianceMetrics['audit_trail_coverage'], 1) }}%</h4>
                            <p class="text-muted mb-0">
                                <span class="text-primary fw-bold font-size-12 me-2">
                                    <i class="ri-history-line me-1 align-middle"></i>
                                    Hoạt động được theo dõi
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="ri-file-list-3-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#overview" role="tab">
                                <i class="ri-dashboard-line me-2"></i>Tổng Quan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#audit" role="tab">
                                <i class="ri-history-line me-2"></i>Nhật Ký Kiểm Toán
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#security" role="tab">
                                <i class="ri-eye-line me-2"></i>Giám Sát Bảo Mật
                                @if(($securitySummary['total_incidents'] ?? 0) > 0)
                                    <span class="badge bg-danger ms-1">{{ $securitySummary['total_incidents'] }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#privacy" role="tab">
                                <i class="ri-user-shield-line me-2"></i>Quyền Riêng Tư
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#reports" role="tab">
                                <i class="ri-file-download-line me-2"></i>Báo Cáo & Xuất Dữ Liệu
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Overview Tab -->
                        <div class="tab-pane active" id="overview" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Thống Kê Xử Lý Đơn</h5>
                                    <div class="table-responsive">
                                        <table class="table table-nowrap">
                                            <tbody>
                                                <tr>
                                                    <td>Tổng số đơn xử lý</td>
                                                    <td><span class="badge bg-primary">{{ $complianceMetrics['total_applications'] }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Đơn đã xử lý</td>
                                                    <td><span class="badge bg-success">{{ $complianceMetrics['processed_applications'] }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Thời gian xử lý trung bình</td>
                                                    <td><span class="badge bg-info">{{ number_format($complianceMetrics['average_processing_time'], 1) }} giờ</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Tỷ lệ sự cố bảo mật</td>
                                                    <td><span class="badge bg-{{ $complianceMetrics['security_incident_rate'] > 5 ? 'danger' : 'success' }}">{{ number_format($complianceMetrics['security_incident_rate'], 2) }}%</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Tuân Thủ Dữ Liệu</h5>
                                    <div class="table-responsive">
                                        <table class="table table-nowrap">
                                            <tbody>
                                                <tr>
                                                    <td>Lưu trữ dữ liệu tuân thủ</td>
                                                    <td><span class="badge bg-{{ $retentionReport['compliance_status'] === 'compliant' ? 'success' : 'warning' }}">{{ $retentionReport['compliance_status'] === 'compliant' ? 'Tuân thủ' : 'Cần xem xét' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Bản ghi được mã hóa</td>
                                                    <td><span class="badge bg-info">{{ $retentionReport['encrypted_records'] }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Bản ghi được ẩn danh</td>
                                                    <td><span class="badge bg-secondary">{{ $retentionReport['anonymized_records'] }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Dọn dẹp tiếp theo</td>
                                                    <td><span class="badge bg-warning">{{ $retentionReport['next_cleanup_date'] }}</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Audit Trail Tab -->
                        <div class="tab-pane" id="audit" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Nhật Ký Kiểm Toán</h5>
                                <button type="button" class="btn btn-primary" onclick="generateAuditReport()">
                                    <i class="ri-download-line me-1"></i> Tạo Báo Cáo
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h4 class="text-primary">{{ $auditSummary['total_activities'] ?? 0 }}</h4>
                                            <p class="text-muted mb-0">Tổng Hoạt Động</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h4 class="text-warning">{{ $auditSummary['high_risk_activities'] ?? 0 }}</h4>
                                            <p class="text-muted mb-0">Hoạt Động Rủi Ro Cao</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h4 class="text-danger">{{ $auditSummary['security_incidents'] ?? 0 }}</h4>
                                            <p class="text-muted mb-0">Sự Cố Bảo Mật</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h4 class="text-success">{{ $auditSummary['compliance_metrics']['compliance_score'] ?? 0 }}%</h4>
                                            <p class="text-muted mb-0">Điểm Tuân Thủ</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Monitoring Tab -->
                        <div class="tab-pane" id="security" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Giám Sát Bảo Mật</h5>
                                <button type="button" class="btn btn-danger" onclick="generateSecurityReport()">
                                    <i class="ri-shield-line me-1"></i> Báo Cáo Bảo Mật
                                </button>
                            </div>
                            @if(($securitySummary['total_incidents'] ?? 0) > 0)
                                <div class="alert alert-danger" role="alert">
                                    <h4 class="alert-heading">Cảnh Báo Bảo Mật!</h4>
                                    <p>Đã phát hiện {{ $securitySummary['total_incidents'] }} sự cố bảo mật trong 7 ngày qua. Vui lòng xem xét ngay.</p>
                                    <hr>
                                    <p class="mb-0">Điểm bảo mật hiện tại: <strong>{{ $securitySummary['security_score'] ?? 0 }}%</strong></p>
                                </div>
                            @else
                                <div class="alert alert-success" role="alert">
                                    <h4 class="alert-heading">Hệ Thống An Toàn!</h4>
                                    <p>Không có sự cố bảo mật nào được phát hiện trong 7 ngày qua.</p>
                                    <p class="mb-0">Điểm bảo mật hiện tại: <strong>{{ $securitySummary['security_score'] ?? 100 }}%</strong></p>
                                </div>
                            @endif
                        </div>

                        <!-- Privacy Tab -->
                        <div class="tab-pane" id="privacy" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Quyền Riêng Tư Dữ Liệu</h5>
                                <button type="button" class="btn btn-info" onclick="generatePrivacyReport()">
                                    <i class="ri-user-shield-line me-1"></i> Báo Cáo Quyền Riêng Tư
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Chính Sách Lưu Trữ Dữ Liệu</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li><i class="ri-check-line text-success me-2"></i>Dữ liệu xác thực doanh nghiệp: 7 năm</li>
                                                <li><i class="ri-check-line text-success me-2"></i>Dữ liệu cá nhân: 5 năm</li>
                                                <li><i class="ri-check-line text-success me-2"></i>Dữ liệu tài chính: 10 năm</li>
                                                <li><i class="ri-check-line text-success me-2"></i>Nhật ký kiểm toán: 3 năm</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Tuân Thủ GDPR/CCPA</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li><i class="ri-check-line text-success me-2"></i>Mã hóa dữ liệu nhạy cảm</li>
                                                <li><i class="ri-check-line text-success me-2"></i>Quyền truy cập và xóa dữ liệu</li>
                                                <li><i class="ri-check-line text-success me-2"></i>Thông báo vi phạm dữ liệu</li>
                                                <li><i class="ri-check-line text-success me-2"></i>Đánh giá tác động bảo vệ dữ liệu</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reports Tab -->
                        <div class="tab-pane" id="reports" role="tabpanel">
                            <h5>Báo Cáo & Xuất Dữ Liệu</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="ri-file-download-line font-size-48 text-primary mb-3"></i>
                                            <h6>Xuất Nhật Ký Kiểm Toán</h6>
                                            <p class="text-muted">Xuất toàn bộ nhật ký kiểm toán theo khoảng thời gian</p>
                                            <button class="btn btn-primary" onclick="exportData('audit_trail')">Xuất Dữ Liệu</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="ri-shield-line font-size-48 text-danger mb-3"></i>
                                            <h6>Xuất Sự Cố Bảo Mật</h6>
                                            <p class="text-muted">Xuất danh sách các sự cố bảo mật đã phát hiện</p>
                                            <button class="btn btn-danger" onclick="exportData('security_incidents')">Xuất Dữ Liệu</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="ri-user-shield-line font-size-48 text-info mb-3"></i>
                                            <h6>Xuất Dữ Liệu Quyền Riêng Tư</h6>
                                            <p class="text-muted">Xuất báo cáo tuân thủ quyền riêng tư dữ liệu</p>
                                            <button class="btn btn-info" onclick="exportData('privacy_data')">Xuất Dữ Liệu</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// Compliance Dashboard JavaScript
function generateAuditReport() {
    const dateFrom = prompt('Từ ngày (YYYY-MM-DD):', '{{ now()->subDays(30)->toDateString() }}');
    const dateTo = prompt('Đến ngày (YYYY-MM-DD):', '{{ now()->toDateString() }}');
    
    if (dateFrom && dateTo) {
        fetch('{{ route("admin.compliance.audit-report") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                date_from: dateFrom,
                date_to: dateTo,
                format: 'json'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Audit Report:', data.report);
                alert('Báo cáo đã được tạo thành công!');
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tạo báo cáo');
        });
    }
}

function generateSecurityReport() {
    fetch('{{ route("admin.compliance.security-report") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            date_from: '{{ now()->subDays(7)->toDateString() }}',
            date_to: '{{ now()->toDateString() }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Security Report:', data.report);
            alert('Báo cáo bảo mật đã được tạo thành công!');
        } else {
            alert('Lỗi: ' + data.message);
        }
    });
}

function generatePrivacyReport() {
    fetch('{{ route("admin.compliance.privacy-report") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            date_from: '{{ now()->subDays(30)->toDateString() }}',
            date_to: '{{ now()->toDateString() }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Privacy Report:', data.report);
            alert('Báo cáo quyền riêng tư đã được tạo thành công!');
        } else {
            alert('Lỗi: ' + data.message);
        }
    });
}

function exportData(exportType) {
    const format = prompt('Chọn định dạng xuất (json/csv/xml):', 'json');
    if (!format) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.compliance.export-compliance-data") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfToken);
    
    const exportTypeInput = document.createElement('input');
    exportTypeInput.type = 'hidden';
    exportTypeInput.name = 'export_type';
    exportTypeInput.value = exportType;
    form.appendChild(exportTypeInput);
    
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    form.appendChild(formatInput);
    
    const dateFromInput = document.createElement('input');
    dateFromInput.type = 'hidden';
    dateFromInput.name = 'date_from';
    dateFromInput.value = '{{ now()->subDays(30)->toDateString() }}';
    form.appendChild(dateFromInput);
    
    const dateToInput = document.createElement('input');
    dateToInput.type = 'hidden';
    dateToInput.name = 'date_to';
    dateToInput.value = '{{ now()->toDateString() }}';
    form.appendChild(dateToInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Auto-refresh security status every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>
@endsection
