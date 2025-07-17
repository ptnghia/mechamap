<?php

use Illuminate\Http\Request;
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
use App\Http\Controllers\AdvancedSearchController;
// use App\Http\Controllers\RealTimeController; // Removed - using Node.js WebSocket server
use App\Http\Controllers\MemberController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserThreadController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ThreadActionController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Frontend\BusinessRegistrationController;
use App\Http\Controllers\AvatarController;
use Illuminate\Support\Facades\Route;

// Test route để debug - REMOVED (should only be in development)









// Trang chủ chính tại route /
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Giữ lại route /homepage để backward compatibility
Route::get('/homepage', function () {
    return redirect('/');
});

// Welcome route for backward compatibility
Route::get('/welcome', [\App\Http\Controllers\HomeController::class, 'index'])->name('welcome');

// Load More threads route cho trang chủ
Route::get('/threads/load-more', [\App\Http\Controllers\HomeController::class, 'getMoreThreads'])->name('threads.load-more');

// Avatar generation routes
Route::prefix('avatar')->name('avatar.')->group(function () {
    Route::get('/{initial}', [AvatarController::class, 'generate'])->name('generate');
    Route::delete('/cache/{initial?}', [AvatarController::class, 'clearCache'])->name('clear-cache');
});

// Test route đơn giản - REMOVED (should only be in development)

// Language switching routes
Route::prefix('language')->name('language.')->group(function () {
    Route::get('/switch/{locale}', [LanguageController::class, 'switch'])->name('switch');
    Route::get('/current', [LanguageController::class, 'current'])->name('current');
    Route::get('/supported', [LanguageController::class, 'supported'])->name('supported');
    Route::post('/auto-detect', [LanguageController::class, 'autoDetect'])->name('auto-detect');
});

// What's New route - REMOVED (duplicate, using WhatsNewController in web-whats-new.php)

// Marketplace routes
Route::prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/', [App\Http\Controllers\MarketplaceController::class, 'index'])->name('index');
    Route::get('/products', [App\Http\Controllers\MarketplaceController::class, 'products'])->name('products.index');
    Route::get('/products/new', [App\Http\Controllers\MarketplaceController::class, 'newProducts'])->name('products.new');
    Route::get('/products/popular', [App\Http\Controllers\MarketplaceController::class, 'popularProducts'])->name('products.popular');
    Route::get('/products/{slug}', [App\Http\Controllers\MarketplaceController::class, 'show'])->name('products.show');
    Route::get('/suppliers', [App\Http\Controllers\MarketplaceController::class, 'suppliers'])->name('suppliers.index');
    Route::get('/suppliers/{slug}', [App\Http\Controllers\MarketplaceController::class, 'seller'])->name('sellers.show');
    Route::get('/categories', [App\Http\Controllers\MarketplaceController::class, 'categories'])->name('categories.index');
    Route::get('/categories/{slug}', [App\Http\Controllers\MarketplaceController::class, 'category'])->name('categories.show');

    // Enhanced Business Tools
    Route::prefix('rfq')->name('rfq.')->group(function () {
        Route::get('/', [App\Http\Controllers\RFQController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\RFQController::class, 'create'])->name('create')->middleware('auth');
        Route::post('/', [App\Http\Controllers\RFQController::class, 'store'])->name('store')->middleware('auth');
        Route::get('/{rfq}', [App\Http\Controllers\RFQController::class, 'show'])->name('show');
        Route::post('/{rfq}/quote', [App\Http\Controllers\RFQController::class, 'submitQuote'])->name('submit-quote')->middleware('auth');
        Route::post('/{rfq}/accept/{response}', [App\Http\Controllers\RFQController::class, 'acceptQuote'])->name('accept-quote')->middleware('auth');
        Route::get('/api/stats', [App\Http\Controllers\RFQController::class, 'getStats'])->name('stats');
    });

    Route::get('/bulk-orders', [App\Http\Controllers\MarketplaceController::class, 'bulkOrders'])->name('bulk-orders');
    Route::get('/orders', [App\Http\Controllers\MarketplaceOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\MarketplaceOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/items/{item}/download', [App\Http\Controllers\MarketplaceOrderController::class, 'downloadFile'])->name('orders.download-file')->where(['order' => '[0-9]+', 'item' => '[0-9]+']);

    Route::get('/orders/{order}/items/{item}/download-simple', function($orderId, $itemId) {
        $order = App\Models\MarketplaceOrder::findOrFail($orderId);
        $item = App\Models\MarketplaceOrderItem::findOrFail($itemId);
        return response()->json([
            'order' => $order->order_number,
            'item' => $item->product_name,
            'product_type' => $item->product->product_type,
            'digital_files' => $item->product->digital_files,
            'message' => 'Simple route works'
        ]);
    });

    // Secure Download routes
    Route::prefix('downloads')->name('downloads.')->group(function () {
        Route::get('/', [App\Http\Controllers\MarketplaceDownloadController::class, 'index'])->name('index');
        Route::get('/orders/{order}/files', [App\Http\Controllers\MarketplaceDownloadController::class, 'orderFiles'])->name('order-files');
        Route::get('/items/{orderItem}/files', [App\Http\Controllers\MarketplaceDownloadController::class, 'itemFiles'])->name('item-files');
        Route::post('/generate-token', [App\Http\Controllers\MarketplaceDownloadController::class, 'generateToken'])->name('generate-token');
        Route::get('/stats', [App\Http\Controllers\MarketplaceDownloadController::class, 'downloadStats'])->name('stats');
        Route::get('/history', [App\Http\Controllers\MarketplaceDownloadController::class, 'downloadHistory'])->name('history');
        Route::post('/redownload/{download}', [App\Http\Controllers\MarketplaceDownloadController::class, 'redownload'])->name('redownload');
    });

    Route::get('/wishlist', [App\Http\Controllers\MarketplaceWishlistController::class, 'index'])->name('wishlist.index');

});

