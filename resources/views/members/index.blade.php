@extends('layouts.app')

@section('title', t_common("members.list_title"))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/members.css') }}">
@endpush

@section('content')

<div class="body_page">
    <div class="mb-4">
        <h1 class="h2 mb-1 title_page">{{ t_common("members.list_title") }}</h1>
        <p class="mb-0 opacity-90">{{ t_common("members.list_description") }}</p>
    </div>
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('members.index') }}" method="GET" class="d-flex">
                        <input type="text" name="filter" class="form-control me-2 input_search" placeholder="{{ t_common("members.search_placeholder") }}" value="{{ $filter }}">
                        <button type="submit" class="btn btn-primary btn-search">{{ t_common("members.search") }}</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end mt-3 mt-md-0">
                        <div class="btn-group btn_group_search" role="group">
                            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary {{ !request('view') ? 'active' : '' }}">
                                <i class="fas fa-list"></i> {{ t_common("members.list_view") }}
                            </a>
                            <a href="{{ route('members.index') }}?view=grid" class="btn btn-outline-secondary {{ request('view') == 'grid' ? 'active' : '' }}">
                                <i class="fa-solid fa-grip"></i> {{ t_common("members.grid_view") }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm rounded-3">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('members.index') && !request('filter') ? 'active' : '' }}" href="{{ route('members.index') }}">
                        {{ t_common("members.all_members") }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('members.online') ? 'active' : '' }}" href="{{ route('members.online') }}">
                        {{ t_common("members.online_now") }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('members.staff') ? 'active' : '' }}" href="{{ route('members.staff') }}">
                        {{ t_common("members.staff") }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="text-muted">{{ t_common("members.total") }}: {{ $members->total() }} {{ t_common("members.members_count") }}</span>
                    @if($filter)
                        <span class="ms-2">{{ t_common("members.filtered_by") }}: <strong>{{ $filter }}</strong></span>
                    @endif
                </div>
                <div class="dropdown dropdown_sort">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ t_common("members.sort_by") }}:
                        @switch($sort)
                            @case('name')
                                {{ t_common("members.name") }}
                                @break
                            @case('posts')
                                {{ t_common("members.posts") }}
                                @break
                            @case('threads')
                                {{ t_common("members.threads") }}
                                @break
                            @default
                                {{ t_common("members.join_date") }}
                        @endswitch
                        ({{ $direction == 'desc' ? t_common("members.descending") : t_common("members.ascending") }})
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="sortDropdown">
                        <li><h6 class="dropdown-header">{{ t_common("members.sort_by") }}</h6></li>
                        <li><a class="dropdown-item {{ $sort == 'joined' ? 'active' : '' }}" href="{{ route('members.index', ['sort' => 'joined', 'direction' => $direction, 'filter' => $filter]) }}">{{ t_common("members.join_date") }}</a></li>
                        <li><a class="dropdown-item {{ $sort == 'name' ? 'active' : '' }}" href="{{ route('members.index', ['sort' => 'name', 'direction' => $direction, 'filter' => $filter]) }}">{{ t_common("members.name") }}</a></li>
                        <li><a class="dropdown-item {{ $sort == 'posts' ? 'active' : '' }}" href="{{ route('members.index', ['sort' => 'posts', 'direction' => $direction, 'filter' => $filter]) }}">{{ t_common("members.posts") }}</a></li>
                        <li><a class="dropdown-item {{ $sort == 'threads' ? 'active' : '' }}" href="{{ route('members.index', ['sort' => 'threads', 'direction' => $direction, 'filter' => $filter]) }}">{{ t_common("members.threads") }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">{{ t_common("members.direction") }}</h6></li>
                        <li><a class="dropdown-item {{ $direction == 'desc' ? 'active' : '' }}" href="{{ route('members.index', ['sort' => $sort, 'direction' => 'desc', 'filter' => $filter]) }}">{{ t_common("members.descending") }}</a></li>
                        <li><a class="dropdown-item {{ $direction == 'asc' ? 'active' : '' }}" href="{{ route('members.index', ['sort' => $sort, 'direction' => 'asc', 'filter' => $filter]) }}">{{ t_common("members.ascending") }}</a></li>
                    </ul>
                </div>
            </div>

            @if($members->count() > 0)
                @if(request('view') == 'grid')
                    <!-- Grid View -->
                    <div class="row g-3">
                        @foreach($members as $member)
                            <div class="col-md-3">
                                <div class="card h-100 item_member_grid">
                                    <div class="card-body text-center">
                                        <img src="{{ $member->getAvatarUrl() }}" alt="{{ $member->name }}" class="rounded-circle mb-3" width="80" height="80">
                                        <h5 class="card-title mb-1">
                                            <a href="{{ route('profile.show', $member->username) }}" class="text-decoration-none">
                                                {{ $member->name }}
                                            </a>
                                        </h5>
                                        <p class="text-muted mb-2">{{ '@' . $member->username }}</p>

                                        @if($member->isOnline())
                                            <span class="badge bg-success">{{ t_common("members.online") }}</span>
                                        @endif

                                        @if($member->role == 'admin')
                                            <span class="badge bg-danger">{{ t_common("members.admin") }}</span>
                                        @elseif($member->role == 'moderator')
                                            <span class="badge bg-primary">{{ t_common("members.moderator") }}</span>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="fw-bold">{{ $member->posts_count ?? 0 }}</div>
                                                <div class="small text-muted">{{ t_common("members.posts") }}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold">{{ $member->threads_count ?? 0 }}</div>
                                                <div class="small text-muted">{{ t_common("members.threads") }}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold">{{ $member->created_at->format('M Y') }}</div>
                                                <div class="small text-muted">{{ t_common("members.joined") }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- List View -->
                    <div class="list-group list-group-flush">
                        @foreach($members as $member)
                            <div class="list-group-item item_member_list py-3 px-0">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $member->getAvatarUrl() }}" alt="{{ $member->name }}" class="rounded-circle me-3" width="50" height="50">
                                            <div>
                                                <h5 class="mb-1">
                                                    <a href="{{ route('profile.show', $member->username) }}" class="text-decoration-none">
                                                        {{ $member->name }}
                                                    </a>

                                                    @if($member->isOnline())
                                                        <span class="badge bg-success ms-2">{{ t_common("members.online") }}</span>
                                                    @endif

                                                    @if($member->role == 'admin')
                                                        <span class="badge bg-danger ms-1">{{ t_common("members.admin") }}</span>
                                                    @elseif($member->role == 'moderator')
                                                        <span class="badge bg-primary ms-1">{{ t_common("members.moderator") }}</span>
                                                    @endif
                                                </h5>
                                                <p class="mb-0 text-muted small">{{ '@' . $member->username }} · {{ t_common("members.joined") }} {{ $member->created_at->format('M Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row text-md-end">
                                            <div class="col-4">
                                                <div class="fw-bold">{{ $member->posts_count ?? 0 }}</div>
                                                <div class="small text-muted">{{ t_common("members.posts") }}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold">{{ $member->threads_count ?? 0 }}</div>
                                                <div class="small text-muted">{{ t_common("members.threads") }}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold">{{ $member->followers_count ?? 0 }}</div>
                                                <div class="small text-muted">{{ t_common("members.followers") }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="d-flex list_post_threads_footer justify-content-center mt-4">
                    {{ $members->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fs-1 text-muted mb-3"></i>
                    <p class="mb-0">{{ t_common("members.no_members_found") }}</p>
                    @if($filter)
                        <p class="text-muted">{{ t_common("members.try_different_search") }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
