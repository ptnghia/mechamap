<div class="quick-actions">
    <div class="d-grid gap-2">
        <!-- Community Actions -->
        <div class="action-group">
            <h6 class="action-group-title">{{ __('dashboard.community') }}</h6>
            <a href="{{ route('threads.create') }}" class="btn btn-outline-primary btn-sm w-100">
                <i class="fas fa-plus-circle me-2"></i>
                {{ __('dashboard.create_new_thread') }}
            </a>
            <a href="{{ route('showcase.create') }}" class="btn btn-outline-info btn-sm w-100">
                <i class="fas fa-star me-2"></i>
                {{ __('dashboard.create_showcase') }}
            </a>
            <a href="{{ route('forums.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                <i class="fas fa-comments me-2"></i>
                {{ __('dashboard.browse_forums') }}
            </a>
        </div>

        {{-- TODO: Implement marketplace permissions --}}
        {{-- <!-- Marketplace Actions (if user has permissions) -->
        @if($currentUser->hasAnyMarketplacePermission())
            <div class="action-group">
                <h6 class="action-group-title">Marketplace</h6>
                <a href="{{ route('marketplace.index') }}" class="btn btn-outline-success btn-sm w-100">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Browse Products
                </a>
                @if($currentUser->hasMarketplacePermission('buy'))
                    <a href="{{ route('dashboard.marketplace.wishlist') }}" class="btn btn-outline-warning btn-sm w-100">
                        <i class="fas fa-heart me-2"></i>
                        My Wishlist
                    </a>
                    <a href="{{ route('dashboard.marketplace.orders') }}" class="btn btn-outline-info btn-sm w-100">
                        <i class="fas fa-shopping-bag me-2"></i>
                        My Orders
                    </a>
                @endif
                @if($currentUser->hasMarketplacePermission('sell'))
                    <a href="{{ route('dashboard.marketplace.seller.products.create') }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-box me-2"></i>
                        Add New Product
                    </a>
                    <a href="{{ route('dashboard.marketplace.seller.dashboard') }}" class="btn btn-outline-dark btn-sm w-100">
                        <i class="fas fa-store me-2"></i>
                        Seller Dashboard
                    </a>
                @endif
            </div>
        @endif --}}

        <!-- Account Actions -->
        <div class="action-group">
            <h6 class="action-group-title">{{ __('dashboard.account') }}</h6>
            <a href="{{ route('dashboard.profile.edit') }}" class="btn btn-outline-secondary btn-sm w-100">
                <i class="fas fa-user-edit me-2"></i>
                {{ __('dashboard.edit_profile') }}
            </a>
            <a href="{{ route('dashboard.settings.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                <i class="fas fa-cog me-2"></i>
                {{ __('dashboard.settings') }}
            </a>
            <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                <i class="fas fa-bell me-2"></i>
                {{ __('dashboard.notifications') }}
                @if(isset($dashboardStats['notifications_unread']) && $dashboardStats['notifications_unread'] > 0)
                    <span class="badge bg-danger ms-1">{{ $dashboardStats['notifications_unread'] }}</span>
                @endif
            </a>
        </div>

        <!-- Help & Support -->
        <div class="action-group">
            <h6 class="action-group-title">{{ __('dashboard.support') }}</h6>
            <a href="{{ route('help.index') }}" class="btn btn-outline-info btn-sm w-100">
                <i class="fas fa-question-circle me-2"></i>
                {{ __('dashboard.documentation') }}
            </a>
            <a href="{{ route('contact') }}" class="btn btn-outline-info btn-sm w-100">
                <i class="fas fa-envelope me-2"></i>
                {{ __('dashboard.contact_support') }}
            </a>
        </div>
    </div>
</div>

<style>
.quick-actions {
    padding: 0;
}

.action-group {
    margin-bottom: 1.5rem;
}

.action-group:last-child {
    margin-bottom: 0;
}

.action-group-title {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #6c757d;
    margin-bottom: 0.75rem;
    padding-bottom: 0.25rem;
    border-bottom: 1px solid #e9ecef;
    letter-spacing: 0.5px;
}

.action-group .btn {
    margin-bottom: 0.5rem;
    text-align: left;
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
    transition: all 0.2s ease;
}

.action-group .btn:last-child {
    margin-bottom: 0;
}

.action-group .btn:hover {
    transform: translateX(2px);
}

.action-group .btn i {
    width: 16px;
    text-align: center;
}

.action-group .btn .badge {
    margin-left: auto;
}
</style>
