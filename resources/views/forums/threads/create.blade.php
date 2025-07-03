@extends('layouts.app')

@section('title', __('forums.actions.create_thread'))

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">{{ __('forums.title') }}</a></li>
            @if(isset($forum))
            <li class="breadcrumb-item"><a href="{{ route('forums.show', $forum) }}">{{ $forum->name }}</a></li>
            @endif
            <li class="breadcrumb-item active">Create Thread</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-plus-circle text-primary me-2"></i>
                        {{ __('forums.actions.create_thread') }}
                    </h1>
                    <p class="text-muted mb-0">{{ __('forums.threads.start_discussion') }}</p>
                </div>
                <div>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('threads.store') }}" method="POST" enctype="multipart/form-data" id="threadForm">
        @csrf

        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Thread Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">
                                Thread Title <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title"
                                   name="title"
                                   value="{{ old('title') }}"
                                   placeholder="Enter a descriptive title for your thread"
                                   maxlength="255"
                                   required>
                            <div class="form-text">
                                <span id="titleCounter">0</span>/255 characters
                            </div>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Forum Selection -->
                        <div class="mb-3">
                            <label for="forum_id" class="form-label">
                                Forum <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('forum_id') is-invalid @enderror"
                                    id="forum_id"
                                    name="forum_id"
                                    required>
                                <option value="">Select a forum</option>
                                @foreach($forums as $forum)
                                    <option value="{{ $forum->id }}"
                                            {{ (old('forum_id', $selectedForum ?? '') == $forum->id) ? 'selected' : '' }}>
                                        {{ $forum->name }}
                                        @if($forum->description)
                                            - {{ Str::limit($forum->description, 50) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('forum_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category Selection -->
                        <div class="mb-3">
                            <label for="category_id" class="form-label">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('category_id') is-invalid @enderror"
                                    id="category_id"
                                    name="category_id"
                                    required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                            {{ (old('category_id', $selectedCategory ?? '') == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label for="content" class="form-label">
                                Content <span class="text-danger">*</span>
                            </label>
                            <div class="editor-toolbar mb-2">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('bold')">
                                        <i class="bx bx-bold"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('italic')">
                                        <i class="bx bx-italic"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('underline')">
                                        <i class="bx bx-underline"></i>
                                    </button>
                                </div>
                                <div class="btn-group btn-group-sm ms-2" role="group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="insertList('ul')">
                                        <i class="bx bx-list-ul"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="insertList('ol')">
                                        <i class="bx bx-list-ol"></i>
                                    </button>
                                </div>
                                <div class="btn-group btn-group-sm ms-2" role="group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="insertLink()">
                                        <i class="bx bx-link"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="insertCode()">
                                        <i class="bx bx-code"></i>
                                    </button>
                                </div>
                            </div>
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      id="content"
                                      name="content"
                                      rows="12"
                                      placeholder="Write your thread content here..."
                                      required>{{ old('content') }}</textarea>
                            <div class="form-text">
                                <span id="contentCounter">0</span> characters
                            </div>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Attachments -->
                        <div class="mb-3">
                            <label for="attachments" class="form-label">
                                Attachments
                            </label>
                            <input type="file"
                                   class="form-control @error('attachments.*') is-invalid @enderror"
                                   id="attachments"
                                   name="attachments[]"
                                   multiple
                                   accept="image/*,.pdf,.doc,.docx,.txt">
                            <div class="form-text">
                                You can upload images, PDFs, and documents. Maximum 5 files, 10MB each.
                            </div>
                            @error('attachments.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- File Preview -->
                            <div id="filePreview" class="mt-2"></div>
                        </div>

                        <!-- Tags -->
                        <div class="mb-3">
                            <label for="tags" class="form-label">
                                Tags
                            </label>
                            <input type="text"
                                   class="form-control @error('tags') is-invalid @enderror"
                                   id="tags"
                                   name="tags"
                                   value="{{ old('tags') }}"
                                   placeholder="Enter tags separated by commas">
                            <div class="form-text">
                                Add relevant tags to help others find your thread (e.g., mechanical, design, CAD)
                            </div>
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Thread Options -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Thread Options</h6>
                    </div>
                    <div class="card-body">
                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>
                                    Published
                                </option>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>
                                    Save as Draft
                                </option>
                            </select>
                        </div>

                        <!-- Thread Type -->
                        <div class="mb-3">
                            <label class="form-label">Thread Type</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="thread_type" id="discussion" value="discussion" checked>
                                <label class="form-check-label" for="discussion">
                                    <i class="bx bx-chat me-1"></i>Discussion
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="thread_type" id="question" value="question">
                                <label class="form-check-label" for="question">
                                    <i class="bx bx-help-circle me-1"></i>Question
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="thread_type" id="showcase" value="showcase">
                                <label class="form-check-label" for="showcase">
                                    <i class="bx bx-image me-1"></i>Showcase
                                </label>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="notify_replies" name="notify_replies" checked>
                                <label class="form-check-label" for="notify_replies">
                                    Notify me of replies
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Poll Options -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="has_poll" name="has_poll">
                            <label class="form-check-label" for="has_poll">
                                <h6 class="mb-0">Add Poll</h6>
                            </label>
                        </div>
                    </div>
                    <div class="card-body" id="pollOptions" style="display: none;">
                        <div class="mb-3">
                            <label for="poll_question" class="form-label">Poll Question</label>
                            <input type="text" class="form-control" id="poll_question" name="poll_question"
                                   placeholder="What would you like to ask?">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Poll Options</label>
                            <div id="pollOptionsList">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="poll_options[]" placeholder="Option 1">
                                    <button type="button" class="btn btn-outline-danger" onclick="removePollOption(this)">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="poll_options[]" placeholder="Option 2">
                                    <button type="button" class="btn btn-outline-danger" onclick="removePollOption(this)">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPollOption()">
                                <i class="bx bx-plus me-1"></i>Add Option
                            </button>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="poll_multiple" name="poll_multiple">
                                <label class="form-check-label" for="poll_multiple">
                                    Allow multiple choices
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="poll_expires" class="form-label">Poll Expires</label>
                            <select class="form-select" id="poll_expires" name="poll_expires">
                                <option value="">Never</option>
                                <option value="1">1 day</option>
                                <option value="7">1 week</option>
                                <option value="30">1 month</option>
                                <option value="90">3 months</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" name="action" value="publish">
                                <i class="bx bx-send me-1"></i>
                                Publish Thread
                            </button>
                            <button type="submit" class="btn btn-outline-secondary" name="action" value="draft">
                                <i class="bx bx-save me-1"></i>
                                Save as Draft
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="previewThread()">
                                <i class="bx bx-show me-1"></i>
                                Preview
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thread Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="$('#previewModal').modal('hide'); $('#threadForm').submit();">
                    Publish Thread
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.editor-toolbar {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 0.5rem;
}

.editor-toolbar .btn {
    border-radius: 4px;
}

#filePreview .file-item {
    display: inline-block;
    margin: 0.25rem;
    padding: 0.5rem;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    position: relative;
}

#filePreview .file-item .remove-file {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #dc3545;
    color: white;
    border: none;
    font-size: 12px;
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
}

@media (max-width: 768px) {
    .editor-toolbar {
        flex-wrap: wrap;
    }

    .editor-toolbar .btn-group {
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Character counters
document.getElementById('title').addEventListener('input', function() {
    document.getElementById('titleCounter').textContent = this.value.length;
});

document.getElementById('content').addEventListener('input', function() {
    document.getElementById('contentCounter').textContent = this.value.length;
});

// Poll toggle
document.getElementById('has_poll').addEventListener('change', function() {
    const pollOptions = document.getElementById('pollOptions');
    pollOptions.style.display = this.checked ? 'block' : 'none';
});

// File preview
document.getElementById('attachments').addEventListener('change', function() {
    const preview = document.getElementById('filePreview');
    preview.innerHTML = '';

    Array.from(this.files).forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.innerHTML = `
            <i class="bx bx-file me-1"></i>
            ${file.name}
            <button type="button" class="remove-file" onclick="removeFile(${index})">×</button>
        `;
        preview.appendChild(fileItem);
    });
});

// Text formatting functions
function formatText(command) {
    const textarea = document.getElementById('content');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);

    let formattedText = '';
    switch(command) {
        case 'bold':
            formattedText = `**${selectedText}**`;
            break;
        case 'italic':
            formattedText = `*${selectedText}*`;
            break;
        case 'underline':
            formattedText = `__${selectedText}__`;
            break;
    }

    textarea.value = textarea.value.substring(0, start) + formattedText + textarea.value.substring(end);
    textarea.focus();
}

function insertList(type) {
    const textarea = document.getElementById('content');
    const listItem = type === 'ul' ? '- ' : '1. ';
    const start = textarea.selectionStart;

    textarea.value = textarea.value.substring(0, start) + listItem + textarea.value.substring(start);
    textarea.focus();
}

function insertLink() {
    const url = prompt('Enter URL:');
    if (url) {
        const text = prompt('Enter link text:') || url;
        const textarea = document.getElementById('content');
        const start = textarea.selectionStart;

        textarea.value = textarea.value.substring(0, start) + `[${text}](${url})` + textarea.value.substring(start);
        textarea.focus();
    }
}

function insertCode() {
    const textarea = document.getElementById('content');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);

    const codeText = selectedText ? `\`${selectedText}\`` : '```\n\n```';
    textarea.value = textarea.value.substring(0, start) + codeText + textarea.value.substring(end);
    textarea.focus();
}

// Poll functions
function addPollOption() {
    const list = document.getElementById('pollOptionsList');
    const optionCount = list.children.length + 1;

    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control" name="poll_options[]" placeholder="Option ${optionCount}">
        <button type="button" class="btn btn-outline-danger" onclick="removePollOption(this)">
            <i class="bx bx-x"></i>
        </button>
    `;

    list.appendChild(div);
}

function removePollOption(button) {
    const list = document.getElementById('pollOptionsList');
    if (list.children.length > 2) {
        button.parentElement.remove();
    }
}

function removeFile(index) {
    // This would need more complex implementation to actually remove from file input
    event.target.parentElement.remove();
}

function previewThread() {
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;

    if (!title || !content) {
        alert('Please fill in title and content before previewing.');
        return;
    }

    // Simple preview - in real implementation, you'd send to server for proper markdown rendering
    const previewContent = document.getElementById('previewContent');
    previewContent.innerHTML = `
        <h3>${title}</h3>
        <div class="border-top pt-3">
            ${content.replace(/\n/g, '<br>')}
        </div>
    `;

    new bootstrap.Modal(document.getElementById('previewModal')).show();
}

// Auto-save draft functionality
let autoSaveTimer;
function autoSave() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => {
        const formData = new FormData(document.getElementById('threadForm'));
        formData.set('action', 'auto_save');

        fetch('{{ route("threads.auto-save") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
    }, 30000); // Auto-save every 30 seconds
}

// Trigger auto-save on content change
document.getElementById('content').addEventListener('input', autoSave);
document.getElementById('title').addEventListener('input', autoSave);
</script>
@endpush
