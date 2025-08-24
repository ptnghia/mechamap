@extends('dashboard.layouts.app')

@section('title', 'Tạo tin nhắn mới')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('dashboard.messages.index') }}" class="btn btn-outline-secondary me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-plus text-primary me-2"></i>
                            Tạo tin nhắn mới
                        </h1>
                        <p class="text-muted mb-0">Bắt đầu cuộc trò chuyện mới với thành viên khác</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Message Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-envelope me-2"></i>
                        Tin nhắn riêng
                    </h5>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('dashboard.messages.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="private">
                        
                        <!-- Recipient Selection -->
                        <div class="mb-4">
                            <label for="recipient_id" class="form-label">
                                <i class="fas fa-user me-1"></i>
                                Người nhận <span class="text-danger">*</span>
                            </label>
                            <select name="recipient_id" id="recipient_id" class="form-select" required>
                                <option value="">Chọn người nhận...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('recipient_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('recipient_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label">
                                <i class="fas fa-heading me-1"></i>
                                Tiêu đề <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   placeholder="Nhập tiêu đề cuộc trò chuyện..." 
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message Content -->
                        <div class="mb-4">
                            <label for="message" class="form-label">
                                <i class="fas fa-comment me-1"></i>
                                Nội dung tin nhắn <span class="text-danger">*</span>
                            </label>
                            <textarea name="message" id="message" class="form-control" rows="6" 
                                      placeholder="Nhập nội dung tin nhắn..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Tin nhắn đầu tiên sẽ bắt đầu cuộc trò chuyện mới
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dashboard.messages.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Hủy bỏ
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>
                                Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Group Conversation Option -->
            @if($canCreateGroupConversations ?? true)
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Tạo nhóm thảo luận
                        </h5>
                    </div>
                    
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-2">Muốn tạo nhóm thảo luận?</h6>
                                <p class="text-muted mb-0">
                                    Tạo nhóm thảo luận để nhiều người cùng tham gia và chia sẻ kiến thức về các chủ đề chuyên môn.
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <a href="{{ route('dashboard.messages.groups.create') }}" class="btn btn-success">
                                    <i class="fas fa-users me-1"></i>
                                    Tạo nhóm
                                </a>
                            </div>
                        </div>
                        
                        <!-- Available Conversation Types -->
                        @if($conversationTypes->count() > 0)
                            <div class="mt-4">
                                <h6 class="mb-3">Các loại nhóm có thể tạo:</h6>
                                <div class="row">
                                    @foreach($conversationTypes as $type)
                                        <div class="col-md-6 mb-3">
                                            <div class="border rounded p-3 h-100">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="mb-0">{{ $type['name'] }}</h6>
                                                    @if($type['requires_approval'])
                                                        <span class="badge bg-warning">Cần duyệt</span>
                                                    @else
                                                        <span class="badge bg-success">Tự động</span>
                                                    @endif
                                                </div>
                                                <p class="text-muted small mb-2">{{ $type['description'] }}</p>
                                                <small class="text-muted">
                                                    <i class="fas fa-users me-1"></i>
                                                    Tối đa {{ $type['max_members'] }} thành viên
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced recipient selection with search
    const recipientSelect = document.getElementById('recipient_id');
    
    // Auto-generate title based on recipient
    recipientSelect.addEventListener('change', function() {
        const titleInput = document.getElementById('title');
        if (this.value && !titleInput.value) {
            const selectedOption = this.options[this.selectedIndex];
            const recipientName = selectedOption.text.split(' (')[0];
            titleInput.value = `Cuộc trò chuyện với ${recipientName}`;
        }
    });
    
    // Character counter for message
    const messageTextarea = document.getElementById('message');
    const maxLength = 1000;
    
    // Create character counter
    const counterDiv = document.createElement('div');
    counterDiv.className = 'form-text text-end';
    counterDiv.innerHTML = `<span id="charCount">0</span>/${maxLength} ký tự`;
    messageTextarea.parentNode.appendChild(counterDiv);
    
    messageTextarea.addEventListener('input', function() {
        const charCount = this.value.length;
        document.getElementById('charCount').textContent = charCount;
        
        if (charCount > maxLength * 0.9) {
            counterDiv.className = 'form-text text-end text-warning';
        } else if (charCount > maxLength) {
            counterDiv.className = 'form-text text-end text-danger';
        } else {
            counterDiv.className = 'form-text text-end text-muted';
        }
    });
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const recipientId = document.getElementById('recipient_id').value;
        const title = document.getElementById('title').value.trim();
        const message = document.getElementById('message').value.trim();
        
        if (!recipientId) {
            e.preventDefault();
            alert('Vui lòng chọn người nhận');
            return;
        }
        
        if (!title) {
            e.preventDefault();
            alert('Vui lòng nhập tiêu đề');
            return;
        }
        
        if (!message) {
            e.preventDefault();
            alert('Vui lòng nhập nội dung tin nhắn');
            return;
        }
        
        if (message.length > maxLength) {
            e.preventDefault();
            alert(`Nội dung tin nhắn không được vượt quá ${maxLength} ký tự`);
            return;
        }
    });
    
    // Auto-resize textarea
    messageTextarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 200) + 'px';
    });
});
</script>
@endpush
