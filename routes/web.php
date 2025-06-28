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
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

// Test route để debug
Route::get('/test', function () {
    return 'Test route hoạt động bình thường!';
});

// Tạo route trang chủ mới hoàn toàn
Route::get('/home-new', function () {
    return view('test-home');
})->name('home-new');

// Trang chủ chính tại route /
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Giữ lại route /homepage để backward compatibility
Route::get('/homepage', function () {
    return redirect('/');
});

// Giữ lại route welcome để test
Route::get('/welcome', [\App\Http\Controllers\HomeController::class, 'index'])->name('welcome');
Route::get('/api/threads', [\App\Http\Controllers\HomeController::class, 'getMoreThreads'])->name('api.threads');

// Language switching routes
Route::prefix('language')->name('language.')->group(function () {
    Route::get('/switch/{locale}', [LanguageController::class, 'switch'])->name('switch');
    Route::get('/current', [LanguageController::class, 'current'])->name('current');
    Route::get('/supported', [LanguageController::class, 'supported'])->name('supported');
    Route::post('/auto-detect', [LanguageController::class, 'autoDetect'])->name('auto-detect');
});

// What's New route
Route::get('/whats-new', function () {
    return view('coming-soon', ['title' => 'What\'s New', 'message' => 'What\'s new section coming soon']);
})->name('whats-new');

// Marketplace routes
Route::prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/', [App\Http\Controllers\MarketplaceController::class, 'index'])->name('index');
    Route::get('/products', [App\Http\Controllers\MarketplaceController::class, 'products'])->name('products.index');
    Route::get('/products/new', [App\Http\Controllers\MarketplaceController::class, 'newProducts'])->name('products.new');
    Route::get('/products/popular', [App\Http\Controllers\MarketplaceController::class, 'popularProducts'])->name('products.popular');
    Route::get('/products/{slug}', [App\Http\Controllers\MarketplaceController::class, 'show'])->name('products.show');
    Route::get('/suppliers', [App\Http\Controllers\MarketplaceController::class, 'suppliers'])->name('suppliers.index');
    Route::get('/suppliers/{slug}', [App\Http\Controllers\MarketplaceController::class, 'seller'])->name('sellers.show');
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
    Route::get('/wishlist', [App\Http\Controllers\MarketplaceWishlistController::class, 'index'])->name('wishlist.index');

    // Shopping Cart routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [App\Http\Controllers\MarketplaceCartController::class, 'index'])->name('index');
        Route::post('/add', [App\Http\Controllers\MarketplaceCartController::class, 'add'])->name('add');
        Route::put('/update/{item}', [App\Http\Controllers\MarketplaceCartController::class, 'update'])->name('update');
        Route::delete('/remove/{item}', [App\Http\Controllers\MarketplaceCartController::class, 'remove'])->name('remove');
        Route::delete('/clear', [App\Http\Controllers\MarketplaceCartController::class, 'clear'])->name('clear');
        Route::get('/data', [App\Http\Controllers\MarketplaceCartController::class, 'data'])->name('data');
        Route::get('/count', [App\Http\Controllers\MarketplaceCartController::class, 'count'])->name('count');
        Route::post('/validate', [App\Http\Controllers\MarketplaceCartController::class, 'validateCart'])->name('validate');
        Route::post('/coupon', [App\Http\Controllers\MarketplaceCartController::class, 'applyCoupon'])->name('coupon');
        Route::get('/checkout', [App\Http\Controllers\MarketplaceCartController::class, 'checkout'])->name('checkout');
    });

    // Checkout Routes
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [App\Http\Controllers\MarketplaceCheckoutController::class, 'index'])->name('index');
        Route::post('/shipping', [App\Http\Controllers\MarketplaceCheckoutController::class, 'shipping'])->name('shipping');
        Route::post('/payment', [App\Http\Controllers\MarketplaceCheckoutController::class, 'payment'])->name('payment');
        Route::get('/review', [App\Http\Controllers\MarketplaceCheckoutController::class, 'review'])->name('review');
        Route::post('/place-order', [App\Http\Controllers\MarketplaceCheckoutController::class, 'placeOrder'])->name('place-order');
        Route::get('/success/{uuid}', [App\Http\Controllers\MarketplaceCheckoutController::class, 'success'])->name('success');
    });

    // Seller Setup routes
    Route::prefix('seller')->name('seller.')->middleware('auth')->group(function () {
        Route::get('/setup', [App\Http\Controllers\MarketplaceSellerSetupController::class, 'show'])->name('setup');
        Route::post('/setup', [App\Http\Controllers\MarketplaceSellerSetupController::class, 'store'])->name('setup.store');
        Route::get('/check-slug', [App\Http\Controllers\MarketplaceSellerSetupController::class, 'checkSlug'])->name('check-slug');
        Route::get('/verification-status', [App\Http\Controllers\MarketplaceSellerSetupController::class, 'verificationStatus'])->name('verification-status');
        Route::post('/resend-verification', [App\Http\Controllers\MarketplaceSellerSetupController::class, 'resendVerification'])->name('resend-verification');
    });
});

