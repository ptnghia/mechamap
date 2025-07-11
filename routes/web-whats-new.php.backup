<?php

use App\Http\Controllers\WhatsNewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| What's New Routes
|--------------------------------------------------------------------------
|
| Routes for the "What's New" section of the application.
|
*/

// Main What's New page - shows recent posts
Route::get('/whats-new', [WhatsNewController::class, 'index'])->name('whats-new');

// Popular content
Route::get('/whats-new/popular', [WhatsNewController::class, 'popular'])->name('whats-new.popular');

// New threads
Route::get('/whats-new/threads', [WhatsNewController::class, 'threads'])->name('whats-new.threads');

// New media
Route::get('/whats-new/media', [WhatsNewController::class, 'media'])->name('whats-new.media');

// Threads looking for replies
Route::get('/whats-new/replies', [WhatsNewController::class, 'replies'])->name('whats-new.replies');

// New showcases
Route::get('/whats-new/showcases', [WhatsNewController::class, 'showcases'])->name('whats-new.showcases');

// Trending content
Route::get('/whats-new/trending', [WhatsNewController::class, 'trending'])->name('whats-new.trending');

// Most viewed content
Route::get('/whats-new/most-viewed', [WhatsNewController::class, 'mostViewed'])->name('whats-new.most-viewed');

// Hot topics
Route::get('/whats-new/hot-topics', [WhatsNewController::class, 'hotTopics'])->name('whats-new.hot-topics');
