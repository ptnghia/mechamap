@props(['placeholder' => 'Search products...', 'size' => 'md'])

@php
$sizeClasses = [
    'sm' => 'px-3 py-2 text-sm',
    'md' => 'px-4 py-2',
    'lg' => 'px-6 py-3 text-lg'
];
$inputClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div class="relative max-w-md mx-auto" x-data="quickSearch()">
    <form method="GET" action="{{ route('marketplace.products.index') }}" class="relative">
        <input 
            type="text" 
            name="search"
            value="{{ request('search') }}"
            class="w-full {{ $inputClass }} pl-10 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
            placeholder="{{ $placeholder }}"
            x-model="query"
            x-on:input.debounce.300ms="searchSuggestions()"
            x-on:keydown.escape="showSuggestions = false"
            x-on:keydown.arrow-down.prevent="highlightNext()"
            x-on:keydown.arrow-up.prevent="highlightPrevious()"
            x-on:keydown.enter.prevent="selectHighlighted()"
            autocomplete="off">
        
        <!-- Search Icon -->
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="bi bi-search text-gray-400"></i>
        </div>
        
        <!-- Submit Button -->
        <button 
            type="submit"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition-colors">
            <i class="bi bi-arrow-right"></i>
        </button>
        
        <!-- Loading Spinner -->
        <div x-show="loading" class="absolute inset-y-0 right-8 flex items-center">
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
        </div>
    </form>

    <!-- Search Suggestions Dropdown -->
    <div 
        x-show="showSuggestions && suggestions.length > 0"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-96 overflow-y-auto"
        x-on:click.away="showSuggestions = false">
        
        <!-- Popular Searches -->
        <div x-show="query.length === 0" class="p-4">
            <h4 class="text-sm font-medium text-gray-900 mb-2">Popular Searches</h4>
            <div class="space-y-1">
                <a href="{{ route('marketplace.products.index', ['search' => 'bearing']) }}" class="block text-sm text-gray-600 hover:text-blue-600 hover:bg-gray-50 px-2 py-1 rounded">Ball Bearings</a>
                <a href="{{ route('marketplace.products.index', ['search' => 'gear']) }}" class="block text-sm text-gray-600 hover:text-blue-600 hover:bg-gray-50 px-2 py-1 rounded">Gear Assembly</a>
                <a href="{{ route('marketplace.products.index', ['search' => 'CAD']) }}" class="block text-sm text-gray-600 hover:text-blue-600 hover:bg-gray-50 px-2 py-1 rounded">CAD Files</a>
                <a href="{{ route('marketplace.products.index', ['search' => 'aluminum']) }}" class="block text-sm text-gray-600 hover:text-blue-600 hover:bg-gray-50 px-2 py-1 rounded">Aluminum Parts</a>
            </div>
        </div>

        <!-- Search Results -->
        <div x-show="query.length > 0">
            <!-- Products -->
            <div x-show="suggestions.products && suggestions.products.length > 0">
                <div class="px-4 py-2 border-b border-gray-100">
                    <h4 class="text-sm font-medium text-gray-900">Products</h4>
                </div>
                <template x-for="(product, index) in suggestions.products" :key="product.id">
                    <a 
                        :href="'/marketplace/products/' + product.slug"
                        class="flex items-center px-4 py-3 hover:bg-gray-50 cursor-pointer"
                        :class="{ 'bg-blue-50': highlightedIndex === index }"
                        x-on:mouseenter="highlightedIndex = index">
                        <div class="flex-shrink-0 w-10 h-10 bg-gray-200 rounded mr-3">
                            <img x-show="product.featured_image" :src="product.featured_image" :alt="product.name" class="w-10 h-10 object-cover rounded">
                            <div x-show="!product.featured_image" class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                <i class="bi bi-image text-gray-400"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate" x-text="product.name"></p>
                            <p class="text-sm text-gray-500 truncate" x-text="'$' + product.price"></p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="product.product_type"></span>
                        </div>
                    </a>
                </template>
            </div>

            <!-- Categories -->
            <div x-show="suggestions.categories && suggestions.categories.length > 0">
                <div class="px-4 py-2 border-b border-gray-100">
                    <h4 class="text-sm font-medium text-gray-900">Categories</h4>
                </div>
                <template x-for="category in suggestions.categories" :key="category.id">
                    <a 
                        :href="'/marketplace/products?category=' + category.slug"
                        class="flex items-center px-4 py-3 hover:bg-gray-50 cursor-pointer">
                        <i class="bi bi-folder text-gray-400 mr-3"></i>
                        <span class="text-sm text-gray-900" x-text="category.name"></span>
                        <span class="ml-auto text-xs text-gray-500" x-text="category.products_count + ' products'"></span>
                    </a>
                </template>
            </div>

            <!-- No Results -->
            <div x-show="suggestions.products && suggestions.products.length === 0 && suggestions.categories && suggestions.categories.length === 0" class="px-4 py-6 text-center">
                <i class="bi bi-search text-gray-400 text-2xl mb-2"></i>
                <p class="text-sm text-gray-500">No results found</p>
                <p class="text-xs text-gray-400 mt-1">Try different keywords or check spelling</p>
            </div>
        </div>

        <!-- View All Results -->
        <div x-show="query.length > 0" class="border-t border-gray-100 px-4 py-3">
            <button 
                type="submit"
                class="w-full text-left text-sm text-blue-600 hover:text-blue-800 font-medium"
                x-on:click="$refs.form.submit()">
                View all results for "<span x-text="query"></span>"
            </button>
        </div>
    </div>
</div>

<script>
function quickSearch() {
    return {
        query: '{{ request('search') }}',
        suggestions: {
            products: [],
            categories: []
        },
        showSuggestions: false,
        loading: false,
        highlightedIndex: -1,

        async searchSuggestions() {
            if (this.query.length < 2) {
                this.showSuggestions = this.query.length === 0;
                return;
            }

            this.loading = true;
            this.showSuggestions = true;

            try {
                const response = await fetch(`/api/marketplace/search-suggestions?q=${encodeURIComponent(this.query)}`);
                const data = await response.json();
                this.suggestions = data;
                this.highlightedIndex = -1;
            } catch (error) {
                console.error('Search suggestions error:', error);
                this.suggestions = { products: [], categories: [] };
            } finally {
                this.loading = false;
            }
        },

        highlightNext() {
            const totalItems = (this.suggestions.products?.length || 0) + (this.suggestions.categories?.length || 0);
            if (totalItems > 0) {
                this.highlightedIndex = Math.min(this.highlightedIndex + 1, totalItems - 1);
            }
        },

        highlightPrevious() {
            if (this.highlightedIndex > 0) {
                this.highlightedIndex--;
            }
        },

        selectHighlighted() {
            if (this.highlightedIndex >= 0) {
                const products = this.suggestions.products || [];
                const categories = this.suggestions.categories || [];
                
                if (this.highlightedIndex < products.length) {
                    // Navigate to product
                    window.location.href = `/marketplace/products/${products[this.highlightedIndex].slug}`;
                } else {
                    // Navigate to category
                    const categoryIndex = this.highlightedIndex - products.length;
                    window.location.href = `/marketplace/products?category=${categories[categoryIndex].slug}`;
                }
            } else {
                // Submit search form
                this.$refs.form.submit();
            }
        }
    }
}
</script>
