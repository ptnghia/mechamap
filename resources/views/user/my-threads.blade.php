@extends('layouts.user-dashboard')

@section('title', __('nav.user.my_threads'))

@php
    $pageTitle = __('nav.user.my_threads');
    $pageDescription = __('messages.my_threads_desc');
    $pageActions = '<a href="' . route('threads.create') . '" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>' . __('threads.create_new') . '
    </a>';
    $breadcrumbs = [
        ['title' => __('nav.user.my_threads'), 'url' => '#']
    ];
@endphp

@section('dashboard-content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card primary">
            <div class="stats-icon">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stats-value" data-stat="total_threads">{{ $stats['total_threads'] ?? 0 }}</div>
            <div class="stats-label">{{ __('messages.total_threads') }}</div>
            @if(isset($stats['threads_change']))
                <div class="stats-change {{ $stats['threads_change'] >= 0 ? 'positive' : 'negative' }}">
                    {{ $stats['threads_change'] >= 0 ? '+' : '' }}{{ $stats['threads_change'] }}%
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-icon">
                <i class="fas fa-eye"></i>
            </div>
            <div class="stats-value" data-stat="total_views">{{ $stats['total_views'] ?? 0 }}</div>
            <div class="stats-label">{{ __('messages.total_views') }}</div>
            @if(isset($stats['views_change']))
                <div class="stats-change {{ $stats['views_change'] >= 0 ? 'positive' : 'negative' }}">
                    {{ $stats['views_change'] >= 0 ? '+' : '' }}{{ $stats['views_change'] }}%
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stats-value" data-stat="avg_rating">{{ number_format($stats['avg_rating'] ?? 0, 1) }}</div>
            <div class="stats-label">{{ __('messages.average_rating') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="stats-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-value" data-stat="pending_threads">{{ $stats['pending_threads'] ?? 0 }}</div>
            <div class="stats-label">{{ __('messages.pending_approval') }}</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="dashboard-filters">
    <form method="GET" action="{{ route('user.my-threads') }}">
        <div class="row">
            <div class="col-md-3">
                <div class="filter-group">
                    <label class="filter-label">{{ __('messages.status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('messages.all_statuses') }}</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>
                            {{ __('messages.published') }}
                        </option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                            {{ __('messages.pending') }}
                        </option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>
                            {{ __('messages.draft') }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group">
                    <label class="filter-label">{{ __('messages.forum') }}</label>
                    <select name="forum" class="form-select">
                        <option value="">{{ __('messages.all_forums') }}</option>
                        @foreach($forums ?? [] as $forum)
                            <option value="{{ $forum->id }}" {{ request('forum') == $forum->id ? 'selected' : '' }}>
                                {{ $forum->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group">
                    <label class="filter-label">{{ __('messages.sort_by') }}</label>
                    <select name="sort" class="form-select">
                        <option value="created_at" {{ request('sort', 'created_at') === 'created_at' ? 'selected' : '' }}>
                            {{ __('messages.newest_first') }}
                        </option>
                        <option value="updated_at" {{ request('sort') === 'updated_at' ? 'selected' : '' }}>
                            {{ __('messages.recently_updated') }}
                        </option>
                        <option value="views" {{ request('sort') === 'views' ? 'selected' : '' }}>
                            {{ __('messages.most_viewed') }}
                        </option>
                        <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>
                            {{ __('messages.highest_rated') }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group">
                    <label class="filter-label">Tìm kiếm</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="{{ __('messages.search_threads') }}"
                           value="{{ request('search') }}">
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Threads Table -->
<div class="dashboard-table">
    @if($threads && $threads->count() > 0)
        <table class="table table-hover">
            <thead>
                <tr>
                    <th data-sortable>{{ __('messages.title') }}</th>
                    <th data-sortable>{{ __('messages.forum') }}</th>
                    <th data-sortable>{{ __('messages.status') }}</th>
                    <th data-sortable>{{ __('messages.views') }}</th>
                    <th data-sortable>{{ __('messages.replies') }}</th>
                    <th data-sortable>{{ __('messages.rating') }}</th>
                    <th data-sortable>{{ __('messages.created_at') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($threads as $thread)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($thread->featured_image)
                                    <img src="{{ $thread->featured_image }}" alt="" class="me-2 rounded"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                @endif
                                <div>
                                    <a href="{{ route('threads.show', $thread->slug) }}" class="fw-bold text-decoration-none">
                                        {{ $thread->title }}
                                    </a>
                                    @if($thread->tags->count() > 0)
                                        <div class="mt-1">
                                            @foreach($thread->tags->take(3) as $tag)
                                                <span class="badge bg-secondary me-1">{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('forums.show', $thread->forum->slug) }}" class="text-decoration-none">
                                {{ $thread->forum->name }}
                            </a>
                        </td>
                        <td>
                            @if($thread->status === 'published')
                                <span class="badge bg-success">{{ __('messages.published') }}</span>
                            @elseif($thread->status === 'pending')
                                <span class="badge bg-warning">{{ __('messages.pending') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('messages.draft') }}</span>
                            @endif
                        </td>
                        <td>{{ number_format($thread->views_count ?? 0) }}</td>
                        <td>{{ $thread->comments_count ?? 0 }}</td>
                        <td>
                            @if($thread->average_rating)
                                <div class="d-flex align-items-center">
                                    <span class="me-1">{{ number_format($thread->average_rating, 1) }}</span>
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $thread->average_rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">{{ __('messages.no_rating') }}</span>
                            @endif
                        </td>
                        <td>{{ $thread->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('threads.show', $thread->slug) }}"
                                   class="btn btn-outline-primary" data-bs-toggle="tooltip"
                                   title="{{ __('messages.view') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('threads.edit', $thread->id) }}"
                                   class="btn btn-outline-secondary" data-bs-toggle="tooltip"
                                   title="{{ __('messages.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger"
                                        onclick="deleteThread({{ $thread->id }})" data-bs-toggle="tooltip"
                                        title="{{ __('messages.delete') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        @if($threads->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $threads->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-comments"></i>
            </div>
            <div class="empty-state-title">{{ __('messages.no_threads_yet') }}</div>
            <div class="empty-state-description">{{ __('messages.no_threads_desc') }}</div>
            <a href="{{ route('threads.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>{{ __('threads.create_first') }}
            </a>
        </div>
    @endif
</div>

<script>
function deleteThread(threadId) {
    if (confirm('{{ __("core/messages.confirm_delete_thread") }}')) {
        fetch(`/threads/${threadId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || '{{ __("core/messages.error_occurred") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("core/messages.error_occurred") }}');
        });
    }
}
</script>
@endsection
