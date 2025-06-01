@extends('admin.layouts.app')

@section('title', 'Đánh chỉ mục lại')
@section('header', 'Đánh chỉ mục lại')

@section('actions')
<a href="{{ route('admin.search.index') }}" class="btn btn-sm btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> {{ __('Quay lại cấu hình') }}
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Đánh chỉ mục lại dữ liệu tìm kiếm') }}</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ __('Quá trình đánh chỉ mục lại sẽ cập nhật toàn bộ dữ liệu tìm kiếm. Điều này có thể mất vài phút
                    tùy thuộc vào lượng dữ liệu.') }}
                </div>

                <form action="{{ route('admin.search.reindex.process') }}" method="POST" id="reindexForm">
                    @csrf

                    <div class="mb-4">
                        <h6>{{ __('Chọn nội dung cần đánh chỉ mục:') }}</h6>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="threads" id="index_threads"
                                name="content_types[]" checked>
                            <label class="form-check-label" for="index_threads">
                                {{ __('Bài đăng (Threads)') }}
                                <small class="text-muted d-block">{{ __('Đánh chỉ mục lại tất cả bài đăng') }}</small>
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="comments" id="index_comments"
                                name="content_types[]" checked>
                            <label class="form-check-label" for="index_comments">
                                {{ __('Bình luận (Comments)') }}
                                <small class="text-muted d-block">{{ __('Đánh chỉ mục lại tất cả bình luận') }}</small>
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="pages" id="index_pages"
                                name="content_types[]" checked>
                            <label class="form-check-label" for="index_pages">
                                {{ __('Trang (Pages)') }}
                                <small class="text-muted d-block">{{ __('Đánh chỉ mục lại tất cả trang') }}</small>
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="users" id="index_users"
                                name="content_types[]">
                            <label class="form-check-label" for="index_users">
                                {{ __('Người dùng (Users)') }}
                                <small class="text-muted d-block">{{ __('Đánh chỉ mục lại thông tin người dùng')
                                    }}</small>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="clear_existing"
                                name="clear_existing" checked>
                            <label class="form-check-label" for="clear_existing">
                                {{ __('Xóa chỉ mục cũ trước khi tạo mới') }}
                                <small class="text-muted d-block">{{ __('Khuyến khích để đảm bảo dữ liệu chỉ mục chính
                                    xác') }}</small>
                            </label>
                        </div>
                    </div>

                    <div class="progress mb-3" style="display: none;" id="progressContainer">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                            style="width: 0%" id="progressBar">
                            <span id="progressText">0%</span>
                        </div>
                    </div>

                    <div id="logContainer" style="display: none;" class="mb-3">
                        <h6>{{ __('Nhật ký quá trình:') }}</h6>
                        <div class="border rounded p-3 bg-light" style="height: 200px; overflow-y: auto;"
                            id="logOutput">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary" id="startButton">
                            <i class="bi bi-arrow-clockwise me-1"></i> {{ __('Bắt đầu đánh chỉ mục') }}
                        </button>

                        <button type="button" class="btn btn-outline-danger" id="cancelButton" style="display: none;">
                            <i class="bi bi-x-circle me-1"></i> {{ __('Hủy bỏ') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($recentIndexes) && count($recentIndexes) > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Lịch sử đánh chỉ mục') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Thời gian') }}</th>
                                <th>{{ __('Loại nội dung') }}</th>
                                <th>{{ __('Số lượng') }}</th>
                                <th>{{ __('Trạng thái') }}</th>
                                <th>{{ __('Thời gian thực hiện') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentIndexes as $index)
                            <tr>
                                <td>{{ $index->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $index->content_types }}</td>
                                <td>{{ number_format($index->total_processed) }}</td>
                                <td>
                                    @if($index->status === 'completed')
                                    <span class="badge bg-success">{{ __('Hoàn thành') }}</span>
                                    @elseif($index->status === 'failed')
                                    <span class="badge bg-danger">{{ __('Thất bại') }}</span>
                                    @else
                                    <span class="badge bg-warning">{{ __('Đang xử lý') }}</span>
                                    @endif
                                </td>
                                <td>{{ $index->duration ? $index->duration . 's' : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reindexForm');
    const startButton = document.getElementById('startButton');
    const cancelButton = document.getElementById('cancelButton');
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const logContainer = document.getElementById('logContainer');
    const logOutput = document.getElementById('logOutput');
    
    let isProcessing = false;
    let intervalId = null;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (isProcessing) return;
        
        // Validate at least one content type is selected
        const selectedTypes = form.querySelectorAll('input[name="content_types[]"]:checked');
        if (selectedTypes.length === 0) {
            alert('{{ __("Vui lòng chọn ít nhất một loại nội dung để đánh chỉ mục.") }}');
            return;
        }
        
        if (!confirm('{{ __("Bạn có chắc chắn muốn bắt đầu quá trình đánh chỉ mục lại?") }}')) {
            return;
        }
        
        startReindexing();
    });

    function startReindexing() {
        isProcessing = true;
        startButton.style.display = 'none';
        cancelButton.style.display = 'inline-block';
        progressContainer.style.display = 'block';
        logContainer.style.display = 'block';
        
        // Submit form via AJAX
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Start monitoring progress
                monitorProgress(data.job_id);
            } else {
                throw new Error(data.message || 'Unknown error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            addLog('{{ __("Lỗi: ") }}' + error.message, 'error');
            resetUI();
        });
    }

    function monitorProgress(jobId) {
        intervalId = setInterval(() => {
            fetch(`{{ route('admin.search.reindex.status') }}?job_id=${jobId}`)
                .then(response => response.json())
                .then(data => {
                    updateProgress(data.progress || 0);
                    
                    if (data.logs && data.logs.length > 0) {
                        data.logs.forEach(log => addLog(log.message, log.type));
                    }
                    
                    if (data.status === 'completed') {
                        updateProgress(100);
                        addLog('{{ __("Quá trình đánh chỉ mục đã hoàn thành thành công!") }}', 'success');
                        clearInterval(intervalId);
                        resetUI();
                    } else if (data.status === 'failed') {
                        addLog('{{ __("Quá trình đánh chỉ mục đã thất bại.") }}', 'error');
                        clearInterval(intervalId);
                        resetUI();
                    }
                })
                .catch(error => {
                    console.error('Error monitoring progress:', error);
                    addLog('{{ __("Lỗi khi theo dõi tiến trình.") }}', 'error');
                });
        }, 2000);
    }

    function updateProgress(percentage) {
        progressBar.style.width = percentage + '%';
        progressText.textContent = Math.round(percentage) + '%';
        
        if (percentage >= 100) {
            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.add('bg-success');
        }
    }

    function addLog(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        const logClass = type === 'error' ? 'text-danger' : (type === 'success' ? 'text-success' : 'text-dark');
        
        const logEntry = document.createElement('div');
        logEntry.className = logClass;
        logEntry.innerHTML = `<small class="text-muted">[${timestamp}]</small> ${message}`;
        
        logOutput.appendChild(logEntry);
        logOutput.scrollTop = logOutput.scrollHeight;
    }

    function resetUI() {
        isProcessing = false;
        startButton.style.display = 'inline-block';
        cancelButton.style.display = 'none';
        
        // Change button text to indicate completion
        setTimeout(() => {
            startButton.innerHTML = '<i class="bi bi-check-lg me-1"></i> {{ __("Đánh chỉ mục lại") }}';
        }, 2000);
    }

    cancelButton.addEventListener('click', function() {
        if (confirm('{{ __("Bạn có chắc chắn muốn hủy bỏ quá trình đánh chỉ mục?") }}')) {
            if (intervalId) {
                clearInterval(intervalId);
            }
            addLog('{{ __("Quá trình đã bị hủy bỏ bởi người dùng.") }}', 'error');
            resetUI();
        }
    });
});
</script>
@endpush