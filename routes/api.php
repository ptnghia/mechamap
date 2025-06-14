<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Direct test route outside of all middleware
Route::get('/marketplace-test', function() {
    return response()->json([
        'success' => true,
        'message' => 'Direct marketplace test working',
        'database' => config('database.default'),
        'env' => app()->environment()
    ]);
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API version 1
Route::prefix('v1')->group(function () {
    // Test CORS for v1 API
    Route::get('/cors-test', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'CORS test successful',
            'origin' => $request->header('Origin'),
            'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'https://mechamap.test,http://mechamap.test')),
        ]);
    });

    // Public routes
    Route::get('/settings', [App\Http\Controllers\Api\SettingsController::class, 'index']);
    Route::get('/settings/{group}', [App\Http\Controllers\Api\SettingsController::class, 'getByGroup']);

    // Geography API routes (public)
    Route::prefix('geography')->group(function () {
        Route::get('/countries', [App\Http\Controllers\Api\GeographyController::class, 'countries']);
        Route::get('/countries/{code}', [App\Http\Controllers\Api\GeographyController::class, 'country']);
        Route::get('/countries/{countryCode}/regions', [App\Http\Controllers\Api\GeographyController::class, 'regionsByCountry']);
        Route::get('/regions/featured', [App\Http\Controllers\Api\GeographyController::class, 'featuredRegions']);
        Route::get('/regions/{region}', [App\Http\Controllers\Api\GeographyController::class, 'region']);
        Route::get('/regions/{region}/forums', [App\Http\Controllers\Api\GeographyController::class, 'forumsByRegion']);
        Route::get('/continents', [App\Http\Controllers\Api\GeographyController::class, 'continents']);
        Route::get('/standards', [App\Http\Controllers\Api\GeographyController::class, 'standardsByLocation']);
        Route::get('/cad-software', [App\Http\Controllers\Api\GeographyController::class, 'cadSoftwareByLocation']);
    });

    // Professional Sidebar API routes
    Route::prefix('sidebar')->group(function () {
        // Public sidebar data
        Route::get('/stats', [App\Http\Controllers\Api\SidebarController::class, 'getStats']);
        Route::get('/trending', [App\Http\Controllers\Api\SidebarController::class, 'getTrendingTopics']);
        Route::get('/data', [App\Http\Controllers\Api\SidebarController::class, 'getSidebarData']);

        // Protected routes (require authentication)
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/recommendations', [App\Http\Controllers\Api\SidebarController::class, 'getRecommendations']);
            Route::post('/track', [App\Http\Controllers\Api\SidebarController::class, 'trackInteraction']);
        });
    });

    // Auth routes (public)
    Route::prefix('auth')->group(function () {
        Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
        Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
        Route::post('/forgot-password', [App\Http\Controllers\Api\AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [App\Http\Controllers\Api\AuthController::class, 'resetPassword']);
        Route::post('/verify-email', [App\Http\Controllers\Api\AuthController::class, 'verifyEmail']);
        Route::post('/social/{provider}', [App\Http\Controllers\Api\AuthController::class, 'socialLogin']);
    });

    // Users routes (public)
    Route::prefix('users')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\UserController::class, 'index']);
        Route::get('/{username}', [App\Http\Controllers\Api\UserController::class, 'show']);
        Route::get('/{username}/threads', [App\Http\Controllers\Api\UserController::class, 'getThreads']);
        Route::get('/{username}/comments', [App\Http\Controllers\Api\UserController::class, 'getComments']);
        Route::get('/{username}/activities', [App\Http\Controllers\Api\UserController::class, 'getActivities']);
        Route::get('/{username}/followers', [App\Http\Controllers\Api\UserController::class, 'getFollowers']);
        Route::get('/{username}/following', [App\Http\Controllers\Api\UserController::class, 'getFollowing']);
    });

    // Forums routes (public)
    Route::prefix('forums')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ForumController::class, 'index']);
        Route::get('/{slug}', [App\Http\Controllers\Api\ForumController::class, 'show']);
        Route::get('/{slug}/threads', [App\Http\Controllers\Api\ForumController::class, 'getThreads']);
    });

    // Categories routes (public)
    Route::prefix('categories')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ForumController::class, 'getCategories']);
        Route::get('/{slug}', [App\Http\Controllers\Api\ForumController::class, 'getCategory']);
        Route::get('/{slug}/forums', [App\Http\Controllers\Api\ForumController::class, 'getCategoryForums']);
    });

    // Tags routes (public)
    Route::prefix('tags')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\TagController::class, 'index']);
        Route::get('/{slug}', [App\Http\Controllers\Api\TagController::class, 'show']);
        Route::get('/{slug}/threads', [App\Http\Controllers\Api\TagController::class, 'getThreads']);
    });

    // Threads routes (public)
    Route::prefix('threads')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ThreadController::class, 'index']);
        Route::get('/featured', [App\Http\Controllers\Api\ThreadController::class, 'getFeatured']);
        Route::get('/trending', [App\Http\Controllers\Api\ThreadController::class, 'getTrending']);
        Route::get('/latest', [App\Http\Controllers\Api\ThreadController::class, 'getLatest']);
        Route::get('/{slug}', [App\Http\Controllers\Api\ThreadController::class, 'show']);
        Route::get('/{slug}/comments', [App\Http\Controllers\Api\ThreadController::class, 'getComments']);
    });

    // Comments routes (public)
    Route::prefix('comments')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\CommentController::class, 'index']);
        Route::get('/{id}', [App\Http\Controllers\Api\CommentController::class, 'show']);
        Route::get('/{id}/replies', [App\Http\Controllers\Api\CommentController::class, 'getReplies']);
    });

    // Search routes (public)
    Route::prefix('search')->group(function () {
        Route::get('/threads', [App\Http\Controllers\Api\SearchController::class, 'searchThreads']);
        Route::get('/users', [App\Http\Controllers\Api\SearchController::class, 'searchUsers']);
        Route::get('/tags', [App\Http\Controllers\Api\SearchController::class, 'searchTags']);
        Route::get('/forums', [App\Http\Controllers\Api\SearchController::class, 'searchForums']);
        Route::get('/global', [App\Http\Controllers\Api\SearchController::class, 'globalSearch']);
        Route::get('/suggestions', [App\Http\Controllers\Api\SearchController::class, 'getSearchSuggestions']);
    });

    // Marketplace API routes (Public access for browsing)
    Route::prefix('marketplace')->group(function () {
        // Test endpoints
        Route::get('/test', function() {
            return response()->json([
                'success' => true,
                'message' => 'Marketplace API is working',
                'timestamp' => now(),
                'database_connection' => config('database.default')
            ]);
        });

        Route::get('/test-db', function() {
            try {
                $categories = \App\Models\ProductCategory::count();
                $products = \App\Models\TechnicalProduct::count();

                return response()->json([
                    'success' => true,
                    'message' => 'Database connection successful',
                    'data' => [
                        'categories_count' => $categories,
                        'products_count' => $products,
                        'connection' => config('database.default')
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database connection failed',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        });

        // Public product browsing
        Route::get('/products', [App\Http\Controllers\Api\MarketplaceController::class, 'index']);
        Route::get('/products/{slug}', [App\Http\Controllers\Api\MarketplaceController::class, 'show']);
        Route::get('/categories', [App\Http\Controllers\Api\MarketplaceController::class, 'categories']);
        Route::get('/search', [App\Http\Controllers\Api\MarketplaceController::class, 'search']);
        Route::get('/featured', [App\Http\Controllers\Api\MarketplaceController::class, 'featured']);
        Route::get('/bestsellers', [App\Http\Controllers\Api\MarketplaceController::class, 'bestsellers']);
    });

    // Payment webhooks and callbacks (public - no auth required)
    Route::prefix('payment')->group(function () {
        Route::get('/methods', [App\Http\Controllers\Api\PaymentController::class, 'paymentMethods']);
        Route::post('/stripe/webhook', [App\Http\Controllers\Api\PaymentController::class, 'webhook'])
            ->middleware('stripe.webhook');
        Route::get('/vnpay/callback', [App\Http\Controllers\Api\PaymentController::class, 'vnpayCallback']);
        Route::post('/vnpay/ipn', [App\Http\Controllers\Api\PaymentController::class, 'vnpayIpn']);
    });

    // Payment Testing Routes (Development only)
    Route::prefix('payment/test')->group(function () {
        Route::get('/configurations', [App\Http\Controllers\Api\PaymentTestController::class, 'testConfigurations']);
        Route::post('/create-order', [App\Http\Controllers\Api\PaymentTestController::class, 'createTestOrder']);
        Route::post('/stripe', [App\Http\Controllers\Api\PaymentTestController::class, 'testStripePayment']);
        Route::post('/vnpay', [App\Http\Controllers\Api\PaymentTestController::class, 'testVNPayPayment']);
        Route::post('/simulate-webhook', [App\Http\Controllers\Api\PaymentTestController::class, 'simulateWebhook']);
        Route::delete('/cleanup', [App\Http\Controllers\Api\PaymentTestController::class, 'cleanupTestData']);
        Route::get('/status', [App\Http\Controllers\Api\PaymentTestController::class, 'getSystemStatus']);
    });

    // Secure download endpoint (public with token validation)
    Route::get('/download/{token}', [App\Http\Controllers\Api\DownloadController::class, 'download']);

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {

        // Auth profile routes (protected)
        Route::prefix('auth')->group(function () {
            Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
            Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
            Route::post('/refresh', [App\Http\Controllers\Api\AuthController::class, 'refresh']);
            Route::put('/profile', [App\Http\Controllers\Api\AuthController::class, 'updateProfile']);
            Route::post('/change-password', [App\Http\Controllers\Api\AuthController::class, 'changePassword']);
            Route::post('/upload-avatar', [App\Http\Controllers\Api\AuthController::class, 'uploadAvatar']);
            Route::delete('/delete-account', [App\Http\Controllers\Api\AuthController::class, 'deleteAccount']);
        });

        // Shopping Cart routes (protected)
        Route::prefix('cart')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\CartController::class, 'index']);
            Route::post('/', [App\Http\Controllers\Api\CartController::class, 'store']);
            Route::put('/{cartItemId}', [App\Http\Controllers\Api\CartController::class, 'update']);
            Route::delete('/{cartItemId}', [App\Http\Controllers\Api\CartController::class, 'destroy']);
            Route::delete('/', [App\Http\Controllers\Api\CartController::class, 'clear']);
            Route::post('/validate', [App\Http\Controllers\Api\CartController::class, 'validateCart']);
            Route::post('/update-prices', [App\Http\Controllers\Api\CartController::class, 'updatePrices']);
            Route::post('/estimate', [App\Http\Controllers\Api\CartController::class, 'estimate']);
        });

        // Order management routes (protected)
        Route::prefix('orders')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\OrderController::class, 'index']);
            Route::post('/', [App\Http\Controllers\Api\OrderController::class, 'store']);
            Route::get('/{orderId}', [App\Http\Controllers\Api\OrderController::class, 'show']);
            Route::put('/{orderId}', [App\Http\Controllers\Api\OrderController::class, 'update']);
            Route::post('/{orderId}/cancel', [App\Http\Controllers\Api\OrderController::class, 'cancel']);
            Route::get('/{orderId}/invoice', [App\Http\Controllers\Api\OrderController::class, 'invoice']);
            Route::get('/{orderId}/downloads', [App\Http\Controllers\Api\OrderController::class, 'downloads']);
            Route::post('/{orderId}/reorder', [App\Http\Controllers\Api\OrderController::class, 'reorder']);
        });

        // Payment processing routes (protected)
        Route::prefix('payment')->group(function () {
            Route::post('/initiate', [App\Http\Controllers\Api\PaymentController::class, 'initiate']);
            Route::post('/stripe/create-intent', [App\Http\Controllers\Api\PaymentController::class, 'createStripeIntent']);
            Route::post('/vnpay/create-payment', [App\Http\Controllers\Api\PaymentController::class, 'createVNPayPayment']);
            Route::post('/confirm/{orderId}', [App\Http\Controllers\Api\PaymentController::class, 'confirmPayment']);
            Route::post('/stripe/confirm', [App\Http\Controllers\Api\PaymentController::class, 'confirmStripe']);
            Route::get('/status/{orderId}', [App\Http\Controllers\Api\PaymentController::class, 'status']);
            Route::post('/cancel', [App\Http\Controllers\Api\PaymentController::class, 'cancel']);
            Route::post('/refund', [App\Http\Controllers\Api\PaymentController::class, 'refund']);
        });

        // Secure Downloads routes (protected)
        Route::prefix('downloads')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\DownloadController::class, 'index']);
            Route::get('/purchase/{purchaseId}', [App\Http\Controllers\Api\DownloadController::class, 'purchaseFiles']);
            Route::post('/generate-link', [App\Http\Controllers\Api\DownloadController::class, 'generateDownloadLink']);
            Route::get('/history', [App\Http\Controllers\Api\DownloadController::class, 'history']);
        });

        // Secure Downloads routes (authenticated)
        Route::prefix('downloads')->middleware('auth:sanctum')->group(function () {
            Route::post('/generate-token', [App\Http\Controllers\Api\SecureDownloadController::class, 'generateToken']);
            Route::get('/history', [App\Http\Controllers\Api\SecureDownloadController::class, 'downloadHistory']);
            Route::get('/analytics/{purchase_id}', [App\Http\Controllers\Api\SecureDownloadController::class, 'purchaseAnalytics']);
        });

        // Download with token (public but token-protected)
        Route::get('/downloads/{token}/file', [App\Http\Controllers\Api\SecureDownloadController::class, 'downloadFile'])
            ->name('api.downloads.file')
            ->middleware(['download.access']);

        // User actions routes (protected)
        Route::prefix('users')->group(function () {
            Route::post('/{username}/follow', [App\Http\Controllers\Api\UserController::class, 'follow']);
            Route::delete('/{username}/follow', [App\Http\Controllers\Api\UserController::class, 'unfollow']);
            Route::post('/{username}/block', [App\Http\Controllers\Api\UserController::class, 'block']);
            Route::delete('/{username}/block', [App\Http\Controllers\Api\UserController::class, 'unblock']);
        });

        // Thread creation and management routes (protected)
        Route::prefix('threads')->group(function () {
            Route::post('/', [App\Http\Controllers\Api\ThreadController::class, 'store']);
            Route::put('/{slug}', [App\Http\Controllers\Api\ThreadController::class, 'update']);
            Route::delete('/{slug}', [App\Http\Controllers\Api\ThreadController::class, 'destroy']);
            Route::post('/{slug}/like', [App\Http\Controllers\Api\ThreadController::class, 'like']);
            Route::delete('/{slug}/like', [App\Http\Controllers\Api\ThreadController::class, 'unlike']);
            Route::post('/{slug}/dislike', [App\Http\Controllers\Api\ThreadController::class, 'dislike']);
            Route::delete('/{slug}/dislike', [App\Http\Controllers\Api\ThreadController::class, 'undislike']);
            Route::post('/{slug}/subscribe', [App\Http\Controllers\Api\ThreadController::class, 'subscribe']);
            Route::delete('/{slug}/subscribe', [App\Http\Controllers\Api\ThreadController::class, 'unsubscribe']);
            Route::post('/{slug}/report', [App\Http\Controllers\Api\ThreadController::class, 'report']);
        });

        // Comment management routes (protected)
        Route::prefix('comments')->group(function () {
            Route::post('/', [App\Http\Controllers\Api\CommentController::class, 'store']);
            Route::put('/{id}', [App\Http\Controllers\Api\CommentController::class, 'update']);
            Route::delete('/{id}', [App\Http\Controllers\Api\CommentController::class, 'destroy']);
            Route::post('/{id}/like', [App\Http\Controllers\Api\CommentController::class, 'like']);
            Route::delete('/{id}/like', [App\Http\Controllers\Api\CommentController::class, 'unlike']);
            Route::post('/{id}/dislike', [App\Http\Controllers\Api\CommentController::class, 'dislike']);
            Route::delete('/{id}/dislike', [App\Http\Controllers\Api\CommentController::class, 'undislike']);
            Route::post('/{id}/report', [App\Http\Controllers\Api\CommentController::class, 'report']);
        });

        // Notifications routes (protected)
        Route::prefix('notifications')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\NotificationController::class, 'index']);
            Route::get('/unread', [App\Http\Controllers\Api\NotificationController::class, 'getUnread']);
            Route::post('/mark-read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
            Route::post('/mark-all-read', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
            Route::delete('/{id}', [App\Http\Controllers\Api\NotificationController::class, 'destroy']);
        });

        // Moderation routes (protected - require moderation permissions)
        Route::prefix('moderation')->middleware(['can:moderate-content'])->group(function () {
            // Thread moderation
            Route::post('/threads/{thread}/flag', [App\Http\Controllers\ModerationController::class, 'flagThread']);
            Route::delete('/threads/{thread}/flag', [App\Http\Controllers\ModerationController::class, 'unflagThread']);
            Route::post('/threads/{thread}/spam', [App\Http\Controllers\ModerationController::class, 'markThreadAsSpam']);
            Route::delete('/threads/{thread}/spam', [App\Http\Controllers\ModerationController::class, 'unmarkThreadAsSpam']);
            Route::put('/threads/{thread}/moderation-status', [App\Http\Controllers\ModerationController::class, 'updateThreadModerationStatus']);
            Route::post('/threads/{thread}/archive', [App\Http\Controllers\ModerationController::class, 'archiveThread']);
            Route::delete('/threads/{thread}/archive', [App\Http\Controllers\ModerationController::class, 'unarchiveThread']);
            Route::post('/threads/{thread}/hide', [App\Http\Controllers\ModerationController::class, 'hideThread']);
            Route::delete('/threads/{thread}/hide', [App\Http\Controllers\ModerationController::class, 'unhideThread']);

            // Comment moderation
            Route::post('/comments/{comment}/flag', [App\Http\Controllers\ModerationController::class, 'flagComment']);
            Route::delete('/comments/{comment}/flag', [App\Http\Controllers\ModerationController::class, 'unflagComment']);
            Route::post('/comments/{comment}/spam', [App\Http\Controllers\ModerationController::class, 'markCommentAsSpam']);
            Route::delete('/comments/{comment}/spam', [App\Http\Controllers\ModerationController::class, 'unmarkCommentAsSpam']);
            Route::post('/comments/{comment}/solution', [App\Http\Controllers\ModerationController::class, 'markCommentAsSolution']);
            Route::delete('/comments/{comment}/solution', [App\Http\Controllers\ModerationController::class, 'unmarkCommentAsSolution']);

            // Batch operations
            Route::post('/threads/batch', [App\Http\Controllers\ModerationController::class, 'batchModerationThreads']);

            // Moderation dashboard
            Route::get('/threads/pending', [App\Http\Controllers\ModerationController::class, 'getPendingThreads']);
            Route::get('/comments/pending', [App\Http\Controllers\ModerationController::class, 'getPendingComments']);
        });

        // Thread Quality routes (protected)
        Route::prefix('threads/{thread}')->group(function () {
            // Rating endpoints
            Route::post('/rate', [App\Http\Controllers\Api\ThreadQualityController::class, 'rateThread']);
            Route::delete('/rate', [App\Http\Controllers\Api\ThreadQualityController::class, 'removeRating']);
            Route::get('/rating', [App\Http\Controllers\Api\ThreadQualityController::class, 'getUserRating']);
            Route::get('/ratings', [App\Http\Controllers\Api\ThreadQualityController::class, 'getThreadRatings']);
            Route::get('/rating-stats', [App\Http\Controllers\Api\ThreadQualityController::class, 'getThreadRatingStats']);

            // Bookmark endpoints
            Route::post('/bookmark', [App\Http\Controllers\Api\ThreadQualityController::class, 'bookmarkThread']);
            Route::delete('/bookmark', [App\Http\Controllers\Api\ThreadQualityController::class, 'removeBookmark']);
            Route::put('/bookmark', [App\Http\Controllers\Api\ThreadQualityController::class, 'updateBookmark']);
            Route::get('/bookmark', [App\Http\Controllers\Api\ThreadQualityController::class, 'getBookmarkStatus']);

            // Follow endpoints
            Route::post('/follow', [App\Http\Controllers\Api\ThreadQualityController::class, 'followThread']);
            Route::delete('/follow', [App\Http\Controllers\Api\ThreadQualityController::class, 'unfollowThread']);
            Route::get('/follow', [App\Http\Controllers\Api\ThreadQualityController::class, 'getFollowStatus']);
        });

        // User Quality Data routes (protected)
        Route::prefix('user')->group(function () {
            Route::get('/bookmarks', [App\Http\Controllers\Api\ThreadQualityController::class, 'getUserBookmarks']);
            Route::get('/bookmark-folders', [App\Http\Controllers\Api\ThreadQualityController::class, 'getBookmarkFolders']);
            Route::get('/followed-threads', [App\Http\Controllers\Api\ThreadQualityController::class, 'getUserFollowedThreads']);
        });

        // Admin routes (protected - require admin role)
        Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
            // Showcase management
            Route::prefix('showcases')->group(function () {
                Route::get('/', [App\Http\Controllers\Api\Admin\ShowcaseController::class, 'index']);
                Route::post('/add', [App\Http\Controllers\Api\Admin\ShowcaseController::class, 'addToShowcase']);
                Route::delete('/{id}', [App\Http\Controllers\Api\Admin\ShowcaseController::class, 'removeFromShowcase']);
            });

            // Reports management
            Route::prefix('reports')->group(function () {
                Route::get('/', [App\Http\Controllers\Api\ReportController::class, 'index']);
                Route::put('/{id}', [App\Http\Controllers\Api\ReportController::class, 'updateStatus']);
            });

            // Tags management
            Route::prefix('tags')->group(function () {
                Route::post('/', [App\Http\Controllers\Api\TagController::class, 'store']);
            });
        });
    });
});
