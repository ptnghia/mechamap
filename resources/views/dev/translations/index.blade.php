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
                            <i class="fas fa-table me-2"></i>Vietnamese Translations
                        </h5>
                        <div class="text-muted">
                            <small><i class="fas fa-info-circle me-1"></i>Click on any row to edit</small>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="translationsTable" class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Key</th>
                                    <th>Content</th>
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
                    { data: 'key', width: '25%' },
                    {
                        data: 'content',
                        width: '30%',
                        render: function(data, type, row) {
                            if (type === 'display' && data.length > 100) {
                                return data.substr(0, 100) + '...';
                            }
                            return data;
                        }
                    },
                    { data: 'group_name', width: '15%' },
                    {
                        data: 'is_active',
                        width: '80px',
                        render: function(data, type, row) {
                            return data === 'Active'
                                ? '<span class="badge bg-success">Active</span>'
                                : '<span class="badge bg-secondary">Inactive</span>';
                        }
                    },
                    { data: 'created_at', width: '120px' },
                    { data: 'updated_at', width: '120px' },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        width: '100px'
                    }
                ],
                order: [[3, 'asc'], [1, 'asc']], // Sort by group, then key
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
        });
    </script>
</body>
</html>
