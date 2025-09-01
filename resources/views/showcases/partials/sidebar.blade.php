{{--
    Showcase Sidebar Component - Simplified Version
    Hiển thị thông tin tác giả cơ bản
--}}

<div class="showcase-sidebar">
    {{-- 1. Hồ sơ tác giả showcase hiện tại --}}
    @if(isset($showcase) && $showcase && isset($showcase->user) && $showcase->user)
    <div class="sidebar-section author-profile">
        <div class="section-header">
            <h5 class="section-title">
                <i class="fas fa-user-circle"></i>
                Hồ sơ tác giả
            </h5>
        </div>

        <div class="author-card">
            <div class="author-avatar">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($showcase->user->name ?? 'User') }}&size=200&background=3498db&color=ffffff&font-size=0.6&rounded=true"
                     alt="{{ $showcase->user->name ?? 'User' }}"
                     class="avatar-img"
                     loading="lazy">
            </div>

            <div class="author-info">
                <h6 class="author-name">
                    {{ $showcase->user->name ?? 'Unknown User' }}
                </h6>

                <div class="author-role">
                    <span class="role-badge role-member">
                        Member
                    </span>
                </div>

                <div class="author-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Tham gia từ {{ $showcase->user->created_at ? $showcase->user->created_at->format('M Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Thống kê tác giả --}}
        @if(isset($authorStats) && is_array($authorStats))
        <div class="author-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $authorStats['total_showcases'] ?? 0 }}</div>
                <div class="stat-label">{{ __('showcase.sidebar.total_showcases') }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ number_format($authorStats['total_views'] ?? 0) }}</div>
                <div class="stat-label">{{ __('showcase.sidebar.total_views') }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ number_format($authorStats['avg_rating'] ?? 0, 1) }}</div>
                <div class="stat-label">{{ __('showcase.sidebar.avg_rating') }}</div>
            </div>
        </div>
        @endif

        {{-- Action buttons --}}
        <div class="author-actions">
            @if($showcase->user->username)
                <a href="{{ route('profile.show', $showcase->user->username) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-user"></i>
                    {{ __('showcase.sidebar.view_profile') }}
                </a>
            @endif

            @auth
                @if($showcase->user_id !== auth()->id())
                    <button type="button"
                            class="btn btn-primary btn-sm follow-btn"
                            data-user-id="{{ $showcase->user_id }}"
                            data-following="false">
                        <i class="fas fa-user-plus"></i>
                        <span class="follow-text">
                            {{ __('showcase.sidebar.follow') }}
                        </span>
                    </button>

                    @if($showcase->user->email)
                        <a href="mailto:{{ $showcase->user->email }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-envelope"></i>
                            {{ __('showcase.sidebar.contact') }}
                        </a>
                    @endif
                @endif
            @endauth
        </div>
    </div>
    @endif

    {{-- 2. Showcases khác của cùng tác giả --}}
    @if(isset($otherShowcases) && $otherShowcases && $otherShowcases->count() > 0)
        <div class="sidebar-section other-showcases">
            <div class="section-header">
                <h5 class="section-title">
                    <i class="fas fa-layer-group"></i>
                    {{ __('showcase.sidebar.other_showcases') }}
                </h5>
            </div>

            <div class="showcase-list">
                @foreach($otherShowcases as $otherShowcase)
                    @if($otherShowcase && $otherShowcase->title)
                    <div class="showcase-item">
                        <div class="showcase-thumbnail">
                            <a href="{{ route('showcase.show', $otherShowcase->slug) }}">
                                <img src="{{ $otherShowcase->cover_image_url ?? asset('images/placeholder-showcase.svg') }}"
                                     alt="{{ $otherShowcase->title ?? 'Showcase' }}"
                                     class="thumbnail-img"
                                     loading="lazy">
                            </a>
                        </div>

                        <div class="showcase-info">
                            <h6 class="showcase-title">
                                <a href="{{ route('showcase.show', $otherShowcase->slug) }}">
                                    {{ Str::limit($otherShowcase->title ?? 'Untitled', 50) }}
                                </a>
                            </h6>

                            <div class="showcase-meta">
                                <span class="meta-item">
                                    <i class="fas fa-eye"></i>
                                    {{ number_format($otherShowcase->view_count ?? 0) }} {{ __('showcase.sidebar.views') }}
                                </span>

                                @if($otherShowcase->rating_average && $otherShowcase->rating_average > 0)
                                    <span class="meta-item">
                                        <i class="fas fa-star"></i>
                                        {{ number_format($otherShowcase->rating_average, 1) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    @else
        <div class="sidebar-section other-showcases">
            <div class="section-header">
                <h5 class="section-title">
                    <i class="fas fa-layer-group"></i>
                    {{ __('showcase.sidebar.other_showcases') }}
                </h5>
            </div>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <p>{{ __('showcase.sidebar.no_other_showcases') }}</p>
            </div>
        </div>
    @endif

    {{-- 3. Showcases nổi bật --}}
    @if(isset($featuredShowcases) && $featuredShowcases && $featuredShowcases->count() > 0)
        <div class="sidebar-section featured-showcases">
            <div class="section-header">
                <h5 class="section-title">
                    <i class="fas fa-star"></i>
                    {{ __('showcase.sidebar.featured_showcases') }}
                </h5>
            </div>

            <div class="showcase-list">
                @foreach($featuredShowcases as $featuredShowcase)
                    @if($featuredShowcase && $featuredShowcase->title && $featuredShowcase->user)
                    <div class="showcase-item">
                        <div class="showcase-thumbnail">
                            <a href="{{ route('showcase.show', $featuredShowcase->slug) }}">
                                <img src="{{ $featuredShowcase->cover_image_url ?? asset('images/placeholder-showcase.svg') }}"
                                     alt="{{ $featuredShowcase->title ?? 'Showcase' }}"
                                     class="thumbnail-img"
                                     loading="lazy">
                            </a>
                        </div>

                        <div class="showcase-info">
                            <h6 class="showcase-title">
                                <a href="{{ route('showcase.show', $featuredShowcase->slug) }}">
                                    {{ Str::limit($featuredShowcase->title ?? 'Untitled', 45) }}
                                </a>
                            </h6>

                            <div class="showcase-author">
                                @if($featuredShowcase->user->username)
                                    <a href="{{ route('profile.show', $featuredShowcase->user->username) }}">
                                        {{ $featuredShowcase->user->name ?? 'Unknown User' }}
                                    </a>
                                @else
                                    {{ $featuredShowcase->user->name ?? 'Unknown User' }}
                                @endif
                            </div>

                            <div class="showcase-meta">
                                <span class="meta-item">
                                    <i class="fas fa-eye"></i>
                                    {{ number_format($featuredShowcase->view_count ?? 0) }}
                                </span>

                                @if($featuredShowcase->rating_average && $featuredShowcase->rating_average > 0)
                                    <span class="meta-item">
                                        <i class="fas fa-star"></i>
                                        {{ number_format($featuredShowcase->rating_average, 1) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    @else
        <div class="sidebar-section featured-showcases">
            <div class="section-header">
                <h5 class="section-title">
                    <i class="fas fa-star"></i>
                    {{ __('showcase.sidebar.featured_showcases') }}
                </h5>
            </div>
            <div class="empty-state">
                <i class="fas fa-star"></i>
                <p>{{ __('showcase.sidebar.no_featured_showcases') }}</p>
            </div>
        </div>
    @endif

    {{-- 4. Top contributors --}}
    @if(isset($topContributors) && $topContributors && $topContributors->count() > 0)
        <div class="sidebar-section top-contributors">
            <div class="section-header">
                <h5 class="section-title">
                    <i class="fas fa-trophy"></i>
                    {{ __('showcase.sidebar.top_contributors') }}
                </h5>
            </div>

            <div class="contributors-list">
                @foreach($topContributors as $index => $contributor)
                    @if($contributor && $contributor->name)
                    <div class="contributor-item">
                        <div class="contributor-rank">
                            #{{ $index + 1 }}
                        </div>

                        <div class="contributor-avatar">
                            <img src="{{ $contributor->avatar_url ?? asset('images/default-avatar.png') }}"
                                 alt="{{ $contributor->name ?? 'User' }}"
                                 class="avatar-img"
                                 loading="lazy">
                        </div>

                        <div class="contributor-info">
                            <h6 class="contributor-name">
                                @if($contributor->username)
                                    <a href="{{ route('profile.show', $contributor->username) }}">
                                        {{ $contributor->name ?? 'Unknown User' }}
                                    </a>
                                @else
                                    {{ $contributor->name ?? 'Unknown User' }}
                                @endif
                            </h6>

                            <div class="contributor-stats">
                                <span class="stat">
                                    {{ $contributor->showcases_count ?? 0 }} {{ __('showcase.sidebar.showcases') }}
                                </span>
                                <span class="stat">
                                    {{ number_format($contributor->total_views ?? 0) }} {{ __('showcase.sidebar.views') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    @else
        <div class="sidebar-section top-contributors">
            <div class="section-header">
                <h5 class="section-title">
                    <i class="fas fa-trophy"></i>
                    {{ __('showcase.sidebar.top_contributors') }}
                </h5>
            </div>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>{{ __('showcase.sidebar.no_contributors') }}</p>
            </div>
        </div>
    @endif
</div>
