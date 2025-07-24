@extends('layouts.app')

@section('title', 'Test Header Features')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">🧪 Test Header Features</h1>
            
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-2"></i>Hướng dẫn test</h5>
                <p class="mb-2">Trang này để test các tính năng mới trên header:</p>
                <ul class="mb-0">
                    <li><strong>Giỏ hàng:</strong> Chỉ hiển thị khi user có quyền mua sản phẩm</li>
                    <li><strong>Thông báo:</strong> Hiển thị notifications và alerts từ database</li>
                    <li><strong>Auto-refresh:</strong> Thông báo tự động cập nhật mỗi 30 giây</li>
                </ul>
            </div>

            <!-- User Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-user me-2"></i>Thông tin người dùng hiện tại</h5>
                </div>
                <div class="card-body">
                    @auth
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Tên:</strong> {{ auth()->user()->name }}</p>
                                <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                                <p><strong>Role:</strong> {{ auth()->user()->role }}</p>
                                <p><strong>Role Group:</strong> {{ auth()->user()->role_group }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Có thể mua sản phẩm:</strong> 
                                    @if(auth()->user()->canBuyAnyProduct())
                                        <span class="badge bg-success">✅ Có</span>
                                    @else
                                        <span class="badge bg-danger">❌ Không</span>
                                    @endif
                                </p>
                                <p><strong>Loại sản phẩm có thể mua:</strong></p>
                                <ul class="list-unstyled">
                                    @php
                                        $allowedBuyTypes = \App\Services\MarketplacePermissionService::getAllowedBuyTypes(auth()->user()->role ?? 'guest');
                                    @endphp
                                    @forelse($allowedBuyTypes as $type)
                                        <li><span class="badge bg-primary me-1">{{ $type }}</span></li>
                                    @empty
                                        <li><span class="text-muted">Không có quyền mua sản phẩm nào</span></li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Bạn chưa đăng nhập. <a href="{{ route('login') }}">Đăng nhập ngay</a> để test các tính năng.
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Notifications Test -->
            @auth
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-bell me-2"></i>Test Notifications</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Thống kê thông báo</h6>
                            @php
                                $unreadNotifications = auth()->user()->userNotifications()->where('is_read', false)->count();
                                $totalNotifications = auth()->user()->userNotifications()->count();
                                $unreadAlerts = auth()->user()->alerts()->whereNull('read_at')->count();
                                $totalAlerts = auth()->user()->alerts()->count();
                            @endphp
                            <ul class="list-unstyled">
                                <li><strong>Notifications chưa đọc:</strong> <span class="badge bg-danger">{{ $unreadNotifications }}</span></li>
                                <li><strong>Tổng Notifications:</strong> <span class="badge bg-info">{{ $totalNotifications }}</span></li>
                                <li><strong>Alerts chưa đọc:</strong> <span class="badge bg-warning">{{ $unreadAlerts }}</span></li>
                                <li><strong>Tổng Alerts:</strong> <span class="badge bg-secondary">{{ $totalAlerts }}</span></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Test Actions</h6>
                            <button class="btn btn-primary btn-sm me-2" onclick="testLoadNotifications()">
                                <i class="fas fa-sync me-1"></i>Load Notifications
                            </button>
                            <button class="btn btn-success btn-sm me-2" onclick="testMarkAllRead()">
                                <i class="fas fa-check me-1"></i>Mark All Read
                            </button>
                            <button class="btn btn-info btn-sm" onclick="testCreateNotification()">
                                <i class="fas fa-plus me-1"></i>Create Test Notification
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Test -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-shopping-cart me-2"></i>Test Cart Visibility</h5>
                </div>
                <div class="card-body">
                    @auth
                        @if(auth()->user()->canBuyAnyProduct())
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Giỏ hàng hiển thị:</strong> User có quyền mua sản phẩm, giỏ hàng sẽ hiển thị trên header.
                            </div>
                            <button class="btn btn-primary" onclick="testLoadCart()">
                                <i class="fas fa-shopping-cart me-1"></i>Test Load Cart
                            </button>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Giỏ hàng ẩn:</strong> User không có quyền mua sản phẩm, giỏ hàng sẽ không hiển thị trên header.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Đăng nhập để test tính năng giỏ hàng.
                        </div>
                    @endauth
                </div>
            </div>
            @endauth

            <!-- API Test Results -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-code me-2"></i>API Test Results</h5>
                </div>
                <div class="card-body">
                    <div id="apiResults">
                        <p class="text-muted">Kết quả API test sẽ hiển thị ở đây...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Test functions
function testLoadNotifications() {
    showApiResult('Loading notifications...', 'info');
    
    fetch('/api/notifications/recent')
        .then(response => response.json())
        .then(data => {
            showApiResult('Notifications loaded successfully', 'success');
            console.log('Notifications:', data);
            
            // Update header notification count
            if (window.updateNotificationCount) {
                window.updateNotificationCount(data.total_unread);
            }
        })
        .catch(error => {
            showApiResult('Error loading notifications: ' + error.message, 'error');
            console.error('Error:', error);
        });
}

function testMarkAllRead() {
    showApiResult('Marking all notifications as read...', 'info');
    
    fetch('/api/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showApiResult('All notifications marked as read', 'success');
            // Reload page to update counts
            setTimeout(() => location.reload(), 1000);
        } else {
            showApiResult('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showApiResult('Error marking notifications as read: ' + error.message, 'error');
        console.error('Error:', error);
    });
}

function testCreateNotification() {
    showApiResult('Creating test notification...', 'info');
    
    // This would typically be done server-side
    showApiResult('Test notification creation would be implemented server-side', 'warning');
}

function testLoadCart() {
    showApiResult('Loading cart...', 'info');
    
    if (window.loadMiniCart) {
        window.loadMiniCart();
        showApiResult('Cart loaded successfully', 'success');
    } else {
        showApiResult('Cart function not available', 'warning');
    }
}

function showApiResult(message, type) {
    const resultsDiv = document.getElementById('apiResults');
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';
    
    const icon = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-exclamation-circle',
        'warning': 'fas fa-exclamation-triangle',
        'info': 'fas fa-info-circle'
    }[type] || 'fas fa-info-circle';
    
    resultsDiv.innerHTML = `
        <div class="alert ${alertClass}">
            <i class="${icon} me-2"></i>
            ${message}
            <small class="d-block mt-1 text-muted">${new Date().toLocaleTimeString()}</small>
        </div>
    `;
}

// Auto-test on page load
document.addEventListener('DOMContentLoaded', function() {
    showApiResult('Page loaded. Ready for testing!', 'info');
});
</script>
@endpush
