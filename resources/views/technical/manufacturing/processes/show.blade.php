@extends('layouts.app')

@section('title', $process->name . ' - Manufacturing Process Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">{{ $process->name }}</h1>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-primary">{{ $process->category }}</span>
                            @if($process->subcategory)
                                <span class="badge bg-secondary">{{ $process->subcategory }}</span>
                            @endif
                            <span class="badge bg-info">{{ $process->process_type }}</span>
                            @if($process->is_featured)
                                <span class="badge bg-warning">Featured</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-bookmark"></i> Save
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-share"></i> Share
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Process Overview -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4>Process Overview</h4>
                            <p class="lead">{{ $process->description }}</p>
                        </div>
                    </div>

                    <!-- Key Specifications -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-cogs text-primary"></i> Key Specifications
                                    </h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Setup Cost:</strong><br>
                                            <span class="text-success">${{ number_format($process->setup_cost, 2) }}</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Lead Time:</strong><br>
                                            <span class="text-info">{{ $process->lead_time_days }} days</span>
                                        </div>
                                        <div class="col-6 mt-2">
                                            <strong>Min Quantity:</strong><br>
                                            <span class="text-warning">{{ number_format($process->minimum_quantity) }} units</span>
                                        </div>
                                        <div class="col-6 mt-2">
                                            <strong>Status:</strong><br>
                                            <span class="badge bg-success">{{ ucfirst($process->status) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-chart-line text-success"></i> Process Metrics
                                    </h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Usage Count:</strong><br>
                                            <span class="text-primary">{{ number_format($process->usage_count) }}</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>View Count:</strong><br>
                                            <span class="text-secondary">{{ number_format($process->view_count) }}</span>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <strong>Last Updated:</strong><br>
                                            <span class="text-muted">{{ $process->updated_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compatible Materials -->
                    @if($process->materials_compatible)
                        <div class="mb-4">
                            <h4>Compatible Materials</h4>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(json_decode($process->materials_compatible) as $material)
                                    <span class="badge bg-outline-primary border">{{ $material }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Required Equipment -->
                    @if($process->required_equipment)
                        <div class="mb-4">
                            <h4>Required Equipment</h4>
                            <ul class="list-group list-group-flush">
                                @foreach(json_decode($process->required_equipment) as $equipment)
                                    <li class="list-group-item d-flex align-items-center">
                                        <i class="fas fa-tools text-primary me-2"></i>
                                        {{ $equipment }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Typical Applications -->
                    @if($process->typical_applications)
                        <div class="mb-4">
                            <h4>Typical Applications</h4>
                            <div class="row">
                                @foreach(json_decode($process->typical_applications) as $application)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            {{ $application }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Process Details -->
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Process Information</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td><strong>Process Code</strong></td>
                                            <td>{{ $process->code }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Process Type</strong></td>
                                            <td>{{ $process->process_type }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Category</strong></td>
                                            <td>{{ $process->category }}</td>
                                        </tr>
                                        @if($process->subcategory)
                                        <tr>
                                            <td><strong>Subcategory</strong></td>
                                            <td>{{ $process->subcategory }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Special Handling Required</strong></td>
                                            <td>
                                                @if($process->requires_special_handling)
                                                    <span class="badge bg-warning">Yes</span>
                                                @else
                                                    <span class="badge bg-success">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created</strong></td>
                                            <td>{{ $process->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated</strong></td>
                                            <td>{{ $process->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('manufacturing.processes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Processes
                            </a>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-calculator"></i> Cost Calculator
                            </button>
                            <button type="button" class="btn btn-success">
                                <i class="fas fa-envelope"></i> Request Quote
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Related Processes -->
            @if($relatedProcesses && $relatedProcesses->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-sitemap text-primary"></i> Related Processes
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($relatedProcesses as $related)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('manufacturing.processes.show', $related) }}" class="text-decoration-none">
                                            {{ $related->name }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">{{ $related->category }}</small>
                                </div>
                                <div>
                                    <span class="badge bg-light text-dark">${{ number_format($related->setup_cost, 0) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt text-warning"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('manufacturing.processes.calculator') }}" class="btn btn-outline-primary">
                            <i class="fas fa-calculator"></i> Cost Calculator
                        </a>
                        <a href="{{ route('manufacturing.processes.selector') }}" class="btn btn-outline-info">
                            <i class="fas fa-search"></i> Process Selector
                        </a>
                        <button type="button" class="btn btn-outline-success">
                            <i class="fas fa-download"></i> Download Spec Sheet
                        </button>
                        <button type="button" class="btn btn-outline-warning">
                            <i class="fas fa-question-circle"></i> Get Expert Help
                        </button>
                    </div>
                </div>
            </div>

            <!-- Process Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar text-info"></i> Process Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-0">{{ number_format($process->view_count) }}</h4>
                                <small class="text-muted">Views</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-0">{{ number_format($process->usage_count) }}</h4>
                            <small class="text-muted">Used</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">
                            Added {{ $process->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.badge.bg-outline-primary {
    background-color: transparent !important;
    color: var(--bs-primary) !important;
    border: 1px solid var(--bs-primary) !important;
}
</style>
@endpush
