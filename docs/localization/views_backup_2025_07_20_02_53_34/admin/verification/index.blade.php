@extends('admin.layouts.app')

@section('title', 'Business Verification Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building text-primary"></i>
                Business Verification Management
            </h1>
            <p class="text-muted mb-0">Manage and review business verification applications</p>
        </div>
        <div>
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#analyticsModal">
                <i class="fas fa-chart-bar"></i> Analytics
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Review
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Under Review
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['under_review'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-search fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approved
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejected
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejected'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filters & Search
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.verification.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="under_review" {{ $status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="requires_additional_info" {{ $status === 'requires_additional_info' ? 'selected' : '' }}>Requires Info</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="type" class="form-label">Application Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="manufacturer" {{ $type === 'manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                        <option value="supplier" {{ $type === 'supplier' ? 'selected' : '' }}>Supplier</option>
                        <option value="brand" {{ $type === 'brand' ? 'selected' : '' }}>Brand</option>
                        <option value="verified_partner" {{ $type === 'verified_partner' ? 'selected' : '' }}>Verified Partner</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="priority" class="form-label">Priority</label>
                    <select name="priority" id="priority" class="form-select">
                        <option value="">All Priorities</option>
                        <option value="urgent" {{ $priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ $priority === 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ $priority === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ $priority === 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Business name, tax ID, user..." value="{{ $search }}">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.verification.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                    <div class="float-end">
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-sort"></i> Sort by: {{ ucfirst(str_replace('_', ' ', $sortBy)) }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'submitted_at', 'sort_order' => 'desc']) }}">Latest Submitted</a></li>
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'business_name', 'sort_order' => 'asc']) }}">Business Name</a></li>
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'priority_level', 'sort_order' => 'desc']) }}">Priority</a></li>
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_order' => 'asc']) }}">Status</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Business Verification Applications
            </h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleBulkActions()">
                    <i class="fas fa-check-square"></i> Bulk Actions
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($applications->count() > 0)
                <!-- Bulk Actions Bar (Hidden by default) -->
                <div id="bulkActionsBar" class="alert alert-info d-none mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <span id="selectedCount">0</span> applications selected
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('approve')">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('reject')">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                                <button type="button" class="btn btn-sm btn-info" onclick="bulkAction('assign_reviewer')">
                                    <i class="fas fa-user-plus"></i> Assign Reviewer
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" onclick="bulkAction('set_priority')">
                                    <i class="fas fa-flag"></i> Set Priority
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>Business Info</th>
                                <th>Applicant</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Submitted</th>
                                <th>Reviewer</th>
                                <th>Progress</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $application)
                                <tr class="{{ $application->is_overdue ? 'table-warning' : '' }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input application-checkbox" 
                                               value="{{ $application->id }}">
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $application->business_name }}</div>
                                        <small class="text-muted">Tax ID: {{ $application->tax_id }}</small>
                                        @if($application->is_overdue)
                                            <br><span class="badge bg-warning">Overdue</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $application->user->name }}</div>
                                        <small class="text-muted">{{ $application->user->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $application->application_type_display }}</span>
                                    </td>
                                    <td>{!! $application->status_badge !!}</td>
                                    <td>{!! $application->priority_badge !!}</td>
                                    <td>
                                        <div>{{ $application->submitted_at?->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $application->days_in_review }} days ago</small>
                                    </td>
                                    <td>
                                        @if($application->reviewer)
                                            <div>{{ $application->reviewer->name }}</div>
                                            <small class="text-muted">{{ $application->reviewed_at?->format('d/m/Y') }}</small>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $application->completion_percentage }}%"
                                                 aria-valuenow="{{ $application->completion_percentage }}" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                {{ $application->completion_percentage }}%
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ $application->documents->where('verification_status', 'verified')->count() }}/{{ $application->documents->count() }} docs verified
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.verification.applications.show', $application) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($application->canBeReviewed())
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="quickApprove({{ $application->id }})" title="Quick Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="quickReject({{ $application->id }})" title="Quick Reject">
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

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }} 
                        of {{ $applications->total() }} applications
                    </div>
                    <div>
                        {{ $applications->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No applications found</h5>
                    <p class="text-muted">No business verification applications match your current filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Include modals and scripts -->
@include('admin.verification.partials.modals')
@endsection

@push('scripts')
<script src="{{ asset('js/admin/verification.js') }}"></script>
@endpush