// Public download route (no auth required, token-based)
Route::get('/download/{token}', [App\Http\Controllers\MarketplaceDownloadController::class, 'downloadFile'])->name('marketplace.download.file');

// Marketplace routes (public)
Route::prefix('marketplace')->name('marketplace.')->group(function () {
    // Shopping Cart routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [App\Http\Controllers\MarketplaceCartController::class, 'index'])->name('index');
        Route::post('/add', [App\Http\Controllers\MarketplaceCartController::class, 'add'])
            ->middleware('marketplace.permission:buy')->name('add');
        Route::put('/update/{item}', [App\Http\Controllers\MarketplaceCartController::class, 'update'])->name('update');
        Route::delete('/remove/{item}', [App\Http\Controllers\MarketplaceCartController::class, 'remove'])->name('remove');
        Route::delete('/clear', [App\Http\Controllers\MarketplaceCartController::class, 'clear'])->name('clear');
        Route::get('/data', [App\Http\Controllers\MarketplaceCartController::class, 'data'])->name('data');
        Route::get('/count', [App\Http\Controllers\MarketplaceCartController::class, 'count'])->name('count');
        Route::post('/validate', [App\Http\Controllers\MarketplaceCartController::class, 'validateCartItems'])->name('validate');
        Route::post('/coupon', [App\Http\Controllers\MarketplaceCartController::class, 'applyCoupon'])->name('coupon');
        Route::get('/checkout', [App\Http\Controllers\MarketplaceCartController::class, 'checkout'])->name('checkout');
    });

    // Checkout Routes - Require authentication and checkout permission
    Route::prefix('checkout')->name('checkout.')->middleware(['auth', 'verified', 'marketplace.permission:checkout'])->group(function () {
        Route::get('/', [App\Http\Controllers\MarketplaceCheckoutController::class, 'index'])->name('index');
        Route::post('/shipping', [App\Http\Controllers\MarketplaceCheckoutController::class, 'shipping'])->name('shipping');
        Route::post('/payment', [App\Http\Controllers\MarketplaceCheckoutController::class, 'payment'])->name('payment');
        Route::get('/review', [App\Http\Controllers\MarketplaceCheckoutController::class, 'review'])->name('review');
        Route::post('/place-order', [App\Http\Controllers\MarketplaceCheckoutController::class, 'placeOrder'])->name('place-order');
        Route::get('/payment-gateway/{uuid}', [App\Http\Controllers\MarketplaceCheckoutController::class, 'paymentGateway'])->name('payment-gateway');
        Route::post('/check-payment-status', [App\Http\Controllers\MarketplaceCheckoutController::class, 'checkPaymentStatus'])->name('check-payment-status');
        Route::get('/success/{uuid}', [App\Http\Controllers\MarketplaceCheckoutController::class, 'success'])->name('success');
    });

    // SePay Webhook (public - no auth required)
    Route::post('/sepay-webhook', [App\Http\Controllers\MarketplaceCheckoutController::class, 'sepayWebhook'])->name('sepay-webhook');

    // Seller Setup routes
    Route::prefix('seller')->name('seller.')->middleware('auth')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\MarketplaceSellerController::class, 'dashboard'])->name('dashboard');
        Route::get('/products', [App\Http\Controllers\MarketplaceSellerController::class, 'products'])->name('products');
        Route::get('/orders', [App\Http\Controllers\MarketplaceSellerController::class, 'orders'])->name('orders');
        Route::get('/analytics', [App\Http\Controllers\MarketplaceSellerController::class, 'analytics'])->name('analytics');
        Route::get('/setup', [App\Http\Controllers\MarketplaceSellerSetupController::class, 'show'])->name('setup');
        Route::post('/setup', [App\Http\Controllers\MarketplaceSellerSetupController::class, 'store'])->name('setup.store');
        Route::get('/check-slug', [App\Http\Controllers\MarketplaceSellerSetupController::class, 'checkSlug'])->name('check-slug');
        Route::get('/verification-status', [App\Http\Controllers\MarketplaceSellerSetupController::class, 'verificationStatus'])->name('verification-status');
        Route::post('/resend-verification', [App\Http\Controllers\MarketplaceSellerSetupController::class, 'resendVerification'])->name('resend-verification');
    });
});

// Redirect old forums routes to whats-new
Route::get('/forums/recent', function () {
    return redirect()->route('whats-new');
})->name('forums.recent');

Route::get('/forums/popular', function () {
    return redirect()->route('whats-new.popular');
})->name('forums.popular');

// Community routes - Enhanced
Route::get('/members', function () {
    return view('coming-soon', ['title' => 'Members', 'message' => 'Members directory coming soon']);
})->name('members.index');

// Companies Directory - Enhanced
Route::prefix('companies')->name('companies.')->group(function () {
    Route::get('/', [App\Http\Controllers\CompanyController::class, 'index'])->name('index');
    Route::get('/export', [App\Http\Controllers\CompanyController::class, 'export'])->name('export');
    Route::get('/{company}', [App\Http\Controllers\CompanyController::class, 'show'])->name('show');
    Route::get('/{company}/products', [App\Http\Controllers\CompanyController::class, 'products'])->name('products');
    Route::get('/{company}/contact', [App\Http\Controllers\CompanyController::class, 'contact'])->name('contact');
    Route::post('/{company}/contact', [App\Http\Controllers\CompanyController::class, 'sendMessage'])->name('sendMessage');
    Route::get('/{company}/stats', [App\Http\Controllers\CompanyController::class, 'getStats'])->name('stats');

    // Favorite functionality (requires authentication)
    Route::middleware('auth')->group(function () {
        Route::post('/{company}/favorite', [App\Http\Controllers\CompanyController::class, 'toggleFavorite'])->name('favorite');
    });
});

