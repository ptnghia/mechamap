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

// API version 1
Route::prefix('v1')->group(function () {
    // Public routes
    Route::get('/settings', [\App\Http\Controllers\Api\SettingsController::class, 'index']);
    Route::get('/settings/{group}', [\App\Http\Controllers\Api\SettingsController::class, 'getByGroup']);
    Route::get('/seo', [\App\Http\Controllers\Api\SeoController::class, 'index']);
    Route::get('/seo/{group}', [\App\Http\Controllers\Api\SeoController::class, 'getByGroup']);
    Route::get('/page-seo/{routeName}', [\App\Http\Controllers\Api\SeoController::class, 'getPageSeoByRoute']);
    Route::get('/page-seo/url/{urlPattern}', [\App\Http\Controllers\Api\SeoController::class, 'getPageSeoByUrl']);
    Route::get('/favicon', [\App\Http\Controllers\Api\FaviconController::class, 'getFavicon']);

    // Auth routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});
