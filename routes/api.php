<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Authentication API routes
Route::middleware('web')->group(function () {
    Route::get('/auth/token', function (Request $request) {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('websocket-access')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user_id' => $user->id,
            'expires_at' => now()->addDays(30)->toISOString()
        ]);
    });

    Route::get('/auth/user', function (Request $request) {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'success' => true,
            'user' => Auth::user()->only(['id', 'name', 'email', 'avatar'])
        ]);
    });

// Get Sanctum token for WebSocket authentication (following realtime server documentation)
Route::middleware(['web'])->get('/user/websocket-token', function (Request $request) {
    if (!Auth::check()) {
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
    }

    $user = Auth::user();

    try {
        // Check if user is active
        if (isset($user->status) && $user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'User account is not active'
            ], 403);
        }

        // Revoke old WebSocket tokens to prevent multiple connections
        $user->tokens()->where('name', 'websocket-connection')->delete();

        // Create Sanctum token for WebSocket authentication (as per realtime server docs)
        $token = $user->createToken('websocket-connection', [
            'websocket:connect',
            'websocket:receive-notifications'
        ]);

        \Log::info('Sanctum WebSocket token created for user:', [
            'user_id' => $user->id,
            'token_id' => $token->accessToken->id,
            'token_prefix' => substr($token->plainTextToken, 0, 20) . '...'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'WebSocket token generated successfully',
            'data' => [
                'token' => $token->plainTextToken,
                'user_id' => $user->id,
                'websocket_url' => 'http://localhost:3000',
                'expires_at' => now()->addHours(24)->toISOString(),
                'permissions' => ['receive_notifications']
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('Failed to create WebSocket token:', [
            'user_id' => $user->id,
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to generate WebSocket token',
            'error' => $e->getMessage()
        ], 500);
    }
});

});

