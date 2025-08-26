@extends('layouts.app')

@section('title', __('forum.create.title'))

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/thread-form.css') }}">
@endpush

@push('scripts')
<script>
// Translation keys for JavaScript
window.threadTranslations = {
    validation: {
        titleRequired: '{{ __('thread.validation.title_required') }}',
        categoryRequired: '{{ __('thread.validation.category_required') }}',
        forumRequired: '{{ __('thread.validation.forum_required') }}',
        contentRequired: '{{ __('thread.validation.content_required') }}',
        pollQuestionRequired: '{{ __('thread.validation.poll_question_required') }}',
        pollMinOptions: '{{ __('thread.validation.poll_min_options') }}',
        checkInfo: '{{ __('thread.validation.check_info') }}',
        selectExistingShowcase: '{{ __('thread.validation.select_existing_showcase') }}'
    },
    file: {
        maxFilesError: '{{ __('thread.file.max_files_error') }}',
        sizeError: '{{ __('thread.file.size_error') }}',
        typeError: '{{ __('thread.file.type_error') }}'
    },
    nav: {
        creating: '{{ __('thread.nav.creating') }}'
    }
};
</script>
@endpush

@section('content')
<!-- Header Section -->
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title mb-2">
                <i class="fas fa-plus-circle"></i>
                {{ __('forum.create.title') }}
            </h1>
            <p class="page-subtitle text-muted mb-0">{{ __('forum.create.subtitle') }}</p>
        </div>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-modern">
                <i class="fas fa-arrow-left"></i>
                <span>{{ __('forum.create.back') }}</span>
            </a>
        </div>
    </div>
</div>

<!-- Progress Indicator với accessibility cải thiện -->
<div class="progress-container" role="progressbar" aria-label="{{ __('forum.create.progress_label') }}" aria-valuemin="0" aria-valuemax="5"
    aria-valuenow="1">
    <div class="progress-steps">
        <div class="progress-line" id="progress-line"></div>
        <button type="button" class="step-item active" data-step="basic" aria-current="step"
            aria-label="{{ __('forum.create.step_basic_aria') }}">
            <div class="step-number">1</div>
            <div class="step-text">{{ __('forum.create.step_basic') }}</div>
        </button>
        <button type="button" class="step-item" data-step="content" aria-label="{{ __('forum.create.step_content_aria') }}" disabled>
            <div class="step-number">2</div>
            <div class="step-text">{{ __('forum.create.step_content') }}</div>
        </button>
        <button type="button" class="step-item" data-step="showcase" aria-label="{{ t_common("thread_showcase.step_aria") }}" disabled>
            <div class="step-number">3</div>
            <div class="step-text">{{ t_common("thread_showcase.step_title") }}</div>
        </button>
        <button type="button" class="step-item" data-step="poll" aria-label="{{ __('forum.create.step_poll_aria') }}" disabled>
            <div class="step-number">4</div>
            <div class="step-text">{{ __('forum.create.step_poll') }}</div>
        </button>
        <button type="button" class="step-item" data-step="review" aria-label="{{ __('forum.create.step_review_aria') }}" disabled>
            <div class="step-number">5</div>
            <div class="step-text">{{ __('forum.create.step_review') }}</div>
        </button>
    </div>
</div>

