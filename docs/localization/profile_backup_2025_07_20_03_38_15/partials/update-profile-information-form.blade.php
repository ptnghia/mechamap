<section>
    <header class="mb-4">
        <h3 class="fw-bold">{{ __('Profile Information') }}</h3>
        <p class="text-muted">{{ __("Update your account's profile information and email address.") }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- Avatar Upload Form -->
    <div class="mb-4">
        <h4 class="mb-3">{{ __('Profile Picture') }}</h4>

        <div class="row align-items-center">
            <div class="col-auto">
                <div class="avatar-upload">
                    <div class="avatar-edit">
                        <input type="file" id="avatar-upload" form="avatar-form" name="avatar" accept=".png, .jpg, .jpeg" />
                        <label for="avatar-upload"><i class="fas fa-edit"></i></label>
                    </div>
                    <div class="avatar-preview">
                        <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" id="avatar-preview-image">
                    </div>
                </div>
            </div>
            <div class="col">
                <form id="avatar-form" method="post" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="d-none">
                    @csrf
                </form>
                <p class="text-muted mb-1">{{ __('Click the edit icon to upload a new profile picture.') }}</p>
                <p class="text-muted small">{{ __('Recommended size: 200x200 pixels. Max file size: 2MB.') }}</p>
                @if (session('status') === 'avatar-updated')
                    <div class="alert alert-success mt-2">
                        {{ __('Profile picture updated successfully.') }}
                    </div>
                @endif
                @error('avatar')
                    <div class="alert alert-danger mt-2">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
    </div>

    <hr class="my-4">

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <div class="alert alert-warning">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="btn btn-link p-0 m-0 align-baseline">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success mt-2">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
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
