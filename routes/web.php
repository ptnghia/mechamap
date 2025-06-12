<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\FollowingController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ShowcaseController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\NewContentController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserThreadController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ThreadActionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/api/threads', [\App\Http\Controllers\HomeController::class, 'getMoreThreads'])->name('api.threads');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified.social'])->name('dashboard');

// Profile routes
Route::get('/users', [ProfileController::class, 'index'])->name('users.index');
Route::get('/users/{user:username}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/users/{user:username}/activities', [ActivityController::class, 'index'])->name('profile.activities');

Route::middleware('auth')->group(function () {
    // Profile edit routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

    // Follow/unfollow routes
    Route::post('/users/{user:username}/follow', [ProfileController::class, 'follow'])->name('profile.follow');
    Route::delete('/users/{user:username}/unfollow', [ProfileController::class, 'unfollow'])->name('profile.unfollow');

    // Profile post routes
    Route::post('/users/{user:username}/posts', [ProfileController::class, 'storeProfilePost'])->name('profile.posts.store');

    // Thread Actions - Simple Form Submissions
    Route::post('/threads/{thread}/bookmark', [ThreadActionController::class, 'addBookmark'])->name('threads.bookmark.add');
    Route::delete('/threads/{thread}/bookmark', [ThreadActionController::class, 'removeBookmark'])->name('threads.bookmark.remove');
    Route::post('/threads/{thread}/follow', [ThreadActionController::class, 'addFollow'])->name('threads.follow.add');
    Route::delete('/threads/{thread}/follow', [ThreadActionController::class, 'removeFollow'])->name('threads.follow.remove');

    // Following routes
    Route::get('/following', [FollowingController::class, 'index'])->name('following.index');
    Route::get('/followers', [FollowingController::class, 'followers'])->name('following.followers');
    Route::get('/followed-threads', [FollowingController::class, 'threads'])->name('following.threads');
    Route::get('/participated-discussions', [FollowingController::class, 'participated'])->name('following.participated');

    // Alerts routes
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::patch('/alerts/{alert}/read', [AlertController::class, 'markAsRead'])->name('alerts.read');
    Route::delete('/alerts/{alert}', [AlertController::class, 'destroy'])->name('alerts.destroy');
    Route::post('/alerts/read-all', [AlertController::class, 'markAllAsRead'])->name('alerts.read-all');

    // Conversations routes
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations', [ConversationController::class, 'store'])->name('conversations.store');
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'storeMessage'])->name('conversations.messages.store');

    // Bookmarks routes
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::delete('/bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');

    // Showcase routes
    Route::get('/showcase', [ShowcaseController::class, 'index'])->name('showcase.index');
    Route::get('/showcase/create', [ShowcaseController::class, 'create'])->name('showcase.create');
    Route::post('/showcase', [ShowcaseController::class, 'store'])->name('showcase.store');
    Route::get('/showcase/{showcase}', [ShowcaseController::class, 'show'])->name('showcase.show');
    Route::get('/showcase/{showcase}/edit', [ShowcaseController::class, 'edit'])->name('showcase.edit');
    Route::put('/showcase/{showcase}', [ShowcaseController::class, 'update'])->name('showcase.update');
    Route::delete('/showcase/{showcase}', [ShowcaseController::class, 'destroy'])->name('showcase.destroy');
    Route::post('/showcase/upload-temp', [ShowcaseController::class, 'uploadTemp'])->name('showcase.upload.temp');
    Route::post('/showcase/{showcase}/comment', [ShowcaseController::class, 'addComment'])->name('showcase.comment');
    Route::delete('/showcase/comment/{comment}', [ShowcaseController::class, 'deleteComment'])->name('showcase.comment.delete');
    Route::post('/showcase/{showcase}/like', [ShowcaseController::class, 'toggleLike'])->name('showcase.toggle-like');
    Route::post('/showcase/{showcase}/follow', [ShowcaseController::class, 'toggleFollow'])->name('showcase.toggle-follow');
    Route::post('/showcase/{showcase}/bookmark', [ShowcaseController::class, 'toggleBookmark'])->name('showcase.bookmark');
    Route::get('/showcase/attachment/{attachment}/download', [ShowcaseController::class, 'downloadAttachment'])->name('showcase.download');
    Route::post('/showcase/{showcase}/attach-to-thread', [ShowcaseController::class, 'attachToThread'])->name('showcase.attach-to-thread');

    // Business routes
    Route::get('/business', [BusinessController::class, 'index'])->name('business.index');
    Route::get('/business/services', [BusinessController::class, 'services'])->name('business.services');

    // Subscription routes
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
    Route::get('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
});

