<section>
    <header class="mb-4">
        <h3 class="fw-bold">{{ __('profile.edit.update_password') }}</h3>
        <p class="text-muted">Đảm bảo tài khoản của bạn sử dụng mật khẩu dài, ngẫu nhiên để giữ an toàn.</p>
    </header>

    <form method="post" action="{{ route('dashboard.profile.password') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="current_password" class="form-label">{{ __('profile.edit.current_password') }}</label>
            <input type="password"
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                   id="current_password"
                   name="current_password"
                   autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('profile.edit.new_password') }}</label>
            <input type="password"
                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                   id="password"
                   name="password"
                   autocomplete="new-password">
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Mật khẩu phải có ít nhất 8 ký tự</div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('profile.edit.confirm_password') }}</label>
            <input type="password"
                   class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                   id="password_confirmation"
                   name="password_confirmation"
                   autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-lock me-1"></i>
                {{ __('profile.edit.save') }}
            </button>

            @if (session('status') === 'password-updated')
                <div class="ms-3">
                    <span class="text-success">
                        <i class="fas fa-check me-1"></i>
                        {{ __('profile.edit.saved') }}
                    </span>
                </div>
            @endif
        </div>
    </form>
</section>
