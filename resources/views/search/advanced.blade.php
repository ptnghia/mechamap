@extends('layouts.app')

@section('title', 'Advanced Search')

@section('css')
<style>
.search-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 60px 0;
    margin-bottom: 40px;
}

.search-box {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.search-input-group {
    position: relative;
    margin-bottom: 20px;
}

.search-input {
    border: 2px solid #e9ecef;
    border-radius: 50px;
    padding: 15px 60px 15px 25px;
    font-size: 16px;
    width: 100%;
    transition: all 0.3s ease;
}

.search-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

.search-btn {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    background: #667eea;
    border: none;
    border-radius: 50px;
    width: 50px;
    height: 50px;
    color: white;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.search-btn:hover {
    background: #5a6fd8;
    transform: translateY(-50%) scale(1.05);
}

.autocomplete-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
    display: none;
}

.autocomplete-item {
    padding: 12px 20px;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    display: flex;
    align-items: center;
    transition: background 0.2s ease;
}

.autocomplete-item:hover {
    background: #f8f9fa;
}

.autocomplete-item:last-child {
    border-bottom: none;
}

.autocomplete-icon {
    width: 20px;
    margin-right: 12px;
    color: #6c757d;
}

.autocomplete-text {
    flex: 1;
}

.autocomplete-category {
    font-size: 12px;
    color: #6c757d;
    background: #e9ecef;
    padding: 2px 8px;
    border-radius: 10px;
}

.filter-group {
    margin-bottom: 20px;
}

.filter-label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #495057;
}

.filter-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.filter-tag {
    background: #e7f3ff;
    color: #0d6efd;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.filter-tag:hover {
    background: #cce7ff;
}

.filter-tag.active {
    background: #0d6efd;
    color: white;
}

.popular-searches {
    margin-top: 20px;
}

.popular-search-item {
    display: inline-block;
    background: #f8f9fa;
    color: #495057;
    padding: 6px 12px;
    border-radius: 15px;
    margin: 4px;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
}

.popular-search-item:hover {
    background: #e9ecef;
    color: #495057;
}
</style>
@endsection

@section('content')
<!-- Search Header -->
<div class="search-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="search-box">
                    <h2 class="text-center mb-4">Advanced Search</h2>

                    <form id="search-form" action="{{ route('search.advanced') }}" method="GET">
                        <div class="search-input-group">
                            <input type="text"
                                   class="search-input"
                                   id="search-query"
                                   name="q"
                                   value="{{ $query ?? '' }}"
                                   placeholder="Tìm kiếm sản phẩm, thảo luận, tài liệu..."
                                   autocomplete="off">
                            <button type="submit" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>

                            <!-- Autocomplete Dropdown -->
                            <div class="autocomplete-dropdown" id="autocomplete-dropdown"></div>
                        </div>

                        <!-- Quick Filters -->
                        <div class="filter-group">
                            <div class="filter-label">Loại nội dung:</div>
                            <div class="filter-tags">
                                <span class="filter-tag active" data-filter="content_type" data-value="all">
                                    Tất cả
                                </span>
                                <span class="filter-tag" data-filter="content_type" data-value="threads">
                                    Thảo luận
                                </span>
                                <span class="filter-tag" data-filter="content_type" data-value="products">
                                    Sản phẩm
                                </span>
                                <span class="filter-tag" data-filter="content_type" data-value="users">
                                    Người dùng
                                </span>
                                <span class="filter-tag" data-filter="content_type" data-value="documents">
                                    Tài liệu
                                </span>
                            </div>
                        </div>

                        <!-- Hidden filter inputs -->
                        <div id="hidden-filters"></div>
                    </form>

                    @if(empty($query ?? ''))
                    <div class="popular-searches">
                        <div class="filter-label">Tìm kiếm phổ biến:</div>
                        <a href="{{ route('search.advanced', ['q' => 'máy tiện CNC']) }}" class="popular-search-item">máy tiện CNC</a>
                        <a href="{{ route('search.advanced', ['q' => 'động cơ servo']) }}" class="popular-search-item">động cơ servo</a>
                        <a href="{{ route('search.advanced', ['q' => 'bearing SKF']) }}" class="popular-search-item">bearing SKF</a>
                        <a href="{{ route('search.advanced', ['q' => 'thép không gỉ']) }}" class="popular-search-item">thép không gỉ</a>
                        <a href="{{ route('search.advanced', ['q' => 'máy phay']) }}" class="popular-search-item">máy phay</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@if(!empty($query ?? ''))
