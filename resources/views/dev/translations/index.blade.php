<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🔧 Dev Translation Manager - MechaMap</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        .dev-warning {
            background: linear-gradient(45deg, #ff6b6b, #feca57);
            color: white;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        .stats-card {
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .form-floating label {
            color: #6c757d;
        }
        .alert-dev {
            border-left: 4px solid #ff6b6b;
            background-color: #fff5f5;
        }

        /* Inline Editing Styles */
        .editable-cell {
            position: relative;
        }

        .editable-content {
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.2s ease;
            min-height: 20px;
            word-wrap: break-word;
            position: relative;
        }

        .editable-content:hover {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .editable-content.empty-content:hover {
            background-color: #e3f2fd;
            border: 1px solid #2196f3;
        }

        .editable-content.editing {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        .editable-content.saving {
            background-color: #d1ecf1;
            border: 2px solid #17a2b8;
            position: relative;
        }

        .editable-content.saving::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 8px;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #17a2b8;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .editable-content.success {
            background-color: #d4edda;
            border: 2px solid #28a745;
            animation: successPulse 0.6s ease-out;
        }

        .editable-content.error {
            background-color: #f8d7da;
            border: 2px solid #dc3545;
            animation: errorShake 0.6s ease-out;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes successPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        @keyframes errorShake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .inline-edit-input {
            width: 100%;
            border: 2px solid #ffc107;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 14px;
            line-height: 1.4;
            resize: vertical;
            min-height: 36px;
            background-color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        .inline-edit-input:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .vi-content .editable-content::before {
            content: '🇻🇳';
            margin-right: 6px;
            font-size: 12px;
        }

        .en-content .editable-content::before {
            content: '🇬🇧';
            margin-right: 6px;
            font-size: 12px;
        }

        .empty-content {
            font-style: italic;
            color: #6c757d;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Development Warning Banner -->
    <div class="dev-warning text-center py-2">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>⚠️ DEVELOPMENT ENVIRONMENT ONLY ⚠️</strong>
        This tool is for development use only and should be removed before production deployment!
        <i class="fas fa-exclamation-triangle ms-2"></i>
    </div>

    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="fas fa-language text-primary me-2"></i>
                            Translation Manager
                        </h1>
                        <p class="text-muted mb-0">Manage Vietnamese translations for MechaMap</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success me-2" id="generateEnglishBtn">
                            <i class="fas fa-magic me-2"></i>Generate Missing English
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTranslationModal">
                            <i class="fas fa-plus me-2"></i>Add Translation
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-primary mb-2">
                            <i class="fas fa-globe fa-2x"></i>
                        </div>
                        <h4 class="mb-1">{{ number_format($stats['total_translations']) }}</h4>
                        <small class="text-muted">Total Translations</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-success mb-2">
                            <i class="fas fa-flag fa-2x"></i>
                        </div>
                        <h4 class="mb-1">{{ number_format($stats['vietnamese_translations']) }}</h4>
                        <small class="text-muted">Vietnamese</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-info mb-2">
                            <i class="fas fa-flag-usa fa-2x"></i>
                        </div>
                        <h4 class="mb-1">{{ number_format($stats['english_translations']) }}</h4>
                        <small class="text-muted">English</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-warning mb-2">
                            <i class="fas fa-layer-group fa-2x"></i>
                        </div>
                        <h4 class="mb-1">{{ number_format($stats['total_groups']) }}</h4>
                        <small class="text-muted">Groups</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        <div class="row">
            <div class="col-12">
                <div class="table-container p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-table me-2"></i>Translation Manager
                        </h5>
                        <div class="text-muted">
                            <small><i class="fas fa-info-circle me-1"></i>Click on content cells to edit inline</small>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="translationsTable" class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Key</th>
                                    <th>Vietnamese Content</th>
                                    <th>English Content</th>
                                    <th>Group</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Translation Modal -->
    <div class="modal fade" id="addTranslationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Add New Translation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addTranslationForm">
                    <div class="modal-body">
                        <div class="alert alert-dev" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Development Tool:</strong> This will add translations for both Vietnamese and English to the database.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="translationKey" name="key" placeholder="common.buttons.save" required>
                                    <label for="translationKey">Translation Key *</label>
                                    <div class="form-text">Use dot notation (e.g., common.buttons.save)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="groupName" name="group_name" required>
                                        <option value="">Select Group</option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group }}">{{ $group }}</option>
                                        @endforeach
                                    </select>
                                    <label for="groupName">Group *</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="translationContentVi" name="content_vi" placeholder="Vietnamese translation content" style="height: 100px" required></textarea>
                                    <label for="translationContentVi">Vietnamese Content *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="translationContentEn" name="content_en" placeholder="English translation content" style="height: 100px" required></textarea>
                                    <label for="translationContentEn">English Content *</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Add Translation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Translation Modal -->
    <div class="modal fade" id="editTranslationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Edit Translation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editTranslationForm">
                    <input type="hidden" id="editTranslationId" name="id">
                    <div class="modal-body">
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Key:</strong> <span id="editTranslationKeyDisplay"></span>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="editGroupName" name="group_name" required>
                                        @foreach($groups as $group)
                                            <option value="{{ $group }}">{{ $group }}</option>
                                        @endforeach
                                    </select>
                                    <label for="editGroupName">Group *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" id="editIsActive" name="is_active" checked>
                                    <label class="form-check-label" for="editIsActive">
                                        Active Translation
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="editTranslationContent" name="content" placeholder="Vietnamese translation content" style="height: 100px" required></textarea>
                            <label for="editTranslationContent">Vietnamese Content *</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Translation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTable
            const table = $('#translationsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ url("dev/translations/data") }}',
                    type: 'GET'
                },
                columns: [
                    { data: 'id', width: '60px' },
                    { data: 'key', width: '20%' },
                    {
                        data: 'vi_content',
                        width: '25%',
                        className: 'editable-cell vi-content',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                const fullContent = data || '';
                                const truncated = fullContent.length > 80 ? fullContent.substr(0, 80) + '...' : fullContent;
                                // Escape HTML attributes properly
                                const escapedContent = fullContent.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                                return `<div class="editable-content" data-key="${row.key}" data-locale="vi" data-full-content="${escapedContent}" title="Click to edit">${truncated}</div>`;
                            }
                            return data || '';
                        }
                    },
                    {
                        data: 'en_content',
                        width: '25%',
                        className: 'editable-cell en-content',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                const fullContent = data || '';
                                const truncated = fullContent.length > 80 ? fullContent.substr(0, 80) + '...' : '';
                                const placeholder = fullContent ? '' : '<span class="text-muted fst-italic">Click to add English translation</span>';
                                const content = fullContent ? truncated : '';
                                // Escape HTML attributes properly
                                const escapedContent = fullContent.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                                return `<div class="editable-content ${fullContent ? '' : 'empty-content'}" data-key="${row.key}" data-locale="en" data-full-content="${escapedContent}" title="Click to edit">${content}${placeholder}</div>`;
                            }
                            return data || '';
                        }
                    },
                    { data: 'group_name', width: '12%' },
                    {
                        data: 'is_active',
                        width: '80px',
                        render: function(data, type, row) {
                            return data === 'Active'
                                ? '<span class="badge bg-success">Active</span>'
                                : '<span class="badge bg-secondary">Inactive</span>';
                        }
                    },
                    { data: 'created_at', width: '100px' },
                    { data: 'updated_at', width: '100px' },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        width: '80px'
                    }
                ],
                order: [[4, 'asc'], [1, 'asc']], // Sort by group, then key
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                language: {
                    processing: '<i class="fas fa-spinner fa-spin"></i> Loading translations...',
                    emptyTable: 'No translations found',
                    info: 'Showing _START_ to _END_ of _TOTAL_ translations',
                    infoEmpty: 'Showing 0 to 0 of 0 translations',
                    infoFiltered: '(filtered from _MAX_ total translations)',
                    lengthMenu: 'Show _MENU_ translations per page',
                    search: 'Search translations:',
                    zeroRecords: 'No matching translations found'
                }
            });

            // Inline Editing Functionality
            let currentEditingElement = null;
            let originalContent = '';

            // Handle click on editable content
            $(document).on('click', '.editable-content', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // If already editing another element, save it first
                if (currentEditingElement && currentEditingElement !== this) {
                    saveInlineEdit(currentEditingElement);
                }

                // If clicking on the same element that's already being edited, do nothing
                if (currentEditingElement === this) {
                    return;
                }

                startInlineEdit(this);
            });

            // Helper function to decode HTML entities
            function decodeHtmlEntities(str) {
                const textarea = document.createElement('textarea');
                textarea.innerHTML = str;
                return textarea.value;
            }

            // Start inline editing
            function startInlineEdit(element) {
                const $element = $(element);
                const key = $element.data('key');
                const locale = $element.data('locale');

                // Get the full content from data attribute, fallback to visible text content
                let fullContent = $element.data('full-content') || '';

                // Decode HTML entities if present
                if (fullContent) {
                    fullContent = decodeHtmlEntities(fullContent);
                }

                // If fullContent is still empty, try to get from the visible text (removing emoji and extra spaces)
                if (!fullContent) {
                    const visibleText = $element.text().trim();
                    // Remove flag emoji and clean up text
                    fullContent = visibleText.replace(/^🇻🇳|^🇬🇧/, '').trim();
                    // Remove placeholder text for empty English content
                    if (fullContent.includes('Click to add English translation') || fullContent.includes('Click to add content')) {
                        fullContent = '';
                    }
                }

                // Store references
                currentEditingElement = element;
                originalContent = fullContent;

                // Add editing class
                $element.addClass('editing').removeClass('empty-content');

                // Create textarea using vanilla JS for better control
                const textarea = document.createElement('textarea');
                textarea.className = 'inline-edit-input';
                textarea.setAttribute('data-key', key);
                textarea.setAttribute('data-locale', locale);
                textarea.value = fullContent;

                // Convert to jQuery object
                const $textarea = $(textarea);

                // Replace content with textarea
                $element.html($textarea);

                // Focus and select all text
                $textarea.focus().select();

                // Auto-resize textarea based on content
                autoResizeTextarea($textarea[0]);

                // Handle textarea events
                $textarea.on('input', function() {
                    autoResizeTextarea(this);
                });

                $textarea.on('keydown', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        saveInlineEdit(element);
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        cancelInlineEdit(element);
                    }
                });

                $textarea.on('blur', function() {
                    // Small delay to allow for other click events
                    setTimeout(() => {
                        if (currentEditingElement === element) {
                            saveInlineEdit(element);
                        }
                    }, 150);
                });
            }

            // Auto-resize textarea
            function autoResizeTextarea(textarea) {
                textarea.style.height = 'auto';
                textarea.style.height = Math.max(36, textarea.scrollHeight) + 'px';
            }

            // Save inline edit
            function saveInlineEdit(element) {
                const $element = $(element);
                const textarea = $element.find('.inline-edit-input');

                if (textarea.length === 0) return;

                const newContent = textarea.val().trim();
                const key = textarea.data('key');
                const locale = textarea.data('locale');

                // If content hasn't changed, just cancel
                if (newContent === originalContent) {
                    cancelInlineEdit(element);
                    return;
                }

                // Validate content
                if (newContent === '') {
                    showInlineError(element, 'Content cannot be empty');
                    return;
                }

                // Show saving state
                $element.removeClass('editing').addClass('saving');
                $element.html('<span>Saving...</span>');

                // Make AJAX request
                $.ajax({
                    url: '{{ route("dev.translations.update-inline") }}',
                    type: 'PATCH',
                    data: {
                        key: key,
                        locale: locale,
                        content: newContent,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showInlineSuccess(element, newContent);

                            // Update the data attribute
                            $element.data('full-content', newContent);

                            // Show success notification
                            showNotification('success', response.message);
                        } else {
                            showInlineError(element, response.message || 'Update failed');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        let errorMessage = 'An error occurred';

                        if (response && response.errors) {
                            errorMessage = Object.values(response.errors).flat().join(', ');
                        } else if (response && response.message) {
                            errorMessage = response.message;
                        }

                        showInlineError(element, errorMessage);
                    }
                });
            }

            // Cancel inline edit
            function cancelInlineEdit(element) {
                const $element = $(element);

                // Restore original content
                restoreOriginalContent(element, originalContent);

                // Clear editing state
                currentEditingElement = null;
                originalContent = '';
            }

            // Show inline success
            function showInlineSuccess(element, newContent) {
                const $element = $(element);

                $element.removeClass('saving editing').addClass('success');

                // Display truncated content
                const truncated = newContent.length > 80 ? newContent.substr(0, 80) + '...' : newContent;
                $element.html(truncated);

                // Remove success class after animation
                setTimeout(() => {
                    $element.removeClass('success');
                    currentEditingElement = null;
                    originalContent = '';
                }, 1000);
            }

            // Show inline error
            function showInlineError(element, message) {
                const $element = $(element);

                $element.removeClass('saving').addClass('error');

                // Show error message briefly, then restore edit mode
                $element.html(`<span class="text-danger">${message}</span>`);

                setTimeout(() => {
                    $element.removeClass('error');

                    // Restore textarea for continued editing
                    const textarea = $('<textarea>', {
                        class: 'inline-edit-input',
                        'data-key': $element.data('key'),
                        'data-locale': $element.data('locale')
                    }).text(originalContent);

                    $element.addClass('editing').html(textarea);
                    textarea.focus();
                    autoResizeTextarea(textarea[0]);

                    // Re-attach events
                    textarea.on('keydown', function(e) {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            saveInlineEdit(element);
                        } else if (e.key === 'Escape') {
                            e.preventDefault();
                            cancelInlineEdit(element);
                        }
                    });

                    textarea.on('blur', function() {
                        setTimeout(() => {
                            if (currentEditingElement === element) {
                                saveInlineEdit(element);
                            }
                        }, 150);
                    });

                }, 2000);

                showNotification('error', message);
            }

            // Restore original content
            function restoreOriginalContent(element, content) {
                const $element = $(element);
                const locale = $element.data('locale');

                $element.removeClass('editing saving success error');

                if (content) {
                    const truncated = content.length > 80 ? content.substr(0, 80) + '...' : content;
                    $element.html(truncated);
                } else {
                    $element.addClass('empty-content');
                    const placeholder = locale === 'en' ?
                        '<span class="text-muted fst-italic">Click to add English translation</span>' :
                        '<span class="text-muted fst-italic">Click to add content</span>';
                    $element.html(placeholder);
                }
            }

            // Show notification
            function showNotification(type, message) {
                const icon = type === 'success' ? 'success' : 'error';
                const title = type === 'success' ? 'Success!' : 'Error!';

                Swal.fire({
                    icon: icon,
                    title: title,
                    text: message,
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }

            // Handle clicks outside to save current edit
            $(document).on('click', function(e) {
                if (currentEditingElement && !$(e.target).closest('.editable-content, .inline-edit-input').length) {
                    saveInlineEdit(currentEditingElement);
                }
            });

            // Add Translation Form Submit
            $('#addTranslationForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Adding...').prop('disabled', true);

                $.ajax({
                    url: '{{ route("dev.translations.store") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#addTranslationModal').modal('hide');
                            $('#addTranslationForm')[0].reset();
                            table.ajax.reload();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        let errorMessage = 'An error occurred';

                        if (response && response.errors) {
                            errorMessage = Object.values(response.errors).flat().join('\n');
                        } else if (response && response.message) {
                            errorMessage = response.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Edit Translation Button Click
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const key = $(this).data('key');
                const content = $(this).data('content');
                const group = $(this).data('group');
                const active = $(this).data('active');

                $('#editTranslationId').val(id);
                $('#editTranslationKeyDisplay').text(key);
                $('#editTranslationContent').val(content);
                $('#editGroupName').val(group);
                $('#editIsActive').prop('checked', active == '1');

                $('#editTranslationModal').modal('show');
            });

            // Edit Translation Form Submit
            $('#editTranslationForm').on('submit', function(e) {
                e.preventDefault();

                const id = $('#editTranslationId').val();
                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...').prop('disabled', true);

                $.ajax({
                    url: `/dev/translations/${id}`,
                    type: 'PUT',
                    data: {
                        content: $('#editTranslationContent').val(),
                        group_name: $('#editGroupName').val(),
                        is_active: $('#editIsActive').is(':checked') ? 1 : 0,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#editTranslationModal').modal('hide');
                            table.ajax.reload();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        let errorMessage = 'An error occurred';

                        if (response && response.errors) {
                            errorMessage = Object.values(response.errors).flat().join('\n');
                        } else if (response && response.message) {
                            errorMessage = response.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Delete Translation Button Click
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const key = $(this).data('key');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `Delete translation key: ${key}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/dev/translations/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    table.ajax.reload();

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message,
                                        timer: 3000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function(xhr) {
                                const response = xhr.responseJSON;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message || 'An error occurred'
                                });
                            }
                        });
                    }
                });
            });

            // Auto-suggest group name based on key
            $('#translationKey').on('input', function() {
                const key = $(this).val();
                if (key.includes('.')) {
                    const suggestedGroup = key.split('.')[0];
                    if ($('#groupName option[value="' + suggestedGroup + '"]').length > 0) {
                        $('#groupName').val(suggestedGroup);
                    }
                }
            });

            // Auto-sync content between Vietnamese and English (optional helper)
            $('#translationContentEn').on('blur', function() {
                const enContent = $(this).val();
                const viContent = $('#translationContentVi').val();

                // If Vietnamese is empty and English is filled, suggest user to fill Vietnamese
                if (enContent && !viContent) {
                    $('#translationContentVi').attr('placeholder', 'Translate: "' + enContent.substring(0, 50) + (enContent.length > 50 ? '..."' : '"'));
                }
            });

            // Generate Missing English Translations
            $('#generateEnglishBtn').on('click', function() {
                const btn = $(this);
                const originalText = btn.html();

                Swal.fire({
                    title: 'Generate Missing English Translations?',
                    html: `
                        <p>This will automatically create English translations for all Vietnamese translations that don't have English counterparts.</p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Current Status:</strong><br>
                            Vietnamese: {{ number_format($stats['vietnamese_translations']) }} translations<br>
                            English: {{ number_format($stats['english_translations']) }} translations<br>
                            <strong>Missing: {{ number_format($stats['vietnamese_translations'] - $stats['english_translations']) }} English translations</strong>
                        </div>
                        <p class="text-muted small">Generated translations will use a mapping dictionary and can be manually edited later.</p>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-magic me-2"></i>Generate Now',
                    cancelButtonText: 'Cancel',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            url: '/dev/translations/generate-english',
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        const response = result.value;

                        if (response.success) {
                            // Reload the table to show new translations
                            table.ajax.reload();

                            // Show success message with details
                            Swal.fire({
                                icon: 'success',
                                title: 'English Translations Generated!',
                                html: `
                                    <div class="text-start">
                                        <p><strong>✅ Successfully created:</strong> ${response.created} English translations</p>
                                        <p><strong>📊 Total missing:</strong> ${response.total_missing} translations</p>
                                        ${response.errors.length > 0 ? `<p><strong>⚠️ Errors:</strong> ${response.errors.length} translations had issues</p>` : ''}
                                    </div>
                                    <div class="alert alert-success mt-3">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Page will reload to show updated statistics...
                                    </div>
                                `,
                                timer: 5000,
                                showConfirmButton: true,
                                confirmButtonText: 'Reload Page Now'
                            }).then(() => {
                                // Reload page to update statistics
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Generation Failed',
                                text: response.message || 'An error occurred while generating translations'
                            });
                        }
                    }
                }).catch((error) => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Request Failed',
                        text: 'Failed to communicate with server. Please try again.'
                    });
                });
            });
        });
    </script>
</body>
</html>
