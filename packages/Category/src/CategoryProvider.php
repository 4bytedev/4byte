<?php

namespace Packages\Category;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Packages\Category\Models\Category;
use Packages\Category\Models\CategoryProfile;
use Packages\Category\Observers\CategoryObserver;
use Packages\Category\Observers\CategoryProfileObserver;
use Packages\Category\Policies\CategoryPolicy;
use Packages\React\Services\ReactService;

class CategoryProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadPolicies();
        $this->loadObservers();
        $this->loadRoutes();
        $this->loadFactories();
        $this->loadSeeders();
        $this->loadMigrations();
        $this->configureReact();
    }

    public function loadPolicies(): void
    {
        Gate::policy(Category::class, CategoryPolicy::class);
    }

    public function loadObservers(): void
    {
        Category::observe(CategoryObserver::class);
        CategoryProfile::observe(CategoryProfileObserver::class);
    }

    public function loadRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Packages\Category\Http\Controllers')
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
                __DIR__ . '/../database/seeders' => database_path('seeders/packages/category'),
            ], 'category-seeders');
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

    protected function configureReact(): void
    {
        ReactService::registerHandler(
            name: 'category',
            class: Category::class,
            callback: fn ($slug) => app(Services\CategoryService::class)->getId($slug)
        );
    }
}