// Forum routes - Enhanced
Route::prefix('forums')->name('forums.')->group(function () {
    Route::get('/', function () {
        return view('coming-soon', ['title' => 'Forums', 'message' => 'Forum system coming soon']);
    })->name('index');
    Route::get('/recent', function () {
        return view('coming-soon', ['title' => 'Recent Discussions', 'message' => 'Recent discussions coming soon']);
    })->name('recent');
    Route::get('/popular', function () {
        return view('coming-soon', ['title' => 'Popular Topics', 'message' => 'Popular topics coming soon']);
    })->name('popular');
});

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
    Route::post('/{company}/contact', [App\Http\Controllers\CompanyController::class, 'sendMessage'])->name('send-message');
    Route::get('/{company}/stats', [App\Http\Controllers\CompanyController::class, 'getStats'])->name('stats');
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
    Route::get('/search', [App\Http\Controllers\MaterialController::class, 'search'])->name('search');
    Route::get('/compare', [App\Http\Controllers\MaterialController::class, 'compare'])->name('compare');
    Route::get('/calculator', [App\Http\Controllers\MaterialController::class, 'calculator'])->name('calculator');
    Route::get('/export', [App\Http\Controllers\MaterialController::class, 'export'])->name('export');
    Route::get('/{material}', [App\Http\Controllers\MaterialController::class, 'show'])->name('show');
});

Route::prefix('standards')->name('standards.')->group(function () {
    Route::get('/', [App\Http\Controllers\StandardController::class, 'index'])->name('index');
    Route::get('/search', [App\Http\Controllers\StandardController::class, 'search'])->name('search');
    Route::get('/compare', [App\Http\Controllers\StandardController::class, 'compare'])->name('compare');
    Route::get('/compliance-checker', [App\Http\Controllers\StandardController::class, 'complianceChecker'])->name('compliance-checker');
    Route::get('/export', [App\Http\Controllers\StandardController::class, 'export'])->name('export');
    Route::get('/{standard}', [App\Http\Controllers\StandardController::class, 'show'])->name('show');
});

Route::prefix('manufacturing')->name('manufacturing.')->group(function () {
    Route::get('/processes', [App\Http\Controllers\ManufacturingProcessController::class, 'index'])->name('processes.index');
    Route::get('/processes/search', [App\Http\Controllers\ManufacturingProcessController::class, 'search'])->name('processes.search');
    Route::get('/processes/selector', [App\Http\Controllers\ManufacturingProcessController::class, 'selector'])->name('processes.selector');
    Route::get('/processes/calculator', [App\Http\Controllers\ManufacturingProcessController::class, 'calculator'])->name('processes.calculator');
    Route::get('/processes/compare', [App\Http\Controllers\ManufacturingProcessController::class, 'compare'])->name('processes.compare');
    Route::get('/processes/export', [App\Http\Controllers\ManufacturingProcessController::class, 'export'])->name('processes.export');
    Route::get('/processes/{process}', [App\Http\Controllers\ManufacturingProcessController::class, 'show'])->name('processes.show');
});

