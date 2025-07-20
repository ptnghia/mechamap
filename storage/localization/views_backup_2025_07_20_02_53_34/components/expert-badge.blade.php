@props([
    'badge',
    'user' => null,
    'size' => 'md',
    'showTooltip' => true,
    'showProgress' => false,
    'clickable' => false
])

@php
$sizeClasses = [
    'xs' => 'width: 20px; height: 20px;',
    'sm' => 'width: 24px; height: 24px;',
    'md' => 'width: 32px; height: 32px;',
    'lg' => 'width: 48px; height: 48px;',
    'xl' => 'width: 64px; height: 64px;',
];

$badgeStyle = $sizeClasses[$size] ?? $sizeClasses['md'];

// Check if user has this badge
$userHasBadge = $user && $badge->users()->where('user_id', $user->id)->exists();
$userBadgeData = null;
$progress = null;

if ($user) {
    if ($userHasBadge) {
        $userBadgeData = $badge->users()->where('user_id', $user->id)->first()->pivot;
    } elseif ($showProgress) {
        $progress = $badge->getProgressForUser($user);
    }
}

$badgeClasses = [
    'expert-badge',
    'position-relative',
    'd-inline-block',
    $userHasBadge ? 'badge-earned' : 'badge-not-earned',
    $clickable ? 'badge-clickable' : '',
    'rarity-' . $badge->rarity,
];

$tooltipContent = $badge->description;
if ($userHasBadge && $userBadgeData) {
    $tooltipContent .= '<br><small class="text-muted">Earned: ' . $userBadgeData->awarded_at->format('M d, Y') . '</small>';
    if ($userBadgeData->verified_at) {
        $tooltipContent .= '<br><small class="text-success">✓ Verified</small>';
    } elseif ($badge->verification_required) {
        $tooltipContent .= '<br><small class="text-warning">⏳ Pending Verification</small>';
    }
} elseif ($progress && $showProgress) {
    $tooltipContent .= '<br><small class="text-info">Progress: ' . $progress['progress'] . '%</small>';
}
@endphp

<div class="{{ implode(' ', $badgeClasses) }}"
     @if($showTooltip)
     data-bs-toggle="tooltip"
     data-bs-placement="top"
     data-bs-html="true"
     title="{{ $tooltipContent }}"
     @endif
     @if($clickable)
     role="button"
     tabindex="0"
     @endif>
    
    <!-- Badge Icon -->
    <div class="badge-icon" style="{{ $badgeStyle }}">
        <img src="{{ $badge->icon_url }}" 
             alt="{{ $badge->name }}" 
             class="img-fluid rounded"
             style="width: 100%; height: 100%; object-fit: cover;">
        
        <!-- Rarity Glow Effect -->
        @if(in_array($badge->rarity, ['epic', 'legendary', 'mythic']))
        <div class="badge-glow" style="
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            border-radius: 50%;
            background: linear-gradient(45deg, {{ $badge->rarity_color }}, transparent);
            z-index: -1;
            opacity: 0.6;
        "></div>
        @endif
        
        <!-- Verification Status -->
        @if($userHasBadge && $userBadgeData)
            @if($userBadgeData->verified_at)
            <div class="verification-status verified" style="
                position: absolute;
                top: -4px;
                right: -4px;
                width: 16px;
                height: 16px;
                background: #28a745;
                border: 2px solid white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 8px;
                color: white;
            ">
                <i class="bx bx-check"></i>
            </div>
            @elseif($badge->verification_required)
            <div class="verification-status pending" style="
                position: absolute;
                top: -4px;
                right: -4px;
                width: 16px;
                height: 16px;
                background: #ffc107;
                border: 2px solid white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 8px;
                color: white;
            ">
                <i class="bx bx-time"></i>
            </div>
            @endif
        @endif
        
        <!-- Progress Ring (if showing progress) -->
        @if($progress && $showProgress && !$userHasBadge)
        <svg class="progress-ring" style="
            position: absolute;
            top: -2px;
            left: -2px;
            width: calc(100% + 4px);
            height: calc(100% + 4px);
            transform: rotate(-90deg);
        ">
            <circle cx="50%" cy="50%" r="45%" 
                    fill="none" 
                    stroke="#e9ecef" 
                    stroke-width="2"/>
            <circle cx="50%" cy="50%" r="45%" 
                    fill="none" 
                    stroke="{{ $badge->rarity_color }}" 
                    stroke-width="2"
                    stroke-dasharray="{{ 2 * pi() * 45 }}"
                    stroke-dashoffset="{{ 2 * pi() * 45 * (1 - $progress['progress'] / 100) }}"
                    style="transition: stroke-dashoffset 0.3s ease;"/>
        </svg>
        @endif
    </div>
    
    <!-- Badge Type Indicator -->
    @if($size === 'lg' || $size === 'xl')
    <div class="badge-type" style="
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        background: {{ $badge->rarity_color }};
        color: white;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    ">
        {{ $badge->type }}
    </div>
    @endif
</div>

@if($showProgress && $progress && !$userHasBadge && ($size === 'lg' || $size === 'xl'))
<!-- Progress Details -->
<div class="badge-progress mt-2">
    <div class="progress" style="height: 4px;">
        <div class="progress-bar" 
             role="progressbar" 
             style="width: {{ $progress['progress'] }}%; background-color: {{ $badge->rarity_color }};"
             aria-valuenow="{{ $progress['progress'] }}" 
             aria-valuemin="0" 
             aria-valuemax="100">
        </div>
    </div>
    <small class="text-muted">{{ $progress['progress'] }}% {{ __('badges.complete') }}</small>
</div>
@endif

@push('styles')
<style>
.expert-badge {
    transition: all 0.3s ease;
}

.expert-badge.badge-clickable:hover {
    transform: scale(1.1);
    cursor: pointer;
}

.expert-badge.badge-not-earned {
    opacity: 0.5;
    filter: grayscale(50%);
}

.expert-badge.badge-earned {
    opacity: 1;
    filter: none;
}

.expert-badge.rarity-legendary .badge-icon {
    animation: legendary-glow 2s ease-in-out infinite alternate;
}

.expert-badge.rarity-mythic .badge-icon {
    animation: mythic-pulse 1.5s ease-in-out infinite;
}

@keyframes legendary-glow {
    from {
        box-shadow: 0 0 5px rgba(253, 126, 20, 0.5);
    }
    to {
        box-shadow: 0 0 20px rgba(253, 126, 20, 0.8);
    }
}

@keyframes mythic-pulse {
    0%, 100% {
        box-shadow: 0 0 5px rgba(220, 53, 69, 0.5);
        transform: scale(1);
    }
    50% {
        box-shadow: 0 0 25px rgba(220, 53, 69, 0.8);
        transform: scale(1.05);
    }
}

.badge-glow {
    animation: glow-rotate 3s linear infinite;
}

@keyframes glow-rotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips for badges
    var badgeTooltips = [].slice.call(document.querySelectorAll('.expert-badge[data-bs-toggle="tooltip"]'));
    badgeTooltips.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Handle clickable badges
    document.querySelectorAll('.expert-badge.badge-clickable').forEach(function(badge) {
        badge.addEventListener('click', function() {
            // You can add custom click handling here
            console.log('Badge clicked:', this.querySelector('img').alt);
        });
        
        badge.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                this.click();
            }
        });
    });
});
</script>
@endpush
