<?php

namespace Packages\Course;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CourseProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutes();
        $this->loadFactories();
        $this->loadSeeders();
        $this->loadMigrations();
    }
    public function loadRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Packages\Course\Http\Controllers')
            ->group(__DIR__ . '/../routes/web.php');
    }

    protected function loadFactories(): void
    {
        $this->loadFactoriesFrom(__DIR__ . '/../database/factories');
    }

    protected function loadSeeders(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/seeders' => database_path('seeders/packages/course'),
            ], 'seeders');
        }
    }

    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations/'),
            ], 'migrations');
        }
    }
}
