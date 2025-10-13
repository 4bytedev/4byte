<?php

namespace Packages\Recommend;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RecommendProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutes();
        $this->loadConfig();
    }

    public function loadRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Packages\Recommend\Http\Controllers')
            ->group(__DIR__.'/../routes/api.php');
    }

    public function loadConfig()
    {
        $this->publishes([
            __DIR__.'/../config/recommend.php' => config_path('recommend.php'),
        ]);
        $this->mergeConfigFrom(__DIR__.'/../config/recommend.php', 'recommend');
    }
}