// Main menu routes
Route::get('/new', [NewContentController::class, 'index'])->name('new');

// Forum routes with caching middleware
Route::middleware('forum.cache')->group(function () {
    Route::get('/forums', [ForumController::class, 'index'])->name('forums.index');
    Route::get('/forums/search', [ForumController::class, 'search'])->name('forums.search');
    Route::get('/forums/{forum}', [ForumController::class, 'show'])->name('forums.show');
});

// Thread routes (MUST be before wildcard routes)
Route::resource('threads', \App\Http\Controllers\ThreadController::class);

// User Thread Browse Routes (Public and Authenticated)
Route::prefix('browse')->name('browse.threads.')->group(function () {
    // Public routes
    Route::get('/threads', [UserThreadController::class, 'index'])->name('index');
    Route::get('/threads/top-rated', [UserThreadController::class, 'topRated'])->name('top-rated');
    Route::get('/threads/trending', [UserThreadController::class, 'trending'])->name('trending');
    Route::get('/threads/by-tag/{tag}', [UserThreadController::class, 'byTag'])->name('by-tag');
    Route::get('/threads/by-forum/{forum}', [UserThreadController::class, 'byForum'])->name('by-forum');
    Route::get('/threads/search', [UserThreadController::class, 'search'])->name('search');
});

