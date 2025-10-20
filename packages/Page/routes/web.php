<?php

use App\Http\Middleware\HandleInertiaRequests;
use Packages\Page\Http\Controllers\PageController;

Route::middleware([HandleInertiaRequests::class])->name('page.')->prefix('sayfa')->controller(PageController::class)->group(function () {
    Route::get('/{slug}', 'view')->name('view');
});
