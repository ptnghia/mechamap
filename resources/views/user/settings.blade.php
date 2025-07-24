@extends('layouts.app')

@section('title', __('user.settings.title'))

@section('content')
<div class="container">
    <div class="row">
        <!-- Settings Navigation -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        {{ __('user.settings.page_title') }}
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="pill">
                        <i class="fas fa-user me-2"></i>
                        Thông tin cá nhân
                    </a>
                    <a href="#account" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                        <i class="fas fa-key me-2"></i>
                        Tài khoản & Bảo mật
                    </a>
                    <a href="#preferences" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                        <i class="fas fa-sliders-h me-2"></i>
                        Tùy chọn
                    </a>
                    <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                        <i class="fas fa-bell me-2"></i>
                        Thông báo
                    </a>
                    <a href="#privacy" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                        <i class="fas fa-shield-alt me-2"></i>
                        Quyền riêng tư
                    </a>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Profile Settings -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin cá nhân</h5>
                        </div>
                        <div class="card-body">
                            <form id="profileForm">
                                @csrf
                                <div class="row">
                                    <!-- Avatar Upload -->
                                    <div class="col-md-4 text-center mb-4">
                                        <div class="avatar-upload">
                                            <div class="avatar-preview">
                                                <img src="{{ auth()->user()->avatar ?? asset('images/default-avatar.png') }}"
                                                    alt="Avatar" class="rounded-circle" width="150" height="150"
                                                    id="avatarPreview">
                                            </div>
                                            <div class="mt-3">
                                                <input type="file" class="form-control" id="avatarUpload" name="avatar"
                                                    accept="image/*">
                                                <small class="text-muted">JPG, PNG tối đa 2MB</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Profile Form -->
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="name" class="form-label">Tên hiển thị <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    value="{{ auth()->user()->name }}" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label">Email <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="{{ auth()->user()->email }}" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="phone" class="form-label">Số điện thoại</label>
                                                <input type="tel" class="form-control" id="phone" name="phone"
                                                    value="{{ auth()->user()->phone }}">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="location" class="form-label">Địa điểm</label>
                                                <input type="text" class="form-control" id="location" name="location"
                                                    value="{{ auth()->user()->location }}"
                                                    placeholder="{{ __('user.settings.placeholders.location') }}">
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label for="bio" class="form-label">Giới thiệu bản thân</label>
                                                <textarea class="form-control" id="bio" name="bio" rows="4"
                                                    placeholder="{{ __('user.settings.placeholders.bio') }}">{{ auth()->user()->bio }}</textarea>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="website" class="form-label">Website</label>
                                                <input type="url" class="form-control" id="website" name="website"
                                                    value="{{ auth()->user()->website }}" placeholder="https://">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="profession" class="form-label">Nghề nghiệp</label>
                                                <input type="text" class="form-control" id="profession"
                                                    name="profession" value="{{ auth()->user()->profession }}"
                                                    placeholder="{{ __('user.settings.placeholders.profession') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Lưu thông tin
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Account & Security Settings -->
                <div class="tab-pane fade" id="account">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Đổi mật khẩu</h5>
                        </div>
                        <div class="card-body">
                            <form id="passwordForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="current_password" class="form-label">Mật khẩu hiện tại <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="current_password"
                                            name="current_password" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="new_password" class="form-label">Mật khẩu mới <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="new_password"
                                            name="new_password" required>
                                        <small class="text-muted">Tối thiểu 8 ký tự</small>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu mới
                                            <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="new_password_confirmation"
                                            name="new_password_confirmation" required>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key me-1"></i>
                                        Đổi mật khẩu
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Two-Factor Authentication -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Xác thực hai yếu tố (2FA)</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6>Xác thực hai yếu tố</h6>
                                    <p class="text-muted mb-0">Tăng cường bảo mật cho tài khoản của bạn</p>
                                </div>
                                <button class="btn btn-outline-success" id="enable2FA">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Kích hoạt 2FA
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Preferences -->
                <div class="tab-pane fade" id="preferences">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Tùy chọn hiển thị</h5>
                        </div>
                        <div class="card-body">
                            <form id="preferencesForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <h6>Giao diện</h6>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="theme" id="themeLight"
                                                value="light" checked>
                                            <label class="form-check-label" for="themeLight">
                                                <i class="fas fa-sun me-1"></i> Sáng
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="theme" id="themeDark"
                                                value="dark">
                                            <label class="form-check-label" for="themeDark">
                                                <i class="fas fa-moon me-1"></i> Tối
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="theme" id="themeAuto"
                                                value="auto">
                                            <label class="form-check-label" for="themeAuto">
                                                <i class="fas fa-adjust me-1"></i> Tự động
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h6>Ngôn ngữ</h6>
                                        <select class="form-select" name="language">
                                            <option value="vi" selected>Tiếng Việt</option>
                                            <option value="en">English</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h6>Số threads mỗi trang</h6>
                                        <select class="form-select" name="threads_per_page">
                                            <option value="10">10 threads</option>
                                            <option value="20" selected>20 threads</option>
                                            <option value="30">30 threads</option>
                                            <option value="50">50 threads</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h6>Múi giờ</h6>
                                        <select class="form-select" name="timezone">
                                            <option value="Asia/Ho_Chi_Minh" selected>Việt Nam (UTC+7)</option>
                                            <option value="Asia/Bangkok">Bangkok (UTC+7)</option>
                                            <option value="Asia/Singapore">Singapore (UTC+8)</option>
                                            <option value="UTC">UTC (UTC+0)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Lưu tùy chọn
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="tab-pane fade" id="notifications">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('user.settings.sections.notifications') }}</h5>
                        </div>
                        <div class="card-body">
                            <form id="notificationForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Thông báo Email</h6>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="emailNewComment"
                                                name="email_new_comment" checked>
                                            <label class="form-check-label" for="emailNewComment">
                                                Bình luận mới trên thread của tôi
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="emailNewRating"
                                                name="email_new_rating" checked>
                                            <label class="form-check-label" for="emailNewRating">
                                                Đánh giá mới trên thread của tôi
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="emailNewFollower"
                                                name="email_new_follower" checked>
                                            <label class="form-check-label" for="emailNewFollower">
                                                Người theo dõi mới
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="emailWeeklyDigest"
                                                name="email_weekly_digest" checked>
                                            <label class="form-check-label" for="emailWeeklyDigest">
                                                Tổng hợp hàng tuần
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6>Thông báo trong App</h6>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="pushNewComment"
                                                name="push_new_comment" checked>
                                            <label class="form-check-label" for="pushNewComment">
                                                Bình luận mới
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="pushNewRating"
                                                name="push_new_rating" checked>
                                            <label class="form-check-label" for="pushNewRating">
                                                Đánh giá mới
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="pushModerationAction"
                                                name="push_moderation_action" checked>
                                            <label class="form-check-label" for="pushModerationAction">
                                                Hành động moderation
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="pushSystemAnnouncement"
                                                name="push_system_announcement" checked>
                                            <label class="form-check-label" for="pushSystemAnnouncement">
                                                Thông báo hệ thống
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        {{ __('user.settings.actions.save_settings') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Privacy Settings -->
                <div class="tab-pane fade" id="privacy">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quyền riêng tư</h5>
                        </div>
                        <div class="card-body">
                            <form id="privacyForm">
                                @csrf
                                <div class="row">
                                    <div class="col-12 mb-4">
                                        <h6>Hiển thị thông tin</h6>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="showEmail"
                                                name="show_email">
                                            <label class="form-check-label" for="showEmail">
                                                Hiển thị email trên hồ sơ công khai
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="showOnlineStatus"
                                                name="show_online_status" checked>
                                            <label class="form-check-label" for="showOnlineStatus">
                                                Hiển thị trạng thái online
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="showActivityFeed"
                                                name="show_activity_feed" checked>
                                            <label class="form-check-label" for="showActivityFeed">
                                                Hiển thị hoạt động gần đây
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="allowDirectMessage"
                                                name="allow_direct_message" checked>
                                            <label class="form-check-label" for="allowDirectMessage">
                                                Cho phép nhận tin nhắn trực tiếp
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-12 mb-4">
                                        <h6>Bảo mật nâng cao</h6>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                id="requireEmailVerification" name="require_email_verification" checked>
                                            <label class="form-check-label" for="requireEmailVerification">
                                                Yêu cầu xác thực email khi đăng nhập từ thiết bị mới
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="logoutOtherDevices"
                                                name="logout_other_devices">
                                            <label class="form-check-label" for="logoutOtherDevices">
                                                Đăng xuất khỏi tất cả thiết bị khác
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-danger" onclick="deleteAccount()">
                                        <i class="fas fa-trash me-1"></i>
                                        Xóa tài khoản
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        {{ __('user.settings.actions.save_settings') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Avatar preview
    document.getElementById('avatarUpload')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Profile form submission
    document.getElementById('profileForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route("user.settings.profile") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.message || {!! json_encode(__('user.settings.errors.general_error')) !!});
            }
        })
        .catch(error => {
            showAlert('danger', {!! json_encode(__('user.settings.errors.update_profile_failed')) !!});
        });
    });

    // Password form submission
    document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route("user.settings.password") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                this.reset(); // Clear form
            } else {
                showAlert('danger', data.message || {!! json_encode(__('user.settings.errors.general_error')) !!});
            }
        })
        .catch(error => {
            showAlert('danger', {!! json_encode(__('user.settings.errors.change_password_failed')) !!});
        });
    });

    // Preferences form submission
    document.getElementById('preferencesForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route("user.settings.preferences") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.message || {!! json_encode(__('user.settings.errors.general_error')) !!});
            }
        })
        .catch(error => {
            showAlert('danger', {!! json_encode(__('user.settings.errors.save_preferences_failed')) !!});
        });
    });

    // Notification form submission
    document.getElementById('notificationForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route("user.settings.notifications") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.message || {!! json_encode(__('user.settings.errors.general_error')) !!});
            }
        })
        .catch(error => {
            showAlert('danger', {!! json_encode(__('user.settings.errors.save_notifications_failed')) !!});
        });
    });

    // Privacy form submission
    document.getElementById('privacyForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route("user.settings.privacy") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.message || {!! json_encode(__('user.settings.errors.general_error')) !!});
            }
        })
        .catch(error => {
            showAlert('danger', {!! json_encode(__('user.settings.errors.save_privacy_failed')) !!});
        });
    });
});

function deleteAccount() {
    if (confirm({!! json_encode(__('user.settings.confirmations.delete_account_1')) !!})) {
        if (confirm({!! json_encode(__('user.settings.confirmations.delete_account_2')) !!})) {
            fetch('{{ route("user.settings.delete-account") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2000);
                } else {
                    showAlert('danger', data.message || {!! json_encode(__('user.settings.errors.general_error')) !!});
                }
            })
            .catch(error => {
                showAlert('danger', {!! json_encode(__('user.settings.errors.delete_account_failed')) !!});
            });
        }
    }
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    const container = document.querySelector('.container');
    container.insertAdjacentHTML('afterbegin', alertHtml);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        }
    }, 5000);
}
</script>
@endpush
