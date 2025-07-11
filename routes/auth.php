<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\RegisterWizardController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Legacy registration routes (keep for backward compatibility)
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    // Multi-step registration wizard routes
    Route::prefix('register/wizard')->name('register.wizard.')->group(function () {
        // Step 1: Basic Information
        Route::get('step1', [RegisterWizardController::class, 'showStep1'])
            ->name('step1');
        Route::post('step1', [RegisterWizardController::class, 'processStep1'])
            ->middleware('throttle:registration');

        // Step 2: Business Information
        Route::get('step2', [RegisterWizardController::class, 'showStep2'])
            ->name('step2');
        Route::post('step2', [RegisterWizardController::class, 'processStep2'])
            ->middleware('throttle:registration');

        // Completion and utilities
        Route::get('complete', [RegisterWizardController::class, 'complete'])
            ->name('complete');
        Route::post('restart', [RegisterWizardController::class, 'restart'])
            ->name('restart');

        // AJAX endpoints
        Route::post('validate-field', [RegisterWizardController::class, 'validateField'])
            ->name('validate-field')
            ->middleware('throttle:field-validation');
        Route::post('check-username', [RegisterWizardController::class, 'checkUsername'])
            ->name('check-username')
            ->middleware('throttle:username-check');
        Route::post('save-progress', [RegisterWizardController::class, 'saveProgress'])
            ->name('save-progress')
            ->middleware('throttle:auto-save');
    });

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // AJAX Authentication Routes
    Route::post('ajax/login', [\App\Http\Controllers\Auth\AjaxAuthController::class, 'login'])
        ->name('ajax.login');

    Route::post('ajax/register', [\App\Http\Controllers\Auth\AjaxAuthController::class, 'register'])
        ->name('ajax.register');

    Route::post('ajax/forgot-password', [\App\Http\Controllers\Auth\AjaxAuthController::class, 'forgotPassword'])
        ->name('ajax.forgot-password');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('logout', [AuthenticatedSessionController::class, 'logoutGet'])
        ->name('logout.get');
});
