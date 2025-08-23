<div class="activity-feed">
    @if($activities->count() > 0)
        @foreach($activities as $activity)
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">
                        <a href="{{ $activity['url'] }}" class="text-decoration-none">
                            {{ $activity['title'] }}
                        </a>
                    </div>
                    @if(isset($activity['description']))
                        <div class="activity-description">
                            {{ $activity['description'] }}
                        </div>
                    @endif
                    <div class="activity-time">
                        {{ $activity['created_at']->diffForHumans() }}
                    </div>
                </div>
                <div class="activity-actions">
                    <a href="{{ $activity['url'] }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center py-4 text-muted">
            <i class="fas fa-clock fa-2x mb-3 opacity-50"></i>
            <p>No recent activity to show.</p>
        </div>
    @endif
</div>

<style>
.activity-feed {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 50%;
    margin-right: 1rem;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.activity-title a {
    color: #333;
}

.activity-title a:hover {
    color: #007bff;
}

.activity-description {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.activity-time {
    font-size: 0.8rem;
    color: #adb5bd;
}

.activity-actions {
    flex-shrink: 0;
    margin-left: 1rem;
}

/* Scrollbar styling */
.activity-feed::-webkit-scrollbar {
    width: 6px;
}

.activity-feed::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.activity-feed::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.activity-feed::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
