@extends('layouts.app')

@section('title', 'Compare Manufacturing Processes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="fas fa-balance-scale text-primary"></i>
                            Compare Manufacturing Processes
                        </h1>
                        <p class="text-muted mb-0">Side-by-side comparison of {{ $processes->count() }} selected processes</p>
                    </div>
                    <div>
                        <a href="{{ route('manufacturing.processes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Processes
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($processes->count() < 2)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Please select at least 2 processes to compare.
                            <a href="{{ route('manufacturing.processes.index') }}" class="alert-link">Go back to select processes</a>.
                        </div>
                    @else
                        <!-- Comparison Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 200px;">Criteria</th>
                                        @foreach($processes as $process)
                                            <th class="text-center">
                                                <div class="d-flex flex-column align-items-center">
                                                    <h5 class="mb-1">{{ $process->name }}</h5>
                                                    <span class="badge bg-primary">{{ $process->category }}</span>
                                                    @if($process->subcategory)
                                                        <span class="badge bg-secondary mt-1">{{ $process->subcategory }}</span>
                                                    @endif
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Basic Information -->
                                    <tr class="table-light">
                                        <td colspan="{{ $processes->count() + 1 }}">
                                            <strong><i class="fas fa-info-circle text-primary"></i> Basic Information</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Process Code</strong></td>
                                        @foreach($processes as $process)
                                            <td class="text-center">{{ $process->code }}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td><strong>Process Type</strong></td>
                                        @foreach($processes as $process)
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $process->process_type }}</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td><strong>Description</strong></td>
                                        @foreach($processes as $process)
                                            <td>
                                                <small>{{ Str::limit($process->description, 100) }}</small>
                                            </td>
                                        @endforeach
                                    </tr>

                                    <!-- Cost & Time -->
                                    <tr class="table-light">
                                        <td colspan="{{ $processes->count() + 1 }}">
                                            <strong><i class="fas fa-dollar-sign text-success"></i> Cost & Time</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Setup Cost</strong></td>
                                        @foreach($processes as $process)
                                            <td class="text-center">
                                                <span class="text-success fw-bold">${{ number_format($process->setup_cost, 2) }}</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td><strong>Lead Time</strong></td>
                                        @foreach($processes as $process)
                                            <td class="text-center">
                                                <span class="text-info">{{ $process->lead_time_days }} days</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td><strong>Minimum Quantity</strong></td>
                                        @foreach($processes as $process)
                                            <td class="text-center">
                                                <span class="text-warning">{{ number_format($process->minimum_quantity) }} units</span>
                                            </td>
                                        @endforeach
                                    </tr>

                                    <!-- Materials -->
                                    <tr class="table-light">
                                        <td colspan="{{ $processes->count() + 1 }}">
                                            <strong><i class="fas fa-cube text-warning"></i> Materials</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Compatible Materials</strong></td>
                                        @foreach($processes as $process)
                                            <td>
                                                @if($process->materials_compatible)
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach(json_decode($process->materials_compatible) as $material)
                                                            <span class="badge bg-outline-primary border">{{ $material }}</span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not specified</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>

                                    <!-- Equipment -->
                                    <tr class="table-light">
                                        <td colspan="{{ $processes->count() + 1 }}">
                                            <strong><i class="fas fa-tools text-secondary"></i> Equipment</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Required Equipment</strong></td>
                                        @foreach($processes as $process)
                                            <td>
                                                @if($process->required_equipment)
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach(json_decode($process->required_equipment) as $equipment)
                                                            <li><small><i class="fas fa-check text-success"></i> {{ $equipment }}</small></li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">Not specified</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>

                                    <!-- Applications -->
                                    <tr class="table-light">
                                        <td colspan="{{ $processes->count() + 1 }}">
                                            <strong><i class="fas fa-cogs text-primary"></i> Applications</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Typical Applications</strong></td>
                                        @foreach($processes as $process)
                                            <td>
                                                @if($process->typical_applications)
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach(json_decode($process->typical_applications) as $application)
                                                            <li><small><i class="fas fa-arrow-right text-primary"></i> {{ $application }}</small></li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">Not specified</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>

                                    <!-- Status & Metrics -->
                                    <tr class="table-light">
                                        <td colspan="{{ $processes->count() + 1 }}">
                                            <strong><i class="fas fa-chart-line text-info"></i> Status & Metrics</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        @foreach($processes as $process)
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ ucfirst($process->status) }}</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td><strong>Featured</strong></td>
                                        @foreach($processes as $process)
                                            <td class="text-center">
                                                @if($process->is_featured)
                                                    <span class="badge bg-warning">Yes</span>
                                                @else
                                                    <span class="badge bg-light text-dark">No</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td><strong>Usage Count</strong></td>
                                        @foreach($processes as $process)
                                            <td class="text-center">
                                                <span class="text-primary">{{ number_format($process->usage_count) }}</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td><strong>View Count</strong></td>
                                        @foreach($processes as $process)
                                            <td class="text-center">
                                                <span class="text-secondary">{{ number_format($process->view_count) }}</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td><strong>Last Updated</strong></td>
                                        @foreach($processes as $process)
                                            <td class="text-center">
                                                <small class="text-muted">{{ $process->updated_at->format('M d, Y') }}</small>
                                            </td>
                                        @endforeach
                                    </tr>

                                    <!-- Actions -->
                                    <tr class="table-light">
                                        <td colspan="{{ $processes->count() + 1 }}">
                                            <strong><i class="fas fa-bolt text-warning"></i> Actions</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>View Details</strong></td>
                                        @foreach($processes as $process)
                                            <td class="text-center">
                                                <a href="{{ route('manufacturing.processes.show', $process) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Section -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-lightbulb text-warning"></i> Comparison Summary
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h6>Lowest Setup Cost:</h6>
                                                @php
                                                    $lowestCost = $processes->min('setup_cost');
                                                    $lowestCostProcess = $processes->where('setup_cost', $lowestCost)->first();
                                                @endphp
                                                <p class="text-success">
                                                    <strong>{{ $lowestCostProcess->name }}</strong> - ${{ number_format($lowestCost, 2) }}
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <h6>Shortest Lead Time:</h6>
                                                @php
                                                    $shortestTime = $processes->min('lead_time_days');
                                                    $shortestTimeProcess = $processes->where('lead_time_days', $shortestTime)->first();
                                                @endphp
                                                <p class="text-info">
                                                    <strong>{{ $shortestTimeProcess->name }}</strong> - {{ $shortestTime }} days
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <h6>Lowest Min Quantity:</h6>
                                                @php
                                                    $lowestQty = $processes->min('minimum_quantity');
                                                    $lowestQtyProcess = $processes->where('minimum_quantity', $lowestQty)->first();
                                                @endphp
                                                <p class="text-warning">
                                                    <strong>{{ $lowestQtyProcess->name }}</strong> - {{ number_format($lowestQty) }} units
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('manufacturing.processes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Processes
                            </a>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> Print Comparison
                            </button>
                            <button type="button" class="btn btn-success">
                                <i class="fas fa-download"></i> Export PDF
                            </button>
                        </div>
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

@media print {
    .card-footer,
    .btn {
        display: none !important;
    }
}
</style>
@endpush
