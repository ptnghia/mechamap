@extends('layouts.app')

@section('title', 'Create New Thread')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/thread-form.css') }}">
@endpush

@section('content')
<div class="thread-form-container">
    <div class="card thread-form-card">
        <div class="card-header">
            <h1>Create New Thread</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('threads.store') }}" method="POST" enctype="multipart/form-data" class="thread-form">
                        @csrf

                        <div class="thread-form-section">
                            <div class="thread-form-section-title">
                                <i class="bi bi-pencil-square"></i> Basic Information
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label">Thread Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required placeholder="Enter a descriptive title for your thread">
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i> Use format: CITY l Project Name l Floors l Status (e.g., CARACAS l Promenade Res. I 24p I E/C)
                                </div>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
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

                                <div class="col-md-6 mb-3">
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
                        </div>

                        <!-- Project Details -->
                        <div class="thread-form-section">
                            <div class="thread-form-section-title">
                                <i class="bi bi-building"></i> Project Details
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}" placeholder="E.g., Las Mercedes, Downtown">
                                    <div class="form-text">
                                        <i class="bi bi-geo-alt me-1"></i> Specify the location of the project
                                    </div>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="usage" class="form-label">Usage</label>
                                    <input type="text" class="form-control @error('usage') is-invalid @enderror" id="usage" name="usage" value="{{ old('usage') }}" placeholder="E.g., Residential, Commercial, Mixed">
                                    <div class="form-text">
                                        <i class="bi bi-house-door me-1"></i> Specify the purpose of the building
                                    </div>
                                    @error('usage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="floors" class="form-label">Floors</label>
                                    <input type="number" class="form-control @error('floors') is-invalid @enderror" id="floors" name="floors" value="{{ old('floors') }}" min="1" placeholder="Number of floors">
                                    <div class="form-text">
                                        <i class="bi bi-layers me-1"></i> Enter the number of floors in the building
                                    </div>
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
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i> Current status of the project
                                    </div>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="thread-form-section">
                            <div class="thread-form-section-title">
                                <i class="bi bi-file-text"></i> Thread Content
                            </div>

                            <div class="mb-4">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="15" required>{{ old('content') }}</textarea>
                                <div class="form-text">
                                    <i class="bi bi-pencil me-1"></i> You can use the editor to format text, add links, and insert images.
                                </div>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="images" class="form-label">
                                    <i class="bi bi-images me-1"></i> Upload Images
                                </label>
                                <div class="custom-file-upload">
                                    <div class="input-group">
                                        <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*">
                                        <label class="input-group-text" for="images"><i class="bi bi-upload me-1"></i> Browse</label>
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i> You can upload multiple images. Maximum 10 images, each up to 5MB.
                                    </div>
                                    @error('images')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @error('images.*')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div id="image-previews" class="row mt-3 image-preview-container"></div>
                        </div>

                        <!-- Poll Section -->
                        <div class="thread-form-section">
                            <div class="thread-form-section-title">
                                <i class="bi bi-bar-chart"></i> Poll (Optional)
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="has_poll" name="has_poll" value="1" {{ old('has_poll') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="has_poll">
                                    Add a poll to this thread
                                </label>
                            </div>

                            <div class="poll-section" style="{{ old('has_poll') ? '' : 'display: none;' }}">
                                <div class="mb-3">
                                    <label for="poll_question" class="form-label">Question <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('poll_question') is-invalid @enderror" id="poll_question" name="poll_question" value="{{ old('poll_question') }}">
                                    @error('poll_question')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Possible responses <span class="text-danger">*</span></label>
                                    <div id="poll-options">
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control @error('poll_options.0') is-invalid @enderror" name="poll_options[]" value="{{ old('poll_options.0') }}" placeholder="Option 1">
                                            <button class="btn btn-outline-secondary remove-option" type="button" disabled><i class="bi bi-trash"></i></button>
                                        </div>
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control @error('poll_options.1') is-invalid @enderror" name="poll_options[]" value="{{ old('poll_options.1') }}" placeholder="Option 2">
                                            <button class="btn btn-outline-secondary remove-option" type="button" disabled><i class="bi bi-trash"></i></button>
                                        </div>
                                        @if(old('poll_options'))
                                            @foreach(old('poll_options') as $index => $option)
                                                @if($index > 1)
                                                    <div class="input-group mb-2">
                                                        <input type="text" class="form-control @error('poll_options.'.$index) is-invalid @enderror" name="poll_options[]" value="{{ $option }}" placeholder="Option {{ $index + 1 }}">
                                                        <button class="btn btn-outline-secondary remove-option" type="button"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-option">
                                        <i class="bi bi-plus-circle"></i> Add Option
                                    </button>
                                    @error('poll_options')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Maximum selectable responses</label>
                                    <div class="d-flex align-items-center">
                                        <div class="form-check me-4">
                                            <input class="form-check-input" type="radio" name="poll_max_options" id="single_choice" value="1" {{ old('poll_max_options', 1) == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="single_choice">
                                                Single choice
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="poll_max_options" id="multiple_choice" value="0" {{ old('poll_max_options') === '0' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="multiple_choice">
                                                Unlimited
                                            </label>
                                        </div>
                                        <div class="ms-4 custom-max-options" style="{{ old('poll_max_options') && old('poll_max_options') != 1 && old('poll_max_options') != 0 ? '' : 'display: none;' }}">
                                            <div class="input-group input-group-sm" style="width: 100px;">
                                                <button class="btn btn-outline-secondary" type="button" id="decrease-max">-</button>
                                                <input type="number" class="form-control text-center" id="custom_max_options" name="custom_max_options" value="{{ old('poll_max_options') && old('poll_max_options') != 1 && old('poll_max_options') != 0 ? old('poll_max_options') : 2 }}" min="2" max="10">
                                                <button class="btn btn-outline-secondary" type="button" id="increase-max">+</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-text">This is the maximum number of responses a voter may select when voting.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Options</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="poll_allow_change_vote" name="poll_allow_change_vote" value="1" {{ old('poll_allow_change_vote', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="poll_allow_change_vote">
                                            Allow voters to change their votes
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="poll_show_votes_publicly" name="poll_show_votes_publicly" value="1" {{ old('poll_show_votes_publicly') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="poll_show_votes_publicly">
                                            Display votes publicly
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="poll_allow_view_without_vote" name="poll_allow_view_without_vote" value="1" {{ old('poll_allow_view_without_vote', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="poll_allow_view_without_vote">
                                            Allow the results to be viewed without voting
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Close this poll after:</label>
                                    <div class="d-flex align-items-center">
                                        <div class="form-check me-4">
                                            <input class="form-check-input" type="checkbox" id="poll_has_close_date" name="poll_has_close_date" value="1" {{ old('poll_has_close_date') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="poll_has_close_date">
                                                Close poll after
                                            </label>
                                        </div>
                                        <div class="poll-close-days" style="{{ old('poll_has_close_date') ? '' : 'display: none;' }}">
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <button class="btn btn-outline-secondary" type="button" id="decrease-days">-</button>
                                                <input type="number" class="form-control text-center" id="poll_close_after_days" name="poll_close_after_days" value="{{ old('poll_close_after_days', 7) }}" min="1" max="365">
                                                <button class="btn btn-outline-secondary" type="button" id="increase-days">+</button>
                                            </div>
                                            <span class="ms-2">days</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="thread-form-submit">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> Create Thread
                            </button>
                        </div>
                    </form>
                </div>
            </div>
@endsection

@push('scripts')
<script>
    // Initialize rich text editor for content
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize CKEditor
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor
                .create(document.querySelector('#content'), {
                    toolbar: {
                        items: [
                            'heading',
                            '|',
                            'bold',
                            'italic',
                            'link',
                            'bulletedList',
                            'numberedList',
                            '|',
                            'outdent',
                            'indent',
                            '|',
                            'imageUpload',
                            'blockQuote',
                            'insertTable',
                            'mediaEmbed',
                            'undo',
                            'redo'
                        ]
                    },
                    language: 'vi',
                    image: {
                        toolbar: [
                            'imageTextAlternative',
                            'imageStyle:full',
                            'imageStyle:side'
                        ]
                    },
                    table: {
                        contentToolbar: [
                            'tableColumn',
                            'tableRow',
                            'mergeTableCells'
                        ]
                    },
                })
                .catch(error => {
                    console.error(error);
                });
        } else {
            // Fallback if CKEditor is not loaded
            console.warn('CKEditor not loaded. Using plain textarea instead.');
        }

        // Poll toggle
        const hasPollCheckbox = document.getElementById('has_poll');
        const pollSection = document.querySelector('.poll-section');

        hasPollCheckbox.addEventListener('change', function() {
            pollSection.style.display = this.checked ? 'block' : 'none';
        });

        // Add poll option
        const addOptionButton = document.getElementById('add-option');
        const pollOptionsContainer = document.getElementById('poll-options');
        let optionCount = document.querySelectorAll('#poll-options .input-group').length;

        addOptionButton.addEventListener('click', function() {
            optionCount++;
            const newOption = document.createElement('div');
            newOption.className = 'input-group mb-2';
            newOption.innerHTML = `
                <input type="text" class="form-control" name="poll_options[]" placeholder="Option ${optionCount}">
                <button class="btn btn-outline-secondary remove-option" type="button"><i class="bi bi-trash"></i></button>
            `;
            pollOptionsContainer.appendChild(newOption);

            // Enable remove buttons if we have more than 2 options
            updateRemoveButtons();
        });

        // Remove poll option
        pollOptionsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-option')) {
                e.target.closest('.input-group').remove();
                optionCount--;

                // Update placeholders
                const inputs = pollOptionsContainer.querySelectorAll('input');
                inputs.forEach((input, index) => {
                    input.placeholder = `Option ${index + 1}`;
                });

                // Disable remove buttons if we have only 2 options
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            const removeButtons = pollOptionsContainer.querySelectorAll('.remove-option');
            const disableRemove = removeButtons.length <= 2;

            removeButtons.forEach(button => {
                button.disabled = disableRemove;
            });
        }

        // Max options controls
        const singleChoiceRadio = document.getElementById('single_choice');
        const multipleChoiceRadio = document.getElementById('multiple_choice');
        const customMaxOptions = document.querySelector('.custom-max-options');
        const customMaxInput = document.getElementById('custom_max_options');
        const decreaseMaxBtn = document.getElementById('decrease-max');
        const increaseMaxBtn = document.getElementById('increase-max');

        singleChoiceRadio.addEventListener('change', function() {
            if (this.checked) {
                customMaxOptions.style.display = 'none';
                document.querySelector('input[name="poll_max_options"]').value = 1;
            }
        });

        multipleChoiceRadio.addEventListener('change', function() {
            if (this.checked) {
                customMaxOptions.style.display = 'none';
                document.querySelector('input[name="poll_max_options"]').value = 0;
            }
        });

        // Custom max options
        decreaseMaxBtn.addEventListener('click', function() {
            if (customMaxInput.value > 2) {
                customMaxInput.value = parseInt(customMaxInput.value) - 1;
                document.querySelector('input[name="poll_max_options"]').value = customMaxInput.value;
            }
        });

        increaseMaxBtn.addEventListener('click', function() {
            if (customMaxInput.value < 10) {
                customMaxInput.value = parseInt(customMaxInput.value) + 1;
                document.querySelector('input[name="poll_max_options"]').value = customMaxInput.value;
            }
        });

        // Poll close date
        const hasCloseDateCheckbox = document.getElementById('poll_has_close_date');
        const pollCloseDays = document.querySelector('.poll-close-days');
        const pollCloseDaysInput = document.getElementById('poll_close_after_days');
        const decreaseDaysBtn = document.getElementById('decrease-days');
        const increaseDaysBtn = document.getElementById('increase-days');

        hasCloseDateCheckbox.addEventListener('change', function() {
            pollCloseDays.style.display = this.checked ? 'flex' : 'none';
        });

        decreaseDaysBtn.addEventListener('click', function() {
            if (pollCloseDaysInput.value > 1) {
                pollCloseDaysInput.value = parseInt(pollCloseDaysInput.value) - 1;
            }
        });

        increaseDaysBtn.addEventListener('click', function() {
            if (pollCloseDaysInput.value < 365) {
                pollCloseDaysInput.value = parseInt(pollCloseDaysInput.value) + 1;
            }
        });

        // Initialize
        updateRemoveButtons();
    });

    // Preview images before upload
    document.getElementById('images').addEventListener('change', function(event) {
        // Remove existing previews
        const previewContainer = document.getElementById('image-previews');
        previewContainer.innerHTML = '';

        // Add previews for each file
        const files = event.target.files;

        if (files.length === 0) {
            return;
        }

        // Show preview container
        previewContainer.style.display = 'flex';
        previewContainer.style.flexWrap = 'wrap';

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            // Only process image files
            if (!file.type.match('image.*')) {
                continue;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                const previewCol = document.createElement('div');
                previewCol.className = 'col-md-3 col-sm-4 col-6 mb-3';

                const previewCard = document.createElement('div');
                previewCard.className = 'card h-100 image-preview';

                const previewImg = document.createElement('img');
                previewImg.src = e.target.result;
                previewImg.className = 'card-img-top';
                previewImg.style.height = '120px';
                previewImg.style.objectFit = 'cover';

                const previewBody = document.createElement('div');
                previewBody.className = 'card-body p-2';

                const previewText = document.createElement('p');
                previewText.className = 'card-text small text-truncate mb-0';
                previewText.title = file.name;
                previewText.textContent = file.name;

                // Add file size
                const fileSizeText = document.createElement('small');
                fileSizeText.className = 'text-muted';
                fileSizeText.textContent = formatFileSize(file.size);

                // Add remove button
                const removeButton = document.createElement('button');
                removeButton.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle';
                removeButton.innerHTML = '<i class="bi bi-x"></i>';
                removeButton.style.width = '24px';
                removeButton.style.height = '24px';
                removeButton.style.padding = '0';
                removeButton.style.fontSize = '14px';
                removeButton.title = 'Remove image';
                removeButton.type = 'button';

                removeButton.addEventListener('click', function() {
                    previewCol.remove();

                    // If no previews left, hide container
                    if (previewContainer.children.length === 0) {
                        previewContainer.style.display = 'none';
                    }
                });

                previewBody.appendChild(previewText);
                previewBody.appendChild(fileSizeText);
                previewCard.appendChild(previewImg);
                previewCard.appendChild(previewBody);
                previewCard.appendChild(removeButton);
                previewCol.appendChild(previewCard);
                previewContainer.appendChild(previewCol);
            };

            reader.readAsDataURL(file);
        }
    });

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
</script>
@endpush
