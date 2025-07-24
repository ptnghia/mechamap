@extends('layouts.app')

@section('title', 'Chọn loại tài khoản - Social Login')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/login.css') }}">
<style>
.social-info-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.social-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 4px solid rgba(255,255,255,0.3);
    object-fit: cover;
}

.account-type-group {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
}

.account-type-group:hover {
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0,123,255,0.15);
}

.account-type-group.selected {
    border-color: #007bff;
    background-color: rgba(0,123,255,0.05);
}

.account-option {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 0.75rem;
    transition: all 0.2s ease;
}

.account-option:hover {
    border-color: #007bff;
    background-color: rgba(0,123,255,0.02);
}

.account-option.selected {
    border-color: #007bff;
    background-color: rgba(0,123,255,0.05);
}

.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.account-description {
    display: block;
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.provider-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    background-color: rgba(255,255,255,0.2);
    border-radius: 20px;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.provider-badge i {
    margin-right: 0.5rem;
}
</style>
@endpush

@section('full-width-content')
<div class="min-vh-100 d-flex align-items-center bg-light py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <!-- Social Info Card -->
                <div class="social-info-card text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        @if($socialData['avatar'])
                            <img src="{{ $socialData['avatar'] }}" alt="{{ $socialData['name'] }}" class="social-avatar me-3">
                        @else
                            <div class="social-avatar me-3 d-flex align-items-center justify-content-center bg-white text-primary">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                        @endif
                        <div class="text-start">
                            <h4 class="mb-1">{{ $socialData['name'] }}</h4>
                            <p class="mb-1 opacity-90">{{ $socialData['email'] }}</p>
                            <div class="provider-badge">
                                <i class="fab fa-{{ $socialData['provider'] }}"></i>
                                Đăng nhập từ {{ ucfirst($socialData['provider']) }}
                            </div>
                        </div>
                    </div>
                    <p class="mb-0 opacity-90">
                        Chào mừng bạn đến với MechaMap! Vui lòng chọn loại tài khoản phù hợp với nhu cầu của bạn.
                    </p>
                </div>

                <!-- Account Type Selection Form -->
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-dark mb-2">Chọn loại tài khoản</h2>
                            <p class="text-muted mb-0">
                                Chọn loại tài khoản phù hợp để có trải nghiệm tốt nhất trên MechaMap
                            </p>
                        </div>

                        <!-- Error Messages -->
                        @if (session('error'))
                            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('auth.social.account-type') }}" id="accountTypeForm">
                            @csrf

                            <!-- Community Members -->
                            <div class="account-type-group" data-group="community">
                                <div class="account-type-header mb-3">
                                    <h4 class="account-group-title">
                                        <i class="fas fa-users text-warning me-2"></i>
                                        Thành viên cộng đồng
                                    </h4>
                                    <p class="account-group-description text-muted mb-0">
                                        Dành cho kỹ sư, sinh viên và những người yêu thích cơ khí
                                    </p>
                                </div>

                                <div class="account-options">
                                    <div class="account-option">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="account_type" id="member" value="member" {{ old('account_type') == 'member' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="member">
                                                <strong>Thành viên</strong>
                                                <span class="badge bg-primary ms-2">Khuyến nghị</span>
                                                <span class="account-description">
                                                    Tham gia thảo luận, chia sẻ kinh nghiệm và học hỏi từ cộng đồng
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="account-option">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="account_type" id="verified_partner" value="verified_partner" {{ old('account_type') == 'verified_partner' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="verified_partner">
                                                <strong>Đối tác xác thực</strong>
                                                <span class="account-description">
                                                    Dành cho chuyên gia, tư vấn viên có kinh nghiệm trong ngành
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Business Partners -->
                            <div class="account-type-group" data-group="business">
                                <div class="account-type-header mb-3">
                                    <h4 class="account-group-title">
                                        <i class="fas fa-building text-primary me-2"></i>
                                        Đối tác kinh doanh
                                    </h4>
                                    <p class="account-group-description text-muted mb-0">
                                        Dành cho doanh nghiệp, nhà sản xuất và nhà cung cấp
                                    </p>
                                </div>

                                <div class="account-options">
                                    <div class="account-option">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="account_type" id="manufacturer" value="manufacturer" {{ old('account_type') == 'manufacturer' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="manufacturer">
                                                <strong>Nhà sản xuất</strong>
                                                <span class="account-description">
                                                    Sản xuất máy móc, thiết bị cơ khí và linh kiện
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="account-option">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="account_type" id="supplier" value="supplier" {{ old('account_type') == 'supplier' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="supplier">
                                                <strong>Nhà cung cấp</strong>
                                                <span class="account-description">
                                                    Cung cấp nguyên vật liệu, phụ tùng và dịch vụ kỹ thuật
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="account-option">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="account_type" id="brand" value="brand" {{ old('account_type') == 'brand' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="brand">
                                                <strong>Thương hiệu</strong>
                                                <span class="account-description">
                                                    Đại diện thương hiệu, phân phối sản phẩm cơ khí
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Username Field -->
                            <div class="mb-4">
                                <label for="username" class="form-label fw-medium text-dark">
                                    <i class="fas fa-at me-2 text-muted"></i>Tên đăng nhập
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg @error('username') is-invalid @enderror" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username') }}" 
                                       placeholder="Nhập tên đăng nhập của bạn"
                                       required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Tên đăng nhập chỉ được chứa chữ cái, số, dấu gạch dưới và dấu gạch ngang
                                </small>
                            </div>

                            <!-- Terms Acceptance -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('terms') is-invalid @enderror" 
                                           type="checkbox" 
                                           name="terms" 
                                           id="terms" 
                                           value="1" 
                                           {{ old('terms') ? 'checked' : '' }} 
                                           required>
                                    <label class="form-check-label" for="terms">
                                        Tôi đồng ý với <a href="#" class="text-primary">Điều khoản sử dụng</a> và <a href="#" class="text-primary">Chính sách bảo mật</a>
                                    </label>
                                    @error('terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-user-plus me-2"></i>
                                Tạo tài khoản
                            </button>

                            <!-- Back to Login -->
                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-muted text-decoration-none">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Quay lại đăng nhập
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle account type selection
    const accountTypeInputs = document.querySelectorAll('input[name="account_type"]');
    const accountOptions = document.querySelectorAll('.account-option');
    const accountGroups = document.querySelectorAll('.account-type-group');

    accountTypeInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Remove selected class from all options and groups
            accountOptions.forEach(option => option.classList.remove('selected'));
            accountGroups.forEach(group => group.classList.remove('selected'));

            // Add selected class to current option and its group
            const selectedOption = this.closest('.account-option');
            const selectedGroup = this.closest('.account-type-group');
            
            if (selectedOption) selectedOption.classList.add('selected');
            if (selectedGroup) selectedGroup.classList.add('selected');
        });
    });

    // Auto-generate username from email
    const emailFromSocial = '{{ $socialData["email"] }}';
    const usernameField = document.getElementById('username');
    
    if (!usernameField.value && emailFromSocial) {
        const suggestedUsername = emailFromSocial.split('@')[0].toLowerCase().replace(/[^a-z0-9_-]/g, '');
        usernameField.value = suggestedUsername;
    }
});
</script>
@endsection
