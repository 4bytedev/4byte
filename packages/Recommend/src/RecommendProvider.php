<?php

namespace Packages\Recommend;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Packages\Recommend\Console\Commands\UploadRecommendations;

class RecommendProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutes();
        $this->loadConfig();
        $this->loadCommands();
    }

    public function loadRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Packages\Recommend\Http\Controllers')
            ->group(__DIR__ . '/../routes/api.php');
    }

    public function loadConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/recommend.php' => config_path('recommend.php'),
        ]);
        $this->mergeConfigFrom(__DIR__ . '/../config/recommend.php', 'recommend');
    }

    public function loadCommands(): void
    {
        $this->commands([
            UploadRecommendations::class,
        ]);
    }
}