<div class="container">
    <div class="row">
        <!-- Results -->
        <div class="col-lg-12">
            <!-- Search Results -->
            <div class="card">
                <div class="card-body">
                    @if(isset($results['total']) && $results['total'] > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Tìm thấy {{ number_format($results['total']) }} kết quả cho "{{ $query }}"</h5>
                            @if(isset($results['meta']['search_time']))
                            <small class="text-muted">Thời gian: {{ $results['meta']['search_time'] }}ms</small>
                            @endif
                        </div>

                        @foreach($results['results'] ?? [] as $result)
                        <div class="border-bottom py-3">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-{{ $result['type'] === 'thread' ? 'success' : ($result['type'] === 'product' ? 'primary' : 'secondary') }} me-2">
                                    {{ ucfirst($result['type']) }}
                                </span>
                                <h6 class="mb-0">
                                    <a href="{{ $result['url'] }}" class="text-decoration-none">
                                        {!! $result['highlights']['title'][0] ?? $result['title'] !!}
                                    </a>
                                </h6>
                            </div>

                            <div class="text-muted small mb-2">
                                @if($result['author'])
                                    Bởi {{ $result['author'] }} •
                                @endif
                                @if($result['category'])
                                    {{ $result['category'] }} •
                                @endif
                                {{ \Carbon\Carbon::parse($result['created_at'])->diffForHumans() }}
                                @if(isset($result['price']))
                                    • <strong>{{ number_format($result['price']) }} VND</strong>
                                @endif
                            </div>

                            <p class="mb-2">
                                {!! $result['highlights']['content'][0] ?? Str::limit(strip_tags($result['content']), 200) !!}
                            </p>

                            @if(!empty($result['tags']))
                            <div>
                                @foreach($result['tags'] as $tag)
                                <span class="badge bg-light text-dark me-1">{{ $tag }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach

                        <!-- Pagination -->
                        @if(isset($results['meta']) && $results['meta']['total_pages'] > 1)
                        <div class="d-flex justify-content-center mt-4">
                            <nav>
                                <ul class="pagination">
                                    @for($i = 1; $i <= min($results['meta']['total_pages'], 10); $i++)
                                    <li class="page-item {{ $i == $results['meta']['page'] ? 'active' : '' }}">
                                        <a class="page-link" href="{{ route('search.advanced', array_merge(request()->all(), ['page' => $i])) }}">
                                            {{ $i }}
                                        </a>
                                    </li>
                                    @endfor
                                </ul>
                            </nav>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5>Không tìm thấy kết quả</h5>
                            <p class="text-muted">Thử sử dụng từ khóa khác hoặc kiểm tra chính tả</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
class AdvancedSearch {
    constructor() {
        this.searchInput = document.getElementById('search-query');
        this.autocompleteDropdown = document.getElementById('autocomplete-dropdown');
        this.searchForm = document.getElementById('search-form');
        this.filters = {};
        this.debounceTimer = null;

        this.init();
    }

    init() {
        this.setupAutocomplete();
        this.setupFilters();
        this.setupKeyboardShortcuts();
    }

    setupAutocomplete() {
        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.getAutocomplete(e.target.value);
            }, 300);
        });

        this.searchInput.addEventListener('focus', () => {
            if (this.searchInput.value.length >= 2) {
                this.getAutocomplete(this.searchInput.value);
            }
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-input-group')) {
                this.hideAutocomplete();
            }
        });
    }

    async getAutocomplete(query) {
        if (query.length < 2) {
            this.hideAutocomplete();
            return;
        }

        try {
            const response = await fetch(`/search/autocomplete?q=${encodeURIComponent(query)}&limit=8`);
            const data = await response.json();

            if (data.success && data.data.length > 0) {
                this.showAutocomplete(data.data);
            } else {
                this.hideAutocomplete();
            }
        } catch (error) {
            console.error('Autocomplete error:', error);
            this.hideAutocomplete();
        }
    }

    showAutocomplete(suggestions) {
        let html = '';

        suggestions.forEach(suggestion => {
            html += `
                <div class="autocomplete-item" data-text="${suggestion.text}">
                    <i class="${suggestion.icon || 'fas fa-search'} autocomplete-icon"></i>
                    <div class="autocomplete-text">${suggestion.display || suggestion.text}</div>
                    <div class="autocomplete-category">${suggestion.category}</div>
                </div>
            `;
        });

        this.autocompleteDropdown.innerHTML = html;
        this.autocompleteDropdown.style.display = 'block';

        // Add click handlers
        this.autocompleteDropdown.querySelectorAll('.autocomplete-item').forEach(item => {
            item.addEventListener('click', () => {
                this.searchInput.value = item.dataset.text;
                this.hideAutocomplete();
                this.searchForm.submit();
            });
        });
    }

    hideAutocomplete() {
        this.autocompleteDropdown.style.display = 'none';
    }

    setupFilters() {
        document.querySelectorAll('.filter-tag').forEach(tag => {
            tag.addEventListener('click', () => {
                // Remove active from siblings
                tag.parentElement.querySelectorAll('.filter-tag').forEach(t => t.classList.remove('active'));
                // Add active to clicked
                tag.classList.add('active');

                const filter = tag.dataset.filter;
                const value = tag.dataset.value;

                this.filters[filter] = value;
                this.updateHiddenFilters();
                this.searchForm.submit();
            });
        });
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Focus search with Ctrl+K or Cmd+K
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.searchInput.focus();
            }
        });
    }

    updateHiddenFilters() {
        const container = document.getElementById('hidden-filters');
        container.innerHTML = '';

        Object.entries(this.filters).forEach(([filter, value]) => {
            if (value !== 'all') {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `filters[${filter}]`;
                input.value = value;
                container.appendChild(input);
            }
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new AdvancedSearch();
});
</script>
@endsection
