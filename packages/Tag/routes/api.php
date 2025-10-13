<?php

use App\Http\Middleware\BatchLogsActivity;
use Packages\Tag\Http\Controllers\TagController;
use Packages\Tag\Models\TagFollow;

Route::name('api.tag.')->prefix('api/tag')->middleware([BatchLogsActivity::class, 'auth'])->controller(TagController::class)->group(function () {

    Route::post('/{slug}/follow', 'follow')->name('follow')->can('create', TagFollow::class);

});
