@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm rounded-3">
                        <div class="card-body p-4">
                            <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <!-- File Upload Component -->
                                <x-file-upload
                                    name="file"
                                    :file-types="['jpg', 'jpeg', 'png', 'gif', 'webp']"
                                    max-size="5MB"
                                    :required="true"
                                    label="{{ __('Select File') }} <span class='text-danger'>*</span>"
                                    id="gallery-file-upload"
                                />
                                @error('file')
                                    <div class="text-danger small mt-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                    </div>
                                @enderror

                                <div class="mb-4">
                                    <label for="title" class="form-label">{{ __('Title') }}</label>
                                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}">
                                    <div class="form-text">{{ __('Give your media a descriptive title (optional).') }}</div>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="form-label">{{ __('Description') }}</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                                    <div class="form-text">{{ __('Add a description for your media (optional).') }}</div>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload me-1"></i> {{ __('Upload Media') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
