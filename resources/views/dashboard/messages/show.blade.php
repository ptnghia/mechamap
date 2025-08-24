@extends('dashboard.layouts.app')

@section('title', $conversation->is_group ? $conversation->title : 'Trò chuyện với ' . ($otherParticipant->name ?? 'Unknown'))

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
                        <h1 class="h4 mb-0">
                            @if($conversation->is_group)
                                <i class="fas fa-users text-primary me-2"></i>
                                {{ $conversation->title }}
                            @else
                                <i class="fas fa-user text-primary me-2"></i>
                                {{ $otherParticipant->name ?? 'Unknown User' }}
                            @endif
                        </h1>
                        @if($conversation->is_group)
                            <p class="text-muted mb-0">
                                {{ $groupMembers->count() }} thành viên
                                @if($conversation->conversationType)
                                    • {{ $conversation->conversationType->name }}
                                @endif
                            </p>
                        @else
                            <p class="text-muted mb-0">
                                @if($otherParticipant && ($otherParticipant->is_online ?? false))
                                    <span class="text-success">
                                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                        Đang online
                                    </span>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                        Offline
                                    </span>
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="d-flex gap-2">
                    @if($conversation->is_group && ($userPermissions['change_settings'] ?? false))
                        <a href="{{ route('dashboard.messages.groups.settings', $conversation->id) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-cog me-1"></i>
                            Cài đặt
                        </a>
                    @endif
                    
                    @if($conversation->is_group)
                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#membersModal">
                            <i class="fas fa-users me-1"></i>
                            Thành viên
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Interface -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="height: 70vh;">
                <!-- Messages Area -->
                <div class="card-body d-flex flex-column p-0">
                    <div class="flex-grow-1 overflow-auto p-3" id="messagesContainer" style="max-height: calc(70vh - 120px);">
                        <div id="messagesList">
                            @foreach($conversation->messages as $message)
                                <div class="message-item mb-3 {{ $message->user_id === Auth::id() ? 'text-end' : '' }}">
                                    @if($message->is_system_message)
                                        <!-- System Message -->
                                        <div class="text-center">
                                            <small class="text-muted bg-light rounded px-3 py-1 d-inline-block">
                                                <i class="fas fa-info-circle me-1"></i>
                                                {{ $message->content }}
                                            </small>
                                            <div class="small text-muted mt-1">
                                                {{ $message->created_at->format('H:i') }}
                                            </div>
                                        </div>
                                    @else
                                        <!-- User Message -->
                                        <div class="d-flex {{ $message->user_id === Auth::id() ? 'justify-content-end' : '' }}">
                                            @if($message->user_id !== Auth::id())
                                                <div class="flex-shrink-0 me-2">
                                                    <img src="{{ $message->user->avatar ?? '/images/default-avatar.png' }}" 
                                                         class="rounded-circle" width="32" height="32" alt="">
                                                </div>
                                            @endif
                                            
                                            <div class="flex-grow-1" style="max-width: 70%;">
                                                @if($message->user_id !== Auth::id())
                                                    <div class="small text-muted mb-1">{{ $message->user->name }}</div>
                                                @endif
                                                
                                                <div class="message-bubble {{ $message->user_id === Auth::id() ? 'bg-primary text-white' : 'bg-light' }} rounded-3 px-3 py-2">
                                                    {{ $message->content }}
                                                </div>
                                                
                                                <div class="small text-muted mt-1">
                                                    {{ $message->created_at->format('H:i') }}
                                                </div>
                                            </div>
                                            
                                            @if($message->user_id === Auth::id())
                                                <div class="flex-shrink-0 ms-2">
                                                    <img src="{{ Auth::user()->avatar ?? '/images/default-avatar.png' }}" 
                                                         class="rounded-circle" width="32" height="32" alt="">
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Message Input -->
                    <div class="border-top p-3">
                        @if($conversation->is_group && !($userPermissions['send_messages'] ?? true))
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Bạn không có quyền gửi tin nhắn trong nhóm này.
                            </div>
                        @else
                            <form id="messageForm" action="{{ route('dashboard.messages.send', $conversation->id) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <textarea name="content" id="messageInput" class="form-control" 
                                              placeholder="Nhập tin nhắn..." rows="1" required
                                              style="resize: none; max-height: 100px;"></textarea>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Members Modal (for groups) -->
