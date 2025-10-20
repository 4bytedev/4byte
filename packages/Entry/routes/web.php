<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;
use Packages\Entry\Http\Controllers\EntryController;

Route::middleware([HandleInertiaRequests::class])->prefix('entry')->name('entry.')->group(function () {
    Route::controller(EntryController::class)->group(function () {
        Route::get('/{slug}', 'view')->name('view');
    });
});

require __DIR__ . '/api.php';
