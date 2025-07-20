@extends('layouts.app')

@section('title', 'Forum Categories')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Forum Categories</h1>
            <p class="text-muted mb-0">Browse discussions by category</p>
        </div>
        @can('create', App\Models\Forum::class)
        <div>
            <a href="{{ route('forums.categories.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i>
                Add Category
            </a>
        </div>
        @endcan
    </div>

    <!-- Categories Grid -->
    <div class="row">
        @forelse($categories as $category)
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card h-100 category-card">
                @if($category->image)
                <div class="card-img-top category-image" style="background-image: url('{{ $category->image }}');">
                    <div class="category-overlay">
                        <div class="category-stats">
                            <span class="badge bg-primary">{{ $category->forums_count }} Forums</span>
                            <span class="badge bg-success">{{ $category->threads_count }} Threads</span>
                        </div>
                    </div>
                </div>
                @endif

                <div class="card-body">
                    <h5 class="card-title">
                        <a href="{{ route('forums.categories.show', $category) }}" class="text-decoration-none">
                            {{ $category->name }}
                        </a>
                    </h5>

                    @if($category->description)
                    <p class="card-text text-muted">{{ Str::limit($category->description, 120) }}</p>
                    @endif

                    <!-- Category Stats -->
                    <div class="row text-center mt-3">
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number">{{ $category->forums_count }}</div>
                                <div class="stat-label">Forums</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number">{{ $category->threads_count }}</div>
                                <div class="stat-label">Threads</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="stat-number">{{ $category->posts_count }}</div>
                                <div class="stat-label">Posts</div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    @if($category->latest_thread)
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted">Latest:</small>
                        <div class="d-flex align-items-center mt-1">
                            <img src="{{ $category->latest_thread->user->getAvatarUrl() }}"
                                 alt="{{ $category->latest_thread->user->name }}"
                                 class="rounded-circle me-2" width="24" height="24">
                            <div class="flex-grow-1 min-w-0">
                                <a href="{{ route('threads.show', $category->latest_thread) }}"
                                   class="text-decoration-none small">
                                    {{ Str::limit($category->latest_thread->title, 40) }}
                                </a>
                                <div class="text-muted small">
                                    {{ $category->latest_thread->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Category Actions -->
                @can('update', $category)
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('forums.categories.edit', $category) }}"
                           class="btn btn-sm btn-outline-primary me-2">
                            <i class="bx bx-edit"></i>
                        </a>
                        @can('delete', $category)
                        <form action="{{ route('forums.categories.destroy', $category) }}"
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Are you sure?')">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
                @endcan
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bx bx-category-alt display-1 text-muted"></i>
                <h3 class="mt-3">No Categories Found</h3>
                <p class="text-muted">There are no forum categories available yet.</p>
                @can('create', App\Models\Forum::class)
                <a href="{{ route('forums.categories.create') }}" class="btn btn-primary">
                    Create First Category
                </a>
                @endcan
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.category-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid rgba(0,0,0,0.125);
}

.category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.category-image {
    height: 150px;
    background-size: cover;
    background-position: center;
    position: relative;
}

.category-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(0,0,0,0.3), rgba(0,0,0,0.1));
    display: flex;
    align-items: flex-end;
    padding: 1rem;
}

.category-stats .badge {
    margin-right: 0.5rem;
}

.stat-item {
    padding: 0.5rem 0;
}

.stat-number {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--bs-primary);
}

.stat-label {
    font-size: 0.75rem;
    color: var(--bs-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.min-w-0 {
    min-width: 0;
}

@media (max-width: 768px) {
    .category-image {
        height: 120px;
    }

    .stat-number {
        font-size: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add animation to category cards
    const cards = document.querySelectorAll('.category-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
        card.classList.add('animate__animated', 'animate__fadeInUp');
    });
});
</script>
@endpush
