{{-- Advanced Search Form for Showcase --}}
<form id="showcase-search-form" method="GET" action="{{ route('showcase.index') }}" class="showcase-search-form mb-3">
    {{-- Basic Search Row --}}
    <div class="search-basic-row">
        <div class="row gx-3 align-items-end">
            <div class="col-md-8">
                <div class="search-input-group">
                    <input type="text"
                            name="search"
                            id="search-input"
                            class="form-control"
                            placeholder="{{ __('showcase.search_placeholder') }}"
                            value="{{ request('search') }}"
                            autocomplete="off">
                    <div class="search-input-icon">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>{{ __('showcase.search_button') }}
                </button>
            </div>
            <div class="col-md-2">
                <button type="button"
                        class="btn btn-outline-primary w-100"
                        id="toggle-filters"
                        data-bs-toggle="collapse"
                        data-bs-target="#advanced-filters"
                        aria-expanded="false">
                    <i class="fas fa-filter me-2"></i>
                    <span class="filter-toggle-text">{{ __('common.advanced_search') }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Advanced Filters (Collapsible) --}}
    <div class="collapse {{ request()->hasAny(['category', 'complexity', 'project_type', 'industry', 'software', 'rating_min', 'has_cad_files', 'has_tutorial', 'allow_downloads']) ? 'show' : '' }}"
            id="advanced-filters">
        <div class="advanced-filters-content mt-4">
            <div class="row g-3">
                {{-- Category Filter --}}
                <div class="col-md-6 col-lg-3">
                    <label for="category" class="form-label fw-semibold">
                        {{ __('showcase.filter_category') }}
                    </label>
                    <select name="category" id="category" class="form-select">
                        <option value="">{{ __('common.all') }}</option>
                        @if(isset($searchFilters['categories']['options']))
                            @foreach($searchFilters['categories']['options'] as $option)
                                @if($option['value'] !== '') {{-- Skip the "All" option since we already have it --}}
                                <option value="{{ $option['value'] }}"
                                        {{ request('category') == $option['value'] ? 'selected' : '' }}>
                                    {{ $option['label'] }}
                                </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Complexity Filter --}}
                <div class="col-md-6 col-lg-3">
                    <label for="complexity" class="form-label fw-semibold">
                        {{ __('showcase.filter_complexity') }}
                    </label>
                    <select name="complexity" id="complexity" class="form-select">
                        <option value="">{{ __('common.all') }}</option>
                        @if(isset($searchFilters['complexity_levels']['options']))
                            @foreach($searchFilters['complexity_levels']['options'] as $level)
                                @if($level['value'] !== '') {{-- Skip the "All" option since we already have it --}}
                        <option value="{{ $level['value'] }}"
                                {{ request('complexity') == $level['value'] ? 'selected' : '' }}>
                            {{ $level['label'] }}
                        </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Project Type Filter --}}
                <div class="col-md-6 col-lg-3">
                    <label for="project_type" class="form-label fw-semibold">
                        {{ __('showcase.filter_project_type') }}
                    </label>
                    <select name="project_type" id="project_type" class="form-select">
                        <option value="">{{ __('common.all') }}</option>
                        @if(isset($searchFilters['project_types']['options']))
                            @foreach($searchFilters['project_types']['options'] as $type)
                                @if($type['value'] !== '') {{-- Skip the "All" option since we already have it --}}
                                <option value="{{ $type['value'] }}"
                                        {{ request('project_type') == $type['value'] ? 'selected' : '' }}>
                                    {{ $type['label'] }}
                                </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Industry Filter --}}
                <div class="col-md-6 col-lg-3">
                    <label for="industry" class="form-label fw-semibold">
                        {{ __('showcase.filter_industry') }}
                    </label>
                    <select name="industry" id="industry" class="form-select">
                        <option value="">{{ __('common.all') }}</option>
                        @if(isset($searchFilters['industries']['options']))
                            @foreach($searchFilters['industries']['options'] as $industry)
                                @if($industry['value'] !== '') {{-- Skip the "All" option since we already have it --}}
                                <option value="{{ $industry['value'] }}"
                                        {{ request('industry') == $industry['value'] ? 'selected' : '' }}>
                                    {{ $industry['label'] }}
                                </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Software Filter --}}
                <div class="col-md-6 col-lg-4">
                    <label for="software" class="form-label fw-semibold">
                        {{ __('showcase.filter_software') }}
                    </label>
                    <select name="software" id="software" class="form-select">
                        <option value="">{{ __('common.all') }}</option>
                        @if(isset($searchFilters['software_options']['options']))
                            @foreach($searchFilters['software_options']['options'] as $software)
                                @if($software['value'] !== '') {{-- Skip the "All" option since we already have it --}}
                                <option value="{{ $software['value'] }}"
                                        {{ request('software') == $software['value'] ? 'selected' : '' }}>
                                    {{ $software['label'] }}
                                </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Rating Filter --}}
                <div class="col-md-6 col-lg-4">
                    <label for="rating_min" class="form-label fw-semibold">
                        {{ __('showcase.filter_rating') }}
                    </label>
                    <select name="rating_min" id="rating_min" class="form-select">
                        <option value="">{{ __('common.all') }}</option>
                        <option value="4" {{ request('rating_min') == '4' ? 'selected' : '' }}>4+ ⭐</option>
                        <option value="3" {{ request('rating_min') == '3' ? 'selected' : '' }}>3+ ⭐</option>
                        <option value="2" {{ request('rating_min') == '2' ? 'selected' : '' }}>2+ ⭐</option>
                        <option value="1" {{ request('rating_min') == '1' ? 'selected' : '' }}>1+ ⭐</option>
                    </select>
                </div>

                {{-- Sort Filter --}}
                <div class="col-md-6 col-lg-4">
                    <label for="sort" class="form-label fw-semibold">
                        {{ __('showcase.filter_sort') }}
                    </label>
                    <select name="sort" id="sort" class="form-select">
                        @foreach($searchFilters['sort_options'] as $option)
                        <option value="{{ $option['value'] }}"
                                {{ request('sort', 'newest') == $option['value'] ? 'selected' : '' }}>
                            {{ $option['label'] }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Feature Checkboxes --}}
            <div class="row g-3 mt-2">
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        {{ __('showcase.filter_features') }}
                    </label>
                    <div class="feature-checkboxes d-flex flex-wrap gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="has_cad_files" value="1"
                                    id="has_cad_files" {{ request('has_cad_files') ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_cad_files">
                                <i class="fas fa-file-alt me-1"></i>{{ __('showcase.has_cad_files') }}
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="has_tutorial" value="1"
                                    id="has_tutorial" {{ request('has_tutorial') ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_tutorial">
                                <i class="fas fa-graduation-cap me-1"></i>{{ __('showcase.has_tutorial') }}
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="allow_downloads" value="1"
                                    id="allow_downloads" {{ request('allow_downloads') ? 'checked' : '' }}>
                            <label class="form-check-label" for="allow_downloads">
                                <i class="fas fa-download me-1"></i>{{ __('showcase.allow_downloads') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>{{ __('showcase.search_button') }}
                    </button>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('showcase.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times-circle me-2"></i>{{ __('showcase.clear_filters') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- SECTION: SEARCH TAGS -->
    @if(request()->hasAny(['search', 'category', 'complexity', 'project_type', 'software', 'rating_min', 'sort']))
    <div class="search-tags-container mb-4 d-flex align-items-center justify-content-lg-between mt-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <span class="search-tags-label">
                <i class="fas fa-filter me-1"></i>{{ __('showcase.active_filters') }}:
            </span>

            @if(request('search'))
            <span class="search-tag">
                "{{ request('search') }}"
                <button type="button" class="search-tag-remove" onclick="removeSearchFilter('search')">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </span>
            @endif

            @if(request('category'))
            <span class="search-tag">
                {{ collect($searchFilters['categories'])->firstWhere('value', request('category'))['label'] ?? request('category') }}
                <button type="button" class="search-tag-remove" onclick="removeSearchFilter('category')">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </span>
            @endif

            @if(request('complexity'))
            <span class="search-tag">
                {{ collect($searchFilters['complexity_levels'])->firstWhere('value', request('complexity'))['label'] ?? request('complexity') }}
                <button type="button" class="search-tag-remove" onclick="removeSearchFilter('complexity')">
                     <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </span>
            @endif

            @if(request('project_type'))
            <span class="search-tag">
                {{ collect($searchFilters['project_types'])->firstWhere('value', request('project_type'))['label'] ?? request('project_type') }}
                <button type="button" class="search-tag-remove" onclick="removeSearchFilter('project_type')">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </span>
            @endif

            @if(request('software'))
            <span class="search-tag">
                {{ request('software') }}
                <button type="button" class="search-tag-remove" onclick="removeSearchFilter('software')">
                     <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </span>
            @endif

            @if(request('rating_min'))
            <span class="search-tag">
                {{ request('rating_min') }}+ {{ __('showcase.stars') }}
                <button type="button" class="search-tag-remove" onclick="removeSearchFilter('rating_min')">
                     <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </span>
            @endif

            @if(request('sort') && request('sort') !== 'newest')
            <span class="search-tag">
                @switch(request('sort'))
                    @case('most_viewed')
                        {{ __('showcase.most_viewed') }}
                        @break
                    @case('highest_rated')
                        {{ __('showcase.highest_rated') }}
                        @break
                    @case('most_downloads')
                        {{ __('showcase.most_downloads') }}
                        @break
                    @case('oldest')
                        {{ __('showcase.oldest') }}
                        @break
                    @default
                        {{ request('sort') }}
                @endswitch
                <button type="button" class="search-tag-remove" onclick="removeSearchFilter('sort')">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </span>
            @endif

            <button type="button" class="btn btn-outline-secondary btn-sm search-tag-remove-all" onclick="clearAllFilters()">
                <i class="fas fa-times-circle me-1"></i>{{ __('showcase.clear_all') }}
            </button>
        </div>
        @if(request()->hasAny(['search', 'category', 'complexity', 'project_type', 'software', 'rating_min']))
        <span class="badge bg-primary ms-3">{{ $allShowcases->total() }} {{ __('showcase.results') }}</span>
        @endif
    </div>
    @endif

</form>
