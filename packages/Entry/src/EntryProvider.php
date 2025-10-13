<?php

namespace Packages\Entry;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Packages\Entry\Models\Entry;
use Packages\Entry\Models\EntryComment;
use Packages\Entry\Models\EntryCommentLike;
use Packages\Entry\Models\EntryDislike;
use Packages\Entry\Models\EntryLike;
use Packages\Entry\Models\EntrySave;
use Packages\Entry\Observers\EntryCommentLikeObserver;
use Packages\Entry\Observers\EntryDislikeObserver;
use Packages\Entry\Observers\EntryLikeObserver;
use Packages\Entry\Observers\EntryObserver;
use Packages\Entry\Policies\EntryCommentLikePolicy;
use Packages\Entry\Policies\EntryCommentPolicy;
use Packages\Entry\Policies\EntryDislikePolicy;
use Packages\Entry\Policies\EntryLikePolicy;
use Packages\Entry\Policies\EntryPolicy;
use Packages\Entry\Policies\EntrySavePolicy;

class EntryProvider extends ServiceProvider
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
    }

    public function loadPolicies(): void
    {
        Gate::policy(Entry::class, EntryPolicy::class);
        Gate::policy(EntryDislike::class, EntryDislikePolicy::class);
        Gate::policy(EntryLike::class, EntryLikePolicy::class);
        Gate::policy(EntrySave::class, EntrySavePolicy::class);
        Gate::policy(EntryComment::class, EntryCommentPolicy::class);
        Gate::policy(EntryCommentLike::class, EntryCommentLikePolicy::class);
    }

    public function loadObservers(): void
    {
        Entry::observe(EntryObserver::class);
        EntryLike::observe(EntryLikeObserver::class);
        EntryDislike::observe(EntryDislikeObserver::class);
        EntryDislike::observe(EntryDislikeObserver::class);
        EntryCommentLike::observe(EntryCommentLikeObserver::class);
        // EntrySave::observe(EntrySaveObserver::class);
    }

    public function loadRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Packages\Entry\Http\Controllers')
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
                __DIR__.'/../database/seeders' => database_path('seeders/packages/entry'),
            ], 'entry-seeders');
        }
    }

    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
