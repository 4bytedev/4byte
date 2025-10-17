<?php

use App\Http\Middleware\HandleInertiaRequests;
use Packages\Category\Http\Controllers\CategoryController;

Route::middleware([HandleInertiaRequests::class])->prefix('kategori')->name('category.')->controller(CategoryController::class)->group(function () {
    Route::get('/{slug}', 'view')->name('view');
});
