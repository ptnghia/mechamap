@extends('layouts.app')

@section('title', 'Track Order #' . $order->order_number . ' - MechaMap Marketplace')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.index') }}">Marketplace</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.orders.index') }}">My Orders</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.orders.show', $order) }}">Order #{{ $order->order_number }}</a></li>
            <li class="breadcrumb-item active">Tracking</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-map text-primary me-2"></i>
                        Track Order #{{ $order->order_number }}
                    </h1>
                    <p class="text-muted mb-0">Real-time tracking information for your order</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('marketplace.orders.show', $order) }}" class="btn btn-outline-primary">
                        <i class="bx bx-arrow-back me-1"></i>
                        Back to Order
                    </a>
                    <button class="btn btn-outline-secondary" onclick="refreshTracking()">
                        <i class="bx bx-refresh me-1"></i>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tracking Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card tracking-summary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="tracking-info">
                                <h6 class="text-muted mb-1">TRACKING NUMBER</h6>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold me-2">{{ $order->tracking_number }}</span>
                                    <button class="btn btn-sm btn-outline-primary" onclick="copyTracking()">
                                        <i class="bx bx-copy"></i>
                                    </button>
                                </div>
                                @if($order->carrier)
                                <div class="text-muted small">Carrier: {{ $order->carrier }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="tracking-info">
                                <h6 class="text-muted mb-1">CURRENT STATUS</h6>
                                <span class="badge bg-{{ $order->getStatusColor() }} fs-6">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="tracking-info">
                                <h6 class="text-muted mb-1">ESTIMATED DELIVERY</h6>
                                <div class="fw-medium">
                                    {{ $order->estimated_delivery_date ? $order->estimated_delivery_date->format('M d, Y') : 'TBD' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="tracking-info">
                                <h6 class="text-muted mb-1">LAST UPDATE</h6>
                                <div class="fw-medium">
                                    {{ $order->updated_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tracking Progress -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-route me-2"></i>
                        Shipping Progress
                    </h5>
                </div>
                <div class="card-body">
                    <div class="tracking-progress">
                        <div class="progress-steps">
                            <div class="step {{ $order->isStatusPassed('confirmed') ? 'completed' : ($order->status == 'confirmed' ? 'active' : '') }}">
                                <div class="step-icon">
                                    <i class="bx bx-check-circle"></i>
                                </div>
                                <div class="step-content">
                                    <h6>Order Confirmed</h6>
                                    <small class="text-muted">
                                        {{ $order->confirmed_at ? $order->confirmed_at->format('M d, g:i A') : 'Pending' }}
                                    </small>
                                </div>
                            </div>

                            <div class="step {{ $order->isStatusPassed('processing') ? 'completed' : ($order->status == 'processing' ? 'active' : '') }}">
                                <div class="step-icon">
                                    <i class="bx bx-cog"></i>
                                </div>
                                <div class="step-content">
                                    <h6>Processing</h6>
                                    <small class="text-muted">
                                        {{ $order->processing_at ? $order->processing_at->format('M d, g:i A') : 'Pending' }}
                                    </small>
                                </div>
                            </div>

                            <div class="step {{ $order->isStatusPassed('shipped') ? 'completed' : ($order->status == 'shipped' ? 'active' : '') }}">
                                <div class="step-icon">
                                    <i class="bx bx-package"></i>
                                </div>
                                <div class="step-content">
                                    <h6>Shipped</h6>
                                    <small class="text-muted">
                                        {{ $order->shipped_at ? $order->shipped_at->format('M d, g:i A') : 'Pending' }}
                                    </small>
                                </div>
                            </div>

                            <div class="step {{ $order->isStatusPassed('out_for_delivery') ? 'completed' : ($order->status == 'out_for_delivery' ? 'active' : '') }}">
                                <div class="step-icon">
                                    <i class="bx bx-truck"></i>
                                </div>
                                <div class="step-content">
                                    <h6>Out for Delivery</h6>
                                    <small class="text-muted">
                                        {{ $order->out_for_delivery_at ? $order->out_for_delivery_at->format('M d, g:i A') : 'Pending' }}
                                    </small>
                                </div>
                            </div>

                            <div class="step {{ $order->isStatusPassed('delivered') ? 'completed' : ($order->status == 'delivered' ? 'active' : '') }}">
                                <div class="step-icon">
                                    <i class="bx bx-check-circle"></i>
                                </div>
                                <div class="step-content">
                                    <h6>Delivered</h6>
                                    <small class="text-muted">
                                        {{ $order->delivered_at ? $order->delivered_at->format('M d, g:i A') : 'Pending' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tracking Details -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-list-ul me-2"></i>
                        Tracking History
                    </h5>
                </div>
                <div class="card-body">
                    @if($trackingEvents && count($trackingEvents) > 0)
                    <div class="tracking-timeline">
                        @foreach($trackingEvents as $event)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $event['status_color'] ?? 'primary' }}">
                                <i class="bx {{ $event['icon'] ?? 'bx-circle' }}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $event['status'] }}</h6>
                                        <p class="text-muted mb-1">{{ $event['description'] }}</p>
                                        @if($event['location'])
                                        <div class="text-muted small">
                                            <i class="bx bx-map-pin me-1"></i>
                                            {{ $event['location'] }}
                                        </div>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-medium">{{ $event['date'] }}</div>
                                        <div class="text-muted small">{{ $event['time'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bx bx-info-circle display-4 text-muted"></i>
                        <h5 class="mt-3">No Tracking Information Available</h5>
                        <p class="text-muted">Tracking information will appear here once your order is shipped.</p>
                        <button class="btn btn-primary" onclick="refreshTracking()">
                            <i class="bx bx-refresh me-1"></i>
                            Check for Updates
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Delivery Instructions -->
            @if($order->delivery_instructions)
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-note me-2"></i>
                        Delivery Instructions
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->delivery_instructions }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Delivery Address -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-map me-2"></i>
                        Delivery Address
                    </h6>
                </div>
                <div class="card-body">
                    @if($order->shipping_address)
                    <div class="address-info">
                        <div class="fw-medium">{{ $order->shipping_address['name'] ?? '' }}</div>
                        <div>{{ $order->shipping_address['address_line_1'] ?? '' }}</div>
                        @if($order->shipping_address['address_line_2'] ?? '')
                        <div>{{ $order->shipping_address['address_line_2'] }}</div>
                        @endif
                        <div>
                            {{ $order->shipping_address['city'] ?? '' }}, 
                            {{ $order->shipping_address['state'] ?? '' }} 
                            {{ $order->shipping_address['postal_code'] ?? '' }}
                        </div>
                        <div>{{ $order->shipping_address['country'] ?? '' }}</div>
                        @if($order->shipping_address['phone'] ?? '')
                        <div class="mt-2">
                            <i class="bx bx-phone me-1"></i>
                            {{ $order->shipping_address['phone'] }}
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Package Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-package me-2"></i>
                        Package Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="package-info">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Items:</span>
                            <span>{{ $order->items->sum('quantity') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Weight:</span>
                            <span>{{ $order->total_weight ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Dimensions:</span>
                            <span>{{ $order->package_dimensions ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Shipping Method:</span>
                            <span>{{ ucfirst($order->shipping_method) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-support me-2"></i>
                        Need Help?
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($order->carrier)
                        <a href="#" class="btn btn-outline-primary btn-sm" onclick="trackWithCarrier()">
                            <i class="bx bx-link-external me-1"></i>
                            Track with {{ $order->carrier }}
                        </a>
                        @endif
                        <a href="#" class="btn btn-outline-info btn-sm" onclick="contactSupport()">
                            <i class="bx bx-message me-1"></i>
                            Contact Support
                        </a>
                        <a href="#" class="btn btn-outline-secondary btn-sm" onclick="reportIssue()">
                            <i class="bx bx-error me-1"></i>
                            Report Issue
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-zap me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-sm" onclick="shareTracking()">
                            <i class="bx bx-share me-1"></i>
                            Share Tracking
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="setDeliveryAlert()">
                            <i class="bx bx-bell me-1"></i>
                            Set Delivery Alert
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="downloadTrackingPDF()">
                            <i class="bx bx-download me-1"></i>
                            Download PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.tracking-summary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.tracking-summary .card-body {
    padding: 2rem;
}

.tracking-info h6 {
    color: rgba(255,255,255,0.8);
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin: 2rem 0;
}

.progress-steps::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: #dee2e6;
    transform: translateY(-50%);
    z-index: 1;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
    z-index: 2;
    background: white;
    padding: 0 1rem;
}

.step-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    background: #dee2e6;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.step.active .step-icon {
    background: var(--bs-primary);
    color: white;
    transform: scale(1.1);
}

.step.completed .step-icon {
    background: var(--bs-success);
    color: white;
}

.step-content h6 {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.step-content small {
    font-size: 0.75rem;
}

.tracking-timeline {
    position: relative;
    padding-left: 2rem;
}

.tracking-timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0.25rem;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
}

.timeline-content {
    padding-left: 1rem;
}

.address-info, .package-info {
    line-height: 1.6;
}

@media (max-width: 768px) {
    .progress-steps {
        flex-direction: column;
        gap: 1rem;
    }
    
    .progress-steps::before {
        display: none;
    }
    
    .step {
        flex-direction: row;
        text-align: left;
        padding: 0;
        background: transparent;
    }
    
    .step-icon {
        margin-bottom: 0;
        margin-right: 1rem;
    }
    
    .tracking-timeline {
        padding-left: 1.5rem;
    }
    
    .timeline-marker {
        left: -1.5rem;
        width: 1rem;
        height: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function refreshTracking() {
    const refreshBtn = document.querySelector('[onclick="refreshTracking()"]');
    const originalText = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i>Refreshing...';
    refreshBtn.disabled = true;
    
    fetch(`/marketplace/orders/{{ $order->id }}/tracking/refresh`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showToast('No new tracking updates available', 'info');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error refreshing tracking information', 'error');
    })
    .finally(() => {
        refreshBtn.innerHTML = originalText;
        refreshBtn.disabled = false;
    });
}

function copyTracking() {
    const trackingNumber = '{{ $order->tracking_number }}';
    navigator.clipboard.writeText(trackingNumber).then(() => {
        showToast('Tracking number copied to clipboard', 'success');
    });
}

function trackWithCarrier() {
    const carrierUrls = {
        'ups': 'https://www.ups.com/track?tracknum=',
        'fedex': 'https://www.fedex.com/apps/fedextrack/?tracknumbers=',
        'usps': 'https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=',
        'dhl': 'https://www.dhl.com/en/express/tracking.html?AWB='
    };
    
    const carrier = '{{ strtolower($order->carrier ?? "") }}';
    const trackingNumber = '{{ $order->tracking_number }}';
    
    if (carrierUrls[carrier]) {
        window.open(carrierUrls[carrier] + trackingNumber, '_blank');
    } else {
        showToast('Carrier tracking not available', 'warning');
    }
}

function shareTracking() {
    const trackingUrl = window.location.href;
    if (navigator.share) {
        navigator.share({
            title: 'Order Tracking - {{ $order->order_number }}',
            text: 'Track my order progress',
            url: trackingUrl
        });
    } else {
        navigator.clipboard.writeText(trackingUrl).then(() => {
            showToast('Tracking link copied to clipboard', 'success');
        });
    }
}

function setDeliveryAlert() {
    if ('Notification' in window) {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                localStorage.setItem('deliveryAlert_{{ $order->id }}', 'true');
                showToast('Delivery alert enabled', 'success');
            }
        });
    } else {
        showToast('Notifications not supported in this browser', 'warning');
    }
}

function downloadTrackingPDF() {
    window.open(`/marketplace/orders/{{ $order->id }}/tracking/pdf`, '_blank');
}

function contactSupport() {
    window.location.href = `/support/contact?order={{ $order->order_number }}&type=tracking`;
}

function reportIssue() {
    window.location.href = `/support/report-issue?order={{ $order->order_number }}&type=delivery`;
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'info' ? 'info' : type === 'warning' ? 'warning' : 'danger'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Auto-refresh tracking every 5 minutes
setInterval(() => {
    if (document.visibilityState === 'visible') {
        fetch(`/marketplace/orders/{{ $order->id }}/tracking/status`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.hasUpdates) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-info alert-dismissible fade show position-fixed';
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
                alertDiv.innerHTML = `
                    <i class="bx bx-info-circle me-2"></i>
                    New tracking updates available!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(alertDiv);
            }
        });
    }
}, 300000); // 5 minutes
</script>
@endpush