// Events & Webinars - Enhanced
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [App\Http\Controllers\EventController::class, 'index'])->name('index');
    Route::get('/upcoming', [App\Http\Controllers\EventController::class, 'upcoming'])->name('upcoming');
    Route::get('/create', [App\Http\Controllers\EventController::class, 'create'])->name('create')->middleware('auth');
    Route::post('/', [App\Http\Controllers\EventController::class, 'store'])->name('store')->middleware('auth');
    Route::get('/export', [App\Http\Controllers\EventController::class, 'export'])->name('export');
    Route::get('/{event}', [App\Http\Controllers\EventController::class, 'show'])->name('show');
    Route::post('/{event}/register', [App\Http\Controllers\EventController::class, 'register'])->name('register');
});

// Job Board - Enhanced
Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', [App\Http\Controllers\JobController::class, 'index'])->name('index');
    Route::get('/stats', [App\Http\Controllers\JobController::class, 'getStats'])->name('stats');
    Route::get('/create', [App\Http\Controllers\JobController::class, 'create'])->name('create')->middleware('auth');
    Route::post('/', [App\Http\Controllers\JobController::class, 'store'])->name('store')->middleware('auth');
    Route::get('/export', [App\Http\Controllers\JobController::class, 'export'])->name('export');
    Route::get('/{job}', [App\Http\Controllers\JobController::class, 'show'])->name('show');
    Route::post('/{job}/apply', [App\Http\Controllers\JobController::class, 'apply'])->name('apply')->middleware('auth');
});

// Technical Resources routes - NEW SECTION
Route::prefix('materials')->name('materials.')->group(function () {
    Route::get('/', [App\Http\Controllers\MaterialController::class, 'index'])->name('index');
    // Route::get('/search', [App\Http\Controllers\MaterialController::class, 'search'])->name('search'); // CONSOLIDATED: Use main search with material filter
    Route::get('/compare', [App\Http\Controllers\MaterialController::class, 'compare'])->name('compare');
    Route::get('/calculator', [App\Http\Controllers\MaterialController::class, 'calculator'])->name('calculator');
    Route::get('/export', [App\Http\Controllers\MaterialController::class, 'export'])->name('export');
    Route::get('/{material}', [App\Http\Controllers\MaterialController::class, 'show'])->name('show');
});

Route::prefix('standards')->name('standards.')->group(function () {
    Route::get('/', [App\Http\Controllers\StandardController::class, 'index'])->name('index');
    // Route::get('/search', [App\Http\Controllers\StandardController::class, 'search'])->name('search'); // CONSOLIDATED: Use main search with standard filter
    Route::get('/compare', [App\Http\Controllers\StandardController::class, 'compare'])->name('compare');
    Route::get('/compliance-checker', [App\Http\Controllers\StandardController::class, 'complianceChecker'])->name('compliance-checker');
    Route::get('/export', [App\Http\Controllers\StandardController::class, 'export'])->name('export');
    Route::get('/{standard}', [App\Http\Controllers\StandardController::class, 'show'])->name('show');
});

Route::prefix('manufacturing')->name('manufacturing.')->group(function () {
    Route::get('/processes', [App\Http\Controllers\ManufacturingProcessController::class, 'index'])->name('processes.index');
    // Route::get('/processes/search', [App\Http\Controllers\ManufacturingProcessController::class, 'search'])->name('processes.search'); // CONSOLIDATED: Use main search with process filter
    Route::get('/processes/selector', [App\Http\Controllers\ManufacturingProcessController::class, 'selector'])->name('processes.selector');
    Route::get('/processes/calculator', [App\Http\Controllers\ManufacturingProcessController::class, 'calculator'])->name('processes.calculator');
    Route::get('/processes/compare', [App\Http\Controllers\ManufacturingProcessController::class, 'compare'])->name('processes.compare');
    Route::get('/processes/export', [App\Http\Controllers\ManufacturingProcessController::class, 'export'])->name('processes.export');
    Route::get('/processes/{process}', [App\Http\Controllers\ManufacturingProcessController::class, 'show'])->name('processes.show');
});

Route::prefix('cad')->name('cad.')->group(function () {
    Route::prefix('library')->name('library.')->group(function () {
        Route::get('/', [App\Http\Controllers\CADLibraryController::class, 'index'])->name('index');
        // Route::get('/search', [App\Http\Controllers\CADLibraryController::class, 'search'])->name('search'); // CONSOLIDATED: Use main search with CAD filter
        Route::get('/stats', [App\Http\Controllers\CADLibraryController::class, 'getStats'])->name('stats');
        Route::get('/my-files', [App\Http\Controllers\CADLibraryController::class, 'myFiles'])->name('my-files')->middleware('auth');
        Route::get('/create', [App\Http\Controllers\CADLibraryController::class, 'create'])->name('create')->middleware('auth');
        Route::post('/', [App\Http\Controllers\CADLibraryController::class, 'store'])->name('store')->middleware('auth');
        Route::get('/export', [App\Http\Controllers\CADLibraryController::class, 'export'])->name('export');
        Route::get('/{cadFile}', [App\Http\Controllers\CADLibraryController::class, 'show'])->name('show');
        Route::get('/{cadFile}/download', [App\Http\Controllers\CADLibraryController::class, 'download'])->name('download')->middleware('auth');
        Route::post('/{cadFile}/rate', [App\Http\Controllers\CADLibraryController::class, 'rate'])->name('rate')->middleware('auth');
        Route::post('/{cadFile}/comment', [App\Http\Controllers\CADLibraryController::class, 'comment'])->name('comment')->middleware('auth');
    });
});

