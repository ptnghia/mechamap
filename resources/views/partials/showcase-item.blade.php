@php
// Thiết lập các biến cần thiết cho showcase item với error handling
$showcaseUrl = route('showcase.show', $showcase);
$userName = $showcase->user->name ?? 'Người dùng';
// Sử dụng method getAvatarUrl() để đảm bảo logic nhất quán
$userAvatar = isset($showcase->user) ? $showcase->user->getAvatarUrl() : route('avatar.generate', ['initial' => 'U']);

$showcaseTitle = $showcase->title ?? 'Untitled Showcase';
$showcaseDescription = isset($showcase->description) ? Str::limit($showcase->description, 100) : '';
$viewCount = $showcase->view_count ?? 0;
$likesCount = $showcase->like_count ?? $showcase->likes_count ?? 0;
$createdAt = isset($showcase->created_at) && $showcase->created_at instanceof \Carbon\Carbon
    ? $showcase->created_at->diffForHumans()
    : '';

// Image handling with fallback
$coverImageUrl = 'https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg';
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

// Rating information
$ratingAverage = $showcase->rating_average ?? 0;
$ratingCount = $showcase->rating_count ?? 0;
$hasRating = $ratingAverage > 0 && $ratingCount > 0;

// Comment count
$commentCount = $showcase->comments_count ?? $showcase->comments()->count();

// Additional showcase info
$projectType = $showcase->project_type ?? null;
$complexityLevel = $showcase->complexity_level ?? null;
$softwareUsed = $showcase->getFormattedSoftwareUsed(40); // Use new helper method
$softwareArray = $showcase->getSoftwareUsedArray(); // Get as array for advanced display
$hasCadFiles = $showcase->has_cad_files ?? false;
$allowDownloads = $showcase->allow_downloads ?? false;
@endphp

