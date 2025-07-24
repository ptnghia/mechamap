<section>
    <header class="mb-4">
        <h3 class="fw-bold text-danger">{{ __('Delete Account') }}</h3>
        <p class="text-muted">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="modal-header">
            <h5 class="modal-title">{{ __('Delete Account') }}</h5>
            <button type="button" class="btn-close" x-on:click="$dispatch('close')"></button>
        </div>

        <form method="post" action="{{ route('profile.destroy') }}">
            <div class="modal-body">
                @csrf
                @method('delete')

                <h5 class="fw-bold mb-3">{{ __('Are you sure you want to delete your account?') }}</h5>

                <p class="text-muted mb-4">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>

                <div class="mb-3">
                    <x-input-label for="password" value="{{ __('Password') }}" class="visually-hidden" />
                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="form-control"
                        placeholder="{{ __('Password') }}"
                    />
                    <x-input-error :messages="$errors->userDeletion->get('password')" />
                </div>
            </div>

            <div class="modal-footer">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <button type="button" class="btn btn-danger">{{ __('Delete Account') }}</button>
            </div>
        </form>
    </x-modal>
</section>
