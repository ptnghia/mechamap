{{-- Portfolio & Dự án Section (thay thế Gallery) --}}
<div class="card portfolio-section mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-briefcase"></i> {{ __('profile.portfolio_projects') }}
            @if(isset($portfolioItems) && $portfolioItems->count() > 0)
                <span class="badge bg-secondary ms-2">{{ $portfolioItems->count() }}</span>
            @endif
        </h5>
        @if(Auth::check() && Auth::id() == $user->id)
            <a href="{{ route('showcase.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> {{ __('profile.add_project') }}
            </a>
        @endif
    </div>
    <div class="card-body">
        @if(isset($portfolioItems) && $portfolioItems->count() > 0)
            <div class="portfolio-grid">
                <div class="row">
                    @foreach($portfolioItems as $item)
                        <div class="col-md-6 col-lg-4 mb-4">
                            @if($item instanceof \App\Models\Showcase)
                                {{-- Use showcase-item partial for showcases --}}
                                @include('partials.showcase-item', ['showcase' => $item])
                            @else
                                {{-- Custom display for threads with attachments --}}
                                <div class="portfolio-item card h-100">
                                {{-- Project Image/Preview --}}
                                <div class="portfolio-image">
                                    @if($item->featured_image)
                                        <img src="{{ $item->featured_image }}" alt="{{ $item->title }}"
                                             class="card-img-top" style="height: 200px; object-fit: cover;">
                                    @elseif($item->images && $item->images->count() > 0)
                                        <img src="{{ $item->images->first()->url }}" alt="{{ $item->title }}"
                                             class="card-img-top" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="placeholder-image card-img-top d-flex align-items-center justify-content-center bg-light"
                                             style="height: 200px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif

                                    {{-- Project Type Badge --}}
                                    <div class="project-type-badge">
                                        @if($item instanceof \App\Models\Showcase)
                                            <span class="badge bg-primary">
                                                <i class="fas fa-star"></i> Showcase
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="fas fa-comments"></i> Thread
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-body">
                                    {{-- Project Title --}}
                                    <h6 class="card-title">
                                        @if($item instanceof \App\Models\Showcase)
                                            <a href="{{ route('showcase.show', $item) }}" class="text-decoration-none">
                                                {{ $item->title }}
                                            </a>
                                        @else
                                            <a href="{{ route('threads.show', $item) }}" class="text-decoration-none">
                                                {{ $item->title }}
                                            </a>
                                        @endif
                                    </h6>

                                    {{-- Project Description --}}
                                    @if($item->description || $item->excerpt)
                                        <p class="card-text text-muted small">
                                            {{ Str::limit($item->description ?? $item->excerpt, 100) }}
                                        </p>
                                    @endif

                                    {{-- Project Category --}}
                                    @if($item->category)
                                        <div class="project-category mb-2">
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-folder"></i> {{ $item->category->name }}
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Project Tags --}}
                                    @if($item->tags && $item->tags->count() > 0)
                                        <div class="project-tags mb-2">
                                            @foreach($item->tags->take(3) as $tag)
                                                <span class="badge bg-secondary me-1">{{ $tag->name }}</span>
                                            @endforeach
                                            @if($item->tags->count() > 3)
                                                <span class="text-muted small">+{{ $item->tags->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Project Stats --}}
                                    <div class="project-stats d-flex justify-content-between align-items-center">
                                        <div class="stats-left">
                                            @if($item instanceof \App\Models\Showcase)
                                                <small class="text-muted">
                                                    <i class="fas fa-eye"></i> {{ $item->views_count ?? 0 }}
                                                    <i class="fas fa-heart ms-2"></i> {{ $item->likes_count ?? 0 }}
                                                </small>
                                            @else
                                                <small class="text-muted">
                                                    <i class="fas fa-comments"></i> {{ $item->comments_count ?? 0 }}
                                                    <i class="fas fa-thumbs-up ms-2"></i> {{ $item->reactions_count ?? 0 }}
                                                </small>
                                            @endif
                                        </div>
                                        <div class="stats-right">
                                            <small class="text-muted">
                                                {{ $item->created_at->format('M Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Project Files/Attachments --}}
                                @if(($item->attachments && $item->attachments->count() > 0) || ($item->files && $item->files->count() > 0))
                                    <div class="card-footer bg-light">
                                        <div class="project-files">
                                            <small class="text-muted">
                                                <i class="fas fa-paperclip"></i>
                                                @php
                                                    $filesCount = $item->attachments ? $item->attachments->count() : ($item->files ? $item->files->count() : 0);
                                                @endphp
                                                {{ $filesCount }} {{ __('profile.files') }}
                                            </small>

                                            {{-- File Type Icons --}}
                                            <div class="file-types mt-1">
                                                @php
                                                    $files = $item->attachments ?? $item->files ?? collect();
                                                    $fileTypes = $files->pluck('filename')->map(function($filename) {
                                                        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                                    })->unique()->take(4);
                                                @endphp

                                                @foreach($fileTypes as $type)
                                                    @switch($type)
                                                        @case('pdf')
                                                            <i class="fas fa-file-pdf text-danger me-1" title="PDF"></i>
                                                            @break
                                                        @case('dwg')
                                                        @case('dxf')
                                                            <i class="fas fa-drafting-compass text-primary me-1" title="CAD"></i>
                                                            @break
                                                        @case('jpg')
                                                        @case('jpeg')
                                                        @case('png')
                                                        @case('gif')
                                                            <i class="fas fa-image text-success me-1" title="Image"></i>
                                                            @break
                                                        @case('doc')
                                                        @case('docx')
                                                            <i class="fas fa-file-word text-primary me-1" title="Word"></i>
                                                            @break
                                                        @case('xls')
                                                        @case('xlsx')
                                                            <i class="fas fa-file-excel text-success me-1" title="Excel"></i>
                                                            @break
                                                        @default
                                                            <i class="fas fa-file text-muted me-1" title="{{ strtoupper($type) }}"></i>
                                                    @endswitch
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- View All Projects Link --}}
            @if($portfolioItems->count() >= 6)
                <div class="text-center mt-4">
                    @if($user->showcases()->count() > 0)
                        <a href="{{ route('showcase.user', $user) }}" class="btn btn-outline-primary">
                            {{ __('profile.view_all_projects') }} ({{ $user->showcases()->count() }})
                        </a>
                    @endif
                </div>
            @endif
        @else
            <div class="empty-state text-center py-5">
                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">{{ __('profile.no_portfolio_items') }}</h6>
                @if(Auth::id() == $user->id)
                    <p class="text-muted mb-3">{{ __('profile.showcase_your_work_message') }}</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('showcase.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('profile.create_showcase') }}
                        </a>
                        <a href="{{ route('threads.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-comments"></i> {{ __('profile.create_thread') }}
                        </a>
                    </div>
                @else
                    <p class="text-muted">{{ $user->name }} {{ __('profile.hasnt_shared_projects_yet') }}</p>
                @endif
            </div>
        @endif
    </div>
</div>

<style>
.portfolio-item {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.portfolio-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.portfolio-image {
    position: relative;
    overflow: hidden;
}

.project-type-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2;
}

.portfolio-image img {
    transition: transform 0.3s ease;
}

.portfolio-item:hover .portfolio-image img {
    transform: scale(1.05);
}

.project-files .file-types i {
    font-size: 0.9em;
}

.card-title a:hover {
    color: #0056b3 !important;
}

@media (max-width: 768px) {
    .portfolio-grid .col-md-6 {
        margin-bottom: 1rem;
    }

    .portfolio-image {
        height: 150px !important;
    }
}
</style>
