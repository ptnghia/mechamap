@extends('admin.layouts.app')

@section('title', 'Chuẩn hóa dữ liệu Users')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">🔧 Chuẩn hóa dữ liệu Users</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active">Chuẩn hóa dữ liệu</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <div id="alert-container"></div>

    <!-- Statistics Overview -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng số Users</p>
                            <h4 class="mb-0">{{ number_format($stats['total_users']) }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="bx bx-user font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Có Avatar</p>
                            <h4 class="mb-0">{{ number_format($stats['users_with_avatar']) }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-success fw-bold font-size-12">
                                    {{ round(($stats['users_with_avatar'] / $stats['total_users']) * 100, 1) }}%
                                </span>
                            </p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="bx bx-image font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Thiếu Avatar</p>
                            <h4 class="mb-0">{{ number_format($stats['users_without_avatar']) }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-danger fw-bold font-size-12">
                                    {{ round(($stats['users_without_avatar'] / $stats['total_users']) * 100, 1) }}%
                                </span>
                            </p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="bx bx-image-alt font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Thiếu thông tin</p>
                            <h4 class="mb-0">{{ number_format($stats['users_missing_info']) }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-danger fw-bold font-size-12">
                                    {{ round(($stats['users_missing_info'] / $stats['total_users']) * 100, 1) }}%
                                </span>
                            </p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-danger">
                                <span class="avatar-title">
                                    <i class="bx bx-info-circle font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Distribution -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">📊 Phân bố theo Role</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Số lượng</th>
                                    <th>Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['role_distribution'] as $role)
                                <tr>
                                    <td>
                                        <span class="badge badge-soft-primary">{{ $role->role }}</span>
                                    </td>
                                    <td>{{ number_format($role->count) }}</td>
                                    <td>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-primary" 
                                                 style="width: {{ round(($role->count / $stats['total_users']) * 100, 1) }}%">
                                            </div>
                                        </div>
                                        <span class="font-size-12 text-muted">
                                            {{ round(($role->count / $stats['total_users']) * 100, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">📈 Phân bố theo Status</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Số lượng</th>
                                    <th>Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['status_distribution'] as $status)
                                <tr>
                                    <td>
                                        <span class="badge badge-soft-{{ $status->status == 'active' ? 'success' : 'warning' }}">
                                            {{ $status->status }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($status->count) }}</td>
                                    <td>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-{{ $status->status == 'active' ? 'success' : 'warning' }}" 
                                                 style="width: {{ round(($status->count / $stats['total_users']) * 100, 1) }}%">
                                            </div>
                                        </div>
                                        <span class="font-size-12 text-muted">
                                            {{ round(($status->count / $stats['total_users']) * 100, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Standardization Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">🔧 Thực hiện chuẩn hóa dữ liệu</h4>
                </div>
                <div class="card-body">
                    <!-- Warning Alert -->
                    <div class="alert alert-warning" role="alert">
                        <h4 class="alert-heading">⚠️ Cảnh báo quan trọng!</h4>
                        <p>Việc chuẩn hóa dữ liệu sẽ thay đổi thông tin của tất cả users. Vui lòng:</p>
                        <ul class="mb-0">
                            <li>Tạo backup trước khi thực hiện</li>
                            <li>Kiểm tra tính toàn vẹn dữ liệu</li>
                            <li>Chỉ thực hiện khi cần thiết</li>
                            <li>Đảm bảo không có users đang online</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <button type="button" class="btn btn-info btn-lg w-100" id="btn-backup">
                                    <i class="bx bx-download me-2"></i>
                                    Tạo Backup dữ liệu
                                </button>
                                <small class="text-muted">Tạo bản sao lưu trước khi chuẩn hóa</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <button type="button" class="btn btn-warning btn-lg w-100" id="btn-check-integrity">
                                    <i class="bx bx-check-shield me-2"></i>
                                    Kiểm tra tính toàn vẹn
                                </button>
                                <small class="text-muted">Kiểm tra foreign key và duplicate data</small>
                            </div>
                        </div>
                    </div>

                    <!-- Standardization Form -->
                    <form id="standardization-form" class="mt-4">
                        <div class="row">
                            <div class="col-12">
                                <h5>Chọn nhóm users cần chuẩn hóa:</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" value="system_management" 
                                                   id="group_system_management" name="groups[]">
                                            <label class="form-check-label" for="group_system_management">
                                                <strong>🏛️ System Management</strong>
                                                <br><small class="text-muted">Super Admin, System Admin, Content Admin</small>
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" value="community_management" 
                                                   id="group_community_management" name="groups[]">
                                            <label class="form-check-label" for="group_community_management">
                                                <strong>👥 Community Management</strong>
                                                <br><small class="text-muted">Content Moderator, Marketplace Moderator, Community Moderator</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" value="community_members" 
                                                   id="group_community_members" name="groups[]">
                                            <label class="form-check-label" for="group_community_members">
                                                <strong>🧑‍🤝‍🧑 Community Members</strong>
                                                <br><small class="text-muted">Senior Member, Member, Guest</small>
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" value="business_partners" 
                                                   id="group_business_partners" name="groups[]">
                                            <label class="form-check-label" for="group_business_partners">
                                                <strong>🤝 Business Partners</strong>
                                                <br><small class="text-muted">Verified Partner, Manufacturer, Supplier, Brand</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirm_backup" name="confirm_backup">
                                    <label class="form-check-label" for="confirm_backup">
                                        <strong>Tôi xác nhận đã tạo backup và hiểu rủi ro khi chuẩn hóa dữ liệu</strong>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger btn-lg" id="btn-standardize">
                                    <i class="bx bx-cog me-2"></i>
                                    Thực hiện chuẩn hóa dữ liệu
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Modal -->
    <div class="modal fade" id="progressModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Đang xử lý...</h5>
                </div>
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3" id="progress-text">Vui lòng đợi...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Backup button
    $('#btn-backup').click(function() {
        showProgress('Đang tạo backup...');
        
        $.post('{{ route("admin.users.standardization.backup") }}')
            .done(function(response) {
                hideProgress();
                if (response.success) {
                    showAlert('success', 'Backup thành công!', response.message);
                } else {
                    showAlert('danger', 'Lỗi!', response.message);
                }
            })
            .fail(function(xhr) {
                hideProgress();
                showAlert('danger', 'Lỗi!', 'Không thể tạo backup: ' + xhr.responseJSON?.message);
            });
    });

    // Check integrity button
    $('#btn-check-integrity').click(function() {
        showProgress('Đang kiểm tra tính toàn vẹn...');
        
        $.post('{{ route("admin.users.standardization.check-integrity") }}')
            .done(function(response) {
                hideProgress();
                if (response.success) {
                    let message = 'Kiểm tra hoàn tất!';
                    if (response.issues.length > 0) {
                        message += '<br>Các vấn đề phát hiện:<br>' + response.issues.join('<br>');
                        showAlert('warning', 'Cảnh báo!', message);
                    } else {
                        showAlert('success', 'Thành công!', 'Dữ liệu không có vấn đề gì.');
                    }
                } else {
                    showAlert('danger', 'Lỗi!', response.message);
                }
            })
            .fail(function(xhr) {
                hideProgress();
                showAlert('danger', 'Lỗi!', 'Không thể kiểm tra: ' + xhr.responseJSON?.message);
            });
    });

    // Standardization form
    $('#standardization-form').submit(function(e) {
        e.preventDefault();
        
        if (!$('#confirm_backup').is(':checked')) {
            showAlert('warning', 'Cảnh báo!', 'Vui lòng xác nhận đã tạo backup trước khi tiếp tục.');
            return;
        }

        const selectedGroups = $('input[name="groups[]"]:checked').map(function() {
            return this.value;
        }).get();

        if (selectedGroups.length === 0) {
            showAlert('warning', 'Cảnh báo!', 'Vui lòng chọn ít nhất một nhóm để chuẩn hóa.');
            return;
        }

        if (!confirm('Bạn có chắc chắn muốn chuẩn hóa dữ liệu? Hành động này không thể hoàn tác!')) {
            return;
        }

        showProgress('Đang chuẩn hóa dữ liệu...');
        
        $.post('{{ route("admin.users.standardization.standardize") }}', {
            confirm_backup: true,
            groups: selectedGroups,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            hideProgress();
            if (response.success) {
                let message = 'Chuẩn hóa thành công!<br>';
                Object.keys(response.results).forEach(function(group) {
                    message += `${group}: ${response.results[group].updated_count} users<br>`;
                });
                showAlert('success', 'Thành công!', message);
                
                // Reload page after 3 seconds
                setTimeout(function() {
                    location.reload();
                }, 3000);
            } else {
                showAlert('danger', 'Lỗi!', response.message);
            }
        })
        .fail(function(xhr) {
            hideProgress();
            showAlert('danger', 'Lỗi!', 'Không thể chuẩn hóa: ' + xhr.responseJSON?.message);
        });
    });

    function showProgress(text) {
        $('#progress-text').text(text);
        $('#progressModal').modal('show');
    }

    function hideProgress() {
        $('#progressModal').modal('hide');
    }

    function showAlert(type, title, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <strong>${title}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#alert-container').html(alertHtml);
        
        // Auto dismiss after 10 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 10000);
    }
});
</script>
@endpush