// User Dashboard Routes (Authenticated only)
Route::middleware('auth')->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Bookmark management
    Route::get('/bookmarks', [UserDashboardController::class, 'bookmarks'])->name('bookmarks');
    Route::post('/bookmarks/search', [UserDashboardController::class, 'searchBookmarks'])->name('bookmarks.search');
    Route::post('/bookmarks/folders', [UserDashboardController::class, 'createFolder'])->name('bookmarks.folders.create');
    Route::put('/bookmarks/folders/{folder}', [UserDashboardController::class, 'updateFolder'])->name('bookmarks.folders.update');
    Route::delete('/bookmarks/folders/{folder}', [UserDashboardController::class, 'deleteFolder'])->name('bookmarks.folders.delete');

    // Rating management
    Route::get('/ratings', [UserDashboardController::class, 'ratings'])->name('ratings');

    // My threads
    Route::get('/my-threads', [UserDashboardController::class, 'myThreads'])->name('my-threads');

    // Activity feed
    Route::get('/activity', [UserDashboardController::class, 'activity'])->name('activity');

    // Settings routes
    Route::get('/settings', [UserDashboardController::class, 'settings'])->name('settings');
    Route::post('/settings/profile', [UserDashboardController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [UserDashboardController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/preferences', [UserDashboardController::class, 'updatePreferences'])->name('settings.preferences');
    Route::post('/settings/notifications', [UserDashboardController::class, 'updateNotifications'])->name('settings.notifications');
    Route::post('/settings/privacy', [UserDashboardController::class, 'updatePrivacy'])->name('settings.privacy');
    Route::delete('/settings/delete-account', [UserDashboardController::class, 'deleteAccount'])->name('settings.delete-account');
});

// Enhanced thread interaction routes (override existing ones)
Route::middleware('auth')->group(function () {
    // Thread bookmarking with folders - COMMENTED OUT to avoid conflict with ThreadActionController
    // Route::post('/threads/{thread}/bookmark', [\App\Http\Controllers\ThreadBookmarkController::class, 'store'])->name('threads.bookmark');
    // Route::delete('/threads/{thread}/bookmark', [\App\Http\Controllers\ThreadBookmarkController::class, 'destroy'])->name('threads.bookmark.remove');

    // Thread rating
    Route::post('/threads/{thread}/rate', [\App\Http\Controllers\ThreadRatingController::class, 'store'])->name('threads.rate');
    Route::put('/threads/{thread}/rate', [\App\Http\Controllers\ThreadRatingController::class, 'update'])->name('threads.rate.update');
    Route::delete('/threads/{thread}/rate', [\App\Http\Controllers\ThreadRatingController::class, 'destroy'])->name('threads.rate.remove');
});

// More menu routes
Route::get('/whats-new', [NewContentController::class, 'whatsNew'])->name('whats-new');
// Redirect old forum-listing to unified forums page for backward compatibility
Route::get('/forum-listing', function () {
    return redirect()->route('forums.index', [], 301);
});
Route::get('/public-showcase', [ShowcaseController::class, 'publicShowcase'])->name('showcase.public');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/advanced-search', [SearchController::class, 'advanced'])->name('search.advanced');
Route::post('/advanced-search', [SearchController::class, 'advancedSearch'])->name('search.advanced.submit');
Route::get('/ajax-search', [SearchController::class, 'ajaxSearch'])->name('search.ajax');
Route::get('/members', [MemberController::class, 'index'])->name('members.index');
Route::get('/members/online', [MemberController::class, 'online'])->name('members.online');
Route::get('/members/staff', [MemberController::class, 'staff'])->name('members.staff');
Route::get('/members/leaderboard', [MemberController::class, 'leaderboard'])->name('members.leaderboard');
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

// Theme routes
Route::post('/theme/dark-mode', [ThemeController::class, 'toggleDarkMode'])->name('theme.dark-mode');
Route::post('/theme/original-view', [ThemeController::class, 'toggleOriginalView'])->name('theme.original-view');

// Forum and thread routes (duplicate route removed - already defined in middleware group above)
Route::get('/categories/{category:slug}', [\App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');
Route::get('/create-thread', [\App\Http\Controllers\ForumSelectionController::class, 'index'])->name('forums.select')->middleware('auth');
Route::post('/create-thread', [\App\Http\Controllers\ForumSelectionController::class, 'selectForum'])->name('forums.select.submit')->middleware('auth');

// Gallery routes
Route::middleware('auth')->group(function () {
    Route::get('/gallery/create', [GalleryController::class, 'create'])->name('gallery.create');
    Route::post('/gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::delete('/gallery/{media}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
});
Route::get('/gallery/{media}', [GalleryController::class, 'show'])->name('gallery.show');

// Admin routes - Sử dụng file route riêng
Route::prefix('admin')->name('admin.')->group(function () {
    require base_path('routes/admin.php');
});

// Social Login Routes
Route::get('/auth/{provider}', [\App\Http\Controllers\Auth\SocialiteController::class, 'redirectToProvider'])->name('auth.socialite');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\Auth\SocialiteController::class, 'handleProviderCallback']);

// Comment routes
Route::post('/threads/{thread}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('threads.comments.store')->middleware('auth');
Route::put('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'update'])->name('comments.update')->middleware('auth');
Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy')->middleware('auth');
Route::post('/comments/{comment}/like', [\App\Http\Controllers\CommentController::class, 'like'])->name('comments.like')->middleware('auth');

// Thread like/save/follow routes
Route::post('/threads/{thread}/like', [\App\Http\Controllers\ThreadLikeController::class, 'toggle'])->name('threads.like')->middleware('auth');
Route::post('/threads/{thread}/save', [\App\Http\Controllers\ThreadSaveController::class, 'toggle'])->name('threads.save')->middleware('auth');
// Route::post('/threads/{thread}/follow', [\App\Http\Controllers\ThreadFollowController::class, 'toggle'])->name('threads.follow.toggle')->middleware('auth'); // COMMENTED OUT - conflict with ThreadActionController
Route::get('/saved-threads', [\App\Http\Controllers\ThreadSaveController::class, 'index'])->name('threads.saved')->middleware('auth');

// Poll routes
Route::post('/threads/{thread}/polls', [\App\Http\Controllers\PollController::class, 'store'])->name('threads.polls.store')->middleware('auth');
Route::post('/polls/{poll}/vote', [\App\Http\Controllers\PollController::class, 'vote'])->name('polls.vote')->middleware('auth');

// Admin Moderation routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|moderator'])->group(function () {
    Route::prefix('moderation')->name('moderation.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\ModerationController::class, 'dashboard'])->name('dashboard');
        Route::get('/threads', [\App\Http\Controllers\Admin\ModerationController::class, 'threads'])->name('threads');
        Route::get('/comments', [\App\Http\Controllers\Admin\ModerationController::class, 'comments'])->name('comments');
        Route::get('/statistics', [\App\Http\Controllers\Admin\ModerationController::class, 'statistics'])->name('statistics');
        Route::get('/user-activity', [\App\Http\Controllers\Admin\ModerationController::class, 'userActivity'])->name('user-activity');

        // Thread moderation actions
        Route::post('/threads/{thread}/approve', [\App\Http\Controllers\Admin\ModerationController::class, 'approveThread'])->name('threads.approve');
        Route::post('/threads/{thread}/reject', [\App\Http\Controllers\Admin\ModerationController::class, 'rejectThread'])->name('threads.reject');
        Route::post('/threads/{thread}/flag', [\App\Http\Controllers\Admin\ModerationController::class, 'flagThread'])->name('threads.flag');
        Route::post('/threads/bulk-action', [\App\Http\Controllers\Admin\ModerationController::class, 'bulkActionThreads'])->name('threads.bulk-action');

        // Comment moderation actions
        Route::post('/comments/{comment}/approve', [\App\Http\Controllers\Admin\ModerationController::class, 'approveComment'])->name('comments.approve');
        Route::delete('/comments/{comment}', [\App\Http\Controllers\Admin\ModerationController::class, 'deleteComment'])->name('comments.delete');
    });
});

// Static pages routes
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/rules', function () {
    return view('pages.rules');
})->name('rules');