// WebSocket verification endpoint (called by Realtime Server with API key)
Route::middleware(['websocket.api'])->prefix('websocket-api')->group(function () {
    Route::post('/verify-user', function (Request $request) {
        try {
            // Get Sanctum token from request body (sent by realtime server)
            $sanctumToken = $request->input('token') ?? $request->input('sanctum_token');

            if (!$sanctumToken) {
                \Log::warning('WebSocket verify-user: Missing Sanctum token', [
                    'request_data' => $request->all(),
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Sanctum token required'
                ], 400);
            }

            // Manually verify Sanctum token
            $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($sanctumToken);

            if (!$tokenModel) {
                \Log::warning('WebSocket verify-user: Invalid Sanctum token', [
                    'token_prefix' => substr($sanctumToken, 0, 20) . '...',
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Sanctum token'
                ], 401);
            }

            // Check if token is expired
            if ($tokenModel->expires_at && $tokenModel->expires_at->isPast()) {
                \Log::warning('WebSocket verify-user: Expired Sanctum token', [
                    'token_id' => $tokenModel->id,
                    'expires_at' => $tokenModel->expires_at,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Token expired'
                ], 401);
            }

            // Get user from token
            $user = $tokenModel->tokenable;

            if (!$user) {
                \Log::warning('WebSocket verify-user: Token has no associated user', [
                    'token_id' => $tokenModel->id,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Token has no associated user'
                ], 401);
            }

            // Check if user is active
            if (isset($user->status) && $user->status !== 'active') {
                \Log::warning('WebSocket verify-user: User not active', [
                    'user_id' => $user->id,
                    'status' => $user->status,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'User account is not active'
                ], 403);
            }

            // Get user permissions for WebSocket
            $userPermissions = ['receive_notifications'];

            \Log::info('WebSocket user verification successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role ?? 'member',
                'permissions' => $userPermissions,
                'token_id' => $tokenModel->id,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User verified successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role ?? 'member',
                        'permissions' => $userPermissions,
                        'avatar' => $user->avatar ?? null,
                        'status' => $user->status ?? 'active',
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('WebSocket user verification error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    });
});

// Backward compatibility: Keep old JWT endpoint for existing code
Route::middleware(['web'])->get('/user/token', function (Request $request) {
    if (!Auth::check()) {
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
    }

    $user = Auth::user();

    // Generate JWT token for WebSocket authentication
    $payload = [
        'userId' => $user->id,
        'role' => $user->role,
        'permissions' => ['websocket:connect'],
        'iat' => time(),
        'exp' => time() + (60 * 60), // 1 hour expiration
    ];

    // Use JWT secret from config or env
    $jwtSecret = env('JWT_SECRET', config('app.key'));

    // Debug JWT secret and payload
    \Log::info('JWT Secret used for encoding:', ['secret' => substr($jwtSecret, 0, 10) . '...']);
    \Log::info('JWT Payload:', $payload);

    try {
        $token = \Firebase\JWT\JWT::encode($payload, $jwtSecret, 'HS256');
        \Log::info('JWT Token created:', ['token' => substr($token, 0, 50) . '...']);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ]
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to generate token',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Direct test route outside of all middleware
Route::get('/marketplace-test', function() {
    return response()->json([
        'success' => true,
        'message' => 'Direct marketplace test working',
        'database' => config('database.default'),
        'env' => app()->environment()
    ]);
});

// Test threads route
Route::get('/threads-test', function() {
    $count = \App\Models\Thread::count();
    return response()->json([
        'success' => true,
        'message' => 'Threads test working',
        'total_threads' => $count,
        'sample_thread' => \App\Models\Thread::first(['id', 'title', 'status'])
    ]);
});

// Community Stats API Routes (Public)
Route::prefix('community')->name('api.community.')->group(function () {
    Route::get('/quick-stats', [App\Http\Controllers\Api\CommunityStatsController::class, 'getQuickStats']);
    Route::get('/online-count', [App\Http\Controllers\Api\CommunityStatsController::class, 'getOnlineUsersCount']);
    Route::get('/recent-activity', [App\Http\Controllers\Api\CommunityStatsController::class, 'getRecentActivity']);
    Route::get('/popular-forums', [App\Http\Controllers\Api\CommunityStatsController::class, 'getPopularForums']);
    Route::get('/trending-topics', [App\Http\Controllers\Api\CommunityStatsController::class, 'getTrendingTopics']);
    Route::get('/overview-stats', [App\Http\Controllers\Api\CommunityStatsController::class, 'getOverviewStats']);
});

// Marketplace Stats API Routes (Public)
Route::prefix('marketplace')->name('api.marketplace.')->group(function () {
    Route::get('/quick-stats', [App\Http\Controllers\Api\MarketplaceStatsController::class, 'quickStats']);
    Route::get('/overview', [App\Http\Controllers\Api\MarketplaceStatsController::class, 'overview']);
    Route::get('/trending', [App\Http\Controllers\Api\MarketplaceStatsController::class, 'trending']);

    // Cart routes (public for header badge)
    Route::get('/cart/count', [App\Http\Controllers\Api\MarketplaceStatsController::class, 'cartCount']);
});

// Sidebar stats endpoint (public - for sidebar statistics)
Route::get('/sidebar/stats', function() {
    try {
        // Get basic statistics for sidebar
        $stats = [
            'total_threads' => \App\Models\Thread::where('status', 'published')->count(),
            'total_users' => \App\Models\User::count(),
            'weekly_activity' => \App\Models\Thread::where('created_at', '>=', now()->subWeek())->count(),
            'growth_rate' => '+100%' // This could be calculated dynamically
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'message' => 'Sidebar stats retrieved successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'stats' => [
                'total_threads' => 0,
                'total_users' => 0,
                'weekly_activity' => 0,
                'growth_rate' => '0%'
            ],
            'error' => 'Failed to retrieve sidebar stats',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Test API ThreadController directly
Route::get('/threads-controller-test', [App\Http\Controllers\Api\ThreadController::class, 'index']);

// Notifications API endpoint (public - for header badge)
Route::get('/notifications', function() {
    return response()->json([
        'success' => true,
        'notifications' => [],
        'unread_count' => 0,
        'message' => 'Notifications endpoint working'
    ]);
});

// Notifications count endpoint (public - for header badge)
Route::get('/notifications/count', function() {
    try {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Use custom notification system to avoid table structure conflict
            $unreadCount = $user->userNotifications()->where('is_read', false)->count();
            $totalCount = $user->userNotifications()->count();

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
                'total_count' => $totalCount,
                'notifications' => $unreadCount,  // For header.js compatibility
                'messages' => 0,  // For header.js compatibility
                'message' => 'Notifications count retrieved successfully'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
                'total_count' => 0,
                'notifications' => 0,  // For header.js compatibility
                'messages' => 0,  // For header.js compatibility
                'message' => 'User not authenticated'
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'unread_count' => 0,
            'total_count' => 0,
            'notifications' => 0,
            'messages' => 0,
            'error' => 'Failed to retrieve notifications count',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Notifications polling endpoint (authenticated - for WebSocket fallback)
Route::middleware('auth:sanctum')->get('/notifications/poll', function() {
    try {
        $user = Auth::user();

        // Get notifications created in the last 30 seconds (for polling)
        $recentNotifications = $user->userNotifications()
            ->where('created_at', '>=', now()->subSeconds(30))
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                $data = is_array($notification->data) ? $notification->data : json_decode($notification->data ?? '{}', true);

                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $data['title'] ?? 'Thông báo mới',
                    'message' => $data['message'] ?? $notification->message ?? '',
                    'url' => $data['url'] ?? null,
                    'created_at' => $notification->created_at->toISOString(),
                    'is_read' => $notification->is_read
                ];
            });

        return response()->json([
            'success' => true,
            'notifications' => $recentNotifications,
            'count' => $recentNotifications->count(),
            'timestamp' => now()->toISOString()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'notifications' => [],
            'count' => 0,
            'error' => 'Polling failed: ' . $e->getMessage()
        ], 500);
    }
});

// Notifications recent endpoint (public - for header dropdown)
Route::get('/notifications/recent', function() {
    try {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Use custom notification system to avoid table structure conflict
            $notifications = $user->userNotifications()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($notification) {
                    $data = is_array($notification->data) ? $notification->data : json_decode($notification->data ?? '{}', true);
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title ?? $data['title'] ?? 'Thông báo',
                        'message' => $notification->message ?? $data['message'] ?? 'Bạn có thông báo mới',
                        'icon' => $data['icon'] ?? 'bell',
                        'color' => $data['color'] ?? 'primary',
                        'time_ago' => $notification->created_at->diffForHumans(),
                        'is_read' => $notification->is_read,
                        'action_url' => $data['action_url'] ?? null,
                        'created_at' => $notification->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'total_unread' => $user->userNotifications()->where('is_read', false)->count(),
                'message' => 'Recent notifications retrieved successfully'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'notifications' => [],
                'total_unread' => 0,
                'message' => 'User not authenticated'
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'notifications' => [],
            'total_unread' => 0,
            'error' => 'Failed to retrieve recent notifications',
            'message' => $e->getMessage()
        ], 500);
    }
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
        // Unified search for header AJAX
        Route::get('/unified', [App\Http\Controllers\Api\UnifiedSearchController::class, 'search'])->name('api.search.unified');

        // Legacy specific searches
        Route::get('/threads', [App\Http\Controllers\Api\SearchController::class, 'searchThreads']);
        Route::get('/users', [App\Http\Controllers\Api\SearchController::class, 'searchUsers']);
        Route::get('/tags', [App\Http\Controllers\Api\SearchController::class, 'searchTags']);
        Route::get('/forums', [App\Http\Controllers\Api\SearchController::class, 'searchForums']);
        Route::get('/global', [App\Http\Controllers\Api\SearchController::class, 'globalSearch']);
        Route::get('/suggestions', [App\Http\Controllers\Api\SearchController::class, 'getSearchSuggestions']);
    });

    // Real-time API routes
    Route::prefix('realtime')->group(function () {
        // Route::get('/metrics', [App\Http\Controllers\RealTimeController::class, 'status']); // Removed
        // Route::get('/online-users', [App\Http\Controllers\RealTimeController::class, 'getOnlineUsers']); // Removed
        Route::get('/activity', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    [
                        'type' => 'user',
                        'icon' => 'fas fa-user',
                        'title' => 'New user registered',
                        'time' => '2 minutes ago'
                    ],
                    [
                        'type' => 'system',
                        'icon' => 'fas fa-cog',
                        'title' => 'System backup completed',
                        'time' => '5 minutes ago'
                    ],
                    [
                        'type' => 'success',
                        'icon' => 'fas fa-check',
                        'title' => 'Order processed successfully',
                        'time' => '8 minutes ago'
                    ]
                ]
            ]);
        });
        // Route::get('/health', [App\Http\Controllers\RealTimeController::class, 'healthCheck']); // Removed
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

        // Search functionality
        Route::get('/search-suggestions', [App\Http\Controllers\Api\MarketplaceSearchController::class, 'suggestions']);
        Route::get('/popular-searches', [App\Http\Controllers\Api\MarketplaceSearchController::class, 'popular']);
        Route::get('/filters', [App\Http\Controllers\Api\MarketplaceSearchController::class, 'filters']);
    });

    // Payment webhooks and callbacks (public - no auth required)
    Route::prefix('payment')->group(function () {
        Route::get('/methods', [App\Http\Controllers\Api\PaymentController::class, 'paymentMethods']);
        Route::post('/stripe/webhook', [App\Http\Controllers\Api\PaymentController::class, 'webhook'])
            ->middleware('stripe.webhook');
        Route::get('/vnpay/callback', [App\Http\Controllers\Api\PaymentController::class, 'vnpayCallback']);
        Route::post('/vnpay/ipn', [App\Http\Controllers\Api\PaymentController::class, 'vnpayIpn']);

        // SePay webhook
        Route::post('/sepay/webhook', [App\Http\Controllers\Api\SePayWebhookController::class, 'handleWebhook']);
        Route::post('/sepay/check-status', [App\Http\Controllers\Api\SePayWebhookController::class, 'checkPaymentStatus']);

        // 🏦 Centralized Payment Webhooks
        Route::post('/centralized/webhook', [App\Http\Controllers\Api\PaymentController::class, 'centralizedWebhook'])
            ->middleware('stripe.webhook');
        Route::post('/centralized/sepay/webhook', [App\Http\Controllers\Api\CentralizedSePayWebhookController::class, 'handleWebhook']);
        Route::post('/centralized/sepay/check-status', [App\Http\Controllers\Api\CentralizedSePayWebhookController::class, 'checkPaymentStatus']);
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

        // 🏦 Centralized Payment Testing Routes
        Route::prefix('centralized')->group(function () {
            Route::get('/configuration', [App\Http\Controllers\Api\CentralizedPaymentTestController::class, 'testConfiguration']);
            Route::post('/create-order', [App\Http\Controllers\Api\CentralizedPaymentTestController::class, 'createTestOrder']);
            Route::post('/stripe', [App\Http\Controllers\Api\CentralizedPaymentTestController::class, 'testStripePayment']);
            Route::post('/sepay', [App\Http\Controllers\Api\CentralizedPaymentTestController::class, 'testSePayPayment']);
            Route::post('/simulate-stripe-webhook', [App\Http\Controllers\Api\CentralizedPaymentTestController::class, 'simulateStripeWebhook']);
            Route::post('/simulate-sepay-webhook', [App\Http\Controllers\Api\CentralizedPaymentTestController::class, 'simulateSePayWebhook']);
            Route::get('/status', [App\Http\Controllers\Api\CentralizedPaymentTestController::class, 'getSystemStatus']);
            Route::delete('/cleanup', [App\Http\Controllers\Api\CentralizedPaymentTestController::class, 'cleanupTestData']);
        });
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
            Route::post('/stripe/create-intent', [App\Http\Controllers\Api\PaymentController::class, 'createMarketplaceStripeIntent']);
            Route::post('/vnpay/create-payment', [App\Http\Controllers\Api\PaymentController::class, 'createVNPayPayment']);
            Route::post('/sepay/create-payment', [App\Http\Controllers\Api\PaymentController::class, 'createSePayPayment']);
            Route::post('/confirm/{orderId}', [App\Http\Controllers\Api\PaymentController::class, 'confirmPayment']);
            Route::post('/stripe/confirm', [App\Http\Controllers\Api\PaymentController::class, 'confirmStripe']);
            Route::get('/status/{orderId}', [App\Http\Controllers\Api\PaymentController::class, 'status']);

            // 🏦 Centralized Payment System Routes
            Route::prefix('centralized')->group(function () {
                Route::post('/stripe/create-intent', [App\Http\Controllers\Api\PaymentController::class, 'createCentralizedStripeIntent']);
                Route::post('/sepay/create-payment', [App\Http\Controllers\Api\PaymentController::class, 'createCentralizedSePayPayment']);
            });
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

        // Notifications routes (protected - legacy system)
        Route::prefix('notifications')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\NotificationController::class, 'index']);
            Route::get('/unread', [App\Http\Controllers\Api\NotificationController::class, 'getUnread']);
            Route::get('/recent', [App\Http\Controllers\Api\NotificationController::class, 'getRecent']);
            Route::post('/mark-read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
            Route::post('/mark-all-read', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
            Route::delete('/{id}', [App\Http\Controllers\Api\NotificationController::class, 'destroy']);
        });

        // Unified Notifications routes (protected - new unified system)
        Route::prefix('unified-notifications')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\UnifiedNotificationController::class, 'index']);
            Route::get('/count', [App\Http\Controllers\Api\UnifiedNotificationController::class, 'count']);
            Route::get('/recent', [App\Http\Controllers\Api\UnifiedNotificationController::class, 'recent']);
            Route::get('/stats', [App\Http\Controllers\Api\UnifiedNotificationController::class, 'stats']);
            Route::post('/mark-as-read', [App\Http\Controllers\Api\UnifiedNotificationController::class, 'markAsRead']);
            Route::post('/mark-all-as-read', [App\Http\Controllers\Api\UnifiedNotificationController::class, 'markAllAsRead']);
            Route::post('/send-test', [App\Http\Controllers\Api\UnifiedNotificationController::class, 'sendTest']);
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

        // ===== NEW MARKETPLACE API ROUTES =====

        // Enhanced Product Management (for business users)
        Route::prefix('marketplace/v2')->group(function () {
            // Product CRUD for business users
            Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);

            // Shopping cart management
            Route::prefix('cart')->group(function () {
                Route::get('/', [App\Http\Controllers\Api\ShoppingCartController::class, 'index']);
                Route::post('/', [App\Http\Controllers\Api\ShoppingCartController::class, 'store']);
                Route::put('/{id}', [App\Http\Controllers\Api\ShoppingCartController::class, 'update']);
                Route::delete('/{id}', [App\Http\Controllers\Api\ShoppingCartController::class, 'destroy']);
                Route::delete('/', [App\Http\Controllers\Api\ShoppingCartController::class, 'clear']);
                Route::get('/count', [App\Http\Controllers\Api\ShoppingCartController::class, 'count']);
            });

            // Seller dashboard (for business users)
            Route::prefix('seller')->middleware('role:supplier,manufacturer,brand')->group(function () {
                Route::get('/dashboard', function() {
                    return response()->json([
                        'success' => true,
                        'message' => 'Seller dashboard endpoint',
                        'data' => ['user' => auth()->user()]
                    ]);
                });
                Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index']);
                Route::get('/orders', function() {
                    return response()->json([
                        'success' => true,
                        'message' => 'Seller orders endpoint',
                        'data' => []
                    ]);
                });
                Route::get('/earnings', function() {
                    return response()->json([
                        'success' => true,
                        'message' => 'Seller earnings endpoint',
                        'data' => ['total_earnings' => 0]
                    ]);
                });
            });
        });

        // Conversations API (authenticated users only)
        Route::prefix('conversations')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\ConversationController::class, 'index']);
            Route::post('/', [App\Http\Controllers\Api\ConversationController::class, 'store']);
            Route::get('/{id}', [App\Http\Controllers\Api\ConversationController::class, 'show']);
            Route::get('/{id}/messages', [App\Http\Controllers\Api\ConversationController::class, 'getMessages']);
            Route::post('/{id}/messages', [App\Http\Controllers\Api\ConversationController::class, 'sendMessage']);
            Route::post('/{id}/read', [App\Http\Controllers\Api\ConversationController::class, 'markAsRead']);
            Route::delete('/{id}', [App\Http\Controllers\Api\ConversationController::class, 'destroy']);
        });

        // Chat API routes
        Route::prefix('chat')->group(function () {
            Route::get('/unread-count', [App\Http\Controllers\ChatController::class, 'getUnreadCount']);
        });

        // Search API routes
        Route::prefix('search')->group(function () {
            Route::get('/users', [App\Http\Controllers\ChatController::class, 'searchUsers']);
        });
    }); // End of auth:sanctum middleware group

    // Analytics API routes (public)
    Route::post('/analytics', [App\Http\Controllers\Api\AnalyticsController::class, 'store']);
    Route::get('/pages/{pageId}/view-count', [App\Http\Controllers\Api\AnalyticsController::class, 'getViewCount']);
    Route::get('/analytics/dashboard', [App\Http\Controllers\Api\AnalyticsController::class, 'getDashboardData']);
});

