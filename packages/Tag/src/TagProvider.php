<?php

namespace Packages\Tag;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Packages\Tag\Models\Tag;
use Packages\Tag\Models\TagFollow;
use Packages\Tag\Models\TagProfile;
use Packages\Tag\Observers\TagFollowObserver;
use Packages\Tag\Observers\TagObserver;
use Packages\Tag\Observers\TagProfileObserver;
use Packages\Tag\Policies\TagFollowPolicy;
use Packages\Tag\Policies\TagPolicy;

class TagProvider extends ServiceProvider
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
        $this->loadPolicies();
        $this->loadObservers();
        $this->loadRoutes();
        $this->loadFactories();
        $this->loadSeeders();
        $this->loadMigrations();
    }

    public function loadPolicies()
    {
        Gate::policy(Tag::class, TagPolicy::class);
        Gate::policy(TagFollow::class, TagFollowPolicy::class);
    }

    public function loadObservers()
    {
        Tag::observe(TagObserver::class);
        TagFollow::observe(TagFollowObserver::class);
        TagProfile::observe(TagProfileObserver::class);
    }

    public function loadRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Packages\Tag\Http\Controllers')
            ->group(__DIR__.'/../routes/web.php');
    }

    protected function loadFactories(): void
    {
        $this->loadFactoriesFrom(__DIR__.'/../database/factories');
    }

    protected function loadSeeders(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/seeders' => database_path('seeders/packages/tag'),
            ], 'tag-seeders');
        }
    }

    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
