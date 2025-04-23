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

    // Follow/unfollow routes
    Route::post('/users/{user:username}/follow', [ProfileController::class, 'follow'])->name('profile.follow');
    Route::delete('/users/{user:username}/unfollow', [ProfileController::class, 'unfollow'])->name('profile.unfollow');

    // Profile post routes
    Route::post('/users/{user:username}/posts', [ProfileController::class, 'storeProfilePost'])->name('profile.posts.store');

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
    Route::post('/showcase', [ShowcaseController::class, 'store'])->name('showcase.store');
    Route::delete('/showcase/{showcase}', [ShowcaseController::class, 'destroy'])->name('showcase.destroy');

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
Route::get('/forums', [ForumController::class, 'index'])->name('forums.index');

// More menu routes
Route::get('/whats-new', [NewContentController::class, 'whatsNew'])->name('whats-new');
Route::get('/forum-listing', [ForumController::class, 'listing'])->name('forums.listing');
Route::get('/public-showcase', [ShowcaseController::class, 'publicShowcase'])->name('showcase.public');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/advanced-search', [SearchController::class, 'advanced'])->name('search.advanced');
Route::post('/advanced-search', [SearchController::class, 'advancedSearch'])->name('search.advanced.submit');
Route::get('/ajax-search', [SearchController::class, 'ajaxSearch'])->name('search.ajax');
Route::get('/members', [MemberController::class, 'index'])->name('members.index');
Route::get('/members/online', [MemberController::class, 'online'])->name('members.online');
Route::get('/members/staff', [MemberController::class, 'staff'])->name('members.staff');
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

// Theme routes
Route::post('/theme/dark-mode', [ThemeController::class, 'toggleDarkMode'])->name('theme.dark-mode');
Route::post('/theme/original-view', [ThemeController::class, 'toggleOriginalView'])->name('theme.original-view');

// Forum and thread routes
Route::get('/forums/{forum}', [ForumController::class, 'show'])->name('forums.show');
Route::get('/create-thread', [\App\Http\Controllers\ForumSelectionController::class, 'index'])->name('forums.select')->middleware('auth');
Route::post('/create-thread', [\App\Http\Controllers\ForumSelectionController::class, 'selectForum'])->name('forums.select.submit')->middleware('auth');