// WebSocket API routes removed - now handled by Node.js WebSocket server

// Notification Engagement API routes (outside v1 prefix for direct access)
Route::middleware('auth:sanctum')->prefix('notifications/engagement')->group(function () {
    Route::post('/track/view', [App\Http\Controllers\Api\NotificationEngagementController::class, 'trackView']);
    Route::post('/track/click', [App\Http\Controllers\Api\NotificationEngagementController::class, 'trackClick']);
    Route::post('/track/dismiss', [App\Http\Controllers\Api\NotificationEngagementController::class, 'trackDismiss']);
    Route::post('/track/action', [App\Http\Controllers\Api\NotificationEngagementController::class, 'trackAction']);
    Route::post('/track/bulk', [App\Http\Controllers\Api\NotificationEngagementController::class, 'bulkTrack']);
    Route::get('/metrics/user', [App\Http\Controllers\Api\NotificationEngagementController::class, 'getUserMetrics']);


    Route::get('/metrics/type', [App\Http\Controllers\Api\NotificationEngagementController::class, 'getTypeMetrics']);
    Route::get('/summary', [App\Http\Controllers\Api\NotificationEngagementController::class, 'getEngagementSummary']);
    Route::get('/top-performing', [App\Http\Controllers\Api\NotificationEngagementController::class, 'getTopPerforming']);
    Route::get('/leaderboard', [App\Http\Controllers\Api\NotificationEngagementController::class, 'getLeaderboard']);
});

