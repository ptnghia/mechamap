@extends('layouts.app')

@section('title', 'Thông báo')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">🔔 Thông báo</h1>
                    <p class="text-muted">Theo dõi các hoạt động và cập nhật mới nhất</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="markAllAsRead()">
                        <i class="fas fa-check-double"></i> Đánh dấu tất cả đã đọc
                    </button>
                    <button class="btn btn-outline-danger btn-sm" onclick="clearAllAlerts()">
                        <i class="fas fa-trash"></i> Xóa tất cả
                    </button>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link {{ !request('filter') ? 'active' : '' }}" 
                               href="{{ route('alerts.index') }}">
                                <i class="fas fa-list"></i> Tất cả
                                <span class="badge bg-secondary ms-1">{{ $alerts->total() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('filter') == 'unread' ? 'active' : '' }}" 
                               href="{{ route('alerts.index', ['filter' => 'unread']) }}">
                                <i class="fas fa-envelope"></i> Chưa đọc
                                <span class="badge bg-warning ms-1">{{ auth()->user()->alerts()->whereNull('read_at')->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('filter') == 'read' ? 'active' : '' }}" 
                               href="{{ route('alerts.index', ['filter' => 'read']) }}">
                                <i class="fas fa-envelope-open"></i> Đã đọc
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('filter') == 'messages' ? 'active' : '' }}" 
                               href="{{ route('alerts.index', ['filter' => 'messages']) }}">
                                <i class="fas fa-comments"></i> Tin nhắn
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Alerts List -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if($alerts->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($alerts as $alert)
                            <div class="list-group-item list-group-item-action {{ $alert->read_at ? '' : 'bg-light' }}" 
                                 onclick="markAsRead({{ $alert->id }})">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-3">
                                                @php
                                                    $iconMap = [
                                                        'thread_reply' => 'fas fa-reply text-primary',
                                                        'mention' => 'fas fa-at text-info',
                                                        'like' => 'fas fa-heart text-danger',
                                                        'follow' => 'fas fa-user-plus text-success',
                                                        'system' => 'fas fa-cog text-warning',
                                                        'message' => 'fas fa-envelope text-primary',
                                                        'order' => 'fas fa-shopping-cart text-success',
                                                        'product' => 'fas fa-box text-info'
                                                    ];
                                                    $iconClass = $iconMap[$alert->type] ?? 'fas fa-bell text-secondary';
                                                @endphp
                                                <i class="{{ $iconClass }}"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 {{ $alert->read_at ? 'text-muted' : '' }}">
                                                    {{ $alert->title }}
                                                </h6>
                                                <p class="mb-1 {{ $alert->read_at ? 'text-muted' : '' }}">
                                                    {{ $alert->message }}
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i>
                                                    {{ $alert->created_at->diffForHumans() }}
                                                    @if($alert->read_at)
                                                        • <i class="fas fa-check text-success"></i> Đã đọc
                                                    @else
                                                        • <span class="text-primary">Mới</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown" onclick="event.stopPropagation()">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if(!$alert->read_at)
                                            <li>
                                                <button class="dropdown-item" onclick="markAsRead({{ $alert->id }}, event)">
                                                    <i class="fas fa-check"></i> Đánh dấu đã đọc
                                                </button>
                                            </li>
                                            @else
                                            <li>
                                                <button class="dropdown-item" onclick="markAsUnread({{ $alert->id }}, event)">
                                                    <i class="fas fa-envelope"></i> Đánh dấu chưa đọc
                                                </button>
                                            </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="deleteAlert({{ $alert->id }}, event)">
                                                    <i class="fas fa-trash"></i> Xóa thông báo
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                @if($alert->action_url)
                                <div class="mt-2">
                                    <a href="{{ $alert->action_url }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation()">
                                        <i class="fas fa-external-link-alt"></i> Xem chi tiết
                                    </a>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">
                                        Hiển thị {{ $alerts->firstItem() }} - {{ $alerts->lastItem() }} 
                                        trong tổng số {{ number_format($alerts->total()) }} thông báo
                                    </small>
                                </div>
                                <div>
                                    {{ $alerts->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Không có thông báo nào</h5>
                            <p class="text-muted">
                                @if(request('filter') == 'unread')
                                    Bạn đã đọc hết tất cả thông báo.
                                @elseif(request('filter') == 'read')
                                    Bạn chưa có thông báo nào đã đọc.
                                @elseif(request('filter') == 'messages')
                                    Bạn chưa có thông báo tin nhắn nào.
                                @else
                                    Thông báo của bạn sẽ xuất hiện ở đây.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function markAsRead(alertId, event = null) {
    if (event) event.stopPropagation();
    
    fetch(`/alerts/${alertId}/read`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAsUnread(alertId, event) {
    event.stopPropagation();
    
    fetch(`/alerts/${alertId}/unread`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteAlert(alertId, event) {
    event.stopPropagation();
    
    if (confirm('Bạn có chắc chắn muốn xóa thông báo này?')) {
        fetch(`/alerts/${alertId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function markAllAsRead() {
    if (confirm('Bạn có chắc chắn muốn đánh dấu tất cả thông báo là đã đọc?')) {
        fetch('/alerts/mark-all-read', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function clearAllAlerts() {
    if (confirm('Bạn có chắc chắn muốn xóa tất cả thông báo? Hành động này không thể hoàn tác.')) {
        fetch('/alerts/clear-all', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>
@endpush
@endsection
