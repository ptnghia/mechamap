<section>
    <header class="mb-4">
        <h3 class="fw-bold">{{ __('Update Password') }}</h3>
        <p class="text-muted">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">{{ __('Current Password') }}</label>
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">{{ __('New Password') }}</label>
            <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" />
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" />
        </div>

        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
                <div class="ms-3">
                    <span class="text-success"
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)">
                        {{ __('Saved.') }}
                    </span>
                </div>
            @endif
        </div>
    </form>
</section>
