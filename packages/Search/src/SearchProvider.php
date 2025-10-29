<?php

namespace Packages\Search;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SearchProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutes();
    }

    public function loadRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Packages\Search\Http\Controllers')
            ->group(__DIR__ . '/../routes/web.php');
    }
}
