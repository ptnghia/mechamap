<section>
    <header class="mb-4">
        <h3 class="fw-bold text-danger">{{ __('profile.delete_account') }}</h3>
        <p class="text-muted">{{ __('profile.delete_warning') }}</p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('profile.delete_account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="modal-header">
            <h5 class="modal-title">{{ __('profile.delete_account') }}</h5>
            <button type="button" class="btn-close" x-on:click="$dispatch('close')"></button>
        </div>

        <form method="post" action="{{ route('profile.destroy') }}">
            <div class="modal-body">
                @csrf
                @method('delete')

                <h5 class="fw-bold mb-3">{{ __('profile.delete_confirmation') }}</h5>

                <p class="text-muted mb-4">{{ __('profile.delete_password_confirmation') }}</p>

                <div class="mb-3">
                    <x-input-label for="password" value="{{ __('auth.password') }}" class="visually-hidden" />
                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="form-control"
                        placeholder="{{ __('auth.password') }}"
                    />
                    <x-input-error :messages="$errors->userDeletion->get('password')" />
                </div>
            </div>

            <div class="modal-footer">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('common.cancel') }}
                </x-secondary-button>

                <button type="button" class="btn btn-danger">{{ __('profile.delete_account') }}</button>
            </div>
        </form>
    </x-modal>
</section>
