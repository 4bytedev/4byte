<?php

namespace Packages\Entry;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Packages\Entry\Models\Entry;
use Packages\Entry\Observers\EntryObserver;
use Packages\Entry\Policies\EntryPolicy;

class EntryProvider extends ServiceProvider
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
    }

    public function loadPolicies(): void
    {
        Gate::policy(Entry::class, EntryPolicy::class);
    }

    public function loadObservers(): void
    {
        Entry::observe(EntryObserver::class);
    }

    public function loadRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Packages\Entry\Http\Controllers')
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
                __DIR__ . '/../database/seeders' => database_path('seeders/packages/entry'),
            ], 'entry-seeders');
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
