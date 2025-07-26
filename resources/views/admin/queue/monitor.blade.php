@extends('admin.layouts.app')

@section('title', 'Queue Monitor')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tasks"></i> Queue Monitor
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" onclick="refreshStats()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button type="button" class="btn btn-warning" onclick="clearFailedJobs()">
                <i class="fas fa-trash"></i> Clear Failed Jobs
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Jobs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-jobs">
                                {{ $stats['total_jobs'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
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
                                Success Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="success-rate">
                                {{ $stats['success_rate'] }}%
                            </div>
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
                                Failed Jobs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="failed-jobs">
                                {{ $stats['failed_jobs'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                Email Jobs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="email-jobs">
                                {{ $stats['email_jobs'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Queue Sizes Chart -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Queue Sizes</h6>
                </div>
                <div class="card-body">
                    <canvas id="queueChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Performance Metrics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">
                                    Avg Processing Time
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['avg_processing_time'] }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">
                                    Processing Jobs
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800" id="processing-jobs">
                                    {{ $stats['processing_jobs'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Jobs -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Jobs</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="recent-jobs-table">
                            <thead>
                                <tr>
                                    <th>Job Class</th>
                                    <th>Queue</th>
                                    <th>Status</th>
                                    <th>Attempts</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentJobs as $job)
                                <tr>
                                    <td>{{ $job->job_class }}</td>
                                    <td><span class="badge badge-info">{{ $job->queue }}</span></td>
                                    <td>
                                        @if($job->status === 'Processing')
                                            <span class="badge badge-warning">Processing</span>
                                        @else
                                            <span class="badge badge-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $job->attempts }}</td>
                                    <td>{{ \Carbon\Carbon::parse($job->created_at)->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Failed Jobs</h6>
                </div>
                <div class="card-body">
                    @foreach($failedJobs->take(5) as $job)
                    <div class="card mb-2">
                        <div class="card-body p-2">
                            <h6 class="card-title mb-1">{{ $job->job_class }}</h6>
                            <p class="card-text small text-muted mb-1">{{ $job->error_message }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">{{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }}</small>
                                <button class="btn btn-sm btn-outline-primary" onclick="retryJob('{{ $job->uuid }}')">
                                    <i class="fas fa-redo"></i> Retry
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Queue sizes chart
const queueData = @json($queueSizes);
const ctx = document.getElementById('queueChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: queueData.map(q => q.queue),
        datasets: [{
            data: queueData.map(q => q.count),
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Auto refresh every 30 seconds
setInterval(refreshStats, 30000);

function refreshStats() {
    fetch('{{ route("admin.queue.api-stats") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-jobs').textContent = data.total_jobs;
            document.getElementById('success-rate').textContent = data.success_rate + '%';
            document.getElementById('failed-jobs').textContent = data.failed_jobs;
            document.getElementById('email-jobs').textContent = data.email_jobs;
            document.getElementById('processing-jobs').textContent = data.processing_jobs;
        });
}

function retryJob(uuid) {
    fetch('{{ route("admin.queue.retry") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({job_id: uuid})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function clearFailedJobs() {
    if (confirm('Bạn có chắc muốn xóa tất cả failed jobs?')) {
        fetch('{{ route("admin.queue.clear-failed") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        });
    }
}
</script>
@endpush
