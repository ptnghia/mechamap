<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ThreadController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ForumController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PageCategoryController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FaqCategoryController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ShowcaseController;
use App\Http\Controllers\Admin\AlertController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\SearchController;
use Illuminate\Support\Facades\Route;

// Admin auth routes (không cần phân quyền)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
});

// Admin authenticated routes (cần phân quyền)
Route::middleware('admin.auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes (tất cả admin và moderator đều có quyền)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/password', [ProfileController::class, 'showChangePasswordForm'])->name('password');
        Route::put('/password', [ProfileController::class, 'changePassword'])->name('password.update');
    });

    // User management routes (chỉ admin có quyền ban_users)
    Route::middleware(['admin.auth'])->prefix('users')->name('users.')->group(function () {
        // Trang chủ quản lý thành viên
        Route::get('/', [UserController::class, 'index'])->name('index');

        // Quản lý thành viên quản trị (Admin và Moderator)
        Route::get('/admins', [UserController::class, 'admins'])->name('admins');
        Route::get('/admins/create', [UserController::class, 'createAdmin'])->name('admins.create');
        Route::post('/admins', [UserController::class, 'storeAdmin'])->name('admins.store');
        Route::get('/admins/{user}/edit', [UserController::class, 'editAdmin'])->name('admins.edit');
        Route::put('/admins/{user}', [UserController::class, 'updateAdmin'])->name('admins.update');
        Route::get('/admins/{user}/permissions', [UserController::class, 'editPermissions'])->name('admins.permissions');
        Route::put('/admins/{user}/permissions', [UserController::class, 'updatePermissions'])->name('admins.permissions.update');

        // Quản lý thành viên thường (Senior và Member)
        Route::get('/members', [UserController::class, 'members'])->name('members');
        Route::get('/members/create', [UserController::class, 'create'])->name('members.create');
        Route::post('/members', [UserController::class, 'store'])->name('members.store');
        Route::get('/members/{user}', [UserController::class, 'show'])->name('members.show');
        Route::get('/members/{user}/edit', [UserController::class, 'edit'])->name('members.edit');
        Route::put('/members/{user}', [UserController::class, 'update'])->name('members.update');

        // Route chung cho cả hai loại thành viên
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::put('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

        // Chức năng chung
        Route::put('/{user}/toggle-ban', [UserController::class, 'toggleBan'])->name('toggle-ban');
    });

    // Thread management routes (admin và moderator có quyền moderate_posts)
    Route::middleware(['admin.auth'])->group(function () {
        Route::resource('threads', ThreadController::class);
        Route::put('threads/{thread}/approve', [ThreadController::class, 'approve'])->name('threads.approve');
        Route::put('threads/{thread}/reject', [ThreadController::class, 'reject'])->name('threads.reject');
        Route::get('threads-statistics', [ThreadController::class, 'statistics'])->name('threads.statistics');
    });

    // Category management routes (chỉ admin có quyền manage_categories)
    Route::middleware(['admin.auth'])->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
    });

    // Forum management routes (chỉ admin có quyền manage_categories)
    Route::middleware(['admin.auth'])->group(function () {
        Route::resource('forums', ForumController::class);
        Route::post('forums/reorder', [ForumController::class, 'reorder'])->name('forums.reorder');
    });

    // Comment management routes (admin và moderator có quyền moderate_posts)
    Route::middleware(['admin.auth'])->group(function () {
        Route::resource('comments', CommentController::class);
        Route::put('comments/{comment}/toggle-visibility', [CommentController::class, 'toggleVisibility'])->name('comments.toggle-visibility');
        Route::put('comments/{comment}/toggle-flag', [CommentController::class, 'toggleFlag'])->name('comments.toggle-flag');
        Route::get('comments-statistics', [CommentController::class, 'statistics'])->name('comments.statistics');
    });

    // Statistics routes (tất cả admin và moderator đều có quyền)
    Route::prefix('statistics')->name('statistics.')->group(function () {
        Route::get('/', [StatisticsController::class, 'index'])->name('index');
        Route::get('/users', [StatisticsController::class, 'users'])->name('users');
        Route::get('/content', [StatisticsController::class, 'content'])->name('content');
        Route::get('/interactions', [StatisticsController::class, 'interactions'])->name('interactions');
        Route::match(['get', 'post'], '/export', [StatisticsController::class, 'export'])->name('export');
    });

    // Page management routes (chỉ admin có quyền manage_system)
    Route::middleware(['admin.auth'])->group(function () {
        Route::resource('pages', PageController::class);
        Route::resource('page-categories', PageCategoryController::class);
        Route::post('page-categories/reorder', [PageCategoryController::class, 'reorder'])->name('page-categories.reorder');
    });

    // FAQ management routes (admin và moderator có quyền)
    Route::resource('faqs', FaqController::class);
    Route::put('faqs/{faq}/toggle-status', [FaqController::class, 'toggleStatus'])->name('faqs.toggle-status');
    Route::resource('faq-categories', FaqCategoryController::class);
    Route::put('faq-categories/{faq_category}/toggle-status', [FaqCategoryController::class, 'toggleStatus'])->name('faq-categories.toggle-status');
    Route::post('faq-categories/reorder', [FaqCategoryController::class, 'reorder'])->name('faq-categories.reorder');

    // Media management routes (admin và moderator có quyền)
    Route::resource('media', MediaController::class);
    Route::get('media/{media}/download', [MediaController::class, 'download'])->name('media.download');
    Route::get('media-library', [MediaController::class, 'library'])->name('media.library');

    // SEO management routes (chỉ admin có quyền manage_system)
    Route::middleware(['admin.auth'])->prefix('seo')->name('seo.')->group(function () {
        Route::get('/', [SeoController::class, 'index'])->name('index');
        Route::put('/', [SeoController::class, 'updateGeneral'])->name('update-general');
        Route::get('/robots', [SeoController::class, 'robots'])->name('robots');
        Route::put('/robots', [SeoController::class, 'updateRobots'])->name('update-robots');
        Route::get('/sitemap', [SeoController::class, 'sitemap'])->name('sitemap');
        Route::post('/sitemap', [SeoController::class, 'generateSitemap'])->name('generate-sitemap');
    });

    // Note: Showcase routes được định nghĩa bằng resource route ở dưới

    // Settings management routes (chỉ admin có quyền manage_system)
    Route::middleware(['admin.auth'])->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::get('/general', [SettingsController::class, 'general'])->name('general');
        Route::put('/general', [SettingsController::class, 'updateGeneral'])->name('update-general');
        Route::get('/company', [SettingsController::class, 'company'])->name('company');
        Route::put('/company', [SettingsController::class, 'updateCompany'])->name('update-company');
        Route::get('/contact', [SettingsController::class, 'contact'])->name('contact');
        Route::put('/contact', [SettingsController::class, 'updateContact'])->name('update-contact');
        Route::get('/social', [SettingsController::class, 'social'])->name('social');
        Route::put('/social', [SettingsController::class, 'updateSocial'])->name('update-social');
        Route::get('/api', [SettingsController::class, 'api'])->name('api');
        Route::put('/api', [SettingsController::class, 'updateApi'])->name('update-api');
        Route::get('/copyright', [SettingsController::class, 'copyright'])->name('copyright');
        Route::put('/copyright', [SettingsController::class, 'updateCopyright'])->name('update-copyright');
        Route::get('/forum', [SettingsController::class, 'forum'])->name('forum');
        Route::put('/forum', [SettingsController::class, 'updateForum'])->name('update-forum');
        Route::get('/user', [SettingsController::class, 'user'])->name('user');
        Route::put('/user', [SettingsController::class, 'updateUser'])->name('update-user');
        Route::get('/email', [SettingsController::class, 'email'])->name('email');
        Route::put('/email', [SettingsController::class, 'updateEmail'])->name('email.update');
        Route::post('/email/test-connection', [SettingsController::class, 'testEmailConnection'])->name('email.test-connection');
        Route::get('/security', [SettingsController::class, 'security'])->name('security');
        Route::put('/security', [SettingsController::class, 'updateSecurity'])->name('security.update');
        Route::get('/wiki', [SettingsController::class, 'wiki'])->name('wiki');
        Route::put('/wiki', [SettingsController::class, 'updateWiki'])->name('wiki.update');
    });

    // Showcase management routes (admin và moderator có quyền)
    Route::prefix('showcases')->name('showcases.')->group(function () {
        Route::get('/', [ShowcaseController::class, 'index'])->name('index');
        Route::get('/create', [ShowcaseController::class, 'create'])->name('create');
        Route::post('/', [ShowcaseController::class, 'store'])->name('store');
        Route::get('/{id}', [ShowcaseController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ShowcaseController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ShowcaseController::class, 'update'])->name('update');
        Route::delete('/{id}', [ShowcaseController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-featured', [ShowcaseController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/{id}/toggle-active', [ShowcaseController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/update-order', [ShowcaseController::class, 'updateOrder'])->name('update-order');
        Route::post('/bulk-action', [ShowcaseController::class, 'bulkAction'])->name('bulk-action');
    });

    // Search management routes (chỉ admin có quyền manage_system)
    Route::middleware(['admin.auth'])->prefix('search')->name('search.')->group(function () {
        Route::get('/', [SearchController::class, 'index'])->name('index');
        Route::put('/settings', [SearchController::class, 'updateSettings'])->name('settings.update');
        Route::get('/reindex', [SearchController::class, 'reindex'])->name('reindex');
        Route::post('/reindex', [SearchController::class, 'performReindex'])->name('reindex.perform');
        Route::get('/statistics', [SearchController::class, 'statistics'])->name('statistics');
        Route::get('/test', [SearchController::class, 'test'])->name('test');
        Route::post('/test', [SearchController::class, 'performTest'])->name('test.perform');
        Route::post('/optimize', [SearchController::class, 'optimizeIndex'])->name('optimize');
        Route::post('/clear-cache', [SearchController::class, 'clearCache'])->name('clear-cache');
        Route::get('/export-statistics', [SearchController::class, 'exportStatistics'])->name('export-statistics');
        Route::post('/rebuild-suggestions', [SearchController::class, 'rebuildSuggestions'])->name('rebuild-suggestions');
    });

    // Alerts management routes (chỉ admin có quyền manage_system)
    Route::middleware(['admin.auth'])->prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/', [AlertController::class, 'index'])->name('index');
        Route::put('/settings', [AlertController::class, 'updateSettings'])->name('settings.update');
        Route::get('/test', [AlertController::class, 'testAlert'])->name('test');
        Route::post('/test', [AlertController::class, 'sendTestAlert'])->name('test.send');
        Route::get('/statistics', [AlertController::class, 'statistics'])->name('statistics');
        Route::post('/cleanup', [AlertController::class, 'cleanupOldAlerts'])->name('cleanup');
        Route::get('/export-statistics', [AlertController::class, 'exportStatistics'])->name('export-statistics');
    });

    // Messages management routes (chỉ admin có quyền manage_system)
    Route::middleware(['admin.auth'])->prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::put('/settings', [MessageController::class, 'updateSettings'])->name('settings.update');
        Route::get('/conversations', [MessageController::class, 'conversations'])->name('conversations');
        Route::get('/conversations/{id}', [MessageController::class, 'showConversation'])->name('conversations.show');
        Route::delete('/conversations/{id}', [MessageController::class, 'deleteConversation'])->name('conversations.destroy');
        Route::get('/statistics', [MessageController::class, 'statistics'])->name('statistics');
        Route::post('/cleanup', [MessageController::class, 'cleanup'])->name('cleanup');
        Route::get('/export-statistics', [MessageController::class, 'exportStatistics'])->name('export-statistics');
    });

    // SEO management routes (chỉ admin có quyền manage_system)
    Route::middleware(['admin.auth'])->prefix('seo')->name('seo.')->group(function () {
        Route::get('/', [SeoController::class, 'index'])->name('index');
        Route::put('/general', [SeoController::class, 'updateGeneral'])->name('general.update');
        Route::get('/robots', [SeoController::class, 'robots'])->name('robots');
        Route::put('/robots', [SeoController::class, 'updateRobots'])->name('robots.update');
        Route::get('/sitemap', [SeoController::class, 'sitemap'])->name('sitemap');
        Route::post('/sitemap/generate', [SeoController::class, 'generateSitemap'])->name('sitemap.generate');
        Route::delete('/sitemap', [SeoController::class, 'deleteSitemap'])->name('sitemap.delete');
        Route::get('/social', [SeoController::class, 'social'])->name('social');
        Route::put('/social', [SeoController::class, 'updateSocial'])->name('social.update');
        Route::get('/advanced', [SeoController::class, 'advanced'])->name('advanced');
        Route::put('/advanced', [SeoController::class, 'updateAdvanced'])->name('advanced.update');
        Route::get('/analytics', [SeoController::class, 'analytics'])->name('analytics');
        Route::get('/audit', [SeoController::class, 'audit'])->name('audit');
        Route::post('/submit-sitemap', [SeoController::class, 'submitSitemap'])->name('submit-sitemap');
        Route::post('/check-indexing', [SeoController::class, 'checkIndexing'])->name('check-indexing');
    });
});
