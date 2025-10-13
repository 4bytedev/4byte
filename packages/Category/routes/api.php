<?php

use App\Http\Middleware\BatchLogsActivity;
use Packages\Category\Http\Controllers\CategoryController;
use Packages\Category\Models\CategoryFollow;

Route::name('api.category.')->prefix('api/category')->middleware([BatchLogsActivity::class, 'auth'])->controller(CategoryController::class)->group(function () {

    Route::post('/{slug}/follow', 'follow')->name('follow')->can('create', CategoryFollow::class);

});