<!-- Main Content -->
<form action="{{ route('threads.store') }}" method="POST" enctype="multipart/form-data" class="modern-form"
    id="thread-form">
    @csrf

    <!-- Step 1: Basic Information -->
    <div class="form-step active" id="step-basic">
        <div class="form-card">
            <div class="card-header-modern">
                <div class="card-title">
                    <i class="info-circle"></i>
                    <span>{{ __('forum.create.step_basic') }}</span>
                </div>
                <div class="card-subtitle">{{ __('forum.create.basic_info_subtitle') }}</div>
            </div>
            <div class="card-body-modern">
                <div class="form-group-modern">
                    <label for="title" class="form-label-modern">
                        <span class="label-text">{{ __('forum.create.thread_title') }}</span>
                        <span class="label-required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="text" class="form-control-modern @error('title') is-invalid @enderror" id="title"
                            name="title" value="{{ old('title') }}" required
                            placeholder="{{ __('forum.create.title_placeholder') }}">
                        <div class="input-icon">
                            <i class="fas fa-edit-square"></i>
                        </div>
                    </div>
                    <div class="form-help-text">
                        <i class="lightbulb"></i>
                        <span>{{ __('forum.create.title_help') }}</span>
                    </div>
                    @error('title')
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group-modern half-width">
                        <label for="category_id" class="form-label-modern">
                            <span class="label-text">{{ __('forum.create.category_label') }}</span>
                            <span class="label-required">*</span>
                        </label>
                        <div class="select-wrapper">
                            <select class="form-select-modern @error('category_id') is-invalid @enderror"
                                id="category_id" name="category_id" required>
                                <option value="">{{ __('forum.create.select_category') }}</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id')==$category->id ?
                                    'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            <div class="select-icon">
                                <i class="chevron-down"></i>
                            </div>
                        </div>
                        @error('category_id')
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group-modern half-width">
                        <label for="forum_id" class="form-label-modern">
                            <span class="label-text">{{ __('forum.create.forum_label') }}</span>
                            <span class="label-required">*</span>
                        </label>
                        <div class="select-wrapper">
                            <select class="form-select-modern @error('forum_id') is-invalid @enderror" id="forum_id"
                                name="forum_id" required>
                                <option value="">{{ __('forum.create.select_forum') }}</option>
                                @foreach($forums as $forum)
                                <option value="{{ $forum->id }}" {{ old('forum_id')==$forum->id ? 'selected' :
                                    '' }}>
                                    {{ $forum->name }}
                                </option>
                                @endforeach
                            </select>
                            <div class="select-icon">
                                <i class="chevron-down"></i>
                            </div>
                        </div>
                        @error('forum_id')
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="step-navigation">
            <button type="button" class="btn btn-secondary btn-modern" disabled>
                <i class="chevron-left"></i>
                <span>{{ __('forum.create.previous') }}</span>
            </button>
            <button type="button" class="btn btn-primary btn-modern" onclick="nextStep()">
                <span>{{ __('forum.create.next') }}</span>
                <i class="chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 2: Content -->
    <div class="form-step" id="step-content">
        <div class="form-card">
            <div class="card-header-modern">
                <div class="card-title">
                    <i class="file-text"></i>
                    <span>{{ __('forum.create.step_content') }}</span>
                </div>
                <div class="card-subtitle">{{ __('forum.create.content_subtitle') }}</div>
            </div>
            <div class="card-body-modern">
                <div class="form-group-modern">
                    <label for="content" class="form-label-modern">
                        <span class="label-text">{{ __('forum.create.content_label') }}</span>
                        <span class="label-required">*</span>
                    </label>
                    <div class="editor-wrapper">
                        <x-tinymce-editor
                            name="content"
                            id="content"
                            :value="old('content')"
                            placeholder="{{ __('forum.create.content_placeholder') }}"
                            context="admin"
                            :height="400"
                            :required="true"
                            class="@error('content') is-invalid @enderror"
                        />
                    </div>
                    <div class="form-help-text">
                        <i class="magic"></i>
                        <span>{{ __('forum.create.content_help') }}</span>
                    </div>
                    @error('content')
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group-modern">
                    <label for="images" class="form-label-modern">
                        <span class="label-text">
                            <i class="images"></i>
                            {{ __('thread.upload.image_label') }}
                        </span>
                    </label>
                    <div class="upload-area" id="upload-area">
                        <div class="upload-content">
                            <div class="upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="upload-text">
                                <h4>{{ __('thread.upload.drag_drop_title') }}</h4>
                                <p>{{ __('thread.upload.or_select') }} <span class="upload-link">{{ __('thread.upload.select_files') }}</span> {{ __('thread.upload.from_computer') }}</p>
                            </div>
                            <input type="file" class="upload-input @error('images') is-invalid @enderror" id="images"
                                name="images[]" multiple accept="image/*">
                        </div>
                    </div>
                    <div class="form-help-text">
                        <i class="info-circle"></i>
                        <span>{{ __('thread.upload.help_text') }}</span>
                    </div>
                    @error('images')
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $message }}
                    </div>
                    @enderror
                    @error('images.*')
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div id="image-previews" class="image-gallery"></div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="step-navigation">
            <button type="button" class="btn btn-secondary btn-modern" onclick="prevStep()">
                <i class="chevron-left"></i>
                <span>{{ __('thread.nav.previous') }}</span>
            </button>
            <button type="button" class="btn btn-primary btn-modern" onclick="nextStep()">
                <span>{{ __('thread.nav.next') }}</span>
                <i class="chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 3: Showcase (Optional) -->
    <div class="form-step" id="step-showcase">
        <div class="form-card">
            <div class="card-header-modern">
                <div class="card-title">
                    <i class="fas fa-star"></i>
                    <span>{{ t_common("thread_showcase.step_title") }}</span>
                </div>
                <div class="card-subtitle">{{ t_common("thread_showcase.step_description") }}</div>
            </div>
            <div class="card-body-modern">
                <!-- Showcase Toggle -->
                <div class="showcase-toggle">
                    <div class="toggle-wrapper">
                        <input type="checkbox" class="toggle-input" id="create_showcase" name="create_showcase" value="1" {{ old('create_showcase') ? 'checked' : '' }}>
                        <label for="create_showcase" class="toggle-label">
                            <div class="toggle-switch">
                                <div class="toggle-slider"></div>
                            </div>
                            <div class="toggle-text">
                                <span class="toggle-title">{{ t_common("thread_showcase.enable_showcase") }}</span>
                                <span class="toggle-subtitle">{{ t_common("thread_showcase.enable_showcase_help") }}</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Showcase Content (Hidden by default) -->
                <div class="showcase-content" style="{{ old('create_showcase') ? '' : 'display: none;' }}">
                    <!-- Showcase Type Selection -->
                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <span class="label-text">Loại Showcase</span>
                        </label>
                        <div class="showcase-type-selection">
                            <div class="radio-group">
                                <div class="radio-item">
                                    <input type="radio" id="showcase_type_new" name="showcase_type" value="new" {{ old('showcase_type', 'new') == 'new' ? 'checked' : '' }}>
                                    <label for="showcase_type_new" class="radio-label">
                                        <div class="radio-content">
                                            <i class="fas fa-plus-circle"></i>
                                            <div class="radio-text">
                                                <span class="radio-title">{{ t_common("thread_showcase.create_new") }}</span>
                                                <span class="radio-subtitle">Tạo showcase mới từ chủ đề này</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" id="showcase_type_existing" name="showcase_type" value="existing" {{ old('showcase_type') == 'existing' ? 'checked' : '' }}>
                                    <label for="showcase_type_existing" class="radio-label">
                                        <div class="radio-content">
                                            <i class="fas fa-link"></i>
                                            <div class="radio-text">
                                                <span class="radio-title">{{ t_common("thread_showcase.attach_existing") }}</span>
                                                <span class="radio-subtitle">Đính kèm showcase đã có sẵn</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Showcase Selection -->
                    <div class="existing-showcase-section" style="{{ old('showcase_type') == 'existing' ? '' : 'display: none;' }}">
                        <div class="form-group-modern">
                            <label for="existing_showcase_id" class="form-label-modern">
                                <span class="label-text">{{ t_common("thread_showcase.select_existing") }}</span>
                                <span class="label-required">*</span>
                            </label>
                            <div class="select-wrapper">
                                <select class="form-select-modern" id="existing_showcase_id" name="existing_showcase_id">
                                    <option value="">Chọn showcase</option>
                                    @if(auth()->user()->showcaseItems->count() > 0)
                                        @foreach(auth()->user()->showcaseItems as $showcase)
                                            <option value="{{ $showcase->id }}" {{ old('existing_showcase_id') == $showcase->id ? 'selected' : '' }}>
                                                {{ $showcase->title }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="select-icon">
                                    <i class="chevron-down"></i>
                                </div>
                            </div>
                            @if(auth()->user()->showcaseItems->count() == 0)
                                <div class="form-help-text">
                                    <i class="info-circle"></i>
                                    <span>{{ t_common("thread_showcase.no_existing_showcases") }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- New Showcase Form -->
                    <div class="new-showcase-section" style="{{ old('showcase_type', 'new') == 'new' ? '' : 'display: none;' }}">
                        <!-- Showcase Title -->
                        <div class="form-group-modern">
                            <label for="showcase_title" class="form-label-modern">
                                <span class="label-text">{{ t_common("thread_showcase.showcase_title") }}</span>
                                <span class="label-required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text" class="form-control-modern" id="showcase_title" name="showcase_title"
                                       value="{{ old('showcase_title') }}"
                                       placeholder="{{ t_common("thread_showcase.showcase_title_placeholder") }}">
                                <div class="input-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Showcase Description -->
                        <div class="form-group-modern">
                            <label for="showcase_description" class="form-label-modern">
                                <span class="label-text">{{ t_common("thread_showcase.showcase_description") }}</span>
                                <span class="label-required">*</span>
                            </label>
                            <x-tinymce-editor
                                name="showcase_description"
                                id="showcase_description"
                                :value="old('showcase_description')"
                                :placeholder="t_common("thread_showcase.showcase_description_placeholder")"
                                context="showcase"
                                :height="200"
                                :required="false"
                                class="form-control-modern"
                            />
                        </div>

                        <!-- Project Details Row -->
                        <div class="form-row">
                            <div class="form-group-modern half-width">
                                <label for="project_type" class="form-label-modern">
                                    <span class="label-text">{{ t_common("thread_showcase.project_type") }}</span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" class="form-control-modern" id="project_type" name="project_type"
                                           value="{{ old('project_type') }}"
                                           placeholder="{{ t_common("thread_showcase.project_type_placeholder") }}">
                                    <div class="input-icon">
                                        <i class="fas fa-cogs"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group-modern half-width">
                                <label for="complexity_level" class="form-label-modern">
                                    <span class="label-text">{{ t_common("thread_showcase.complexity_level") }}</span>
                                </label>
                                <div class="select-wrapper">
                                    <select class="form-select-modern" id="complexity_level" name="complexity_level">
                                        <option value="">Chọn độ phức tạp</option>
                                        <option value="Beginner" {{ old('complexity_level') == 'Beginner' ? 'selected' : '' }}>Cơ bản</option>
                                        <option value="Intermediate" {{ old('complexity_level') == 'Intermediate' ? 'selected' : '' }}>Trung bình</option>
                                        <option value="Advanced" {{ old('complexity_level') == 'Advanced' ? 'selected' : '' }}>Nâng cao</option>
                                        <option value="Expert" {{ old('complexity_level') == 'Expert' ? 'selected' : '' }}>Chuyên gia</option>
                                    </select>
                                    <div class="select-icon">
                                        <i class="chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Attachments Section -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <span class="label-text">
                                    <i class="fas fa-paperclip me-1"></i>
                                    {{ __('showcase.file_attachments') }}
                                </span>
                                <span class="label-optional">({{ __('showcase.file_attachments_optional') }})</span>
                            </label>
                            <div class="file-upload-area-modern" id="showcaseFileUploadArea">
                                <div class="upload-zone-modern" id="showcaseUploadZone">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <div class="upload-text">
                                        <p class="upload-primary">{{ __('showcase.file_upload_area') }} <button type="button" class="btn-link-modern" id="showcaseBrowseFiles">{{ __('showcase.browse_files') }}</button></p>
                                        <p class="upload-secondary">
                                            {{ __('showcase.file_upload_help') }}<br>
                                            {{ __('showcase.file_upload_limits') }}
                                        </p>
                                    </div>
                                    <input type="file" id="showcaseFileInput" name="showcase_attachments[]" multiple
                                           accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.dwg,.dxf,.step,.stp,.stl,.obj,.iges,.igs"
                                           style="display: none;">
                                </div>

                                <!-- File Preview Area -->
                                <div class="file-previews-modern" id="showcaseFilePreviews" style="display: none;">
                                    <div class="file-previews-header">
                                        <h6 class="file-previews-title">{{ __('showcase.files_selected') }}</h6>
                                    </div>
                                    <div class="file-previews-grid" id="showcaseFilePreviewContainer"></div>
                                </div>
                            </div>
                            <div class="form-help">
                                {{ __('showcase.file_upload_description') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="step-navigation">
            <button type="button" class="btn btn-secondary btn-modern" onclick="prevStep()">
                <i class="chevron-left"></i>
                <span>{{ __('thread.nav.previous') }}</span>
            </button>
            <button type="button" class="btn btn-primary btn-modern" onclick="nextStep()">
                <span>{{ __('thread.nav.next') }}</span>
                <i class="chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 4: Poll (Optional) -->
    <div class="form-step" id="step-poll">
        <div class="form-card">
            <div class="card-header-modern">
                <div class="card-title">
                    <i class="bar-chart"></i>
                    <span>{{ __('thread.poll.title') }}</span>
                </div>
                <div class="card-subtitle">{{ __('thread.poll.subtitle') }}</div>
            </div>
            <div class="card-body-modern">
                <div class="poll-toggle">
                    <div class="toggle-wrapper">
                        <input type="checkbox" class="toggle-input" id="has_poll" name="has_poll" value="1" {{
                            old('has_poll') ? 'checked' : '' }}>
                        <label for="has_poll" class="toggle-label">
                            <div class="toggle-switch">
                                <div class="toggle-slider"></div>
                            </div>
                            <div class="toggle-text">
                                <span class="toggle-title">{{ __('thread.poll.enable_title') }}</span>
                                <span class="toggle-subtitle">{{ __('thread.poll.enable_subtitle') }}</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="poll-content" style="{{ old('has_poll') ? '' : 'display: none;' }}">
                    <div class="form-group-modern">
                        <label for="poll_question" class="form-label-modern">
                            <span class="label-text">{{ __('thread.poll.question_label') }}</span>
                            <span class="label-required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <input type="text" class="form-control-modern @error('poll_question') is-invalid @enderror"
                                id="poll_question" name="poll_question" value="{{ old('poll_question') }}"
                                placeholder="{{ __('forum.poll.question_placeholder') }}">
                            <div class="input-icon">
                                <i class="question-circle"></i>
                            </div>
                        </div>
                        @error('poll_question')
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <span class="label-text">{{ __('thread.poll.options_label') }}</span>
                            <span class="label-required">*</span>
                        </label>
                        <div class="poll-options-container" id="poll-options">
                            <div class="poll-option-item">
                                <div class="option-input-wrapper">
                                    <input type="text"
                                        class="form-control-modern @error('poll_options.0') is-invalid @enderror"
                                        name="poll_options[]" value="{{ old('poll_options.0') }}"
                                        placeholder="{{ __('forum.poll.option_placeholder', ['number' => 1]) }}">
                                    <button type="button" class="remove-option-btn" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="poll-option-item">
                                <div class="option-input-wrapper">
                                    <input type="text"
                                        class="form-control-modern @error('poll_options.1') is-invalid @enderror"
                                        name="poll_options[]" value="{{ old('poll_options.1') }}"
                                        placeholder="{{ __('forum.poll.option_placeholder', ['number' => 2]) }}">
                                    <button type="button" class="remove-option-btn" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @if(old('poll_options'))
                            @foreach(old('poll_options') as $index => $option)
                            @if($index > 1)
                            <div class="poll-option-item">
                                <div class="option-input-wrapper">
                                    <input type="text"
                                        class="form-control-modern @error('poll_options.'.$index) is-invalid @enderror"
                                        name="poll_options[]" value="{{ $option }}"
                                        placeholder="{{ __('forum.poll.option_placeholder', ['number' => $index + 1]) }}">
                                    <button type="button" class="remove-option-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @endif
                            @endforeach
                            @endif
                        </div>
                        <button type="button" class="add-option-btn" id="add-option">
                            <i class="fas fa-plus-circle"></i>
                            <span>{{ __('thread.poll.add_option') }}</span>
                        </button>
                        @error('poll_options')
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="poll-settings">
                        <div class="settings-group">
                            <div class="setting-title">Số lựa chọn tối đa</div>
                            <div class="radio-group">
                                <label class="radio-item">
                                    <input type="radio" name="poll_max_options" value="1" {{ old('poll_max_options',
                                        1)==1 ? 'checked' : '' }}>
                                    <span class="radio-check"></span>
                                    <span class="radio-text">Chỉ một lựa chọn</span>
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="poll_max_options" value="0" {{
                                        old('poll_max_options')==='0' ? 'checked' : '' }}>
                                    <span class="radio-check"></span>
                                    <span class="radio-text">Không giới hạn</span>
                                </label>
                            </div>
                        </div>

                        <div class="settings-group">
                            <div class="setting-title">Tùy chọn khảo sát</div>
                            <div class="checkbox-group">
                                <label class="checkbox-item">
                                    <input type="checkbox" name="poll_allow_change_vote" value="1" {{
                                        old('poll_allow_change_vote', 1) ? 'checked' : '' }}>
                                    <span class="checkbox-check">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <span class="checkbox-text">Cho phép thay đổi lựa chọn</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="poll_show_votes_publicly" value="1" {{
                                        old('poll_show_votes_publicly') ? 'checked' : '' }}>
                                    <span class="checkbox-check">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <span class="checkbox-text">Hiển thị kết quả công khai</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="poll_allow_view_without_vote" value="1" {{
                                        old('poll_allow_view_without_vote', 1) ? 'checked' : '' }}>
                                    <span class="checkbox-check">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <span class="checkbox-text">Cho phép xem kết quả mà không cần bình
                                        chọn</span>
                                </label>
                            </div>
                        </div>

                        <div class="settings-group">
                            <div class="setting-title">Thời gian đóng khảo sát</div>
                            <label class="checkbox-item">
                                <input type="checkbox" id="poll_has_close_date" name="poll_has_close_date" value="1" {{
                                    old('poll_has_close_date') ? 'checked' : '' }}>
                                <span class="checkbox-check">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span class="checkbox-text">Đóng khảo sát sau</span>
                            </label>
                            <div class="poll-close-settings"
                                style="{{ old('poll_has_close_date') ? '' : 'display: none;' }}">
                                <div class="number-input-group">
                                    <button type="button" class="number-btn decrease" id="decrease-days">
                                        <i class="dash"></i>
                                    </button>
                                    <input type="number" class="number-input" id="poll_close_after_days"
                                        name="poll_close_after_days" value="{{ old('poll_close_after_days', 7) }}"
                                        min="1" max="365">
                                    <button type="button" class="number-btn increase" id="increase-days">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <span class="number-unit">ngày</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="step-navigation">
            <button type="button" class="btn btn-secondary btn-modern" onclick="prevStep()">
                <i class="chevron-left"></i>
                <span>{{ __('thread.nav.previous') }}</span>
            </button>
            <button type="button" class="btn btn-primary btn-modern" onclick="nextStep()">
                <span>{{ __('thread.nav.next') }}</span>
                <i class="chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 5: Review -->
    <div class="form-step" id="step-review">
        <div class="form-card">
            <div class="card-header-modern">
                <div class="card-title">
                    <i class="fas fa-eye"></i>
                    <span>Xem Lại Thông Tin</span>
                </div>
                <div class="card-subtitle">Kiểm tra lại tất cả thông tin trước khi tạo chủ đề</div>
            </div>
            <div class="card-body-modern">
                <div class="review-section">
                    <div class="review-item">
                        <div class="review-label">Tiêu đề:</div>
                        <div class="review-value" id="review-title">-</div>
                    </div>
                    <div class="review-item">
                        <div class="review-label">Danh mục:</div>
                        <div class="review-value" id="review-category">-</div>
                    </div>
                    <div class="review-item">
                        <div class="review-label">Diễn đàn:</div>
                        <div class="review-value" id="review-forum">-</div>
                    </div>
                    <div class="review-item">
                        <div class="review-label">Nội dung:</div>
                        <div class="review-value content-preview" id="review-content">-</div>
                    </div>
                    <div class="review-item" id="review-showcase-section" style="display: none;">
                        <div class="review-label">Showcase:</div>
                        <div class="review-value" id="review-showcase">Không có showcase</div>
                    </div>
                    <div class="review-item">
                        <div class="review-label">Hình ảnh:</div>
                        <div class="review-value" id="review-images">Không có hình ảnh</div>
                    </div>
                    <div class="review-item">
                        <div class="review-label">Khảo sát:</div>
                        <div class="review-value" id="review-poll">Không có khảo sát</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Final Submit -->
        <div class="final-submit">
            <button type="button" class="btn btn-secondary btn-modern" onclick="prevStep()">
                <i class="chevron-left"></i>
                <span>{{ __('forum.create.previous') }}</span>
            </button>
            <button type="submit" class="btn btn-success btn-modern btn-submit">
                <i class="fas fa-check-circle"></i>
                <span>{{ __('forum.create.create_button') }}</span>
            </button>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    // Biến global để quản lý multi-step form
let currentStepIndex = 0;
const steps = ['basic', 'content', 'showcase', 'poll', 'review'];

// Khởi tạo khi trang load
document.addEventListener('DOMContentLoaded', function() {
    initializeForm();
    initializeEditor();
    initializeUploadArea();
    initializePollFeatures();
    initializeShowcaseFeatures();
    initShowcaseFileUpload();
    initializeValidation();
    initializeMultiStepForm();
    enhanceAccessibility();
});

// Khởi tạo multi-step form
function initializeMultiStepForm() {
    // Initialize step display
    showStep(currentStepIndex);
    updateStepProgress();

    // Add click handlers for step navigation
    document.querySelectorAll('.step-item').forEach((item, index) => {
        item.addEventListener('click', function() {
            if (index <= currentStepIndex || validateStepsUpTo(index - 1)) {
                currentStepIndex = index;
                showStep(currentStepIndex);
                updateStepProgress();
            }
        });
    });
}

// Validate steps up to a certain index
function validateStepsUpTo(targetIndex) {
    for (let i = 0; i <= targetIndex; i++) {
        const step = steps[i];
        switch (step) {
            case 'basic':
                if (!validateBasicInfo()) return false;
                break;
            case 'content':
                if (!validateContent()) return false;
                break;
            case 'poll':
                if (!validatePoll()) return false;
                break;
        }
    }
    return true;
}

// Khởi tạo form và các chức năng cơ bản
function initializeForm() {
    // Auto-select forum if provided in URL
    const urlParams = new URLSearchParams(window.location.search);
    const forumId = urlParams.get('forum_id');
    if (forumId) {
        const forumSelect = document.getElementById('forum_id');
        if (forumSelect) {
            forumSelect.value = forumId;
        }
    }

    // Update progress on input change
    document.querySelectorAll('input, select, textarea').forEach(element => {
        element.addEventListener('input', updateProgress);
        element.addEventListener('change', updateProgress);
    });
}

// TinyMCE is now initialized via the component
// Listen for TinyMCE content changes for progress tracking
function initializeEditor() {
    // Wait for TinyMCE to be initialized
    const checkTinyMCE = setInterval(() => {
        const editor = tinymce.get('content');
        if (editor) {
            clearInterval(checkTinyMCE);

            // Listen for content changes
            editor.on('input keyup change', () => {
                updateProgress();
            });

            window.contentEditor = editor;
            console.log({!! json_encode(__('thread.console.tinymce_ready')) !!});
        }
    }, 100);
}

// Khởi tạo khu vực upload
function initializeUploadArea() {
    const uploadArea = document.getElementById('upload-area');
    const fileInput = document.getElementById('images');
    const uploadLink = uploadArea.querySelector('.upload-link');

    // Click to browse files
    uploadLink.addEventListener('click', (e) => {
        e.preventDefault();
        fileInput.click();
    });

    uploadArea.addEventListener('click', () => {
        fileInput.click();
    });

    // Drag and drop functionality
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleImageFiles(files);
        }
    });

    // Handle file selection
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleImageFiles(e.target.files);
        }
    });
}

