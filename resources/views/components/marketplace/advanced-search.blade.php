@props(['categories' => [], 'currentFilters' => []])

<div class="card mb-4" id="advancedSearchPanel" style="display: none;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('ui.common.marketplace.advanced_search') }}</h5>
        <button type="button" class="btn-close" onclick="toggleAdvancedSearch()" aria-label="{{ __('ui.common.marketplace.close') }}"></button>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('marketplace.products.index') }}" id="advancedSearchForm">
            <div class="row g-3">
                <!-- Keyword Search -->
                <div class="col-12">
                    <label class="form-label fw-semibold">{{ __('ui.common.marketplace.keywords') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="{{ __('ui.common.marketplace.search_descriptions') }}">
                    </div>
                    <div class="form-text">{{ __('ui.common.marketplace.use_quotes_help') }}</div>
                </div>

                <!-- Category -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('ui.common.marketplace.category') }}</label>
                    <select name="category" class="form-select">
                        <option value="">{{ __('ui.common.marketplace.all_categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Product Type -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('ui.common.marketplace.product_type') }}</label>
                    <select name="product_type" class="form-select">
                        <option value="">{{ __('ui.common.marketplace.all_types') }}</option>
                        <option value="physical" {{ request('product_type') == 'physical' ? 'selected' : '' }}>{{ __('ui.common.marketplace.physical_products') }}</option>
                        <option value="digital" {{ request('product_type') == 'digital' ? 'selected' : '' }}>{{ __('ui.common.marketplace.digital_products') }}</option>
                        <option value="service" {{ request('product_type') == 'service' ? 'selected' : '' }}>{{ __('ui.common.marketplace.services') }}</option>
                    </select>
                </div>

                <!-- Seller Type -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('ui.common.marketplace.seller_type') }}</label>
                    <select name="seller_type" class="form-select">
                        <option value="">{{ __('ui.common.marketplace.all_sellers') }}</option>
                        <option value="supplier" {{ request('seller_type') == 'supplier' ? 'selected' : '' }}>{{ __('ui.common.marketplace.suppliers') }}</option>
                        <option value="manufacturer" {{ request('seller_type') == 'manufacturer' ? 'selected' : '' }}>{{ __('ui.common.marketplace.manufacturers') }}</option>
                        <option value="brand" {{ request('seller_type') == 'brand' ? 'selected' : '' }}>{{ __('ui.common.marketplace.brands') }}</option>
                    </select>
                </div>

                <!-- Price Range -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('ui.common.marketplace.price_range_usd') }}</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="number"
                                   name="min_price"
                                   value="{{ request('min_price') }}"
                                   class="form-control"
                                   placeholder="{{ __('ui.common.marketplace.min_price') }}"
                                   min="0"
                                   step="0.01">
                        </div>
                        <div class="col-6">
                            <input type="number"
                                   name="max_price"
                                   value="{{ request('max_price') }}"
                                   class="form-control"
                                   placeholder="{{ __('ui.common.marketplace.max_price') }}"
                                   min="0"
                                   step="0.01">
                        </div>
                    </div>
                </div>

                <!-- Material (for physical products) -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">{{ __('ui.common.marketplace.material') }}</label>
                    <select name="material" class="form-select">
                        <option value="">{{ __('ui.common.marketplace.any_material') }}</option>
                        <option value="Steel" {{ request('material') == 'Steel' ? 'selected' : '' }}>{{ __('ui.common.marketplace.steel') }}</option>
                        <option value="Aluminum" {{ request('material') == 'Aluminum' ? 'selected' : '' }}>{{ __('ui.common.marketplace.aluminum') }}</option>
                        <option value="Stainless Steel" {{ request('material') == 'Stainless Steel' ? 'selected' : '' }}>{{ __('ui.common.marketplace.stainless_steel') }}</option>
                        <option value="Titanium" {{ request('material') == 'Titanium' ? 'selected' : '' }}>{{ __('ui.common.marketplace.titanium') }}</option>
                    </select>
                </div>

                <!-- File Format (for digital products) -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">{{ __('ui.common.marketplace.file_format') }}</label>
                    <select name="file_format" class="form-select">
                        <option value="">{{ __('ui.common.marketplace.any_format') }}</option>
                        <option value="STEP" {{ request('file_format') == 'STEP' ? 'selected' : '' }}>STEP</option>
                        <option value="IGES" {{ request('file_format') == 'IGES' ? 'selected' : '' }}>IGES</option>
                        <option value="DWG" {{ request('file_format') == 'DWG' ? 'selected' : '' }}>DWG</option>
                        <option value="PDF" {{ request('file_format') == 'PDF' ? 'selected' : '' }}>PDF</option>
                    </select>
                </div>

                <!-- Rating -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">{{ __('ui.common.marketplace.minimum_rating') }}</label>
                    <select name="min_rating" class="form-select">
                        <option value="">{{ __('ui.common.marketplace.any_rating') }}</option>
                        <option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }}>{{ __('ui.common.marketplace.4_plus_stars') }}</option>
                        <option value="3" {{ request('min_rating') == '3' ? 'selected' : '' }}>{{ __('ui.common.marketplace.3_plus_stars') }}</option>
                        <option value="2" {{ request('min_rating') == '2' ? 'selected' : '' }}>{{ __('ui.common.marketplace.2_plus_stars') }}</option>
                    </select>
                </div>

                <!-- Availability -->
                <div class="col-12">
                    <label class="form-label fw-semibold">{{ __('ui.common.marketplace.availability') }}</label>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check">
                            <input type="checkbox"
                                   name="in_stock"
                                   value="1"
                                   id="inStockAdvanced"
                                   class="form-check-input"
                                   {{ request('in_stock') ? 'checked' : '' }}>
                            <label for="inStockAdvanced" class="form-check-label">{{ __('ui.common.marketplace.in_stock_only') }}</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox"
                                   name="featured"
                                   value="1"
                                   id="featuredAdvanced"
                                   class="form-check-input"
                                   {{ request('featured') ? 'checked' : '' }}>
                            <label for="featuredAdvanced" class="form-check-label">{{ __('ui.common.marketplace.featured_only') }}</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox"
                                   name="on_sale"
                                   value="1"
                                   id="onSaleAdvanced"
                                   class="form-check-input"
                                   {{ request('on_sale') ? 'checked' : '' }}>
                            <label for="onSaleAdvanced" class="form-check-label">{{ __('ui.common.marketplace.on_sale') }}</label>
                        </div>
                    </div>
                </div>

                <!-- Sort Options -->
                <div class="col-12">
                    <label class="form-label fw-semibold">{{ __('ui.common.marketplace.sort_results_by') }}</label>
                    <div class="row g-2">
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="radio" name="sort" value="relevance" class="form-check-input" id="sortRelevance" {{ request('sort', 'relevance') == 'relevance' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sortRelevance">{{ __('ui.common.marketplace.relevance') }}</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="radio" name="sort" value="created_at" class="form-check-input" id="sortLatest" {{ request('sort') == 'created_at' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sortLatest">{{ __('ui.common.marketplace.latest') }}</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="radio" name="sort" value="price_low" class="form-check-input" id="sortPriceLow" {{ request('sort') == 'price_low' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sortPriceLow">{{ __('ui.common.marketplace.price_low_to_high') }}</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="radio" name="sort" value="price_high" class="form-check-input" id="sortPriceHigh" {{ request('sort') == 'price_high' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sortPriceHigh">{{ __('ui.common.marketplace.price_high_to_low') }}</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="radio" name="sort" value="rating" class="form-check-input" id="sortRating" {{ request('sort') == 'rating' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sortRating">{{ __('ui.common.marketplace.highest_rated') }}</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="radio" name="sort" value="popular" class="form-check-input" id="sortPopular" {{ request('sort') == 'popular' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sortPopular">{{ __('ui.common.marketplace.most_popular') }}</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="radio" name="sort" value="name" class="form-check-input" id="sortName" {{ request('sort') == 'name' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sortName">{{ __('ui.common.marketplace.name_a_z') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>
                        {{ __('ui.common.marketplace.search_products') }}
                    </button>
                    <a href="{{ route('marketplace.products.index') }}" class="btn btn-outline-secondary">
                        <i class="arrow-clockwise me-2"></i>
                        {{ __('ui.common.marketplace.clear_all') }}
                    </a>
                </div>
                <div class="text-muted small">
                    <span id="filterCount">{{ count(array_filter(request()->all())) }}</span> {{ __('ui.common.marketplace.filters_applied') }}
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
        if (button) button.innerHTML = '<i class="fas fa-search mr-2"></i> Hide Advanced Search';
    } else {
        panel.style.display = 'none';
        if (button) button.innerHTML = '<i class="fas fa-search mr-2"></i> Advanced Search';
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
