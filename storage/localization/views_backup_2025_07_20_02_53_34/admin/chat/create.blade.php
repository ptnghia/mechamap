@extends('admin.layouts.dason')

@section('title', 'Tin nhắn mới - Admin Chat')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Tin nhắn mới</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.chat.index') }}">Chat</a></li>
                        <li class="breadcrumb-item active">Tin nhắn mới</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.chat.index') }}" class="btn btn-outline-secondary me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h4 class="card-title mb-0">
                            <i class="fas fa-plus me-2"></i>Tin nhắn mới
                        </h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.chat.store') }}" method="POST" id="chatForm">
                        @csrf

                        <!-- Recipient Selection -->
                        <div class="mb-3">
                            <label for="recipientSearch" class="form-label">
                                <i class="fas fa-user me-1"></i>Người nhận:
                            </label>
                            <div class="position-relative">
                                <input type="text"
                                       class="form-control @error('recipient_id') is-invalid @enderror"
                                       id="recipientSearch"
                                       placeholder="Tìm kiếm thành viên (tên, email, username)..."
                                       autocomplete="off">
                                <input type="hidden" name="recipient_id" id="selectedRecipientId" value="{{ old('recipient_id') }}" required>

                                <!-- Search Results Dropdown -->
                                <div id="searchResults" class="dropdown-menu w-100" style="display: none; max-height: 300px; overflow-y: auto;">
                                    <!-- Results will be populated here -->
                                </div>
                            </div>

                            <!-- Selected Recipient Display -->
                            <div id="selectedRecipient" class="mt-2" style="display: none;">
                                <div class="d-flex align-items-center p-3 bg-light rounded border">
                                    <img id="selectedAvatar" src="" class="rounded-circle me-3" width="40" height="40" alt="">
                                    <div class="flex-grow-1">
                                        <strong id="selectedName"></strong>
                                        <div class="text-muted small" id="selectedEmail"></div>
                                        <div class="text-muted small" id="selectedRole"></div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="clearSelection">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            @error('recipient_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message Content -->
                        <div class="mb-3">
                            <label for="content" class="form-label">
                                <i class="fas fa-comment me-1"></i>Tin nhắn đầu tiên:
                            </label>
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      id="content"
                                      name="content"
                                      rows="4"
                                      placeholder="Nhập nội dung tin nhắn..."
                                      maxlength="1000">{{ old('content') }}</textarea>
                            <div class="form-text">Tối đa 1000 ký tự</div>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message Templates -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-templates me-1"></i>Mẫu tin nhắn nhanh:
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm template-btn"
                                        data-template="Chào bạn! Tôi là quản trị viên của MechaMap. Có điều gì tôi có thể hỗ trợ bạn không?">
                                    👋 Chào hỏi
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm template-btn"
                                        data-template="Xin chào! Tôi thấy bạn có thắc mắc về kỹ thuật. Hãy chia sẻ chi tiết để tôi có thể hỗ trợ bạn tốt nhất.">
                                    ❓ Hỗ trợ kỹ thuật
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm template-btn"
                                        data-template="Cảm ơn bạn đã tham gia cộng đồng MechaMap! Chúng tôi rất vui được hỗ trợ bạn trong hành trình học tập và phát triển.">
                                    🎉 Chào mừng
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm template-btn"
                                        data-template="Chúng tôi nhận thấy có một số vấn đề cần thảo luận. Bạn có thể liên hệ để chúng ta cùng giải quyết không?">
                                    ⚠️ Thông báo
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.chat.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Hủy
                            </a>
                            <button type="submit" class="btn btn-primary" id="sendBtn" disabled>
                                <i class="fas fa-paper-plane me-1"></i>Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.template-btn {
    font-size: 0.875rem;
}

.dropdown-menu {
    border: 1px solid #dee2e6;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.position-relative {
    position: relative;
}
</style>
@endsection

@push('scripts')
<script>
// Simple test first
console.log('Script loading...');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing admin chat...');

    // Get DOM elements
    const recipientSearch = document.getElementById('recipientSearch');
    const selectedRecipientId = document.getElementById('selectedRecipientId');
    const searchResults = document.getElementById('searchResults');
    const selectedRecipient = document.getElementById('selectedRecipient');
    const contentTextarea = document.getElementById('content');
    const sendBtn = document.getElementById('sendBtn');
    const templateBtns = document.querySelectorAll('.template-btn');
    const clearSelectionBtn = document.getElementById('clearSelection');

    let searchTimeout;

    // Debug: Check if elements exist
    console.log('Elements found:', {
        recipientSearch: !!recipientSearch,
        selectedRecipientId: !!selectedRecipientId,
        searchResults: !!searchResults,
        selectedRecipient: !!selectedRecipient,
        contentTextarea: !!contentTextarea,
        sendBtn: !!sendBtn,
        templateBtns: templateBtns.length,
        clearSelectionBtn: !!clearSelectionBtn
    });

    // User search functionality
    if (recipientSearch) {
        recipientSearch.addEventListener('input', function() {
            const query = this.value.trim();
            console.log('Search query:', query);

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                console.log('Fetching users for query:', query);
                fetch(`{{ route('admin.chat.api.search-users') }}?q=${encodeURIComponent(query)}`)
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Search results:', data);
                        displaySearchResults(data.data);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchResults.style.display = 'none';
                    });
            }, 300);
        });
    }

    // Display search results
    function displaySearchResults(users) {
        if (!searchResults) {
            console.error('searchResults element not found');
            return;
        }

        console.log('Displaying results for', users.length, 'users');

        if (users.length === 0) {
            searchResults.innerHTML = '<div class="dropdown-item-text text-muted p-3">Không tìm thấy thành viên nào</div>';
        } else {
            searchResults.innerHTML = users.map(user => `
                <a href="#" class="dropdown-item user-option" data-user='${JSON.stringify(user).replace(/'/g, '&apos;')}'>
                    <div class="d-flex align-items-center py-2">
                        <img src="${user.avatar || '/images/default-avatar.png'}" class="rounded-circle me-3" width="35" height="35" alt="">
                        <div class="flex-grow-1">
                            <div class="fw-medium">${user.name}</div>
                            <div class="text-muted small">${user.email}</div>
                            ${user.username ? `<div class="text-muted small">@${user.username}</div>` : ''}
                        </div>
                        <div class="text-end">
                            <span class="badge bg-secondary small">${getRoleLabel(user.role || 'member')}</span>
                        </div>
                    </div>
                </a>
            `).join('');
        }
        searchResults.style.display = 'block';
        console.log('Search results displayed');
    }

    // Get role label in Vietnamese
    function getRoleLabel(role) {
        const roleLabels = {
            'admin': 'Quản trị',
            'moderator': 'Kiểm duyệt',
            'senior_member': 'Thành viên cao cấp',
            'member': 'Thành viên',
            'guest': 'Khách',
            'supplier': 'Nhà cung cấp',
            'manufacturer': 'Nhà sản xuất',
            'brand': 'Thương hiệu'
        };
        return roleLabels[role] || 'Thành viên';
    }

    // Handle user selection
    if (searchResults) {
        searchResults.addEventListener('click', function(e) {
            e.preventDefault();
            const userOption = e.target.closest('.user-option');
            if (userOption) {
                try {
                    const userDataStr = userOption.dataset.user.replace(/&apos;/g, "'");
                    const user = JSON.parse(userDataStr);
                    console.log('Selected user:', user);
                    selectUser(user);
                } catch (error) {
                    console.error('Error parsing user data:', error);
                }
            }
        });
    }

    // Select user
    function selectUser(user) {
        console.log('Selecting user:', user);

        if (selectedRecipientId) selectedRecipientId.value = user.id;
        if (recipientSearch) recipientSearch.value = user.name;

        const selectedAvatar = document.getElementById('selectedAvatar');
        const selectedName = document.getElementById('selectedName');
        const selectedEmail = document.getElementById('selectedEmail');
        const selectedRole = document.getElementById('selectedRole');

        if (selectedAvatar) selectedAvatar.src = user.avatar || '/images/default-avatar.png';
        if (selectedName) selectedName.textContent = user.name;
        if (selectedEmail) selectedEmail.textContent = user.email;
        if (selectedRole) selectedRole.textContent = getRoleLabel(user.role || 'member');

        if (selectedRecipient) selectedRecipient.style.display = 'block';
        if (searchResults) searchResults.style.display = 'none';
        if (recipientSearch) recipientSearch.style.display = 'none';

        checkFormValidity();
    }

    // Clear selection
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function() {
            console.log('Clearing selection');
            if (selectedRecipientId) selectedRecipientId.value = '';
            if (recipientSearch) recipientSearch.value = '';
            if (selectedRecipient) selectedRecipient.style.display = 'none';
            if (recipientSearch) {
                recipientSearch.style.display = 'block';
                recipientSearch.focus();
            }
            checkFormValidity();
        });
    }

    // Template buttons
    templateBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('Template button clicked:', this.dataset.template);
            const template = this.dataset.template;
            if (contentTextarea) {
                contentTextarea.value = template;
                checkFormValidity();
            }
        });
    });

    // Check form validity
    function checkFormValidity() {
        const hasRecipient = selectedRecipientId ? selectedRecipientId.value !== '' : false;
        const hasContent = contentTextarea ? contentTextarea.value.trim() !== '' : false;

        console.log('Form validity check:', { hasRecipient, hasContent });

        if (sendBtn) {
            sendBtn.disabled = !(hasRecipient && hasContent);
        }
    }

    // Content change listener
    if (contentTextarea) {
        contentTextarea.addEventListener('input', checkFormValidity);
    }

    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (searchResults && !e.target.closest('.position-relative')) {
            searchResults.style.display = 'none';
        }
    });

    // Initial form validation
    checkFormValidity();

    console.log('Admin chat create JavaScript initialized successfully');
});
</script>
@endpush
