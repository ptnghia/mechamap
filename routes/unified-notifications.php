<?php

use App\Http\Controllers\UnifiedNotificationController;
use Illuminate\Support\Facades\Route;

/**
 * Unified Notification Routes
 * Single route structure for all notification operations
 * Replaces separate alerts and notifications routes
 */

// Main notification page
Route::get('/notifications', [UnifiedNotificationController::class, 'index'])
    ->name('notifications.index')
    ->middleware(['auth', 'verified']);

// AJAX endpoints for notification operations
Route::prefix('ajax/notifications')->middleware(['auth', 'verified'])->group(function () {
    
    // Get notifications for dropdown
    Route::get('/dropdown', [UnifiedNotificationController::class, 'dropdown'])
        ->name('ajax.notifications.dropdown');
    
    // Get unread count
    Route::get('/unread-count', [UnifiedNotificationController::class, 'unreadCount'])
        ->name('ajax.notifications.unread-count');
    
    // Mark as read/unread
    Route::patch('/{notification}/read', [UnifiedNotificationController::class, 'markAsRead'])
        ->name('ajax.notifications.mark-read');
    
    Route::patch('/{notification}/unread', [UnifiedNotificationController::class, 'markAsUnread'])
        ->name('ajax.notifications.mark-unread');
    
    // Mark all as read
    Route::patch('/mark-all-read', [UnifiedNotificationController::class, 'markAllAsRead'])
        ->name('ajax.notifications.mark-all-read');
    
    // Delete operations
    Route::delete('/{notification}', [UnifiedNotificationController::class, 'delete'])
        ->name('ajax.notifications.delete');
    
    Route::delete('/clear-all', [UnifiedNotificationController::class, 'clearAll'])
        ->name('ajax.notifications.clear-all');
    
    // Archive operations
    Route::patch('/{notification}/archive', [UnifiedNotificationController::class, 'archive'])
        ->name('ajax.notifications.archive');
    
    // Track interactions
    Route::post('/{notification}/track', [UnifiedNotificationController::class, 'trackInteraction'])
        ->name('ajax.notifications.track');
});

// API endpoints for mobile/external access
Route::prefix('api/v2/notifications')->middleware(['auth:sanctum'])->group(function () {
    
    // Get notifications with pagination
    Route::get('/', [UnifiedNotificationController::class, 'index'])
        ->name('api.v2.notifications.index');
    
    // Get unread count
    Route::get('/unread-count', [UnifiedNotificationController::class, 'unreadCount'])
        ->name('api.v2.notifications.unread-count');
    
    // Mark operations
    Route::patch('/{notification}/read', [UnifiedNotificationController::class, 'markAsRead'])
        ->name('api.v2.notifications.mark-read');
    
    Route::patch('/mark-all-read', [UnifiedNotificationController::class, 'markAllAsRead'])
        ->name('api.v2.notifications.mark-all-read');
    
    // Delete operations
    Route::delete('/{notification}', [UnifiedNotificationController::class, 'delete'])
        ->name('api.v2.notifications.delete');
    
    Route::delete('/clear-all', [UnifiedNotificationController::class, 'clearAll'])
        ->name('api.v2.notifications.clear-all');
});

// Admin routes for notification management
Route::prefix('admin/notifications')->middleware(['auth', 'admin'])->group(function () {
    
    // Admin notification overview
    Route::get('/', [UnifiedNotificationController::class, 'adminIndex'])
        ->name('admin.notifications.index');
    
    // Send bulk notifications
    Route::post('/bulk-send', [UnifiedNotificationController::class, 'bulkSend'])
        ->name('admin.notifications.bulk-send');
    
    // Notification analytics
    Route::get('/analytics', [UnifiedNotificationController::class, 'analytics'])
        ->name('admin.notifications.analytics');
    
    // System notifications
    Route::post('/system-announcement', [UnifiedNotificationController::class, 'systemAnnouncement'])
        ->name('admin.notifications.system-announcement');
});

// Redirect old routes to new unified routes
Route::redirect('/alerts', '/notifications', 301);
Route::redirect('/ajax/alerts/{any}', '/ajax/notifications/{any}', 301)->where('any', '.*');

// Legacy API redirects
Route::redirect('/api/v1/notifications/{any}', '/api/v2/notifications/{any}', 301)->where('any', '.*');
Route::redirect('/api/v1/unified-notifications/{any}', '/api/v2/notifications/{any}', 301)->where('any', '.*');