Route::prefix('cad')->name('cad.')->group(function () {
    Route::prefix('library')->name('library.')->group(function () {
        Route::get('/', [App\Http\Controllers\CADLibraryController::class, 'index'])->name('index');
        Route::get('/search', [App\Http\Controllers\CADLibraryController::class, 'search'])->name('search');
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

Route::get('/showcase', function () {
    return view('coming-soon', ['title' => 'Design Showcase', 'message' => 'Design showcase coming soon']);
})->name('showcase.index');

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

Route::get('/terms', function () {
    return view('coming-soon', ['title' => 'Terms of Service', 'message' => 'Terms of service coming soon']);
})->name('terms.index');

Route::get('/privacy', function () {
    return view('coming-soon', ['title' => 'Privacy Policy', 'message' => 'Privacy policy coming soon']);
})->name('privacy.index');

// Contact route duplicate removed

// Privacy route duplicate removed

// Terms route duplicate removed

Route::get('/cookies', function () {
    return view('coming-soon', ['title' => 'Cookie Policy', 'message' => 'Cookie policy coming soon']);
})->name('cookies');

Route::get('/accessibility', function () {
    return view('coming-soon', ['title' => 'Accessibility', 'message' => 'Accessibility information coming soon']);
})->name('accessibility');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified.social'])->name('dashboard');

// Test chat widget
Route::get('/test-chat', function () {
    return view('test-chat');
})->middleware(['auth'])->name('test-chat');

// Chat/Messages routes
Route::middleware(['auth'])->prefix('messages')->name('chat.')->group(function () {
    Route::get('/', [App\Http\Controllers\ChatController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ChatController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\ChatController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\ChatController::class, 'show'])->name('show');
    Route::post('/{id}/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('send');

    // API routes for chat
    Route::get('/api/search-users', [App\Http\Controllers\ChatController::class, 'searchUsers'])->name('api.search-users');
    Route::get('/api/unread-count', [App\Http\Controllers\ChatController::class, 'getUnreadCount'])->name('api.unread-count');
});

// Demo Admin Chat (temporary for testing)
Route::middleware(['auth'])->prefix('demo-admin-chat')->name('demo.admin.chat.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\ChatController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Admin\ChatController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Admin\ChatController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\Admin\ChatController::class, 'show'])->name('show');
    Route::post('/{id}/send', [App\Http\Controllers\Admin\ChatController::class, 'sendMessage'])->name('send');
    Route::get('/api/search-users', [App\Http\Controllers\Admin\ChatController::class, 'searchUsers'])->name('api.search-users');
});

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

// Admin routes are loaded via RouteServiceProvider with proper prefix and middleware

// Supplier Dashboard routes
Route::middleware(['auth', 'role:supplier'])->prefix('supplier')->name('supplier.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Supplier\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/products', [App\Http\Controllers\Supplier\ProductController::class, 'index'])->name('products.index');
    Route::get('/orders', [App\Http\Controllers\Supplier\OrderController::class, 'index'])->name('orders.index');
    Route::get('/analytics', [App\Http\Controllers\Supplier\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/settings', [App\Http\Controllers\Supplier\SettingsController::class, 'index'])->name('settings.index');
});

// Manufacturer Dashboard routes
Route::middleware(['auth', 'role:manufacturer'])->prefix('manufacturer')->name('manufacturer.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Manufacturer\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/designs', [App\Http\Controllers\Manufacturer\DesignController::class, 'index'])->name('designs.index');
    Route::get('/orders', [App\Http\Controllers\Manufacturer\OrderController::class, 'index'])->name('orders.index');
    Route::get('/analytics', [App\Http\Controllers\Manufacturer\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/settings', [App\Http\Controllers\Manufacturer\SettingsController::class, 'index'])->name('settings.index');
});

// Brand Dashboard routes - View only access
Route::middleware(['auth', 'role:brand'])->prefix('brand')->name('brand.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Brand\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/insights', [App\Http\Controllers\Brand\InsightsController::class, 'index'])->name('insights.index');
    Route::get('/marketplace-analytics', [App\Http\Controllers\Brand\MarketplaceAnalyticsController::class, 'index'])->name('marketplace.analytics');
    Route::get('/forum-analytics', [App\Http\Controllers\Brand\ForumAnalyticsController::class, 'index'])->name('forum.analytics');
    Route::get('/promotion-opportunities', [App\Http\Controllers\Brand\PromotionController::class, 'index'])->name('promotion.index');
});

// Test routes (remove in production)
Route::get('/test-header', function () {
    return view('test-header');
})->name('test.header');

Route::get('/test-language', function () {
    return view('test-language');
})->name('test.language');

Route::get('/debug-language', function () {
    return response()->json([
        'app_locale' => app()->getLocale(),
        'session_locale' => session('locale'),
        'config_locale' => config('app.locale'),
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'supported_locales' => \App\Services\LanguageService::getSupportedLocales(),
        'current_language_info' => \App\Services\LanguageService::getCurrentLanguageInfo(),
    ]);
})->name('debug.language');

Route::get('/simple-language-test', function () {
    return view('simple-language-test');
})->name('simple.language.test');

// AJAX Search route - Disabled, using SearchController::ajaxSearch instead
/*
Route::get('/ajax-search', function () {
    // This route is disabled - using SearchController::ajaxSearch instead
    // See line 449: Route::get('/ajax-search', [SearchController::class, 'ajaxSearch'])->name('search.ajax');
})->name('ajax.search.disabled');
*/

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
