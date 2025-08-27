@extends('dashboard.layouts.app')

@section('title', __('showcase.my_showcases'))

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .page-header h3 {
        color: white;
        margin-bottom: 0.5rem;
    }

    .page-header .text-muted {
        color: rgba(255, 255, 255, 0.8) !important;
    }

    .showcase-card {
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .showcase-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .showcase-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #f8f9fa;
    }

    .showcase-status {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .status-approved {
        background: #d1edff;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .status-featured {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .showcase-stats {
        display: flex;
        gap: 1rem;
        font-size: 0.875rem;
        color: #6c757d;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .filter-section {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e9ecef;
    }

    .stats-overview {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }

    .stat-card {
        text-align: center;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 0.375rem;
        backdrop-filter: blur(10px);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .showcase-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('dashboard-content')
<div class="showcases-dashboard">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="mb-1">
                    <i class="fas fa-trophy me-2 text-primary"></i>
                    {{ __('showcase.my_showcases') }}
                </h3>
                <p class="mb-0 text-muted">{{ __('showcase.manage_description') }}</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('showcase.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    {{ __('showcase.create.title') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-overview">
        <div class="row align-items-center">
            <div class="col-md-12">
                    <i class="fas fa-plus me-2"></i>{{ __('showcase.create_new') }}
                </a>
            </div>
        </div>

        <div class="stats-grid mt-4">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total'] }}</div>
                <div class="stat-label">{{ __('showcase.stats.total') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['published'] }}</div>
                <div class="stat-label">{{ __('showcase.stats.published') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['pending'] }}</div>
                <div class="stat-label">{{ __('showcase.stats.pending') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['featured'] }}</div>
                <div class="stat-label">{{ __('showcase.stats.featured') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_views'] }}</div>
                <div class="stat-label">{{ __('showcase.stats.total_views') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['average_rating'] }}</div>
                <div class="stat-label">{{ __('showcase.stats.avg_rating') }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('dashboard.community.showcases.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">{{ __('showcase.filter.status') }}</label>
                <select name="status" id="status" class="form-select">
                    <option value="">{{ __('showcase.filter.all_status') }}</option>
                    <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>{{ __('showcase.status.pending') }}</option>
                    <option value="approved" {{ $currentStatus === 'approved' ? 'selected' : '' }}>{{ __('showcase.status.approved') }}</option>
                    <option value="featured" {{ $currentStatus === 'featured' ? 'selected' : '' }}>{{ __('showcase.status.featured') }}</option>
                    <option value="rejected" {{ $currentStatus === 'rejected' ? 'selected' : '' }}>{{ __('showcase.status.rejected') }}</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="category" class="form-label">{{ __('showcase.filter.category') }}</label>
                <select name="category" id="category" class="form-select">
                    <option value="">{{ __('showcase.filter.all_categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $currentCategory == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="search" class="form-label">{{ __('showcase.filter.search') }}</label>
                <input type="text" name="search" id="search" class="form-control"
                       value="{{ $search }}" placeholder="{{ __('showcase.filter.search_placeholder') }}">
            </div>

            <div class="col-md-3">
                <label for="sort" class="form-label">{{ __('showcase.filter.sort') }}</label>
                <select name="sort" id="sort" class="form-select">
                    <option value="newest" {{ $currentSort === 'newest' ? 'selected' : '' }}>{{ __('showcase.sort.newest') }}</option>
                    <option value="oldest" {{ $currentSort === 'oldest' ? 'selected' : '' }}>{{ __('showcase.sort.oldest') }}</option>
                    <option value="most_viewed" {{ $currentSort === 'most_viewed' ? 'selected' : '' }}>{{ __('showcase.sort.most_viewed') }}</option>
                    <option value="highest_rated" {{ $currentSort === 'highest_rated' ? 'selected' : '' }}>{{ __('showcase.sort.highest_rated') }}</option>
                </select>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-2"></i>{{ __('common.filter') }}
                </button>
                <a href="{{ route('dashboard.community.showcases.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>{{ __('common.clear') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Showcases Grid -->
    @if($showcases->count() > 0)
        <div class="row">
            @foreach($showcases as $showcase)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card showcase-card h-100">
                        <div class="position-relative">
                            @if($showcase->cover_image)
                                <img src="{{ asset('storage/' . $showcase->cover_image) }}"
                                     class="showcase-image" alt="{{ $showcase->title }}"
                                     onerror="this.src='{{ asset('images/placeholder.svg') }}'">
                            @else
                                <div class="showcase-image d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif

                            <span class="showcase-status status-{{ $showcase->status }}">
                                {{ __('showcase.status.' . $showcase->status) }}
                            </span>
                        </div>

                        <div class="card-body">
                            <h6 class="card-title">{{ Str::limit($showcase->title, 50) }}</h6>
                            <p class="card-text text-muted small">
                                {{ Str::limit($showcase->description, 100) }}
                            </p>

                            @if($showcase->showcaseCategory)
                                <span class="badge bg-secondary mb-2">{{ $showcase->showcaseCategory->name }}</span>
                            @endif

                            <div class="showcase-stats">
                                <div class="stat-item">
                                    <i class="fas fa-eye"></i>
                                    <span>{{ $showcase->view_count ?? 0 }}</span>
                                </div>
                                <div class="stat-item">
                                    <i class="fas fa-star"></i>
                                    <span>{{ number_format($showcase->rating_average ?? 0, 1) }}</span>
                                </div>
                                <div class="stat-item">
                                    <i class="fas fa-heart"></i>
                                    <span>{{ $showcase->like_count ?? 0 }}</span>
                                </div>
                            </div>

                            <div class="showcase-actions">
                                <a href="{{ route('dashboard.community.showcases.show', $showcase) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>{{ __('common.view') }}
                                </a>
                                <a href="{{ route('dashboard.community.showcases.edit', $showcase) }}"
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-edit me-1"></i>{{ __('common.edit') }}
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                        onclick="confirmDelete({{ $showcase->id }})">
                                    <i class="fas fa-trash me-1"></i>{{ __('common.delete') }}
                                </button>
                            </div>
                        </div>

                        <div class="card-footer text-muted small">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $showcase->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $showcases->appends(request()->query())->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-star"></i>
            <h4>{{ __('showcase.empty.title') }}</h4>
            <p>{{ __('showcase.empty.description') }}</p>
            <a href="{{ route('showcase.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>{{ __('showcase.create_first') }}
            </a>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('showcase.delete.title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('showcase.delete.confirmation') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('common.cancel') }}
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        {{ __('common.delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(showcaseId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `{{ route('dashboard.community.showcases.index') }}/${showcaseId}`;

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Auto-submit form when filters change
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('.filter-section form');
    const selects = filterForm.querySelectorAll('select');

    selects.forEach(select => {
        select.addEventListener('change', function() {
            filterForm.submit();
        });
    });
});
</script>
@endpush
