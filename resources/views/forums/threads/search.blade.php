@extends('layouts.app')

@section('title', 'Advanced Forum Search')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
            <li class="breadcrumb-item active">Advanced Search</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-1">
                <i class="bx bx-search-alt text-primary me-2"></i>
                Advanced Forum Search
            </h1>
            <p class="text-muted mb-0">Find specific threads and posts with detailed filters</p>
        </div>
    </div>

    <div class="row">
        <!-- Search Form -->
        <div class="col-lg-4 mb-4">
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-filter me-2"></i>
                        Search Filters
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('forums.search') }}" method="GET" id="searchForm">
                        <!-- Keywords -->
                        <div class="mb-3">
                            <label for="q" class="form-label">Keywords</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="q" 
                                   name="q" 
                                   value="{{ request('q') }}" 
                                   placeholder="Enter search terms">
                            <div class="form-text">
                                Use quotes for exact phrases, + for required words, - to exclude
                            </div>
                        </div>

                        <!-- Search In -->
                        <div class="mb-3">
                            <label class="form-label">Search In</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="search_titles" name="search_in[]" value="titles" 
                                       {{ in_array('titles', request('search_in', ['titles', 'content'])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="search_titles">Thread Titles</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="search_content" name="search_in[]" value="content"
                                       {{ in_array('content', request('search_in', ['titles', 'content'])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="search_content">Thread Content</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="search_posts" name="search_in[]" value="posts"
                                       {{ in_array('posts', request('search_in', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="search_posts">Post Replies</label>
                            </div>
                        </div>

                        <!-- Forums -->
                        <div class="mb-3">
                            <label for="forums" class="form-label">Forums</label>
                            <select class="form-select" id="forums" name="forums[]" multiple size="4">
                                <option value="">All Forums</option>
                                @foreach($forums as $forum)
                                    <option value="{{ $forum->id }}" 
                                            {{ in_array($forum->id, request('forums', [])) ? 'selected' : '' }}>
                                        {{ $forum->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Hold Ctrl/Cmd to select multiple</div>
                        </div>

                        <!-- Categories -->
                        <div class="mb-3">
                            <label for="categories" class="form-label">Categories</label>
                            <select class="form-select" id="categories" name="categories[]" multiple size="3">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ in_array($category->id, request('categories', [])) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Author -->
                        <div class="mb-3">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="author" 
                                   name="author" 
                                   value="{{ request('author') }}" 
                                   placeholder="Username or display name">
                        </div>

                        <!-- Date Range -->
                        <div class="mb-3">
                            <label class="form-label">Date Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="date" 
                                           class="form-control form-control-sm" 
                                           name="date_from" 
                                           value="{{ request('date_from') }}"
                                           placeholder="From">
                                </div>
                                <div class="col-6">
                                    <input type="date" 
                                           class="form-control form-control-sm" 
                                           name="date_to" 
                                           value="{{ request('date_to') }}"
                                           placeholder="To">
                                </div>
                            </div>
                        </div>

                        <!-- Thread Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Thread Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Any Status</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="pinned" {{ request('status') == 'pinned' ? 'selected' : '' }}>Pinned</option>
                                <option value="solved" {{ request('status') == 'solved' ? 'selected' : '' }}>Solved</option>
                            </select>
                        </div>

                        <!-- Has Attachments -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="has_attachments" name="has_attachments" value="1"
                                       {{ request('has_attachments') ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_attachments">
                                    Has Attachments
                                </label>
                            </div>
                        </div>

                        <!-- Sort Options -->
                        <div class="mb-3">
                            <label for="sort" class="form-label">Sort By</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="relevance" {{ request('sort', 'relevance') == 'relevance' ? 'selected' : '' }}>Relevance</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="most_replies" {{ request('sort') == 'most_replies' ? 'selected' : '' }}>Most Replies</option>
                                <option value="most_views" {{ request('sort') == 'most_views' ? 'selected' : '' }}>Most Views</option>
                                <option value="last_activity" {{ request('sort') == 'last_activity' ? 'selected' : '' }}>Last Activity</option>
                            </select>
                        </div>

                        <!-- Results Per Page -->
                        <div class="mb-3">
                            <label for="per_page" class="form-label">Results Per Page</label>
                            <select class="form-select" id="per_page" name="per_page">
                                <option value="10" {{ request('per_page', '20') == '10' ? 'selected' : '' }}>10</option>
                                <option value="20" {{ request('per_page', '20') == '20' ? 'selected' : '' }}>20</option>
                                <option value="50" {{ request('per_page', '20') == '50' ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page', '20') == '100' ? 'selected' : '' }}>100</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search me-1"></i>
                                Search
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearForm()">
                                <i class="bx bx-x me-1"></i>
                                Clear Filters
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="saveSearch()">
                                <i class="bx bx-bookmark me-1"></i>
                                Save Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div class="col-lg-8">
            @if(request()->has('q') || request()->hasAny(['forums', 'categories', 'author', 'date_from', 'date_to']))
                <!-- Search Summary -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Search Results</h5>
                                <p class="text-muted mb-0">
                                    Found {{ $results->total() ?? 0 }} results
                                    @if(request('q'))
                                        for "<strong>{{ request('q') }}</strong>"
                                    @endif
                                    @if(request('author'))
                                        by author "<strong>{{ request('author') }}</strong>"
                                    @endif
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary" onclick="exportResults()">
                                    <i class="bx bx-export me-1"></i>
                                    Export
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        View
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="changeView('list')">List View</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="changeView('grid')">Grid View</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="changeView('compact')">Compact View</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                @if(isset($results) && $results->count() > 0)
                    <div id="searchResults">
                        @foreach($results as $result)
                            <div class="card mb-3 search-result-item">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="mb-2">
                                                <a href="{{ route('threads.show', $result) }}" class="text-decoration-none">
                                                    {!! $result->highlighted_title ?? $result->title !!}
                                                </a>
                                                @if($result->is_pinned)
                                                    <span class="badge bg-warning ms-2">Pinned</span>
                                                @endif
                                                @if($result->is_solved)
                                                    <span class="badge bg-success ms-2">Solved</span>
                                                @endif
                                            </h6>
                                            
                                            <p class="text-muted mb-2">
                                                {!! $result->highlighted_content ?? Str::limit(strip_tags($result->content), 150) !!}
                                            </p>
                                            
                                            <div class="d-flex align-items-center text-muted small">
                                                <img src="{{ $result->user->getAvatarUrl() }}" 
                                                     alt="{{ $result->user->name }}" 
                                                     class="rounded-circle me-2" width="20" height="20">
                                                <span class="me-3">{{ $result->user->name }}</span>
                                                <span class="me-3">
                                                    <i class="bx bx-time me-1"></i>
                                                    {{ $result->created_at->diffForHumans() }}
                                                </span>
                                                <span class="me-3">
                                                    <i class="bx bx-message me-1"></i>
                                                    {{ $result->comments_count }} replies
                                                </span>
                                                <span>
                                                    <i class="bx bx-show me-1"></i>
                                                    {{ $result->views_count }} views
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 text-md-end">
                                            <div class="mb-2">
                                                <span class="badge bg-primary">{{ $result->forum->name }}</span>
                                                @if($result->category)
                                                    <span class="badge bg-secondary">{{ $result->category->name }}</span>
                                                @endif
                                            </div>
                                            
                                            @if($result->tags->count() > 0)
                                                <div class="mb-2">
                                                    @foreach($result->tags->take(3) as $tag)
                                                        <span class="badge bg-light text-dark me-1">{{ $tag->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            
                                            <div class="text-muted small">
                                                Last activity: {{ $result->updated_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($results->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $results->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <!-- No Results -->
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bx bx-search-alt-2 display-1 text-muted"></i>
                            <h4 class="mt-3">No Results Found</h4>
                            <p class="text-muted">Try adjusting your search criteria or using different keywords.</p>
                            
                            <div class="mt-4">
                                <h6>Search Tips:</h6>
                                <ul class="list-unstyled text-muted">
                                    <li>• Use quotes for exact phrases: "mechanical design"</li>
                                    <li>• Use + for required words: +CAD +tutorial</li>
                                    <li>• Use - to exclude words: design -automotive</li>
                                    <li>• Try broader search terms</li>
                                    <li>• Check spelling and try synonyms</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- Search Instructions -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-search display-1 text-primary"></i>
                        <h3 class="mt-3">Advanced Forum Search</h3>
                        <p class="text-muted mb-4">Use the filters on the left to find specific threads and posts in the community.</p>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6><i class="bx bx-bulb text-warning me-2"></i>Search Tips</h6>
                                        <ul class="list-unstyled text-start small">
                                            <li>• Use specific keywords</li>
                                            <li>• Filter by forum or category</li>
                                            <li>• Search by author name</li>
                                            <li>• Use date ranges for recent content</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6><i class="bx bx-star text-success me-2"></i>Popular Searches</h6>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="?q=CAD" class="badge bg-primary text-decoration-none">CAD</a>
                                            <a href="?q=design" class="badge bg-primary text-decoration-none">Design</a>
                                            <a href="?q=tutorial" class="badge bg-primary text-decoration-none">Tutorial</a>
                                            <a href="?q=mechanical" class="badge bg-primary text-decoration-none">Mechanical</a>
                                            <a href="?q=engineering" class="badge bg-primary text-decoration-none">Engineering</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.search-result-item {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.search-result-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.sticky-top {
    z-index: 1020;
}

.form-check-label {
    cursor: pointer;
}

.badge {
    font-size: 0.75em;
}

@media (max-width: 768px) {
    .sticky-top {
        position: relative !important;
        top: auto !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
function clearForm() {
    document.getElementById('searchForm').reset();
    window.location.href = '{{ route("forums.search") }}';
}

function saveSearch() {
    const formData = new FormData(document.getElementById('searchForm'));
    const searchParams = new URLSearchParams(formData);
    
    const searchName = prompt('Enter a name for this saved search:');
    if (searchName) {
        // Save to localStorage for now (in real app, save to server)
        const savedSearches = JSON.parse(localStorage.getItem('savedSearches') || '[]');
        savedSearches.push({
            name: searchName,
            url: '?' + searchParams.toString(),
            created_at: new Date().toISOString()
        });
        localStorage.setItem('savedSearches', JSON.stringify(savedSearches));
        
        alert('Search saved successfully!');
    }
}

function exportResults() {
    const searchParams = new URLSearchParams(window.location.search);
    searchParams.set('export', 'csv');
    
    window.open('{{ route("forums.search") }}?' + searchParams.toString());
}

function changeView(viewType) {
    const results = document.getElementById('searchResults');
    if (!results) return;
    
    results.className = `search-results-${viewType}`;
    localStorage.setItem('searchViewType', viewType);
}

// Restore saved view type
document.addEventListener('DOMContentLoaded', function() {
    const savedViewType = localStorage.getItem('searchViewType') || 'list';
    changeView(savedViewType);
});

// Auto-complete for author field
document.getElementById('author').addEventListener('input', function() {
    const query = this.value;
    if (query.length >= 2) {
        // Implement autocomplete (AJAX call to get user suggestions)
        // For now, just a placeholder
    }
});
</script>
@endpush