// Technical Resources main page
Route::get('/technical', function () {
    return view('technical.index');
})->name('technical.index');

Route::prefix('technical')->name('technical.')->group(function () {
    Route::get('/drawings', function () {
        return view('coming-soon', ['title' => 'Technical Drawings', 'message' => 'Technical drawings library coming soon']);
    })->name('drawings.index');
});

// Community main page
Route::get('/community', function () {
    return view('community.index');
})->name('community.index');

// Showcase main route (moved from public-showcase)
Route::get('/showcase', [ShowcaseController::class, 'publicShowcase'])->name('showcase.index');

// Tools & Calculators routes
Route::prefix('tools')->name('tools.')->group(function () {
    Route::get('/material-calculator', function () {
        return view('tools.material-calculator');
    })->name('material-calculator');
    Route::get('/process-selector', function () {
        return view('coming-soon', ['title' => 'Process Selector', 'message' => 'Manufacturing process selector coming soon']);
    })->name('process-selector');
    Route::get('/standards-checker', function () {
        return view('coming-soon', ['title' => 'Standards Compliance', 'message' => 'Standards compliance checker coming soon']);
    })->name('standards-checker');
});

// Knowledge Base routes - NEW SECTION
Route::prefix('knowledge')->name('knowledge.')->group(function () {
    Route::get('/base', function () {
        return view('knowledge.base.index');
    })->name('base.index');
});

Route::get('/tutorials', function () {
    return view('coming-soon', ['title' => 'Tutorials & Guides', 'message' => 'Tutorials section coming soon']);
})->name('tutorials.index');

Route::get('/documentation', function () {
    return view('coming-soon', ['title' => 'Technical Documentation', 'message' => 'Documentation library coming soon']);
})->name('documentation.index');

Route::prefix('news')->name('news.')->group(function () {
    Route::get('/industry', function () {
        return view('news.industry.index');
    })->name('industry.index');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/industry', function () {
        return view('coming-soon', ['title' => 'Industry Reports', 'message' => 'Industry reports coming soon']);
    })->name('industry.index');
});

// Enhanced More menu routes
Route::get('/gallery', function () {
    return view('coming-soon', ['title' => 'Photo Gallery', 'message' => 'Photo gallery coming soon']);
})->name('gallery.index');

Route::get('/tags', function () {
    return view('coming-soon', ['title' => 'Browse by Tags', 'message' => 'Tag browser coming soon']);
})->name('tags.index');

Route::get('/faq', function () {
    return view('coming-soon', ['title' => 'FAQ', 'message' => 'FAQ section coming soon']);
})->name('faq.index');

Route::get('/help', function () {
    return view('help.index');
})->name('help.index');

// Contact route moved to avoid duplicates

Route::get('/about', function () {
    return view('about.index');
})->name('about.index');

// Dynamic pages from database
Route::get('/terms', [App\Http\Controllers\PageController::class, 'showByRoute'])->defaults('routeName', 'terms')->name('terms.index');
Route::get('/privacy', [App\Http\Controllers\PageController::class, 'showByRoute'])->defaults('routeName', 'privacy')->name('privacy.index');

// Contact route duplicate removed

// Privacy route duplicate removed

// Terms route duplicate removed

Route::get('/cookies', function () {
    return view('coming-soon', ['title' => 'Cookie Policy', 'message' => 'Cookie policy coming soon']);
})->name('cookies');

Route::get('/accessibility', function () {
    return view('coming-soon', ['title' => 'Accessibility', 'message' => 'Accessibility information coming soon']);
})->name('accessibility');

// Main Dashboard Route - Redirects to role-specific dashboard
Route::get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    // Redirect based on role using existing dashboard routes
    return match($user->role) {
        'super_admin', 'admin', 'moderator' => redirect()->route('admin.dashboard'),
        'supplier' => redirect()->route('supplier.dashboard'),
        'manufacturer' => redirect()->route('manufacturer.dashboard'),
        'brand' => redirect()->route('brand.dashboard'),
        'verified_partner' => redirect()->route('partner.dashboard'),
        'member', 'senior_member', 'guest' => redirect()->route('user.dashboard'),
        default => redirect()->route('user.dashboard'),
    };
})->middleware(['auth', 'verified.social'])->name('dashboard');



// Chat/Messages routes
Route::middleware(['auth'])->prefix('messages')->name('chat.')->group(function () {
    Route::get('/', [App\Http\Controllers\ChatController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ChatController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\ChatController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\ChatController::class, 'show'])->name('show');
    Route::post('/{id}/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('send');

    // API routes for chat
    // Route::get('/api/search-users', [App\Http\Controllers\ChatController::class, 'searchUsers'])->name('api.search-users'); // CONSOLIDATED: Use main search with user filter
    Route::get('/api/unread-count', [App\Http\Controllers\ChatController::class, 'getUnreadCount'])->name('api.unread-count');
});

// Demo Admin Chat - REMOVED (temporary testing route)

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

    // Profile orders route
    Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');

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
    Route::patch('/alerts/{alert}/unread', [AlertController::class, 'markAsUnread'])->name('alerts.unread');
    Route::delete('/alerts/{alert}', [AlertController::class, 'destroy'])->name('alerts.destroy');
    Route::patch('/alerts/mark-all-read', [AlertController::class, 'markAllAsRead'])->name('alerts.mark-all-read');
    Route::delete('/alerts/clear-all', [AlertController::class, 'clearAll'])->name('alerts.clear-all');

    // Conversations routes
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations', [ConversationController::class, 'store'])->name('conversations.store');
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'storeMessage'])->name('conversations.messages.store');

    // Bookmarks routes
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::delete('/bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');

    // Showcase routes (authenticated) - Core functionality only
    Route::get('/showcase/create', [ShowcaseController::class, 'create'])->name('showcase.create');
    Route::post('/showcase', [ShowcaseController::class, 'store'])->name('showcase.store');
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
    Route::get('/forums', [\App\Http\Controllers\CategoryController::class, 'index'])->name('forums.index');

    // Forum search routes - Keep forum advanced search as primary (most complete UI)
    Route::get('/forums/search', [ForumController::class, 'search'])->name('forums.search');
    Route::get('/forums/search/advanced', [ForumController::class, 'advancedSearch'])->name('forums.search.advanced');
    Route::get('/forums/search/categories', [ForumController::class, 'searchByCategory'])->name('forums.search.categories');

    Route::get('/forums/{forum}', [ForumController::class, 'show'])->name('forums.show');
});

