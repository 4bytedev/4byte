<?php

use App\Http\Middleware\BatchLogsActivity;
use Illuminate\Support\Facades\Route;
use Packages\Recommend\Http\Controllers\FeedController;

Route::name('api.feed.')->prefix('api/feed')->middleware(BatchLogsActivity::class)->controller(FeedController::class)->group(function () {
    Route::get('/', 'data')->name('data');
    Route::get('/feed', 'feed')->name('feed');
});
