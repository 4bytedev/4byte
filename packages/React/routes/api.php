<?php

use App\Http\Middleware\BatchLogsActivity;
use Packages\React\Http\Controllers\ReactController;
use Packages\React\Models\Comment;
use Packages\React\Models\Dislike;
use Packages\React\Models\Follow;
use Packages\React\Models\Like;
use Packages\React\Models\Save;

Route::name('api.react.')->prefix('api/react')->middleware([BatchLogsActivity::class])->controller(ReactController::class)->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::post('/{type}/{slug}/like', 'like')->name('like')->can('create', Like::class);
        Route::post('/{type}/{slug}/dislike', 'dislike')->name('dislike')->can('create', Dislike::class);
        Route::post('/{type}/{slug}/save', 'save')->name('save')->can('create', Save::class);
        Route::post('/{type}/{slug}/comment', 'comment')->name('comment')->can('create', Comment::class);
        Route::post('/{type}/{slug}/follow', 'follow')->name('follow')->can('create', Follow::class);
    });
    Route::post('/{type}/{slug}/comments', 'comments')
        ->name('comments');
    Route::post('/{type}/{slug}/comment/{parent}/replies', 'replies')
        ->name('comment.replies');
});
