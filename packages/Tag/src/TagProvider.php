<?php

namespace Packages\Tag;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Packages\React\Services\ReactService;
use Packages\Tag\Models\Tag;
use Packages\Tag\Models\TagProfile;
use Packages\Tag\Observers\TagObserver;
use Packages\Tag\Observers\TagProfileObserver;
use Packages\Tag\Policies\TagPolicy;

class TagProvider extends ServiceProvider
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
        Gate::policy(Tag::class, TagPolicy::class);
    }

    public function loadObservers(): void
    {
        Tag::observe(TagObserver::class);
        TagProfile::observe(TagProfileObserver::class);
    }

    public function loadRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Packages\Tag\Http\Controllers')
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
                __DIR__ . '/../database/seeders' => database_path('seeders/packages/tag'),
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

    protected function configureReact(): void
    {
        ReactService::registerHandler(
            name: "tag",
            class: Tag::class, 
            callback: fn ($slug) => app(Services\TagService::class)->getId($slug)
        );
    }
}
