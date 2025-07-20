@extends('layouts.app')

@section('title', 'Tin nhắn mới')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('chat.index') }}" class="btn btn-light btn-sm me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h5 class="mb-0">
                            <i class="fas fa-plus me-2"></i>
                            Tin nhắn mới
                        </h5>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('chat.store') }}" method="POST" id="newChatForm">
                        @csrf

                        <!-- Recipient Selection -->
                        <div class="mb-4">
                            <label for="recipientSearch" class="form-label">
                                <i class="fas fa-user me-2"></i>
                                Người nhận:
                            </label>
                            <div class="position-relative">
                                <input type="text" class="form-control" id="recipientSearch"
                                       placeholder="Tìm kiếm thành viên..." autocomplete="off">
                                <input type="hidden" name="recipient_id" id="selectedRecipientId" required>

                                <!-- Search Results Dropdown -->
                                <div id="searchResults" class="dropdown-menu w-100" style="display: none; max-height: 300px; overflow-y: auto;">
                                    <!-- Results will be populated here -->
                                </div>
                            </div>

                            <!-- Selected Recipient Display -->
                            <div id="selectedRecipient" class="mt-2" style="display: none;">
                                <div class="alert alert-info d-flex align-items-center">
                                    <img id="selectedAvatar" src="" class="rounded-circle me-3" width="40" height="40" alt="">
                                    <div>
                                        <strong id="selectedName"></strong>
                                        <br>
                                        <small id="selectedEmail" class="text-muted"></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" onclick="clearSelection()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            @error('recipient_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message Input -->
                        <div class="mb-4">
                            <label for="message" class="form-label">
                                <i class="fas fa-comment me-2"></i>
                                Tin nhắn đầu tiên:
                            </label>
                            <textarea class="form-control" id="message" name="message" rows="4"
                                      placeholder="Nhập tin nhắn..." maxlength="1000" required>{{ old('message') }}</textarea>
                            <div class="form-text">Tối đa 1000 ký tự</div>

                            @error('message')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quick Templates -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-magic me-2"></i>
                                Mẫu tin nhắn nhanh:
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="useTemplate('Chào bạn! Tôi thấy profile của bạn rất thú vị. Có thể kết nối và trao đổi kinh nghiệm không?')">
                                    👋 Chào hỏi
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="useTemplate('Xin chào! Tôi có một câu hỏi kỹ thuật và nghĩ bạn có thể giúp đỡ. Bạn có thời gian trao đổi không?')">
                                    ❓ Hỏi kỹ thuật
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="useTemplate('Hi! Tôi thấy bạn có kinh nghiệm về CAD/CNC. Có thể chia sẻ một số tips không?')">
                                    🔧 Chia sẻ kinh nghiệm
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="useTemplate('Chào bạn! Tôi đang tìm hiểu về dự án và nghĩ chúng ta có thể hợp tác. Bạn có quan tâm không?')">
                                    🤝 Hợp tác
                                </button>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('chat.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                Hủy
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>
                                Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.dropdown-menu {
    border: 1px solid #dee2e6;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.dropdown-item {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f8f9fa;
}

.dropdown-item:last-child {
    border-bottom: none;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.position-relative {
    position: relative;
}

#searchResults {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const recipientSearch = document.getElementById('recipientSearch');
    const searchResults = document.getElementById('searchResults');
    const selectedRecipientId = document.getElementById('selectedRecipientId');
    const selectedRecipient = document.getElementById('selectedRecipient');
    const submitBtn = document.getElementById('submitBtn');

    let searchTimeout;

    // Search users
    recipientSearch.addEventListener('input', function() {
        const query = this.value.trim();

        clearTimeout(searchTimeout);

        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            searchUsers(query);
        }, 300);
    });

    // Search users function
    function searchUsers(query) {
        fetch(`/messages/api/search-users?q=${encodeURIComponent(query)}`, {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data.data || []);
        })
        .catch(error => {
            console.error('Error searching users:', error);
            searchResults.style.display = 'none';
        });
    }

    // Display search results
    function displaySearchResults(users) {
        if (users.length === 0) {
            searchResults.innerHTML = '<div class="dropdown-item text-muted">Không tìm thấy người dùng nào</div>';
            searchResults.style.display = 'block';
            return;
        }

        const html = users.map(user => `
            <div class="dropdown-item" style="cursor: pointer;" onclick="selectUser(${user.id}, '${user.name}', '${user.email}', '${user.avatar || '/images/default-avatar.png'}')">
                <div class="d-flex align-items-center">
                    <img src="${user.avatar || '/images/default-avatar.png'}"
                         class="rounded-circle me-3" width="40" height="40" alt="">
                    <div>
                        <div class="fw-medium">${user.name}</div>
                        <small class="text-muted">${user.email}</small>
                    </div>
                </div>
            </div>
        `).join('');

        searchResults.innerHTML = html;
        searchResults.style.display = 'block';
    }

    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!recipientSearch.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
});

// Select user function (global scope for onclick)
function selectUser(id, name, email, avatar) {
    document.getElementById('selectedRecipientId').value = id;
    document.getElementById('recipientSearch').value = name;
    document.getElementById('selectedName').textContent = name;
    document.getElementById('selectedEmail').textContent = email;
    document.getElementById('selectedAvatar').src = avatar;

    document.getElementById('selectedRecipient').style.display = 'block';
    document.getElementById('searchResults').style.display = 'none';
}

// Clear selection
function clearSelection() {
    document.getElementById('selectedRecipientId').value = '';
    document.getElementById('recipientSearch').value = '';
    document.getElementById('selectedRecipient').style.display = 'none';
}

// Use template
function useTemplate(template) {
    document.getElementById('message').value = template;
}
</script>
@endpush
@endsection
