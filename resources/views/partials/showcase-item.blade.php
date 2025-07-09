@php
// Thiết lập các biến cần thiết cho showcase item với error handling
$showcaseUrl = route('showcase.show', $showcase);
$userName = $showcase->user->name ?? 'Người dùng';
$userAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&color=7F9CF5&background=EBF4FF';

// Safe avatar handling - consistent với thread-item
if (isset($showcase->user)) {
    $userAvatar = $showcase->user->profile_photo_url ?? (
        $showcase->user->avatar ??
        $userAvatar
    );
}

$showcaseTitle = $showcase->title ?? 'Untitled Showcase';
$showcaseDescription = isset($showcase->description) ? Str::limit($showcase->description, 100) : '';
$viewCount = $showcase->view_count ?? 0;
$likesCount = $showcase->likes_count ?? 0;
$createdAt = isset($showcase->created_at) && $showcase->created_at instanceof \Carbon\Carbon
    ? $showcase->created_at->diffForHumans()
    : '';

// Image handling with fallback
$coverImageUrl = 'https://via.placeholder.com/400x200/f8f9fa/6c757d?text=No+Image';
try {
    if (method_exists($showcase, 'getCoverImageUrl')) {
        $coverImageUrl = $showcase->getCoverImageUrl();
    } elseif (isset($showcase->cover_image)) {
        $coverImageUrl = $showcase->cover_image;
    } elseif (isset($showcase->featured_image)) {
        $coverImageUrl = $showcase->featured_image;
    }
} catch (\Exception $e) {
    // Use fallback image
}

$category = $showcase->category ?? null;
@endphp

<div class="showcase-card h-100">
    <div class="showcase-image">
        <img src="{{ $coverImageUrl }}" alt="{{ $showcaseTitle }}" class="img-fluid">
        <div class="showcase-overlay">
            <div class="showcase-actions">
                <a href="{{ $showcaseUrl }}" class="btn btn-light btn-sm">
                    <i class="fas fa-eye"></i> {{ __('buttons.view_details') }}
                </a>
            </div>
        </div>
    </div>
    <div class="showcase-content">
        <div class="showcase-meta">
            <img src="{{ $userAvatar }}" alt="{{ $userName }}" class="author-avatar rounded-circle"
                 width="40" height="40" style="object-fit: cover;"
                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($userName) }}&color=7F9CF5&background=EBF4FF'">
            <div class="author-info">
                <h6 class="author-name">{{ $userName }}</h6>
                <small class="text-muted">{{ $createdAt }}</small>
            </div>
        </div>
        <h5 class="showcase-title">
            <a href="{{ $showcaseUrl }}">{{ $showcaseTitle }}</a>
        </h5>
        @if($showcaseDescription)
        <p class="showcase-description">{{ $showcaseDescription }}</p>
        @endif
        <div class="showcase-stats">
            <div class="d-flex align-items-center gap-3">
                <span class="stat-item">
                    <i class="fas fa-eye"></i> {{ number_format($viewCount) }}
                </span>
                <span class="stat-item">
                    <i class="fas fa-heart"></i> {{ number_format($likesCount) }}
                </span>
            </div>
            @if($category)
            <span class="badge bg-info">{{ $category }}</span>
            @endif
        </div>
    </div>
</div>
