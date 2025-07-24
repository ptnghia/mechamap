@extends('admin.layouts.dason')

@section('title', 'Cấu hình Email')

@section('styles')
<style>
    .setting-card {
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px 10px 0 0 !important;
        padding: 15px 20px;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        border-color: #667eea;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

    .input-group-text {
        background-color: #e9ecef;
        border-color: #ced4da;
    }

    .test-connection-btn {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        border-radius: 20px;
        padding: 8px 20px;
        color: white;
        font-weight: 500;
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
                    <h2 class="mb-1">Cấu hình Email</h2>
                    <p class="text-muted mb-0">Quản lý cấu hình SMTP và email hệ thống</p>
                </div>
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

            <form action="{{ route('admin.settings.email.update') }}" method="POST" id="emailSettingsForm">
                @csrf
                @method('PUT')

                <!-- Email Sender Configuration -->
                <div class="card setting-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-envelope me-2"></i>
                            Cấu hình người gửi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="setting-group">
                            <h6><i class="fas fa-user-badge me-2"></i>Thông tin người gửi</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="from_address" class="form-label">
                                        Email người gửi <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email"
                                            class="form-control @error('from_address') is-invalid @enderror"
                                            id="from_address" name="from_address"
                                            value="{{ old('from_address', $settings['from_address'] ?? '') }}"
                                            placeholder="noreply@mechamap.com" required>
                                    </div>
                                    @error('from_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="from_name" class="form-label">
                                        Tên người gửi <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control @error('from_name') is-invalid @enderror"
                                            id="from_name" name="from_name"
                                            value="{{ old('from_name', $settings['from_name'] ?? '') }}"
                                            placeholder="MechaMap System" required>
                                    </div>
                                    @error('from_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="reply_to" class="form-label">
                                    Email trả lời
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-reply"></i></span>
                                    <input type="email" class="form-control @error('reply_to') is-invalid @enderror"
                                        id="reply_to" name="reply_to"
                                        value="{{ old('reply_to', $settings['reply_to'] ?? '') }}"
                                        placeholder="support@mechamap.com">
                                </div>
                                @error('reply_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Email để người nhận có thể trả lời. Để trống nếu dùng email người
                                    gửi.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SMTP Configuration -->
                <div class="card setting-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-server me-2"></i>
                            Cấu hình SMTP
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="setting-group">
                            <h6><i class="fas fa-network-wired me-2"></i>Máy chủ SMTP</h6>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="smtp_host" class="form-label">
                                        Host SMTP <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-server"></i></span>
                                        <input type="text" class="form-control @error('smtp_host') is-invalid @enderror"
                                            id="smtp_host" name="smtp_host"
                                            value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}"
                                            placeholder="smtp.gmail.com" required>
                                    </div>
                                    @error('smtp_host')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="smtp_port" class="form-label">
                                        Port SMTP <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-network-wired"></i></span>
                                        <select class="form-select @error('smtp_port') is-invalid @enderror"
                                            id="smtp_port" name="smtp_port" required>
                                            <option value="">Chọn port</option>
                                            <option value="25" {{ (old('smtp_port', $settings['smtp_port'] ?? '' )=='25'
                                                ) ? 'selected' : '' }}>25 (SMTP)</option>
                                            <option value="465" {{ (old('smtp_port', $settings['smtp_port'] ?? ''
                                                )=='465' ) ? 'selected' : '' }}>465 (SMTPS)</option>
                                            <option value="587" {{ (old('smtp_port', $settings['smtp_port'] ?? ''
                                                )=='587' ) ? 'selected' : '' }}>587 (STARTTLS)</option>
                                            <option value="2525" {{ (old('smtp_port', $settings['smtp_port'] ?? ''
                                                )=='2525' ) ? 'selected' : '' }}>2525 (Alternative)</option>
                                        </select>
                                    </div>
                                    @error('smtp_port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="smtp_username" class="form-label">
                                        Username SMTP <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-check"></i></span>
                                        <input type="text"
                                            class="form-control @error('smtp_username') is-invalid @enderror"
                                            id="smtp_username" name="smtp_username"
                                            value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}"
                                            placeholder="username@gmail.com" required>
                                    </div>
                                    @error('smtp_username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="smtp_password" class="form-label">
                                        Password SMTP <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="password"
                                            class="form-control @error('smtp_password') is-invalid @enderror"
                                            id="smtp_password" name="smtp_password"
                                            value="{{ old('smtp_password', $settings['smtp_password'] ?? '') }}"
                                            placeholder="••••••••••••••••" required>
                                        <button type="button" class="btn btn-outline-secondary"
                                            onclick="togglePassword('smtp_password')">
                                            <i class="fas fa-eye" id="smtp_password_icon"></i>
                                        </button>
                                    </div>
                                    @error('smtp_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Với Gmail, hãy sử dụng App Password thay vì mật khẩu thường.
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="test-connection-btn" onclick="testEmailConnection()">
                                        <i class="fas fa-wifi me-2"></i>
                                        Kiểm tra kết nối
                                    </button>
                                    <div id="connection-result" class="mt-2"></div>
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
                                <i class="fas fa-check me-2"></i>
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
    // Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');

    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

// Test email connection
function testEmailConnection() {
    const btn = document.querySelector('.test-connection-btn');
    const result = document.getElementById('connection-result');

    // Disable button and show loading
    btn.disabled = true;
    btn.innerHTML = '<i class="spinner-border spinner-border-sm me-2" role="status"></i>Đang kiểm tra...';

    // Get form data
    const formData = new FormData(document.getElementById('emailSettingsForm'));
      // AJAX request to test connection
    fetch('/admin/settings/email/test-connection', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            result.innerHTML = '<div class="alert alert-success mt-2"><i class="fas fa-check-circle me-2"></i>' + data.message + '</div>';
        } else {
            result.innerHTML = '<div class="alert alert-danger mt-2"><i class="fas fa-exclamation-triangle me-2"></i>' + data.message + '</div>';
        }
    })
    .catch(error => {
        result.innerHTML = '<div class="alert alert-danger mt-2"><i class="fas fa-exclamation-triangle me-2"></i>Có lỗi xảy ra khi kiểm tra kết nối.</div>';
    })
    .finally(() => {
        // Re-enable button
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-wifi me-2"></i>Kiểm tra kết nối';

        // Auto hide result after 5 seconds
        setTimeout(() => {
            result.innerHTML = '';
        }, 5000);
    });
}

// Reset form
function resetForm() {
    if (confirm('Bạn có chắc chắn muốn đặt lại tất cả các thay đổi?')) {
        document.getElementById('emailSettingsForm').reset();
    }
}

// Form validation
document.getElementById('emailSettingsForm').addEventListener('submit', function(e) {
    const requiredFields = ['from_address', 'from_name', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password'];
    let hasError = false;

    requiredFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            hasError = true;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    if (hasError) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ các trường bắt buộc!');
    }
});

// Email validation
document.getElementById('from_address').addEventListener('blur', function() {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (this.value && !emailRegex.test(this.value)) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});

document.getElementById('reply_to').addEventListener('blur', function() {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (this.value && !emailRegex.test(this.value)) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});

// Port suggestion based on host
document.getElementById('smtp_host').addEventListener('blur', function() {
    const host = this.value.toLowerCase();
    const portSelect = document.getElementById('smtp_port');

    if (host.includes('gmail.com')) {
        portSelect.value = '587';
    } else if (host.includes('outlook.com') || host.includes('hotmail.com')) {
        portSelect.value = '587';
    } else if (host.includes('yahoo.com')) {
        portSelect.value = '587';
    }
});
</script>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection