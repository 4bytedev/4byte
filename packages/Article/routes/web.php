<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;
use Packages\Article\Http\Controllers\ArticleController;
use Packages\Article\Http\Controllers\ArticleCrudController;
use Packages\Article\Models\Article;

Route::middleware([HandleInertiaRequests::class])->prefix('makale')->name('article.')->group(function () {
    Route::controller(ArticleCrudController::class)->middleware('auth')->name('crud.')->group(function () {
        Route::get('/yaz', 'createView')->name('create.view')->can('create', Article::class);
        Route::get('/{article:slug}/duzenle', 'editView')->name('edit.view')->can('view,article');
    });

    Route::controller(ArticleController::class)->group(function () {
        Route::get('/{slug}', 'view')->name('view');
    });
});

require __DIR__ . '/api.php';