// Gallery routes
Route::middleware('auth')->group(function () {
    Route::get('/gallery/create', [GalleryController::class, 'create'])->name('gallery.create');
    Route::post('/gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::delete('/gallery/{media}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
});
Route::get('/gallery/{media}', [GalleryController::class, 'show'])->name('gallery.show');

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin auth routes
    Route::middleware('guest')->group(function () {
        Route::get('login', [\App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.submit');
    });

    // Admin authenticated routes
    Route::middleware('admin.auth')->group(function () {
        Route::post('logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Profile routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('index');
            Route::put('/', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('update');
            Route::get('/password', [\App\Http\Controllers\Admin\ProfileController::class, 'showChangePasswordForm'])->name('password');
            Route::put('/password', [\App\Http\Controllers\Admin\ProfileController::class, 'changePassword'])->name('password.update');
        });

        // User management routes
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::put('users/{user}/toggle-ban', [\App\Http\Controllers\Admin\UserController::class, 'toggleBan'])->name('users.toggle-ban');
        Route::put('users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.reset-password');

        // Thread management routes
        Route::resource('threads', \App\Http\Controllers\Admin\ThreadController::class);
        Route::put('threads/{thread}/approve', [\App\Http\Controllers\Admin\ThreadController::class, 'approve'])->name('threads.approve');
        Route::put('threads/{thread}/reject', [\App\Http\Controllers\Admin\ThreadController::class, 'reject'])->name('threads.reject');
        Route::get('threads-statistics', [\App\Http\Controllers\Admin\ThreadController::class, 'statistics'])->name('threads.statistics');

        // Category management routes
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::post('categories/reorder', [\App\Http\Controllers\Admin\CategoryController::class, 'reorder'])->name('categories.reorder');

        // Forum management routes
        Route::resource('forums', \App\Http\Controllers\Admin\ForumController::class);
        Route::post('forums/reorder', [\App\Http\Controllers\Admin\ForumController::class, 'reorder'])->name('forums.reorder');

        // Comment management routes
        Route::resource('comments', \App\Http\Controllers\Admin\CommentController::class);
        Route::put('comments/{comment}/toggle-visibility', [\App\Http\Controllers\Admin\CommentController::class, 'toggleVisibility'])->name('comments.toggle-visibility');
        Route::put('comments/{comment}/toggle-flag', [\App\Http\Controllers\Admin\CommentController::class, 'toggleFlag'])->name('comments.toggle-flag');
        Route::get('comments-statistics', [\App\Http\Controllers\Admin\CommentController::class, 'statistics'])->name('comments.statistics');

        // Statistics routes
        Route::prefix('statistics')->name('statistics.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\StatisticsController::class, 'index'])->name('index');
            Route::get('/users', [\App\Http\Controllers\Admin\StatisticsController::class, 'users'])->name('users');
            Route::get('/content', [\App\Http\Controllers\Admin\StatisticsController::class, 'content'])->name('content');
            Route::get('/interactions', [\App\Http\Controllers\Admin\StatisticsController::class, 'interactions'])->name('interactions');
            Route::post('/export', [\App\Http\Controllers\Admin\StatisticsController::class, 'export'])->name('export');
        });

        // Page management routes
        Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);
        Route::resource('page-categories', \App\Http\Controllers\Admin\PageCategoryController::class);
        Route::post('page-categories/reorder', [\App\Http\Controllers\Admin\PageCategoryController::class, 'reorder'])->name('page-categories.reorder');

        // FAQ management routes
        Route::resource('faqs', \App\Http\Controllers\Admin\FaqController::class);
        Route::put('faqs/{faq}/toggle-status', [\App\Http\Controllers\Admin\FaqController::class, 'toggleStatus'])->name('faqs.toggle-status');
        Route::resource('faq-categories', \App\Http\Controllers\Admin\FaqCategoryController::class);
        Route::put('faq-categories/{faq_category}/toggle-status', [\App\Http\Controllers\Admin\FaqCategoryController::class, 'toggleStatus'])->name('faq-categories.toggle-status');
        Route::post('faq-categories/reorder', [\App\Http\Controllers\Admin\FaqCategoryController::class, 'reorder'])->name('faq-categories.reorder');

        // Media management routes
        Route::resource('media', \App\Http\Controllers\Admin\MediaController::class);
        Route::get('media/{media}/download', [\App\Http\Controllers\Admin\MediaController::class, 'download'])->name('media.download');
        Route::get('media-library', [\App\Http\Controllers\Admin\MediaController::class, 'library'])->name('media.library');

        // SEO routes
        Route::prefix('seo')->name('seo.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SeoController::class, 'index'])->name('index');
            Route::put('/', [\App\Http\Controllers\Admin\SeoController::class, 'updateGeneral'])->name('update-general');

            Route::get('/robots', [\App\Http\Controllers\Admin\SeoController::class, 'robots'])->name('robots');
            Route::put('/robots', [\App\Http\Controllers\Admin\SeoController::class, 'updateRobots'])->name('update-robots');

            Route::get('/sitemap', [\App\Http\Controllers\Admin\SeoController::class, 'sitemap'])->name('sitemap');
            Route::post('/sitemap', [\App\Http\Controllers\Admin\SeoController::class, 'generateSitemap'])->name('generate-sitemap');
            Route::delete('/sitemap', [\App\Http\Controllers\Admin\SeoController::class, 'deleteSitemap'])->name('delete-sitemap');

            Route::get('/social', [\App\Http\Controllers\Admin\SeoController::class, 'social'])->name('social');
            Route::put('/social', [\App\Http\Controllers\Admin\SeoController::class, 'updateSocial'])->name('update-social');

            Route::get('/advanced', [\App\Http\Controllers\Admin\SeoController::class, 'advanced'])->name('advanced');
            Route::put('/advanced', [\App\Http\Controllers\Admin\SeoController::class, 'updateAdvanced'])->name('update-advanced');
        });

        // Page SEO routes
        Route::resource('page-seo', \App\Http\Controllers\Admin\PageSeoController::class);

        // Settings routes
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/general', [\App\Http\Controllers\Admin\SettingsController::class, 'general'])->name('general');
            Route::put('/general', [\App\Http\Controllers\Admin\SettingsController::class, 'updateGeneral'])->name('update-general');

            Route::get('/company', [\App\Http\Controllers\Admin\SettingsController::class, 'company'])->name('company');
            Route::put('/company', [\App\Http\Controllers\Admin\SettingsController::class, 'updateCompany'])->name('update-company');

            Route::get('/contact', [\App\Http\Controllers\Admin\SettingsController::class, 'contact'])->name('contact');
            Route::put('/contact', [\App\Http\Controllers\Admin\SettingsController::class, 'updateContact'])->name('update-contact');

            Route::get('/social', [\App\Http\Controllers\Admin\SettingsController::class, 'social'])->name('social');
            Route::put('/social', [\App\Http\Controllers\Admin\SettingsController::class, 'updateSocial'])->name('update-social');

            Route::get('/api', [\App\Http\Controllers\Admin\SettingsController::class, 'api'])->name('api');
            Route::put('/api', [\App\Http\Controllers\Admin\SettingsController::class, 'updateApi'])->name('update-api');

            Route::get('/copyright', [\App\Http\Controllers\Admin\SettingsController::class, 'copyright'])->name('copyright');
            Route::put('/copyright', [\App\Http\Controllers\Admin\SettingsController::class, 'updateCopyright'])->name('update-copyright');
        });

        // Thêm các route admin khác ở đây
    });
});

// Social Login Routes
Route::get('/auth/{provider}', [\App\Http\Controllers\Auth\SocialiteController::class, 'redirectToProvider'])->name('auth.socialite');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\Auth\SocialiteController::class, 'handleProviderCallback']);

// Thread routes
Route::resource('threads', \App\Http\Controllers\ThreadController::class);

// Comment routes
Route::post('/threads/{thread}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('threads.comments.store')->middleware('auth');
Route::put('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'update'])->name('comments.update')->middleware('auth');
Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy')->middleware('auth');
Route::post('/comments/{comment}/like', [\App\Http\Controllers\CommentController::class, 'like'])->name('comments.like')->middleware('auth');

// Thread like/save/follow routes
Route::post('/threads/{thread}/like', [\App\Http\Controllers\ThreadLikeController::class, 'toggle'])->name('threads.like')->middleware('auth');
Route::post('/threads/{thread}/save', [\App\Http\Controllers\ThreadSaveController::class, 'toggle'])->name('threads.save')->middleware('auth');
Route::post('/threads/{thread}/follow', [\App\Http\Controllers\ThreadFollowController::class, 'toggle'])->name('threads.follow.toggle')->middleware('auth');
Route::get('/saved-threads', [\App\Http\Controllers\ThreadSaveController::class, 'index'])->name('threads.saved')->middleware('auth');

// Poll routes
Route::post('/threads/{thread}/polls', [\App\Http\Controllers\PollController::class, 'store'])->name('threads.polls.store')->middleware('auth');
Route::post('/polls/{poll}/vote', [\App\Http\Controllers\PollController::class, 'vote'])->name('polls.vote')->middleware('auth');

require __DIR__ . '/auth.php';
