<?php

use App\Http\Middleware\BatchLogsActivity;
use Illuminate\Support\Facades\Route;
use Packages\Article\Http\Controllers\ArticleController;
use Packages\Article\Http\Controllers\ArticleCrudController;
use Packages\Article\Models\Article;
use Packages\Article\Models\ArticleComment;
use Packages\Article\Models\ArticleCommentLike;
use Packages\Article\Models\ArticleDislike;
use Packages\Article\Models\ArticleLike;
use Packages\Article\Models\ArticleSave;

Route::name('api.article.')->prefix('api/article')->middleware([BatchLogsActivity::class])->group(function () {

    Route::controller(ArticleController::class)->group(function () {
        Route::post('/{slug}/comments', 'commentList')
            ->name('comment.list');

        Route::post('/{slug}/comment/{comment}/replies', 'commentReplies')
            ->name('comment.replies');
    });

    Route::middleware('auth')->group(function () {

        Route::controller(ArticleController::class)->group(function () {
            Route::post('/{slug}/comment', 'comment')
                ->name('comment')
                ->can('create', ArticleComment::class);

            Route::post('/{slug}/like', 'like')
                ->name('like')
                ->can('create', ArticleLike::class);

            Route::post('/{slug}/dislike', 'dislike')
                ->name('dislike')
                ->can('create', ArticleDislike::class);

            Route::post('/{slug}/save', 'save')
                ->name('save')
                ->can('create', ArticleSave::class);

            Route::post('/{slug}/comment/{comment}/like', 'commentLike')
                ->name('comment.like')
                ->can('create', ArticleCommentLike::class);
        });

        Route::prefix('crud')->name('crud.')->controller(ArticleCrudController::class)->group(function () {
            Route::post('/create', 'create')
                ->name('create')
                ->can('create', Article::class);

            Route::post('/{article:slug}/edit', 'edit')
                ->name('edit')
                ->can('update,article');
        });
    });
});
