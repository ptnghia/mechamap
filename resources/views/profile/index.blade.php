@extends('layouts.app-full')

@section('title', __('ui.users.page_title'))

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/users.css') }}">
@endpush

@section('content')
<div class="body_page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="div_title_page">
            <h1 class="h2 mb-1 title_page">{{ seo_title_short(__('ui.users.page_title')) }}</h1>
            <p class="text-muted mb-0">{{ seo_value('description', '')  }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted">{{ $users->total() }} {{ __('ui.users.member_count') }}</span>
            <!-- View toggle -->
            <div class="btn-group" role="group">
                <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}"
                    class="btn btn-sm {{ request('view', 'list') === 'list' ? 'btn-primary' : 'btn-outline-secondary' }}">
                    <i class="fas fa-list"></i>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}"
                    class="btn btn-sm {{ request('view') === 'grid' ? 'btn-primary' : 'btn-outline-secondary' }}">
                    <i class="fas fa-th"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Filter tabs -->
    <ul class="nav nav-pills nav-fill mb-3 tab_mechamap">
        <li class="nav-item">
            <a class="nav-link {{ request('filter', 'all') === 'all' ? 'active' : '' }}"
                href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}">
                {{ __('ui.users.all_members') }}
                <span class="badge bg-danger ms-1">{{ $stats['total_members'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('filter') === 'online' ? 'active' : '' }}"
                href="{{ request()->fullUrlWithQuery(['filter' => 'online']) }}">
                {{ __('ui.users.online_members') }}
                <span class="badge bg-danger ms-1">{{ $stats['online_members'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('filter') === 'staff' ? 'active' : '' }}"
                href="{{ request()->fullUrlWithQuery(['filter' => 'staff']) }}">
                {{ __('ui.users.staff') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('users.leaderboard') }}">
                {{ __('ui.users.leaderboard') }}
            </a>
        </li>
    </ul>

    <!-- Advanced Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET" class="row g-3">
                <!-- Preserve current filter and view -->
                <input type="hidden" name="filter" value="{{ request('filter', 'all') }}">
                <input type="hidden" name="view" value="{{ request('view', 'list') }}">
                <div class="col-md-9">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control form-sm" id="search" name="search"  value="{{ request('search') }}" placeholder="Tên, username hoặc email...">
                        </div>

                        <div class="col-md-4">
                            <select class="form-select form-sm" id="role" name="role">
                                <option value="">{{ __('ui.users.all_roles') }}</option>

                                <!-- Community Management Group -->
                                <optgroup label="{{ __('ui.users.community_management_group') }}">
                                    <option value="community_management" {{ request('role') == 'community_management' ? 'selected' : '' }}>{{ __('ui.users.all_moderator') }}</option>
                                    <option value="content_moderator" {{ request('role') == 'content_moderator' ? 'selected' : '' }}>Content Moderator</option>
                                    <option value="marketplace_moderator" {{ request('role') == 'marketplace_moderator' ? 'selected' : '' }}>Marketplace Moderator</option>
                                    <option value="community_moderator" {{ request('role') == 'community_moderator' ? 'selected' : '' }}>Community Moderator</option>
                                </optgroup>

                                <!-- Community Members Group -->
                                <optgroup label="{{ __('ui.users.community_members_group') }}">
                                    <option value="community_members" {{ request('role') == 'community_members' ? 'selected' : '' }}>{{ __('ui.users.all_members_role') }}</option>
                                    <option value="senior_member" {{ request('role') == 'senior_member' ? 'selected' : '' }}>Senior Member</option>
                                    <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Member</option>
                                    <option value="guest" {{ request('role') == 'guest' ? 'selected' : '' }}>Guest</option>
                                </optgroup>

                                <!-- Business Partners Group -->
                                <optgroup label="{{ __('ui.users.business_partners_group') }}">
                                    <option value="business_partners" {{ request('role') == 'business_partners' ? 'selected' : '' }}>{{ __('ui.users.all_partners') }}</option>
                                    <option value="verified_partner" {{ request('role') == 'verified_partner' ? 'selected' : '' }}>Verified Partner</option>
                                    <option value="manufacturer" {{ request('role') == 'manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                                    <option value="supplier" {{ request('role') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                                    <option value="brand" {{ request('role') == 'brand' ? 'selected' : '' }}>Brand</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <select class="form-select form-sm" id="sort" name="sort">
                                <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>{{ __('ui.users.newest') }}</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('ui.users.oldest') }}</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('ui.users.by_name') }}</option>
                                <option value="posts" {{ request('sort') == 'posts' ? 'selected' : '' }}>{{ __('ui.users.by_posts') }}</option>
                                <option value="threads" {{ request('sort') == 'threads' ? 'selected' : '' }}>{{ __('ui.users.by_threads') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary btn-sm w-100 me-3">
                        <i class="fas fa-search"></i> {{ __('ui.users.search') }}
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-undo"></i> {{ __('ui.users.reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Display -->
    @if(request('view', 'list') === 'grid')
        <!-- Grid View -->
        <div class="row">
            @forelse($users as $user)
                <div class="col-md-4 col-sm-3 col-lg-3 mb-4">
                    <div class="grid_user_item h-100">
                        <div class="text-center">
                            <div class="position-relative d-inline-block mb-3">
                                <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle " width="80" height="80">
                                @if($user->isOnline())
                                    <span class="badge bg-success online-badge"></span>
                                @else
                                    <span class="badge bg-secondary offline-badge"></span>
                                @endif
                            </div>
                            <h5 class="grid_user_item_title">
                                <a href="{{ route('profile.show', $user->username) }}" class="text-decoration-none">
                                    {{ $user->name }}
                                </a>
                                <div class="text-muted small mb-2">{{ '@'.$user->username }}</div>
                            </h5>

                            <!-- Role Badge -->

                            @php
                                $roleInfo = match($user->role) {
                                    'super_admin', 'system_admin', 'content_admin' => ['Admin', 'bg-danger'],
                                    'content_moderator', 'marketplace_moderator', 'community_moderator' => ['Moderator', 'bg-success'],
                                    'verified_partner', 'manufacturer', 'supplier', 'brand' => ['Business', 'bg-warning'],
                                    'senior_member' => ['Senior', 'bg-info'],
                                    'student' => ['Student', 'bg-secondary'],
                                    default => ['Member', 'bg-primary']
                                };
                            @endphp
                            <span class="badge {{ $roleInfo[1] }} mb-2">{{ $roleInfo[0] }}</span>


                            @if($user->about_me)
                                <p class="">{{ Str::limit($user->about_me, 80) }}</p>
                            @endif
                            <div class="text-muted small list_user_item_meta">
                                <span><i class="fas fa-calendar"></i> {{ __('ui.users.joined') }} {{ $user->created_at->format('d/m/Y') }}</span>
                                @if($user->location)
                                    <span class="mx-1">•</span>
                                    <i class="fas fa-map-marker-alt"></i> {{ $user->location }}
                                @endif
                            </div>
                            <hr>
                            <div class="row text-center small text-muted mb-3 grid_user_item_thongke">
                                <div class="col-4">
                                    <div class="fw-bold">{{ $user->posts_count ?? 0 }}</div>
                                    <div>{{ __('ui.users.posts') }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold">{{ $user->threads_count ?? 0 }}</div>
                                    <div>{{ __('ui.users.threads') }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold followers-count" data-username="{{ $user->username }}">{{ $user->followers_count ?? 0 }}</div>
                                    <div>{{ __('ui.users.followers') }}</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                @auth
                                    @if(Auth::id() !== $user->id)
                                        <button type="button"
                                            class="btn btn-sm w-100 follow-btn {{ Auth::user()->isFollowing($user) ? 'btn-outline-secondary' : 'btn-outline-primary' }}"
                                            data-username="{{ $user->username }}"
                                            data-following="{{ Auth::user()->isFollowing($user) ? 'true' : 'false' }}"
                                            data-followers-count="{{ $user->followers_count ?? 0 }}">
                                            <i class="fas {{ Auth::user()->isFollowing($user) ? 'fa-user-minus' : 'fa-user-plus' }}"></i>
                                            <span class="follow-text">{{ Auth::user()->isFollowing($user) ? __('ui.users.unfollow') : __('ui.users.follow') }}</span>
                                        </button>
                                    @endif
                                @endauth
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5>{{ __('ui.users.no_members_found') }}</h5>
                        <p class="text-muted">{{ __('ui.users.try_different_filters') }}</p>
                    </div>
                </div>
            @endforelse
        </div>
    @else
    <!-- List View -->
    <div class="list-group list-group-flush">
        <div class="row g-3">
            @forelse($users as $user)
            <div class="col-lg-6">
                <div class="list_user_item">
                    <div class="d-flex gx-4">
                        <div class="text-center list_user_item_left">
                            <div class="flex-shrink-0 text-center mb-3">
                                <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle" width="64" height="64" >
                            </div>
                        </div>

                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1 list_user_item_name">
                                        <a href="{{ route('profile.show', $user->username) }}" class="text-decoration-none">
                                            {{ $user->name }}
                                        </a>

                                        <!-- Enhanced Role Badge -->
                                        @php
                                            $roleInfo = match($user->role) {
                                                'super_admin' => ['Super Admin', 'bg-danger'],
                                                'system_admin' => ['System Admin', 'bg-danger'],
                                                'content_admin' => ['Content Admin', 'bg-danger'],
                                                'content_moderator' => ['Content Moderator', 'bg-success'],
                                                'marketplace_moderator' => ['Marketplace Moderator', 'bg-success'],
                                                'community_moderator' => ['Community Moderator', 'bg-success'],
                                                'verified_partner' => ['Verified Partner', 'bg-warning'],
                                                'manufacturer' => ['Manufacturer', 'bg-warning'],
                                                'supplier' => ['Supplier', 'bg-warning'],
                                                'brand' => ['Brand', 'bg-warning'],
                                                'senior_member' => ['Senior Member', 'bg-info'],
                                                'student' => ['Student', 'bg-secondary'],
                                                'guest' => ['Guest', 'bg-light text-dark'],
                                                default => ['Member', 'bg-primary']
                                            };
                                        @endphp
                                        <span class="badge {{ $roleInfo[1] }} ms-1">{{ $roleInfo[0] }}</span>

                                        @if($user->isOnline())
                                            <span class="badge bg-success ms-1">{{ __('ui.users.online') }}</span>
                                        @endif
                                    </h5>
                                    <div class="text-muted small list_user_item_meta">
                                        <span>{{ '@'.$user->username }}</span>
                                        <span class="mx-1">•</span>
                                        <span>{{ __('ui.users.joined') }} {{ $user->created_at->format('d/m/Y') }}</span>
                                        @if($user->location)
                                            <span class="mx-1">•</span>
                                            <i class="fas fa-map-marker-alt"></i> {{ $user->location }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($user->about_me)
                                <p class="text-muted small mt-2 mb-2">{{ Str::limit($user->about_me, 150) }}</p>
                            @endif

                            <div class="d-flex mt-2 text-muted small">
                                <div class="me-3">
                                    <i class="fas fa-comment"></i>
                                    <span>{{ $user->posts_count ?? 0 }} {{ __('ui.users.posts') }}</span>
                                </div>
                                <div class="me-3">
                                    <i class="fas fa-file-alt"></i>
                                    <span>{{ $user->threads_count ?? 0 }} {{ __('ui.users.threads') }}</span>
                                </div>
                                <div class="me-3">
                                    <i class="fas fa-users"></i>
                                    <span><span class="followers-count" data-username="{{ $user->username }}">{{ $user->followers_count ?? 0 }}</span> {{ __('ui.users.followers') }}</span>
                                </div>
                            </div>
                        </div>
                        @auth
                        @if(Auth::id() !== $user->id)
                            <button type="button"
                                class="btn btn-sm follow-btn {{ Auth::user()->isFollowing($user) ? 'btn-outline-secondary' : 'btn-outline-primary' }}"
                                data-username="{{ $user->username }}"
                                data-following="{{ Auth::user()->isFollowing($user) ? 'true' : 'false' }}"
                                data-followers-count="{{ $user->followers_count ?? 0 }}">
                                <i class="fas {{ Auth::user()->isFollowing($user) ? 'fa-user-minus' : 'fa-user-plus' }}"></i>
                                <span class="follow-text">{{ Auth::user()->isFollowing($user) ? __('ui.users.unfollow') : __('ui.users.follow') }}</span>
                            </button>
                        @endif
                    @endauth
                    </div>
                </div>
            </div>
            @empty
            <div class="list-group-item p-4 text-center">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5>{{ __('ui.users.no_members_found') }}</h5>
                <p class="text-muted mb-0">{{ __('ui.users.try_different_filters') }}</p>
            </div>
            @endforelse
        </div>
    </div>
    @endif

    <!-- Pagination -->
    @if($users->hasPages())
        <div class=" mt-4">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // AJAX Follow/Unfollow functionality
    const followButtons = document.querySelectorAll('.follow-btn');

    followButtons.forEach(button => {
        button.addEventListener('click', function() {
            const username = this.dataset.username;
            const isFollowing = this.dataset.following === 'true';
            const followersCount = parseInt(this.dataset.followersCount) || 0;

            // Disable button during request
            this.disabled = true;
            const originalText = this.querySelector('.follow-text').textContent;
            this.querySelector('.follow-text').textContent = 'Đang xử lý...';

            // Determine URL and method
            const url = isFollowing
                ? `/ajax/users/${username}/unfollow`
                : `/ajax/users/${username}/follow`;
            const method = isFollowing ? 'DELETE' : 'POST';

            // Make AJAX request
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button state
                    const newIsFollowing = data.is_following;
                    this.dataset.following = newIsFollowing ? 'true' : 'false';
                    this.dataset.followersCount = data.followers_count;

                    // Update button appearance
                    if (newIsFollowing) {
                        this.className = 'btn btn-sm follow-btn btn-outline-secondary';
                        this.querySelector('i').className = 'fas fa-user-minus';
                        this.querySelector('.follow-text').textContent = '{{ __("ui.users.unfollow") }}';
                    } else {
                        this.className = 'btn btn-sm follow-btn btn-outline-primary';
                        this.querySelector('i').className = 'fas fa-user-plus';
                        this.querySelector('.follow-text').textContent = '{{ __("ui.users.follow") }}';
                    }

                    // Update followers count in all places for this user
                    const followersElements = document.querySelectorAll(`.followers-count[data-username="${username}"]`);
                    followersElements.forEach(element => {
                        element.textContent = data.followers_count;
                    });

                    // Show success message (optional)
                    if (window.showNotification) {
                        window.showNotification(data.message, 'success');
                    }
                } else {
                    // Show error message
                    if (window.showNotification) {
                        window.showNotification(data.message || 'Có lỗi xảy ra', 'error');
                    } else {
                        alert(data.message || 'Có lỗi xảy ra');
                    }

                    // Restore original text
                    this.querySelector('.follow-text').textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);

                // Show error message
                if (window.showNotification) {
                    window.showNotification('Có lỗi xảy ra khi xử lý yêu cầu', 'error');
                } else {
                    alert('Có lỗi xảy ra khi xử lý yêu cầu');
                }

                // Restore original text
                this.querySelector('.follow-text').textContent = originalText;
            })
            .finally(() => {
                // Re-enable button
                this.disabled = false;
            });
        });
    });
});
</script>
@endpush
