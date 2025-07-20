@extends('layouts.app')

@section('title', 'Edit Profile - MechaMap')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.profile.index') }}">My Profile</a></li>
            <li class="breadcrumb-item active">Edit Profile</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-edit text-primary me-2"></i>
                        Edit Profile
                    </h1>
                    <p class="text-muted mb-0">Update your personal information and preferences</p>
                </div>
                <div>
                    <a href="{{ route('users.profile.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i>
                        Back to Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('users.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-user me-2"></i>
                            Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" 
                                           class="form-control @error('username') is-invalid @enderror" 
                                           id="username" 
                                           name="username" 
                                           value="{{ old('username', $user->username) }}" 
                                           required>
                                </div>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Professional Title</label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $user->title) }}" 
                                   placeholder="e.g., Mechanical Engineer, CAD Designer">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                      id="bio" 
                                      name="bio" 
                                      rows="4" 
                                      placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                            <div class="form-text">
                                <span id="bioCounter">{{ strlen($user->bio ?? '') }}</span>/500 characters
                            </div>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" 
                                       class="form-control @error('location') is-invalid @enderror" 
                                       id="location" 
                                       name="location" 
                                       value="{{ old('location', $user->location) }}" 
                                       placeholder="City, Country">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" 
                                       class="form-control @error('website') is-invalid @enderror" 
                                       id="website" 
                                       name="website" 
                                       value="{{ old('website', $user->website) }}" 
                                       placeholder="https://yourwebsite.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-briefcase me-2"></i>
                            Professional Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company" class="form-label">Company</label>
                                <input type="text" 
                                       class="form-control @error('company') is-invalid @enderror" 
                                       id="company" 
                                       name="company" 
                                       value="{{ old('company', $user->company) }}">
                                @error('company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="industry" class="form-label">Industry</label>
                                <select class="form-select @error('industry') is-invalid @enderror" 
                                        id="industry" 
                                        name="industry">
                                    <option value="">Select Industry</option>
                                    <option value="automotive" {{ old('industry', $user->industry) == 'automotive' ? 'selected' : '' }}>Automotive</option>
                                    <option value="aerospace" {{ old('industry', $user->industry) == 'aerospace' ? 'selected' : '' }}>Aerospace</option>
                                    <option value="manufacturing" {{ old('industry', $user->industry) == 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                    <option value="construction" {{ old('industry', $user->industry) == 'construction' ? 'selected' : '' }}>Construction</option>
                                    <option value="energy" {{ old('industry', $user->industry) == 'energy' ? 'selected' : '' }}>Energy</option>
                                    <option value="robotics" {{ old('industry', $user->industry) == 'robotics' ? 'selected' : '' }}>Robotics</option>
                                    <option value="medical" {{ old('industry', $user->industry) == 'medical' ? 'selected' : '' }}>Medical Devices</option>
                                    <option value="other" {{ old('industry', $user->industry) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('industry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="experience_years" class="form-label">Years of Experience</label>
                                <select class="form-select @error('experience_years') is-invalid @enderror" 
                                        id="experience_years" 
                                        name="experience_years">
                                    <option value="">Select Experience</option>
                                    <option value="0-1" {{ old('experience_years', $user->experience_years) == '0-1' ? 'selected' : '' }}>0-1 years</option>
                                    <option value="2-5" {{ old('experience_years', $user->experience_years) == '2-5' ? 'selected' : '' }}>2-5 years</option>
                                    <option value="6-10" {{ old('experience_years', $user->experience_years) == '6-10' ? 'selected' : '' }}>6-10 years</option>
                                    <option value="11-15" {{ old('experience_years', $user->experience_years) == '11-15' ? 'selected' : '' }}>11-15 years</option>
                                    <option value="16+" {{ old('experience_years', $user->experience_years) == '16+' ? 'selected' : '' }}>16+ years</option>
                                </select>
                                @error('experience_years')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="education_level" class="form-label">Education Level</label>
                                <select class="form-select @error('education_level') is-invalid @enderror" 
                                        id="education_level" 
                                        name="education_level">
                                    <option value="">Select Education</option>
                                    <option value="high_school" {{ old('education_level', $user->education_level) == 'high_school' ? 'selected' : '' }}>High School</option>
                                    <option value="associate" {{ old('education_level', $user->education_level) == 'associate' ? 'selected' : '' }}>Associate Degree</option>
                                    <option value="bachelor" {{ old('education_level', $user->education_level) == 'bachelor' ? 'selected' : '' }}>Bachelor's Degree</option>
                                    <option value="master" {{ old('education_level', $user->education_level) == 'master' ? 'selected' : '' }}>Master's Degree</option>
                                    <option value="phd" {{ old('education_level', $user->education_level) == 'phd' ? 'selected' : '' }}>PhD</option>
                                    <option value="other" {{ old('education_level', $user->education_level) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('education_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Skills -->
                        <div class="mb-3">
                            <label for="skills" class="form-label">Skills & Expertise</label>
                            <input type="text" 
                                   class="form-control @error('skills') is-invalid @enderror" 
                                   id="skills" 
                                   name="skills" 
                                   value="{{ old('skills', is_array($user->skills) ? implode(', ', $user->skills) : $user->skills) }}" 
                                   placeholder="e.g., CAD Design, SolidWorks, AutoCAD, 3D Printing">
                            <div class="form-text">Separate skills with commas</div>
                            @error('skills')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Interests -->
                        <div class="mb-3">
                            <label for="interests" class="form-label">Interests</label>
                            <input type="text" 
                                   class="form-control @error('interests') is-invalid @enderror" 
                                   id="interests" 
                                   name="interests" 
                                   value="{{ old('interests', is_array($user->interests) ? implode(', ', $user->interests) : $user->interests) }}" 
                                   placeholder="e.g., Robotics, Automation, Renewable Energy">
                            <div class="form-text">Separate interests with commas</div>
                            @error('interests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-link me-2"></i>
                            Social Links
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="linkedin" class="form-label">LinkedIn</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bxl-linkedin"></i></span>
                                    <input type="url" 
                                           class="form-control @error('social_links.linkedin') is-invalid @enderror" 
                                           id="linkedin" 
                                           name="social_links[linkedin]" 
                                           value="{{ old('social_links.linkedin', $user->social_links['linkedin'] ?? '') }}" 
                                           placeholder="https://linkedin.com/in/username">
                                </div>
                                @error('social_links.linkedin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="twitter" class="form-label">Twitter</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bxl-twitter"></i></span>
                                    <input type="url" 
                                           class="form-control @error('social_links.twitter') is-invalid @enderror" 
                                           id="twitter" 
                                           name="social_links[twitter]" 
                                           value="{{ old('social_links.twitter', $user->social_links['twitter'] ?? '') }}" 
                                           placeholder="https://twitter.com/username">
                                </div>
                                @error('social_links.twitter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="github" class="form-label">GitHub</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bxl-github"></i></span>
                                    <input type="url" 
                                           class="form-control @error('social_links.github') is-invalid @enderror" 
                                           id="github" 
                                           name="social_links[github]" 
                                           value="{{ old('social_links.github', $user->social_links['github'] ?? '') }}" 
                                           placeholder="https://github.com/username">
                                </div>
                                @error('social_links.github')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="portfolio" class="form-label">Portfolio</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-briefcase"></i></span>
                                    <input type="url" 
                                           class="form-control @error('social_links.portfolio') is-invalid @enderror" 
                                           id="portfolio" 
                                           name="social_links[portfolio]" 
                                           value="{{ old('social_links.portfolio', $user->social_links['portfolio'] ?? '') }}" 
                                           placeholder="https://yourportfolio.com">
                                </div>
                                @error('social_links.portfolio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Profile Picture -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-image me-2"></i>
                            Profile Picture
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="profile-picture-container mb-3">
                            <img src="{{ $user->getAvatarUrl() }}" 
                                 alt="Profile Picture" 
                                 class="profile-picture rounded-circle" 
                                 id="profilePicturePreview">
                            <div class="profile-picture-overlay">
                                <button type="button" class="btn btn-sm btn-light rounded-circle" 
                                        onclick="document.getElementById('profilePicture').click()">
                                    <i class="bx bx-camera"></i>
                                </button>
                            </div>
                        </div>
                        <input type="file" 
                               class="d-none" 
                               id="profilePicture" 
                               name="profile_picture" 
                               accept="image/*" 
                               onchange="previewProfilePicture(this)">
                        <div class="form-text">
                            Maximum file size: 2MB<br>
                            Supported formats: JPG, PNG, GIF
                        </div>
                    </div>
                </div>

                <!-- Privacy Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-shield me-2"></i>
                            Privacy Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="email_public" name="email_public" 
                                   {{ old('email_public', $user->email_public) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_public">
                                Make email address public
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="profile_public" name="profile_public" 
                                   {{ old('profile_public', $user->profile_public) ? 'checked' : '' }}>
                            <label class="form-check-label" for="profile_public">
                                Make profile publicly visible
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="show_online_status" name="show_online_status" 
                                   {{ old('show_online_status', $user->show_online_status) ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_online_status">
                                Show online status
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Notification Preferences -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-bell me-2"></i>
                            Notification Preferences
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" 
                                   {{ old('email_notifications', $user->email_notifications) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_notifications">
                                Email notifications
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="forum_notifications" name="forum_notifications" 
                                   {{ old('forum_notifications', $user->forum_notifications) ? 'checked' : '' }}>
                            <label class="form-check-label" for="forum_notifications">
                                Forum activity notifications
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="marketplace_notifications" name="marketplace_notifications" 
                                   {{ old('marketplace_notifications', $user->marketplace_notifications) ? 'checked' : '' }}>
                            <label class="form-check-label" for="marketplace_notifications">
                                Marketplace notifications
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Save Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>
                                Save Changes
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="bx bx-reset me-1"></i>
                                Reset Form
                            </button>
                            <a href="{{ route('users.profile.index') }}" class="btn btn-outline-danger">
                                <i class="bx bx-x me-1"></i>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.profile-picture-container {
    position: relative;
    display: inline-block;
}

.profile-picture {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border: 3px solid #dee2e6;
}

.profile-picture-overlay {
    position: absolute;
    bottom: 0;
    right: 0;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.profile-picture-container:hover .profile-picture-overlay {
    opacity: 1;
}

.form-check-label {
    cursor: pointer;
}

#bioCounter {
    font-weight: 500;
}

@media (max-width: 768px) {
    .profile-picture {
        width: 100px;
        height: 100px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Bio character counter
document.getElementById('bio').addEventListener('input', function() {
    const counter = document.getElementById('bioCounter');
    counter.textContent = this.value.length;
    
    if (this.value.length > 500) {
        counter.classList.add('text-danger');
    } else {
        counter.classList.remove('text-danger');
    }
});

// Profile picture preview
function previewProfilePicture(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePicturePreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Reset form
function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        document.getElementById('profileForm').reset();
        document.getElementById('profilePicturePreview').src = '{{ $user->getAvatarUrl() }}';
        document.getElementById('bioCounter').textContent = '{{ strlen($user->bio ?? '') }}';
    }
}

// Form validation
document.getElementById('profileForm').addEventListener('submit', function(e) {
    const bio = document.getElementById('bio').value;
    if (bio.length > 500) {
        e.preventDefault();
        alert('Bio must be 500 characters or less.');
        return false;
    }
});

// Auto-save draft functionality
let autoSaveTimer;
function autoSave() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => {
        const formData = new FormData(document.getElementById('profileForm'));
        formData.append('_method', 'PUT');
        formData.append('auto_save', 'true');
        
        fetch('{{ route("users.profile.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show subtle indication that draft was saved
                const saveBtn = document.querySelector('button[type="submit"]');
                const originalText = saveBtn.innerHTML;
                saveBtn.innerHTML = '<i class="bx bx-check me-1"></i>Draft Saved';
                setTimeout(() => {
                    saveBtn.innerHTML = originalText;
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Auto-save error:', error);
        });
    }, 30000); // Auto-save every 30 seconds
}

// Trigger auto-save on form changes
document.querySelectorAll('#profileForm input, #profileForm textarea, #profileForm select').forEach(element => {
    element.addEventListener('input', autoSave);
    element.addEventListener('change', autoSave);
});
</script>
@endpush
