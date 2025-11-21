<?php

use App\Http\Middleware\HandleInertiaRequests;
use Packages\Course\Http\Controllers\CourseController;

Route::middleware([HandleInertiaRequests::class])->prefix('egitim')->name('course.')->controller(CourseController::class)->group(function () {
    Route::get('/{slug}', 'view')->name('view');
    Route::get('/{slug}/{page}', 'page')->name('page');
});

require __DIR__ . '/api.php';