@if($conversation->is_group)
<div class="modal fade" id="membersModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-users me-2"></i>
                    Thành viên nhóm ({{ $groupMembers->count() }})
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach($groupMembers as $member)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="flex-shrink-0 me-3">
                                    <img src="{{ $member['avatar'] }}" class="rounded-circle" width="40" height="40" alt="">
                                    @if($member['is_online'])
                                        <span class="position-absolute translate-middle p-1 bg-success border border-light rounded-circle">
                                            <span class="visually-hidden">Online</span>
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $member['name'] }}</h6>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-{{ $member['role_color'] }} me-2">
                                            {{ $member['role_label'] }}
                                        </span>
                                        <small class="text-muted">
                                            Tham gia {{ $member['joined_at']->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                                
                                @if($userPermissions['change_roles'] ?? false)
                                    <div class="flex-shrink-0">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" onclick="changeMemberRole({{ $member['id'] }}, 'admin')">
                                                    <i class="fas fa-user-shield me-2"></i>Làm Admin
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="changeMemberRole({{ $member['id'] }}, 'moderator')">
                                                    <i class="fas fa-user-cog me-2"></i>Làm Moderator
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="changeMemberRole({{ $member['id'] }}, 'member')">
                                                    <i class="fas fa-user me-2"></i>Làm Member
                                                </a></li>
                                                @if($userPermissions['remove_members'] ?? false)
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="removeMember({{ $member['id'] }})">
                                                        <i class="fas fa-user-times me-2"></i>Xóa khỏi nhóm
                                                    </a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($userPermissions['invite_members'] ?? false)
                    <div class="border-top pt-3 mt-3">
                        <button type="button" class="btn btn-primary" onclick="showInviteMemberModal()">
                            <i class="fas fa-user-plus me-2"></i>
                            Mời thành viên mới
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messagesContainer');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    
    // Scroll to bottom
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Initial scroll to bottom
    scrollToBottom();
    
    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });
    
    // Handle form submission
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const content = messageInput.value.trim();
        if (!content) return;
        
        // Add message to UI immediately
        addMessageToUI({
            content: content,
            user_name: '{{ Auth::user()->name }}',
            created_at: 'Vừa xong',
            is_own: true
        });
        
        // Clear input
        messageInput.value = '';
        messageInput.style.height = 'auto';
        
        // Send via AJAX
        fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ content: content })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Failed to send message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
    
    // Add message to UI
    function addMessageToUI(message) {
        const messagesList = document.getElementById('messagesList');
        const messageHtml = `
            <div class="message-item mb-3 ${message.is_own ? 'text-end' : ''}">
                <div class="d-flex ${message.is_own ? 'justify-content-end' : ''}">
                    ${!message.is_own ? `
                        <div class="flex-shrink-0 me-2">
                            <img src="/images/default-avatar.png" class="rounded-circle" width="32" height="32" alt="">
                        </div>
                    ` : ''}
                    
                    <div class="flex-grow-1" style="max-width: 70%;">
                        ${!message.is_own ? `<div class="small text-muted mb-1">${message.user_name}</div>` : ''}
                        
                        <div class="message-bubble ${message.is_own ? 'bg-primary text-white' : 'bg-light'} rounded-3 px-3 py-2">
                            ${message.content}
                        </div>
                        
                        <div class="small text-muted mt-1">
                            ${message.created_at}
                        </div>
                    </div>
                    
                    ${message.is_own ? `
                        <div class="flex-shrink-0 ms-2">
                            <img src="{{ Auth::user()->avatar ?? '/images/default-avatar.png' }}" class="rounded-circle" width="32" height="32" alt="">
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        messagesList.insertAdjacentHTML('beforeend', messageHtml);
        scrollToBottom();
    }
    
    // Enter to send (Shift+Enter for new line)
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            messageForm.dispatchEvent(new Event('submit'));
        }
    });
});

// Group member management functions
function changeMemberRole(userId, role) {
    if (!confirm('Bạn có chắc muốn thay đổi vai trò của thành viên này?')) return;
    
    fetch(`{{ route('dashboard.messages.groups.members.role', [$conversation->id, ':userId']) }}`.replace(':userId', userId), {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ role: role })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
    });
}

function removeMember(userId) {
    if (!confirm('Bạn có chắc muốn xóa thành viên này khỏi nhóm?')) return;
    
    fetch(`{{ route('dashboard.messages.groups.members.remove', [$conversation->id, ':userId']) }}`.replace(':userId', userId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
    });
}
</script>
@endpush
