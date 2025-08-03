{{--
    Thread Showcase Partial
    Hiển thị showcase liên quan đến thread với validation bảo mật

    Required variables:
    - $thread: Thread model instance with showcase relationship loaded
--}}

<!-- Related Showcase Section -->
@if($thread->shouldDisplayShowcase())
<div class="showcase-section mb-4">
    <div class="showcase-section-header">
        <h5 class="title_page_sub">
            <i class="fas fa-tools text-muted me-1 me-2"></i>
            {{ __('showcase.related') }}
        </h5>
        <!--small class="text-muted">{{ __('showcase.for_thread') }}</!smal-->
    </div>
    <div class="showcase-section-body">
        <div class="row">
            <!-- Showcase Image -->
            <div class="col-md-4 mb-3 mb-md-0">
                @if($thread->showcase->cover_image)
                    <img src="{{ asset($thread->showcase->cover_image) }}"
                            alt="{{ $thread->showcase->title }}"
                            class="img-fluid rounded shadow-sm"
                            style="width: 100%; height: 200px; object-fit: cover;"
                            onerror="this.src='{{ asset('images/placeholders/300x200.png') }}'">
                @elseif($thread->showcase->media && $thread->showcase->media->count() > 0)
                    @php
                        $firstMedia = $thread->showcase->media->first();
                    @endphp
                    <img src="{{ asset($firstMedia->file_path) }}"
                            alt="{{ $thread->showcase->title }}"
                            class="img-fluid rounded shadow-sm"
                            style="width: 100%; height: 200px; object-fit: cover;"
                            onerror="this.src='{{ asset('images/placeholders/300x200.png') }}'">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                            style="width: 100%; height: 200px;">
                        <i class="fas fa-image text-muted fs-1"></i>
                    </div>
                @endif
            </div>

            <!-- Showcase Content -->
            <div class="col-md-8">
                <h6 class="fw-bold mb-2">{{ $thread->showcase->title }}</h6>

                @if($thread->showcase->description)
                <p class="text-muted mb-3">{{ Str::limit($thread->showcase->description, 200) }}</p>
                @endif

                <!-- Showcase Meta -->
                <div class="showcase-meta mb-3">
                    <div class="d-flex align-items-center me-3">
                        <img src="{{ get_avatar_url($thread->showcase->user) }}"
                                alt="{{ $thread->showcase->user->name }}"
                                class="rounded-circle me-2"
                                width="24" height="24">
                        <small class="text-muted">
                            {{ __('common.labels.by') }}
                            <a href="{{ route('profile.show', $thread->showcase->user->id) }}"
                                class="text-decoration-none">
                                {{ $thread->showcase->user->name }}
                            </a>
                        </small>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        {{ $thread->showcase->created_at->diffForHumans() }}
                    </small>
                </div>

                <!-- Action Buttons -->
                <div class="showcase-actions">
                    <a href="{{ route('showcase.show', $thread->showcase) }}"
                        class="btn btn-main btn-sm">
                        <i class="fas fa-external-link-alt me-1"></i>
                        {{ __('ui.actions.view_full_showcase') }}
                    </a>

                    @if($thread->showcase->showcase_url)
                    <a href="{{ $thread->showcase->showcase_url }}"
                        target="_blank"
                        class="btn btn-main btn-sm ms-2">
                        <i class="fas fa-link me-1"></i>
                        {{ __('ui.actions.view_details') }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@else
<!-- Create Showcase Section -->
@auth
    @if($thread->userCanCreateShowcase(Auth::user()) && $thread->canCreateShowcase() && !$thread->showcase)
    <div class="create-showcase-section mb-4 d-flex justify-content-between align-items-center">
        <div class="create-showcase-left">
            <h5 class="create-showcase-title mb-0">
                <i class="fas fa-tools text-success me-2"></i>
                {{ __('showcase.create_from_thread') }}
            </h5>
            <small class="text-muted">{{ __('showcase.create_showcase_info') }}</small>
        </div>
        <div class="create-showcase-rigth">
            <button type="button" class="btn btn-main active" data-bs-toggle="modal" data-bs-target="#createShowcaseModal">
                <i class="fa-solid fa-file-circle-plus me-2"></i>
                {{ __('showcase.create_showcase_button') }}
            </button>
        </div>
    </div>

    <!-- Create Showcase Modal -->
    <div class="modal fade" id="createShowcaseModal" tabindex="-1" aria-labelledby="createShowcaseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content createShowcaseModal">
                <form id="createShowcaseForm" action="{{ route('threads.create-showcase', $thread) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createShowcaseModalLabel">
                            <i class="fas fa-tools text-success me-2"></i>
                            {{ __('showcase.create_from_thread_title') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Progress Steps -->
                        <div class="progress mb-4" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 33%" id="progressBar"></div>
                        </div>

                        <div class="steps-indicator mb-4">
                            <div class="d-flex justify-content-between">
                                <div class="step active" data-step="1">
                                    <div class="step-number">1</div>
                                    <div class="step-label">{{ __('showcase.basic_info') }}</div>
                                </div>
                                <div class="step" data-step="2">
                                    <div class="step-number">2</div>
                                    <div class="step-label">{{ __('showcase.content') }}</div>
                                </div>
                                <div class="step" data-step="3">
                                    <div class="step-number">3</div>
                                    <div class="step-label">{{ __('showcase.complete') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 1: Basic Information -->
                        <div class="step-content" id="step1">
                            <h6 class="mb-3">{{ __('showcase.step_1_title') }}</h6>

                            <div class="mb-3">
                                <label for="showcase_title" class="form-label">{{ __('showcase.showcase_title') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="showcase_title" name="title"
                                       value="{{ $thread->title }}" required>
                                <div class="form-text">{{ __('showcase.showcase_title_help') }}</div>
                            </div>

                            <div class="mb-3">
                                <label for="showcase_category" class="form-label">{{ __('showcase.category') }} <span class="text-danger">*</span></label>
                                <select class="form-select" id="showcase_category" name="showcase_category_id" required>
                                    <option value="">{{ __('showcase.select_category') }}</option>
                                    @foreach($showcaseCategories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $thread->category && $thread->category->name == $category->name ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="project_type" class="form-label">{{ __('showcase.project_type') }}</label>
                                <select class="form-select" id="project_type" name="showcase_type_id">
                                    <option value="">{{ __('showcase.select_project_type') }}</option>
                                    @foreach($showcaseTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Step 2: Content -->
                        <div class="step-content d-none" id="step2">
                            <h6 class="mb-3">{{ __('showcase.step_2_title') }}</h6>

                            <div class="mb-3">
                                <label for="showcase_description" class="form-label">{{ __('showcase.project_description') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="showcase_description" name="description" rows="6" required>{{ strip_tags($thread->content) }}</textarea>
                                <div class="form-text">{{ __('showcase.project_description_help') }}</div>
                            </div>

                            <!-- Cover Image Upload -->
                            <div class="mb-3">
                                <label for="cover_image" class="form-label">{{ __('showcase.cover_image') }}</label>
                                <div class="cover-image-upload">
                                    <!-- Current Thread Image Preview -->
                                    @if($thread->featured_image)
                                    <div class="current-image-preview mb-3">
                                        <small class="text-muted d-block mb-2">{{ __('showcase.current_thread_image') }}</small>
                                        <div class="position-relative d-inline-block">
                                            <img src="{{ asset($thread->featured_image) }}"
                                                 alt="{{ __('thread.image_alt.thread_image') }}"
                                                 class="img-thumbnail current-preview-image"
                                                 style="max-width: 200px; max-height: 150px; cursor: pointer;"
                                                 onclick="document.getElementById('cover_image').click()">
                                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50 opacity-0 hover-overlay rounded" style="transition: opacity 0.3s;">
                                                <i class="fas fa-camera text-white"></i>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">{{ __('showcase.click_to_change_image') }}</small>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- File Input -->
                                    <input type="file"
                                           class="form-control"
                                           id="cover_image"
                                           name="cover_image"
                                           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                           style="{{ $thread->featured_image ? 'display: none;' : '' }}">

                                    <!-- New Image Preview -->
                                    <div id="new-image-preview" class="mt-3" style="display: none;">
                                        <small class="text-muted d-block mb-2">{{ __('showcase.new_image_preview') }}</small>
                                        <img id="new-preview-image" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearNewImage()">
                                                <i class="fas fa-times me-1"></i>{{ __('showcase.remove_new_image') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text">{{ __('showcase.cover_image_help') }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="complexity_level" class="form-label">{{ __('showcase.complexity_level') }}</label>
                                        <select class="form-select" id="complexity_level" name="complexity_level">
                                            <option value="Beginner">{{ __('showcase.complexity_levels.beginner') }}</option>
                                            <option value="Intermediate" selected>{{ __('showcase.complexity_levels.intermediate') }}</option>
                                            <option value="Advanced">{{ __('showcase.complexity_levels.advanced') }}</option>
                                            <option value="Expert">{{ __('showcase.complexity_levels.expert') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="industry_application" class="form-label">{{ __('showcase.industry_application') }}</label>
                                        <input type="text" class="form-control" id="industry_application" name="industry_application"
                                               placeholder="{{ __('showcase.industry_placeholder') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- File Attachments Section -->
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-paperclip me-1"></i>
                                    {{ __('showcase.file_attachments') }}
                                    <small class="text-muted">({{ __('showcase.file_attachments_optional') }})</small>
                                </label>
                                <div class="file-upload-area border rounded p-3" id="fileUploadArea">
                                    <div class="upload-zone text-center py-3" id="uploadZone">
                                        <i class="fas fa-cloud-upload-alt fs-2 text-muted mb-2"></i>
                                        <p class="mb-2">{{ __('showcase.file_upload_area') }} <button type="button" class="btn btn-link p-0" id="browseFiles">{{ __('showcase.browse_files') }}</button></p>
                                        <small class="text-muted">
                                            {{ __('showcase.file_upload_help') }}<br>
                                            {{ __('showcase.file_upload_limits') }}
                                        </small>
                                        <input type="file" id="fileInput" name="attachments[]" multiple
                                               accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.dwg,.dxf,.step,.stp,.stl,.obj,.iges,.igs"
                                               style="display: none;">
                                    </div>

                                    <!-- File Preview Area -->
                                    <div class="file-previews mt-3" id="filePreviews" style="display: none;">
                                        <h6 class="mb-2">{{ __('showcase.files_selected') }}</h6>
                                        <div class="row" id="filePreviewContainer"></div>
                                    </div>
                                </div>
                                <div class="form-text">
                                    {{ __('showcase.file_upload_description') }}
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Confirmation -->
                        <div class="step-content d-none" id="step3">
                            <h6 class="mb-3">{{ __('showcase.step_3_title') }}</h6>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>{{ __('showcase.confirm_info') }}</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach(__('showcase.confirm_points') as $point)
                                    <li>{{ $point }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                                <label class="form-check-label" for="agree_terms">
                                    {{ __('showcase.agree_terms') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('showcase.previous') }}
                        </button>
                        <button type="button" class="btn btn-primary" id="nextBtn">
                            {{ __('showcase.next') }}<i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                            <i class="fas fa-star me-2"></i>{{ __('showcase.create_showcase') }}
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.actions.cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endauth
@endif

{{-- CSS cho showcase section --}}
@push('styles')
<style>
/* Showcase Section Styling */
.showcase-section {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    background-color: #f8f9fa;
    padding: 1.5rem;
}

.showcase-section-header .title_page_sub {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.showcase-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.showcase-actions .btn {
    font-size: 0.875rem;
}

/* Create Showcase Section */
.create-showcase-section {
    border: 1px solid #28a745;
    border-radius: 0.5rem;
    background-color: #f8fff9;
    padding: 1rem 1.5rem;
}

.create-showcase-title {
    color: #28a745;
    font-weight: 600;
}
</style>
@endpush




