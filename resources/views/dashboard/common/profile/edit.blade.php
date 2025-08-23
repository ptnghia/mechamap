@extends('dashboard.layouts.app')

@section('title', __('profile.edit.title'))

@php
    $pageTitle = __('profile.edit.title');
    $pageDescription = __('profile.edit.description');
    $breadcrumbs = [
        ['title' => __('profile.edit.title'), 'url' => '#']
    ];
@endphp

@section('content')
<div class="row g-4">
    <!-- Profile Information -->
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    {{ __('profile.edit.profile_information') }}
                </h5>
            </div>
            <div class="card-body">
                @include('dashboard.common.profile.partials.update-profile-information-form')
            </div>
        </div>
    </div>

    <!-- Update Password -->
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lock me-2"></i>
                    {{ __('profile.edit.update_password') }}
                </h5>
            </div>
            <div class="card-body">
                @include('dashboard.common.profile.partials.update-password-form')
            </div>
        </div>
    </div>

    <!-- Delete Account -->
    <div class="col-md-12">
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-danger bg-opacity-10">
                <h5 class="mb-0 text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ __('profile.edit.danger_zone') }}
                </h5>
            </div>
            <div class="card-body">
                @include('dashboard.common.profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Avatar preview
        const avatarUpload = document.getElementById('avatar-upload');
        const avatarPreview = document.getElementById('avatar-preview-image');
        const avatarForm = document.getElementById('avatar-form');

        if (avatarUpload && avatarPreview && avatarForm) {
            avatarUpload.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                    }

                    reader.readAsDataURL(this.files[0]);

                    // Auto submit form when file is selected
                    avatarForm.submit();
                }
            });
        }
    });
</script>
@endpush
