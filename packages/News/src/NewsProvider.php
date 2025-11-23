<?php

namespace Packages\News;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Packages\News\Models\News;
use Packages\News\Observers\NewsObserver;
use Packages\News\Policies\NewsPolicy;
use Packages\Recommend\Services\FeedService;

class NewsProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadPolicies();
        $this->loadObservers();
        $this->loadSeeders();
        $this->loadMigrations();
        $this->configureFeed();
    }

    public function loadPolicies(): void
    {
        Gate::policy(News::class, NewsPolicy::class);
    }

    public function loadObservers(): void
    {
        News::observe(NewsObserver::class);
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

    protected function configureFeed(): void
    {
        FeedService::registerHandler(
            name: 'news',
            isFilter: false,
            callback: fn ($slug) => app(Services\NewsService::class)->getData($slug)
        );
    }
}
