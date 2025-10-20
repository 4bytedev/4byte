<?php

use App\Http\Middleware\BatchLogsActivity;
use Illuminate\Support\Facades\Route;
use Packages\Article\Http\Controllers\ArticleCrudController;
use Packages\Article\Models\Article;

Route::name('api.article.')->prefix('api/article')->middleware([BatchLogsActivity::class])->group(function () {
    Route::middleware('auth')->group(function () {
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
