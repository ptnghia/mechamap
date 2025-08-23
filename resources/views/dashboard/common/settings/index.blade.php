@extends('dashboard.layouts.app')

@section('title', __('settings.index.title'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- General Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>
                        {{ __('settings.index.general') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dashboard.settings.preferences') }}">
                        @csrf
                        @method('PATCH')

                        <!-- Language & Region -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="locale" class="form-label">{{ __('settings.index.language') }}</label>
                                <select class="form-select @error('locale') is-invalid @enderror"
                                        id="locale"
                                        name="locale">
                                    <option value="vi" {{ (auth()->user()->locale ?? 'vi') == 'vi' ? 'selected' : '' }}>
                                        Tiếng Việt
                                    </option>
                                    <option value="en" {{ (auth()->user()->locale ?? 'vi') == 'en' ? 'selected' : '' }}>
                                        English
                                    </option>
                                </select>
                                @error('locale')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="timezone" class="form-label">{{ __('settings.index.timezone') }}</label>
                                <select class="form-select @error('timezone') is-invalid @enderror"
                                        id="timezone"
                                        name="timezone">
                                    <option value="Asia/Ho_Chi_Minh" {{ (auth()->user()->timezone ?? 'Asia/Ho_Chi_Minh') == 'Asia/Ho_Chi_Minh' ? 'selected' : '' }}>
                                        Asia/Ho Chi Minh (GMT+7)
                                    </option>
                                    <option value="UTC" {{ (auth()->user()->timezone ?? 'Asia/Ho_Chi_Minh') == 'UTC' ? 'selected' : '' }}>
                                        UTC (GMT+0)
                                    </option>
                                    <option value="America/New_York" {{ (auth()->user()->timezone ?? 'Asia/Ho_Chi_Minh') == 'America/New_York' ? 'selected' : '' }}>
                                        America/New York (GMT-5)
                                    </option>
                                </select>
                                @error('timezone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Theme -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('settings.index.theme') }}</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="theme"
                                               id="theme_light"
                                               value="light"
                                               {{ (auth()->user()->preferences['theme'] ?? 'light') == 'light' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="theme_light">
                                            <i class="fas fa-sun me-1"></i>
                                            {{ __('settings.index.light') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="theme"
                                               id="theme_dark"
                                               value="dark"
                                               {{ (auth()->user()->preferences['theme'] ?? 'light') == 'dark' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="theme_dark">
                                            <i class="fas fa-moon me-1"></i>
                                            {{ __('settings.index.dark') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="theme"
                                               id="theme_auto"
                                               value="auto"
                                               {{ (auth()->user()->preferences['theme'] ?? 'light') == 'auto' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="theme_auto">
                                            <i class="fas fa-adjust me-1"></i>
                                            {{ __('settings.index.auto') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                {{ __('settings.index.save') }}
                            </button>

                            @if (session('success'))
                                <div class="ms-3">
                                    <span class="text-success">
                                        <i class="fas fa-check me-1"></i>
                                        {{ session('success') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell me-2"></i>
                        {{ __('settings.index.notifications') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dashboard.settings.notifications') }}">
                        @csrf
                        @method('PATCH')

                        <!-- Email Notifications -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">{{ __('settings.index.email_notifications') }}</h6>

                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="email_notifications"
                                       name="email_notifications"
                                       value="1"
                                       {{ (auth()->user()->preferences['notifications']['email_notifications'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_notifications">
                                    {{ __('settings.index.enable_email') }}
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="thread_replies"
                                       name="thread_replies"
                                       value="1"
                                       {{ (auth()->user()->preferences['notifications']['thread_replies'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="thread_replies">
                                    {{ __('settings.index.thread_replies') }}
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="thread_likes"
                                       name="thread_likes"
                                       value="1"
                                       {{ (auth()->user()->preferences['notifications']['thread_likes'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="thread_likes">
                                    {{ __('settings.index.thread_likes') }}
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="new_followers"
                                       name="new_followers"
                                       value="1"
                                       {{ (auth()->user()->preferences['notifications']['new_followers'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="new_followers">
                                    {{ __('settings.index.new_followers') }}
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="mention_notifications"
                                       name="mention_notifications"
                                       value="1"
                                       {{ (auth()->user()->preferences['notifications']['mention_notifications'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="mention_notifications">
                                    {{ __('settings.index.mentions') }}
                                </label>
                            </div>
                        </div>

                        <!-- Marketing -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">{{ __('settings.index.marketing') }}</h6>

                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="marketing_emails"
                                       name="marketing_emails"
                                       value="1"
                                       {{ (auth()->user()->preferences['notifications']['marketing_emails'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="marketing_emails">
                                    {{ __('settings.index.marketing_emails') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                {{ __('settings.index.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Privacy Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        {{ __('settings.index.privacy') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dashboard.settings.privacy') }}">
                        @csrf
                        @method('PATCH')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="profile_visibility" class="form-label">{{ __('settings.index.profile_visibility') }}</label>
                                <select class="form-select" id="profile_visibility" name="profile_visibility">
                                    <option value="public" {{ (auth()->user()->preferences['privacy']['profile_visibility'] ?? 'public') == 'public' ? 'selected' : '' }}>
                                        {{ __('settings.index.public') }}
                                    </option>
                                    <option value="private" {{ (auth()->user()->preferences['privacy']['profile_visibility'] ?? 'public') == 'private' ? 'selected' : '' }}>
                                        {{ __('settings.index.private') }}
                                    </option>
                                    <option value="friends" {{ (auth()->user()->preferences['privacy']['profile_visibility'] ?? 'public') == 'friends' ? 'selected' : '' }}>
                                        {{ __('settings.index.friends_only') }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="allow_messages" class="form-label">{{ __('settings.index.allow_messages') }}</label>
                                <select class="form-select" id="allow_messages" name="allow_messages">
                                    <option value="everyone" {{ (auth()->user()->preferences['privacy']['allow_messages'] ?? 'everyone') == 'everyone' ? 'selected' : '' }}>
                                        {{ __('settings.index.everyone') }}
                                    </option>
                                    <option value="friends" {{ (auth()->user()->preferences['privacy']['allow_messages'] ?? 'everyone') == 'friends' ? 'selected' : '' }}>
                                        {{ __('settings.index.friends_only') }}
                                    </option>
                                    <option value="none" {{ (auth()->user()->preferences['privacy']['allow_messages'] ?? 'everyone') == 'none' ? 'selected' : '' }}>
                                        {{ __('settings.index.no_one') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="show_online_status"
                                       name="show_online_status"
                                       value="1"
                                       {{ (auth()->user()->preferences['privacy']['show_online_status'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_online_status">
                                    {{ __('settings.index.show_online_status') }}
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="show_activity"
                                       name="show_activity"
                                       value="1"
                                       {{ (auth()->user()->preferences['privacy']['show_activity'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_activity">
                                    {{ __('settings.index.show_activity') }}
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="indexable_profile"
                                       name="indexable_profile"
                                       value="1"
                                       {{ (auth()->user()->preferences['privacy']['indexable_profile'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="indexable_profile">
                                    {{ __('settings.index.indexable_profile') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                {{ __('settings.index.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Account Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-cog me-2"></i>
                        {{ __('settings.index.account_actions') }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Export Data -->
                    <div class="d-grid gap-2 mb-3">
                        <a href="{{ route('dashboard.settings.export-data') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-1"></i>
                            {{ __('settings.index.export_data') }}
                        </a>
                    </div>

                    <!-- Reset Settings -->
                    <div class="d-grid gap-2 mb-3">
                        <button type="button"
                                class="btn btn-outline-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#resetSettingsModal">
                            <i class="fas fa-undo me-1"></i>
                            {{ __('settings.index.reset_settings') }}
                        </button>
                    </div>

                    <!-- Deactivate Account -->
                    <div class="d-grid gap-2">
                        <button type="button"
                                class="btn btn-outline-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#deactivateAccountModal">
                            <i class="fas fa-user-times me-1"></i>
                            {{ __('settings.index.deactivate_account') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Account Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __('settings.index.account_info') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>{{ __('settings.index.member_since') }}:</strong>
                        <span class="text-muted">{{ auth()->user()->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="mb-2">
                        <strong>{{ __('settings.index.last_login') }}:</strong>
                        <span class="text-muted">
                            {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'Chưa có thông tin' }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <strong>{{ __('settings.index.role') }}:</strong>
                        <span class="badge bg-primary">{{ auth()->user()->role }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Settings Modal -->
<div class="modal fade" id="resetSettingsModal" tabindex="-1" aria-labelledby="resetSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-warning" id="resetSettingsModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ __('settings.index.reset_settings') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('dashboard.settings.reset-defaults') }}">
                <div class="modal-body">
                    @csrf
                    @method('PATCH')

                    <div class="alert alert-warning">
                        <h6 class="fw-bold mb-2">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            {{ __('settings.index.reset_warning') }}
                        </h6>
                        <p class="mb-0">{{ __('settings.index.reset_description') }}</p>
                    </div>

                    <div class="mb-3">
                        <label for="reset_password" class="form-label">{{ __('settings.index.current_password') }}</label>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="reset_password"
                               name="password"
                               placeholder="{{ __('settings.index.enter_password') }}"
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        {{ __('settings.index.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-undo me-1"></i>
                        {{ __('settings.index.reset') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Deactivate Account Modal -->
<div class="modal fade" id="deactivateAccountModal" tabindex="-1" aria-labelledby="deactivateAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="deactivateAccountModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ __('settings.index.deactivate_account') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('dashboard.settings.deactivate') }}">
                <div class="modal-body">
                    @csrf
                    @method('DELETE')

                    <div class="alert alert-danger">
                        <h6 class="fw-bold mb-2">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            {{ __('settings.index.deactivate_warning') }}
                        </h6>
                        <p class="mb-0">{{ __('settings.index.deactivate_description') }}</p>
                    </div>

                    <div class="mb-3">
                        <label for="deactivate_password" class="form-label">{{ __('settings.index.current_password') }}</label>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="deactivate_password"
                               name="password"
                               placeholder="{{ __('settings.index.enter_password') }}"
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">{{ __('settings.index.reason') }}</label>
                        <textarea class="form-control @error('reason') is-invalid @enderror"
                                  id="reason"
                                  name="reason"
                                  rows="3"
                                  placeholder="{{ __('settings.index.reason_placeholder') }}"
                                  required></textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        {{ __('settings.index.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-user-times me-1"></i>
                        {{ __('settings.index.deactivate') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
