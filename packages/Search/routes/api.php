<?php

use Packages\Search\Http\Controllers\SearchController;

Route::name('api.search')->prefix('api/search')->controller(SearchController::class)->group(function () {
    Route::get('/', 'search');
});