Route::get('/help/writing-guide', function () {
    return view('pages.writing-guide');
})->name('help.writing-guide');

// Contact page
Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

// Contact support - redirect to main contact page
Route::get('/contact/support', function () {
    return redirect()->route('contact');
})->name('contact.support');

// Include What's New routes
require __DIR__ . '/web-whats-new.php';
require __DIR__ . '/auth.php';

// Test routes - chỉ trong development
if (app()->environment('local', 'development')) {
    Route::get('/test/thread-actions', function () {
        $threads = \App\Models\Thread::with(['user', 'category', 'forum'])
            ->latest()
            ->take(5)
            ->get();

        return view('test.thread-actions', compact('threads'));
    })->name('test.thread-actions')->middleware('auth');
}

// Web-based API routes for JavaScript calls (with CSRF protection)
Route::middleware(['webapi', 'auth'])->prefix('api/threads/{thread}')->group(function () {
    Route::post('/bookmark', [App\Http\Controllers\Api\ThreadQualityController::class, 'bookmarkThread']);
    Route::delete('/bookmark', [App\Http\Controllers\Api\ThreadQualityController::class, 'removeBookmark']);
    Route::post('/follow', [App\Http\Controllers\Api\ThreadQualityController::class, 'followThread']);
    Route::delete('/follow', [App\Http\Controllers\Api\ThreadQualityController::class, 'unfollowThread']);
});

// Test route for thread actions (only in development)
if (app()->environment('local')) {
    Route::get('/test-thread-actions-simple', function () {
        return view('test-thread-actions');
    })->name('test.thread-actions.simple');

    Route::get('/test-js-conflict', function () {
        return view('test-js_conflict');
    })->name('test.js-conflict');

    Route::get('/test-auth-actions', function () {
        return view('test-auth-actions');
    })->name('test.auth-actions');
}
