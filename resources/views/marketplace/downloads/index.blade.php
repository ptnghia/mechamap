@extends('layouts.app')

@section('title', 'Lịch Sử Tải Xuống')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('marketplace.partials.sidebar')
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-download me-2"></i>
                                Lịch Sử Tải Xuống
                            </h4>
                            <p class="card-title-desc mb-0">Quản lý và theo dõi các file đã tải xuống</p>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadDownloadStats()">
                                <i class="fas fa-chart-bar me-1"></i> Thống Kê
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4" id="statsCards" style="display: none;">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1" id="totalDownloads">0</h5>
                                            <p class="mb-0">Tổng Lượt Tải</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-download fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1" id="uniqueProducts">0</h5>
                                            <p class="mb-0">Sản Phẩm Khác Nhau</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-box fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1" id="recentDownloads">0</h5>
                                            <p class="mb-0">30 Ngày Qua</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-calendar fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1" id="totalFileSize">0 MB</h5>
                                            <p class="mb-0">Tổng Dung Lượng</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-hdd fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="productFilter" class="form-label">Lọc theo sản phẩm</label>
                            <select class="form-select" id="productFilter">
                                <option value="">Tất cả sản phẩm</option>
                                @foreach($downloads->pluck('product')->unique('id') as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="dateFrom" class="form-label">Từ ngày</label>
                            <input type="date" class="form-control" id="dateFrom">
                        </div>
                        <div class="col-md-3">
                            <label for="dateTo" class="form-label">Đến ngày</label>
                            <input type="date" class="form-control" id="dateTo">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                    <i class="fas fa-filter me-1"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Download History Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>File</th>
                                    <th>Sản Phẩm</th>
                                    <th>Đơn Hàng</th>
                                    <th>Dung Lượng</th>
                                    <th>Thời Gian Tải</th>
                                    <th>Trạng Thái</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody id="downloadHistoryTable">
                                @forelse($downloads as $download)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <i class="{{ getFileIcon($download->original_filename) }} fa-2x text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $download->original_filename }}</h6>
                                                <small class="text-muted">{{ $download->mime_type }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('marketplace.products.show', $download->product) }}" class="text-decoration-none">
                                            {{ $download->product->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('marketplace.orders.show', $download->order) }}" class="text-decoration-none">
                                            {{ $download->order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ formatFileSize($download->file_size) }}</td>
                                    <td>
                                        <span title="{{ $download->downloaded_at->format('d/m/Y H:i:s') }}">
                                            {{ $download->downloaded_at->diffForHumans() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($download->is_valid_download)
                                            <span class="badge bg-success">Thành Công</span>
                                        @else
                                            <span class="badge bg-danger">Thất Bại</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <form action="{{ route('marketplace.downloads.redownload', $download) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-primary" title={{ t_feature('marketplace.actions.reload') }}>
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-outline-info" onclick="showDownloadDetails({{ $download->id }})" title={{ t_feature('marketplace.actions.details') }}>
                                                <i class="fas fa-info"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-download fa-3x mb-3"></i>
                                            <p class="mb-0">Chưa có lịch sử tải xuống nào</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($downloads->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $downloads->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Download Details Modal -->
<div class="modal fade" id="downloadDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi Tiết Tải Xuống</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="downloadDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loadDownloadStats() {
    fetch('{{ route("marketplace.downloads.stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#totalDownloads').text(data.data.total_downloads);
                $('#uniqueProducts').text(data.data.unique_products);
                $('#recentDownloads').text(data.data.recent_downloads);
                $('#totalFileSize').text(formatFileSize(data.data.total_file_size));
                $('#statsCards').show();
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
        });
}

function applyFilters() {
    const productId = $('#productFilter').val();
    const dateFrom = $('#dateFrom').val();
    const dateTo = $('#dateTo').val();
    
    const params = new URLSearchParams();
    if (productId) params.append('product_id', productId);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);
    
    fetch(`{{ route("marketplace.downloads.history") }}?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateDownloadTable(data.data.data);
            }
        })
        .catch(error => {
            console.error('Error applying filters:', error);
        });
}

function updateDownloadTable(downloads) {
    const tbody = $('#downloadHistoryTable');
    tbody.empty();
    
    if (downloads.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-download fa-3x mb-3"></i>
                        <p class="mb-0">Không tìm thấy kết quả phù hợp</p>
                    </div>
                </td>
            </tr>
        `);
        return;
    }
    
    downloads.forEach(download => {
        const row = `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="${getFileIcon(download.original_filename)} fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${download.original_filename}</h6>
                            <small class="text-muted">${download.mime_type}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <a href="/marketplace/products/${download.product.id}" class="text-decoration-none">
                        ${download.product.name}
                    </a>
                </td>
                <td>
                    <a href="/marketplace/orders/${download.order.id}" class="text-decoration-none">
                        ${download.order.order_number}
                    </a>
                </td>
                <td>${formatFileSize(download.file_size)}</td>
                <td>
                    <span title="${new Date(download.downloaded_at).toLocaleString()}">
                        ${timeAgo(download.downloaded_at)}
                    </span>
                </td>
                <td>
                    ${download.is_valid_download ? 
                        '<span class="badge bg-success">Thành Công</span>' : 
                        '<span class="badge bg-danger">Thất Bại</span>'
                    }
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <form action="/marketplace/downloads/redownload/${download.id}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary" title="Tải lại">
                                <i class="fas fa-redo"></i>
                            </button>
                        </form>
                        <button type="button" class="btn btn-outline-info" onclick="showDownloadDetails(${download.id})" title="Chi tiết">
                            <i class="fas fa-info"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function showDownloadDetails(downloadId) {
    // Implementation for showing download details
    $('#downloadDetailsModal').modal('show');
}

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

function timeAgo(date) {
    const now = new Date();
    const past = new Date(date);
    const diffInSeconds = Math.floor((now - past) / 1000);
    
    if (diffInSeconds < 60) return {{ t_feature('marketplace.time.just_now') }};
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' phút trước';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' giờ trước';
    return Math.floor(diffInSeconds / 86400) + ' ngày trước';
}
</script>
@endpush
