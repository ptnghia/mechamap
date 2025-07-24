@extends('admin.layouts.dason')

@section('title', 'Cấu hình Bảo mật')

@section('styles')
<style>
    .setting-card {
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .card-header {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        color: white;
        border-radius: 10px 10px 0 0 !important;
        padding: 15px 20px;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        border-color: #dc3545;
    }

    .btn-primary {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        border: none;
        border-radius: 25px;
        padding: 10px 30px;
        font-weight: 600;
    }

    .setting-group {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .setting-group h6 {
        color: #495057;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .form-switch .form-check-input:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .security-alert {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .ip-whitelist {
        font-family: 'Courier New', monospace;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
    }

    .strength-indicator {
        height: 8px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .strength-weak {
        background-color: #dc3545;
    }

    .strength-medium {
        background-color: #ffc107;
    }

    .strength-strong {
        background-color: #28a745;
    }
</style>
@endsection

@push('styles')
<!-- Page specific CSS -->
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            @include('admin.settings.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Cấu hình Bảo mật</h2>
                    <p class="text-muted mb-0">Quản lý các tùy chọn bảo mật và xác thực của hệ thống</p>
                </div>
            </div>

            <!-- Security Alert -->
            <div class="security-alert">
                <h6 class="mb-2">
                    <i class="fas fa-shield-alt text-warning me-2"></i>
                    Cảnh báo bảo mật
                </h6>
                <p class="mb-0">
                    Các thay đổi cấu hình bảo mật có thể ảnh hưởng đến tất cả người dùng.
                    Hãy cẩn thận khi điều chỉnh các tùy chọn này và đảm bảo bạn hiểu rõ tác động của chúng.
                </p>
            </div>

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Có lỗi xảy ra, vui lòng kiểm tra lại!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form action="{{ route('admin.settings.security.update') }}" method="POST" id="securitySettingsForm">
                @csrf
                @method('PUT')

                <!-- Authentication Security -->
                <div class="card setting-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-key me-2"></i>
                            Bảo mật xác thực
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="setting-group">
                            <h6><i class="fas fa-shield-alt me-2"></i>Xác thực hai yếu tố (2FA)</h6>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="two_factor_auth_enabled"
                                    name="two_factor_auth_enabled" value="1" {{ old('two_factor_auth_enabled',
                                    $settings['two_factor_auth_enabled'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="two_factor_auth_enabled">
                                    Bật xác thực hai yếu tố cho admin
                                </label>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Yêu cầu admin nhập mã từ ứng dụng authenticator khi đăng nhập.
                            </div>
                        </div>

                        <div class="setting-group">
                            <h6><i class="fas fa-clock me-2"></i>Thời gian phiên làm việc</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="session_timeout" class="form-label">
                                        Thời gian hết hạn phiên (phút) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                        class="form-control @error('session_timeout') is-invalid @enderror"
                                        id="session_timeout" name="session_timeout"
                                        value="{{ old('session_timeout', $settings['session_timeout'] ?? 120) }}"
                                        min="1" max="1440" required>
                                    @error('session_timeout')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Từ 1 phút đến 1440 phút (24 giờ)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Login Security -->
                <div class="card setting-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-door-open me-2"></i>
                            Bảo mật đăng nhập
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="setting-group">
                            <h6><i class="fas fa-shield-alt me-2"></i>Bảo vệ chống tấn công brute force</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="max_login_attempts" class="form-label">
                                        Số lần đăng nhập sai tối đa <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                        class="form-control @error('max_login_attempts') is-invalid @enderror"
                                        id="max_login_attempts" name="max_login_attempts"
                                        value="{{ old('max_login_attempts', $settings['max_login_attempts'] ?? 5) }}"
                                        min="1" max="10" required>
                                    @error('max_login_attempts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="login_lockout_duration" class="form-label">
                                        Thời gian khóa tài khoản (phút) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                        class="form-control @error('login_lockout_duration') is-invalid @enderror"
                                        id="login_lockout_duration" name="login_lockout_duration"
                                        value="{{ old('login_lockout_duration', $settings['login_lockout_duration'] ?? 30) }}"
                                        min="1" max="1440" required>
                                    @error('login_lockout_duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password Security -->
                <div class="card setting-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-lock me-2"></i>
                            Chính sách mật khẩu
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="setting-group">
                            <h6><i class="fas fa-key-fill me-2"></i>Yêu cầu mật khẩu</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password_min_length" class="form-label">
                                        Độ dài tối thiểu <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                        class="form-control @error('password_min_length') is-invalid @enderror"
                                        id="password_min_length" name="password_min_length"
                                        value="{{ old('password_min_length', $settings['password_min_length'] ?? 8) }}"
                                        min="6" max="50" required>
                                    @error('password_min_length')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_expiry_days" class="form-label">
                                        Hết hạn sau (ngày)
                                    </label>
                                    <input type="number"
                                        class="form-control @error('password_expiry_days') is-invalid @enderror"
                                        id="password_expiry_days" name="password_expiry_days"
                                        value="{{ old('password_expiry_days', $settings['password_expiry_days'] ?? '') }}"
                                        min="1" max="365" placeholder="Để trống nếu không hết hạn">
                                    @error('password_expiry_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="password_require_uppercase"
                                            name="password_require_uppercase" value="1" {{
                                            old('password_require_uppercase', $settings['password_require_uppercase'] ??
                                            true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="password_require_uppercase">
                                            Yêu cầu chữ hoa (A-Z)
                                        </label>
                                    </div>

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="password_require_lowercase"
                                            name="password_require_lowercase" value="1" {{
                                            old('password_require_lowercase', $settings['password_require_lowercase'] ??
                                            true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="password_require_lowercase">
                                            Yêu cầu chữ thường (a-z)
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="password_require_numbers"
                                            name="password_require_numbers" value="1" {{ old('password_require_numbers',
                                            $settings['password_require_numbers'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="password_require_numbers">
                                            Yêu cầu số (0-9)
                                        </label>
                                    </div>

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="password_require_symbols"
                                            name="password_require_symbols" value="1" {{ old('password_require_symbols',
                                            $settings['password_require_symbols'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="password_require_symbols">
                                            Yêu cầu ký tự đặc biệt (!@#$%)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Password Strength Indicator -->
                            <div class="mt-3">
                                <label class="form-label">Độ mạnh mật khẩu hiện tại:</label>
                                <div class="strength-indicator" id="passwordStrengthIndicator"></div>
                                <small class="text-muted" id="passwordStrengthText">Cấu hình chính sách để đánh
                                    giá</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- IP Security -->
                <div class="card setting-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-globe2 me-2"></i>
                            Bảo mật IP
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="setting-group">
                            <h6><i class="fas fa-list me-2"></i>Danh sách IP được phép truy cập Admin</h6>

                            <div class="mb-3">
                                <label for="admin_ip_whitelist" class="form-label">
                                    IP Whitelist
                                </label>
                                <textarea
                                    class="form-control ip-whitelist @error('admin_ip_whitelist') is-invalid @enderror"
                                    id="admin_ip_whitelist" name="admin_ip_whitelist" rows="5"
                                    placeholder="192.168.1.1&#10;10.0.0.0/24&#10;203.162.4.190">{{ old('admin_ip_whitelist', $settings['admin_ip_whitelist'] ?? '') }}</textarea>
                                @error('admin_ip_whitelist')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Nhập mỗi IP hoặc CIDR trên một dòng. Để trống để cho phép tất cả IP.
                                    <br>
                                    <strong>IP hiện tại của bạn:</strong> <code
                                        id="currentIP">{{ request()->ip() }}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-3">
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                <i class="fas fa-sync-alt me-2"></i>
                                Đặt lại
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-shield-alt me-2"></i>
                                Lưu cấu hình
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Update password strength indicator
function updatePasswordStrength() {
    const minLength = parseInt(document.getElementById('password_min_length').value) || 8;
    const requireUpper = document.getElementById('password_require_uppercase').checked;
    const requireLower = document.getElementById('password_require_lowercase').checked;
    const requireNumbers = document.getElementById('password_require_numbers').checked;
    const requireSymbols = document.getElementById('password_require_symbols').checked;

    const indicator = document.getElementById('passwordStrengthIndicator');
    const text = document.getElementById('passwordStrengthText');

    let score = 0;
    let requirements = [];

    if (minLength >= 8) score += 25;
    if (requireUpper) { score += 15; requirements.push('chữ hoa'); }
    if (requireLower) { score += 15; requirements.push('chữ thường'); }
    if (requireNumbers) { score += 20; requirements.push('số'); }
    if (requireSymbols) { score += 25; requirements.push('ký tự đặc biệt'); }

    let strength = 'weak';
    let strengthText = 'Yếu';

    if (score >= 70) {
        strength = 'strong';
        strengthText = 'Mạnh';
    } else if (score >= 50) {
        strength = 'medium';
        strengthText = 'Trung bình';
    }

    indicator.className = `strength-indicator strength-${strength}`;
    indicator.style.width = `${score}%`;

    text.textContent = `${strengthText} (${score}/100) - Yêu cầu: ít nhất ${minLength} ký tự`;
    if (requirements.length > 0) {
        text.textContent += `, ${requirements.join(', ')}`;
    }
}

// Event listeners for password policy changes
document.addEventListener('DOMContentLoaded', function() {
    updatePasswordStrength();

    const passwordInputs = [
        'password_min_length',
        'password_require_uppercase',
        'password_require_lowercase',
        'password_require_numbers',
        'password_require_symbols'
    ];

    passwordInputs.forEach(id => {
        document.getElementById(id).addEventListener('change', updatePasswordStrength);
    });
});

// Reset form
function resetForm() {
    if (confirm('Bạn có chắc chắn muốn đặt lại tất cả các thay đổi?')) {
        document.getElementById('securitySettingsForm').reset();
        updatePasswordStrength();
    }
}

// Form validation
document.getElementById('securitySettingsForm').addEventListener('submit', function(e) {
    const requiredFields = ['session_timeout', 'max_login_attempts', 'login_lockout_duration', 'password_min_length'];
    let hasError = false;

    requiredFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field.value.trim() || parseInt(field.value) < parseInt(field.min) || parseInt(field.value) > parseInt(field.max)) {
            field.classList.add('is-invalid');
            hasError = true;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    // Check IP whitelist format if provided
    const ipWhitelist = document.getElementById('admin_ip_whitelist').value.trim();
    if (ipWhitelist) {
        const lines = ipWhitelist.split('\n').filter(line => line.trim());
        const ipRegex = /^(\d{1,3}\.){3}\d{1,3}(\/\d{1,2})?$/;

        for (let line of lines) {
            if (!ipRegex.test(line.trim())) {
                document.getElementById('admin_ip_whitelist').classList.add('is-invalid');
                hasError = true;
                break;
            }
        }
    }

    if (hasError) {
        e.preventDefault();
        alert('Vui lòng kiểm tra lại các trường không hợp lệ!');
    }
});

// Warn about IP whitelist
document.getElementById('admin_ip_whitelist').addEventListener('input', function() {
    const currentIP = document.getElementById('currentIP').textContent;
    const whitelist = this.value.trim();

    if (whitelist && !whitelist.includes(currentIP)) {
        if (!document.getElementById('ipWarning')) {
            const warning = document.createElement('div');
            warning.id = 'ipWarning';
            warning.className = 'alert alert-warning mt-2';
            warning.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i><strong>Cảnh báo:</strong> IP hiện tại của bạn không có trong danh sách. Bạn có thể bị khóa khỏi admin panel!';
            this.parentNode.insertBefore(warning, this.nextSibling);
        }
    } else {
        const warning = document.getElementById('ipWarning');
        if (warning) warning.remove();
    }
});
</script>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection