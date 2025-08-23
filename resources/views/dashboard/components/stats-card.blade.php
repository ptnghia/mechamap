<div class="card stats-card h-100">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="stats-icon me-3">
                <i class="{{ $icon }} fa-2x text-{{ $color }}"></i>
            </div>
            <div class="stats-content">
                <div class="stats-value">{{ $value }}</div>
                <div class="stats-title">{{ $title }}</div>
                @if(isset($change))
                    <div class="stats-change">
                        <i class="fas fa-{{ $change > 0 ? 'arrow-up text-success' : ($change < 0 ? 'arrow-down text-danger' : 'minus text-muted') }}"></i>
                        <span class="text-{{ $change > 0 ? 'success' : ($change < 0 ? 'danger' : 'muted') }}">
                            {{ abs($change) }}%
                        </span>
                        <small class="text-muted">{{ __('dashboard.vs_last_period') }}</small>
                    </div>
                @endif
            </div>
        </div>
        @if(isset($url))
            <div class="stats-action mt-3">
                <a href="{{ $url }}" class="btn btn-sm btn-outline-{{ $color }}">
                    {{ __('dashboard.view_details') }} <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.stats-card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.stats-icon {
    flex-shrink: 0;
}

.stats-content {
    flex: 1;
}

.stats-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #333;
    line-height: 1;
}

.stats-title {
    font-size: 0.9rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.stats-change {
    font-size: 0.8rem;
    margin-top: 0.5rem;
}

.stats-action {
    border-top: 1px solid #f8f9fa;
    padding-top: 0.75rem;
}
</style>