// User Follow API routes (outside v1 prefix for direct access)
Route::middleware('auth:sanctum')->prefix('users')->group(function () {
    Route::post('/{user}/follow', [App\Http\Controllers\UserFollowController::class, 'follow']);
    Route::delete('/{user}/follow', [App\Http\Controllers\UserFollowController::class, 'unfollow']);
    Route::get('/{user}/followers', [App\Http\Controllers\UserFollowController::class, 'followers']);
    Route::get('/{user}/following', [App\Http\Controllers\UserFollowController::class, 'following']);
    Route::get('/{user}/is-following', [App\Http\Controllers\UserFollowController::class, 'isFollowing']);
    Route::get('/{user}/mutual-followers', [App\Http\Controllers\UserFollowController::class, 'mutualFollowers']);
    Route::get('/follow/suggestions', [App\Http\Controllers\UserFollowController::class, 'suggestions']);
    Route::get('/follow/statistics', [App\Http\Controllers\UserFollowController::class, 'statistics']);
    Route::post('/follow/bulk', [App\Http\Controllers\UserFollowController::class, 'bulkAction']);
});

// Achievement API routes (outside v1 prefix for direct access)
Route::middleware('auth:sanctum')->prefix('achievements')->group(function () {
    Route::get('/', [App\Http\Controllers\AchievementController::class, 'index']);
    Route::get('/my', [App\Http\Controllers\AchievementController::class, 'myAchievements']);
    Route::get('/available', [App\Http\Controllers\AchievementController::class, 'myAvailableAchievements']);
    Route::post('/check', [App\Http\Controllers\AchievementController::class, 'checkAchievements']);
    Route::get('/statistics', [App\Http\Controllers\AchievementController::class, 'statistics']);
    Route::get('/leaderboard', [App\Http\Controllers\AchievementController::class, 'leaderboard']);
    Route::post('/seed', [App\Http\Controllers\AchievementController::class, 'seedAchievements']);
    Route::get('/users/{user}', [App\Http\Controllers\AchievementController::class, 'userAchievements']);
    Route::get('/users/{user}/available', [App\Http\Controllers\AchievementController::class, 'availableAchievements']);
});

