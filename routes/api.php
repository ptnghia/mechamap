<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\SeoController;
use App\Http\Controllers\Api\FaviconController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\ThreadController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ShowcaseController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\ConversationController;

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
    // Public routes
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::get('/settings/{group}', [SettingsController::class, 'getByGroup']);
    Route::get('/seo', [SeoController::class, 'index']);
    Route::get('/seo/{group}', [SeoController::class, 'getByGroup']);
    Route::get('/page-seo/{routeName}', [SeoController::class, 'getPageSeoByRoute']);
    Route::get('/page-seo/url/{urlPattern}', [SeoController::class, 'getPageSeoByUrl']);
    Route::get('/favicon', [FaviconController::class, 'getFavicon']);

    // Auth routes (public)
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
        Route::post('/social/{provider}', [AuthController::class, 'socialLogin']);
    });

    // Users routes (public)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{username}', [UserController::class, 'show']);
        Route::get('/{username}/threads', [UserController::class, 'getThreads']);
        Route::get('/{username}/comments', [UserController::class, 'getComments']);
        Route::get('/{username}/activities', [UserController::class, 'getActivities']);
        Route::get('/{username}/followers', [UserController::class, 'getFollowers']);
        Route::get('/{username}/following', [UserController::class, 'getFollowing']);
    });

    // Forums routes (public)
    Route::prefix('forums')->group(function () {
        Route::get('/', [ForumController::class, 'index']);
        Route::get('/{slug}', [ForumController::class, 'show']);
        Route::get('/{slug}/threads', [ForumController::class, 'getThreads']);
    });

    // Categories routes (public)
    Route::prefix('categories')->group(function () {
        Route::get('/', [ForumController::class, 'getCategories']);
        Route::get('/{slug}', [ForumController::class, 'getCategory']);
        Route::get('/{slug}/forums', [ForumController::class, 'getCategoryForums']);
    });

    // Threads routes (public)
    Route::prefix('threads')->group(function () {
        Route::get('/', [ThreadController::class, 'index']);
        Route::get('/{slug}', [ThreadController::class, 'show']);
        Route::get('/{slug}/comments', [ThreadController::class, 'getComments']);
        Route::get('/{slug}/media', [MediaController::class, 'getThreadMedia']);
    });

    // Search routes (public)
    Route::prefix('search')->group(function () {
        Route::get('/', [SearchController::class, 'search']);
        Route::get('/suggestions', [SearchController::class, 'suggestions']);
    });

    // SEO routes (public)
    Route::prefix('seo')->group(function () {
        Route::get('/', [SeoController::class, 'index']);
        Route::get('/pages/{slug}', [SeoController::class, 'getPageSeo']);
        Route::get('/threads/{slug}', [SeoController::class, 'getThreadSeo']);
        Route::get('/forums/{slug}', [SeoController::class, 'getForumSeo']);
        Route::get('/categories/{slug}', [SeoController::class, 'getCategorySeo']);
        Route::get('/users/{username}', [SeoController::class, 'getUserSeo']);
    });

    // Showcase routes (public)
    Route::prefix('showcases')->group(function () {
        Route::get('/', [ShowcaseController::class, 'index']);
        Route::get('/{slug}', [ShowcaseController::class, 'show']);
    });
    
    // Stats routes (public)
    Route::prefix('stats')->group(function () {
        Route::get('/forum', [StatsController::class, 'getForumStats']);
        Route::get('/forums/popular', [StatsController::class, 'getPopularForums']);
        Route::get('/users/active', [StatsController::class, 'getActiveUsers']);
        Route::get('/threads/featured', [StatsController::class, 'getFeaturedThreads']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // User info
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Auth routes (protected)
        Route::prefix('auth')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
        });

        // Users routes (protected)
        Route::prefix('users')->group(function () {
            Route::put('/{username}', [UserController::class, 'update']);
            Route::delete('/{username}', [UserController::class, 'destroy']);
            Route::post('/{username}/follow', [UserController::class, 'follow']);
            Route::delete('/{username}/follow', [UserController::class, 'unfollow']);
            Route::post('/avatar', [UserController::class, 'updateAvatar']);
        });

        // Threads routes (protected)
        Route::prefix('threads')->group(function () {
            Route::post('/', [ThreadController::class, 'store']);
            Route::put('/{slug}', [ThreadController::class, 'update']);
            Route::delete('/{slug}', [ThreadController::class, 'destroy']);
            Route::post('/{slug}/like', [ThreadController::class, 'like']);
            Route::delete('/{slug}/like', [ThreadController::class, 'unlike']);
            Route::post('/{slug}/save', [ThreadController::class, 'save']);
            Route::delete('/{slug}/save', [ThreadController::class, 'unsave']);
            Route::post('/{slug}/follow', [ThreadController::class, 'follow']);
            Route::delete('/{slug}/follow', [ThreadController::class, 'unfollow']);
            Route::get('/saved', [ThreadController::class, 'getSaved']);
            Route::get('/followed', [ThreadController::class, 'getFollowed']);
            Route::post('/{slug}/comments', [CommentController::class, 'store']);
        });

        // Comments routes (protected)
        Route::prefix('comments')->group(function () {
            Route::put('/{id}', [CommentController::class, 'update']);
            Route::delete('/{id}', [CommentController::class, 'destroy']);
            Route::post('/{id}/like', [CommentController::class, 'like']);
            Route::delete('/{id}/like', [CommentController::class, 'unlike']);
            Route::get('/{id}/replies', [CommentController::class, 'getReplies']);
            Route::post('/{id}/replies', [CommentController::class, 'storeReply']);
        });

        // Alerts routes (protected)
        Route::prefix('alerts')->group(function () {
            Route::get('/', [AlertController::class, 'index']);
            Route::post('/{id}/read', [AlertController::class, 'markAsRead']);
            Route::delete('/{id}', [AlertController::class, 'destroy']);
            Route::post('/read-all', [AlertController::class, 'markAllAsRead']);
        });

        // Conversations routes (protected)
        Route::prefix('conversations')->group(function () {
            Route::get('/', [ConversationController::class, 'index']);
            Route::post('/', [ConversationController::class, 'store']);
            Route::get('/{id}', [ConversationController::class, 'show']);
            Route::post('/{id}/messages', [ConversationController::class, 'sendMessage']);
            Route::post('/{id}/read', [ConversationController::class, 'markAsRead']);
        });

        // Media routes (protected)
        Route::prefix('media')->group(function () {
            Route::get('/', [MediaController::class, 'index']);
            Route::post('/', [MediaController::class, 'store']);
            Route::get('/{id}', [MediaController::class, 'show']);
            Route::put('/{id}', [MediaController::class, 'update']);
            Route::delete('/{id}', [MediaController::class, 'destroy']);
        });

        // Showcase routes (protected)
        Route::prefix('showcases')->group(function () {
            Route::post('/', [ShowcaseController::class, 'store']);
            Route::put('/{slug}', [ShowcaseController::class, 'update']);
            Route::delete('/{slug}', [ShowcaseController::class, 'destroy']);
        });
    });
});
