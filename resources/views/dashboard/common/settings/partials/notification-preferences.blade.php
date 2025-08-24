<!-- Enhanced Notification Preferences -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-bell me-2"></i>
            {{ __('settings.notifications.title') }}
        </h5>
        <p class="text-muted mb-0 mt-1">{{ __('settings.notifications.description') }}</p>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('dashboard.settings.notifications') }}" id="notification-preferences-form">
            @csrf
            @method('PATCH')

            <!-- Global Settings -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">
                    <i class="fas fa-globe me-2"></i>
                    {{ __('settings.notifications.global_settings') }}
                </h6>
                <div class="row">
                    @foreach($deliveryMethods as $methodKey => $method)
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input global-toggle"
                                   type="checkbox"
                                   id="global_{{ $methodKey }}"
                                   name="global[{{ $methodKey }}_enabled]"
                                   value="1"
                                   data-method="{{ $methodKey }}"
                                   {{ $userPreferences['global'][$methodKey . '_enabled'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="global_{{ $methodKey }}">
                                <i class="{{ $method['icon'] }} me-2"></i>
                                {{ $method['name'] }}
                            </label>
                        </div>
                        <small class="text-muted">{{ $method['description'] }}</small>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Category-based Preferences -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">
                    <i class="fas fa-layer-group me-2"></i>
                    {{ __('settings.notifications.category_preferences') }}
                </h6>

                <div class="accordion" id="notificationAccordion">
                    @foreach($notificationCategories as $categoryKey => $category)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ ucfirst($categoryKey) }}">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ ucfirst($categoryKey) }}" 
                                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                                    aria-controls="collapse{{ ucfirst($categoryKey) }}">
                                <i class="{{ $category['icon'] }} text-{{ $category['color'] }} me-2"></i>
                                {{ $category['name'] }}
                                <span class="badge bg-{{ $category['color'] }} ms-2">{{ count($category['types']) }}</span>
                            </button>
                        </h2>
                        <div id="collapse{{ ucfirst($categoryKey) }}" 
                             class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                             aria-labelledby="heading{{ ucfirst($categoryKey) }}" 
                             data-bs-parent="#notificationAccordion">
                            <div class="accordion-body">
                                <!-- Category Toggle -->
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input category-toggle"
                                           type="checkbox"
                                           id="category_{{ $categoryKey }}"
                                           name="categories[{{ $categoryKey }}][enabled]"
                                           value="1"
                                           data-category="{{ $categoryKey }}"
                                           {{ $userPreferences['categories'][$categoryKey]['enabled'] ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="category_{{ $categoryKey }}">
                                        {{ __('settings.notifications.enable_category', ['category' => $category['name']]) }}
                                    </label>
                                </div>

                                <!-- Notification Types -->
                                <div class="notification-types" data-category="{{ $categoryKey }}">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('settings.notifications.notification_type') }}</th>
                                                    @foreach($deliveryMethods as $methodKey => $method)
                                                    <th class="text-center">
                                                        <i class="{{ $method['icon'] }}" title="{{ $method['name'] }}"></i>
                                                    </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($category['types'] as $typeKey => $typeName)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $typeName }}</strong>
                                                    </td>
                                                    @foreach($deliveryMethods as $methodKey => $method)
                                                    <td class="text-center">
                                                        <div class="form-check form-switch d-inline-block">
                                                            <input class="form-check-input type-toggle"
                                                                   type="checkbox"
                                                                   id="{{ $categoryKey }}_{{ $typeKey }}_{{ $methodKey }}"
                                                                   name="categories[{{ $categoryKey }}][types][{{ $typeKey }}][{{ $methodKey }}]"
                                                                   value="1"
                                                                   data-category="{{ $categoryKey }}"
                                                                   data-type="{{ $typeKey }}"
                                                                   data-method="{{ $methodKey }}"
                                                                   {{ $userPreferences['categories'][$categoryKey]['types'][$typeKey][$methodKey] ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="{{ $categoryKey }}_{{ $typeKey }}_{{ $methodKey }}"></label>
                                                        </div>
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Delivery Method Settings -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">
                    <i class="fas fa-cog me-2"></i>
                    {{ __('settings.notifications.delivery_settings') }}
                </h6>

                <div class="row">
                    @foreach($deliveryMethods as $methodKey => $method)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="{{ $method['icon'] }} me-2"></i>
                                    {{ $method['name'] }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Frequency -->
                                <div class="mb-3">
                                    <label for="frequency_{{ $methodKey }}" class="form-label">
                                        {{ __('settings.notifications.frequency') }}
                                    </label>
                                    <select class="form-select" 
                                            id="frequency_{{ $methodKey }}"
                                            name="delivery_methods[{{ $methodKey }}][frequency]">
                                        @foreach($frequencyOptions as $value => $label)
                                        <option value="{{ $value }}" 
                                                {{ $userPreferences['delivery_methods'][$methodKey]['frequency'] === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                @if($methodKey === 'push' || $methodKey === 'email')
                                <!-- Quiet Hours -->
                                <div class="mb-3">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="quiet_hours_{{ $methodKey }}"
                                               name="delivery_methods[{{ $methodKey }}][quiet_hours][enabled]"
                                               value="1"
                                               {{ $userPreferences['delivery_methods'][$methodKey]['quiet_hours']['enabled'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="quiet_hours_{{ $methodKey }}">
                                            {{ __('settings.notifications.quiet_hours') }}
                                        </label>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="quiet_start_{{ $methodKey }}" class="form-label small">
                                                {{ __('settings.notifications.start_time') }}
                                            </label>
                                            <input type="time" 
                                                   class="form-control form-control-sm"
                                                   id="quiet_start_{{ $methodKey }}"
                                                   name="delivery_methods[{{ $methodKey }}][quiet_hours][start]"
                                                   value="{{ $userPreferences['delivery_methods'][$methodKey]['quiet_hours']['start'] }}">
                                        </div>
                                        <div class="col-6">
                                            <label for="quiet_end_{{ $methodKey }}" class="form-label small">
                                                {{ __('settings.notifications.end_time') }}
                                            </label>
                                            <input type="time" 
                                                   class="form-control form-control-sm"
                                                   id="quiet_end_{{ $methodKey }}"
                                                   name="delivery_methods[{{ $methodKey }}][quiet_hours][end]"
                                                   value="{{ $userPreferences['delivery_methods'][$methodKey]['quiet_hours']['end'] }}">
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">
                    <i class="fas fa-magic me-2"></i>
                    {{ __('settings.notifications.quick_actions') }}
                </h6>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary" id="enable-all">
                        <i class="fas fa-check-circle me-1"></i>
                        {{ __('settings.notifications.enable_all') }}
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="disable-all">
                        <i class="fas fa-times-circle me-1"></i>
                        {{ __('settings.notifications.disable_all') }}
                    </button>
                    <button type="button" class="btn btn-outline-info" id="reset-defaults">
                        <i class="fas fa-undo me-1"></i>
                        {{ __('settings.notifications.reset_defaults') }}
                    </button>
                </div>
            </div>

            <!-- Save Button -->
            <div class="d-flex align-items-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>
                    {{ __('settings.notifications.save_preferences') }}
                </button>

                @if (session('success'))
                    <div class="ms-3">
                        <span class="text-success">
                            <i class="fas fa-check me-1"></i>
                            {{ session('success') }}
                        </span>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/notification-preferences.js') }}?v={{ filemtime(public_path('js/notification-preferences.js')) }}"></script>
@endpush
