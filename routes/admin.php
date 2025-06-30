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
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\PageSeoController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ShowcaseController;
use App\Http\Controllers\Admin\AlertController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\DasonDashboardController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\DynamicDashboardController;
use Illuminate\Support\Facades\Route;

// Admin auth routes (không cần phân quyền)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
});

// Admin authenticated routes (cần phân quyền)
Route::middleware(['admin.redirect', App\Http\Middleware\AdminAccessMiddleware::class])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('logout', [AuthController::class, 'logoutGet'])->name('logout.get');

    // Dynamic Dashboard - Phase 2
    Route::get('/', [DynamicDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/realtime', [DynamicDashboardController::class, 'getRealtimeData'])->name('dashboard.realtime');

    // Legacy dashboard (backup)
    Route::get('/legacy', [DashboardController::class, 'index'])->name('dashboard.legacy');

    // Profile routes (tất cả admin và moderator đều có quyền)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/password', [ProfileController::class, 'showChangePasswordForm'])->name('password');
        Route::put('/password', [ProfileController::class, 'changePassword'])->name('password.update');
    });

    // Roles & Permissions management routes (chỉ admin có quyền)
    Route::middleware(['admin.auth'])->prefix('roles')->name('roles.')->group(function () {
        Route::get('/', function() {
            return view('admin.roles.index');
        })->name('index');
        Route::get('/create', function() {
            return view('admin.roles.create');
        })->name('create');
        Route::get('/{role}/edit', function($role) {
            return view('admin.roles.edit', compact('role'));
        })->name('edit');
    });

    // User management routes (cần quyền view_users)
    Route::middleware(['admin.permission:view_users'])->prefix('users')->name('users.')->group(function () {
        // Trang chủ quản lý thành viên
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');

        // Quản lý thành viên quản trị (cần quyền manage_admins)
        Route::middleware(['admin.permission:manage_admins'])->group(function () {
            Route::get('/admins', [UserController::class, 'admins'])->name('admins');
            Route::get('/admins/create', [UserController::class, 'createAdmin'])->name('admins.create');
            Route::post('/admins', [UserController::class, 'storeAdmin'])->name('admins.store');
            Route::get('/admins/{user}/edit', [UserController::class, 'editAdmin'])->name('admins.edit');
            Route::put('/admins/{user}', [UserController::class, 'updateAdmin'])->name('admins.update');
            Route::get('/admins/{user}/permissions', [UserController::class, 'editPermissions'])->name('admins.permissions');
            Route::put('/admins/{user}/permissions', [UserController::class, 'updatePermissions'])->name('admins.permissions.update');
        });

        // Quản lý thành viên thường (Senior và Member)
        Route::get('/members', [UserController::class, 'members'])->name('members');
        Route::get('/members/export', [UserController::class, 'exportMembers'])->name('members.export');
        Route::get('/members/create', [UserController::class, 'create'])->name('members.create');
        Route::post('/members', [UserController::class, 'store'])->name('members.store');
        Route::get('/members/{user}', [UserController::class, 'show'])->name('members.show');
        Route::get('/members/{user}/edit', [UserController::class, 'edit'])->name('members.edit');
        Route::put('/members/{user}', [UserController::class, 'update'])->name('members.update');

        // Quản lý admin export
        Route::get('/admins/export', [UserController::class, 'exportAdmins'])->name('admins.export');

        // Route chung cho cả hai loại thành viên
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::put('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

        // Chức năng chung
        Route::put('/{user}/toggle-ban', [UserController::class, 'toggleBan'])->name('toggle-ban');

        // Bulk actions và toggle status
        Route::post('/bulk-action', [UserController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Thread management routes (cần quyền moderate-content)
    Route::middleware(['admin.permission:moderate-content'])->group(function () {
        Route::resource('threads', ThreadController::class);
        Route::put('threads/{thread}/approve', [ThreadController::class, 'approve'])->name('threads.approve');
        Route::put('threads/{thread}/reject', [ThreadController::class, 'reject'])->name('threads.reject');
        Route::get('threads-statistics', [ThreadController::class, 'statistics'])->name('threads.statistics');
    });

    // Test route để debug middleware
    Route::get('/test-middleware', function() {
        return response()->json([
            'message' => 'Middleware working!',
            'user' => auth()->user() ? auth()->user()->email : 'Not authenticated',
            'timestamp' => now()
        ]);
    })->middleware(['admin.auth']);

    // Moderation management routes (cần quyền moderate-content hoặc view-reports)
    Route::middleware(['admin.auth'])->prefix('moderation')->name('moderation.')->group(function () {
        // Dashboard tổng quan
        Route::get('/', [ModerationController::class, 'index'])->name('index');
        Route::get('/dashboard', [ModerationController::class, 'dashboard'])->name('dashboard');

        // Quản lý threads
        Route::get('/threads', [ModerationController::class, 'threads'])->name('threads');
        Route::post('/threads/{thread}/approve', [ModerationController::class, 'approveThread'])->name('threads.approve');
        Route::post('/threads/{thread}/reject', [ModerationController::class, 'rejectThread'])->name('threads.reject');
        Route::post('/threads/{thread}/flag', [ModerationController::class, 'flagThread'])->name('threads.flag');
        Route::post('/threads/bulk-action', [ModerationController::class, 'bulkActionThreads'])->name('threads.bulk-action');

        // Quản lý comments
        Route::get('/comments', [ModerationController::class, 'comments'])->name('comments');
        Route::post('/comments/{comment}/approve', [ModerationController::class, 'approveComment'])->name('comments.approve');
        Route::post('/comments/{comment}/reject', [ModerationController::class, 'rejectComment'])->name('comments.reject');
        Route::post('/comments/{comment}/flag', [ModerationController::class, 'flagComment'])->name('comments.flag');
        Route::post('/comments/bulk-action', [ModerationController::class, 'bulkActionComments'])->name('comments.bulk-action');

        // Reports management (cần quyền view-reports)
        Route::middleware(['admin.permission:view-reports'])->group(function () {
            Route::get('/reports', [ModerationController::class, 'reports'])->name('reports');
            Route::post('/reports/{report}/resolve', [ModerationController::class, 'resolveReport'])->name('reports.resolve');
            Route::post('/reports/{report}/dismiss', [ModerationController::class, 'dismissReport'])->name('reports.dismiss');
            Route::post('/reports/bulk-action', [ModerationController::class, 'bulkActionReports'])->name('reports.bulk-action');
        });

        // Thống kê
        Route::get('/statistics', [ModerationController::class, 'statistics'])->name('statistics');
        Route::get('/user-activity', [ModerationController::class, 'userActivity'])->name('user-activity');

        // AJAX endpoints
        Route::post('/threads/{thread}/quick-action', [ModerationController::class, 'quickActionThread'])->name('threads.quick-action');
        Route::post('/comments/{comment}/quick-action', [ModerationController::class, 'quickActionComment'])->name('comments.quick-action');

        // Quick moderation actions
        Route::post('/quick-approve/{type}/{id}', [ModerationController::class, 'quickApprove'])->name('quick-approve');
        Route::post('/quick-reject/{type}/{id}', [ModerationController::class, 'quickReject'])->name('quick-reject');
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

    // Comment management routes (cần quyền moderate-content)
    Route::middleware(['admin.permission:moderate-content'])->group(function () {
        Route::resource('comments', CommentController::class);
        Route::put('comments/{comment}/toggle-visibility', [CommentController::class, 'toggleVisibility'])->name('comments.toggle-visibility');
        Route::put('comments/{comment}/toggle-flag', [CommentController::class, 'toggleFlag'])->name('comments.toggle-flag');
        Route::get('comments-statistics', [CommentController::class, 'statistics'])->name('comments.statistics');
    });

    // Reports management routes (cần quyền view-reports)
    Route::middleware(['admin.permission:view-reports'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', function() {
            return view('admin.reports.index');
        })->name('index');
        Route::get('/{report}', function($report) {
            return view('admin.reports.show', compact('report'));
        })->name('show');
        Route::put('/{report}/resolve', function($report) {
            return redirect()->back()->with('success', 'Report resolved successfully');
        })->name('resolve');
    });

    // Statistics routes (tất cả admin và moderator đều có quyền)
    Route::prefix('statistics')->name('statistics.')->group(function () {
        Route::get('/', [StatisticsController::class, 'index'])->name('index');
        Route::get('/users', [StatisticsController::class, 'users'])->name('users');
        Route::get('/content', [StatisticsController::class, 'content'])->name('content');
        Route::get('/interactions', [StatisticsController::class, 'interactions'])->name('interactions');
        Route::match(['get', 'post'], '/export', [StatisticsController::class, 'export'])->name('export');
    });

    // Advanced Analytics routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('index');
        Route::get('/revenue', [App\Http\Controllers\Admin\AnalyticsController::class, 'revenue'])->name('revenue');
        Route::get('/users', [App\Http\Controllers\Admin\AnalyticsController::class, 'users'])->name('users');
        Route::get('/content', [App\Http\Controllers\Admin\AnalyticsController::class, 'content'])->name('content');
        Route::get('/marketplace', [App\Http\Controllers\Admin\AnalyticsController::class, 'marketplace'])->name('marketplace');
        Route::get('/technical', [App\Http\Controllers\Admin\AnalyticsController::class, 'technical'])->name('technical');
        Route::get('/export', [App\Http\Controllers\Admin\AnalyticsController::class, 'export'])->name('export');
        Route::get('/realtime-data', [App\Http\Controllers\Admin\AnalyticsController::class, 'realtime'])->name('realtime.data');

        // Real-time Analytics Dashboard
        Route::get('/realtime', [App\Http\Controllers\Admin\RealtimeAnalyticsController::class, 'dashboard'])->name('realtime');
        Route::get('/realtime/metrics', [App\Http\Controllers\Admin\RealtimeAnalyticsController::class, 'getRealtimeMetrics'])->name('realtime.metrics');
        Route::get('/realtime/predictive', [App\Http\Controllers\Admin\RealtimeAnalyticsController::class, 'getPredictiveAnalytics'])->name('realtime.predictive');

        // Custom KPI Builder
        Route::prefix('kpi')->name('kpi.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\CustomKPIController::class, 'index'])->name('index');
            Route::post('/create', [App\Http\Controllers\Admin\CustomKPIController::class, 'create'])->name('create');
            Route::post('/calculate', [App\Http\Controllers\Admin\CustomKPIController::class, 'calculate'])->name('calculate');
            Route::get('/dashboard', [App\Http\Controllers\Admin\CustomKPIController::class, 'dashboard'])->name('dashboard');
            Route::get('/export', [App\Http\Controllers\Admin\CustomKPIController::class, 'export'])->name('export');
        });

        // Business Analytics
        Route::prefix('business')->name('business.')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Admin\BusinessAnalyticsController::class, 'dashboard'])->name('dashboard');
            Route::get('/marketplace', [App\Http\Controllers\Admin\BusinessAnalyticsController::class, 'marketplace'])->name('marketplace');
            Route::get('/revenue', [App\Http\Controllers\Admin\BusinessAnalyticsController::class, 'revenue'])->name('revenue');
            Route::get('/commissions', [App\Http\Controllers\Admin\BusinessAnalyticsController::class, 'commissions'])->name('commissions');
            Route::get('/realtime-metrics', [App\Http\Controllers\Admin\BusinessAnalyticsController::class, 'getRealtimeMetrics'])->name('realtime.metrics');
        });
    });

    // Performance & Security routes
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PerformanceController::class, 'index'])->name('index');
        Route::get('/cache', [App\Http\Controllers\Admin\PerformanceController::class, 'cache'])->name('cache');
        Route::get('/database', [App\Http\Controllers\Admin\PerformanceController::class, 'database'])->name('database');
        Route::get('/security', [App\Http\Controllers\Admin\PerformanceController::class, 'security'])->name('security');

        // Performance actions
        Route::post('/clear-cache', [App\Http\Controllers\Admin\PerformanceController::class, 'clearCache'])->name('clear-cache');
        Route::post('/warm-up-cache', [App\Http\Controllers\Admin\PerformanceController::class, 'warmUpCache'])->name('warm-up-cache');
        Route::post('/optimize-database', [App\Http\Controllers\Admin\PerformanceController::class, 'optimizeDatabase'])->name('optimize-database');
        Route::post('/toggle-maintenance', [App\Http\Controllers\Admin\PerformanceController::class, 'toggleMaintenanceMode'])->name('toggle-maintenance');

        // API endpoints
        Route::get('/metrics', [App\Http\Controllers\Admin\PerformanceController::class, 'realtimeMetrics'])->name('metrics');
        Route::get('/audit-logs', [App\Http\Controllers\Admin\PerformanceController::class, 'auditLogs'])->name('audit-logs');
        Route::get('/export-report', [App\Http\Controllers\Admin\PerformanceController::class, 'exportReport'])->name('export-report');
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

    // Knowledge Base management routes
    Route::prefix('knowledge')->name('knowledge.')->group(function () {
        Route::get('/', function() {
            return view('admin.knowledge.index');
        })->name('index');
        Route::get('/create', function() {
            return view('admin.knowledge.create');
        })->name('create');
        Route::get('/{article}/edit', function($article) {
            return view('admin.knowledge.edit', compact('article'));
        })->name('edit');
    });

    // Marketplace management routes
    Route::prefix('marketplace')->name('marketplace.')->group(function () {
        Route::get('/', function() {
            return view('admin.marketplace.index');
        })->name('index');

        // Products management
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MarketplaceProductController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\MarketplaceProductController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\MarketplaceProductController::class, 'store'])->name('store');
            Route::get('/{product}', [App\Http\Controllers\Admin\MarketplaceProductController::class, 'show'])->name('show');
            Route::get('/{product}/edit', [App\Http\Controllers\Admin\MarketplaceProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [App\Http\Controllers\Admin\MarketplaceProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [App\Http\Controllers\Admin\MarketplaceProductController::class, 'destroy'])->name('destroy');

            // Bulk actions
            Route::post('/bulk-approve', [App\Http\Controllers\Admin\MarketplaceProductController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/bulk-reject', [App\Http\Controllers\Admin\MarketplaceProductController::class, 'bulkReject'])->name('bulk-reject');
            Route::post('/{product}/toggle-featured', [App\Http\Controllers\Admin\MarketplaceProductController::class, 'toggleFeatured'])->name('toggle-featured');
        });

        // Orders management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MarketplaceOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [App\Http\Controllers\Admin\MarketplaceOrderController::class, 'show'])->name('show');
            Route::put('/{order}/status', [App\Http\Controllers\Admin\MarketplaceOrderController::class, 'updateStatus'])->name('update-status');
            Route::put('/{order}/payment-status', [App\Http\Controllers\Admin\MarketplaceOrderController::class, 'updatePaymentStatus'])->name('update-payment-status');
            Route::post('/{order}/cancel', [App\Http\Controllers\Admin\MarketplaceOrderController::class, 'cancel'])->name('cancel');
            Route::get('/{order}/invoice', [App\Http\Controllers\Admin\MarketplaceOrderController::class, 'generateInvoice'])->name('invoice');
            Route::get('/export/excel', [App\Http\Controllers\Admin\MarketplaceOrderController::class, 'export'])->name('export');
            Route::get('/statistics/data', [App\Http\Controllers\Admin\MarketplaceOrderController::class, 'getStatistics'])->name('statistics');
        });

        // Sellers management
        Route::prefix('sellers')->name('sellers.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MarketplaceSellerController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\MarketplaceSellerController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\MarketplaceSellerController::class, 'store'])->name('store');
            Route::get('/{seller}', [App\Http\Controllers\Admin\MarketplaceSellerController::class, 'show'])->name('show');
            Route::get('/{seller}/edit', [App\Http\Controllers\Admin\MarketplaceSellerController::class, 'edit'])->name('edit');
            Route::put('/{seller}', [App\Http\Controllers\Admin\MarketplaceSellerController::class, 'update'])->name('update');
            Route::delete('/{seller}', [App\Http\Controllers\Admin\MarketplaceSellerController::class, 'destroy'])->name('destroy');

            // Seller actions
            Route::post('/{seller}/verify', [App\Http\Controllers\Admin\MarketplaceSellerController::class, 'verify'])->name('verify');
            Route::post('/{seller}/reject', [App\Http\Controllers\Admin\MarketplaceSellerController::class, 'reject'])->name('reject');
            Route::post('/{seller}/toggle-featured', [App\Http\Controllers\Admin\MarketplaceSellerController::class, 'toggleFeatured'])->name('toggle-featured');
            Route::post('/{seller}/suspend', [App\Http\Controllers\Admin\MarketplaceSellerController::class, 'suspend'])->name('suspend');
            Route::post('/{seller}/reactivate', [App\Http\Controllers\Admin\MarketplaceSellerController::class, 'reactivate'])->name('reactivate');
            Route::get('/{seller}/earnings', [App\Http\Controllers\Admin\MarketplaceSellerController::class, 'earningsReport'])->name('earnings');
        });
    });

    // Technical Management routes
    // Technical Management (với permission checking)
    Route::prefix('technical')->name('technical.')->group(function () {
        // Technical Drawings (cần quyền view_cad_files)
        Route::middleware(['admin.permission:view_cad_files'])->prefix('drawings')->name('drawings.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\TechnicalDrawingController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\TechnicalDrawingController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\TechnicalDrawingController::class, 'store'])->name('store');
            Route::get('/{drawing}', [App\Http\Controllers\Admin\TechnicalDrawingController::class, 'show'])->name('show');
            Route::get('/{drawing}/edit', [App\Http\Controllers\Admin\TechnicalDrawingController::class, 'edit'])->name('edit');
            Route::put('/{drawing}', [App\Http\Controllers\Admin\TechnicalDrawingController::class, 'update'])->name('update');
            Route::delete('/{drawing}', [App\Http\Controllers\Admin\TechnicalDrawingController::class, 'destroy'])->name('destroy');
            Route::get('/{drawing}/download', [App\Http\Controllers\Admin\TechnicalDrawingController::class, 'download'])->name('download');

            // Bulk actions
            Route::post('/bulk-approve', [App\Http\Controllers\Admin\TechnicalDrawingController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/{drawing}/toggle-featured', [App\Http\Controllers\Admin\TechnicalDrawingController::class, 'toggleFeatured'])->name('toggle-featured');
        });

        // CAD Files (cần quyền view_cad_files)
        Route::middleware(['admin.permission:view_cad_files'])->prefix('cad-files')->name('cad-files.')->group(function () {
            Route::get('/', function() {
                return view('admin.technical.cad-files.index');
            })->name('index');
            Route::get('/create', function() {
                return view('admin.technical.cad-files.create');
            })->name('create');
        });

        // Materials (cần quyền view_materials)
        Route::middleware(['admin.permission:view_materials'])->prefix('materials')->name('materials.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MaterialController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\MaterialController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\MaterialController::class, 'store'])->name('store');
            Route::get('/{material}', [App\Http\Controllers\Admin\MaterialController::class, 'show'])->name('show');
            Route::get('/{material}/edit', [App\Http\Controllers\Admin\MaterialController::class, 'edit'])->name('edit');
            Route::put('/{material}', [App\Http\Controllers\Admin\MaterialController::class, 'update'])->name('update');
            Route::delete('/{material}', [App\Http\Controllers\Admin\MaterialController::class, 'destroy'])->name('destroy');
            Route::get('/{material}/datasheet', [App\Http\Controllers\Admin\MaterialController::class, 'downloadDatasheet'])->name('datasheet');

            // Material actions
            Route::post('/{material}/toggle-featured', [App\Http\Controllers\Admin\MaterialController::class, 'toggleFeatured'])->name('toggle-featured');
            Route::post('/compare', [App\Http\Controllers\Admin\MaterialController::class, 'compare'])->name('compare');
        });

        // Manufacturing Processes (cần quyền view_materials)
        Route::middleware(['admin.permission:view_materials'])->prefix('processes')->name('processes.')->group(function () {
            Route::get('/', function() {
                return view('admin.technical.processes.index');
            })->name('index');
            Route::get('/create', function() {
                return view('admin.technical.processes.create');
            })->name('create');
        });

        // Engineering Standards (cần quyền view_standards)
        Route::middleware(['admin.permission:view_standards'])->prefix('standards')->name('standards.')->group(function () {
            Route::get('/', function() {
                return view('admin.technical.standards.index');
            })->name('index');
            Route::get('/create', function() {
                return view('admin.technical.standards.create');
            })->name('create');
        });
    });

    // Global Search route
    Route::get('/search/global', function() {
        $query = request('q');
        // Placeholder for global search functionality
        return view('admin.search.global', compact('query'));
    })->name('search.global');

    // Language switcher routes
    Route::get('/language/{locale}', function($locale) {
        if (in_array($locale, ['vi', 'en'])) {
            session(['locale' => $locale]);
        }
        return redirect()->back();
    })->name('language.switch');

    // Notifications routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', function() {
            return view('admin.notifications.index');
        })->name('index');
        Route::post('/{id}/mark-read', function($id) {
            // Mark notification as read
            return response()->json(['success' => true]);
        })->name('mark-read');
    });

    // Media management routes (admin và moderator có quyền)
    Route::resource('media', MediaController::class);
    Route::get('media/{media}/download', [MediaController::class, 'download'])->name('media.download');
    Route::get('media-library', [MediaController::class, 'library'])->name('media.library');
    Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::get('media/stats', [MediaController::class, 'stats'])->name('media.stats');
    Route::put('media/{media}/approve', [MediaController::class, 'approve'])->name('media.approve');
    Route::get('media/user/{user?}', [MediaController::class, 'userMedia'])->name('media.user');

    // Countries & Regions management routes (admin có quyền)
    Route::middleware(['admin.auth'])->group(function () {
        Route::resource('countries', CountryController::class);
        Route::resource('regions', RegionController::class);
        Route::get('regions/country/{country}', [RegionController::class, 'byCountry'])->name('regions.by-country');
        Route::get('regions/featured', [RegionController::class, 'featured'])->name('regions.featured');
    });

    // SEO management routes được định nghĩa ở cuối file

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

        // SEO Settings (redirect to SEO management)
        Route::get('/seo', function() {
            return redirect()->route('admin.seo.index');
        })->name('seo');

        // Payment Settings
        Route::get('/payment', function() {
            return view('admin.settings.payment');
        })->name('payment');
        Route::put('/payment', function() {
            return redirect()->back()->with('success', 'Payment settings updated successfully');
        })->name('payment.update');
    });

    // Showcase management routes (admin và moderator có quyền)
    Route::prefix('showcases')->name('showcases.')->group(function () {
        Route::get('/', [ShowcaseController::class, 'index'])->name('index');
        Route::get('/pending', function() {
            return view('admin.showcases.pending');
        })->name('pending');
        Route::get('/featured', function() {
            return view('admin.showcases.featured');
        })->name('featured');
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
    // Search management routes (chỉ admin và moderator có quyền)
    Route::middleware(['admin.auth'])->prefix('search')->name('search.')->group(function () {
        Route::get('/', [SearchController::class, 'index'])->name('index');
        Route::put('/settings', [SearchController::class, 'updateSettings'])->name('settings.update');
        Route::put('/', [SearchController::class, 'updateSettings'])->name('update');
        Route::get('/reindex', [SearchController::class, 'reindex'])->name('reindex');
        Route::post('/reindex', [SearchController::class, 'performReindex'])->name('reindex.perform');
        Route::get('/statistics', [SearchController::class, 'statistics'])->name('statistics');
        Route::get('/test', [SearchController::class, 'test'])->name('test');
        Route::post('/test', [SearchController::class, 'performTest'])->name('test.perform');
        Route::post('/optimize', [SearchController::class, 'optimizeIndex'])->name('optimize');
        Route::post('/clear-cache', [SearchController::class, 'clearCache'])->name('clear-cache');
        Route::get('/export-statistics', [SearchController::class, 'exportStatistics'])->name('export-statistics');
        Route::post('/rebuild-suggestions', [SearchController::class, 'rebuildSuggestions'])->name('rebuild-suggestions');

        // Analytics routes
        Route::get('/analytics', [SearchController::class, 'analytics'])->name('analytics');
        Route::get('/analytics/api', [SearchController::class, 'analyticsApi'])->name('analytics.api');
        Route::get('/analytics/recent', [SearchController::class, 'analyticsRecent'])->name('analytics.recent');
        Route::get('/analytics/export', [SearchController::class, 'analyticsExport'])->name('analytics.export');

        // API routes for search logging and testing
        Route::post('/log', [SearchController::class, 'logSearch'])->name('log');
        Route::post('/test-api', [SearchController::class, 'testSearch'])->name('test.api');
    });

    // Alerts management routes (chỉ admin có quyền manage_system)
    Route::middleware(['admin.auth'])->prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/', [AlertController::class, 'index'])->name('index');
        Route::put('/settings', [AlertController::class, 'updateSettings'])->name('settings.update');
        Route::put('/', [AlertController::class, 'updateSettings'])->name('update');
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
        Route::put('/general', [SeoController::class, 'updateGeneral'])->name('update-general');
        Route::get('/robots', [SeoController::class, 'robots'])->name('robots');
        Route::put('/robots', [SeoController::class, 'updateRobots'])->name('update-robots');
        Route::get('/sitemap', [SeoController::class, 'sitemap'])->name('sitemap');
        Route::post('/sitemap/generate', [SeoController::class, 'generateSitemap'])->name('sitemap.generate');
        Route::delete('/sitemap', [SeoController::class, 'deleteSitemap'])->name('sitemap.delete');
        Route::get('/social', [SeoController::class, 'social'])->name('social');
        Route::put('/social', [SeoController::class, 'updateSocial'])->name('update-social');
        Route::get('/advanced', [SeoController::class, 'advanced'])->name('advanced');
        Route::put('/advanced', [SeoController::class, 'updateAdvanced'])->name('update-advanced');
        Route::get('/analytics', [SeoController::class, 'analytics'])->name('analytics');
        Route::get('/audit', [SeoController::class, 'audit'])->name('audit');
        Route::post('/submit-sitemap', [SeoController::class, 'submitSitemap'])->name('submit-sitemap');
        Route::post('/check-indexing', [SeoController::class, 'checkIndexing'])->name('check-indexing');
    });

    // Page SEO management routes (chỉ admin có quyền manage_system)
    Route::middleware(['admin.auth'])->resource('page-seo', PageSeoController::class);

    // ===== DASON TEMPLATE INTEGRATION ROUTES =====

    // New Dason-powered dashboard
    Route::get('/dason', [DasonDashboardController::class, 'index'])->name('dason.dashboard');

    // Test route for Dason dashboard (temporary)
    Route::get('/dashboard-test', function() {
        return view('admin.dashboard-test');
    })->name('dashboard.test');

    // Analytics routes with Dason UI
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/overview', [DasonDashboardController::class, 'index'])->name('overview');
        Route::get('/marketplace', [DasonDashboardController::class, 'marketplaceAnalytics'])->name('marketplace');
        Route::get('/users', [DasonDashboardController::class, 'userAnalytics'])->name('users');
        Route::get('/revenue', [DasonDashboardController::class, 'index'])->name('revenue');
        Route::get('/export', [DasonDashboardController::class, 'exportData'])->name('export');
    });

    // Marketplace management with Dason UI (với permission checking)
    Route::prefix('marketplace')->name('marketplace.')->group(function () {
        // Products (cần quyền view_products)
        Route::middleware(['admin.permission:view_products'])->group(function () {
            Route::get('/products', function() {
                return view('admin.marketplace.products-dason');
            })->name('products.index');
        });

        // Orders (cần quyền view_orders)
        Route::middleware(['admin.permission:view_orders'])->group(function () {
            Route::get('/orders', function() {
                return view('admin.marketplace.orders-dason');
            })->name('orders.index');
        });

        // Sellers (cần quyền manage_sellers)
        Route::middleware(['admin.permission:manage_sellers'])->group(function () {
            Route::get('/sellers', function() {
                return view('admin.marketplace.sellers-dason');
            })->name('sellers.index');
        });

        // Categories (cần quyền view_products)
        Route::middleware(['admin.permission:view_products'])->group(function () {
            Route::get('/categories', function() {
                return view('admin.marketplace.categories-dason');
            })->name('categories.index');
        });

        // Transactions (cần quyền view_payments)
        Route::middleware(['admin.permission:view_payments'])->group(function () {
            Route::get('/transactions', function() {
                return view('admin.marketplace.transactions-dason');
            })->name('transactions.index');
        });
    });

    // Direct marketplace routes for sidebar compatibility
    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/products', function() {
            return redirect()->route('admin.marketplace.products.index');
        })->name('products.index');

        Route::get('/orders', function() {
            return redirect()->route('admin.marketplace.orders.index');
        })->name('orders.index');

        Route::get('/sellers', function() {
            return redirect()->route('admin.marketplace.sellers.index');
        })->name('sellers.index');

        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', function() {
                return view('admin.payments.index');
            })->name('index');
            Route::get('/{payment}', function($payment) {
                return view('admin.payments.show', compact('payment'));
            })->name('show');
        });
    });

    // Admin Chat routes (tất cả admin và moderator đều có quyền)
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/create', [ChatController::class, 'create'])->name('create');
        Route::post('/', [ChatController::class, 'store'])->name('store');
        Route::get('/{id}', [ChatController::class, 'show'])->name('show');
        Route::post('/{id}/send', [ChatController::class, 'sendMessage'])->name('send');

        // API routes for admin chat
        Route::get('/api/search-users', [ChatController::class, 'searchUsers'])->name('api.search-users');
        Route::get('/api/unread-count', [ChatController::class, 'getUnreadCount'])->name('api.unread-count');
    });

    // Duplicate moderation routes removed - using the one with middleware above (lines 125-151)
});
