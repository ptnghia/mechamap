@extends('layouts.app')

@section('title', 'Edit Profile')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/avatar.css') }}">
@endpush

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="card shadow-sm rounded-3">
                        <div class="card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card shadow-sm rounded-3">
                        <div class="card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card shadow-sm rounded-3 border-danger">
                        <div class="card-body">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
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
