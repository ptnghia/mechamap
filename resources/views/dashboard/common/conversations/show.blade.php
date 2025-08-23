@extends('dashboard.layouts.app')

@section('title', 'Chi tiết cuộc hội thoại')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <i class="fas fa-comments text-primary me-3 fs-3"></i>
                <div>
                    <h1 class="h3 mb-1">{{ $conversation->title ?: 'Cuộc hội thoại' }}</h1>
                    <p class="text-muted mb-0">
                        Với {{ $otherParticipants->pluck('user.username')->join(', ') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('dashboard.conversations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Messages Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-comment-dots me-2"></i>Tin nhắn
                    </h5>
                    <small class="text-muted">{{ $messages->total() }} tin nhắn</small>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    @if($messages->count() > 0)
                        @foreach($messages->reverse() as $message)
                            <div class="message-item mb-3 {{ $message->user_id === auth()->id() ? 'text-end' : '' }}">
                                <div class="d-inline-block {{ $message->user_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }} rounded p-3" style="max-width: 70%;">
                                    <div class="message-content">
                                        {!! nl2br(e($message->content)) !!}
                                    </div>
                                    <div class="message-meta mt-2 {{ $message->user_id === auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                        <small>
                                            <strong>{{ $message->user->username }}</strong>
                                            • {{ $message->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comment-slash text-muted fs-1 mb-3"></i>
                            <p class="text-muted">Chưa có tin nhắn nào trong cuộc hội thoại này.</p>
                        </div>
                    @endif
                </div>
                
                <!-- Pagination -->
                @if($messages->hasPages())
                    <div class="card-footer">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reply Form -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-reply me-2"></i>Trả lời
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.conversations.reply', $conversation) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="content" class="form-label">Tin nhắn</label>
                            <textarea 
                                class="form-control @error('content') is-invalid @enderror" 
                                id="content" 
                                name="content" 
                                rows="4" 
                                placeholder="Nhập tin nhắn của bạn..."
                                required
                            >{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Tin nhắn sẽ được gửi đến tất cả người tham gia cuộc hội thoại
                            </small>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.message-item {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.message-content {
    word-wrap: break-word;
    line-height: 1.5;
}

.card-body {
    scrollbar-width: thin;
    scrollbar-color: #dee2e6 transparent;
}

.card-body::-webkit-scrollbar {
    width: 6px;
}

.card-body::-webkit-scrollbar-track {
    background: transparent;
}

.card-body::-webkit-scrollbar-thumb {
    background-color: #dee2e6;
    border-radius: 3px;
}

.card-body::-webkit-scrollbar-thumb:hover {
    background-color: #adb5bd;
}
</style>
@endsection
