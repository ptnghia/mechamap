@extends('layouts.app')

@section('title', 'Conversations')

@section('content')

<div class="py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Conversations</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                <i class="fas fa-edit-square me-1"></i> Start conversation
            </button>
        </div>

        <div class="card shadow-sm rounded-3">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Messages</h5>
                    <div class="d-flex gap-2">
                        <!-- Search Box -->
                        <div class="position-relative">
                            <input type="text" class="form-control form-control-sm" id="conversationSearch"
                                   placeholder="Tìm kiếm cuộc trò chuyện..." value="{{ $search ?? '' }}"
                                   style="width: 250px;">
                            <div class="position-absolute top-50 end-0 translate-middle-y me-2">
                                <i class="fas fa-search text-muted"></i>
                            </div>
                            <!-- Search Results Dropdown -->
                            <div id="searchResults" class="dropdown-menu w-100" style="max-height: 300px; overflow-y: auto;"></div>
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-sort me-1"></i> Sắp xếp
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="sortDropdown">
                                <li><a class="dropdown-item" href="{{ route('conversations.index', array_merge(request()->query(), ['sort_by' => 'updated_at', 'sort_order' => 'desc'])) }}">
                                    <i class="fas fa-clock me-2"></i>Mới nhất
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('conversations.index', array_merge(request()->query(), ['sort_by' => 'updated_at', 'sort_order' => 'asc'])) }}">
                                    <i class="fas fa-clock me-2"></i>Cũ nhất
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('conversations.index', array_merge(request()->query(), ['sort_by' => 'title', 'sort_order' => 'asc'])) }}">
                                    <i class="fas fa-sort-alpha-down me-2"></i>Theo tên A-Z
                                </a></li>
                            </ul>
                        </div>

                        <!-- Filter Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                id="filtersDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-filter me-1"></i> Bộ lọc
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filtersDropdown">
                                <li><a class="dropdown-item {{ !$filter ? 'active' : '' }}" href="{{ route('conversations.index', array_merge(request()->except('filter'), request()->query())) }}">
                                    <i class="fas fa-comments me-2"></i>Tất cả
                                    @if(isset($filterCounts['all']))<span class="badge bg-secondary ms-1">{{ $filterCounts['all'] }}</span>@endif
                                </a></li>
                                <li><a class="dropdown-item {{ $filter === 'unread' ? 'active' : '' }}" href="{{ route('conversations.index', array_merge(request()->query(), ['filter' => 'unread'])) }}">
                                    <i class="fas fa-envelope me-2"></i>Chưa đọc
                                    @if(isset($filterCounts['unread']))<span class="badge bg-primary ms-1">{{ $filterCounts['unread'] }}</span>@endif
                                </a></li>
                                <li><a class="dropdown-item {{ $filter === 'started' ? 'active' : '' }}" href="{{ route('conversations.index', array_merge(request()->query(), ['filter' => 'started'])) }}">
                                    <i class="fas fa-user-edit me-2"></i>Tôi bắt đầu
                                    @if(isset($filterCounts['started']))<span class="badge bg-info ms-1">{{ $filterCounts['started'] }}</span>@endif
                                </a></li>
                                <li><a class="dropdown-item {{ $filter === 'active' ? 'active' : '' }}" href="{{ route('conversations.index', array_merge(request()->query(), ['filter' => 'active'])) }}">
                                    <i class="fas fa-fire me-2"></i>Hoạt động gần đây
                                    @if(isset($filterCounts['active']))<span class="badge bg-success ms-1">{{ $filterCounts['active'] }}</span>@endif
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Active Filters Display -->
                @if($filter || $search)
                <div class="d-flex flex-wrap gap-2 mb-2">
                    @if($search)
                    <span class="badge bg-primary">
                        <i class="fas fa-search me-1"></i>Tìm kiếm: "{{ $search }}"
                        <a href="{{ route('conversations.index', array_merge(request()->except('search'), request()->query())) }}" class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    @endif
                    @if($filter)
                    <span class="badge bg-info">
                        <i class="fas fa-filter me-1"></i>Lọc: {{ ucfirst($filter) }}
                        <a href="{{ route('conversations.index', array_merge(request()->except('filter'), request()->query())) }}" class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    @endif
                </div>
                @endif
            </div>
            <div class="card-body p-0">
                @if($conversations->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($conversations as $conversation)
                    <a href="{{ route('conversations.show', $conversation) }}"
                        class="list-group-item list-group-item-action py-3 px-3 {{ $conversation->hasUnreadMessages(Auth::id()) ? 'bg-light' : '' }}">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                @php
                                $otherParticipant = $conversation->participants->where('user_id', '!=',
                                Auth::id())->first()->user ?? null;
                                @endphp

                                @if($otherParticipant)
                                <img src="{{ $otherParticipant->getAvatarUrl() }}" alt="{{ $otherParticipant->name }}"
                                    class="rounded-circle me-3" width="50" height="50">
                                @else
                                <div class="rounded-circle bg-secondary me-3 d-flex align-items-center justify-content-center"
                                    style="width: 50px; height: 50px;">
                                    <i class="fas fa-users-fill text-white"></i>
                                </div>
                                @endif

                                <div>
                                    <h6 class="mb-1 d-flex align-items-center">
                                        @if($conversation->hasUnreadMessages(Auth::id()))
                                        <span class="badge bg-primary me-2">{{
                                            $conversation->unreadMessagesCount(Auth::id()) }}</span>
                                        @endif

                                        <span
                                            class="{{ $conversation->hasUnreadMessages(Auth::id()) ? 'fw-bold' : '' }}">{{
                                            $otherParticipant->name ?? $conversation->title ?? __('conversations.Conversation')
                                            }}</span>
                                    </h6>
                                    <p class="mb-1 text-truncate" style="max-width: 500px;">
                                        @if($conversation->lastMessage && $conversation->lastMessage->user_id ==
                                        Auth::id())
                                        <span class="text-muted">You:</span>
                                        @elseif($conversation->lastMessage && $otherParticipant)
                                        <span class="text-muted">{{ $otherParticipant->name }}:</span>
                                        @endif
                                        {{ $conversation->lastMessage->content ?? __('conversations.No messages yet') }}
                                    </p>
                                    <small class="text-muted">
                                        {{ $conversation->lastMessage ?
                                        $conversation->lastMessage->created_at->diffForHumans() :
                                        $conversation->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $conversations->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-comment-dots fs-1 text-muted mb-3"></i>
                    <p class="mb-0">{{ __('conversations.There are no conversations to display.') }}</p>
                    <p class="text-muted">{{ __('conversations.Start a new conversation to connect with other users.') }}</p>
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                        data-bs-target="#newConversationModal">
                        <i class="fas fa-edit-square me-1"></i> {{ __('conversations.Start conversation') }}
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- New Conversation Modal -->
<div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('conversations.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="newConversationModalLabel">{{ __('conversations.Start conversation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="recipient_id" class="form-label fw-medium">{{ __('conversations.Recipients') }}</label>
                        <p class="text-muted small mb-2">{{ __('conversations.You may enter multiple names here.') }}</p>
                        <select name="recipient_id" id="recipient_id" class="form-select" required>
                            <option value="">{{ __('conversations.Select a user') }}</option>
                            @foreach(App\Models\User::where('id', '!=', Auth::id())->orderBy('name')->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} (@{{ $user->username }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="title" class="form-label fw-medium">{{ __('conversations.Title') }}</label>
                        <input type="text" name="title" id="title" class="form-control"
                            placeholder="{{ __('conversations.Conversation title...') }}">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label fw-medium">{{ __('conversations.Message') }}</label>
                        <textarea name="message" id="message" class="form-control" rows="8"
                            placeholder="{{ __('conversations.Your message...') }}" required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="allow_invite" id="allow_invite"
                                value="1">
                            <label class="form-check-label" for="allow_invite">
                                {{ __('conversations.Allow anyone in the conversation to invite others') }}
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lock_conversation"
                                id="lock_conversation" value="1">
                            <label class="form-check-label" for="lock_conversation">
                                {{ __('conversations.Lock conversation (no responses will be allowed)') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('conversations.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> {{ __('conversations.Start conversation') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('conversationSearch');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    // Search functionality
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        // Clear previous timeout
        clearTimeout(searchTimeout);

        if (query.length < 2) {
            searchResults.classList.remove('show');
            return;
        }

        // Debounce search
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });

    // Perform AJAX search
    function performSearch(query) {
        const currentFilter = new URLSearchParams(window.location.search).get('filter');
        const searchUrl = new URL('{{ route("conversations.search") }}', window.location.origin);
        searchUrl.searchParams.set('q', query);
        if (currentFilter) {
            searchUrl.searchParams.set('filter', currentFilter);
        }

        fetch(searchUrl)
            .then(response => response.json())
            .then(data => {
                displaySearchResults(data.data);
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResults.innerHTML = '<div class="dropdown-item text-danger">Lỗi tìm kiếm</div>';
                searchResults.classList.add('show');
            });
    }

    // Display search results
    function displaySearchResults(results) {
        if (results.length === 0) {
            searchResults.innerHTML = '<div class="dropdown-item text-muted">Không tìm thấy kết quả</div>';
        } else {
            const resultsHtml = results.map(result => {
                const participantName = result.other_participant ? result.other_participant.name : 'Unknown';
                const participantAvatar = result.other_participant ? result.other_participant.avatar : '/images/default-avatar.png';
                const lastMessage = result.last_message ? result.last_message.content : 'Chưa có tin nhắn';
                const lastMessageTime = result.last_message ? new Date(result.last_message.created_at).toLocaleDateString() : '';

                return `
                    <a href="${result.url}" class="dropdown-item py-2">
                        <div class="d-flex align-items-center">
                            <img src="${participantAvatar}" alt="${participantName}" class="rounded-circle me-2" width="32" height="32">
                            <div class="flex-grow-1">
                                <div class="fw-medium">${participantName}</div>
                                <div class="text-muted small">${lastMessage}</div>
                                ${lastMessageTime ? `<div class="text-muted small">${lastMessageTime}</div>` : ''}
                            </div>
                        </div>
                    </a>
                `;
            }).join('');

            searchResults.innerHTML = resultsHtml;
        }

        searchResults.classList.add('show');
    }

    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.remove('show');
        }
    });

    // Handle Enter key for full search
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = this.value.trim();
            if (query) {
                const currentUrl = new URL(window.location);
                currentUrl.searchParams.set('search', query);
                window.location.href = currentUrl.toString();
            }
        }
    });

    // Clear search when input is empty
    searchInput.addEventListener('keyup', function() {
        if (this.value === '') {
            searchResults.classList.remove('show');
        }
    });
});
</script>
@endpush

@endsection
