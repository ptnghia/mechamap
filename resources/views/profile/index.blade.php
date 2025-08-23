@extends('layouts.app')

@section('title', __('ui.users.page_title'))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Header with tabs -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">{{ __('ui.users.page_title') }}</h1>
                <div class="d-flex align-items-center gap-3">
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
                    <span class="text-muted">{{ $users->total() }} {{ __('ui.users.member_count') }}</span>
                </div>
            </div>

            <!-- Filter tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link {{ request('filter', 'all') === 'all' ? 'active' : '' }}"
                       href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}">
                        {{ __('ui.users.all_members') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('filter') === 'online' ? 'active' : '' }}"
                       href="{{ request()->fullUrlWithQuery(['filter' => 'online']) }}">
                        {{ __('ui.users.online_members') }}
                        <span class="badge bg-success ms-1">{{ $stats['online_members'] }}</span>
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
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('users.index') }}" method="GET" class="row g-3">
                        <!-- Preserve current filter and view -->
                        <input type="hidden" name="filter" value="{{ request('filter', 'all') }}">
                        <input type="hidden" name="view" value="{{ request('view', 'list') }}">

                        <div class="col-md-4">
                            <label for="search" class="form-label">{{ __('ui.users.search_profiles') }}</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Tên, username hoặc email...">
                        </div>

                        <div class="col-md-4">
                            <label for="role" class="form-label">{{ __('ui.users.filter_by_role') }}</label>
                            <select class="form-select" id="role" name="role">
                                <option value="">{{ __('ui.users.all_roles') }}</option>

                                <!-- System Management Group -->
                                <optgroup label="{{ __('ui.users.system_management_group') }}">
                                    <option value="system_management" {{ request('role') == 'system_management' ? 'selected' : '' }}>{{ __('ui.users.all_admin') }}</option>
                                    <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="system_admin" {{ request('role') == 'system_admin' ? 'selected' : '' }}>System Admin</option>
                                    <option value="content_admin" {{ request('role') == 'content_admin' ? 'selected' : '' }}>Content Admin</option>
                                </optgroup>

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
                            <label for="sort" class="form-label">{{ __('ui.users.sort_by') }}</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>{{ __('ui.users.newest') }}</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('ui.users.oldest') }}</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('ui.users.by_name') }}</option>
                                <option value="posts" {{ request('sort') == 'posts' ? 'selected' : '' }}>{{ __('ui.users.by_posts') }}</option>
                                <option value="threads" {{ request('sort') == 'threads' ? 'selected' : '' }}>{{ __('ui.users.by_threads') }}</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> {{ __('ui.users.search') }}
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
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
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}"
                                         class="rounded-circle mb-3" width="80" height="80"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-primary text-white fw-bold"
                                         style="width: 80px; height: 80px; font-size: 32px; display: none;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>

                                    <h5 class="card-title mb-1">
                                        <a href="{{ route('profile.show', $user->username) }}" class="text-decoration-none">
                                            {{ $user->name }}
                                        </a>
                                    </h5>

                                    <div class="text-muted small mb-2">{{ $user->username }}</div>

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
                                        <p class="text-muted small">{{ Str::limit($user->about_me, 80) }}</p>
                                    @endif

                                    <div class="row text-center small text-muted mb-3">
                                        <div class="col-4">
                                            <div class="fw-bold">{{ $user->posts_count ?? 0 }}</div>
                                            <div>{{ __('ui.users.posts') }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold">{{ $user->threads_count ?? 0 }}</div>
                                            <div>{{ __('ui.users.threads') }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold">{{ $user->followers_count ?? 0 }}</div>
                                            <div>{{ __('ui.users.followers') }}</div>
                                        </div>
                                    </div>

                                    @auth
                                        @if(Auth::id() !== $user->id)
                                            @if(Auth::user()->isFollowing($user))
                                                <form action="{{ route('profile.unfollow', $user->username) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-user-minus"></i> {{ __('ui.users.unfollow') }}
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('profile.follow', $user->username) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-user-plus"></i> {{ __('ui.users.follow') }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    @endauth
                                </div>
                                <div class="card-footer text-muted small text-center">
                                    {{ __('ui.users.joined') }} {{ $user->created_at->format('d/m/Y') }}
                                    @if($user->isOnline())
                                        <span class="badge bg-success ms-1">{{ __('ui.users.online') }}</span>
                                    @endif
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
                <div class="card">
                    <div class="list-group list-group-flush">
                        @forelse($users as $user)
                            <div class="list-group-item p-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}"
                                             class="rounded-circle" width="64" height="64"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold"
                                             style="width: 64px; height: 64px; font-size: 24px; display: none;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="mb-1">
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
                                                <div class="text-muted small">
                                                    <span>{{ $user->username }}</span>
                                                    <span class="mx-1">•</span>
                                                    <span>{{ __('ui.users.joined') }} {{ $user->created_at->format('d/m/Y') }}</span>
                                                    @if($user->location)
                                                        <span class="mx-1">•</span>
                                                        <i class="fas fa-map-marker-alt"></i> {{ $user->location }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                @auth
                                                    @if(Auth::id() !== $user->id)
                                                        @if(Auth::user()->isFollowing($user))
                                                            <form action="{{ route('profile.unfollow', $user->username) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                                    <i class="fas fa-user-minus"></i> {{ __('ui.users.unfollow') }}
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('profile.follow', $user->username) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-user-plus"></i> {{ __('ui.users.follow') }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                @endauth
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
                                                <span>{{ $user->followers_count ?? 0 }} {{ __('ui.users.followers') }}</span>
                                            </div>
                                        </div>
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
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <div class="col-lg-4 mt-4 mt-lg-0">
            <!-- Community Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> {{ __('ui.users.community_stats') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span><i class="fas fa-users text-primary"></i> {{ __('ui.users.total_members') }}</span>
                        <span class="fw-bold text-primary">{{ number_format($stats['total_members']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span><i class="fas fa-user-plus text-success"></i> {{ __('ui.users.newest_member') }}</span>
                        <span class="fw-bold">
                            @if($stats['newest_member'])
                                <a href="{{ route('profile.show', $stats['newest_member']->username) }}"
                                   class="text-decoration-none">{{ $stats['newest_member']->name }}</a>
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-0">
                        <span><i class="fas fa-circle text-success"></i> {{ __('ui.users.online_count') }}</span>
                        <span class="fw-bold text-success">{{ number_format($stats['online_members']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Top Contributors This Month -->
            @if($topContributors->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-trophy text-warning"></i> {{ __('ui.users.top_contributors') }}</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($topContributors as $index => $user)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        @if($index === 0)
                                            <i class="fas fa-crown text-warning"></i>
                                        @elseif($index === 1)
                                            <i class="fas fa-medal text-secondary"></i>
                                        @elseif($index === 2)
                                            <i class="fas fa-award text-warning"></i>
                                        @else
                                            <span class="text-muted">{{ $index + 1 }}.</span>
                                        @endif
                                    </div>
                                    <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}"
                                         class="rounded-circle me-2" width="32" height="32"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="rounded-circle me-2 d-flex align-items-center justify-content-center bg-primary text-white fw-bold"
                                         style="width: 32px; height: 32px; font-size: 14px; display: none;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('profile.show', $user->username) }}"
                                           class="text-decoration-none fw-medium">{{ $user->name }}</a>
                                        <div class="small text-muted">{{ $user->username }}</div>
                                    </div>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $user->comments_count }} bài viết</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('users.leaderboard') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-chart-line"></i> {{ __('ui.users.view_leaderboard') }}
                        </a>
                    </div>
                </div>
            @endif

            <!-- Staff Members -->
            @if($staffMembers->isNotEmpty())
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shield-alt text-primary"></i> {{ __('ui.users.staff_members') }}</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($staffMembers as $user)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}"
                                         class="rounded-circle me-2" width="32" height="32"
                                         onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($user->name, 0, 1))]) }}'">
                                    <div>
                                        <a href="{{ route('profile.show', $user->username) }}"
                                           class="text-decoration-none fw-medium">{{ $user->name }}</a>
                                        <div class="small">
                                            @php
                                                $roleInfo = match($user->role) {
                                                    'super_admin' => ['Super Admin', 'text-danger'],
                                                    'system_admin' => ['System Admin', 'text-danger'],
                                                    'content_admin' => ['Content Admin', 'text-danger'],
                                                    'content_moderator' => ['Content Moderator', 'text-success'],
                                                    'marketplace_moderator' => ['Marketplace Moderator', 'text-success'],
                                                    'community_moderator' => ['Community Moderator', 'text-success'],
                                                    default => ['Staff', 'text-primary']
                                                };
                                            @endphp
                                            <span class="{{ $roleInfo[1] }}">{{ $roleInfo[0] }}</span>
                                        </div>
                                    </div>
                                </div>
                                @if($user->isOnline())
                                    <span class="badge bg-success">{{ __('ui.users.online') }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('users.staff') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-users-cog"></i> {{ __('ui.users.view_all') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
