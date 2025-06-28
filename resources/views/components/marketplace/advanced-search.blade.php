@props(['categories' => [], 'currentFilters' => []])

<div class="card mb-4" id="advancedSearchPanel" style="display: none;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Advanced Search</h5>
        <button type="button" class="btn-close" onclick="toggleAdvancedSearch()" aria-label="Close"></button>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('marketplace.products.index') }}" id="advancedSearchForm">
            <div class="row g-3">
                <!-- Keyword Search -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Keywords</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search products, descriptions, specifications...">
                    </div>
                    <div class="form-text">Use quotes for exact phrases, + for required words, - to exclude words</div>
                </div>

                <!-- Category -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Product Type -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Product Type</label>
                    <select name="product_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="physical" {{ request('product_type') == 'physical' ? 'selected' : '' }}>Physical Products</option>
                        <option value="digital" {{ request('product_type') == 'digital' ? 'selected' : '' }}>Digital Products</option>
                        <option value="service" {{ request('product_type') == 'service' ? 'selected' : '' }}>Services</option>
                    </select>
                </div>

                <!-- Seller Type -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Seller Type</label>
                    <select name="seller_type" class="form-select">
                        <option value="">All Sellers</option>
                        <option value="supplier" {{ request('seller_type') == 'supplier' ? 'selected' : '' }}>Suppliers</option>
                        <option value="manufacturer" {{ request('seller_type') == 'manufacturer' ? 'selected' : '' }}>Manufacturers</option>
                        <option value="brand" {{ request('seller_type') == 'brand' ? 'selected' : '' }}>Brands</option>
                    </select>
                </div>

                <!-- Price Range -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Price Range (USD)</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="number"
                                   name="min_price"
                                   value="{{ request('min_price') }}"
                                   class="form-control"
                                   placeholder="Min price"
                                   min="0"
                                   step="0.01">
                        </div>
                        <div class="col-6">
                            <input type="number"
                                   name="max_price"
                                   value="{{ request('max_price') }}"
                                   class="form-control"
                                   placeholder="Max price"
                                   min="0"
                                   step="0.01">
                        </div>
                    </div>
                </div>

                <!-- Material (for physical products) -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Material</label>
                    <select name="material" class="form-select">
                        <option value="">Any Material</option>
                        <option value="Steel" {{ request('material') == 'Steel' ? 'selected' : '' }}>Steel</option>
                        <option value="Aluminum" {{ request('material') == 'Aluminum' ? 'selected' : '' }}>Aluminum</option>
                        <option value="Stainless Steel" {{ request('material') == 'Stainless Steel' ? 'selected' : '' }}>Stainless Steel</option>
                        <option value="Titanium" {{ request('material') == 'Titanium' ? 'selected' : '' }}>Titanium</option>
                    </select>
                </div>

                <!-- File Format (for digital products) -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">File Format</label>
                    <select name="file_format" class="form-select">
                        <option value="">Any Format</option>
                        <option value="STEP" {{ request('file_format') == 'STEP' ? 'selected' : '' }}>STEP</option>
                        <option value="IGES" {{ request('file_format') == 'IGES' ? 'selected' : '' }}>IGES</option>
                        <option value="DWG" {{ request('file_format') == 'DWG' ? 'selected' : '' }}>DWG</option>
                        <option value="PDF" {{ request('file_format') == 'PDF' ? 'selected' : '' }}>PDF</option>
                    </select>
                </div>

                <!-- Rating -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Minimum Rating</label>
                    <select name="min_rating" class="form-select">
                        <option value="">Any Rating</option>
                        <option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                        <option value="3" {{ request('min_rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                        <option value="2" {{ request('min_rating') == '2' ? 'selected' : '' }}>2+ Stars</option>
                    </select>
                </div>

                <!-- Availability -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Availability</label>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check">
                            <input type="checkbox"
                                   name="in_stock"
                                   value="1"
                                   id="inStockAdvanced"
                                   class="form-check-input"
                                   {{ request('in_stock') ? 'checked' : '' }}>
                            <label for="inStockAdvanced" class="form-check-label">In Stock Only</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox"
                                   name="featured"
                                   value="1"
                                   id="featuredAdvanced"
                                   class="form-check-input"
                                   {{ request('featured') ? 'checked' : '' }}>
                            <label for="featuredAdvanced" class="form-check-label">Featured Only</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox"
                                   name="on_sale"
                                   value="1"
                                   id="onSaleAdvanced"
                                   class="form-check-input"
                                   {{ request('on_sale') ? 'checked' : '' }}>
                            <label for="onSaleAdvanced" class="form-check-label">On Sale</label>
                        </div>
                    </div>
                </div>

                <!-- Sort Options -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Sort Results By</label>
                    <div class="row g-2">
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="radio" name="sort" value="relevance" class="form-check-input" id="sortRelevance" {{ request('sort', 'relevance') == 'relevance' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sortRelevance">Relevance</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="radio" name="sort" value="created_at" class="form-check-input" id="sortLatest" {{ request('sort') == 'created_at' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sortLatest">Latest</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="radio" name="sort" value="price_low" class="form-check-input" id="sortPriceLow" {{ request('sort') == 'price_low' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sortPriceLow">Price: Low to High</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="radio" name="sort" value="price_high" class="form-check-input" id="sortPriceHigh" {{ request('sort') == 'price_high' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sortPriceHigh">Price: High to Low</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="radio" name="sort" value="rating" class="form-check-input" id="sortRating" {{ request('sort') == 'rating' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sortRating">Highest Rated</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="radio" name="sort" value="popular" class="form-check-input" id="sortPopular" {{ request('sort') == 'popular' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sortPopular">Most Popular</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="radio" name="sort" value="name" class="form-check-input" id="sortName" {{ request('sort') == 'name' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sortName">Name A-Z</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-2"></i>
                        Search Products
                    </button>
                    <a href="{{ route('marketplace.products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise me-2"></i>
                        Clear All
                    </a>
                </div>
                <div class="text-muted small">
                    <span id="filterCount">{{ count(array_filter(request()->all())) }}</span> filters applied
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAdvancedSearch() {
    const panel = document.getElementById('advancedSearchPanel');
    const button = document.getElementById('advancedSearchToggle');

    if (panel.style.display === 'none') {
        panel.style.display = 'block';
        if (button) button.innerHTML = '<i class="bi bi-search mr-2"></i> Hide Advanced Search';
    } else {
        panel.style.display = 'none';
        if (button) button.innerHTML = '<i class="bi bi-search mr-2"></i> Advanced Search';
    }
}

// Update filter count
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('advancedSearchForm');
    if (form) {
        form.addEventListener('change', function() {
            const formData = new FormData(form);
            let count = 0;
            for (let [key, value] of formData.entries()) {
                if (value && value.trim() !== '') {
                    count++;
                }
            }
            document.getElementById('filterCount').textContent = count;
        });
    }
});
</script>