// Weekly Digest API routes (outside v1 prefix for direct access)
Route::middleware('auth:sanctum')->prefix('digest')->group(function () {
    Route::get('/latest', [App\Http\Controllers\WeeklyDigestController::class, 'myLatestDigest']);
    Route::get('/history', [App\Http\Controllers\WeeklyDigestController::class, 'myDigestHistory']);
    Route::get('/preview', [App\Http\Controllers\WeeklyDigestController::class, 'previewDigest']);
    Route::put('/preferences', [App\Http\Controllers\WeeklyDigestController::class, 'updatePreferences']);
    Route::get('/statistics', [App\Http\Controllers\WeeklyDigestController::class, 'statistics']);
    Route::post('/send', [App\Http\Controllers\WeeklyDigestController::class, 'sendDigest']);
    Route::get('/engagement', [App\Http\Controllers\WeeklyDigestController::class, 'engagementMetrics']);
});

// Notification Preferences API routes (outside v1 prefix for direct access)
Route::middleware('auth:sanctum')->prefix('notification-preferences')->group(function () {
    Route::get('/', [App\Http\Controllers\NotificationPreferencesController::class, 'getPreferences']);
    Route::put('/', [App\Http\Controllers\NotificationPreferencesController::class, 'update']);
    Route::post('/reset', [App\Http\Controllers\NotificationPreferencesController::class, 'resetPreferences']);
    Route::put('/types/{type}', [App\Http\Controllers\NotificationPreferencesController::class, 'updateNotificationType']);
});

