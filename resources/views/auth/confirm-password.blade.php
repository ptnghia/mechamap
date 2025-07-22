@extends('layouts.app')

@section('title', 'Page Title')

@section('content')
    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif<h2 class="text-center mb-4">{{ __('auth.confirm_password') }}</h2>

    <div class="alert alert-warning mb-4">
        {{ __('auth.secure_area_message') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('auth.password') }}</label>
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex justify-content-end mb-3">
            <button type="submit" class="btn btn-primary">{{ __('auth.confirm') }}</button>
        </div>
    </form>
@endsection
