<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\Common\DashboardController;
use App\Http\Controllers\Dashboard\Common\ProfileController;
use App\Http\Controllers\Dashboard\Common\ActivityController;
use App\Http\Controllers\Dashboard\Common\NotificationController;
use App\Http\Controllers\Dashboard\Common\ConversationController;
use App\Http\Controllers\Dashboard\Common\SettingsController;
use App\Http\Controllers\Dashboard\Community\ThreadController;
use App\Http\Controllers\Dashboard\Community\BookmarkController;
use App\Http\Controllers\Dashboard\Community\CommentController;
use App\Http\Controllers\Dashboard\Community\ShowcaseController;
use App\Http\Controllers\Dashboard\Community\RatingController;
use App\Http\Controllers\Dashboard\Marketplace\OrderController;
use App\Http\Controllers\Dashboard\Marketplace\DownloadController;
use App\Http\Controllers\Dashboard\Marketplace\WishlistController;
use App\Http\Controllers\Dashboard\Marketplace\SellerController;
use App\Http\Controllers\Dashboard\Marketplace\ProductController;
use App\Http\Controllers\Dashboard\Marketplace\AnalyticsController;
use App\Http\Controllers\Dashboard\MessagesController;
use App\Http\Controllers\Dashboard\GroupConversationController;

// Import old controllers for backward compatibility
use App\Http\Controllers\ProfileController as OldProfileController;
use App\Http\Controllers\ConversationController as OldConversationController;
use App\Http\Controllers\BookmarkController as OldBookmarkController;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Tất cả routes cho dashboard của thành viên đã đăng nhập
| Được tổ chức theo nhóm chức năng: Common, Community, Marketplace
|
*/

// Main dashboard redirect
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified.social'])
    ->name('dashboard');

// =============================================================================
// COMMON DASHBOARD ROUTES (Dành cho mọi thành viên)
// =============================================================================
Route::middleware(['auth', 'verified.social'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {

        // Profile management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
            Route::patch('/', [ProfileController::class, 'update'])->name('update');
            Route::patch('/details', [ProfileController::class, 'updateDetails'])->name('update-details');
            Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');
            Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('delete-avatar');
            Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password');
            Route::get('/delete', [ProfileController::class, 'showDeleteForm'])->name('delete');
            Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
            Route::get('/stats', [ProfileController::class, 'stats'])->name('stats');
        });

        // Activity & Social
        Route::get('/activity', [ActivityController::class, 'index'])->name('activity');
        // Note: Following/Followers functionality will be implemented later

        // Communications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/{notification}/unread', [NotificationController::class, 'markAsUnread'])->name('unread');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{notification}', [NotificationController::class, 'delete'])->name('delete');
            Route::delete('/', [NotificationController::class, 'clearAll'])->name('clear-all');
            Route::post('/{notification}/archive', [NotificationController::class, 'archive'])->name('archive');
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
            Route::get('/dropdown', [NotificationController::class, 'dropdown'])->name('dropdown');

            // Bulk operations
            Route::post('/bulk', [NotificationController::class, 'bulk'])->name('bulk');

            // Archive functionality
            Route::get('/archive', [NotificationController::class, 'archiveIndex'])->name('archive');
            Route::patch('/{notification}/restore', [NotificationController::class, 'restore'])->name('restore');
            Route::post('/restore-all', [NotificationController::class, 'restoreAll'])->name('restore-all');
            Route::delete('/delete-all-archived', [NotificationController::class, 'deleteAllArchived'])->name('delete-all-archived');
        });

        // Messages System (thay thế conversations cũ)
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [MessagesController::class, 'index'])->name('index');
            Route::get('/create', [MessagesController::class, 'create'])->name('create');
            Route::post('/', [MessagesController::class, 'store'])->name('store');

            // Group Conversations - Must be before /{conversation} route
            Route::prefix('groups')->name('groups.')->group(function () {
                Route::get('/', [GroupConversationController::class, 'index'])->name('index');
                Route::get('/create', [GroupConversationController::class, 'create'])->name('create');
                Route::post('/request', [GroupConversationController::class, 'submitRequest'])->name('request');

                // Group Management
                Route::get('/{conversation}/settings', [GroupConversationController::class, 'settings'])->name('settings');
                Route::put('/{conversation}/settings', [GroupConversationController::class, 'updateSettings'])->name('settings.update');

                // Member Management
                Route::post('/{conversation}/members', [GroupConversationController::class, 'addMember'])->name('members.add');
                Route::delete('/{conversation}/members/{user}', [GroupConversationController::class, 'removeMember'])->name('members.remove');
                Route::patch('/{conversation}/members/{user}/role', [GroupConversationController::class, 'changeMemberRole'])->name('members.role');

                // Group Actions
                Route::post('/{conversation}/leave', [GroupConversationController::class, 'leaveGroup'])->name('leave');
                Route::post('/{conversation}/transfer-ownership', [GroupConversationController::class, 'transferOwnership'])->name('transfer-ownership');

                // AJAX endpoints
                Route::get('/search-users', [GroupConversationController::class, 'searchUsers'])->name('search-users');

                // WebSocket test page
                Route::get('/websocket-test', function () {
                    $groups = \App\Models\Conversation::where('is_group', true)
                        ->whereHas('groupMembers', function ($query) {
                            $query->where('user_id', auth()->id())
                                  ->where('is_active', true);
                        })
                        ->get();

                    return view('groups.websocket-test', compact('groups'));
                })->name('websocket-test');
            });

            // Individual conversation routes - Must be after groups
            Route::get('/{conversation}', [MessagesController::class, 'show'])->name('show');
            Route::post('/{conversation}/send', [MessagesController::class, 'sendMessage'])->name('send');
            Route::get('/{conversation}/messages', [MessagesController::class, 'getMessages'])->name('get-messages');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::patch('/preferences', [SettingsController::class, 'updatePreferences'])->name('preferences');
            Route::patch('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications');
            Route::patch('/privacy', [SettingsController::class, 'updatePrivacy'])->name('privacy');
            Route::patch('/security', [SettingsController::class, 'updateSecurity'])->name('security');
            Route::get('/export-data', [SettingsController::class, 'exportData'])->name('export-data');
            Route::post('/deactivate', [SettingsController::class, 'deactivateAccount'])->name('deactivate');
            Route::post('/reset-defaults', [SettingsController::class, 'resetToDefaults'])->name('reset-defaults');
            Route::get('/download-data', [SettingsController::class, 'downloadData'])->name('download-data');
        });
    });

