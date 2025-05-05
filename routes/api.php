<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Test CORS
Route::get('/cors-test', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'CORS test successful',
        'origin' => $request->header('Origin'),
        'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'https://mechamap.com,https://www.mechamap.com,http://localhost:3000')),
    ]);
});

// API version 1
Route::prefix('v1')->group(function () {
    // Public routes
    Route::get('/settings', [App\Http\Controllers\Api\SettingsController::class, 'index']);
    Route::get('/settings/{group}', [App\Http\Controllers\Api\SettingsController::class, 'getByGroup']);
    Route::get('/seo', [App\Http\Controllers\Api\SeoController::class, 'index']);
    Route::get('/seo/{group}', [App\Http\Controllers\Api\SeoController::class, 'getByGroup']);
    Route::get('/page-seo/{routeName}', [App\Http\Controllers\Api\SeoController::class, 'getPageSeoByRoute']);
    Route::get('/page-seo/url/{urlPattern}', [App\Http\Controllers\Api\SeoController::class, 'getPageSeoByUrl']);
    Route::get('/favicon', [App\Http\Controllers\Api\FaviconController::class, 'getFavicon']);

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

    // Threads routes (public)
    Route::prefix('threads')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ThreadController::class, 'index']);
        Route::get('/actions/approve-all', [App\Http\Controllers\Api\ThreadController::class, 'approveAllThreads']);
        Route::get('/{slug}', [App\Http\Controllers\Api\ThreadController::class, 'show']);
        Route::get('/{slug}/comments', [App\Http\Controllers\Api\ThreadController::class, 'getComments']);
        Route::get('/{slug}/media', [App\Http\Controllers\Api\MediaController::class, 'getThreadMedia']);
    });

    // Search routes (public)
    Route::prefix('search')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\SearchController::class, 'search']);
        Route::get('/suggestions', [App\Http\Controllers\Api\SearchController::class, 'suggestions']);
    });

    // SEO routes (public)
    Route::prefix('seo')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\SeoController::class, 'index']);
        Route::get('/pages/{slug}', [App\Http\Controllers\Api\SeoController::class, 'getPageSeo']);
        Route::get('/threads/{slug}', [App\Http\Controllers\Api\SeoController::class, 'getThreadSeo']);
        Route::get('/forums/{slug}', [App\Http\Controllers\Api\SeoController::class, 'getForumSeo']);
        Route::get('/categories/{slug}', [App\Http\Controllers\Api\SeoController::class, 'getCategorySeo']);
        Route::get('/users/{username}', [App\Http\Controllers\Api\SeoController::class, 'getUserSeo']);
    });

    // Showcase routes (public)
    Route::prefix('showcases')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ShowcaseController::class, 'index']);
        Route::get('/{slug}', [App\Http\Controllers\Api\ShowcaseController::class, 'show']);
    });

    // Stats routes (public)
    Route::prefix('stats')->group(function () {
        Route::get('/forum', [App\Http\Controllers\Api\StatsController::class, 'getForumStats']);
        Route::get('/forums/popular', [App\Http\Controllers\Api\StatsController::class, 'getPopularForums']);
        Route::get('/users/active', [App\Http\Controllers\Api\StatsController::class, 'getActiveUsers']);
        Route::get('/threads/featured', [App\Http\Controllers\Api\StatsController::class, 'getFeaturedThreads']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // User info
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Auth routes (protected)
        Route::prefix('auth')->group(function () {
            Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
            Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
            Route::post('/refresh', [App\Http\Controllers\Api\AuthController::class, 'refresh']);
        });

        // Users routes (protected)
        Route::prefix('users')->group(function () {
            Route::put('/{username}', [App\Http\Controllers\Api\UserController::class, 'update']);
            Route::delete('/{username}', [App\Http\Controllers\Api\UserController::class, 'destroy']);
            Route::post('/{username}/follow', [App\Http\Controllers\Api\UserController::class, 'follow']);
            Route::delete('/{username}/follow', [App\Http\Controllers\Api\UserController::class, 'unfollow']);
            Route::post('/avatar', [App\Http\Controllers\Api\UserController::class, 'updateAvatar']);
        });

        // Threads routes (protected)
        Route::prefix('threads')->group(function () {
            Route::post('/', [App\Http\Controllers\Api\ThreadController::class, 'store']);
            Route::put('/{slug}', [App\Http\Controllers\Api\ThreadController::class, 'update']);
            Route::delete('/{slug}', [App\Http\Controllers\Api\ThreadController::class, 'destroy']);
            Route::post('/{slug}/like', [App\Http\Controllers\Api\ThreadController::class, 'like']);
            Route::delete('/{slug}/like', [App\Http\Controllers\Api\ThreadController::class, 'unlike']);
            Route::post('/{slug}/save', [App\Http\Controllers\Api\ThreadController::class, 'save']);
            Route::delete('/{slug}/save', [App\Http\Controllers\Api\ThreadController::class, 'unsave']);
            Route::post('/{slug}/follow', [App\Http\Controllers\Api\ThreadController::class, 'follow']);
            Route::delete('/{slug}/follow', [App\Http\Controllers\Api\ThreadController::class, 'unfollow']);
            Route::get('/saved', [App\Http\Controllers\Api\ThreadController::class, 'getSaved']);
            Route::get('/followed', [App\Http\Controllers\Api\ThreadController::class, 'getFollowed']);
            Route::post('/{slug}/comments', [App\Http\Controllers\Api\CommentController::class, 'store']);
        });

        // Comments routes (protected)
        Route::prefix('comments')->group(function () {
            Route::put('/{id}', [App\Http\Controllers\Api\CommentController::class, 'update']);
            Route::delete('/{id}', [App\Http\Controllers\Api\CommentController::class, 'destroy']);
            Route::post('/{id}/like', [App\Http\Controllers\Api\CommentController::class, 'like']);
            Route::delete('/{id}/like', [App\Http\Controllers\Api\CommentController::class, 'unlike']);
            Route::get('/{id}/replies', [App\Http\Controllers\Api\CommentController::class, 'getReplies']);
            Route::post('/{id}/replies', [App\Http\Controllers\Api\CommentController::class, 'storeReply']);
        });

        // Alerts routes (protected)
        Route::prefix('alerts')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\AlertController::class, 'index']);
            Route::post('/{id}/read', [App\Http\Controllers\Api\AlertController::class, 'markAsRead']);
            Route::delete('/{id}', [App\Http\Controllers\Api\AlertController::class, 'destroy']);
            Route::post('/read-all', [App\Http\Controllers\Api\AlertController::class, 'markAllAsRead']);
        });

        // Conversations routes (protected)
        Route::prefix('conversations')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\ConversationController::class, 'index']);
            Route::post('/', [App\Http\Controllers\Api\ConversationController::class, 'store']);
            Route::get('/{id}', [App\Http\Controllers\Api\ConversationController::class, 'show']);
            Route::post('/{id}/messages', [App\Http\Controllers\Api\ConversationController::class, 'sendMessage']);
            Route::post('/{id}/read', [App\Http\Controllers\Api\ConversationController::class, 'markAsRead']);
        });

        // Media routes (protected)
        Route::prefix('media')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\MediaController::class, 'index']);
            Route::post('/', [App\Http\Controllers\Api\MediaController::class, 'store']);
            Route::get('/{id}', [App\Http\Controllers\Api\MediaController::class, 'show']);
            Route::put('/{id}', [App\Http\Controllers\Api\MediaController::class, 'update']);
            Route::delete('/{id}', [App\Http\Controllers\Api\MediaController::class, 'destroy']);
        });

        // Showcase routes (protected)
        Route::prefix('showcases')->group(function () {
            Route::post('/', [App\Http\Controllers\Api\ShowcaseController::class, 'store']);
            Route::put('/{slug}', [App\Http\Controllers\Api\ShowcaseController::class, 'update']);
            Route::delete('/{slug}', [App\Http\Controllers\Api\ShowcaseController::class, 'destroy']);
        });
    });
});
