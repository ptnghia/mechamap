@extends('layouts.app')

@section('title', 'Cài đặt thông báo')

@section('content')
<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-cog me-2 text-primary"></i>
                        Cài đặt thông báo
                    </h1>
                    <p class="text-muted mb-0">Tùy chỉnh cách bạn nhận thông báo từ MechaMap</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" id="resetPreferencesBtn">
                        <i class="fas fa-undo me-1"></i>
                        Khôi phục mặc định
                    </button>
                    <button type="button" class="btn btn-primary" id="savePreferencesBtn">
                        <i class="fas fa-save me-1"></i>
                        Lưu cài đặt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="preferencesForm">
        @csrf
        
        <!-- Global Settings -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-globe me-2"></i>
                    Cài đặt chung
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="emailNotificationsEnabled" 
                                   name="email_notifications_enabled"
                                   {{ auth()->user()->email_notifications_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="emailNotificationsEnabled">
                                <strong>Bật thông báo email</strong>
                                <div class="text-muted small">Nhận thông báo qua email</div>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Lưu ý:</strong> Thông báo bảo mật sẽ luôn được gửi qua email để đảm bảo an toàn tài khoản.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Categories -->
        @foreach($notificationTypes as $categoryKey => $category)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="{{ $category['icon'] }} me-2"></i>
                        {{ $category['label'] }}
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($category['types'] as $type)
                        @php
                            $typePrefs = $preferences[$type] ?? [];
                            $isSecurityType = in_array($type, ['login_from_new_device', 'password_changed']);
                        @endphp
                        
                        <div class="notification-type-item border-bottom pb-3 mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="form-check form-switch me-3">
                                            <input class="form-check-input notification-toggle" 
                                                   type="checkbox" 
                                                   id="enabled_{{ $type }}"
                                                   name="preferences[{{ $type }}][enabled]"
                                                   {{ ($typePrefs['enabled'] ?? true) ? 'checked' : '' }}
                                                   {{ $isSecurityType ? 'disabled' : '' }}>
                                        </div>
                                        <div>
                                            <label class="form-check-label fw-medium" for="enabled_{{ $type }}">
                                                {{ $typePrefs['label'] ?? ucfirst(str_replace('_', ' ', $type)) }}
                                                @if($isSecurityType)
                                                    <span class="badge bg-warning ms-2">Bắt buộc</span>
                                                @endif
                                            </label>
                                            <div class="text-muted small">
                                                {{ $typePrefs['description'] ?? 'Mô tả thông báo' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-3 justify-content-end">
                                        <!-- Email Option -->
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="email_{{ $type }}"
                                                   name="preferences[{{ $type }}][email]"
                                                   {{ ($typePrefs['email'] ?? false) ? 'checked' : '' }}
                                                   {{ $isSecurityType ? 'checked disabled' : '' }}>
                                            <label class="form-check-label small" for="email_{{ $type }}">
                                                <i class="fas fa-envelope me-1"></i>
                                                Email
                                            </label>
                                        </div>
                                        
                                        <!-- Push Option -->
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="push_{{ $type }}"
                                                   name="preferences[{{ $type }}][push]"
                                                   {{ ($typePrefs['push'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="push_{{ $type }}">
                                                <i class="fas fa-bell me-1"></i>
                                                Trình duyệt
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Additional Settings -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-sliders-h me-2"></i>
                    Cài đặt nâng cao
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="digestFrequency" class="form-label">Tần suất email tổng hợp</label>
                            <select class="form-select" id="digestFrequency" name="digest_frequency">
                                <option value="daily">Hàng ngày</option>
                                <option value="weekly" selected>Hàng tuần</option>
                                <option value="monthly">Hàng tháng</option>
                                <option value="never">Không bao giờ</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="quietHours" class="form-label">Giờ im lặng</label>
                            <div class="d-flex gap-2">
                                <input type="time" class="form-control" id="quietHoursStart" name="quiet_hours_start" value="22:00">
                                <span class="align-self-center">đến</span>
                                <input type="time" class="form-control" id="quietHoursEnd" name="quiet_hours_end" value="08:00">
                            </div>
                            <div class="form-text">Không gửi thông báo trong khoảng thời gian này</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="groupSimilar" name="group_similar" checked>
                            <label class="form-check-label" for="groupSimilar">
                                Nhóm các thông báo tương tự
                            </label>
                            <div class="form-text">Gộp nhiều thông báo cùng loại thành một</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="smartTiming" name="smart_timing">
                            <label class="form-check-label" for="smartTiming">
                                Thời gian thông báo thông minh
                            </label>
                            <div class="form-text">Tự động chọn thời điểm tốt nhất để gửi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Thao tác nhanh
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-success w-100 quick-action-btn" data-action="enable-all">
                            <i class="fas fa-check-circle me-1"></i>
                            Bật tất cả
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-danger w-100 quick-action-btn" data-action="disable-all">
                            <i class="fas fa-times-circle me-1"></i>
                            Tắt tất cả
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-primary w-100 quick-action-btn" data-action="email-only">
                            <i class="fas fa-envelope me-1"></i>
                            Chỉ email
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-info w-100 quick-action-btn" data-action="push-only">
                            <i class="fas fa-bell me-1"></i>
                            Chỉ trình duyệt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Save preferences
    document.getElementById('savePreferencesBtn').addEventListener('click', function() {
        const form = document.getElementById('preferencesForm');
        const formData = new FormData(form);
        
        // Convert FormData to JSON
        const preferences = {};
        const emailEnabled = formData.get('email_notifications_enabled') === 'on';
        
        // Process preferences
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('preferences[')) {
                const matches = key.match(/preferences\[([^\]]+)\]\[([^\]]+)\]/);
                if (matches) {
                    const [, type, setting] = matches;
                    if (!preferences[type]) preferences[type] = {};
                    preferences[type][setting] = value === 'on';
                }
            }
        }
        
        // Send AJAX request
        fetch('/ajax/notification-preferences', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                email_notifications_enabled: emailEnabled,
                preferences: preferences
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Có lỗi xảy ra', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi lưu cài đặt', 'error');
        });
    });
    
    // Reset preferences
    document.getElementById('resetPreferencesBtn').addEventListener('click', function() {
        if (confirm('Khôi phục tất cả cài đặt về mặc định?')) {
            fetch('/ajax/notification-preferences/reset', {
                method: 'POST',
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
    
    // Quick actions
    document.querySelectorAll('.quick-action-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            const checkboxes = document.querySelectorAll('.notification-toggle:not([disabled])');
            const emailCheckboxes = document.querySelectorAll('input[type="checkbox"][id^="email_"]:not([disabled])');
            const pushCheckboxes = document.querySelectorAll('input[type="checkbox"][id^="push_"]');
            
            switch (action) {
                case 'enable-all':
                    checkboxes.forEach(cb => cb.checked = true);
                    emailCheckboxes.forEach(cb => cb.checked = true);
                    pushCheckboxes.forEach(cb => cb.checked = true);
                    break;
                case 'disable-all':
                    checkboxes.forEach(cb => cb.checked = false);
                    emailCheckboxes.forEach(cb => cb.checked = false);
                    pushCheckboxes.forEach(cb => cb.checked = false);
                    break;
                case 'email-only':
                    checkboxes.forEach(cb => cb.checked = true);
                    emailCheckboxes.forEach(cb => cb.checked = true);
                    pushCheckboxes.forEach(cb => cb.checked = false);
                    break;
                case 'push-only':
                    checkboxes.forEach(cb => cb.checked = true);
                    emailCheckboxes.forEach(cb => cb.checked = false);
                    pushCheckboxes.forEach(cb => cb.checked = true);
                    break;
            }
        });
    });
    
    // Toggle dependent checkboxes
    document.querySelectorAll('.notification-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const type = this.id.replace('enabled_', '');
            const emailCheckbox = document.getElementById(`email_${type}`);
            const pushCheckbox = document.getElementById(`push_${type}`);
            
            if (!this.checked) {
                if (emailCheckbox && !emailCheckbox.disabled) emailCheckbox.checked = false;
                if (pushCheckbox && !pushCheckbox.disabled) pushCheckbox.checked = false;
            }
        });
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
.notification-type-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.form-check-input:disabled {
    opacity: 0.5;
}

.quick-action-btn {
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .quick-action-btn {
        margin-bottom: 1rem;
    }
    
    .notification-type-item .row {
        flex-direction: column;
    }
    
    .notification-type-item .col-md-6:last-child {
        margin-top: 1rem;
    }
}
</style>
@endpush