// =============================================================================
// COMMUNITY DASHBOARD ROUTES (Forum & Showcase)
// =============================================================================
Route::middleware(['auth'])
    ->prefix('dashboard/community')
    ->name('dashboard.community.')
    ->group(function () {

        // Thread management
        Route::prefix('threads')->name('threads.')->group(function () {
            Route::get('/', [ThreadController::class, 'index'])->name('index');
            Route::get('/followed', [ThreadController::class, 'followedThreads'])->name('followed');
            Route::get('/participated', [ThreadController::class, 'participatedThreads'])->name('participated');
            Route::get('/data', [ThreadController::class, 'getThreadsData'])->name('data');
            Route::post('/bulk-action', [ThreadController::class, 'bulkAction'])->name('bulk-action');
        });

        // Bookmark management
        Route::prefix('bookmarks')->name('bookmarks.')->group(function () {
            Route::get('/', [BookmarkController::class, 'index'])->name('index');
            Route::post('/folders', [BookmarkController::class, 'createFolder'])->name('folders.create');
            Route::patch('/folders/{folder}', [BookmarkController::class, 'updateFolder'])->name('folders.update');
            Route::delete('/folders/{folder}', [BookmarkController::class, 'deleteFolder'])->name('folders.delete');
            Route::patch('/{bookmark}', [BookmarkController::class, 'updateBookmark'])->name('update');
            Route::delete('/{bookmark}', [BookmarkController::class, 'deleteBookmark'])->name('delete');
            Route::delete('/', [BookmarkController::class, 'bulkDeleteBookmarks'])->name('bulk-delete');
            Route::get('/export', [BookmarkController::class, 'exportBookmarks'])->name('export');
        });

        // Comment management
        Route::prefix('comments')->name('comments.')->group(function () {
            Route::get('/', [CommentController::class, 'index'])->name('index');
            Route::get('/{comment}', [CommentController::class, 'show'])->name('show');
            Route::get('/{comment}/edit', [CommentController::class, 'edit'])->name('edit');
            Route::patch('/{comment}', [CommentController::class, 'update'])->name('update');
            Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-action', [CommentController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/export', [CommentController::class, 'exportComments'])->name('export');
        });

        // Showcase management
        Route::prefix('showcases')->name('showcases.')->group(function () {
            Route::get('/', [ShowcaseController::class, 'index'])->name('index');
            Route::get('/create', [ShowcaseController::class, 'create'])->name('create');
            Route::post('/', [ShowcaseController::class, 'store'])->name('store');
            Route::get('/{showcase}', [ShowcaseController::class, 'show'])->name('show');
            Route::get('/{showcase}/edit', [ShowcaseController::class, 'edit'])->name('edit');
            Route::patch('/{showcase}', [ShowcaseController::class, 'update'])->name('update');
            Route::delete('/{showcase}', [ShowcaseController::class, 'destroy'])->name('destroy');
        });

        // Rating management
        Route::prefix('ratings')->name('ratings.')->group(function () {
            Route::get('/', [RatingController::class, 'index'])->name('index');
            Route::get('/{type}/{id}/edit', [RatingController::class, 'edit'])->name('edit');
            Route::patch('/{type}/{id}', [RatingController::class, 'update'])->name('update');
            Route::delete('/{type}/{id}', [RatingController::class, 'destroy'])->name('destroy');
        });
    });

