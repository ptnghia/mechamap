<section>
    <header class="mb-4">
        <h3 class="fw-bold text-danger">{{ __('profile.edit.delete_account') }}</h3>
        <p class="text-muted">{{ __('profile.edit.delete_warning') }}</p>
    </header>

    <button type="button"
            class="btn btn-danger"
            data-bs-toggle="modal"
            data-bs-target="#deleteAccountModal">
        <i class="fas fa-trash me-1"></i>
        {{ __('profile.edit.delete_account') }}
    </button>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteAccountModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ __('profile.edit.delete_account') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="post" action="{{ route('dashboard.profile.destroy') }}">
                    <div class="modal-body">
                        @csrf
                        @method('delete')

                        <div class="alert alert-danger">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                {{ __('profile.edit.delete_confirm') }}
                            </h6>
                            <p class="mb-0">{{ __('profile.edit.delete_warning') }}</p>
                        </div>

                        <p class="text-muted mb-3">
                            Để xác nhận, vui lòng nhập mật khẩu của bạn:
                        </p>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password"
                                   class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="Nhập mật khẩu để xác nhận"
                                   required>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>
                            {{ __('profile.edit.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>
                            {{ __('profile.edit.delete') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
