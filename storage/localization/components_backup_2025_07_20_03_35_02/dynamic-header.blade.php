{{--
    MechaMap Dynamic Header Component
    Sử dụng MenuService để load menu component phù hợp theo user role
--}}
@props(['showBanner' => true, 'isMarketplace' => false])

@php
    use App\Services\MenuService;
    
    // Lấy menu component phù hợp cho user hiện tại
    $menuComponent = MenuService::getMenuComponent(auth()->user());
    $menuConfig = MenuService::getMenuConfiguration(auth()->user());
@endphp

<header class="site-header">
    <!-- Banner (optional) -->
    @if($showBanner && get_setting('show_banner', true))
    <div class="header-banner">
        <img src="{{ get_banner_url() }}" alt="Banner" class="w-100">
    </div>
    @endif
    
    <!-- Dynamic Menu Component -->
    <div class="header-content" id="header-content">
        <x-dynamic-component :component="$menuComponent" :config="$menuConfig" />
    </div>
    
    <!-- Search Modal (Global) -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">
                        <i class="fas fa-search me-2"></i>
                        Tìm kiếm
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('search') }}" method="GET" id="globalSearchForm">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg" 
                                   name="q" placeholder="Nhập từ khóa tìm kiếm..." 
                                   value="{{ request('q') }}" autocomplete="off">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        
                        <!-- Search Filters -->
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Tìm trong:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="types[]" value="threads" id="searchThreads" checked>
                                    <label class="form-check-label" for="searchThreads">Bài viết</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="types[]" value="showcases" id="searchShowcases" checked>
                                    <label class="form-check-label" for="searchShowcases">Showcases</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="types[]" value="products" id="searchProducts" checked>
                                    <label class="form-check-label" for="searchProducts">Sản phẩm</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="types[]" value="users" id="searchUsers">
                                    <label class="form-check-label" for="searchUsers">Người dùng</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sắp xếp theo:</label>
                                <select class="form-select" name="sort">
                                    <option value="relevance">Độ liên quan</option>
                                    <option value="newest">Mới nhất</option>
                                    <option value="oldest">Cũ nhất</option>
                                    <option value="popular">Phổ biến</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Quick Search Results -->
                    <div id="quickSearchResults" class="mt-3" style="display: none;">
                        <h6>Kết quả nhanh:</h6>
                        <div class="list-group" id="quickResultsList">
                            <!-- Results will be loaded here via AJAX -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" form="globalSearchForm" class="btn btn-primary">Tìm kiếm</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Search Modal -->
    <div class="modal fade" id="mobileSearchModal" tabindex="-1" aria-labelledby="mobileSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mobileSearchModalLabel">Tìm kiếm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('search') }}" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="q" placeholder="Tìm kiếm..." autocomplete="off">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Header Styles -->
<style>
/* Dynamic Header Styles */
.site-header {
    position: sticky;
    top: 0;
    z-index: 1030;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.header-banner img {
    max-height: 120px;
    object-fit: cover;
}

.header-content {
    background: white;
}

/* Search Modal Styles */
#searchModal .modal-dialog {
    max-width: 600px;
}

#quickSearchResults .list-group-item {
    border: none;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
}

#quickSearchResults .list-group-item:last-child {
    border-bottom: none;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .header-banner {
        display: none;
    }
    
    .site-header {
        position: fixed;
        width: 100%;
    }
    
    /* Add padding to body to compensate for fixed header */
    body {
        padding-top: 70px;
    }
}

/* Loading States */
.menu-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 60px;
    background: #f8f9fa;
}

.menu-loading .spinner-border {
    width: 1.5rem;
    height: 1.5rem;
}

/* Error States */
.menu-error {
    background: #f8d7da;
    color: #721c24;
    padding: 0.75rem;
    text-align: center;
    border: 1px solid #f5c6cb;
}

/* Animation for menu transitions */
.menu-transition {
    transition: all 0.3s ease-in-out;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .site-header {
        background: #1a1a1a;
        color: white;
    }
    
    .header-content {
        background: #1a1a1a;
    }
}
</style>

<!-- Header JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dynamic header functionality
    initDynamicHeader();
    
    // Initialize search functionality
    initGlobalSearch();
    
    // Initialize mobile menu enhancements
    initMobileMenuEnhancements();
});

function initDynamicHeader() {
    // Track menu component loading
    const menuComponent = '{{ $menuComponent }}';
    
    // Log menu component for analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', 'menu_component_loaded', {
            'component': menuComponent,
            'user_role': '{{ auth()->user()->role ?? "guest" }}'
        });
    }
    
    // Handle menu component errors
    window.addEventListener('error', function(e) {
        if (e.target && e.target.closest('.header-content')) {
            console.error('Menu component error:', e);
            showMenuError();
        }
    });
}

function initGlobalSearch() {
    const searchInput = document.querySelector('#globalSearchForm input[name="q"]');
    const quickResults = document.getElementById('quickSearchResults');
    const resultsList = document.getElementById('quickResultsList');
    
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    performQuickSearch(query, resultsList, quickResults);
                }, 300);
            } else {
                quickResults.style.display = 'none';
            }
        });
    }
}

function performQuickSearch(query, resultsList, quickResults) {
    // Show loading state
    resultsList.innerHTML = '<div class="text-center py-2"><div class="spinner-border spinner-border-sm" role="status"></div></div>';
    quickResults.style.display = 'block';
    
    // Perform AJAX search
    fetch(`{{ route('api.search.quick') }}?q=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        displayQuickResults(data.results || [], resultsList);
    })
    .catch(error => {
        console.error('Quick search error:', error);
        resultsList.innerHTML = '<div class="text-muted text-center py-2">Lỗi tìm kiếm</div>';
    });
}

function displayQuickResults(results, resultsList) {
    if (results.length === 0) {
        resultsList.innerHTML = '<div class="text-muted text-center py-2">Không tìm thấy kết quả</div>';
        return;
    }
    
    const html = results.map(result => `
        <a href="${result.url}" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
                <h6 class="mb-1">${result.title}</h6>
                <small class="text-muted">${result.type}</small>
            </div>
            <p class="mb-1">${result.excerpt}</p>
        </a>
    `).join('');
    
    resultsList.innerHTML = html;
}

function initMobileMenuEnhancements() {
    // Handle mobile menu toggle
    const mobileToggle = document.querySelector('.navbar-toggler');
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            // Add mobile menu analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'mobile_menu_toggle');
            }
        });
    }
}

function showMenuError() {
    const headerContent = document.getElementById('header-content');
    if (headerContent) {
        headerContent.innerHTML = `
            <div class="menu-error">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Lỗi tải menu. Vui lòng tải lại trang.
                <button onclick="location.reload()" class="btn btn-sm btn-outline-danger ms-2">
                    Tải lại
                </button>
            </div>
        `;
    }
}

// Export functions for external use
window.MechaMapHeader = {
    initDynamicHeader,
    initGlobalSearch,
    showMenuError
};
</script>
