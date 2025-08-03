@extends('layouts.app')

@section('title', 'Search Results - MechaMap Marketplace')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('marketplace.index') }}">Marketplace</a></li>
            <li class="breadcrumb-item active">Search Results</li>
        </ol>
    </nav>

    <!-- Search Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Search Results</h1>
                    <p class="text-muted mb-0">
                        Found {{ $products->total() }} products
                        @if(request('q'))
                            for "<strong>{{ request('q') }}</strong>"
                        @endif
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('marketplace.search.advanced') }}" class="btn btn-outline-primary">
                        <i class="bx bx-filter me-1"></i>
                        Advanced Search
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-export me-1"></i>
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportResults('csv')">
                                <i class="bx bx-file me-2"></i>CSV
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportResults('pdf')">
                                <i class="bx bx-file-pdf me-2"></i>PDF
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bx bx-filter me-2"></i>
                        Refine Results
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Active Filters -->
                    @if(request()->hasAny(['q', 'categories', 'price_min', 'price_max', 'materials', 'seller_types']))
                    <div class="mb-3">
                        <h6 class="small text-muted mb-2">ACTIVE FILTERS</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @if(request('q'))
                                <span class="badge bg-primary">
                                    "{{ request('q') }}"
                                    <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="text-white ms-1">×</a>
                                </span>
                            @endif
                            @foreach(request('categories', []) as $categoryId)
                                @php $category = $categories->find($categoryId) @endphp
                                @if($category)
                                <span class="badge bg-secondary">
                                    {{ $category->name }}
                                    <a href="#" class="text-white ms-1" onclick="removeFilter('categories', '{{ $categoryId }}')">×</a>
                                </span>
                                @endif
                            @endforeach
                            @if(request('price_min') || request('price_max'))
                                <span class="badge bg-info">
                                    ${{ request('price_min', '0') }} - ${{ request('price_max', '∞') }}
                                    <a href="{{ request()->fullUrlWithQuery(['price_min' => null, 'price_max' => null]) }}" class="text-white ms-1">×</a>
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('marketplace.search.results') }}" class="btn btn-sm btn-outline-secondary mt-2">
                            Clear All
                        </a>
                    </div>
                    <hr>
                    @endif

                    <!-- Quick Filters -->
                    <form action="{{ route('marketplace.search.results') }}" method="GET" id="filterForm">
                        <!-- Preserve existing search -->
                        <input type="hidden" name="q" value="{{ request('q') }}">

                        <!-- Categories -->
                        <div class="mb-3">
                            <h6 class="small text-muted mb-2">CATEGORIES</h6>
                            @foreach($categories->take(8) as $category)
                            <div class="form-check form-check-sm">
                                <input class="form-check-input" type="checkbox"
                                       id="cat_{{ $category->id }}"
                                       name="categories[]"
                                       value="{{ $category->id }}"
                                       {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                <label class="form-check-label small" for="cat_{{ $category->id }}">
                                    {{ $category->name }}
                                    <span class="text-muted">({{ $category->products_count ?? 0 }})</span>
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <!-- Price Range -->
                        <div class="mb-3">
                            <h6 class="small text-muted mb-2">PRICE RANGE</h6>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm"
                                           name="price_min" value="{{ request('price_min') }}"
                                           placeholder="Min" min="0" step="0.01">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm"
                                           name="price_max" value="{{ request('price_max') }}"
                                           placeholder="Max" min="0" step="0.01">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-primary mt-2 w-100">
                                Apply Price Filter
                            </button>
                        </div>

                        <!-- Seller Type -->
                        <div class="mb-3">
                            <h6 class="small text-muted mb-2">SELLER TYPE</h6>
                            <div class="form-check form-check-sm">
                                <input class="form-check-input" type="checkbox" id="seller_supplier"
                                       name="seller_types[]" value="supplier"
                                       {{ in_array('supplier', request('seller_types', [])) ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                <label class="form-check-label small" for="seller_supplier">
                                    <i class="bx bx-store text-success me-1"></i>Suppliers
                                </label>
                            </div>
                            <div class="form-check form-check-sm">
                                <input class="form-check-input" type="checkbox" id="seller_manufacturer"
                                       name="seller_types[]" value="manufacturer"
                                       {{ in_array('manufacturer', request('seller_types', [])) ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                <label class="form-check-label small" for="seller_manufacturer">
                                    <i class="bx bx-cube text-primary me-1"></i>Manufacturers
                                </label>
                            </div>
                        </div>

                        <!-- Rating -->
                        <div class="mb-3">
                            <h6 class="small text-muted mb-2">RATING</h6>
                            @for($i = 4; $i >= 1; $i--)
                            <div class="form-check form-check-sm">
                                <input class="form-check-input" type="radio"
                                       id="rating_{{ $i }}" name="min_rating" value="{{ $i }}"
                                       {{ request('min_rating') == $i ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                <label class="form-check-label small" for="rating_{{ $i }}">
                                    @for($j = 1; $j <= 5; $j++)
                                        <i class="bx {{ $j <= $i ? 'bxs-star text-warning' : 'bx-star text-muted' }}"></i>
                                    @endfor
                                    & up
                                </label>
                            </div>
                            @endfor
                        </div>

                        <!-- Availability -->
                        <div class="mb-3">
                            <h6 class="small text-muted mb-2">AVAILABILITY</h6>
                            <div class="form-check form-check-sm">
                                <input class="form-check-input" type="checkbox" id="in_stock"
                                       name="in_stock" value="1"
                                       {{ request('in_stock') ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                <label class="form-check-label small" for="in_stock">
                                    In Stock Only
                                </label>
                            </div>
                            <div class="form-check form-check-sm">
                                <input class="form-check-input" type="checkbox" id="on_sale"
                                       name="on_sale" value="1"
                                       {{ request('on_sale') ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                <label class="form-check-label small" for="on_sale">
                                    On Sale
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div class="col-lg-9">
            <!-- Results Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <span class="me-3">
                                    {{ __('ui.marketplace.showing_results', [
                                        'start' => $products->firstItem() ?? 0,
                                        'end' => $products->lastItem() ?? 0,
                                        'total' => $products->total()
                                    ]) }}
                                </span>
                                @if(request('q'))
                                <span class="badge bg-primary">{{ request('q') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-md-end align-items-center gap-3">
                                <!-- Sort -->
                                <div class="d-flex align-items-center">
                                    <label class="form-label small mb-0 me-2">Sort:</label>
                                    <select class="form-select form-select-sm" onchange="updateSort(this.value)" style="width: auto;">
                                        <option value="relevance" {{ request('sort', 'relevance') == 'relevance' ? 'selected' : '' }}>Relevance</option>
                                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price ↑</option>
                                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price ↓</option>
                                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating</option>
                                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular</option>
                                    </select>
                                </div>

                                <!-- View Toggle -->
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                            onclick="changeView('grid')" id="gridView">
                                        <i class="bx bx-grid-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                            onclick="changeView('list')" id="listView">
                                        <i class="bx bx-list-ul"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Search Filters Display -->
            @php
                $hasActiveFilters = request()->hasAny(['search', 'q', 'category', 'product_type', 'seller_type', 'min_price', 'max_price', 'material', 'file_format', 'min_rating', 'in_stock', 'featured', 'on_sale', 'sort']);
                $activeFilters = [];

                // Collect active filters
                if (request('search') || request('q')) {
                    $searchTerm = request('search', request('q'));
                    $activeFilters[] = [
                        'type' => 'search',
                        'label' => 'Từ khóa: "' . $searchTerm . '"',
                        'remove_url' => request()->fullUrlWithQuery(['search' => null, 'q' => null])
                    ];
                }

                if (request('category')) {
                    $category = $categories->where('slug', request('category'))->first();
                    if ($category) {
                        $activeFilters[] = [
                            'type' => 'category',
                            'label' => 'Danh mục: ' . $category->name,
                            'remove_url' => request()->fullUrlWithQuery(['category' => null])
                        ];
                    }
                }

                if (request('product_type')) {
                    $productTypes = [
                        'physical' => 'Sản phẩm vật lý',
                        'digital' => 'Sản phẩm số',
                        'service' => 'Dịch vụ'
                    ];
                    $activeFilters[] = [
                        'type' => 'product_type',
                        'label' => 'Loại sản phẩm: ' . ($productTypes[request('product_type')] ?? request('product_type')),
                        'remove_url' => request()->fullUrlWithQuery(['product_type' => null])
                    ];
                }

                if (request('seller_type')) {
                    $sellerTypes = [
                        'supplier' => 'Nhà cung cấp',
                        'manufacturer' => 'Nhà sản xuất',
                        'brand' => 'Thương hiệu'
                    ];
                    $activeFilters[] = [
                        'type' => 'seller_type',
                        'label' => 'Loại người bán: ' . ($sellerTypes[request('seller_type')] ?? request('seller_type')),
                        'remove_url' => request()->fullUrlWithQuery(['seller_type' => null])
                    ];
                }

                if (request('min_price') || request('max_price')) {
                    $priceLabel = 'Giá: ';
                    if (request('min_price') && request('max_price')) {
                        $priceLabel .= number_format(request('min_price')) . ' - ' . number_format(request('max_price')) . ' VND';
                    } elseif (request('min_price')) {
                        $priceLabel .= 'Từ ' . number_format(request('min_price')) . ' VND';
                    } else {
                        $priceLabel .= 'Đến ' . number_format(request('max_price')) . ' VND';
                    }
                    $activeFilters[] = [
                        'type' => 'price',
                        'label' => $priceLabel,
                        'remove_url' => request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null])
                    ];
                }

                if (request('material')) {
                    $activeFilters[] = [
                        'type' => 'material',
                        'label' => 'Vật liệu: ' . request('material'),
                        'remove_url' => request()->fullUrlWithQuery(['material' => null])
                    ];
                }

                if (request('file_format')) {
                    $activeFilters[] = [
                        'type' => 'file_format',
                        'label' => 'Định dạng file: ' . request('file_format'),
                        'remove_url' => request()->fullUrlWithQuery(['file_format' => null])
                    ];
                }

                if (request('min_rating')) {
                    $activeFilters[] = [
                        'type' => 'min_rating',
                        'label' => 'Đánh giá tối thiểu: ' . request('min_rating') . '+ sao',
                        'remove_url' => request()->fullUrlWithQuery(['min_rating' => null])
                    ];
                }

                // Status filters
                if (request('in_stock')) {
                    $activeFilters[] = [
                        'type' => 'in_stock',
                        'label' => 'Còn hàng',
                        'remove_url' => request()->fullUrlWithQuery(['in_stock' => null])
                    ];
                }

                if (request('featured')) {
                    $activeFilters[] = [
                        'type' => 'featured',
                        'label' => 'Sản phẩm nổi bật',
                        'remove_url' => request()->fullUrlWithQuery(['featured' => null])
                    ];
                }

                if (request('on_sale')) {
                    $activeFilters[] = [
                        'type' => 'on_sale',
                        'label' => 'Đang giảm giá',
                        'remove_url' => request()->fullUrlWithQuery(['on_sale' => null])
                    ];
                }

                if (request('sort') && request('sort') !== 'relevance') {
                    $sortOptions = [
                        'newest' => 'Mới nhất',
                        'price_low' => 'Giá thấp đến cao',
                        'price_high' => 'Giá cao đến thấp',
                        'rating' => 'Đánh giá cao nhất',
                        'popular' => 'Phổ biến nhất',
                        'name_az' => 'Tên A-Z'
                    ];
                    $activeFilters[] = [
                        'type' => 'sort',
                        'label' => 'Sắp xếp: ' . ($sortOptions[request('sort')] ?? request('sort')),
                        'remove_url' => request()->fullUrlWithQuery(['sort' => null])
                    ];
                }
            @endphp

            @if(count($activeFilters) > 0)
            <div class="card mb-4">
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="text-muted small me-2">
                            <i class="fas fa-filter me-1"></i>
                            Bộ lọc đang áp dụng:
                        </span>

                        @foreach($activeFilters as $filter)
                            <span class="badge bg-primary d-flex align-items-center">
                                {{ $filter['label'] }}
                                <a href="{{ $filter['remove_url'] }}"
                                   class="text-white ms-2 text-decoration-none"
                                   title="Xóa bộ lọc này"
                                   style="font-size: 1.1em; line-height: 1;">
                                    ×
                                </a>
                            </span>
                        @endforeach

                        <a href="{{ route('marketplace.search.results') }}"
                           class="btn btn-outline-secondary btn-sm ms-2"
                           title="Xóa tất cả bộ lọc">
                            <i class="fas fa-times me-1"></i>
                            Xóa tất cả
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Products Grid/List -->
            @if($products->count() > 0)
                <div id="productsContainer" class="products-grid">
                    @foreach($products as $product)
                        @include('marketplace.partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->appends(request()->query())->links() }}
                </div>
                @endif
            @else
                <!-- No Results -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-search-alt-2 display-1 text-muted"></i>
                        <h4 class="mt-3">No Products Found</h4>
                        <p class="text-muted">Try adjusting your search criteria or browse our categories.</p>

                        <div class="mt-4">
                            <a href="{{ route('marketplace.index') }}" class="btn btn-primary me-2">
                                Browse All Products
                            </a>
                            <a href="{{ route('marketplace.search.advanced') }}" class="btn btn-outline-primary">
                                Advanced Search
                            </a>
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
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.products-list .product-card {
    display: flex;
    margin-bottom: 1rem;
}

.products-list .product-card .card-body {
    display: flex;
    flex-direction: row;
    align-items: center;
}

.form-check-sm .form-check-input {
    margin-top: 0.1rem;
}

.form-check-sm .form-check-label {
    font-size: 0.875rem;
}

.btn-group .btn.active {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

.badge {
    font-size: 0.75em;
}

@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function updateSort(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    window.location.href = url.toString();
}

function changeView(viewType) {
    const container = document.getElementById('productsContainer');
    const gridBtn = document.getElementById('gridView');
    const listBtn = document.getElementById('listView');

    // Update container class
    container.className = `products-${viewType}`;

    // Update button states
    gridBtn.classList.toggle('active', viewType === 'grid');
    listBtn.classList.toggle('active', viewType === 'list');

    // Save preference
    localStorage.setItem('productViewType', viewType);
}

function removeFilter(filterName, value) {
    const url = new URL(window.location);
    const currentValues = url.searchParams.getAll(filterName + '[]');
    const newValues = currentValues.filter(v => v !== value);

    url.searchParams.delete(filterName + '[]');
    newValues.forEach(v => url.searchParams.append(filterName + '[]', v));

    window.location.href = url.toString();
}

function exportResults(format) {
    const url = new URL(window.location);
    url.searchParams.set('export', format);
    window.open(url.toString());
}

// Restore view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('productViewType') || 'grid';
    changeView(savedView);
});
</script>
@endpush
