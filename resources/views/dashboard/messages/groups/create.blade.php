@extends('dashboard.layouts.app')

@section('title', 'Tạo nhóm thảo luận')

@section('content')
<div class="container-fluid group-creation-wizard">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.messages.index') }}">
                                    <i class="fas fa-comments me-1"></i>
                                    Tin nhắn
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Tạo nhóm</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-users text-success me-2"></i>
                        Tạo nhóm thảo luận
                    </h1>
                    <p class="text-muted mb-0">Tạo nhóm thảo luận để nhiều người cùng tham gia và chia sẻ kiến thức</p>
                </div>
                <div>
                    <a href="{{ route('dashboard.messages.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Create Group Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle text-success me-2"></i>
                        Thông tin nhóm
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Wizard Steps -->
                    <div class="wizard-steps mb-4">
                        <div class="step-indicator active" data-step="1">
                            <div class="step-number">1</div>
                            <div class="step-title">Thông tin cơ bản</div>
                        </div>
                        <div class="step-indicator" data-step="2">
                            <div class="step-number">2</div>
                            <div class="step-title">Thành viên</div>
                        </div>
                        <div class="step-indicator" data-step="3">
                            <div class="step-number">3</div>
                            <div class="step-title">Xác nhận</div>
                        </div>
                    </div>

                    <form action="{{ route('dashboard.messages.groups.request') }}" method="POST" id="createGroupForm" class="wizard-form">
                        @csrf

                        <!-- Step 1: Basic Information -->
                        <div class="wizard-step" data-step="1">
                            <!-- Conversation Type -->
                            <div class="form-group mb-4">
                            <label for="conversation_type_id" class="form-label fw-semibold">
                                <i class="fas fa-tag text-primary me-1"></i>
                                Loại nhóm <span class="text-danger">*</span>
                            </label>
                            <select name="conversation_type_id" id="conversation_type_id" class="form-select @error('conversation_type_id') is-invalid @enderror" required>
                                <option value="">Chọn loại nhóm...</option>
                                @foreach($conversationTypes ?? [] as $type)
                                    <option value="{{ $type->id }}"
                                            data-max-members="{{ $type->max_members }}"
                                            data-requires-approval="{{ $type->requires_approval ? 'true' : 'false' }}"
                                            data-description="{{ $type->description }}"
                                            {{ old('conversation_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                        @if($type->requires_approval)
                                            (Cần duyệt)
                                        @else
                                            (Tự động)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('conversation_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="typeDescription" class="form-text mt-2"></div>
                        </div>

                            <!-- Group Title -->
                            <div class="form-group mb-4">
                            <label for="title" class="form-label fw-semibold">
                                <i class="fas fa-heading text-primary me-1"></i>
                                Tên nhóm <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   placeholder="Nhập tên nhóm thảo luận..."
                                   value="{{ old('title') }}"
                                   maxlength="255"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <span id="titleCount">0</span>/255 ký tự
                            </div>
                        </div>

                        <!-- Group Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">
                                <i class="fas fa-align-left text-primary me-1"></i>
                                Mô tả nhóm <span class="text-danger">*</span>
                            </label>
                            <textarea name="description"
                                      id="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Mô tả chi tiết về mục đích và nội dung thảo luận của nhóm..."
                                      maxlength="1000"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <span id="descriptionCount">0</span>/1000 ký tự
                            </div>
                        </div>

                        <!-- Group Purpose -->
                        <div class="mb-4">
                            <label for="purpose" class="form-label fw-semibold">
                                <i class="fas fa-bullseye text-primary me-1"></i>
                                Mục đích tạo nhóm <span class="text-danger">*</span>
                            </label>
                            <textarea name="purpose"
                                      id="purpose"
                                      class="form-control @error('purpose') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Giải thích lý do tạo nhóm và những gì bạn muốn đạt được..."
                                      maxlength="500"
                                      required>{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <span id="purposeCount">0</span>/500 ký tự
                            </div>
                        </div>

                        <!-- Initial Members -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user-plus text-primary me-1"></i>
                                Mời thành viên ban đầu (tùy chọn)
                            </label>
                            <div class="border rounded p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text"
                                               id="memberSearch"
                                               class="form-control mb-3"
                                               placeholder="Tìm kiếm thành viên...">
                                        <div id="memberList" class="member-list" style="max-height: 200px; overflow-y: auto;">
                                            @foreach($availableUsers ?? [] as $user)
                                                <div class="member-item d-flex align-items-center p-2 border-bottom" data-user-id="{{ $user->id }}">
                                                    <img src="{{ $user->avatar ?? '/images/default-avatar.png' }}"
                                                         alt="{{ $user->name }}"
                                                         class="rounded-circle me-2"
                                                         width="32" height="32">
                                                    <div class="flex-grow-1">
                                                        <div class="fw-semibold">{{ $user->name }}</div>
                                                        <small class="text-muted">{{ $user->email }}</small>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary add-member-btn">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-semibold mb-3">Thành viên đã chọn (<span id="selectedCount">0</span>/10)</h6>
                                        <div id="selectedMembers" class="selected-members">
                                            <!-- Selected members will be added here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-text">
                                Bạn có thể mời tối đa 10 thành viên ban đầu. Có thể thêm thành viên khác sau khi nhóm được tạo.
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="mb-4">
                            <label for="additional_info" class="form-label fw-semibold">
                                <i class="fas fa-info-circle text-primary me-1"></i>
                                Thông tin bổ sung (tùy chọn)
                            </label>
                            <textarea name="additional_info"
                                      id="additional_info"
                                      class="form-control @error('additional_info') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Thông tin bổ sung cho admin xem xét (nếu cần duyệt)..."
                                      maxlength="500">{{ old('additional_info') }}</textarea>
                            @error('additional_info')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <span id="additionalInfoCount">0</span>/500 ký tự
                            </div>
                        </div>

                        <!-- Approval Notice -->
                        <div id="approvalNotice" class="alert alert-info d-none">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Lưu ý:</strong> Loại nhóm này cần được admin duyệt trước khi có thể sử dụng.
                            Bạn sẽ nhận được thông báo khi yêu cầu được xử lý.
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-1"></i>
                                Gửi yêu cầu tạo nhóm
                            </button>
                            <a href="{{ route('dashboard.messages.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Hủy bỏ
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- Group Types Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Các loại nhóm
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($conversationTypes ?? [] as $type)
                        <div class="mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">{{ $type->name }}</h6>
                                <span class="badge {{ $type->requires_approval ? 'bg-warning' : 'bg-success' }}">
                                    {{ $type->requires_approval ? 'Cần duyệt' : 'Tự động' }}
                                </span>
                            </div>
                            <p class="text-muted small mb-2">{{ $type->description }}</p>
                            <div class="small text-muted">
                                <i class="fas fa-users me-1"></i>
                                Tối đa {{ $type->max_members }} thành viên
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Guidelines -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        Hướng dẫn tạo nhóm
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Chọn tên nhóm rõ ràng, dễ hiểu
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Mô tả chi tiết mục đích và nội dung
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Mời những thành viên phù hợp
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Tuân thủ quy định cộng đồng
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Duy trì hoạt động tích cực
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counters
    const fields = ['title', 'description', 'purpose', 'additional_info'];
    fields.forEach(field => {
        const input = document.getElementById(field);
        const counter = document.getElementById(field + 'Count');
        if (input && counter) {
            input.addEventListener('input', function() {
                counter.textContent = this.value.length;
            });
            // Initialize counter
            counter.textContent = input.value.length;
        }
    });

    // Conversation type change handler
    const typeSelect = document.getElementById('conversation_type_id');
    const approvalNotice = document.getElementById('approvalNotice');
    const typeDescription = document.getElementById('typeDescription');

    typeSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const requiresApproval = option.dataset.requiresApproval === 'true';
        const description = option.dataset.description;
        const maxMembers = option.dataset.maxMembers;

        if (requiresApproval) {
            approvalNotice.classList.remove('d-none');
        } else {
            approvalNotice.classList.add('d-none');
        }

        if (description) {
            typeDescription.innerHTML = `<i class="fas fa-info-circle text-info me-1"></i>${description} (Tối đa ${maxMembers} thành viên)`;
        } else {
            typeDescription.innerHTML = '';
        }
    });

    // Member selection functionality
    const memberSearch = document.getElementById('memberSearch');
    const memberList = document.getElementById('memberList');
    const selectedMembers = document.getElementById('selectedMembers');
    const selectedCount = document.getElementById('selectedCount');
    let selectedMemberIds = [];

    // Search functionality
    memberSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const memberItems = memberList.querySelectorAll('.member-item');

        memberItems.forEach(item => {
            const name = item.querySelector('.fw-semibold').textContent.toLowerCase();
            const email = item.querySelector('.text-muted').textContent.toLowerCase();

            if (name.includes(searchTerm) || email.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Add member functionality
    memberList.addEventListener('click', function(e) {
        if (e.target.closest('.add-member-btn')) {
            const memberItem = e.target.closest('.member-item');
            const userId = memberItem.dataset.userId;

            if (selectedMemberIds.length >= 10) {
                alert('Bạn chỉ có thể mời tối đa 10 thành viên ban đầu.');
                return;
            }

            if (!selectedMemberIds.includes(userId)) {
                addSelectedMember(memberItem, userId);
            }
        }
    });

    // Remove member functionality
    selectedMembers.addEventListener('click', function(e) {
        if (e.target.closest('.remove-member-btn')) {
            const userId = e.target.closest('.selected-member').dataset.userId;
            removeSelectedMember(userId);
        }
    });

    function addSelectedMember(memberItem, userId) {
        selectedMemberIds.push(userId);

        const name = memberItem.querySelector('.fw-semibold').textContent;
        const email = memberItem.querySelector('.text-muted').textContent;
        const avatar = memberItem.querySelector('img').src;

        const selectedMemberHtml = `
            <div class="selected-member d-flex align-items-center p-2 border rounded mb-2" data-user-id="${userId}">
                <img src="${avatar}" alt="${name}" class="rounded-circle me-2" width="32" height="32">
                <div class="flex-grow-1">
                    <div class="fw-semibold small">${name}</div>
                    <small class="text-muted">${email}</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-member-btn">
                    <i class="fas fa-times"></i>
                </button>
                <input type="hidden" name="initial_members[]" value="${userId}">
            </div>
        `;

        selectedMembers.insertAdjacentHTML('beforeend', selectedMemberHtml);
        memberItem.style.display = 'none';
        updateSelectedCount();
    }

    function removeSelectedMember(userId) {
        selectedMemberIds = selectedMemberIds.filter(id => id !== userId);

        const selectedMember = selectedMembers.querySelector(`[data-user-id="${userId}"]`);
        if (selectedMember) {
            selectedMember.remove();
        }

        const memberItem = memberList.querySelector(`[data-user-id="${userId}"]`);
        if (memberItem) {
            memberItem.style.display = 'flex';
        }

        updateSelectedCount();
    }

    function updateSelectedCount() {
        selectedCount.textContent = selectedMemberIds.length;
    }
});
</script>

<!-- Group Creation Wizard Styles -->
<link rel="stylesheet" href="{{ asset('css/frontend/group-responsive.css') }}">

<!-- Group Creation Wizard Scripts -->
<script src="{{ asset('js/group-creation-wizard.js') }}"></script>
<script src="{{ asset('js/group-mobile-enhancements.js') }}"></script>
<script src="{{ asset('js/group-features-integration.js') }}"></script>
@endpush