// Xử lý files hình ảnh
function handleImageFiles(files) {
    const previewContainer = document.getElementById('image-previews');
    previewContainer.innerHTML = '';

    if (files.length === 0) return;

    previewContainer.classList.add('active');

    Array.from(files).forEach((file, index) => {
        if (!file.type.match('image.*')) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const imageItem = createImagePreview(file, e.target.result, index);
            previewContainer.appendChild(imageItem);
        };
        reader.readAsDataURL(file);
    });

    updateProgress();
}

// Tạo preview cho hình ảnh
function createImagePreview(file, src, index) {
    const imageItem = document.createElement('div');
    imageItem.className = 'image-item';
    imageItem.innerHTML = `
        <div class="image-preview">
            <img src="${src}" alt="${file.name}">
            <div class="image-overlay">
                <button type="button" class="remove-image-btn" onclick="removeImagePreview(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="image-info">
            <div class="image-name">${file.name}</div>
            <div class="image-size">${formatFileSize(file.size)}</div>
        </div>
    `;
    return imageItem;
}

// Xóa preview hình ảnh
function removeImagePreview(button) {
    const imageItem = button.closest('.image-item');
    imageItem.remove();

    // Update file input
    const previewContainer = document.getElementById('image-previews');
    if (previewContainer.children.length === 0) {
        previewContainer.classList.remove('active');
        // Reset file input
        document.getElementById('images').value = '';
    }

    updateProgress();
}

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 ' + {!! json_encode(__('thread.file_size.bytes')) !!};
    const k = 1024;
    const sizes = [{!! json_encode(__('thread.file_size.bytes')) !!}, 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Khởi tạo các tính năng poll
function initializePollFeatures() {
    const toggleInput = document.getElementById('has_poll');
    const pollContent = document.querySelector('.poll-content');

    toggleInput.addEventListener('change', function() {
        pollContent.style.display = this.checked ? 'block' : 'none';
        updateProgress();
    });

    // Poll options management
    initializePollOptions();

    // Poll close date toggle
    const closeDateCheckbox = document.getElementById('poll_has_close_date');
    const closeSettings = document.querySelector('.poll-close-settings');

    closeDateCheckbox.addEventListener('change', function() {
        closeSettings.style.display = this.checked ? 'block' : 'none';
    });

    // Number input controls
    initializeNumberInputs();
}

// Khởi tạo quản lý poll options
function initializePollOptions() {
    const addButton = document.querySelector('.add-option-btn');
    const optionsContainer = document.getElementById('poll-options');

    addButton.addEventListener('click', addPollOption);

    // Initialize existing options
    updateRemoveButtons();

    // Handle remove buttons
    optionsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-option-btn')) {
            removePollOption(e.target.closest('.remove-option-btn'));
        }
    });
}

