<?php

use App\Http\Middleware\BatchLogsActivity;
use Illuminate\Support\Facades\Route;
use Packages\Article\Models\ArticleComment;
use Packages\Article\Models\ArticleCommentLike;
use Packages\Entry\Http\Controllers\EntryController;
use Packages\Entry\Http\Controllers\EntryCrudController;
use Packages\Entry\Models\Entry;
use Packages\Entry\Models\EntryDislike;
use Packages\Entry\Models\EntryLike;
use Packages\Entry\Models\EntrySave;

Route::name('api.entry.')->prefix('api/entry')->middleware([BatchLogsActivity::class])->group(function () {

    Route::controller(EntryController::class)->group(function () {
        Route::post('/{slug}/comments', 'commentList')
            ->name('comment.list');

        Route::post('/{slug}/comment/{comment}/replies', 'commentReplies')
            ->name('comment.replies');
    });

    Route::middleware('auth')->group(function () {

        Route::controller(EntryController::class)->group(function () {
            Route::post('/{slug}/comment', 'comment')
                ->name('comment')
                ->can('create', ArticleComment::class);

            Route::post('/{slug}/like', 'like')
                ->name('like')
                ->can('create', EntryLike::class);

            Route::post('/{slug}/dislike', 'dislike')
                ->name('dislike')
                ->can('create', EntryDislike::class);

            Route::post('/{slug}/save', 'save')
                ->name('save')
                ->can('create', EntrySave::class);

            Route::post('/{slug}/comment/{comment}/like', 'commentLike')
                ->name('comment.like')
                ->can('create', ArticleCommentLike::class);
        });

        Route::prefix('crud')->name('crud.')->controller(EntryCrudController::class)->group(function () {
            Route::post('/create', 'create')
                ->name('create')
                ->can('create', Entry::class);
        });
    });

});
