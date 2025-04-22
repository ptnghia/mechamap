@extends('layouts.app')

@section('title', 'Create New Thread')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h1 class="h4 mb-0">Create New Thread</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('threads.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            <div class="form-text">
                                Use format: CITY l Project Name l Floors l Status (e.g., CARACAS l Promenade Res. I 24p I E/C)
                            </div>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="forum_id" class="form-label">Forum</label>
                                <select class="form-select @error('forum_id') is-invalid @enderror" id="forum_id" name="forum_id" required>
                                    <option value="">Select a forum</option>
                                    @foreach($forums as $forum)
                                        <option value="{{ $forum->id }}" {{ old('forum_id') == $forum->id ? 'selected' : '' }}>{{ $forum->name }}</option>
                                    @endforeach
                                </select>
                                @error('forum_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Project Details -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">Project Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}">
                                        <div class="form-text">E.g., Las Mercedes, Downtown, etc.</div>
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="usage" class="form-label">Usage</label>
                                        <input type="text" class="form-control @error('usage') is-invalid @enderror" id="usage" name="usage" value="{{ old('usage') }}">
                                        <div class="form-text">E.g., Residential, Commercial, Mixed, etc.</div>
                                        @error('usage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="floors" class="form-label">Floors</label>
                                        <input type="number" class="form-control @error('floors') is-invalid @enderror" id="floors" name="floors" value="{{ old('floors') }}" min="1">
                                        @error('floors')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                            <option value="">Select status</option>
                                            <option value="Proposed" {{ old('status') == 'Proposed' ? 'selected' : '' }}>Proposed</option>
                                            <option value="Approved" {{ old('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="Under Construction" {{ old('status') == 'Under Construction' ? 'selected' : '' }}>Under Construction (E/C)</option>
                                            <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="On Hold" {{ old('status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                            <option value="Cancelled" {{ old('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10" required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="images" class="form-label">Images</label>
                            <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*">
                            <div class="form-text">You can upload multiple images. Maximum 10 images, each up to 5MB.</div>
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('images.*')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div id="image-previews" class="row mt-3"></div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Thread</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize rich text editor for content
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor
                .create(document.querySelector('#content'))
                .catch(error => {
                    console.error(error);
                });
        }
    });
        
    // Preview images before upload
    document.getElementById('images').addEventListener('change', function(event) {
        // Remove existing previews
        const previewContainer = document.getElementById('image-previews');
        previewContainer.innerHTML = '';
        
        // Add previews for each file
        const files = event.target.files;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Only process image files
            if (!file.type.match('image.*')) {
                continue;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewCol = document.createElement('div');
                previewCol.className = 'col-md-3 mb-3';
                
                const previewCard = document.createElement('div');
                previewCard.className = 'card h-100';
                
                const previewImg = document.createElement('img');
                previewImg.src = e.target.result;
                previewImg.className = 'card-img-top';
                previewImg.style.height = '150px';
                previewImg.style.objectFit = 'cover';
                
                const previewBody = document.createElement('div');
                previewBody.className = 'card-body p-2';
                
                const previewText = document.createElement('p');
                previewText.className = 'card-text small text-truncate';
                previewText.textContent = file.name;
                
                previewBody.appendChild(previewText);
                previewCard.appendChild(previewImg);
                previewCard.appendChild(previewBody);
                previewCol.appendChild(previewCard);
                previewContainer.appendChild(previewCol);
            };
            
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
