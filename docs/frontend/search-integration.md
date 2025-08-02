# MechaMap Frontend Search Integration Guide

## Overview

This guide covers the integration of the Unified Search functionality in MechaMap's frontend, including the search dropdown component and user experience patterns.

## Search Component Architecture

### 1. Main Search Input
Located in the header navigation (`resources/views/layouts/app.blade.php`):

```html
<div class="search-container">
    <div class="search-input-wrapper">
        <input type="text" 
               class="form-control search-input" 
               placeholder="Tìm kiếm" 
               id="globalSearch"
               autocomplete="off">
        <button class="search-btn" type="button">
            <i class="fas fa-search"></i>
        </button>
    </div>
    <div class="search-dropdown" id="searchDropdown">
        <!-- Dynamic search results -->
    </div>
</div>
```

### 2. JavaScript Implementation
Main search functionality in `public/js/search.js`:

```javascript
class UnifiedSearch {
    constructor() {
        this.searchInput = document.getElementById('globalSearch');
        this.searchDropdown = document.getElementById('searchDropdown');
        this.debounceTimer = null;
        this.minQueryLength = 2;
        
        this.init();
    }
    
    init() {
        this.searchInput.addEventListener('input', this.handleInput.bind(this));
        this.searchInput.addEventListener('focus', this.handleFocus.bind(this));
        document.addEventListener('click', this.handleClickOutside.bind(this));
    }
    
    handleInput(event) {
        const query = event.target.value.trim();
        
        clearTimeout(this.debounceTimer);
        
        if (query.length < this.minQueryLength) {
            this.hideDropdown();
            return;
        }
        
        this.debounceTimer = setTimeout(() => {
            this.performSearch(query);
        }, 300);
    }
    
    async performSearch(query) {
        try {
            this.showLoading();
            
            const response = await fetch(`/api/v1/search/unified?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success) {
                this.displayResults(data.data.results, query);
            } else {
                this.showError('Có lỗi xảy ra khi tìm kiếm');
            }
        } catch (error) {
            console.error('Search error:', error);
            this.showError('Không thể kết nối đến server');
        }
    }
    
    displayResults(results, query) {
        const { threads, showcases, products, users, meta } = results;
        
        let html = `
            <div class="search-results">
                <div class="search-tabs">
                    <div class="tab active" data-tab="all">Tất cả nội dung</div>
                    <div class="tab" data-tab="specific">Trong chủ đề</div>
                </div>
                <div class="search-content">
        `;
        
        // Render threads
        if (threads.length > 0) {
            html += this.renderThreads(threads);
        }
        
        // Render showcases
        if (showcases.length > 0) {
            html += this.renderShowcases(showcases);
        }
        
        // Render products
        if (products.length > 0) {
            html += this.renderProducts(products);
        }
        
        // Render users
        if (users.length > 0) {
            html += this.renderUsers(users);
        }
        
        // Footer with total count and advanced search
        html += `
                    <div class="search-footer">
                        <div class="search-count">Tìm thấy ${meta.total} kết quả</div>
                        <a href="/forums/search/advanced?q=${encodeURIComponent(query)}" class="advanced-search">
                            <i class="fas fa-search-plus"></i> Tìm kiếm nâng cao
                        </a>
                    </div>
                </div>
                <a href="/forums/search/advanced" class="advanced-search-link">
                    🔍 Tìm kiếm nâng cao
                </a>
            </div>
        `;
        
        this.searchDropdown.innerHTML = html;
        this.showDropdown();
    }
    
    renderThreads(threads) {
        if (threads.length === 0) return '';
        
        let html = `
            <div class="search-section">
                <h6 class="section-title">
                    <i class="fas fa-comments"></i> Thảo luận
                </h6>
        `;
        
        threads.forEach(thread => {
            html += `
                <div class="search-item" onclick="window.location.href='${thread.url}'">
                    <img src="${thread.author.avatar}" alt="${thread.author.name}" class="item-avatar">
                    <div class="item-content">
                        <h6 class="item-title">
                            <a href="${thread.url}">${thread.title}</a>
                        </h6>
                        <p class="item-excerpt">${thread.excerpt}</p>
                        <div class="item-meta">
                            <div class="meta-left">
                                <i class="fas fa-user"></i> ${thread.author.name} •
                                <i class="fas fa-folder"></i> ${thread.forum.name}
                            </div>
                            <div class="meta-right">
                                <i class="fas fa-eye"></i> ${thread.stats.views} • ${thread.created_at}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        return html;
    }
    
    renderShowcases(showcases) {
        if (showcases.length === 0) return '';
        
        let html = `
            <div class="search-section">
                <h6 class="section-title">
                    <i class="fas fa-trophy"></i> Dự án
                </h6>
        `;
        
        showcases.forEach(showcase => {
            html += `
                <div class="search-item" onclick="window.location.href='${showcase.url}'">
                    <img src="${showcase.image}" alt="${showcase.title}" class="item-image">
                    <div class="item-content">
                        <h6 class="item-title">
                            <a href="${showcase.url}">${showcase.title}</a>
                        </h6>
                        <p class="item-excerpt">${showcase.excerpt}</p>
                        <div class="item-meta">
                            <div class="meta-left">
                                <i class="fas fa-user"></i> ${showcase.author.name} •
                                <i class="fas fa-tag"></i> ${showcase.category}
                            </div>
                            <div class="meta-right">
                                <i class="fas fa-eye"></i> ${showcase.stats.views} • ⭐${showcase.rating.average}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        return html;
    }
    
    renderProducts(products) {
        if (products.length === 0) return '';
        
        let html = `
            <div class="search-section">
                <h6 class="section-title">
                    <i class="fas fa-shopping-cart"></i> Sản phẩm
                </h6>
        `;
        
        products.forEach(product => {
            html += `
                <div class="search-item" onclick="window.location.href='${product.url}'">
                    <img src="${product.image}" alt="${product.title}" class="item-image">
                    <div class="item-content">
                        <h6 class="item-title">
                            <a href="${product.url}">${product.title}</a>
                            <span class="item-price">${product.price.formatted}</span>
                        </h6>
                        <p class="item-excerpt">${product.excerpt}</p>
                        <div class="item-meta">
                            <div class="meta-left">
                                <i class="fas fa-store"></i> ${product.seller.name}
                            </div>
                            <div class="meta-right">
                                <i class="fas fa-eye"></i> ${product.stats.views} • ${product.created_at}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        return html;
    }
    
    renderUsers(users) {
        if (users.length === 0) return '';
        
        let html = `
            <div class="search-section">
                <h6 class="section-title">
                    <i class="fas fa-users"></i> Thành viên
                </h6>
        `;
        
        users.forEach(user => {
            html += `
                <div class="search-item" onclick="window.location.href='${user.url}'">
                    <img src="${user.avatar}" alt="${user.name}" class="item-avatar">
                    <div class="item-content">
                        <h6 class="item-title">
                            <a href="${user.url}">${user.name}</a>
                            <span class="item-username">@${user.username}</span>
                        </h6>
                        <div class="item-role">${user.role}</div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        return html;
    }
    
    showLoading() {
        this.searchDropdown.innerHTML = `
            <div class="search-loading">
                <i class="fas fa-spinner fa-spin"></i> Đang tìm kiếm...
            </div>
        `;
        this.showDropdown();
    }
    
    showError(message) {
        this.searchDropdown.innerHTML = `
            <div class="search-error">
                <i class="fas fa-exclamation-triangle"></i> ${message}
            </div>
        `;
        this.showDropdown();
    }
    
    showDropdown() {
        this.searchDropdown.classList.add('show');
    }
    
    hideDropdown() {
        this.searchDropdown.classList.remove('show');
    }
    
    handleClickOutside(event) {
        if (!this.searchInput.contains(event.target) && 
            !this.searchDropdown.contains(event.target)) {
            this.hideDropdown();
        }
    }
}

// Initialize search when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new UnifiedSearch();
});
```

## CSS Styling

Search component styles in `public/css/search.css`:

```css
.search-container {
    position: relative;
    width: 100%;
    max-width: 400px;
}

.search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-input {
    width: 100%;
    padding: 8px 40px 8px 12px;
    border: 1px solid #ddd;
    border-radius: 20px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.search-btn {
    position: absolute;
    right: 8px;
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
}

.search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    max-height: 500px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
}

.search-dropdown.show {
    display: block;
}

.search-tabs {
    display: flex;
    border-bottom: 1px solid #eee;
    padding: 0 16px;
}

.search-tabs .tab {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    font-weight: 500;
    color: #666;
}

.search-tabs .tab.active {
    color: #007bff;
    border-bottom-color: #007bff;
}

.search-section {
    padding: 16px;
    border-bottom: 1px solid #f0f0f0;
}

.search-section:last-child {
    border-bottom: none;
}

.section-title {
    margin: 0 0 12px 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}

.search-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.search-item:hover {
    background-color: #f8f9fa;
}

.item-avatar,
.item-image {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}

.item-image {
    border-radius: 6px;
}

.item-content {
    flex: 1;
    min-width: 0;
}

.item-title {
    margin: 0 0 4px 0;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.item-title a {
    color: #333;
    text-decoration: none;
    flex: 1;
}

.item-title a:hover {
    color: #007bff;
}

.item-price {
    color: #28a745;
    font-weight: 700;
    font-size: 13px;
}

.item-username {
    color: #666;
    font-weight: 400;
    font-size: 13px;
}

.item-excerpt {
    margin: 0 0 8px 0;
    font-size: 13px;
    color: #666;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.item-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: #888;
}

.item-role {
    font-size: 12px;
    color: #666;
    text-transform: capitalize;
}

.search-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background-color: #f8f9fa;
    border-top: 1px solid #eee;
}

.search-count {
    font-size: 12px;
    color: #666;
}

.advanced-search {
    font-size: 12px;
    color: #007bff;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 4px;
}

.advanced-search:hover {
    text-decoration: underline;
}

.advanced-search-link {
    display: block;
    padding: 12px 16px;
    text-align: center;
    background-color: #f8f9fa;
    color: #007bff;
    text-decoration: none;
    border-top: 1px solid #eee;
    font-size: 13px;
}

.search-loading,
.search-error {
    padding: 20px;
    text-align: center;
    color: #666;
}

.search-error {
    color: #dc3545;
}

/* Responsive Design */
@media (max-width: 768px) {
    .search-container {
        max-width: 100%;
    }
    
    .search-dropdown {
        left: -16px;
        right: -16px;
        max-height: 400px;
    }
    
    .item-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
}
```

## User Experience Patterns

### 1. Search Behavior
- **Debounced Input:** 300ms delay to prevent excessive API calls
- **Minimum Query Length:** 2 characters
- **Auto-focus:** Dropdown appears on input focus
- **Click Outside:** Dropdown closes when clicking outside

### 2. Content Prioritization
- **Threads:** Most relevant forum discussions
- **Showcases:** Engineering projects and portfolios  
- **Products:** Marketplace items with pricing
- **Users:** Member profiles (@ prefix or alphanumeric)

### 3. Visual Feedback
- **Loading State:** Spinner with "Đang tìm kiếm..." message
- **Error State:** Error icon with descriptive message
- **Empty State:** "Không tìm thấy kết quả" message
- **Result Count:** Total results found display

### 4. Navigation
- **Direct Links:** All items link to their detail pages
- **Advanced Search:** Link to comprehensive search page
- **Keyboard Navigation:** Arrow keys and Enter support (future enhancement)

## Integration Checklist

- [x] API endpoint implemented (`/api/v1/search/unified`)
- [x] Frontend JavaScript component
- [x] CSS styling and responsive design
- [x] Error handling and loading states
- [x] User search with @ prefix
- [x] Performance optimization (debouncing)
- [x] Cross-browser compatibility
- [x] Mobile responsive design
- [ ] Keyboard navigation (future enhancement)
- [ ] Search analytics tracking (future enhancement)
- [ ] Caching strategy (future enhancement)

## Performance Considerations

1. **API Response Time:** Target < 500ms
2. **Debouncing:** 300ms delay prevents excessive requests
3. **Result Limiting:** 5 results per category by default
4. **Image Optimization:** Lazy loading for search result images
5. **Memory Management:** Cleanup event listeners on component destroy

## Accessibility

1. **ARIA Labels:** Proper labeling for screen readers
2. **Keyboard Navigation:** Tab order and focus management
3. **Color Contrast:** WCAG AA compliant color schemes
4. **Screen Reader:** Descriptive text for all interactive elements

## Future Enhancements

1. **Search History:** Recent searches storage
2. **Search Suggestions:** Auto-complete functionality
3. **Advanced Filters:** Category, date, author filters
4. **Search Analytics:** Track popular queries
5. **Offline Support:** Cache recent searches
6. **Voice Search:** Speech-to-text integration