// Thêm poll option
function addPollOption() {
    const optionsContainer = document.getElementById('poll-options');
    const optionCount = optionsContainer.children.length + 1;

    const optionItem = document.createElement('div');
    optionItem.className = 'poll-option-item';
    optionItem.innerHTML = `
        <div class="option-input-wrapper">
            <input type="text"
                   class="form-control-modern"
                   name="poll_options[]"
                   placeholder="{!! json_encode(__('forum.poll.option_placeholder_dynamic')) !!} ${optionCount}">
            <button type="button" class="remove-option-btn">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    optionsContainer.appendChild(optionItem);
    updateRemoveButtons();
}

// Xóa poll option
function removePollOption(button) {
    const optionItem = button.closest('.poll-option-item');
    optionItem.remove();

    // Update placeholders
    const optionsContainer = document.getElementById('poll-options');
    const inputs = optionsContainer.querySelectorAll('input');
    inputs.forEach((input, index) => {
        input.placeholder = `Lựa chọn ${index + 1}`;
    });

    updateRemoveButtons();
}

// Cập nhật trạng thái nút remove
function updateRemoveButtons() {
    const optionsContainer = document.getElementById('poll-options');
    const removeButtons = optionsContainer.querySelectorAll('.remove-option-btn');
    const shouldDisable = removeButtons.length <= 2;

    removeButtons.forEach(button => {
        button.disabled = shouldDisable;
    });
}

// Khởi tạo number inputs
function initializeNumberInputs() {
    // Poll close days
    const decreaseDaysBtn = document.getElementById('decrease-days');
    const increaseDaysBtn = document.getElementById('increase-days');
    const daysInput = document.getElementById('poll_close_after_days');

    if (decreaseDaysBtn) {
        decreaseDaysBtn.addEventListener('click', () => {
            if (daysInput.value > 1) {
                daysInput.value = parseInt(daysInput.value) - 1;
            }
        });
    }

    if (increaseDaysBtn) {
        increaseDaysBtn.addEventListener('click', () => {
            if (daysInput.value < 365) {
                daysInput.value = parseInt(daysInput.value) + 1;
            }
        });
    }
}

// Khởi tạo các tính năng showcase
function initializeShowcaseFeatures() {
    const toggleInput = document.getElementById('create_showcase');
    const showcaseContent = document.querySelector('.showcase-content');
    const showcaseTypeRadios = document.querySelectorAll('input[name="showcase_type"]');
    const existingSection = document.querySelector('.existing-showcase-section');
    const newSection = document.querySelector('.new-showcase-section');

    // Toggle showcase content visibility
    if (toggleInput && showcaseContent) {
        toggleInput.addEventListener('change', function() {
            showcaseContent.style.display = this.checked ? 'block' : 'none';
            updateProgress();
        });
    }

    // Handle showcase type selection
    showcaseTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'existing') {
                existingSection.style.display = 'block';
                newSection.style.display = 'none';
            } else {
                existingSection.style.display = 'none';
                newSection.style.display = 'block';
            }
            updateProgress();
        });
    });

    // Auto-fill showcase title from thread title
    const threadTitle = document.getElementById('title');
    const showcaseTitle = document.getElementById('showcase_title');

    if (threadTitle && showcaseTitle) {
        threadTitle.addEventListener('input', function() {
            if (!showcaseTitle.value) {
                showcaseTitle.value = this.value;
            }
        });
    }
}

// Validate showcase step
function validateShowcase() {
    const createShowcase = document.getElementById('create_showcase');

    if (!createShowcase || !createShowcase.checked) {
        return true; // Showcase is optional
    }

    const showcaseType = document.querySelector('input[name="showcase_type"]:checked');

    if (!showcaseType) {
        showValidationMessage({!! json_encode(__('thread.validation.select_showcase_type')) !!}, 'error');
        return false;
    }

    if (showcaseType.value === 'existing') {
        const existingShowcaseId = document.getElementById('existing_showcase_id');
        if (!existingShowcaseId.value) {
            showValidationMessage(window.threadTranslations.validation.selectExistingShowcase, 'error');
            return false;
        }
    } else {
        // Validate new showcase fields
        const showcaseTitle = document.getElementById('showcase_title');
        const showcaseDescription = document.getElementById('showcase_description');

        if (!showcaseTitle.value.trim()) {
            showValidationMessage('{{ t_common("thread_showcase.title_required") }}', 'error');
            return false;
        }

        if (!showcaseDescription.value.trim()) {
            showValidationMessage('{{ t_common("thread_showcase.description_required") }}', 'error');
            return false;
        }

        if (showcaseDescription.value.trim().length < 50) {
            showValidationMessage('{{ t_common("thread_showcase.description_min") }}', 'error');
            return false;
        }
    }

    return true;
}

// Navigation giữa các bước
function nextStep() {
    if (currentStepIndex < steps.length - 1) {
        if (validateCurrentStep()) {
            currentStepIndex++;
            showStep(currentStepIndex);
            updateStepProgress();

            if (currentStepIndex === steps.length - 1) {
                updateReviewData();
            }
        }
    }
}

function prevStep() {
    if (currentStepIndex > 0) {
        currentStepIndex--;
        showStep(currentStepIndex);
        updateStepProgress();
    }
}

// Hiển thị bước hiện tại
function showStep(index) {
    const formSteps = document.querySelectorAll('.form-step');

    formSteps.forEach((step, i) => {
        step.classList.toggle('active', i === index);
    });

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Cập nhật progress steps
function updateStepProgress() {
    const stepItems = document.querySelectorAll('.step-item');
    const progressLine = document.getElementById('progress-line');

    stepItems.forEach((item, index) => {
        item.classList.remove('active', 'completed');

        if (index < currentStepIndex) {
            item.classList.add('completed');
        } else if (index === currentStepIndex) {
            item.classList.add('active');
        }
    });

    // Update progress line width
    if (progressLine) {
        const progress = (currentStepIndex / (stepItems.length - 1)) * 100;
        progressLine.style.width = `${progress}%`;
    }
}

// Validate bước hiện tại
function validateCurrentStep() {
    const currentStep = steps[currentStepIndex];
    let isValid = true;

    switch (currentStep) {
        case 'basic':
            isValid = validateBasicInfo();
            break;
        case 'content':
            isValid = validateContent();
            break;
        case 'showcase':
            isValid = validateShowcase();
            break;
        case 'poll':
            isValid = validatePoll();
            break;
    }

    return isValid;
}

// Validate thông tin cơ bản
function validateBasicInfo() {
    const title = document.getElementById('title');
    const category = document.getElementById('category_id');
    const forum = document.getElementById('forum_id');

    let isValid = true;

    if (!title.value.trim()) {
        showFieldError(title, window.threadTranslations.validation.titleRequired);
        isValid = false;
    } else {
        clearFieldError(title);
    }

    if (!category.value) {
        showFieldError(category, window.threadTranslations.validation.categoryRequired);
        isValid = false;
    } else {
        clearFieldError(category);
    }

    if (!forum.value) {
        showFieldError(forum, window.threadTranslations.validation.forumRequired);
        isValid = false;
    } else {
        clearFieldError(forum);
    }

    return isValid;
}

// Validate nội dung
function validateContent() {
    const contentValue = window.contentEditor ? window.contentEditor.getData() : document.getElementById('content').value;

    if (!contentValue.trim()) {
        showValidationMessage('Vui lòng nhập nội dung cho chủ đề', 'error');
        return false;
    }

    return true;
}

// Validate poll
function validatePoll() {
    const hasPoll = document.getElementById('has_poll').checked;

    if (!hasPoll) return true;

    const question = document.getElementById('poll_question');
    const options = document.querySelectorAll('#poll-options input[name="poll_options[]"]');

    let isValid = true;

    if (!question.value.trim()) {
        showFieldError(question, window.threadTranslations.validation.pollQuestionRequired);
        isValid = false;
    } else {
        clearFieldError(question);
    }

    let validOptions = 0;
    options.forEach(option => {
        if (option.value.trim()) {
            validOptions++;
        }
    });

    if (validOptions < 2) {
        showValidationMessage(window.threadTranslations.validation.pollMinOptions, 'error');
        isValid = false;
    }

    return isValid;
}

// Hiển thị lỗi field
function showFieldError(field, message) {
    field.classList.add('is-invalid');

    let errorDiv = field.parentNode.querySelector('.error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        field.parentNode.appendChild(errorDiv);
    }

    errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
}

// Xóa lỗi field
function clearFieldError(field) {
    field.classList.remove('is-invalid');
    const errorDiv = field.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// Hiển thị thông báo validation
function showValidationMessage(message, type = 'info', duration = 5000) {
    // Tạo toast notification
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="${type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(toast);

    // Hiển thị và tự động ẩn
    setTimeout(() => toast.classList.add('show'), 100);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, duration);

    // Thông báo cho screen reader
    const liveRegion = document.getElementById('form-status');
    if (liveRegion) {
        liveRegion.textContent = message;
    }

    // Focus vào thông báo lỗi để screen reader đọc
    if (type === 'error') {
        const alertElement = document.querySelector('.alert-danger');
        if (alertElement) {
            alertElement.setAttribute('role', 'alert');
            alertElement.setAttribute('aria-live', 'assertive');
            alertElement.focus();
        }
    }
}

// Cập nhật dữ liệu review
function updateReviewData() {
    // Title
    document.getElementById('review-title').textContent =
        document.getElementById('title').value || '-';

    // Category
    const categorySelect = document.getElementById('category_id');
    document.getElementById('review-category').textContent =
        categorySelect.options[categorySelect.selectedIndex]?.text || '-';

    // Forum
    const forumSelect = document.getElementById('forum_id');
    document.getElementById('review-forum').textContent =
        forumSelect.options[forumSelect.selectedIndex]?.text || '-';

    // Content
    const contentValue = window.contentEditor ? window.contentEditor.getData() : document.getElementById('content').value;
    const contentPreview = document.getElementById('review-content');
    if (contentValue.trim()) {
        contentPreview.innerHTML = contentValue.substring(0, 200) + (contentValue.length > 200 ? '...' : '');
    } else {
        contentPreview.textContent = '-';
    }

    // Images
    const imageCount = document.querySelectorAll('#image-previews .image-item').length;
    document.getElementById('review-images').textContent =
        imageCount > 0 ? `${imageCount} hình ảnh` : 'Không có hình ảnh';

    // Poll
    const hasPoll = document.getElementById('has_poll').checked;
    const pollReview = document.getElementById('review-poll');
    if (hasPoll) {
        const question = document.getElementById('poll_question').value;
        const optionCount = document.querySelectorAll('#poll-options input[name="poll_options[]"]').length;
        pollReview.textContent = question ? `${question} (${optionCount} lựa chọn)` : 'Có khảo sát';
    } else {
        pollReview.textContent = 'Không có khảo sát';
    }

    // Showcase
    const createShowcase = document.getElementById('create_showcase');
    const showcaseSection = document.getElementById('review-showcase-section');
    const showcaseReview = document.getElementById('review-showcase');

    if (createShowcase && createShowcase.checked) {
        showcaseSection.style.display = 'block';

        const showcaseType = document.querySelector('input[name="showcase_type"]:checked');
        if (showcaseType && showcaseType.value === 'existing') {
            const existingSelect = document.getElementById('existing_showcase_id');
            const selectedText = existingSelect.options[existingSelect.selectedIndex]?.text || 'Showcase có sẵn';
            showcaseReview.textContent = `Đính kèm: ${selectedText}`;
        } else {
            const showcaseTitle = document.getElementById('showcase_title').value;
            const projectType = document.getElementById('project_type').value;
            const complexity = document.getElementById('complexity_level').value;

            let showcaseInfo = showcaseTitle || 'Showcase mới';
            if (projectType) showcaseInfo += ` (${projectType})`;
            if (complexity) showcaseInfo += ` - ${complexity}`;

            showcaseReview.textContent = showcaseInfo;
        }
    } else {
        showcaseSection.style.display = 'none';
        showcaseReview.textContent = 'Không có showcase';
    }
}

// Cập nhật progress tổng thể
function updateProgress() {
    // This could be used to show overall form completion progress
    // For now, we'll just mark steps as completed when moving forward
}

// Cải thiện khả năng truy cập và điều hướng bàn phím
function enhanceAccessibility() {
    // Thêm hỗ trợ điều hướng bàn phím cho progress steps
    const stepButtons = document.querySelectorAll('.step-item');

    stepButtons.forEach((step, index) => {
        step.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (!step.disabled && !step.classList.contains('active')) {
                    goToStep(step.dataset.step);
                }
            }

            // Arrow key navigation
            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                e.preventDefault();
                const nextStep = stepButtons[index + 1];
                if (nextStep && !nextStep.disabled) {
                    nextStep.focus();
                }
            }

            if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                e.preventDefault();
                const prevStep = stepButtons[index - 1];
                if (prevStep) {
                    prevStep.focus();
                }
            }
        });
    });

    // Thêm live region cho thông báo
    const liveRegion = document.createElement('div');
    liveRegion.setAttribute('aria-live', 'polite');
    liveRegion.setAttribute('aria-atomic', 'true');
    liveRegion.className = 'sr-only';
    liveRegion.id = 'form-status';
    document.body.appendChild(liveRegion);
}

// Function để chuyển đến step cụ thể với accessibility improvements
function goToStep(step) {
    const stepIndex = steps.indexOf(step);
    if (stepIndex === -1) return;

    // Validate các bước trước
    if (!validateStepsUpTo(stepIndex - 1)) {
        return false;
    }

    currentStepIndex = stepIndex;
    showStep(currentStepIndex);
    updateStepProgress();

    if (currentStepIndex === steps.length - 1) {
        updateReviewData();
    }

    return true;
}

// Cập nhật trạng thái step có thể truy cập
function updateStepAvailability(currentStep) {
    const stepButtons = document.querySelectorAll('.step-item');
    const currentIndex = steps.indexOf(currentStep);

    stepButtons.forEach((button, index) => {
        if (index <= currentIndex + 1) {
            button.disabled = false;
            button.setAttribute('tabindex', '0');
        } else {
            button.disabled = true;
            button.setAttribute('tabindex', '-1');
        }
    });
}

// Function để lấy step hiện tại
function getCurrentStep() {
    return steps[currentStepIndex];
}

// Update progress line animation
function updateProgressLine() {
    const progressLine = document.getElementById('progress-line');
    const stepItems = document.querySelectorAll('.step-item');

    if (progressLine && stepItems.length > 0) {
        const progress = (currentStepIndex / (stepItems.length - 1)) * 100;
        progressLine.style.width = `${progress}%`;

        // Add smooth transition
        progressLine.style.transition = 'width 0.3s ease-in-out';
    }
}

// Khởi tạo validation
function initializeValidation() {
    // Real-time validation for required fields
    const requiredFields = document.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                clearFieldError(this);
            }
        });

        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
                clearFieldError(this);
            }
        });
    });
}

// File Upload Functionality for Showcase
function initShowcaseFileUpload() {
    const fileInput = document.getElementById('showcaseFileInput');
    const browseBtn = document.getElementById('showcaseBrowseFiles');
    const uploadZone = document.getElementById('showcaseUploadZone');
    const fileUploadArea = document.getElementById('showcaseFileUploadArea');
    const filePreviews = document.getElementById('showcaseFilePreviews');
    const filePreviewContainer = document.getElementById('showcaseFilePreviewContainer');

    if (!fileInput || !browseBtn || !uploadZone) return;

    let selectedFiles = [];

    // Browse files button
    browseBtn.addEventListener('click', function(e) {
        e.preventDefault();
        fileInput.click();
    });

    // File input change
    fileInput.addEventListener('change', function(e) {
        handleFileSelection(Array.from(e.target.files));
    });

    // Drag and drop
    uploadZone.addEventListener('click', function() {
        fileInput.click();
    });

    fileUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    fileUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });

    fileUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        const files = Array.from(e.dataTransfer.files);
        handleFileSelection(files);
    });

    function handleFileSelection(files) {
        // Validate file count
        if (selectedFiles.length + files.length > 10) {
            showValidationMessage(window.threadTranslations.file.maxFilesError, 'error');
            return;
        }

        // Validate and add files
        files.forEach(file => {
            if (validateFile(file)) {
                selectedFiles.push(file);
                addFilePreview(file);
            }
        });

        updateFileInput();
        toggleFilePreviewsVisibility();
    }

    function validateFile(file) {
        // Check file size (50MB max)
        if (file.size > 50 * 1024 * 1024) {
            showValidationMessage(`File "${file.name}" ${window.threadTranslations.file.sizeError}`, 'error');
            return false;
        }

        // Check file type
        const allowedExtensions = [
            'jpg', 'jpeg', 'png', 'gif', 'webp',
            'pdf', 'doc', 'docx',
            'dwg', 'dxf', 'step', 'stp', 'stl', 'obj', 'iges', 'igs'
        ];

        const fileExtension = file.name.split('.').pop().toLowerCase();

        if (!allowedExtensions.includes(fileExtension)) {
            showValidationMessage(`File "${file.name}" ${window.threadTranslations.file.typeError}`, 'error');
            return false;
        }

        return true;
    }

    function addFilePreview(file) {
        const fileId = 'file_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        const isImage = file.type.startsWith('image/');
        const fileSize = formatFileSize(file.size);
        const fileIcon = getFileIcon(file);

        const previewHtml = `
            <div class="file-preview-item-modern ${isImage ? 'image-preview' : ''}" data-file-id="${fileId}">
                <button type="button" class="remove-file-modern" onclick="removeShowcaseFile('${fileId}')">
                    <i class="fas fa-times"></i>
                </button>
                <div class="file-icon-modern">
                    ${isImage ?
                        `<img src="${URL.createObjectURL(file)}" alt="${file.name}">` :
                        `<i class="${fileIcon}"></i>`
                    }
                </div>
                <div class="file-info-modern">
                    <div class="file-name-modern" title="${file.name}">
                        ${file.name}
                    </div>
                    <div class="file-size-modern">${fileSize}</div>
                </div>
            </div>
        `;

        filePreviewContainer.insertAdjacentHTML('beforeend', previewHtml);
    }

    function updateFileInput() {
        // Create new DataTransfer object to update file input
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    function toggleFilePreviewsVisibility() {
        if (selectedFiles.length > 0) {
            filePreviews.style.display = 'block';
        } else {
            filePreviews.style.display = 'none';
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function getFileIcon(file) {
        const extension = file.name.split('.').pop().toLowerCase();

        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
            return 'fas fa-image text-success';
        } else if (extension === 'pdf') {
            return 'fas fa-file-pdf text-danger';
        } else if (['doc', 'docx'].includes(extension)) {
            return 'fas fa-file-word text-primary';
        } else if (['dwg', 'dxf', 'step', 'stp', 'stl', 'obj', 'iges', 'igs'].includes(extension)) {
            return 'fas fa-cube text-info';
        } else {
            return 'fas fa-file text-secondary';
        }
    }

    // Make removeFile function global
    window.removeShowcaseFile = function(fileId) {
        // Remove from selectedFiles array
        const previewElement = document.querySelector(`[data-file-id="${fileId}"]`);
        if (previewElement) {
            const fileName = previewElement.querySelector('.file-name-modern').textContent.trim();
            selectedFiles = selectedFiles.filter(file => file.name !== fileName);
            previewElement.remove();
            updateFileInput();
            toggleFilePreviewsVisibility();
        }
    };
}

// Submit form
document.getElementById('thread-form').addEventListener('submit', function(e) {
    // Final validation before submit
    if (!validateBasicInfo() || !validateContent() || !validateShowcase() || !validatePoll()) {
        e.preventDefault();
        showValidationMessage(window.threadTranslations.validation.checkInfo, 'error');
        return false;
    }

    // Show loading state
    const submitButton = document.querySelector('.btn-submit');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = `<i class="fas fa-hourglass-half"></i> <span>${window.threadTranslations.nav.creating}</span>`;
    }
});
</script>
@endpush
