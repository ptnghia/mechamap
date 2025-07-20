@extends('layouts.app')

@section('title', 'Download Files - Order #' . $order->order_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('marketplace.index') }}">Marketplace</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('marketplace.orders.index') }}">My Orders</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('marketplace.orders.show', $order) }}">Order #{{ $order->order_number }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Download Files</li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Download Files</h1>
                    <p class="text-muted mb-0">Order #{{ $order->order_number }} - {{ $item->product_name }}</p>
                </div>
                <div>
                    <a href="{{ route('marketplace.orders.show', $order) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Order
                    </a>
                </div>
            </div>

            <!-- Order Info Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">{{ $item->product_name }}</h5>
                            <p class="text-muted mb-2">SKU: {{ $item->product_sku }}</p>
                            <p class="mb-0">{{ $item->product_description }}</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="mb-2">
                                <span class="badge bg-success">{{ ucfirst($order->payment_status) }}</span>
                                <span class="badge bg-info">{{ ucfirst($order->status) }}</span>
                            </div>
                            <p class="h5 mb-0">${{ number_format($item->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download Security Notice -->
            <div class="alert alert-info mb-4">
                <div class="d-flex">
                    <i class="fas fa-shield-alt me-3 mt-1"></i>
                    <div>
                        <h6 class="alert-heading mb-2">Secure Download Information</h6>
                        <ul class="mb-0 small">
                            <li>Files are protected and can only be downloaded by verified purchasers</li>
                            <li>Download links are temporary and expire after use</li>
                            <li>Each file can be downloaded a limited number of times based on your license</li>
                            <li>All downloads are tracked for security purposes</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Available Files -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-download me-2"></i>Available Files ({{ $digitalFiles->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($digitalFiles->count() > 0)
                        <div class="row">
                            @foreach($digitalFiles as $file)
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex align-items-start">
                                            <!-- File Icon -->
                                            <div class="me-3">
                                                @switch($file->file_category)
                                                    @case('cad_drawing')
                                                        <i class="fas fa-drafting-compass text-primary fa-2x"></i>
                                                        @break
                                                    @case('cad_model')
                                                        <i class="fas fa-cube text-success fa-2x"></i>
                                                        @break
                                                    @case('technical_doc')
                                                        <i class="fas fa-file-pdf text-danger fa-2x"></i>
                                                        @break
                                                    @default
                                                        <i class="fas fa-file text-secondary fa-2x"></i>
                                                @endswitch
                                            </div>

                                            <!-- File Info -->
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $file->file_name }}</h6>
                                                <p class="text-muted small mb-2">
                                                    {{ ucfirst(str_replace('_', ' ', $file->file_category)) }}
                                                    @if($file->cad_software)
                                                        • {{ $file->cad_software }}
                                                        @if($file->cad_version)
                                                            {{ $file->cad_version }}
                                                        @endif
                                                    @endif
                                                </p>
                                                <p class="small text-muted mb-2">
                                                    Size: {{ number_format($file->file_size / 1024 / 1024, 2) }} MB
                                                    • Type: {{ strtoupper($file->file_extension) }}
                                                </p>

                                                <!-- Download Button -->
                                                <button class="btn btn-primary btn-sm generate-download-btn"
                                                        data-file-id="{{ $file->id }}"
                                                        data-order-id="{{ $order->id }}"
                                                        data-item-id="{{ $item->id }}">
                                                    <i class="fas fa-download me-1"></i>Generate Download Link
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
                            <h5 class="text-muted">No Files Available</h5>
                            <p class="text-muted">There are no digital files available for download for this product.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Order Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Order Number:</strong><br>{{ $order->order_number }}</p>
                    <p><strong>Status:</strong><br>
                        <span class="badge bg-success">{{ ucfirst($order->payment_status) }}</span>
                        <span class="badge bg-info">{{ ucfirst($order->status) }}</span>
                    </p>
                    <p><strong>Total Amount:</strong><br>${{ number_format($order->total_amount, 2) }}</p>
                    <p><strong>Order Date:</strong><br>{{ $order->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Download Modal -->
<div class="modal fade" id="downloadModal" tabindex="-1" aria-labelledby="downloadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="downloadModalLabel">Secure Download</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="downloadContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle generate download button clicks
    document.querySelectorAll('.generate-download-btn').forEach(button => {
        button.addEventListener('click', function() {
            const fileId = this.dataset.fileId;
            const orderId = this.dataset.orderId;
            const itemId = this.dataset.itemId;

            // Disable button and show loading
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Generating...';

            // Generate secure download token (this would call the secure download API)
            generateDownloadToken(fileId, orderId, itemId, this);
        });
    });
});

function generateDownloadToken(fileId, orderId, itemId, button) {
    // For now, show a placeholder modal
    // In production, this would call the secure download API
    const modal = new bootstrap.Modal(document.getElementById('downloadModal'));
    const content = document.getElementById('downloadContent');

    content.innerHTML = `
        <div class="text-center">
            <i class="fas fa-shield-alt text-success fa-3x mb-3"></i>
            <h5>Secure Download System</h5>
            <p class="text-muted">This feature will integrate with the secure download system to generate temporary, protected download links.</p>
            <div class="alert alert-info">
                <strong>File ID:</strong> ${fileId}<br>
                <strong>Order ID:</strong> ${orderId}<br>
                <strong>Item ID:</strong> ${itemId}
            </div>
            <p class="small text-muted">The secure download system includes token-based authentication, download limits, and anti-piracy protection.</p>
        </div>
    `;

    modal.show();

    // Re-enable button
    button.disabled = false;
    button.innerHTML = '<i class="fas fa-download me-1"></i>Generate Download Link';
}
</script>
@endpush
