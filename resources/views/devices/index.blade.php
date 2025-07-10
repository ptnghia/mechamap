@extends('layouts.app')

@section('title', 'Quản lý thiết bị')

@section('content')
<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-shield-alt me-2 text-primary"></i>
                        Quản lý thiết bị
                    </h1>
                    <p class="text-muted mb-0">Xem và quản lý các thiết bị đã đăng nhập vào tài khoản của bạn</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-warning" id="cleanOldDevicesBtn">
                        <i class="fas fa-broom me-1"></i>
                        Dọn dẹp thiết bị cũ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-devices fs-4 text-primary"></i>
                        </div>
                    </div>
                    <h3 class="mb-1">{{ $stats['total'] }}</h3>
                    <p class="text-muted mb-0">Tổng thiết bị</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-shield-check fs-4 text-success"></i>
                        </div>
                    </div>
                    <h3 class="mb-1">{{ $stats['trusted'] }}</h3>
                    <p class="text-muted mb-0">Tin cậy</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-exclamation-triangle fs-4 text-warning"></i>
                        </div>
                    </div>
                    <h3 class="mb-1">{{ $stats['untrusted'] }}</h3>
                    <p class="text-muted mb-0">Chưa tin cậy</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock fs-4 text-info"></i>
                        </div>
                    </div>
                    <h3 class="mb-1">{{ $stats['active_last_30_days'] }}</h3>
                    <p class="text-muted mb-0">Hoạt động 30 ngày</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Devices List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Danh sách thiết bị
                @if($devices->count() > 0)
                    <span class="badge bg-primary ms-2">{{ $devices->count() }}</span>
                @endif
            </h5>
        </div>
        
        <div class="card-body p-0">
            @if($devices->count() > 0)
                <div class="device-list">
                    @foreach($devices as $device)
                        @php
                            $isCurrentDevice = $device->device_fingerprint === $currentFingerprint;
                            $deviceIcon = match($device->device_type) {
                                'mobile' => 'fas fa-mobile-alt',
                                'tablet' => 'fas fa-tablet-alt',
                                'desktop' => 'fas fa-desktop',
                                default => 'fas fa-laptop'
                            };
                        @endphp
                        
                        <div class="device-item border-bottom p-3 {{ $isCurrentDevice ? 'bg-light' : '' }}" 
                             data-device-id="{{ $device->id }}">
                            <div class="d-flex">
                                <!-- Device Icon -->
                                <div class="device-icon me-3">
                                    <div class="bg-{{ $device->is_trusted ? 'success' : 'warning' }} bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="{{ $deviceIcon }} text-{{ $device->is_trusted ? 'success' : 'warning' }} fs-5"></i>
                                    </div>
                                </div>
                                
                                <!-- Device Info -->
                                <div class="device-info flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="device-name mb-1 d-flex align-items-center">
                                                {{ $device->device_name }}
                                                @if($isCurrentDevice)
                                                    <span class="badge bg-primary ms-2">Thiết bị hiện tại</span>
                                                @endif
                                                @if($device->is_trusted)
                                                    <span class="badge bg-success ms-2">Tin cậy</span>
                                                @endif
                                            </h6>
                                            <div class="device-details text-muted small">
                                                <div class="mb-1">
                                                    <i class="fas fa-globe me-1"></i>
                                                    {{ $device->browser }} trên {{ $device->platform }}
                                                </div>
                                                <div class="mb-1">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $device->city }}, {{ $device->country }}
                                                    <span class="text-muted">({{ $device->ip_address }})</span>
                                                </div>
                                                <div>
                                                    <i class="fas fa-clock me-1"></i>
                                                    Lần cuối: {{ $device->last_seen_at->diffForHumans() }}
                                                    <span class="text-muted">({{ $device->last_seen_at->format('d/m/Y H:i') }})</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Device Actions -->
                                        <div class="device-actions d-flex gap-1">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-info view-details-btn"
                                                    data-device-id="{{ $device->id }}"
                                                    title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            @if($device->is_trusted)
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-warning untrust-btn"
                                                        data-device-id="{{ $device->id }}"
                                                        title="Bỏ tin cậy">
                                                    <i class="fas fa-shield-alt"></i>
                                                </button>
                                            @else
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success trust-btn"
                                                        data-device-id="{{ $device->id }}"
                                                        title="Đánh dấu tin cậy">
                                                    <i class="fas fa-shield-check"></i>
                                                </button>
                                            @endif
                                            
                                            @if(!$isCurrentDevice)
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger remove-btn"
                                                        data-device-id="{{ $device->id }}"
                                                        title="Xóa thiết bị">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Security Info -->
                                    <div class="security-info">
                                        <small class="text-muted">
                                            <i class="fas fa-fingerprint me-1"></i>
                                            Đăng ký lần đầu: {{ $device->first_seen_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-devices text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="text-muted mb-2">Không có thiết bị nào</h5>
                    <p class="text-muted mb-4">
                        Danh sách thiết bị sẽ xuất hiện khi bạn đăng nhập từ các thiết bị khác nhau.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Device Details Modal -->
<div class="modal fade" id="deviceDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Chi tiết thiết bị
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="deviceDetailsContent">
                    <div class="text-center py-3">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trust device
    document.querySelectorAll('.trust-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const deviceId = this.dataset.deviceId;
            
            fetch(`/ajax/devices/${deviceId}/trust`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    location.reload();
                } else {
                    showToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Có lỗi xảy ra', 'error');
            });
        });
    });
    
    // Untrust device
    document.querySelectorAll('.untrust-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const deviceId = this.dataset.deviceId;
            
            if (confirm('Bỏ đánh dấu tin cậy thiết bị này?')) {
                fetch(`/ajax/devices/${deviceId}/untrust`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        location.reload();
                    } else {
                        showToast(data.message || 'Có lỗi xảy ra', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Có lỗi xảy ra', 'error');
                });
            }
        });
    });
    
    // Remove device
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const deviceId = this.dataset.deviceId;
            
            if (confirm('Xóa thiết bị này khỏi danh sách? Bạn sẽ cần đăng nhập lại từ thiết bị đó.')) {
                fetch(`/ajax/devices/${deviceId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        location.reload();
                    } else {
                        showToast(data.message || 'Có lỗi xảy ra', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Có lỗi xảy ra', 'error');
                });
            }
        });
    });
    
    // View device details
    document.querySelectorAll('.view-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const deviceId = this.dataset.deviceId;
            const modal = new bootstrap.Modal(document.getElementById('deviceDetailsModal'));
            
            // Show modal with loading
            modal.show();
            
            fetch(`/ajax/devices/${deviceId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const device = data.device;
                    document.getElementById('deviceDetailsContent').innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Thông tin thiết bị</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Tên:</strong> ${device.device_name}</li>
                                    <li><strong>Loại:</strong> ${device.device_type}</li>
                                    <li><strong>Trình duyệt:</strong> ${device.browser}</li>
                                    <li><strong>Hệ điều hành:</strong> ${device.platform}</li>
                                    <li><strong>Trạng thái:</strong> 
                                        <span class="badge bg-${device.is_trusted ? 'success' : 'warning'}">
                                            ${device.is_trusted ? 'Tin cậy' : 'Chưa tin cậy'}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Thông tin kết nối</h6>
                                <ul class="list-unstyled">
                                    <li><strong>IP:</strong> ${device.ip_address}</li>
                                    <li><strong>Quốc gia:</strong> ${device.country || 'Không xác định'}</li>
                                    <li><strong>Thành phố:</strong> ${device.city || 'Không xác định'}</li>
                                    <li><strong>Lần đầu:</strong> ${device.first_seen_at}</li>
                                    <li><strong>Lần cuối:</strong> ${device.last_seen_at} (${device.last_seen_human})</li>
                                </ul>
                            </div>
                        </div>
                    `;
                } else {
                    document.getElementById('deviceDetailsContent').innerHTML = `
                        <div class="alert alert-danger">
                            ${data.message || 'Không thể tải thông tin thiết bị'}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('deviceDetailsContent').innerHTML = `
                    <div class="alert alert-danger">
                        Có lỗi xảy ra khi tải thông tin thiết bị
                    </div>
                `;
            });
        });
    });
    
    // Clean old devices
    document.getElementById('cleanOldDevicesBtn')?.addEventListener('click', function() {
        if (confirm('Xóa tất cả thiết bị chưa tin cậy và không hoạt động trong 90 ngày qua?')) {
            fetch('/ajax/devices/clean-old', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ days: 90 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    if (data.deleted_count > 0) {
                        location.reload();
                    }
                } else {
                    showToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Có lỗi xảy ra', 'error');
            });
        }
    });
});

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}
</script>
@endpush

@push('styles')
<style>
.device-item {
    transition: all 0.3s ease;
}

.device-item:hover {
    background-color: #f8f9fa !important;
}

.device-item .device-actions {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.device-item:hover .device-actions {
    opacity: 1;
}

.device-icon {
    flex-shrink: 0;
}

.device-info {
    min-width: 0;
}

.device-name {
    word-break: break-word;
}

.device-details {
    line-height: 1.4;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .device-item .device-actions {
        opacity: 1;
    }
    
    .device-details {
        font-size: 0.8rem;
    }
}
</style>
@endpush
