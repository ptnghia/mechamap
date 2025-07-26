{{-- Comment Details Modal Content --}}
<div class="comment-details">
    {{-- Comment Header --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-3">
                <img src="{{ $comment->user->getAvatarUrl() }}"
                    alt="Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px;"
                    onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($comment->user->name, 0, 1))]) }}'">
                <div>
                    <h5 class="mb-1">{{ $comment->user->name }}</h5>
                    <small class="text-muted">{{ $comment->user->email }}</small>
                    <br>
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        {{ $comment->created_at->format('d/m/Y H:i') }}
                        ({{ $comment->created_at->diffForHumans() }})
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-end">
            @php
            $statusClasses = [
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'flagged' => 'bg-danger',
            'spam' => 'bg-dark'
            ];
            $statusLabels = [
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            'flagged' => 'Đã báo cáo',
            'spam' => 'Spam'
            ];
            @endphp
            <div class="mb-2">
                <span class="badge {{ $statusClasses[$comment->moderation_status] ?? 'bg-secondary' }} fs-6">
                    {{ $statusLabels[$comment->moderation_status] ?? $comment->moderation_status }}
                </span>
            </div>
            @if($comment->spam_score)
            <div class="mb-2">
                @php
                $score = round($comment->spam_score * 100);
                $scoreClass = 'bg-success';
                if ($score >= 70) $scoreClass = 'bg-danger';
                elseif ($score >= 30) $scoreClass = 'bg-warning';
                @endphp
                <span class="badge {{ $scoreClass }}">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Spam: {{ $score }}%
                </span>
            </div>
            @endif
        </div>
    </div>

    {{-- Thread Context --}}
    <div class="card mb-4">
        <div class="card-header py-2">
            <h6 class="mb-0">
                <i class="fas fa-link me-1"></i>
                Thread gốc
            </h6>
        </div>
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-8">
                    <h6>
                        <a href="{{ route('threads.show', $comment->thread->slug) }}" target="_blank"
                            class="text-decoration-none">
                            {{ $comment->thread->title }}
                        </a>
                    </h6>
                    <small class="text-muted">
                        <span class="badge bg-secondary me-1">{{ $comment->thread->forum->name }}</span>
                        <i class="fas fa-user me-1"></i>{{ $comment->thread->user->name }}
                        <i class="fas fa-calendar ms-2 me-1"></i>{{ $comment->thread->created_at->format('d/m/Y') }}
                    </small>
                </div>
                <div class="col-md-4 text-end">
                    @php
                    $threadStatusClasses = [
                    'pending' => 'bg-warning',
                    'approved' => 'bg-success',
                    'rejected' => 'bg-danger',
                    'flagged' => 'bg-danger'
                    ];
                    @endphp
                    <span
                        class="badge {{ $threadStatusClasses[$comment->thread->moderation_status] ?? 'bg-secondary' }}">
                        Thread: {{ ucfirst($comment->thread->moderation_status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Parent Comment (nếu là reply) --}}
    @if($comment->parent_id && $comment->parent)
    <div class="card mb-4">
        <div class="card-header py-2 bg-light">
            <h6 class="mb-0">
                <i class="fas fa-reply me-1"></i>
                Trả lời comment của {{ $comment->parent->user->name }}
            </h6>
        </div>
        <div class="card-body py-2">
            <div class="border-start border-3 border-info ps-3">
                <div class="d-flex align-items-center mb-2">
                    <img src="{{ $comment->parent->user->getAvatarUrl() }}"
                        alt="Avatar" class="rounded-circle me-2" style="width: 24px; height: 24px;"
                        onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($comment->parent->user->name, 0, 1))]) }}'">
                    <strong>{{ $comment->parent->user->name }}</strong>
                    <small class="text-muted ms-2">{{ $comment->parent->created_at->diffForHumans() }}</small>
                </div>
                <div class="text-muted">
                    {{ Str::limit(strip_tags($comment->parent->content), 200) }}
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Comment Content --}}
    <div class="card mb-4">
        <div class="card-header py-2">
            <h6 class="mb-0">
                <i class="fas fa-comment me-1"></i>
                Nội dung comment
            </h6>
        </div>
        <div class="card-body">
            <div class="border rounded p-3 bg-light">
                {!! nl2br(e($comment->content)) !!}
            </div>

            {{-- Content Analysis --}}
            <div class="mt-3">
                <h6>Phân tích nội dung:</h6>
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center p-2 border rounded">
                            <div class="fw-bold">{{ str_word_count(strip_tags($comment->content)) }}</div>
                            <small class="text-muted">Số từ</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-2 border rounded">
                            <div class="fw-bold">{{ strlen(strip_tags($comment->content)) }}</div>
                            <small class="text-muted">Ký tự</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-2 border rounded">
                            <div class="fw-bold">{{ preg_match_all('/https?:\/\/[^\s]+/', $comment->content) }}</div>
                            <small class="text-muted">Links</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-2 border rounded">
                            <div class="fw-bold">{{ preg_match_all('/[A-Z]/', $comment->content) }}</div>
                            <small class="text-muted">Chữ hoa</small>
                        </div>
                    </div>
                </div>

                {{-- Red Flags Detection --}}
                @php
                $redFlags = [];
                if (str_word_count(strip_tags($comment->content)) < 3) { $redFlags[]='Nội dung quá ngắn' ; } if
                    (preg_match('/https?:\/\//', $comment->content)) {
                    $redFlags[] = 'Chứa liên kết';
                    }
                    if (preg_match('/[A-Z]{5,}/', $comment->content)) {
                    $redFlags[] = 'Nhiều chữ hoa liên tiếp';
                    }
                    if (preg_match('/(.)\1{4,}/', $comment->content)) {
                    $redFlags[] = 'Ký tự lặp lại';
                    }
                    $suspiciousWords = ['click', 'free', 'buy', 'sale', 'discount', 'offer'];
                    foreach ($suspiciousWords as $word) {
                    if (stripos($comment->content, $word) !== false) {
                    $redFlags[] = 'Chứa từ khóa spam: ' . $word;
                    }
                    }
                    @endphp

                    @if(count($redFlags) > 0)
                    <div class="mt-3">
                        <h6 class="text-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Cảnh báo ({{ count($redFlags) }}):
                        </h6>
                        <ul class="list-group list-group-flush">
                            @foreach($redFlags as $flag)
                            <li class="list-group-item py-1 border-0 text-danger">
                                <i class="fas fa-times me-1"></i>{{ $flag }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
            </div>
        </div>
    </div>

    {{-- Reports (nếu có) --}}
    @if($comment->reports && $comment->reports->count() > 0)
    <div class="card mb-4">
        <div class="card-header py-2 bg-danger text-white">
            <h6 class="mb-0">
                <i class="fas fa-flag me-1"></i>
                Báo cáo ({{ $comment->reports->count() }})
            </h6>
        </div>
        <div class="card-body py-2">
            @foreach($comment->reports as $report)
            <div class="border rounded p-2 mb-2 {{ $loop->last ? 'mb-0' : '' }}">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center">
                        <img src="{{ $report->reporter->getAvatarUrl() }}"
                            alt="Avatar" class="rounded-circle me-2" style="width: 24px; height: 24px;"
                            onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($report->reporter->name, 0, 1))]) }}'">
                        <strong>{{ $report->reporter->name }}</strong>
                    </div>
                    <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                </div>
                <div class="small">
                    <strong>Lý do:</strong> {{ $report->reason }}
                </div>
                @if($report->description)
                <div class="small text-muted">
                    <strong>Mô tả:</strong> {{ $report->description }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- User Information --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0">Thông tin tác giả</h6>
                </div>
                <div class="card-body py-2">
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="text-primary">
                                <i class="fas fa-comments fa-lg"></i>
                            </div>
                            <div><strong>{{ $comment->user->threads_count ?? 0 }}</strong></div>
                            <small class="text-muted">Threads</small>
                        </div>
                        <div class="col-6">
                            <div class="text-success">
                                <i class="fas fa-comment fa-lg"></i>
                            </div>
                            <div><strong>{{ $comment->user->comments_count ?? 0 }}</strong></div>
                            <small class="text-muted">Comments</small>
                        </div>
                    </div>
                    <div class="small">
                        <div><strong>Đăng ký:</strong> {{ $comment->user->created_at->format('d/m/Y') }}</div>
                        <div><strong>Thành viên từ:</strong> {{ $comment->user->created_at->diffForHumans() }}</div>
                        @if($comment->user->last_activity_at)
                        <div><strong>Hoạt động cuối:</strong> {{ $comment->user->last_activity_at->diffForHumans() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0">Lịch sử moderation</h6>
                </div>
                <div class="card-body py-2">
                    @php
                    $userModerationStats = [
                    'pending_comments' => $comment->user->comments()->where('moderation_status', 'pending')->count(),
                    'rejected_comments' => $comment->user->comments()->where('moderation_status', 'rejected')->count(),
                    'spam_comments' => $comment->user->comments()->where('moderation_status', 'spam')->count(),
                    'total_reports' => $comment->user->receivedReports()->count()
                    ];
                    @endphp
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="text-warning">
                                <i class="fas fa-clock fa-lg"></i>
                            </div>
                            <div><strong>{{ $userModerationStats['pending_comments'] }}</strong></div>
                            <small class="text-muted">Chờ duyệt</small>
                        </div>
                        <div class="col-6">
                            <div class="text-danger">
                                <i class="fas fa-times fa-lg"></i>
                            </div>
                            <div><strong>{{ $userModerationStats['rejected_comments'] }}</strong></div>
                            <small class="text-muted">Bị từ chối</small>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="text-dark">
                                <i class="fas fa-exclamation-triangle fa-lg"></i>
                            </div>
                            <div><strong>{{ $userModerationStats['spam_comments'] }}</strong></div>
                            <small class="text-muted">Spam</small>
                        </div>
                        <div class="col-6">
                            <div class="text-info">
                                <i class="fas fa-flag fa-lg"></i>
                            </div>
                            <div><strong>{{ $userModerationStats['total_reports'] }}</strong></div>
                            <small class="text-muted">Báo cáo</small>
                        </div>
                    </div>

                    @if($userModerationStats['rejected_comments'] > 5 || $userModerationStats['spam_comments'] > 3 ||
                    $userModerationStats['total_reports'] > 5)
                    <div class="alert alert-warning mt-2 py-1">
                        <small>
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            User có lịch sử moderation đáng ngờ
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-resize modal content if needed
$(document).ready(function() {
    setTimeout(() => {
        const modal = $('#commentDetailsModal');
        if (modal.length && modal.hasClass('show')) {
            const maxHeight = $(window).height() - 120;
            modal.find('.modal-body').css('max-height', maxHeight + 'px').css('overflow-y', 'auto');
        }
    }, 100);
});
</script>

<style>
    .comment-details .card {
        border: 1px solid #dee2e6;
    }

    .comment-details .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .comment-details .list-group-item {
        background-color: transparent;
    }

    .comment-details .border-start {
        border-left-width: 3px !important;
    }
</style>
