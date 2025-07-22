<section>
    <header class="mb-4">
        <h3 class="fw-bold">{{ __('profile.update_password') }}</h3>
        <p class="text-muted">{{ __('profile.password_security_message') }}</p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">{{ __('profile.current_password') }}</label>
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">{{ __('profile.new_password') }}</label>
            <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" />
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">{{ __('auth.confirm_password') }}</label>
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" />
        </div>

        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>

            @if (session('status') === 'password-updated')
                <div class="ms-3">
                    <span class="text-success"
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)">
                        {{ __('common.saved') }}
                    </span>
                </div>
            @endif
        </div>
    </form>
</section>
