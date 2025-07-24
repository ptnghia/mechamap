@extends('layouts.app')

@section('title', 'Tải File - ' . $orderItem->product->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('marketplace.partials.sidebar')
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Product Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-cube me-2"></i>
                                {{ $orderItem->product->name }}
                            </h4>
                            <p class="card-title-desc mb-0">
                                Đơn hàng: <a href="{{ route('marketplace.downloads.order-files', $orderItem->order) }}" class="text-decoration-none">
                                    #{{ $orderItem->order->order_number }}
                                </a>
                            </p>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-download me-1"></i>
                                {{ count($files) }} File
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            @if($orderItem->product->featured_image)
                                <img src="{{ Storage::url($orderItem->product->featured_image) }}" 
                                     alt="{{ $orderItem->product->name }}" 
                                     class="img-fluid rounded">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-cube fa-4x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <p class="text-muted">{{ $orderItem->product->short_description }}</p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Loại sản phẩm:</strong> {{ ucfirst($orderItem->product->product_type) }}</p>
                                    <p><strong>Người bán:</strong> {{ $orderItem->product->seller->user->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Số lượng mua:</strong> {{ $orderItem->quantity }}</p>
                                    <p><strong>Giá:</strong> {{ number_format($orderItem->unit_price) }} VNĐ</p>
                                </div>
                            </div>

                            @if($orderItem->product->file_formats)
                                <p><strong>Định dạng file:</strong> 
                                    @foreach($orderItem->product->file_formats as $format)
                                        <span class="badge bg-secondary me-1">{{ $format }}</span>
                                    @endforeach
                                </p>
                            @endif

                            @if($orderItem->product->software_compatibility)
                                <p><strong>Tương thích:</strong> 
                                    @foreach($orderItem->product->software_compatibility as $software)
                                        <span class="badge bg-info me-1">{{ $software }}</span>
                                    @endforeach
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download Files -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-download me-2"></i>
                        File Tải Xuống ({{ count($files) }})
                    </h4>
                    <p class="card-title-desc mb-0">Nhấp vào nút tải để tạo link download bảo mật</p>
                </div>
                <div class="card-body">
                    @if(count($files) > 0)
                        <div class="row">
                            @foreach($files as $index => $file)
                            <div class="col-lg-6 mb-3">
                                <div class="card border file-card" data-file-index="{{ $index }}">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <i class="{{ getFileIcon($file['name']) }} fa-3x text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $file['name'] }}</h6>
                                                <p class="text-muted mb-1">
                                                    <small>{{ formatFileSize($file['size']) }}</small>
                                                    @if(isset($file['extension']))
                                                        <span class="badge bg-secondary ms-2">{{ strtoupper($file['extension']) }}</span>
                                                    @endif
                                                </p>
                                                <p class="text-muted mb-3">
                                                    <small>{{ $file['mime_type'] ?? 'application/octet-stream' }}</small>
                                                </p>
                                                
                                                <button type="button" 
                                                        class="btn btn-primary btn-sm download-btn" 
                                                        data-file-index="{{ $index }}"
                                                        onclick="generateDownloadLink({{ $index }})">
                                                    <i class="fas fa-download me-1"></i>
                                                    Tải Xuống
                                                </button>
                                                
                                                <div class="download-progress mt-2" style="display: none;">
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                             role="progressbar" style="width: 100%"></div>
                                                    </div>
                                                    <small class="text-muted">Đang tạo link download...</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Download Statistics -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="mb-1">{{ $orderItem->download_count ?? 0 }}</h5>
                                        <p class="mb-0 text-muted">Lượt tải của sản phẩm này</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="mb-1">{{ formatFileSize(array_sum(array_column($files, 'size'))) }}</h5>
                                        <p class="mb-0 text-muted">Tổng dung lượng</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="mb-1">Không giới hạn</h5>
                                        <p class="mb-0 text-muted">Thời gian tải</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Không có file để tải</h5>
                            <p class="text-muted">Sản phẩm này hiện chưa có file nào có thể tải xuống.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Security Notice -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6><i class="fas fa-shield-alt text-success me-2"></i>Bảo Mật & Quyền Riêng Tư</h6>
                            <p class="mb-0">
                                Tất cả file được bảo vệ bằng hệ thống token bảo mật. 
                                Chỉ tài khoản đã mua sản phẩm mới có thể tải xuống. 
                                Mỗi link download có thời hạn 24 giờ và được theo dõi để đảm bảo an toàn.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('marketplace.downloads.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-history me-1"></i>
                                Xem Lịch Sử Tải
                            </a>
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
.file-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-card:hover {
    border-color: #007bff !important;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 123, 255, 0.075);
}

.download-progress .progress {
    height: 4px;
}

.download-btn:disabled {
    opacity: 0.6;
}
</style>
@endpush

@push('scripts')
<script>
function generateDownloadLink(fileIndex) {
    const btn = $(`.download-btn[data-file-index="${fileIndex}"]`);
    const progress = btn.siblings('.download-progress');
    
    // Disable button and show progress
    btn.prop('disabled', true);
    progress.show();
    
    // Generate download token
    fetch('{{ route("marketplace.downloads.generate-token") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            order_item_id: {{ $orderItem->id }},
            file_index: fileIndex
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to download URL
            window.location.href = data.data.download_url;
            
            // Show success message
            showNotification('success', 'Đang tải file xuống...');
        } else {
            showNotification('error', data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Có lỗi xảy ra khi tạo link download');
    })
    .finally(() => {
        // Re-enable button and hide progress
        btn.prop('disabled', false);
        progress.hide();
    });
}

function showNotification(type, message) {
    // Simple notification - you can replace with your preferred notification library
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.alert('close');
    }, 5000);
}

// Helper functions (should be moved to a global JS file)
function getFileIcon(filename) {
    const extension = filename.split('.').pop().toLowerCase();
    const iconMap = {
        'dwg': 'fas fa-drafting-compass',
        'dxf': 'fas fa-drafting-compass',
        'step': 'fas fa-cube',
        'stp': 'fas fa-cube',
        'iges': 'fas fa-cube',
        'igs': 'fas fa-cube',
        'stl': 'fas fa-shapes',
        'pdf': 'fas fa-file-pdf',
        'doc': 'fas fa-file-word',
        'docx': 'fas fa-file-word',
        'zip': 'fas fa-file-archive',
        'rar': 'fas fa-file-archive'
    };
    return iconMap[extension] || 'fas fa-file';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endpush
