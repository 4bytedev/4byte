<?php

use App\Http\Middleware\BatchLogsActivity;
use Illuminate\Support\Facades\Route;
use Packages\Entry\Http\Controllers\EntryCrudController;
use Packages\Entry\Models\Entry;

Route::name('api.entry.')->prefix('api/entry')->middleware([BatchLogsActivity::class])->group(function () {
    Route::middleware('auth')->group(function () {
        Route::prefix('crud')->name('crud.')->controller(EntryCrudController::class)->group(function () {
            Route::post('/create', 'create')
                ->name('create')
                ->can('create', Entry::class);
        });
    });
});
