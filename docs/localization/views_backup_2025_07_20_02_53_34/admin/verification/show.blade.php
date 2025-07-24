@extends('admin.layouts.app')

@section('title', 'Business Verification Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.verification.index') }}">Business Verification</a>
                    </li>
                    <li class="breadcrumb-item active">Application #{{ $application->id }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building text-primary"></i>
                {{ $application->business_name }}
            </h1>
            <p class="text-muted mb-0">{{ $application->application_type_display }} Verification</p>
        </div>
        <div>
            <a href="{{ route('admin.verification.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Application Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Application Status
                    </h6>
                    <div>
                        {!! $application->status_badge !!}
                        {!! $application->priority_badge !!}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Submitted Date:</label>
                                <div>{{ $application->submitted_at?->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Days in Review:</label>
                                <div>{{ $application->days_in_review }} days</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estimated Review Time:</label>
                                <div>{{ $application->estimated_review_time }} hours</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Completion Progress:</label>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $application->completion_percentage }}%"
                                         aria-valuenow="{{ $application->completion_percentage }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ $application->completion_percentage }}%
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Verification Score:</label>
                                <div>
                                    <span class="badge bg-{{ $application->verification_score >= 80 ? 'success' : ($application->verification_score >= 60 ? 'warning' : 'danger') }}">
                                        {{ $application->verification_score }}/100
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($application->canBeReviewed())
                        <div class="border-top pt-3 mt-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-success w-100" 
                                            onclick="approveApplication({{ $application->id }})">
                                        <i class="fas fa-check"></i> Approve Application
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-danger w-100" 
                                            onclick="rejectApplication({{ $application->id }})">
                                        <i class="fas fa-times"></i> Reject Application
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-warning w-100" 
                                            onclick="requestAdditionalInfo({{ $application->id }})">
                                        <i class="fas fa-question-circle"></i> Request Info
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Business Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building"></i> Business Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Business Name:</label>
                                <div>{{ $application->business_name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Business Type:</label>
                                <div>{{ $application->business_type }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tax ID:</label>
                                <div>{{ $application->tax_id }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Registration Number:</label>
                                <div>{{ $application->registration_number ?: 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Business Phone:</label>
                                <div>{{ $application->business_phone ?: 'N/A' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Business Email:</label>
                                <div>{{ $application->business_email ?: 'N/A' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Website:</label>
                                <div>
                                    @if($application->business_website)
                                        <a href="{{ $application->business_website }}" target="_blank">
                                            {{ $application->business_website }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Years in Business:</label>
                                <div>{{ $application->years_in_business ?: 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Business Address:</label>
                        <div>{{ $application->business_address }}</div>
                    </div>

                    @if($application->business_description)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Business Description:</label>
                            <div>{{ $application->business_description }}</div>
                        </div>
                    @endif

                    @if($application->business_categories)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Business Categories:</label>
                            <div>
                                @foreach($application->business_categories as $category)
                                    <span class="badge bg-secondary me-1">{{ $category }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Documents Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt"></i> Verification Documents
                    </h6>
                </div>
                <div class="card-body">
                    @if($application->documents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Document Type</th>
                                        <th>File Name</th>
                                        <th>Size</th>
                                        <th>Status</th>
                                        <th>Verified By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($application->documents as $document)
                                        <tr>
                                            <td>{{ $document->document_type_display }}</td>
                                            <td>
                                                <div>{{ $document->document_name }}</div>
                                                <small class="text-muted">{{ $document->original_filename }}</small>
                                            </td>
                                            <td>{{ $document->file_size_human }}</td>
                                            <td>{!! $document->verification_status_badge !!}</td>
                                            <td>
                                                @if($document->verifier)
                                                    <div>{{ $document->verifier->name }}</div>
                                                    <small class="text-muted">{{ $document->verified_at?->format('d/m/Y') }}</small>
                                                @else
                                                    <span class="text-muted">Not verified</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    @if($document->canBePreewed())
                                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                                onclick="previewDocument({{ $document->id }})">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    @endif
                                                    <a href="{{ route('admin.verification.documents.download', $document) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if($document->canBeVerified())
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="verifyDocument({{ $document->id }}, 'verified')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="verifyDocument({{ $document->id }}, 'rejected')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No documents uploaded</h5>
                            <p class="text-muted">The applicant has not uploaded any verification documents yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Audit Trail Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history"></i> Audit Trail
                    </h6>
                </div>
                <div class="card-body">
                    @if($application->auditTrail->count() > 0)
                        <div class="timeline">
                            @foreach($application->auditTrail->sortByDesc('created_at') as $audit)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">{{ $audit->action_description }}</h6>
                                        <p class="timeline-text">
                                            <strong>{{ $audit->performedBy->name }}</strong>
                                            <span class="text-muted">{{ $audit->created_at->format('d/m/Y H:i') }}</span>
                                        </p>
                                        @if($audit->notes)
                                            <div class="alert alert-light">
                                                <small>{{ $audit->notes }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No audit trail available.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Applicant Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user"></i> Applicant Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="{{ $application->user->avatar_url ?? asset('images/default-avatar.png') }}" 
                             alt="Avatar" class="rounded-circle" width="80" height="80">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Name:</label>
                        <div>{{ $application->user->name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email:</label>
                        <div>{{ $application->user->email }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Role:</label>
                        <div>
                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $application->user->role)) }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Member Since:</label>
                        <div>{{ $application->user->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Reviewer Assignment -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-check"></i> Reviewer Assignment
                    </h6>
                </div>
                <div class="card-body">
                    @if($application->reviewer)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Reviewer:</label>
                            <div>{{ $application->reviewer->name }}</div>
                            <small class="text-muted">Assigned: {{ $application->reviewed_at?->format('d/m/Y H:i') }}</small>
                        </div>
                    @endif

                    @if($application->canBeReviewed())
                        <form onsubmit="assignReviewer(event, {{ $application->id }})">
                            <div class="mb-3">
                                <label for="reviewer_id" class="form-label">Assign Reviewer:</label>
                                <select name="reviewer_id" id="reviewer_id" class="form-select" required>
                                    <option value="">Select Reviewer</option>
                                    @foreach($reviewers as $reviewer)
                                        <option value="{{ $reviewer->id }}" 
                                                {{ $application->reviewed_by == $reviewer->id ? 'selected' : '' }}>
                                            {{ $reviewer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-user-plus"></i> Assign Reviewer
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-info" 
                                onclick="sendNotification({{ $application->id }})">
                            <i class="fas fa-bell"></i> Send Notification
                        </button>
                        <button type="button" class="btn btn-outline-warning" 
                                onclick="addInternalNote({{ $application->id }})">
                            <i class="fas fa-sticky-note"></i> Add Internal Note
                        </button>
                        <button type="button" class="btn btn-outline-secondary" 
                                onclick="exportApplication({{ $application->id }})">
                            <i class="fas fa-file-export"></i> Export Application
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include modals -->
@include('admin.verification.partials.modals')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/verification.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/admin/verification.js') }}"></script>
<script>
    const applicationId = {{ $application->id }};
</script>
@endpush