// =============================================================================
// MARKETPLACE DASHBOARD ROUTES (Có quyền marketplace)
// =============================================================================
Route::middleware(['auth', 'marketplace.permission'])
    ->prefix('dashboard/marketplace')
    ->name('dashboard.marketplace.')
    ->group(function () {

        // Order management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            Route::get('/{order}/tracking', [OrderController::class, 'tracking'])->name('tracking');
            Route::post('/{order}/reorder', [OrderController::class, 'reorder'])->name('reorder');
            Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
            Route::get('/export', [OrderController::class, 'exportOrders'])->name('export');
        });

        // Download management
        Route::prefix('downloads')->name('downloads.')->group(function () {
            Route::get('/', [DownloadController::class, 'index'])->name('index');
            Route::get('/order-files/{orderItem}', [DownloadController::class, 'showOrderFiles'])->name('order-files');
            Route::post('/token/{orderItem}', [DownloadController::class, 'createDownloadToken'])->name('token');
            Route::get('/redownload/{download}', [DownloadController::class, 'redownload'])->name('redownload');
            Route::delete('/{download}', [DownloadController::class, 'deleteDownload'])->name('delete');
        });

        // Wishlist management
        Route::prefix('wishlist')->name('wishlist.')->group(function () {
            Route::get('/', [WishlistController::class, 'index'])->name('index');
            Route::post('/add', [WishlistController::class, 'add'])->name('add');
            Route::delete('/{wishlistItem}', [WishlistController::class, 'remove'])->name('remove');
            Route::delete('/', [WishlistController::class, 'bulkRemove'])->name('bulk-remove');
            Route::post('/add-all-to-cart', [WishlistController::class, 'addAllToCart'])->name('add-all-to-cart');
            Route::get('/check-product', [WishlistController::class, 'checkProduct'])->name('check-product');
        });

        // Seller dashboard (cho roles có quyền bán)
        Route::middleware('marketplace.permission:sell')
            ->prefix('seller')
            ->name('seller.')
            ->group(function () {

                Route::get('/', [SellerController::class, 'dashboard'])->name('dashboard');
                Route::get('/setup', [SellerController::class, 'setup'])->name('setup');
                Route::post('/setup', [SellerController::class, 'storeSetup'])->name('setup.store');
                Route::get('/orders', [SellerController::class, 'orders'])->name('orders');
                Route::patch('/orders/{orderItem}/status', [SellerController::class, 'updateOrderStatus'])->name('orders.status');
                Route::get('/export', [SellerController::class, 'exportData'])->name('export');

                // Product management
                Route::prefix('products')->name('products.')->group(function () {
                    Route::get('/', [ProductController::class, 'index'])->name('index');
                    Route::get('/create', [ProductController::class, 'create'])->name('create');
                    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
                    Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
                    Route::patch('/{product}/status', [ProductController::class, 'updateStatus'])->name('status');
                    Route::post('/{product}/duplicate', [ProductController::class, 'duplicate'])->name('duplicate');
                    Route::post('/bulk-action', [ProductController::class, 'bulkAction'])->name('bulk-action');
                });

                // Analytics
                Route::prefix('analytics')->name('analytics.')->group(function () {
                    Route::get('/', [AnalyticsController::class, 'index'])->name('index');
                    Route::get('/sales-data', [AnalyticsController::class, 'getSalesData'])->name('sales-data');
                    Route::get('/product-performance', [AnalyticsController::class, 'getProductPerformance'])->name('product-performance');
                });
            });
    });

// =============================================================================
// BACKWARD COMPATIBILITY ROUTES
// =============================================================================
// Maintain old route names for backward compatibility with existing views/links

Route::middleware(['auth', 'verified.social'])->group(function () {
    // Old profile route names → Dashboard profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

    // Old notification route names → Dashboard notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/{notification}/unread', [NotificationController::class, 'markAsUnread'])->name('notifications.unread');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'delete'])->name('notifications.delete');
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::patch('/notifications/{notification}/archive', [NotificationController::class, 'archive'])->name('notifications.archive');

    // Old conversation route names → Dashboard messages routes
    Route::get('/conversations', [MessagesController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/{conversation}', [MessagesController::class, 'show'])->name('conversations.show');
    Route::post('/conversations', [MessagesController::class, 'store'])->name('conversations.store');
    Route::post('/conversations/{conversation}/messages', [MessagesController::class, 'sendMessage'])->name('conversations.messages.store');

    // Old bookmark route names → Dashboard bookmark routes
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::delete('/bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');
});
