<?php

use App\Http\Middleware\HandleInertiaRequests;
use Packages\Tag\Http\Controllers\TagController;

Route::middleware([HandleInertiaRequests::class])->prefix('etiket')->controller(TagController::class)->name('tag.')->group(function () {
    Route::get('/{slug}', 'view')->name('view');
});