// Typing Indicator API routes (outside v1 prefix for direct access)
Route::middleware('auth:sanctum')->prefix('typing')->group(function () {
    Route::post('/start', [App\Http\Controllers\TypingIndicatorController::class, 'start']);
    Route::post('/update', [App\Http\Controllers\TypingIndicatorController::class, 'update']);
    Route::post('/stop', [App\Http\Controllers\TypingIndicatorController::class, 'stop']);
    Route::get('/active', [App\Http\Controllers\TypingIndicatorController::class, 'getActive']);
    Route::get('/my-contexts', [App\Http\Controllers\TypingIndicatorController::class, 'myTypingContexts']);
    Route::post('/stop-all', [App\Http\Controllers\TypingIndicatorController::class, 'stopAll']);
    Route::post('/heartbeat', [App\Http\Controllers\TypingIndicatorController::class, 'heartbeat']);
    Route::post('/bulk', [App\Http\Controllers\TypingIndicatorController::class, 'bulk']);
    Route::get('/statistics', [App\Http\Controllers\TypingIndicatorController::class, 'statistics']);
    Route::post('/cleanup', [App\Http\Controllers\TypingIndicatorController::class, 'cleanup']);
});

// Test API routes (only in development)
if (app()->environment('local')) {
    Route::middleware('auth:sanctum')->prefix('test')->group(function () {
        Route::post('/notification', function (Illuminate\Http\Request $request) {
            $user = auth()->user();

            $notification = App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => $request->input('type', 'test'),
                'title' => $request->input('title', 'Test Notification'),
                'message' => $request->input('message', 'This is a test notification'),
                'data' => $request->input('data', []),
                'priority' => 'normal',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test notification created',
                'notification' => $notification
            ]);
        });

        Route::post('/follower-notification', function () {
            $user = auth()->user();

            $notification = App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'user_followed',
                'title' => 'Bạn có người theo dõi mới!',
                'message' => 'Test User đã bắt đầu theo dõi bạn',
                'data' => [
                    'follower_id' => $user->id,
                    'follower_name' => 'Test User',
                    'action_url' => '/profile/' . $user->id,
                    'action_text' => 'Xem hồ sơ'
                ],
                'priority' => 'normal',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Follower notification created',
                'notification' => $notification
            ]);
        });
    });
}

// ============================================================================
// WEBSOCKET SERVER API ROUTES (với API Key authentication)
// ============================================================================

// WebSocket Server API Routes (với API Key authentication)
Route::middleware(['websocket.api'])->prefix('websocket-api')->group(function () {
    // User authentication verification
    Route::post('/verify-user', [App\Http\Controllers\Api\UnifiedNotificationController::class, 'verifyUser']);

    // User information
    Route::get('/user/{id}', [App\Http\Controllers\Api\UnifiedNotificationController::class, 'getUserById']);

    // Broadcasting from WebSocket server to Laravel
    Route::post('/broadcast-to-laravel', [App\Http\Controllers\Api\UnifiedNotificationController::class, 'broadcastFromWebSocket']);

    // Health check for WebSocket server
    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'message' => 'Laravel API is healthy',
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment()
        ]);
    });

    // Get user permissions
    Route::get('/user/{id}/permissions', [App\Http\Controllers\Api\UnifiedNotificationController::class, 'getUserPermissions']);
});
