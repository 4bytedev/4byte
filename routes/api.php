<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\BatchLogsActivity;
use Illuminate\Support\Facades\Route;

Route::name('api.')->prefix('api')->middleware(BatchLogsActivity::class)->group(function () {
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/{username}/preview', [UserController::class, 'preview'])->name('preview');
    });

    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password.request');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::prefix('user/me/settings')->name('user.settings.')->group(function () {
            Route::post('/account', [UserController::class, 'updateAccount'])->name('account')->can('update-account');
            Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile')->can('update-profile');
            Route::post('/password', [UserController::class, 'updatePassword'])->name('password')->can('update-password');
            Route::post('/logout-other-sessions', [UserController::class, 'logOutOtherSessions'])->name('logout-other-sessions')->can('delete-sessions');
            Route::post('/delete-account', [UserController::class, 'deleteAccount'])->name('delete-account')->can('delete-account');
        });

        Route::post('/user/me/verification/resend', [UserController::class, 'verificationResend'])->name('user.verification.resend');

        Route::prefix('notifications')->name('notification.')->controller(NotificationController::class)->group(function () {
            Route::get('/', 'list')->name('list')->can('view-notification');
            Route::get('/count', 'count')->name('count')->can('view-notification');
            Route::post('/mark-as-read', 'markAsRead')->name('mark-as-read')->can('view-notification');
            Route::post('/mark-all-as-read', 'markAllAsRead')->name('mark-all-as-read')->can('view-notification');
        });
    });
});
