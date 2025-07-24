{{-- Rating Item Component --}}
<div class="rating-item mb-3" id="rating-{{ $rating->id }}">
    <div class="d-flex gap-3">
        <a href="{{ route('profile.show', $rating->user->username) }}">
            <img src="{{ $rating->user->getAvatarUrl() }}" class="rounded-circle" width="40" height="40" 
                alt="Avatar của {{ $rating->user->display_name }}"
                onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr($rating->user->name, 0, 1))) }}&background=6366f1&color=fff&size=40'">
        </a>
        <div class="flex-grow-1">
            <div class="rating-content bg-light p-3 rounded">
                <div class="rating-header mb-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <a href="{{ route('profile.show', $rating->user->username) }}" 
                                class="fw-semibold text-decoration-none">
                                {{ $rating->user->display_name }}
                            </a>
                            <small class="text-muted ms-2">
                                {{ $rating->created_at->diffForHumans() }}
                            </small>
                        </div>
                        {{-- Nút xóa rating (chỉ hiện với chủ sở hữu) --}}
                        @auth
                        @if($rating->user_id === auth()->id() || $showcase->user_id === auth()->id())
                        <form action="{{ route('showcase.rating.delete', $rating) }}" method="POST" 
                            onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                        @endauth
                    </div>
                </div>

                {{-- Nội dung đánh giá (nếu có) --}}
                @if($rating->review)
                <div class="rating-body mb-3">
                    {!! nl2br(e($rating->review)) !!}
                </div>
                @endif

                {{-- Phần đánh giá các tiêu chí --}}
                <div class="rating-criteria">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="criteria-item d-flex justify-content-between align-items-center">
                                <span class="criteria-label">Chất lượng kỹ thuật:</span>
                                <div class="criteria-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $rating->technical_quality ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-1 small text-muted">({{ $rating->technical_quality }}/5)</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="criteria-item d-flex justify-content-between align-items-center">
                                <span class="criteria-label">Tính sáng tạo:</span>
                                <div class="criteria-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $rating->innovation ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-1 small text-muted">({{ $rating->innovation }}/5)</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="criteria-item d-flex justify-content-between align-items-center">
                                <span class="criteria-label">Tính hữu ích:</span>
                                <div class="criteria-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $rating->usefulness ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-1 small text-muted">({{ $rating->usefulness }}/5)</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="criteria-item d-flex justify-content-between align-items-center">
                                <span class="criteria-label">Chất lượng tài liệu:</span>
                                <div class="criteria-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $rating->documentation ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-1 small text-muted">({{ $rating->documentation }}/5)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tổng điểm đánh giá --}}
                    <div class="overall-rating mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Đánh giá tổng thể:</span>
                            <div class="d-flex align-items-center">
                                @php
                                    $averageRating = ($rating->technical_quality + $rating->innovation + $rating->usefulness + $rating->documentation) / 4;
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= round($averageRating) ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <span class="ms-2 fw-bold text-primary">{{ number_format($averageRating, 1) }}/5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
