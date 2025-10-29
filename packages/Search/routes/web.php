<?php

use App\Http\Middleware\HandleInertiaRequests;
use Packages\Search\Http\Controllers\SearchController;

Route::middleware([HandleInertiaRequests::class])->prefix('ara')->name('search.')->controller(SearchController::class)->group(function () {
    Route::get('/', 'view')->name('view');
});

require __DIR__ . '/api.php';
