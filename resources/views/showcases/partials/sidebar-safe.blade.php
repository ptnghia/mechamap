{{--
    Showcase Sidebar Component
    Hiển thị thông tin tác giả, showcases khác, showcases nổi bật và top contributors
--}}

<div class="showcase-sidebar">
    {{-- 1. Hồ sơ tác giả showcase hiện tại --}}
    @if($showcase && $showcase->user)
    <div class="sidebar-section author-profile">
        <div class="section-header">
            <h5 class="section-title">
                <i class="fas fa-user-circle"></i>
                {{ __('showcase.sidebar.author_profile') }}
            </h5>
        </div>

        <div class="author-card">
            <div class="author-avatar">
                @php
                    // Simplified avatar logic to avoid conflicts between method and accessor
                    $userAvatar = route('avatar.generate', ['initial' => 'U']);
                    if ($showcase->user) {
                        $user = $showcase->user;
                        if (!empty($user->avatar)) {
                            if (strpos($user->avatar, 'http') === 0) {
                                $userAvatar = $user->avatar;
                            } elseif (strpos($user->avatar, '/images/') === 0) {
                                $userAvatar = asset($user->avatar);
                            } else {
                                $cleanPath = ltrim($user->avatar, '/');
                                $userAvatar = asset('images/' . $cleanPath);
                            }
                        } else {
                            // Generate initials for avatar
                            $name = $user->name ?: $user->username ?: $user->email ?: 'User';
                            $initials = strtoupper(substr($name, 0, 1));
                            if (strpos($name, ' ') !== false) {
                                $nameParts = array_filter(explode(' ', trim($name)));
                                if (count($nameParts) >= 2) {
                                    $firstInitial = strtoupper(substr($nameParts[0], 0, 1));
                                    $lastInitial = strtoupper(substr(end($nameParts), 0, 1));
                                    $initials = $firstInitial . $lastInitial;
                                }
                            }
                            $userAvatar = route('avatar.generate', ['initial' => $initials]);
                        }
                    }
                @endphp
                <img src="{{ $userAvatar }}"
                     alt="{{ $showcase->user->name }}"
                     class="avatar-img">
            </div>

            <div class="author-info">
                <h6 class="author-name">
                    <a href="{{ route('profile.show', $showcase->user->username) }}">
                        {{ $showcase->user->name }}
                    </a>
                </h6>

                <div class="author-role">
                    @php
                        $userRole = is_string($showcase->user->role) ? $showcase->user->role : 'member';
                    @endphp
                    <span class="role-badge role-{{ $userRole }}">
                        {{ ucfirst(str_replace('_', ' ', $userRole)) }}
                    </span>
                </div>

                <div class="author-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ __('showcase.sidebar.member_since') }} {{ $showcase->user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Thống kê tác giả --}}
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

        {{-- Action buttons --}}
        <div class="author-actions">
            <a href="{{ route('profile.show', $showcase->user->username) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-user"></i>
                {{ __('showcase.sidebar.view_profile') }}
            </a>

            @auth
                @if($showcase->user_id !== auth()->id())
                    <button type="button"
                            class="btn btn-primary btn-sm follow-btn"
                            data-user-id="{{ $showcase->user_id }}"
                            data-following="{{ $showcase->isFollowedBy(auth()->id()) ? 'true' : 'false' }}">
                        <i class="fas fa-{{ $showcase->isFollowedBy(auth()->id()) ? 'user-minus' : 'user-plus' }}"></i>
                        <span class="follow-text">
                            {{ $showcase->isFollowedBy(auth()->id()) ? __('showcase.sidebar.unfollow') : __('showcase.sidebar.follow') }}
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
    @if($otherShowcases && $otherShowcases->count() > 0)
        <div class="sidebar-section other-showcases">
            <div class="section-header">
                <h5 class="section-title">
                    <i class="fas fa-layer-group"></i>
                    {{ __('showcase.sidebar.other_showcases') }}
                </h5>
            </div>

            <div class="showcase-list">
                @foreach($otherShowcases as $otherShowcase)
                    @include('partials.showcase-item', ['showcase' => $otherShowcase, 'layout' => 'sidebar'])
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
    @if($featuredShowcases && $featuredShowcases->count() > 0)
        <div class="sidebar-section featured-showcases">
            <div class="section-header">
                <h5 class="section-title">
                    <i class="fas fa-star"></i>
                    {{ __('showcase.sidebar.featured_showcases') }}
                </h5>
            </div>

            <div class="showcase-list">
                @foreach($featuredShowcases as $featuredShowcase)
                    @php
                        // Image handling with fallback - simplified to avoid infinite loops
                        $coverImageUrl = asset('images/placeholder.svg');
                        try {
                            if (!empty($featuredShowcase->cover_image)) {
                                // Direct cover image handling without file_exists checks
                                if (filter_var($featuredShowcase->cover_image, FILTER_VALIDATE_URL)) {
                                    $coverImageUrl = $featuredShowcase->cover_image;
                                } elseif (strpos($featuredShowcase->cover_image, '/images/') === 0) {
                                    $coverImageUrl = asset($featuredShowcase->cover_image);
                                } else {
                                    $cleanPath = ltrim(str_replace('public/', '', $featuredShowcase->cover_image), '/');
                                    $coverImageUrl = asset('storage/' . $cleanPath);
                                }
                            }
                        } catch (\Exception $e) {
                            // Use fallback image
                        }
                    @endphp
                    <div class="showcase-item">
                        <div class="showcase-thumbnail">
                            <a href="{{ route('showcase.show', $featuredShowcase) }}">
                                <img src="{{ $coverImageUrl }}"
                                     alt="{{ $featuredShowcase->title }}"
                                     class="thumbnail-img"
                                     onerror="if(this.src!='{{ asset('images/placeholder.svg') }}' && this.src!='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg=='){this.src='{{ asset('images/placeholder.svg') }}'}else if(this.src=='{{ asset('images/placeholder.svg') }}'){this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg=='}">
                            </a>
                        </div>

                        <div class="showcase-info">
                            <h6 class="showcase-title">
                                <a href="{{ route('showcase.show', $featuredShowcase) }}">
                                    {{ Str::limit($featuredShowcase->title, 45) }}
                                </a>
                            </h6>

                            <div class="showcase-author">
                                <a href="{{ route('profile.show', $featuredShowcase->user->username) }}">
                                    {{ $featuredShowcase->user->name }}
                                </a>
                            </div>

                            <div class="showcase-meta">
                                <span class="meta-item">
                                    <i class="fas fa-eye"></i>
                                    {{ number_format($featuredShowcase->view_count ?? 0) }}
                                </span>

                                @if($featuredShowcase->rating_average)
                                    <span class="meta-item">
                                        <i class="fas fa-star"></i>
                                        {{ number_format($featuredShowcase->rating_average, 1) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
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
                <i class="fas fa-star-o"></i>
                <p>{{ __('showcase.sidebar.no_featured_showcases') }}</p>
            </div>
        </div>
    @endif

    {{-- 4. Top contributors --}}
    @if($topContributors && $topContributors->count() > 0)
        <div class="sidebar-section top-contributors">
            <div class="section-header">
                <h5 class="section-title">
                    <i class="fas fa-trophy"></i>
                    {{ __('showcase.sidebar.top_contributors') }}
                </h5>
            </div>

            <div class="contributors-list">
                @foreach($topContributors as $index => $contributor)
                    @php
                        // Simplified avatar logic to avoid conflicts between method and accessor
                        $contributorAvatar = route('avatar.generate', ['initial' => 'U']);
                        if (!empty($contributor->avatar)) {
                            if (strpos($contributor->avatar, 'http') === 0) {
                                $contributorAvatar = $contributor->avatar;
                            } elseif (strpos($contributor->avatar, '/images/') === 0) {
                                $contributorAvatar = asset($contributor->avatar);
                            } else {
                                $cleanPath = ltrim($contributor->avatar, '/');
                                $contributorAvatar = asset('images/' . $cleanPath);
                            }
                        } else {
                            // Generate initials for avatar
                            $name = $contributor->name ?: $contributor->username ?: $contributor->email ?: 'User';
                            $initials = strtoupper(substr($name, 0, 1));
                            if (strpos($name, ' ') !== false) {
                                $nameParts = array_filter(explode(' ', trim($name)));
                                if (count($nameParts) >= 2) {
                                    $firstInitial = strtoupper(substr($nameParts[0], 0, 1));
                                    $lastInitial = strtoupper(substr(end($nameParts), 0, 1));
                                    $initials = $firstInitial . $lastInitial;
                                }
                            }
                            $contributorAvatar = route('avatar.generate', ['initial' => $initials]);
                        }
                    @endphp
                    <div class="contributor-item">
                        <div class="contributor-rank">
                            #{{ $index + 1 }}
                        </div>

                        <div class="contributor-avatar">
                            <img src="{{ $contributorAvatar }}"
                                 alt="{{ $contributor->name }}"
                                 class="avatar-img">
                        </div>

                        <div class="contributor-info">
                            <h6 class="contributor-name">
                                <a href="{{ route('profile.show', $contributor->username) }}">
                                    {{ $contributor->name }}
                                </a>
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