// Category routes
Route::get('/categories/{category:slug}', [\App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');
// Route::get('/categories/{category:slug}/search', [\App\Http\Controllers\CategoryController::class, 'search'])->name('categories.search'); // CONSOLIDATED: Use main search with category filter
Route::get('/categories/trending', [\App\Http\Controllers\CategoryController::class, 'trending'])->name('categories.trending');

// Thread routes (MUST be before wildcard routes)
Route::resource('threads', \App\Http\Controllers\ThreadController::class);

// Thread to Showcase conversion
Route::post('/threads/{thread}/create-showcase', [\App\Http\Controllers\ThreadController::class, 'createShowcase'])
    ->name('threads.create-showcase')
    ->middleware('auth');

// User Thread Browse Routes (Public and Authenticated)
Route::prefix('browse')->name('browse.threads.')->group(function () {
    // Public routes
    Route::get('/threads', [UserThreadController::class, 'index'])->name('index');
    Route::get('/threads/top-rated', [UserThreadController::class, 'topRated'])->name('top-rated');
    Route::get('/threads/trending', [UserThreadController::class, 'trending'])->name('trending');
    Route::get('/threads/by-tag/{tag}', [UserThreadController::class, 'byTag'])->name('by-tag');
    Route::get('/threads/by-forum/{forum}', [UserThreadController::class, 'byForum'])->name('by-forum');
    // Route::get('/threads/search', [UserThreadController::class, 'search'])->name('search'); // CONSOLIDATED: Use main search with thread filter
});

// User Dashboard Routes (Authenticated only) - For Community Members
Route::middleware('auth')->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-threads', [UserDashboardController::class, 'myThreads'])->name('my-threads');
    Route::get('/bookmarks', [UserDashboardController::class, 'bookmarks'])->name('bookmarks');
    Route::get('/activity', [UserDashboardController::class, 'activity'])->name('activity');
    Route::get('/following', [UserDashboardController::class, 'following'])->name('following');

    // Bookmark management
    Route::get('/bookmarks', [UserDashboardController::class, 'bookmarks'])->name('bookmarks');
    // Route::post('/bookmarks/search', [UserDashboardController::class, 'searchBookmarks'])->name('bookmarks.search'); // CONSOLIDATED: Use main search with bookmark filter
    Route::post('/bookmarks/folders', [UserDashboardController::class, 'createBookmarkFolder'])->name('bookmarks.create-folder');
    Route::put('/bookmarks/folders/{folder}', [UserDashboardController::class, 'updateFolder'])->name('bookmarks.folders.update');
    Route::delete('/bookmarks/folders/{folder}', [UserDashboardController::class, 'deleteFolder'])->name('bookmarks.folders.delete');
    Route::put('/bookmarks/{bookmark}', [UserDashboardController::class, 'updateBookmark'])->name('bookmarks.update');
    Route::delete('/bookmarks/{bookmark}', [UserDashboardController::class, 'deleteBookmark'])->name('bookmarks.delete');
    Route::post('/bookmarks/bulk-delete', [UserDashboardController::class, 'bulkDeleteBookmarks'])->name('bookmarks.bulk-delete');

    // Rating management
    Route::get('/ratings', [UserDashboardController::class, 'ratings'])->name('ratings');

    // My threads
    Route::get('/my-threads', [UserDashboardController::class, 'myThreads'])->name('my-threads');

    // My comments
    Route::get('/comments', [UserDashboardController::class, 'comments'])->name('comments');

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

// More menu routes - REMOVED whats-new duplicate (using WhatsNewController in web-whats-new.php)
// Redirect old forum-listing to unified forums page for backward compatibility
Route::get('/forum-listing', function () {
    return redirect()->route('forums.index', [], 301);
});
// Backward compatibility redirect for old URL
Route::get('/public-showcase', function () {
    return redirect()->route('showcase.index', [], 301);
})->name('showcase.public');

// Public Showcase Detail Route (no auth required)
Route::get('/showcase/{showcase}', [ShowcaseController::class, 'show'])->name('showcase.show');

// Showcase Rating routes
Route::post('/showcases/{showcase}/ratings', [App\Http\Controllers\ShowcaseRatingController::class, 'store'])->name('showcase.rating.store');
Route::get('/showcases/{showcase}/ratings', [App\Http\Controllers\ShowcaseRatingController::class, 'index'])->name('showcase.rating.index');
Route::delete('/showcases/{showcase}/ratings', [App\Http\Controllers\ShowcaseRatingController::class, 'destroy'])->name('showcase.rating.destroy');
Route::delete('/ratings/{rating}', [App\Http\Controllers\ShowcaseRatingController::class, 'deleteRating'])->name('showcase.rating.delete');

// Image upload for comments/ratings
Route::post('/upload-images', [App\Http\Controllers\ImageUploadController::class, 'upload'])->name('images.upload');

// TinyMCE unified upload routes
Route::prefix('api/tinymce')->name('api.tinymce.')->middleware(['auth'])->group(function () {
    Route::post('/upload', [App\Http\Controllers\Api\TinyMCEController::class, 'upload'])->name('upload');
    Route::get('/config', [App\Http\Controllers\Api\TinyMCEController::class, 'getConfig'])->name('config');
    Route::get('/files', [App\Http\Controllers\Api\TinyMCEController::class, 'getFiles'])->name('files');
    Route::delete('/files', [App\Http\Controllers\Api\TinyMCEController::class, 'deleteFile'])->name('files.delete');
});

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
// Unified Search Routes
Route::prefix('search')->name('search.')->group(function () {
    // Main search interfaces
    Route::get('/', [AdvancedSearchController::class, 'basic'])->name('index');
    Route::get('/basic', [AdvancedSearchController::class, 'basic'])->name('basic');

    // CONSOLIDATED: Redirect global advanced search to forum advanced search for better UX
    Route::get('/advanced', function (Request $request) {
        // If no specific type filter, default to forum search which has better UI
        $type = $request->get('filters.type', $request->get('type'));
        if (!$type || $type === 'forum' || $type === 'all') {
            return redirect()->route('forums.search.advanced', $request->query());
        }
        // For other specific types, use global search
        return app(AdvancedSearchController::class)->index($request);
    })->name('advanced');

    // Search API endpoints
    Route::get('/api', [AdvancedSearchController::class, 'search'])->name('api');
    Route::get('/ajax', [AdvancedSearchController::class, 'ajaxSearch'])->name('ajax');
    Route::get('/autocomplete', [AdvancedSearchController::class, 'autocomplete'])->name('autocomplete');
    Route::get('/suggestions', [AdvancedSearchController::class, 'suggestions'])->name('suggestions');
    Route::get('/facets', [AdvancedSearchController::class, 'facets'])->name('facets');

    // Search management (authenticated)
    Route::middleware('auth')->group(function () {
        Route::post('/save', [AdvancedSearchController::class, 'saveSearch'])->name('save');
        Route::get('/saved', [AdvancedSearchController::class, 'savedSearches'])->name('saved');
        Route::get('/analytics', [AdvancedSearchController::class, 'analytics'])->name('analytics');
    });
});

// Unified AJAX search for header
Route::get('/ajax-search', function (Request $request) {
    return app(App\Http\Controllers\UnifiedSearchController::class)->ajaxSearch($request);
})->name('search.ajax.unified');

// Real-time routes removed - now handled by Node.js WebSocket server
Route::get('/members', [MemberController::class, 'index'])->name('members.index');
Route::get('/members/online', [MemberController::class, 'online'])->name('members.online');
Route::get('/members/staff', [MemberController::class, 'staff'])->name('members.staff');
Route::get('/members/leaderboard', [MemberController::class, 'leaderboard'])->name('members.leaderboard');
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

// Theme routes
Route::post('/theme/dark-mode', [ThemeController::class, 'toggleDarkMode'])->name('theme.dark-mode');
Route::post('/theme/original-view', [ThemeController::class, 'toggleOriginalView'])->name('theme.original-view');

// Forum and thread routes (duplicate route removed - already defined in middleware group above)
Route::get('/create-thread', [\App\Http\Controllers\ForumSelectionController::class, 'index'])->name('forums.select')->middleware('auth');
Route::post('/create-thread', [\App\Http\Controllers\ForumSelectionController::class, 'selectForum'])->name('forums.select.submit')->middleware('auth');

// Gallery routes
Route::middleware('auth')->group(function () {
    Route::get('/gallery/create', [GalleryController::class, 'create'])->name('gallery.create');
    Route::post('/gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::delete('/gallery/{media}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
});
Route::get('/gallery/{media}', [GalleryController::class, 'show'])->name('gallery.show');

// Admin routes được load trong RouteServiceProvider

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

// Admin Moderation routes moved to routes/admin.php to avoid route conflicts

// Static pages routes
// About route duplicate removed - using about.index instead

// Dynamic pages from database
Route::get('/rules', [App\Http\Controllers\PageController::class, 'showByRoute'])->defaults('routeName', 'rules')->name('rules');
Route::get('/help/writing-guide', [App\Http\Controllers\PageController::class, 'showByRoute'])->defaults('routeName', 'help')->name('help.writing-guide');

// Dynamic pages from database
Route::get('/contact', [App\Http\Controllers\PageController::class, 'showByRoute'])->defaults('routeName', 'contact')->name('contact');

// Page management routes
Route::get('/pages/categories', [App\Http\Controllers\PageController::class, 'categories'])->name('pages.categories');
Route::get('/pages/category/{slug}', [App\Http\Controllers\PageController::class, 'category'])->name('pages.category');
// Route::get('/pages/search', [App\Http\Controllers\PageController::class, 'search'])->name('pages.search'); // CONSOLIDATED: Use main search with page filter
Route::get('/pages/popular', [App\Http\Controllers\PageController::class, 'popular'])->name('pages.popular');
Route::get('/pages/recent', [App\Http\Controllers\PageController::class, 'recent'])->name('pages.recent');
Route::get('/pages/{slug}', [App\Http\Controllers\PageController::class, 'show'])->name('pages.show');

// Legacy routes and redirects
Route::get('/legacy/{path}', [App\Http\Controllers\PageController::class, 'handleLegacyRoute'])->name('pages.legacy');

// Development/admin routes - REMOVED (should only be in development)

// SEO Routes
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-pages.xml', [App\Http\Controllers\SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-forums.xml', [App\Http\Controllers\SitemapController::class, 'forums'])->name('sitemap.forums');
Route::get('/sitemap-threads.xml', [App\Http\Controllers\SitemapController::class, 'threads'])->name('sitemap.threads');
Route::get('/sitemap-users.xml', [App\Http\Controllers\SitemapController::class, 'users'])->name('sitemap.users');
Route::get('/sitemap-products.xml', [App\Http\Controllers\SitemapController::class, 'products'])->name('sitemap.products');
Route::get('/robots.txt', [App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');

// Documentation Routes (Public)
Route::prefix('docs')->name('docs.')->group(function () {
    Route::get('/', [App\Http\Controllers\DocumentationController::class, 'index'])->name('index');
    // Route::get('/search', [App\Http\Controllers\DocumentationController::class, 'search'])->name('search'); // CONSOLIDATED: Use main search with documentation filter
    Route::get('/category/{category:slug}', [App\Http\Controllers\DocumentationController::class, 'category'])->name('category');
    Route::get('/download/{documentation}/{file}', [App\Http\Controllers\DocumentationController::class, 'download'])->name('download');
    Route::get('/{documentation:slug}', [App\Http\Controllers\DocumentationController::class, 'show'])->name('show');

    // User interactions (requires auth)
    Route::middleware('auth')->group(function () {
        Route::post('/{documentation}/rate', [App\Http\Controllers\DocumentationController::class, 'rate'])->name('rate');
        Route::post('/{documentation}/comment', [App\Http\Controllers\DocumentationController::class, 'comment'])->name('comment');
    });
});

// Contact support - redirect to main contact page
Route::get('/contact/support', function () {
    return redirect()->route('contact');
})->name('contact.support');

// Admin routes are loaded via RouteServiceProvider with proper prefix and middleware

// Supplier Dashboard routes
Route::middleware(['auth', 'role:supplier'])->prefix('supplier')->name('supplier.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Supplier\DashboardController::class, 'index'])->name('dashboard');

    // Product management routes
    Route::get('/products', [App\Http\Controllers\Supplier\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [App\Http\Controllers\Supplier\ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\Supplier\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [App\Http\Controllers\Supplier\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [App\Http\Controllers\Supplier\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [App\Http\Controllers\Supplier\ProductController::class, 'destroy'])->name('products.destroy');

    // Order management routes
    Route::get('/orders', [App\Http\Controllers\Supplier\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderItem}', [App\Http\Controllers\Supplier\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{orderItem}/status', [App\Http\Controllers\Supplier\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/orders/export', [App\Http\Controllers\Supplier\OrderController::class, 'export'])->name('orders.export');

    // Analytics routes
    Route::get('/analytics', [App\Http\Controllers\Supplier\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/export', [App\Http\Controllers\Supplier\AnalyticsController::class, 'export'])->name('analytics.export');

    Route::get('/settings', [App\Http\Controllers\Supplier\SettingsController::class, 'index'])->name('settings.index');
});

// Manufacturer Dashboard routes
Route::middleware(['auth', 'role:manufacturer'])->prefix('manufacturer')->name('manufacturer.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Manufacturer\DashboardController::class, 'index'])->name('dashboard');

    // Product management routes
    Route::get('/products', [App\Http\Controllers\Manufacturer\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [App\Http\Controllers\Manufacturer\ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\Manufacturer\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [App\Http\Controllers\Manufacturer\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [App\Http\Controllers\Manufacturer\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [App\Http\Controllers\Manufacturer\ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/designs', [App\Http\Controllers\Manufacturer\DesignController::class, 'index'])->name('designs.index');
    Route::get('/orders', [App\Http\Controllers\Manufacturer\OrderController::class, 'index'])->name('orders.index');
    Route::get('/analytics', [App\Http\Controllers\Manufacturer\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/settings', [App\Http\Controllers\Manufacturer\SettingsController::class, 'index'])->name('settings.index');
});


// Brand Dashboard routes - View only access
Route::middleware(['auth', 'role:brand'])->prefix('brand')->name('brand.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Brand\DashboardController::class, 'index'])->name('dashboard');

    // Product management routes (showcase products)
    Route::get('/products', [App\Http\Controllers\Brand\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [App\Http\Controllers\Brand\ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\Brand\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [App\Http\Controllers\Brand\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [App\Http\Controllers\Brand\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [App\Http\Controllers\Brand\ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/insights', [App\Http\Controllers\Brand\InsightsController::class, 'index'])->name('insights.index');
    Route::get('/marketplace-analytics', [App\Http\Controllers\Brand\MarketplaceAnalyticsController::class, 'index'])->name('marketplace.analytics');
    Route::get('/forum-analytics', [App\Http\Controllers\Brand\ForumAnalyticsController::class, 'index'])->name('forum.analytics');
    Route::get('/promotion-opportunities', [App\Http\Controllers\Brand\PromotionController::class, 'index'])->name('promotion.index');
});

// Verified Partner Dashboard routes - Premium access
Route::middleware(['auth', 'role:verified_partner'])->prefix('partner')->name('partner.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\VerifiedPartner\DashboardController::class, 'index'])->name('dashboard');

    // Product management routes (all product types)
    Route::get('/products', [App\Http\Controllers\VerifiedPartner\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [App\Http\Controllers\VerifiedPartner\ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\VerifiedPartner\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [App\Http\Controllers\VerifiedPartner\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [App\Http\Controllers\VerifiedPartner\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [App\Http\Controllers\VerifiedPartner\ProductController::class, 'destroy'])->name('products.destroy');

    // Order management routes
    Route::get('/orders', [App\Http\Controllers\VerifiedPartner\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderItem}', [App\Http\Controllers\VerifiedPartner\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{orderItem}/status', [App\Http\Controllers\VerifiedPartner\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/orders/export', [App\Http\Controllers\VerifiedPartner\OrderController::class, 'export'])->name('orders.export');

    // Analytics routes
    Route::get('/analytics', [App\Http\Controllers\VerifiedPartner\AnalyticsController::class, 'index'])->name('analytics.index');

    // Settings routes
    Route::get('/settings', [App\Http\Controllers\VerifiedPartner\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [App\Http\Controllers\VerifiedPartner\SettingsController::class, 'updateProfile'])->name('settings.updateProfile');
    Route::put('/settings/business', [App\Http\Controllers\VerifiedPartner\SettingsController::class, 'updateBusiness'])->name('settings.updateBusiness');
    Route::put('/settings/password', [App\Http\Controllers\VerifiedPartner\SettingsController::class, 'updatePassword'])->name('settings.updatePassword');
    Route::put('/settings/notifications', [App\Http\Controllers\VerifiedPartner\SettingsController::class, 'updateNotifications'])->name('settings.updateNotifications');
    Route::put('/settings/payment', [App\Http\Controllers\VerifiedPartner\SettingsController::class, 'updatePayment'])->name('settings.updatePayment');
    Route::put('/settings/shipping', [App\Http\Controllers\VerifiedPartner\SettingsController::class, 'updateShipping'])->name('settings.updateShipping');
    Route::delete('/settings/deactivate', [App\Http\Controllers\VerifiedPartner\SettingsController::class, 'deactivateAccount'])->name('settings.deactivate');
});

// Test routes - REMOVED (should only be in development)

// AJAX Search route - CONSOLIDATED into unified search system

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

// Thread Follow AJAX API routes
Route::middleware(['auth'])->prefix('ajax/threads/{thread}')->group(function () {
    Route::post('/follow', [\App\Http\Controllers\ThreadFollowController::class, 'follow'])->name('ajax.threads.follow');
    Route::delete('/follow', [\App\Http\Controllers\ThreadFollowController::class, 'unfollow'])->name('ajax.threads.unfollow');
    Route::get('/follow-status', [\App\Http\Controllers\ThreadFollowController::class, 'status'])->name('ajax.threads.follow.status');
});

// Notification AJAX API routes
Route::middleware(['auth'])->prefix('ajax/notifications')->group(function () {
    Route::get('/dropdown', [\App\Http\Controllers\NotificationController::class, 'dropdown'])->name('ajax.notifications.dropdown');
    Route::get('/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('ajax.notifications.unread-count');
    Route::patch('/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('ajax.notifications.mark-read');
    Route::patch('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('ajax.notifications.mark-all-read');
    Route::delete('/{notification}', [\App\Http\Controllers\NotificationController::class, 'delete'])->name('ajax.notifications.delete');
});

// Notification pages
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
});

// Device management AJAX API routes
Route::middleware(['auth'])->prefix('ajax/devices')->group(function () {
    Route::get('/{device}', [\App\Http\Controllers\DeviceController::class, 'show'])->name('ajax.devices.show');
    Route::patch('/{device}/trust', [\App\Http\Controllers\DeviceController::class, 'trust'])->name('ajax.devices.trust');
    Route::patch('/{device}/untrust', [\App\Http\Controllers\DeviceController::class, 'untrust'])->name('ajax.devices.untrust');
    Route::delete('/{device}', [\App\Http\Controllers\DeviceController::class, 'remove'])->name('ajax.devices.remove');
    Route::post('/clean-old', [\App\Http\Controllers\DeviceController::class, 'cleanOld'])->name('ajax.devices.clean-old');
});

// Device management pages
Route::middleware(['auth'])->group(function () {
    Route::get('/devices', [\App\Http\Controllers\DeviceController::class, 'index'])->name('devices.index');
});

// Notification preferences AJAX API routes
Route::middleware(['auth'])->prefix('ajax/notification-preferences')->group(function () {
    Route::patch('/', [\App\Http\Controllers\NotificationPreferencesController::class, 'update'])->name('ajax.notification-preferences.update');
    Route::post('/reset', [\App\Http\Controllers\NotificationPreferencesController::class, 'reset'])->name('ajax.notification-preferences.reset');
});

// Notification preferences pages
Route::middleware(['auth'])->group(function () {
    Route::get('/notification-preferences', [\App\Http\Controllers\NotificationPreferencesController::class, 'index'])->name('notification-preferences.index');
});

// 🏢 Business Registration & Verification Routes - Phase 2
Route::prefix('business')->name('business.')->group(function () {
    // Public registration wizard
    Route::get('/register', [BusinessRegistrationController::class, 'showRegistrationWizard'])->name('registration.wizard');

    // Registration steps (AJAX)
    Route::post('/register/step-1', [BusinessRegistrationController::class, 'processStep1'])->name('registration.step1');
    Route::post('/register/step-2', [BusinessRegistrationController::class, 'processStep2'])->name('registration.step2');
    Route::post('/register/step-4', [BusinessRegistrationController::class, 'processStep4'])->name('registration.step4');

    // Document management
    Route::post('/documents/upload', [BusinessRegistrationController::class, 'uploadDocument'])->name('documents.upload');
    Route::delete('/documents/remove', [BusinessRegistrationController::class, 'removeDocument'])->name('documents.remove');
    Route::get('/documents/required', [BusinessRegistrationController::class, 'getRequiredDocuments'])->name('documents.required');

    // Verification status (authenticated users only)
    Route::middleware(['auth'])->group(function () {
        Route::get('/verification/status', [BusinessRegistrationController::class, 'showVerificationStatus'])->name('verification.status');
        Route::get('/verification/status/ajax', [BusinessRegistrationController::class, 'getApplicationStatus'])->name('verification.status.ajax');
    });
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

    Route::get('/test/header-features', function () {
        return view('test.header-features');
    })->name('test.header-features');

    Route::get('/test/real-time-notifications', function () {
        return view('test.real-time-notifications');
    })->name('test.real-time-notifications')->middleware('auth');
}

// Coming Soon page
Route::get('/coming-soon', [App\Http\Controllers\ComingSoonController::class, 'show'])->name('coming-soon');