<div class="showcase-card h-100">
    <div class="showcase-image">
        <img src="{{ $coverImageUrl }}" alt="{{ $showcaseTitle }}" class="img-fluid"
             onerror="this.src='{{ asset('images/placeholder.svg') }}'">

        {{-- Enhanced: Overlay badges --}}
        <div class="showcase-badges">
            @if($complexityLevel)
            <span class="badge badge-complexity badge-{{ $complexityLevel }}">
                <i class="fa-solid fa-layer-group me-1"></i> {{ t_showcase('complexity_levels.' . $complexityLevel) }}
            </span>
            @endif

            @if($hasCadFiles)
            <span class="badge badge-feature bg-success">
                <i class="fas fa-cube me-1"></i>{{ t_showcase('features.cad') }}
            </span>
            @endif

            @if($allowDownloads)
            <span class="badge badge-feature bg-info">
                <i class="fas fa-download me-1"></i>{{ t_showcase('features.download') }}
            </span>
            @endif
        </div>

        <div class="showcase-overlay">
            <div class="showcase-actions">
                <a href="{{ $showcaseUrl }}" class="btn btn-light btn-sm">
                    <i class="fas fa-eye"></i> {{ t_ui('buttons.view_details') }}
                </a>

                @auth
                {{-- Bookmark Button --}}
                @php
                    $isBookmarked = auth()->user()->bookmarks()
                        ->where('bookmarkable_type', 'App\Models\Showcase')
                        ->where('bookmarkable_id', $showcase->id)
                        ->exists();
                @endphp
                <button class="btn btn-outline-warning btn-sm showcase-bookmark-btn"
                        data-showcase-id="{{ $showcase->slug }}"
                        data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}">
                    <i class="fas fa-bookmark"></i>
                    <span class="bookmark-text">
                        {{ $isBookmarked ? t_ui('buttons.bookmarked') : t_ui('buttons.bookmark') }}
                    </span>
                </button>

                {{-- Follow Button (Follow Author) --}}
                @if($showcase->user_id !== auth()->id())
                @php
                    $isFollowing = auth()->user()->following()
                        ->where('following_id', $showcase->user_id)
                        ->exists();
                @endphp
                <button class="btn btn-outline-info btn-sm showcase-follow-btn"
                        data-showcase-id="{{ $showcase->slug }}"
                        data-following="{{ $isFollowing ? 'true' : 'false' }}">
                    <i class="fas fa-bell"></i>
                    <span class="follow-text">
                        {{ $isFollowing ? t_ui('buttons.following') : t_ui('buttons.follow') }}
                    </span>
                </button>
                @endif
                @endauth
            </div>
        </div>
    </div>
    <div class="showcase-content">
        <div class="showcase-meta">
            <img src="{{ $userAvatar }}" alt="{{ $userName }}" class="author-avatar rounded-circle"
                 width="40" height="40" style="object-fit: cover;"
                 onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($userName, 0, 1))]) }}'">
            <div class="author-info">
                <h6 class="author-name">{{ $userName }}</h6>
                <small class="text-muted">{{ $createdAt }}</small>
            </div>
        </div>
        <h5 class="showcase-title">
            <a href="{{ $showcaseUrl }}">{{ $showcaseTitle }}</a>
        </h5>
        {{-- Enhanced: Rating Display --}}
        @if($hasRating)
        <div class="showcase-rating mb-2">
            <div class="rating-display">
                <div class="stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($ratingAverage))
                        <i class="fas fa-star text-warning"></i>
                        @elseif($i - 0.5 <= $ratingAverage)
                        <i class="fas fa-star-half-alt text-warning"></i>
                        @else
                        <i class="far fa-star text-muted"></i>
                        @endif
                    @endfor
                </div>
                <span class="rating-text small text-muted ms-2">
                    {{ number_format($ratingAverage, 1) }} ({{ $ratingCount }} {{ t_showcase('ratings') }})
                </span>
            </div>
        </div>
        @endif
        @if($showcaseDescription)
        <p class="showcase-description">{{ $showcaseDescription }}</p>
        @endif

        {{-- Enhanced: Technical Info --}}
        @if($softwareUsed)
        <div class="showcase-tech-info mb-2">
            <div class="tech-item">
                <i class="fas fa-tools text-muted me-1"></i>
                @if(count($softwareArray) > 1)
                    {{-- Multiple software: show as badges --}}
                    <div class="software-badges">
                        @foreach($softwareArray as $index => $software)
                            @if($index < 3) {{-- Show max 3 badges --}}
                                <span class="badge bg-light text-dark me-1 small">{{ $software }}</span>
                            @elseif($index === 3)
                                <span class="badge bg-secondary small">+{{ count($softwareArray) - 3 }}</span>
                                @break
                            @endif
                        @endforeach
                    </div>
                @else
                    {{-- Single software: show as text --}}
                    <div class="software-badges">
                    <span class="badge bg-light text-dark me-1 small">{{ $softwareUsed }}</span>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <div class="showcase-stats">
            <div class="d-flex align-items-center">
                <span class="stat-item">
                    <i class="fas fa-eye"></i> {{ number_format($viewCount) }}
                </span>
                <span class="stat-item">
                    <i class="fas fa-heart"></i> {{ number_format($likesCount) }}
                </span>
                @if($commentCount > 0)
                <span class="stat-item">
                    <i class="fas fa-comment"></i> {{ number_format($commentCount) }}
                </span>
                @endif
                @if($showcase->download_count > 0)
                <span class="stat-item">
                    <i class="fas fa-download"></i> {{ number_format($showcase->download_count) }}
                </span>
                @endif
            </div>
            {{-- Enhanced: Category & Project Type with Smart Logic --}}
            <div class="showcase-categories mb-2">
                @php
                    // Smart logic to avoid duplication and improve UX
                    $showCategory = !empty($category);
                    $showProjectType = !empty($projectType) && $projectType !== $category;

                    // Generate category link if route exists
                    $categoryLink = null;
                    if ($showCategory && Route::has('showcase.index')) {
                        $categoryLink = route('showcase.index', ['category' => $category]);
                    }
                @endphp

                @if($showCategory)
                    @if($categoryLink)
                        <a href="{{ $categoryLink }}" class="badge bg-primary text-decoration-none me-1"
                           title="{{ t_showcase('view_category_showcases', ['category' => ucfirst($category)]) }}">
                            <i class="fa-solid fa-folder-open me-1"></i> {{ ucfirst($category) }}
                        </a>
                    @else
                        <span class="badge bg-primary me-1" title="{{ t_showcase('category') }}: {{ ucfirst($category) }}">
                            <i class="fa-solid fa-folder-open me-1"></i> {{ ucfirst($category) }}
                        </span>
                    @endif
                @endif

                @if($showProjectType)
                    <span class="badge bg-info text-dark" title="{{ t_showcase('project_type') }}: {{ ucfirst($projectType) }}">
                        <i class="fa-solid fa-cogs me-1"></i> {{ ucfirst($projectType) }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
